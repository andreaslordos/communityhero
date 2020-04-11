<?php

function azexo_tgmpa_register() {

    $plugins = array();
    if (file_exists(get_stylesheet_directory() . '/plugins/' . azexo_get_skin() . '-page-builder.zip')) {
        $plugins[] = array(
            'name' => esc_html__('Core theme plugin', 'foodpicky'),
            'slug' => azexo_get_skin() . '-page-builder',
            'source' => get_stylesheet_directory() . '/plugins/' . azexo_get_skin() . '-page-builder.zip',
            'required' => true,
            'version' => AZEXO_FRAMEWORK_VERSION,
        );
    }
    $plugins[] = array(
        'name' => esc_html__('Redux Framework', 'foodpicky'),
        'slug' => 'redux-framework',
        'required' => true,
    );
    $plugins[] = array(
        'name' => esc_html__('WordPress Importer', 'foodpicky'),
        'slug' => 'wordpress-importer',
        'required' => true,
    );
    $plugins[] = array(
        'name' => esc_html__('WP-LESS', 'foodpicky'),
        'slug' => 'wp-less',
    );
    $plugins[] = array(
        'name' => esc_html__('Infinite scroll', 'foodpicky'),
        'slug' => 'infinite-scroll',
    );
    $plugins[] = array(
        'name' => esc_html__('Widget CSS Classes', 'foodpicky'),
        'slug' => 'widget-css-classes',
    );
    $plugins[] = array(
        'name' => esc_html__('Contact Form 7', 'foodpicky'),
        'slug' => 'contact-form-7',
    );



    if (in_array(azexo_get_skin(), array('foodpicky', 'loocal', 'wisem', 'sportak', 'kuponhub', 'medican'))) {
        $plugins[] = array(
            'name' => esc_html__('Custom Sidebars', 'foodpicky'),
            'slug' => 'custom-sidebars',
            'required' => true,
        );
    }

    if (in_array(azexo_get_skin(), array('foodpicky', 'loocal', 'kuponhub', 'medican'))) {
        $plugins[] = array(
            'name' => esc_html__('Custom Post Type UI', 'foodpicky'),
            'slug' => 'custom-post-type-ui',
            'required' => true,
        );
    }

    if (file_exists(get_stylesheet_directory() . '/plugins/custom-classes.zip')) {
        $plugins[] = array(
            'name' => esc_html__('Custom classes for page/post', 'foodpicky'),
            'slug' => 'custom-classes',
            'source' => get_stylesheet_directory() . '/plugins/custom-classes.zip',
            'required' => true,
            'version' => '0.1',
        );
    }
    $plugin_path = get_stylesheet_directory() . '/plugins/js_composer.zip';
    if (file_exists($plugin_path)) {
        $plugins[] = array(
            'name' => esc_html__('WPBakery Page Builder', 'foodpicky'),
            'slug' => 'js_composer',
            'source' => get_stylesheet_directory() . '/plugins/js_composer.zip',
            'required' => true,
            'version' => '5.7',
            'external_url' => '',
        );
    }
    $plugins = apply_filters('azexo_plugins', $plugins);
    if (!empty($plugins)) {
        tgmpa($plugins, array());
    }


    $additional_plugins = array(
	'jetpack-widget-visibility' => esc_html__('JP Widget Visibility', 'foodpicky'),
        'vc_widgets' => esc_html__('Visual Composer Widgets', 'foodpicky'),
        'azexo_vc_elements' => esc_html__('AZEXO Visual Composer elements', 'foodpicky'),
        'az_social_login' => esc_html__('AZEXO Social Login', 'foodpicky'),
        'az_email_verification' => esc_html__('AZEXO Email Verification', 'foodpicky'),
        'az_likes' => esc_html__('AZEXO Post/Comments likes', 'foodpicky'),
        'az_voting' => esc_html__('AZEXO Voting', 'foodpicky'),
        'azexo_html' => esc_html__('AZEXO HTML Customizer', 'foodpicky'),
        'azh_extension' => esc_html__('AZEXO HTML Library', 'foodpicky'),
        'page-builder-by-azexo' => esc_html__('Page builder by AZEXO', 'foodpicky'),
        'elements-library-for-azexo-builder' => esc_html__('Elements Library for AZEXO Builder', 'foodpicky'),
        'az_listings' => esc_html__('AZEXO Listings', 'foodpicky'),
        'az_query_form' => esc_html__('AZEXO Query Form', 'foodpicky'),
        'az_group_buying' => esc_html__('AZEXO Group Buying', 'foodpicky'),
        'az_vouchers' => esc_html__('AZEXO Vouchers', 'foodpicky'),
        'az_bookings' => esc_html__('AZEXO Bookings', 'foodpicky'),
        'az_deals' => esc_html__('AZEXO Deals', 'foodpicky'),
        'az_sport_club' => esc_html__('AZEXO Sport Club', 'foodpicky'),
        'az_locations' => esc_html__('AZEXO Locations', 'foodpicky'),
        'circular_countdown' => esc_html__('Circular CountDown', 'foodpicky'),
    );
    $plugins = array();
    foreach ($additional_plugins as $additional_plugin_slug => $additional_plugin_name) {
        $plugin_path = get_stylesheet_directory() . '/plugins/' . $additional_plugin_slug . '.zip';
        if (file_exists($plugin_path)) {
            $plugins[] = array(
                'name' => $additional_plugin_name,
                'slug' => $additional_plugin_slug,
                'source' => $plugin_path,
                'required' => true,
                'version' => AZEXO_FRAMEWORK_VERSION,
            );
        }
    }
    $plugins = apply_filters('azexo_plugins', $plugins);
    if (!empty($plugins)) {
        tgmpa($plugins, array(
//            'is_automatic' => true,
        ));
    }
}

add_action('tgmpa_register', 'azexo_tgmpa_register');
