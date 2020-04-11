<?php
add_action('admin_init', 'azqf_options_init');

function azqf_options_init() {
    register_setting('azqf_options', 'azqf_options');
}

add_action('admin_menu', 'azqf_options_page');

function azqf_options_page() {
    if (function_exists('cmb2_metabox_form')) {
        $options_page = add_menu_page('Query Forms', 'Query Forms', 'manage_options', 'azqf_options', 'azqf_admin_page_display');
        add_action("admin_print_styles-{$options_page}", array('CMB2_hookup', 'enqueue_cmb_css'));
    }
}

function azqf_admin_page_display() {
    wp_enqueue_style('azqf-jqueryui', (is_ssl() ? 'https' : 'http') . '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jsoneditor', plugins_url('json-editor/dist/jsoneditor.js', __FILE__));

    wp_enqueue_script('azqf-admin', plugins_url('js/azqf-admin.js', __FILE__));
    wp_enqueue_style('azqf-admin', plugins_url('css/azqf-admin.css', __FILE__));

    $post_types = get_post_types(array(), 'objects');
    $post_types_options = array();
    if (is_array($post_types) && !empty($post_types)) {
        foreach ($post_types as $slug => $post_type) {
            if ($slug !== 'revision' && $slug !== 'nav_menu_item') {
                $post_types_options[$slug] = $post_type->label;
            }
        }
    }

    $taxonomies = get_taxonomies(array(), 'objects');
    $taxonomy_options = array();
    foreach ($taxonomies as $slug => $taxonomy) {
        $taxonomy_options[$slug] = $taxonomy->label;
    }
    ?>
    <script type="text/javascript">
        window.azqf = {};
        window.azqf.post_types = <?php print json_encode($post_types_options); ?>;
        window.azqf.taxonomies = <?php print json_encode($taxonomy_options); ?>;
        window.azqf.registered_query_vars = <?php global $wp;
    print json_encode(array_merge($wp->public_query_vars, $wp->private_query_vars));
    ?>;
    </script>
    <div class="wrap cmb2-options-page <?php echo 'azqf_options'; ?>">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
        <?php cmb2_metabox_form('azqf_option_metabox', 'azqf_options'); ?>
    </div>
    <?php
}

add_action('cmb2_admin_init', 'azqf_options_page_metabox');

function azqf_options_page_metabox() {
    add_action("cmb2_save_options-page_fields_azqf_option_metabox", 'azqf_settings_notices', 10, 2);
    $cmb = new_cmb2_box(array(
        'id' => 'azqf_option_metabox',
        'hookup' => false,
        'cmb_styles' => false,
        'show_on' => array(
            'key' => 'options-page',
            'value' => array('azqf_options')
        ),
    ));
    
    $cmb->add_field(array(
        'name' => __('Google Map API key', 'azl'),
        'id' => 'gmap_api_key',
        'type' => 'text',
    ));

    $group_field_id = $cmb->add_field(array(
        'id' => 'forms',
        'type' => 'group',
        'options' => array(
            'group_title' => __('Form {#}', 'azqf'),
            'add_button' => __('Add New Form', 'azqf'),
            'remove_button' => __('Remove Form', 'azqf'),
        ),
    ));
    $cmb->add_group_field($group_field_id, array(
        'name' => __('Form name', 'azqf'),
        'id' => 'name',
        'type' => 'text',
    ));
    $cmb->add_group_field($group_field_id, array(
        'name' => 'Form settings',
        'id' => 'form',
        'type' => 'textarea_code',
        'options' => array( 'disable_codemirror' => true ),
    ));
}

function azqf_settings_notices($object_id, $updated) {
    if ($object_id !== 'azqf_options' || empty($updated)) {
        return;
    }
    add_settings_error('azqf_options-notices', '', __('Settings updated.', 'azqf'), 'updated');
    settings_errors('azqf_options-notices');
}
