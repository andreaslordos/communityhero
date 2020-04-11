<?php

add_action('wp_enqueue_scripts', 'azt_scripts');

function azt_scripts() {
    $settings = get_option('azh-settings');
    if (isset($settings['templates']['enable']) && $settings['templates']['enable']) {
        wp_enqueue_script('ion-range-slider', plugins_url('js/ion.rangeSlider.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_style('ion-range-slider', plugins_url('css/ion.rangeSlider.css', __FILE__), false, AZH_PLUGIN_VERSION);
        wp_enqueue_script('air-datepicker', plugins_url('js/air-datepicker.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_style('air-datepicker', plugins_url('css/air-datepicker.css', __FILE__), false, AZH_PLUGIN_VERSION);
        wp_enqueue_script('azt_frontend', plugins_url('js/templates.js', __FILE__), array('jquery', 'ion-range-slider', 'air-datepicker', 'underscore'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_style('azt_frontend', plugins_url('css/templates.css', __FILE__), array(), AZH_PLUGIN_VERSION);
        wp_localize_script('azt_frontend', 'azt', azt_get_frontend_object());
    }
}

add_action('admin_enqueue_scripts', 'azt_admin_scripts');

function azt_admin_scripts() {
    $settings = get_option('azh-settings');
    if (isset($settings['templates']['enable']) && $settings['templates']['enable']) {
        if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
            wp_localize_script('azh-frontend-customization-options', 'azt', azt_get_frontend_object());
        }
    }
}

function azt_get_item_fields($post_id) {
    $row = array('id' => $post_id);
    $metadata = get_post_meta($post_id);
    foreach ($metadata as $key => $value) {
        if ($key[0] != '_') {
            $row[$key] = reset($value);
        }
    }
    return $row;
}

function azt_get_frontend_object() {
    $azt = array(
        'fields' => array('id' => array()),
        'table' => array(),
    );
    $settings = get_option('azh-settings');
    if (isset($settings['templates']['enable']) && $settings['templates']['enable']) {
        $posts = get_posts(array(
            'post_type' => 'azt_item',
            'posts_per_page' => '-1',
        ));
        if ($posts) {
            foreach ($posts as $post) {
                $row = json_decode($post->post_content, true);
                if (!$row) {
                    $row = azt_get_item_fields($post->ID);
                }
                foreach ($row as $key => $value) {
                    if (!isset($azt['fields'][$key])) {
                        $azt['fields'][$key] = array('values' => array());
                    }
                    $azt['fields'][$key]['values'][] = $row[$key];
                    $azt['fields'][$key]['values'] = array_values(array_unique($azt['fields'][$key]['values']));
                }
                $azt['table'][] = $row;
            }
        }
        $admin_columns = get_option('cpac_options_azt_item');
        if ($admin_columns && is_array($admin_columns)) {
            foreach($admin_columns as $admin_column) {
                if($admin_column['type'] === 'column-meta' && $admin_column['field_type'] === 'numeric') {                    
                    if(isset($azt['fields'][$admin_column['field']])) {
                        $azt['fields'][$admin_column['field']]['type'] = 'number';
                    }
                }
            }
        }
    }
    return apply_filters('azt_get_frontend_object', $azt);
}

add_filter('wp_insert_post_data', 'azt_insert_post_data', 10, 2);

function azt_insert_post_data($data, $postarr) {
    if ($data['post_type'] === 'azt_item' && !empty($postarr['ID'])) {
        $data['post_content'] = wp_slash(json_encode(azt_get_item_fields($postarr['ID'])));
    }
    return $data;
}

add_action('init', 'azt_init');

function azt_init() {
    $settings = get_option('azh-settings');
    if (isset($settings['templates']['enable']) && $settings['templates']['enable']) {
        register_post_type('azt_item', array(
            'labels' => array(
                'name' => __('Item', 'azh'),
                'singular_name' => __('Item', 'azh'),
                'add_new' => __('Add Item', 'azh'),
                'add_new_item' => __('Add New Item', 'azh'),
                'edit_item' => __('Edit Item', 'azh'),
                'new_item' => __('New Item', 'azh'),
                'view_item' => __('View Item', 'azh'),
                'search_items' => __('Search Items', 'azh'),
                'not_found' => __('No Item found', 'azh'),
                'not_found_in_trash' => __('No Item found in Trash', 'azh'),
                'parent_item_colon' => __('Parent Item:', 'azh'),
                'menu_name' => __('Items', 'azh'),
            ),
            'query_var' => false,
            'rewrite' => false,
            'hierarchical' => true,
            'supports' => array('thumbnail', 'title', 'custom-fields', 'author', 'comments'),
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => true,
            'public' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
        ));
    }
}

add_action('pt-ocdi/before_content_import_execution', 'azt_ocdi_before_import', 11, 3);

function azt_ocdi_before_import($import_files, $predefined_import_files, $predefined_index) {
    $demo = $predefined_import_files[$predefined_index];
    if (isset($demo['options_url'])) {
        azt_init();
    }
}

function azh_templates_section_callback() {
    
}

add_action('admin_init', 'azt_settings');

function azt_settings() {
    add_settings_section(
            'azh_templates_section', // Section ID
            esc_html__('Templates', 'azh'), // Title above settings section
            'azh_templates_section_callback', // Name of function that renders a description of the settings section
            'azh-settings'                     // Page to show on
    );
    add_settings_field(
            'templates', // Field ID
            esc_html__('Templates', 'azh'), // Label to the left
            'azh_checkbox', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_templates_section', // Associate with which settings section?
            array(
        'id' => 'templates',
        'desc' => esc_html__('Templates engine.', 'azh'),
        'default' => array(
            'enable' => '0',
        ),
        'options' => array(
            'enable' => __('Enable', 'azh')
        )
            )
    );
}

add_filter('azh-all-settings', 'azt_all_settings');

function azt_all_settings($all_settings) {
    foreach ($all_settings as $dir => $dir_settings) {
        $dir_settings['child-suggestions']['general/form-container.htm'][] = 'forms/ion-range-slider.htm';
        $dir_settings['child-suggestions']['general/form-without-submit.htm'][] = 'forms/ion-range-slider.htm';
        $dir_settings['files_settings']['forms/ion-range-slider.htm'] = array('order' => -2);
        $all_settings[$dir] = $dir_settings;
    }
    return $all_settings;
}

add_filter('acp/editing/save_value', 'azt_acp_editing_save_value', 10, 3);

function azt_acp_editing_save_value($value, $column, $post_id) {
    if ($column->get_post_type() === 'azt_item') {
        if ($column->get_field_type() === 'date') {
            return DateTime::createFromFormat('Ymd', $value)->getTimestamp();
        }
        if ($column->get_field_type() === 'image') {
            return wp_get_attachment_image_url($value, 'full');
        }
    }
    return $value;
}

add_action('acp/editing/saved', 'azt_acp_editing_saved', 10, 3);

function azt_acp_editing_saved($column, $post_id, $value) {
    if ($column->get_post_type() === 'azt_item') {
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => wp_slash(json_encode(azt_get_item_fields($post_id))),
        ));
    }
}

add_action('acf/save_post', 'azt_acf_save_post', 20, 1);

function azt_acf_save_post($post_id) {
    if ($_POST['post_type'] === 'azt_item') {
        wp_update_post(array(
            'ID' => $post_id,
            'post_content' => wp_slash(json_encode(azt_get_item_fields($post_id))),
        ));
    }
}
