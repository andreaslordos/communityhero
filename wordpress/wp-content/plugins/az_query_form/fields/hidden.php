<?php

add_filter('azqf_hidden_render', 'azqf_hidden_render', 10, 2);

function azqf_hidden_render($output, $field) {
    if (isset($field['value'])) {
        $output = '<input type="hidden" name="' . $field['name'] . '" value="' . $field['value'] . '" />';
    } else {
        $output = '<input type="hidden" name="' . $field['name'] . '" value="' . (isset($_GET[$field['name']]) ? sanitize_text_field($_GET[$field['name']]) : '') . '" />';
    }
    return $output;
}
