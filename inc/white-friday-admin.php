<?php

/**
 * Dashboard → White Friday → Reminders / Early Access
 * English source strings, translated to Arabic with WPML String Translation (domain: arqamweb).
 * Customer-facing messages (WhatsApp / email) stay in Arabic.
 */

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

define('ELSONDOS_WF_CAP', 'manage_woocommerce');

/**
 * Message sent to a customer, prefilled in WhatsApp / email
 */
function elsondos_wf_customer_message($row)
{
    $url = elsondos_wf_page_url();

    if ('reminder' === $row->type) {
        $nights = elsondos_wf_nights();
        $night  = $nights[(int) $row->night] ?? null;

        if ($night) {
            /* translators: 1: night name, 2: category, 3: discount %, 4: page URL */
            return sprintf(__('أهلاً! %1$s من عروض الجمعة البيضاء في السندس فتحت دلوقتي: %2$s بخصم لحد %3$s%%. ادخل من هنا: %4$s', 'arqamweb'), $night['day'], $night['title'], $night['pct'], $url);
        }
    }

    /* translators: 1: customer name, 2: page URL */
    return sprintf(__('أهلاً %1$s! ده لينك عروض الجمعة البيضاء الخاص بيك قبل الافتتاح بساعتين: %2$s', 'arqamweb'), $row->name, $url);
}

function elsondos_wf_whatsapp_link($row)
{
    return 'https://wa.me/' . elsondos_wf_whatsapp_number($row->phone) . '?text=' . rawurlencode(elsondos_wf_customer_message($row));
}

function elsondos_wf_email_link($row)
{
    return 'mailto:' . $row->email . '?subject=' . rawurlencode(__('عروض الجمعة البيضاء في السندس', 'arqamweb')) . '&body=' . rawurlencode(elsondos_wf_customer_message($row));
}

/**
 * Filters shared by the table and the CSV export
 */
function elsondos_wf_query_where($type)
{
    global $wpdb;

    $where = $wpdb->prepare('WHERE type = %s', $type);

    if (isset($_REQUEST['night']) && '' !== $_REQUEST['night']) {
        $where .= $wpdb->prepare(' AND night = %d', (int) $_REQUEST['night']);
    }

    $method = sanitize_key($_REQUEST['method'] ?? '');
    if ($method) {
        $where .= $wpdb->prepare(' AND method = %s', $method);
    }

    $status = sanitize_key($_REQUEST['status'] ?? '');
    if ('notified' === $status) {
        $where .= ' AND notified_at IS NOT NULL';
    } elseif ('pending' === $status) {
        $where .= ' AND notified_at IS NULL';
    }

    $search = trim(wp_unslash($_REQUEST['s'] ?? ''));
    if ('' !== $search) {
        $like   = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(' AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)', $like, $like, $like);
    }

    return $where;
}

