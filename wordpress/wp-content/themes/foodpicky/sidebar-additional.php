<?php if (is_active_sidebar('additional_sidebar')) : ?>
    <div id="additional" class="sidebar-container" role="complementary">
        <div class="sidebar-inner">
            <div class="widget-area clearfix">
                <?php dynamic_sidebar('additional_sidebar'); ?>
            </div><!-- .widget-area -->
        </div><!-- .sidebar-inner -->
    </div><!-- #tertiary -->
<?php endif; ?>