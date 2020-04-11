<?php
global $azexo_woo_templates;
$azexo_woo_templates = array(
    'single_product' => esc_html__('Single product', 'foodpicky'), //default template
    'shop_product' => esc_html__('Shop product', 'foodpicky'), //default template
    'detailed_shop_product' => esc_html__('Detailed shop product', 'foodpicky'), //fixed in shop modes
    'related_product' => esc_html__('Related product', 'foodpicky'), //fixed in related template
    'upsells_product' => esc_html__('Up-sells product', 'foodpicky'), //fixed in up-sells template
);
add_filter('azexo_templates', 'azexo_woo_templates');

function azexo_woo_templates($azexo_templates) {
    global $azexo_woo_templates;
    return array_merge($azexo_templates, $azexo_woo_templates);
}

global $azexo_woo_fields;
$azexo_woo_fields = array(
    'purchased' => esc_html__('Product purchased', 'foodpicky'),
    'discount' => esc_html__('Product discount', 'foodpicky'),
    'sale_time_left' => esc_html__('Product sale time left', 'foodpicky'),
    'availability' => esc_html__('Product availability', 'foodpicky'),
    'last_review_rating' => esc_html__('Product last review rating', 'foodpicky'),
    'loop_sale_flash' => esc_html__('Loop product sale flash', 'foodpicky'),
    'loop_rating' => esc_html__('Loop product average rating', 'foodpicky'),
    'loop_price' => esc_html__('Loop product price', 'foodpicky'),
    'loop_add_to_cart' => esc_html__('Loop product add to cart link', 'foodpicky'),
    'product_thumbnail' => esc_html__('Product thumbnail', 'foodpicky'),
    'product_gallery' => esc_html__('Product gallery', 'foodpicky'),
    'single_sale_flash' => esc_html__('Single product sale flash', 'foodpicky'),
    'single_add_to_cart' => esc_html__('Single product add to cart', 'foodpicky'),
    'single_rating' => esc_html__('Single product average rating', 'foodpicky'),
    'single_price' => esc_html__('Single product price', 'foodpicky'),
    'single_meta' => esc_html__('Single product meta', 'foodpicky'),
    'single_sharing' => esc_html__('Single product sharing', 'foodpicky'),
    'single_data_tabs' => esc_html__('Single data tabs', 'foodpicky'),
    'single_related_products' => esc_html__('Single related products', 'foodpicky'),
    'single_upsell_display' => esc_html__('Single upsell display', 'foodpicky'),
    'single_description' => esc_html__('Single description', 'foodpicky'),
    'single_reviews' => esc_html__('Single reviews', 'foodpicky'),
    'single_additional_information' => esc_html__('Single additional information', 'foodpicky'),
);

if (class_exists('WCV_Vendor_Shop')) {
    $azexo_fields['loop_sold_by'] = esc_html__('Loop product sold by', 'foodpicky');
}
if (class_exists('YITH_WCWL_Shortcode')) {
    $azexo_fields['add_to_wishlist'] = esc_html__('Add product to wishlist', 'foodpicky');
}
if (class_exists('YITH_WCQV_Frontend')) {
    $azexo_fields['quick_view'] = esc_html__('Product quick view', 'foodpicky');
}

add_filter('azexo_fields', 'azexo_woo_fields');

function azexo_woo_fields($azexo_fields) {
    global $azexo_woo_fields;
    return array_merge($azexo_fields, $azexo_woo_fields);
}

add_filter('azexo_fields_post_types', 'azexo_woo_fields_post_types');

function azexo_woo_fields_post_types($azexo_fields_post_types) {
    global $azexo_woo_fields;
    $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_woo_fields), array_fill(0, count(array_keys($azexo_woo_fields)), 'product')));
    return $azexo_fields_post_types;
}

add_action('after_setup_theme', 'azexo_woo_after_setup_theme');

function azexo_woo_after_setup_theme() {
    add_theme_support('woocommerce');
}

add_filter('azexo_settings_sections', 'azexo_woo_settings_sections');

function azexo_woo_settings_sections($sections) {

    $sections[] = array(
        'type' => 'divide',
    );

    $sections[] = array(
        'icon' => 'el-icon-cogs',
        'title' => esc_html__('WooCommerce templates configuration', 'foodpicky'),
        'fields' => array(
            array(
                'id' => 'shop_title',
                'type' => 'text',
                'title' => esc_html__('Shop page title', 'foodpicky'),
                'default' => 'Shop',
            ),
            array(
                'id' => 'product_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Product" labels', 'foodpicky'),
                'default' => 'Product',
            ),
            array(
                'id' => 'products_label',
                'type' => 'text',
                'title' => esc_html__('Alter "Products" labels', 'foodpicky'),
                'default' => 'Products',
            ),
            array(
                'id' => 'review_form_placeholders',
                'type' => 'checkbox',
                'title' => esc_html__('Use placeholders in review form', 'foodpicky'),
                'default' => '0',
            ),
            array(
                'id' => 'custom_sorting',
                'type' => 'select',
                'multi' => true,
                'sortable' => true,
                'title' => esc_html__('Custom sorting', 'foodpicky'),
                'options' => array(
                    'menu_order' => esc_html__('Default sorting', 'foodpicky'),
                    'popularity' => esc_html__('Sort by popularity', 'foodpicky'),
                    'rating' => esc_html__('Sort by average rating', 'foodpicky'),
                    'date' => esc_html__('Sort by newness', 'foodpicky'),
                    'price' => esc_html__('Sort by price: low to high', 'foodpicky'),
                    'price-desc' => esc_html__('Sort by price: high to low', 'foodpicky'),
                ),
                'default' => array('menu_order', 'popularity', 'rating', 'date', 'price', 'price-desc'),
            ),
            array(
                'id' => 'custom_sorting_numeric_meta_keys',
                'type' => 'multi_text',
                'title' => esc_html__('Custom sorting numeric meta keys', 'foodpicky'),
            ),
            array(
                'id' => 'shop_modes',
                'type' => 'checkbox',
                'title' => esc_html__('Show shop modes', 'foodpicky'),
                'default' => '0',
            ),
            array(
                'id' => 'show_data_tabs',
                'type' => 'checkbox',
                'title' => esc_html__('Show data tabs', 'foodpicky'),
                'default' => '1',
            ),
            array(
                'id' => 'show_related_products',
                'type' => 'checkbox',
                'title' => esc_html__('Show related products', 'foodpicky'),
                'default' => '1',
            ),
            array(
                'id' => 'show_upsells_products',
                'type' => 'checkbox',
                'title' => esc_html__('Show upsells products', 'foodpicky'),
                'default' => '1',
            ),
            array(
                'id' => 'upsells_products_carousel_margin',
                'type' => 'text',
                'title' => esc_html__('Up-sells products carousel margin', 'foodpicky'),
                'default' => '0',
            ),
            array(
                'id' => 'related_products_carousel_margin',
                'type' => 'text',
                'title' => esc_html__('Related products carousel margin', 'foodpicky'),
                'default' => '0',
            ),
            array(
                'id' => 'show_ratings_summary',
                'type' => 'checkbox',
                'title' => esc_html__('Show ratings summary', 'foodpicky'),
                'default' => '0',
            ),
            array(
                'id' => 'review_marks',
                'type' => 'multi_text',
                'title' => esc_html__('Review marks', 'foodpicky'),
            ),
            array(
                'id' => 'review_likes',
                'type' => 'checkbox',
                'title' => esc_html__('Review likes', 'foodpicky'),
                'default' => '0',
            ),
        )
    );

    return $sections;
}

