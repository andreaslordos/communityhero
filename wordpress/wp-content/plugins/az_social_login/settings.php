<?php
add_action('admin_menu', 'azsl_admin_menu');

function azsl_admin_menu() {
    add_menu_page(__('Social Login', 'azsl'), __('Social Login', 'azsl'), 'manage_options', 'azsl-settings', 'azsl_settings_page');
}

function azsl_settings_page() {
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('AZEXO Social Login Settings', 'azsl'); ?></h2>

        <form method="post" action="options.php" class="azsl-form">
            <?php
            settings_errors();
            settings_fields('azsl-settings');
            do_settings_sections('azsl-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>

    <?php
}

function azsl_general_options_callback() {
    
}

add_action('admin_init', 'azsl_general_options');

function azsl_general_options() {
    add_settings_section(
            'azsl_general_options_section', // Section ID
            __('Social Networks application IDs', 'azsl'), // Title above settings section
            'azsl_general_options_callback', // Name of function that renders a description of the settings section
            'azsl-settings'                     // Page to show on
    );
    register_setting('azsl-settings', 'azsl-settings');
    global $azsl_social_networks;
    foreach ($azsl_social_networks as $network) {        
        add_settings_field(
                $network . '_id', // Field ID
                $network, // Label to the left
                'azsl_textfield', // Name of function that renders options on the page
                'azsl-settings', // Page to show on
                'azsl_general_options_section', // Associate with which settings section?
                array(
            'id' => $network . '_id'
                )
        );
    }
}

function azsl_textfield($args) {
    extract($args);
    $options = get_option('azsl-settings');
    ?>
    <input type="text" name="azsl-settings[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($options[$id]); ?>">
    <?php
}