class Elsondos_WF_Signups_Table extends WP_List_Table
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;

        parent::__construct(array(
            'singular' => 'signup',
            'plural'   => 'signups',
            'ajax'     => false,
        ));
    }

    public function get_columns()
    {
        $columns = array('cb' => '<input type="checkbox">');

        if ('reminder' === $this->type) {
            $columns['contact'] = __('Contact', 'arqamweb');
            $columns['method']  = __('Method', 'arqamweb');
            $columns['night']   = __('Night', 'arqamweb');
        } else {
            $columns['name']     = __('Name', 'arqamweb');
            $columns['contact']  = __('WhatsApp', 'arqamweb');
            $columns['interest'] = __('Looking for', 'arqamweb');
        }

        $columns['lang']        = __('Language', 'arqamweb');
        $columns['notified_at'] = __('Status', 'arqamweb');
        $columns['created_at']  = __('Date', 'arqamweb');

        return $columns;
    }

    // the row actions live on the contact column
    protected function get_default_primary_column_name()
    {
        return 'contact';
    }

    protected function get_sortable_columns()
    {
        return array(
            'created_at' => array('created_at', true),
            'night'      => array('night', false),
        );
    }

    protected function get_bulk_actions()
    {
        return array(
            'notified' => __('Mark as notified', 'arqamweb'),
            'pending'  => __('Mark as not notified', 'arqamweb'),
            'delete'   => __('Delete', 'arqamweb'),
        );
    }

    public function no_items()
    {
        _e('No signups yet.', 'arqamweb');
    }

    public function prepare_items()
    {
        global $wpdb;

        $table    = elsondos_wf_table();
        $where    = elsondos_wf_query_where($this->type);
        $per_page = 30;
        $paged    = $this->get_pagenum();

        $orderby = in_array($_REQUEST['orderby'] ?? '', array('created_at', 'night'), true) ? $_REQUEST['orderby'] : 'created_at';
        $order   = 'asc' === strtolower($_REQUEST['order'] ?? '') ? 'ASC' : 'DESC';

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} {$where}");

        $this->items = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} {$where} ORDER BY {$orderby} {$order}, id DESC LIMIT %d OFFSET %d",
            $per_page,
            ($paged - 1) * $per_page
        ));

        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $this->set_pagination_args(array(
            'total_items' => $total,
            'per_page'    => $per_page,
        ));
    }

    protected function extra_tablenav($which)
    {
        if ('top' !== $which) {
            return;
        }

        $night  = $_REQUEST['night'] ?? '';
        $method = sanitize_key($_REQUEST['method'] ?? '');
        $status = sanitize_key($_REQUEST['status'] ?? '');

        echo '<div class="alignleft actions">';

        if ('reminder' === $this->type) {
            echo '<select name="night"><option value="">' . esc_html__('All nights', 'arqamweb') . '</option>';
            foreach (elsondos_wf_nights() as $i => $n) {
                printf('<option value="%d"%s>%s</option>', $i, selected((string) $night, (string) $i, false), esc_html($n['day'] . ' – ' . $n['title']));
            }
            echo '</select>';

            echo '<select name="method"><option value="">' . esc_html__('All methods', 'arqamweb') . '</option>';
            printf('<option value="whatsapp"%s>%s</option>', selected($method, 'whatsapp', false), esc_html__('WhatsApp', 'arqamweb'));
            printf('<option value="email"%s>%s</option>', selected($method, 'email', false), esc_html__('Email', 'arqamweb'));
            echo '</select>';
        }

        echo '<select name="status"><option value="">' . esc_html__('All statuses', 'arqamweb') . '</option>';
        printf('<option value="pending"%s>%s</option>', selected($status, 'pending', false), esc_html__('Not notified', 'arqamweb'));
        printf('<option value="notified"%s>%s</option>', selected($status, 'notified', false), esc_html__('Notified', 'arqamweb'));
        echo '</select>';

        submit_button(__('Filter', 'arqamweb'), '', 'filter_action', false);

        $export = wp_nonce_url(add_query_arg(array(
            'action' => 'elsondos_wf_export',
            'type'   => $this->type,
            'night'  => $night,
            'method' => $method,
            'status' => $status,
            's'      => wp_unslash($_REQUEST['s'] ?? ''),
        ), admin_url('admin-post.php')), 'elsondos_wf_export');

        echo ' <a class="button" href="' . esc_url($export) . '">' . esc_html__('Export CSV', 'arqamweb') . '</a>';
        echo '</div>';
    }

    protected function column_cb($item)
    {
        return '<input type="checkbox" name="ids[]" value="' . (int) $item->id . '">';
    }

    protected function column_contact($item)
    {
        $is_email = 'email' === $item->method;
        $contact  = $is_email ? $item->email : $item->phone;
        $link     = $is_email ? elsondos_wf_email_link($item) : elsondos_wf_whatsapp_link($item);
        $page_url = menu_page_url($_REQUEST['page'] ?? '', false);

        $action_url = function ($action) use ($item, $page_url) {
            return wp_nonce_url(add_query_arg(array('wf_action' => $action, 'id' => $item->id), $page_url), 'elsondos_wf_row_' . $item->id);
        };

        $actions = array(
            'send' => '<a href="' . esc_url($link) . '" target="_blank" rel="noopener">' . esc_html($is_email ? __('Send email', 'arqamweb') : __('Send on WhatsApp', 'arqamweb')) . '</a>',
        );

        if ($item->notified_at) {
            $actions['pending'] = '<a href="' . esc_url($action_url('pending')) . '">' . esc_html__('Mark as not notified', 'arqamweb') . '</a>';
        } else {
            $actions['notified'] = '<a href="' . esc_url($action_url('notified')) . '">' . esc_html__('Mark as notified', 'arqamweb') . '</a>';
        }

        $actions['delete'] = '<a href="' . esc_url($action_url('delete')) . '" class="submitdelete" onclick="return confirm(\'' . esc_js(__('Delete this signup?', 'arqamweb')) . '\');">' . esc_html__('Delete', 'arqamweb') . '</a>';

        return '<strong dir="ltr">' . esc_html($contact) . '</strong>' . $this->row_actions($actions);
    }

    protected function column_method($item)
    {
        return 'email' === $item->method ? esc_html__('Email', 'arqamweb') : esc_html__('WhatsApp', 'arqamweb');
    }

    protected function column_night($item)
    {
        $nights = elsondos_wf_nights();
        $night  = $nights[(int) $item->night] ?? null;

        return $night ? esc_html($night['day']) . '<br><small>' . esc_html($night['title'] . ' · ' . $night['date']) . '</small>' : '—';
    }

    protected function column_notified_at($item)
    {
        if (!$item->notified_at) {
            return '<span style="color:#b26200">' . esc_html__('Not notified', 'arqamweb') . '</span>';
        }

        return '<span style="color:#008a20">' . esc_html__('Notified', 'arqamweb') . '</span><br><small>' . esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $item->notified_at)) . '</small>';
    }

    protected function column_created_at($item)
    {
        return esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $item->created_at));
    }

    protected function column_default($item, $column_name)
    {
        return esc_html($item->$column_name ?? '') ?: '—';
    }
}

