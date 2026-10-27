<?php



/**

 * The template for displaying all pages.

 *

 * This is the template that displays all pages by default.

 * Please note that this is the WordPress construct of pages

 * and that other 'pages' on your WordPress site may use a

 * different template.

 *

 * @link https://codex.wordpress.org/Template_Hierarchy

 *

 * @package Astra

 * @since 1.0.0

 */



if (!defined('ABSPATH')) {

	exit(); // Exit if accessed directly.

} ?>
<?php get_header(); ?>
<div <?php astra_primary_class(); ?>>


	<div class="min-h-screen">
		<?php get_template_part('/template/frontpage/frontpage', 'slider'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'features'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'brands'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'best_seller'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'categories'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'offers'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'why_us'); ?>
		<?php get_template_part('/template/frontpage/frontpage', 'reviews'); ?>

	</div>


</div><!-- #primary -->

<?php get_footer(); ?>