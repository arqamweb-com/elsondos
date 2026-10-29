<?php

/**
 * Nights timeline ("جدول العروض"), edited from Dashboard → White Friday → Offers schedule
 * Each night has its own open / close time, a title + discount, and either an image or a product.
 * Nights are keyed by a stable id, so the saved reminders keep pointing to the right night
 * when a night is added, removed or reordered.
 */

define('ELSONDOS_WF_NIGHTS_OPTION', 'elsondos_wf_nights');

// the campaign runs on Cairo time, whatever the site timezone is
function elsondos_wf_tz()
{
    return new DateTimeZone('Africa/Cairo');
}

/**
 * The nights that shipped with the campaign, used until the schedule is saved once
 */
function elsondos_wf_default_nights()
{
    $start  = strtotime(ELSONDOS_WF_START);
    $titles = array(__('العطور الرجالي', 'arqamweb'), __('العطور الحريمي', 'arqamweb'), __('الرصاصي وسويس أرابيان', 'arqamweb'), __('الفرصة الأخيرة على كل حاجة', 'arqamweb'));
    $days   = array(__('الجمعة الأولى', 'arqamweb'), __('الجمعة التانية', 'arqamweb'), __('الجمعة التالتة', 'arqamweb'), __('الجمعة الأخيرة', 'arqamweb'));
    $pcts   = array(40, 50, 35, 50);
    $nights = array();

    foreach ($titles as $i => $title) {
        $nights[$i] = array(
            'day'        => $days[$i],
            'title'      => $title,
            'pct'        => $pcts[$i],
            'start'      => $start + $i * DAY_IN_SECONDS,
            'end'        => $start + ($i + 1) * DAY_IN_SECONDS,
            'image_id'   => 0,
            'product_id' => 0,
            'link'       => '',
        );
    }

    return $nights;
}

function elsondos_wf_saved_nights()
{
    $saved = get_option(ELSONDOS_WF_NIGHTS_OPTION, null);

    return is_array($saved) ? $saved : elsondos_wf_default_nights();
}

/**
 * Nights ready for the page, the emails and the Reminders screen, sorted by opening time
 * Texts go through WPML String Translation (domain: arqamweb)
 */
function elsondos_wf_nights()
{
    $nights = array();

    foreach (elsondos_wf_saved_nights() as $id => $n) {
        $product = !empty($n['product_id']) && function_exists('wc_get_product') ? wc_get_product($n['product_id']) : null;

        $day   = apply_filters('wpml_translate_single_string', $n['day'], 'arqamweb', 'wf_night_' . $id . '_day');
        $title = '' !== $n['title'] ? apply_filters('wpml_translate_single_string', $n['title'], 'arqamweb', 'wf_night_' . $id . '_title') : '';

        $image = $n['image_id'] ? wp_get_attachment_image_url($n['image_id'], 'woocommerce_thumbnail') : '';
        $link  = $n['link'];

        if ($product) {
            $title = $title ?: $product->get_name();
            $image = $image ?: wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
            $link  = $link ?: $product->get_permalink();
        }

        $nights[$id] = array(
            'id'    => (int) $id,
            'day'   => $day,
            'date'  => wp_date('l j F', $n['start'], elsondos_wf_tz()),
            'title' => $title,
            'pct'   => (int) $n['pct'],
            'start' => wp_date('c', $n['start'], elsondos_wf_tz()),
            'end'   => wp_date('c', $n['end'], elsondos_wf_tz()),
            'ts'    => (int) $n['start'],
            'te'    => (int) $n['end'],
            'image' => $image ?: '',
            'price' => $product ? elsondos_wf_price_text($product->get_price()) : '',
            'regular' => $product && $product->is_on_sale() ? elsondos_wf_price_text($product->get_regular_price()) : '',
            'link'  => $link,
        );
    }

    uasort($nights, function ($a, $b) {
        return $a['ts'] <=> $b['ts'];
    });

    return $nights;
}

function elsondos_wf_price_text($amount)
{
    return '' === (string) $amount ? '' : html_entity_decode(wp_strip_all_tags(wc_price($amount)), ENT_QUOTES, 'UTF-8');
}

/**
 * Last closing time of the campaign (the nights can run past ELSONDOS_WF_END)
 */
function elsondos_wf_last_night_end()
{
    $ends = wp_list_pluck(elsondos_wf_saved_nights(), 'end');

    return max(array_merge(array(strtotime(ELSONDOS_WF_END)), $ends));
}

if (!is_admin()) {
    return;
}

/**
 * Admin screen
 */
