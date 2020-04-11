<?php

function azh_wpbakery_widget_form_field($settings, $value) {
    if (empty($value)) {
        $value = azh_create_post(array(
            'post_title' => 'WPBakery element',
            'post_type' => 'azh_widget',
            'post_status' => 'hidden',
            'element' => $settings['element_path'],
        ));
        if (isset($_POST['post_id'])) {
            $post_id = (int) $_POST['post_id'];
            $azh_widgets = get_post_meta($post_id, '_azh_widgets', true);
            if (!$azh_widgets) {
                $azh_widgets = array();
            }
            $azh_widgets[] = $value;
            update_post_meta($post_id, '_azh_widgets', $azh_widgets);
        }
    }
    $output = '<input type="hidden" class="azh-widget wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '" data-element="' . $settings['element_file'] . '" name="' . $settings['param_name'] . '" value="' . $value . '">';

    if (isset($_POST['post_id'])) {
        $post_id = (int) $_POST['post_id'];
        $same_url = add_query_arg(array('azh' => 'customize', 'azhf' => $post_id), get_edit_post_link($value));
        $blank_url = add_query_arg(array('azh' => 'customize'), get_edit_post_link($value));
        $output .= '<a href="' . $same_url . '" class="button azh-widget-edit-button" target="_blank" data-post-id="' . $post_id . '">' . esc_attr__('On same page', 'azh') . '</a> ';
        $output .= ' <a href="' . $blank_url . '" class="button azh-widget-edit-button" target="_blank" data-post-id="' . $post_id . '">' . esc_attr__('On blank page', 'azh') . '</a>';
    }

    return $output;
}

add_filter('wp_insert_post_data', 'azh_wpbakery_insert_post_data', 10, 2);

function azh_wpbakery_insert_post_data($data, $postarr) {
    if (function_exists('vc_map')) {
        preg_match_all('/azh_widget=\"(\d+)\"/', wp_unslash($data['post_content']), $matches);
        if ($matches && !empty($matches[1])) {
            $azh_widgets = array();
            foreach ($matches[1] as $post_id) {
                if (!isset($azh_widgets[$post_id])) {
                    $azh_widgets[$post_id] = 0;
                }
                $azh_widgets[$post_id] = $azh_widgets[$post_id] + 1;
            }
            $replaces = array();
            foreach ($azh_widgets as $post_id => $count) {
                if ($count > 1) {
                    $post = get_post($post_id);
                    $replaces[$post_id] = array($post_id);
                    for ($i = 1; $i < $count; $i++) {
                        $replaces[$post_id][] = azh_clone_post($post);
                    }
                }
            }
            if (!empty($replaces)) {
                $data['post_content'] = wp_slash(preg_replace_callback('/azh_widget=\"(\d+)\"/', function($m) use (&$replaces) {
                            if ($replaces[$m[1]]) {
                                return 'azh_widget="' . array_pop($replaces[$m[1]]) . '"';
                            } else {
                                return $m[0];
                            }
                        }, wp_unslash($data['post_content'])));
            }
        }
    }
    return $data;
}

add_action('before_delete_post', 'azh_wpbakery_before_delete_post');

function azh_wpbakery_before_delete_post($post_id) {
    if (function_exists('vc_map')) {
        $azh_widgets = get_post_meta($post_id, '_azh_widgets', true);
        if (is_array($azh_widgets)) {
            foreach ($azh_widgets as $azh_widget) {
                wp_delete_post((int) $azh_widget);
            }
        }
        $post = get_post($post_id);
        preg_match_all('/azh_widget=\"(\d+)\"/', $post->post_content, $matches);
        if ($matches && !empty($matches[1])) {
            foreach ($matches[1] as $azh_widget) {
                wp_delete_post((int) $azh_widget);
            }
        }
    }
}

function azh_wpbakery_getExtraClass($el_class) {
    $output = '';
    if ('' !== $el_class) {
        $output = ' ' . str_replace('.', '', $el_class);
    }

    return $output;
}

