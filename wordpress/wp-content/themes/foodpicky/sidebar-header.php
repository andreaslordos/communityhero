<?php if (is_active_sidebar('header_sidebar')) : ?>
    <div id="secondary" class="sidebar-container <?php $options = get_option(AZEXO_FRAMEWORK); print ((isset($options['header_sidebar_fullwidth']) && $options['header_sidebar_fullwidth']) ? '' : 'container'); ?>" role="complementary">
        <div class="sidebar-inner">
            <div class="widget-area clearfix">
                <?php dynamic_sidebar('header_sidebar'); ?>
            </div><!-- .widget-area -->
        </div><!-- .sidebar-inner -->
    </div><!-- #secondary -->
<?php endif; ?>