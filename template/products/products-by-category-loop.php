<div class="ast-container">
    <?php
    // Custom WooCommerce Product Loop for Products by Category

    if ($args['category']) {
        // Get the current language
        $current_language = apply_filters('wpml_current_language', null);

        // Ensure $args['category'] is the correct category slug
        $category_slug = $args['category'];

        // Retrieve the category term object
        $term = get_term_by('slug', $category_slug, 'product_cat');

        if ($term) {
            // Get the translated category ID
            $translated_term_id = apply_filters('wpml_object_id', $term->term_id, 'product_cat', false, $current_language);

            if ($translated_term_id) {
                // Get the translated category slug
                $translated_term = get_term($translated_term_id, 'product_cat');
                $translated_category_slug = $translated_term->slug;

                // Now use the translated category slug in the query
                $argument = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 6, // Limit the number of products displayed
                    'orderby'        => 'date', // Order by date
                    'order'          => 'DESC', // Descending order
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_cat', // WooCommerce product category taxonomy
                            'field'    => 'slug', // Use 'slug'
                            'terms'    => $translated_category_slug, // Use the translated category slug
                        ),
                    ),
                );
            } else {
                echo 'Translated category not found.';
                return;
            }
        } else {
            echo 'Category not found.';
            return;
        }
    }

    $loop = new WP_Query($argument);

    if ($loop->have_posts()) :
        echo '<ul class="products-container">'; // Add a wrapper class for custom styling
        while ($loop->have_posts()) : $loop->the_post();
            global $product; // Access the product data

            // Display the product
            echo '<li class="product">';
            elsondos_sale_ribbon();
            echo '<a href="' . get_permalink() . '">';

            // Display the product thumbnail with lazy loading
            echo wp_get_attachment_image(
                $product->get_image_id(),
                'sondos-product', // Custom thumbnail size
                false,
                ['loading' => 'lazy', 'alt' => get_the_title($product->get_id())]
            );

            echo '</a>';

            // Display the product title
            echo '<h2 class="product-title">';
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '</h2>';

            // Display the product price
            echo '<p class="product-price">' . $product->get_price_html() . '</p>';

            echo "<a href='?add-to-cart=" . $product->get_id() . "' aria-describedby='woocommerce_loop_add_to_cart_link_describedby_" . $product->get_id() . "' data-quantity='1' class='button product_type_simple add_to_cart_button ajax_add_to_cart' data-product_id='" . $product->get_id() . "' data-product_sku='' rel='nofollow'>" .  __('اضف الي السلة', 'arqamweb') .  "</a>";

            echo '</li>';
        endwhile;
        echo '</ul>';
    else :
        echo __('لم يتم العثور على منتجات في هذه الفئة.', 'arqamweb');
    endif;

    wp_reset_postdata(); // Always reset after the custom loop
    ?>
</div>