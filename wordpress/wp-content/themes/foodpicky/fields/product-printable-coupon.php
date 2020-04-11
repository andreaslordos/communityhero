<?php
/*
  Field Name: Product printable coupon
 */
?>
<?php
$product = wc_get_product();
$url = get_post_meta($product->get_id(), 'file', true);
wp_enqueue_script('azexo-fields', get_template_directory_uri() . '/js/fields.js', array('jquery'), AZEXO_FRAMEWORK_VERSION, true);
?>

<?php if (!empty($url)) : ?>
    <div class="coupon-wrapper">
        <a href="javascript:print();" class="printable-coupon" data-coupon="<?php print esc_url($url); ?>" ><?php esc_attr_e('Print coupon', 'foodpicky'); ?></a>
    </div>
<?php endif; ?>


