<?php

/**
 * ArqamWeb Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ArqamWeb
 * @since 1.0.1
 */

/**
 * Define Constants
 */
define('CHILD_THEME_ARQAMWEB_VERSION', '1.1.8');

/**
 * Enqueue styles
 */
function child_enqueue_styles()
{
    // Enqueue Theme Style
    wp_enqueue_style(
        'arqamweb-theme-css',
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-theme-css'), // Astra theme dependency
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );

    // Enqueue Blaze Slider Style
    wp_enqueue_style(
        'blaze-slider-css',
        get_stylesheet_directory_uri() . '/frontend/public/css/blaze.css',
        ['astra-theme-css'],
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );

    // Enqueue Main Custom Style
    wp_enqueue_style(
        'tailwind-css',
        get_stylesheet_directory_uri() . '/frontend/public/style.min.css',
        array('arqamweb-theme-css'), // Load after theme styles
        CHILD_THEME_ARQAMWEB_VERSION,
        'all'
    );
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);



/**
 * Enqueue script
 */
function my_custom_scripts()
{
    wp_enqueue_script(
        'blaze-slider-js',
        get_stylesheet_directory_uri() . '/frontend/public/js/blaze-slider.min.js',
        array(),
        CHILD_THEME_ARQAMWEB_VERSION,
        true
    );


    wp_enqueue_script(
        'main-js',
        get_stylesheet_directory_uri() . '/frontend/public/js/main.js',
        array(),
        CHILD_THEME_ARQAMWEB_VERSION,
        true
    );
}

add_action('wp_enqueue_scripts', 'my_custom_scripts');


require_once(get_stylesheet_directory() . '/inc/features.php');
require_once(get_stylesheet_directory() . '/inc/white-friday.php');
require_once(get_stylesheet_directory() . '/inc/hero-slider.php');
