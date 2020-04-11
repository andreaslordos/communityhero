<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product, $post, $wp_query;
$original = $post;

$upsells = $product->get_upsells();

if (sizeof($upsells) == 0) {
    return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
    'post_type' => 'product',
    'ignore_sticky_posts' => 1,
    'no_found_rows' => 1,
    'posts_per_page' => $posts_per_page,
    'orderby' => $orderby,
    'post__in' => $upsells,
    'post__not_in' => array($product->get_id()),
    'meta_query' => $meta_query
);

$products = new WP_Query($args);

$template = 'upsells_product';
$options = get_option(AZEXO_FRAMEWORK);
$thumbnail_size = isset($options[$template . '_thumbnail_size']) && !empty($options[$template . '_thumbnail_size']) ? $options[$template . '_thumbnail_size'] : 'large';
azexo_add_image_size($thumbnail_size);
$size = azexo_get_image_sizes($thumbnail_size);
wp_enqueue_script('owl-carousel');
wp_enqueue_style('owl-carousel');

if ($products->have_posts()) :
    ?>

    <div class="upsells products">

        <h2><?php esc_html_e('You may also like&hellip;', 'woocommerce'); ?></h2>

        <div class="owl-carousel posts-list related-product" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>" data-margin="<?php print esc_attr($options['upsells_products_carousel_margin']); ?>">

            <?php while ($products->have_posts()) : $products->the_post(); ?>
                <div class="item">
                    <?php
                    $located = wc_locate_template('content-product.php');
                    if (file_exists($located)) {
                        $template_name = $template;
                        $azexo_woo_base_tag = 'div';
                        include( $located );
                    }
                    ?>
                </div>
            <?php endwhile; // end of the loop.  ?>

        </div>

    </div>

    <?php
endif;
$wp_query->post = $original;
wp_reset_postdata();
