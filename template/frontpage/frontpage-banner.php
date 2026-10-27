<section class="banner">
    <?php
    // Original image ID
    $image_id = 594; // Replace with your actual image ID

    // Get the translated image ID based on the current language
    $translated_image_id = apply_filters('wpml_object_id', $image_id, 'attachment', true);

    echo wp_get_attachment_image($translated_image_id, 'full'); ?>
</section>