<?php
add_filter('azexo_templates', 'azexo_azlp_templates');

function azexo_azlp_templates($azexo_templates) {
    $azexo_templates['single_profile'] = esc_html__('Single profile', 'foodpicky');
    $azexo_templates['list_profile'] = esc_html__('List profile', 'foodpicky');
    return $azexo_templates;
}

add_filter('azexo_template_name', 'azexo_azlp_template_name');

function azexo_azlp_template_name($template_name) {
    if (in_array(get_post_type(), array('azl_profile'))) {
        return 'single_profile';
    }
    return $template_name;
}

add_filter('azexo_fields', 'azexo_azlp_fields');

function azexo_azlp_fields($azexo_fields) {
    return array_merge($azexo_fields, array(
        'profile_thumbnail' => esc_html__('Profile: Thumbnail', 'foodpicky'),
        'profile_rating' => esc_html__('Profile: Average rating', 'foodpicky'),
        'profile_review_count' => esc_html__('Profile: Review count', 'foodpicky'),
        'profile_last_review_rating' => esc_html__('Profile: last review rating', 'foodpicky'),
        'profile_link' => esc_html__('Profile: Link to author profile', 'foodpicky'),
        'profile_products_count' => esc_html__('Profile: Products count', 'foodpicky'),
    ));
}

add_filter('azexo_fields_post_types', 'azexo_azlp_fields_post_types');

function azexo_azlp_fields_post_types($azexo_fields_post_types) {
    $azexo_fields_post_types['profile_thumbnail'] = 'azl_profile';
    $azexo_fields_post_types['profile_rating'] = 'azl_profile';
    $azexo_fields_post_types['profile_review_count'] = 'azl_profile';
    $azexo_fields_post_types['profile_last_review_rating'] = 'azl_profile';
    $azexo_fields_post_types['profile_link'] = '';
    $azexo_fields_post_types['profile_products_count'] = '';
    return $azexo_fields_post_types;
}

add_action('wp_update_comment_count', 'azexo_azlp_update_comment_count');

function azexo_azlp_update_comment_count($post_id) {
    delete_post_meta($post_id, '_azlp_average_rating');
    delete_post_meta($post_id, '_azlp_rating_count');
    delete_post_meta($post_id, '_azlp_review_count');
}

add_filter('azexo_settings_sections', 'azexo_azlp_settings_sections');

function azexo_azlp_settings_sections($sections) {

    $sections[] = array(
        'type' => 'divide',
    );

    $sections[] = array(
        'icon' => 'el-icon-cogs',
        'title' => esc_html__('Profiles templates configuration', 'foodpicky'),
        'fields' => array(
            array(
                'id' => 'profile_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Profile" labels', 'foodpicky'),
                'default' => 'Profile',
            ),
            array(
                'id' => 'profiles_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Profiles" labels', 'foodpicky'),
                'default' => 'Profiles',
            ),
            array(
                'id' => 'redirect_product_to_profile',
                'type' => 'checkbox',
                'title' => esc_html__('Redirect product to profile', 'foodpicky'),
                'default' => '0',
            ),
        )
    );

    return $sections;
}

add_filter('azexo_entry_field', 'azexo_azlp_entry_field', 10, 2);

