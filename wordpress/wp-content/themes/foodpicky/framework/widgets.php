<?php
add_action('widgets_init', 'azexo_register_widgets');

function azexo_register_widgets() {
    register_widget('AZEXO_Title');
    register_widget('AZEXO_Post');
    register_widget('AZEXO_Taxonomy');
    register_widget('AZEXO_Dashboard_Links');
    register_widget('AZEXO_Breadcrumb_Widget');
}

class AZEXO_Title extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_title', AZEXO_FRAMEWORK . ' - Page title');
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }


        global $post, $wp_query;
        $original = $post;
        if (azexo_get_closest_current_post('page')) {
            $post = azexo_get_closest_current_post('page');
        } else if (azexo_get_closest_current_post(array('vc_widget', 'azh_widget'), false)) {
            $post = azexo_get_closest_current_post(array('vc_widget', 'azh_widget'), false);
        }
        if ($original->ID != $post->ID) {
            setup_postdata($post);
        }

        get_template_part('template-parts/general', 'title');

        if ($original->ID != $post->ID) {
            $wp_query->post = $original;
            wp_reset_postdata();
        }



        print $args['after_widget'];
    }

}

class AZEXO_Post extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_post', AZEXO_FRAMEWORK . ' - One post');
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        if (!empty($instance['post'])) {
            if ($instance['full'] == 'on') {
                global $post, $wp_query;
                $original = $post;
                $post = get_post($instance['post']);
                setup_postdata($post);
                $template_name = $instance['template'];
                print '<div class="scoped-style">' . azexo_get_post_wpb_css($instance['post']);
                include(get_template_directory() . '/content.php');
                print '</div>';
                $wp_query->post = $original;
                wp_reset_postdata();
            } else {
                print azexo_get_post_content($instance['post']);
            }
        } else {
            if ($instance['full'] == 'on') {
                $template_name = $instance['template'];
                print '<div class="scoped-style">' . azexo_get_post_wpb_css();
                include(get_template_directory() . '/content.php');
                print '</div>';
            } else {
                print azexo_get_post_content();
            }
        }

        print $args['after_widget'];
    }

    function update($new_instance, $old_instance) {
        $instance = parent::update($new_instance, $old_instance);
        $instance['full'] = $new_instance['full'];
        return $instance;
    }

    function form($instance) {
        $defaults = array('post' => '', 'title' => '', 'template' => 'widget_post', 'full' => 'off');
        $instance = wp_parse_args((array) $instance, $defaults);
        global $azexo_templates;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'foodpicky'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
        </p><p>            
            <label for="<?php echo esc_attr($this->get_field_id('post')); ?>"><?php esc_html_e('Post ID:', 'foodpicky'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('post')); ?>" name="<?php echo esc_attr($this->get_field_name('post')); ?>" type="text" value="<?php echo esc_attr($instance['post']); ?>" />
        </p>
        <p>    
            <input class="checkbox" type="checkbox" <?php checked($instance['full'], 'on'); ?> id="<?php echo esc_attr($this->get_field_id('full')); ?>" name="<?php echo esc_attr($this->get_field_name('full')); ?>" /> 
            <label for="<?php echo esc_attr($this->get_field_id('full')); ?>"><?php esc_html_e('Full post', 'foodpicky'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('template')); ?>"><?php esc_html_e('Post template:', 'foodpicky'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('template')); ?>" name="<?php echo esc_attr($this->get_field_name('template')); ?>">
                <?php
                foreach ($azexo_templates as $slug => $name) :
                    ?>
                    <option value="<?php echo esc_attr($slug) ?>" <?php selected($slug, $instance['template']) ?>><?php echo esc_attr($name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>        
        <?php
    }

}

class AZEXO_Taxonomy extends WP_Widget {

    public function __construct() {
        $widget_ops = array('classname' => 'widget_categories', 'description' => esc_html__("A list or dropdown of categories.", 'foodpicky'));
        parent::__construct('azexo_taxonomy', AZEXO_FRAMEWORK . ' - Taxonomy', $widget_ops);
    }