/**
 * Menu
 */
add_action('admin_menu', function () {
    add_menu_page(
        __('White Friday', 'arqamweb'),
        __('White Friday', 'arqamweb'),
        ELSONDOS_WF_CAP,
        'elsondos-wf-reminders',
        'elsondos_wf_render_reminders',
        'dashicons-megaphone',
        56
    );

    $reminders = add_submenu_page('elsondos-wf-reminders', __('Reminders', 'arqamweb'), __('Reminders', 'arqamweb'), ELSONDOS_WF_CAP, 'elsondos-wf-reminders', 'elsondos_wf_render_reminders');
    $early     = add_submenu_page('elsondos-wf-reminders', __('Early Access', 'arqamweb'), __('Early Access', 'arqamweb'), ELSONDOS_WF_CAP, 'elsondos-wf-early', 'elsondos_wf_render_early');

    add_action('load-' . $reminders, 'elsondos_wf_handle_actions');
    add_action('load-' . $early, 'elsondos_wf_handle_actions');
});

/**
 * Row + bulk actions, then redirect back so a refresh doesn't repeat them
 */
function elsondos_wf_handle_actions()
{
    global $wpdb;

    if (!current_user_can(ELSONDOS_WF_CAP)) {
        return;
    }

    $ids    = array();
    $action = '';

    if (!empty($_GET['wf_action']) && !empty($_GET['id'])) {
        $id = (int) $_GET['id'];
        check_admin_referer('elsondos_wf_row_' . $id);
        $ids    = array($id);
        $action = sanitize_key($_GET['wf_action']);
    } elseif (!empty($_REQUEST['ids'])) {
        $bulk = sanitize_key($_REQUEST['action'] ?? '');
        $bulk = ('-1' === $bulk || '' === $bulk) ? sanitize_key($_REQUEST['action2'] ?? '') : $bulk;
        if (!$bulk || '-1' === $bulk) {
            return;
        }
        check_admin_referer('bulk-signups');
        $ids    = array_map('intval', (array) $_REQUEST['ids']);
        $action = $bulk;
    }

    if (!$ids || !in_array($action, array('notified', 'pending', 'delete'), true)) {
        return;
    }

    $table = elsondos_wf_table();
    $in    = implode(',', array_map('intval', $ids));

    if ('delete' === $action) {
        $wpdb->query("DELETE FROM {$table} WHERE id IN ({$in})");
    } elseif ('notified' === $action) {
        $wpdb->query($wpdb->prepare("UPDATE {$table} SET notified_at = %s WHERE id IN ({$in})", current_time('mysql')));
    } else {
        $wpdb->query("UPDATE {$table} SET notified_at = NULL WHERE id IN ({$in})");
    }

    wp_safe_redirect(add_query_arg('wf_done', count($ids), remove_query_arg(array('wf_action', 'id', '_wpnonce', '_wp_http_referer', 'action', 'action2', 'ids', 'wf_done'))));
    exit;
}

/**
 * Number of reminders per night, shown above the Reminders table
 */
