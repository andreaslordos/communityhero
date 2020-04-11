<?php
add_action('admin_menu', 'azh_admin_menu');

function azh_admin_menu() {
    add_menu_page(__('AZEXO Builder', 'azh'), __('AZEXO Builder', 'azh'), 'manage_options', 'azh-settings', 'azh_settings_page', 'dashicons-schedule');
    add_submenu_page('azh-settings', __('AZEXO Builder Forms Settings', 'azh'), __('Forms Settings', 'azh'), 'manage_options', 'azh-forms-settings', 'azh_forms_settings_page', 1);
}

function azh_settings_page() {
    wp_enqueue_style('azh_admin', plugins_url('css/admin.css', __FILE__));
    ?>

    <div class="wrap">
        <h2><?php _e('AZEXO Builder Settings', 'azh'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-settings');
            do_settings_sections('azh-settings');
            submit_button(__('Save Settings', 'azh'));
            ?>
        </form>
    </div>

    <?php
}

function azh_forms_settings_page() {
    wp_enqueue_style('azh_admin', plugins_url('css/admin.css', __FILE__));
    ?>

    <div class="wrap">
        <h2><?php _e('AZEXO Builder Forms Settings', 'azh'); ?></h2>

        <form method="post" action="options.php" class="azh-form">
            <?php
            settings_errors();
            settings_fields('azh-forms-settings');
            do_settings_sections('azh-forms-settings');
            submit_button(__('Save Settings', 'azh'));
            ?>
        </form>
    </div>

    <?php
}

function azh_general_options_callback() {
    
}

function azh_license_callback() {
    
}

function azh_forms_settings_callback() {
    
}

function azh_active_license_callback() {
    ?>
    <p><?php echo esc_html_e('Active license', 'azh'); ?></p>
    <?php
}

add_action('admin_init', 'azh_general_options');

function azh_general_options() {
    if (file_exists(AZH_DIR . 'azh_settings.json')) {
        $settings = get_option('azh-settings');
        if (!is_array($settings) || !isset($settings['post-types'])) {
            azh_filesystem();
            global $wp_filesystem;
            $settings = $wp_filesystem->get_contents(AZH_DIR . 'azh_settings.json');
            update_option('azh-settings', json_decode($settings, true));
        }
    }
    register_setting('azh-settings', 'azh-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));
    register_setting('azh-forms-settings', 'azh-forms-settings', array('sanitize_callback' => 'azh_settings_sanitize_callback'));
    if (isset($_GET['page']) && $_GET['page'] == 'azh-settings') {
        wp_enqueue_style('azh_admin', plugins_url('css/admin.css', __FILE__));
        wp_enqueue_script('azh_admin', plugins_url('js/admin.js', __FILE__), array('wp-color-picker'), false, true);
    }

    if (function_exists('azexo_is_activated')) {
        add_settings_section(
                'azh_license_section', // Section ID
                esc_html__('Product license', 'azh'), // Title above settings section
                'azh_license_callback', // Name of function that renders a description of the settings section
                'azh-settings'                     // Page to show on
        );
        if (azexo_is_activated()) {
            add_settings_field(
                    'oauth_keys', // Field ID
                    esc_html__('Status', 'azh'), // Label to the left
                    'azh_active_license_callback', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_license_section' // Associate with which settings section?
            );
        } else {
            add_settings_field(
                    'oauth_keys', // Field ID
                    esc_html__('Login with Envato to activate', 'azh'), // Label to the left
                    'azexo_oauth_login_callback', // Name of function that renders options on the page
                    'azh-settings', // Page to show on
                    'azh_license_section' // Associate with which settings section?
            );
        }
    }


    add_settings_section(
            'azh_general_options_section', // Section ID
            esc_html__('General options', 'azh'), // Title above settings section
            'azh_general_options_callback', // Name of function that renders a description of the settings section
            'azh-settings'                     // Page to show on
    );

//    add_settings_field(
//            'patterns', // Field ID
//            esc_html__('Customizer patterns', 'azh'), // Label to the left
//            'azh_textarea', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'patterns',
//        'default' => '',
//            )
//    );
//    add_settings_field(
//            'custom-icons-classes', // Field ID
//            esc_html__('Custom icons classes', 'azh'), // Label to the left
//            'azh_textarea', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'custom-icons-classes',
//        'default' => '',
//            )
//    );
//
//    add_settings_field(
//            'custom-icons-css', // Field ID
//            esc_html__('Custom icons css files', 'azh'), // Label to the left
//            'azh_textarea', // Name of function that renders options on the page
//            'azh-settings', // Page to show on
//            'azh_general_options_section', // Associate with which settings section?
//            array(
//        'id' => 'custom-icons-css',
//        'desc' => esc_html('Path relative "azh" theme/plugin folder', 'azh'),
//        'default' => '',
//            )
//    );

    global $wp_post_types;
    $post_types = wp_filter_object_list($wp_post_types, array('publicly_queryable' => true), 'and', 'label');
    $post_types = array_merge(array('page' => __('Page', 'azh')), $post_types);
    add_settings_field(
            'restrict_roles', // Field ID
            esc_html__('Post types', 'azh'), // Label to the left
            'azh_checkbox', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'post-types',
        'desc' => esc_html__('Enable AZEXO Builder for pages, posts and custom post types.', 'azh'),
        'default' => array(
            'page' => '1',
        ),
        'options' => $post_types
            )
    );

    add_settings_field(
            'container-widths', // Field ID
            esc_html__('Container class widths', 'azh'), // Label to the left
            'azh_textarea', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'container-widths',
        'desc' => esc_html__('Screen max-width : Element max-width', 'azh'),
        'default' => "768px:750px\n"
        . "992px:970px\n"
        . "1200px:1170px",
            )
    );
    add_settings_field(
            'credits', // Field ID
            esc_html__('Credits', 'azh'), // Label to the left
            'azh_checkbox', // Name of function that renders options on the page
            'azh-settings', // Page to show on
            'azh_general_options_section', // Associate with which settings section?
            array(
        'id' => 'credits',
        'desc' => esc_html__('Show AZEXO credits in footer.', 'azh'),
        'default' => array(
            'enable' => '0',
        ),
        'options' => array(
            'enable' => __('Enable', 'azh')
        )
            )
    );
    


    if (isset($_GET['page']) && $_GET['page'] == 'azh-forms-settings') {
        add_settings_section(
                'azh_general_settings', // Section ID
                esc_html__('General settings', 'azh'), // Title above settings section
                'azh_forms_settings_callback', // Name of function that renders a description of the settings section
                'azh-forms-settings'                     // Page to show on
        );
        add_settings_field(
                'form-submission-notification', // Field ID
                esc_html__('Form submission notification', 'aze'), // Label to the left
                'azh_select', // Name of function that renders options on the page
                'azh-forms-settings', // Page to show on
                'azh_general_settings', // Associate with which settings section?
                array(
            'id' => 'form-submission-notification',
            'options' => array(
                'immediately' => __('Immediately', 'aze'),
                'cron' => __('Via CRON', 'aze'),
            ),
            'default' => 'cron',
                )
        );

        $forms = azh_get_forms_from_pages();
        if (empty($forms)) {
            add_settings_section(
                    'azh_no_forms', // Section ID
                    esc_html__('You do not have pages with forms at this moment', 'azh'), // Title above settings section
                    'azh_forms_settings_callback', // Name of function that renders a description of the settings section
                    'azh-forms-settings'                     // Page to show on
            );
        } else {
            $current_user = wp_get_current_user();
            foreach ($forms as $name => $fields) {
                add_settings_section(
                        $name, // Section ID
                        strtoupper($name) . ' ' . esc_html__('form submissions email notification', 'azh'), // Title above settings section
                        'azh_forms_settings_callback', // Name of function that renders a description of the settings section
                        'azh-forms-settings'                     // Page to show on
                );
                add_settings_field(
                        $name . '-to', // Field ID
                        esc_html__('To email', 'azh'), // Label to the left
                        'azh_textfield', // Name of function that renders options on the page
                        'azh-forms-settings', // Page to show on
                        $name, // Associate with which settings section?
                        array(
                    'id' => $name . '-to',
                    'default' => $current_user->user_email,
                        )
                );
                $desc = array();
                foreach ($fields as $field_name => $field) {
                    $desc[] = '<b>{' . $field_name . '}</b> - ' . $field['label'];
                }
                $desc = esc_html__('Available form fields: ', 'azh') . implode(', ', $desc);
                add_settings_field(
                        $name . '-subject-template', // Field ID
                        esc_html__('Email subject template', 'azh'), // Label to the left
                        'azh_textfield', // Name of function that renders options on the page
                        'azh-forms-settings', // Page to show on
                        $name, // Associate with which settings section?
                        array(
                    'id' => $name . '-subject-template',
                    'desc' => $desc,
                    'default' => '',
                        )
                );
                add_settings_field(
                        $name . '-body-template', // Field ID
                        esc_html__('Email body template', 'azh'), // Label to the left
                        'azh_textarea', // Name of function that renders options on the page
                        'azh-forms-settings', // Page to show on
                        $name, // Associate with which settings section?
                        array(
                    'id' => $name . '-body-template',
                    'desc' => $desc,
                    'default' => '',
                        )
                );
            }
        }
    }
}