function azexo_woo_tgmpa_register() {

    $plugins = array(
        array(
            'name' => esc_html__('WooCommerce', 'foodpicky'),
            'slug' => 'woocommerce',
            'required' => true,
        ),
    );
    if (class_exists('WooCommerce')) {
        $plugins[] = array(
            'name' => esc_html__('WC Vendors', 'foodpicky'),
            'slug' => 'wc-vendors',
        );
    }
    tgmpa($plugins, array());
}

add_action('tgmpa_register', 'azexo_woo_tgmpa_register');

add_filter('woocommerce_enqueue_styles', '__return_false');
add_action('widgets_init', 'azexo_woo_widgets_init');

function azexo_woo_widgets_init() {
    if (function_exists('register_sidebar')) {
        register_sidebar(array('name' => 'Shop sidebar', 'id' => "shop", 'before_widget' => '<div id="%1$s" class="widget %2$s">', 'after_widget' => '</div>', 'before_title' => '<div class="widget-title"><h3>', 'after_title' => '</h3></div>'));
    }
}

add_action('init', 'azexo_woo_init', 11);

function azexo_woo_init() {
    $options = get_option(AZEXO_FRAMEWORK);


    if (class_exists('WooCommerce')) {
        $lightbox = get_option('woocommerce_enable_lightbox');
        if ($lightbox == 'yes') {
            update_option('woocommerce_enable_lightbox', '');
        }

        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

        if (isset($options['show_breadcrumbs']) && !$options['show_breadcrumbs']) {
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        }

        remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

        //if (isset($options['show_data_tabs']) && !$options['show_data_tabs']) {
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
        //}


        remove_action('woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10);
        remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10);
        remove_action('woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10);


        if (isset($options['show_related_products']) && !$options['show_related_products']) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        }
        if (isset($options['show_upsells_products']) && !$options['show_upsells_products']) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
        }
    }

    if (class_exists('WCV_Vendor_Shop')) {
        remove_action('woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9);
        remove_action('woocommerce_product_meta_start', array('WCV_Vendor_Cart', 'sold_by_meta'), 10, 2);

        remove_filter('post_type_archive_link', array('WCV_Vendor_Shop', 'change_archive_link')); //function make infinite recursion
        add_filter('admin_init', array('WCV_Vendor_Shop', 'change_archive_link')); //FIX from https://wordpress.org/support/topic/nesting-level
    }
    if (class_exists('YITH_WCWL_Shortcode')) {
        $position = get_option('yith_wcwl_button_position');
        if ($position != 'shortcode') {
            update_option('yith_wcwl_button_position', 'shortcode');
        }
    }
}

add_filter('woocommerce_register_post_type_product', 'azexo_woo_register_post_type_product');

function azexo_woo_register_post_type_product($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (isset($options['product_label']) && isset($options['products_label']) && !empty($options['product_label']) && !empty($options['products_label'])) {
            $args['labels']['name'] = $options['products_label'];
            $args['labels']['singular_name'] = $options['product_label'];
        }
    }
    return $args;
}

add_filter('woocommerce_taxonomy_args_product_cat', 'azexo_woo_taxonomy_args_product_cat');

function azexo_woo_taxonomy_args_product_cat($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (isset($options['product_label']) && !empty($options['product_label'])) {
            $args['label'] = $options['product_label'] . ' ' . esc_html__('Categories', 'foodpicky');
            $args['labels']['name'] = $options['product_label'] . ' ' . esc_html__('Categories', 'foodpicky');
            $args['labels']['singular_name'] = $options['product_label'] . ' ' . esc_html__('Category', 'foodpicky');
        }
    }
    return $args;
}

add_filter('woocommerce_taxonomy_args_product_tag', 'azexo_woo_taxonomy_args_product_tag');

function azexo_woo_taxonomy_args_product_tag($args) {
    if (!is_admin()) {
        $options = get_option(AZEXO_FRAMEWORK);
        if (isset($options['product_label']) && !empty($options['product_label'])) {
            $args['label'] = $options['product_label'] . ' ' . esc_html__('Tags', 'foodpicky');
            $args['labels']['name'] = $options['product_label'] . ' ' . esc_html__('Tags', 'foodpicky');
            $args['labels']['singular_name'] = $options['product_label'] . ' ' . esc_html__('Tag', 'foodpicky');
        }
    }
    return $args;
}

