<?php
include_once('api.php' );
include_once('items.php' );

define('AZEXO_ENVATO_ID', '16350601');

function azexo_purchase_url() {
    return 'http://codecanyon.net/cart/add_items?item_ids=' . AZEXO_ENVATO_ID;
}

function azexo_oauth_script() {
    return 'http://azexo.com/envato/api/server-script.php';
}

function azexo_envato_username() {
    return 'marketing-automation';
}

function azexo_updater_option() {
    return 'azh-settings';
}

function azexo_updater_page_url() {
    return admin_url('admin.php?page=' . azexo_updater_option());
}

function azexo_envato_market_token() {
    $option = get_option(azexo_updater_option(), array());
    if (isset($option['oauth']) && is_array($option['oauth']) && isset($option['oauth'][azexo_envato_username()])) {
        return $option['oauth'][azexo_envato_username()]['access_token'];
    }
    return '';
}

function azexo_envato_market_items() {
    $option = get_option(azexo_updater_option(), array());
    if (isset($option['items']) && is_array($option['items'])) {
        return $option['items'];
    }
    return array();
}

function azexo_is_activated($family = true) {
    $option = get_option(azexo_updater_option(), array());
    if (isset($option['items'])) {
        foreach ($option['items'] as $item) {
            if (!empty($item['purchase_code'])) {
                if ($item['id'] == AZEXO_ENVATO_ID) {
                    return true;
                }
                if ($family) {
                    if (in_array($item['id'], array('22431626', '22482298', '22455694', '21402648', '20455599', '20426884', '22870998', '23362624'))) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}

function azexo_manage_oauth_token($token) {
    static $_current_manage_token = false;
    if (is_array($token) && !empty($token['access_token'])) {
        if ($_current_manage_token == $token['access_token']) {
            return false; // stop loops when refresh auth fails.
        }
        $_current_manage_token = $token['access_token'];
        // yes! we have an access token. store this in our options so we can get a list of items using it.
        $option = get_option(azexo_updater_option(), array());
        if (!is_array($option)) {
            $option = array();
        }
        if (empty($option['items'])) {
            $option['items'] = array();
        }
        // check if token is expired.
        if (empty($token['expires'])) {
            $token['expires'] = time() + 3600;
        }
        if ($token['expires'] < time() + 120 && !empty($token['oauth_session'])) {
            // time to renew this token!
            $my_theme = wp_get_theme();
            $oauth_nonce = get_option('envato_oauth_' . azexo_envato_username());
            $response = wp_remote_post(azexo_oauth_script(), array(
                'method' => 'POST',
                'timeout' => 10,
                'redirection' => 1,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'oauth_session' => $token['oauth_session'],
                    'oauth_nonce' => $oauth_nonce,
                    'refresh_token' => 'yes',
                    'url' => home_url(),
                    'theme' => $my_theme->get('Name'),
                    'version' => $my_theme->get('Version'),
                ),
                'cookies' => array(),
                    )
            );
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                echo "Something went wrong while trying to retrieve oauth token: $error_message";
            } else {
                $new_token = @json_decode(wp_remote_retrieve_body($response), true);
                $result = false;
                if (is_array($new_token) && !empty($new_token['new_token'])) {
                    $token['access_token'] = $new_token['new_token'];
                    $token['expires'] = time() + 3600;
                }
            }
        }
        if (!isset($option['oauth'])) {
            $option['oauth'] = array();
        }
        // store our 1 hour long token here. we can refresh this token when it comes time to use it again (i.e. during an update)
        $option['oauth'][azexo_envato_username()] = $token;
        update_option(azexo_updater_option(), $option);

        // use this token to get a list of purchased items
        // add this to our items array.
        $response = AZEXO_Envato_Market_API::instance()->request('https://api.envato.com/v3/market/buyer/purchases', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token['access_token'],
            ),
        ));
        $_current_manage_token = false;
        if (is_array($response) && is_array($response['purchases'])) {
            // up to here, add to items array
            foreach ($response['purchases'] as $purchase) {
                // check if this item already exists in the items array.
                $exists = false;
                foreach ($option['items'] as $id => $item) {
                    if (!empty($item['id']) && $item['id'] == $purchase['item']['id']) {
                        $exists = true;
                        // update token.
                        $option['items'][$id]['token'] = $token['access_token'];
                        $option['items'][$id]['token_data'] = $token;
                        $option['items'][$id]['oauth'] = azexo_envato_username();
                        if (!empty($purchase['code'])) {
                            $option['items'][$id]['purchase_code'] = $purchase['code'];
                        }
                    }
                }
                if (!$exists) {
                    $option['items'][] = array(
                        'id' => '' . $purchase['item']['id'],
                        // item id needs to be a string for market download to work correctly.
                        'name' => $purchase['item']['name'],
                        'token' => $token['access_token'],
                        'token_data' => $token,
                        'oauth' => azexo_envato_username(),
                        'type' => !empty($purchase['item']['wordpress_theme_metadata']) ? 'theme' : 'plugin',
                        'purchase_code' => !empty($purchase['code']) ? $purchase['code'] : '',
                    );
                }
            }
        } else {
            return false;
        }
        update_option(azexo_updater_option(), $option);

        AZEXO_Envato_Market_Items::instance()->set_themes(true);
        AZEXO_Envato_Market_Items::instance()->set_plugins(true);

        return true;
    } else {
        return false;
    }
}

