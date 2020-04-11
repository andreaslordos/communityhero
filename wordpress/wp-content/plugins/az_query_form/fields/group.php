<?php

add_filter('azqf_group_render', 'azqf_group_render', 10, 2);

function azqf_group_render($output, $field) {
    return '<div class="group" data-class="' . (isset($field['class']) ? esc_attr($field['class']) : '') . '" data-label="' . (isset($field['label']) ? esc_attr($field['label']) : '') . '"></div>';
}
