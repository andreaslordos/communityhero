<?php
define('AZEXO_FRAMEWORK', 'AZEXO');
define('AZEXO_FRAMEWORK_VERSION', '1.27');

add_action('admin_notices', 'azexo_admin_notices');

function azexo_admin_notices() {
    if (version_compare(phpversion(), '5.3.6', '<')) {
        ?>
        <div class="error">
            <p><?php esc_html_e('PHP version must be >= 5.3.6', 'foodpicky'); ?></p>
        </div>
        <?php
    }
}

function azexo_create_azh_widget($title, $content) {
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_type' => 'azh_widget',
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
        'post_content' => $content,
            ), true);
    if (!is_wp_error($post_id)) {
        azh_set_post_content($content, $post_id);
        add_post_meta($post_id, 'azh', 'azh', true);
        return $post_id;
    }
    return false;
}

function azexo_pre_set_widget($sidebar, $name, $args = array()) {
    if (!$sidebars = get_option('sidebars_widgets')) {
        $sidebars = array();
    }

    // Create the sidebar if it doesn't exist.
    if (!isset($sidebars[$sidebar])) {
        $sidebars[$sidebar] = array();
    }

    // Check for existing saved widgets.
    if ($widget_opts = get_option("widget_$name")) {
        // Get next insert id.
        ksort($widget_opts);
        end($widget_opts);
        $insert_id = key($widget_opts);
    } else {
        // None existing, start fresh.
        $widget_opts = array('_multiwidget' => 1);
        $insert_id = 0;
    }
    if (!is_numeric($insert_id)) {
        $insert_id = 0;
    }
    // Add our settings to the stack.
    $widget_opts[++$insert_id] = $args;
    // Add our widget!
    $sidebars[$sidebar][] = "$name-$insert_id";

    update_option('sidebars_widgets', $sidebars);
    update_option("widget_$name", $widget_opts);
}

function azexo_clear_sidebar($sidebar) {
    $sidebars = get_option('sidebars_widgets');
    if (!$sidebars) {
        $sidebars = array();
    }
    $sidebars[$sidebar] = array();
    update_option('sidebars_widgets', $sidebars);    
}

function azexo_init_azh_settings() {
    $settings = get_option('azh-settings');
    if (is_child_theme()) {
        $azh_settings = get_stylesheet_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/azh_settings.json';
    } else {
        $azh_settings = get_template_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/azh_settings.json';
    }
    if ((!is_array($settings) || !isset($settings['azh-uri'])) && file_exists($azh_settings)) {
        azexo_filesystem();
        global $wp_filesystem;
        $extension_settings = $wp_filesystem->get_contents($azh_settings);
        $extension_settings = json_decode($extension_settings, true);
        $settings = array_merge((array)$settings, $extension_settings);
        update_option('azh-settings', $settings);
    }
}

function azexo_header_footer_install() {
    if (defined('AZH_VERSION')) {
        if (!get_option('azexo_header_footer_installed')) {
            azh_filesystem();
            global $wp_filesystem;
            if (is_child_theme()) {
                $header = get_stylesheet_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/header.html';
            } else {
                $header = get_template_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/header.html';
            }
            if (file_exists($header)) {
                $header = $wp_filesystem->get_contents($header);
                $header = azexo_create_azh_widget('header', $header);
                if ($header) {
                    azexo_clear_sidebar('header_sidebar');
                    azexo_pre_set_widget('header_sidebar', 'azh_widget', array('post' => $header));
                    $options = get_option(AZEXO_FRAMEWORK);
                    $options['header'] = array();
                    $options['show_page_title'] = false;
                    update_option(AZEXO_FRAMEWORK, $options);
                }
            }
            if (is_child_theme()) {
                $footer = get_stylesheet_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/footer.html';
            } else {
                $footer = get_template_directory() . '/data/' . ucfirst(azexo_get_skin()) . '/footer.html';
            }
            if (file_exists($footer)) {
                $footer = $wp_filesystem->get_contents($footer);
                $footer = azexo_create_azh_widget('footer', $footer);
                if ($footer) {
                    azexo_clear_sidebar('footer_sidebar');
                    azexo_pre_set_widget('footer_sidebar', 'azh_widget', array('post' => $footer));
                }
            }
            update_option('azexo_header_footer_installed', true);
        }
    } else {
        if (get_option('azexo_header_footer_installed') && is_admin()) {
            $options = get_option(AZEXO_FRAMEWORK);
            $options['skin'] = false;
            update_option(AZEXO_FRAMEWORK, $options);
            update_option('azexo_header_footer_installed', false);
            azexo_load_default_skin_options();            
        }
    }
}

add_action('after_setup_theme', 'azexo_after_setup_theme');

function azexo_after_setup_theme() {
    load_theme_textdomain('foodpicky', get_template_directory() . '/languages');
    add_theme_support('custom-logo');
    add_theme_support('post-formats', array('gallery', 'video'));
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    azexo_init_azh_settings();
    azexo_header_footer_install();
}

add_action('admin_enqueue_scripts', 'azexo_admin_scripts');

function azexo_admin_scripts() {
    wp_enqueue_script('azexo-admin', get_template_directory_uri() . '/js/admin.js', array('jquery'), AZEXO_FRAMEWORK_VERSION, true);
    $options = get_option(AZEXO_FRAMEWORK);
    wp_localize_script('azexo-admin', 'azexo', array(
        'framework_confirmation' => isset($options['framework_confirmation']) ? $options['framework_confirmation'] : 0,
        'templates_configuration' => esc_html__('Templates configuration', 'foodpicky'),
        'fields_configuration' => esc_html__('Fields configuration', 'foodpicky'),
        'post_types_settings' => esc_html__('Post types settings', 'foodpicky'),
        'woocommerce_templates_configuration' => esc_html__('WooCommerce templates configuration', 'foodpicky'),
        'section_alert' => esc_html__('Please click "Ok" to confirm this info. Theme not provide CSS styles for all possible configurations in this section. Please use configurations only provided with demo content. Of course you can use it with complete freedom, but in this case it may be necessary to add CSS styles. You can save confirmation of this alert in "AZEXO Options > General settings".', 'foodpicky'),
        'element_alert' => esc_html__('Please click "Ok" to confirm this info. Theme not provide CSS styles for all possible settings of this element. Please use settings only provided with demo content. Of course you can use it with complete freedom, but in this case it may be necessary to add CSS styles. You can save confirmation of this alert in "AZEXO Options > General settings".', 'foodpicky'),
    ));
}

function azexo_locate_template($template_names) {
    $located = '';
    foreach ((array) $template_names as $template_name) {
        if (!$template_name) {
            continue;
        }
        if (file_exists(get_stylesheet_directory() . '/' . $template_name)) {
            $located = get_stylesheet_directory() . '/' . $template_name;
            break;
        }
        if (file_exists(get_template_directory() . '/' . $template_name)) {
            $located = get_template_directory() . '/' . $template_name;
            break;
        }
    }
    return $located;
}

function azexo_is_empty($var) {
    return empty($var);
}

function azexo_get_templates() {
    $options = get_option(AZEXO_FRAMEWORK);
    global $azexo_templates;
    if (!isset($azexo_templates)) {
        $azexo_templates = array();
    }
    $azexo_templates = array_merge($azexo_templates, array(
        'post' => esc_html__('Post', 'foodpicky'), //default template
        'masonry_post' => esc_html__('Masonry post', 'foodpicky'), //fixed selector in JS
        'navigation_post' => esc_html__('Navigation post', 'foodpicky'), //fixed in full post navigation
        'related_post' => esc_html__('Related post', 'foodpicky'), //fixed in YARP template
    ));

    if (isset($options['templates']) && is_array($options['templates'])) {
        $options['templates'] = array_filter($options['templates']);
        if (!empty($options['templates'])) {
            $azexo_templates = array_merge($azexo_templates, array_combine(array_map('sanitize_title', $options['templates']), $options['templates']));
        }
    }

    $azexo_templates = apply_filters('azexo_templates', $azexo_templates);
    return $azexo_templates;
}

//add_action('admin0bar0menu', 'azexo_adminbarmenu', 999);

function azexo_adminbarmenu($wp_admin_bar) {
    $args = array(
        'id' => 'edit-links',
        'title' => esc_html__('Edit links', 'foodpicky'),
        'href' => '#',
        'meta' => array(
            'class' => 'active',
        ),
    );
    $wp_admin_bar->add_node($args);
}

function azexo_get_skin() {
    $options = get_option(AZEXO_FRAMEWORK);
    $skin = '';
    if (isset($options['skin'])) {
        if (is_child_theme()) {
            if (file_exists(get_stylesheet_directory() . '/less/' . $options['skin'] . '/skin.less')) {
                $skin = $options['skin'];
            }
        } else {
            if (file_exists(get_template_directory() . '/less/' . $options['skin'] . '/skin.less')) {
                $skin = $options['skin'];
            }
        }
    }
    if (empty($skin)) {
        if (is_child_theme()) {
            $skin = get_stylesheet();
        } else {
            $skin = get_template();
        }
    }
    return $skin;
}

function azexo_filesystem() {
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

add_action('init', 'azexo_load_default_skin_options', 12); // after options-init.php

function azexo_load_default_skin_options() {
    if (is_admin()) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (is_child_theme()) {
            $json_file = get_stylesheet_directory() . '/options/' . $options['skin'] . '.json';
        } else {
            $json_file = get_template_directory() . '/options/' . $options['skin'] . '.json';
        }
        if (is_child_theme()) {
            $php_file = get_stylesheet_directory() . '/options/' . $options['skin'] . '.php';
        } else {
            $php_file = get_template_directory() . '/options/' . $options['skin'] . '.php';
        }
        if (!isset($options['skin']) || empty($options['skin']) || (!file_exists($json_file) && !file_exists($php_file))) {
            if (is_child_theme()) {
                $skin = get_stylesheet();
            } else {
                $skin = get_template();
            }
            if (is_child_theme()) {
                $json_file = get_stylesheet_directory() . '/options/' . $skin . '.json';
            } else {
                $json_file = get_template_directory() . '/options/' . $skin . '.json';
            }
            if (is_child_theme()) {
                $php_file = get_stylesheet_directory() . '/options/' . $skin . '.php';
            } else {
                $php_file = get_template_directory() . '/options/' . $skin . '.php';
            }
            $options = false;
            if (file_exists($php_file)) {
                include($php_file);
            } else if (file_exists($json_file)) {
                azexo_filesystem();
                global $wp_filesystem;
                $file_contents = $wp_filesystem->get_contents($json_file);

                $options = json_decode($file_contents, true);
            }
            if ($options && is_array($options)) {
                if (get_option('azexo_header_footer_installed')) {
                    $options['header'] = array();
                    $options['show_page_title'] = false;
                }
                if (function_exists('get_redux_instance')) {
                    $redux = get_redux_instance(AZEXO_FRAMEWORK);
                    if(is_object($redux) && method_exists($redux ,'set_options')) {
                        $redux->set_options($options);
                    } else {
                        update_option(AZEXO_FRAMEWORK, $options);
                    }
                } else {
                    update_option(AZEXO_FRAMEWORK, $options);
                }
            }
        }
    }
}

