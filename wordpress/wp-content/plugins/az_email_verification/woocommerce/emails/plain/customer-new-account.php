<?php

/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails/Plain
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

echo "= " . $email_heading . " =\n\n";

echo sprintf(wp_kses(__("Thanks for creating an account on %s. Your username is <strong>%s</strong>.", 'azev'), array('strong' => array())), $blogname, $user_login) . "\n\n";

if (get_option('woocommerce_registration_generate_password') === 'yes' && $password_generated)
    echo sprintf(wp_kses(__("Your password is <strong>%s</strong>.", 'azev'), array('strong' => array())), $user_pass) . "\n\n";


if (function_exists('azev_get_verification_link')) {
    $verification_link = azev_get_verification_link($user_login);
    echo esc_html__('Click below link to verify your account.', 'azev') . "\n\n";
    print $verification_link . "\n\n";
}

echo sprintf(esc_html__('You can access your account area to view your orders and change your password here: %s.', 'azev'), wc_get_page_permalink('myaccount')) . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
