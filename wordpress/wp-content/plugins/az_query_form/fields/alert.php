<?php

add_filter('azqf_alert_render', 'azqf_alert_render', 10, 2);

function azqf_alert_render($output, $field) {
    $output = '<div class="alert">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    if (!is_user_logged_in()) {
        $output .= '<input type="email" class="email" value="" placeholder="' . (isset($field['placeholder']) ? esc_attr($field['placeholder']) : '') . '" />';
    }
    $output .= '<a href="#">' . esc_html__('Save email alert', 'azqf') . '</a>';
    $output .= '</div>';
    return $output;
}

function azqf_alert($user_id, $name, $q) {
    delete_user_meta($user_id, 'azqf-alert', array('name' => $name, 'query' => $q));
    add_user_meta($user_id, 'azqf-alert', array('name' => $name, 'query' => $q));

    if (class_exists('BNFW')) {
        parse_str($q, $params);
        if (isset($params['post_type'])) {
            $bnfw = BNFW::factory();
            $notifications = $bnfw->notifier->get_notifications('new-' . $params['post_type']);
            foreach ($notifications as $notification) {
                $users = get_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', true);
                $users[] = $user_id;
                $users = array_unique($users);
                delete_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users');
                add_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', $users);
            }
        }
    }
}

function azqf_alert_remove($user_id, $name, $q) {
    delete_user_meta($user_id, 'azqf-alert', array('name' => $name, 'query' => $q));

    if (class_exists('BNFW')) {
        parse_str($q, $params);
        if (isset($params['post_type'])) {
            $bnfw = BNFW::factory();
            $notifications = $bnfw->notifier->get_notifications('new-' . $params['post_type']);
            foreach ($notifications as $notification) {
                $users = get_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', true);
                $users = array_diff($users, array($user_id));
                delete_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users');
                add_post_meta($notification->ID, BNFW_Notification::META_KEY_PREFIX . 'users', $users);
            }
        }
    }
}

add_action('wp_ajax_azqf_alert', 'azqf_alert_callback');
add_action('wp_ajax_nopriv_azqf_alert', 'azqf_alert_callback');

function azqf_alert_callback() {

    if (isset($_POST['name']) && isset($_POST['query'])) {
        if (is_user_logged_in()) {
            azqf_alert(get_current_user_id(), sanitize_text_field($_POST['name']), sanitize_text_field($_POST['query']));
            print esc_html__('Saved', 'azqf');
        } else {
            if (isset($_POST['email'])) {
                $email = sanitize_email($_POST['email']);
                $user = get_user_by('email', $email);
                if ($user && is_object($user)) {
                    $user_id = $user->ID;
                } else {
                    $user_login = sanitize_user(sanitize_email($email), true);
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
                    azqf_alert($user_id, sanitize_text_field($_POST['name']), sanitize_text_field($_POST['query']));
                    print esc_html__('Saved', 'azqf');
                }
            }
        }
    }

    wp_die();
}

add_action('wp_ajax_azqf_alert_remove', 'azqf_alert_remove_callback');
add_action('wp_ajax_nopriv_azqf_alert_remove', 'azqf_alert_remove_callback');

function azqf_alert_remove_callback() {

    if (isset($_POST['name']) && isset($_POST['query'])) {
        if (is_user_logged_in()) {
            azqf_alert_remove(get_current_user_id(), sanitize_text_field($_POST['name']), sanitize_text_field($_POST['query']));
        }
    }

    wp_die();
}

add_filter('bnfw_to_emails', 'azqf_bnfw_to_emails', 10, 3);

function azqf_bnfw_to_emails($to_emails, $setting, $id) {
    if (!defined('AZEXO_APPROVE_USER')) {
        define('AZEXO_APPROVE_USER', 'user-approved');
    }
    if (strpos($setting['notification'], 'new-') !== false) {
        foreach ($to_emails as $email) {
            $user = get_user_by('email', $email);
            $status = get_user_meta($user->ID, AZEXO_APPROVE_USER, true);
            if ($status && $status == 1) {
                $alert = get_user_meta($user->ID, 'azqf-alert');
                if ($alert && is_array($alert)) {
                    foreach ($alert as $data) {
                        parse_str($data['query'], $params);
                        if (isset($params['post_type']) && $setting['notification'] == ('new-' . $params['post_type'])) {
                            if (function_exists('cmb2_get_option')) {
                                $forms = cmb2_get_option('azqf_options', 'forms');
                                if (is_array($forms)) {
                                    foreach ($forms as $form) {
                                        if ($form['name'] == $data['name']) {
                                            $fields = json_decode($form['form'], true);
                                            $query = new WP_Query();
                                            $query->init();
                                            $query->query = $query->query_vars = wp_parse_args($data['query']);
                                            $query->parse_query();
                                            azqf_form_process($fields, $query);
                                            $post_ids = $query->get('post__in');
                                            if (empty($post_ids) || array_search($id, $post_ids)) {
                                                $query->set('post__in', array($id));
                                                $posts = $query->get_posts();
                                                if (!$query->have_posts()) {
                                                    $to_emails = array_diff($to_emails, array($email));
                                                }
                                            } else {
                                                $to_emails = array_diff($to_emails, array($email));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $to_emails;
}

add_shortcode('azqf-saved-searches', 'azqf_saved_searches');

function azqf_saved_searches($atts = array()) {
    wp_enqueue_script('azqf');
    $output = '';
    $alert = get_user_meta(get_current_user_id(), 'azqf-alert');
    if ($alert && is_array($alert)) {
        $output .= '<div class="azqf-saved-searches">';
        foreach ($alert as $data) {
            parse_str($data['query'], $params);
            if (function_exists('cmb2_get_option')) {
                $forms = cmb2_get_option('azqf_options', 'forms');
                if (is_array($forms)) {
                    foreach ($forms as $form) {
                        if ($form['name'] == $data['name']) {
                            $fields = json_decode($form['form'], true);
                            $short = azqf_form_short($fields, $params);
                            if (empty($short)) {
                                $short = '<div class="all">' . esc_html__('All', 'azqf') . '</div>';
                            }
                            $output .= '<div class="form" data-name="' . esc_attr($data['name']) . '" data-query="' . esc_attr($data['query']) . '">' . $short . '<a href="#" class="remove">' . esc_html__('Remove', 'azqf') . '</a></div>';
                        }
                    }
                }
            }
        }
        $output .= '</div>';
    }
    return $output;
}