function azexo_dynamic_css() {
    $css = '';

    global $azexo_category_fields;
    if ($azexo_category_fields) {
        $post_categories = get_categories();
        if (!empty($post_categories)) {
            foreach ($post_categories as $cat) {
                $cat_color = $azexo_category_fields->get_category_meta($cat->cat_ID, 'color');
                $css .= $cat_color ? 'a.' . esc_attr(str_replace('_', '-', $cat->slug)) . '[rel="category tag"], a.' . esc_attr(str_replace('_', '-', $cat->slug)) . '[rel="category"] { background-color:' . esc_attr($cat_color) . ' !important;}' : '';
            }
        }
    }

    $azexo_custom_css = get_option('azexo_wpb_shortcodes_custom_css', array());
    if (is_array($azexo_custom_css)) {
        foreach ($azexo_custom_css as $custom_css) {
            $css .= $custom_css;
        }
    }

    return $css;
}

add_filter('embed_defaults', 'azexo_embed_defaults');

function azexo_embed_defaults() {
    return array('width' => 1000, 'height' => 500);
}

function azexo_excerpt($content, $excerpt_length = false, $trim_by_words = true) {
    if (empty($excerpt_length)) {
        $excerpt_length = isset($options['excerpt_length']) ? $options['excerpt_length'] : 15;
    }
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['strip_excerpt']) && $options['strip_excerpt'] && is_numeric($excerpt_length)) {
        $excerpt = wp_strip_all_tags(strip_shortcodes($content));
        if ($trim_by_words) {
            $excerpt = wp_trim_words($excerpt, $excerpt_length);
        } else {
            $excerpt = substr($excerpt, 0, $excerpt_length) . '...';
        }
        return $excerpt;
    } else {
        return $content;
    }
}

function azexo_comment_excerpt($content) {
    $options = get_option(AZEXO_FRAMEWORK);
    $excerpt = wp_trim_words(wp_strip_all_tags(strip_shortcodes($content)), isset($options['comment_excerpt_length']) ? $options['comment_excerpt_length'] : 15);
    return $excerpt;
}

if (!isset($content_width)) {
    $content_width = 1;
}

class AZEXO_Walker_Comment extends Walker_Comment {

    protected function comment($comment, $depth, $args) {
        if ('div' == $args['style']) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        $options = get_option(AZEXO_FRAMEWORK);
        ?>
        <<?php print $tag; ?> <?php comment_class($this->has_children ? 'parent' : '' ); ?> id="comment-<?php comment_ID(); ?>">
        <?php if ('div' != $args['style']) : ?>
            <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <?php endif; ?>
            <div class="comment-author">
                <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>                
            </div>
            <div class="comment-data">
                <?php printf(wp_kses(__('<cite class="fn">%s</cite>', 'foodpicky'), array('cite' => array('class' => array()))), get_comment_author_link()); ?>
                <?php if ('0' == $comment->comment_approved) : ?>
                    <em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'foodpicky') ?></em>
                    <br />
                <?php endif; ?>
                <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID, $args)); ?>">
                        <?php
                        /* translators: 1: date, 2: time */
                        printf(esc_html__('%1$s at %2$s', 'foodpicky'), get_comment_date(), get_comment_time());
                        ?></a><?php edit_comment_link(esc_html__('(Edit)', 'foodpicky'), '&nbsp;&nbsp;', '');
                        ?>
                </div>
                <?php comment_text(get_comment_id(), array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                <div class="reply">
                    <?php comment_reply_link(array_merge($args, array('reply_text' => esc_html__('Reply', 'foodpicky'), 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                </div>
                <?php if (isset($options['comment_likes']) && $options['comment_likes']) : ?>
                    <div class="like"> <?php
                        if (function_exists('azpl_get_simple_likes_button')) {
                            print azpl_get_simple_likes_button($comment->comment_ID, true);
                        }
                        ?> </div>
                <?php endif; ?>                    
            </div>
            <?php if ('div' != $args['style']) : ?>
            </div>
        <?php endif; ?>
        <?php
    }

}

if (function_exists('register_nav_menus')) {
    register_nav_menus(array(
        'primary' => esc_html__('Top primary menu', 'foodpicky'),
        'secondary' => esc_html__('Secondary menu', 'foodpicky'),
    ));
}


add_filter('update_post_metadata', 'azexo_update_post_metadata', 10, 5);

function azexo_update_post_metadata($check, $object_id, $meta_key, $meta_value, $prev_value) {
    if ($meta_key == '_wpb_shortcodes_custom_css') {
        $azexo_custom_css = get_option('azexo_wpb_shortcodes_custom_css', array());
        $azexo_custom_css[$object_id] = $meta_value;
        update_option('azexo_wpb_shortcodes_custom_css', $azexo_custom_css);
    }
    return $check;
}

function azexo_get_post_wpb_css($id = NULL) {
    if ($id == NULL) {
        $id = get_the_ID();
    }

    $azexo_custom_css = get_option('azexo_wpb_shortcodes_custom_css', array());
    if (!isset($azexo_custom_css[$id])) {
        $azexo_custom_css[$id] = get_post_meta($id, '_wpb_shortcodes_custom_css', true);
        update_option('azexo_wpb_shortcodes_custom_css', $azexo_custom_css);

        $shortcodes_custom_css = $azexo_custom_css[$id];
        if (!empty($shortcodes_custom_css)) {
            return '<style data-type="vc_shortcodes-custom-css" scoped>' . $shortcodes_custom_css . '</style>';
        }
    }
    return '';
}

function azexo_replace_vc_ids($content) {
    $matches = array();
    preg_match_all('/tab\_id\=\"([^\"]+)\"/', $content, $matches);
    foreach ($matches[0] as $match) {
        $content = str_replace($match, 'tab_id="azexo-' . rand(0, 99999999) . '"', $content);
    }
    return $content;
}

global $azexo_current_post_stack;
$azexo_current_post_stack = array();
add_action('the_post', 'azexo_the_post');

function azexo_the_post($post) {
    global $azexo_current_post_stack;
    $index = count($azexo_current_post_stack);
    while ($index) {
        $index--;
        if ($azexo_current_post_stack[$index]->ID == $post->ID) {
            array_splice($azexo_current_post_stack, $index);
        }
    }
    $azexo_current_post_stack[] = $post;
}

add_filter('document_title_parts', 'azexo_document_title_parts');

function azexo_document_title_parts($title) {
    if (!isset($title['title']) || is_null($title['title'])) {
        global $azexo_queried_object;
        if (isset($azexo_queried_object) && is_object($azexo_queried_object) && property_exists($azexo_queried_object, 'post_title')) {
            $title['title'] = $azexo_queried_object->post_title;
        }
    }
    return $title;
}

add_filter('template_include', 'azexo_template_include');

function azexo_template_include($template) {
    if (is_singular() || is_home()) {
        global $azexo_current_post_stack, $wp_query;
        $queried_object = $wp_query->get_queried_object();
        if (is_object($queried_object)) {
            if (property_exists($queried_object, 'ID')) {
                $azexo_current_post_stack = array($queried_object);
            }
        }
    }
    return $template;
}

function azexo_get_earliest_current_post($post_type, $equal = true) {
    global $azexo_current_post_stack;
    $post = null;
    $index = 0;
    $post_type = (array) $post_type;
    while ($index < count($azexo_current_post_stack)) {
        if ($equal) {
            if (in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        } else {
            if (!in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        }
        $index++;
    }
    if (is_null($post)) {
        $post = apply_filters('azexo_get_earliest_current_post', $post, $post_type, $equal);
    }
    return $post;
}

function azexo_get_closest_current_post($post_type, $equal = true) {
    global $azexo_current_post_stack;
    $post = null;
    $index = count($azexo_current_post_stack);
    $post_type = (array) $post_type;
    while ($index) {
        $index--;
        if ($equal) {
            if (in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        } else {
            if (!in_array($azexo_current_post_stack[$index]->post_type, $post_type)) {
                $post = $azexo_current_post_stack[$index];
                break;
            }
        }
    }
    if (is_null($post)) {
        $post = apply_filters('azexo_get_closest_current_post', $post, $post_type, $equal);
    }
    return $post;
}

function azexo_is_current_post($id) {
    global $azexo_current_post_stack;
    $current_post = reset($azexo_current_post_stack);
    return $current_post && is_object($current_post) && (is_single() || is_page()) && ($current_post->ID == $id);
}

function azexo_get_post_content($id = NULL) {
    $content = '';
    if ($id == NULL) {
        $content = get_the_content('');
        $content = azexo_replace_vc_ids($content);
        $content = '<div class="scoped-style" data-content-id="' . esc_attr(get_the_ID()) . '">' . azexo_get_post_wpb_css(get_the_ID()) . apply_filters('the_content', $content) . '</div>';
    } else {
        global $post, $wp_query;
        $original = $post;
        $current_post = get_post($id);
        if ($current_post) {
            $wp_query->post = $current_post;
            wp_reset_postdata();
            $content = get_the_content('');
            $content = azexo_replace_vc_ids($content);
            $content = '<div class="scoped-style" data-content-id="' . esc_attr($id) . '">' . azexo_get_post_wpb_css($id) . apply_filters('the_content', $content) . '</div>';
            $wp_query->post = $original;
            wp_reset_postdata();
        }
    }

    return $content;
}

add_filter('nav_menu_link_attributes', 'azexo_nav_menu_link_attributes', 10, 4);

function azexo_nav_menu_link_attributes($atts, $item, $args, $depth) {
    if (strpos($atts['title'], 'mega') !== false) {
        $atts['title'] = str_replace('mega', '', $atts['title']);
        $atts['href'] = '#';
    }
    $atts['class'] = 'menu-link';
    return $atts;
}

add_filter('nav_menu_css_class', 'azexo_nav_menu_css_class', 10, 4);

function azexo_nav_menu_css_class($classes, $item, $args, $depth) {
    if (strpos($item->attr_title, 'mega') !== false && $depth == 0) {
        $classes[] = 'mega';
    }
    return $classes;
}

add_filter('widget_nav_menu_args', 'azexo_widget_nav_menu_args', 10, 3);

function azexo_widget_nav_menu_args($nav_menu_args, $nav_menu, $args) {
    $nav_menu_args['walker'] = new AZEXO_Walker_Nav_Menu();
    if (isset($args['vc']) && $args['vc']) {
        $nav_menu_args['vc'] = true;
        $nav_menu_args['menu_class'] = 'menu vc';
    }
    return $nav_menu_args;
}

class AZEXO_Walker_Nav_Menu extends Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        if (is_array($args) && isset($args['vc']) && $args['vc'] || is_object($args) && isset($args->vc) && $args->vc) {
            $output .= "\n$indent<ul class=\"sub-menu vc\">\n";
        } else {
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $item->additions = '';
        if (is_array($args)) {
            $args = (object) $args;
        }
        $item = apply_filters('azexo_menu_start_el', $item, $args);
        if (preg_match('/icon\(([^\)]*)\)/i', $item->attr_title, $icon)) {
            $item->attr_title = str_replace($icon[0], '', $item->attr_title);
            $args->link_before = ' <span class="' . $icon[1] . '"></span>';
        }
        if (is_array($item->classes)) {
            if (in_array('fa', $item->classes)) {
                $item->classes = array_diff($item->classes, array('fa'));
                $searchword = 'fa-';
                $matches = array_filter($item->classes, function($var) use ($searchword) {
                    return preg_match("/\b$searchword\b/i", $var);
                });
                foreach ($matches as $match) {
                    $item->classes = array_diff($item->classes, array($match));
                    $args->link_before = ' <span class="fa ' . $match . '"></span>';
                }
            }
            $searchword = 'ti-';
            $matches = array_filter($item->classes, function($var) use ($searchword) {
                return preg_match("/\b$searchword\b/i", $var);
            });
            foreach ($matches as $match) {
                $item->classes = array_diff($item->classes, array($match));
                $args->link_before = ' <span class="' . $match . '"></span>';
            }
        }
        if (isset($item->description) && !empty($item->description)) {
            $args->link_after = '<span class="description">' . $item->description . '</span>';
        }
        parent::start_el($output, $item, $depth, $args, $id);
        if (is_object($args)) {
            $args->link_before = '';
            $args->link_after = '';
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        if (strpos($item->attr_title, 'mega') !== false && $depth == 0) {
            $output .= '<div class="page">' . azexo_get_post_content($item->object_id) . '</div>';
        }
        $output .= $item->additions;
        $item->additions = '';
        $output .= "</li>\n";
    }

}

add_filter('widget_categories_args', 'azexo_widget_categories_args');

function azexo_widget_categories_args($args) {
    $args['walker'] = new AZEXO_Walker_Category();
    return $args;
}

function azexo_list_cats($name, $category = false) {
    if ($category) {
        if (function_exists('get_term_meta')) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            if ($thumbnail_id) {
                return wp_get_attachment_image($thumbnail_id) . '<span>' . $name . '</span>';
            }
        }
    }
    return $name;
}

class AZEXO_Walker_Category extends Walker_Category {

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
        $show_count = 0;
        if (isset($args['show_count'])) {
            $show_count = $args['show_count'];
            $args['show_count'] = 0;
        }

        add_filter('list_cats', 'azexo_list_cats', 10, 2);
        parent::start_el($output, $category, $depth, $args, $id);
        remove_filter('list_cats', 'azexo_list_cats', 10, 2);

        $args['show_count'] = $show_count;

        if ($show_count) {
            $output .= ' <span class="count">' . number_format_i18n($category->count) . '</span>';
        }
    }

}

add_filter('get_archives_link', 'azexo_get_archives_link', 10, 6);

function azexo_get_archives_link($link_html, $url, $text, $format, $before, $after) {
    $text = wptexturize($text);
    $url = esc_url($url);

    if ('link' == $format)
        $link_html = "\t<link rel='archives' title='" . esc_attr($text) . "' href='" . esc_url($url) . "' />\n";
    elseif ('option' == $format)
        $link_html = "\t<option value='" . esc_url($url) . "'>$before $text $after</option>\n";
    elseif ('html' == $format)
        $link_html = "\t<li><span class='before'>$before</span><a href='" . esc_url($url) . "'>$text</a><span class='after'>$after</span></li>\n";
    else // custom
        $link_html = "\t<span class='before'>$before</span><a href='" . esc_url($url) . "'>$text</a><span class='after'>$after</span>\n";

    return $link_html;
}

add_action('widgets_init', 'azexo_widgets_init');

function azexo_widgets_init() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array('name' => esc_html__('Right/Left sidebar', 'foodpicky'), 'id' => "sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Header sidebar', 'foodpicky'), 'id' => "header_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Middle sidebar', 'foodpicky'), 'id' => "middle_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Footer sidebar', 'foodpicky'), 'id' => "footer_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Dashboard sidebar', 'foodpicky'), 'id' => "dashboard_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
        register_sidebar(array('name' => esc_html__('Additional sidebar', 'foodpicky'), 'id' => "additional_sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
    }
}

function azexo_add_element($settings) {
    if (function_exists('vc_map')) {
        vc_map($settings);
        global $azh_shortcodes;
        if (isset($azh_shortcodes)) {
            $azh_shortcodes[$settings['base']] = $settings;
        }
    } else {
        if (function_exists('azh_add_element')) {
            azh_add_element($settings);
        }
    }
}

if (file_exists(get_template_directory() . '/framework/az_bookings.php') && function_exists('azb_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_bookings.php'));
}
if (file_exists(get_template_directory() . '/framework/az_group_buying.php') && function_exists('azgb_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_group_buying.php'));
}
if (file_exists(get_template_directory() . '/framework/az_listings.php') && function_exists('azl_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_listings.php'));
}
if (file_exists(get_template_directory() . '/framework/profiles.php') && function_exists('azl_plugins_loaded')) {
    require_once(get_theme_file_path('framework/profiles.php'));
}
if (file_exists(get_template_directory() . '/framework/az_query_form.php') && function_exists('azqf_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_query_form.php'));
}
if (file_exists(get_template_directory() . '/framework/az_vouchers.php') && function_exists('azv_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_vouchers.php'));
}
if (file_exists(get_template_directory() . '/framework/az_deals.php') && function_exists('azd_plugins_loaded')) {
    require_once(get_theme_file_path('framework/az_deals.php'));
}
if (file_exists(get_template_directory() . '/framework/woocommerce.php')) {
    require_once(get_theme_file_path('framework/woocommerce.php'));
}
if (file_exists(get_template_directory() . '/framework/azh.php')) {
    require_once(get_theme_file_path('framework/azh.php'));
}
if (file_exists(get_template_directory() . '/framework/contact-form-7.php')) {
    require_once(get_theme_file_path('framework/contact-form-7.php'));
}
if (file_exists(get_template_directory() . '/framework/class.category-custom-fields.php')) {
    require_once(get_theme_file_path('framework/class.category-custom-fields.php'));
}
if (file_exists(get_template_directory() . '/framework/class.breadcrumb.php')) {
    require_once(get_theme_file_path('framework/class.breadcrumb.php'));
}
require_once(get_theme_file_path('framework/google-fonts.php'));
if (is_admin()) {
    require_once(get_theme_file_path('framework/redux-extensions/loader.php'));
    require_once(get_theme_file_path('framework/options-init.php'));
    require_once(get_theme_file_path('tgm/class-tgm-plugin-activation.php'));
    require_once(get_theme_file_path('framework/tgm-init.php'));
}
require_once(get_theme_file_path('framework/options-wpml.php'));
if (file_exists(get_template_directory() . '/framework/widgets.php')) {
    require_once(get_theme_file_path('framework/widgets.php'));
}

