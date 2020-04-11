<?php
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options[get_post_type() . '_show_sidebar']) ? $options[get_post_type() . '_show_sidebar'] : 'right';
    if ($show_sidebar == 'hidden') {
        $show_sidebar = false;
    }
}
$additional_sidebar = isset($options[get_post_type() . '_additional_sidebar']) ? (array) $options[get_post_type() . '_additional_sidebar'] : array();
get_header();
?>

<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('sidebar') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('single', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
    <?php
    if ($show_sidebar == 'left') {
        get_sidebar();
    } else {
        if (in_array('single', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
    <div id="primary" class="content-area">
        <?php
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('content', get_post_format()); ?>                

                <?php
                if (isset($options['comments']) && $options['comments'] && !azexo_is_dashboard()) {
                    if (comments_open()) {
                        comments_template();
                    }
                }
                ?>

            <?php endwhile; ?>

        </div><!-- #content -->
    </div><!-- #primary -->

    <?php
    if ($show_sidebar == 'right') {
        get_sidebar();
    } else {
        if (in_array('single', $additional_sidebar)) {
            get_sidebar('additional');
        }
    }
    ?>
</div>
<?php get_footer(); ?>