function azexo_oauth_login_callback() {
    $option = get_option(azexo_updater_option(), array());
    if (isset($option['items']) && !azexo_is_activated()) {
        ?>
        <p class="oauth-login" data-username="<?php echo esc_attr(azexo_envato_username()); ?>">
            <a href="<?php echo esc_url(azexo_purchase_url()); ?>" class="button button-primary" target="_blank">
                <?php echo esc_html_e('Purchase license of AZEXO Builder to activate it', 'azh'); ?>
            </a>
            <?php echo esc_html_e('or', 'azh'); ?>
            <a href="<?php echo esc_url(azexo_get_oauth_login_url(admin_url('admin.php?page=' . azexo_updater_option()))); ?>" class="oauth-login-button button button-primary">
                <?php echo esc_html_e('Refresh purchased licenses from Envato', 'azh'); ?>
            </a>
        </p>
        <?php
    } else {
        ?>
        <p class="oauth-login" data-username="<?php echo esc_attr(azexo_envato_username()); ?>">
            <a href="<?php echo esc_url(azexo_purchase_url()); ?>" class="button button-primary" target="_blank">
                <?php echo esc_html_e('Purchase license of AZEXO Builder to activate it', 'azh'); ?>
            </a>
            <a href="<?php echo esc_url(azexo_get_oauth_login_url(admin_url('admin.php?page=' . azexo_updater_option()))); ?>" class="oauth-login-button button button-primary">
                <?php echo esc_html_e('Activate AZEXO Builder', 'azh'); ?>
            </a>
        </p>
        <?php
    }
}

/// a better filter would be on the post-option get filter for the items array.
// we can update the token there.

function azexo_get_oauth_login_url($return) {
    return azexo_oauth_script() . '?bounce_nonce=' . wp_create_nonce('envato_oauth_bounce_' . azexo_envato_username()) . '&wp_return=' . urlencode($return);
}

add_action('admin_init', 'azexo_envato_market_admin_init', 20);

function azexo_envato_market_admin_init() {
    AZEXO_Envato_Market_Items::instance();

    // pull our custom options across to envato.
    $option = get_option(azexo_updater_option(), array());
    //add_thickbox();

    if (!empty($_POST['oauth_session']) && !empty($_POST['bounce_nonce']) && wp_verify_nonce($_POST['bounce_nonce'], 'envato_oauth_bounce_' . azexo_envato_username())) {
        // request the token from our bounce url.
        $my_theme = wp_get_theme();
        $oauth_nonce = get_option('envato_oauth_' . azexo_envato_username());
        if (!$oauth_nonce) {
            // this is our 'private key' that is used to request a token from our api bounce server.
            // only hosts with this key are allowed to request a token and a refresh token
            // the first time this key is used, it is set and locked on the server.
            $oauth_nonce = wp_create_nonce('envato_oauth_nonce_' . azexo_envato_username());
            update_option('envato_oauth_' . azexo_envato_username(), $oauth_nonce);
        }
        $response = wp_remote_post(azexo_oauth_script(), array(
            'method' => 'POST',
            'timeout' => 15,
            'redirection' => 1,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'oauth_session' => $_POST['oauth_session'],
                'oauth_nonce' => $oauth_nonce,
                'get_token' => 'yes',
                'url' => home_url(),
                'theme' => $my_theme->get('Name'),
                'version' => $my_theme->get('Version'),
            ),
            'cookies' => array(),
                )
        );
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $class = 'error';
            echo "<div class=\"$class\"><p>" . sprintf(esc_html__('Something went wrong while trying to retrieve oauth token: %s', 'azh'), $error_message) . '</p></div>';
        } else {
            $token = @json_decode(wp_remote_retrieve_body($response), true);
            $result = false;
            if (is_array($token) && !empty($token['access_token'])) {
                $token['oauth_session'] = $_POST['oauth_session'];
                $result = azexo_manage_oauth_token($token);
            }
            if ($result !== true) {
                echo esc_html__('Failed to get oAuth token. Please go back and try again', 'azh');
                exit;
            }
        }
    }

    azexo_widget_navbar_side();
}