add_action('wp_enqueue_scripts', 'azexo_woo_scripts');

function azexo_woo_scripts() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('azexo-woo', get_template_directory_uri() . '/js/woocommerce.js', array('jquery', 'select2'), AZEXO_FRAMEWORK_VERSION, true);
        wp_localize_script('azexo-woo', 'azexo_woo', array(
            'myaccounturl' => esc_url(get_permalink(wc_get_page_id('myaccount'))),
        ));
    }
}

add_action('wp_enqueue_scripts', 'azexo_woo_styles');

function azexo_woo_styles() {
    //move styles to header for HTML5 validation
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('select2', str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/' . 'css/select2.css');
    }
}

function azexo_woo_sale_time_left($label = '') {
    $sale_expire = get_post_meta(get_the_ID(), '_sale_price_dates_to', true);
    azexo_time_left($sale_expire, $label);
}

add_action('after_setup_theme', 'azexo_woo_remove_quick_view_button');

function azexo_woo_remove_quick_view_button() {
    if (class_exists('YITH_WCQV_Frontend')) {
        remove_action('woocommerce_after_shop_loop_item', array(YITH_WCQV_Frontend(), 'yith_add_quick_view_button'), 15);

        remove_action('yith_wcqv_product_image', 'woocommerce_show_product_sale_flash', 10);
        remove_action('yith_wcqv_product_image', 'woocommerce_show_product_images', 20);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_title', 5);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_rating', 10);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_price', 15);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_excerpt', 20);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25);
        remove_action('yith_wcqv_product_summary', 'woocommerce_template_single_meta', 30);
        add_action('yith_wcqv_product_summary', 'azexo_woo_product_summary');
    }
}

add_action('woocommerce_after_subcategory_title', 'azexo_woo_after_subcategory_title');

function azexo_woo_after_subcategory_title($category) {
    print '<div class="description">' . esc_html($category->description) . '</div>';
}

function azexo_woo_product_summary() {
    $located = wc_locate_template('content-product.php');
    if (file_exists($located)) {
        $product_template = 'single_product';
        $azexo_woo_base_tag = 'div';
        include( $located );
    }
}

add_filter('azexo_entry_field', 'azexo_woo_entry_field', 10, 2);

function azexo_woo_entry_field($output, $name) {
    global $product, $post;
    $options = get_option(AZEXO_FRAMEWORK);

    $image = (isset($options[$name . '_image']) && !empty($options[$name . '_image']['url'])) ? '<img src="' . esc_html($options[$name . '_image']['url']) . '" alt="">' : '';
    $label = (isset($options[$name . '_prefix']) && !empty($options[$name . '_prefix'])) ? '<label>' . esc_html($options[$name . '_prefix']) . '</label>' : '';

    switch ($name) {
        case 'product_thumbnail':
            ob_start();
            azexo_woo_product_thumbnail_field();
            $thumbnail = ob_get_clean();
            $thumbnail = trim($thumbnail);
            return empty($thumbnail) ? '' : '<div class="entry-thumbnail">' . $thumbnail . '</div>';
            break;
        case 'product_gallery':
            ob_start();
            azexo_woo_product_gallery_field();
            $gallery = ob_get_clean();
            $gallery = trim($gallery);
            return empty($gallery) ? '' : '<div class="entry-gallery">' . $gallery . '</div>';
            break;
        case 'purchased':
            return '<span class="purchased">' . $label . esc_html(get_post_meta(get_the_ID(), 'total_sales', true)) . '</span>';
            break;
        case 'discount':
            if ($product->is_on_sale() && $product->get_regular_price() > 0) {
                $discount = round((wc_get_price_to_display($product, array('price' => $product->get_sale_price())) / wc_get_price_to_display($product, array('price' => $product->get_regular_price())) - 1) * 100);
                if (!empty($discount)) {
                    $discount = $discount . '%';
                    return '<span class="discount">' . $discount . '</span>';
                }
            }
            break;
        case 'sale_time_left':
            ob_start();
            azexo_woo_sale_time_left((isset($options[$name . '_prefix']) && !empty($options[$name . '_prefix'])) ? esc_html($options[$name . '_prefix']) : '');
            return ob_get_clean();
            break;
        case 'availability':
            $availability = $product->get_availability();
            $availability_html = empty($availability['availability']) ? '' : '<p class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</p>';
            return apply_filters('woocommerce_stock_html', $availability_html, $availability['availability'], $product);
            break;
        case 'last_review_rating':
            $args = array(
                'post_id' => get_the_ID(),
                'number' => '1',
            );
            $comments = get_comments($args); //get_comments have caching
            $comment = reset($comments);
            if ($comment) {
                $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                if ($rating) {
                    return wc_get_rating_html($rating);
                }
            }
            break;
        case 'loop_sale_flash':
            ob_start();
            woocommerce_show_product_loop_sale_flash();
            return ob_get_clean();
            break;
        case 'loop_rating':
            ob_start();
            woocommerce_template_loop_rating();
            return ob_get_clean();
            break;
        case 'loop_price':
            ob_start();
            woocommerce_template_loop_price();
            return ob_get_clean();
            break;
        case 'loop_add_to_cart':
            ob_start();
            woocommerce_template_loop_add_to_cart();
            return ob_get_clean();
            break;
        case 'loop_sold_by':
            ob_start();
            WCV_Vendor_Shop::template_loop_sold_by($product->get_id());
            return ob_get_clean();
            break;
        case 'single_sale_flash':
            ob_start();
            woocommerce_show_product_sale_flash();
            return ob_get_clean();
            break;
        case 'single_rating':
            ob_start();
            woocommerce_template_single_rating();
            return ob_get_clean();
            break;
        case 'single_price':
            ob_start();
            woocommerce_template_single_price();
            return ob_get_clean();
            break;
        case 'single_add_to_cart':
            ob_start();
            woocommerce_template_single_add_to_cart();
            return ob_get_clean();
            break;
        case 'single_meta':
            ob_start();
            woocommerce_template_single_meta();
            return ob_get_clean();
            break;
        case 'single_sharing':
            ob_start();
            woocommerce_template_single_sharing();
            return ob_get_clean();
            break;
        case 'single_data_tabs':
            ob_start();
            woocommerce_output_product_data_tabs();
            return ob_get_clean();
            break;
        case 'single_related_products':
            ob_start();
            woocommerce_output_related_products();
            return ob_get_clean();
            break;
        case 'single_upsell_display':
            ob_start();
            woocommerce_upsell_display();
            return ob_get_clean();
            break;
        case 'single_description':
            ob_start();
            woocommerce_product_description_tab();
            return ob_get_clean();
            break;
        case 'single_reviews':
            ob_start();
            if (comments_open()) {
                comments_template();
            }
            return ob_get_clean();
            break;
        case 'single_additional_information':
            ob_start();
            woocommerce_product_additional_information_tab();
            return ob_get_clean();
            break;
        case 'add_to_wishlist':
            if (class_exists('YITH_WCWL_Shortcode')) {
                return YITH_WCWL_Shortcode::add_to_wishlist(array());
            }
            break;
        case 'quick_view':
            if (class_exists('YITH_WCQV_Frontend')) {
                wp_enqueue_script('jquery-flexslider');
                ob_start();
                YITH_WCQV_Frontend()->yith_add_quick_view_button();
                return ob_get_clean();
            }
            break;
    }
    return $output;
}

