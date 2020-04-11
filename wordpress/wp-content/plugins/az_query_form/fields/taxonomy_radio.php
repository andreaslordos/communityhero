<?php

add_filter('azqf_radio_render', 'azqf_radio_render', 10, 2);

function azqf_radio_render($output, $field) {

    $output = '<div class="taxonomy-radio ' . esc_attr($field['taxonomy']) . '-wrapper" data-taxonomy="' . $field['taxonomy'] . '">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
    $selected = false;
    global $wp_query;
    $slug = '';
    if(isset($_GET[$field['taxonomy']])) {
        $slug = sanitize_text_field($_GET[$field['taxonomy']]);
    } else {
        if(isset($wp_query->query[$field['taxonomy']])) {
            $slug = sanitize_text_field($wp_query->query[$field['taxonomy']]);
        }        
    }    
    if (!empty($slug)) {
        if (is_numeric($slug)) {
            $term = get_term_by('id', $slug, $field['taxonomy']);
        } else {
            $term = get_term_by('slug', $slug, $field['taxonomy']);
        }
        if (is_object($term)) {
            $selected = $term->slug;
        }
    }
    $terms = get_terms($field['taxonomy'], array('hide_empty' => $field['hide_empty']));
    $output .= '<ul>';
    if (isset($field['show_option_none']) && isset($field['option_none_value']) && $field['show_option_none'] != '') {
        $output .= '<li><input type="radio" id="in-' . $field['taxonomy'] . '-none" name="' . $field['taxonomy'] . '" value="' . $field['option_none_value'] . '" ' . ($field['option_none_value'] == $slug ? 'checked' : '') . '><label for="in-' . $field['taxonomy'] . '-none">' . $field['show_option_none'] . '</label></li>';
    }    
    foreach ($terms as $term) {
        $output .= '<li class="' . $term->slug . '"><input type="radio" id="in-' . $field['taxonomy'] . '-' . $term->term_id . '" name="' . $field['taxonomy'] . '" value="' . $term->slug . '" ' . ($term->slug == $selected ? 'checked' : '') . '><label for="in-' . $field['taxonomy'] . '-' . $term->term_id . '">' . $term->name . '</label></li>';
    }
    $output .= '</ul></div>';
    return $output;
}

add_action('azqf_radio_short', 'azqf_radio_short', 10, 3);

function azqf_radio_short($output, $field, $params) {
    $labels = get_taxonomy_labels($field['taxonomy']);
    $name = $labels['name'];
    if (isset($field['label'])) {
        $name = $field['label'];
    }
    if (isset($params[$field['taxonomy']]) && is_string($params[$field['taxonomy']])) {
        $slug = $params[$field['taxonomy']];
        if (is_numeric($slug)) {
            $term = get_term_by('id', $slug, $field['taxonomy']);
        } else {
            $term = get_term_by('slug', $slug, $field['taxonomy']);
        }
        if (is_object($term)) {
            $value = $term->name;
            $output .= '<dt>' . $name . '</dt>';
            $output .= '<dd>' . $value . '</dd>';
        }
    }

    return $output;
}
