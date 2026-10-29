<?php
/**
 * Discount box above the WhatsApp button + the gift box popup
 */
?>
<button type="button" class="wf-discount-fab" data-popup-open aria-haspopup="dialog" aria-controls="wf-discount-popup">
    <span class="wf-discount-fab__icon" aria-hidden="true">🎁</span>
    <span class="wf-discount-fab__txt"><small><?php echo __('الجمعة البيضاء', 'arqamweb'); ?></small><strong><?php echo __('افتح هديتك', 'arqamweb'); ?></strong></span>
</button>

<div class="wf-popup" id="wf-discount-popup" role="dialog" aria-modal="true" aria-labelledby="wf-popup-title" hidden>
    <div class="wf-popup__overlay" data-popup-close></div>
    <div class="wf-popup__box">
        <button type="button" class="wf-popup__close" data-popup-close aria-label="<?php echo esc_attr__('إغلاق', 'arqamweb'); ?>">×</button>
        <span class="eyebrow eyebrow--dark"><?php echo __('الجمعة البيضاء · هدية لكل زائر', 'arqamweb'); ?></span>
        <h2 id="wf-popup-title"><?php echo __('افتح الصندوق', 'arqamweb'); ?> <span class="text-gradient-gold"><?php echo __('واكسب شحن مجاني', 'arqamweb'); ?></span></h2>
        <?php get_template_part('/template/white-friday/gift', 'box'); ?>
    </div>
</div>