add_action('init', 'azexo_init', 12); // after options-init.php

function azexo_init() {
    if (file_exists(get_template_directory() . '/framework/vc_extend.php')) {
        require_once(get_theme_file_path('framework/vc_extend.php'));
    }
    global $azexo_fields_post_types;
    if (!isset($azexo_fields_post_types)) {
        $azexo_fields_post_types = array();
    }
    global $azexo_post_fields;
    if (!isset($azexo_post_fields)) {
        $azexo_post_fields = array();
    }
    if (!empty($azexo_post_fields)) {
        $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_post_fields), array_fill(0, count(array_keys($azexo_post_fields)), 'post')));
    }

    $azexo_fields_post_types = apply_filters('azexo_fields_post_types', $azexo_fields_post_types);
}

function azexo_removeDemoModeLink() {
    if (class_exists('ReduxFrameworkPlugin')) {
        remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2);
        remove_action('admin_notices', array(ReduxFrameworkPlugin::get_instance(), 'admin_notices'));
    }
}

add_action('init', 'azexo_removeDemoModeLink');

add_filter('use_default_gallery_style', '__return_false');

if (is_admin()) {
    if (file_exists(trailingslashit(get_template_directory()) . 'framework/exporter/export.php')) {
        require_once(get_theme_file_path('framework/exporter/export.php'));
    }
    if (file_exists(trailingslashit(get_template_directory()) . 'framework/importer/import.php')) {
        require_once(get_theme_file_path('framework/importer/import.php'));
    }
    if (class_exists('OCDI_Plugin') && file_exists(trailingslashit(get_template_directory()) . 'framework/one-click-demo-import.php')) {
        require_once(get_theme_file_path('framework/one-click-demo-import.php'));
    }
}

function azexo_header_parts() {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['header'])) {
        foreach ((array) $options['header'] as $part) {

            $template_part = azexo_is_template_part_exists('template-parts/header', $part);
            if (!empty($template_part)) {
                get_template_part('template-parts/header', $part);
            } else {
                switch ($part) {
                    case 'logo':
                        ?>
                        <a class="site-title" href="<?php print esc_url(home_url('/')); ?>" rel="home">
                            <?php
                            if (function_exists('has_custom_logo') && has_custom_logo()):
                                $custom_logo_id = get_theme_mod('custom_logo');
                                if ($custom_logo_id) {
                                    $image_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                                }
                                ?>
                                <img src="<?php print esc_url($image_url); ?>" alt="<?php esc_attr_e('logo', 'foodpicky'); ?>">
                            <?php elseif (isset($options['logo']['url']) && !empty($options['logo']['url'])): ?>
                                <img src="<?php print esc_url($options['logo']['url']); ?>" alt="<?php esc_attr_e('logo', 'foodpicky'); ?>">
                            <?php else: ?>
                                <span class="title"><?php print esc_html(get_bloginfo('name')); ?></span>
                            <?php endif; ?>
                        </a>
                        <?php
                        break;
                    case 'search':
                        azexo_get_search_form();
                        break;
                    case 'mobile_menu_button':
                        ?>
                        <div class="mobile-menu-button"><span><i class="fa fa-bars"></i></span></div>                    
                        <?php
                        break;
                    case 'mobile_menu':
                        ?><nav class="site-navigation mobile-menu"><?php
                                    if (has_nav_menu('primary')) {
                                        wp_nav_menu(array(
                                            'theme_location' => 'primary',
                                            'menu_class' => 'nav-menu',
                                            'menu_id' => 'primary-menu-mobile',
                                            'walker' => new AZEXO_Walker_Nav_Menu(),
                                        ));
                                    }
                                    ?></nav><?php
                        break;
                    case 'primary_menu':
                        ?><nav class="site-navigation primary-navigation"><?php
                            if (has_nav_menu('primary')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'primary',
                                    'menu_class' => 'nav-menu',
                                    'menu_id' => 'primary-menu',
                                    'walker' => new AZEXO_Walker_Nav_Menu(),
                                ));
                            }
                            ?></nav><?php
                        break;
                    case 'secondary_menu':
                        ?><nav class="secondary-navigation"><?php
                            if (has_nav_menu('secondary')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'secondary',
                                    'menu_class' => 'nav-menu',
                                    'menu_id' => 'secondary-menu',
                                    'walker' => new AZEXO_Walker_Nav_Menu(),
                                ));
                            }
                            ?></nav><?php
                        break;
                    default:
                        break;
                }
            }
        }
    }
}

