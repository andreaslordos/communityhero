<?php
/**
 * Customer new account email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php
if (function_exists('azev_get_verification_link')) {

    $options = get_option('azev-settings');
    $verification_link = azev_get_verification_link($user_login);
    $user = get_user_by('login', $user_login);
    $email_body = $options['registration_email_body'];
    $email_body = str_replace('{{verification_link}}', '<a target="_blank" href="' . esc_url($verification_link) . '">' . $verification_link . '</a>', $email_body);
    $email_body = str_replace('{{verification_url}}', $verification_link, $email_body);
    $email_body = str_replace('{{user_email}}', $user->user_email, $email_body);
    $email_body = str_replace('{{user_login}}', $user->user_login, $email_body);
    $email_body = str_replace('{{user_firstname}}', $user->first_name, $email_body);
    $email_body = str_replace('{{user_lastname}}', $user->last_name, $email_body);
    $email_body = str_replace('{{user_displayname}}', $user->display_name, $email_body);

    if (get_option('woocommerce_registration_generate_password') == 'yes' && $password_generated) {
        $email_body = str_replace('{{user_pass}}', '<p>' . $options['password_message'] . '<strong> ' . $user_pass . '</strong></p>', $email_body);
    } else {
        $email_body = str_replace('{{user_pass}}', '', $email_body);
    }
    print $email_body;
} else {
    ?><p><?php printf(wp_kses(__("Thanks for creating an account on %s. Your username is <strong>%s</strong>", 'azev'), array('strong')), esc_html($blogname), esc_html($user_login)); ?></p>

    <?php if ('yes' === get_option('woocommerce_registration_generate_password') && $password_generated) : ?>

        <p><?php printf(wp_kses(__("Your password has been automatically generated: <strong>%s</strong>", 'azev'), array('strong')), esc_html($user_pass)); ?></p>

    <?php endif; ?>

    <p><?php printf(esc_html__('You can access your account area to view your orders and change your password here: %s.', 'azev'), wc_get_page_permalink('myaccount')); ?></p><?php
}
?>

<?php do_action('woocommerce_email_footer'); ?>
