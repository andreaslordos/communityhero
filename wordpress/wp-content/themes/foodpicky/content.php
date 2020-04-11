<?php
$keys = (array)get_post_custom_keys();
$cached_keys = get_option('azexo_' . get_post_type() . '_meta_keys', array());
$diff = array_diff($keys, $cached_keys);
if(!empty($diff)) {
    $cached_keys = array_merge($cached_keys, $keys);
    $cached_keys = array_unique($cached_keys);
    update_option('azexo_' . get_post_type() . '_meta_keys', $cached_keys);
}


$options = get_option(AZEXO_FRAMEWORK);
$more_link_text = sprintf(esc_html__('Read more', 'foodpicky'));
$default_template = isset($options['default_' . get_post_type() . '_template']) ? $options['default_' . get_post_type() . '_template'] : 'post';
if (is_single() && isset($options['single_' . get_post_type() . '_template']) && !empty($options['single_' . get_post_type() . '_template'])) {
    $default_template = $options['single_' . get_post_type() . '_template'];
}
if (!isset($template_name)) {
    $template_name = apply_filters('azexo_template_name', $default_template);
}
$thumbnail_size = isset($options[$template_name . '_thumbnail_size']) && !empty($options[$template_name . '_thumbnail_size']) ? $options[$template_name . '_thumbnail_size'] : 'large';
$image_thumbnail = isset($options[$template_name . '_image_thumbnail']) ? $options[$template_name . '_image_thumbnail'] : false;

?>

<div <?php post_class(array('entry', str_replace('_', '-', $template_name))); ?>>
    <?php do_action('azexo_entry_open'); ?>
    <?php if (isset($options[$template_name . '_show_thumbnail']) && $options[$template_name . '_show_thumbnail']) : ?>
        <?php if (!post_password_required() && !is_attachment()) : ?>
            <?php if (((get_post_type() == 'post' && has_post_format('gallery')) || get_post_meta(get_the_ID(), '_gallery')) && !$image_thumbnail) : ?>
                <div class="entry-gallery">
                    <?php
                    azexo_post_gallery_field($template_name);
                    ?>
                    <?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'hover'))) : ?>
                        <div class="entry-hover"><?php print azexo_entry_meta($template_name, 'hover'); ?></div>
                    <?php endif; ?>
                    <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                </div>
            <?php elseif (((get_post_type() == 'post' && has_post_format('video')) || get_post_meta(get_the_ID(), '_video')) && !$image_thumbnail && $post_video_field = azexo_post_video_field()) : ?>
                <div class="entry-video">
                    <?php
                    print $post_video_field;
                    ?>
                    <?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'hover'))) : ?>
                        <div class="entry-hover"><?php print azexo_entry_meta($template_name, 'hover'); ?></div>
                    <?php endif; ?>
                    <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                </div>
            <?php else: ?>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php
                        azexo_post_thumbnail_field($template_name);
                        ?>                
                        <?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'hover'))) : ?>
                            <div class="entry-hover"><?php print azexo_entry_meta($template_name, 'hover'); ?></div>
                        <?php endif; ?>
                        <?php print azexo_entry_meta($template_name, 'thumbnail'); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <div class="entry-data">
        <div class="entry-header"><?php
            ?><?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'extra'))) : ?>
                <div class="entry-extra"><?php print azexo_entry_meta($template_name, 'extra'); ?></div>
            <?php endif; ?><?php
            if (isset($options[$template_name . '_show_title']) && $options[$template_name . '_show_title']) {
                if (is_single() && $template_name == $default_template) :
                    the_title('<h2 class="entry-title">', '</h2>');
                else :
                    the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                endif;
            }
            ?><?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'meta'))) : ?>
                <div class="entry-meta"><?php print azexo_entry_meta($template_name, 'meta'); ?></div>
            <?php endif; ?><?php
            print azexo_entry_meta($template_name, 'header');
            ?></div>
        <?php if (isset($options[$template_name . '_show_content']) && $options[$template_name . '_show_content'] != 'hidden'): ?>
            <?php if (is_search() || $options[$template_name . '_show_content'] == 'excerpt') : ?>
                <?php
                $summary = azexo_excerpt(apply_filters('the_excerpt', get_the_excerpt()), isset($options[$template_name . '_excerpt_length']) ? $options[$template_name . '_excerpt_length'] : false, isset($options[$template_name . '_excerpt_words_trim']) ? $options[$template_name . '_excerpt_words_trim'] : true );
                $summary = trim($summary);
                ?>
                <?php if (!empty($summary)) : ?>
                    <div class="entry-summary"><?php print $summary; ?></div>
                <?php endif; ?>        
            <?php else : ?>
                <?php
                $content = '';
                if (!get_post_format() || has_post_format('gallery') || has_post_format('video')) {
                    if (has_post_format('gallery')) {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = azexo_strip_first_shortcode(get_the_content($more_link_text), 'gallery');
                        else
                            $content = azexo_strip_first_shortcode(get_the_content(''), 'gallery');
                    } elseif (has_post_format('video')) {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = azexo_strip_first_shortcode(get_the_content($more_link_text), 'embed');
                        else
                            $content = azexo_strip_first_shortcode(get_the_content(''), 'embed');
                    } else {
                        if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content'])
                            $content = get_the_content($more_link_text);
                        else
                            $content = get_the_content('');
                    }
                    $content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $content));
                } else {
                    if (isset($options[$template_name . '_more_inside_content']) && $options[$template_name . '_more_inside_content']) {
                        ob_start();
                        the_content($more_link_text);
                        $content = ob_get_clean();
                    } else {
                        ob_start();
                        the_content('');
                        $content = ob_get_clean();
                    }
                }
                $content = trim($content);
                ?>
                <?php if (!empty($content)) : ?>
                    <div class="entry-content"><?php
                        print $content;
                        wp_link_pages(array(
                            'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'foodpicky') . '</span>',
                            'after' => '</div>',
                            'link_before' => '<span class="page">',
                            'link_after' => '</span>',
                        ));
                        ?></div>
                <?php endif; ?>        
            <?php endif; ?>        
        <?php endif; ?>
        <?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'footer'))) : ?>
            <div class="entry-footer"><?php print azexo_entry_meta($template_name, 'footer'); ?></div>
        <?php endif; ?>
        <?php print azexo_entry_meta($template_name, 'data'); ?>
    </div>    
    <?php if (!azexo_is_empty(azexo_entry_meta($template_name, 'additions'))) : ?>
        <div class="entry-additions"><?php print azexo_entry_meta($template_name, 'additions'); ?></div>
    <?php endif; ?>
    <?php do_action('azexo_entry_close'); ?>
</div>
<?php
print azexo_entry_meta($template_name, 'next');
?>