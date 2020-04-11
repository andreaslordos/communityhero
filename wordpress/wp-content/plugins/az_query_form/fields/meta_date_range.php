<?php

add_filter('azqf_date_range_render', 'azqf_date_range_render', 10, 2);

function azqf_date_range_render($output, $field) {
    $datepicker = isset($field['datepicker']) && $field['datepicker'];
    if ($datepicker) {
        wp_enqueue_script('jquery-ui-datepicker');
    }
    $output = '<div class="' . esc_attr($field['meta_key']) . '-wrapper date-range ' . ($datepicker ? 'datepicker' : '') . '">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    $min = $field['meta_key'] . '_min';
    $output .= '<div class="dates">';
    $output .= '<div class="min"><input class="min" ' . ($datepicker ? 'type="text"' : 'type="date"') . ' name="' . esc_attr($min) . '" value="' . (isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : '') . '" placeholder="' . (isset($field['placeholder_min']) ? $field['placeholder_min'] : '') . '" /></div>';
    $max = $field['meta_key'] . '_max';
    $output .= '<div class="max"><input class="max" ' . ($datepicker ? 'type="text"' : 'type="date"') . ' name="' . esc_attr($max) . '" value="' . (isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : '') . '" placeholder="' . (isset($field['placeholder_max']) ? $field['placeholder_max'] : '') . '" /></div>';
    $output .= '</div>';

    $output .= '</div>';
    return $output;
}

add_action('azqf_date_range_process', 'azqf_date_range_process', 10, 2);

function azqf_date_range_process($query, $field) {
    $min = $field['meta_key'] . '_min';
    $min_v = strtotime(isset($_GET[$min]) ? sanitize_text_field($_GET[$min]) : '');
    $max = $field['meta_key'] . '_max';
    $max_v = strtotime(isset($_GET[$max]) ? sanitize_text_field($_GET[$max]) : '');
    if (is_numeric($min_v) && is_numeric($max_v)) {
        if (isset($field['exclude']) && $field['exclude']) {
            global $wpdb;
            if (isset($field['numeric_meta']) && $field['numeric_meta']) {
                $sql = $wpdb->prepare("
                SELECT DISTINCT pm.post_id
                FROM $wpdb->postmeta AS pm
                WHERE pm.meta_key=%s
                    AND CAST(pm.meta_value AS UNSIGNED) > %d
                    AND CAST(pm.meta_value AS UNSIGNED) < %d
                ", $field['meta_key'], $min_v, $max_v
                );
            } else {
                $sql = $wpdb->prepare("
                SELECT DISTINCT pm.post_id
                FROM $wpdb->postmeta AS pm
                WHERE pm.meta_key=%s
                    AND pm.meta_value > %s
                    AND pm.meta_value < %s
                ", $field['meta_key'], date('Y-m-d', $min_v), date('Y-m-d', $max_v)
                );
            }
            $post_ids = $wpdb->get_results($sql, OBJECT_K);
            if (is_array($post_ids) && !empty($post_ids)) {
                $query->set('post__not_in', array_keys($post_ids));
            }
        } else {
            $meta_query = $query->get('meta_query');
            if (isset($field['numeric_meta']) && $field['numeric_meta']) {
                $meta_query[] = array(
                    'key' => $field['meta_key'],
                    'value' => array($min_v, $max_v),
                    'type' => 'numeric',
                    'compare' => 'BETWEEN',
                );
            } else {
                $meta_query[] = array(
                    'key' => $field['meta_key'],
                    'value' => array(date('Y-m-d', $min_v), date('Y-m-d', $max_v)),
                    'compare' => 'BETWEEN',
                );
            }
            $query->set('meta_query', $meta_query);
        }
    }
}

add_action('azqf_date_range_short', 'azqf_date_range_short', 10, 3);

function azqf_date_range_short($output, $field, $params) {
    $name = esc_html__('Date range', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    } else {
        if (isset($field['placeholder_min']) || $field['placeholder_max']) {
            $name = (isset($field['placeholder_min']) ? esc_html($field['placeholder_min']) : '') . ' - ' . (isset($field['placeholder_max']) ? esc_html($field['placeholder_max']) : '');
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
