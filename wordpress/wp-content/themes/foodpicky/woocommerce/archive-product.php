<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wp_query;
$post_type = isset($wp_query->query['post_type']) ? $wp_query->query['post_type'] : 'post';
if (is_array($post_type)) {
    $post_type = reset($post_type);
}
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
}
if (!isset($product_template)) {
    $product_template = isset($options['default_product_template']) ? $options['default_product_template'] : 'shop_product';
}
if (isset($_GET['template'])) {
    $product_template = $_GET['template'];
}
if (isset($_GET['mode']) && $_GET['mode'] == 'gmap') {
    $product_template = 'google_map_post';
}
$additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
get_header('shop');
?>

<?php if (isset($options['before_list_place']) && ($options['before_list_place'] == 'before_container')): ?>
    <div class="before-shop-loop">
        <?php
        /**
         * woocommerce_before_shop_loop hook
         *
         * @hooked wc_print_notices - 10
         * @hooked woocommerce_result_count - 20
         * @hooked woocommerce_catalog_ordering - 30
         */
        do_action('woocommerce_before_shop_loop');
        ?>
    </div>
<?php endif; ?>

<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('shop') && ($show_sidebar != 'hidden') ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('list', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'left') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
    <div id="primary" class="content-area">
        <?php
        /**
         * woocommerce_before_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
         * @hooked woocommerce_breadcrumb - 20
         */
        do_action('woocommerce_before_main_content');
        ?>

        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <div class="page-header">
                <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            </div>            
        <?php endif; ?>
        <div id="content" class="site-content <?php print str_replace('_', '-', $product_template); ?> <?php print ((isset($options['infinite_scroll']) && $options['infinite_scroll']) ? 'infinite-scroll' : '') ?>" role="main">


            <?php do_action('woocommerce_archive_description'); ?>

            <?php if (have_posts()) : ?>

                <?php if (isset($options['before_list_place']) && ($options['before_list_place'] == 'inside_content_area')): ?>
                    <div class="before-shop-loop">
                        <?php
                        /**
                         * woocommerce_before_shop_loop hook
                         *
                         * @hooked woocommerce_result_count - 20
                         * @hooked woocommerce_catalog_ordering - 30
                         */
                        do_action('woocommerce_before_shop_loop');
                        ?>
                    </div>
                <?php endif; ?>

                <?php
                if (isset($_GET['mode']) && $_GET['mode'] == 'gmap' && function_exists('azl_google_map_shortcode')) {
                    print azl_google_map_shortcode(array());
                    remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
                } else {
                    woocommerce_product_loop_start();
                    woocommerce_product_subcategories();
                    while (have_posts()) {
                        the_post();
                        $located = wc_locate_template('content-product.php');
                        if (file_exists($located)) {
                            include( $located );
                        }
                    }
                    woocommerce_product_loop_end();
                }
                ?>

                <div class="after-shop-loop">
                    <?php
                    /**
                     * woocommerce_after_shop_loop hook
                     *
                     * @hooked woocommerce_pagination - 10
                     */
                    do_action('woocommerce_after_shop_loop');
                    ?>
                </div>
            <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) : ?>

                <?php wc_get_template('loop/no-products-found.php'); ?>

            <?php endif; ?>

        </div><!-- #content -->
        <?php
        /**
         * woocommerce_after_main_content hook
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action('woocommerce_after_main_content');
        ?>
    </div><!-- #primary -->
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'right') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>
<?php get_footer('shop'); ?>
