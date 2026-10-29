<?php
/**
 * "أقوى عروض الجمعة البيضاء" (concept 1) — products from the "احدث العروض" category
 */
if (!class_exists('WooCommerce')) {
    return;
}

$offers_slugs = apply_filters('elsondos_white_friday_offers_slugs', array('احدث-العروض', 'latest-offers'));

$loop = new WP_Query(array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $offers_slugs,
        ),
    ),
));

$offers_term = get_term_by('slug', $offers_slugs[0], 'product_cat');
$offers_link = $offers_term ? get_term_link(apply_filters('wpml_object_id', $offers_term->term_id, 'product_cat', true), 'product_cat') : '';
$offers_link = (!$offers_link || is_wp_error($offers_link)) ? wc_get_page_permalink('shop') : $offers_link;

$fmt = function ($n) {
    return number_format((float) $n) . ' ' . __('ج.م', 'arqamweb');
};
?>
<!-- DEALS (concept 1) -->
<section class="section" id="deals" style="background: hsl(var(--lavender)); padding-top: 72px;">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow eyebrow--dark"><?php echo __('الأكثر طلباً', 'arqamweb'); ?></span>
            <h2><?php echo __('أقوى عروض الجمعة البيضاء', 'arqamweb'); ?></h2>
            <p><?php echo __('أسعار مخصوص لفترة العروض بس، وبترجع لسعرها بعدها.', 'arqamweb'); ?></p>
        </div>
        <div class="products">
            <?php
            while ($loop->have_posts()) : $loop->the_post();
                $product = wc_get_product(get_the_ID());
                if (!$product) {
                    continue;
                }

                $regular = $product->is_type('variable') ? $product->get_variation_regular_price('min') : $product->get_regular_price();
                $regular = wc_get_price_to_display($product, array('price' => (float) $regular));
                $current = wc_get_price_to_display($product);
                $off     = ($regular > $current && $regular > 0) ? round((1 - $current / $regular) * 100) : 0;

                $brands = get_the_terms($product->get_id(), 'product_brand');
                $brand  = (!empty($brands) && !is_wp_error($brands)) ? $brands[0]->name : '';

                $image = get_the_post_thumbnail_url($product->get_id(), 'woocommerce_single') ?: wc_placeholder_img_src();
                $link  = get_permalink();
                $ajax  = $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart');
            ?>
                <article class="p-card">
                    <div class="p-card__img">
                        <span class="wf-ribbon" aria-hidden="true"><span><?php echo __('تخفيضات', 'arqamweb'); ?></span></span>
                        <?php if ($off) : ?><span class="p-card__off">-<?php echo (int) $off; ?>%</span><?php endif; ?>
                        <a href="<?php echo esc_url($link); ?>"><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" width="700" height="700"></a>
                    </div><div class="p-card__body">
                        <span class="p-card__brand"><?php echo esc_html($brand); ?></span>
                        <h3 class="p-card__title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h3>
                        <div class="p-card__price"><ins><?php echo esc_html($fmt($current)); ?></ins><?php if ($off) : ?><del><?php echo esc_html($fmt($regular)); ?></del><?php endif; ?></div>
                        <?php if ($ajax) : ?>
                            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="p-card__add add_to_cart_button ajax_add_to_cart" data-quantity="1" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" rel="nofollow"><?php echo __('أضف للسلة', 'arqamweb'); ?></a>
                        <?php else : ?>
                            <a href="<?php echo esc_url($link); ?>" class="p-card__add"><?php echo esc_html($product->add_to_cart_text()); ?></a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <div style="text-align:center;margin-top:36px"><a href="<?php echo esc_url($offers_link); ?>" class="btn btn-primary"><?php echo __('شوف كل العروض', 'arqamweb'); ?></a></div>
    </div>
</section>
