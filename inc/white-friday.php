<?php

/**
 * White Friday campaign
 * - "يصل حتى 20%" corner ribbon on every product card
 * - Hanging tag "عروض الجمعة البيضاء" under the "احدث العروض" menu item
 *
 * To switch the campaign off after White Friday, change the constant to false.
 */
define('ELSONDOS_WHITE_FRIDAY', true);

// Campaign window (Cairo time), used by the countdowns, the nights and the reminder emails
define('ELSONDOS_WF_START', '2026-11-27T00:00:00+02:00');
define('ELSONDOS_WF_END', '2026-11-30T23:59:59+02:00');

function elsondos_white_friday_active()
{
    return (bool) apply_filters('elsondos_white_friday_active', ELSONDOS_WHITE_FRIDAY);
}

/**
 * Enqueue campaign styles
 */
add_action('wp_enqueue_scripts', function () {
    if (!elsondos_white_friday_active()) {
        return;
    }

    wp_enqueue_style(
        'white-friday-css',
        get_stylesheet_directory_uri() . '/css/white-friday.css',
        array('tailwind-css'),
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );
}, 20);

/**
 * Ribbon markup (also used by the custom loops in template/products)
 */
function elsondos_sale_ribbon()
{
    if (!elsondos_white_friday_active()) {
        return;
    }

    echo '<span class="wf-ribbon" aria-hidden="true"><span>' . esc_html__('يصل حتى 20%', 'arqamweb') . '</span></span>';
}

// WooCommerce loops (shop, categories, search, related products...)
// Priority 7 = right after Astra opens .astra-shop-thumbnail-wrap (priority 6)
add_action('woocommerce_before_shop_loop_item', 'elsondos_sale_ribbon', 7);

/**
 * Hanging tag on the "احدث العروض" menu item
 */
function elsondos_is_offers_menu_item($item)
{
    // the White Friday landing page (any language, WPML points the item to the translated page)
    if ('post_type' === $item->type && 'page' === $item->object) {
        return 'template/white-friday.php' === get_page_template_slug((int) $item->object_id);
    }

    if ('taxonomy' !== $item->type || 'product_cat' !== $item->object) {
        return false;
    }

    // WPML swaps the menu item to the current language's term, so match every translation's slug
    $slugs = apply_filters('elsondos_white_friday_offers_slugs', array('احدث-العروض', 'latest-offers'));
    $term = get_term((int) $item->object_id, 'product_cat');

    return $term && !is_wp_error($term) && in_array(urldecode($term->slug), $slugs, true);
}

add_filter('nav_menu_item_title', function ($title, $item) {
    if (!elsondos_white_friday_active() || !elsondos_is_offers_menu_item($item)) {
        return $title;
    }

    $tag  = '<span class="wf-hang" aria-hidden="true"><span class="wf-hang__swing">';
    $tag .= '<span class="wf-hang__string"></span>';
    $tag .= '<span class="wf-hang__tag">';
    $tag .= '<small>' . esc_html__('عروض', 'arqamweb') . '</small>';
    $tag .= '<strong>' . esc_html__('الجمعة البيضاء', 'arqamweb') . '</strong>';
    $tag .= '</span></span></span>';

    return '<span class="wf-hang-anchor">' . $title . $tag . '</span>';
}, 5, 2);

/**
 * White Friday landing page (template/white-friday.php)
 */
function elsondos_is_white_friday_page()
{
    return is_page_template('template/white-friday.php');
}