function elsondos_wf_render_night_counts()
{
    global $wpdb;

    $table  = elsondos_wf_table();
    $counts = $wpdb->get_results("SELECT night, COUNT(*) AS total, SUM(notified_at IS NULL) AS pending FROM {$table} WHERE type = 'reminder' GROUP BY night", OBJECT_K);

    echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin:16px 0">';
    foreach (elsondos_wf_nights() as $i => $n) {
        $total   = isset($counts[$i]) ? (int) $counts[$i]->total : 0;
        $pending = isset($counts[$i]) ? (int) $counts[$i]->pending : 0;
        $url     = add_query_arg(array('night' => $i, 'status' => 'pending'), menu_page_url('elsondos-wf-reminders', false));

        echo '<div class="card" style="margin:0;max-width:none;padding:12px 16px">';
        echo '<strong>' . esc_html($n['day']) . '</strong> <small>(' . esc_html($n['date']) . ')</small>';
        echo '<div style="font-size:24px;font-weight:700;margin:6px 0">' . (int) $total . '</div>';
        /* translators: %d: reminders not notified yet */
        echo '<a href="' . esc_url($url) . '">' . esc_html(sprintf(__('%d not notified yet', 'arqamweb'), $pending)) . '</a>';
        echo '</div>';
    }
    echo '</div>';
}

/**
 * Automatic emails: status, on/off, test email
 */
function elsondos_wf_render_email_box()
{
    $enabled = elsondos_wf_auto_email_enabled();
    $log     = get_option('elsondos_wf_email_log', array());
    $next    = wp_next_scheduled(ELSONDOS_WF_EMAIL_HOOK);
    $live    = elsondos_wf_live_night();
    $nights  = elsondos_wf_nights();
    $format  = get_option('date_format') . ' ' . get_option('time_format');

    $toggle = wp_nonce_url(add_query_arg(array('action' => 'elsondos_wf_toggle_email'), admin_url('admin-post.php')), 'elsondos_wf_toggle_email');
    $test   = wp_nonce_url(add_query_arg(array('action' => 'elsondos_wf_test_email'), admin_url('admin-post.php')), 'elsondos_wf_test_email');
    ?>
    <div class="card" style="max-width:none;margin:16px 0;padding:12px 16px">
        <h2 style="margin:0 0 8px;font-size:15px"><?php esc_html_e('Automatic reminder emails', 'arqamweb'); ?></h2>
        <p style="margin:0 0 8px">
            <?php esc_html_e('While a night is open, everyone who chose email for it gets the reminder automatically (checked every 10 minutes) and is marked as notified. WhatsApp reminders are sent by hand.', 'arqamweb'); ?>
        </p>
        <p style="margin:0 0 12px">
            <strong><?php esc_html_e('Status:', 'arqamweb'); ?></strong>
            <?php if ($enabled) : ?>
                <span style="color:#008a20"><?php esc_html_e('On', 'arqamweb'); ?></span>
            <?php else : ?>
                <span style="color:#b32d2e"><?php esc_html_e('Paused', 'arqamweb'); ?></span>
            <?php endif; ?>
            &nbsp;·&nbsp;
            <strong><?php esc_html_e('Open night:', 'arqamweb'); ?></strong>
            <?php echo null === $live ? esc_html__('None right now', 'arqamweb') : esc_html($nights[$live]['day'] . ' – ' . $nights[$live]['title']); ?>
            &nbsp;·&nbsp;
            <strong><?php esc_html_e('Next check:', 'arqamweb'); ?></strong>
            <?php echo $next ? esc_html(get_date_from_gmt(gmdate('Y-m-d H:i:s', $next), $format)) : '—'; ?>
            &nbsp;·&nbsp;
            <strong><?php esc_html_e('Emails sent:', 'arqamweb'); ?></strong>
            <?php echo (int) ($log['sent'] ?? 0); ?>
            <?php if (!empty($log['failed'])) : ?>
                <span style="color:#b32d2e">(<?php echo esc_html(sprintf(__('%d failed in the last run', 'arqamweb'), (int) $log['failed'])); ?>)</span>
            <?php endif; ?>
        </p>
        <a class="button" href="<?php echo esc_url($toggle); ?>"><?php echo $enabled ? esc_html__('Pause automatic emails', 'arqamweb') : esc_html__('Turn automatic emails on', 'arqamweb'); ?></a>
        <a class="button" href="<?php echo esc_url($test); ?>"><?php esc_html_e('Send me a test email', 'arqamweb'); ?></a>
    </div>
    <?php
}

add_action('admin_post_elsondos_wf_toggle_email', function () {
    if (!current_user_can(ELSONDOS_WF_CAP)) {
        wp_die(esc_html__('Sorry, you are not allowed to do that.', 'arqamweb'));
    }
    check_admin_referer('elsondos_wf_toggle_email');

    update_option('elsondos_wf_auto_email', elsondos_wf_auto_email_enabled() ? 'off' : 'on');

    wp_safe_redirect(add_query_arg('wf_done', 1, menu_page_url('elsondos-wf-reminders', false)));
    exit;
});