if (!function_exists('azh_settings_sanitize_callback')) {

    function azh_settings_sanitize_callback($input) {
        $input = apply_filters('azh_settings_sanitize_callback', $input);
        return $input;
    }

}

if (!function_exists('azh_textfield')) {

    function azh_textfield($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        if (!isset($type)) {
            $type = 'text';
        }
        ?>
        <input class="<?php print esc_attr($class); ?>" type="<?php print esc_attr($type); ?>" name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($settings[$id]); ?>" <?php print isset($step) ? 'step="' . $step . '"' : ''; ?>>
        <p>
            <em>
                <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_textarea')) {

    function azh_textarea($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <textarea name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" cols="50" rows="5"><?php print esc_attr($settings[$id]); ?></textarea>
        <p>
            <em>
                <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_checkbox')) {

    function azh_checkbox($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        foreach ($options as $value => $label) {
            ?>
            <div>
                <input id="<?php print esc_attr($id) . '-' . esc_attr($value); ?>" type="checkbox" name="<?php print $option; ?>[<?php print esc_attr($id); ?>][<?php print esc_attr($value); ?>]" value="1" <?php @checked($settings[$id][$value], 1); ?>>
                <label for="<?php print esc_attr($id) . '-' . esc_attr($value); ?>"><?php print esc_html($label); ?></label>
            </div>
            <?php
        }
        ?>
        <p>
            <em>
                <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_select')) {

    function azh_select($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <select name="<?php print $option; ?>[<?php print esc_attr($id); ?>]">
            <?php
            foreach ($options as $value => $label) {
                ?>
                <option value="<?php print esc_attr($value); ?>" <?php @selected($settings[$id], $value); ?>><?php print esc_html($label); ?></option>
                <?php
            }
            ?>
        </select>
        <p>
            <em>
                <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

if (!function_exists('azh_radio')) {

    function azh_radio($args) {
        extract($args);
        $option = 'azh-settings';
        if (isset($_GET['page'])) {
            $option = sanitize_text_field($_GET['page']);
        }
        $settings = get_option($option);
        if (isset($default) && !isset($settings[$id])) {
            $settings[$id] = $default;
        }
        ?>
        <div>
            <?php
            foreach ($options as $value => $label) {
                ?>
                <input id="<?php print esc_attr($id) . esc_attr($value); ?>" type="radio" name="<?php print $option; ?>[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($value); ?>" <?php @checked($settings[$id], $value); ?>>
                <label for="<?php print esc_attr($id) . esc_attr($value); ?>"><?php print esc_html($label); ?></label>
                <?php
            }
            ?>
        </div>
        <p>
            <em>
                <?php if (isset($desc)) print $desc; ?>
            </em>
        </p>
        <?php
    }

}

