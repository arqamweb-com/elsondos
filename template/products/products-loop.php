<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 products">
    <style>
        .products .add_to_cart_button {
            background: white;
            padding: 10px 20px;
            border-radius: 999px;
            font-size: 14px;
            opacity: 0;
            transform: translateY(10px);
            transition: all .3s;
        }

        .group:hover .add_to_cart_button {
            opacity: 1;
            transform: translateY(0);
        }
    </style>



    <?php

    $product_cat = get_query_var('product_cat');

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 8,
    );

    if (!empty($product_cat)) {

        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $product_cat,
            ),
        );
    }

    $loop = new WP_Query($args);

    if ($loop->have_posts()) :
        $delay = 0;

        while ($loop->have_posts()) : $loop->the_post();
            global $product;

            $image = get_the_post_thumbnail_url($product->get_id(), 'large');
            $price = $product->get_price_html();
            $link = get_permalink();
            $terms = get_the_terms($product->get_id(), 'product_cat');
    ?>

            <div class="group relative bg-card rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 animate-fade-up"
                style="animation-delay: <?php echo $delay; ?>s;">

                <div class="relative aspect-square overflow-hidden bg-muted">

                    <?php elsondos_sale_ribbon(); ?>

                    <a href="<?php echo $link; ?>">
                        <img src="<?php echo esc_url($image); ?>"
                            alt="<?php the_title(); ?>"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    </a>

                    <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/20 transition-all duration-300 flex items-center justify-center">
                        <?php woocommerce_template_loop_add_to_cart(); ?>
                    </div>

                    <?php if ($product->is_on_sale()) : ?>
                        <span class="wf-badge-swap absolute top-3 right-3 bg-accent text-accent-foreground text-xs px-3 py-1 rounded-full font-medium">
                            <?php
                            if (!empty($terms)) {
                                echo esc_html($terms[0]->name);
                            }
                            ?>
                        </span>
                    <?php endif; ?>

                </div>

                <div class="p-4">

                    <p class="text-muted-foreground text-xs mb-1">
                        <?php
                        if (!empty($terms)) {
                            echo esc_html($terms[0]->name);
                        }
                        ?>
                    </p>

                    <h3 class="font-semibold text-foreground text-sm mb-2 group-hover:text-primary transition-colors">
                        <a class="text-foreground" href="<?php echo $link; ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <div class="flex items-center gap-2 price">
                        <?php echo $price; ?>
                    </div>

                </div>

            </div>

    <?php
            $delay += 0.1;
        endwhile;
        wp_reset_postdata();
    endif;
    ?>

</div>