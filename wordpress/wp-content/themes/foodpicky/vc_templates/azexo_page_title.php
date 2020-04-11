<?php

global $post, $wp_query;
$original = $post;
if (azexo_get_closest_current_post('page')) {
    $post = azexo_get_closest_current_post('page');
} else if (azexo_get_closest_current_post(array('vc_widget', 'azh_widget'), false)) {
    $post = azexo_get_closest_current_post(array('vc_widget', 'azh_widget'), false);
}
if ($original->ID != $post->ID) {
    setup_postdata($post);
}

get_template_part('template-parts/general', 'title');

if ($original->ID != $post->ID) {
    $wp_query->post = $original;
    wp_reset_postdata();
}