add_action('wp_insert_comment', 'azexo_insert_comment', 10, 2);

function azexo_insert_comment($id, $comment) {
    delete_post_meta($comment->comment_post_ID, 'last_comment_date');
    add_post_meta($comment->comment_post_ID, 'last_comment_date', $comment->comment_date);
}

function azexo_is_post_type_query($query, $post_type) {
    $post_types = $query->get('post_type');
    if (!is_array($post_types)) {
        $post_types = array($post_types);
    }

    $taxonomy = false;
    $taxonomy_names = get_object_taxonomies($post_type);
    foreach ($taxonomy_names as $taxonomy_name) {
        if ($query->get($taxonomy_name)) {
            $taxonomy = true;
            break;
        }
    }
    return (in_array($post_type, $post_types) && count($post_types) == 1) || $taxonomy;
}

add_action('pre_get_posts', 'azexo_pre_get_posts');

function azexo_pre_get_posts($query) {
    if ($query->is_main_query()) {
        $post_type = isset($query->query['post_type']) ? $query->query['post_type'] : 'post';
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }
        $options = get_option(AZEXO_FRAMEWORK);
        $orderby_value = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'menu_order';
        $order = explode('-', $orderby_value);
        $order = !empty($order[1]) ? $order[1] : '';
        $order = strtoupper($order);
        $order = ($order == 'DESC') ? 'DESC' : 'ASC';

        switch ($orderby_value) {
            case 'menu_order':
                if (isset($options[$post_type . '_custom_sorting']) && is_array($options[$post_type . '_custom_sorting'])) {
                    if (in_array('menu_order', $options[$post_type . '_custom_sorting'])) {
                        $query->set('orderby', 'menu_order title');
                        $query->set('order', $order);
                    }
                }
                break;
            case 'date':
                if (isset($options[$post_type . '_custom_sorting']) && is_array($options[$post_type . '_custom_sorting'])) {
                    if (in_array('date', $options[$post_type . '_custom_sorting'])) {
                        $query->set('orderby', 'date ID');
                        $query->set('order', $order);
                    }
                }
                break;
            default:
                if (isset($options[$post_type . '_custom_sorting_numeric_meta_keys']) && is_array($options[$post_type . '_custom_sorting_numeric_meta_keys'])) {
                    foreach ($options[$post_type . '_custom_sorting_numeric_meta_keys'] as $meta_key) {
                        if ($meta_key . '-asc' == $orderby_value) {
                            $query->set('orderby', 'meta_value_num');
                            $query->set('order', 'ASC');
                            $query->set('meta_key', $meta_key);
                        }
                        if ($meta_key . '-desc' == $orderby_value) {
                            $query->set('orderby', 'meta_value_num');
                            $query->set('order', 'DESC');
                            $query->set('meta_key', $meta_key);
                        }
                    }
                }
                break;
        }
    }
}

function azexo_before_list($post_type) {
    global $wp_query;
    $options = get_option(AZEXO_FRAMEWORK);
    $orderby_options = array(
        'menu_order' => esc_html__('Default sorting', 'foodpicky'),
        'date' => esc_html__('Sort by newness', 'foodpicky'),
    );
    if (isset($options[$post_type . '_custom_sorting']) && is_array($options[$post_type . '_custom_sorting'])) {
        $orderby_options = array_intersect_key($orderby_options, array_combine($options[$post_type . '_custom_sorting'], $options[$post_type . '_custom_sorting']));
    }
    if (isset($options[$post_type . '_custom_sorting_numeric_meta_keys']) && is_array($options[$post_type . '_custom_sorting_numeric_meta_keys'])) {
        foreach ($options[$post_type . '_custom_sorting_numeric_meta_keys'] as $meta_key) {
            if (!empty($meta_key)) {
                $orderby_options[esc_attr($meta_key) . '-desc'] = sprintf(esc_attr__('Sort by %s: high to low', 'foodpicky'), esc_attr($meta_key));
                $orderby_options[esc_attr($meta_key) . '-asc'] = sprintf(esc_attr__('Sort by %s: low to high', 'foodpicky'), esc_attr($meta_key));
            }
        }
    }
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'menu_order';

    if (isset($options[$post_type . '_before_list']) && is_array($options[$post_type . '_before_list']) && !empty($options[$post_type . '_before_list'])) {
        ?><div class="before-list"><?php
            foreach ((array) $options[$post_type . '_before_list'] as $part) {
                switch ($part) {
                    case 'result_count':
                        ?><p class="result-count"><?php
                        $paged = max(1, $wp_query->get('paged'));
                        $per_page = $wp_query->get('posts_per_page');
                        $total = $wp_query->found_posts;
                        $first = ( $per_page * $paged ) - $per_page + 1;
                        $last = min($total, $wp_query->get('posts_per_page') * $paged);

                        if ($total <= $per_page || -1 === $per_page) {
                            printf(_n('Showing the single result', 'Showing all %d results', $total, 'foodpicky'), $total);
                        } else {
                            printf(_nx('Showing the single result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, '%1$d = first, %2$d = last, %3$d = total', 'foodpicky'), $first, $last, $total);
                        }
                        ?></p><?php
                        break;
                    case 'ordering':
                        ?>
                        <form class="ordering" method="get">
                            <select name="orderby" class="orderby">
                                <?php foreach ($orderby_options as $id => $name) : ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected(esc_attr($orderby), $id); ?>><?php echo esc_html($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php
                            foreach ($_GET as $key => $val) {
                                if ('orderby' === $key || 'submit' === $key) {
                                    continue;
                                }
                                if (is_array($val)) {
                                    foreach ($val as $innerVal) {
                                        echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($innerVal) . '" />';
                                    }
                                } else {
                                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
                                }
                            }
                            ?>
                        </form>
                        <?php
                        break;
                    default:
                        break;
                }
            }
            ?></div><?php
    }
}

function azexo_paging_nav() {
    global $wp_query, $wp_rewrite;

    // Don't print empty markup if there's only one page.
    if ($wp_query->max_num_pages < 2) {
        return;
    }

    $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $query_args = array();
    $url_parts = explode('?', $pagenum_link);

    if (isset($url_parts[1])) {
        wp_parse_str($url_parts[1], $query_args);
    }

    $pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
    $pagenum_link = trailingslashit($pagenum_link) . '%_%';

    $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

    // Set up paginated links.
    $links = paginate_links(array(
        'base' => $pagenum_link,
        'format' => $format,
        'total' => $wp_query->max_num_pages,
        'current' => $paged,
        'mid_size' => 1,
        'add_args' => array_map('urlencode', $query_args),
        'prev_text' => '<i class="prev"></i>' . '<span>' . esc_html__('Previous', 'foodpicky') . '</span>',
        'next_text' => '<span>' . esc_html__('Next', 'foodpicky') . '</span>' . '<i class="next"></i>',
    ));

    if ($links) :
        ?>
        <nav class="navigation paging-navigation">
            <div class="pagination loop-pagination">
                <?php print $links; ?>
            </div><!-- .pagination -->
        </nav><!-- .navigation -->
        <?php
    endif;
}

function azexo_post_nav_full() {
    global $post;

    // Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
    $next = get_adjacent_post(false, '', false);
    $options = get_option(AZEXO_FRAMEWORK);
    if (!$next && !$previous)
        return;
    ?>
    <nav class="navigation post-navigation-full clearfix">
        <div class="prev-post">
            <?php
            global $post, $wp_query;
            $original = $post;
            $wp_query->post = $previous;
            wp_reset_postdata();

            $template_name = 'navigation_post';
            include(get_theme_file_path(apply_filters('azexo_post_template_path', 'content.php', $template_name)));

            $wp_query->post = $original;
            wp_reset_postdata();
            ?>
        </div>
        <div class="next-post">            
            <?php
            global $post, $wp_query;
            $original = $post;
            $wp_query->post = $next;
            wp_reset_postdata();

            $template_name = 'navigation_post';
            include(get_theme_file_path(apply_filters('azexo_post_template_path', 'content.php', $template_name)));

            $wp_query->post = $original;
            wp_reset_postdata();
            ?>
        </div>            
    </nav><!-- .navigation -->
    <?php
}

function azexo_post_nav() {
    global $post;

    // Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post($post->post_parent) : get_adjacent_post(false, '', true);
    $next = get_adjacent_post(false, '', false);
    $options = get_option(AZEXO_FRAMEWORK);
    if (!$next && !$previous)
        return;
    ?>
    <nav class="navigation post-navigation clearfix">
        <div class="nav-links">

            <?php previous_post_link('%link', '<i class="prev"></i><div class="prev-post"><span class="helper">' . (isset($options['post_navigation_previous']) ? $options['post_navigation_previous'] : '') . '</span><span class="title">%title</span></div>'); ?>
            <?php next_post_link('%link', '<i class="next"></i><div class="next-post"><span class="helper">' . (isset($options['post_navigation_next']) ? $options['post_navigation_next'] : '') . '</span><span class="title">%title</span></div>'); ?>

        </div><!-- .nav-links -->
    </nav><!-- .navigation -->
    <?php
}

function azexo_get_the_category_list($separator = '', $parents = '', $post_id = false) {
    global $wp_rewrite;
    if (!is_object_in_taxonomy(get_post_type($post_id), 'category')) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_category', '', $separator, $parents);
    }

    $categories = get_the_category($post_id);
    if (empty($categories)) {
        /** This filter is documented in wp-includes/category-template.php */
        return apply_filters('the_category', esc_html__('Uncategorized', 'foodpicky'), $separator, $parents);
    }

    $rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

    $thelist = '';
    if ('' == $separator) {
        $thelist .= '<ul class="post-categories">';
        foreach ($categories as $category) {
            $thelist .= "\n\t<li>";
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, true, $separator);
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a></li>';
                    break;
                case 'single':
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '"  ' . $rel . '>';
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, false, $separator);
                    $thelist .= $category->name . '</a></li>';
                    break;
                case '':
                default:
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a></li>';
            }
        }
        $thelist .= '</ul>';
    } else {
        $i = 0;
        foreach ($categories as $category) {
            if (0 < $i)
                $thelist .= $separator;
            switch (strtolower($parents)) {
                case 'multiple':
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, true, $separator);
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a>';
                    break;
                case 'single':
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>';
                    if ($category->parent)
                        $thelist .= get_category_parents($category->parent, false, $separator);
                    $thelist .= "$category->name</a>";
                    break;
                case '':
                default:
                    $thelist .= '<a class="' . str_replace('_', '-', $category->slug) . '" href="' . esc_url(get_category_link($category->term_id)) . '" ' . $rel . '>' . $category->name . '</a>';
            }
            ++$i;
        }
    }
    return apply_filters('the_category', $thelist, $separator, $parents);
}

