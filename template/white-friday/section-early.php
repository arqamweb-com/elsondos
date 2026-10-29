<!-- EARLY ACCESS (concept 1) -->
<section class="section" id="early" style="padding-top:0">
    <div class="container">
        <div class="early reveal">
            <div>
                <span class="eyebrow eyebrow--dark"><?php echo __('قائمة الوصول المبكر', 'arqamweb'); ?></span>
                <h2><?php echo __('ادخل قبل الكل بساعتين', 'arqamweb'); ?></h2>
                <p><?php echo __('سجّل رقمك وهنبعتلك لينك العروض على واتساب قبل الافتتاح، عشان تلحق الكميات قبل ما تخلص.', 'arqamweb'); ?></p>
                <ul>
                    <li><?php echo __('لينك خاص قبل الافتتاح بساعتين', 'arqamweb'); ?></li>
                    <li><?php echo __('كود خصم إضافي 5% للمسجلين بس', 'arqamweb'); ?></li>
                    <li><?php echo __('تنبيه لما عطرك المفضل ينزل في العروض', 'arqamweb'); ?></li>
                </ul>
            </div>
            <form class="form" data-wf-signup="early" novalidate>
                <input type="text" name="website" class="wf-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <label for="n1"><?php echo __('الاسم', 'arqamweb'); ?></label>
                <input id="n1" name="name" required placeholder="<?php echo esc_attr__('اسمك', 'arqamweb'); ?>">
                <label for="p1"><?php echo __('رقم الواتساب', 'arqamweb'); ?></label>
                <input id="p1" name="phone" type="tel" inputmode="tel" required placeholder="01xxxxxxxxx" dir="ltr" style="text-align:right">
                <label for="c1"><?php echo __('بتدور على عطور', 'arqamweb'); ?></label>
                <select id="c1" name="interest"><option><?php echo __('رجالي', 'arqamweb'); ?></option><option><?php echo __('حريمي', 'arqamweb'); ?></option><option><?php echo __('يونيسكس', 'arqamweb'); ?></option><option><?php echo __('هدايا', 'arqamweb'); ?></option></select>
                <button class="btn btn-gold btn-block" type="submit"><?php echo __('سجّلني', 'arqamweb'); ?></button>
                <small><?php echo __('مش هنبعت أي رسائل غير عن العروض.', 'arqamweb'); ?></small>
            </form>
        </div>
    </div>
</section>
