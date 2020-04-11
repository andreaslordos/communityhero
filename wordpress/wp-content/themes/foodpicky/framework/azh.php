<?php

add_filter('azh_directory', 'azexo_azh_directory');

function azexo_azh_directory($dir) {
    if (isset($dir[get_template_directory() . '/azh'])) {
        unset($dir[get_template_directory() . '/azh']);
    }
    $dir[get_template_directory() . '/azh/' . azexo_get_skin()] = get_template_directory_uri() . '/azh/' . azexo_get_skin();
    return $dir;
}

add_filter('azh_uri', 'azexo_azh_uri');

function azexo_azh_uri($uri) {
    return get_template_directory_uri() . '/azh/' . azexo_get_skin();
}

add_filter('azh_options', 'azexo_azh_options');

function azexo_azh_options($options) {
    return $options;
}

if (file_exists(get_template_directory() . '/azh/' . azexo_get_skin() . '.php')) {
    require_once(get_template_directory() . '/azh/' . azexo_get_skin() . '.php');
}