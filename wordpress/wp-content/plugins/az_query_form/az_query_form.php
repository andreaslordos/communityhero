<?php

/**
 * Plugin Name:  AZEXO Query Form
 * Plugin URI:   http://www.azexo.com
 * Description:  WordPress Meta Data & Taxonomies Filters
 * Author:       AZEXO
 * Author URI:   http://www.azexo.com
 * Version: 1.27
 * Text Domain:  azqf
 * Domain Path:  languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('AZQF_URL', plugins_url('', __FILE__));
define('AZQF_DIR', trailingslashit(dirname(__FILE__)));


add_action('plugins_loaded', 'azqf_plugins_loaded');

function azqf_plugins_loaded() {
    load_plugin_textdomain('azqf', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('tgmpa_register', 'azqf_tgmpa_register');

function azqf_tgmpa_register() {
    $plugins = array(
        array(
            'name' => 'CMB2',
            'slug' => 'cmb2',
            'required' => true,
        ),
        array(
            'name' => 'Better Notifications for WordPress',
            'slug' => 'bnfw',
        ),
    );
    tgmpa($plugins, array());
}

if (file_exists(AZQF_DIR . 'options.php')) {
    require_once(AZQF_DIR . 'options.php' );
}

$directory_iterator = new DirectoryIterator(AZQF_DIR . 'fields');
foreach ($directory_iterator as $fileInfo) {
    if ($fileInfo->isFile() && $fileInfo->getExtension() == 'php') {
        require_once($fileInfo->getPathname());
    }
}

add_action('init', 'azqf_init');

function azqf_init() {
}

add_action('wp_enqueue_scripts', 'azqf_enqueue_scripts');

function azqf_enqueue_scripts() {
    wp_register_script('azqf', plugins_url('js/azqf.js', __FILE__), array(), false, true);
    wp_localize_script('azqf', 'azqf', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
    wp_register_style('azqf', plugins_url('css/azqf.css', __FILE__));
    wp_enqueue_style('azqf');
}

add_action('wp_loaded', 'azqf_wp_loaded');

function azqf_wp_loaded() {
    //WMPL
    if (class_exists('SitePress')) {
        if (is_admin()) {
            if (function_exists('cmb2_get_option')) {
                $forms = cmb2_get_option('azqf_options', 'forms');
                if (is_array($forms)) {
                    foreach ($forms as $form) {
                        if (isset($form['form'])) {
                            $fiels = json_decode($form['form'], true);
                            foreach ($fiels as $field) {
                                if (isset($field['label'])) {
                                    do_action('wpml_register_single_string', 'AZEXO Query Form', $form['name'] . '-' . $field['label'] . ' label', $field['label']);
                                }
                                if (isset($field['placeholder'])) {
                                    do_action('wpml_register_single_string', 'AZEXO Query Form', $form['name'] . '-' . $field['placeholder'] . ' placeholder', $field['placeholder']);
                                }
                                if (isset($field['show_option_none'])) {
                                    do_action('wpml_register_single_string', 'AZEXO Query Form', $form['name'] . '-' . $field['show_option_none'] . ' show_option_none', $field['show_option_none']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function azqf_form_render($name, $fields) {
    wp_enqueue_script('azqf');
    wp_enqueue_style('azqf');

    $output = '<form method="get" class="azqf-query-form ' . esc_attr(sanitize_title_with_dashes($name)) . '" data-name="' . esc_attr($name) . '" action="' . esc_url(home_url('/')) . '"><span class="toggle"></span><div class="wrapper">';
    foreach ($fields as $field) {
        //WMPL
        if (class_exists('SitePress')) {
            if (isset($field['label'])) {
                $field['label'] = icl_translate('AZEXO Query Form', $name . ' ' . $field['label'] . ' label', $field['label']);
                $field['label'] = apply_filters('wpml_translate_single_string', $field['label'], 'AZEXO Query Form', $name . '-' . $field['label'] . ' label');
            }
            if (isset($field['placeholder'])) {
                $field['placeholder'] = apply_filters('wpml_translate_single_string', $field['placeholder'], 'AZEXO Query Form', $name . '-' . $field['placeholder'] . ' placeholder');
            }
            if (isset($field['show_option_none'])) {
                $field['show_option_none'] = apply_filters('wpml_translate_single_string', $field['show_option_none'], 'AZEXO Query Form', $name . '-' . $field['show_option_none'] . ' show_option_none');
            }
        }

        if (isset($field['type'])) {
            $output .= apply_filters('azqf_' . $field['type'] . '_render', '', $field);
        }
    }
    $output .= '<div class="submit"><input type="submit" value="' . esc_attr__('Search', 'azqf') . '"></div>';
    $output .= '</div></form>';
    return $output;
}

function azqf_form_process($fields, $query = false) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }
    foreach ($fields as $field) {
        if (isset($field['type'])) {
            do_action('azqf_' . $field['type'] . '_process', $query, $field);
        }
    }
}

function azqf_form_short($fields, $params) {
    $output = '';
    foreach ($fields as $field) {
        if (isset($field['type'])) {
            $output .= apply_filters('azqf_' . $field['type'] . '_short', '', $field, $params);
        }
    }
    if (!empty($output)) {
        $output = '<dl>' . $output . '</dl>';
    }
    return $output;
}

add_shortcode('azqf-form', 'azqf_form_shortcode');

function azqf_form_shortcode($atts, $content = null) {
    if (function_exists('cmb2_get_option')) {
        $forms = cmb2_get_option('azqf_options', 'forms');
        if (is_array($forms)) {
            foreach ($forms as $form) {
                if ($form['name'] == $atts['name']) {
                    return azqf_form_render($atts['name'], json_decode($form['form'], true));
                }
            }
        }
    }
    return '';
}

add_action('pre_get_posts', 'azqf_pre_get_posts', 20);

function azqf_pre_get_posts($query) {
    if ($query->is_main_query()) {
        if (function_exists('cmb2_get_option')) {
            $forms = cmb2_get_option('azqf_options', 'forms');
            if (is_array($forms)) {
                foreach ($forms as $form) {
                    $fields = json_decode($form['form'], true);
                    azqf_form_process($fields, $query);
                }
            }
        }
    }
}
