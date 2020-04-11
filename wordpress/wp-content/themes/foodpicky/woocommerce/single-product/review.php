<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$options = get_option(AZEXO_FRAMEWORK);
$rating = floatval(get_comment_meta($comment->comment_ID, 'rating', true));
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

    <div id="comment-<?php comment_ID(); ?>" class="comment_container">

        <?php echo get_avatar($comment, apply_filters('woocommerce_review_gravatar_size', isset($options['avatar_size']) ? $options['avatar_size'] : 60), ''); ?>

        <div class="comment-text">

            <?php ?>

            <?php
            if (get_option('woocommerce_enable_review_rating') === 'yes') {
                ?>
                <div class="ratings">
                    <?php if ($rating) : ?>

                        <div class="star-rating review" title="<?php echo sprintf(esc_html__('Rated %d out of 5', 'woocommerce'), $rating) ?>">
                            <span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong><?php print esc_html($rating); ?></strong> <?php esc_html_e('out of 5', 'woocommerce'); ?></span>
                        </div>

                        <?php
                    endif;
                    $review_marks = azexo_review_marks();
                    if (!empty($review_marks)) {
                        ?>
                        <div class="marks">
                            <?php
                            foreach ($review_marks as $slug => $label) {
                                $value = get_comment_meta($comment->comment_ID, $slug, true);
                                ?>
                                <div class="star-rating mark" title="<?php echo sprintf(esc_html__('Rated %d out of 5', 'woocommerce'), $value) ?>">
                                    <label><?php print esc_html($label); ?></label>
                                    <span style="width:<?php echo ( $value / 5 ) * 100; ?>%"><strong><?php print esc_html($value); ?></strong> <?php esc_html_e('out of 5', 'woocommerce'); ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>

            <?php if ($comment->comment_approved == '0') : ?>

                <p class="meta"><em><?php esc_html_e('Your comment is awaiting approval', 'woocommerce'); ?></em></p>

            <?php else : ?>

                <p class="meta">
                    <strong ><?php comment_author(); ?></strong> <?php
                    if (get_option('woocommerce_review_rating_verification_label') === 'yes')
                        if (wc_customer_bought_product($comment->comment_author_email, $comment->user_id, $comment->comment_post_ID))
                            echo '<em class="verified">(' . esc_html__('verified owner', 'woocommerce') . ')</em> ';
                    ?> <time datetime="<?php echo get_comment_date('c'); ?>"><?php echo get_comment_date(wc_date_format()); ?></time>
                </p>

            <?php endif; ?>

            <div class="description"><?php comment_text(); ?></div>

            <?php if (isset($options['review_likes']) && $options['review_likes']) : ?>

                <div class="like"> <?php if(function_exists('azpl_get_simple_likes_button')) { print azpl_get_simple_likes_button($comment->comment_ID, true); } ?> </div>

            <?php endif; ?>
        </div>
    </div>
