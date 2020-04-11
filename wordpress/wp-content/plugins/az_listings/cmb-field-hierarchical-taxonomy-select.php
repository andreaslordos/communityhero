<?php

function azl_hts_tree($field_type_object, $term, $saved_term = null, $level = 0) {
    $options = '';
    if (is_object($term)) {

        $options .= $field_type_object->select_option(array(
            'label' => str_repeat('-', $level) . $term->name,
            'value' => $term->slug,
            'checked' => $saved_term == $term->slug,
        ));
        $children = get_term_children($term->term_id, $term->taxonomy);
        if (!empty($children)) {
            $level++;
            foreach ($children as $child_id) {
                $child = get_term($child_id);
                $options .= azl_hts_tree($field_type_object, $child, $saved_term, $level);
            }
        }
    }
    return $options;
}

add_filter('cmb2_render_hierarchical_taxonomy_select', 'azl_hierarchical_taxonomy_select', 10, 5);

function azl_hierarchical_taxonomy_select($field, $value, $object_id, $object_type, $field_type_object) {
    $names = $field_type_object->get_object_terms();
    $saved_term = is_wp_error($names) || empty($names) ? $field_type_object->field->args('default') : $names[key($names)]->slug;
    $terms = get_terms($field_type_object->field->args('taxonomy'), 'hide_empty=0&parent=0');
    $options = '';

    $option_none = $field_type_object->field->args('show_option_none');
    if (!empty($option_none)) {
        $option_none_value = apply_filters('cmb2_taxonomy_select_default_value', '');
        $option_none_value = apply_filters("cmb2_taxonomy_select_{$field_type_object->_id()}_default_value", $option_none_value);

        $options .= $field_type_object->select_option(array(
            'label' => $option_none,
            'value' => $option_none_value,
            'checked' => $saved_term == $option_none_value,
        ));
    }

    foreach ($terms as $term) {
        $options .= azl_hts_tree($field_type_object, $term, $saved_term);
    }

    print $field_type_object->select(array('options' => $options));
}

add_filter('cmb2_sanitize_hierarchical_taxonomy_select', 'azl_hierarchical_taxonomy_select_sanitize', 10, 5);

function azl_hierarchical_taxonomy_select_sanitize($check, $value, $object_id, $field_args, $sanitize_object) {
    if ($object_id == AZL_FRONTEND_OBJECT_ID) {
        return $value;
    }
    if ($sanitize_object->field->args('taxonomy')) {
        wp_set_object_terms($sanitize_object->field->object_id, $sanitize_object->value, $sanitize_object->field->args('taxonomy'));
    }
    return '';
}
