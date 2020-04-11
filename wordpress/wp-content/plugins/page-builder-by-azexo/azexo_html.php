<?php
/*
  Plugin Name: Page Builder by AZEXO
  Description: Front-end page builder
  Author: azexo
  Author URI: http://azexo.com
  Version: 1.27.123
  Text Domain: azh
 */

define('AZH_VERSION', '1.27');
define('AZH_PLUGIN_VERSION', '1.27.123');
define('AZH_URL', plugins_url('', __FILE__));
define('AZH_DIR', trailingslashit(dirname(__FILE__)));
define('AZH_PLUGIN_NAME', trailingslashit(basename(dirname(__FILE__))) . 'azexo_html.php');

//ini_get('mbstring.func_overload') !==false && ini_get('mbstring.func_overload') > 0

function azh_linux_path($path) {
    return str_replace('\\', '/', $path);
}

global $azh_shortcodes;
$azh_shortcodes = array();

include_once(AZH_DIR . 'icons.php' );
include_once(AZH_DIR . 'auto_templates.php' );
include_once(AZH_DIR . 'widgets.php' );
include_once(AZH_DIR . 'integrations/gutenberg.php' );
include_once(AZH_DIR . 'integrations/elementor.php' );
include_once(AZH_DIR . 'integrations/wpbakery.php' );
include_once(AZH_DIR . 'integrations/divi.php' );
//include_once(AZH_DIR . 'integrations/siteorigin.php' );
//include_once(AZH_DIR . 'integrations/beaver.php' );
include_once(AZH_DIR . 'integrations/misc.php' );
if (file_exists(AZH_DIR . 'integrations/woocommerce.php')) {
    include_once(AZH_DIR . 'integrations/woocommerce.php' );
}
include_once(AZH_DIR . 'google-fonts.php' );
if (is_admin()) {
    include_once(AZH_DIR . 'admin.php' );
    if (file_exists(AZH_DIR . 'envato/updater.php')) {
        include_once(AZH_DIR . 'envato/updater.php' );
    }
    include_once(AZH_DIR . 'settings.php' );
    include_once(AZH_DIR . 'customizer.php' );
    if (file_exists(AZH_DIR . 'html_template_export.php')) {
        include_once(AZH_DIR . 'html_template_export.php' );
    }
    include_once(AZH_DIR . 'options-translation.php');
}
include_once(AZH_DIR . 'templates.php' );

add_action('plugins_loaded', 'azh_plugins_loaded');

function azh_plugins_loaded() {
    load_plugin_textdomain('azh', FALSE, basename(dirname(__FILE__)) . '/languages/');
    add_action('add_meta_boxes', 'azh_add_meta_boxes', 10, 2);
    $settings = get_option('azh-settings');
    $icon_types = array('fontawesome', 'openiconic', 'typicons', 'entypo', 'linecons', 'themify');
    global $azh_icons, $azh_icons_index;
    $azh_icons = array();
    $azh_icons_index = array();
    foreach ($icon_types as $icon_type) {
        $azh_icons[$icon_type] = array();
        $arr1 = apply_filters('azh_icon-type-' . $icon_type, array());
        foreach ($arr1 as $arr2) {
            if (is_array($arr2)) {
                if (count($arr2) == 1) {
                    reset($arr2);
                    $azh_icons[$icon_type][key($arr2)] = current($arr2);
                    $azh_icons_index[key($arr2)] = $icon_type;
                } else {
                    foreach ($arr2 as $arr3) {
                        if (count($arr3) == 1) {
                            reset($arr3);
                            $azh_icons[$icon_type][key($arr3)] = current($arr3);
                            $azh_icons_index[key($arr3)] = $icon_type;
                        }
                    }
                }
            }
        }
    }
    if (isset($settings['custom-icons-classes'])) {
        $custom_icons = explode("\n", trim($settings['custom-icons-classes']));
        if (count($custom_icons) <= 1) {
            $custom_icons = explode(" ", trim($settings['custom-icons-classes']));
        }
        $azh_icons['custom'] = array_combine($custom_icons, $custom_icons);
        $azh_icons_index = array_merge($azh_icons_index, array_combine($custom_icons, array_fill(0, count($custom_icons), 'custom')));
    }


    $azh_widgets = get_option('azh_widgets');
    if (is_array($azh_widgets) || empty($azh_widgets)) {
        update_post_cache($azh_widgets);
    } else {
        $azh_widgets = get_posts(array(
            'post_type' => 'azh_widget',
            'posts_per_page' => '-1',
        ));
        update_option('azh_widgets', $azh_widgets);
        update_post_cache($azh_widgets);
    }
}

register_activation_hook(__FILE__, 'azh_activate');

function azh_activate() {
    update_option('azh-library', array());
    update_option('azh-elements-library-shortcode', array());
    update_option('azh-sections-library-shortcode', array());
    update_option('azh-all-settings', array());
    update_option('azh-get-content-scripts', array());
    update_option('azh-content-settings', array());
}

add_filter('azh_directory', 'azh_directory');

function azh_directory($dir) {
    $dir[untrailingslashit(dirname(__FILE__)) . '/azh'] = plugins_url('', __FILE__) . '/azh';
    return $dir;
}

add_action('edit_form_after_title', 'azh_print_switch_mode_button', 10, 1);

function azh_print_switch_mode_button($post) {
    $settings = get_option('azh-settings');
    if (is_object($post) && isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types']))) {
        wp_nonce_field(basename(__FILE__), '_azh_edit_mode_nonce');
        ?>
        <div id="azh-switch-mode">
            <input id="azh-switch-mode-input" type="hidden" name="_azh_edit_mode" value="<?php echo get_post_meta($post->ID, 'azh', true); ?>" />
            <button id="azh-switch-mode-button" class="azh-button button button-primary button-hero">
                <span class="azh-switch-mode-on">
                    <?php _e('&#8592; Back to WordPress Editor', 'azh'); ?>
                </span>
                <span class="azh-switch-mode-off">
                    <span class="dashicons dashicons-schedule"></span>
                    <?php _e('Edit with AZEXO', 'azh'); ?>
                </span>
            </button>
        </div>
        <div id="azh-editor">
            <a id="azh-go-to-edit-page-link" href="<?php echo add_query_arg('azh', 'customize', get_edit_post_link($post)); ?>">
                <div id="azh-editor-button" class="azh-button button button-primary button-hero">
                    <span class="dashicons dashicons-schedule"></span>
                    <?php _e('Edit with AZEXO', 'azh'); ?>
                </div>
            </a>
        </div>
        <?php
    }
}

add_action('save_post', 'azh_save_post', 10, 3);

function azh_save_post($post_id, $post, $update) {
    if (!isset($_POST['_azh_edit_mode_nonce']) || !wp_verify_nonce($_POST['_azh_edit_mode_nonce'], basename(__FILE__))) {
        return;
    }
//    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
//        return;
//    }
    if (!empty($_POST['_azh_edit_mode'])) {
        update_post_meta($post_id, 'azh', 'azh');
    } else {
        delete_post_meta($post_id, 'azh');
    }

    if ($post->post_type == 'azh_widget') {
        $azh_widgets = get_option('azh_widgets');
        if (!is_array($azh_widgets)) {
            $azh_widgets = array();
        }
        $azh_widgets[$post_id] = $post;
        update_option('azh_widgets', $azh_widgets);
        update_post_cache($azh_widgets);
    }
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types']))) {
        $settings = get_option('azh-forms-settings', array());
        $current_user = wp_get_current_user();
        $forms = azh_get_forms_from_page($post);
        foreach ($forms as $form_title => $form) {
            $fields = '';
            foreach ($form as $name => $field) {
                $fields .= $field['label'] . ': {' . $name . "}\n";
            }
            if (!isset($settings[$form_title . '-to'])) {
                $settings[$form_title . '-to'] = $current_user->user_email;
            }
            if (!isset($settings[$form_title . '-subject-template'])) {
                $settings[$form_title . '-subject-template'] = esc_html__('{form_title} submission', 'azh');
            }
            if (!isset($settings[$form_title . '-body-template'])) {
                $settings[$form_title . '-body-template'] = $fields;
            }
        }
        update_option('azh-forms-settings', $settings);
    }
}

function azh_get_frontend_object() {
    $azh = array(
        'device_prefixes' => azh_device_prefixes(),
        'site_url' => site_url(),
        'ajaxurl' => admin_url('admin-ajax.php'),
        'post_id' => get_the_ID(),
        'i18n' => array()
    );
    return apply_filters('azh_get_frontend_object', $azh);
}

function azh_get_shapes() {
    azh_filesystem();
    global $wp_filesystem;
    $shapes = array();
    $iterator = new DirectoryIterator(AZH_DIR . 'shapes');
    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && $fileInfo->getExtension() == 'svg') {
            $shapes[$fileInfo->getFilename()] = $wp_filesystem->get_contents($fileInfo->getPathname());
        }
    }
    return $shapes;
}

function azh_get_object() {
    static $azh;
    if (empty($azh)) {
        $empty_html = esc_html__('Please switch to HTML and input content', 'azh');
        global $azh_shortcodes, $azh_google_fonts, $azh_google_fonts_locale_subsets;
        $settings = get_option('azh-settings');
        $patterns = isset($settings['patterns']) ? $settings['patterns'] : '';
        $patterns = preg_replace("/\r\n/", "\n", $patterns);
        $properties = explode("\n\n", $patterns);
        $patterns = array();
        foreach ($properties as $property) {
            $property = explode("\n", $property);
            $patterns[$property[0]] = array();
            if ($property[0] == 'dropdown_patterns') {
                for ($i = 1; $i < count($property); $i = $i + 2) {
                    $options = array();
                    $options_value_label = explode("|", $property[$i + 1]);
                    foreach ($options_value_label as $value_label) {
                        $vl = explode(":", $value_label);
                        $options[$vl[0]] = $vl[1];
                    }
                    $patterns[$property[0]][] = array(
                        'pattern' => $property[$i],
                        'options' => $options,
                    );
                }
            } else {
                for ($i = 1; $i < count($property); $i++) {
                    $patterns[$property[0]][] = $property[$i];
                }
            }
        }
        $edit_post_frontend_link = add_query_arg(array('azh' => 'customize', 'azhp' => get_the_ID()), get_permalink());
        if (isset($_GET['azhf']) && is_numeric($_GET['azhf'])) {
            $edit_post_frontend_link = add_query_arg(array('azh' => 'customize', 'azhp' => get_the_ID()), get_permalink((int) $_GET['azhf']));
        }
        $post_settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        //$settings = array_replace_recursive($settings, $post_settings);
        $all_settings = azh_get_all_settings();
        $library = azh_get_library();
        $html = azh_get_shapes();
        $azh = array(
            'options' => apply_filters('azh_options', $patterns),
            'dirs_options' => $all_settings,
            'files_settings' => azh_get_files_settings($all_settings),
            'files_scripts' => azh_get_files_scripts($library),
            'icons' => apply_filters('azh_icons', array()),
            'shortcodes' => $azh_shortcodes,
            'shortcode_instances' => isset($post_settings['shortcodes']) ? $post_settings['shortcodes'] : array(),
            'html' => $html,
            'site_url' => site_url(),
            'plugin_url' => AZH_URL,
            'ajaxurl' => admin_url('admin-ajax.php'),
            'helpers' => array(
                '.azh-wrapper.azh-inline' => esc_html__('<strong>Right mouse click without text selection</strong> - switch between text and link<br><strong>Right mouse click with text selection</strong> - split text-field to 3 text-fields<br><strong>Drag-and-drop text-field</strong> - merge 2 adjacent text-fields', 'azh'),
            ),
            'user_logged_in' => is_user_logged_in(),
            'post_id' => get_the_ID(),
            'edit_post_link' => is_admin() ? '' : get_edit_post_link(),
            'edit_post_frontend_link' => $edit_post_frontend_link,
            'default_category' => apply_filters('azh_default_category', get_template()),
            'google_fonts' => $azh_google_fonts,
            'google_fonts_locale_subsets' => $azh_google_fonts_locale_subsets,
            'locale' => get_locale(),
            'fill_utility_on_init' => array(),
            'cloneable_refresh' => array(
                '.az-cloneable-refresh',
            ),
            'cloneable_refresh_children' => array(
                '.g-recaptcha',
                '.az-gmap',
                '.az-cloneable-refresh',
            ),
            'device_prefixes' => azh_device_prefixes(),
            'device_prefix' => 'lg',
            'responsive' => true,
            'editor_toolbar' => array('boldButton', 'italicButton', 'linkButton', 'leftButton', 'centerButton', 'rightButton', 'sizeSelector', 'colorInput', 'colorBgInput', 'removeFormatButton'),
            'table_editor' => true,
            'recognition' => false,
            'categories_order' => array('layout', 'content', 'forms', 'free-positioning', 'WordPress', 'WooCommerce', 'AZEXO', 'elements'),
            'i18n' => array(
                'select_element' => esc_html__('Select Elements', 'azh'),
                'paste_from_clipboard' => esc_html__('Paste from clipboard', 'azh'),
                'ok' => esc_html__('OK', 'azh'),
                'cancel' => esc_html__('Cancel', 'azh'),
                'edit_link' => esc_html__('Edit link', 'azh'),
                'edit_image' => esc_html__('Edit image', 'azh'),
                'edit_icon' => esc_html__('Edit icon', 'azh'),
                'label' => esc_html__('Label', 'azh'),
                'value' => esc_html__('Value', 'azh'),
                'clone' => esc_html__('Clone', 'azh'),
                'remove' => esc_html__('Remove', 'azh'),
                'move' => esc_html__('Move', 'azh'),
                'default' => esc_html__('Default', 'azh'),
                'section' => esc_html__('Section', 'azh'),
                'element' => esc_html__('Element', 'azh'),
                'elements' => esc_html__('Elements', 'azh'),
                'general' => esc_html__('General', 'azh'),
                'content' => esc_html__('Content', 'azh'),
                'layout' => esc_html__('Layout', 'azh'),
                'forms' => esc_html__('Forms', 'azh'),
                'free-positioning' => esc_html__('Free positioning', 'azh'),
                'add_element' => esc_html__('Add element', 'azh'),
                'move_element' => esc_html__('Relative shift of element (click to reset shift)', 'azh'),
                'add_element_after' => esc_html__('Add element after', 'azh'),
                'add_element_before' => esc_html__('Add element before', 'azh'),
                'remove_element' => esc_html__('Remove element', 'azh'),
                'edit_tags' => esc_html__('Edit tags', 'azh'),
                'saved' => esc_html__('Saved', 'azh'),
                'select_image' => esc_html__('Select image', 'azh'),
                'click_to_edit_shortcode' => esc_html__('Click to edit shortcode', 'azh'),
                'shortcode_edit' => esc_html__('Shortcode edit', 'azh'),
                'options' => esc_html__('Options', 'azh'),
                'select_options_edit' => esc_html__('Select options edit', 'azh'),
                'value_must_be_unique_in_this_scope' => esc_html__('Value must be unique in this scope', 'azh'),
                'insert_column_before' => esc_html__('Insert column before', 'azh'),
                'insert_column_after' => esc_html__('Insert column after', 'azh'),
                'insert_row_before' => esc_html__('Insert row before', 'azh'),
                'insert_row_after' => esc_html__('Insert row after', 'azh'),
                'delete_row' => esc_html__('Delete row', 'azh'),
                'delete_column' => esc_html__('Delete column', 'azh'),
                'elements_hierarchy' => esc_html__('Elements hierarchy', 'azh'),
                //backend builder
                'edit_frontend_builder' => esc_html__('Edit with AZEXO', 'azh'),
                'empty_html' => $empty_html,
                'enter_text_here' => esc_html__('enter text here', 'azh'),
                'upload_text' => esc_html__('Upload', 'azh'),
                'edit_text' => esc_html__('Edit', 'azh'),
                'clear' => esc_html__('Clear', 'azh'),
                'collapse' => esc_html__('Collapse', 'azh'),
                'expand' => esc_html__('Expand', 'azh'),
                'clone' => esc_html__('Clone', 'azh'),
                'copy' => esc_html__('Copy', 'azh'),
                'copied' => esc_html__('Copied', 'azh'),
                'paste' => esc_html__('Paste', 'azh'),
                'move' => esc_html__('Move', 'azh'),
                'done' => esc_html__('Done', 'azh'),
                'add' => esc_html__('Add', 'azh'),
                'remove' => esc_html__('Remove', 'azh'),
                'set' => esc_html__('Set', 'azh'),
                'title' => esc_html__('Title', 'azh'),
                'url' => esc_html__('URL', 'azh'),
                'selected' => esc_html__('Selected', 'azh'),
                'required' => esc_html__('Required', 'azh'),
                'checked' => esc_html__('Checked', 'azh'),
                'device' => esc_html__('Device', 'azh'),
                'large' => esc_html__('Large', 'azh'),
                'medium' => esc_html__('Medium', 'azh'),
                'small' => esc_html__('Small', 'azh'),
                'preview' => esc_html__('Preview', 'azh'),
                'customize' => esc_html__('Customize', 'azh'),
                //'elements' => esc_html__('elements', 'azh'),
                'sections' => esc_html__('sections', 'azh'),
                'extra_small' => esc_html__('Extra small', 'azh'),
                'column_width' => esc_html__('Column width', 'azh'),
                'column_offset' => esc_html__('Column offset', 'azh'),
                'column_responsive' => esc_html__('Column responsive settings', 'azh'),
                'select_url' => esc_html__('Select URL', 'azh'),
                'switch_to_html' => esc_html__('Switch to html', 'azh'),
                'switch_to_customizer' => esc_html__('Switch to customizer', 'azh'),
                'control_description' => esc_html__('Control description', 'azh'),
                'description' => esc_html__('Description', 'azh'),
                'filter_by_tag' => esc_html__('Filter by tag', 'azh'),
                'paste_sections_list_here' => esc_html__('Paste sections list here', 'azh'),
                'content_wrapper_does_not_exists' => esc_html__('Sorry, the content area was not found in your page. You must call the_content function in the current template, in order for AZEXO builder to work on this page.', 'azh'),
                'element_settings' => esc_html__('Element settings', 'azh'),
                'column_settings' => esc_html__('Column settings', 'azh'),
                'section_settings' => esc_html__('Section settings', 'azh'),
                'revision_does_not_have_builder_content' => esc_html__('Revision does not have builder content', 'azh'),
                'revision_has_been_restored' => esc_html__('Revision has been restored', 'azh'),
                'shortcode' => esc_html__('Shortcode', 'azh'),
                'edit_item_tags' => esc_html__('Edit item tags', 'azh'),
                'tags' => esc_html__('Tags', 'azh'),
                'change_the_tags_of_this_item' => esc_html__('Change the tags of this item (separated by comma)', 'azh'),
                'edit_section_html' => esc_html__('Edit section HTML', 'azh'),
                'html_is_not_valid' => esc_html__('HTML is not valid.', 'azh'),
                'source_code' => esc_html__('Source code', 'azh'),
            ),
        );
        $azh = apply_filters('azh_get_object', $azh);
    }
    return $azh;
}