function azexo_azlp_entry_field($output, $name) {
    global $post, $azexo_fields_post_types, $wp_query;
    $original = false;

    if (is_array($azexo_fields_post_types) && isset($azexo_fields_post_types[$name]) && $azexo_fields_post_types[$name] == 'azl_profile') {
        if ($post->post_type == 'product') {
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'author' => $post->post_author,
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $original = $post;
                $post = $profile;
                setup_postdata($profile);
            }
        }
    }

    switch ($name) {
        case 'profile_thumbnail':
            ob_start();
            azexo_post_thumbnail_field();
            $thumbnail = ob_get_clean();
            $thumbnail = trim($thumbnail);
            $output = empty($thumbnail) ? '' : '<div class="entry-thumbnail">' . $thumbnail . '</div>';
            break;
        case 'profile_products_count':
            $posts = get_posts(array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'author' => $post->post_author,
                'ignore_sticky_posts' => 1,
                'no_found_rows' => 1,
                'posts_per_page' => '-1',
            ));
            $count = count($posts);
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => $post->post_author,
                'ignore_sticky_posts' => 1,
                'no_found_rows' => 1,
                'posts_per_page' => '-1',
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $products = '<a href="' . esc_url(get_permalink($profile)) . '"><span class="count">' . esc_html($count) . '</span><span class="label">' . esc_html__('products', 'foodpicky') . '</span></a>';
                $output = '<span class="products-count">' . $products . '</span>';
            }
            break;
        case 'profile_link':
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => $post->post_author,
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $options = get_option(AZEXO_FRAMEWORK);
                $label = (isset($options['profile_link_prefix']) && !empty($options['profile_link_prefix'])) ? esc_html($options['profile_link_prefix']) : esc_html__('Profile', 'foodpicky');
                $output = '<div class="azl-profile"><a href="' . esc_url(get_permalink($profile)) . '">' . esc_html($label) . '</a></div>';
            }
            break;
        case 'profile_rating':
            if (!metadata_exists('post', $post->ID, '_azlp_rating_count')) {
                global $wpdb;

                $counts = array();
                $raw_counts = $wpdb->get_results($wpdb->prepare("
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = 'rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		", $post->ID));

                foreach ($raw_counts as $count) {
                    $counts[$count->meta_value] = $count->meta_value_count;
                }

                update_post_meta($post->ID, '_azlp_rating_count', $counts);
            }
            $counts = get_post_meta($post->ID, '_azlp_rating_count', true);
            $rating_count = array_sum($counts);

            $average = 0;
            if (!metadata_exists('post', $post->ID, '_azlp_average_rating')) {
                if ($rating_count) {
                    global $wpdb;

                    $ratings = $wpdb->get_var($wpdb->prepare("
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = 'rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			", $post->ID));
                    $average = number_format($ratings / $rating_count, 2, '.', '');
                }
                update_post_meta($post->ID, '_azlp_average_rating', $average);
            }
            $average = floatval(get_post_meta($post->ID, '_azlp_average_rating', true));

            if ($rating_count > 0) {
                ob_start();
                ?>
                <div class="star-rating" title="<?php printf(esc_html__('Rated %s out of 5', 'foodpicky'), $average); ?>">
                    <span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
                        <strong class="rating"><?php echo esc_html($average); ?></strong> <?php printf(esc_html__('out of %s5%s', 'foodpicky'), '<span>', '</span>'); ?>
                        <?php printf(_n('based on %s user rating', 'based on %s user ratings', $rating_count, 'foodpicky'), '<span class="rating">' . $rating_count . '</span>'); ?>
                    </span>
                </div>
                <?php
                $output = ob_get_clean();
            }
            break;
        case 'profile_review_count':
            if (!metadata_exists('post', $post->ID, '_azlp_review_count')) {
                global $wpdb;
                $count = $wpdb->get_var($wpdb->prepare("
				SELECT COUNT(*) FROM $wpdb->comments
				WHERE comment_parent = 0
				AND comment_post_ID = %d
				AND comment_approved = '1'
			", $post->ID));

                update_post_meta($post->ID, '_azlp_review_count', $count);
            } else {
                $count = get_post_meta($post->ID, '_azlp_review_count', true);
            }


            if (comments_open()) {
                ob_start();
                ?>
                <a href="#reviews" class="profile-review-link" rel="nofollow">(<?php printf(_n('%s customer review', '%s customer reviews', $count, 'foodpicky'), '<span class="count">' . $count . '</span>'); ?>)</a>
                <?php
                $output = ob_get_clean();
            }
            break;
        case 'profile_last_review_rating':
            $args = array(
                'post_id' => $post->ID,
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                if ($rating) {
                    $rating_html = '<div class="star-rating" title="' . sprintf(esc_html__('Rated %s out of 5', 'foodpicky'), $rating) . '">';
                    $rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . esc_html__('out of 5', 'foodpicky') . '</span>';
                    $rating_html .= '</div>';
                    $output = $rating_html;
                }
            }
            break;
    }
    if ($original) {
        $wp_query->post = $original;
        wp_reset_postdata();
    }
    return $output;
}

add_filter('azexo_entry_field_meta_field', 'azexo_azlp_entry_field_meta_field', 10, 2);

function azexo_azlp_entry_field_meta_field($value, $name) {
    if ($value == '') {
        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'author' => get_the_author_meta('ID'),
        ));
        if (!empty($posts)) {
            $profile = reset($posts);
            return get_post_meta($profile->ID, $name, true);
        }
    }
    return $value;
}

add_filter('azexo_entry_field_taxonomy_field', 'azexo_azlp_entry_field_taxonomy_field', 10, 2);

function azexo_azlp_entry_field_taxonomy_field($term_list, $name) {
    if ($term_list === false) {
        $taxonomies = get_object_taxonomies('azl_profile');
        $slug = str_replace('taxonomy_', '', $name);
        if (in_array($slug, $taxonomies)) {
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'post_status' => 'publish',
                'author' => get_the_author_meta('ID'),
            ));
            if (!empty($posts)) {
                $profile = reset($posts);
                $term_list = get_the_term_list($profile->ID, $slug, '', '<span class="delimiter">,</span> ', '');
                return $term_list;
            }
        }
    }
    return $term_list;
}

