<?php
if (azexo_is_dashboard()) :
    get_sidebar('dashboard');
else:
    ?>
    <?php if (is_active_sidebar('sidebar')) : ?>
        <div id="tertiary" class="sidebar-container" role="complementary">
            <div class="sidebar-inner">
                <div class="widget-area clearfix">
                    <?php dynamic_sidebar('sidebar'); ?>
                </div><!-- .widget-area -->
            </div><!-- .sidebar-inner -->
        </div><!-- #tertiary -->
    <?php endif; ?>
<?php endif; ?>