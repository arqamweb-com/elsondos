<?php

/**
 * Automatic reminder emails
 * Every 10 minutes (WP-Cron), while a night is open, email everyone who asked to be reminded
 * about it by email and wasn't notified yet, then mark them as notified.
 * WhatsApp reminders stay manual (Dashboard → White Friday → Reminders → Send on WhatsApp).
 */

define('ELSONDOS_WF_EMAIL_HOOK', 'elsondos_wf_send_reminder_emails');
define('ELSONDOS_WF_EMAIL_BATCH', 50); // per run, keeps each cron run short

function elsondos_wf_auto_email_enabled()
{
    return 'off' !== get_option('elsondos_wf_auto_email', 'on');
}

/**
 * Id of the night that is open right now, or null (open / close times come from the Offers schedule)
 */
function elsondos_wf_live_night($now = null)
{
    $now = $now ?? time();

    foreach (elsondos_wf_saved_nights() as $id => $n) {
        if ($now >= $n['start'] && $now < $n['end']) {
            return (int) $id;
        }
    }

    return null;
}

add_filter('cron_schedules', function ($schedules) {
    $schedules['elsondos_wf_10min'] = array(
        'interval' => 10 * MINUTE_IN_SECONDS,
        'display'  => 'Every 10 minutes (White Friday reminders)',
    );
    return $schedules;
});

/**
 * Keep the event scheduled during the campaign, remove it once it's over
 */
add_action('init', function () {
    $scheduled = wp_next_scheduled(ELSONDOS_WF_EMAIL_HOOK);
    $needed    = elsondos_white_friday_active() && elsondos_wf_auto_email_enabled() && time() <= elsondos_wf_last_night_end();

    if ($needed && !$scheduled) {
        wp_schedule_event(time() + MINUTE_IN_SECONDS, 'elsondos_wf_10min', ELSONDOS_WF_EMAIL_HOOK);
    } elseif (!$needed && $scheduled) {
        wp_clear_scheduled_hook(ELSONDOS_WF_EMAIL_HOOK);
    }
});

/**
 * Build the email in the customer's language (WPML), wrapped in the WooCommerce email template
 */
function elsondos_wf_reminder_email($night_index, $lang = '')
{
    $switched = false;

    if ($lang) {
        $languages = apply_filters('wpml_active_languages', null);
        if (!empty($languages[$lang]['default_locale'])) {
            do_action('wpml_switch_language', $lang);
            $switched = switch_to_locale($languages[$lang]['default_locale']);
        }
    }

    $nights = elsondos_wf_nights();
    $night  = $nights[$night_index];
    $url    = add_query_arg(array(
        'utm_source'   => 'reminder',
        'utm_medium'   => 'email',
        'utm_campaign' => 'white-friday',
    ), apply_filters('wpml_permalink', elsondos_wf_page_url(), $lang ?: null));

    /* translators: 1: night name, 2: category, 3: discount % */
    $subject = sprintf(__('%1$s فتحت: %2$s بخصم لحد %3$s%%', 'arqamweb'), $night['day'], $night['title'], $night['pct']);
    $heading = __('الليلة فتحت!', 'arqamweb');

    ob_start();
    ?>
    <p><?php echo esc_html__('أهلاً،', 'arqamweb'); ?></p>
    <p><?php
        /* translators: %s: night name */
        echo esc_html(sprintf(__('%s من عروض الجمعة البيضاء في السندس فتحت دلوقتي.', 'arqamweb'), $night['day']));
    ?></p>
    <h2 style="margin:24px 0 4px"><?php echo esc_html($night['title']); ?></h2>
    <p style="font-size:18px;margin:0 0 24px"><?php echo esc_html__('خصم لحد', 'arqamweb'); ?> <strong><?php echo (int) $night['pct']; ?>%</strong> · <?php echo esc_html__('والكميات محدودة.', 'arqamweb'); ?></p>
    <p style="text-align:center;margin:0 0 28px">
        <a href="<?php echo esc_url($url); ?>" style="display:inline-block;padding:14px 32px;border-radius:8px;background:#c8a95b;color:#2d2038;font-weight:700;text-decoration:none"><?php echo esc_html__('ادخل الليلة', 'arqamweb'); ?></a>
    </p>
    <p style="font-size:12px;color:#7a6b85"><?php echo esc_html__('وصلك الإيميل ده لأنك دوست "فكّرني" على صفحة الجمعة البيضاء. ده إيميل واحد بس ومش هنبعتلك تاني عن الليلة دي.', 'arqamweb'); ?></p>
    <?php
    $body = ob_get_clean();

    if (function_exists('WC') && WC()->mailer()) {
        $body = WC()->mailer()->wrap_message($heading, $body);
    }

    if ($switched) {
        restore_previous_locale();
    }
    if ($lang) {
        do_action('wpml_switch_language', null);
    }

    return array('subject' => $subject, 'body' => $body);
}

function elsondos_wf_send_email($to, $email)
{
    if (function_exists('WC') && WC()->mailer()) {
        // WooCommerce inlines its email styles and uses the store's "From" name / address
        return WC()->mailer()->send($to, $email['subject'], $email['body']);
    }

    return wp_mail($to, $email['subject'], $email['body'], array('Content-Type: text/html; charset=UTF-8'));
}

/**
 * Cron job
 */
add_action(ELSONDOS_WF_EMAIL_HOOK, function () {
    if (!elsondos_white_friday_active() || !elsondos_wf_auto_email_enabled()) {
        return;
    }

    $night = elsondos_wf_live_night();
    if (null === $night) {
        return;
    }

    // a slow run must not overlap the next one
    if (get_transient('elsondos_wf_email_lock')) {
        return;
    }
    set_transient('elsondos_wf_email_lock', 1, 9 * MINUTE_IN_SECONDS);

    elsondos_wf_process_reminder_emails($night);

    delete_transient('elsondos_wf_email_lock');
});

/**
 * Send one batch of reminder emails for a night and log the result
 */
function elsondos_wf_process_reminder_emails($night)
{
    global $wpdb;

    $table = elsondos_wf_table();
    $rows  = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE type = 'reminder' AND method = 'email' AND night = %d AND notified_at IS NULL ORDER BY id ASC LIMIT %d",
        $night,
        ELSONDOS_WF_EMAIL_BATCH
    ));

    $sent    = 0;
    $failed  = 0;
    $emails  = array(); // one build per language

    foreach ($rows as $row) {
        $lang = $row->lang ?: '';
        if (!isset($emails[$lang])) {
            $emails[$lang] = elsondos_wf_reminder_email($night, $lang);
        }

        if (elsondos_wf_send_email($row->email, $emails[$lang])) {
            $wpdb->update($table, array('notified_at' => current_time('mysql')), array('id' => $row->id));
            $sent++;
        } else {
            $failed++; // stays "not notified", retried next run while the night is open
        }
    }

    $log = get_option('elsondos_wf_email_log', array());
    update_option('elsondos_wf_email_log', array(
        'last_run' => current_time('mysql'),
        'sent'     => (int) ($log['sent'] ?? 0) + $sent,
        'failed'   => $failed,
    ), false);

    return array('sent' => $sent, 'failed' => $failed);
}