function azh_editor_scripts() {
    wp_enqueue_script('simplemodal', plugins_url('js/jquery.simplemodal.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_style('azh_admin', plugins_url('css/admin.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_style('select2', plugins_url('css/select2.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('select2', plugins_url('js/select2.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('azh_admin', plugins_url('js/admin.js', __FILE__), array('azexo_html'), AZH_PLUGIN_VERSION, true);

    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_style('azexo_html', plugins_url('css/azexo_html.css', __FILE__), array(), AZH_PLUGIN_VERSION);
    wp_enqueue_script('azexo_html', plugins_url('js/azexo_html.js', __FILE__), array('underscore', 'azh_htmlparser', 'jquery-ui-sortable', 'jquery-ui-autocomplete'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_style('jquery-ui', plugins_url('css/jquery-ui.css', __FILE__), false, AZH_PLUGIN_VERSION, false);
    wp_enqueue_script('azh_htmlparser', plugins_url('js/htmlparser.js', __FILE__), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('ace', plugins_url('js/ace/ace.js', __FILE__), AZH_PLUGIN_VERSION, true);

    wp_localize_script('azexo_html', 'azh', azh_get_object());
}

function azh_add_meta_boxes($post_type, $post) {
    $settings = get_option('azh-settings');
    if (!isset($_GET['azh'])) {
        $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
        if (is_array($dirs)) {
            foreach ($dirs as $dir => $uri) {
                if (is_dir($dir) && isset($settings['post-types']) && in_array($post_type, apply_filters('azh_meta_box_post_types', array_keys($settings['post-types'])))) {
                    add_meta_box('azh', __('AZEXO Builder', 'azh'), 'azh_meta_box', $post_type, 'side', 'default');
                    break;
                }
            }
        }
    }
    if (in_array($post_type, apply_filters('azh_meta_box_post_types', array_keys($settings['post-types'])))) {
        if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
            wp_enqueue_script('azh_admin_frontend', plugins_url('js/admin-frontend.js', __FILE__), array('jquery', 'underscore', 'azh_htmlparser', 'jquery-ui-mouse', 'jquery-ui-sortable', 'jquery-ui-autocomplete'), AZH_PLUGIN_VERSION, true);
            $azh = azh_get_object();
            wp_localize_script('azh_admin_frontend', 'azh', $azh);
            wp_enqueue_style('azh_admin', plugins_url('css/admin.css', __FILE__), false, AZH_PLUGIN_VERSION);
            wp_enqueue_style('jquery-ui', plugins_url('css/jquery-ui.css', __FILE__), false, AZH_PLUGIN_VERSION);
            azh_builder_scripts();
        } else {
            azh_editor_scripts();
        }
        do_action('azh_load', $post_type, $post);
    }
}

add_filter('page_row_actions', 'azh_page_row_actions', 10, 2);

function azh_page_row_actions($actions, $post) {
    if (get_post_meta($post->ID, 'azh', true)) {
        $settings = get_option('azh-settings');
        if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types']))) {
            $actions['azh'] = '<a href="' . add_query_arg('azh', 'customize', get_edit_post_link($post)) . '" title="' . esc_attr__('Edit with AZEXO', 'azh') . '">' . esc_html__('Edit with AZEXO', 'azh') . '</a>';
        }
    }
    return $actions;
}

add_filter('display_post_states', 'azh_display_post_states', 10, 2);

function azh_display_post_states($post_states, $post) {
    if (get_post_meta($post->ID, 'azh', true)) {
        $settings = get_option('azh-settings');
        if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types']))) {
            $post_states['azh'] = __('AZEXO', 'azh');
        }
    }
    return $post_states;
}

function azh_get_library() {
    static $library = array();
    if (!empty($library)) {
        return $library;
    }
    $library = get_option('azh-library', array());
    $user = wp_get_current_user();
    if (in_array('administrator', (array) $user->roles) && WP_DEBUG || empty($library)) {
        $general_elements = array();
        $elements = array();
        $elements_dir = array();
        $elements_uri = array();
        $elements_categories = array();
        $general_sections = array();
        $sections = array();
        $sections_dir = array();
        $sections_uri = array();
        $sections_categories = array();
        $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
        if (is_array($dirs)) {
            foreach ($dirs as $dir => $uri) {
                if (is_dir($dir)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $fileInfo) {
                        if ($fileInfo->isFile() && $fileInfo->getExtension() == 'html') {
                            if (isset($_GET['azh']) && $_GET['azh'] == 'customize' && strpos($fileInfo->getPathname(), 'pre-made') !== false) {
                                continue;
                            }
                            $sections[azh_linux_path($fileInfo->getPathname())] = $fileInfo->getFilename();
                            $sections_dir[azh_linux_path($fileInfo->getPathname())] = azh_linux_path($dir);
                            $sections_uri[azh_linux_path($fileInfo->getPathname())] = $uri;
                            $sections_categories[trim(str_replace(azh_linux_path($dir), '', azh_linux_path($fileInfo->getPath())), '/')] = true;
                            if (in_array(strtolower(basename(dirname($fileInfo->getPathname()))), array('empty rows'))) {
                                $general_sections[azh_linux_path($fileInfo->getPathname())] = $fileInfo->getFilename();
                            }
                        }
                        if ($fileInfo->isFile() && $fileInfo->getExtension() == 'htm') {
                            $elements[azh_linux_path($fileInfo->getPathname())] = $fileInfo->getFilename();
                            $elements_dir[azh_linux_path($fileInfo->getPathname())] = azh_linux_path($dir);
                            $elements_uri[azh_linux_path($fileInfo->getPathname())] = $uri;
                            $elements_categories[trim(str_replace(azh_linux_path($dir), '', azh_linux_path($fileInfo->getPath())), '/')] = true;
                            if (in_array(strtolower(basename(dirname($fileInfo->getPathname()))), array('empty rows', 'general'))) {
                                $general_elements[azh_linux_path($fileInfo->getPathname())] = $fileInfo->getFilename();
                            }
                        }
                    }
                }
            }
        }
        ksort($elements);
        ksort($elements_categories);
        ksort($sections);
        ksort($sections_categories);
        $library = apply_filters('azh_get_library', array(
            'general_elements' => $general_elements, //used for adding "general" class
            'elements' => $elements, //used for adding element name
            'elements_dir' => $elements_dir, //used for AZH folder relativity
            'elements_uri' => $elements_uri, //used for make URL relative to AZH folder
            'elements_categories' => $elements_categories, //used for generation category filter - based on substring of element path
            'general_sections' => $general_sections,
            'sections' => $sections,
            'sections_dir' => $sections_dir,
            'sections_uri' => $sections_uri,
            'sections_categories' => $sections_categories,
        ));
        update_option('azh-library', $library);
    }
    return $library;
}

function azh_get_files_settings($all_settings) {
    $files_settings = array();
    foreach ($all_settings as $dir => $settings) {
        if (isset($settings['files_settings'])) {
            foreach ($settings['files_settings'] as $file => $file_settings) {
                if (!isset($files_settings[$file])) {
                    $files_settings[$file] = array();
                }
                $files_settings[$file] = array_replace_recursive($files_settings[$file], $file_settings);
            }
        }
    }
    return $files_settings;
}

function azh_get_files_scripts($library) {
    static $files_scripts = array();
    if (!empty($files_scripts)) {
        return $files_scripts;
    }
    $files_scripts = apply_filters('azh_get_files_scripts', $files_scripts, $library);
    return $files_scripts;
}

function azh_get_files_order($all_settings) {
    $files_order = array();
    foreach ($all_settings as $dir => $settings) {
        if (isset($settings['files_settings'])) {
            foreach ($settings['files_settings'] as $file => $file_settings) {
                if (isset($file_settings['order'])) {
                    $files_order[$dir . '/' . $file] = $file_settings['order'];
                }
            }
        }
    }
    return $files_order;
}

function azh_meta_box($post = NULL, $metabox = NULL, $post_type = 'page') {
    if (!is_null($post)) {
        $post_type = get_post_type($post);
    }
    $library = azh_get_library();
    $all_settings = azh_get_all_settings();
    $files_order = azh_get_files_order($all_settings);
    extract($library);
    ?>
    <?php //if ($post_type != 'azh_widget'):                                                                                                                                 ?>
    <div class="azh-actions" style="display: none;">
        <a href="#" class="azh-copy-sections-list"><?php esc_html_e('Copy sections', 'azh') ?></a>
        <a href="#" class="azh-insert-sections-list"><?php esc_html_e('Insert sections', 'azh') ?></a>
    </div>
    <div class="azh-structure" style="max-height: 600px;"></div>
    <div class="azh-section-operations">
        <a href="#" class="azh-add-section" data-open="<?php esc_html_e('Add section', 'azh') ?>" data-close="<?php esc_html_e('Close library', 'azh') ?>"><?php esc_html_e('Add section', 'azh') ?></a>    
        <a href="#" class="azh-add-section" data-category="empty rows" data-open="<?php esc_html_e('Add empty section', 'azh') ?>" data-close="<?php esc_html_e('Close library', 'azh') ?>"><?php esc_html_e('Add empty section', 'azh') ?></a>    
    </div>
    <?php //endif;          ?>
    <div class="azh-library" style="display: none;">
        <?php //if ($post_type != 'azh_widget'):         ?>
        <div class="azh-library-filters">
            <select class="azh-categories">
                <option value=""><?php esc_html_e('Filter by category', 'azh') ?></option>
                <?php
                foreach ($sections_categories as $category => $flag) {
                    ?>
                    <option value="<?php print esc_attr($category) ?>"><?php print esc_html($category) ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="azh-sections">
            <?php
            foreach ($sections as $path => $name) {
                $preview = '';
                if (file_exists(str_replace('.html', '.jpg', $path))) {
                    $preview = str_replace('.html', '.jpg', $path);
                }
                if (file_exists(str_replace('.html', '.png', $path))) {
                    $preview = str_replace('.html', '.png', $path);
                }
                $dir = $sections_dir[$path];
                $url = str_replace($dir, $sections_uri[$path], $path);
                if (file_exists($preview)) {
                    $preview = str_replace($dir, $sections_uri[$path], $preview);
                    ?><div class="azh-section azh-fuzzy <?php print isset($general_sections[$path]) ? 'general' : ''; ?>" data-url="<?php print esc_attr($url); ?>" data-order="<?php print isset($files_order[$path]) ? $files_order[$path] : 999999  ?>" data-path="<?php print esc_attr(ltrim(str_replace($dir, '', $path), '/')) ?>" data-dir="<?php print esc_attr($dir) ?>" data-dir-uri="<?php print esc_attr($sections_uri[$path]) ?>"  style="background-image: url('<?php print esc_attr($preview); ?>');"><div class="azh-name"><?php print esc_html($name) ?></div><img src="<?php print esc_attr($preview); ?>"></div><?php
                } else {
                    ?><div class="azh-section no-image <?php print isset($general_sections[$path]) ? 'general' : ''; ?>" data-url="<?php print esc_attr($url); ?>" data-order="<?php print isset($files_order[$path]) ? $files_order[$path] : 999999  ?>" data-path="<?php print esc_attr(ltrim(str_replace($dir, '', $path), '/')) ?>" data-dir="<?php print esc_attr($dir) ?>" data-dir-uri="<?php print esc_attr($sections_uri[$path]) ?>"><div class="azh-name"><?php print esc_html($name) ?></div><img src="<?php print esc_attr($preview); ?>"></div><?php
                    }
                }
                ?>        
        </div>
        <?php //endif;        ?>
        <div class="azh-elements" style="display: none;">  
            <div class="azh-elements-filters">
                <select class="azh-categories">
                    <option value="" selected=""><?php esc_html_e('Filter by category', 'azh') ?></option>
                    <?php
                    foreach ($elements_categories as $category => $flag) {
                        ?>
                        <option value="<?php print esc_attr($category) ?>"><?php print esc_html($category) ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
            foreach ($elements as $path => $name) {
                $preview = '';
                if (file_exists(str_replace('.htm', '.jpg', $path))) {
                    $preview = str_replace('.htm', '.jpg', $path);
                }
                if (file_exists(str_replace('.htm', '.png', $path))) {
                    $preview = str_replace('.htm', '.png', $path);
                }
                if (file_exists(str_replace('.htm', '.svg', $path))) {
                    $preview = str_replace('.htm', '.svg', $path);
                }
                $dir = $elements_dir[$path];
                $url = str_replace($dir, $elements_uri[$path], $path);
                if (file_exists($preview)) {
                    $preview = str_replace($dir, $elements_uri[$path], $preview);
                    ?><div class="azh-element <?php print isset($general_elements[$path]) ? 'general' : ''; ?>" data-url="<?php print esc_attr($url); ?>" data-order="<?php print isset($files_order[$path]) ? $files_order[$path] : 999999  ?>" data-path="<?php print esc_attr(ltrim(str_replace($dir, '', $path), '/')) ?>" data-dir="<?php print esc_attr($dir) ?>" data-dir-uri="<?php print esc_attr($elements_uri[$path]) ?>" style="background-image: url('<?php print esc_attr($preview); ?>');"><div class="azh-name"><?php print esc_html($name) ?></div></div><?php
                } else {
                    ?><div class="azh-element no-image  <?php print isset($general_elements[$path]) ? 'general' : ''; ?>" data-url="<?php print esc_attr($url); ?>" data-order="<?php print isset($files_order[$path]) ? $files_order[$path] : 999999  ?>" data-path="<?php print esc_attr(ltrim(str_replace($dir, '', $path), '/')) ?>" data-dir="<?php print esc_attr($dir) ?>" data-dir-uri="<?php print esc_attr($elements_uri[$path]) ?>"><div class="azh-name"><?php print esc_html($name) ?></div></div><?php
                    }
                }
                ?>        
        </div>
    </div>
    <?php
}

add_filter('admin_body_class', 'azh_admin_body_class');

function azh_admin_body_class($classes) {
    global $pagenow;
    $settings = get_option('azh-settings');
    if (in_array($pagenow, array('post.php', 'post-new.php')) && isset($settings['post-types']) && in_array(get_post_type(), array_keys($settings['post-types']))) {
        $post = get_post();

        $mode_class = get_post_meta($post->ID, 'azh', true) ? 'azh-editor-active' : '';

        $classes .= ' ' . $mode_class;
    }
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        $classes .= ' azh-customize';
    }

    return $classes;
}

add_filter('body_class', 'azh_body_class');

function azh_body_class($classes) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        $classes[] = 'azh-customize';
    }
    return $classes;
}

function azh_parse_revision($revision) {
    static $authors;
    $current_time = current_time('timestamp');

    $date = date_i18n(_x('M j @ H:i', 'revision date format', 'azh'), strtotime($revision->post_modified));

    $human_time = human_time_diff(strtotime($revision->post_modified), $current_time);

    if (false !== strpos($revision->post_name, 'autosave')) {
        $type = 'autosave';
    } else {
        $type = 'revision';
    }

    if (!isset($authors[$revision->post_author])) {
        $authors[$revision->post_author] = array(
            'avatar' => get_avatar($revision->post_author, 22),
            'display_name' => get_the_author_meta('display_name', $revision->post_author),
        );
    }
    return array(
        'id' => $revision->ID,
        'author' => $authors[$revision->post_author]['display_name'],
        'date' => sprintf(__('%1$s ago (%2$s)', 'azh'), $human_time, $date),
        'type' => $type,
        'gravatar' => $authors[$revision->post_author]['avatar'],
    );
}

function azh_get_revisions($post_id = 0, $query_args = array()) {
    $post = get_post($post_id);

    if (!$post || empty($post->ID)) {
        return array();
    }

    $revisions = array();

    $default_query_args = array(
        'posts_per_page' => 100,
        'meta_key' => '_azh_content',
    );

    $query_args = array_merge($default_query_args, $query_args);

    $posts = wp_get_post_revisions($post->ID, $query_args);

    foreach ($posts as $revision) {
        $revisions[] = azh_parse_revision($revision);
    }

    return $revisions;
}

add_action('wp_footer', 'azh_footer');

function azh_footer() {
    $post = get_post();
    if (isset($post->ID) && current_user_can('edit_post', $post->ID)) {
        if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
            $azh = azh_get_object();
            $revisions = azh_get_revisions();
            ?>
            <div id="azexo-html-library">
                <div class="azh-library-actions">
                    <div class="azh-builder" title="<?php esc_html_e('Builder', 'azh'); ?>"></div>
                    <div class="azh-revisions" title="<?php esc_html_e('Revisions history', 'azh'); ?>"></div>
                    <div class="azh-divider"></div>
                    <div class="azh-save" title="<?php esc_html_e('Save page', 'azh'); ?>"></div>
                    <a href="<?php print get_permalink(); ?>" target="_blank" class="azh-live" title="<?php esc_html_e('View page', 'azh'); ?>"></a>
                    <a href="<?php print get_edit_post_link($post); ?>" class="azh-edit-page" title="<?php esc_html_e('Edit page', 'azh'); ?>"></a>
                </div>
                <div class="azh-panel azh-builder azh-active">
                    <?php
                    azh_meta_box();
                    ?>
                </div>
                <div class="azh-panel azh-revisions">
                    <div class="azh-panel-title">
                        <?php esc_html_e('Revisions history', 'azh'); ?>
                    </div>
                    <div class="azh-restore-revision" style="display: none"><?php esc_html_e('Restore this revision', 'azh'); ?></div>                            
                    <div class="azh-panel-content">
                        <?php foreach ($revisions as $revision): ?>
                            <div class="azh-revision" data-id="<?php print $revision['id']; ?>" data-type="<?php print $revision['type']; ?>">
                                <div class="azh-gravatar">
                                    <?php print $revision['gravatar']; ?>
                                </div>
                                <div class="azh-details">
                                    <div class="azh-date">
                                        <?php print $revision['date']; ?>
                                    </div>
                                    <div class="azh-meta">
                                        <?php print $revision['author']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>                    
            </div>
            <?php
        }
    }
    if (apply_filters('azexo-credits', true)) {
        print '<div class="azexo-credits">Works with <a href="https://azexo.com" target="_blank">AZEXO</a> page builder</div>';
    }
}

add_action('admin_enqueue_scripts', 'azh_admin_scripts');

function azh_admin_scripts() {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        azh_icon_font_enqueue('fontawesome');
        azh_icon_font_enqueue('openiconic');
        azh_icon_font_enqueue('typicons');
        azh_icon_font_enqueue('entypo');
        azh_icon_font_enqueue('linecons');
        azh_icon_font_enqueue('themify');
        azh_icon_font_enqueue('custom');
    }
}