add_action('admin_post_elsondos_wf_test_email', function () {
    if (!current_user_can(ELSONDOS_WF_CAP)) {
        wp_die(esc_html__('Sorry, you are not allowed to do that.', 'arqamweb'));
    }
    check_admin_referer('elsondos_wf_test_email');

    $night = elsondos_wf_live_night() ?? array_key_first(elsondos_wf_nights());
    $email = elsondos_wf_reminder_email($night, (string) apply_filters('wpml_current_language', ''));
    $sent  = elsondos_wf_send_email(wp_get_current_user()->user_email, $email);

    wp_safe_redirect(add_query_arg('wf_test', $sent ? 1 : 0, menu_page_url('elsondos-wf-reminders', false)));
    exit;
});

function elsondos_wf_render_page($type, $title, $intro)
{
    $table = new Elsondos_WF_Signups_Table($type);
    $table->prepare_items();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
        <hr class="wp-header-end">

        <?php if (!empty($_GET['wf_done'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Done.', 'arqamweb'); ?></p></div>
        <?php endif; ?>

        <?php if (isset($_GET['wf_test'])) : ?>
            <?php if ('1' === $_GET['wf_test']) : ?>
                <div class="notice notice-success is-dismissible"><p><?php echo esc_html(sprintf(__('Test email sent to %s.', 'arqamweb'), wp_get_current_user()->user_email)); ?></p></div>
            <?php else : ?>
                <div class="notice notice-error is-dismissible"><p><?php esc_html_e('The test email could not be sent. Check the site\'s email (SMTP) settings.', 'arqamweb'); ?></p></div>
            <?php endif; ?>
        <?php endif; ?>

        <p><?php echo esc_html($intro); ?></p>

        <?php if ('reminder' === $type) {
            elsondos_wf_render_email_box();
            elsondos_wf_render_night_counts();
        } ?>

        <form method="get">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page'] ?? ''); ?>">
            <?php
            $table->search_box(__('Search', 'arqamweb'), 'wf-search');
            $table->display();
            ?>
        </form>
    </div>
    <?php
}

function elsondos_wf_render_reminders()
{
    elsondos_wf_render_page(
        'reminder',
        __('Reminders', 'arqamweb'),
        __('Customers who pressed "Remind me" on a White Friday night. Email reminders go out automatically when the night opens. For WhatsApp, use "Send on WhatsApp" on each one and mark them as notified.', 'arqamweb')
    );
}

function elsondos_wf_render_early()
{
    elsondos_wf_render_page(
        'early',
        __('Early Access', 'arqamweb'),
        __('Customers who signed up to get the offers link two hours before the opening.', 'arqamweb')
    );
}

/**
 * Names come from visitors, so stop Excel from running them as formulas
 */
function elsondos_wf_csv_cell($value)
{
    $value = (string) $value;

    if ('' !== $value && preg_match('/^[=@\t\r]|^[+\-](?!\d+$)/', $value)) {
        return "'" . $value;
    }

    return $value;
}

/**
 * CSV export (same filters as the table)
 */
add_action('admin_post_elsondos_wf_export', function () {
    global $wpdb;

    if (!current_user_can(ELSONDOS_WF_CAP)) {
        wp_die(esc_html__('Sorry, you are not allowed to do that.', 'arqamweb'));
    }
    check_admin_referer('elsondos_wf_export');

    $type   = 'early' === ($_GET['type'] ?? '') ? 'early' : 'reminder';
    $table  = elsondos_wf_table();
    $where  = elsondos_wf_query_where($type);
    $rows   = $wpdb->get_results("SELECT * FROM {$table} {$where} ORDER BY created_at DESC");
    $nights = elsondos_wf_nights();

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="white-friday-' . $type . '-' . gmdate('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel shows Arabic correctly

    fputcsv($out, array('Name', 'Method', 'Phone', 'WhatsApp', 'Email', 'Night', 'Category', 'Looking for', 'Language', 'Notified at', 'Date'));

    foreach ($rows as $row) {
        $night = null !== $row->night && isset($nights[(int) $row->night]) ? $nights[(int) $row->night] : null;

        fputcsv($out, array_map('elsondos_wf_csv_cell', array(
            $row->name,
            $row->method,
            $row->phone,
            $row->phone ? elsondos_wf_whatsapp_number($row->phone) : '',
            $row->email,
            $night ? $night['day'] : '',
            $night ? $night['title'] : '',
            $row->interest,
            $row->lang,
            $row->notified_at,
            $row->created_at,
        )));
    }

    fclose($out);
    exit;
});
