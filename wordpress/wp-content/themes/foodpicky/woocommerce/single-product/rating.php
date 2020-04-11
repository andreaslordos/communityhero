<?php
/**
 * Single Product Rating
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$product = wc_get_product();

if (get_option('woocommerce_enable_review_rating') === 'no') {
    return;
}

if (!$product) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average = $product->get_average_rating();

if ($rating_count > 0) :
    ?>

    <div class="woocommerce-product-rating">
        <div class="star-rating" title="<?php printf(esc_html__('Rated %s out of 5', 'woocommerce'), $average); ?>">
            <span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
                <strong class="rating"><?php print esc_html($average); ?></strong> <?php printf(esc_html__('out of %s5%s', 'woocommerce'), '<span>', '</span>'); ?>
                <?php printf(_n('based on %s customer rating', 'based on %s customer ratings', $rating_count, 'woocommerce'), '<span class="rating">' . esc_html($rating_count) . '</span>'); ?>
            </span>
        </div>
        <?php if (comments_open()) : ?><a href="<?php print esc_url(get_permalink()); ?>#reviews" class="woocommerce-review-link roll" rel="nofollow">(<?php printf(_n('%s customer review', '%s customer reviews', $review_count, 'woocommerce'), '<span class="count">' . $review_count . '</span>'); ?>)</a><?php endif ?>
    </div>

<?php endif; ?>
