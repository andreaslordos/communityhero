<?php

add_filter('pre_get_posts', 'azls_pre_get_posts');

function azls_pre_get_posts($query) {
    if (!current_user_can('manage_options') && !is_admin() && $query->is_main_query()) {
        if (function_exists('cmb2_get_option')) {
            $subsciption_post_type = cmb2_get_option('azl_options', 'subsciption_post_type');
            if (!empty($subsciption_post_type)) {
                if (azl_is_post_type_query($query, $subsciption_post_type)) {
                    $meta_query = $query->get('meta_query');
                    $meta_query[] = array(
                        'key' => '_azl_listed',
                        'value' => '1',
                    );
                    $query->set('meta_query', $meta_query);
                }
            }
        }
    }
    return $query;
}

function azl_set_listed_posts($user_id, $list) {
    if (function_exists('cmb2_get_option')) {
        $subsciption_post_type = cmb2_get_option('azl_options', 'subsciption_post_type');
        if (!empty($subsciption_post_type)) {
            $posts = get_posts(array(
                'numberposts' => -1,
                'suppress_filters' => false,
                'post_type' => $subsciption_post_type,
                'post_author' => $user_id,
            ));
            if (is_array($posts)) {
                foreach ($posts as $post) {
                    update_post_meta($post->ID, '_azl_listed', $list);
                }
            }
            return $posts;
        }
    }
    return array();
}