function azexo_woo_get_images_links($thumbnail_size) {
    $images_links = array();
    $product = wc_get_product();
    if (is_object($product)) {
        $attachment_ids = $product->get_gallery_image_ids();
        if ($attachment_ids) {
            foreach ($attachment_ids as $attachment_id) {
                $image_link = azexo_get_attachment_thumbnail($attachment_id, $thumbnail_size, true);
                if (!empty($image_link))
                    $images_links[] = $image_link[0];
            }
        }
    }
    return $images_links;
}

function azexo_woo_product_thumbnail_field($product_template = false) {
    if (!$product_template) {
        $product_template = get_post_type();
    }
    $options = get_option(AZEXO_FRAMEWORK);
    $thumbnail_size = isset($options[$product_template . '_thumbnail_size']) && !empty($options[$product_template . '_thumbnail_size']) ? $options[$product_template . '_thumbnail_size'] : 'large';
    azexo_add_image_size($thumbnail_size);
    $image_thumbnail = isset($options[$product_template . '_image_thumbnail']) ? $options[$product_template . '_image_thumbnail'] : false;

    $size = azexo_get_image_sizes($thumbnail_size);
    $zoom = isset($options[$product_template . '_zoom']) && !empty($options[$product_template . '_zoom']) ? 'zoom' : '';
    $lazy = isset($options[$product_template . '_lazy']) && !empty($options[$product_template . '_lazy']) ? $options[$product_template . '_lazy'] : false;
    if ($lazy) {
        wp_enqueue_script('jquery-waypoints');
    }
    $url = azexo_get_the_post_thumbnail(get_the_ID(), $thumbnail_size, true);
    if ($url):
        ?>
        <a href="<?php esc_url(the_permalink()); ?>">
            <?php if ($lazy) { ?>
                <?php if (preg_match('/\d+x\d+/', $thumbnail_size)) { ?>
                    <div class="image lazy <?php print esc_attr($zoom); ?>" data-src="<?php print esc_url($url[0]); ?>" style="height: <?php print esc_attr($size['height']); ?>px;" data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                    </div>
                <?php } else { ?>
                    <img class="image lazy" data-src="<?php print esc_url($url[0]); ?>" alt="">
                <?php }; ?>
            <?php } else { ?>
                <?php if (preg_match('/\d+x\d+/', $thumbnail_size)) { ?>
                    <div class="image <?php print esc_attr($zoom); ?>" style='background-image: url("<?php print esc_url($url[0]); ?>"); height: <?php print esc_attr($size['height']); ?>px;' data-width="<?php print esc_attr($size['width']); ?>" data-height="<?php print esc_attr($size['height']); ?>">
                    </div>
                <?php } else { ?>
                    <img class="image" src="<?php print esc_url($url[0]); ?>" alt="">
                <?php }; ?>
            <?php }; ?>
        </a>        
        <?php
    endif;
}

