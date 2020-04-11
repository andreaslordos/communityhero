<?php
/**
 * Related Products
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

if (empty($product) || !$product->exists()) {
    return;
}

$related = wc_get_related_products($product->get_id(), $posts_per_page);

if (sizeof($related) == 0)
    return;

$args = apply_filters('woocommerce_related_products_args', array(
    'post_type' => 'product',
    'ignore_sticky_posts' => 1,
    'no_found_rows' => 1,
    'posts_per_page' => $posts_per_page,
    'orderby' => $orderby,
    'post__in' => $related,
    'post__not_in' => array($product->get_id())
        ));

$products = new WP_Query($args);

$template = 'related_product';
$options = get_option(AZEXO_FRAMEWORK);
$thumbnail_size = isset($options[$template . '_thumbnail_size']) && !empty($options[$template . '_thumbnail_size']) ? $options[$template . '_thumbnail_size'] : 'large';
azexo_add_image_size($thumbnail_size);
$size = azexo_get_image_sizes($thumbnail_size);
wp_enqueue_script('owl-carousel');
wp_enqueue_style('owl-carousel');

if ($products->have_posts()) :
    ?>

    <div class="related products">

        <h2><?php esc_html_e('Related Products', 'woocommerce'); ?></h2>

        <div class="owl-carousel posts-list related-product" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>" data-margin="<?php print esc_attr($options['related_products_carousel_margin']); ?>">

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