function azexo_post_thumbnail_field($template_name = false) {
    if (!$template_name) {
        $template_name = get_post_type();
    }
    $options = get_option(AZEXO_FRAMEWORK);
    $thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
    $lazy = isset($options[$template_name . '_lazy']) && !empty($options[$template_name . '_lazy']) ? $options[$template_name . '_lazy'] : false;
    if ($lazy) {
        wp_enqueue_script('jquery-waypoints');
    }
    $url = azexo_get_the_post_thumbnail(get_the_ID(), $thumbnail_size, true);
    if ($url) {
        $size = azexo_get_image_sizes($thumbnail_size);
        $zoom = isset($options[$template_name . '_zoom']) && esc_attr($options[$template_name . '_zoom']) ? 'zoom' : '';
        ?>                
        <?php if (!is_single()): ?><a href="<?php esc_url(the_permalink()); ?>"><?php endif; ?>
            <?php if ($lazy): ?>
                <?php if (preg_match('/\d+x\d+/', $thumbnail_size)): ?>
                    <div class="image lazy <?php print esc_attr($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" style="height: <?php print esc_attr($size['height']); ?>px;" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                    </div>
                <?php else: ?>
                    <img class="image lazy <?php print esc_attr($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" alt="<?php esc_attr_e('image', 'foodpicky'); ?>">
                <?php endif; ?>
            <?php else: ?>
                <?php if (preg_match('/\d+x\d+/', $thumbnail_size)): ?>
                    <div class="image <?php print esc_attr($zoom); ?>" style='background-image: url("<?php print esc_url($url[0]); ?>"); height: <?php print esc_attr($size['height']); ?>px;' data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                    </div>
                <?php else: ?>
                    <img class="image <?php print esc_attr($zoom); ?>" src="<?php print esc_url($url[0]); ?>" alt="<?php esc_attr_e('image', 'foodpicky'); ?>">
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!is_single()): ?></a><?php endif; ?>
        <?php
    }
}