function azh_wpbakery_add_element($name, $base, $icon, $category, $element_path, $element_file) {
    global $shortcode_tags;
    vc_map(array(
        'name' => $name,
        'base' => $base,
        'icon' => $icon,
        'category' => $category,
        'params' => array(
            array(
                'type' => 'azh_widget',
                'heading' => __('Editor', 'azh'),
                'element_path' => $element_path,
                'element_file' => $element_file,
                'param_name' => 'azh_widget',
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Extra class name', 'azh'),
                'param_name' => 'el_class',
                'description' => __('Style particular content element differently - add a class name and refer to it in custom CSS.', 'azh'),
            ),
            array(
                'type' => 'css_editor',
                'heading' => __('CSS box', 'azh'),
                'param_name' => 'css',
                'group' => __('Design Options', 'azh'),
            ),
        ),
    ));
    $shortcode_tags[$base] = function($atts, $content = null, $tag = null) use($element_file) {
        if (isset($atts['azh_widget']) && is_numeric($atts['azh_widget'])) {
            $css_class = $class_to_filter = '';
            if (function_exists('vc_shortcode_custom_css_class')) {
                $class_to_filter .= vc_shortcode_custom_css_class($atts['css'], ' ') . azh_wpbakery_getExtraClass($atts['el_class']);
                $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $tag, $atts);
            }
            $id = 'azh' . uniqid();
            $content = '<div id="' . $id . '" class="' . $css_class . '">';
            $content .= azh_post(array('id' => $atts['azh_widget']));
            $content .= '</div>';

            if (isset($_POST['action']) && $_POST['action'] == 'vc_load_shortcode') {
                ob_start();
                wp_print_styles();
                $content .= ob_get_clean();
                $content .= '<script>window.azh.frontend_init(window.jQuery(window.document.getElementById("' . $id . '")));</script>';
            }
        }
        return $content;
    };
}

add_action('init', 'azh_wpbakery_init');

function azh_wpbakery_init() {
    if (!is_admin()) {
        register_post_status('hidden', array(
            'public' => true,
        ));
    }
    if (function_exists('vc_map')) {

        vc_add_shortcode_param('azh_widget', 'azh_wpbakery_widget_form_field', AZH_URL . '/js/wpbakery.js');
        azh_wpbakery_add_element(__('AZEXO Container', 'azh'), 'azh-wpb-container', 'icon-wpb-layout_sidebar', __('Structure', 'azh'), 'empty rows/row.htm', '');

        $library = azh_get_library();
        if (is_array($library['elements'])) {
            foreach ($library['elements'] as $element_file => $name) {
                $preview = '';
                if (file_exists(str_replace('.htm', '.jpg', $element_file))) {
                    $preview = str_replace('.htm', '.jpg', $element_file);
                }
                if (file_exists(str_replace('.htm', '.png', $element_file))) {
                    $preview = str_replace('.htm', '.png', $element_file);
                }
                if (file_exists(str_replace('.htm', '.svg', $element_file))) {
                    $preview = str_replace('.htm', '.svg', $element_file);
                }
                if (file_exists($preview)) {
                    $preview = str_replace($library['elements_dir'][$element_file], $library['elements_uri'][$element_file], $preview);
                } else {
                    $preview = false;
                }
                $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
                $parts = explode('/', $path);
                if (!in_array($parts[0], array('general', 'ajax', 'empty rows', 'forms')) && !in_array('forms', $parts) && !in_array('forms-black', $parts) && !in_array('forms-white', $parts)) {
                    azh_wpbakery_add_element(str_replace(array('.htm'), '', $name), 'azh-' . preg_replace('/[^\w\d]/', '-', $path), ($preview ? 'azh-' . preg_replace('/[^\w\d_]/', '-', $path) : 'icon-wpb-raw-html'), ucfirst($parts[0]), $path, $element_file);
                }
            }
        }
    }
}

add_action('admin_footer', 'azh_wpbakery_admin_footer');

function azh_wpbakery_admin_footer() {
    if (function_exists('vc_map')) {
        $library = azh_get_library();
        if (is_array($library['elements'])) {
            print '<style>';
            foreach ($library['elements'] as $element_file => $name) {
                $preview = '';
                if (file_exists(str_replace('.htm', '.jpg', $element_file))) {
                    $preview = str_replace('.htm', '.jpg', $element_file);
                }
                if (file_exists(str_replace('.htm', '.png', $element_file))) {
                    $preview = str_replace('.htm', '.png', $element_file);
                }
                if (file_exists($preview)) {
                    $preview = str_replace($library['elements_dir'][$element_file], $library['elements_uri'][$element_file], $preview);
                } else {
                    $preview = false;
                }
                if ($preview) {
                    $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
                    $icon = 'azh-' . preg_replace('/[^\w\d_]/', '-', $path);
                    print '.' . $icon . ' { background-image: url(' . $preview . ') !important; background-size: contain !important; background-position: center !important; background-repeat: no-repeat !important; }';
                    print "\n";
                }
            }
            print '</style>';
        }
    }
}
