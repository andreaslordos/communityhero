<?php

function azexo_azqf_is_vendors_query($query) {
    return $query->is_main_query() && isset($_GET['role']) && isset($_GET['role']) == 'vendor' && is_archive() && azexo_is_post_type_query($query, 'product');
}

add_action('pre_get_posts', 'azexo_azqf_pre_get_posts', 30);

function azexo_azqf_pre_get_posts($query) {
    if (azexo_azqf_is_vendors_query($query)) {
        $azl_profile_meta_keys = get_option('azexo_azl_profile_meta_keys', array());
        $taxonomy_names = get_object_taxonomies('azl_profile');
        global $azexo_vendors_taxonomies, $azexo_vendors_meta_query;
        $azexo_vendors_taxonomies = array();
        foreach ($taxonomy_names as $taxonomy_name) {
            if ($query->get($taxonomy_name)) {
                $azexo_vendors_taxonomies[$taxonomy_name] = $query->get($taxonomy_name);
                unset($query->query_vars[$taxonomy_name]);
            }
        }
        $meta_query = $query->get('meta_query');
        $new_meta_query = array();
        $azexo_vendors_meta_query = array();
        foreach ($meta_query as $meta_rule) {
            if (in_array($meta_rule['key'], $azl_profile_meta_keys)) {
                $azexo_vendors_meta_query[] = $meta_rule;
            } else {
                $new_meta_query[] = $meta_rule;
            }
        }
        $query->set('meta_query', $new_meta_query);
        unset($query->query_vars['meta_key']);

        $vendor_profiles = $query->get('post__in');        
        $query->set('post__in', array());
        if (!empty($vendor_profiles)) {
            $vendor_users = array();
            $posts = get_posts(array(
                'include' => $vendor_profiles,
                'posts_per_page' => -1,
                'post_type' => 'any',
            ));
            if (empty($posts)) {   
                $vendor_users = array(0);
            } else {
                foreach($posts as $post) {
                    $vendor_users[] = $post->post_author;
                }
                $vendor_users = array_unique($vendor_users);                
            }
            $query->set('author__in', $vendor_users);
        }
    }
}

function azexo_azqf_profiles_template($template) {
    $template = get_template_directory() . '/page-templates/vendors-profiles.php';
    return $template;
}

add_filter('posts_distinct', 'azexo_azqf_posts_distinct', 10, 2);

function azexo_azqf_posts_distinct($distinct, $query) {
    if (azexo_azqf_is_vendors_query($query)) {
        global $wpdb;
        return "distinct";
    }
    return $distinct;
}

add_filter('posts_groupby', 'azexo_azqf_clear_posts_piece', 10, 2);
add_filter('posts_orderby', 'azexo_azqf_clear_posts_piece', 10, 2);
add_filter('posts_orderby_request', 'azexo_azqf_clear_posts_piece', 10, 2);

function azexo_azqf_clear_posts_piece($piece, $query) {
    if (azexo_azqf_is_vendors_query($query)) {
        return "";
    }
    return $piece;
}

add_filter('posts_fields', 'azexo_azqf_posts_fields', 10, 2);

function azexo_azqf_posts_fields($fields, $query) {
    if (azexo_azqf_is_vendors_query($query)) {
        global $wpdb;
        return "$wpdb->posts.post_author";
    }
    return $fields;
}

add_filter('posts_pre_query', 'azexo_azqf_posts_pre_query', 10, 2);

function azexo_azqf_posts_pre_query($posts, $query) {
    if (azexo_azqf_is_vendors_query($query)) {
        global $wpdb;
        $author_ids = $wpdb->get_col($query->request);
        $author_ids = array_unique($author_ids);
        $author_ids = array_map('intval', $author_ids);
        global $azexo_vendors_author__in;
        $azexo_vendors_author__in = empty($author_ids) ? array(0) : $author_ids;
        $posts = array(); //prevent get_post calls
        add_filter('template_include', 'azexo_azqf_profiles_template', 20);
    }
    return $posts;
}
