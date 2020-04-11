<?php

add_action('init', 'azlp_init');

function azlp_init() {
    register_post_type('azl_profile', apply_filters('azl_register_post_type_profile', array(
        'labels' => array(
            'name' => __('Profiles', 'azl'),
            'singular_name' => __('Profile', 'azl'),
            'add_new' => __('Add Profile', 'azl'),
            'add_new_item' => __('Add New Profile', 'azl'),
            'edit_item' => __('Edit Profile', 'azl'),
            'new_item' => __('New Profile', 'azl'),
            'view_item' => __('View Profile', 'azl'),
            'search_items' => __('Search Profiles', 'azl'),
            'not_found' => __('No Profile found', 'azl'),
            'not_found_in_trash' => __('No Profile found in Trash', 'azl'),
            'parent_item_colon' => __('Parent Profile:', 'azl'),
            'menu_name' => __('Profiles', 'azl'),
        ),
        'supports' => array('title', 'editor', 'excerpt', 'author', 'custom-fields', 'revisions', 'thumbnail', 'comments'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-admin-users',
        'rewrite' => array('slug' => 'profile'),
        'query_var' => true,
        'hierarchical' => true,
            ))
    );
}

add_filter('manage_azl_profile_posts_columns', 'azlp_manage_azl_profile_posts_columns');

function azlp_manage_azl_profile_posts_columns($columns) {
    $columns['author'] = __('Owner', 'azl');
    return $columns;
}

add_filter('wp_insert_post_data', 'azlp_wp_insert_post_data');

function azlp_wp_insert_post_data($data) {
    if ($data['post_type'] == 'azl_profile') {
        $data['comment_status'] = 'open';
    }
    return $data;
}

add_action('user_register', 'azlp_user_register', 10, 1);


function azlp_user_register($user_id) {    
    $posts = get_posts(array(
        'post_type' => 'azl_profile',
        'author' => $user_id,
    ));
    if (empty($posts)) {
        $user = get_userdata($user_id);
        wp_insert_post(array(
            'post_title' => $user->user_login,
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'post_author' => $user_id,
                ), true);
    }    
}

add_filter('get_avatar_url', 'azlp_get_avatar_url', 10, 5);

function azlp_get_avatar_url($url, $id_or_email, $args) {
    $user_id = false;
    if (is_numeric($id_or_email)) {
        $user_id = $id_or_email;
    } elseif (is_string($id_or_email)) {
        // md5 hash
        // email address
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Post) {
        $user_id = $id_or_email->post_author;
    } elseif ($id_or_email instanceof WP_Comment) {
        if (!empty($id_or_email->user_id)) {
            $user_id = $id_or_email->user_id;
        }
    }
    if ($user_id) {
        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'author' => $user_id,
        ));
        if (!empty($posts)) {
            $profile = reset($posts);
            $thumbnail_url = get_the_post_thumbnail_url($profile, 'thumbnail');
            if ($thumbnail_url) {
                return $thumbnail_url;
            }
        }
    }
    return $url;
}

add_filter('author_link', 'azlp_author_link', 10, 3);

function azlp_author_link($link, $author_id, $author_nicename) {
    $posts = get_posts(array(
        'post_type' => 'azl_profile',
        'post_status' => 'publish',
        'author' => $author_id,
    ));
    if (!empty($posts)) {
        $profile = reset($posts);
        return get_permalink($profile);
    }
    return $link;
}

add_filter('the_author', 'azlp_the_author');

function azlp_the_author($display_name) {
    global $authordata;    
    $posts = get_posts(array(
        'post_type' => 'azl_profile',
        'post_status' => 'publish',
        'author' => $authordata->ID,
    ));
    if (!empty($posts)) {
        $profile = reset($posts);
        return $profile->post_title;
    }
    return $display_name;
}