function azexo_woo_product_gallery_field($product_template = false) {
    if (!$product_template) {
        $product_template = get_post_type();
    }
    $options = get_option(AZEXO_FRAMEWORK);
    $thumbnail_size = isset($options[$product_template . '_thumbnail_size']) && !empty($options[$product_template . '_thumbnail_size']) ? $options[$product_template . '_thumbnail_size'] : 'large';
    azexo_add_image_size($thumbnail_size);
    $image_thumbnail = isset($options[$product_template . '_image_thumbnail']) ? $options[$product_template . '_image_thumbnail'] : false;

    $images_links = azexo_woo_get_images_links($thumbnail_size);
    $full_images_links = azexo_woo_get_images_links('full');

    $size = azexo_get_image_sizes($thumbnail_size);
    $zoom = isset($options[$product_template . '_zoom']) && !empty($options[$product_template . '_zoom']) ? 'zoom' : '';
    $lazy = isset($options[$product_template . '_lazy']) && !empty($options[$product_template . '_lazy']) ? $options[$product_template . '_lazy'] : false;
    if ($lazy) {
        wp_enqueue_script('jquery-waypoints');
    }
    if ((count($images_links) > 1)) :
        ?>
        <div id="images-<?php the_ID(); ?>" 
             class="images <?php print (isset($options[$product_template . '_gallery_slider_thumbnails']) && esc_attr($options[$product_template . '_gallery_slider_thumbnails']) ? 'thumbnails' : ''); ?> <?php print (isset($options[$product_template . '_show_carousel']) && esc_attr($options[$product_template . '_show_carousel']) ? 'carousel' : ''); ?>" 
             data-width="<?php print esc_attr($size['width']); ?>" 
             data-height="<?php print esc_attr($size['height']); ?>" 
             data-vertical="<?php print esc_attr(isset($options[$product_template . '_gallery_slider_thumbnails_vertical']) && $options[$product_template . '_gallery_slider_thumbnails_vertical']); ?>">
                 <?php
                 if (isset($options[$product_template . '_show_carousel']) && esc_attr($options[$product_template . '_show_carousel'])) {
                     wp_enqueue_script('jquery-owl-carousel');
                     wp_enqueue_style('jquery-owl-carousel');
                     wp_enqueue_script('jquery-magnific-popup');
                     wp_enqueue_style('jquery-magnific-popup');
                 } else {
                     wp_enqueue_style('jquery-flexslider');
                     wp_enqueue_script('jquery-flexslider');
                 }
                 $i = 0;
                 foreach ($images_links as $image_link):
                     ?>                        
                     <?php if ($lazy) { ?>
                         <?php if (preg_match('/\d+x\d+/', $thumbnail_size)) { ?>
                        <div class="image lazy <?php print esc_attr($zoom); ?>" data-popup="<?php print esc_url($full_images_links[$i]); ?>" data-src="<?php print esc_url($image_link); ?>" style="height: <?php print esc_attr($size['height']); ?>px;">
                        </div>
                    <?php } else { ?>
                        <img class="image lazy" data-src="<?php print esc_url($image_link); ?>" alt="" data-popup="<?php print esc_url($full_images_links[$i]); ?>">
                    <?php }; ?>
                <?php } else { ?>
                    <?php if (preg_match('/\d+x\d+/', $thumbnail_size)) { ?>
                        <div class="image <?php print esc_attr($zoom); ?>" data-popup="<?php print esc_url($full_images_links[$i]); ?>" style='background-image: url("<?php print esc_url($image_link); ?>"); height: <?php print esc_attr($size['height']); ?>px;'>
                        </div>
                    <?php } else { ?>
                        <img class="image" src="<?php print esc_url($image_link); ?>" alt="" data-popup="<?php print esc_url($full_images_links[$i]); ?>">
                    <?php }; ?>
                <?php }; ?>
                <?php
                $i++;
            endforeach;
            ?>
        </div>
        <?php
    endif;
}

add_filter('formatted_woocommerce_price', 'azexo_woo_formatted_woocommerce_price', 10, 5);

function azexo_woo_formatted_woocommerce_price($number_string, $price, $decimals, $decimal_separator, $thousand_separator) {
    $number_string = number_format($price, $decimals, $decimal_separator, $thousand_separator);
    $number_parts = explode($decimal_separator, $number_string);
    if (count($number_parts) == 2) {
        return $number_parts[0] . '<span class="decimals">' . $decimal_separator . $number_parts[1] . '</span>';
    } else {
        return $number_string;
    }
}

add_filter('woocommerce_price_format', 'azexo_woo_price_format', 10, 2);

function azexo_woo_price_format($format, $currency_pos) {
    $format = '%1$s%2$s';

    switch ($currency_pos) {
        case 'left' :
            $format = '<span class="currency">%1$s</span>%2$s';
            break;
        case 'right' :
            $format = '%2$s<span class="currency">%1$s</span>';
            break;
        case 'left_space' :
            $format = '<span class="currency">%1$s</span>&nbsp;%2$s';
            break;
        case 'right_space' :
            $format = '%2$s&nbsp;<span class="currency">%1$s</span>';
            break;
    }
    return $format;
}

add_filter('woocommerce_product_categories_widget_args', 'azexo_woo_product_categories_widget_args');

function azexo_woo_product_categories_widget_args($list_args) {

    if (!class_exists('AZEXO_Product_Cat_List_Walker')) {

        class AZEXO_Product_Cat_List_Walker extends WC_Product_Cat_List_Walker {

            public function start_el(&$output, $cat, $depth = 0, $args = array(), $current_object_id = 0) {
                $output .= '<li class="cat-item cat-item-' . $cat->term_id;

                if ($args['current_category'] == $cat->term_id) {
                    $output .= ' current-cat';
                }

                if ($args['has_children'] && $args['hierarchical']) {
                    $output .= ' cat-parent';
                }

                if ($args['current_category_ancestors'] && $args['current_category'] && in_array($cat->term_id, $args['current_category_ancestors'])) {
                    $output .= ' current-cat-parent';
                }

                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image = wp_get_attachment_thumb_url($thumbnail_id);
                if ($image) {
                    $image = '<img alt="" src="' . esc_url($image) . '">';
                }

                $output .= '"><a href="' . esc_url(get_term_link((int) $cat->term_id, 'product_cat')) . '">' . $image . '<span>' . $cat->name . '</span></a>';

                if ($args['show_count']) {
                    $output .= ' <span class="count">(' . $cat->count . ')</span>';
                }
            }

        }

    }

    $list_args['walker'] = new AZEXO_Product_Cat_List_Walker;
    return $list_args;
}

add_filter('azexo_post_template_path', 'azexo_woo_post_template_path', 10, 2);

function azexo_woo_post_template_path($template, $template_name) {
    global $azexo_woo_templates;
    global $post;
    if (class_exists('WooCommerce') && (in_array($template_name, array_keys($azexo_woo_templates)) || $post->post_type == 'product')) {
        return WC()->template_path() . "content-product.php";
    } else {
        return $template;
    }
}

add_action('pre_get_posts', 'azexo_woo_pre_get_posts', 20);

