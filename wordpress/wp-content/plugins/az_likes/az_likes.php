<?php
/**
 * Plugin Name:  AZEXO Post likes
 * Plugin URI:   http://www.azexo.com
 * Description:  Post/Comments likes system
 * Author:       AZEXO
 * Author URI:   http://www.azexo.com
 * Version: 1.27
 * Text Domain:  azpl
 * Domain Path:  languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('AZPL_URL', plugins_url('', __FILE__));
define('AZPL_DIR', trailingslashit(dirname(__FILE__)));

add_action('plugins_loaded', 'azpl_plugins_loaded');

function azpl_plugins_loaded() {
    load_plugin_textdomain('azpl', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/**
 * Register the stylesheets for the public-facing side of the site.
 * @since    0.5
 */
add_action('wp_enqueue_scripts', 'azpl_enqueue_scripts');

function azpl_enqueue_scripts() {
    wp_enqueue_script('simple-likes-public-js', AZPL_URL . '/js/simple-likes-public.js', array('jquery'), '0.5', true);
    wp_localize_script('simple-likes-public-js', 'simpleLikes', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'like' => esc_html__('Like', 'azpl'),
        'unlike' => esc_html__('Unlike', 'azpl')
    ));
}

/**
 * Processes like/unlike
 * @since    0.5
 */
add_action('wp_ajax_nopriv_process_simple_like', 'azpl_process_simple_like');
add_action('wp_ajax_process_simple_like', 'azpl_process_simple_like');

