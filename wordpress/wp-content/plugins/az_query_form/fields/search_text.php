<?php

add_filter('azqf_search_text_render', 'azqf_search_text_render', 10, 2);

function azqf_search_text_render($output, $field) {
    $output = '<div class="s-wrapper">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }    
    $output .= '<input type="text" name="s" value="' . (isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '') . '" placeholder="' . (isset($field['placeholder']) ? esc_attr($field['placeholder']) : '') . '" />';
    $output .= '</div>';
    return $output;
}

add_action('azqf_search_text_short', 'azqf_search_text_short', 10, 3);

function azqf_search_text_short($output, $field, $params) {
    $name = esc_html__('Search text', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    } else {
        if (isset($field['placeholder'])) {
            $name = $field['placeholder'];
        }
    }
    if (isset($params['s'])) {
        $value = $params['s'];
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    }

    return $output;
}
