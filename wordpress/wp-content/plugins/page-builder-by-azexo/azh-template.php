<?php
if (defined('SCRIPT_DEBUG')) {
    if (!SCRIPT_DEBUG && !defined('CONCATENATE_SCRIPTS')) {
        define('CONCATENATE_SCRIPTS', false);
    }
} else {
    define('SCRIPT_DEBUG', true);
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title><?php the_title(); ?></title>
        <?php
        wp_head();
        //global $wp_styles;
        //wp_print_styles(array_values($post_scripts['css']));
        ?>
    </head>
    <body <?php body_class(); ?>>  
        <div class="az-container">
            <?php while (have_posts()) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; ?>            
        </div>
        <?php
        wp_footer();
        //global $wp_scripts;
        //wp_print_scripts(array('azexo-frontend', 'sticky-kit'));
        ?>
    </body>
</html>