function azexo_woo_pre_get_posts($query) {
    if (is_user_logged_in() && azexo_is_post_type_query($query, 'product')) {
        if ($query->get('author') == get_current_user_id()) {
            $post_status = (array) $query->get('post_status');
            if (empty($post_status)) {
                $post_status = array();
            }
            $post_status = array_merge($post_status, array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'));
            $post_status = array_unique($post_status);
            $query->set('post_status', $post_status);
            $tax_query = $query->get('tax_query');
            if (is_array($tax_query)) {
                foreach ($tax_query as $key => $tq) {
                    if (isset($tq['taxonomy']) && $tq['taxonomy'] == 'product_visibility' && isset($tax_query[$key]['terms'])) {
                        unset($tax_query[$key]);
                    }
                }
                $query->set('tax_query', $tax_query);
            }
        }
    }
}

add_filter('azexo_posts_list_loop_args', 'azexo_woo_posts_list_loop_args');

function azexo_woo_posts_list_loop_args($loop_args) {
    if ($loop_args['post_type'] == 'product') {
        $visibility = array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_visibility',
                'field' => 'slug',
                'terms' => array('exclude-from-catalog'),
                'operator' => 'NOT IN',
            ),
        );
        if (!isset($loop_args['tax_query'])) {
            $loop_args['tax_query'] = $visibility;
        } else {
            $visibility[] = $loop_args['tax_query'];
            $loop_args['tax_query'] = $visibility;
        }
        $loop_args['tax_query']['relation'] = 'AND';
    }

    return $loop_args;
}

function azexo_woo_order_by_rating_post_clauses($args, $query) {
    global $wpdb;

    $args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";

    $args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";

    $args['join'] .= "
			LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

    $args['orderby'] = "average_rating DESC, $wpdb->posts.post_date DESC";

    $args['groupby'] = "$wpdb->posts.ID";

    return $args;
}

function azexo_woo_featured_post_clauses($args, $query) {
    global $wpdb;

    $term = get_term_by('slug', 'featured', 'product_visibility');
    $args['where'] .= " AND ({$wpdb->posts}.ID IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ({$term->term_id}))) ";
    return $args;
}

add_filter('wp_nav_menu_objects', 'azexo_woo_wp_nav_menu_objects', 10, 2);

function azexo_woo_wp_nav_menu_objects($sorted_menu_items, $args) {
    if (class_exists('WooCommerce')) {
        $woocommerce_myaccount_page_id = get_option('woocommerce_myaccount_page_id');
        if (!is_user_logged_in()) {
            foreach ($sorted_menu_items as $i => &$menu_item) {
                if ($menu_item->object_id == $woocommerce_myaccount_page_id && $menu_item->object == 'page') {
                    $menu_item->title = esc_html__('Login/Register', 'foodpicky');
                }
            }
        }
    }
    $remove_array = array();
    if (class_exists('WC_Vendors')) {
        $remove_array[] = get_option('wcvendors_vendor_dashboard_page_id');
        $remove_array[] = get_option('wcvendors_shop_settings_page_id');
        $remove_array[] = get_option('wcvendors_product_orders_page_id');
    }
    if (class_exists('WCV_Vendors')) {
        if (!WCV_Vendors::is_vendor(get_current_user_id())) {
            foreach ($sorted_menu_items as $i => &$menu_item) {
                if (in_array($menu_item->object_id, $remove_array) && $menu_item->object == 'page') {
                    unset($sorted_menu_items[$i]);
                    prev($sorted_menu_items);
                }
            }
        }
    }
    return $sorted_menu_items;
}

add_filter('azexo_menu_start_el', 'azexo_woo_menu_start_el', 10, 2);

function azexo_woo_menu_start_el($item, $args) {
    $woocommerce_cart_page_id = get_option('woocommerce_cart_page_id');
    if ($item->object_id == $woocommerce_cart_page_id && $item->object == 'page') {
        global $woocommerce;
        $item->classes[] = 'cart';
        $args->link_before = '<span class="fa fa-shopping-cart"></span><span class="count">' . $woocommerce->cart->cart_contents_count . '</span>';
    }
    if (is_array($item->classes)) {
        if (in_array('hot', $item->classes)) {
            $item->classes = array_diff($item->classes, array('hot'));
            $item->additions .= '<span class="hot">' . esc_html__('Hot', 'foodpicky') . '</span>';
        }
        if (in_array('new', $item->classes)) {
            $item->classes = array_diff($item->classes, array('new'));
            $item->additions .= '<span class="new">' . esc_html__('New', 'foodpicky') . '</span>';
        }
    }
    return $item;
}

add_action('widgets_init', 'azexo_woo_register_widgets');

function azexo_woo_register_widgets() {
    register_widget('AZEXO_WOO_Breadcrumb');
    register_widget('AZEXO_WOO_Related_Products');
    register_widget('AZEXO_WOO_Upsell_Products');
}

class AZEXO_WOO_Breadcrumb extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_woo_breadcrumb', AZEXO_FRAMEWORK . ' - WooCommerce breadcrumb');
    }

    function widget($args, $instance) {
        print '<div class="widget azexo-woo-breadcrumb">';
        woocommerce_breadcrumb();
        print '</div>';
    }

}

class AZEXO_WOO_Related_Products extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_woo_related_products', AZEXO_FRAMEWORK . ' - WooCommerce related products');
    }

    function widget($args, $instance) {
        if (is_product()) {
            woocommerce_output_related_products();
        }
    }

}

class AZEXO_WOO_Upsell_Products extends WP_Widget {

    public function __construct() {
        parent::__construct('azexo_woo_upsell_products', AZEXO_FRAMEWORK . ' - WooCommerce upsell products');
    }

    function widget($args, $instance) {
        if (is_product()) {
            woocommerce_upsell_display();
        }
    }

}