add_filter('azh_icons', 'azh_icons');

function azh_icons($icons) {
    global $azh_icons;
    return array_merge($azh_icons, $icons);
}

function azh_get_icon_font_url($font) {
    switch ($font) {
        case 'fontawesome':
            return array('font-awesome' => plugins_url('css/font-awesome/css/font-awesome.min.css', __FILE__));
            break;
        case 'openiconic':
            return array('openiconic' => plugins_url('css/az-open-iconic/az_openiconic.min.css', __FILE__));
            break;
        case 'typicons':
            return array('typicons' => plugins_url('css/typicons/src/font/typicons.min.css', __FILE__));
            break;
        case 'entypo':
            return array('entypo' => plugins_url('css/az-entypo/az_entypo.min.css', __FILE__));
            break;
        case 'linecons':
            return array('linecons-icons' => plugins_url('css/az-linecons/az_linecons_icons.min.css', __FILE__));
            break;
        case 'themify':
            return array('themify-icons' => plugins_url('css/themify/css/themify-icons.css', __FILE__));
            break;
        case 'custom':
            $urls = array();
            $settings = get_option('azh-settings');
            if (!empty($settings['custom-icons-css'])) {
                $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
                if (is_array($dirs)) {
                    foreach ($dirs as $dir => $uri) {
                        if (is_dir($dir)) {
                            $custom_icons_css = explode("\n", $settings['custom-icons-css']);
                            if (is_array($custom_icons_css)) {
                                foreach ($custom_icons_css as $file) {
                                    if (file_exists($dir . '/' . $file)) {
                                        $urls[$uri . '/' . $file] = $uri . '/' . $file;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $urls;
            break;
        default:
            return apply_filters('azh_get_icon_font_url', false, $font);
    }
    return false;
}

function azh_icon_font_enqueue($font) {
    $urls = (array) azh_get_icon_font_url($font);
    if (!empty($urls)) {
        foreach ($urls as $handle => $url) {
            wp_enqueue_style($handle, $url);
        }
    }
}

function azh_content_begin($content) {
    global $azh_widget_nested, $post;
    if (!isset($azh_widget_nested)) {
        $azh_widget_nested = 0;
    }
    if (is_object($post)) {
        if ($post->post_type == 'azh_widget') {
            $content = azh_get_post_content($post);
            $azh_widget_nested++;
            if ($azh_widget_nested == 1) {
                remove_filter('the_content', 'wptexturize');
                remove_filter('the_content', 'convert_smilies', 20);
                remove_filter('the_content', 'wpautop');
                remove_filter('the_content', 'gutenberg_wpautop', 6);
                remove_filter('the_content', 'shortcode_unautop');
                remove_filter('the_content', 'prepend_attachment');
                remove_filter('the_content', 'wp_make_content_images_responsive');
            }
        } else {
            $settings = get_option('azh-settings');
            if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types'])) && get_post_meta($post->ID, 'azh', true)) {
                $content = azh_get_post_content($post);
                remove_filter('the_content', 'wpautop');
                remove_filter('the_content', 'gutenberg_wpautop', 6);
            }
        }
    }
    return $content;
}

function azh_content_end($content) {
    global $azh_widget_nested, $post;

    if (is_object($post)) {
        if ($post->post_type == 'azh_widget') {
            $post_id = azh_post_id_from_url();
            if (!$post_id) {
                $current_post = azh_get_earliest_current_post(array('vc_widget', 'azh_widget'), false);
                if ($current_post) {
                    $post_id = $current_post->ID;
                }
            }
            if (isset($_GET['azh']) && $_GET['azh'] == 'customize' && $post_id == $post->ID) {
                $content = '<div class="azh-content-wrapper">' . $content . '</div>';
            }
            $azh_widget_nested--;
            if ($azh_widget_nested == 0) {
                add_filter('the_content', 'wptexturize');
                add_filter('the_content', 'convert_smilies', 20);
                add_filter('the_content', 'wpautop');
                add_filter('the_content', 'shortcode_unautop');
                add_filter('the_content', 'prepend_attachment');
                add_filter('the_content', 'wp_make_content_images_responsive');
            }
        } else {
            $settings = get_option('azh-settings');
            if (isset($settings['post-types']) && in_array($post->post_type, array_keys($settings['post-types'])) && get_post_meta($post->ID, 'azh', true)) {
                $post_id = azh_post_id_from_url();
                if (!$post_id) {
                    $current_post = azh_get_earliest_current_post(array('vc_widget', 'azh_widget'), false);
                    if ($current_post) {
                        $post_id = $current_post->ID;
                    }
                }
                if (isset($_GET['azh']) && $_GET['azh'] == 'customize' && $post_id == $post->ID) {
                    if (empty($content)) {
                        $content = '<div data-section="content"><div data-cloneable><div data-element=" "></div></div></div>';
                    }
                    $content = '<div class="azh-content-wrapper">' . $content . '</div>';
                }
                if (!empty($content) && false === strpos($content, ' data-section=')) {
                    $content = '<div data-section="content">' . $content . '</div>';
                }
                add_filter('the_content', 'wpautop');
            }
        }
    }
    return $content;
}

function azh_enqueue_icons($content) {
    global $azh_icons, $azh_icons_index;
    foreach ($azh_icons as $icon_type => $icons) {
        $pattern = '/' . implode('|', array_keys($icons)) . '/';
        if (preg_match($pattern, $content, $matches)) {
            if (isset($azh_icons_index[$matches[0]])) {
                azh_icon_font_enqueue($azh_icons_index[$matches[0]]);
            }
        }
    }
}

function azh_get_icons_fonts_urls($content) {
    $fonts_urls = array();
    global $azh_icons, $azh_icons_index;
    foreach ($azh_icons as $icon_type => $icons) {
        $pattern = '/' . implode('|', array_keys($icons)) . '/';
        if (preg_match($pattern, $content, $matches)) {
            $urls = (array) azh_get_icon_font_url($azh_icons_index[$matches[0]]);
            if (!empty($urls)) {
                $fonts_urls = array_merge($fonts_urls, $urls);
            }
        }
    }
    return $fonts_urls;
}

function azh_generate_ids($content) {
    preg_match_all('{{id-(\d+)}}', $content, $matches);
    if ($matches) {
        $numbers = array_unique($matches[1]);
        sort($numbers, SORT_NUMERIC);
        foreach ($numbers as $number) {
            $id = 'id' . substr(md5(rand()), 0, 7);
            $content = str_replace('{{id-' . $number . '}}', $id, $content);
        }
    }
    return $content;
}

function azh_uri_replace($content) {
    $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
    return preg_replace_callback('#{{azh-uri}}([\/\w\d\-\_\.]+)#i', function($m) use ($dirs) {
        foreach ($dirs as $path => $url) {
            if (file_exists($path . $m[1])) {
                return $url . $m[1];
            }
        }
        return '';
    }, $content);
}

function azh_replaces($content, $azh_uri = false) {
    $replaces = array(
        'azh-uri' => $azh_uri ? $azh_uri : apply_filters('azh_uri', get_template_directory_uri() . '/azh'),
    );

    $post = azh_get_closest_current_post('azh_widget', false);
    if ($post) {
        $replaces['post_title'] = $post->post_title;
        $replaces['post_excerpt'] = $post->post_excerpt;
        $replaces['post_content'] = $post->post_content;
        $replaces['post_thumbnail'] = get_the_post_thumbnail_url($post, 'full');
        $replaces['post_permalink'] = get_permalink($post);
    } else {
        $replaces['post_title'] = get_bloginfo('name');
        $replaces['post_excerpt'] = get_bloginfo('description');
        $replaces['post_content'] = '';
        $replaces['post_thumbnail'] = '';
        $replaces['post_permalink'] = '';
    }

    $replaces = apply_filters('azh_replaces', $replaces);
    $content = preg_replace_callback('#{{([^}]+)}}#i', function($m) use ($replaces) {
        if (isset($replaces[$m[1]])) { // If it exists in our array            
            return $replaces[$m[1]]; // Then replace it from our array
        } else {
            return $m[0]; // Otherwise return the whole match (basically we won't change it)
        }
    }, $content);

    return $content;
}

function azh_remove_comments($content) {
    $content = preg_replace_callback('#\[\[([^\]]+)\]\]#i', function($m) {
        return '';
    }, $content);
    return $content;
}

function azh_post_id_from_url() {
    $post_id = isset($_GET['azhp']) ? $_GET['azhp'] : false;

    if (!$post_id) {
        $post_id = isset($_GET['p']) ? $_GET['p'] : false;
    }
    if (!$post_id) {
        $post_id = isset($_GET['page_id']) ? $_GET['page_id'] : false;
    }
    if (!$post_id) {
        $post_id = isset($_GET['post']) ? $_GET['post'] : false;
    }
    return $post_id;
}

function azh_refresh_attachements_urls($old_home_url, $new_home_url) {
    $settings = get_option('azh-settings');
    if (isset($settings['post-types']) && is_array($settings['post-types'])) {
        $pages = get_posts(array(
            'post_type' => array_keys($settings['post-types']),
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows' => 1,
            'posts_per_page' => -1,
            'numberposts' => -1,
            'meta_query' => array(
                array(
                    'key' => '_azh_content',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => 'azh',
                    'value' => 'azh',
                )
            )
        ));
        foreach ($pages as $page) {
            $content = azh_get_post_content($page);
            preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $match);
            if ($match) {
                foreach ($match[0] as $url) {
                    if (strpos($url, $old_home_url) !== false) {
                        $new_url = str_replace($old_home_url, $new_home_url, $url);
                        $post_id = attachment_url_to_postid($new_url);
                        if ($post_id) {
                            $content = str_replace($url, $new_url, $content);
                        }
                    }
                }
                azh_set_post_content($content, $page->ID);
            }
        }
    }
}

add_action('init', 'azh_init');

function azh_init() {
    if (is_admin()) {
        $settings = get_option('azh-settings');

        if (!isset($settings['home_url']) || $settings['home_url'] !== home_url('/') || !isset($settings['version']) || $settings['version'] !== AZH_PLUGIN_VERSION) {
            if (isset($settings['home_url']) && !empty($settings['home_url']) && $settings['home_url'] !== home_url('/')) {
                azh_refresh_attachements_urls($settings['home_url'], home_url('/'));
            }
            $settings['home_url'] = home_url('/');
            $settings['version'] = AZH_PLUGIN_VERSION;
            update_option('azh-settings', $settings);
            update_option('azh-library', array());
            update_option('azh-all-settings', array());
            update_option('azh-get-content-scripts', array());
            update_option('azh-content-settings', array());
        }

        if (isset($settings['post-types']) && is_array($settings['post-types'])) {
            foreach ($settings['post-types'] as $post_type => $flag) {
                add_post_type_support($post_type, 'revisions');
            }

            if (!isset($settings['post-types']['azh_widget'])) {
                if (isset($settings['post-types'])) {
                    $settings['post-types']['azh_widget'] = 1;
                } else {
                    $settings['post-types'] = array(
                        'page' => 1,
                        'azh_widget' => 1,
                    );
                }
                update_option('azh-settings', $settings);
            }
        }
    }

    $current_user = wp_get_current_user();

    azh_add_element(array(
        "name" => "Form",
        "base" => "azh_form",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'textfield',
                'group' => esc_html__('Notification', 'azh'),
                'heading' => esc_html__('Notification to email', 'azh'),
                'param_name' => 'to_email',
                'value' => $current_user->user_email,
            ),
            array(
                'type' => 'textfield',
                'group' => esc_html__('Notification', 'azh'),
                'heading' => esc_html__('Email subject template', 'azh'),
                'param_name' => 'subject_template',
                'value' => '',
                'description' => __('Available form fields: {form-fields}', 'azh'),
            ),
            array(
                'type' => 'textarea_raw_html',
                'group' => esc_html__('Notification', 'azh'),
                'heading' => esc_html__('Email body template', 'azh'),
                'param_name' => 'content',
                'value' => '',
                'description' => __('Available form fields: {form-fields} ', 'azh'),
            ),
            array(
                'type' => 'url',
                'group' => esc_html__('Confirmation', 'azh'),
                'heading' => esc_html__('Confirmation redirect', 'azh'),
                'param_name' => 'success_redirect',
                'value' => '',
            ),
            array(
                'type' => 'textfield',
                'group' => esc_html__('Confirmation', 'azh'),
                'heading' => esc_html__('Confirmation message', 'azh'),
                'param_name' => 'success',
                'value' => '',
            ),
            array(
                'type' => 'textfield',
                'group' => esc_html__('Autoresponder', 'azh'),
                'heading' => esc_html__('Autorespond to email from submitted values', 'azh'),
                'param_name' => 'autoresponder_to_email',
                'value' => '',
                'description' => __('Available form fields: {form-fields}', 'azh'),
            ),
            array(
                'type' => 'textfield',
                'group' => esc_html__('Autoresponder', 'azh'),
                'heading' => esc_html__('Autorespond from email', 'azh'),
                'param_name' => 'autoresponder_from_email',
                'value' => get_bloginfo('admin_email'),
            ),
            array(
                'type' => 'textfield',
                'group' => esc_html__('Autoresponder', 'azh'),
                'heading' => esc_html__('Email subject template', 'azh'),
                'param_name' => 'autoresponder_subject_template',
                'value' => '',
                'description' => __('Available form fields: {form-fields}', 'azh'),
            ),
            array(
                'type' => 'textarea_raw_html',
                'group' => esc_html__('Autoresponder', 'azh'),
                'heading' => esc_html__('Email body template', 'azh'),
                'param_name' => 'autoresponder_body_template',
                'value' => '',
                'description' => __('Available form fields: {form-fields} ', 'azh'),
            ),
        ),
            ), 'azh_form');

    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        if (defined('SCRIPT_DEBUG')) {
            if (!SCRIPT_DEBUG && !defined('CONCATENATE_SCRIPTS')) {
                define('CONCATENATE_SCRIPTS', false);
            }
        } else {
            define('SCRIPT_DEBUG', true);
        }

        $post_id = get_the_ID();
        if (!$post_id) {
            $post_id = azh_post_id_from_url();
        }
        if ($post_id) {
            $post = get_post($post_id);
            update_post_meta($post_id, 'azh', 'azh');
            if ($post->post_status == 'auto-draft') {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'draft',
                ));
            }
        }
    }
}

add_filter('the_content', 'azh_the_content', 0);

function azh_the_content($content) {

    $content = azh_content_begin($content);
    $settings = get_option('azh-settings');
    if (get_post_type() == 'azh_widget' || (isset($settings['post-types']) && in_array(get_post_type(), array_keys($settings['post-types'])) && get_post_meta(get_the_ID(), 'azh', true))) {

        if (preg_match('/carousel-wrapper/', $content)) {
            wp_enqueue_script('owl.carousel');
            wp_enqueue_style('owl.carousel');
        }
        if (preg_match('/(image-popup|iframe-popup)/', $content)) {
            wp_enqueue_script('magnific-popup');
            wp_enqueue_style('magnific-popup');
        }
        if (preg_match('/data-sr/', $content)) {
            wp_enqueue_script('scrollReveal');
        }
        if (preg_match('/masonry/', $content)) {
            wp_enqueue_script('masonry');
        }
        if (preg_match('/azexo-tabs/', $content)) {
            wp_enqueue_script('jquery-ui-tabs');
        }
        if (preg_match('/azexo-accordion/', $content)) {
            wp_enqueue_script('jquery-ui-accordion');
        }
    }

    $content = azh_replaces($content);
    $content = azh_generate_ids($content);
    $content = azh_remove_comments($content);

    azh_enqueue_icons($content);

    return $content;
}