function azexo_post_gallery_field($template_name = false) {
    if (!$template_name) {
        $template_name = get_post_type();
    }
    $options = get_option(AZEXO_FRAMEWORK);
    $thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
    $show_carousel = isset($options[$template_name . '_show_carousel']) && $options[$template_name . '_show_carousel'];
    $gallery_slider_thumbnails = isset($options[$template_name . '_gallery_slider_thumbnails']) && $options[$template_name . '_gallery_slider_thumbnails'];
    $gallery_slider_thumbnails_vertical = isset($options[$template_name . '_gallery_slider_thumbnails_vertical']) && $options[$template_name . '_gallery_slider_thumbnails_vertical'];
    $lazy = isset($options[$template_name . '_lazy']) && !empty($options[$template_name . '_lazy']) ? $options[$template_name . '_lazy'] : false;
    $zoom = isset($options[$template_name . '_zoom']) && !empty($options[$template_name . '_zoom']) ? 'zoom' : '';
    $gallery = get_post_gallery(get_the_ID(), false);
    if (is_array($gallery)) {
        if (isset($gallery['ids'])) {
            $attachment_ids = explode(",", $gallery['ids']);
            print azexo_entry_gallery($attachment_ids, $show_carousel, $gallery_slider_thumbnails, $thumbnail_size, $gallery_slider_thumbnails_vertical, $lazy, $zoom);
            return;
        }
    }
    $attachment_ids = get_post_meta(get_the_ID(), '_gallery', true);
    if ($attachment_ids) {
        $attachment_ids = explode(",", $attachment_ids);
        print azexo_entry_gallery($attachment_ids, $show_carousel, $gallery_slider_thumbnails, $thumbnail_size, $gallery_slider_thumbnails_vertical, $lazy, $zoom);
        return;
    }
    $attachment_ids = get_children(array('post_parent' => get_the_ID(), 'fields' => 'ids', 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
    if (!empty($attachment_ids)) {
        print azexo_entry_gallery($attachment_ids, $show_carousel, $gallery_slider_thumbnails, $thumbnail_size, $gallery_slider_thumbnails_vertical, $lazy, $zoom);
        return;
    }
    if (is_array($gallery)) {
        if (isset($gallery['src']) && is_array($gallery['src'])) {
            print azexo_entry_gallery($gallery['src'], $show_carousel, $gallery_slider_thumbnails, $thumbnail_size, $gallery_slider_thumbnails_vertical, $lazy, $zoom);
            return;
        }
    }
}

function azexo_post_video_field() {
    $embed = azexo_get_first_shortcode(get_the_content(''), 'embed');
    if ($embed) {
        global $wp_embed;
        return $wp_embed->run_shortcode($embed);
    } else {
        $url = get_post_meta(get_the_ID(), '_video');
        if ($url) {
            return wp_oembed_get($url);
        }
    }
}

function azexo_get_field_templates() {
    $field_templates = get_transient('field_templates');
    if (!is_array($field_templates) || is_admin()) {
        $field_templates = array();
        foreach (array(get_template_directory(), get_stylesheet_directory()) as $path) {
            if (is_dir($path . '/fields')) {
                $directory_iterator = new RecursiveDirectoryIterator($path . '/fields');
                foreach ($directory_iterator as $fileInfo) {
                    if ($fileInfo->isFile() && $fileInfo->getExtension() == 'php') {
                        azexo_filesystem();
                        global $wp_filesystem;
                        $file_contents = $wp_filesystem->get_contents($fileInfo->getPathname());

                        if (!preg_match('|Field Name:(.*)$|mi', $file_contents, $header)) {
                            continue;
                        }
                        $field_templates[$fileInfo->getFilename()] = _cleanup_header_comment($header[1]);
                    }
                }
            }
        }
        set_transient('field_templates', $field_templates);
    }

    return $field_templates;
}

function azexo_get_the_term_list($id, $taxonomy, $before = '', $sep = '', $after = '') {
    $terms = get_the_terms($id, $taxonomy);

    if (is_wp_error($terms)) {
        return $terms;
    }

    if (empty($terms)) {
        return false;
    }

    $links = array();

    foreach ($terms as $term) {
        $link = get_term_link($term, $taxonomy);
        if (is_wp_error($link)) {
            return $link;
        }
        $links[] = '<a class="' . esc_attr($term->slug) . '" href="' . esc_url($link) . '" rel="tag">' . $term->name . '</a>';
    }
    $term_links = apply_filters("term_links-$taxonomy", $links);

    return $before . join($sep, $term_links) . $after;
}

function azexo_entry_field($name) {
    $options = get_option(AZEXO_FRAMEWORK);

    $output = apply_filters('azexo_entry_field', false, $name);
    if ($output) {
        return $output;
    }

    if (is_numeric($name)) {
        return azexo_get_post_content($name);
    }

    if (strpos($name, '.php') !== false) {
        ob_start();
        include(get_theme_file_path('fields/' . $name));
        return ob_get_clean();
    }

    $image = (isset($options[$name . '_image']) && !empty($options[$name . '_image']['url'])) ? '<img src="' . esc_html($options[$name . '_image']['url']) . '" alt="' . esc_attr__('image', 'foodpicky') . '">' : '';
    $label = (isset($options[$name . '_prefix']) && !empty($options[$name . '_prefix'])) ? '<label>' . esc_html($options[$name . '_prefix']) . '</label>' : '';
    $hide_empty = isset($options[$name . '_hide_empty']) ? $options[$name . '_hide_empty'] : false;

    switch ($name) {
        case 'post_title':
            return the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>', false);
            break;
        case 'post_summary':
            return '<div class="entry-summary">' . get_the_excerpt() . '</div>';
            break;
        case 'post_content':
            return '<div class="entry-content">' . get_the_content('') . '</div>';
            break;
        case 'post_thumbnail':
            ob_start();
            azexo_post_thumbnail_field();
            return '<div class="entry-thumbnail">' . ob_get_clean() . '</div>';
            break;
        case 'post_video':
            ob_start();
            azexo_post_video_field();
            return '<div class="entry-video">' . ob_get_clean() . '</div>';
            break;
        case 'post_gallery':
            ob_start();
            azexo_post_gallery_field();
            return '<div class="entry-gallery">' . ob_get_clean() . '</div>';
            break;
        case 'post_sticky':
            if (is_sticky() && is_home() && !is_paged())
                return '<span class="featured-post">' . esc_html__('Sticky', 'foodpicky') . '</span>';
            break;
        case 'post_splitted_date':
            return azexo_entry_splitted_date(false);
            break;
        case 'post_date':
            return azexo_entry_date(false);
            break;
        case 'post_category':
            $categories_list = azexo_get_the_category_list('<span class="delimiter">,</span> ');
            if ($categories_list) {
                return '<span class="categories-links">' . (isset($options['post_category_prefix']) ? '<span class="label">' . esc_html($options['post_category_prefix']) . '</span>' : '') . $categories_list . '</span>';
            }
            break;
        case 'post_tags':
            $tag_list = get_the_tag_list('', '<span class="delimiter">,</span> ');
            if ($tag_list) {
                return '<span class="tags-links">' . (isset($options['post_tags_prefix']) ? '<span class="label">' . esc_html($options['post_tags_prefix']) . '</span>' : '') . $tag_list . '</span>';
            }
            break;
        case 'post_author':
            return sprintf('<span class="author vcard">' . (isset($options['post_author_prefix']) ? '<span class="label">' . esc_html($options['post_author_prefix']) . '</span>' : '') . '<a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(esc_html__('View all posts by %s', 'foodpicky'), get_the_author())), esc_html(get_the_author()));
            break;
        case 'post_author_avatar':
            return '<span class="avatar">' . get_avatar(get_the_author_meta('ID')) . sprintf('<span class="author vcard">' . $label . '<a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url(get_author_posts_url(get_the_author_meta('ID'))), esc_attr(sprintf(esc_html__('View all posts by %s', 'foodpicky'), get_the_author())), esc_html(get_the_author())) . '</span>';
            break;
        case 'post_like':
            if (function_exists('azpl_get_simple_likes_button')) {
                return '<span class="like">' . azpl_get_simple_likes_button(get_the_ID()) . '</span>';
            }
            break;
        case 'post_last_comment':
            $args = array(
                'post_id' => get_the_ID(),
                'status' => 'approve',
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<div class="last-comment">' . esc_html(azexo_comment_excerpt($comment->comment_content)) . '</div>';
            }
            break;
        case 'post_last_comment_author':
            $args = array(
                'post_id' => get_the_ID(),
                'status' => 'approve',
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return sprintf('<span class="author vcard last-comment">' . $label . '<a class="url fn n" href="%1$s" rel="author">%2$s</a></span>', esc_url(get_author_posts_url(username_exists($comment->comment_author))), esc_html($comment->comment_author));
            }
            break;
        case 'post_last_comment_author_avatar':
            $args = array(
                'post_id' => get_the_ID(),
                'status' => 'approve',
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<span class="avatar last-comment">' . get_avatar($comment->comment_author_email) . sprintf('<span class="author vcard">' . $label . '<a class="url fn n" href="%1$s" rel="author">%2$s</a></span>', esc_url(get_author_posts_url(username_exists($comment->comment_author))), esc_html($comment->comment_author)) . '</span>';
            }
            break;
        case 'post_last_comment_date':
            $args = array(
                'post_id' => get_the_ID(),
                'status' => 'approve',
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                return '<div class="last-comment-date">' . azexo_comment_date(false, $comment) . '</div>';
            }
            break;
        case 'post_comments_count':
            if (comments_open()) {
                $comment_count = get_comment_count(get_the_ID());
                $comments = '<a href="' . esc_url(get_comments_link()) . '"><span class="count">' . $comment_count['total_comments'] . '</span><span class="label">' . ($comment_count['total_comments'] == 1 ? esc_html__('comment', 'foodpicky') : esc_html__('comments', 'foodpicky')) . '</span></a>';
                return '<span class="comments">' . $comments . '</span>';
            }
            break;
        case 'post_read_more':
            $more_link_text = sprintf(esc_html__('Read more', 'foodpicky'));
            return '<div class="entry-more">' . apply_filters('the_content_more_link', ' <a href="' . esc_url(get_permalink()) . "#more-" . get_the_ID() . "\" class=\"more-link\">" . $more_link_text . "</a>", $more_link_text) . '</div>';
            break;
        case 'post_share':
            ob_start();
            azexo_entry_share();
            return '<div class="entry-share">' . '<div class="helper">' . (isset($options['post_share_prefix']) ? esc_html($options['post_share_prefix']) : '') . '</div><span class="links">' . ob_get_clean() . '</span></div>';
            break;
        case 'post_comments':
            ob_start();
            if (comments_open()) {
                comments_template();
            }
            return ob_get_clean();
            break;
        case 'post_navigation':
            ob_start();
            azexo_post_nav();
            return ob_get_clean();
            break;
        default:
            if (isset($options['meta_fields']) && in_array($name, array_filter($options['meta_fields']))) {
                $value = get_post_meta(get_the_ID(), $name, true);
                $value = trim($value);
                $value = apply_filters('azexo_entry_field_meta_field', $value, $name);
                if (!empty($value) || (empty($value) && !$hide_empty)) {
                    return '<span class="meta-field ' . str_replace(array('_', ' '), '-', strtolower($name)) . ' ' . (empty($value) ? 'empty' : '') . '">' . $image . ' ' . $label . ' <span class="value">' . $value . ((isset($options[$name . '_suffix']) && !empty($value)) ? ' <span class="units">' . esc_html($options[$name . '_suffix']) . '</span>' : '') . '</span>' . '</span>';
                }
            } else {
                $taxonomies = get_object_taxonomies(get_post_type());
                $slug = str_replace('taxonomy_', '', $name);
                $term_list = false;
                if (in_array($slug, $taxonomies)) {
                    $term_list = azexo_get_the_term_list(get_the_ID(), $slug, '', '<span class="delimiter">,</span> ', '');
                    $term_list = apply_filters('azexo_entry_field_taxonomy_field', $term_list, $name);
                    $term_list = trim($term_list);
                    if (!empty($term_list) || (empty($term_list) && !$hide_empty)) {
                        return '<span class="taxonomy ' . str_replace('_', '-', $slug) . ' ' . (empty($term_list) ? 'empty' : '') . '">' . $image . ' ' . $label . ' <span class="links">' . $term_list . '</span></span>';
                    }
                }
            }
            return apply_filters('azexo_entry_field_default', '', $name);
            break;
    }
    return '';
}

function azexo_entry_meta($template_name = 'post', $place = 'meta') {
    static $cache = array();
    if (isset($cache[get_the_ID()][$template_name][$place])) {
        return $cache[get_the_ID()][$template_name][$place];
    }
    $options = get_option(AZEXO_FRAMEWORK);
    $meta = '';
    if (isset($options[$template_name . '_' . $place]) && is_array($options[$template_name . '_' . $place])) {
        foreach ($options[$template_name . '_' . $place] as $field) {
            $meta .= azexo_entry_field($field);
        }
    }
    $cache[get_the_ID()][$template_name][$place] = $meta;
    return $meta;
}

function azexo_entry_share() {
    global $post;
    $image = null;
    if (is_object($post)) {
        $image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
    }
    print '<a class="facebook-share" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-facebook"></i></span></a>';
    print '<a class="twitter-share" target="_blank" href="https://twitter.com/home?status=' . rawurlencode(esc_attr__('Check out this article: ', 'foodpicky')) . rawurlencode(get_the_title()) . '%20-%20' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-twitter"></i></span></a>';
    if (!empty($image)) {
        print '<a class="pinterest-share" target="_blank" href="https://pinterest.com/pin/create/button/?url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '&media=' . rawurlencode($image) . '&description=' . rawurlencode(get_the_title()) . '"><span class="share-box"><i class="fa fa-pinterest"></i></span></a>';
    }
    print '<a class="linkedin-share" target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '&title=' . rawurlencode(get_the_title()) . '&source=LinkedIn"><span class="share-box"><i class="fa fa-linkedin"></i></span></a>';
    print '<a class="google-plus-share" target="_blank" href="https://plus.google.com/share?url=' . rawurlencode(esc_url(apply_filters('the_permalink', get_permalink()))) . '"><span class="share-box"><i class="fa fa-google-plus"></i></span></a>';
    if (comments_open() && !is_single() && !is_page()) {
        $comments = '<span class="share-box"><i class="fa fa-comment-o"></i></span>';
        comments_popup_link($comments, $comments, $comments, '', '');
    }
}

function azexo_entry_splitted_date($echo = true) {

    $date = '<div class="date"><div class="day">' . get_the_date('d') . '</div><div class="month">' . get_the_date('M') . '</div><div class="year">' . get_the_date('Y') . '</div></div>';

    if ($echo) {
        print $date;
    }

    return $date;
}

function azexo_entry_date($echo = true, $post = null) {
    if (has_post_format(array('chat', 'status'), $post))
        $format_prefix = _x('%1$s on %2$s', '1: post format name. 2: date', 'foodpicky');
    else
        $format_prefix = '%2$s';

    $options = get_option(AZEXO_FRAMEWORK);
    $date = sprintf('<span class="date">' . (isset($options['post_date_prefix']) ? esc_html($options['post_date_prefix']) : '') . '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>', esc_url(get_permalink($post)), esc_attr(sprintf(esc_html__('Permalink to %s', 'foodpicky'), the_title_attribute(array('echo' => false, 'post' => $post)))), esc_attr(get_the_date('c', $post)), esc_html(sprintf($format_prefix, get_post_format_string(get_post_format($post)), get_the_date('', $post)))
    );

    if ($echo) {
        print $date;
    }

    return $date;
}

function azexo_comment_date($echo = true, $comment = null) {

    $format_prefix = '%2$s';

    $options = get_option(AZEXO_FRAMEWORK);
    $date = sprintf('<span class="date">' . (isset($options['post_date_prefix']) ? esc_html($options['post_date_prefix']) : '') . '<a href="%1$s" title="%2$s" rel="bookmark"><time class="comment-date" datetime="%3$s">%4$s</time></a></span>', esc_url(get_comment_link($comment)), esc_attr(sprintf(esc_html__('Permalink to %s', 'foodpicky'), the_title_attribute(array('echo' => false, 'post' => $comment)))
            ), esc_attr(get_comment_date('c', $comment->comment_ID)), esc_html(sprintf($format_prefix, get_post_format_string(get_post_format($comment)), get_comment_date('', $comment->comment_ID))
            )
    );

    if ($echo) {
        print $date;
    }

    return $date;
}

function azexo_the_attached_image() {
    $attachment_size = apply_filters('azexo_attachment_size', array(724, 724));
    $next_attachment_url = wp_get_attachment_url();
    $post = get_post();

    $attachment_ids = get_posts(array(
        'post_parent' => $post->post_parent,
        'fields' => 'ids',
        'numberposts' => -1,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'orderby' => 'menu_order ID'
    ));

    // If there is more than 1 attachment in a gallery...
    if (count($attachment_ids) > 1) {
        foreach ($attachment_ids as $attachment_id) {
            if ($attachment_id == $post->ID) {
                $next_id = current($attachment_ids);
                break;
            }
        }

        // get the URL of the next image attachment...
        if ($next_id)
            $next_attachment_url = get_attachment_link($next_id);

        // or get the URL of the first image attachment.
        else
            $next_attachment_url = get_attachment_link(array_shift($attachment_ids));
    }

    printf('<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>', esc_url($next_attachment_url), the_title_attribute(array('echo' => false)), wp_get_attachment_image($post->ID, $attachment_size)
    );
}

function azexo_entry_gallery($attachment_ids, $carousel, $thumbnails, $img_size, $vertical = 0, $lazy = false, $zoom = '') {
    $output = '';
    azexo_add_image_size($img_size);
    $size = azexo_get_image_sizes($img_size);
    if ($carousel) {
        wp_enqueue_script('jquery-owl-carousel');
        wp_enqueue_style('jquery-owl-carousel');
    } else {
        wp_enqueue_style('jquery-flexslider');
        wp_enqueue_script('jquery-flexslider');
    }
    $output .= '<div class="images ' . ($thumbnails ? 'thumbnails' : '') . ' ' . ($carousel ? 'carousel' : '') . '" data-width="' . esc_attr($size['width']) . '" data-height="' . esc_attr($size['height']) . '" data-vertical="' . esc_attr($vertical) . '">';
    foreach ($attachment_ids as $attachment_id) {
        $image_url = $attachment_id;
        $full_image_url = $attachment_id;
        if (is_numeric($attachment_id)) {
            $image_url = azexo_get_attachment_thumbnail($attachment_id, $img_size, true);
            $image_url = $image_url[0];

            $full_image_url = azexo_get_attachment_thumbnail($attachment_id, 'full', true);
            $full_image_url = $full_image_url[0];
        }
        if (!empty($image_url)) {
            if ($lazy) {
                if (preg_match('/\d+x\d+/', $img_size)) {
                    $output .= '<div class="image lazy ' . esc_attr($zoom) . '" data-src="' . esc_url($image_url) . '" data-popup="' . esc_url($full_image_url) . '" style=\'height: ' . esc_attr($size['height']) . 'px;\'></div>';
                } else {
                    $output .= '<img class="image lazy" data-src="' . esc_url($image_url) . '" data-popup="' . esc_url($full_image_url) . '" alt="' . esc_attr__('image', 'foodpicky') . '">';
                }
            } else {
                if (preg_match('/\d+x\d+/', $img_size)) {
                    $output .= '<div class="image ' . esc_attr($zoom) . '" data-popup="' . esc_url($full_image_url) . '" style=\'background-image: url("' . esc_url($image_url) . '"); height: ' . esc_attr($size['height']) . 'px;\'></div>';
                } else {
                    $output .= '<img class="image" src="' . esc_url($image_url) . '" data-popup="' . esc_url($full_image_url) . '" alt="' . esc_attr__('image', 'foodpicky') . '">';
                }
            }
        }
    }
    $output .= "</div><!-- images -->\n";
    return $output;
}

function azexo_get_link_url() {
    $content = get_the_content();
    $has_url = get_url_in_content($content);
    return ( $has_url ) ? $has_url : apply_filters('the_permalink', get_permalink());
}

function azexo_get_image_sizes($size = '') {

    global $_wp_additional_image_sizes;

    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();

    // Create the full array with sizes and crop info
    foreach ($get_intermediate_image_sizes as $_size) {

        if (in_array($_size, array('thumbnail', 'medium', 'large'))) {

            $sizes[$_size]['width'] = get_option($_size . '_size_w');
            $sizes[$_size]['height'] = get_option($_size . '_size_h');
            $sizes[$_size]['crop'] = (bool) get_option($_size . '_crop');
        } elseif (isset($_wp_additional_image_sizes[$_size])) {

            $sizes[$_size] = array(
                'width' => $_wp_additional_image_sizes[$_size]['width'],
                'height' => $_wp_additional_image_sizes[$_size]['height'],
                'crop' => $_wp_additional_image_sizes[$_size]['crop']
            );
        }
    }

    // Get only 1 size if found
    if ($size) {

        if (isset($sizes[$size])) {
            return $sizes[$size];
        } else {
            return false;
        }
    }

    return $sizes;
}

function azexo_add_image_size($size) {
    if (!has_image_size($size) && !in_array($size, array('thumbnail', 'medium', 'large'))) {
        $size_array = explode('x', $size);
        if (count($size_array) == 2) {
            add_image_size($size, $size_array[0], $size_array[1], true);
        }
    }
}

function azexo_get_attachment_thumbnail($attachment_id, $size, $url = false) {
    azexo_add_image_size($size);

    $metadata = wp_get_attachment_metadata($attachment_id);
    if (is_array($metadata)) {
        $regenerate = false;
        $size_array = explode('x', $size);
        if (count($size_array) == 2) {
            $regenerate = true;
            if (isset($metadata['width']) && isset($metadata['height'])) {
                if ((intval($metadata['width']) <= intval($size_array[0])) && (intval($metadata['height']) <= intval($size_array[1]))) {
                    $regenerate = false;
                }
            } else {
                $regenerate = false;
            }
        }
        if ($regenerate && (!isset($metadata['sizes']) || !isset($metadata['sizes'][$size]))) {
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $meta => $data) {
                    azexo_add_image_size($meta);
                }
            }
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/post.php');
            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id)));
            $metadata = wp_get_attachment_metadata($attachment_id);
        }
    }
    if ($url) {
        $image = wp_get_attachment_image_src($attachment_id, $size);
        if (empty($image)) {
            $image = wp_get_attachment_image_src($attachment_id, 'full');
        }
        return $image;
    } else {
        $image = wp_get_attachment_image($attachment_id, $size);
        if (empty($image)) {
            $image = wp_get_attachment_image($attachment_id, 'full');
        }
        return $image;
    }
}

