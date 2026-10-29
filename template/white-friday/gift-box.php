<?php
/**
 * Gift box (concept 2) — shared by the hero and the discount popup.
 * Both boxes read the same saved prize, so every visitor gets one try only.
 */
?>
<div class="gift-stage" data-wf-gift>
    <div class="gift-stage__glow"></div>
    <div class="confetti" data-gift-confetti></div>
    <button class="gift" type="button" data-gift-box aria-label="<?php echo esc_attr__('افتح صندوق الهدية', 'arqamweb'); ?>">
        <span class="gift__lid"><span class="gift__ribbon-v"></span><span class="gift__bow"></span></span>
        <span class="gift__body"></span>
        <span class="gift__ribbon-v"></span>
        <span class="gift__label"><?php echo __('دوس افتحه', 'arqamweb'); ?></span>
    </button>
    <div class="prize" data-gift-prize aria-live="polite">
        <small><?php echo __('مبروك! كسبت', 'arqamweb'); ?></small>
        <b data-prize-title>—</b>
        <p><?php echo __('على كل الطلبات في الموقع، من غير كود.', 'arqamweb'); ?></p>
        <a href="<?php echo esc_url(elsondos_is_white_friday_page() ? '#deals' : elsondos_wf_page_url() . '#deals'); ?>" class="btn btn-primary btn-sm" style="margin-top:12px" data-popup-close><?php echo __('تسوق دلوقتي', 'arqamweb'); ?></a>
    </div>
</div>