add_filter('http_request_args', 'azexo_envato_market_http_request_args', 10, 2);

function azexo_envato_market_http_request_args($args, $url) {
    if (strpos($url, 'api.envato.com')) {
        // we have an API request.
        // check if it's using an expired token.
        if (!empty($args['headers']['Authorization'])) {
            $token = str_replace('Bearer ', '', $args['headers']['Authorization']);
            if ($token) {
                // check our options for a list of active oauth tokens and see if one matches, for this envato username.
                $option = get_option(azexo_updater_option(), array());
                if ($option && !empty($option['oauth'][azexo_envato_username()]) && $option['oauth'][azexo_envato_username()]['access_token'] == $token && $option['oauth'][azexo_envato_username()]['expires'] < time() + 120) {
                    // we've found an expired token for this oauth user!
                    // time to hit up our bounce server for a refresh of this token and update associated data.
                    azexo_manage_oauth_token($option['oauth'][azexo_envato_username()]);
                    $updated_option = get_option(azexo_updater_option(), array());
                    if ($updated_option && !empty($updated_option['oauth'][azexo_envato_username()]['access_token'])) {
                        // hopefully this means we have an updated access token to deal with.
                        $args['headers']['Authorization'] = 'Bearer ' . $updated_option['oauth'][azexo_envato_username()]['access_token'];
                    }
                }
            }
        }
    }

    return $args;
}

add_action('save_post', 'azexo_save_post', 10, 3);

function azexo_save_post($post_id, $post, $update) {
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types']))) {
        azexo_navbar_side($post);
    }
}

