<?php

/*
  Template Name: Profiles
 */
?>

<?php

global $wp_query, $azexo_queried_object;
$azexo_queried_object = get_post(get_option('azexo_profiles'));
if (empty($azexo_queried_object)) {
    $azexo_queried_object = $wp_query->get_queried_object();
}

query_posts(array(
    'post_type' => 'azl_profile',
    'post_status' => 'publish',
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ),
));

include(get_template_directory() . '/list.php');
?>
