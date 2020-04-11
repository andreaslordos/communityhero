<?php
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <?php $options = get_option(AZEXO_FRAMEWORK); ?>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>">        
        <?php
        if (!function_exists('has_site_icon') || !has_site_icon()) {
            if (isset($options['favicon']['url']) && !empty($options['favicon']['url'])) {
                print '<link rel="shortcut icon" href="' . esc_url($options['favicon']['url']) . '" />';
            }
        }
        ?>
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>        
        <div id="preloader"><div id="status"></div></div>
        <div id="page" class="hfeed site">
            <header id="masthead" class="site-header clearfix">
                <?php
                get_sidebar('header');
                ?>                
                <div class="header-main clearfix">
                    <div class="header-parts <?php print ((isset($options['header_parts_fullwidth']) && $options['header_parts_fullwidth']) ? '' : 'container'); ?>">
                        <?php
                        azexo_header_parts();
                        ?>                        
                    </div>
                </div>
                <?php
                get_sidebar('middle');
                ?>                                
            </header><!-- #masthead -->
            <div id="main" class="site-main">
