<?php

add_filter('gutenberg_can_edit_post_type', 'azh_gutenberg_can_edit_post_type', 10, 2);
add_filter('use_block_editor_for_post_type', 'azh_gutenberg_can_edit_post_type', 10, 2);

function azh_gutenberg_can_edit_post_type($can_edit, $post_type) {
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && is_array($settings['post-types'])) {
        if (in_array($post_type, array_keys($settings['post-types']))) {
            $can_edit = false;
        }
    }
    return $can_edit;
}
