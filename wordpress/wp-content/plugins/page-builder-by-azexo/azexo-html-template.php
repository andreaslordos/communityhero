<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <?php 
        wp_head();
        ?>
    </head>
    <body <?php body_class(); ?>>  
        <?php if (is_active_sidebar('azh_header')) : ?>
            <div class="az-container">
                <?php dynamic_sidebar('azh_header'); ?>
            </div>
        <?php endif; ?>
        <?php while (have_posts()) : the_post(); ?>
            <div <?php post_class(); ?>>
                <?php the_content(); ?>
            </div><!-- #post -->
        <?php endwhile; ?>
        <?php if (is_active_sidebar('azh_footer')) : ?>
            <div class="az-container">
                <?php dynamic_sidebar('azh_footer'); ?>
            </div>
        <?php endif; ?>
        <?php 
        wp_footer(); 
        ?>
    </body>
</html>