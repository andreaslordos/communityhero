<?php

function azh_azexo_header() {
    $options = get_option(AZEXO_FRAMEWORK);
    ?>
    <div id="preloader"><div id="status"></div></div>
    <div id="page" class="hfeed site">
        <header id="masthead" class="site-header clearfix">
            <?php
            locate_template(array('sidebar-header.php'), true, false);
            ?>                
            <div class="header-main clearfix">
                <div class="header-parts <?php print ((isset($options['header_parts_fullwidth']) && $options['header_parts_fullwidth']) ? '' : 'container'); ?>">
                    <?php
                    azexo_header_parts();
                    ?>                        
                </div>
            </div>
            <?php
            locate_template(array('sidebar-middle.php'), true, false);
            ?>                                
        </header><!-- #masthead -->
        <div id="main" class="site-main">
            <?php
        }

        function azh_azexo_footer() {
            ?>
        </div><!-- #main -->
        <footer id="colophon" class="site-footer clearfix">
            <?php
            locate_template(array('sidebar-footer.php'), true, false);
            ?>
        </footer><!-- #colophon -->
    </div><!-- #page -->                
    <?php
    wp_print_scripts(array('azexo-frontend', 'sticky-kit'));
}

function azh_azexo_page($page) {
    azh_azexo_header();
    ?>
    <div id="primary" class="content-area">
        <?php
        $options = get_option(AZEXO_FRAMEWORK);
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
            <div id="post-<?php print $page->ID; ?>" <?php post_class(array('entry', 'page'), $page->ID); ?>>
                <div class="entry-content">
                    <?php print apply_filters('the_content', $page->post_content); ?>
                </div><!-- .entry-content -->
            </div><!-- #post -->
        </div><!-- #content -->
    </div><!-- #primary -->
    <?php
    azh_azexo_footer();
}

function azh_azexo_with_container($page) {
    azh_azexo_header();
    ?>
    <div class="container">
        <div id="primary" class="content-area">
            <div id="content" class="site-content" role="main">
                <div <?php post_class('entry', $page->ID); ?>>
                    <div class="entry-content">
                        <?php print apply_filters('the_content', $page->post_content); ?>
                    </div><!-- .entry-content -->
                </div><!-- #post -->
            </div><!-- #content -->
        </div><!-- #primary -->
    </div>
    <?php
    azh_azexo_footer();
}

function azh_azexo_without_container($page) {
    azh_azexo_header();
    ?>
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <div <?php post_class('entry', $page->ID); ?>>
                <div class="entry-content">
                    <?php print apply_filters('the_content', $page->post_content); ?>
                </div><!-- .entry-content -->
            </div><!-- #post -->
        </div><!-- #content -->
    </div><!-- #primary -->
    <?php
    azh_azexo_footer();
}

