<?php

class Walker_CategoryDropdown_QF extends Walker_CategoryDropdown {

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $pad = str_repeat('-', $depth);

        /** This filter is documented in wp-includes/category-template.php */
        $cat_name = apply_filters('list_cats', $category->name, $category);

        if (isset($args['value_field']) && isset($category->{$args['value_field']})) {
            $value_field = $args['value_field'];
        } else {
            $value_field = 'term_id';
        }

        $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr($category->{$value_field}) . "\"";

        // Type-juggling causes false matches, so we force everything to a string.
        if ((string) $category->{$value_field} === (string) $args['selected']) {
            $output .= ' selected="selected"';
        }            
        $output .= '>';
        $output .= $pad . $cat_name;
        if ($args['show_count']) {
            $output .= '&nbsp;&nbsp;(' . number_format_i18n($category->count) . ')';
        }
        $output .= "</option>\n";
    }

}

add_filter('azqf_dropdown_render', 'azqf_dropdown_render', 10, 2);

function azqf_dropdown_render($output, $field) {
    if (isset($field['class'])) {
        if (strpos($field['class'], 'select2') !== FALSE) {
            wp_enqueue_script('select2');
        }
    }

    $output = '<div class="taxonomy-dropdown ' . esc_attr($field['taxonomy']) . '-wrapper" data-taxonomy="' . $field['taxonomy'] . '">';
    if (isset($field['label'])) {
        $output .= '<label>' . esc_html($field['label']) . '</label>';
    }
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
            $field['selected'] = $term->slug;
        }
    }
    $field['name'] = $field['taxonomy'];
    $field['echo'] = false;
    if (isset($field['hierarchical']) && $field['hierarchical']) {
        $field['class'] = 'hierarchical';
    }
    $field['value_field'] = 'slug';
    $field['walker'] = new Walker_CategoryDropdown_QF();
    $output .= wp_dropdown_categories($field);
    $output .= '</div>';
    return $output;
}

add_action('azqf_dropdown_short', 'azqf_dropdown_short', 10, 3);

function azqf_dropdown_short($output, $field, $params) {
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
