<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php
/**
 * woocommerce_before_single_product hook
 *
 * @hooked wc_print_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="product-main">
        <?php
        $options = get_option(AZEXO_FRAMEWORK);
        $located = wc_locate_template('content-product.php');
        if (file_exists($located)) {
            $product_template = isset($options['single_' . get_post_type() . '_template']) ? $options['single_' . get_post_type() . '_template'] : 'single_product';
            $azexo_woo_base_tag = 'div';
            include( $located );
        }
                
        if ((isset($options['show_data_tabs']) && $options['show_data_tabs'])) {
            woocommerce_output_product_data_tabs();
        }       
        ?>
    </div>

    <?php
    /**
     * woocommerce_after_single_product_summary hook
     *
     * @hooked woocommerce_output_product_data_tabs - 10
     * @hooked woocommerce_upsell_display - 15
     * @hooked woocommerce_output_related_products - 20
     */
    do_action('woocommerce_after_single_product_summary');
    ?>    

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action('woocommerce_after_single_product'); ?>
