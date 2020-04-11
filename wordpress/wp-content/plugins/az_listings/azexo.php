<?php

add_action('azexo_entry_open', 'azl_entry_open');

function azl_entry_open() {
    global $post;
    $location = azl_get_location($post);
    if ($location) {
        print '<script type="application/json" data-post="' . $post->ID . '">';
        print json_encode($location);
        print '</script>';
    }
}