add_action('azsl_social_login_insert_user', 'azexo_azlp_social_login_insert_user');

function azexo_azlp_social_login_insert_user($user_id) {
    if (isset($_POST['picture'])) {
        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'post_status' => 'publish',
            'author' => $user_id,
        ));
        if (!empty($posts)) {
            $profile = reset($posts);

            wp_update_post(array(
                'ID' => $profile->ID,
                'post_title' => sanitize_text_field($_POST['name']),
            ));

            $file_array = array();
            $file_array['name'] = basename(untrailingslashit(esc_url($_POST['picture'])));
            $file_array['tmp_name'] = download_url(esc_url($_POST['picture']));
            if (!is_wp_error($file_array['tmp_name'])) {
                $id = media_handle_sideload($file_array, $profile->ID);
                if (!is_wp_error($id)) {
                    set_post_thumbnail($profile, $id);
                }
            }
        }
    }
}

add_filter('azexo_review_allowed', 'azexo_azlp_review_allowed', 10, 4);

function azexo_azlp_review_allowed($allowed, $customer_email, $user_id, $post) {
    if ($post->post_type == 'azl_profile') {
        return true;
    }
    return $allowed;
}

add_filter('azexo_dashboard_links', 'azexo_azlp_dashboard_links');

function azexo_azlp_dashboard_links($links) {

    if (class_exists('WC_Vendors')) {
        if (WCV_Vendors::is_vendor(get_current_user_id())) {

            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'author' => get_current_user_id(),
            ));
            $profile = reset($posts);
            if ($profile) {
                $edit_profile = 0;
                if (function_exists('cmb2_get_option')) {
                    $forms = cmb2_get_option('azl_options', 'forms');
                    if (is_array($forms)) {
                        foreach ($forms as $form) {
                            if ($form['post_type'] == 'azl_profile') {
                                if (isset($form['page']) && is_numeric($form['page'])) {
                                    $edit_profile = $form['page'];
                                }
                            }
                        }
                    }
                }
                $links = array_merge(array(
                    array(
                        'id' => $profile->ID,
                        'url' => esc_url(get_permalink($profile)),
                        'title' => esc_html__('My Profile', 'foodpicky'),
                    ),
                    array(
                        'id' => $edit_profile,
                        'url' => esc_url(add_query_arg(array('azl' => 'edit', 'id' => $profile->ID))),
                        'title' => esc_html__('Edit Profile', 'foodpicky'),
                    ),), $links);
            }
        }
    }


    return $links;
}

add_action('azsl_social_login', 'azexo_azlp_social_login');