function azexo_get_the_post_thumbnail($post_id, $size, $url = false) {
    azexo_add_image_size($size);
    $post_thumbnail_id = get_post_thumbnail_id($post_id);
    if (empty($post_thumbnail_id)) {
        if ($url) {
            
        } else {
            
        }
    }
    return azexo_get_attachment_thumbnail($post_thumbnail_id, $size, $url);
}

function azexo_get_attachment_image_src($attachment_id, $size) {
    return azexo_get_attachment_thumbnail($attachment_id, $size, true);
}

function azexo_strip_first_shortcode($content, $first_shortcode) {
    preg_match_all('/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
        foreach ($matches as $shortcode) {
            if ($first_shortcode === $shortcode[2]) {
                $pos = strpos($content, $shortcode[0]);
                if ($pos !== false)
                    return substr_replace($content, '', $pos, strlen($shortcode[0]));
            }
        }
    }
    return $content;
}

function azexo_get_first_shortcode($content, $first_shortcode) {
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

function azexo_get_search_form($echo = true) {
    $result = '<div class="search-wrapper">';
    $result .= get_search_form(false);
    $result .= '<i class="fa fa-search"></i></div>';
    if ($echo) {
        print $result;
    } else {
        return $result;
    }
}

function azexo_unparse_url(array $parsed) {
    $scheme = & $parsed['scheme'];
    $host = & $parsed['host'];
    $port = & $parsed['port'];
    $user = & $parsed['user'];
    $pass = & $parsed['pass'];
    $path = & $parsed['path'];
    $query = & $parsed['query'];
    $fragment = & $parsed['fragment'];

    $userinfo = !strlen($pass) ? $user : "$user:$pass";
    $host = !"$port" ? $host : "$host:$port";
    $authority = !strlen($userinfo) ? $host : "$userinfo@$host";
    $hier_part = !strlen($authority) ? $path : "//$authority$path";
    $url = !strlen($scheme) ? $hier_part : "$scheme:$hier_part";
    $url = !strlen($query) ? $url : "$url?$query";
    $url = !strlen($fragment) ? $url : "$url#$fragment";

    return $url;
}

add_filter('embed_oembed_html', 'azexo_embed_oembed_html', 10, 4);

function azexo_embed_oembed_html($html, $url, $attr, $post_ID) {
    if (preg_match('/src="([^"]*)"/', $html, $matches)) {
        $oembed_src = parse_url($matches[1]);
        $user_src = parse_url($url);
        if (isset($user_src['query'])) {
            $oembed_src['query'] = $user_src['query'];
        }
        $html = preg_replace('/src="[^"]*"/', 'src="' . esc_url(azexo_unparse_url($oembed_src)) . '"', $html); //HTML5 query url fix - htmlentities(esc_url(azexo_unparse_url($oembed_src)))
    }
    $html = str_replace(array('frameborder="0"', 'webkitallowfullscreen', 'mozallowfullscreen', 'allowfullscreen'), '', $html);
    return $html;
}

function azexo_display_select_tree($term, $selected = '', $level = 0) {
    if (is_object($term)) {
        if (!empty($term->children)) {
            echo '<option value="" disabled>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
            $level++;
            foreach ($term->children as $key => $child) {
                azexo_display_select_tree($child, $selected, $level);
            }
        } else {
            echo '<option value="' . $term->slug . '" ' . ( $term->slug == $selected ? 'selected="selected"' : '' ) . '>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
        }
    }
}

function azexo_array_filter_recursive($input) {
    foreach ($input as &$value) {
        if (is_array($value)) {
            $value = azexo_array_filter_recursive($value);
        }
    }
    return array_filter($input);
}

function azexo_time_left($date_to, $label = '') {
    if (!empty($date_to)) {
        $expire = $date_to - current_time('timestamp');
        if ($expire < 0) {
            $expire = 0;
        }
        $days = floor($expire / 60 / 60 / 24);
        $hours = floor(($expire - $days * 60 * 60 * 24) / 60 / 60);
        $minutes = floor(($expire - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = $expire - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60;
        wp_enqueue_script('jquery-countdown');
        ?>
        <div class="time-left">
            <?php print (empty($label) ? '' : '<label>' . esc_html($label) . '</label>'); ?>
            <div class="time" data-time="<?php print date('Y/m/d H:i:s', $date_to); ?>">
                <div class="days"><span class="count"><?php print $days; ?></span><span class="title"><?php print (($days == 1) ? esc_html__('day', 'foodpicky') : esc_html__('days', 'foodpicky')); ?></span></div>
                <div class="hours"><span class="count"><?php print $hours; ?></span><span class="title"><?php print (($hours == 1) ? esc_html__('hour', 'foodpicky') : esc_html__('hrs', 'foodpicky')); ?></span></div>
                <div class="minutes"><span class="count"><?php print $minutes; ?></span><span class="title"><?php print esc_html__('min', 'foodpicky'); ?></span></div>
                <div class="seconds"><span class="count"><?php print $seconds; ?></span><span class="title"><?php print esc_html__('sec', 'foodpicky'); ?></span></div>
            </div>
        </div>
        <?php
    }
}

function azexo_current_time_ordering_filter($args, $query) {
    if (isset($query->query['orderby'])) {
        if ($query->query['orderby'] == 'meta_value' || $query->query['orderby'] == 'meta_value_num') {
            if (isset($query->meta_query) && isset($query->meta_query->meta_table)) {
                if ($query->query['order'] == 'DESC') {
                    $args['where'] .= " AND ( " . esc_sql($query->meta_query->meta_table) . ".meta_value <= unix_timestamp())  ";
                } else {
                    $args['where'] .= " AND ( " . esc_sql($query->meta_query->meta_table) . ".meta_value >= unix_timestamp())  ";
                }
            }
        }
    }
    return $args;
}

function azexo_current_user_author_filter($args, $query) {
    global $wpdb;

    $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql(get_current_user_id()) . ") ";

    return $args;
}

add_filter('comment_form_fields', 'azexo_comment_form_fields');

function azexo_comment_form_fields($comment_fields) {
    $comment = $comment_fields['comment'];
    unset($comment_fields['comment']);
    $comment_fields = $comment_fields + array('comment' => $comment);
    return $comment_fields;
}

function azexo_is_template_part_exists($slug, $name = null) {
    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
        $templates[] = "{$slug}-{$name}.php";
    }
    $templates[] = "{$slug}.php";
    return azexo_locate_template($templates);
}

add_filter('infinite_scroll_js_options', 'azexo_infinite_scroll_js_options');

function azexo_infinite_scroll_js_options($options) {
    $options['nextSelector'] = 'nav.navigation .loop-pagination a.next';
    $options['navSelector'] = 'nav.navigation .loop-pagination';
    $options['itemSelector'] = '#content > .entry.post';
    $options['contentSelector'] = '#content.infinite-scroll';
    $options['loading']['img'] = get_template_directory_uri() . "/images/infinitescroll-loader.svg";
    $options['loading']['msgText'] = '<em class="infinite-scroll-loading">' . esc_html__('Loading ...', 'foodpicky') . '</em>';
    $options['loading']['finishedMsg'] = '<em class="infinite-scroll-done">' . esc_html__('Done', 'foodpicky') . '</em>';
    return $options;
}

add_filter('wp_update_comment_count', 'azexo_wp_update_comment_count');

function azexo_wp_update_comment_count($post_id) {
    delete_post_meta($post_id, '_az_review_count');
}

function azexo_review_count($post) {
    $count = apply_filters('azexo_review_count', false, $post);
    if ($count) {
        return $count;
    }
    global $wpdb;
    if (!metadata_exists('post', $post->ID, '_az_review_count')) {
        $count = $wpdb->get_var($wpdb->prepare("
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_parent = 0
				AND comment_post_ID = %d
				AND comment_approved = '1'
			", $post->ID));

        update_post_meta($post->ID, '_az_review_count', $count);
    } else {
        $count = get_post_meta($post->ID, '_az_review_count', true);
    }
    return $count;
}

function azexo_review_allowed($customer_email, $user_id, $post) {
    return apply_filters('azexo_review_allowed', false, $customer_email, $user_id, $post);
}

function azexo_get_dashboard_links() {
    if (!is_user_logged_in()) {
        return array();
    }
    static $links = false;
    if ($links) {
        return $links;
    }
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', esc_url(home_url($wp->request)));
    $links = array(
        array(
            'url' => esc_url(wp_logout_url($current_url)),
            'title' => esc_html__('Log out', 'foodpicky'),
        )
    );
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['custom_dashboard_pages']) && !empty($options['custom_dashboard_pages'])) {
        $custom_dashboard_pages = array();
        foreach ($options['custom_dashboard_pages'] as $page) {
            $custom_dashboard_pages[] = array(
                'id' => $page,
                'url' => esc_url(get_permalink($page)),
                'title' => get_the_title($page),
            );
        }
        $links = array_merge($custom_dashboard_pages, $links);
    }
    $links = apply_filters('azexo_dashboard_links', $links);
    foreach ($links as &$link) {
        if (!isset($link['active'])) {
            if (isset($link['id'])) {
                $link['active'] = azexo_is_current_post($link['id']);
            } else {
                $link['active'] = ($link['url'] == add_query_arg(NULL, NULL) || strpos($current_url, untrailingslashit($link['url'])) !== false );
            }
        }
    }
    return $links;
}

function azexo_is_dashboard() {
    if (isset($_GET['disable-dashboard']) && $_GET['disable-dashboard'] == '1') {
        return false;
    }
    $links = azexo_get_dashboard_links();
    foreach ($links as $link) {
        if (isset($link['active']) && $link['active']) {
            return true;
        }
    }
    return false;
}

add_action('is_active_sidebar', 'azexo_is_active_sidebar', 20, 2);

function azexo_is_active_sidebar($is_active_sidebar, $index) {
    if (($index == 'sidebar' || $index == 'shop') && azexo_is_dashboard()) {
        return is_active_sidebar('dashboard_sidebar');
    }
    return $is_active_sidebar;
}

add_filter('azexo_page_title', 'azexo_page_title');

function azexo_page_title($page_title) {
    $links = azexo_get_dashboard_links();
    foreach ($links as $link) {
        if (isset($link['active']) && $link['active']) {
            return $link['title'];
        }
    }
    return $page_title;
}

add_filter('body_class', 'azexo_body_class');

function azexo_body_class($classes) {

    if (azexo_is_dashboard()) {
        $classes[] = 'dashboard';
    }

    return $classes;
}

add_action('wp_update_comment_count', 'azexo_update_comment_count');

function azexo_update_comment_count($post_id) {
    $review_marks = azexo_review_marks();
    if (!empty($review_marks)) {
        foreach ($review_marks as $slug => $label) {
            delete_post_meta($post_id, '_' . $slug . '_average_rating');
            delete_post_meta($post_id, '_' . $slug . '_rating_count');
        }
    }
}

function azexo_review_marks() {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['review_marks']) && is_array($options['review_marks'])) {
        $options['review_marks'] = array_filter($options['review_marks']);
        if (!empty($options['review_marks'])) {
            return array_combine(array_map('sanitize_title', $options['review_marks']), $options['review_marks']);
        }
    }
    return array();
}

