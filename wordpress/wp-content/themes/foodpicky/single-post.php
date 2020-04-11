<?php
$options = get_option(AZEXO_FRAMEWORK);
if (!isset($show_sidebar)) {
    $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
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
                <?php
                $options = get_option(AZEXO_FRAMEWORK);
                if (isset($options['post_navigation']) && ($options['post_navigation'] == 'before')) {
                    if (isset($options['post_navigation_full']) && $options['post_navigation_full']) {
                        azexo_post_nav_full();
                    } else {
                        azexo_post_nav();
                    }
                }
                ?>
                <?php get_template_part('content', get_post_format()); ?>                

                <?php
                if (isset($options['author_bio']) && $options['author_bio']) {
                    get_template_part('template-parts/general', 'author-bio');
                }
                ?>
                <?php
                if (isset($options['post_navigation']) && ($options['post_navigation'] == 'after')) {
                    if (isset($options['post_navigation_full']) && $options['post_navigation_full']) {
                        azexo_post_nav_full();
                    } else {
                        azexo_post_nav();
                    }
                }
                ?>
                <?php
                if (isset($options['related_posts']) && $options['related_posts']) {
                    if (function_exists('related_posts')) {
                        related_posts(array(
                            'template' => 'yarpp-template-default.php',
                        ));
                    }
                }
                ?>
                <?php
                if (isset($options['comments']) && $options['comments']) {
                    comments_template();
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