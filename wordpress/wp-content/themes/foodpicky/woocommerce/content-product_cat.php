<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

global $woocommerce_loop;

if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}
$woocommerce_loop['loop']++;

?>

<li <?php wc_product_cat_class(); ?>>
    <div class="category">
        <?php do_action('woocommerce_before_subcategory', $category); ?>


        <div class="category-thumbnail">
            <a href="<?php echo esc_url(get_term_link($category->slug, 'product_cat')); ?>">
                <?php
                woocommerce_subcategory_thumbnail($category);
                ?>
            </a>        
        </div>
        <div class="category-data">
            <?php
            /**
             * woocommerce_before_subcategory_title hook
             *
             * @hooked woocommerce_subcategory_thumbnail - 10
             */
            do_action('woocommerce_before_subcategory_title', $category);
            ?>
            <a href="<?php echo esc_url(get_term_link($category->slug, 'product_cat')); ?>">
                <h3>
                    <?php
                    print esc_html($category->name);

                    if ($category->count > 0)
                        echo apply_filters('woocommerce_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category);
                    ?>
                </h3>
            </a>

            <?php
            /**
             * woocommerce_after_subcategory_title hook
             */
            do_action('woocommerce_after_subcategory_title', $category);
            ?>
        </div>

        <?php do_action('woocommerce_after_subcategory', $category); ?>
    </div>
</li>
