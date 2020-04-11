<?php
add_filter('wp_editor_settings', 'azh_editor_settings', 10, 2);

function azh_editor_settings($settings, $editor_id) {
    if ($editor_id == 'customize-posts-content') {
        $settings['default_editor'] = 'html';
    }
    return $settings;
}

add_action('customize_controls_print_footer_scripts', 'azh_render_structure_and_library', 0);

function azh_render_structure_and_library() {
    global $wp_customize;
    $autofocus = $wp_customize->get_autofocus();
    if (isset($autofocus['section'])) {
        $match = array();
        if (preg_match('/post\[[\w-_]+\]\[\d+\]/', $autofocus['section'], $match)) {
            wp_enqueue_script('azh_admin_customizer', plugins_url('js/admin-customizer.js', __FILE__), array('azh_admin'), false, true);
            ?><div id="customize-azexo-html" style="display: none;"><?php
                azh_meta_box();
                ?></div><?php
            azh_editor_scripts();
        }
    }
}
