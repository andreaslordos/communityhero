<?php

/*
  Template Name: Vendors profiles
 */
?>

<?php

global $wp_query, $azexo_queried_object, $azexo_vendors_author__in, $azexo_vendors_taxonomies, $azexo_vendors_meta_query;
$azexo_queried_object = get_post(get_option('azexo_vendors_profiles'));
if (empty($azexo_queried_object)) {
    $azexo_queried_object = $wp_query->get_queried_object();
}
$azexo_vendors_taxonomies = isset($azexo_vendors_taxonomies) ? $azexo_vendors_taxonomies : array();
$azexo_vendors_meta_query = isset($azexo_vendors_meta_query) ? $azexo_vendors_meta_query : array();
if (empty($azexo_vendors_author__in)) {
    add_filter('posts_where', 'azexo_woo_vendors_where_filter');
}
query_posts(array_merge(array(
    'post_type' => 'azl_profile',
    'post_status' => 'publish',
    'author__in' => empty($azexo_vendors_author__in) ? array() : $azexo_vendors_author__in,
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ),
    'meta_query' => $azexo_vendors_meta_query,
), $azexo_vendors_taxonomies));
if (empty($azexo_vendors_author__in)) {
    remove_filter('posts_where', 'azexo_woo_vendors_where_filter');
}

include(get_parent_theme_file_path('list.php'));
?>