if (class_exists('WPBakeryShortCode')) {

    class WPBakeryShortCode_woocommerce_breadcrumb extends WPBakeryShortCode {
        
    }

    class WPBakeryShortCode_azexo_product_search_form extends WPBakeryShortCode {
        
    }

    class WPBakeryShortCode_woocommerce_cart_widget extends WPBakeryShortCode {
        
    }

    class WPBakeryShortCode_azexo_product_fields_wrapper extends WPBakeryShortCodesContainer {
        
    }

}

azexo_add_element(array(
    "name" => "Breadcrumb",
    "base" => "woocommerce_breadcrumb",
    'icon' => 'icon-wpb-woocommerce',
    'category' => esc_html__('WooCommerce', 'foodpicky'),
    'show_settings_on_create' => false,
));


azexo_add_element(array(
    "name" => "AZEXO - Product Search Form",
    "base" => "azexo_product_search_form",
    'category' => esc_html__('AZEXO', 'foodpicky'),
    'show_settings_on_create' => false,
));


azexo_add_element(array(
    'name' => esc_html__('Cart widget', 'foodpicky'),
    'base' => 'woocommerce_cart_widget',
    'icon' => 'icon-wpb-woocommerce',
    'category' => esc_html__('WooCommerce', 'foodpicky'),
    'description' => esc_html__('Displays the cart contents', 'foodpicky'),
    'show_settings_on_create' => false,
    'params' => array(
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Widget title', 'foodpicky'),
            'param_name' => 'title',
            'description' => esc_html__('What text use as a widget title. Leave blank to use default widget title.', 'foodpicky'),
            'value' => esc_html__('Cart', 'foodpicky'),
        ),
        array(
            'type' => 'checkbox',
            'heading' => esc_html__('Hide if cart is empty', 'foodpicky'),
            'param_name' => 'hide_if_empty',
            'value' => array(
                esc_html__('Yes, please', 'foodpicky') => 1,
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Extra class name', 'foodpicky'),
            'param_name' => 'el_class',
            'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'foodpicky'),
        ),
    )
));

azexo_add_element(array(
    "name" => "AZEXO - Product Fields Wrapper",
    "base" => "azexo_product_fields_wrapper",
    'category' => esc_html__('AZEXO', 'foodpicky'),
    "as_parent" => array('except' => 'azexo_product_fields_wrapper'),
    "content_element" => true,
    "controls" => "full",
    "show_settings_on_create" => true,
    "is_container" => true,
    'params' => array(
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Extra class name', 'foodpicky'),
            'param_name' => 'el_class',
            'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'foodpicky'),
        ),
        array(
            'type' => 'css_editor',
            'heading' => esc_html__('Css', 'foodpicky'),
            'param_name' => 'css',
            'group' => esc_html__('Design options', 'foodpicky'),
        ),
    ),
    "js_view" => 'VcColumnView'
));


add_action('woocommerce_before_shop_loop', 'azexo_woo_before_shop_loop', 10);

function azexo_woo_before_shop_loop() {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['shop_modes']) && $options['shop_modes']) {
        ?> 
        <div class="modes">
            <a class="mode shop-product <?php print (isset($_GET['template']) && $_GET['template'] == 'shop_product') ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('template' => 'shop_product', 'mode' => false))); ?>">
                <i class="fa fa-th"></i>
            </a>
            <a class="mode detailed-shop-product <?php print (isset($_GET['template']) && $_GET['template'] == 'detailed_shop_product') ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('template' => 'detailed_shop_product', 'mode' => false))); ?>">
                <i class="fa fa-th-list"></i>
            </a>
            <?php if (function_exists('azl_google_map_shortcode')): ?> 
                <a class="mode gmap <?php print (isset($_GET['mode']) && $_GET['mode'] == 'gmap') ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg(array('mode' => 'gmap', 'template' => false))); ?>">
                    <i class="fa fa-map-o"></i>
                </a>
            <?php endif; ?> 
        </div>
        <?php
    }
}

add_filter('woocommerce_show_page_title', 'azexo_woo_show_page_title');

function azexo_woo_show_page_title() {
    $options = get_option(AZEXO_FRAMEWORK);
    return isset($options['show_page_title']) && $options['show_page_title'];
}

add_filter('woocommerce_product_description_heading', 'azexo_woo_product_description_heading');

function azexo_woo_product_description_heading() {
    return false;
}

add_action('comment_post', 'azexo_woo_comment_post');

function azexo_woo_comment_post($comment_id) {
    if ('product' === get_post_type($_POST['comment_post_ID'])) {
        $review_marks = azexo_review_marks();
        if (!empty($review_marks)) {
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
            if ('yes' === get_option('woocommerce_enable_review_rating') && 'yes' !== get_option('woocommerce_review_rating_required')) {
                delete_comment_meta($comment_id, 'rating');
                add_comment_meta($comment_id, 'rating', $rating, true);
            }
        }
    }
}

add_filter('woocommerce_get_catalog_ordering_args', 'azexo_woo_get_catalog_ordering_args');

function azexo_woo_get_catalog_ordering_args($args) {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['custom_sorting_numeric_meta_keys']) && is_array($options['custom_sorting_numeric_meta_keys'])) {
        $orderby_value = isset($_GET['orderby']) ? woocommerce_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
        foreach ($options['custom_sorting_numeric_meta_keys'] as $meta_key) {
            if ($meta_key . '-asc' == $orderby_value) {
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                $args['meta_key'] = $meta_key;
            }
            if ($meta_key . '-desc' == $orderby_value) {
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                $args['meta_key'] = $meta_key;
            }
        }
    }
    return $args;
}

add_filter('woocommerce_default_catalog_orderby_options', 'azexo_woo_catalog_orderby');
add_filter('woocommerce_catalog_orderby', 'azexo_woo_catalog_orderby');

