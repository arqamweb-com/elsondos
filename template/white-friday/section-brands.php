<?php
/**
 * Brands marquee (concept 3) — logos from the product_brand taxonomy instead of names
 */
if (!class_exists('WooCommerce')) {
    return;
}

$brands = get_terms(array(
    'taxonomy'   => 'product_brand',
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
    'hide_empty' => true,
));

if (empty($brands) || is_wp_error($brands)) {
    return;
}

$logos = '';
foreach ($brands as $brand) {
    $thumbnail_id = get_term_meta($brand->term_id, 'thumbnail_id', true);
    if (!$thumbnail_id) {
        continue;
    }

    $logos .= '<a href="' . esc_url(get_term_link($brand)) . '" tabindex="-1">';
    $logos .= '<img src="' . esc_url(wp_get_attachment_url($thumbnail_id)) . '" alt="' . esc_attr($brand->name) . '" loading="lazy">';
    $logos .= '</a>';
}

if (!$logos) {
    return;
}
?>
<!-- BRANDS (concept 3) -->
<div class="brands" aria-hidden="true">
    <div class="brands__track"><?php echo $logos . $logos; // twice for a seamless loop ?></div>
</div>