function azexo_buildQuery($atts) {

    $atts['items_per_page'] = $atts['query_items_per_page'] = isset($atts['max_items']) ? $atts['max_items'] : '';
    $atts['query_offset'] = isset($atts['offset']) ? $atts['offset'] : '';

    $defaults = array(
        'post_type' => 'post',
        'orderby' => '',
        'order' => 'DESC',
        'meta_key' => '',
        'max_items' => '10',
        'offset' => '0',
        'taxonomies' => '',
        'custom_query' => '',
        'include' => '',
        'exclude' => '',
    );
    $atts = wp_parse_args($atts, $defaults);

    // Set include & exclude
    if ($atts['post_type'] !== 'ids' && !empty($atts['exclude'])) {
        $atts['exclude'] .= ',' . $atts['exclude'];
    } else {
        $atts['exclude'] = $atts['exclude'];
    }
    if ($atts['post_type'] !== 'ids') {
        $settings = array(
            'posts_per_page' => $atts['query_items_per_page'],
            'offset' => $atts['query_offset'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'meta_key' => in_array($atts['orderby'], array(
                'meta_value',
                'meta_value_num',
            )) ? $atts['meta_key'] : '',
            'post_type' => $atts['post_type'],
            'exclude' => $atts['exclude'],
        );
        if (!empty($atts['taxonomies'])) {
            $vc_taxonomies_types = get_taxonomies(array('public' => true));
            $terms = get_terms(array_keys($vc_taxonomies_types), array(
                'hide_empty' => false,
                'include' => $atts['taxonomies'],
            ));
            $settings['tax_query'] = array();
            $tax_queries = array(); // List of taxnonimes
            foreach ($terms as $t) {
                if (!isset($tax_queries[$t->taxonomy])) {
                    $tax_queries[$t->taxonomy] = array(
                        'taxonomy' => $t->taxonomy,
                        'field' => 'id',
                        'terms' => array($t->term_id),
                        'operator' => 'IN'
                    );
                } else {
                    $tax_queries[$t->taxonomy]['terms'][] = $t->term_id;
                }
            }
            $settings['tax_query'] = array_values($tax_queries);
            $settings['tax_query']['relation'] = 'OR';
        }
    } else {
        if (empty($atts['include'])) {
            $atts['include'] = - 1;
        } elseif (!empty($atts['exclude'])) {
            $atts['include'] = preg_replace(
                    '/(('
                    . preg_replace(
                            array('/^\,\*/', '/\,\s*$/', '/\s*\,\s*/'), array('', '', '|'), $atts['exclude']
                    )
                    . ')\,*\s*)/', '', $atts['include']);
        }
        $settings = array(
            'include' => $atts['include'],
            'posts_per_page' => $atts['query_items_per_page'],
            'offset' => $atts['query_offset'],
            'post_type' => 'any',
            'orderby' => 'post__in',
        );
    }

    return $settings;
}

function azexo_filterQuerySettings($args) {
    $defaults = array(
        'numberposts' => 5,
        'offset' => 0,
        'category' => 0,
        'orderby' => 'date',
        'order' => 'DESC',
        'include' => array(),
        'exclude' => array(),
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'public' => true
    );

    $r = wp_parse_args($args, $defaults);
    if (empty($r['post_status'])) {
        $r['post_status'] = ( 'attachment' == $r['post_type'] ) ? 'inherit' : 'publish';
    }
    if (!empty($r['numberposts']) && empty($r['posts_per_page'])) {
        $r['posts_per_page'] = $r['numberposts'];
    }
    if (!empty($r['category'])) {
        $r['cat'] = $r['category'];
    }
    if (!empty($r['include'])) {
        $incposts = wp_parse_id_list($r['include']);
        $r['posts_per_page'] = count($incposts);  // only the number of posts included
        $r['post__in'] = $incposts;
    } elseif (!empty($r['exclude'])) {
        $r['post__not_in'] = wp_parse_id_list($r['exclude']);
    }

    $r['ignore_sticky_posts'] = true;
    $r['no_found_rows'] = true;

    return azexo_array_filter_recursive($r);
}

function azexo_posts_list_filters($posts, $taxonomy) {
    $filter_terms = array();
    foreach ($posts as $post) {
        $terms = apply_filters('azexo_posts_list_post_terms', false, $post, $taxonomy);
        if (!$terms) {
            $terms = wp_get_post_terms($post->ID, $taxonomy);
        }
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $filter_terms[$term->term_id] = $term;
            }
        }
    }
    return $filter_terms;
}

function azexo_posts_list_post($only_content, $template, $filter, $item_wrapper) {
    global $post;
    if ($only_content) {
        print azexo_get_post_content($post->ID);
    } else {
        $template_name = $template;
        $azexo_woo_base_tag = 'div';
        if (!empty($filter)) {
            $filter_terms = apply_filters('azexo_posts_list_post_terms', false, $post, $filter);
            if (!$filter_terms) {
                $filter_terms = wp_get_post_terms($post->ID, $filter);
            }
            if (is_array($filter_terms)) {
                $filter_terms = array_map(function($term) {
                    return $term->slug;
                }, $filter_terms);
                print '<div class="filterable ' . esc_attr(implode(' ', $filter_terms)) . '">';
            }
        }
        if ($item_wrapper) {
            print '<div class="item">';
        }
        include(get_theme_file_path(apply_filters('azexo_post_template_path', 'content.php', $template)));
        if ($item_wrapper) {
            print '</div>';
        }
        if (!empty($filter)) {
            print '</div>';
        }
    }
}
