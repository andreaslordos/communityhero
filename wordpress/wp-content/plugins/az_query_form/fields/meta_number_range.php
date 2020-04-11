<?php

add_filter('azqf_number_range_render', 'azqf_number_range_render', 10, 2);

function azqf_number_range_render($output, $field) {
    $slider = isset($field['slider']) && $field['slider'];
    if ($slider) {
        wp_enqueue_script('jquery-ui-slider');
    }
    $output = '<div class="' . esc_attr($field['meta_key']) . '-wrapper number-range ' . ($slider ? 'slider' : '') . '">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    $min = $field['meta_key'] . '_min';
    $default_min = isset($field['default_min']) ? $field['default_min'] : '';
    $max = $field['meta_key'] . '_max';
    $default_max = isset($field['default_max']) ? $field['default_max'] : '';
    if (!isset($field['mode']) || $field['mode'] == 'between') {
        if ($slider) {
            $output .= '<div class="slider" data-step="' . (isset($field['step']) ? esc_attr($field['step']) : '1') . '" data-min="' . (isset($field['slider_min']) ? $field['slider_min'] : '0') . '" data-max="' . (isset($field['slider_max']) ? $field['slider_max'] : '10') . '"></div>';
        }
        $output .= '<div class="numbers">';
        $output .= '<div class="min"><input class="min" type="number" name="' . esc_attr($min) . '" value="' . (isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : $default_min) . '" placeholder="' . (isset($field['placeholder_min']) ? esc_attr($field['placeholder_min']) : '') . '" min="' . (isset($field['slider_min']) ? esc_attr($field['slider_min']) : '1') . '" max="' . (isset($field['slider_max']) ? esc_attr($field['slider_max']) : '10') . '" step="' . (isset($field['step']) ? esc_attr($field['step']) : '1') . '" /></div>';
        $output .= '<div class="max"><input class="max" type="number" name="' . esc_attr($max) . '" value="' . (isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : $default_max) . '" placeholder="' . (isset($field['placeholder_max']) ? esc_attr($field['placeholder_max']) : '') . '" min="' . (isset($field['slider_min']) ? esc_attr($field['slider_min']) : '1') . '" max="' . (isset($field['slider_max']) ? esc_attr($field['slider_max']) : '10') . '" step="' . (isset($field['step']) ? esc_attr($field['step']) : '1') . '" /></div>';
        $output .= '</div>';
    }
    if (isset($field['mode']) && $field['mode'] == '>') {
        $output .= '<div class="min"><input class="min" type="number" name="' . esc_attr($min) . '" value="' . (isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : $default_min) . '" placeholder="' . (isset($field['placeholder_min']) ? esc_attr($field['placeholder_min']) : '') . '" min="' . (isset($field['slider_min']) ? esc_attr($field['slider_min']) : '1') . '" max="' . (isset($field['slider_max']) ? esc_attr($field['slider_max']) : '10') . '" step="' . (isset($field['step']) ? esc_attr($field['step']) : '1') . '" /></div>';
    }
    if (isset($field['mode']) && $field['mode'] == '<') {
        $output .= '<div class="max"><input class="max" type="number" name="' . esc_attr($max) . '" value="' . (isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : $default_max) . '" placeholder="' . (isset($field['placeholder_max']) ? esc_attr($field['placeholder_max']) : '') . '" min="' . (isset($field['slider_min']) ? esc_attr($field['slider_min']) : '1') . '" max="' . (isset($field['slider_max']) ? esc_attr($field['slider_max']) : '10') . '" step="' . (isset($field['step']) ? esc_attr($field['step']) : '1') . '" /></div>';
    }

    $output .= '</div>';
    return $output;
}

add_action('azqf_number_range_process', 'azqf_number_range_process', 10, 2);

function azqf_number_range_process($query, $field) {
    $min = $field['meta_key'] . '_min';
    $min_v = isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : '';
    $max = $field['meta_key'] . '_max';
    $max_v = isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : '';
    if (is_numeric($min_v) && is_numeric($max_v)) {
        $meta_query = $query->get('meta_query');
        $meta_query[] = array(
            'key' => $field['meta_key'],
            'value' => array($min_v, $max_v),
            'type' => 'numeric',
            'compare' => 'BETWEEN',
        );
        $query->set('meta_query', $meta_query);
    } else {
        if (is_numeric($min_v)) {
            $meta_query = $query->get('meta_query');
            $meta_query[] = array(
                'key' => $field['meta_key'],
                'value' => $min_v,
                'type' => 'numeric',
                'compare' => '>',
            );
            $query->set('meta_query', $meta_query);
        } else if (is_numeric($max_v)) {
            $meta_query = $query->get('meta_query');
            $meta_query[] = array(
                'key' => $field['meta_key'],
                'value' => $max_v,
                'type' => 'numeric',
                'compare' => '<',
            );
            $query->set('meta_query', $meta_query);
        }
    }
}

add_action('azqf_number_range_short', 'azqf_number_range_short', 10, 3);

function azqf_number_range_short($output, $field, $params) {
    $name = esc_html__('Number range', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    } else {
        if (isset($field['placeholder_min']) || $field['placeholder_max']) {
            $name = (isset($field['placeholder_min']) ? esc_html($field['placeholder_min']) : '') . ' - ' . (isset($field['placeholder_max']) ? esc_html($field['placeholder_max']) : '');
        }
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
