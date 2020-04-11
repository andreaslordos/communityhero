<?php
global $wp_query;
$post_type = isset($wp_query->query['post_type']) ? $wp_query->query['post_type'] : 'post';
if (is_array($post_type)) {
    $post_type = reset($post_type);
}
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
}
if (!isset($template_name)) {
    $template_name = isset($options['default_' . $post_type . '_template']) ? $options['default_' . $post_type . '_template'] : 'post';
}
if (isset($_GET['template'])) {
    $template_name = $_GET['template'];
}
$additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
get_header();
$have_posts = have_posts();
if ($have_posts && (isset($options['before_list_place']) && ($options['before_list_place'] == 'before_container'))) {
    azexo_before_list($post_type);
}
?>
<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('sidebar') && ($show_sidebar != 'hidden') ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('list', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
    <?php
    if ($show_sidebar == 'left') {
        get_sidebar();
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
    <div id="primary" class="content-area">
        <?php
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        if ($have_posts && (isset($options['before_list_place']) && ($options['before_list_place'] == 'inside_content_area'))) {
            azexo_before_list($post_type);
        }
        ?>
        <div id="content" class="site-content <?php print str_replace('_', '-', $template_name); ?> <?php print ((isset($options['infinite_scroll']) && $options['infinite_scroll']) ? 'infinite-scroll' : '') ?>" role="main">
            <?php
            if (isset($options['author_bio']) && $options['author_bio']) {
                if (is_author()) {
                    get_template_part('template-parts/general', 'author-bio');
                }
            }
            ?>
            <?php if ($have_posts) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php include(get_template_directory() . '/content.php'); ?>
                <?php endwhile; ?>
            <?php else: ?>
                <?php include(get_template_directory() . '/content-none.php'); ?>
            <?php endif; ?>

        </div><!-- #content -->
        <?php
        if ($have_posts) {
            azexo_paging_nav();
        }
        ?>
    </div><!-- #primary -->

    <?php
    if ($show_sidebar == 'right') {
        get_sidebar();
    } else {
        if (in_array('list', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>
<?php get_footer(); ?>