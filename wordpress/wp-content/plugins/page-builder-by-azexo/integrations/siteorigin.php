<?php
add_action('wp_enqueue_scripts', 'azh_siteorigin_enqueue_scripts');

function azh_siteorigin_enqueue_scripts() {
    if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
        global $wp_widget_factory;

        foreach ($wp_widget_factory->widgets as $class => $widget_obj) {
            if (!empty($widget_obj) && is_object($widget_obj) && is_subclass_of($widget_obj, 'SiteOrigin_Widget')) {
                ob_start();
                $widget_obj->widget(array(), array());
                ob_clean();
            }
        }
    }
}

add_action('admin_init', 'azh_siteorigin_admin_init');

function azh_siteorigin_admin_init() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        add_filter('siteorigin_widgets_is_preview', '__return_true');
    }
}

add_action('azh_wp_widget', 'azh_wp_widget_siteorigin');

function azh_wp_widget_siteorigin() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        if (function_exists('siteorigin_widget_print_styles')) {
            siteorigin_widget_print_styles();
            ?>
            <script>
                setTimeout(function () {
                    jQuery(window.sowb).trigger('setup_widgets');
                });
            </script>
            <?php
        }
    }
}

add_action('init', 'azh_siteorigin_init');

function azh_siteorigin_init() {
    if (!is_admin()) {
        register_post_status('hidden', array(
            'public' => true,
        ));
    }
}

add_filter('siteorigin_panels_widget_dialog_tabs', 'azh_siteorigin_widgets_tabs');

function azh_siteorigin_widgets_tabs($tabs) {
    $library = azh_get_library();
    if (is_array($library['elements'])) {
        foreach ($library['elements'] as $element_file => $name) {
            $preview = '';
            if (file_exists(str_replace('.htm', '.jpg', $element_file))) {
                $preview = str_replace('.htm', '.jpg', $element_file);
            }
            if (file_exists(str_replace('.htm', '.png', $element_file))) {
                $preview = str_replace('.htm', '.png', $element_file);
            }
            if (file_exists($preview)) {
                $preview = str_replace($library['elements_dir'][$element_file], $library['elements_uri'][$element_file], $preview);
            } else {
                $preview = false;
            }
            $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
            $parts = explode('/', $path);
            if (!in_array($parts[0], array('general', 'ajax', 'empty rows', 'forms')) && !in_array('forms', $parts) && !in_array('forms-black', $parts) && !in_array('forms-white', $parts)) {
                $tabs[ucfirst($parts[0])] = array(
                    'title' => ucfirst($parts[0]),
                    'filter' => array(
                        'groups' => array(ucfirst($parts[0]))
                    )
                );
            }
        }
    }

    return $tabs;
}

add_action('widgets_init', 'azh_siteorigin_widgets_init');

