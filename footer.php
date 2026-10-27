<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<?php astra_content_bottom(); ?>
</div> <!-- ast-container -->
</div><!-- #content -->

<footer id="footer" class="purple-gradient text-primary-foreground">
	<div class="container mx-auto px-4 py-16">
		<div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-10">

			<div class="col-span-2 md:col-span-1">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/frontend/assets/logo-sondos.png" alt="<?php echo __('السندس', 'arqamweb'); ?>" class="h-14 mb-5 brightness-0 invert">

				<p class="text-primary-foreground/60 text-sm leading-relaxed">
					<?php echo __('وجهتك الأولى للعطور الفاخرة. نقدم أرقى العطور العربية والعالمية بأسعار منافسة.', 'arqamweb'); ?>
				</p>

				<div class="flex gap-3 mt-5">
					<a href="https://www.instagram.com/perfumeselsondos/" class="w-10 h-10 rounded-full bg-primary-foreground/10 flex items-center justify-center text-primary-foreground/60 hover:text-accent hover:bg-primary-foreground/20 transition-all" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram">
							<rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
							<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
							<line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
						</svg>
					</a>
					<a href="https://www.facebook.com/share/1E5SNGPYmg/?mibextid=wwXIfr" class="w-10 h-10 rounded-full bg-primary-foreground/10 flex items-center justify-center text-primary-foreground/60 hover:text-accent hover:bg-primary-foreground/20 transition-all" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 320 512" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram">
							<path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path>
						</svg>
					</a>
					<a href="https://www.tiktok.com/@perfumeselsondos?_r=1&_t=ZS-95BTqayZbea" class="w-10 h-10 rounded-full bg-primary-foreground/10 flex items-center justify-center text-primary-foreground/60 hover:text-accent hover:bg-primary-foreground/20 transition-all" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tiktok">
							<path d="M16.708 0.027c1.745-0.027 3.48-0.011 5.213-0.027 0.105 2.041 0.839 4.12 2.333 5.563 1.491 1.479 3.6 2.156 5.652 2.385v5.369c-1.923-0.063-3.855-0.463-5.6-1.291-0.76-0.344-1.468-0.787-2.161-1.24-0.009 3.896 0.016 7.787-0.025 11.667-0.104 1.864-0.719 3.719-1.803 5.255-1.744 2.557-4.771 4.224-7.88 4.276-1.907 0.109-3.812-0.411-5.437-1.369-2.693-1.588-4.588-4.495-4.864-7.615-0.032-0.667-0.043-1.333-0.016-1.984 0.24-2.537 1.495-4.964 3.443-6.615 2.208-1.923 5.301-2.839 8.197-2.297 0.027 1.975-0.052 3.948-0.052 5.923-1.323-0.428-2.869-0.308-4.025 0.495-0.844 0.547-1.485 1.385-1.819 2.333-0.276 0.676-0.197 1.427-0.181 2.145 0.317 2.188 2.421 4.027 4.667 3.828 1.489-0.016 2.916-0.88 3.692-2.145 0.251-0.443 0.532-0.896 0.547-1.417 0.131-2.385 0.079-4.76 0.095-7.145 0.011-5.375-0.016-10.735 0.025-16.093z"></path>
						</svg>
					</a>
				</div>
			</div>

			<div>
				<h4 class="font-bold mb-4 text-accent text-sm">
					<?php echo __('روابط سريعة', 'arqamweb'); ?>
				</h4>

				<ul class="space-y-2.5 text-sm text-primary-foreground/60">
					<li>
						<a href="<?php echo site_url(); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('الرئيسية', 'arqamweb'); ?>
						</a>
					</li>

					<li>
						<a href="#products" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('المنتجات', 'arqamweb'); ?>
						</a>
					</li>

					<li>
						<a href="#brands" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('الماركات', 'arqamweb'); ?>
						</a>
					</li>

					<li>
						<a href="#offers" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('العروض', 'arqamweb'); ?>
						</a>
					</li>
				</ul>
			</div>

			<div>
				<h4 class="font-bold mb-4 text-accent text-sm">
					<?php echo __('خدمة العملاء', 'arqamweb'); ?>
				</h4>

				<ul class="space-y-2.5 text-sm text-primary-foreground/60">
					<li>
						<a href="<?php echo esc_url(get_permalink(3835)); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('الشروط والأحكام', 'arqamweb'); ?>
						</a>
					</li>
					
					<li>
						<a href="<?php echo esc_url(get_permalink(10)); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('سياسة الاسترجاع', 'arqamweb'); ?>
						</a>
					</li>
					
					<li>
						<a href="<?php echo esc_url(get_permalink(3)); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('سياسة الخصوصية', 'arqamweb'); ?>
						</a>
					</li>

					<li>
						<a href="<?php echo esc_url(get_permalink(293)); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('طلبات الجملة', 'arqamweb'); ?>
						</a>
					</li>

					<li>
						<a href="<?php echo esc_url(get_permalink(3841)); ?>" class="text-primary-foreground/60 hover:text-accent transition-colors">
							<?php echo __('سياسة الشحن', 'arqamweb'); ?>
						</a>
					</li>
				</ul>
			</div>

			<div>
				<h4 class="font-bold mb-4 text-accent text-sm">
					<?php echo __('تواصل معنا', 'arqamweb'); ?>
				</h4>

				<ul class="space-y-3 text-sm text-primary-foreground/60">

					<li class="flex items-center gap-2">
						<span dir="ltr">+201555204158</span>
					</li>

					<li class="flex items-center gap-2">
						<span>info@elsondos.com</span>
					</li>

					<li class="flex items-center gap-2">
						<span><?php echo __('القاهرة : ٢٣ ٢ شارع دولتيان - الخلفاوي', 'arqamweb'); ?></span>
					</li>

					<li class="flex items-center gap-2">
						<span><?php echo __('طنطا : شارع الاستاد سور نادي سيتي كلوب', 'arqamweb'); ?></span>
					</li>

					<li class="flex items-center gap-2">
						<span><?php echo __('٩ ص - ١١ م يومياً', 'arqamweb'); ?></span>
					</li>

					<li class="flex items-center gap-2">
						<span><?php echo __('رقم التسجيل الضريبي:<br> 620-065-798', 'arqamweb'); ?></span>
					</li>

				</ul>
			</div>

			<div>

				<h4 class="font-bold mb-4 text-accent text-sm">
					<?php echo __('النشرة البريدية', 'arqamweb'); ?>
				</h4>

				<p class="text-primary-foreground/60 text-sm mb-3">
					<?php echo __('اشترك ليصلك كل جديد وعروضنا الحصرية', 'arqamweb'); ?>
				</p>

				<div class="flex gap-2">
					<?php echo do_shortcode('[newsletter_form]') ?>
				</div>
			</div>

		</div>

		<div class="border-t border-primary-foreground/10 mt-12 pt-8 text-center text-primary-foreground/40 text-sm" style="border-top-style: solid;">

			<p class="site-credit">
                ©
                <span id="y"><?php echo esc_html(wp_date('Y')); ?></span> <?php echo esc_html(__('El Sondos Perfumes. All rights reserved.', 'arqamweb')) ?>
                <span aria-hidden="true"> · </span>
                <?php echo esc_html(__('Designed and Developed by', 'arqamweb')) ?>
                <a href="https://www.arqamweb.com"
                   class="arqam-credit"><?php echo esc_html(__('Arqam Web', 'arqamweb')) ?></a>

                <style>
                    .arqam-credit {
                        color: white;
                        text-decoration: none;
                    }

                    .arqam-credit:hover {
                        color: hsl(var(--accent));
                    }

                    .arqam-credit:focus-visible {
                        outline: 2px solid gold;
                        outline-offset: 3px;
                    }
                </style>
            </p>

		</div>

	</div>
</footer>
<?php
astra_content_after();

astra_footer_before();

astra_footer();

astra_footer_after();
?>
</div><!-- #page -->
<?php
astra_body_bottom();
wp_footer();
?>
</body>

</html>