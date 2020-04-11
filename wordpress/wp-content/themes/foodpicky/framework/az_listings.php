<?php

global $azexo_azl_templates;
$azexo_azl_templates = array(
    'google_map_post' => esc_html__('Google Map post', 'foodpicky'),
);
add_filter('azexo_templates', 'azexo_azl_templates');

function azexo_azl_templates($azexo_templates) {
    global $azexo_azl_templates;
    return array_merge($azexo_templates, $azexo_azl_templates);
}

add_filter('azexo_post_template_path', 'azexo_azl_post_template_path', 10, 2);

function azexo_azl_post_template_path($template, $template_name) {
    global $azexo_azl_templates;
    if (in_array($template_name, array_keys($azexo_azl_templates))) {
        return WC()->template_path() . "content-product.php";
    } else {
        return $template;
    }
}

global $azexo_azl_fields;

$azexo_azl_fields = array(
    'azl_actions' => esc_html__('Listing: Actions', 'foodpicky'),
    'azl_edit_link' => esc_html__('Listing: Edit link', 'foodpicky'),
    'azl_delete_link' => esc_html__('Listing: Delete link', 'foodpicky'),
    'azl_claim_link' => esc_html__('Listing: Claim link', 'foodpicky'),
    'azl_abuse_link' => esc_html__('Listing: Abuse link', 'foodpicky'),
    'azl_favorite_link' => esc_html__('Listing: Favorite link', 'foodpicky'),
);

add_filter('azexo_fields', 'azexo_azl_fields');

function azexo_azl_fields($azexo_fields) {
    global $azexo_azl_fields;
    return array_merge($azexo_fields, $azexo_azl_fields);
}

add_filter('azexo_fields_post_types', 'azexo_azl_fields_post_types');

function azexo_azl_fields_post_types($azexo_fields_post_types) {
    global $azexo_azl_fields;
    $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_azl_fields), array_fill(0, count(array_keys($azexo_azl_fields)), '')));
    return $azexo_fields_post_types;
}

add_filter('azexo_entry_field', 'azexo_azl_entry_field', 10, 2);

function azexo_azl_entry_field($output, $name) {
    global $post;
    wp_enqueue_script('azl');
    switch ($name) {
        case 'azl_actions':
            return '<div class="azl-actions"><input id="azl-actions-' . $post->ID . '" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);"><div></div><label for="azl-actions-' . $post->ID . '">' . esc_html('Actions', 'foodpicky') . '</label><div class=dropdown>'
                    . azexo_azl_entry_field('', 'azl_edit_link')
                    . azexo_azl_entry_field('', 'azl_delete_link')
                    . azexo_azl_entry_field('', 'azl_favorite_link')
                    . azexo_azl_entry_field('', 'azl_claim_link')
                    . azexo_azl_entry_field('', 'azl_abuse_link')
                    . '</div></div>';
            break;
        case 'azl_edit_link':
            if ($post->post_author == get_current_user_id() || current_user_can('manage_options')) {
                return '<div class="azl-edit"><a href="' . esc_url(add_query_arg(array('azl' => 'edit', 'id' => $post->ID))) . '">' . esc_html__('Edit', 'foodpicky') . '</a></div>';
            }
            break;
        case 'azl_delete_link':
            if ($post->post_author == get_current_user_id() || current_user_can('manage_options')) {
                return '<div class="azl-delete"><a href="' . esc_url(add_query_arg(array('azl' => 'delete', 'id' => $post->ID))) . '">' . esc_html__('Delete', 'foodpicky') . '</a></div>';
            }
            break;
        case 'azl_claim_link':
            if ($post->post_author == 1) {
                $users = get_post_meta($post->ID, 'claim');
                if (!in_array(get_current_user_id(), $users)) {
                    return '<div class="azl-claim"><a href="' . esc_url(add_query_arg(array('azl' => 'claim', 'id' => $post->ID))) . '">' . esc_html__('Claim', 'foodpicky') . '</a></div>';
                } else {
                    return '<div class="azl-claim claimed"></div>';
                }
            }
            break;
        case 'azl_abuse_link':
            return '<div class="azl-abuse"><a href="' . esc_url(add_query_arg(array('azl' => 'abuse', 'id' => $post->ID))) . '">' . esc_html__('Report spam, abuse, or inappropriate content', 'foodpicky') . '</a></div>';
            break;
        case 'azl_favorite_link':
            $users = get_post_meta($post->ID, 'favorite');
            if (in_array(get_current_user_id(), $users)) {
                return '<div class="azl-favorite remove"><a href="' . esc_url(add_query_arg(array('azl' => 'favorite', 'id' => $post->ID))) . '">' . esc_html__('Remove from favorites', 'foodpicky') . '</a></div>';
            } else {
                return '<div class="azl-favorite add"><a href="' . esc_url(add_query_arg(array('azl' => 'favorite', 'id' => $post->ID))) . '">' . esc_html__('Add to favorites', 'foodpicky') . '</a></div>';
            }
            break;
    }
    return $output;
}

