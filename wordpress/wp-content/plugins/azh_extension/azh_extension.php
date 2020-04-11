<?php

/*
  Plugin Name: AZEXO HTML library
  Description: AZEXO HTML library
  Author: azexo
  Author URI: http://azexo.com
  Version: 1.27
  Text Domain: azh
 */


add_filter('upload_mimes', 'azh_upload_mimes');

function azh_upload_mimes($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

add_action('admin_notices', 'azh_extension_notices');

function azh_extension_notices() {
    if (defined('AZH_VERSION')) {
        $plugin_data = get_plugin_data(__FILE__);
        $plugin_version = $plugin_data['Version'];
        if (version_compare($plugin_version, AZH_VERSION) !== 0) {
            print '<div id="azh-version" class="notice-error settings-error notice is-dismissible"><p>' . __('AZEXO Builder version does not correspond with library version. Please updae library plugin or builder plugin', 'azh') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
        }
    }
}

add_action('admin_init', 'azh_extension_options', 11);

function azh_extension_options() {
    if (file_exists(dirname(__FILE__) . '/azh_settings.json')) {
        $settings = get_option('azh-settings');
        if ((!is_array($settings) || !isset($settings['azh-uri'])) && function_exists('azh_filesystem')) {
            azh_filesystem();
            global $wp_filesystem;
            $settings = $wp_filesystem->get_contents(dirname(__FILE__) . '/azh_settings.json');
            update_option('azh-settings', json_decode($settings, true));
        }
    }
    if (class_exists('WPLessPlugin')) {
        if (!defined('AZEXO_FRAMEWORK')) {
            add_settings_field(
                    'brand-color', // Field ID
                    esc_html__('Brand color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'brand-color',
                'type' => 'color',
                'default' => '#FF0000',
                    )
            );
            add_settings_field(
                    'accent-1-color', // Field ID
                    esc_html__('Accent 1 color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'accent-1-color',
                'type' => 'color',
                'default' => '#00FF00',
                    )
            );
            add_settings_field(
                    'accent-2-color', // Field ID
                    esc_html__('Accent 2 color', 'azh'), // Label to the left
                    'azh_textfield', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_general_options_section', // Associate with which settings section?
                    array(
                'id' => 'accent-2-color',
                'type' => 'color',
                'default' => '#0000FF',
                    )
            );
        }

        add_settings_field(
                'google-fonts', // Field ID
                esc_html__('Google fonts families', 'azh'), // Label to the left
                'azh_textarea', // Name of function that renders options on the page
                'azh-settings', // Page to show on
                'azh_general_options_section', // Associate with which settings section?
                array(
            'id' => 'google-fonts',
            'default' => "Open+Sans:300,400,500,600,700\n"
            . "Montserrat:400,700\n"
            . "Droid+Serif:400,700",
                )
        );
    }

//    add_settings_field(
//            'css-provided', // Field ID
//            esc_html__('CSS provided', 'azh'), // Label to the left
//            'azh_checkbox', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'css-provided',
//        'default' => array(
//        ),
//        'options' => array(
//        ),
//            )
//    );

    add_settings_field(
            'prefix', // Field ID
            esc_html__('Prefix', 'azh'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'prefix',
        'default' => "azen",
            )
    );

    add_settings_field(
            'azh-uri', // Field ID
            esc_html__('AZH folder URI', 'azh'), // Label to the left
            'azh_textfield', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'azh-uri',
            )
    );
}

add_action('azh_load', 'azh_extension_admin_load', 10, 2);

function azh_extension_admin_load($post_type, $post) {
    wp_enqueue_script('azh_extension_admin', plugins_url('js/admin.js', __FILE__), array('azexo_html'), false, true);
}

add_filter('wp-less_stylesheet_compute_target_path', 'azh_extension_target_path');

function azh_extension_target_path($target_path) {
    $target_path = preg_replace('#^' . plugin_dir_url('') . '#U', '/', $target_path);
    return $target_path;
}

add_filter('azh_get_content_scripts', 'azh_extension_get_content_scripts');