add_action('admin_menu', function () {
    $hook = add_submenu_page('elsondos-wf-reminders', __('Offers schedule', 'arqamweb'), __('Offers schedule', 'arqamweb'), ELSONDOS_WF_CAP, 'elsondos-wf-nights', 'elsondos_wf_render_nights');

    add_action('admin_print_scripts-' . $hook, function () {
        wp_enqueue_media();
        wp_enqueue_script('wc-enhanced-select');
        wp_enqueue_style('woocommerce_admin_styles');
    });
}, 20);

add_action('admin_post_elsondos_wf_save_nights', function () {
    if (!current_user_can(ELSONDOS_WF_CAP)) {
        wp_die(esc_html__('Sorry, you are not allowed to do that.', 'arqamweb'));
    }
    check_admin_referer('elsondos_wf_save_nights');

    $tz     = elsondos_wf_tz();
    $posted = wp_unslash($_POST['nights'] ?? array());
    $nights = array();

    foreach ((array) $posted as $id => $n) {
        $start = date_create_immutable((string) ($n['start'] ?? ''), $tz);
        $end   = date_create_immutable((string) ($n['end'] ?? ''), $tz);

        if (!$start || !$end) {
            continue;
        }

        $id = (int) $id;

        $nights[$id] = array(
            'day'        => sanitize_text_field($n['day'] ?? ''),
            'title'      => sanitize_text_field($n['title'] ?? ''),
            'pct'        => max(0, min(100, (int) ($n['pct'] ?? 0))),
            'start'      => $start->getTimestamp(),
            'end'        => max($end->getTimestamp(), $start->getTimestamp() + MINUTE_IN_SECONDS),
            'image_id'   => absint($n['image_id'] ?? 0),
            'product_id' => absint($n['product_id'] ?? 0),
            'link'       => esc_url_raw($n['link'] ?? ''),
        );

        do_action('wpml_register_single_string', 'arqamweb', 'wf_night_' . $id . '_day', $nights[$id]['day']);
        if ('' !== $nights[$id]['title']) {
            do_action('wpml_register_single_string', 'arqamweb', 'wf_night_' . $id . '_title', $nights[$id]['title']);
        }
    }

    update_option(ELSONDOS_WF_NIGHTS_OPTION, $nights, false);

    wp_safe_redirect(add_query_arg('wf_done', 1, menu_page_url('elsondos-wf-nights', false)));
    exit;
});

