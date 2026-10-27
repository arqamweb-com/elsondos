<?php

/**
 * Add Custom Size to Product image
 */
add_image_size('sondos-product', 1000, 714, false);


/**
 * Remove Flex in Container
 */
add_filter('astra_apply_flex_based_css', '__return_false');

/**
 * Add Svg To Media Library
 */

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype($filename, $mimes);
    return [
        'ext' => $filetype['ext'],
        'type' => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];
}, 10, 4);

function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function fix_svg()
{
    echo '<style type="text/css">
          .attachment-266x266, .thumbnail img {
               width: 100% !important;
               height: auto !important;
          }
          </style>';
}
add_action('admin_head', 'fix_svg');


// Remove Fields In Checkout Page
add_filter('woocommerce_checkout_fields', 'customize_checkout_fields');

function customize_checkout_fields($fields)
{

    unset($fields['billing']['billing_postcode']); // Remove billing postal code
    unset($fields['shipping']['shipping_postcode']); // Remove shipping postal code


    // Change placeholder for mobile number field
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['placeholder'] = __('رقم الموبايل', 'arqamweb');
        $fields['billing']['billing_phone']['priority'] = 21; // Change priority to 21
    }

    // Add a new field for "Land Mobile"
    $fields['billing']['billing_land_mobile'] = array(
        'type'        => 'tel',
        'label'       => __('رقم اخر / رقم ارضي', 'arqamweb'),
        'placeholder' => __('رقم اخر / رقم ارضي', 'arqamweb'),
        'required'    => false, // Set to true if the field is mandatory
        'class'       => array('form-row-wide'),
        'priority'    => 26, // Positioning order
    );

    return $fields;
}

add_action('woocommerce_checkout_update_order_meta', 'save_land_mobile_field');

function save_land_mobile_field($order_id)
{
    if (!empty($_POST['billing_land_mobile'])) {
        update_post_meta($order_id, '_billing_land_mobile', sanitize_text_field($_POST['billing_land_mobile']));
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_land_mobile_in_admin', 10, 1);

function display_land_mobile_in_admin($order)
{
    $land_mobile = get_post_meta($order->get_id(), '_billing_land_mobile', true);
    if (!empty($land_mobile)) {
        echo '<p><strong>Landline Number:</strong> ' . esc_html($land_mobile) . '</p>';
    }
}



// add image quality to product

add_action('woocommerce_single_product_summary', 'add_dynamic_image_before_short_description', 19);
// add_action('woocommerce_before_add_to_cart_button', 'add_image_before_add_to_cart');

function add_dynamic_image_before_short_description()
{
    echo '<div class="quality-product">';
    echo '<h4>';
    echo __('علامات جودة المنتج', 'arqamweb');
    echo '</h4>';
    echo '<div class="images" style="margin-bottom:0;">';
    echo '<div><img src="https://elsondosperfumes.com/wp-content/uploads/2025/01/51MX4cmO0L._AC__upscayl_3x_realesrgan-x4plus.png" alt="Custom Image" class="before-add-to-cart-image"></div>';
    echo '<div><img src="https://elsondosperfumes.com/wp-content/uploads/2025/01/download-1_upscayl_4x_realesrgan-x4plus.png" alt="Custom Image" class="before-add-to-cart-image"></div>';
    echo '<div><img src="https://elsondosperfumes.com/wp-content/uploads/2025/01/ISO_9001-2015_w.png" alt="Custom Image" class="before-add-to-cart-image"></div>';
    echo '<div><img src="https://elsondosperfumes.com/wp-content/uploads/2025/01/sfda-black.png" alt="Custom Image" class="before-add-to-cart-image"></div>';
    echo '<div><img src="https://elsondosperfumes.com/wp-content/uploads/2025/01/تنزيل.png" alt="Custom Image" class="before-add-to-cart-image"></div>';
    echo '</div>';
    echo '</div>';
}


function filter_search_for_products($query)
{
    if (!is_admin() && $query->is_search() && $query->is_main_query()) {
        $query->set('post_type', 'product'); // Only show WooCommerce products
    }
}
add_action('pre_get_posts', 'filter_search_for_products');


/*
** Fix slug Brands in product_brand taxonomy 
*/
add_filter('register_taxonomy_args', function ($args, $taxonomy) {

    if ($taxonomy === 'product_brand') {
        $args['rewrite'] = [
            'slug'       => 'brand',
            'with_front' => false,
        ];
    }

    return $args;
}, 20, 2);