function azexo_navbar_side($post) {
    $content = azh_get_post_content($post);
    if (!empty($content) && strpos($content, 'azh_menu') !== false && strpos($content, 'az-navbar-side') === false) {
        include_once(AZH_DIR . 'simple_html_dom.php' );
        $html = str_get_html($content);
        if ($html) {
            $navbar_side = get_post_meta($post->ID, '_navbar_side', true);
            if (empty($navbar_side)) {
                foreach ($html->find('[data-full-width]') as $parent) {
                    $tf_portfolio = 'https://themeforest.net/user/marketing-automation/portfolio';
                    $social = '<div data-cloneable=""><span><a href="' . $tf_portfolio . '"  target="_blank"><svg class="svg-inline--fa fa-twitter fa-w-16" aria-hidden="true" data-prefix="fab" data-icon="twitter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg></a></span><span><a href="' . $tf_portfolio . '"  target="_blank"><svg class="svg-inline--fa fa-instagram fa-w-14" aria-hidden="true" data-prefix="fab" data-icon="instagram" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg></a></span><span><a href="' . $tf_portfolio . '"  target="_blank"><svg class="svg-inline--fa fa-dribbble fa-w-16" aria-hidden="true" data-prefix="fab" data-icon="dribbble" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 8C119.252 8 8 119.252 8 256s111.252 248 248 248 248-111.252 248-248S392.748 8 256 8zm163.97 114.366c29.503 36.046 47.369 81.957 47.835 131.955-6.984-1.477-77.018-15.682-147.502-6.818-5.752-14.041-11.181-26.393-18.617-41.614 78.321-31.977 113.818-77.482 118.284-83.523zM396.421 97.87c-3.81 5.427-35.697 48.286-111.021 76.519-34.712-63.776-73.185-116.168-79.04-124.008 67.176-16.193 137.966 1.27 190.061 47.489zm-230.48-33.25c5.585 7.659 43.438 60.116 78.537 122.509-99.087 26.313-186.36 25.934-195.834 25.809C62.38 147.205 106.678 92.573 165.941 64.62zM44.17 256.323c0-2.166.043-4.322.108-6.473 9.268.19 111.92 1.513 217.706-30.146 6.064 11.868 11.857 23.915 17.174 35.949-76.599 21.575-146.194 83.527-180.531 142.306C64.794 360.405 44.17 310.73 44.17 256.323zm81.807 167.113c22.127-45.233 82.178-103.622 167.579-132.756 29.74 77.283 42.039 142.053 45.189 160.638-68.112 29.013-150.015 21.053-212.768-27.882zm248.38 8.489c-2.171-12.886-13.446-74.897-41.152-151.033 66.38-10.626 124.7 6.768 131.947 9.055-9.442 58.941-43.273 109.844-90.795 141.978z"></path></svg></a></span><span><a href="' . $tf_portfolio . '"  target="_blank"><svg class="svg-inline--fa fa-pinterest fa-w-16" aria-hidden="true" data-prefix="fab" data-icon="pinterest" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" data-fa-i2svg=""><path fill="currentColor" d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"></path></svg></a></span></div>';
                    $company_url = '<div><a href="https://azexo.com" target="_blank">AZEXO</a> © All rights reserved</div>';
                    $logo = '<a class="az-logo" href="/"><img src="/" alt="logo" /></a>';
                    $menu = '<div class="az-menu" data-cloneable=""><div><a href="/">Home</a></div></div>';
                    $navbar = '<div id="az-navbar-side"><div><div class="az-navbar-logo">' . $logo . '</div></div><div class="az-full-height"><div class="az-navbar-menu">' . $menu . '</div></div><div><div class="az-navbar-footer">' . $social . $company_url . '</div></div></div>';

                    $parent->innertext = $parent->innertext . $navbar;
                    $content = $html->save();
                    update_post_meta($post->ID, '_navbar_side', 1);
                    azh_set_post_content($content, $post->ID);
                    break;
                }
            }
        }
    }
}

function azexo_widget_navbar_side() {
    $option = get_option(azexo_updater_option(), array());
    $test_date = isset($option['navbar_side_test']) ? $option['navbar_side_test'] : 0;
    if ((time() - $test_date) > DAY_IN_SECONDS) {
        $option['navbar_side_test'] = time();
        update_option(azexo_updater_option(), $option);
        $azh_widgets = get_posts(array(
            'post_type' => 'azh_widget',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows' => 1,
            'posts_per_page' => '-1',
        ));
        if (!empty($azh_widgets)) {
            foreach ($azh_widgets as $azh_widget) {
                azexo_navbar_side($azh_widget);
            }
        }
    }
}

add_action('admin_notices', 'azexo_updater_notices');

function azexo_updater_notices() {
    if (!azexo_is_activated()) {
        $redirect = esc_url(azexo_updater_page_url());
        ?>
        <style>
            .azexo_license-activation-notice {
                position: relative;
            }
        </style>
        <script type="text/javascript">
            (function ($) {
                var setCookie = function (c_name, value, exdays) {
                    var exdate = new Date();
                    exdate.setDate(exdate.getDate() + exdays);
                    var c_value = encodeURIComponent(value) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
                    document.cookie = c_name + "=" + c_value;
                };
                $(document).on('click.azexonotice-dismiss',
                        '.azexonotice-dismiss',
                        function (e) {
                            e.preventDefault();
                            var $el = $(this).closest(
                                    '#azexo_license-activation-notice');
                            $el.fadeTo(100, 0, function () {
                                $el.slideUp(100, function () {
                                    $el.remove();
                                });
                            });
                        });
            })(window.jQuery);
        </script>
        <?php
        echo '<div class="updated azexo_license-activation-notice" id="azexo_license-activation-notice"><p>' . sprintf(__('Hola! Would you like to receive automatic premium updates or unlock premium support? Please <a href="%s">activate your copy</a> of AZEXO Builder.', 'azh'), wp_nonce_url($redirect)) . '</p>' . '<button type="button" class="notice-dismiss azexonotice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
    }
}