add_action('wp_enqueue_scripts', function () {
    // the gift box button (above WhatsApp) shows on every page, so its styles + script load site-wide
    if (!elsondos_is_white_friday_page() && !elsondos_white_friday_active()) {
        return;
    }

    wp_enqueue_style(
        'white-friday-font',
        'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'white-friday-page-css',
        get_stylesheet_directory_uri() . '/css/white-friday-page.css',
        array('tailwind-css'),
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );

    if (elsondos_is_white_friday_page() && wp_script_is('wc-add-to-cart', 'registered')) {
        wp_enqueue_script('wc-add-to-cart');
    }

    wp_enqueue_script(
        'white-friday-page-js',
        get_stylesheet_directory_uri() . '/frontend/public/js/white-friday-page.js',
        array(),
        CHILD_THEME_ARQAMWEB_VERSION,
        true
    );

    wp_localize_script('white-friday-page-js', 'WF_DATA', array(
        'start'  => ELSONDOS_WF_START,
        'end'    => ELSONDOS_WF_END,
        'nights' => array_values(elsondos_wf_nights()),
        'page'   => elsondos_wf_page_url(),
        'ajax'   => admin_url('admin-ajax.php'),
        'lang'   => (string) apply_filters('wpml_current_language', ''),
        // weights are out of 100, the code is created by the El Sondos Free Shipping Coupon plugin
        'prizes' => array(
            array('title' => __('شحن مجاني', 'arqamweb'), 'code' => 'WFSHIP', 'w' => 100),
        ),
        'i18n'   => array(
            'live'        => __('شغالة دلوقتي', 'arqamweb'),
            'done'        => __('خلصت', 'arqamweb'),
            'locked'      => __('🔒 مقفولة', 'arqamweb'),
            'discountUpTo' => __('خصم لحد', 'arqamweb'),
            'remind'      => __('🔔 فكّرني', 'arqamweb'),
            'reminded'    => __('✓ هنفكّرك', 'arqamweb'),
            'enterNight'  => __('ادخل الليلة', 'arqamweb'),
            'remindOn'    => __('هنبعتلك أول ما الليلة تفتح', 'arqamweb'),
            'startsIn'    => __('العروض تبدأ خلال', 'arqamweb'),
            'endsIn'      => __('العروض تنتهي خلال', 'arqamweb'),
            'ended'       => __('انتهت العروض', 'arqamweb'),
            'added'       => __('✓ اتضاف للسلة', 'arqamweb'),
            'addedToast'  => __('تمت الإضافة إلى السلة', 'arqamweb'),
            'copied'      => __('تم نسخ الكود', 'arqamweb'),
            'code'        => __('الكود:', 'arqamweb'),
            'registered'  => __('تم التسجيل، هنبعتلك على واتساب', 'arqamweb'),
            'remindAlready' => __('إنت مسجل بالفعل، هنفكّرك أول ما الليلة تفتح', 'arqamweb'),
            'error'       => __('حصلت مشكلة، جرّب تاني', 'arqamweb'),
            'phoneHint'   => __('رقم الواتساب', 'arqamweb'),
            'emailHint'   => __('الإيميل', 'arqamweb'),
        ),
    ));
}, 20);

// Full width, no sidebar, so the sections can stretch edge to edge
add_filter('astra_get_content_layout', function ($layout) {
    return elsondos_is_white_friday_page() ? 'page-builder' : $layout;
});

add_filter('astra_page_layout', function ($layout) {
    return elsondos_is_white_friday_page() ? 'no-sidebar' : $layout;
});

/**
 * URL of the page that uses the White Friday template (used in the WhatsApp / email messages)
 */
function elsondos_wf_page_url()
{
    $pages = get_pages(array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'template/white-friday.php',
        'number'     => 1,
    ));

    return $pages ? get_permalink($pages[0]) : home_url('/');
}

/**
 * Gift box button above WhatsApp + its popup, on every page while the campaign is on
 */
add_action('wp_footer', function () {
    if (!elsondos_is_white_friday_page() && !elsondos_white_friday_active()) {
        return;
    }

    echo '<div class="wf-page wf-page--fab">';
    get_template_part('/template/white-friday/discount', 'popup');
    echo '<div class="toast" role="status" aria-live="polite" data-toast></div>';
    echo '</div>';
});

require_once(get_stylesheet_directory() . '/inc/white-friday-nights.php');
require_once(get_stylesheet_directory() . '/inc/white-friday-signups.php');
require_once(get_stylesheet_directory() . '/inc/white-friday-emails.php');

if (is_admin()) {
    require_once(get_stylesheet_directory() . '/inc/white-friday-admin.php');
}
