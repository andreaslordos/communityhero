<?php
add_filter('azh_wp_widget_class', 'azh_fix_namespace_escaping', 5);

function azh_fix_namespace_escaping($class) {
    return preg_replace('/\\\\+/', '\\', $class);
}

add_shortcode('azh_wp_widget', 'azh_wp_widget');

function azh_wp_widget($attr, $content) {
    $attr = shortcode_atts(array(
        'class' => false,
        'id' => '',
            ), $attr, 'azg_widget');

    $attr['class'] = html_entity_decode($attr['class']);
    $attr['class'] = apply_filters('azh_wp_widget_class', $attr['class']);

    global $wp_widget_factory;
    if (!empty($attr['class']) && isset($wp_widget_factory->widgets[$attr['class']])) {
        $the_widget = $wp_widget_factory->widgets[$attr['class']];
        
        $data = json_decode(base64_decode($content, ENT_QUOTES), true);

        $widget_args = !empty($data['args']) ? $data['args'] : array();
        $widget_instance = !empty($data['instance']) ? $data['instance'] : array();
        if (empty($widget_instance)) {
            $widget_instance = $data;
        }
        
        $widget_args = wp_parse_args(array(
            'widget_id' => 'wp-widget-' . $the_widget->id_base,
            'before_widget' => sprintf('<div id="%1$s" class="widget %2$s">', $the_widget->id, ltrim(str_replace('-', '_', $the_widget->option_name), '_')),
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title"><h3>',
            'after_title' => '</h3></div>',
                ), $widget_args);
        ob_start();
        $the_widget->widget($widget_args, $widget_instance);
        do_action('azh_wp_widget');
        return ob_get_clean();
    }
}

add_action('admin_enqueue_scripts', 'azh_wp_widget_enqueue_admin_scripts');

function azh_wp_widget_enqueue_admin_scripts() {
    global $wp_widget_factory;
    $original_post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
    foreach ($wp_widget_factory->widgets as $class => $widget_obj) {
        ob_start();
        $return = $widget_obj->form(array());
        if (method_exists($widget_obj, 'enqueue_admin_scripts')) {
            $widget_obj->enqueue_admin_scripts();
        }
        ob_clean();
        if (method_exists($widget_obj, 'render_control_template_scripts')) {
            $widget_obj->render_control_template_scripts();
        }
    }
    $GLOBALS['post'] = $original_post;
}

function azh_is_js_widget($widget) {
    $js_widgets = array(
        'WP_Widget_Media_Audio',
        'WP_Widget_Media_Image',
        'WP_Widget_Media_Video',
        'WP_Widget_Text',
    );

    $is_js_widget = in_array(get_class($widget), $js_widgets) &&
            // Need to check this for `WP_Widget_Text` which was not a JS widget before 4.8
            method_exists($widget, 'render_control_template_scripts');

    return $is_js_widget;
}

function azh_wp_widget_get_form($widget, $instance, $raw = false, $widget_number = '{$id}') {
    global $wp_widget_factory;
    $form = '';
    $the_widget = !empty($wp_widget_factory->widgets[$widget]) ? $wp_widget_factory->widgets[$widget] : false;
    if (is_a($the_widget, 'WP_Widget')) {
        if ($raw) {
            $instance = $the_widget->update($instance, $instance);
        }
        $the_widget->id = 'temp';
        $the_widget->number = $widget_number;

        ob_start();
        if (azh_is_js_widget($the_widget)) {
            ?>
            <div class="widget-inside media-widget-control">
                <div class="form">
                    <div class="widget-content">
                        <?php
                    }
                    $return = $the_widget->form($instance);
                    if (azh_is_js_widget($the_widget)) {
                        ?>
                    </div>
                    <input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($the_widget->id_base); ?>" />
                    <input type="hidden" class="widget-id" value="widget-<?php echo esc_attr($widget_number); ?>">
                </div>
            </div>
            <?php
        }
        $form = ob_get_clean();
        $exp = preg_quote($the_widget->get_field_name('____'));
        $exp = str_replace('____', '(.*?)', $exp);
        $form = preg_replace('/' . $exp . '/', 'widgets[' . preg_replace('/\$(\d)/', '\\\$$1', $widget_number) . '][$1]', $form);
    }
    return $form;
}

add_action('init', 'azh_wp_widget_init');

function azh_wp_widget_init() {
    global $wp_widget_factory;
    $classes = array();
    foreach ($wp_widget_factory->widgets as $class => $widget) {
        $classes[$widget->name] = $class;
    }
    azh_add_element(array(
        "name" => esc_html__('Widget', 'azh'),
        "category" => esc_html__('WordPress', 'azh'),
        "base" => "azh_wp_widget",
        "image" => AZH_URL . '/images/widget.png',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Widget type', 'azh'),
                'param_name' => 'class',
                'value' => $classes,
            ),
            array(
                'type' => 'widget_settings',
                'heading' => esc_html__('Settings', 'azh'),
                'param_name' => 'content',
            ),
        ),
            ), 'azh_wp_widget');
}

add_action('wp_ajax_wp_widget_form', 'azh_wp_widget_form');

function azh_wp_widget_form() {
    if (isset($_POST['widget'])) {
        $widget = $_POST['widget'];
        $instance = isset($_POST['instance']) ? $_POST['instance'] : '';
        $instance = json_decode(base64_decode($instance, ENT_QUOTES), true);        
        print azh_wp_widget_get_form($widget, $instance);
    }
    wp_die();
}
