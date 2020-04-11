<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
global $post;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!comments_open()) {
    return;
}
$options = get_option(AZEXO_FRAMEWORK);
?>
<div id="reviews">
    <div id="comments">
        <h2><?php
            if (get_option('woocommerce_enable_review_rating') === 'yes' && ( $count = azexo_review_count($post) )) {
                printf(_n('%s review for %s', '%s reviews for %s', $count, 'woocommerce'), $count, get_the_title());
            } else {
                esc_html_e('Reviews', 'woocommerce');
            }
            ?></h2>

        <?php if (have_comments()) : ?>

            <?php if (isset($options['show_ratings_summary']) && $options['show_ratings_summary']) : ?>
                <div class="ratings-summary">
                    <div class="average-rating">
                        <?php woocommerce_template_single_rating(); ?>
                        <label><?php esc_html_e('Overall rating', 'foodpicky'); ?></label>
                    </div>
                    <div class="stars">
                        <?php woocommerce_template_single_rating(); ?>
                    </div>         
                    <?php
                    $review_marks = azexo_review_marks();
                    if (!empty($review_marks)) {
                        ?>
                        <div class="average-marks">
                            <?php
                            foreach ($review_marks as $slug => $label) {

                                if (!metadata_exists('post', $post->ID, '_' . $slug . '_rating_count')) {
                                    global $wpdb;

                                    $counts = array();
                                    $raw_counts = $wpdb->get_results($wpdb->prepare("
                                        SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
                                        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                                        WHERE meta_key = %s
                                        AND comment_post_ID = %d
                                        AND comment_approved = '1'
                                        AND meta_value > 0
                                        GROUP BY meta_value
                                    ", $slug, $post->ID));

                                    foreach ($raw_counts as $count) {
                                        $counts[$count->meta_value] = $count->meta_value_count;
                                    }

                                    update_post_meta($post->ID, '_' . $slug . '_rating_count', $counts);
                                }
                                $counts = get_post_meta($post->ID, '_' . $slug . '_rating_count', true);
                                $rating_count = array_sum($counts);

                                $average = 0;
                                if (!metadata_exists('post', $post->ID, '_' . $slug . '_average_rating')) {
                                    if ($rating_count) {
                                        global $wpdb;

                                        $ratings = $wpdb->get_var($wpdb->prepare("
                                            SELECT SUM(meta_value) FROM $wpdb->commentmeta
                                            LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                                            WHERE meta_key = %s
                                            AND comment_post_ID = %d
                                            AND comment_approved = '1'
                                            AND meta_value > 0
                                        ", $slug, $post->ID));
                                        $average = number_format($ratings / $rating_count, 2, '.', '');
                                    }
                                    update_post_meta($post->ID, '_' . $slug . '_average_rating', $average);
                                }
                                $average = floatval(get_post_meta($post->ID, '_' . $slug . '_average_rating', true));
                                ?>
                                <div class="star-rating mark" title="<?php echo sprintf(esc_html__('Rated %d out of 5', 'woocommerce'), $average) ?>">
                                    <label><?php print esc_html($label); ?></label>
                                    <span style="width:<?php echo ( $average / 5 ) * 100; ?>%"><strong><?php print esc_html($average); ?></strong> <?php esc_html_e('out of 5', 'woocommerce'); ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php endif; ?>

            <ol class="commentlist">
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>
            </ol>

            <?php
            if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                echo '<nav class="woocommerce-pagination">';
                paginate_comments_links(apply_filters('woocommerce_comment_pagination_args', array(
                    'format' => '',
                    'add_args' => '',
                    'prev_text' => '<i class="prev"></i>' . '<span>' . esc_html__('Previous', 'foodpicky') . '</span>',
                    'next_text' => '<span>' . esc_html__('Next', 'foodpicky') . '</span>' . '<i class="next"></i>',
                )));
                echo '</nav>';
            endif;
            ?>

        <?php else : ?>

            <p class="woocommerce-noreviews"><?php esc_html_e('There are no reviews yet.', 'woocommerce'); ?></p>

        <?php endif; ?>
    </div>

    <?php if (azexo_review_allowed('', get_current_user_id(), $post)) : ?>

        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter = wp_get_current_commenter();

                $comment_form = array(
                    'title_reply' => have_comments() ? __('Add a review', 'woocommerce') : sprintf(__('Be the first to review &ldquo;%s&rdquo;', 'woocommerce'), get_the_title()),
                    'title_reply_to' => esc_html__('Leave a Reply to %s', 'woocommerce'),
                    'comment_notes_before' => '',
                    'comment_notes_after' => '',
                    'fields' => array(
                        'author' => '<p class="comment-form-author">' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? '' : '<label for="author">' . esc_html__('Name', 'woocommerce') . ' <span class="required">*</span></label> ') .
                        '<input id="author" name="author" type="text" placeholder="' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? esc_html__('Name', 'woocommerce') : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" /></p>',
                        'email' => '<p class="comment-form-email">' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? '' : '<label for="email">' . esc_html__('Email', 'woocommerce') . ' <span class="required">*</span></label> ') .
                        '<input id="email" name="email" type="text" placeholder="' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? esc_html__('Email', 'woocommerce') : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" /></p>',
                    ),
                    'label_submit' => esc_html__('Submit', 'woocommerce'),
                    'logged_in_as' => '',
                    'comment_field' => ''
                );

                if ($account_page_url = wc_get_page_permalink('myaccount')) {
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(wp_kses(__('You must be <a href="%s">logged in</a> to post a review.', 'woocommerce'), array('a' => array('href' => array()))), esc_url($account_page_url)) . '</p>';
                }

                if (get_option('woocommerce_enable_review_rating') === 'yes') {
                    $comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . esc_html__('Your rating', 'woocommerce') . '</label><select name="rating" id="rating">
							<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
							<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
							<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
							<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
							<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
							<option value="1">' . esc_html__('Very Poor', 'woocommerce') . '</option>
						</select></p>';
                }

                $review_marks = azexo_review_marks();
                if (!empty($review_marks) && get_option('woocommerce_enable_review_rating') === 'yes') {
                    foreach ($review_marks as $slug => $label) {
                        $comment_form['comment_field'] .= '<p class="comment-form-mark"><label for="' . esc_attr($slug) . '">' . esc_html($label) . '</label><select name="' . esc_attr($slug) . '">
							<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
							<option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
							<option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
							<option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
							<option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
							<option value="1">' . esc_html__('Very Poor', 'woocommerce') . '</option>
						</select></p>';
                    }
                }

                if (is_user_logged_in()) {
                    $comment_form['comment_field'] = '<div class="review-fields">' . $comment_form['comment_field'] . '</div>';
                } else {
                    $comment_form['fields']['author'] = '<div class="review-fields">' . $comment_form['fields']['author'];
                    $comment_form['comment_field'] = $comment_form['comment_field'] . '</div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment">' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? '' : '<label for="comment">' . esc_html__('Your review', 'woocommerce') . '</label>') . '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="' . (isset($options['review_form_placeholders']) && $options['review_form_placeholders'] ? esc_html__('Your review', 'woocommerce') : '') . '"></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                ?>
            </div>
        </div>

    <?php else : ?>

        <p class="woocommerce-verification-required"><?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?></p>

    <?php endif; ?>

    <div class="clear"></div>
</div>
