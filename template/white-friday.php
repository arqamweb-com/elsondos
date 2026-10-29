<?php /* Template Name: White Friday */ ?>
<?php
/**
 * White Friday landing page
 * Sections are taken as-is from the 4 campaign concepts, each one lives in template/white-friday/
 *
 * @package Astra
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<main class="wf-page" id="top">
    <?php get_template_part('/template/white-friday/section', 'hero'); ?>
    <?php get_template_part('/template/white-friday/section', 'brands'); ?>
    <?php get_template_part('/template/white-friday/section', 'categories'); ?>
    <?php get_template_part('/template/white-friday/section', 'nights'); ?>
    <?php get_template_part('/template/white-friday/section', 'deals'); ?>
    <?php get_template_part('/template/white-friday/section', 'early'); ?>
    <?php get_template_part('/template/white-friday/section', 'trust'); ?>
</main>

<div class="wf-page">
    <?php get_template_part('/template/white-friday/discount', 'popup'); ?>
    <?php get_template_part('/template/white-friday/reminder', 'popup'); ?>
    <div class="toast" role="status" aria-live="polite" data-toast></div>
</div>

<?php get_footer(); ?>
