<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options[get_post_type() . '_show_sidebar']) ? $options[get_post_type() . '_show_sidebar'] : 'right';
    if ($show_sidebar == 'hidden') {
        $show_sidebar = false;
    }
}
if(post_password_required()) {
    $show_sidebar = false;
}
$additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
get_header('shop');
?>

<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('shop') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('single', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
    <?php
    /**
     * woocommerce_sidebar hook
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    if ($show_sidebar == 'left') {
        do_action('woocommerce_sidebar');
    } else {
        if (in_array('single', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
    <div id="primary" class="content-area">
        <?php
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
            <?php
            /**
             * woocommerce_before_main_content hook
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action('woocommerce_before_main_content');
            ?>

            <?php while (have_posts()) : the_post(); ?>

                <?php wc_get_template_part('content', 'single-product'); ?>

            <?php endwhile; // end of the loop. ?>

            <?php
            /**
             * woocommerce_after_main_content hook
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action('woocommerce_after_main_content');
            ?>
        </div><!-- #content -->
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
        if (in_array('single', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>

<?php get_footer('shop'); ?>
