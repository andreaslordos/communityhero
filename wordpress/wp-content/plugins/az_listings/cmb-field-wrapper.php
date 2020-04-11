<?php

add_filter('cmb2_render_wrapper', 'cmb2_render_wrapper_field_callback', 10, 5);

function cmb2_render_wrapper_field_callback($field, $value, $object_id, $object_type, $field_type) {
    print '<div class="wrapper" data-class="' . esc_attr($field->args('class')) . '"></div>';
}


add_filter('cmb2_override_meta_save', 'cmb2_override_meta_save_wrapper_callback', 10, 4);

function cmb2_override_meta_save_wrapper_callback($null, $a, $field_args, $field) {
    if ($field_args['type'] == 'wrapper') {
        return true;
    }
}