function azpl_process_simple_like() {
    // Security
    $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : 0;
    if (!wp_verify_nonce($nonce, 'simple-likes-nonce')) {
        exit(esc_html__('Not permitted', 'azpl'));
    }
    // Test if javascript is disabled
    $disabled = ( isset($_REQUEST['disabled']) && $_REQUEST['disabled'] == true ) ? true : false;
    // Test if this is a comment
    $is_comment = ( isset($_REQUEST['is_comment']) && $_REQUEST['is_comment'] == 1 ) ? 1 : 0;
    // Base variables
    $post_id = ( isset($_REQUEST['post_id']) && is_numeric($_REQUEST['post_id']) ) ? $_REQUEST['post_id'] : '';
    $result = array();
    $post_users = NULL;
    $like_count = 0;
    // Get plugin options
    if ($post_id != '') {
        $count = ( $is_comment == 1 ) ? get_comment_meta($post_id, "_comment_like_count", true) : get_post_meta($post_id, "_post_like_count", true); // like count
        $count = ( isset($count) && is_numeric($count) ) ? $count : 0;
        if (!azpl_already_liked($post_id, $is_comment)) { // Like the post
            if (is_user_logged_in()) { // user is logged in
                $user_id = get_current_user_id();
                $post_users = azpl_post_user_likes($user_id, $post_id, $is_comment);
                if ($is_comment == 1) {
                    // Update User & Comment
                    $user_like_count = get_user_option("_comment_like_count", $user_id);
                    $user_like_count = ( isset($user_like_count) && is_numeric($user_like_count) ) ? $user_like_count : 0;
                    update_user_option($user_id, "_comment_like_count", ++$user_like_count);
                    if ($post_users) {
                        update_comment_meta($post_id, "_user_comment_liked", $post_users);
                    }
                } else {
                    // Update User & Post
                    $user_like_count = get_user_option("_user_like_count", $user_id);
                    $user_like_count = ( isset($user_like_count) && is_numeric($user_like_count) ) ? $user_like_count : 0;
                    update_user_option($user_id, "_user_like_count", ++$user_like_count);
                    if ($post_users) {
                        update_post_meta($post_id, "_user_liked", $post_users);
                    }
                    $liked_posts = get_user_meta($user_id, "_liked_posts", true);
                    if (empty($liked_posts)) {
                        $liked_posts = array();
                    }
                    $liked_posts[] = $post_id;
                    $liked_posts = array_unique($liked_posts);
                    update_user_meta($user_id, "_liked_posts", $liked_posts);
                }
            } else { // user is anonymous
                $user_ip = azpl_get_ip();
                $post_users = azpl_post_ip_likes($user_ip, $post_id, $is_comment);
                // Update Post
                if ($post_users) {
                    if ($is_comment == 1) {
                        update_comment_meta($post_id, "_user_comment_IP", $post_users);
                    } else {
                        update_post_meta($post_id, "_user_IP", $post_users);
                    }
                }
            }
            $like_count = ++$count;
            $response['status'] = "liked";
            $response['icon'] = azpl_get_liked_icon();
        } else { // Unlike the post
            if (is_user_logged_in()) { // user is logged in
                $user_id = get_current_user_id();
                $post_users = azpl_post_user_likes($user_id, $post_id, $is_comment);
                // Update User
                if ($is_comment == 1) {
                    $user_like_count = get_user_option("_comment_like_count", $user_id);
                    $user_like_count = ( isset($user_like_count) && is_numeric($user_like_count) ) ? $user_like_count : 0;
                    if ($user_like_count > 0) {
                        update_user_option($user_id, "_comment_like_count", --$user_like_count);
                    }
                } else {
                    $user_like_count = get_user_option("_user_like_count", $user_id);
                    $user_like_count = ( isset($user_like_count) && is_numeric($user_like_count) ) ? $user_like_count : 0;
                    if ($user_like_count > 0) {
                        update_user_option($user_id, '_user_like_count', --$user_like_count);
                    }
                }
                // Update Post
                if ($post_users) {
                    $uid_key = array_search($user_id, $post_users);
                    unset($post_users[$uid_key]);
                    if ($is_comment == 1) {
                        update_comment_meta($post_id, "_user_comment_liked", $post_users);
                    } else {
                        update_post_meta($post_id, "_user_liked", $post_users);

                        $liked_posts = get_user_meta($user_id, "_liked_posts", true);
                        if (empty($liked_posts)) {
                            $liked_posts = array();
                        }
                        $post_key = array_search($post_id, $liked_posts);
                        unset($liked_posts[$post_key]);
                        update_user_meta($user_id, "_liked_posts", $liked_posts);
                    }
                }
            } else { // user is anonymous
                $user_ip = azpl_get_ip();
                $post_users = azpl_post_ip_likes($user_ip, $post_id, $is_comment);
                // Update Post
                if ($post_users) {
                    $uip_key = array_search($user_ip, $post_users);
                    unset($post_users[$uip_key]);
                    if ($is_comment == 1) {
                        update_comment_meta($post_id, "_user_comment_IP", $post_users);
                    } else {
                        update_post_meta($post_id, "_user_IP", $post_users);
                    }
                }
            }
            $like_count = ( $count > 0 ) ? --$count : 0; // Prevent negative number
            $response['status'] = "unliked";
            $response['icon'] = azpl_get_unliked_icon();
        }
        if ($is_comment == 1) {
            update_comment_meta($post_id, "_comment_like_count", $like_count);
            update_comment_meta($post_id, "_comment_like_modified", date('Y-m-d H:i:s'));
        } else {
            update_post_meta($post_id, "_post_like_count", $like_count);
            update_post_meta($post_id, "_post_like_modified", date('Y-m-d H:i:s'));
        }
        $response['count'] = azpl_get_like_count($like_count);
        $response['testing'] = $is_comment;
        if ($disabled == true) {
            if ($is_comment == 1) {
                wp_redirect(get_permalink(get_the_ID()));
                exit();
            } else {
                wp_redirect(get_permalink($post_id));
                exit();
            }
        } else {
            wp_send_json($response);
        }
    }
}

/**
 * Utility to test if the post is already liked
 * @since    0.5
 */
function azpl_already_liked($post_id, $is_comment) {
    $post_users = NULL;
    $user_id = NULL;
    if (is_user_logged_in()) { // user is logged in
        $user_id = get_current_user_id();
        $post_meta_users = ( $is_comment == 1 ) ? get_comment_meta($post_id, "_user_comment_liked") : get_post_meta($post_id, "_user_liked");
        if (count($post_meta_users) != 0) {
            $post_users = $post_meta_users[0];
        }
    } else { // user is anonymous
        $user_id = azpl_get_ip();
        $post_meta_users = ( $is_comment == 1 ) ? get_comment_meta($post_id, "_user_comment_IP") : get_post_meta($post_id, "_user_IP");
        if (count($post_meta_users) != 0) { // meta exists, set up values
            $post_users = $post_meta_users[0];
        }
    }
    if (is_array($post_users) && in_array($user_id, $post_users)) {
        return true;
    } else {
        return false;
    }
}

// azpl_already_liked()

/**
 * Output the like button
 * @since    0.5
 */
