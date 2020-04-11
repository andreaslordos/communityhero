<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Claims_Table extends WP_List_Table {

    function __construct() {

        $this->index = 0;
        parent::__construct(array(
            'singular' => 'order',
            'plural' => 'orders',
            'ajax' => false
        ));
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user' => __('User', 'azl'),
            'post' => __('Post', 'azl'),
            'author' => __('Author', 'azl'),
        );
        return $columns;
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /* $1%s */ 'id',
                /* $2%s */ $item->id
        );
    }

    function prepare_items() {
        global $wpdb;

        $_SERVER['REQUEST_URI'] = remove_query_arg('_wp_http_referer', $_SERVER['REQUEST_URI']);

        $per_page = $this->get_items_per_page('claims_per_page', 10);
        $current_page = $this->get_pagenum();

        $orderby = !empty($_REQUEST['orderby']) ? esc_attr($_REQUEST['orderby']) : 'id';
        $order = (!empty($_REQUEST['order']) && $_REQUEST['order'] == 'asc' ) ? 'ASC' : 'DESC';

        $this->_column_headers = $this->get_column_info();



        $sql = "FROM {$wpdb->posts} as p "
                . "LEFT JOIN {$wpdb->postmeta} as m ON m.post_id = p.id "
                . "LEFT JOIN {$wpdb->users} as u ON m.meta_value = u.id "
                . "LEFT JOIN {$wpdb->users} as a ON p.post_author = a.id "
                . "WHERE m.meta_key = 'claim' "
                . "AND u.ID != a.ID";

        $max = $wpdb->get_var("SELECT COUNT(*) " . $sql);


        $sql = "SELECT m.meta_id as id, u.ID as user_id, u.user_nicename as user_name, p.ID as post_id, p.post_title as post_title, a.ID as author_id, a.user_nicename as author_name " . $sql;


        $offset = ( $current_page - 1 ) * $per_page;

        $sql .= " ORDER BY `{$orderby}` {$order} LIMIT {$offset}, {$per_page}";
        
        $this->items = $wpdb->get_results($sql);

        $this->set_pagination_args(array(
            'total_items' => $max,
            'per_page' => $per_page,
            'total_pages' => ceil($max / $per_page)
        ));
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'user' => array('user_name', false),
            'post' => array('post_title', false),
            'author' => array('author_name', false),
        );
        return $sortable_columns;
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'user':
                return '<a href="' . admin_url('user-edit.php?user_id=' . $item->user_id) . '">' . $item->user_name . '</a>';
            case 'author':
                return '<a href="' . admin_url('user-edit.php?user_id=' . $item->author_id) . '">' . $item->author_name . '</a>';
            case 'post':
                $parent = get_post_ancestors($item->post_id);
                $post_id = $parent ? $parent[0] : $item->post_id;
                return '<a href="' . admin_url('post.php?post=' . $post_id . '&action=edit') . '">' . $item->post_title . '</a>';
        }
    }

}

add_action('admin_menu', 'azl_add_menu_items');

function azl_add_menu_items() {
    $hook = add_submenu_page('azl_options', __('Claims', 'azl'), __('Claims', 'azl'), 'view_woocommerce_reports', 'claims', 'azl_claims', 'dashicons-admin-network', 32);
    add_action("load-$hook", 'azl_claims_add_options');
}

function azl_claims_add_options() {
    global $ClaimsTable;

    $args = array(
        'label' => 'Rows',
        'default' => 10,
        'option' => 'orders_per_page'
    );
    add_screen_option('per_page', $args);

    $ClaimsTable = new Claims_Table();
}

function azl_claims() {
    global $ClaimsTable;

    echo '<div class="wrap"><h2>' . __('Claims', 'azl') . '</h2>';

    $ClaimsTable->prepare_items();
    $ClaimsTable->views();
    $ClaimsTable->display();

    echo '</div>';
}
