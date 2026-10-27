<section class="py-20 bg-muted/30">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <p class="text-accent text-sm tracking-[0.2em] mb-3"><?php echo __('تصفح حسب', 'arqamweb'); ?></p>
            <h2 class="text-3xl md:text-4xl font-bold text-foreground"><?php echo __('الأقسام', 'arqamweb'); ?></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">

            <?php

            $categories = [
                [
                    'slug' => 'العطور-الرجالية',
                    'image' => 'category-men.jpeg',
                    'small' => __('قوة وأناقة', 'arqamweb'),
                    'title' => __('عطور رجالية', 'arqamweb'),
                    'delay' => '0s'
                ],
                [
                    'slug' => 'العطور-النسائية',
                    'image' => 'category-women.jpeg',
                    'small' => __('جمال وسحر', 'arqamweb'),
                    'title' => __('عطور نسائية', 'arqamweb'),
                    'delay' => '0.15s'
                ],
                [
                    'slug' => 'للجنسين',
                    'image' => 'category-unisex.jpeg',
                    'small' => __('وصل حديثاً', 'arqamweb'),
                    'title' => __('للجنسين', 'arqamweb'),
                    'delay' => '0.3s'
                ]
            ];

            foreach ($categories as $cat) {

                $link = get_term_link($cat['slug'], 'product_cat');

                if (is_wp_error($link)) {
                    $link = '#';
                }
            ?>

                <a href="<?php echo esc_url($link); ?>">
                    <div class="group relative h-80 md:h-96 rounded-2xl overflow-hidden cursor-pointer animate-fade-up"
                        style="animation-delay: <?php echo esc_attr($cat['delay']); ?>;">

                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/frontend/assets/<?php echo esc_attr($cat['image']); ?>"
                            alt="<?php echo esc_attr($cat['title']); ?>"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent"></div>

                        <div class="absolute inset-0 border border-solid border-accent/0 group-hover:border-accent/30 rounded-2xl transition-all duration-500"></div>

                        <div class="absolute bottom-0 right-0 left-0 p-8 text-primary-foreground">

                            <p class="text-accent text-sm mb-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <?php echo esc_html($cat['small']); ?>
                            </p>

                            <h3 class="text-2xl md:text-3xl font-bold translate-y-4 group-hover:translate-y-0 transition-transform duration-500 text-primary-foreground">
                                <?php echo esc_html($cat['title']); ?>
                            </h3>

                            <div class="w-12 h-0.5 bg-accent mt-4 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-right"></div>

                        </div>

                    </div>
                </a>

            <?php } ?>

        </div>
    </div>
</section>