function azh_extension_get_content_scripts($post_scripts) {
    static $projects_enqueued = array();
    if (isset($post_scripts['paths'])) {
        $projects = array();
        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . get_template() . '-skin.less')) {
            $projects[get_template()] = true;
        }
        if (function_exists('azexo_get_skin')) {
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . azexo_get_skin() . '-skin.less')) {
                $projects[azexo_get_skin()] = true;
            }
        }
        foreach ($post_scripts['paths'] as $section_element => $path) {
            $folders = explode('/', $section_element);
            $project = reset($folders);
            if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/' . $project . '-skin.less')) {
                $projects[$project] = true;
            }
        }
        if (!empty($projects)) {
            if (class_exists('WPLessPlugin')) {
                $less = WPLessPlugin::getInstance();
                azh_extension_set_less_variables();

                WPLessStylesheet::$upload_dir = $less->getConfiguration()->getUploadDir();
                WPLessStylesheet::$upload_uri = $less->getConfiguration()->getUploadUrl();

                if (!wp_mkdir_p(WPLessStylesheet::$upload_dir)) {
                    throw new WPLessException(sprintf('The upload dir folder (`%s`) is not writable from %s.', WPLessStylesheet::$upload_dir, get_class($less)));
                }
                foreach ($projects as $project => $flag) {
                    if (!isset($projects_enqueued[$project])) {
                        wp_enqueue_style('azexo-extension-skin-' . $project, plugins_url('', __FILE__) . '/less/' . $project . '-skin.less');
                        $stylesheet = $less->processStylesheet('azexo-extension-skin-' . $project);
                        $post_scripts['css'][] = $stylesheet->getTargetUri();
                        $projects_enqueued[$project] = true;
                    }
                }
            } else {
                foreach ($projects as $project => $flag) {
                    if (!isset($projects_enqueued[$project])) {
                        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/css/' . $project . '-skin.css')) {
                            $url = plugins_url('', __FILE__) . '/css/' . $project . '-skin.css';
                            wp_enqueue_style('azexo-extension-skin-' . $project, $url);
                            $post_scripts['css'][] = $url;
                            $projects_enqueued[$project] = true;
                        }
                    }
                }
            }
        }
    }

    return $post_scripts;
}

function azh_extension_get_colors() {
    global $post;
    $brand_color = '#FF0000';
    $accent_1_color = '#00FF00';
    $accent_2_color = '#0000FF';
    if (defined('AZEXO_FRAMEWORK')) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (isset($options['brand-color'])) {
            $brand_color = $options['brand-color'];
        }
        if (isset($options['accent-1-color'])) {
            $accent_1_color = $options['accent-1-color'];
        }
        if (isset($options['accent-2-color'])) {
            $accent_2_color = $options['accent-2-color'];
        }
    } else {
        $settings = get_option('azh-settings');
        if (isset($settings['brand-color'])) {
            $brand_color = $settings['brand-color'];
        }
        if (isset($settings['accent-1-color'])) {
            $accent_1_color = $settings['accent-1-color'];
        }
        if (isset($settings['accent-2-color'])) {
            $accent_2_color = $settings['accent-2-color'];
        }
    }
    if ($post) {
        if (get_post_meta($post->ID, '_brand-color', true)) {
            $brand_color = get_post_meta($post->ID, '_brand-color', true);
        }
        if (get_post_meta($post->ID, '_accent-1-color', true)) {
            $accent_1_color = get_post_meta($post->ID, '_accent-1-color', true);
        }
        if (get_post_meta($post->ID, '_accent-2-color', true)) {
            $accent_2_color = get_post_meta($post->ID, '_accent-2-color', true);
        }
    }
    return array(
        'brand_color' => $brand_color,
        'accent_1_color' => $accent_1_color,
        'accent_2_color' => $accent_2_color
    );
}

function azh_extension_set_less_variables() {
    if (class_exists('WPLessPlugin')) {
        global $post;
        $settings = get_option('azh-settings');
        $less = WPLessPlugin::getInstance();
        $colors = azh_extension_get_colors();
        extract($colors);
        if ($brand_color) {
            $less->addVariable('brand-color', $brand_color);
        }
        if ($accent_1_color) {
            $less->addVariable('accent-1-color', $accent_1_color);
        }
        if ($accent_2_color) {
            $less->addVariable('accent-2-color', $accent_2_color);
        }

        if (function_exists('azh_get_google_fonts')) {
            $google_fonts = azh_get_google_fonts(azh_get_all_settings());
            if (is_array($google_fonts)) {
                foreach ($google_fonts as $font_family => $weights) {
                    $less->addVariable('google-font-family-' . str_replace('+', '-', $font_family), str_replace('+', ' ', $font_family));
                }
            }
        }
        if (class_exists('Less_Colors')) {
            Less_Colors::$colors = array();
        }
    }
}

add_action('admin_enqueue_scripts', 'azh_extension_admin_scripts');

function azh_extension_admin_scripts() {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        wp_enqueue_style('azh-extension-admin-frontend', plugins_url('css/admin-frontend.css', __FILE__));
        wp_enqueue_script('azh-extension-admin-frontend', plugins_url('js/admin-frontend.js', __FILE__), array('azh_admin_frontend'), false, true);
        wp_enqueue_script('azh-extension-frontend-customization-options', plugins_url('frontend-customization-options.js', __FILE__), array(), false, true);
    }
}