    public function widget($args, $instance) {

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Categories', 'foodpicky') : $instance['title'], $instance, $this->id_base);

        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array(
            'class' => 'azexo-taxonomy-dropdown',
            'orderby' => 'name',
            'show_count' => $c,
            'hierarchical' => $h,
            'taxonomy' => $instance['taxonomy']
        );

        if ($d) {
            static $first_dropdown = true;

            $dropdown_id = ( $first_dropdown ) ? 'cat' : "{$this->id_base}-dropdown-{$this->number}";
            $first_dropdown = false;

            echo '<label class="screen-reader-text" for="' . esc_attr($dropdown_id) . '">' . $title . '</label>';

            $cat_args['show_option_none'] = esc_html__('Please select', 'foodpicky');
            $cat_args['id'] = $dropdown_id;

            /**
             * Filter the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             */
            wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            ?>

            <?php
        } else {
            ?>
            <ul>
                <?php
                $cat_args['title_li'] = '';

                /**
                 * Filter the arguments for the Categories widget.
                 *
                 * @since 2.8.0
                 *
                 * @param array $cat_args An array of Categories widget options.
                 */
                wp_list_categories(apply_filters('widget_categories_args', $cat_args));
                ?>
            </ul>
            <?php
        }

        print $args['after_widget'];
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;
        $instance['taxonomy'] = $new_instance['taxonomy'];
        return $instance;
    }

    public function form($instance) {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => '', 'taxonomy' => 'category'));
        $title = esc_attr($instance['title']);
        $count = isset($instance['count']) ? (bool) $instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset($instance['dropdown']) ? (bool) $instance['dropdown'] : false;

        $taxonomies = get_taxonomies(array(), 'objects');
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'foodpicky'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>"><?php esc_html_e('Taxonomy:', 'foodpicky'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>" name="<?php echo esc_attr($this->get_field_name('taxonomy')); ?>">
                <?php
                foreach ($taxonomies as $slug => $taxonomy) :
                    ?>
                    <option value="<?php echo esc_attr($slug) ?>" <?php selected($slug, $instance['taxonomy']) ?>><?php echo esc_attr($taxonomy->label); ?></option>
                <?php endforeach; ?>
            </select>
        </p>        

        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>"<?php checked($dropdown); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php esc_html_e('Display as dropdown', 'foodpicky'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked($count); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Show post counts', 'foodpicky'); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked($hierarchical); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php esc_html_e('Show hierarchy', 'foodpicky'); ?></label>
        </p>
        <?php
    }

}

class AZEXO_Dashboard_Links extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_dashboard_links', AZEXO_FRAMEWORK . ' - Dashboard Links');
    }

    function print_links($links) {

        foreach ($links as $link) {
            print '<li class="' . ($link['active'] ? 'active' : '') . '">';
            print '<a href="' . esc_url($link['url']) . '">' . esc_html($link['title']) . '</a>';
            if (isset($link['children'])) {
                print '<ul class="children">';
                $this->print_links($link['children']);
                print '</ul>';
            }
            print '</li>';
        }
    }

    static public function is_visible($instance) {
        if (isset($instance['visible_if_active']) && $instance['visible_if_active']) {
            return azexo_is_dashboard();
        }
        return true;
    }

    function widget($args, $instance) {
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        print $args['before_widget'];
        if ($title) {
            print $args['before_title'] . $title . $args['after_title'];
        }

        print '<ul class="root">';
        $this->print_links(azexo_get_dashboard_links());
        print '</ul>';

        print $args['after_widget'];
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['visible_if_active'] = !empty($new_instance['visible_if_active']) ? 1 : 0;
        return $instance;
    }

    public function form($instance) {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = esc_attr($instance['title']);
        $visible_if_active = isset($instance['visible_if_active']) ? (bool) $instance['visible_if_active'] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'foodpicky'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>            
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('visible_if_active')); ?>" name="<?php echo esc_attr($this->get_field_name('visible_if_active')); ?>"<?php checked($visible_if_active); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('visible_if_active')); ?>"><?php esc_html_e('Visible if have active link', 'foodpicky'); ?></label><br />
        </p>
        <?php
    }

}

class AZEXO_Breadcrumb_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_breadcrumb', AZEXO_FRAMEWORK . ' - breadcrumb');
    }

    function widget($args, $instance) {
        print '<div class="widget azexo-breadcrumb">';
        azexo_breadcrumb();
        print '</div>';
    }

}

