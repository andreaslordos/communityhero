<?php
add_action('init', 'azh_elementor_init');

function azh_elementor_init() {
    if (!is_admin()) {
        register_post_status('hidden', array(
            'public' => true,
        ));
    }
}

add_action('admin_print_footer_scripts', 'azh_elementor_print_footer_scripts');

function azh_elementor_print_footer_scripts() {
    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
        wp_register_script('azh-elementor', AZH_URL . '/js/elementor.js', array('jquery'), AZH_PLUGIN_VERSION, true);
        wp_print_scripts(array('azh-elementor'));
        $library = azh_get_library();
        if (is_array($library['elements'])) {
            print '<style>';
            foreach ($library['elements'] as $element_file => $name) {
                $preview = '';
                if (file_exists(str_replace('.htm', '.jpg', $element_file))) {
                    $preview = str_replace('.htm', '.jpg', $element_file);
                }
                if (file_exists(str_replace('.htm', '.png', $element_file))) {
                    $preview = str_replace('.htm', '.png', $element_file);
                }
                if (file_exists(str_replace('.htm', '.svg', $element_file))) {
                    $preview = str_replace('.htm', '.svg', $element_file);
                }
                if (file_exists($preview)) {
                    $preview = str_replace($library['elements_dir'][$element_file], $library['elements_uri'][$element_file], $preview);
                } else {
                    $preview = false;
                }
                if ($preview) {
                    $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
                    $icon = 'azh-' . preg_replace('/[^\w\d_]/', '-', $path);
                    print '.' . $icon . ' { background-image: url(' . $preview . ') !important; background-size: contain !important; background-position: center !important; display: block; background-repeat: no-repeat; height: 65px;}';
                    print "\n";
                }
            }
            print '</style>';
        }
    }
}

add_action('elementor/controls/controls_registered', 'azh_elementor_controls_registered', 10, 1);

function azh_elementor_controls_registered($controls_manager) {
    if (class_exists('Elementor\Base_UI_Control')) {

        class Control_AZEXO_Widget extends Elementor\Base_Data_Control {

            public function get_type() {
                return 'azh_widget';
            }

            public function content_template() {
                if (!defined('AZH_VERSION')) {
                    print __('Please install <a href="https://codecanyon.net/item/azexo-html-customizer/16350601">Page builder by AZEXO</a> plugin.', 'azh');
                    return;
                }
                $same_url = add_query_arg(array('azh' => 'customize', 'azhf' => get_the_ID()), get_edit_post_link());
                $blank_url = add_query_arg(array('azh' => 'customize'), get_edit_post_link());
                $same_url = str_replace('post=' . get_the_ID(), 'post={{{data.controlValue}}}', $same_url);
                $blank_url = str_replace('post=' . get_the_ID(), 'post={{{data.controlValue}}}', $blank_url);
                ?>
                <div class="elementor-control-field">
                    <label class="elementor-control-title">{{{ data.label }}}</label>

                    <input class="azexo-post-id" type="hidden" data-setting="{{{ data.name }}}" />

                    <# 
                    if(!data.controlValue) {
                    data.controlValue = '{{{data.controlValue}}}';
                    }
                    #>

                    <div class="elementor-control-input-wrapper">
                        <button data-element="{{{ data.element_path }}}" data-url="<?php print $same_url ?>" onclick="azh_elementor_click(this)" class="elementor-button elementor-button-default azh-elementor-button"><?php esc_html_e('On same page', 'azh') ?></button>
                        <button data-element="{{{ data.element_path }}}" data-url="<?php print $blank_url ?>" onclick="azh_elementor_click(this)" class="elementor-button elementor-button-default"><?php esc_html_e('On blank page', 'azh') ?></button>
                    </div>

                </div>
                <# if ( data.description ) { #>
                <div class="elementor-control-field-description">{{{ data.description }}}</div>
                <# } #>
                <?php
            }

        }

        $controls_manager->register_control('azh_widget', new Control_AZEXO_Widget());
    }
}