function azh_siteorigin_widgets_init() {
    if (basename($_SERVER["SCRIPT_FILENAME"], '.php') !== 'widgets') {
        if (class_exists('SiteOrigin_Widget')) {

            class AZEXO_SiteOrigin_Widget extends SiteOrigin_Widget {

                public $element_path;

                function __construct() {
                    global $azexo_siteorigin_widgets;

                    $id = get_class($this);
                    $id = str_replace('AZEXO_SiteOrigin_Widget_', '', $id);
                    $id = str_replace('_', '-', $id);
                    $this->element_path = $azexo_siteorigin_widgets[$id]['element_path'];

                    parent::__construct($id, $azexo_siteorigin_widgets[$id]['name'], array('panels_icon' => $azexo_siteorigin_widgets[$id]['icon'], 'panels_groups' => array($azexo_siteorigin_widgets[$id]['category'])));
                }

                function widget($args, $instance) {
                    print $args['before_widget'];
                    if (!empty($instance['azh_widget'])) {
                        print azh_post(array('id' => $instance['azh_widget']));
                    }
                    print $args['after_widget'];
                }

                function update($new_instance, $old_instance, $form_type = 'widget') {
                    if (empty($new_instance['azh_widget'])) {
                        $new_instance['azh_widget'] = azh_create_post(array(
                            'post_title' => 'SiteOrigin Widget',
                            'post_type' => 'azh_widget',
                            'post_status' => 'hidden',
                            'element' => $this->element_path,
                        ));
                    }
                    return $new_instance;
                }

                function form($instance, $form_type = 'widget') {
                    $defaults = array('azh_widget' => '');
                    $instance = wp_parse_args((array) $instance, $defaults);

                    if ($_REQUEST['action'] && $_REQUEST['action'] == 'so_panels_widget_form') {
                        $new_widget = false;
                        if (empty($instance['azh_widget'])) {
                            $new_widget = true;
                            $instance['azh_widget'] = azh_create_post(array(
                                'post_title' => 'SiteOrigin Widget',
                                'post_type' => 'azh_widget',
                                'post_status' => 'hidden',
                                'element' => $this->element_path,
                            ));
                        }

                        $id = uniqid();
                        $same_url = add_query_arg(array('azh' => 'customize', 'azhf' => '{post_id}'), get_edit_post_link($instance['azh_widget']));
                        $blank_url = add_query_arg(array('azh' => 'customize'), get_edit_post_link($instance['azh_widget']));
                        ?>
                        <p id="<?php print $id; ?>">
                            <label for="<?php echo esc_attr($this->get_field_id('azh_widget')); ?>"><?php esc_html_e('Editor:', 'azh'); ?></label>
                            <input id="<?php echo esc_attr($this->get_field_id('azh_widget')); ?>" name="<?php echo esc_attr($this->get_field_name('azh_widget')); ?>" type="hidden" value="<?php echo esc_attr($instance['azh_widget']); ?>" />

                            <a href="<?php print $same_url; ?>" class="button" target="_blank"><?php esc_html_e('On same page', 'azh') ?></a>
                            <a href="<?php print $blank_url; ?>" class="button" target="_blank"><?php esc_html_e('On blank page', 'azh') ?></a>
                        </p>        
                        <script>
                            jQuery(function ($) {
                                var new_widget = <?php print ($new_widget ? 'true' : 'false'); ?>;
                                var post_id = $('#post_ID').val();
                                $('#<?php print $id; ?> a').hide();
                                if (post_id) {
                                    $('#<?php print $id; ?> a').each(function () {
                                        $(this).attr('href', $(this).attr('href').replace('{post_id}', post_id));
                                        $(this).show();
                                        $(this).on('click', function () {
                                            function update_and_open() {
                                                $.post(ajaxurl, {
                                                    action: 'azh_update_post',
                                                    post: {
                                                        post_content: $('#content').val(),
                                                        ID: post_id
                                                    },
//                                                    meta: {
//                                                        'panels_data': $('input[name="panels_data"]').val()
//                                                    }
                                                }, function (data) {
                                                    window.open($button.attr('href'), '_blank');
                                                });
                                            }
                                            var $button = $(this);
                                            if(new_widget) {
                                                $('#content').one('change', update_and_open);
                                            } else {
                                                setTimeout(update_and_open);
                                            }                                            
                                            $('.so-panels-dialog-wrapper > .so-panels-dialog > .so-toolbar .so-close').trigger('click');
                                            return false;
                                        });
                                    });
                                }
                            });
                        </script>
                        <?php
                    }
                }

            }

            function azh_class_alias($original, $alias) {
                eval("class $alias extends $original {}");
            }

            global $azexo_siteorigin_widgets;
            $azexo_siteorigin_widgets = array();
            $library = azh_get_library();
            if (is_array($library['elements'])) {
                foreach ($library['elements'] as $element_file => $name) {
                    $preview = '';
                    if (file_exists(str_replace('.htm', '.jpg', $element_file))) {
                        $preview = str_replace('.htm', '.jpg', $element_file);
                    }
                    if (file_exists(str_replace('.htm', '.png', $element_file))) {
                        $preview = str_replace('.htm', '.png', $element_file);
                    }
                    if (file_exists($preview)) {
                        $preview = str_replace($library['elements_dir'][$element_file], $library['elements_uri'][$element_file], $preview);
                    } else {
                        $preview = false;
                    }
                    $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
                    $parts = explode('/', $path);
                    if (!in_array($parts[0], array('general', 'ajax', 'empty rows', 'forms')) && !in_array('forms', $parts) && !in_array('forms-black', $parts) && !in_array('forms-white', $parts)) {
                        $id = 'azh-' . preg_replace('/[^\w\d]/', '-', $path);

                        $azexo_siteorigin_widgets[$id] = array(
                            'element_path' => $path,
                            'name' => str_replace(array('.htm'), '', $name),
                            'icon' => $id,
                            'category' => ucfirst($parts[0]),
                        );

                        siteorigin_widget_register($id, __FILE__, 'AZEXO_SiteOrigin_Widget_' . str_replace('-', '_', $id));
                        azh_class_alias('AZEXO_SiteOrigin_Widget', 'AZEXO_SiteOrigin_Widget_' . str_replace('-', '_', $id));
                    }
                }
            }
        }
    }
}


add_filter('wp_insert_post_data', 'azh_siteorigin_insert_post_data', 10, 2);

function azh_siteorigin_insert_post_data($data, $postarr) {
    if (class_exists('SiteOrigin_Widget')) {
        preg_match_all('/&quot;azh_widget&quot;:&quot;(\d+)&quot;/', wp_unslash($data['post_content']), $matches);
        if ($matches && !empty($matches[1])) {
            $azh_widgets = array();
            foreach ($matches[1] as $post_id) {
                if (!isset($azh_widgets[$post_id])) {
                    $azh_widgets[$post_id] = 0;
                }
                $azh_widgets[$post_id] = $azh_widgets[$post_id] + 1;
            }
            $replaces = array();
            foreach ($azh_widgets as $post_id => $count) {
                if ($count > 1) {
                    $post = get_post($post_id);
                    $replaces[$post_id] = array($post_id);
                    for ($i = 1; $i < $count; $i++) {
                        $replaces[$post_id][] = azh_clone_post($post);
                    }
                }
            }
            if (!empty($replaces)) {
                $data['post_content'] = wp_slash(preg_replace_callback('/&quot;azh_widget&quot;:&quot;(\d+)&quot;/', function($m) use (&$replaces) {
                            if ($replaces[$m[1]]) {
                                return '&quot;azh_widget&quot;:&quot;' . array_pop($replaces[$m[1]]) . '&quot;';
                            } else {
                                return $m[0];
                            }
                        }, wp_unslash($data['post_content'])));
            }
        }
    }
    return $data;
}

add_action('before_delete_post', 'azh_siteorigin_before_delete_post');

function azh_siteorigin_before_delete_post($post_id) {
    if (class_exists('SiteOrigin_Widget')) {
        $post = get_post($post_id);
        preg_match_all('/&quot;azh_widget&quot;:&quot;(\d+)&quot;/', $post->post_content, $matches);
        if ($matches && !empty($matches[1])) {
            foreach ($matches[1] as $azh_widget) {
                wp_delete_post((int) $azh_widget);
            }
        }
    }
}