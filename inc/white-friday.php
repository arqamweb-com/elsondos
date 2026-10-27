<?php

/**
 * White Friday campaign
 * - "يصل حتى 20%" corner ribbon on every product card
 * - Hanging tag "عروض الجمعة البيضاء" under the "احدث العروض" menu item
 *
 * To switch the campaign off after White Friday, change the constant to false.
 */
define('ELSONDOS_WHITE_FRIDAY', true);

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
