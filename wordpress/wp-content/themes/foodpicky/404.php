<?php
get_header();
$options = get_option(AZEXO_FRAMEWORK);
?>

<div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?>">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">

            <?php if ($options['show_page_title']) get_template_part('template-parts/general', 'title') ?>
            <div class="page-wrapper">
                <div class="page-content">
                    <h2><?php esc_html_e('This is somewhat embarrassing, isn&rsquo;t it?', 'foodpicky'); ?></h2>
                    <p><?php esc_html_e('It looks like nothing was found at this location.', 'foodpicky'); ?></p>
                    <?php azexo_get_search_form(); ?>                    
                </div><!-- .page-content -->
            </div><!-- .page-wrapper -->

        </div><!-- #content -->
    </div><!-- #primary -->
</div>
<?php get_footer(); ?>