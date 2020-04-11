<?php

$output = $template = $post_type = $post_id = $el_class = $css = '';
extract(shortcode_atts(array(
    'post_id' => '',
    'template' => 'post',
    'post_type' => 'post',
    'el_class' => '',
    'css' => '',
                ), $atts));

$css_class = $el_class;
if (function_exists('vc_shortcode_custom_css_class')) {
    $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);
}

$template_name = $template;
$azexo_woo_base_tag = 'div';
if (is_numeric($post_id)) {
    global $post, $wp_query;
    $original = $post;
    $post = get_post($post_id);
    setup_postdata($post);

    print '<div class="' . esc_attr($css_class) . '">';
    include(get_template_directory() . '/' . apply_filters('azexo_post_template_path', 'content.php', $template_name));
    print '</div>';

    $wp_query->post = $original;
    wp_reset_postdata();
} else {
    $closest_post = azexo_get_closest_current_post($post_type);

    global $post, $wp_query;
    $original = $post;
    $post = $closest_post;
    setup_postdata($post);

    print '<div class="' . esc_attr($css_class) . '">';
    include(get_template_directory() . '/' . apply_filters('azexo_post_template_path', 'content.php', $template_name));
    print '</div>';

    $wp_query->post = $original;
    wp_reset_postdata();
}
