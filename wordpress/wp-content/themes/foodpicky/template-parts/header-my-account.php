<?php
global $current_user;
wp_get_current_user();
?>


<div class="header-my-account <?php print (is_user_logged_in() ? 'logged-in' : '') ?>">
    <div class="dropdown">
        <input id="login-register-toggle" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
        <div class="link">
            <a href="<?php print ( function_exists('wc_get_page_permalink') ? esc_url(wc_get_page_permalink('myaccount')) : ''); ?>">
                <span><?php print (is_user_logged_in() ? esc_html($current_user->display_name) : esc_html__('Login/Register', 'foodpicky')) ?></span>
                <?php print (is_user_logged_in() ? get_avatar($current_user->ID) : '') ?>            
            </a>            
            <label for="login-register-toggle"></label>
        </div>
        <?php if (is_user_logged_in()): ?>
            <?php
            $type = 'AZEXO_Dashboard_Links';
            global $wp_widget_factory;
            if (is_object($wp_widget_factory) && isset($wp_widget_factory->widgets, $wp_widget_factory->widgets[$type])) {
                the_widget($type, array('title' => esc_html__('My account', 'foodpicky')));
            }
            ?>
        <?php else: ?>
            <div class="form">
                <label for="login-register-toggle"></label>
                <?php if (function_exists('wc_get_page_permalink') && !azexo_is_current_post(wc_get_page_id('myaccount'))): ?>
                    <input id="register-toggle" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <div></div>
                    <label for="register-toggle">
                        <span class="login"><?php esc_html_e('Already have an account?', 'foodpicky'); ?></span>
                        <span class="register"><?php esc_html_e("Don't have an account?", 'foodpicky'); ?></span>
                    </label>
                    <?php
                    wp_enqueue_script('wc-password-strength-meter');
                    if (function_exists('wc_get_template')) {
                        wc_get_template('myaccount/form-login.php');
                    }
                endif;
                ?>
                <div class="social-login">
                    <label><?php esc_html_e('Connect with Social Networks:', 'foodpicky'); ?></label> <?php print (function_exists('azsl_social_login_links') ? azsl_social_login_links() : ''); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>    
</div>
