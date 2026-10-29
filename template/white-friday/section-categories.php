<?php
/**
 * Category deals (concept 1)
 * Term IDs are the Arabic categories, WPML swaps them to the current language
 */
$img_dir = get_stylesheet_directory_uri() . '/frontend/assets/white-friday/';

$cats = array(
    array('term' => 39, 'img' => 'cat-men.jpg',    'label' => __('رجالي', 'arqamweb'),   'title' => __('عطور رجالي', 'arqamweb')),
    array('term' => 40, 'img' => 'cat-women.jpg',  'label' => __('حريمي', 'arqamweb'),   'title' => __('عطور حريمي', 'arqamweb')),
    array('term' => 94, 'img' => 'cat-unisex.jpg', 'label' => __('يونيسكس', 'arqamweb'), 'title' => __('عطور يونيسكس', 'arqamweb')),
);
?>
<!-- CATEGORIES (concept 1) -->
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow eyebrow--dark"><?php echo __('اختار قسمك', 'arqamweb'); ?></span>
            <h2><?php echo __('خصومات على كل الأقسام', 'arqamweb'); ?></h2>
            <p><?php echo __('عروض على كل قسم، والكمية محدودة.', 'arqamweb'); ?></p>
        </div>
        <div class="cats">
            <?php foreach ($cats as $cat) :
                $term_id = apply_filters('wpml_object_id', $cat['term'], 'product_cat', true);
                $link = get_term_link((int) $term_id, 'product_cat');
                $link = is_wp_error($link) ? '#deals' : $link;
            ?>
                <a href="<?php echo esc_url($link); ?>" class="cat reveal"><img src="<?php echo esc_url($img_dir . $cat['img']); ?>" alt="<?php echo esc_attr($cat['title']); ?>" loading="lazy"><div class="cat__body"><small><?php echo esc_html($cat['label']); ?></small><h3><?php echo esc_html($cat['title']); ?></h3><span class="cat__go"><?php echo __('تسوق القسم ←', 'arqamweb'); ?></span></div></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