function azh_azexo_list($query) {
    azh_azexo_header();
    $post_type = isset($query->query['post_type']) ? $query->query['post_type'] : 'post';
    if (is_array($post_type)) {
        $post_type = reset($post_type);
    }
    $options = get_option(AZEXO_FRAMEWORK);
    if (!isset($show_sidebar)) {
        $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
    }
    if (!isset($template_name)) {
        $template_name = isset($options['default_' . $post_type . '_template']) ? $options['default_' . $post_type . '_template'] : 'post';
    }
    $additional_sidebar = isset($options[$post_type . '_additional_sidebar']) ? (array) $options[$post_type . '_additional_sidebar'] : array();
    $have_posts = $query->have_posts();
    if ($have_posts && (isset($options['before_list_place']) && ($options['before_list_place'] == 'before_container'))) {
        azexo_before_list($post_type);
    }
    ?>
    <div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('sidebar') && ($show_sidebar != 'hidden') ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('list', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
        <?php
        if ($show_sidebar == 'left') {
            locate_template(array('sidebar.php'), true, false);
        } else {
            if (in_array('list', $additional_sidebar)) {
                locate_template(array('sidebar-additional.php'), true, false);
            }
        }
        ?>
        <div id="primary" class="content-area">
            <?php
            if ($options['show_page_title']) {
                get_template_part('template-parts/general', 'title');
            }
            if ($have_posts && (isset($options['before_list_place']) && ($options['before_list_place'] == 'inside_content_area'))) {
                azexo_before_list($post_type);
            }
            ?>
            <div id="content" class="site-content <?php print str_replace('_', '-', $template_name); ?> <?php print ((isset($options['infinite_scroll']) && $options['infinite_scroll']) ? 'infinite-scroll' : '') ?>" role="main">
                <?php
                if (isset($options['author_bio']) && $options['author_bio']) {
                    if (is_author()) {
                        get_template_part('template-parts/general', 'author-bio');
                    }
                }
                ?>
                <?php if ($have_posts) : ?>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php include(azexo_locate_template('content.php')); ?>
                    <?php endwhile; ?>
                <?php endif; ?>

            </div><!-- #content -->
            <?php
            if ($have_posts) {
                azexo_paging_nav();
            }
            ?>
        </div><!-- #primary -->

        <?php
        if ($show_sidebar == 'right') {
            locate_template(array('sidebar.php'), true, false);
        } else {
            if (in_array('list', $additional_sidebar)) {
                locate_template(array('sidebar-additional.php'), true, false);
            }
        }
        ?>
    </div>
    <?php
    azh_azexo_footer();
}

function azh_azexo_single_post() {
    azh_azexo_header();
    $options = get_option(AZEXO_FRAMEWORK);
    if (!isset($show_sidebar)) {
        $show_sidebar = isset($options['show_sidebar']) ? $options['show_sidebar'] : 'right';
    }
    $additional_sidebar = isset($options[get_post_type() . '_additional_sidebar']) ? (array) $options[get_post_type() . '_additional_sidebar'] : array();
    ?>
    <div class="<?php print ((isset($options['content_fullwidth']) && $options['content_fullwidth']) ? '' : 'container'); ?> <?php print (is_active_sidebar('sidebar') && $show_sidebar ? 'active-sidebar ' . esc_attr($show_sidebar) : ''); ?> <?php print (in_array('single', $additional_sidebar) ? 'additional-sidebar' : ''); ?>">
        <?php
        if ($show_sidebar == 'left') {
            locate_template(array('sidebar.php'), true, false);
        } else {
            if (in_array('single', $additional_sidebar)) {
                locate_template(array('sidebar-additional.php'), true, false);
            }
        }
        ?>
        <div id="primary" class="content-area">
            <?php
            if ($options['show_page_title']) {
                get_template_part('template-parts/general', 'title');
            }
            ?>
            <div id="content" class="site-content" role="main">
                <?php
                if (isset($options['post_navigation']) && ($options['post_navigation'] == 'before')) {
                    if (isset($options['post_navigation_full']) && $options['post_navigation_full']) {
                        azexo_post_nav_full();
                    } else {
                        azexo_post_nav();
                    }
                }
                ?>
                <?php get_template_part('content', get_post_format()); ?>                

                <?php
                if (isset($options['author_bio']) && $options['author_bio']) {
                    get_template_part('template-parts/general', 'author-bio');
                }
                ?>
                <?php
                if (isset($options['post_navigation']) && ($options['post_navigation'] == 'after')) {
                    if (isset($options['post_navigation_full']) && $options['post_navigation_full']) {
                        azexo_post_nav_full();
                    } else {
                        azexo_post_nav();
                    }
                }
                ?>
                <?php
                if (isset($options['related_posts']) && $options['related_posts']) {
                    if (function_exists('related_posts')) {
                        related_posts(array(
                            'template' => 'yarpp-template-default.php',
                        ));
                    }
                }
                ?>
                <?php
                if (isset($options['comments']) && $options['comments']) {
                    comments_template();
                }
                ?>
            </div><!-- #content -->
        </div><!-- #primary -->

        <?php
        if ($show_sidebar == 'right') {
            locate_template(array('sidebar.php'), true, false);
        } else {
            if (in_array('single', $additional_sidebar)) {
                locate_template(array('sidebar-additional.php'), true, false);
            }
        }
        ?>
    </div>    
    <?php
    azh_azexo_footer();
}
