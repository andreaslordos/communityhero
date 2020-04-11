<?php
/*
  Field Name: Product price drop alert
 */
?>
<?php
$product = wc_get_product();
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'price-drop-alert-remove') {
    if (is_user_logged_in()) {
        $alerts = get_user_meta(get_current_user_id(), 'price-drop-alert');
        if ($alerts && is_array($alerts)) {
            foreach ($alerts as $alert) {
                if ($alert['id'] == $product->get_id()) {
                    delete_user_meta(get_current_user_id(), 'price-drop-alert', $alert);
                    break;
                }
            }
        }
        if (class_exists('BNFW')) {
            $bnfw = BNFW::factory();
            $notifications = $bnfw->notifier->get_notifications('update-product');
            foreach ($notifications as $notification) {
                $users = get_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', true);
                $users = array_diff($users, array(get_current_user_id()));
                delete_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users');
                add_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', $users);
            }
        }
    }
}
?>

<div class="price-drop-alert">
    <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'price-drop-alert') : ?>
        <?php
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
        } else {
            if (isset($_REQUEST['email'])) {
                $email = sanitize_email($_REQUEST['email']);
                $user = get_user_by('email', $email);
                if ($user && is_object($user)) {
                    $user_id = $user->ID;
                } else {
                    $user_login = sanitize_user(sanitize_email($email), true);
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
                    $user_id = register_new_user($user_login, $email);
                }
                if (!is_wp_error($user_id)) {
                    
                }
            }
        }
        delete_user_meta($user_id, 'price-drop-alert', array('id' => $product->get_id(), 'expected-price' => sanitize_text_field($_REQUEST['expected-price'])));
        add_user_meta($user_id, 'price-drop-alert', array('id' => $product->get_id(), 'expected-price' => sanitize_text_field($_REQUEST['expected-price'])));
        if (class_exists('BNFW')) {
            $bnfw = BNFW::factory();
            $notifications = $bnfw->notifier->get_notifications('update-product');
            foreach ($notifications as $notification) {
                $users = get_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', true);
                $users[] = $user_id;
                $users = array_unique($users);
                delete_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users');
                add_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', $users);
            }
        }
        ?>
        <p class="info"><?php esc_html_e('Price drop alert created', 'foodpicky'); ?></p>
    <?php else: ?>
        <?php
        $expected_price = false;
        if (is_user_logged_in()) {
            $alerts = get_user_meta(get_current_user_id(), 'price-drop-alert');
            if ($alerts && is_array($alerts)) {
                foreach ($alerts as $alert) {
                    if ($alert['id'] == $product->get_id()) {
                        $expected_price = $alert['expected-price'];
                    }
                }
            }
        }
        ?>
        <?php if ($expected_price) : ?>
            <p class="info"><label><?php esc_html_e('Expected price drop alert:', 'foodpicky'); ?></label> <span class="expected-price"><?php print wc_price($expected_price); ?></span><a href="<?php print esc_url(add_query_arg('action', 'price-drop-alert-remove')); ?>" class="remove"><?php esc_attr_e('Remove', 'foodpicky'); ?></a></p>
        <?php else: ?>
            <form method="post" action="<?php print esc_url(get_permalink()); ?>">
                <input type="hidden" name="action" value="price-drop-alert" />
                <p class="expected-price"><input type="number" name="expected-price" value="" placeholder="<?php esc_attr_e('Expected price', 'foodpicky'); ?>" /></p>
                <?php if (!is_user_logged_in()) : ?>
                    <p class="email"><input type="email" name="email" required="required" value="" placeholder="<?php esc_attr_e('Your email', 'foodpicky'); ?>" /></p>
                <?php endif; ?>
                <p class="submit"><input type="submit" value="<?php esc_html_e('Get price-drop alert', 'foodpicky'); ?>" /></p>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>