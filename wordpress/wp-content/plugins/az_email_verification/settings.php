<?php
add_action('admin_menu', 'azev_admin_menu');

function azev_admin_menu() {
    add_menu_page(__('Email verification', 'azev'), __('Email verification', 'azev'), 'manage_options', 'azev-settings', 'azev_settings_page');
}

function azev_settings_page() {
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e('AZEXO Email Verification Settings', 'azev'); ?></h2>

        <form method="post" action="options.php" class="azev-form">
            <?php
            settings_errors();
            settings_fields('azev-settings');
            do_settings_sections('azev-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>

    <?php
}

function azev_general_options_callback() {
    
}

function azev_filesystem() {
    static $creds = false;

    require_once ABSPATH . '/wp-admin/includes/template.php';
    require_once ABSPATH . '/wp-admin/includes/file.php';

    if ($creds === false) {
        if (false === ( $creds = request_filesystem_credentials(admin_url()) )) {
            exit();
        }
    }

    if (!WP_Filesystem($creds)) {
        request_filesystem_credentials(admin_url(), '', true);
        exit();
    }
}

add_action('admin_init', 'azev_general_options');

function azev_general_options() {
    if (file_exists(dirname(__FILE__) . '/azev_settings.json')) {
        $settings = get_option('azev-settings');
        if (!is_array($settings)) {
            azev_filesystem();
            global $wp_filesystem;
            $settings = $wp_filesystem->get_contents(dirname(__FILE__) . '/azev_settings.json');
            update_option('azev-settings', json_decode($settings, true));
        }
    }
    add_settings_section(
            'azev_general_options_section', // Section ID
            '', // Title above settings section
            'azev_general_options_callback', // Name of function that renders a description of the settings section
            'azev-settings'                     // Page to show on
    );
    register_setting('azev-settings', 'azev-settings');

    $roles = get_editable_roles();
    $roles_options = array_combine(array_keys($roles), array_keys($roles));
    add_settings_field(
            'restrict_roles', // Field ID
            esc_html__('Skip Account Verification', 'azev'), // Label to the left
            'azev_checkbox', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'restrict_roles',
        'desc' => esc_html__('Check roles to skip account verification for all users within that role. All checked role users not need account verification. They can login without any verification.', 'azev'),
        'default' => array(
            'administrator' => '1',
        ),
        'options' => $roles_options
            )
    );
    add_settings_field(
            'registration_success', // Field ID
            esc_html__('Registration Success', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'registration_success',
        'default' => '<strong>Success:</strong> Account created successfully. Please check your email address to verify account.',
        'desc' => esc_html__('Message for successful registration.', 'azev'),
            )
    );
    add_settings_field(
            'verification_success', // Field ID
            esc_html__('Verification Success', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'verification_success',
        'default' => '<strong>Success:</strong> Your account is verified. Try login now.',
        'desc' => esc_html__('Message for successful verification.', 'azev'),
            )
    );
    add_settings_field(
            'verification_fail', // Field ID
            esc_html__('Verification Fail', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'verification_fail',
        'default' => '<strong>Error:</strong> Invalid verification credentials. Try login to get new verificaton key.',
        'desc' => esc_html__('Message for failed verification.', 'azev'),
            )
    );
    add_settings_field(
            'new_verification_link', // Field ID
            esc_html__('New Verification Link', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'new_verification_link',
        'default' => '<strong>Success:</strong> Check email for new verification link.',
        'desc' => esc_html__('Message for resend verification link.', 'azev'),
            )
    );
    add_settings_field(
            'fail_login', // Field ID
            esc_html__('Login Failed', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'fail_login',
        'default' => '<strong>ERROR:</strong> Please verify your account before login. {{resend_verification_link}}.',
        'desc' => esc_html__('Message for failed login attempt. Use this shortcode to display resend link in message: {{resend_verification_link}}', 'azev'),
            )
    );
    add_settings_field(
            'resend_link_text', // Field ID
            esc_html__('Resend Verification Link Text', 'azev'), // Label to the left
            'azev_textfield', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'resend_link_text',
        'default' => 'Resend Verification Link',
        'desc' => esc_html__('Text for resend link which is generated by shortcode {{resend_verification_link}} (see previous field).', 'azev'),
            )
    );
    add_settings_field(
            'resend_email_heading', // Field ID
            esc_html__('Resend Email Heading', 'azev'), // Label to the left
            'azev_textfield', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'resend_email_heading',
        'default' => 'New verification key',
        'desc' => esc_html__('Email heading text.', 'azev'),
            )
    );
    add_settings_field(
            'resend_email_subject', // Field ID
            esc_html__('Resend Email Subject', 'azev'), // Label to the left
            'azev_textfield', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'resend_email_subject',
        'default' => 'New verification key',
        'desc' => esc_html__('Email subject text.', 'azev'),
            )
    );
    add_settings_field(
            'resend_email_body', // Field ID
            esc_html__('Resend Verification Email Body', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'resend_email_body',
        'default' => '<p>Click below link to verify your account.</p>' . "\n" . '<p>{{verification_link}}</p>',
        'desc' => esc_html__('Email body text.
                                            	You can use below shortcodes in email body. &nbsp;
                                                {{verification_link}}  &mdash; To generate verification link (with anchor tag). &nbsp;
                                                {{verification_url}}  &mdash; To generate verification url. &nbsp;
                                                {{user_email}}  &mdash; To generate user email. &nbsp;
                                                {{user_login}}  &mdash; To generate username. &nbsp;
                                                {{user_firstname}}  &mdash; To generate user first name. &nbsp;
                                                {{user_lastname}} &mdash; To generate user last name. &nbsp;
                                                {{user_displayname}}  &mdash; To generate user display name', 'azev'),
            )
    );
    add_settings_field(
            'registration_email_body', // Field ID
            esc_html__('Registration Email Body', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'registration_email_body',
        'default' => '<p>Thanks for creating an account with us. Your username is <strong>{{user_login}}</strong>.</p>' . "\n" . '<p>Click below link to verify your account.</p>' . "\n" . '<p>{{verification_link}}</p>' . "\n" . '{{user_pass}}',
        'desc' => esc_html__('Email body text.
                                            	You can use below shortcodes in email body. &nbsp;
                                                {{verification_link}}  &mdash; To generate verification link (with anchor tag). &nbsp;
                                                {{verification_url}}  &mdash; To generate verification url. &nbsp;
                                                {{user_email}}  &mdash; To generate user email. &nbsp;
                                                {{user_login}}  &mdash; To generate username. &nbsp;
                                                {{user_firstname}}  &mdash; To generate user first name. &nbsp;
                                                {{user_lastname}} &mdash; To generate user last name. &nbsp;
                                                {{user_displayname}}  &mdash; To generate user display name', 'azev'),
            )
    );
    add_settings_field(
            'password_message', // Field ID
            esc_html__('Password Text (Password will append to this text).', 'azev'), // Label to the left
            'azev_textarea', // Name of function that renders options on the page
            'azev-settings', // Page to show on
            'azev_general_options_section', // Associate with which settings section?
            array(
        'id' => 'password_message',
        'default' => 'Your password has been automatically generated:',
        'desc' => esc_html__('Password will be append to this text. This will only work if you have used {{user_pass}} in previous email body and password has been automatically generated by WooCommerce.', 'azev'),
            )
    );
}

function azev_checkbox($args) {
    extract($args);
    $settings = get_option('azev-settings');
    if (isset($default) && !isset($settings[$id])) {
        $settings[$id] = $default;
    }
    foreach ($options as $value => $label) {
        ?>
        <div>
            <input id="<?php print esc_attr($id) . esc_attr($value); ?>" type="checkbox" name="azev-settings[<?php print esc_attr($id); ?>][<?php print esc_attr($value); ?>]" value="1" <?php @checked($settings[$id][$value], 1); ?>>
            <label for="<?php print esc_attr($id) . esc_attr($value); ?>"><?php print esc_html($label); ?></label>
        </div>
        <?php
    }
    ?>
    <p><em>
            <?php print esc_html($desc); ?>
        </em></p>
    <?php
}

function azev_textfield($args) {
    extract($args);
    $settings = get_option('azev-settings');
    if (isset($default) && !isset($settings[$id])) {
        $settings[$id] = $default;
    }
    ?>
    <input type="text" name="azev-settings[<?php print esc_attr($id); ?>]" value="<?php print esc_attr($settings[$id]); ?>">
    <p><em>
            <?php print esc_html($desc); ?>
        </em></p>
    <?php
}

function azev_textarea($args) {
    extract($args);
    $settings = get_option('azev-settings');
    if (isset($default) && !isset($settings[$id])) {
        $settings[$id] = $default;
    }
    ?>
    <textarea name="azev-settings[<?php print esc_attr($id); ?>]" cols="50" rows="5"><?php print esc_attr($settings[$id]); ?></textarea>
    <p><em>
            <?php print esc_html($desc); ?>
        </em></p>
    <?php
}