add_filter('azl_google_map_location', 'azexo_azl_google_map_location', 10, 2);

function azexo_azl_google_map_location($location, $location_post) {

    global $post, $wp_query;
    $original = $post;
    $post = $location_post;
    setup_postdata($location_post);

    $location = array_merge(array(
        'thumbnail' => azexo_entry_meta('google_map_post', 'thumbnail'),
        'extra' => azexo_entry_meta('google_map_post', 'extra'),
        'meta' => azexo_entry_meta('google_map_post', 'meta'),
        'header' => azexo_entry_meta('google_map_post', 'header'),
        'footer' => azexo_entry_meta('google_map_post', 'footer'),
        'data' => azexo_entry_meta('google_map_post', 'data')
            ), $location);

    ob_start();
    azexo_post_thumbnail_field('google_map_post');
    $location['image'] = ob_get_clean();

    $wp_query->post = $original;
    wp_reset_postdata();

    return $location;
}

add_filter('azexo_dashboard_links', 'azexo_azl_dashboard_links');

function azexo_azl_dashboard_links($links) {

    $submit_page = false;
    if (function_exists('cmb2_get_option')) {
        $forms = cmb2_get_option('azl_options', 'forms');
        if (is_array($forms)) {
            foreach ($forms as $form) {
                if (isset($form['form'])) {
                    if (isset($form['post_type']) && $form['post_type'] == 'product') {
                        $submit_page = $form['page'];
                        break;
                    }
                }
            }
        }
    }

    if (class_exists('WC_Vendors')) {
        if (WCV_Vendors::is_vendor(get_current_user_id())) {
            $shop_page = WCV_Vendors::get_vendor_shop_page(wp_get_current_user()->user_login);
            $links = array_merge(array(
                array(
                    'url' => $shop_page,
                    'title' => esc_html__('My items', 'foodpicky'),
                ),), $links);
        }
    }

    $can_submit = 'yes';
    if (class_exists('WC_Vendors')) {
        $can_submit = get_option('wcvendors_capability_products_enabled');        
    }
    if (($can_submit == 'yes') && $submit_page) {
        $links = array_merge(array(
            array(
                'id' => $submit_page,
                'url' => get_permalink($submit_page),
                'title' => esc_html__('Submit new item', 'foodpicky'),
            ),), $links);
    }

    if (class_exists('WC_Vendors')) {
        $vendor_dashboard_page = get_option('wcvendors_vendor_dashboard_page_id');
        if ($vendor_dashboard_page) {
            $links = array_merge(array(
                array(
                    'id' => $vendor_dashboard_page,
                    'url' => get_permalink($vendor_dashboard_page),
                    'title' => esc_html__('Vendor dashboard', 'wc-vendors'),
                ),), $links);
        }
        if (WCV_Vendors::is_vendor(get_current_user_id())) {
            $settings_page = get_option('wcvendors_shop_settings_page_id');
            if ($settings_page) {
                $links = array_merge(array(
                    array(
                        'id' => $settings_page,
                        'url' => get_permalink($settings_page),
                        'title' => esc_html__('Store Settings', 'wc-vendors'),
                    ),), $links);
            }
        }
    }

    return $links;
}

add_filter('wpcf7_form_hidden_fields', 'azexo_azl_form_hidden_fields');

function azexo_azl_form_hidden_fields($hidden_fields) {
    $page = azexo_get_closest_current_post('page');
    if ($page) {
        if (isset($_GET['abuse']) && is_numeric($_GET['abuse'])) {
            $hidden_fields['_abuse'] = $_GET['abuse'];
        }
    }
    return $hidden_fields;
}

add_filter('wpcf7_special_mail_tags', 'azexo_azl_special_mail_tags', 10, 3);

function azexo_azl_special_mail_tags($output, $name, $html) {

    $name = preg_replace('/^wpcf7\./', '_', $name); // for back-compat

    $submission = WPCF7_Submission::get_instance();

    if (!$submission) {
        return $output;
    }

    if ('abuse-url' == $name) {
        if (isset($submission->posted_data['_abuse'])) {
            return get_permalink($submission->posted_data['_abuse']);
        } else {
            return '';
        }
    }

    return $output;
}