add_filter('the_content', 'azh_the_content_last', 100);

function azh_the_content_last($content) {

    $content = azh_content_end($content);
    return $content;
}

add_action('widgets_init', 'azh_widgets_register_widgets');

function azh_widgets_register_widgets() {
    register_widget('AZH_Widget');
}

class AZH_Widget extends WP_Widget {

    function __construct() {
        parent::__construct('azh_widget', __('AZEXO - HTML Widget', 'azh'));
    }

    function widget($args, $instance) {

        $body = '';
        if (!empty($instance['post'])) {
            $wpautop = false;
            if (has_filter('the_content', 'wpautop')) {
                remove_filter('the_content', 'wpautop');
                $wpautop = true;
            }
            $gutenberg_wpautop = false;
            if (has_filter('the_content', 'gutenberg_wpautop')) {
                remove_filter('the_content', 'gutenberg_wpautop', 6);
                $gutenberg_wpautop = true;
            }

            if ($instance['post'] == NULL) {
                $body = '<div data-post-id="' . get_the_ID() . '">' . apply_filters('the_content', get_the_content()) . '</div>';
            } else {

                if (!apply_filters('azh_widget_' . $instance['post'] . '_visible', true, $instance['post'])) {
                    return;
                }

                global $post, $wp_query;
                $original = $post;
                $post = get_post($instance['post']);
                setup_postdata($post);
                $body = '<div data-post-id="' . $instance['post'] . '">' . apply_filters('the_content', $post->post_content) . '</div>';
                $wp_query->post = $original;
                wp_reset_postdata();
            }

            if ($wpautop) {
                add_filter('the_content', 'wpautop');
            }
            if ($gutenberg_wpautop) {
                add_filter('the_content', 'gutenberg_wpautop', 6);
            }
        }

        $args = apply_filters('azh_widget_args', $args, $instance, $body);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        print $body;

        print $args['after_widget'];
    }

