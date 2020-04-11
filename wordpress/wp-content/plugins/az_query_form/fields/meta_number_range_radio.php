<?php

add_filter('azqf_number_range_radio_render', 'azqf_number_range_radio_render', 10, 2);

function azqf_number_range_radio_render($output, $field) {
    $output = '<div class="' . esc_attr($field['meta_key']) . '-wrapper number-range-radio">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    $selected = isset($_GET[$field['meta_key']]) ? sanitize_text_field($_GET[$field['meta_key']]) : (isset($field['default']) ? $field['default'] : '');

    
    $output .= '<ul class="radio">';
    foreach ($field['rules'] as $rule => $label) {
        $id = $field['meta_key'];
        $id = ltrim($id, '_');
        $id = esc_attr($id) . '-' . sanitize_title($rule);
        $output .= '<li><input id="' . $id . '" type="radio" data-rule="' . $rule . '" name="' . esc_attr($field['meta_key']) . '" value="' . sanitize_title($rule) . '" ' . (($selected == sanitize_title($rule)) ? 'checked="checked"' : '') . '><label for="' . $id . '">' . esc_html($label) . '</label></li>';
    }
    $output .= '</ul>';


    $min = $field['meta_key'] . '_min';
    $output .= '<div class="min"><input class="min" type="hidden" name="' . esc_attr($min) . '" value="' . (isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : '') . '" /></div>';
    $max = $field['meta_key'] . '_max';
    $output .= '<div class="max"><input class="max" type="hidden" name="' . esc_attr($max) . '" value="' . (isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : '') . '" /></div>';

    $output .= '</div>';
    return $output;
}

add_action('azqf_number_range_radio_process', 'azqf_number_range_process', 10, 2);

add_action('azqf_number_range_radio_short', 'azqf_number_range_radio_short', 10, 3);

function azqf_number_range_radio_short($output, $field, $params) {
    $name = esc_html__('Rating', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    }
    $min = $field['meta_key'] . '_min';
    $max = $field['meta_key'] . '_max';
    if (isset($params[$min]) && is_numeric($params[$min]) || isset($params[$max]) && is_numeric($params[$max])) {
        $value = (isset($params[$min]) ? $params[$min] : '') . ' - ' . (isset($params[$max]) ? $params[$max] : '');
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    }

    return $output;
}