add_action('wp_enqueue_scripts', 'azh_extension_scripts', 1000);

function azh_extension_scripts() {
    $skin_style = false;
    if (file_exists(untrailingslashit(dirname(__FILE__)) . '/css/skin.css')) {
        $skin_style = plugins_url('', __FILE__) . '/css/skin.css';
    }


    if (class_exists('WPLessPlugin')) {
        $less = WPLessPlugin::getInstance();
        azh_extension_set_less_variables();
        $less->dispatch();
        if (file_exists(untrailingslashit(dirname(__FILE__)) . '/less/skin.less')) {
            $skin_style = plugins_url('', __FILE__) . '/less/skin.less';
        }
    }

    if (!empty($skin_style)) {
        wp_enqueue_style('azexo-extension-skin', $skin_style);
    }


    wp_enqueue_script('flexslider', plugins_url('js/jquery.flexslider.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('azh-owl.carousel', plugins_url('js/owl.carousel.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('knob', plugins_url('js/jquery.knob.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('fitvids', plugins_url('js/jquery.fitvids.js', __FILE__), array('jquery'), false, true);
    wp_enqueue_script('azh-extension-frontend', plugins_url('js/frontend.js', __FILE__), array('jquery', 'flexslider', 'isotope', 'azh-owl.carousel', 'imagesloaded'), false, true);
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        wp_enqueue_style('azh-extension-admin-frontend', plugins_url('css/admin-frontend.css', __FILE__));
        wp_enqueue_script('azh-extension-admin-frontend', plugins_url('js/admin-frontend.js', __FILE__), array('azh_admin_frontend'), false, true);
        wp_enqueue_script('azh-extension-frontend-customization-options', plugins_url('frontend-customization-options.js', __FILE__), array(), false, true);
    }
    if (isset($_GET['azh']) && $_GET['azh'] == 'fullpage') {
        wp_enqueue_style('fullpage', plugins_url('css/jquery.fullpage.css', __FILE__), array(), null);
        wp_enqueue_script('easings', plugins_url('js/jquery.easings.min.js', __FILE__), array('jquery'), false, true);
        wp_enqueue_script('fullpage', plugins_url('js/jquery.fullpage.js', __FILE__), array('jquery', 'easings'), false, true);
    }
}

add_filter('azh_directory', 'azh_extension_directory');

function azh_extension_directory($dir) {
    $settings = get_option('azh-settings');
    if (empty($settings['azh-uri'])) {
        $dir[untrailingslashit(dirname(__FILE__)) . '/azh'] = plugins_url('', __FILE__) . '/azh';
    } else {
        $dir[untrailingslashit(dirname(__FILE__)) . '/azh'] = $settings['azh-uri'];
    }
    return $dir;
}

add_filter('azh_replaces', 'azh_extension_replaces');

function azh_extension_replaces($replaces) {
    return $replaces;
}

add_filter('azh_settings_sanitize_callback', 'azh_extension_settings_sanitize_callback');

function azh_extension_settings_sanitize_callback($input) {
    if (!file_exists(dirname(__FILE__) . '/azh_settings.json') && function_exists('azh_filesystem')) {
        azh_filesystem();
        global $wp_filesystem;
        $wp_filesystem->put_contents(dirname(__FILE__) . '/azh_settings.json', json_encode($input), FS_CHMOD_FILE);
    }
    return $input;
}

add_filter('azh_get_object', 'azh_extension_get_object');

function azh_extension_get_object($azh) {
    global $post;
    $colors = azh_extension_get_colors();
    extract($colors);

    $azh['brand_color'] = $brand_color;
    $azh['accent_1_color'] = $accent_1_color;
    $azh['accent_2_color'] = $accent_2_color;
    $azh['cloneable_refresh'][] = '.az-slides';
    $azh['cloneable_refresh'][] = '.az-flex-thumbnails';
    $azh['cloneable_refresh'][] = '.az-carousel';
    $azh['cloneable_refresh'][] = '[data-masonry-items]';
    $azh['cloneable_refresh'][] = '[data-isotope-items]';
    $azh['cloneable_refresh'][] = '[data-isotope-filters]';
    $azh['i18n']['please_wait_page_reload'] = esc_html__('Please wait page reload', 'azh');
    $azh['i18n']['accent_colors'] = esc_html__('Accent colors', 'azh');
    $azh['i18n']['brand_color'] = esc_html__('Brand color', 'azh');
    $azh['i18n']['accent_1_color'] = esc_html__('Accent 1 color', 'azh');
    $azh['i18n']['accent_2_color'] = esc_html__('Accent 2 color', 'azh');
    return $azh;
}