function azpl_get_simple_likes_button($post_id, $is_comment = NULL) {
    $is_comment = ( NULL == $is_comment ) ? 0 : 1;
    $output = '';
    $nonce = wp_create_nonce('simple-likes-nonce'); // Security
    if ($is_comment == 1) {
        $post_id_class = esc_attr(' sl-comment-button-' . $post_id);
        $comment_class = esc_attr(' sl-comment');
        $like_count = get_comment_meta($post_id, "_comment_like_count", true);
        $like_count = ( isset($like_count) && is_numeric($like_count) ) ? $like_count : 0;
    } else {
        $post_id_class = esc_attr(' sl-button-' . $post_id);
        $comment_class = esc_attr('');
        $like_count = get_post_meta($post_id, "_post_like_count", true);
        $like_count = ( isset($like_count) && is_numeric($like_count) ) ? $like_count : 0;
    }
    $count = azpl_get_like_count($like_count);
    $icon_empty = azpl_get_unliked_icon();
    $icon_full = azpl_get_liked_icon();
    // Loader
    $loader = '<span class="sl-loader"></span>';
    // Liked/Unliked Variables
    if (azpl_already_liked($post_id, $is_comment)) {
        $class = esc_attr(' liked');
        $title = esc_html__('Unlike', 'azpl');
        $icon = $icon_full;
    } else {
        $class = '';
        $title = esc_html__('Like', 'azpl');
        $icon = $icon_empty;
    }
    $output = '<span class="sl-wrapper"><a href="' . esc_url(admin_url('admin-ajax.php?action=process_simple_like' . '&nonce=' . $nonce . '&post_id=' . $post_id . '&disabled=true&is_comment=' . $is_comment)) . '" class="sl-button' . $post_id_class . $class . $comment_class . '" data-nonce="' . $nonce . '" data-post-id="' . $post_id . '" data-iscomment="' . $is_comment . '" title="' . $title . '">' . $icon . $count . '</a>' . $loader . '</span>';
    return $output;
}

// azpl_get_simple_likes_button()

/**
 * Processes shortcode to manually add the button to posts
 * @since    0.5
 */
function azpl_shortcode() {
    return azpl_get_simple_likes_button(get_the_ID(), 0);
}

// azpl_shortcode()

/**
 * Utility retrieves post meta user likes (user id array), 
 * then adds new user id to retrieved array
 * @since    0.5
 */
function azpl_post_user_likes($user_id, $post_id, $is_comment) {
    $post_users = '';
    $post_meta_users = ( $is_comment == 1 ) ? get_comment_meta($post_id, "_user_comment_liked") : get_post_meta($post_id, "_user_liked");
    if (count($post_meta_users) != 0) {
        $post_users = $post_meta_users[0];
    }
    if (!is_array($post_users)) {
        $post_users = array();
    }
    if (!in_array($user_id, $post_users)) {
        $post_users['user-' . $user_id] = $user_id;
    }
    return $post_users;
}

// azpl_post_user_likes()

/**
 * Utility retrieves post meta ip likes (ip array), 
 * then adds new ip to retrieved array
 * @since    0.5
 */
function azpl_post_ip_likes($user_ip, $post_id, $is_comment) {
    $post_users = '';
    $post_meta_users = ( $is_comment == 1 ) ? get_comment_meta($post_id, "_user_comment_IP") : get_post_meta($post_id, "_user_IP");
    // Retrieve post information
    if (count($post_meta_users) != 0) {
        $post_users = $post_meta_users[0];
    }
    if (!is_array($post_users)) {
        $post_users = array();
    }
    if (!in_array($user_ip, $post_users)) {
        $post_users['ip-' . $user_ip] = $user_ip;
    }
    return $post_users;
}

// azpl_post_ip_likes()

/**
 * Utility to retrieve IP address
 * @since    0.5
 */
function azpl_get_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    $ip = ( $ip === false ) ? '0.0.0.0' : $ip;
    return $ip;
}

// azpl_get_ip()

/**
 * Utility returns the button icon for "like" action
 * @since    0.5
 */
function azpl_get_liked_icon() {
    /* If already using Font Awesome with your theme, replace svg with: <i class="fa fa-heart"></i> */
    $icon = '<span class="sl-icon"><svg role="img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve"><path d="M124 20.4C111.5-7 73.7-4.8 64 19 54.3-4.9 16.5-7 4 20.4c-14.7 32.3 19.4 63 60 107.1C104.6 83.4 138.7 52.7 124 20.4z"/></svg></span>';
    return $icon;
}