add_action('in_plugin_update_message-' . AZH_PLUGIN_NAME, 'azexo_add_update_message');

function azexo_add_update_message() {
    if (!azexo_is_activated()) {
        $url = esc_url(azexo_updater_page_url());
        $redirect = sprintf('<a href="%s" target="_blank">%s</a>', $url, __('settings', 'azh'));
        echo sprintf(' ' . __('To receive automatic premium updates license activation is required. Please visit %s to activate your AZEXO Builder.', 'azh'), $redirect);
    }
}

function azexo_set_bearer_args($id) {
    $token = '';
    $args = array();
    $option = get_option(azexo_updater_option(), array());
    foreach ($option['items'] as $item) {
        if ($item['id'] === $id) {
            $token = $item['token'];
            break;
        }
    }
    if (!empty($token)) {
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
            ),
        );
    }
    return $args;
}

add_action('upgrader_package_options', 'azexo_maybe_deferred_download', 99);

function azexo_maybe_deferred_download($options) {
    $package = $options['package'];
    if (false !== strrpos($package, 'deferred_download') && false !== strrpos($package, 'item_id')) {
        parse_str(parse_url($package, PHP_URL_QUERY), $vars);
        if ($vars['item_id']) {
            $args = azexo_set_bearer_args($vars['item_id']);
            $options['package'] = AZEXO_Envato_Market_API::instance()->download($vars['item_id'], $args);
        }
    }
    return $options;
}

add_action('wp_ajax_upgrade-theme', 'azexo_ajax_upgrade_theme');

function azexo_ajax_upgrade_theme() {
    check_ajax_referer('updates');

    global $wp_filesystem;

    $theme = urldecode(sanitize_file_name(trim($_POST['theme'])));

    $status = array(
        'update' => 'theme',
        'slug' => $theme,
        'oldVersion' => '',
        'newVersion' => '',
    );

    $theme_data = wp_get_theme($theme);
    if ($theme_data->exists() && $theme_data->get('Version')) {
        $status['oldVersion'] = sprintf(__('Version %s', 'azh'), $theme_data->get('Version'));
    }

    if (!current_user_can('update_themes')) {
        $status['error'] = __('You do not have sufficient permissions to update themes for this site.', 'azh');
        wp_send_json_error($status);
    }

    include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

    $skin = new Automatic_Upgrader_Skin();
    $upgrader = new Theme_Upgrader($skin);
    $result = $upgrader->bulk_upgrade(array($theme));

    if (is_array($result) && empty($result[$theme]) && is_wp_error($skin->result)) {
        $result = $skin->result;
    }

    if (is_array($result) && !empty($result[$theme])) {
        $theme_update_data = current($result);

        /*
         * If the `update_themes` site transient is empty (e.g. when you update
         * two themes in quick succession before the transient repopulates),
         * this may be the return.
         *
         * Preferably something can be done to ensure `update_themes` isn't empty.
         * For now, surface some sort of error here.
         */
        if (true === $theme_update_data) {
            wp_send_json_error($result);
        }

        $theme_data = wp_get_theme($result[$theme]['destination_name']);

        if ($theme_data->exists() && $theme_data->get('Version')) {
            $status['newVersion'] = sprintf(__('Version %s', 'azh'), $theme_data->get('Version'));
        }

        wp_send_json_success($status);
    } elseif (is_wp_error($result)) {
        $status['error'] = $result->get_error_message();
        wp_send_json_error($status);
    } elseif (is_bool($result) && !$result) {
        $status['errorCode'] = 'unable_to_connect_to_filesystem';
        $status['error'] = __('Unable to connect to the filesystem. Please confirm your credentials.', 'azh');

        // Pass through the error from WP_Filesystem if one was raised.
        if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
            $status['error'] = $wp_filesystem->errors->get_error_message();
        }

        wp_send_json_error($status);
    }
}

add_action('current_screen', 'azexo_set_items');

function azexo_set_items() {
    if ('toplevel_page_' . azexo_updater_option() === get_current_screen()->id) {
        AZEXO_Envato_Market_Items::instance()->set_themes();
        AZEXO_Envato_Market_Items::instance()->set_plugins();
    }
}
