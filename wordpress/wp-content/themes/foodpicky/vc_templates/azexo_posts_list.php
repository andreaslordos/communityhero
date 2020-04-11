<?php

$output = $template = $title = $filter = $group = $loop = $posts_clauses = $only_content = $carousel = $full_width = $center = $carousel_stagePadding = $item_margin = $posts_per_item = $el_class = $css = '';
extract(shortcode_atts(array(
    'title' => '',
    'filter' => '',
    'group' => '',
    'posts_clauses' => '',
    'template' => 'post',
    'only_content' => false,
    'item_wrapper' => false,
    'carousel' => false,
    'center' => false,
    'loop' => false,
    'carousel_stagePadding' => 0,
    'item_margin' => 0,
    'posts_per_item' => 1,
    'full_width' => false,
    'el_class' => '',
    'css' => '',
                ), $atts));

$css_class = $el_class;
if (function_exists('vc_shortcode_custom_css_class')) {
    $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $el_class . vc_shortcode_custom_css_class($css, ' '), $this->settings['base'], $atts);
}


$loop_args = azexo_filterQuerySettings(azexo_buildQuery($atts));

if (!empty($posts_clauses) && function_exists($posts_clauses)) {
    add_filter('posts_clauses', $posts_clauses, 10, 2);
}

$loop_args = apply_filters('azexo_posts_list_loop_args', $loop_args);
$query = new WP_Query($loop_args);
$post_type = isset($query->query['post_type']) ? $query->query['post_type'] : 'post';
if (is_array($post_type)) {
    $post_type = $post_type[0];
}

if (!empty($posts_clauses) && function_exists($posts_clauses))
    remove_filter('posts_clauses', $posts_clauses);

if ($carousel) {
    wp_enqueue_script('owl-carousel');
    wp_enqueue_style('owl-carousel');
}

if ($query->have_posts()) {
    $options = get_option(AZEXO_FRAMEWORK);

    if ($only_content) {
        $size = array('width' => '', 'height' => '');
    } else {
        $thumbnail_size = isset($options[$template . '_thumbnail_size']) && !empty($options[$template . '_thumbnail_size']) ? $options[$template . '_thumbnail_size'] : 'large';
        azexo_add_image_size($thumbnail_size);
        $size = azexo_get_image_sizes($thumbnail_size);
    }

    print '<div class="posts-list-wrapper ' . esc_attr($css_class) . '">';
    if (!empty($title) || !empty($title)) {
        print '<div class="list-header">';
    }
    if (!empty($title)) {
        print '<div class="list-title"><h3>' . $title . '</h3></div>';
    }
    if (!empty($filter)) {
        //$filter_all_terms = get_terms($filter);
        $filter_all_terms = azexo_posts_list_filters($query->posts, $filter);
        if (is_array($filter_all_terms) && !empty($filter_all_terms)) {
            print '<div class="list-filter">';
            print '<div class="filter-term active">' . esc_html__('All', 'foodpicky') . '</div>';
            foreach ($filter_all_terms as $term) {
                print '<div class="filter-term" data-term="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</div>';
            }
            print '</div>';
        }
    }
    if (!empty($title) || !empty($title)) {
        print '</div>';
    }
    print '<div class="posts-list ' . ($only_content ? '' : str_replace('_', '-', $template)) . ' ' . ($carousel ? 'owl-carousel' : '') . ' ' . esc_attr($el_class) . '" data-contents-per-item="' . esc_attr($posts_per_item) . '" data-width="' . $size['width'] . '" data-height="' . $size['height'] . '" data-stagePadding="' . esc_attr($carousel_stagePadding) . '" data-margin="' . esc_attr($item_margin) . '" data-full-width="' . esc_attr($full_width) . '" data-center="' . esc_attr($center) . '" data-loop="' . esc_attr($loop) . '">';
    global $post, $wp_query;
    $original = $post;

    $ids = array();
    if (empty($group)) {
        while ($query->have_posts()) {
            $query->the_post();
            $ids[] = $post->ID;
            azexo_posts_list_post($only_content, $template, $filter, $item_wrapper);
        }
    } else {
        $groups = array();
        for ($i = 0; $i < $query->post_count; $i++) {
            $meta_value = get_post_meta($query->posts[$i]->ID, $group, true);
            if (!isset($groups[$meta_value])) {
                $groups[$meta_value] = array();
            }
            $groups[$meta_value][] = $query->posts[$i];
        }
        ksort($groups);
        foreach ($groups as $group_name => $posts) {
            print '<div class="list-group">';
            print '<input id="group-toggle-' . sanitize_title($group_name) . '" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">';
            print '<div id="list-group-' . sanitize_title($group_name) . '" class="group-header"><h4>' . esc_html($group_name) . '</h4><label for="group-toggle-' . sanitize_title($group_name) . '"></label></div>';
            print '<div class="group-posts">';
            for ($i = 0; $i < count($posts); $i++) {
                $post = $posts[$i];
                $ids[] = $post->ID;
                $query->setup_postdata($posts[$i]);
                azexo_posts_list_post($only_content, $template, $filter, $item_wrapper);
            }
            print '</div></div>';
        }
    }

    $wp_query->post = $original;
    wp_reset_postdata();
    
    global $azexo_current_post_stack;
    $first_id = reset($ids);
    $index = count($azexo_current_post_stack);
    while ($index) {
        $index--;
        if ($azexo_current_post_stack[$index]->ID == $first_id) {
            array_splice($azexo_current_post_stack, $index);
        }
    }
    
    print '</div></div>';
}