add_action('elementor/elements/categories_registered', 'azh_elementor_categories_registered');

function azh_elementor_categories_registered($elements_manager) {
    $library = azh_get_library();
    if (is_array($library['elements'])) {
        foreach ($library['elements'] as $element_file => $name) {
            $path = esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/'));
            $parts = explode('/', $path);
            if (!in_array($parts[0], array('general', 'ajax', 'empty rows', 'forms')) && !in_array('forms', $parts) && !in_array('forms-black', $parts) && !in_array('forms-white', $parts)) {
                $elements_manager->add_category(
                        ucfirst($parts[0]), array(
                    'title' => ucfirst($parts[0]),
                    'icon' => 'fa fa-plug',
                        )
                );
            }
        }
    }
}

add_action('elementor/widgets/widgets_registered', 'azh_elementor_widgets_registered');

function azh_elementor_widgets_registered() {
    if (class_exists('Elementor\Widget_Base')) {

        class Widget_AZEXO_Form extends Elementor\Widget_Base {

            public function get_name() {
                return 'azexo_form';
            }

            public function get_title() {
                return __('AZEXO Form', 'azh');
            }

            public function get_icon() {
                return 'eicon-form-horizontal';
            }

            public function get_categories() {
                return array('basic');
            }

            protected function _register_controls() {
                $this->start_controls_section(
                        'section_spacer', array(
                    'label' => __('AZEXO Form', 'azh'),
                        )
                );

                $this->add_control(
                        'post-id', array(
                    'label' => __('Edit form in new Window', 'azh'),
                    'type' => 'azh_widget',
                    'default' => '',
                    'label_block' => true,
                    'element_path' => 'general/form-container.htm',
                        )
                );

                $this->end_controls_section();
            }

            protected function render() {
                if (!defined('AZH_VERSION')) {
                    print __('Please install <a href="https://codecanyon.net/item/azexo-html-customizer/16350601">Page builder by AZEXO</a> plugin.', 'azh');
                    return;
                }
                $settings = $this->get_settings();
                if (!empty($settings['post-id'])) {
                    global $wp_styles;
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $elementor_wp_styles = $wp_styles;
                        $wp_styles = new WP_Styles();
                    }
                    $id = 'azh' . uniqid();
                    print '<div id="' . $id . '">';
                    print azh_post(array('id' => $settings['post-id']));
                    print '</div>';
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor' || isset($_POST['action']) && $_POST['action'] == 'elementor_ajax') {
                        wp_print_styles();
                        ?>
                        <script>
                            window.azh.frontend_init(jQuery(document.getElementById("<?php print $id; ?>")));
                        </script>                            
                        <?php
                    }
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $wp_styles = $elementor_wp_styles;
                    }
                }
            }

        }

        class Widget_AZEXO_Container extends Elementor\Widget_Base {

            public function get_name() {
                return 'azexo_container';
            }

            public function get_title() {
                return __('AZEXO Container', 'azh');
            }

            public function get_icon() {
                return 'eicon-form-horizontal';
            }

            public function get_categories() {
                return array('basic');
            }

            protected function _register_controls() {
                $this->start_controls_section(
                        'section_spacer', array(
                    'label' => __('AZEXO Container', 'azh'),
                        )
                );

                $this->add_control(
                        'post-id', array(
                    'label' => __('Edit container in new Window', 'azh'),
                    'type' => 'azh_widget',
                    'default' => '',
                    'label_block' => true,
                    'element_path' => 'empty rows/row.htm',
                        )
                );

                $this->end_controls_section();
            }

            protected function render() {
                if (!defined('AZH_VERSION')) {
                    print __('Please install <a href="https://codecanyon.net/item/azexo-html-customizer/16350601">Page builder by AZEXO</a> plugin.', 'azh');
                    return;
                }
                $settings = $this->get_settings();
                if (!empty($settings['post-id'])) {
                    global $wp_styles;
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $elementor_wp_styles = $wp_styles;
                        $wp_styles = new WP_Styles();
                    }
                    $id = 'azh' . uniqid();
                    print '<div id="' . $id . '">';
                    print azh_post(array('id' => $settings['post-id']));
                    print '</div>';
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor' || isset($_POST['action']) && $_POST['action'] == 'elementor_ajax') {
                        wp_print_styles();
                        ?>
                        <script>
                            window.azh.frontend_init(jQuery(document.getElementById("<?php print $id; ?>")));
                        </script>                            
                        <?php
                    }
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $wp_styles = $elementor_wp_styles;
                    }
                }
            }

        }

        class Widget_AZEXO_Element extends Elementor\Widget_Base {

            public $element_path = 'empty rows/row.htm';
            public $category = 'basic';

            public function get_name() {
                return 'azh-' . preg_replace('/[^\w\d]/', '-', $this->element_path);
            }

            public function get_title() {
                $element_path = explode('/', $this->element_path);
                return str_replace(array('.htm'), '', end($element_path));
            }

            public function get_icon() {
                return 'azh-' . preg_replace('/[^\w\d]/', '-', $this->element_path);
            }

            public function get_categories() {
                return array($this->category);
            }

            protected function _register_controls() {
                $this->start_controls_section(
                        'section_spacer', array(
                    'label' => __('AZEXO Element', 'azh'),
                        )
                );

                $this->add_control(
                        'post-id', array(
                    'label' => __('Edit element in new Window', 'azh'),
                    'type' => 'azh_widget',
                    'default' => '',
                    'label_block' => true,
                    'element_path' => $this->element_path,
                        )
                );

                $this->end_controls_section();
            }

            protected function render() {
                if (!defined('AZH_VERSION')) {
                    print __('Please install <a href="https://codecanyon.net/item/azexo-html-customizer/16350601">Page builder by AZEXO</a> plugin.', 'azh');
                    return;
                }
                $settings = $this->get_settings();
                if (!empty($settings['post-id'])) {
                    global $wp_styles;
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $elementor_wp_styles = $wp_styles;
                        $wp_styles = new WP_Styles();
                    }
                    $id = 'azh' . uniqid();
                    print '<div id="' . $id . '">';
                    print azh_post(array('id' => $settings['post-id']));
                    print '</div>';
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor' || isset($_POST['action']) && $_POST['action'] == 'elementor_ajax') {
                        wp_print_styles();
                        ?>
                        <script>
                            window.azh.frontend_init(jQuery(document.getElementById("<?php print $id; ?>")));
                        </script>                            
                        <?php
                    }
                    if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
                        $wp_styles = $elementor_wp_styles;
                    }
                }
            }

        }

        Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widget_AZEXO_Container());
        Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widget_AZEXO_Form());

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
                    $widget_type = new Widget_AZEXO_Element();
                    $widget_type->element_path = $path;
                    $widget_type->category = ucfirst($parts[0]);
                    Elementor\Plugin::instance()->widgets_manager->register_widget_type($widget_type);
                }
            }
        }
    }
}

add_action('before_delete_post', 'azh_elementor_before_delete_post');

function azh_elementor_delete_azh($elements) {
    foreach ($elements as $element) {
        if (isset($element['widgetType']) && in_array($element['widgetType'], array('azexo_container', 'azexo_form'))) {
            if (isset($element['settings']) && isset($element['settings']['post-id']) && is_numeric($element['settings']['post-id'])) {
                wp_delete_post($element['settings']['post-id']);
            }
        }
        if (isset($element['elements']) && is_array($element['elements'])) {
            azh_elementor_delete_azh($element['elements']);
        }
    }
}

function azh_elementor_before_delete_post($post_id) {
    $elementor_data = get_post_meta($post_id, '_elementor_data', true);
    if ($elementor_data) {
        $elementor_data = json_decode($elementor_data, true);
        if ($elementor_data && is_array($elementor_data)) {
            azh_elementor_delete_azh($elementor_data);
        }
    }
}
