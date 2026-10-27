<section id="brands" class="py-20 bg-muted/50">
    <div class="ast-container">
        <div class="text-center mb-12">
            <p class="text-accent text-sm tracking-[0.2em] mb-3"><?php echo __('تشكيلة مميزة', 'arqamweb'); ?></p>
            <h2 class="text-3xl md:text-4xl font-bold text-foreground"><?php echo __('أشهر الماركات', 'arqamweb'); ?></h2>
        </div>

        <?php
        // Check if WooCommerce is active
        if (class_exists('WooCommerce')) {

            $args = array(
                'taxonomy'      => 'product_brand',
                'orderby'    => 'menu_order',
                'order'      => 'ASC',
                'hide_empty'    => true,
                'number'        => 9,
            );

            $brands = get_terms($args);

            if (!empty($brands) && !is_wp_error($brands)) {

                echo '<div class="brands grid grid-cols-2 md:grid-cols-3 gap-6 max-w-4xl mx-auto">';

                foreach ($brands as $brand) {

                    $brand_link = get_term_link($brand);
                    $thumbnail_id  = get_term_meta($brand->term_id, 'thumbnail_id', true);
                    $thumbnail_url = $thumbnail_id
                        ? wp_get_attachment_url($thumbnail_id)
                        : wc_placeholder_img_src();

                    echo '<a class="brand" href="' . esc_url($brand_link) . '">';
                    echo '<div class="glass-card rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-105 cursor-pointer p-6 h-32">';
                    echo '<img class="max-w-[140px] max-h-[80px] object-contain" src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr($brand->name) . '" loading="lazy">';
                    echo '</div>';
                    echo '</a>';
                }

                echo '</div>';
            } else {
                echo '<p>No brands found.</p>';
            }
        } else {
            echo '<p>WooCommerce is not active.</p>';
        }
        ?>
    </div>
</section>