function elsondos_wf_night_row($id, $n)
{
    $name    = 'nights[' . $id . ']';
    $product = !empty($n['product_id']) && function_exists('wc_get_product') ? wc_get_product($n['product_id']) : null;
    $image   = $n['image_id'] ? wp_get_attachment_image_url($n['image_id'], 'thumbnail') : '';
    $local   = function ($ts) {
        return $ts ? wp_date('Y-m-d\TH:i', $ts, elsondos_wf_tz()) : '';
    };
    ?>
    <div class="card wf-night-row" style="max-width:none;margin:0 0 12px;padding:12px 16px">
        <table class="form-table" role="presentation" style="margin:0">
            <tr>
                <th><label><?php esc_html_e('Night name', 'arqamweb'); ?></label></th>
                <td><input type="text" class="regular-text" name="<?php echo esc_attr($name); ?>[day]" value="<?php echo esc_attr($n['day']); ?>" placeholder="الجمعة الأولى" required></td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Opens', 'arqamweb'); ?></label></th>
                <td>
                    <input type="datetime-local" name="<?php echo esc_attr($name); ?>[start]" value="<?php echo esc_attr($local($n['start'])); ?>" required>
                    &nbsp;<?php esc_html_e('Closes', 'arqamweb'); ?>&nbsp;
                    <input type="datetime-local" name="<?php echo esc_attr($name); ?>[end]" value="<?php echo esc_attr($local($n['end'])); ?>" required>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Product (optional)', 'arqamweb'); ?></label></th>
                <td>
                    <select class="wc-product-search" style="width:100%;max-width:400px" name="<?php echo esc_attr($name); ?>[product_id]" data-placeholder="<?php esc_attr_e('Search for a product…', 'arqamweb'); ?>" data-action="woocommerce_json_search_products_and_variations" data-allow_clear="true">
                        <?php if ($product) : ?>
                            <option value="<?php echo (int) $product->get_id(); ?>" selected><?php echo esc_html(wp_strip_all_tags($product->get_formatted_name())); ?></option>
                        <?php endif; ?>
                    </select>
                    <p class="description"><?php esc_html_e('When a product is chosen, its name, image, price and link are used unless you fill the fields below.', 'arqamweb'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Offer title', 'arqamweb'); ?></label></th>
                <td><input type="text" class="regular-text" name="<?php echo esc_attr($name); ?>[title]" value="<?php echo esc_attr($n['title']); ?>" placeholder="<?php esc_attr_e('Leave empty to use the product name', 'arqamweb'); ?>"></td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Discount up to (%)', 'arqamweb'); ?></label></th>
                <td><input type="number" min="0" max="100" class="small-text" name="<?php echo esc_attr($name); ?>[pct]" value="<?php echo (int) $n['pct']; ?>"></td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Image', 'arqamweb'); ?></label></th>
                <td>
                    <input type="hidden" name="<?php echo esc_attr($name); ?>[image_id]" value="<?php echo (int) $n['image_id']; ?>" data-image-id>
                    <img src="<?php echo esc_url($image); ?>" alt="" data-image-preview style="width:80px;height:80px;object-fit:cover;border-radius:6px;vertical-align:middle;<?php echo $image ? '' : 'display:none;'; ?>">
                    <button type="button" class="button" data-image-pick><?php esc_html_e('Choose image', 'arqamweb'); ?></button>
                    <button type="button" class="button-link" data-image-remove<?php echo $image ? '' : ' style="display:none"'; ?>><?php esc_html_e('Remove', 'arqamweb'); ?></button>
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e('Button link (optional)', 'arqamweb'); ?></label></th>
                <td><input type="url" class="regular-text" dir="ltr" name="<?php echo esc_attr($name); ?>[link]" value="<?php echo esc_attr($n['link']); ?>" placeholder="<?php esc_attr_e('Product link, or the deals section if empty', 'arqamweb'); ?>"></td>
            </tr>
        </table>
        <p style="margin:8px 0 0;text-align:end"><button type="button" class="button-link button-link-delete" data-night-remove><?php esc_html_e('Remove this night', 'arqamweb'); ?></button></p>
    </div>
    <?php
}

function elsondos_wf_render_nights()
{
    $nights = elsondos_wf_saved_nights();
    uasort($nights, function ($a, $b) {
        return $a['start'] <=> $b['start'];
    });
    $next = $nights ? max(array_keys($nights)) + 1 : 0;

    $blank = array('day' => '', 'title' => '', 'pct' => 0, 'start' => 0, 'end' => 0, 'image_id' => 0, 'product_id' => 0, 'link' => '');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Offers schedule', 'arqamweb'); ?></h1>

        <?php if (!empty($_GET['wf_done'])) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Saved.', 'arqamweb'); ?></p></div>
        <?php endif; ?>

        <p><?php esc_html_e('The nights shown in the "جدول العروض" section of the White Friday page. Times are Cairo time. A night stays locked until it opens, and reminder emails go out once it opens.', 'arqamweb'); ?></p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="elsondos_wf_save_nights">
            <?php wp_nonce_field('elsondos_wf_save_nights'); ?>

            <div data-nights>
                <?php foreach ($nights as $id => $n) {
                    elsondos_wf_night_row($id, $n);
                } ?>
            </div>

            <script type="text/html" id="tmpl-wf-night"><?php elsondos_wf_night_row('__ID__', $blank); ?></script>

            <p>
                <button type="button" class="button" data-night-add data-next="<?php echo (int) $next; ?>"><?php esc_html_e('Add a night', 'arqamweb'); ?></button>
                <?php submit_button(__('Save schedule', 'arqamweb'), 'primary', 'submit', false); ?>
            </p>
        </form>
    </div>

    <script>
    jQuery(function ($) {
        var $list = $('[data-nights]');

        $('[data-night-add]').on('click', function () {
            var id = +this.getAttribute('data-next');
            this.setAttribute('data-next', id + 1);
            $list.append($('#tmpl-wf-night').html().replace(/__ID__/g, id));
            $(document.body).trigger('wc-enhanced-select-init');
        });

        $list.on('click', '[data-night-remove]', function () {
            if (confirm(<?php echo wp_json_encode(__('Remove this night? Reminders already saved for it will no longer show a night.', 'arqamweb')); ?>)) {
                $(this).closest('.wf-night-row').remove();
            }
        });

        $list.on('click', '[data-image-pick]', function () {
            var $row = $(this).closest('td');
            var frame = wp.media({ title: <?php echo wp_json_encode(__('Choose image', 'arqamweb')); ?>, library: { type: 'image' }, multiple: false });
            frame.on('select', function () {
                var img = frame.state().get('selection').first().toJSON();
                $row.find('[data-image-id]').val(img.id);
                $row.find('[data-image-preview]').attr('src', (img.sizes && img.sizes.thumbnail ? img.sizes.thumbnail : img).url).show();
                $row.find('[data-image-remove]').show();
            });
            frame.open();
        });

        $list.on('click', '[data-image-remove]', function () {
            var $row = $(this).closest('td');
            $row.find('[data-image-id]').val(0);
            $row.find('[data-image-preview]').hide();
            $(this).hide();
        });
    });
    </script>
    <?php
}
