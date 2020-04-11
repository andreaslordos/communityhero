<?php
add_action('admin_init', 'azl_options_init');

function azl_options_init() {
    register_setting('azl_options', 'azl_options');
}

add_action('admin_menu', 'azl_options_page');

function azl_options_page() {
    if (function_exists('cmb2_metabox_form')) {
        $options_page = add_menu_page(__('Listings', 'azl'), __('Listings', 'azl'), 'manage_options', 'azl_options', 'azl_admin_page_display');
        add_action("admin_print_styles-{$options_page}", array('CMB2_hookup', 'enqueue_cmb_css'));
    }
}

function azl_admin_page_display() {
    wp_enqueue_style('azl-jqueryui', (is_ssl() ? 'https' : 'http') . '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jsoneditor', plugins_url('json-editor/dist/jsoneditor.js', __FILE__));

    wp_enqueue_script('azl-admin', plugins_url('js/azl-admin.js', __FILE__));
    wp_enqueue_style('azl-admin', plugins_url('css/azl-admin.css', __FILE__));

    $post_types = get_post_types(array(), 'objects');
    $post_types_options = array();
    if (is_array($post_types) && !empty($post_types)) {
        foreach ($post_types as $slug => $post_type) {
            if ($slug !== 'revision' && $slug !== 'nav_menu_item') {
                $post_types_options[$slug] = $post_type->label;
            }
        }
    }

    $taxonomies = get_taxonomies(array(), 'objects');
    $taxonomy_options = array();
    foreach ($taxonomies as $slug => $taxonomy) {
        $taxonomy_options[$slug] = $taxonomy->label;
    }
    ?>
    <script type="text/javascript">
        window.azl = {};
        window.azl.post_types = <?php print json_encode($post_types_options); ?>;
        window.azl.taxonomies = <?php print json_encode($taxonomy_options); ?>;
        window.azl.registered_query_vars = <?php global $wp;
    print json_encode(array_merge($wp->public_query_vars, $wp->private_query_vars));
    ?>;
    </script>
    <div class="wrap cmb2-options-page <?php echo 'azl_options'; ?>">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <?php cmb2_metabox_form('azl_option_metabox', 'azl_options'); ?>
    </div>
    <?php
}

add_action('cmb2_admin_init', 'azl_options_page_metabox');

