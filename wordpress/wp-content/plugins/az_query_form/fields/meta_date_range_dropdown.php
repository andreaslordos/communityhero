<?php

add_filter('azqf_date_range_dropdown_render', 'azqf_date_range_dropdown_render', 10, 2);

function azqf_date_range_dropdown_render($output, $field) {
    $output = '<div class="' . esc_attr($field['meta_key']) . '-wrapper date-range-dropdown">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    $option = esc_attr($field['meta_key']);
    $selected = isset($_GET[$option]) ? sanitize_text_field($_GET[$option]) : (isset($field['default']) ? $field['default'] : '');
    $output .= '<select class="' . (isset($field['class']) ? esc_attr($field['class']) : '') . '" name="' . $option . '">';
    $output .= '<option value="0" ' . ((empty($selected)) ? 'selected="selected"' : '') . '>' . (isset($field['placeholder']) ? esc_html($field['placeholder']) : '') . '</option>';
    $value = 0;
    foreach ($field['rules'] as $rule => $label) {
        $value++;
        $output .= '<option value="' . $value . '" data-rule="' . esc_attr($rule) . '" ' . (($selected == $value) ? 'selected="selected"' : '') . '>' . esc_html($label) . '</option>';
    }
    $output .= '</select>';
    $min = $field['meta_key'] . '_min';
    $output .= '<div class="min"><input class="min" type="hidden" name="' . esc_attr($min) . '" value="' . (isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : '') . '" /></div>';
    $max = $field['meta_key'] . '_max';
    $output .= '<div class="max"><input class="max" type="hidden" name="' . esc_attr($max) . '" value="' . (isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : '') . '" /></div>';

    $output .= '</div>';
    return $output;
}

add_action('azqf_date_range_dropdown_process', 'azqf_date_range_process', 10, 2);

add_action('azqf_date_range_dropdown_short', 'azqf_date_range_dropdown_short', 10, 3);

function azqf_date_range_dropdown_short($output, $field, $params) {
    $name = esc_html__('Date range', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    } else {
        if (isset($field['placeholder'])) {
            $name = $field['placeholder'];
        }
    }
    $min = $field['meta_key'] . '_min';
    $max = $field['meta_key'] . '_max';
    if (isset($params[$min]) && !empty($params[$min]) || isset($params[$max]) && !empty($params[$max])) {
        $value = (isset($params[$min]) ? date_i18n(get_option('date_format'), strtotime($params[$min])) : '') . ' - ' . (isset($params[$max]) ? date_i18n(get_option('date_format'), strtotime($params[$max])) : '');
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    }

    return $output;
}
