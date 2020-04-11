<?php if (is_active_sidebar('middle_sidebar')) : ?>
    <div id="middle" class="sidebar-container <?php $options = get_option(AZEXO_FRAMEWORK); print ((isset($options['middle_sidebar_fullwidth']) && $options['middle_sidebar_fullwidth']) ? '' : 'container'); ?>" role="complementary">
        <div class="sidebar-inner">
            <div class="widget-area clearfix">
                <?php dynamic_sidebar('middle_sidebar'); ?>
            </div><!-- .widget-area -->
        </div><!-- .sidebar-inner -->
    </div><!-- #middle -->
<?php endif; ?>