// azpl_get_liked_icon()

/**
 * Utility returns the button icon for "unlike" action
 * @since    0.5
 */
function azpl_get_unliked_icon() {
    /* If already using Font Awesome with your theme, replace svg with: <i class="fa fa-heart-o"></i> */
    $icon = '<span class="sl-icon"><svg role="img" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve"><path d="M64 127.5C17.1 79.9 3.9 62.3 1 44.4c-3.5-22 12.2-43.9 36.7-43.9 10.5 0 20 4.2 26.4 11.2 6.3-7 15.9-11.2 26.4-11.2 24.3 0 40.2 21.8 36.7 43.9C124.2 62 111.9 78.9 64 127.5zM37.6 13.4c-9.9 0-18.2 5.2-22.3 13.8C5 49.5 28.4 72 64 109.2c35.7-37.3 59-59.8 48.6-82 -4.1-8.7-12.4-13.8-22.3-13.8 -15.9 0-22.7 13-26.4 19.2C60.6 26.8 54.4 13.4 37.6 13.4z"/></svg></span>';
    return $icon;
}

// azpl_get_unliked_icon()

/**
 * Utility function to format the button count,
 * appending "K" if one thousand or greater,
 * "M" if one million or greater,
 * and "B" if one billion or greater (unlikely).
 * $precision = how many decimal points to display (1.25K)
 * @since    0.5
 */
function azpl_format_count($number) {
    $precision = 2;
    if ($number >= 1000 && $number < 1000000) {
        $formatted = number_format($number / 1000, $precision) . 'K';
    } else if ($number >= 1000000 && $number < 1000000000) {
        $formatted = number_format($number / 1000000, $precision) . 'M';
    } else if ($number >= 1000000000) {
        $formatted = number_format($number / 1000000000, $precision) . 'B';
    } else {
        $formatted = $number; // Number is less than 1000
    }
    $formatted = str_replace('.00', '', $formatted);
    return $formatted;
}

// azpl_format_count()

/**
 * Utility retrieves count plus count options, 
 * returns appropriate format based on options
 * @since    0.5
 */
function azpl_get_like_count($like_count) {
    $like_text = esc_html__('Like', 'azpl');
    if (is_numeric($like_count) && $like_count > 0) {
        $number = azpl_format_count($like_count);
    } else {
        $number = $like_text;
    }
    $count = '<span class="sl-count">' . $number . '</span>';
    return $count;
}

function azpl_liked_post_clauses($args, $query) {
    global $wpdb;

    if (is_user_logged_in()) {
        $liked_posts = get_user_meta(get_current_user_id(), "_liked_posts", true);
        if (empty($liked_posts)) {
            $liked_posts = array();
        }
        $args['where'] .= " AND ( $wpdb->posts.ID IN (" . implode(',', $liked_posts) . ")) ";
    } else {
        $args['where'] .= ' AND 1=0 ';
    }

    return $args;
}

// azpl_get_like_count()
// User Profile List
add_action('show_user_profile', 'azpl_show_user_likes');
add_action('edit_user_profile', 'azpl_show_user_likes');

function azpl_show_user_likes($user) {
    ?>        
    <table class="form-table">
        <tr>
            <th><label for="user_likes"><?php esc_html_e('You Like:', 'azpl'); ?></label></th>
            <td>
                <?php
                $types = get_post_types(array('public' => true));
                $args = array(
                    'numberposts' => -1,
                    'post_type' => $types,
                    'meta_query' => array(
                        array(
                            'key' => '_user_liked',
                            'value' => $user->ID,
                            'compare' => 'LIKE'
                        )
                ));
                $sep = '';
                $like_query = new WP_Query($args);
                if ($like_query->have_posts()) :
                    ?>
                    <p>
                        <?php
                        while ($like_query->have_posts()) : $like_query->the_post();
                            print $sep;
                            ?><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                            <?php
                            $sep = ' &middot; ';
                        endwhile;
                        ?>
                    </p>
                <?php else : ?>
                    <p><?php esc_html_e('You do not like anything yet.', 'azpl'); ?></p>
                <?php
                endif;
                wp_reset_postdata();
                ?>
            </td>
        </tr>
    </table>
    <?php
}

// azpl_show_user_likes()
