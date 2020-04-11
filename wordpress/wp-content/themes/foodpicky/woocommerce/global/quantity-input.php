<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php if (is_product()) : ?>
    <div class="quantity">
        <input type="number" step="<?php echo esc_attr($step); ?>" <?php if (is_numeric($min_value)) : ?>min="<?php echo esc_attr($min_value); ?>"<?php endif; ?> <?php if (is_numeric($max_value)) : ?>max="<?php echo esc_attr($max_value); ?>"<?php endif; ?> name="<?php echo esc_attr($input_name); ?>" value="<?php echo esc_attr($input_value); ?>" title="<?php _ex('Qty', 'Product quantity input tooltip', 'woocommerce') ?>" class="input-text qty text" size="4" />
        <div class="qty-arrows">
            <input type="button" class="qty-increase" value="+">
            <input type="button" class="qty-decrease" value="-">
        </div>            
    </div>
<?php else: ?>
    <div class="quantity">
        <input type="number" step="<?php echo esc_attr($step); ?>" <?php if (is_numeric($min_value)) : ?>min="<?php echo esc_attr($min_value); ?>"<?php endif; ?> <?php if (is_numeric($max_value)) : ?>max="<?php echo esc_attr($max_value); ?>"<?php endif; ?> name="<?php echo esc_attr($input_name); ?>" value="<?php echo esc_attr($input_value); ?>" title="<?php _ex('Qty', 'Product quantity input tooltip', 'woocommerce') ?>" class="input-text qty text" size="4" />
    </div>
<?php endif; ?>
