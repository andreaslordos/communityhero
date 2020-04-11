<?php

/**
 * Plugin Name:  AZEXO Social Login
 * Plugin URI:   http://www.azexo.com
 * Description:  OAuth2 authentication
 * Author:       AZEXO
 * Author URI:   http://www.azexo.com
 * Version: 1.27
 * Text Domain:  azsl
 * Domain Path:  languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('AZSL_URL', plugins_url('', __FILE__));
define('AZSL_DIR', trailingslashit(dirname(__FILE__)));

if (is_admin()) {
    require_once AZSL_DIR . 'settings.php';
}

add_action('plugins_loaded', 'azsl_plugins_loaded');

function azsl_plugins_loaded() {
    load_plugin_textdomain('azsl', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    global $azsl_social_networks;
    $azsl_social_networks = array('amazon', 'bikeindex', 'box', 'dropbox', 'facebook', 'flickr', 'foursquare', 'github', 'google', 'instagram', 'joinme', 'linkedin', 'soundcloud', 'tumblr', 'twitter', 'vimeo', 'vk', 'windows', 'yahoo');
    global $azsl_social_networks_icons;
    $azsl_social_networks_icons = array(
        'amazon' => 'fa fa-amazon',
        'bikeindex' => 'fa fa-bikeindex',
        'box' => 'fa fa-box',
        'dropbox' => 'fa fa-dropbox',
        'facebook' => 'fa fa-facebook',
        'flickr' => 'fa fa-flickr',
        'foursquare' => 'fa fa-foursquare',
        'github' => 'fa fa-github',
        'google' => 'fa fa-google',
        'instagram' => 'fa fa-instagram',
        'joinme' => 'fa fa-joinme',
        'linkedin' => 'fa fa-linkedin',
        'soundcloud' => 'fa fa-soundcloud',
        'tumblr' => 'fa fa-tumblr',
        'twitter' => 'fa fa-twitter',
        'vimeo' => 'fa fa-vimeo',
        'vk' => 'fa fa-vk',
        'windows' => 'fa fa-windows',
        'yahoo' => 'fa fa-yahoo'
    );
}

add_action('wp_enqueue_scripts', 'azsl_scripts');

function azsl_scripts() {
    $options = get_option('azsl-settings');
    global $azsl_social_networks;
    $networks = array();
    foreach ($azsl_social_networks as $network) {
        if (isset($options[$network . '_id']) && !empty($options[$network . '_id'])) {
            $networks[$network] = $options[$network . '_id'];
        }
    }
    wp_enqueue_script('hello', plugins_url('js/hello.all.min.js', __FILE__), array(), false, true);
    wp_enqueue_script('azsl', plugins_url('js/azsl.js', __FILE__), array('hello'), false, true);
    wp_localize_script('azsl', 'azsl', array(
        'homeurl' => esc_url(home_url('/')),
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'logged_in' => is_user_logged_in(),
        'nonce' => wp_create_nonce('ajax'),
        'networks' => $networks,
    ));
}

add_filter('template_include', 'azsl_template_include');

function azsl_template_include($template) {
    if (isset($_GET['azsl']) && $_GET['azsl'] == 'social-login-redirect') {
        $template = locate_template('social-login-redirect.php');
        if (!$template) {
            $template = plugin_dir_path(__FILE__) . 'social-login-redirect.php';
        }
        return $template;
    }
    return $template;
}

add_action('wp_ajax_azsl_social_login', 'azsl_social_login');
add_action('wp_ajax_nopriv_azsl_social_login', 'azsl_social_login');

function azsl_social_login() {
    if (isset($_POST['nonce'])) {
        if (wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'ajax')) {
            $user_id = email_exists(sanitize_email($_POST['email']));
            if ($user_id === false) {
                $user_login = sanitize_user(sanitize_email($_POST['email']), true);
                $user_login = explode('@', $user_login);
                $user_login = $user_login[0];
                if (username_exists($user_login)) {
                    $i = 1;
                    $user_login_tmp = $user_login;
                    do {
                        $user_login_tmp = $user_login . '_' . ($i++);
                    } while (username_exists($user_login_tmp));
                    $user_login = $user_login_tmp;
                }
                $user_fields = array(
                    'user_login' => $user_login,
                    'display_name' => sanitize_text_field($_POST['name']),
                    'user_email' => sanitize_email($_POST['email']),
                    'first_name' => sanitize_text_field($_POST['first_name']),
                    'last_name' => sanitize_text_field($_POST['last_name']),
                    'user_url' => '',
                    'user_pass' => wp_generate_password(),
                    'role' => get_option('default_role')
                );
                //$_POST['timezone']            
                $user_id = wp_insert_user($user_fields);
                // wp_insert_user may fail on first and last name meta, expliciting setting to correct.
                update_user_meta($user_id, 'first_name', apply_filters('pre_user_first_name', $user_fields['first_name']));
                update_user_meta($user_id, 'last_name', apply_filters('pre_user_last_name', $user_fields['last_name']));
                do_action('azsl_social_login_insert_user', $user_id);
            }
            $user_data = get_userdata($user_id);
            if ($user_data !== false) {
                wp_clear_auth_cookie();
                wp_set_auth_cookie($user_data->ID, true);
                do_action('wp_login', $user_data->user_login, $user_data);
                do_action('azsl_social_login', $user_id);
            }
        }
    }
    wp_die();
}

function azsl_social_login_links() {
    $options = get_option('azsl-settings');
    global $azsl_social_networks;
    global $azsl_social_networks_icons;
    $links = '';
    foreach ($azsl_social_networks as $network) {
        if (isset($options[$network . '_id']) && !empty($options[$network . '_id'])) {
            $links .= '<a href="' . add_query_arg('social-login', $network) . '"><i class="' . $azsl_social_networks_icons[$network] . '"></i></a>';
        }
    }
    return $links;
}
