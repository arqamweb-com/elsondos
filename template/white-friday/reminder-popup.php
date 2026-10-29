<?php
/**
 * "فكّرني" popup on the nights — the customer leaves a WhatsApp number or an email
 */
?>
<div class="wf-popup" id="wf-reminder-popup" role="dialog" aria-modal="true" aria-labelledby="wf-reminder-title" hidden>
    <div class="wf-popup__overlay" data-popup-close></div>
    <div class="wf-popup__box">
        <button type="button" class="wf-popup__close" data-popup-close aria-label="<?php echo esc_attr__('إغلاق', 'arqamweb'); ?>">×</button>
        <span class="eyebrow eyebrow--dark" data-reminder-day></span>
        <h2 id="wf-reminder-title"><?php echo __('فكّرني لما', 'arqamweb'); ?> <span class="text-gradient-gold"><?php echo __('الليلة تفتح', 'arqamweb'); ?></span></h2>
        <p class="wf-popup__sub" data-reminder-title></p>

        <form class="form wf-reminder-form" data-wf-signup="reminder" novalidate>
            <input type="hidden" name="night" value="">
            <input type="text" name="website" class="wf-hp" tabindex="-1" autocomplete="off" aria-hidden="true">

            <div class="tabs" role="radiogroup" aria-label="<?php echo esc_attr__('نبعتلك على', 'arqamweb'); ?>">
                <label class="tab"><input type="radio" name="method" value="whatsapp" checked> <?php echo __('واتساب', 'arqamweb'); ?></label>
                <label class="tab"><input type="radio" name="method" value="email"> <?php echo __('إيميل', 'arqamweb'); ?></label>
            </div>

            <label for="wf-reminder-contact" data-contact-label><?php echo __('رقم الواتساب', 'arqamweb'); ?></label>
            <input id="wf-reminder-contact" name="contact" type="tel" inputmode="tel" required placeholder="01xxxxxxxxx" dir="ltr" style="text-align:right">
            <button class="btn btn-gold btn-block" type="submit"><?php echo __('فكّرني', 'arqamweb'); ?></button>
            <small><?php echo __('هنبعتلك رسالة واحدة بس لما الليلة تفتح.', 'arqamweb'); ?></small>
        </form>
    </div>
</div>