function azexo_azlp_social_login($user_id) {
    if (class_exists('WCV_Vendors')) {
        if (WCV_Vendors::is_vendor($user_id)) {
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'author' => $user_id,
            ));
            $profile = reset($posts);
            if ($profile) {
                print esc_url(get_permalink($profile));
                return;
            }
        }
    }
    if (function_exists('wc_get_page_id')) {
        print esc_url(get_permalink(wc_get_page_id('myaccount')));
    }
}

function azexo_azlp_closest_profile_author_filter($args, $query) {
    global $wpdb;

    $profile_post = azexo_get_closest_current_post('azl_profile');
    if ($profile_post) {
        $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql($profile_post->post_author) . ") ";
    }

    return $args;
}

function azexo_azlp_closest_product_author_filter($args, $query) {
    global $wpdb;

    $product_post = azexo_get_closest_current_post('product');
    if ($product_post) {
        $args['where'] .= " AND ( $wpdb->posts.post_author = " . esc_sql($product_post->post_author) . ") ";
    }

    return $args;
}

add_action('comment_post', 'azexo_azlp_comment_post', 10, 1);

function azexo_azlp_comment_post($comment_id) {
    if ('azl_profile' === get_post_type($_POST['comment_post_ID'])) {
        $review_marks = azexo_review_marks();
        if (empty($review_marks)) {
            if (isset($_POST['rating'])) {
                if (!$_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0) {
                    return;
                }
                add_comment_meta($comment_id, 'rating', (int) esc_attr($_POST['rating']), true);
            }
        } else {
            $rating = 0;
            foreach ($review_marks as $slug => $label) {
                if (isset($_POST[$slug])) {
                    if (!$_POST[$slug] || $_POST[$slug] > 5 || $_POST[$slug] < 0) {
                        continue;
                    }
                    add_comment_meta($comment_id, $slug, (int) esc_attr($_POST[$slug]), true);
                    $rating += (int) $_POST[$slug];
                }
            }
            $rating = number_format($rating / count($review_marks), 1);
            delete_comment_meta($comment_id, 'rating');
            add_comment_meta($comment_id, 'rating', $rating, true);
        }
    }
}

add_filter('comments_template', 'azexo_azlp_comments_template');

function azexo_azlp_comments_template($template) {
    if (get_post_type() == 'azl_profile') {
        wp_enqueue_script('wc-single-product');

        $check_dirs = array(
            trailingslashit(get_stylesheet_directory()) . WC()->template_path(),
            trailingslashit(get_template_directory()) . WC()->template_path(),
            trailingslashit(get_stylesheet_directory()),
            trailingslashit(get_template_directory()),
            trailingslashit(WC()->plugin_path()) . 'templates/'
        );

        if (WC_TEMPLATE_DEBUG_MODE) {
            $check_dirs = array(array_pop($check_dirs));
        }

        foreach ($check_dirs as $dir) {
            if (file_exists(trailingslashit($dir) . 'single-product-reviews.php')) {
                return trailingslashit($dir) . 'single-product-reviews.php';
            }
        }
    }
    return $template;
}

add_action('template_redirect', 'azexo_azlp_template_redirect');

function azexo_azlp_template_redirect() {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['redirect_product_to_profile']) && $options['redirect_product_to_profile']) {
        if (function_exists('is_product') && is_product()) {
            global $post;
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'author' => $post->post_author,
            ));
            $profile = reset($posts);
            if ($profile) {
                exit(wp_redirect(get_permalink($profile)));
            }
        }
    }

    if (class_exists('WCV_Vendors') && WCV_Vendors::is_vendor_page()) {
        $vendor_shop = urldecode(get_query_var('vendor_shop'));
        $vendor_id = WCV_Vendors::get_vendor_id($vendor_shop);

        $posts = get_posts(array(
            'post_type' => 'azl_profile',
            'author' => $vendor_id,
        ));
        $profile = reset($posts);
        if ($profile) {
            exit(wp_redirect(get_permalink($profile)));
        }
    }
}

add_filter('update_post_metadata', 'azexo_azlp_update_post_metadata', 10, 5);

