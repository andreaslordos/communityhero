<?php

//add_filter('et_builder_post_types', 'azh_divi_post_types');
//add_filter('et_fb_post_types', 'azh_divi_post_types');

function azh_divi_post_types($post_types) {
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && is_array($settings['post-types'])) {
        $pts = array_keys($settings['post-types']);
        $post_types = array_diff($post_types, $pts);
    }
    return $post_types;
}

add_action('save_post', 'azh_divi_save_post', 11, 3);

function azh_divi_save_post($post_id, $post, $update) {
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && is_array($settings['post-types'])) {
        if (in_array($post->post_type, array_keys($settings['post-types']))) {
            if(get_post_meta($post_id, 'azh', true) === 'azh') {
                delete_post_meta($post_id, '_et_pb_use_builder');
            }            
        }
    }
}
