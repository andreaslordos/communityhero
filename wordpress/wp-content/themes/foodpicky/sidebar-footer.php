<?php if (is_active_sidebar('footer_sidebar')) : ?>
    <div id="quaternary" class="sidebar-container <?php $options = get_option(AZEXO_FRAMEWORK); print ((isset($options['footer_sidebar_fullwidth']) && $options['footer_sidebar_fullwidth']) ? '' : 'container'); ?>" role="complementary">
        <div class="sidebar-inner">
            <div class="widget-area clearfix">
                <?php dynamic_sidebar('footer_sidebar'); ?>
            </div><!-- .widget-area -->
        </div><!-- .sidebar-inner -->
    </div><!-- #quaternary -->
<?php endif; ?>