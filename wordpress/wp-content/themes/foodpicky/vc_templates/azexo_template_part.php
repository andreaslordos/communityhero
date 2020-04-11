<?php

$output = $template_part = $el_class = $css = '';
extract(shortcode_atts(array(
    'template_part' => '',
    'el_class' => '',
    'css' => '',
                ), $atts));

$css_class = $el_class;
if (function_exists('vc_shortcode_custom_css_class')) {
    $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);
}

$slug = str_replace('.php', '', $template_part);
$path = azexo_is_template_part_exists('template-parts/' . $slug);
if (!empty($path)) {
    print '<div class="template-part ' . sanitize_title($slug) . esc_attr($css_class) . '">';
    get_template_part('template-parts/' . $slug);
    print '</div>';
}