function azl_options_page_metabox() {
    $post_types = get_post_types();
    $taxonomies = get_taxonomies(array(), 'objects');
    $taxonomy_options = array();
    foreach ($taxonomies as $slug => $taxonomy) {
        $taxonomy_options[$slug] = $taxonomy->label;
    }
    $products = get_posts(array(
        'numberposts' => -1,
        'post_type' => 'product',
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    $products_options = array();
    if (is_array($products)) {
        foreach ($products as $product) {
            $products_options[$product->ID] = $product->post_title;
        }
    }
    add_action("cmb2_save_options-page_fields_azl_option_metabox", 'azl_settings_notices', 10, 2);
    $cmb = new_cmb2_box(array(
        'id' => 'azl_option_metabox',
        'hookup' => false,
        'cmb_styles' => false,
        'show_on' => array(
            'key' => 'options-page',
            'value' => array('azl_options')
        ),
    ));
    $cmb->add_field(array(
        'name' => __('Subsciption post type', 'azl'),
        'desc' => __('If choose - all posts with selected type of correspond author will be automatically hidden based on his subscription limits', 'azl'),
        'id' => 'subsciption_post_type',
        'type' => 'select',
        'show_option_none' => true,
        'options' => array_combine($post_types, $post_types),
        'default' => '',
    ));
    $cmb->add_field(array(
        'name' => __('Default free subsciption product', 'azl'),
        'desc' => __('If choose - all registered users will have this subsciption free of charge', 'azl'),
        'id' => 'default_subsciption',
        'type' => 'select',
        'show_option_none' => true,
        'options' => $products_options,
        'default' => '',
    ));

    $cmb->add_field(array(
        'name' => __('Google Map API key', 'azl'),
        'id' => 'gmap_api_key',
        'type' => 'text',
    ));

    $cmb->add_field(array(
        'name' => __('Google Map location taxonomy (if exists)', 'azl'),
        'desc' => __('This taxonomy will be removed from query arguments in AJAX locations auto-load when user darging map', 'azl'),
        'id' => 'gmap_location_taxonomy',
        'type' => 'select',
        'show_option_none' => true,
        'options' => $taxonomy_options,
        'default' => '',
    ));

    $cmb->add_field(array(
        'name' => __('Google Map infobox image size', 'azl'),
        'id' => 'gmap_image_size',
        'type' => 'select',
        'options' => array_combine(get_intermediate_image_sizes(), get_intermediate_image_sizes()),
        'default' => 'shop_catalog',
    ));

    $cmb->add_field(array(
        'name' => __('Google Map marker image taxonomy', 'azl'),
        'desc' => __('Taxonomy term must have thumbnail_id meta value', 'azl'),
        'id' => 'gmap_marker_image_taxonomy',
        'type' => 'select',
        'show_option_none' => true,
        'options' => $taxonomy_options,
        'default' => 'product_cat',
    ));

    $cmb->add_field(array(
        'name' => __('Google Map styles', 'azl'),
        'id' => 'gmap_styles',
        'type' => 'textarea_code',
        'default' => '[{"featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{"color": "#333333"}]}, {"featureType": "landscape", "elementType": "all", "stylers": [{"color": "#f5f5f5"}]}, {"featureType": "poi", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "road", "elementType": "all", "stylers": [{"saturation": -100}, {"lightness": 45}]}, {"featureType": "road.highway", "elementType": "all", "stylers": [{"visibility": "simplified"}]}, {"featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {"featureType": "transit", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "water", "elementType": "all", "stylers": [{"color": "#ffffff"}, {"visibility": "on"}]}]'
    ));

    $cmb->add_field(array(
        'name' => __('Google Map marker image', 'azl'),
        'id' => 'gmap_marker_image',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'default' => AZL_URL . '/images/marker.png'
    ));

    $cmb->add_field(array(
        'name' => __('Google Map cluster styles', 'azl'),
        'id' => 'gmap_cluster_styles',
        'type' => 'textarea_code',
        'default' => '[{height: 38, width: 42, textColor: "#ffffff"}]'
    ));

    $cmb->add_field(array(
        'name' => __('Google Map cluster image', 'azl'),
        'id' => 'gmap_cluster_image',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'default' => AZL_URL . '/images/cluster.png'
    ));

    $cmb->add_field(array(
        'name' => __('Page with abuse form', 'azl'),
        'id' => 'abuse',
        'type' => 'post_search_text',
        'post_type' => 'page',
        'select_type' => 'radio',
        'select_behavior' => 'replace',
    ));

    $group_field_id = $cmb->add_field(array(
        'id' => 'forms',
        'type' => 'group',
        'options' => array(
            'group_title' => __('Submission form {#}', 'azl'),
            'add_button' => __('Add New Form', 'azl'),
            'remove_button' => __('Remove Form', 'azl'),
        ),
    ));
    $cmb->add_group_field($group_field_id, array(
        'name' => __('Form name', 'azl'),
        'id' => 'name',
        'type' => 'text',
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('Page with shortcode', 'azl'),
        'id' => 'page',
        'type' => 'post_search_text',
        'post_type' => 'page',
        'select_type' => 'radio',
        'select_behavior' => 'replace',
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('Post type', 'azl'),
        'id' => 'post_type',
        'type' => 'select',
        'show_option_none' => true,
        'options' => array_combine($post_types, $post_types),
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('General tab title', 'azl'),
        'id' => 'general_title',
        'type' => 'text',
        'defualt' => __('General', 'azl'),
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('General tab description', 'azl'),
        'id' => 'general_desc',
        'type' => 'text',
        'defualt' => __('Listing general info.', 'azl'),
    ));
    $cmb->add_group_field($group_field_id, array(
        'name' => __('General tab class', 'azl'),
        'id' => 'general_class',
        'type' => 'text',
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => 'General fields',
        'id' => 'general_fields',
        'type' => 'multicheck_inline',
        'options' => array(
            'title' => __('Title', 'azl'),
            'description' => __('Description', 'azl'),
            'short_description' => __('Short description', 'azl'),
        )
    ));
    $cmb->add_group_field($group_field_id, array(
        'name' => __('General fields wrapper class', 'azl'),
        'id' => 'general_wrapper_class',
        'type' => 'text'
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('Title template', 'azl'),
        'id' => 'title_template',
        'type' => 'text',
        'desc' => __('If title not exists in form', 'azl'),
    ));

    $cmb->add_group_field($group_field_id, array(
        'name' => __('Form settings', 'azl'),
        'id' => 'form',
        'type' => 'textarea_code',
        'options' => array( 'disable_codemirror' => true ),
    ));
}

function azl_settings_notices($object_id, $updated) {
    if ($object_id !== 'azl_options' || empty($updated)) {
        return;
    }
    add_settings_error('azl_options-notices', '', __('Settings updated.', 'azl'), 'updated');
    settings_errors('azl_options-notices');
}