    function form($instance) {
        $defaults = array('post' => '', 'title' => '');
        $instance = wp_parse_args((array) $instance, $defaults);


        $azh_widgets = array();
        $loop = new WP_Query(array(
            'post_type' => 'azh_widget',
            'post_status' => 'publish',
            'showposts' => 100,
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        if ($loop->have_posts()) {
            global $post, $wp_query;
            $original = $post;
            while ($loop->have_posts()) {
                $loop->the_post();
                $azh_widgets[] = $post;
            }
            $wp_query->post = $original;
            wp_reset_postdata();
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'azh'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('post'); ?>"><?php _e('AZH Widget:', 'azh'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('post'); ?>" name="<?php echo $this->get_field_name('post'); ?>">
                <?php
                foreach ($azh_widgets as $azh_widget) :
                    ?>
                    <option value="<?php echo $azh_widget->ID ?>" <?php selected($azh_widget->ID, $instance['post']) ?>><?php echo $azh_widget->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </p>        
        <?php
    }

}

add_action('init', 'azh_widgets_register');

function azh_widgets_register() {
    register_post_type('azh_widget', array(
        'labels' => array(
            'name' => __('AZH Widget', 'azh'),
            'singular_name' => __('AZH Widget', 'azh'),
            'add_new' => __('Add AZH Widget', 'azh'),
            'add_new_item' => __('Add New AZH Widget', 'azh'),
            'edit_item' => __('Edit AZH Widget', 'azh'),
            'new_item' => __('New AZH Widget', 'azh'),
            'view_item' => __('View AZH Widget', 'azh'),
            'search_items' => __('Search AZH Widgets', 'azh'),
            'not_found' => __('No AZH Widget found', 'azh'),
            'not_found_in_trash' => __('No AZH Widget found in Trash', 'azh'),
            'parent_item_colon' => __('Parent AZH Widget:', 'azh'),
            'menu_name' => __('AZH Widgets', 'azh'),
        ),
        'query_var' => false,
        'rewrite' => false,
        'hierarchical' => true,
        'supports' => array('title', 'editor', 'revisions', 'thumbnail', 'custom-fields'),
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
            )
    );
    register_taxonomy('widget_type', array('azh_widget'), array(
        'label' => __('Widget type', 'azh'),
        'hierarchical' => true,
    ));
}

add_filter('manage_azh_widget_posts_columns', 'azh_widget_columns');

function azh_widget_columns($columns) {
    $columns['shortcode'] = __('Shortcode', 'azh');
    return $columns;
}

add_action('manage_azh_widget_posts_custom_column', 'azh_widget_custom_columns', 10, 2);

function azh_widget_custom_columns($column, $post_id) {
    switch ($column) {
        case 'shortcode' :
            print '[azh_post id="' . $post_id . '"]';
            break;
    }
}

//add_filter('default_content', 'azh_default_content', 10, 2);

function azh_default_content($content, $post) {

    if ($post->post_type == 'azh_widget') {
        return '<div data-section="element"><div data-cloneable><div data-element=" "></div></div></div>';
    }

    return $content;
}

function azh_builder_scripts() {
    wp_enqueue_style('azh_admin_frontend', plugins_url('css/admin-frontend.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('azh-frontend-customization-options', plugins_url('frontend-customization-options.js', __FILE__), false, AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('simplemodal', plugins_url('js/jquery.simplemodal.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_style('select2', plugins_url('css/select2.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('select2', plugins_url('js/select2.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('azh_admin', plugins_url('js/admin.js', __FILE__), array('azh_admin_frontend'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('azh_htmlparser', plugins_url('js/htmlparser.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('azh_html_editor', plugins_url('js/html_editor.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('jquery-ui-mouse');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_style('dashicons');
    wp_enqueue_script('ace', plugins_url('js/ace/ace.js', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('css', plugins_url('js/css.js', __FILE__), false, AZH_PLUGIN_VERSION);


    $suffix = SCRIPT_DEBUG ? '' : '.min';
    wp_register_script('iris', '/wp-admin/js/iris.min.js', array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), AZH_PLUGIN_VERSION, 1);
    wp_register_script('wp-color-picker', "/wp-admin/js/color-picker$suffix.js", array('iris'), false, true);
    wp_enqueue_style('wp-color-picker', "/wp-admin/css/color-picker$suffix.css");
    wp_enqueue_script('wp-color-picker-alpha', plugins_url('js/wp-color-picker-alpha.js', __FILE__), array('wp-color-picker'), AZH_PLUGIN_VERSION, true);
    wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
        'clear' => __('Clear', 'azh'),
        'defaultString' => __('Default', 'azh'),
        'pick' => __('Select Color', 'azh'),
        'current' => __('Current Color', 'azh'),
    ));
}

function azh_device_prefixes() {
    $settings = get_option('azh-settings');
    $device_prefixes = array(
        'lg' => array(
            'label' => __('Large device', 'azh'),
            'width' => false,
            'height' => false,
            'container' => 1170,
            'min' => 1200
        ),
        'md' => array(
            'label' => __('Medium device', 'azh'),
            'width' => 992,
            'height' => false,
            'container' => 970,
            'max' => 1199,
            'min' => 992
        ),
        'sm' => array(
            'label' => __('Small device', 'azh'),
            'width' => 768,
            'height' => 1150,
            'container' => 750,
            'max' => 991,
            'min' => 768
        ),
        'xs' => array(
            'label' => __('Extra small device', 'azh'),
            'width' => 320,
            'height' => 750,
            'max' => 767
        ),
    );
    if (isset($settings['container-widths'])) {
        $widths = explode("\n", trim($settings['container-widths']));
        if (count($widths) >= 3) {
            $sm_min_max = explode(":", $widths[0]);
            $md_min_max = explode(":", $widths[1]);
            $lg_min_max = explode(":", $widths[2]);
            if (count($sm_min_max) === 2 && count($md_min_max) === 2 && count($lg_min_max) === 2) {

                $device_prefixes['sm']['container'] = (int) $sm_min_max[1];
                $device_prefixes['sm']['width'] = (int) $sm_min_max[0];
                $device_prefixes['md']['container'] = (int) $md_min_max[1];
                $device_prefixes['md']['width'] = (int) $md_min_max[0];
                $device_prefixes['lg']['container'] = (int) $lg_min_max[1];


                $device_prefixes['xs']['max'] = (int) $sm_min_max[0] - 1;
                $device_prefixes['sm']['min'] = (int) $sm_min_max[0];
                $device_prefixes['sm']['max'] = (int) $md_min_max[0] - 1;
                $device_prefixes['md']['min'] = (int) $md_min_max[0];
                $device_prefixes['md']['max'] = (int) $lg_min_max[0] - 1;
                $device_prefixes['lg']['min'] = (int) $lg_min_max[0];
            }
        }
    }
    return $device_prefixes;
}

function azh_container_widths() {
    $device_prefixes = azh_device_prefixes();

    $custom_css = ".az-container {
        padding-right: 15px;
        padding-left: 15px;
        margin-left: auto;
        margin-right: auto;
        box-sizing: border-box;
    }\n";
    foreach (array('xs', 'sm', 'md', 'lg') as $prefix) {
        $settings = $device_prefixes[$prefix];
        if (isset($settings['min']) && $settings['container']) {
            $custom_css .= "@media (min-width: " . $settings['min'] . "px) {
                    .az-container {
                        max-width: " . $settings['container'] . "px;
                    }
                }\n";
        }
    }
    wp_add_inline_style('azh_frontend', $custom_css);
    return $custom_css;
}

function azh_get_sections_elements_edit_links($edit_links) {
    $sections_edit = array();
    $post = get_post();
    preg_match_all('/ data-section=[\'"]([^\'"]+)[\'"]/i', azh_get_post_content($post), $matches);
    if (is_array($matches)) {
        $post_type_object = get_post_type_object($post->post_type);
        foreach ($matches[1] as $match) {
            $sections_edit['[data-section="' . $match . '"]'] = esc_url(admin_url(sprintf($post_type_object->_edit_link . '&action=edit&section=' . $match, get_the_ID())));
        }
    }
    $edit_links['sections'] = array(
        'links' => $sections_edit,
        'css' => array('top' => '0', 'left' => '0'),
        'text' => esc_html__('Edit section', 'azh'),
        'target' => '_self',
    );
    $elements_edit = array();
    preg_match_all('/ data-element=[\'"]([^\'"]+)[\'"]/i', azh_get_post_content($post), $matches);
    if (is_array($matches)) {
        $post_type_object = get_post_type_object($post->post_type);
        foreach ($matches[1] as $match) {
            $elements_edit['[data-element="' . $match . '"]'] = esc_url(admin_url(sprintf($post_type_object->_edit_link . '&action=edit&element=' . $match, get_the_ID())));
        }
    }
    $edit_links['elements'] = array(
        'links' => $elements_edit,
        'css' => array('top' => '0', 'right' => '0'),
        'text' => esc_html__('Edit element', 'azh'),
        'target' => '_self',
    );
    return $edit_links;
}

function azh_get_edit_links() {
    global $post;
    $edit_links = array();
    $azh_widgets_edit = array();
    if (is_object($post)) {
        global $wp_widget_factory;
        foreach ($wp_widget_factory->widgets as $name => $widget_obj) {
            if ($name == 'AZH_Widget') {
                $instances = $widget_obj->get_settings();
                foreach ($instances as $number => $instance) {
                    if (isset($instance['post']) && is_numeric($instance['post'])) {
                        $widget_post = get_post($instance['post']);
                        //$selector = '#' . $widget_obj->id_base . '-' . $number;
                        $selector = '[data-post-id="' . $instance['post'] . '"]';
                        if ($widget_post) {
                            $azh_widgets_edit[$selector] = add_query_arg(array('azh' => 'customize', 'azhf' => $post->ID), get_edit_post_link($widget_post));
                        }
                    }
                }
            }
        }
    }
    $edit_links['azh_widgets'] = array(
        'links' => $azh_widgets_edit,
        'text' => esc_html__('Edit AZH Widget', 'azh'),
        'target' => '_blank',
    );
    if (is_page() && get_post_meta(get_the_ID(), 'azh', true)) {
        //$edit_links = azh_get_sections_elements_edit_links($edit_links);
    }
    return $edit_links;
}

add_action('wp_enqueue_scripts', 'azh_scripts');

function azh_scripts() {
    global $post;
    wp_enqueue_script('isotope', plugins_url('js/isotope.pkgd.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('waypoints', plugins_url('js/jquery.waypoints.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('countdown', plugins_url('js/jquery.countdown.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('maskedinput', plugins_url('js/jquery.maskedinput.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_style('swiper', plugins_url('css/swiper.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('swiper', plugins_url('js/swiper.js', __FILE__), false, AZH_PLUGIN_VERSION, true);
    wp_enqueue_style('animate', plugins_url('css/animate.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_style('magnific-popup', plugins_url('css/magnific-popup.css', __FILE__), false, AZH_PLUGIN_VERSION);
    wp_enqueue_script('magnific-popup', plugins_url('js/jquery.magnific-popup.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('scrollReveal', plugins_url('js/scrollReveal.js', __FILE__), array('jquery'), AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('azh-parallax', plugins_url('js/parallax.js', __FILE__), false, AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('rellax', plugins_url('js/rellax.js', __FILE__), false, AZH_PLUGIN_VERSION, true);
    wp_enqueue_script('liquid', plugins_url('js/liquid.js', __FILE__), false, AZH_PLUGIN_VERSION);


    $user = wp_get_current_user();
    if (isset($post->ID) && current_user_can('edit_post', $post->ID)) {
        wp_enqueue_script('azh_admin_frontend', plugins_url('js/admin-frontend.js', __FILE__), array('jquery', 'underscore', 'jquery-ui-mouse', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-autocomplete'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_script('azh_frontend', plugins_url('js/frontend.js', __FILE__), array('jquery', 'imagesloaded', 'azh_admin_frontend'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_style('azh_frontend', plugins_url('css/frontend.css', __FILE__), false, AZH_PLUGIN_VERSION);
        $azh = azh_get_object();
        if (in_array('administrator', (array) $user->roles)) {
            $azh['edit_links'] = azh_get_edit_links();
        }
        wp_localize_script('azh_admin_frontend', 'azh', $azh);

        if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
            azh_builder_scripts();
        }
    } else {
        wp_enqueue_script('azh_frontend', plugins_url('js/frontend.js', __FILE__), array('jquery', 'imagesloaded'), AZH_PLUGIN_VERSION, true);
        wp_enqueue_style('azh_frontend', plugins_url('css/frontend.css', __FILE__), false, AZH_PLUGIN_VERSION);

        if (get_page_template_slug() != 'azexo-html-library.php') {
            wp_localize_script('azh_frontend', 'azh', azh_get_frontend_object());
        }
    }

    azh_container_widths();
    $settings = get_option('azh-settings');
    if (isset($settings['credits']) && isset($settings['credits']['enable']) && $settings['credits']['enable']) {
        wp_add_inline_style('azh_frontend', '.azexo-credits {display: block;}');
    }

    $post_content = azh_get_post_content($post);
    $post_settings = azh_get_post_settings();
    $widgets_content = azh_get_widgets_content();
    $included_widgets_content = azh_get_included_widgets_content($post_content);
    $fonts_url = azh_get_google_fonts_url($post_settings, $widgets_content . $included_widgets_content . $post_content, $post);
    if ($fonts_url) {
        wp_enqueue_style('azh-fonts', $fonts_url, array(), null);
    }
    $post_scripts = azh_get_post_scripts();
    if (!empty($post_scripts['css'])) {
        foreach ($post_scripts['css'] as $css) {
            wp_enqueue_style($css, $css);
        }
    }
    if (!empty($post_scripts['js'])) {
        foreach ($post_scripts['js'] as $js) {
            wp_enqueue_script($js, $js, array('azh_frontend'), false, true);
        }
    }
}

add_filter('post_type_link', 'azh_post_link', 10, 3);

function azh_post_link($permalink, $post, $leavename) {
    if (in_array($post->post_type, array('azh_widget'))) {
        $external_url = get_post_meta($post->ID, 'external_url', true);
        if (!empty($external_url)) {
            return $external_url;
        }
    }
    return $permalink;
}

function azh_group_label_order($a, $b) {
    if ($a['group'] < $b['group']) {
        return -1;
    } else {
        if ($a['group'] > $b['group']) {
            return 1;
        } else {
            if ($a['label'] < $b['label']) {
                return -1;
            } else {
                if ($a['label'] > $b['label']) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
    }
}

add_action('wp_ajax_azh_terms_autocomplete_labels', 'azh_terms_autocomplete_labels');

function azh_terms_autocomplete_labels() {
    azh_get_terms_labels();
    wp_die();
}

function azh_get_terms_labels($taxonomy = array(), $slug = false) {
    $include = array_filter(explode(',', sanitize_text_field($_POST['values'])));
    if (empty($taxonomy)) {
        $taxonomies_types = get_taxonomies(array('public' => true), 'objects');
        $taxonomy = array_keys($taxonomies_types);
    }
    $args = array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    );
    if (!empty($include)) {
        if ($slug) {
            $args['slug'] = $include;
        } else {
            $args['include'] = $include;
        }
    }
    $terms = get_terms($args);
    $data = array();
    if (is_array($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            if (is_object($term)) {
                if ($slug) {
                    $data[$term->slug] = $term->name;
                } else {
                    $data[$term->term_id] = $term->name;
                }
            }
        }
    }
    print json_encode($data);
}

add_action('wp_ajax_azh_posts_autocomplete_labels', 'azh_posts_autocomplete_labels');

function azh_posts_autocomplete_labels() {
    azh_get_posts_labels();
    wp_die();
}

function azh_get_posts_labels($post_type = 'any') {
    if (empty($post_type)) {
        $post_type = 'any';
    }
    $include = array_filter(explode(',', sanitize_text_field($_POST['values'])));
    if (empty($include)) {
        $include = array(0);
    }
    $data = array();
    $posts = get_posts(array(
        'post_type' => $post_type,
        'include' => $include,
    ));
    if (is_array($posts) && !empty($posts)) {
        foreach ($posts as $post) {
            if (is_object($post)) {
                $data[$post->ID] = $post->post_title;
            }
        }
    }
    print json_encode($data);
}

add_action('wp_ajax_azh_autocomplete_labels', 'azh_autocomplete_labels');

function azh_autocomplete_labels() {
    if (isset($_POST['shortcode']) && isset($_POST['param_name']) && isset($_POST['values'])) {
        do_action('azh_autocomplete_' . sanitize_text_field($_POST['shortcode']) . '_' . sanitize_text_field($_POST['param_name']) . '_labels');
    }
    wp_die();
}

add_action('wp_ajax_azh_terms_autocomplete', 'azh_terms_autocomplete');

function azh_terms_autocomplete() {
    azh_search_terms();
    wp_die();
}

function azh_search_terms($taxonomy = array(), $slug = false) {
    $data = array();
    $taxonomies_types = get_taxonomies(array('public' => true), 'objects');
    if (empty($taxonomy)) {
        $taxonomy = array_keys($taxonomies_types);
    }
    $exclude = array();
    if (isset($_POST['exclude'])) {
        $exclude = array_filter(explode(',', sanitize_text_field($_POST['exclude'])));
    }
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'exclude' => $exclude,
        'search' => sanitize_text_field($_POST['search']),
    ));
    if (is_array($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            if (is_object($term)) {
                if ($slug) {
                    $data[] = array(
                        'label' => $term->name,
                        'value' => $term->slug,
                        'group' => isset($taxonomies_types[$term->taxonomy], $taxonomies_types[$term->taxonomy]->labels, $taxonomies_types[$term->taxonomy]->labels->name) ? $taxonomies_types[$term->taxonomy]->labels->name : __('Taxonomies', 'azh'),
                    );
                } else {
                    $data[] = array(
                        'label' => $term->name,
                        'value' => $term->term_id,
                        'group' => isset($taxonomies_types[$term->taxonomy], $taxonomies_types[$term->taxonomy]->labels, $taxonomies_types[$term->taxonomy]->labels->name) ? $taxonomies_types[$term->taxonomy]->labels->name : __('Taxonomies', 'azh'),
                    );
                }
            }
        }
    }
    usort($data, 'azh_group_label_order');
    print json_encode($data);
}

add_action('wp_ajax_azh_posts_autocomplete', 'azh_posts_autocomplete');

function azh_posts_autocomplete() {
    azh_search_posts();
    wp_die();
}

function azh_search_posts($post_type = 'any') {
    if (empty($post_type)) {
        $post_type = 'any';
    }
    $data = array();
    $post_types = get_post_types(array('public' => true), 'objects');
    $exclude = array();
    if (isset($_POST['exclude'])) {
        $exclude = array_filter(explode(',', sanitize_text_field($_POST['exclude'])));
    }

    $posts = get_posts(array(
        'post_type' => $post_type,
        'exclude' => $exclude,
        's' => sanitize_text_field($_POST['search']),
    ));
    if (is_array($posts) && !empty($posts)) {
        foreach ($posts as $post) {
            if (is_object($post)) {
                $data[] = array(
                    'label' => $post->post_title,
                    'value' => $post->ID,
                    'group' => isset($post_types[$post->post_type], $post_types[$post->post_type]->labels, $post_types[$post->post_type]->labels->name) ? $post_types[$post->post_type]->labels->name : __('Posts', 'azh'),
                );
            }
        }
    }
    usort($data, 'azh_group_label_order');
    print json_encode($data);
}

add_action('wp_ajax_azh_autocomplete', 'azh_autocomplete');

function azh_autocomplete() {
    if (isset($_POST['shortcode']) && isset($_POST['param_name']) && isset($_POST['search'])) {
        do_action('azh_autocomplete_' . sanitize_text_field($_POST['shortcode']) . '_' . sanitize_text_field($_POST['param_name']));
    }
    wp_die();
}

add_action('wp_ajax_azh_search_post', 'azh_search_post');

function azh_search_post() {
    if (isset($_REQUEST['values']) && is_array($_REQUEST['values'])) {
        $ids = array_map('sanitize_text_field', $_REQUEST['values']);
        $options = array();
        $posts = get_posts(array(
            'post_type' => 'any',
            'include' => $ids,
        ));
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        print json_encode($options);
        wp_die();
    }
    $results = array(
        'results' => array(),
    );
    if (isset($_REQUEST['term'])) {
        $post_type = 'any';
        if (!empty($_REQUEST['post_type'])) {
            $post_type = array_map('sanitize_text_field', (array) $_REQUEST['post_type']);
        }
        $posts = get_posts(array(
            'post_type' => $post_type,
            's' => sanitize_text_field($_REQUEST['term']),
            'posts_per_page' => '10',
        ));
        if (!empty($posts)) {
            $groups = array();
            foreach ($posts as $post) {
                if (!isset($groups[$post->post_type])) {
                    $groups[$post->post_type] = array();
                }
                $groups[$post->post_type][] = array(
                    'id' => $post->ID,
                    'text' => $post->post_title,
                );
            }
            foreach ($groups as $post_type => $group) {
                $obj = get_post_type_object($post_type);
                $results['results'][] = array(
                    'text' => $obj->labels->singular_name,
                    'children' => $group,
                );
            }
        }
    }
    print json_encode($results);
    wp_die();
}

add_action('wp_ajax_azh_search_term', 'azh_search_term');

function azh_search_term() {
    $slug = isset($_REQUEST['slug']) ? true : false;
    if (isset($_REQUEST['values']) && is_array($_REQUEST['values'])) {
        $values = array_map('sanitize_text_field', $_REQUEST['values']);
        $options = array();
        $args = array(
            'hide_empty' => false,
        );
        if ($slug) {
            $args['slug'] = $values;
        } else {
            $args['include'] = $values;
        }
        $terms = get_terms($args);
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if ($slug) {
                    $options[$term->slug] = $term->name;
                } else {
                    $options[$term->term_id] = $term->name;
                }
            }
        }
        print json_encode($options);
        wp_die();
    }
    $results = array(
        'results' => array(),
    );
    if (isset($_REQUEST['term'])) {
        $taxonomies_types = get_taxonomies(array('public' => true), 'objects');
        $taxonomy = array_keys($taxonomies_types);
        if (!empty($_REQUEST['taxonomy'])) {
            $taxonomy = array_map('sanitize_text_field', (array) $_REQUEST['taxonomy']);
        }
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'search' => sanitize_text_field($_REQUEST['term']),
        ));

        if (!empty($terms)) {
            $groups = array();
            foreach ($terms as $term) {
                if (!isset($groups[$term->taxonomy])) {
                    $groups[$term->taxonomy] = array();
                }
                if ($slug) {
                    $groups[$term->taxonomy][] = array(
                        'id' => $term->slug,
                        'text' => $term->name,
                    );
                } else {
                    $groups[$term->taxonomy][] = array(
                        'id' => $term->term_id,
                        'text' => $term->name,
                    );
                }
            }
            foreach ($groups as $taxonomy => $group) {
                $results['results'][] = array(
                    'text' => $taxonomies_types[$taxonomy]->labels->name,
                    'children' => $group,
                );
            }
        }
    }
    print json_encode($results);
    wp_die();
}

function azh_get_attributes($tag, $atts) {
    global $azh_shortcodes;
    if (isset($azh_shortcodes)) {
        if ($tag && isset($azh_shortcodes[$tag])) {
            $settings = $azh_shortcodes[$tag];
            if (isset($settings['params']) && !empty($settings['params'])) {
                if(!is_array($atts)) {
                        $atts = array();
                }
                foreach ($settings['params'] as $param) {
                    if (!isset($atts[$param['param_name']]) && isset($param['value'])) {
                        $atts[$param['param_name']] = $param['value'];
                        if (is_array($atts[$param['param_name']])) {
                            $atts[$param['param_name']] = current($atts[$param['param_name']]);
                        }
                    }
                }
            }
        }
    }
    return $atts;
}

function azh_shortcode($atts, $content = null, $tag = null) {
    global $azh_shortcodes;
    if (isset($azh_shortcodes)) {
        if ($tag && isset($azh_shortcodes[$tag])) {
            $atts = azh_get_attributes($tag, $atts);
            if (isset($azh_shortcodes[$tag]['html_template']) && file_exists($azh_shortcodes[$tag]['html_template'])) {
                ob_start();
                include($azh_shortcodes[$tag]['html_template']);
                return ob_get_clean();
            } else {
                $located = locate_template('vc_templates' . '/' . $tag . '.php');
                if ($located) {
                    ob_start();
                    include($located);
                    return ob_get_clean();
                }
            }
        }
    }
}

function azh_add_element($settings, $func = false) {
    global $azh_shortcodes;
    if (isset($settings['base'])) {
        $azh_shortcodes[$settings['base']] = apply_filters('azh_add_element', $settings);
        if (!shortcode_exists($settings['base'])) {
            if ($func) {
                add_shortcode($settings['base'], $func);
            } else {
                add_shortcode($settings['base'], 'azh_shortcode');
            }
        }
    }
}

function azexo_get_dir_files($src) {
    $files = array();
    $dir = opendir($src);
    if (is_resource($dir))
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $files[$file] = realpath($src . '/' . $file);
            }
        }
    closedir($dir);
    return $files;
}

function azexo_get_skins() {
    $skins = array();
    if (is_child_theme()) {
        $files = azexo_get_dir_files(get_stylesheet_directory() . '/less');
    } else {
        $files = azexo_get_dir_files(get_template_directory() . '/less');
    }
    foreach ($files as $name => $path) {
        if (is_dir($path)) {
            $skin_files = azexo_get_dir_files($path);
            if (isset($skin_files['skin.less'])) {
                $skins[] = $name;
            }
        }
    }
    return $skins;
}

add_action('wp_ajax_azh_upload', 'azh_upload');

function azh_upload() {
    if (isset($_POST['code']) && isset($_POST['dir']) && isset($_POST['file'])) {
        $user = wp_get_current_user();
        if (in_array('administrator', (array) $user->roles)) {
            $file = sanitize_text_field($_POST['dir']) . '/' . sanitize_text_field($_POST['file']);
            if (file_exists($file)) {
                azh_filesystem();
                global $wp_filesystem;
                if ($wp_filesystem->put_contents($file, stripslashes($_POST['code']), FS_CHMOD_FILE)) {
                    print '1';
                }
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azh_copy', 'azh_copy');

function azh_copy() {
    if (isset($_POST['code'])) {
        update_option('azh_clipboard', stripslashes($_POST['code']));
    }
    if (isset($_POST['path'])) {
        update_option('azh_clipboard_path', stripslashes($_POST['path']));
    }
    wp_die();
}

add_action('wp_ajax_azh_paste', 'azh_paste');

function azh_paste() {
    print json_encode(array(
        'code' => get_option('azh_clipboard'),
        'path' => get_option('azh_clipboard_path'),
    ));
    wp_die();
}

function azh_shortcode_restore($atts, $content = null, $tag = null) {
    $shortcode = '[' . $tag;
    if (is_array($atts)) {
        foreach ($atts as $name => $value) {
            $shortcode .= ' ' . $name . '="' . $value . '"';
        }
    }
    $shortcode .= ']';
    if ($content) {
        $shortcode .= $content . '[/' . $tag . ']';
    }
    return $shortcode;
}

function azh_shortcode_wrapper($atts, $content = null, $tag = null) {
    $shortcode = '<div data-element="">' . azh_shortcode_restore($atts, $content, $tag) . '</div>';
    return $shortcode;
}

function azh_wrap_shortcodes($content) {
    global $shortcode_tags;
    $original_shortcode_tags = $shortcode_tags;
    $tags = array_keys($shortcode_tags);
    $shortcode_tags = array();
    foreach ($tags as $tag) {
        if (strpos($tag, 'azh_') === false) {
            $shortcode_tags[$tag] = 'azh_shortcode_wrapper';
        }
    }
    $content = do_shortcode($content);
    $shortcode_tags = $original_shortcode_tags;
    return $content;
}

function azh_get_post_content($post) {
    if (is_object($post)) {
        $content = get_post_meta($post->ID, '_azh_content', true);
        if (empty($content) && !empty($post->post_content) && ((get_post_meta($post->ID, 'azh', true) || $post->post_type === 'azh_widget'))) {
            $content = $post->post_content;
            $content = azh_wrap_shortcodes($content);
            if (!empty($content) && false === strpos($content, ' data-section=')) {
                $content = '<div data-section="content">' . $content . '</div>';
            }
            return azh_set_post_content($content, $post->ID);
        }
        return $content;
    }
}

function azh_wp_post_content($content, $post_id) {
    $override = apply_filters('azh_wp_post_content', false, $content, $post_id);
    if ($override) {
        return $override;
    }
    $plain_content = $content;
    if (!empty($plain_content)) {
        $allowedposttags = array(
            'address' => array(),
            'a' => array(
                'href' => true,
                'rel' => true,
                'rev' => true,
                'name' => true,
                'target' => true,
            ),
            'abbr' => array(),
            'acronym' => array(),
            'area' => array(
                'alt' => true,
                'coords' => true,
                'href' => true,
                'nohref' => true,
                'shape' => true,
                'target' => true,
            ),
            'article' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'aside' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'audio' => array(
                'autoplay' => true,
                'controls' => true,
                'loop' => true,
                'muted' => true,
                'preload' => true,
                'src' => true,
            ),
            'b' => array(),
            'bdo' => array(
                'dir' => true,
            ),
            'big' => array(),
            'blockquote' => array(
                'cite' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'br' => array(),
            'button' => array(
                'disabled' => true,
                'name' => true,
                'type' => true,
                'value' => true,
            ),
            'caption' => array(
                'align' => true,
            ),
            'cite' => array(
                'dir' => true,
                'lang' => true,
            ),
            'code' => array(),
            'col' => array(
                'align' => true,
                'char' => true,
                'charoff' => true,
                'span' => true,
                'dir' => true,
                'valign' => true,
                'width' => true,
            ),
            'colgroup' => array(
                'align' => true,
                'char' => true,
                'charoff' => true,
                'span' => true,
                'valign' => true,
                'width' => true,
            ),
            'del' => array(
                'datetime' => true,
            ),
            'dd' => array(),
            'dfn' => array(),
            'details' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'open' => true,
                'xml:lang' => true,
            ),
//        'div' => array(
//            'align' => true,
//            'dir' => true,
//            'lang' => true,
//            'xml:lang' => true,
//        ),
            'dl' => array(),
            'dt' => array(),
            'em' => array(),
            'fieldset' => array(),
            'figure' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'figcaption' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'font' => array(
                'color' => true,
                'face' => true,
                'size' => true,
            ),
            'footer' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'form' => array(
                'action' => true,
                'accept' => true,
                'accept-charset' => true,
                'enctype' => true,
                'method' => true,
                'name' => true,
                'target' => true,
            ),
            'h1' => array(
                'align' => true,
            ),
            'h2' => array(
                'align' => true,
            ),
            'h3' => array(
                'align' => true,
            ),
            'h4' => array(
                'align' => true,
            ),
            'h5' => array(
                'align' => true,
            ),
            'h6' => array(
                'align' => true,
            ),
            'header' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'hgroup' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'hr' => array(
                'align' => true,
                'noshade' => true,
                'size' => true,
                'width' => true,
            ),
            'i' => array(),
            'img' => array(
                'alt' => true,
                'align' => true,
                'border' => true,
                'height' => true,
                'hspace' => true,
                'longdesc' => true,
                'vspace' => true,
                'src' => true,
                'usemap' => true,
                'width' => true,
            ),
            'ins' => array(
                'datetime' => true,
                'cite' => true,
            ),
            'kbd' => array(),
            'label' => array(
                'for' => true,
            ),
            'legend' => array(
                'align' => true,
            ),
            'li' => array(
                'align' => true,
                'value' => true,
            ),
            'map' => array(
                'name' => true,
            ),
            'mark' => array(),
            'menu' => array(
                'type' => true,
            ),
            'nav' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'p' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'pre' => array(
                'width' => true,
            ),
            'q' => array(
                'cite' => true,
            ),
            's' => array(),
            'samp' => array(),
//        'span' => array(
//            'dir' => true,
//            'align' => true,
//            'lang' => true,
//            'xml:lang' => true,
//        ),
            'section' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'small' => array(),
            'strike' => array(),
            'strong' => array(),
            'sub' => array(),
            'summary' => array(
                'align' => true,
                'dir' => true,
                'lang' => true,
                'xml:lang' => true,
            ),
            'sup' => array(),
            'table' => array(
                'align' => true,
                'bgcolor' => true,
                'border' => true,
                'cellpadding' => true,
                'cellspacing' => true,
                'dir' => true,
                'rules' => true,
                'summary' => true,
                'width' => true,
            ),
            'tbody' => array(
                'align' => true,
                'char' => true,
                'charoff' => true,
                'valign' => true,
            ),
            'td' => array(
                'abbr' => true,
                'align' => true,
                'axis' => true,
                'bgcolor' => true,
                'char' => true,
                'charoff' => true,
                'colspan' => true,
                'dir' => true,
                'headers' => true,
                'height' => true,
                'nowrap' => true,
                'rowspan' => true,
                'scope' => true,
                'valign' => true,
                'width' => true,
            ),
            'textarea' => array(
                'cols' => true,
                'rows' => true,
                'disabled' => true,
                'name' => true,
                'readonly' => true,
            ),
            'tfoot' => array(
                'align' => true,
                'char' => true,
                'charoff' => true,
                'valign' => true,
            ),
            'th' => array(
                'abbr' => true,
                'align' => true,
                'axis' => true,
                'bgcolor' => true,
                'char' => true,
                'charoff' => true,
                'colspan' => true,
                'headers' => true,
                'height' => true,
                'nowrap' => true,
                'rowspan' => true,
                'scope' => true,
                'valign' => true,
                'width' => true,
            ),
            'thead' => array(
                'align' => true,
                'char' => true,
                'charoff' => true,
                'valign' => true,
            ),
            'title' => array(),
            'tr' => array(
                'align' => true,
                'bgcolor' => true,
                'char' => true,
                'charoff' => true,
                'valign' => true,
            ),
            'track' => array(
                'default' => true,
                'kind' => true,
                'label' => true,
                'src' => true,
                'srclang' => true,
            ),
            'tt' => array(),
            'u' => array(),
            'ul' => array(
                'type' => true,
            ),
            'ol' => array(
                'start' => true,
                'type' => true,
                'reversed' => true,
            ),
            'var' => array(),
            'video' => array(
                'autoplay' => true,
                'controls' => true,
                'height' => true,
                'loop' => true,
                'muted' => true,
                'poster' => true,
                'preload' => true,
                'src' => true,
                'width' => true,
            ),
            'iframe' => array(
                'src' => true,
            ),
        );
        $allowedposttags = apply_filters('azh_plain_allowedposttags', $allowedposttags, $post_id);
        if ($allowedposttags) {
            $plain_content = wp_kses($content, $allowedposttags);
        }
    }
    return $plain_content;
}

function azh_set_post_content($content, $post_id) {
    $content = apply_filters('azh_set_post_content', $content, $post_id);
    update_post_meta($post_id, '_azh_content', $content);

    $post_content = $content;
    if (get_post_type($post_id) !== 'azh_widget') {
        $post_content = azh_wp_post_content($post_content, $post_id);
    }
    wp_update_post(array(
        'ID' => $post_id,
        'post_content' => $post_content,
    ));
    return $content;
}

function azh_save_revision($revision_id) {
    $parent_id = wp_is_post_revision($revision_id);
    if ($parent_id && get_post_meta($parent_id, 'azh', true)) {
        $content = get_post_meta($parent_id, '_azh_content', true);
        update_metadata('post', $revision_id, '_azh_content', $content);

        $settings = get_post_meta($parent_id, '_azh_settings', true);
        update_metadata('post', $revision_id, '_azh_settings', $settings);

        update_metadata('post', $revision_id, 'azh', 'azh');
    }
}

add_action('wp_restore_post_revision', 'azh_restore_post_revision', 10, 2);

function azh_restore_post_revision($parent_id, $revision_id) {
    if (get_post_meta($revision_id, 'azh', true)) {
        $content = get_post_meta($revision_id, '_azh_content', true);
        update_post_meta($parent_id, '_azh_content', $content);

        $settings = get_post_meta($revision_id, '_azh_settings', true);
        update_post_meta($parent_id, '_azh_settings', $settings);

        update_post_meta($parent_id, 'azh', 'azh');
    }
}

add_action('wp_ajax_azh_autosave', 'azh_autosave');

function azh_autosave() {
    if (isset($_POST['content']) && is_numeric((int) $_POST['post_id'])) {
        $post = get_post((int) $_POST['post_id']);
        if (current_user_can('edit_post', $post->ID)) {

            add_filter('wp_save_post_revision_post_has_changed', '__return_true');
            add_action('_wp_put_post_revision', 'azh_save_revision');

            $content = stripslashes($_POST['content']);

            $old_autosave = wp_get_post_autosave((int) $_POST['post_id'], get_current_user_id());

            if ($old_autosave) {
                wp_delete_post_revision($old_autosave->ID);
            }

            if ($post->post_type !== 'azh_widget') {
                $content = azh_wp_post_content($content, $post->ID);
            }

            $autosave_id = wp_create_post_autosave(array(
                'post_ID' => (int) $_POST['post_id'],
                'post_title' => __('Auto Save', 'azh') . ' ' . date('Y-m-d H:i'),
                'post_modified' => current_time('mysql'),
                'post_content' => $content,
            ));

            if ($autosave_id && !is_wp_error($autosave_id)) {
                if (isset($_POST['shortcodes'])) {
                    $settings = get_post_meta((int) $_POST['post_id'], '_azh_settings', true);
                    $settings['shortcodes'] = stripslashes_deep($_POST['shortcodes']);
                    update_metadata('post', $autosave_id, '_azh_settings', $settings);

//                    $settings = array_replace_recursive(get_option('azh-settings', array()), $settings);
//                    update_option('azh-settings', $settings);
                }

                $content = apply_filters('azh_set_post_content', $content, $autosave_id);
                update_metadata('post', $autosave_id, '_azh_content', $content);

                print json_encode(azh_parse_revision(get_post($autosave_id)));
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azh_save', 'azh_save');

function azh_save() {
    if (isset($_POST['content']) && is_numeric((int) $_POST['post_id'])) {
        $post = get_post((int) $_POST['post_id']);
        if (current_user_can('edit_post', $post->ID)) {

            add_filter('wp_save_post_revision_post_has_changed', '__return_true');
            add_action('_wp_put_post_revision', 'azh_save_revision');

            if (isset($_POST['shortcodes'])) {
                $settings = get_post_meta((int) $_POST['post_id'], '_azh_settings', true);
                $settings['shortcodes'] = stripslashes_deep($_POST['shortcodes']);
                update_post_meta((int) $_POST['post_id'], '_azh_settings', $settings);


//                $settings = array_replace_recursive(get_option('azh-settings', array()), $settings);
//                update_option('azh-settings', $settings);
            }

            azh_set_post_content(stripslashes($_POST['content']), (int) $_POST['post_id']);
            update_post_meta((int) $_POST['post_id'], 'azh', 'azh');

            $revisions = wp_get_post_revisions((int) $_POST['post_id']);
            $latest_revision = array_shift($revisions);

            print json_encode(azh_parse_revision($latest_revision));
        }
    }
    wp_die();
}

add_action('wp_ajax_azh_update_shortcode', 'azh_update_shortcode');

function azh_update_shortcode() {
    if (isset($_POST['instance']) && isset($_POST['shortcode']) && is_numeric((int) $_POST['post_id'])) {
        $shortcode = stripslashes($_POST['shortcode']);
        $instance = sanitize_text_field($_POST['instance']);

        $settings = get_post_meta((int) $_POST['post_id'], '_azh_settings', true);
        $settings['shortcodes'][$instance] = $shortcode;
        update_post_meta((int) $_POST['post_id'], '_azh_settings', $settings);

        $shortcodes = get_option('azh-shortcodes');
        $shortcodes[$instance] = $shortcode;
        update_option('azh-shortcodes', $shortcodes);



        global $post, $wp_query;
        $original = $post;
        $post = get_post((int) $_POST['post_id']);
        setup_postdata($post);

        print do_shortcode($shortcode);
        //wp_print_styles();
        //wp_print_scripts();

        $wp_query->post = $original;
        wp_reset_postdata();
    }
    wp_die();
}

azh_add_element(array(
    "name" => esc_html__('Text', 'azh'),
    "category" => esc_html__('WordPress', 'azh'),
    "base" => "azh_text",
    "image" => AZH_URL . '/images/text.png',
    "show_settings_on_create" => true,
    'params' => array(
        array(
            'type' => 'textarea_html',
            'heading' => esc_html__('Content', 'azh'),
            'holder' => 'div',
            'param_name' => 'content',
            'value' => wp_kses(__('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>', 'azh'), array('p'))
        ),
    ),
        ), 'azh_text');

function azh_text($atts, $content = null, $tag = null) {
    return do_shortcode(shortcode_unautop($content));
}

function azh_get_file($file, $azh_uri, $frontend = true) {
    if (file_exists($file)) {
        azh_filesystem();
        global $wp_filesystem;
        $content = $wp_filesystem->get_contents($file);
        $content = azh_replaces($content, $azh_uri);
        $content = azh_generate_ids($content);
        $content = azh_remove_comments($content);
        $content = str_replace(array("\t", "\r", "\n"), '', $content);
        $content = preg_replace('/> +</', '><', $content);
        if ($frontend) {
            azh_enqueue_icons($content);
            $content = do_shortcode($content);
        }
        return $content;
    } else {
        return '';
    }
}

function azh_enqueue_content_scripts($content) {
    $fonts_url = azh_get_google_fonts_url(azh_get_content_settings($content), $content);
    if ($fonts_url) {
        wp_enqueue_style('azh-fonts-' . md5($content), $fonts_url, array(), null);
    }

    $scripts = azh_get_content_scripts($content);
    if (!empty($scripts['css'])) {
        foreach ($scripts['css'] as $css) {
            wp_enqueue_style($css, $css);
        }
    }
    if (!empty($scripts['js'])) {
        foreach ($scripts['js'] as $js) {
            wp_enqueue_script($js, $js, array(), false, true);
        }
    }
}

add_shortcode('azh_post', 'azh_post');

function azh_post($atts, $content = null, $tag = null) {
    if (isset($atts['id'])) {

        if (!apply_filters('azh_widget_' . $atts['id'] . '_visible', true, $atts['id'])) {
            return '';
        }

        global $post, $wp_query;
        $original = $post;
        $post = get_post($atts['id']);
        setup_postdata($post);
        $content = '<div data-post-id="' . $atts['id'] . '">' . apply_filters('the_content', get_the_content('')) . '</div>';
        $wp_query->post = $original;
        wp_reset_postdata();

        azh_enqueue_content_scripts($content);

        return $content;
    }
}

add_action('wp_ajax_azh_get_wp_editor', 'azh_get_wp_editor');

function azh_get_wp_editor() {
    ob_start();
    wp_editor('', sanitize_text_field($_POST['id']), array(
        'dfw' => false,
        'media_buttons' => true,
        'tabfocus_elements' => 'insert-media-button',
        'editor_height' => 360,
        'wpautop' => false,
        'drag_drop_upload' => true,
    ));
    $editor = ob_get_contents();
    ob_end_clean();
    print $editor;
    die();
}

add_action('admin_bar_menu', 'azh_admin_bar_menu', 999);

function azh_admin_bar_menu($wp_admin_bar) {
    if (!(isset($_GET['azh']) && $_GET['azh'] == 'customize')) {
        if (!is_admin()) {
            $wp_admin_bar->add_node(array(
                'id' => 'edit-links',
                'title' => esc_html__('Edit links', 'azh'),
                'href' => '#',
                'meta' => array(
                    'class' => 'active',
                ),
            ));
        }
        $settings = get_option('azh-settings');
        if ((isset($settings['post-types']) && in_array(get_post_type(), array_keys($settings['post-types'])) && get_post_meta(get_the_ID(), 'azh', true)) || (is_singular() && get_post_type() == 'azh_widget')) {
            $wp_admin_bar->add_node(array(
                'id' => 'azh-frontend-builder',
                'title' => esc_html__('Edit with AZEXO', 'azh'),
                'href' => add_query_arg('azh', 'customize', get_edit_post_link()),
            ));
        }
    }
}

add_action('template_redirect', 'azh_template_redirect');

function azh_template_redirect() {
    if (isset($_GET['azh']) && $_GET['azh'] == 'library' && isset($_GET['customize'])) {
        $files = explode('|', $_GET['customize']);
        $library = azh_get_library();
        $content = '';
        foreach ($files as $file) {
            if (is_array($library['elements'])) {
                foreach ($library['elements'] as $element_file => $name) {
                    if (strlen($element_file) - strlen($file) == strrpos($element_file, $file)) {
                        $content .= '<div data-element="' . esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/')) . '">';
                        $content .= azh_get_file($element_file, $library['elements_uri'][$element_file]);
                        $content .= '</div>';
                        break;
                    }
                }
            }
            if (is_array($library['sections'])) {
                foreach ($library['sections'] as $section_file => $name) {
                    if (strlen($section_file) - strlen($file) == strrpos($section_file, $file)) {
                        $content .= '<div data-section="' . esc_attr(ltrim(str_replace($library['sections_dir'][$section_file], '', $section_file), '/')) . '">';
                        $content .= azh_get_file($section_file, $library['sections_uri'][$section_file]);
                        $content .= '</div>';
                        break;
                    }
                }
            }
        }
        if (!empty($content)) {
            $post_id = wp_insert_post(array(
                'post_title' => '',
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_content' => $content,
                    ), true);
            if (!is_wp_error($post_id)) {
                azh_set_post_content($content, $post_id);
                add_post_meta($post_id, 'azh', 'azh', true);
                exit(wp_redirect(get_permalink($post_id)));
            }
        }
    }
}

add_filter('theme_page_templates', 'azh_theme_page_templates', 10, 4);

function azh_theme_page_templates($post_templates, $theme, $post, $post_type) {
    $post_templates['azexo-html-template.php'] = esc_html__('AZEXO Canvas', 'azh');
    $post_templates['azexo-html-library.php'] = esc_html__('AZEXO Library', 'azh');
    return $post_templates;
}

add_filter('template_include', 'azh_template_include');

function azh_template_include($template) {
    global $azh_current_post_stack;
    if (is_object(get_queried_object()) && get_class(get_queried_object()) == 'WP_Post') {
        $azh_current_post_stack = array(get_queried_object());
    }
    if (isset($_GET['azh']) && $_GET['azh'] == 'library') {
        return plugin_dir_path(__FILE__) . 'azexo-html-library.php';
    }
    if (is_page()) {
        $template_slug = get_page_template_slug();
        if ($template_slug == 'azexo-html-template.php') {
            $template = locate_template('azexo-html-template.php');
            if (!$template) {
                $template = plugin_dir_path(__FILE__) . 'azexo-html-template.php';
            }
            return $template;
        }
        if ($template_slug == 'azexo-html-library.php') {
            $template = locate_template('azexo-html-library.php');
            if (!$template) {
                $template = plugin_dir_path(__FILE__) . 'azexo-html-library.php';
            }
            return $template;
        }
    } else {
        if (is_singular() && get_post_type() == 'azh_widget') {
            $template = locate_template('azh-template.php');
            if (!$template) {
                $template = plugin_dir_path(__FILE__) . 'azh-template.php';
            }
            return $template;
        }
    }
    return $template;
}

add_filter('post_class', 'azh_post_class', 100);

function azh_post_class($classes) {
    if (is_page()) {
        if (get_page_template_slug() == 'azexo-html-template.php') {
            $classes = array('az-container');
        }
    }
    return $classes;
}

function azh_filesystem() {
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

function azh_get_all_settings() {
    static $all_settings = array();
    if (!empty($all_settings)) {
        return $all_settings;
    }
    $all_settings = get_option('azh-all-settings', array());
    $user = wp_get_current_user();
    if (in_array('administrator', (array) $user->roles) && WP_DEBUG || empty($all_settings)) {
        azh_filesystem();
        global $wp_filesystem;
        $all_settings = array();
        $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
        if (is_array($dirs)) {
            foreach ($dirs as $dir => $uri) {
                if (is_dir($dir)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $fileInfo) {
                        if ($fileInfo->isFile() && $fileInfo->getExtension() == 'json') {
                            $settings = $wp_filesystem->get_contents($fileInfo->getPathname());
                            if ($settings) {
                                $settings = json_decode($settings, true);
                                $all_settings[$fileInfo->getPath()] = $settings;
                            }
                        }
                    }
                }
            }
        }
        update_option('azh-all-settings', $all_settings);
    }
    return apply_filters('azh-all-settings', $all_settings);
}

function azh_get_content_settings($content) {
    $md5 = md5($content);
    $content_settings = get_option('azh-content-settings', array());
    $user = wp_get_current_user();
    if (in_array('administrator', (array) $user->roles) || !isset($content_settings[$md5])) {
        $pattern = get_shortcode_regex(array('azh_post'));
        $content = preg_replace_callback("/$pattern/i", 'do_shortcode_tag', $content);
        preg_match_all('/(data-section|data-element)=[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        if (is_array($matches)) {
            foreach ($matches[2] as $section) {
                $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
                if (is_array($dirs)) {
                    foreach ($dirs as $dir => $uri) {
                        if (is_dir($dir)) {
                            if (file_exists($dir . '/' . $section)) {
                                $folders = explode('/', $section);
                                $subdir = '';
                                foreach ($folders as $folder) {
                                    $subdir = $subdir . '/' . $folder;
                                    if (!isset($content_settings[$md5][$dir . $subdir]) && is_dir($dir . $subdir)) {
                                        if (file_exists($dir . $subdir . '/azh_settings.json')) {
                                            azh_filesystem();
                                            global $wp_filesystem;
                                            $settings = $wp_filesystem->get_contents($dir . $subdir . '/azh_settings.json');
                                            if ($settings) {
                                                $settings = json_decode($settings, true);
                                                $content_settings[$md5][$dir . $subdir] = $settings;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        update_option('azh-content-settings', $content_settings);
    }

    return isset($content_settings[$md5]) ? $content_settings[$md5] : false;
}

function azh_get_visible_widgets() {
    global $wp_widget_factory;
    $settings = array();
    $widget_areas = wp_get_sidebars_widgets();
    foreach ($widget_areas as $widget_area => $widgets) {
        if (empty($widgets)) {
            continue;
        }

        if (!is_array($widgets)) {
            continue;
        }

        if ('wp_inactive_widgets' == $widget_area) {
            continue;
        }

        foreach ($widgets as $position => $widget_id) {
            // Find the conditions for this widget.
            if (preg_match('/^(.+?)-(\d+)$/', $widget_id, $matches)) {
                $id_base = $matches[1];
                $widget_number = intval($matches[2]);
            } else {
                $id_base = $widget_id;
                $widget_number = null;
            }

            $wp_widget = null;
            foreach ($wp_widget_factory->widgets as $widget_class => $widget_object) {
                if ($widget_object->id_base == $id_base) {
                    $wp_widget = $widget_object;
                }
            }
            if (!isset($settings[$id_base])) {
                $settings[$id_base] = get_option('widget_' . $id_base);
            }

            // New multi widget (WP_Widget)
            if (!is_null($widget_number)) {
                //$settings[$id_base][$widget_number]
                //$widget_areas[$widget_area][$position]
            }
            // Old single widget
            else if (!empty($settings[$id_base])) {
                //$widget_areas[$widget_area][$position]
            }
        }
    }
    return $settings;
}

function azh_get_widgets_content() {
    static $widgets_content = false;
    if ($widgets_content === false) {
        $widgets_content = '';
        $visible_widgets = azh_get_visible_widgets();
        foreach ($visible_widgets as $id_base => $widgets) {
            if ($id_base == 'azh_widget') {
                foreach ($widgets as $i => $settings) {
                    if (isset($settings['post']) && is_numeric($settings['post'])) {
                        $widget_post = get_post($settings['post']);
                        $widgets_content .= azh_get_post_content($widget_post);
                    }
                }
            }
        }
    }
    return $widgets_content;
}

function azh_get_included_widgets_content($post_content) {
    static $widgets_content = false;
    if ($widgets_content === false) {
        $widgets_content = '';
        preg_match_all('/' . get_shortcode_regex(array('azh_post')) . '/s', $post_content, $matches);
        if (!empty($matches)) {
            foreach ($matches[3] as $atts) {
                $atts = shortcode_parse_atts($atts);
                if ($atts['id'] && is_numeric($atts['id'])) {
                    $widget_post = get_post($atts['id']);
                    $widgets_content .= azh_get_post_content($widget_post);
                }
            }
        }
    }
    return $widgets_content;
}

function azh_get_post_settings($post = false) {
    global $azh_content;
    if ($azh_content) {
        return azh_get_content_settings($azh_content);
    } else {
        if (!$post) {
            $post = get_post();
        }
        if ($post) {
            $post_content = azh_get_post_content($post);
            if (empty($post_content)) {
                return azh_get_content_settings(azh_get_widgets_content() . azh_get_included_widgets_content($post->post_content) . $post->post_content);
            } else {
                return azh_get_content_settings(azh_get_widgets_content() . azh_get_included_widgets_content($post_content) . $post_content);
            }
        } else {
            return false;
        }
    }
}

function azh_get_content_scripts($content) {
    $cache = get_option('azh-get-content-scripts', array());
    $md5 = md5($content);
    $user = wp_get_current_user();
    if (in_array('administrator', (array) $user->roles) && WP_DEBUG || !isset($cache[$md5])) {
        $post_scripts = array('paths' => array(), 'css' => array(), 'js' => array());
        $pattern = get_shortcode_regex(array('azh_post'));
        $content = preg_replace_callback("/$pattern/i", 'do_shortcode_tag', $content);
        preg_match_all('/(data-section|data-element)=[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        if (is_array($matches)) {
            foreach ($matches[2] as $section_element) {
                $dirs = apply_filters('azh_directory', array_combine(array(get_template_directory() . '/azh'), array(get_template_directory_uri() . '/azh')));
                if (is_array($dirs)) {
                    foreach ($dirs as $dir => $uri) {
                        if (is_dir($dir)) {
                            if (file_exists($dir . '/' . $section_element)) {
                                $post_scripts['paths'][$section_element] = $dir . '/' . $section_element;
                                $folders = explode('/', $section_element);
                                $subdir = '';
                                foreach ($folders as $folder) {
                                    $subdir = $subdir . '/' . $folder;
                                    if (is_dir($dir . $subdir)) {
                                        if (file_exists($dir . $subdir . '/azh_settings.json')) {
                                            $iterator = new DirectoryIterator($dir . $subdir);
                                            foreach ($iterator as $fileInfo) {
                                                if ($fileInfo->isFile() && $fileInfo->getExtension() == 'css') {
                                                    $post_scripts['css'][$dir . $subdir . '/' . $fileInfo->getFilename()] = $uri . $subdir . '/' . $fileInfo->getFilename();
                                                }
                                            }
                                        }
                                        if (file_exists($dir . $subdir . '/azh_settings.json')) {
                                            $iterator = new DirectoryIterator($dir . $subdir);
                                            foreach ($iterator as $fileInfo) {
                                                if ($fileInfo->isFile() && $fileInfo->getExtension() == 'js') {
                                                    $post_scripts['js'][$dir . $subdir . '/' . $fileInfo->getFilename()] = $uri . $subdir . '/' . $fileInfo->getFilename();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $cache[$md5] = $post_scripts;
        update_option('azh-get-content-scripts', $cache);
    }
    $post_scripts = $cache[$md5];
    $post_scripts = apply_filters('azh_get_content_scripts', $post_scripts, $content);
    return $post_scripts;
}

function azh_get_post_scripts() {
    global $azh_content;
    if ($azh_content) {
        return azh_get_content_scripts($azh_content);
    } else {
        $post = get_post();
        if ($post) {
            $post_content = azh_get_post_content($post);
            if (empty($post_content)) {
                return azh_get_content_scripts(azh_get_widgets_content() . azh_get_included_widgets_content($post->post_content) . $post->post_content);
            } else {
                return azh_get_content_scripts(azh_get_widgets_content() . azh_get_included_widgets_content($post_content) . $post_content);
            }
        } else {
            return azh_get_content_scripts(azh_get_widgets_content());
        }
    }
}

global $azh_current_post_stack;
$azh_current_post_stack = array();
add_action('the_post', 'azh_the_post');

function azh_the_post($post) {
    if (is_object($post) && get_class($post) == 'WP_Post') {
        global $azh_current_post_stack;
        $index = count($azh_current_post_stack);
        while ($index) {
            $index--;
            if (isset($azh_current_post_stack[$index]->ID)) {
                if ($azh_current_post_stack[$index]->ID == $post->ID) {
                    array_splice($azh_current_post_stack, $index);
                }
            }
        }
        $azh_current_post_stack[] = $post;
    }
}

function azh_get_earliest_current_post($post_type, $equal = true) {
    global $azh_current_post_stack;
    $post = null;
    $index = 0;
    $post_type = (array) $post_type;
    while ($index < count($azh_current_post_stack)) {
        if ($equal) {
            if (in_array($azh_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azh_current_post_stack[$index];
                break;
            }
        } else {
            if (!in_array($azh_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azh_current_post_stack[$index];
                break;
            }
        }
        $index++;
    }
    if (is_null($post)) {
        $post = apply_filters('azh_get_earliest_current_post', $post, $post_type, $equal);
    }
    return $post;
}

function azh_get_closest_current_post($post_type, $equal = true) {
    global $azh_current_post_stack;
    $post = null;
    $index = count($azh_current_post_stack);
    $post_type = (array) $post_type;
    while ($index) {
        $index--;
        if ($equal) {
            if (in_array($azh_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azh_current_post_stack[$index];
                break;
            }
        } else {
            if (!in_array($azh_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azh_current_post_stack[$index];
                break;
            }
        }
    }
    if (is_null($post)) {
        $post = apply_filters('azh_get_closest_current_post', $post, $post_type, $equal);
    }
    return $post;
}

function azh_is_current_post($id) {
    global $azh_current_post_stack;
    $current_post = reset($azh_current_post_stack);
    return $current_post && is_object($current_post) && (is_single() || is_page()) && ($current_post->ID == $id);
}

function azh_get_first_shortcode($content, $first_shortcode) {
    preg_match_all('/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
        foreach ($matches as $shortcode) {
            if ($first_shortcode === $shortcode[2]) {
                $pos = strpos($content, $shortcode[0]);
                if ($pos !== false)
                    return $shortcode[0];
            }
        }
    }
    return false;
}

function azh_post_video_url() {
    $embed = azh_get_first_shortcode(get_the_content(''), 'embed');
    if ($embed) {
        preg_match('/' . get_shortcode_regex(array('embed')) . '/s', $embed, $matches);
        return trim($matches[5]);
    } else {
        return trim(get_post_meta(get_the_ID(), '_video', true));
    }
}

function azh_add_image_size($size) {
    if (!has_image_size($size) && !in_array($size, array('thumbnail', 'medium', 'large'))) {
        $size_array = explode('x', $size);
        if (count($size_array) == 2) {
            add_image_size($size, $size_array[0], $size_array[1], true);
        }
    }
}

function azh_get_attachment_url($attachment_id, $size) {
    azh_add_image_size($size);

    $metadata = wp_get_attachment_metadata($attachment_id);
    if (is_array($metadata)) {
        $regenerate = false;
        $size_array = explode('x', $size);
        if (count($size_array) == 2) {
            $regenerate = true;
            if (isset($metadata['width']) && isset($metadata['height'])) {
                if ((intval($metadata['width']) < intval($size_array[0])) && (intval($metadata['height']) < intval($size_array[1]))) {
                    $regenerate = false;
                }
            } else {
                $regenerate = false;
            }
        }
        if ($regenerate && (!isset($metadata['sizes']) || !isset($metadata['sizes'][$size]))) {
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $meta => $data) {
                    azh_add_image_size($meta);
                }
            }
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/post.php');
            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id)));
            $metadata = wp_get_attachment_metadata($attachment_id);
        }
    }
    $image = wp_get_attachment_image_src($attachment_id, $size);
    if (empty($image)) {
        $image = wp_get_attachment_image_src($attachment_id, 'full');
    }
    return $image[0];
}

function azh_get_post_gallery_urls($size) {
    $gallery = get_post_gallery(get_the_ID(), false);
    if (is_array($gallery)) {
        if (isset($gallery['ids'])) {
            $attachment_ids = explode(",", $gallery['ids']);
            $urls = array();
            foreach ($attachment_ids as $attachment_id) {
                $urls[] = azh_get_attachment_url($attachment_id, $size);
            }
            return $urls;
        }
    }
    $attachment_ids = get_post_meta(get_the_ID(), '_gallery', true);
    if ($attachment_ids) {
        $attachment_ids = explode(",", $attachment_ids);
        $urls = array();
        foreach ($attachment_ids as $attachment_id) {
            $urls[] = azh_get_attachment_url($attachment_id, $size);
        }
        return $urls;
    }
    $attachment_ids = get_children(array('post_parent' => get_the_ID(), 'fields' => 'ids', 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
    if (!empty($attachment_ids)) {
        $urls = array();
        foreach ($attachment_ids as $attachment_id) {
            $urls[] = azh_get_attachment_url($attachment_id, $size);
        }
        return $urls;
    }
    if (is_array($gallery)) {
        if (isset($gallery['src']) && is_array($gallery['src'])) {
            return $gallery['src'];
        }
    }
    return array();
}

add_action('widgets_init', 'azh_widgets_init');

function azh_widgets_init() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array('name' => esc_html__('AZEXO Canvas header', 'AZEXO'), 'id' => "azh_header", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '', 'after_title' => ''));
        register_sidebar(array('name' => esc_html__('AZEXO Canvas footer', 'AZEXO'), 'id' => "azh_footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '', 'after_title' => ''));
    }
}

add_filter('azh_settings_google_fonts', 'azh_settings_google_fonts', 10, 2);

function azh_settings_google_fonts($google_fonts, $settings) {
    if (isset($settings['main-google-font'])) {
        $google_fonts[] = $settings['main-google-font'];
    }
    if (isset($settings['menu-google-font'])) {
        $google_fonts[] = $settings['menu-google-font'];
    }
    if (isset($settings['header-google-font'])) {
        $google_fonts[] = $settings['header-google-font'];
    }
    return $google_fonts;
}

function azh_get_google_fonts($azh_settings = false, $post = false) {
    $settings = get_option('azh-settings');

    $all_google_fonts = explode("\n", $settings['google-fonts'] ? $settings['google-fonts'] : '');

    $settings_google_fonts = apply_filters('azh_settings_google_fonts', array(), $settings);
    foreach ($settings_google_fonts as $font) {
        $all_google_fonts[] = str_replace(' ', '+', $font) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
    }
    if ($post) {
        $post_google_fonts = apply_filters('azh_post_google_fonts', array(), $post);
        foreach ($post_google_fonts as $font) {
            $all_google_fonts[] = str_replace(' ', '+', $font) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
        }
    }

    if ($azh_settings && is_array($azh_settings)) {
        foreach ($azh_settings as $dir_settings) {
            if (isset($dir_settings['google-fonts'])) {
                $dir_settings_google_fonts = explode("\n", $dir_settings['google-fonts']);
                $all_google_fonts = array_merge($all_google_fonts, $dir_settings_google_fonts);
            }
        }
    }
    $google_fonts = array();
    if (is_array($all_google_fonts)) {
        $all_google_fonts = array_unique($all_google_fonts);
        foreach ($all_google_fonts as $font_family) {
            if (!empty($font_family)) {
                $font = explode(':', $font_family);
                if (!isset($google_fonts[$font[0]])) {
                    $google_fonts[$font[0]] = array();
                }
                if (count($font) == 2) {
                    $weights = explode(',', $font[1]);
                    foreach ($weights as $weight) {
                        if (!isset($google_fonts[$font[0]][$weight])) {
                            $google_fonts[$font[0]][$weight] = true;
                        }
                    }
                }
            }
        }
    }
    return $google_fonts;
}

function azh_get_google_fonts_url($azh_settings = false, $content = false, $post = false) {
    global $azh_google_fonts, $azh_google_fonts_locale_subsets;
    $fonts_url = false;
    $google_fonts = azh_get_google_fonts($azh_settings, $post);
    $font_families = array();
    foreach ($google_fonts as $font_family => $weights) {
        if ('off' !== esc_html__('on', $font_family . ' font: on or off', 'azh')) {
            $font_families[] = $font_family . ':' . implode(',', array_keys($weights));
        }
    }
    if ($content) {
        $pattern = '/font-family: \'(' . implode('|', $azh_google_fonts) . ')\'/';
        $fonts = array();
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $match) {
                $fonts[] = $match;
            }
        }
        $fonts = array_unique($fonts);
        foreach ($fonts as $font) {
            $font_families[] = $font . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
        }
    }
    if (!empty($font_families)) {
        $subset = 'latin,latin-ext';
        if (isset($azh_google_fonts_locale_subsets[get_locale()])) {
            $subset = $azh_google_fonts_locale_subsets[get_locale()];
        }
        $query_args = array(
            'family' => implode(urlencode('|'), $font_families),
            'subset' => $subset,
        );
        $fonts_url = add_query_arg($query_args, (is_ssl() ? 'https' : 'http') . '://fonts.googleapis.com/css');
    }
    return $fonts_url;
}

add_action('wp_ajax_azh_get_scripts_urls', 'azh_get_scripts_urls');

function azh_get_scripts_urls() {
    if (isset($_POST['content'])) {
        $scripts = azh_get_content_scripts(stripslashes($_POST['content']));
        $fonts_url = azh_get_google_fonts_url(azh_get_content_settings(stripslashes($_POST['content'])));
        if ($fonts_url) {
            $scripts['css'][] = $fonts_url;
        }
        $scripts['css'] = array_merge($scripts['css'], azh_get_icons_fonts_urls(stripslashes($_POST['content'])));
        print json_encode($scripts);
    }
    die();
}

function azh_parse_form_field($field, &$fields) {
    $name = trim($field->name);
    $name = str_replace(array('[', ']'), '', $name);
    $fields[$name] = array(
        'tag' => $field->tag,
        'label' => $name,
        'type' => isset($field->type) ? $field->type : '',
        'options' => array(),
    );
    if ($field->tag == 'select') {
        $options = array();
        foreach ($field->children() as $option) {
            $options[$option->value] = $option->innertext;
        }
        $fields[$name]['options'] = $options;
    }
}

function azh_get_form_fields_from_content($content) {
    include_once(AZH_DIR . 'simple_html_dom.php' );
    $fields = array();
    $content = azh_replaces($content);
    $content = azh_generate_ids($content);
    $content = azh_remove_comments($content);
    $html = str_get_html($content);
    $fields = array();
    if ($html) {
        foreach ($html->find('[name]') as $field) {
            azh_parse_form_field($field, $fields);
        }
    }
    return $fields;
}

function azh_get_forms_from_content($content) {
    include_once(AZH_DIR . 'simple_html_dom.php' );
    $forms = array();
    $content = azh_replaces($content);
    $content = azh_generate_ids($content);
    $content = azh_remove_comments($content);
    $html = str_get_html($content);
    if ($html) {
        foreach ($html->find('form') as $form) {
            $fields = array();
            if (count($form->find('input[name="form_title"]')) > 0) {
                $form_title = $form->find('input[name="form_title"]', 0);
                foreach ($form->find('[name]') as $field) {
                    azh_parse_form_field($field, $fields);
                }
                $forms[$form_title->value] = $fields;
            }
        }
    }
    return $forms;
}

function azh_get_forms_from_page($page) {
    return array_merge_recursive(azh_get_forms_from_content(azh_get_post_content($page)), apply_filters('azh_get_forms_from_page', array(), $page));
}

function azh_get_forms_from_pages() {
    $user_id = get_current_user_id();
    if ($user_id) {
        static $forms = array();
        if (isset($forms[$user_id])) {
            return $forms[$user_id];
        }
        $forms[$user_id] = array();
        $pages = get_posts(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'author' => $user_id,
            'ignore_sticky_posts' => 1,
            'no_found_rows' => 1,
            'posts_per_page' => '-1',
            'meta_query' => array(
                array(
                    'key' => 'azh',
                    'value' => 'azh',
                )
            )
        ));
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $forms[$user_id] = array_merge($forms[$user_id], azh_get_forms_from_page($page));
            }
        }
        $azh_widgets = get_posts(array(
            'post_type' => 'azh_widget',
            'post_status' => 'publish',
            'author' => $user_id,
            'ignore_sticky_posts' => 1,
            'no_found_rows' => 1,
            'posts_per_page' => '-1',
        ));
        if (!empty($azh_widgets)) {
            foreach ($azh_widgets as $azh_widget) {
                $forms[$user_id] = array_merge($forms[$user_id], azh_get_forms_from_page($azh_widget));
            }
        }
        return $forms[$user_id];
    }
    return false;
}

add_filter('wp_kses_allowed_html', 'azh_kses_allowed_html', 10, 2);

function azh_kses_allowed_html($allowedposttags, $context) {
    if ($context == 'post') {
        //if (isset($allowedposttags['form'])) {
        $allowedposttags['form']['data-azh-form'] = 1;
        //}
    }
    return $allowedposttags;
}

add_shortcode('azh_home_url', 'azh_home_url');

function azh_home_url($atts, $content = null, $tag = null) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        return addslashes(azh_shortcode_restore($atts, $content, $tag));
    } else {
        return esc_url(home_url(isset($atts['path']) ? $atts['path'] : ''));
    }
}

add_shortcode('azh_get_search_query', 'azh_get_search_query');

function azh_get_search_query($atts, $content = null, $tag = null) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        return addslashes(azh_shortcode_restore($atts, $content, $tag));
    } else {
        return get_search_query();
    }
}

function azh_form($atts, $content = null, $tag = null) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        return addslashes(azh_shortcode_restore($atts, $content, $tag));
    } else {
        static $count = 0;
        $nonce = wp_create_nonce('azh-form-nonce-' . $count) . '|' . $count;
        $count++;
        $form_settings = $atts;
        if (!empty($content)) {
            //$form_settings['content'] = base64_decode($content);
            $form_settings['content'] = $content;
        }
        if (!empty($atts['autoresponder_body_template'])) {
            //$form_settings['autoresponder_body_template'] = base64_decode($atts['autoresponder_body_template']);
        }
        delete_transient($nonce);
        set_transient($nonce, $form_settings, HOUR_IN_SECONDS);
        return $nonce;
    }
}

function azh_recursive_sanitize_text_field($array) {
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = azh_recursive_sanitize_text_field($value);
        } else {
            $value = wp_unslash(sanitize_text_field($value));
        }
    }
    return $array;
}

add_action('wp_ajax_nopriv_azh_process_form', 'azh_process_form');
add_action('wp_ajax_azh_process_form', 'azh_process_form');

function azh_process_form() {
    $response = '';
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;
    $nonce = explode('|', $nonce);
    if (wp_verify_nonce($nonce[0], 'azh-form-nonce-' . $nonce[1])) {
        unset($_POST['action']);
        unset($_POST['nonce']);
        $form_settings = get_transient(implode('|', $nonce));

        $files = array();
        if (!empty($_FILES)) {
            if (!function_exists('wp_handle_upload')) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            foreach ($_FILES as $name => $file) {
                $attachments = array();
                if (is_array($file['name'])) {
                    foreach ($file['name'] as $key => $value) {
                        if ($file['name'][$key]) {
                            $attachments[] = array(
                                'name' => $file['name'][$key],
                                'type' => $file['type'][$key],
                                'tmp_name' => $file['tmp_name'][$key],
                                'error' => $file['error'][$key],
                                'size' => $file['size'][$key]
                            );
                        }
                    }
                } else {
                    $attachments[] = $file;
                }
                $files[$name] = array();
                foreach ($attachments as $attachment) {
                    $upload = wp_handle_upload($attachment, array('test_form' => false));
                    if ($upload && !isset($upload['error'])) {
                        $files[$name][] = $upload['file'];
                    }
                }
            }
        }

        $response = array('message' => esc_html__('Form submitted successfully', 'azh'), 'status' => 'success');
        $response = apply_filters('azh_process_form', $response, $files, $form_settings);

        $settings = get_option('azh-forms-settings');

        if (isset($settings['form-submission-notification']) && $settings['form-submission-notification'] == 'cron') {
            wp_schedule_single_event(time() + 5, 'azh_process_form_cron_event', array(
                'post_id' => (int) $_POST['post_id'],
                'fields' => azh_recursive_sanitize_text_field($_POST),
                'files' => $files,
                'form_settings' => $form_settings,
            ));
        } else {
            do_action('azh_process_form_cron_event', (int) $_POST['post_id'], azh_recursive_sanitize_text_field($_POST), $files, $form_settings);
        }

        if (isset($_POST['form_title'])) {
            if (shortcode_exists('cfdb-save-form-post')) {
                do_shortcode('[cfdb-save-form-post]');
            }
        }

        if (!empty($form_settings['success_redirect'])) {
            $response['status'] = 'redirect';
            $response['url'] = $form_settings['success_redirect'];
        }
        if (!empty($form_settings['success'])) {
            $response['message'] = $form_settings['success'];
        }
    } else {
        $response = array('message' => esc_html__('Does not permitted', 'azh'), 'status' => 'error');
    }
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        print json_encode($response);
        wp_die();
    } else {
        switch ($response['status']) {
            case 'redirect' :
                if (empty($response['url'])) {
                    exit(wp_redirect(get_permalink((int) $_POST['post_id'])));
                } else {
                    exit(wp_redirect($response['url']));
                }
                break;
            case 'success' :
                if (empty($_POST['success-redirect'])) {
                    exit(wp_redirect(get_permalink((int) $_POST['post_id'])));
                } else {
                    exit(wp_redirect($_POST['success-redirect']));
                }
                break;
            case 'error' :
                if (empty($_POST['error-redirect'])) {
                    exit(wp_redirect(get_permalink((int) $_POST['post_id'])));
                } else {
                    exit(wp_redirect($_POST['error-redirect']));
                }
                break;
            default:
                exit(wp_redirect(get_permalink((int) $_POST['post_id'])));
        }
    }
}

function azh_tokens($string, $fields) {
    $string = preg_replace_callback('#{([^}]+)}#', function($m) use ($fields) {
        if (isset($fields[strtolower($m[1])])) { // If it exists in our array            
            return $fields[strtolower($m[1])]; // Then replace it from our array
        } else {
            return $m[0]; // Otherwise return the whole match (basically we won't change it)
        }
    }, $string);
    return $string;
}

add_filter('wp_mail_from_name', 'azh_wp_mail_from_name');

function azh_wp_mail_from_name($from_name) {
    return get_bloginfo('name');
}

function azh_is_base64_encoded($data) {
    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function azh_mail($email, $from, $subject, $message, $fields, $files) {
    if (azh_is_base64_encoded($message)) {
        $message = base64_decode($message);
    }
    $subject = azh_tokens($subject, $fields);
    $message = azh_tokens($message, $fields);

    $attachments = array();
    foreach ($files as $name => $file) {
        $attachments = array_merge($attachments, $file);
        if (count($file) === 1) {
            $message = str_replace('{' . $name . '}', basename($file[0]), $message);
        }
    }

    $headers = array();
    $headers[] = 'from: ' . $from;
    wp_mail($email, $subject, $message, $headers, $attachments);
}

add_action('azh_process_form_cron_event', 'azh_form_notification', 10, 4);

function azh_form_notification($post_id, $fields, $files, $form_settings) {
    $form_title = false;
    if (isset($fields['form_title'])) {
        $form_title = $fields['form_title'];
    }
    $email = false;
    $subject = false;
    $message = false;
    if ($form_settings && $form_settings['to_email']) {
        $email = $form_settings['to_email'];
        $subject = isset($form_settings['subject_template']) ? $form_settings['subject_template'] : '';
        $message = isset($form_settings['content']) ? $form_settings['content'] : '';
    } else {
        $settings = get_option('azh-forms-settings');
        if ($settings) {
            if (!empty($form_title) && isset($settings[$form_title . '-to'])) {
                $email = $settings[$form_title . '-to'];
                $subject = isset($settings[$form_title . '-subject-template']) ? $settings[$form_title . '-subject-template'] : '';
                $message = isset($settings[$form_title . '-body-template']) ? $settings[$form_title . '-body-template'] : '';
            }
        }
    }
    if ($form_title) {
        //$forms = azh_get_forms_from_pages();
        //$fields = $forms[$form_title];            
    }
    if (!empty($email)) {
        azh_mail($email, get_bloginfo('admin_email'), $subject, $message, $fields, $files);
    }
    if (!empty($form_settings['autoresponder_to_email']) && !empty($form_settings['autoresponder_subject_template']) && !empty($form_settings['autoresponder_body_template'])) {
        $email = $form_settings['autoresponder_to_email'];
        $from = $form_settings['autoresponder_from_email'];
        $subject = $form_settings['autoresponder_subject_template'];
        $message = $form_settings['autoresponder_body_template'];

        $email = azh_tokens($email, $fields);

        if (empty($from)) {
            $from = get_bloginfo('admin_email');
        }

        azh_mail($email, $from, $subject, $message, $fields, $files);
    }
}

add_action('wp_ajax_azh_get_posts', 'azh_get_posts');
add_action('wp_ajax_nopriv_azh_get_posts', 'azh_get_posts');

function azh_get_posts() {
    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $posts = array();
            foreach ($_POST['ids'] as $id) {
                if (is_numeric($id)) {
                    $post = get_post($id);
                    if ($post) {
                        $meta = get_post_meta($id);
                        foreach ($meta as &$v) {
                            $v = reset($v);
                        }
                        $posts[$id] = array(
                            'post_thumbnail' => wp_get_attachment_url(get_post_thumbnail_id($id)),
                            'post' => $post,
                            'meta' => $meta,
                        );
                    }
                }
            }
            print json_encode($posts);
        }
    }
    wp_die();
}

function azh_get_element_width($element_file) {
    $all_settings = azh_get_all_settings();
    $element_width = '100%';
    foreach ($all_settings as $dir => $settings) {
        if (strpos($element_file, $dir) == 0) {
            if (isset($settings['elements-widths'])) {
                foreach ($settings['elements-widths'] as $element => $width) {
                    if ($element_file == $dir . '/' . $element) {
                        $element_width = $width;
                        break;
                    }
                }
            }
        }
    }
    return $element_width;
}

function azh_get_page_template($files) {
    $content = '';
    $library = azh_get_library();
    $files = explode('|', $files);
    foreach ($files as $file) {
        if (is_array($library['elements'])) {
            foreach ($library['elements'] as $element_file => $name) {
                if (strlen($element_file) - strlen($file) == strrpos($element_file, $file)) {
                    $content .= '<div data-element="' . esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/')) . '" style="margin: auto; max-width: ' . azh_get_element_width($element_file) . '">';
                    $content .= azh_get_file($element_file, $library['elements_uri'][$element_file]);
                    $content .= '</div>';
                    break;
                }
            }
        }
        if (is_array($library['sections'])) {
            foreach ($library['sections'] as $section_file => $name) {
                if (strlen($section_file) - strlen($file) == strrpos($section_file, $file)) {
                    $content .= '<div data-section="' . esc_attr(ltrim(str_replace($library['sections_dir'][$section_file], '', $section_file), '/')) . '">';
                    $content .= azh_get_file($section_file, $library['sections_uri'][$section_file]);
                    $content .= '</div>';
                    break;
                }
            }
        }
    }
    return $content;
}

add_action('wp_ajax_azh_restore_revision', 'azh_restore_revision');

function azh_restore_revision() {
    if (isset($_POST['post_id']) && is_numeric((int) $_POST['post_id'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $post_id = wp_restore_post_revision((int) $_POST['post_id']);
        }
    }
}

add_action('wp_ajax_azh_load_post', 'azh_load_post');

function azh_load_post() {
    if (isset($_POST['post_id']) && is_numeric((int) $_POST['post_id'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $content = '';
            $settings = array();
            $scripts = array();
            if (get_post_meta((int) $_POST['post_id'], 'azh', true)) {
                $content = get_post_meta((int) $_POST['post_id'], '_azh_content', true);
                $scripts = azh_get_content_scripts($content);


                global $post, $wp_query;
                $original = $post;
                $post = get_post((int) $_POST['post_id']);
                setup_postdata($post);
                $content = do_shortcode($content);
                $wp_query->post = $original;
                wp_reset_postdata();

                $settings = get_post_meta((int) $_POST['post_id'], '_azh_settings', true);
            }
            //wp_print_styles();
            //wp_print_scripts();
            print json_encode(array(
                'content' => $content,
                'settings' => $settings,
                'scripts' => $scripts,
            ));
        }
    }
    wp_die();
}

function azh_create_post($post) {
    $user_id = get_current_user_id();
    if ($user_id) {
        $new_post_id = wp_insert_post(array(
            'post_title' => sanitize_text_field($post['post_title']),
            'post_status' => (isset($post['post_status']) ? sanitize_text_field($post['post_status']) : 'publish'),
            'post_type' => sanitize_text_field($post['post_type']),
            'post_author' => $user_id
        ));
        if (isset($post['general_element'])) {
            $general_element = sanitize_text_field($post['general_element']);

            $default = '<div data-section="element"><div data-cloneable=""><div data-element="general/' . $general_element . '">';
            $library = azh_get_library();
            if (is_array($library['elements']) && is_array($library['general_elements'])) {
                foreach ($library['general_elements'] as $element_file => $name) {
                    if ($name == $general_element) {
                        if (isset($library['elements'][$element_file])) {
                            $default .= azh_get_file($element_file, $library['elements_uri'][$element_file], false);
                        }
                    }
                }
            }
            $default .= '</div></div></div>';
            azh_set_post_content($default, $new_post_id);
            update_post_meta($new_post_id, 'azh', 'azh');
        }
        if (isset($post['element'])) {
            $element = sanitize_text_field($post['element']);

            $default = '<div data-section="element"><div data-cloneable=""><div data-element="' . $element . '">';
            $library = azh_get_library();
            if (is_array($library['elements'])) {
                foreach ($library['elements'] as $element_file => $name) {
                    $path = ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/');
                    if ($path == $element) {
                        if (isset($library['elements'][$element_file])) {
                            $default .= azh_get_file($element_file, $library['elements_uri'][$element_file], false);
                        }
                    }
                }
            }
            $default .= '</div></div></div>';
            azh_set_post_content($default, $new_post_id);
            update_post_meta($new_post_id, 'azh', 'azh');
        }
        return $new_post_id;
    }
}

add_action('wp_ajax_azh_add_post', 'azh_add_post');

function azh_add_post() {
    if (isset($_POST['post_title']) && isset($_POST['post_type'])) {
        print azh_create_post($_POST);
    }
    wp_die();
}

function azh_clone_post($post) {
    $post_array = (array) $post;
    unset($post_array['ID']);
    $new_post_id = wp_insert_post($post_array);
    $data = get_post_meta($post->ID);
    foreach ($data as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }
    return $new_post_id;
}

add_action('wp_ajax_azh_duplicate_post', 'azh_duplicate_post');

function azh_duplicate_post() {
    if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $post = get_post((int) $_POST['post_id']);
            if (current_user_can('edit_post', $post->ID)) {
                print azh_clone_post($post);
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azh_remove_post', 'azh_remove_post');

function azh_remove_post() {
    if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $post = get_post((int) $_POST['post_id']);
            if (current_user_can('edit_post', $post->ID)) {
                wp_delete_post($post->ID);
            }
        }
    }
    wp_die();
}

add_action('wp_ajax_azh_update_post', 'azh_update_post');
add_action('wp_ajax_nopriv_azh_update_post', 'azh_update_post');

function azh_update_post() {
    if (isset($_POST['post']) && isset($_POST['post']['ID']) && is_numeric($_POST['post']['ID'])) {
        $user_id = get_current_user_id();
        if ($user_id) {
            $post = get_post((int) $_POST['post']['ID']);
            if (current_user_can('edit_post', $post->ID)) {
                if (count($_POST['post']) > 1) {
                    if (isset($_POST['post']['post_content'])) {
                        $post_content = $_POST['post']['post_content'];
                    }
                    $post_data = azh_recursive_sanitize_text_field($_POST['post']);
                    if (isset($_POST['post']['post_content'])) {
                        $post_data['post_content'] = $post_content;
                    }
                    wp_update_post($post_data);
                }
                if (isset($_POST['meta']) && is_array($_POST['meta'])) {
                    $post_meta = azh_recursive_sanitize_text_field($_POST['meta']);
                    foreach ($post_meta as $key => $value) {
                        update_post_meta((int) $_POST['post']['ID'], $key, $value);
                    }
                }
            }
        }
    }
    wp_die();
}

function azh_dec($encoded) {
    $decoded = "";
    $strlen = strlen($encoded);
    for ($i = 0; $i < strlen($encoded); $i++) {
        $b = ord($encoded[$i]);
        $a = $b ^ 7;
        $decoded .= chr($a);
    }
    return $decoded;
}

add_shortcode('azh_sections_library', 'azh_sections_library');

function azh_sections_library($atts, $content = null, $tag = null) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        return azh_shortcode_restore($atts, $content, $tag);
    } else {
        $sections_library = get_option('azh-sections-library-shortcode', array());
        if (empty($sections_library)) {
            ob_start();
            include(AZH_DIR . 'shortcodes/azh_sections_library.php' );
            $sections_library = ob_get_clean();
            update_option('azh-sections-library-shortcode', $sections_library);
        }
        azh_enqueue_icons($sections_library);
        return $sections_library;
    }
}

add_shortcode('azh_elements_library', 'azh_elements_library');

function azh_elements_library($atts, $content = null, $tag = null) {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        return azh_shortcode_restore($atts, $content, $tag);
    } else {
        $elements_library = get_option('azh-elements-library-shortcode', array());
        if (empty($elements_library)) {
            ob_start();
            include(AZH_DIR . 'shortcodes/azh_elements_library.php' );
            $elements_library = ob_get_clean();
            update_option('azh-elements-library-shortcode', $elements_library);
        }
        azh_enqueue_icons($elements_library);
        return $elements_library;
    }
}