function azexo_azlp_update_post_metadata($check, $object_id, $meta_key, $meta_value, $prev_value) {
    if ($meta_key == '_wp_page_template' && $meta_value == 'page-templates/vendors-profiles.php') {
        update_option('azexo_vendors_profiles', $object_id);
        if (get_option('azexo_profiles') == $object_id) {
            update_option('azexo_profiles', 0);
        }
    }
    if ($meta_key == '_wp_page_template' && $meta_value == 'page-templates/profiles.php') {
        update_option('azexo_profiles', $object_id);
        if (get_option('azexo_vendors_profiles') == $object_id) {
            update_option('azexo_vendors_profiles', 0);
        }
    }
    return $check;
}

add_filter('azl_register_post_type_profile', 'azexo_azlp_register_post_type_profile');

function azexo_azlp_register_post_type_profile($post_type) {
    if (get_option('azexo_vendors_profiles') === false) {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-templates/vendors-profiles.php'
        ));
        if (!empty($pages)) {
            $page = reset($pages);
            update_option('azexo_vendors_profiles', $page->ID);
        } else {
            update_option('azexo_vendors_profiles', 0);
        }
    }
    if (get_option('azexo_profiles') === false) {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-templates/profiles.php'
        ));
        if (!empty($pages)) {
            $page = reset($pages);
            update_option('azexo_profiles', $page->ID);
        } else {
            update_option('azexo_profiles', 0);
        }
    }
    $page_id = false;
    if (isset($_GET['role'])) {
        if (isset($_GET['role']) == 'vendor') {
            $page_id = get_option('azexo_vendors_profiles');
        } else {
            $page_id = get_option('azexo_profiles');
        }
    } else {
        if (!azexo_is_empty(get_option('azexo_vendors_profiles'))) {
            $page_id = get_option('azexo_vendors_profiles');
        } else if (!azexo_is_empty(get_option('azexo_profiles'))) {
            $page_id = get_option('azexo_profiles');
        }
    }
    if (!empty($page_id)) {
        $page = get_post($page_id);
        if ($page) {
            $post_type['labels']['name'] = $page->post_title;
            $post_type['labels']['singular_name'] = $page->post_title;
            $post_type['has_archive'] = get_page_uri($page);
        }
    }
    if (!is_admin()) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (isset($options['profile_label']) && isset($options['profiles_label']) && !empty($options['profile_label']) && !empty($options['profiles_label'])) {
            $post_type['labels']['name'] = $options['profiles_label'];
            $post_type['labels']['singular_name'] = $options['profile_label'];
        }
    }

    return $post_type;
}

add_action('pre_get_posts', 'azexo_azlp_pre_get_posts', 30);

function azexo_azlp_pre_get_posts($query) {
    if (!is_admin()) {
        if (azexo_is_post_type_query($query, 'azl_profile')) {
            if($query->is_main_query()) {
                if (isset($_GET['role'])) {
                    $ids = get_users(array('role' => sanitize_text_field($_GET['role']), 'fields' => 'ID'));
                    if (empty($ids)) {
                        $ids = array(0);
                    }
                    $query->set('author__in', $ids);
                } else {
                    if (!azexo_is_empty(get_option('azexo_vendors_profiles')) && azexo_is_empty(get_option('azexo_profiles'))) {
                        $ids = get_users(array('role' => 'vendor', 'fields' => 'ID'));
                        if (empty($ids)) {
                            $ids = array(0);
                        }
                        $query->set('author__in', $ids);
                    }
                }
            } else {
                if(empty($query->get('author'))) {
                    $ids = get_users(array('role' => 'vendor', 'fields' => 'ID'));
                    if (empty($ids)) {
                        $ids = array(0);
                    }
                    $query->set('author__in', $ids);                
                }
            }
        }
    }
}

function azexo_azlp_get_author_posts($post_author) {
    $ids = get_posts(array(
        'fields' => 'ids',
        'author' => $post_author,
        'post_type' => 'product', //need - not in azl_profile
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows' => 1,
        'posts_per_page' => '-1',
    ));
    return $ids;
}

add_filter('azexo_posts_list_post_terms', 'azexo_azlp_posts_list_post_terms', 10, 3);

