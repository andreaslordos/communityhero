<?php

/**
 * Plugin Name:  AZEXO WooCommerce Email Verification
 * Plugin URI:   http://www.azexo.com
 * Description:  WooCommerce Email Verification
 * Author:       AZEXO
 * Author URI:   http://www.azexo.com
 * Version: 1.27
 * Text Domain:  azev
 * Domain Path:  languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('AZEV_URL', plugins_url('', __FILE__));
define('AZEV_DIR', trailingslashit(dirname(__FILE__)));
define('AZEXO_APPROVE_USER', 'user-approved');

if (is_admin()) {
    require_once AZEV_DIR . 'settings.php';
}

add_action('plugins_loaded', 'azev_plugins_loaded');

function azev_plugins_loaded() {
    load_plugin_textdomain('azev', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_filter('woocommerce_locate_template', 'azev_woocommerce_locate_template', 10, 3);

function azev_woocommerce_locate_template($template, $template_name, $template_path) {
    global $woocommerce;

    $_template = $template;

    $plugin_path = plugin_dir_path(__FILE__) . 'woocommerce/';

    $template = $plugin_path . $template_name;
    if (!file_exists($template)) {
        $template = $_template;
    }

    return $template;
}


add_action('after_switch_theme', 'azev_switch_theme');

function azev_switch_theme() {
    update_user_meta(get_current_user_id(), AZEXO_APPROVE_USER, true);
}

function azev_get_verification_link($user_login) {
    global $wpdb;
    $key = wp_generate_password(20, false);
    $wpdb->query($wpdb->prepare("update " . $wpdb->prefix . "users set user_activation_key = %s where user_login = %s", $key, $user_login));
    return $verification_link = wc_get_page_permalink('myaccount') . '?action=verify_account&user_login=' . urlencode($user_login) . '&key=' . urlencode($key);
}

add_action('woocommerce_registration_redirect', 'azev_user_autologout', 20);

function azev_user_autologout() {
    if (is_user_logged_in()) {
        wp_logout();
        return wc_get_page_permalink('myaccount') . '?registered=true';
    }
}

add_action('template_redirect', 'azev_verify_user');
add_action('template_redirect', 'azev_resend_key');

function azev_verify_user() {
    if (!is_user_logged_in()) {
        if (isset($_GET['action']) && $_GET['action'] == 'verify_account') {
            $verify = 'false';
            if (isset($_GET['user_login']) && isset($_GET['key'])) {
                global $wpdb;
                $user = $wpdb->get_row($wpdb->prepare("select * from " . $wpdb->prefix . "users where user_login = %s and user_activation_key = %s", $_GET['user_login'], $_GET['key']));
                if ($user) {
                    $verify = 'true';
                    update_user_meta($user->ID, AZEXO_APPROVE_USER, true);
                    $key = wp_generate_password(20, false);
                    $wpdb->query($wpdb->prepare("update " . $wpdb->prefix . "users set user_activation_key = %s where ID = %d", $key, $user->ID));
                }
            }
            wp_redirect(wc_get_page_permalink('myaccount') . '?verify=' . $verify);
            die;
        }
    }
}

function azev_resend_key() {
    if (!is_user_logged_in()) {
        if (isset($_GET['action']) && $_GET['action'] == 'resend_key') {
            $resend = 'false';
            if (isset($_GET['user_login']) && isset($_GET['nonce'])) {
                if (wp_verify_nonce($_GET['nonce'], 'resend_key' . $_GET['user_login'])) {
                    global $wpdb;

                    $user = get_user_by('login', $_GET['user_login']);
                    if ($user) {
                        global $woocommerce;
                        $mailer = $woocommerce->mailer();

                        $resend = 'true';
                        $verification_link = azev_get_verification_link($user->user_login);

                        $options = get_option('azev-settings');

                        $email_heading = $options['resend_email_heading'];
                        $subject = $options['resend_email_subject'];

                        $email_body = $options['resend_email_body'];
                        $email_body = str_replace('{{verification_link}}', '<a target="_blank" href="' . esc_url($verification_link) . '">' . $verification_link . '</a>', $email_body);
                        $email_body = str_replace('{{verification_url}}', $verification_link, $email_body);
                        $email_body = str_replace('{{user_email}}', $user->user_email, $email_body);
                        $email_body = str_replace('{{user_login}}', $user->user_login, $email_body);
                        $email_body = str_replace('{{user_firstname}}', $user->first_name, $email_body);
                        $email_body = str_replace('{{user_lastname}}', $user->last_name, $email_body);
                        $email_body = str_replace('{{user_displayname}}', $user->display_name, $email_body);

                        ob_start();
                        woocommerce_get_template('emails/resend-verification-key.php', array(
                            'email_heading' => $email_heading,
                            'email_body' => $email_body
                        ));
                        $message = ob_get_clean();
                        woocommerce_mail($user->user_email, $subject, $message, $headers = "Content-Type: text/htmlrn", $attachments = "");
                    }
                    wp_redirect(wc_get_page_permalink('myaccount') . '?resend=' . $resend);
                    die;
                }
            }
        }
    }
}

add_action('woocommerce_before_customer_login_form', 'azev_reg_success_message');
add_action('woocommerce_before_customer_login_form', 'azev_resend_message');
add_filter('woocommerce_before_customer_login_form', 'azev_verification_message', 10, 3);

function azev_verification_message() {
    if (isset($_GET['verify'])) {
        $options = get_option('azev-settings');
        if ($_GET['verify'] == 'false') {
            print "<div class='woocommerce-error'>" . wp_kses($options['verification_fail'], array('strong' => array())) . "</div>";
        }
        if ($_GET['verify'] == 'true') {
            print "<div class='woocommerce-message'>" . wp_kses($options['verification_success'], array('strong' => array())) . "</div>";
        }
    }
}

function azev_reg_success_message() {
    if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
        $options = get_option('azev-settings');
        ?>
        <div class="woocommerce-message">
            <?php print wp_kses($options['registration_success'], array('strong' => array())); ?>
        </div><?php
    }
}

function azev_resend_message() {
    if (isset($_GET['resend']) && $_GET['resend'] == 'true') {
        $options = get_option('azev-settings');
        ?>
        <div class="woocommerce-message">
            <?php print wp_kses($options['new_verification_link'], array('strong' => array())); ?>
        </div><?php
    }
}

add_filter('wp_authenticate_user', 'azev_check_user_status_before_login', 10, 3);

function azev_check_user_status_before_login($userdata) {
    $options = get_option('azev-settings');
    if (isset($options['restrict_roles']) && is_array($options['restrict_roles']) && is_array($userdata->roles)) {
        $common_values = array_intersect(array_keys($options['restrict_roles']), $userdata->roles);
        if (count($common_values) == 0 && !in_array('administrator', $userdata->roles)) {
            $is_approved = get_user_meta($userdata->ID, AZEXO_APPROVE_USER, true);
            if (!$is_approved || $is_approved == 0) {
                $regenerate_link = wc_get_page_permalink('myaccount') . '?action=resend_key&user_login=' . $userdata->user_login . '&nonce=' . wp_create_nonce('resend_key' . $userdata->user_login);
                $userdata = new WP_Error(
                        'az_confirmation_error', str_replace('{{resend_verification_link}}', '<a href="' . esc_url($regenerate_link) . '">' . esc_html($options['resend_link_text']) . '</a>', wp_kses($options['fail_login'], array('strong' => array())))
                );
            }
        }
    }
    return $userdata;
}

add_filter('manage_users_columns', 'azev_manage_user_columns');

function azev_manage_user_columns($column) {
    $column['status'] = 'Status';
    return $column;
}

add_filter('manage_users_custom_column', 'azev_manage_user_column_row', 10, 3);

function azev_manage_user_column_row($val, $column_name, $user_id) {
    $user = get_userdata($user_id);
    switch ($column_name) {
        case 'status' :
            $status = get_user_meta($user_id, AZEXO_APPROVE_USER, true);
            $val = '<span style="color:red;">' . esc_html__('Not Verified', 'azev') . '</span>';
            if ($status && $status == 1) {
                $val = '<span style="color:green;">' . esc_html__('Verified', 'azev') . '</span>';
            }
            return $val;
            break;
        default:
    }
    return $return;
}

add_filter('user_row_actions', 'azev_user_table_action_links', 10, 2);

function azev_user_table_action_links($actions, $user) {
    $is_approved = get_user_meta($user->ID, AZEXO_APPROVE_USER, true);
    $actions['az_status'] = "<a style='color:" . ((!$is_approved || $is_approved == 0) ? 'green' : 'red') . "' href='" . esc_url(admin_url("users.php?action=az_change_status&users=" . $user->ID . "&nonce=" . wp_create_nonce('az_change_status_' . $user->ID))) . "'>" . ((!$is_approved || $is_approved == 0) ? esc_html__('Approve', 'azev') : esc_html__('Unapprove', 'azev')) . "</a>";
    return $actions;
}

add_action('admin_action_az_change_status', 'azev_change_status');

function azev_change_status() {
    if (isset($_REQUEST['users']) && isset($_REQUEST['nonce'])) {
        $nonce = $_REQUEST['nonce'];
        $users = $_REQUEST['users'];

        if (wp_verify_nonce($nonce, 'az_change_status_' . $users)) {
            $is_approved = get_user_meta($users, AZEXO_APPROVE_USER, true);
            if (!$is_approved || $is_approved == 0) {
                $new_status = 1;
                $message_param = 'approved';
            } else {
                $new_status = 0;
                $message_param = 'unapproved';
            }
            update_user_meta($users, AZEXO_APPROVE_USER, $new_status);
            $redirect = admin_url('users.php?updated=' . $message_param);
        } else {
            $redirect = admin_url('users.php?updated=az_false');
        }
    } else {
        $redirect = admin_url('users.php?updated=az_false');
    }
    wp_redirect($redirect);
}

add_action('admin_notices', 'azev_change_status_notices');

function azev_change_status_notices() {
    global $pagenow;
    if ($pagenow == 'users.php') {
        if (isset($_REQUEST['updated'])) {
            $message = $_REQUEST['updated'];
            if ($message == 'az_false') {
                print '<div class="updated notice error is-dismissible"><p>' . esc_html__('Something wrong. Please try again.', 'azev') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azev') . '</span></button></div>';
            }
            if ($message == 'approved') {
                print '<div class="updated notice is-dismissible"><p>' . esc_html__('User approved.', 'azev') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azev') . '</span></button></div>';
            }
            if ($message == 'unapproved') {
                print '<div class="updated notice is-dismissible"><p>' . esc_html__('User unapproved.', 'azev') . '</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . esc_html__('Dismiss this notice.', 'azev') . '</span></button></div>';
            }
        }
    }
}
