<?php if (is_user_logged_in()): ?>
    <?php
    $type = 'AZEXO_Dashboard_Links';
    global $wp_widget_factory;
    if (is_object($wp_widget_factory) && isset($wp_widget_factory->widgets, $wp_widget_factory->widgets[$type])) {
        the_widget($type, array('title' => esc_html__('My account', 'foodpicky')));
    }
    ?>
<?php else: ?>
    <div class="social-login">
        <label><?php esc_html_e('Login with Social Networks:', 'foodpicky'); ?></label> <?php print (function_exists('azsl_social_login_links') ? azsl_social_login_links() : ''); ?>
    </div>
<?php endif; ?>

