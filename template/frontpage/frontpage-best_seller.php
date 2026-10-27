<section id="products" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <p class="text-accent text-sm tracking-[0.2em] mb-3"><?php echo __('تسوق الآن', 'arqamweb'); ?></p>
            <h2 class="text-3xl md:text-4xl font-bold text-foreground"><?php echo __('منتجاتنا المميزة', 'arqamweb'); ?></h2>
        </div>

        <?php
        set_query_var('product_cat', 'الاكثر-مبيعاً');
        get_template_part('template/products/products-loop');
        ?>
    </div>
</section>