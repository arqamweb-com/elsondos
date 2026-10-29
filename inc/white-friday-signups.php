<?php

/**
 * White Friday signups
 * - "فكّرني" reminders on the nights (WhatsApp or email)
 * - "ادخل قبل الكل بساعتين" early access form
 * Both are saved in one table and listed under Dashboard → White Friday.
 */

define('ELSONDOS_WF_DB_VERSION', '1');

function elsondos_wf_table()
{
    global $wpdb;
    return $wpdb->prefix . 'elsondos_wf_signups';
}

/**
 * Create / update the table when the version changes
 */
add_action('init', function () {
    if (ELSONDOS_WF_DB_VERSION === get_option('elsondos_wf_db_version')) {
        return;
    }

    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table   = elsondos_wf_table();
    $charset = $wpdb->get_charset_collate();

    dbDelta("CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        type varchar(20) NOT NULL,
        method varchar(20) NOT NULL DEFAULT '',
        name varchar(190) NOT NULL DEFAULT '',
        phone varchar(32) NOT NULL DEFAULT '',
        email varchar(190) NOT NULL DEFAULT '',
        night tinyint(3) unsigned DEFAULT NULL,
        interest varchar(100) NOT NULL DEFAULT '',
        lang varchar(10) NOT NULL DEFAULT '',
        notified_at datetime DEFAULT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY type_night (type, night)
    ) {$charset};");

    update_option('elsondos_wf_db_version', ELSONDOS_WF_DB_VERSION);
});

/**
 * Keep digits only, with an optional leading +
 */
function elsondos_wf_clean_phone($phone)
{
    $phone = trim((string) $phone);
    // Arabic-Indic digits typed on Arabic keyboards
    $phone = strtr($phone, array('٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'));
    $plus  = 0 === strpos($phone, '+');
    $phone = preg_replace('/\D+/', '', $phone);

    return ($plus ? '+' : '') . $phone;
}

/**
 * International number for wa.me (Egyptian 01xxxxxxxxx → 201xxxxxxxxx)
 */
function elsondos_wf_whatsapp_number($phone)
{
    $phone = ltrim($phone, '+');

    if (0 === strpos($phone, '00')) {
        return substr($phone, 2);
    }
    if (0 === strpos($phone, '0')) {
        return '20' . substr($phone, 1);
    }

    return $phone;
}

/**
 * AJAX: save a signup (guests included)
 * No nonce: the page is cached by LiteSpeed and a cached nonce would expire, so it's
 * protected by a honeypot, strict validation and a per-IP limit instead.
 */
function elsondos_wf_ajax_signup()
{
    global $wpdb;

    // honeypot, real visitors never see this field
    if (!empty($_POST['website'])) {
        wp_send_json_success(array('status' => 'saved'));
    }

    $ip_key = 'elsondos_wf_rate_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    $hits   = (int) get_transient($ip_key);
    if ($hits >= 20) {
        wp_send_json_error(array('message' => 'rate_limited'), 429);
    }
    set_transient($ip_key, $hits + 1, HOUR_IN_SECONDS);

    $type = sanitize_key(wp_unslash($_POST['type'] ?? ''));
    $lang = sanitize_key(wp_unslash($_POST['lang'] ?? ''));
    $row  = array(
        'type'       => $type,
        'method'     => '',
        'name'       => '',
        'phone'      => '',
        'email'      => '',
        'night'      => null,
        'interest'   => '',
        'lang'       => substr($lang, 0, 10),
        'created_at' => current_time('mysql'),
    );

    if ('reminder' === $type) {
        $night  = isset($_POST['night']) ? (int) $_POST['night'] : -1;
        $method = sanitize_key(wp_unslash($_POST['method'] ?? ''));

        if (!array_key_exists($night, elsondos_wf_saved_nights()) || !in_array($method, array('whatsapp', 'email'), true)) {
            wp_send_json_error(array('message' => 'invalid'), 400);
        }

        $row['night']  = $night;
        $row['method'] = $method;

        if ('email' === $method) {
            $email = sanitize_email(wp_unslash($_POST['contact'] ?? ''));
            if (!is_email($email)) {
                wp_send_json_error(array('message' => 'invalid_contact'), 400);
            }
            $row['email'] = $email;
        } else {
            $row['phone'] = elsondos_wf_clean_phone(wp_unslash($_POST['contact'] ?? ''));
        }
    } elseif ('early' === $type) {
        $row['method']   = 'whatsapp';
        $row['name']     = substr(sanitize_text_field(wp_unslash($_POST['name'] ?? '')), 0, 190);
        $row['phone']    = elsondos_wf_clean_phone(wp_unslash($_POST['phone'] ?? ''));
        $row['interest'] = substr(sanitize_text_field(wp_unslash($_POST['interest'] ?? '')), 0, 100);

        if ('' === $row['name']) {
            wp_send_json_error(array('message' => 'invalid'), 400);
        }
    } else {
        wp_send_json_error(array('message' => 'invalid'), 400);
    }

    if ('whatsapp' === $row['method'] && !preg_match('/^\+?\d{8,15}$/', $row['phone'])) {
        wp_send_json_error(array('message' => 'invalid_contact'), 400);
    }

    // same person, same night (or already on the early list) → don't save twice
    $table = elsondos_wf_table();
    $sql = $wpdb->prepare("SELECT id FROM {$table} WHERE type = %s AND phone = %s AND email = %s", $row['type'], $row['phone'], $row['email']);
    $sql .= null === $row['night'] ? ' AND night IS NULL' : $wpdb->prepare(' AND night = %d', $row['night']);
    $exists = $wpdb->get_var($sql);

    if ($exists) {
        wp_send_json_success(array('status' => 'exists'));
    }

    $wpdb->insert($table, $row);

    if (!$wpdb->insert_id) {
        wp_send_json_error(array('message' => 'db_error'), 500);
    }

    wp_send_json_success(array('status' => 'saved'));
}

add_action('wp_ajax_elsondos_wf_signup', 'elsondos_wf_ajax_signup');
add_action('wp_ajax_nopriv_elsondos_wf_signup', 'elsondos_wf_ajax_signup');
