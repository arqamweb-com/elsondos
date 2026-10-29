<?php $slides = elsondos_hero_slides(); ?>
<section class="hero-slider" aria-label="<?php echo esc_attr__('عطور السندس الفاخرة', 'arqamweb'); ?>">
    <?php if (empty($slides)) : ?>
        <div class="hero-slider__slide is-active">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/frontend/assets/hero-perfume.jpg" alt="<?php echo esc_attr__('عطور السندس الفاخرة', 'arqamweb'); ?>">
        </div>
    <?php else : ?>
        <?php foreach ($slides as $index => $slide_id) : ?>
            <div class="hero-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>">
                <?php
                echo wp_get_attachment_image($slide_id, 'full', false, array(
                    'loading'       => 0 === $index ? 'eager' : 'lazy',
                    'fetchpriority' => 0 === $index ? 'high' : 'auto',
                    'sizes'         => '100vw',
                ));
                ?>
            </div>
        <?php endforeach; ?>

        <?php if (count($slides) > 1) : ?>
            <button type="button" class="hero-slider__arrow hero-slider__arrow--prev" aria-label="<?php echo esc_attr__('السابق', 'arqamweb'); ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
            </button>
            <button type="button" class="hero-slider__arrow hero-slider__arrow--next" aria-label="<?php echo esc_attr__('التالي', 'arqamweb'); ?>">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6" /></svg>
            </button>
        <?php endif; ?>
    <?php endif; ?>
</section>