if (class_exists('WC_Subscriptions') && class_exists('WC_Subscriptions_Manager')) {

    function azl_get_wc_subscriptions($user_id) {
        $subscriptions = WC_Subscriptions_Manager::get_users_subscriptions($user_id);
        if (function_exists('cmb2_get_option')) {
            $default_subsciption = cmb2_get_option('azl_options', 'default_subsciption');
            if (is_numeric($default_subsciption)) {
                $subscriptions[] = array(
                    'status' => 'active',
                    'product_id' => $default_subsciption,
                );
            }
        }
        return $subscriptions;
    }

    add_filter('woocommerce_add_to_cart_redirect', 'azl_add_to_cart_redirect');

    function azl_add_to_cart_redirect($url) {
        if (isset($_REQUEST['add-to-cart'])) {

            $product_id = (int) apply_filters('woocommerce_add_to_cart_product_id', $_REQUEST['add-to-cart']);

            if (WC_Subscriptions_Product::is_subscription($product_id)) {
                return WC()->cart->get_checkout_url();
            }
        }
        return $url;
    }

    add_action('updated_users_subscriptions', 'azl_refresh_listed_posts');

    function azl_refresh_listed_posts($user_id) {
        $user = get_userdata($user_id);
        if ($user->has_cap('manage_options')) {
            azl_set_listed_posts($user_id, 1);
        } else {
            $subscriptions = azl_get_wc_subscriptions($user_id);
            $list = 0;
            $limit_max = 0;
            foreach ($subscriptions as $subscription) {
                if ($subscription['status'] == 'active') {
                    $limit = get_post_meta($subscription['product_id'], AZL_LISTING_LIMIT, true);
                    if ($limit_max < $limit) {
                        $limit_max = $limit;
                    }
                    $list = 1;
                }
            }
            $posts = azl_set_listed_posts($user_id, 0);
            if ($list) {
                $i = 0;
                foreach ($posts as $post) {
                    update_post_meta($post->ID, '_azl_listed', 1);
                    if ($i >= $limit_max) {
                        break;
                    }
                    $i++;
                }
            }
        }
    }

    add_action('wp_insert_post', 'azl_wp_insert_post', 10, 3);

    function azl_wp_insert_post($post_ID, $post, $update) {
        if (function_exists('cmb2_get_option')) {
            $subsciption_post_type = cmb2_get_option('azl_options', 'subsciption_post_type');
            if ($post->post_type == $subsciption_post_type) {
                azl_refresh_listed_posts($post->post_author);
            }
        }
    }

    function azl_current_user_subscriptions($args, $query) {
        global $wpdb;

        $args['where'] = str_replace("('visible','catalog')", "('visible','catalog','hidden')", $args['where']);

        $subscriptions = azl_get_wc_subscriptions(get_current_user_id());
        $ids = array();
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                $ids[] = $subscription['product_id'];
            }
        }
        if (empty($ids)) {
            $ids = array(0);
        }
        $args['where'] .= " AND ( $wpdb->posts.ID IN (" . esc_sql(implode(',', $ids)) . ") ) ";

        return $args;
    }

} elseif (function_exists('yith_ywsbs_constructor')) {

    function azl_get_yith_subscriptions($user_id) {
        $subscriptions = get_posts(array(
            'post_type' => YWSBS_Subscription()->post_type_name,
            'meta_query' => array(
                array(
                    'key' => '_user_id',
                    'value' => $user_id,
                ),
                array(
                    'key' => '_status',
                    'value' => 'active',
                ),
            )
        ));
        return $subscriptions;
    }

    function azl_get_subscriptions_products($user_id) {
        $subscriptions = azl_get_yith_subscriptions($user_id);
        $ids = array();
        if (is_array($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                $ids[] = get_post_meta($subscription->ID, '_product_id', true);
            }
        }
        if (function_exists('cmb2_get_option')) {
            $default_subsciption = cmb2_get_option('azl_options', 'default_subsciption');
            if (is_numeric($default_subsciption)) {
                $ids[] = $default_subsciption;
            }
        }
        return $ids;
    }

    add_filter('woocommerce_add_to_cart_redirect', 'azl_add_to_cart_redirect');

    function azl_add_to_cart_redirect($url) {
        if (isset($_REQUEST['add-to-cart'])) {

            $product_id = (int) apply_filters('woocommerce_add_to_cart_product_id', $_REQUEST['add-to-cart']);

            if (YITH_WC_Subscription::get_instance()->is_subscription($product_id)) {
                return WC()->cart->get_checkout_url();
            }
        }
        return $url;
    }

    add_action('ywsbs_subscription_cancelled', 'azl_subscription_cancelled');

    function azl_subscription_cancelled($subscription_id) {
        $user_id = get_post_meta($subscription_id, '_user_id', true);
        if (!empty($user_id)) {
            azl_refresh_listed_posts($user_id);
        }
    }

    add_action('woocommerce_payment_complete', 'azl_payment_complete', 11);
    add_action('woocommerce_order_status_completed', 'azl_payment_complete', 11);
    add_action('woocommerce_order_status_processing', 'azl_payment_complete', 11);

    function azl_payment_complete($order_id) {
        $subscriptions = get_post_meta($order_id, 'subscriptions', true);
        if (!empty($subscriptions)) {
            foreach ($subscriptions as $subscription_id) {
                $user_id = get_post_meta($subscription_id, '_user_id', true);
                if (!empty($user_id)) {
                    azl_refresh_listed_posts($user_id);
                }
            }
        }
    }

    function azl_refresh_listed_posts($user_id) {
        $user = get_userdata($user_id);
        if ($user->has_cap('manage_options')) {
            azl_set_listed_posts($user_id, 1);
        } else {
            $subscriptions_products = azl_get_subscriptions_products($user_id);
            $list = 0;
            $limit_max = 0;
            foreach ($subscriptions_products as $product_id) {
                $limit = get_post_meta($product_id, AZL_LISTING_LIMIT, true);
                if (is_numeric($limit)) {
                    if ($limit_max < $limit) {
                        $limit_max = $limit;
                    }
                }
                $list = 1;
            }
            $posts = azl_set_listed_posts($user_id, 0);
            if ($list) {
                $i = 0;
                foreach ($posts as $post) {
                    update_post_meta($post->ID, '_azl_listed', 1);
                    if ($i >= $limit_max) {
                        break;
                    }
                    $i++;
                }
            }
        }
    }

    add_action('wp_insert_post', 'azl_wp_insert_post', 10, 3);

    function azl_wp_insert_post($post_ID, $post, $update) {
        if (function_exists('cmb2_get_option')) {
            $subsciption_post_type = cmb2_get_option('azl_options', 'subsciption_post_type');
            if ($post->post_type == $subsciption_post_type) {
                azl_refresh_listed_posts($post->post_author);
            }
        }
    }

    function azl_current_user_subscriptions($args, $query) {
        global $wpdb;

        $args['where'] = str_replace("('visible','catalog')", "('visible','catalog','hidden')", $args['where']);

        $ids = azl_get_subscriptions_products(get_current_user_id());
        if (empty($ids)) {
            $ids = array(0);
        }
        $args['where'] .= " AND ( $wpdb->posts.ID IN (" . esc_sql(implode(',', $ids)) . ") ) ";

        return $args;
    }

}