function azexo_woo_catalog_orderby($sortby) {
    $options = get_option(AZEXO_FRAMEWORK);
    if (isset($options['custom_sorting']) && is_array($options['custom_sorting'])) {
        $sortby = array_intersect_key($sortby, array_combine($options['custom_sorting'], $options['custom_sorting']));
    }
    if (isset($options['custom_sorting_numeric_meta_keys']) && is_array($options['custom_sorting_numeric_meta_keys'])) {
        foreach ($options['custom_sorting_numeric_meta_keys'] as $meta_key) {
            if (!empty($meta_key)) {
                $sortby[esc_attr($meta_key) . '-desc'] = sprintf(esc_attr__('Sort by %s: high to low', 'foodpicky'), esc_attr($meta_key));
                $sortby[esc_attr($meta_key) . '-asc'] = sprintf(esc_attr__('Sort by %s: low to high', 'foodpicky'), esc_attr($meta_key));
            }
        }
    }
    return $sortby;
}

add_filter('azexo_review_count', 'azexo_woo_review_count', 10, 2);

function azexo_woo_review_count($count, $post) {
    if ($post->post_type == 'product') {
        $product = wc_get_product($post);
        return $product->get_review_count();
    }
    return $count;
}

add_filter('azexo_review_allowed', 'azexo_woo_review_allowed', 10, 4);

function azexo_woo_review_allowed($allowed, $customer_email, $user_id, $post) {
    if ($post->post_type == 'product') {
        return get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product($customer_email, $user_id, $post->ID);
    }
    return $allowed;
}

add_filter('woocommerce_get_breadcrumb', 'azexo_woo_get_breadcrumb', 10, 2);

function azexo_woo_get_breadcrumb($crumbs, $wc_breadcrumb) {
    foreach ($crumbs as &$crumb) {
        if (empty($crumb[0]) && empty($crumb[1])) {
            global $azexo_queried_object;
            if (isset($azexo_queried_object) && is_object($azexo_queried_object) && property_exists($azexo_queried_object, 'post_title')) {
                $crumb[0] = $azexo_queried_object->post_title;
            }
        }
    }
    return $crumbs;
}

add_action('woocommerce_scheduled_sales', 'azexo_woo_scheduled_sales', 11);

function azexo_woo_scheduled_sales() {
    global $wpdb;
    $product_ids = $wpdb->get_col("
		SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
		WHERE (postmeta.meta_key = '_sale_price_dates_to' AND postmeta.meta_value = '')
                OR (postmeta.meta_key = '_sale_price_dates_from' AND postmeta.meta_value = '')
	");
    if ($product_ids) {
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                if ($product->is_type('variable')) {
                    $children = $product->get_children();
                    azexo_woo_variable_product_sync($product_id, $children);
                }
            }
        }
    }
}

add_action('woocommerce_api_process_product_meta_variable', 'azexo_woo_api_process_product_meta_variable', 10, 2);

function azexo_woo_api_process_product_meta_variable($product_id, $data) {
    azexo_woo_variable_product_sync($product_id);
}

add_action('woocommerce_variable_product_sync', 'azexo_woo_variable_product_sync', 10, 2);

function azexo_woo_variable_product_sync($product_id, $children = null) {
    if ($children == null) {
        $product = wc_get_product($product_id);
        $children = $product->get_children();
    }
    $min_from = null;
    $max_to = null;
    foreach ($children as $child_id) {
        $from = get_post_meta($child_id, '_sale_price_dates_from', true);
        if (is_numeric($from)) {
            if (is_null($min_from)) {
                $min_from = $from;
            } else {
                if ($min_from > $from) {
                    $min_from = $from;
                }
            }
        }
        $to = get_post_meta($child_id, '_sale_price_dates_to', true);
        if (is_numeric($to)) {
            if (is_null($max_to)) {
                $max_to = $to;
            } else {
                if ($max_to < $to) {
                    $max_to = $to;
                }
            }
        }
    }
    if (!is_null($min_from)) {
        update_post_meta($product_id, '_sale_price_dates_from', $min_from);
    }
    if (!is_null($max_to)) {
        update_post_meta($product_id, '_sale_price_dates_to', $max_to);
    }
}

add_filter('gettext_with_context', 'azexo_woo_gettext_with_context', 10, 4);

function azexo_woo_gettext_with_context($translations, $text, $context, $domain) {
    if ($domain == 'woocommerce' && $context == 'Price range: from-to' && $text == '%1$s&ndash;%2$s') {
        return '%1$s<span class="dash">&ndash;</span>%2$s';
    }
    return $translations;
}

function azexo_woo_vendors_where_filter($where) {
    global $wpdb;

    $ids = get_users(array('role' => 'vendor', 'fields' => 'ID'));
    if (empty($ids)) {
        $ids = array(0);
    }
    $ids = implode(',', $ids);
    $where .= " AND $wpdb->posts.post_author IN ($ids)";
    return $where;
}

function azexo_woo_vendors_filter($args, $query) {
    global $wpdb;

    $ids = get_users(array('role' => 'vendor', 'fields' => 'ID'));
    if (empty($ids)) {
        $ids = array(0);
    }
    $ids = implode(',', $ids);
    $args['where'] .= " AND $wpdb->posts.post_author IN ($ids)";

    return $args;
}

add_filter('woocommerce_add_cart_item', 'azexo_woo_add_cart_item', 10, 2);

function azexo_woo_add_cart_item($cart_item_data, $cart_item_key) {
    if ($cart_item_data['variation_id'] && empty($cart_item_data['variation'])) {
        $cart_item_data['variation'] = wc_get_product_variation_attributes($cart_item_data['variation_id']);
    }
    return $cart_item_data;
}

add_filter('wp_ajax_nopriv_azexo_update_mini_cart', 'azexo_woo_update_mini_cart');
add_filter('wp_ajax_azexo_update_mini_cart', 'azexo_woo_update_mini_cart');

function azexo_woo_update_mini_cart() {
    echo wc_get_template('cart/mini-cart.php');
    die();
}

add_filter( 'woocommerce_show_variation_price', '__return_true' );
