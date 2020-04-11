<?php

/*
  Template Name: Blog background image post
 */
?>

<?php

global $wp_query, $azexo_queried_object;
$azexo_queried_object = $wp_query->get_queried_object();


query_posts(array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ),
));

$template_name = 'bg_image_post';
include(get_template_directory() . '/list.php');
?>