if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
    add_filter('widget_display_callback', 'azexo_filter_widget', 10, 2);
    add_filter('sidebars_widgets', 'azexo_sidebars_widgets');
}

function azexo_sidebars_widgets($widget_areas) {
    if (did_action('wp_loaded')) {
        global $wp_widget_factory;
        $settings = array();
        foreach ($widget_areas as $widget_area => $widgets) {
            if (empty($widgets))
                continue;

            if (!is_array($widgets))
                continue;

            if ('wp_inactive_widgets' == $widget_area)
                continue;

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
                    if (isset($settings[$id_base][$widget_number]) && false === azexo_filter_widget($settings[$id_base][$widget_number], $wp_widget)) {
                        unset($widget_areas[$widget_area][$position]);
                    }
                }

                // Old single widget
                else if (!empty($settings[$id_base]) && false === azexo_filter_widget($settings[$id_base], $wp_widget)) {
                    unset($widget_areas[$widget_area][$position]);
                }
            }
        }
    }
    return $widget_areas;
}

global $azexo_widgets_visibility;
$azexo_widgets_visibility = array(
    'show_on_dashboard' => esc_html__('Show only on dashboard', 'foodpicky'),
    'hide_on_dashboard' => esc_html__('Hide on dashboard', 'foodpicky'),
    'show_on_profiles_list' => esc_html__('Show only on profiles list', 'foodpicky'),
    'hide_on_profiles_list' => esc_html__('Hide on profiles list', 'foodpicky'),
    'show_on_shop' => esc_html__('Show only on shop', 'foodpicky'),
    'hide_on_shop' => esc_html__('Hide on shop', 'foodpicky'),
);

function azexo_filter_widget($instance, $wp_widget) {
    if ($instance) {
        if (method_exists($wp_widget, 'is_visible')) {
            return $wp_widget->is_visible($instance) ? $instance : false;
        }
        if (isset($instance['show_on_dashboard']) && $instance['show_on_dashboard']) {
            if (!azexo_is_dashboard()) {
                return false;
            }
        }
        if (isset($instance['hide_on_dashboard']) && $instance['hide_on_dashboard']) {
            if (azexo_is_dashboard()) {
                return false;
            }
        }
        global $wp_query;
        if (isset($instance['show_on_profiles_list']) && $instance['show_on_profiles_list']) {
            if (basename(get_page_template()) != 'profiles.php' && basename(get_page_template()) != 'vendors-profiles.php' && !azexo_is_post_type_query($wp_query, 'azl_profile')) {
                return false;
            }
        }
        if (isset($instance['hide_on_profiles_list']) && $instance['hide_on_profiles_list']) {
            if (basename(get_page_template()) == 'profiles.php' || basename(get_page_template()) == 'vendors-profiles.php' || azexo_is_post_type_query($wp_query, 'azl_profile')) {
                return false;
            }
        }
        if (isset($instance['show_on_shop']) && $instance['show_on_shop']) {
            if (function_exists('is_shop') && !is_shop()) {
                return false;
            }
        }
        if (isset($instance['hide_on_shop']) && $instance['hide_on_shop']) {
            if (function_exists('is_shop') && is_shop()) {
                return false;
            }
        }
    }
    return $instance;
}

add_action('in_widget_form', 'azexo_in_widget_form', 10, 3);

function azexo_in_widget_form($widget, $return, $instance) {
    global $azexo_widgets_visibility;
    print '<p>';
    foreach ($azexo_widgets_visibility as $field => $title) {
        $value = isset($instance[$field]) ? (bool) $instance[$field] : false;
        ?>
        <input type="checkbox" class="checkbox" id="<?php echo esc_attr($widget->get_field_id($field)); ?>" name="<?php echo esc_attr($widget->get_field_name($field)); ?>"<?php checked($value); ?> />
        <label for="<?php echo esc_attr($widget->get_field_id($field)); ?>"><?php print $title; ?></label>
        <br/>
        <?php
    }
    print '</p>';
}

add_filter('widget_update_callback', 'azexo_widget_update_callback', 10, 3);

function azexo_widget_update_callback($instance, $new_instance, $old_instance) {
    global $azexo_widgets_visibility;
    foreach ($azexo_widgets_visibility as $field => $title) {
        $instance[$field] = !empty($new_instance[$field]) ? 1 : 0;
    }
    return $instance;
}
