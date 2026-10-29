<?php

/**
 * Front page hero slider
 * - 3 images picked from Appearance > Customize > "سلايدر الصفحة الرئيسية"
 * - images only, cross-fade between them (see main.js)
 */
define('ELSONDOS_HERO_SLIDES', 3);

/**
 * Customizer section with one image picker per slide
 */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('elsondos_hero_slider', array(
        'title'    => __('سلايدر الصفحة الرئيسية', 'arqamweb'),
        'priority' => 30,
    ));

    for ($i = 1; $i <= ELSONDOS_HERO_SLIDES; $i++) {
        $wp_customize->add_setting('elsondos_hero_slide_' . $i, array(
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'elsondos_hero_slide_' . $i, array(
            'label'     => sprintf(__('صورة السلايد %d', 'arqamweb'), $i),
            'section'   => 'elsondos_hero_slider',
            'mime_type' => 'image',
        )));
    }
});

/**
 * Attachment IDs of the slides that have an image
 */
function elsondos_hero_slides()
{
    $slides = array();

    for ($i = 1; $i <= ELSONDOS_HERO_SLIDES; $i++) {
        $id = absint(get_theme_mod('elsondos_hero_slide_' . $i));

        if ($id && wp_attachment_is_image($id)) {
            $slides[] = $id;
        }
    }

    return $slides;
}

/**
 * Enqueue slider styles
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_front_page()) {
        return;
    }

    wp_enqueue_style(
        'hero-slider-css',
        get_stylesheet_directory_uri() . '/css/hero-slider.css',
        array('tailwind-css'),
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );
}, 20);
