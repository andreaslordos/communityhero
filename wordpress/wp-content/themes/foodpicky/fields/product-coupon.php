<?php
/*
  Field Name: Product coupon
 */
?>
<?php
$product = wc_get_product();
$coupon = get_post_meta($product->get_id(), 'coupon', true);
$product_url = get_post_meta($product->get_id(), '_product_url', true);
wp_enqueue_script('azexo-fields', get_template_directory_uri() . '/js/fields.js', array('jquery'), AZEXO_FRAMEWORK_VERSION, true);
?>
<?php if (!empty($coupon)) : ?>
    <div class="coupon-wrapper">
        <?php if ($product->is_in_stock()) : ?>
            <a href="<?php print (!empty($product_url) ? esc_url($product_url) : '#'); ?>" target="_blank" data-code="<?php print esc_attr($coupon); ?>" class="coupon" data-copied="<?php esc_attr_e('Code copied to the clipboard', 'foodpicky'); ?>"><?php esc_attr_e('Show code', 'foodpicky'); ?></a>
        <?php else: ?>
            <span class="coupon" ><?php esc_attr_e('Not available', 'foodpicky'); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>