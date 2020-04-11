<?php

add_filter('azqf_open_render', 'azqf_open_render', 10, 2);

function azqf_open_render($output, $field) {

    $output = '<div class="open">';

    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }

    $id = 'on-' . rand(0, 99999999);
    $output .= '<input id="' . $id . '" type="checkbox" name="open" ' . ((isset($field['default']) && $field['default']) ? 'checked="checked' : '') . '">';
    $output .= '<label for="' . $id . '">';
    $output .= esc_attr__('Open now', 'azqf');
    $output .= '</label>';


    $output .= '</div>';
    return $output;
}

add_action('azqf_open_process', 'azqf_open_process', 10, 2);

function azqf_open_process($query, $field) {
    $open = isset($_GET['open']) && 'on' == $_GET['open'];
    if ($open) {
        $meta_query = $query->get('meta_query');
        $meta_query[] = array(
            'key' => $field['meta_key'] . '-' . date('N', time() + get_option('gmt_offset') * HOUR_IN_SECONDS) . '-hours',
            'value' => date('G', time() + get_option('gmt_offset') * HOUR_IN_SECONDS)
        );
        $query->set('meta_query', $meta_query);
    }
}

add_action('azqf_open_short', 'azqf_open_short', 10, 3);

function azqf_open_short($output, $field, $params) {
    $name = esc_html__('Open', 'azqf');
    if (isset($field['label'])) {
        $name = $field['label'];
    }
    if (isset($params['open'])) {
        $value = $params['open'];
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    }

    return $output;
}
