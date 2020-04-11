<?php
/*
  Template Name: With right shop sidebar
 */
?>

<?php get_header(); $options = get_option(AZEXO_FRAMEWORK); ?>

<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> active-sidebar right">
    <div id="primary" class="content-area">
        <?php        
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
            <?php while (have_posts()) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                        <?php wp_link_pages(array('before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'foodpicky') . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
                    </div><!-- .entry-content -->
                </div><!-- #post -->
                <?php
                if (isset($options['comments']) && $options['comments']) {
                    if (comments_open()) {
                        comments_template();
                    }
                }
                ?>
            <?php endwhile; ?>
        </div><!-- #content -->
    </div><!-- #primary -->
    <?php get_sidebar('shop'); ?>
</div>
<?php get_footer(); ?>