<?php
if (azexo_is_dashboard()) :
    get_sidebar('dashboard');
else:
    ?>
    <?php if (is_active_sidebar('shop')) : ?>
        <div id="tertiary" class="sidebar-container shop" role="complementary">
            <div class="sidebar-inner">
                <div class="widget-area clearfix">
                    <?php dynamic_sidebar('shop'); ?>
                </div><!-- .widget-area -->
            </div><!-- .sidebar-inner -->
        </div><!-- #tertiary -->
    <?php endif; ?>
<?php endif; ?>