function azexo_azlp_posts_list_post_terms($terms, $post, $taxonomy) {
    if ($post->post_type == 'azl_profile') {
        $taxonomy_names = get_object_taxonomies('azl_profile');
        if (!in_array($taxonomy, $taxonomy_names)) {
            $terms = wp_get_object_terms(azexo_azlp_get_author_posts($post->post_author), $taxonomy, array('fields' => 'all'));
        }
    }
    return $terms;
}

add_action('woocommerce_before_checkout_form', 'azexo_woo_minimum_order_amount');
add_action('woocommerce_before_cart', 'azexo_woo_minimum_order_amount');
add_action('woocommerce_before_mini_cart', 'azexo_woo_minimum_order_amount');

function azexo_woo_minimum_order_amount() {
    if (class_exists('WC_Vendors')) {
        if (isset(WC()->cart) && isset(WC()->cart->cart_contents) && !empty(WC()->cart->cart_contents)) {
            $vendors = array();
            foreach (WC()->cart->cart_contents as $line) {
                if (isset($line['data'])) {
                    $product = get_post($line['data']->get_id());
                    if (WCV_Vendors::is_vendor($product->post_author) || $product->post_author == 1) {
                        if (!isset($vendors[$product->post_author])) {
                            $posts = get_posts(array(
                                'post_type' => 'azl_profile',
                                'author' => $product->post_author,
                            ));
                            $profile = reset($posts);
                            if ($profile) {
                                $minimum_order_amount = get_post_meta($profile->ID, 'minimum_order_amount', true);
                                if (!empty($minimum_order_amount)) {
                                    $vendors[$product->post_author] = array(
                                        'profile' => $profile,
                                        'minimum_order_amount' => $minimum_order_amount,
                                        'lines' => array(),
                                    );
                                }
                            }
                        }
                        if (isset($vendors[$product->post_author])) {
                            $vendors[$product->post_author]['lines'][] = $line;
                        }
                    }
                }
            }
            foreach ($vendors as $vendor) {
                $total = 0;
                foreach ($vendor['lines'] as $line) {
                    $total += $line['line_total'];
                }
                if (isset($vendor['minimum_order_amount'])) {
                    if (floatval($total) < floatval($vendor['minimum_order_amount'])) {
                        wc_add_notice(sprintf(esc_html__('%s: minimum order amount %s, current order total is %s.', 'foodpicky'), $vendor['profile']->post_title, wc_price($vendor['minimum_order_amount']), wc_price($total)), 'error');
                    }
                }
            }
            wc_print_notices();
        }
    }
}

add_filter('azexo_get_closest_current_post', 'azexo_azlp_get_closest_current_post', 10, 3);

function azexo_azlp_get_closest_current_post($current_post, $post_type, $equal) {
    if ($equal && in_array('azl_profile', $post_type) && is_null($current_post)) {
        $product = azexo_get_closest_current_post('product');
        if (!is_null($product)) {
            $posts = get_posts(array(
                'post_type' => 'azl_profile',
                'author' => $product->post_author,
            ));
            $profile = reset($posts);
            if ($profile) {
                return $profile;
            }
        }
    }
    return $current_post;
}

add_filter('wpcf7_form_hidden_fields', 'azexo_azlp_form_hidden_fields');

function azexo_azlp_form_hidden_fields($hidden_fields) {
    $profile_post = azexo_get_closest_current_post('azl_profile');
    if ($profile_post) {
        $hidden_fields['_profile_author'] = $profile_post->post_author;
    }
    return $hidden_fields;
}

add_action('wpcf7_before_send_mail', 'azexo_azlp_before_send_mail');

function azexo_azlp_before_send_mail($contact_form) {
    if ($submission = WPCF7_Submission::get_instance()) {
        if (!azexo_is_empty($submission->get_posted_data('_profile_author')) && is_numeric($submission->get_posted_data('_profile_author'))) {
            $properties = $contact_form->get_properties();
            $user_info = get_userdata($submission->get_posted_data('_profile_author'));
            $properties['mail']['recipient'] = $user_info->user_email;
            $contact_form->set_properties($properties);
        }
    }
}
