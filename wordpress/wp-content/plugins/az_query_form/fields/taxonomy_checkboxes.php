<?php

add_filter('azqf_checkboxes_render', 'azqf_checkboxes_render', 10, 2);

function azqf_checkboxes_render($output, $field) {

    require_once(ABSPATH . 'wp-admin/includes/template.php');

    if (!class_exists('Walker_Category_Checklist_QF')) {

        class Walker_Category_Checklist_QF extends Walker_Category_Checklist {

            public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
                if (empty($args['taxonomy'])) {
                    $taxonomy = 'category';
                } else {
                    $taxonomy = $args['taxonomy'];
                }


                $args['popular_cats'] = empty($args['popular_cats']) ? array() : (array) $args['popular_cats'];
                $class = in_array($category->term_id, $args['popular_cats']) ? ' class="popular-category ' . $category->slug . '"' : ' class="' . $category->slug . '"';

                $args['selected_cats'] = empty($args['selected_cats']) ? array() : (array) $args['selected_cats'];

                /** This filter is documented in wp-includes/category-template.php */
                if (!empty($args['list_only'])) {
                    $aria_cheched = 'false';
                    $inner_class = 'category';

                    if (in_array($category->term_id, $args['selected_cats'])) {
                        $inner_class .= ' selected';
                        $aria_cheched = 'true';
                    }

                    $output .= "\n" . '<li' . $class . '>' .
                            '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                            ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                            esc_html(apply_filters('the_category', $category->name)) . '</div>';
                } else {
                    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>"
                            . '<input value="' . $category->slug . '" type="checkbox" name="' . $taxonomy . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"'
                            . checked(in_array($category->term_id, $args['selected_cats']), true, false) . ' />'
                            . '<label for="in-' . $taxonomy . '-' . $category->term_id . '" class="selectit">'
                            . esc_html(apply_filters('the_category', $category->name)) . '</label>';
                }
            }

        }

    }

    $output = '<div class="taxonomy-checkboxes ' . $field['taxonomy'] . '-wrapper">';
    if (isset($field['label'])) {
        $output .= '<label>' . $field['label'] . '</label>';
    }
    global $wp_query;
    $slug = '';
    if (isset($_GET[$field['taxonomy']])) {
        $slug = sanitize_text_field($_GET[$field['taxonomy']]);
    } else {
        if (isset($wp_query->query[$field['taxonomy']])) {
            $slug = sanitize_text_field($wp_query->query[$field['taxonomy']]);
        }
    }
    if (!empty($slugs)) {
        if (is_string($slugs)) {
            $slugs = explode(',', $slugs);
        }
        $terms = array();
        foreach ($slugs as $slug) {
            if (is_numeric($slug)) {
                $term = get_term_by('id', $slug, $field['taxonomy']);
            } else {
                $term = get_term_by('slug', $slug, $field['taxonomy']);
            }
            $terms[] = $term->slug;
        }
        $field['selected_cats'] = $terms;
    }
    $output .= '<ul class="list" data-taxonomy="' . $field['taxonomy'] . '">';
    $field['walker'] = new Walker_Category_Checklist_QF();

    ob_start();
    wp_terms_checklist(0, $field);
    $output .= ob_get_clean();

    $output .= '</ul></div>';
    return $output;
}

add_action('azqf_checkboxes_short', 'azqf_checkboxes_short', 10, 3);

function azqf_checkboxes_short($output, $field, $params) {
    $labels = get_taxonomy_labels($field['taxonomy']);
    $name = $labels['name'];
    if (isset($field['label'])) {
        $name = $field['label'];
    }
    if (isset($params[$field['taxonomy']]) && is_string($params[$field['taxonomy']])) {
        $slugs = explode(',', $params[$field['taxonomy']]);
        $terms = array();
        foreach ($slugs as $slug) {
            if (is_numeric($slug)) {
                $term = get_term_by('id', $slug, $field['taxonomy']);
            } else {
                $term = get_term_by('slug', $slug, $field['taxonomy']);
            }
            if (is_object($term)) {
                $terms[] = $term->name;
            }
        }
        $value = implode(',', $terms);
        $output .= '<dt>' . $name . '</dt>';
        $output .= '<dd>' . $value . '</dd>';
    }

    return $output;
}
