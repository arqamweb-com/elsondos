<!-- HERO (concept 2) -->
<section class="hero2">
    <div class="container hero2__in">
        <div>
            <span class="eyebrow eyebrow--dark animate-fade-up d1"><?php echo __('الجمعة البيضاء · هدية لكل زائر', 'arqamweb'); ?></span>
            <h1 class="animate-fade-up d2"><?php echo __('افتح الصندوق', 'arqamweb'); ?><em class="text-gradient-gold"><?php echo __('واكسب شحن مجاني', 'arqamweb'); ?></em></h1>
            <p class="hero2__sub animate-fade-up d3"><?php echo __('كل زائر ليه محاولة واحدة. الصندوق فيه كود شحن مجاني، والكود يشتغل على أي طلب في الموقع.', 'arqamweb'); ?></p>
            <div class="hero2__count animate-fade-up d4">
                <span data-countdown-label><?php echo __('العروض تبدأ خلال', 'arqamweb'); ?></span>
                <div class="countdown countdown--light" data-countdown>
                    <div class="countdown__cell"><div class="countdown__num" data-d>00</div><span class="countdown__label"><?php echo __('يوم', 'arqamweb'); ?></span></div>
                    <div class="countdown__cell"><div class="countdown__num" data-h>00</div><span class="countdown__label"><?php echo __('ساعة', 'arqamweb'); ?></span></div>
                    <div class="countdown__cell"><div class="countdown__num" data-m>00</div><span class="countdown__label"><?php echo __('دقيقة', 'arqamweb'); ?></span></div>
                    <div class="countdown__cell"><div class="countdown__num" data-s>00</div><span class="countdown__label"><?php echo __('ثانية', 'arqamweb'); ?></span></div>
                </div>
            </div>
        </div>

        <?php get_template_part('/template/white-friday/gift', 'box'); ?>
    </div>
</section>
