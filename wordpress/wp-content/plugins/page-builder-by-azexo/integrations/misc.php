<?php

if (isset($_GET['azh']) && $_GET['azh'] == 'customize') {
    add_filter('wp_smush_should_skip_parse', '__return_true');
}