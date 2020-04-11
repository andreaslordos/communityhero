<?php if (is_active_sidebar('dashboard_sidebar')) : ?>
    <div id="tertiary" class="sidebar-container" role="complementary">
        <div class="sidebar-inner">
            <div class="widget-area clearfix">
                <?php dynamic_sidebar('dashboard_sidebar'); ?>
            </div><!-- .widget-area -->
        </div><!-- .sidebar-inner -->
    </div><!-- #tertiary -->
<?php endif; ?>