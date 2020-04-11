<?php

add_filter('azexo_settings_sections', 'azexo_wpml_settings_sections', 100);

function azexo_wpml_settings_sections($sections) {
    //^^^ this function called only in admin
    if (class_exists('SitePress')) {
        $options = get_option(AZEXO_FRAMEWORK);
        $translatable = get_option(AZEXO_FRAMEWORK . '-translatable', array());
        $translatable_old = $translatable;
        foreach ($sections as $section) {
            if (isset($section['fields']) && is_array($section['fields'])) {
                foreach ($section['fields'] as $field) {
                    if (isset($field['type']) && ($field['type'] == 'text' || $field['type'] == 'textarea')) {
                        $size_pos = strpos($field['id'], '_size');
                        $length_pos = strpos($field['id'], '_length');
                        if ($size_pos != (strlen($field['id']) - strlen('_size')) && $length_pos != (strlen($field['id']) - strlen('_length'))) {
                            if (isset($options[$field['id']])) {
                                if (!is_numeric($options[$field['id']])) {
                                    $translatable[$field['id']] = true;
                                }
                            } else {
                                if (isset($field['default'])) {
                                    if (!is_numeric($field['default'])) {
                                        $translatable[$field['id']] = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $diff = array_diff(array_keys($translatable), array_keys($translatable_old));
        if (!empty($diff)) {
            update_option(AZEXO_FRAMEWORK . '-translatable', $translatable);
        }
    }
    return $sections;
}

add_action('wp_loaded', 'azexo_wpml_wp_loaded');

function azexo_wpml_wp_loaded() {
    if (class_exists('SitePress')) {
        $translatable = array_keys(get_option(AZEXO_FRAMEWORK . '-translatable', array()));
        foreach ($translatable as $name) {
            do_action('wpml_register_single_string', AZEXO_FRAMEWORK, $name, $name);
        }
    }
}

add_filter('option_' . AZEXO_FRAMEWORK, 'azexo_wpml_options');

function azexo_wpml_options($options) {
    if (class_exists('SitePress')) {
        $theme = wp_get_theme();        
        $translatable = array_keys(get_option(AZEXO_FRAMEWORK . '-translatable', array()));
        foreach ($translatable as $name) {
            if (isset($options[$name])) {
                $translation = apply_filters('wpml_translate_single_string', $name, $theme->get('TextDomain'), $name);
                if ($translation != $name) {
                    $options[$name] = $translation;
                }
            }
        }
    }
    return $options;
}
