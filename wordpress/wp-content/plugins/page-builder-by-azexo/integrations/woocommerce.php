<?php

add_action('wp_loaded', 'azh_woocommerce_wp_loaded');

function azh_woocommerce_wp_loaded() {
    if (class_exists('WooCommerce')) {


        azh_add_element(array(
            "name" => esc_html__('WC Cart', 'azh'),
            "category" => esc_html__('WooCommerce', 'azh'),
            "base" => "woocommerce_cart",
            "image" => AZH_URL . '/images/woocommerce.png',
            "show_settings_on_create" => false,
        ));
        azh_add_element(array(
            "name" => esc_html__('WC Checkout', 'azh'),
            "category" => esc_html__('WooCommerce', 'azh'),
            "base" => "woocommerce_checkout",
            "image" => AZH_URL . '/images/woocommerce.png',
            "show_settings_on_create" => false,
        ));
        azh_add_element(array(
            "name" => esc_html__('WC My Account', 'azh'),
            "category" => esc_html__('WooCommerce', 'azh'),
            "base" => "woocommerce_my_account",
            "image" => AZH_URL . '/images/woocommerce.png',
            "show_settings_on_create" => false,
        ));
        azh_add_element(array(
            "name" => esc_html__('WC Order Trackig', 'azh'),
            "category" => esc_html__('WooCommerce', 'azh'),
            "base" => "woocommerce_order_tracking",
            "image" => AZH_URL . '/images/woocommerce.png',
            "show_settings_on_create" => false,
        ));

        azh_add_element(array(
            "name" => esc_html__('Add to cart', 'azh'),
            "category" => esc_html__('WooCommerce', 'azh'),
            "base" => "add_to_cart",
            "image" => AZH_URL . '/images/woocommerce.png',
            "show_settings_on_create" => true,
            'params' => array(
                array(
                    'type' => 'ajax_dropdown',
                    'url' => admin_url('admin-ajax.php') . '?action=azh_search_post&post_type=product',
                    'heading' => esc_html__('Product', 'azh'),
                    'param_name' => 'id',
                    'multiple' => false,
                    'description' => esc_html__('Show the price and add to cart button of a single product.', 'azh'),
                    'admin_label' => true,
                ),
            ),
        ));

        $attribute_taxonomies = array(esc_html__('Select attribute', 'azh') => '');
        $at = wc_get_attribute_taxonomies();
        if (!empty($at)) {
            foreach ($at as $tax) {
                $attribute_taxonomy_name = wc_attribute_taxonomy_name($tax->attribute_name);
                $label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
                $attribute_taxonomies[$label] = $attribute_taxonomy_name;
            }
        }

        azh_add_element(array(
            "name" => "WC Products",
            "image" => AZH_URL . '/images/woocommerce.png',
            "base" => "products",
            'category' => esc_html__('WooCommerce', 'azh'),
            "show_settings_on_create" => true,
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Limit', 'azh'),
                    'param_name' => 'limit',
                    'description' => esc_html__('The number of products to display. Defaults to display all (-1).', 'azh'),
                    'value' => '-1',
                    'admin_label' => true,
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Columns', 'azh'),
                    'param_name' => 'columns',
                    'description' => esc_html__('The number of columns to display. Defaults to 4.', 'azh'),
                    'value' => '4',
                    'admin_label' => true,
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => esc_html__('Sorts the products displayed by the entered option.', 'azh'),
                    'param_name' => 'orderby',
                    'value' => array(
                        esc_html__('The product title (default)', 'azh') => 'title',
                        esc_html__('The date the product was published', 'azh') => 'date',
                        esc_html__('The post ID of the product', 'azh') => 'id',
                        esc_html__('The Menu Order, if set (lower numbers display first)', 'azh') => 'menu_order',
                        esc_html__('The number of purchases', 'azh') => 'popularity',
                        esc_html__('Randomly order the products on page load', 'azh') => 'rand',
                        esc_html__('The average product rating', 'azh') => 'rating',
                        esc_html__('The date the product was published', 'azh') => 'skus',
                    ),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => esc_html__('Order', 'azh'),
                    'param_name' => 'order',
                    'value' => array(
                        esc_html__('Descending', 'azh') => 'DESC',
                        esc_html__('Ascending', 'azh') => 'ASC',
                    ),
                    'description' => esc_html__('States whether the product ordering is ascending (ASC) or descending (DESC), using the method set in orderby.', 'AZEXO'),
                    'admin_label' => true,
                    'dependency' => array(
                        'element' => 'orderby',
                        'not_empty' => true,
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => esc_html__('On sale', 'azh'),
                    'param_name' => 'on_sale',
                    'value' => array(esc_html__('Yes, please', 'AZEXO') => 'true'),
                    'description' => esc_html__('Retrieve on sale products.', 'azh'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => esc_html__('Best selling', 'azh'),
                    'param_name' => 'best_selling',
                    'value' => array(esc_html__('Yes, please', 'AZEXO') => 'true'),
                    'description' => esc_html__('Retrieve best selling products.', 'azh'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => esc_html__('Top rated', 'azh'),
                    'param_name' => 'top_rated',
                    'value' => array(esc_html__('Yes, please', 'azh') => 'true'),
                    'description' => esc_html__('Retrieve top rated products.', 'azh'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'textfield',
                    'heading' => esc_html__('Class', 'azh'),
                    'param_name' => 'class',
                    'description' => esc_html__('Adds an HTML wrapper class so you can modify the specific output with custom CSS.', 'azh'),
                ),
                array(
                    'type' => 'ajax_multiselect',
                    'url' => admin_url('admin-ajax.php') . '?action=azh_search_post&post_type=product',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Exact products', 'azh'),
                    'param_name' => 'ids',
                    'description' => esc_html__('Will display exact products.', 'azh'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Attribute', 'azh'),
                    'param_name' => 'attribute',
                    'description' => esc_html__('Retrieves products using the specified attribute.', 'azh'),
                    'value' => $attribute_taxonomies,
                    'admin_label' => true,
                ),
                array(
                    'type' => 'ajax_multiselect',
                    'url' => admin_url('admin-ajax.php') . '?action=azh_search_term&taxonomy=product_cat&slug=slug',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Terms', 'azh'),
                    'param_name' => 'terms',
                    'description' => esc_html__('List of attribute terms to be used with attribute.', 'azh'),
                    'admin_label' => true,
                    'dependency' => array(
                        'element' => 'attribute',
                        'not_empty' => true,
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Terms operator', 'azh'),
                    'param_name' => 'terms_operator',
                    'value' => array(
                        esc_html__('Will display products with the chosen attribute (default).', 'azh') => 'IN',
                        esc_html__('Will display products from all of the chosen attributes.', 'azh') => 'AND',
                        esc_html__('Will display products that are not in the chosen attributes.', 'azh') => 'NOT IN',
                    ),
                    'description' => esc_html__('Operator to compare attribute terms.', 'AZEXO'),
                    'admin_label' => true,
                    'dependency' => array(
                        'element' => 'terms',
                        'not_empty' => true,
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Visibility', 'azh'),
                    'param_name' => 'visibility',
                    'value' => array(
                        esc_html__('Products visibile on shop and search results (default).', 'azh') => 'visible',
                        esc_html__('Products visible on the shop only, but not search results.', 'azh') => 'catalog',
                        esc_html__('Products visible in search results only, but not on the shop.', 'azh') => 'search',
                        esc_html__('Products that are hidden from both shop and search, accessible only by direct URL.', 'azh') => 'hidden',
                        esc_html__('Products that are marked as Featured Products.', 'azh') => 'featured',
                    ),
                    'description' => esc_html__('Will display products based on the selected visibility.', 'AZEXO'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'ajax_multiselect',
                    'url' => admin_url('admin-ajax.php') . '?action=azh_search_term&taxonomy=product_cat&slug=slug',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Category', 'azh'),
                    'param_name' => 'category',
                    'description' => esc_html__('Retries products using the specified category.', 'azh'),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'dropdown',
                    'group' => esc_html__('Data Settings', 'AZEXO'),
                    'heading' => esc_html__('Category operator', 'azh'),
                    'param_name' => 'cat_operator',
                    'value' => array(
                        esc_html__('Will display products within the chosen category (default).', 'azh') => 'IN',
                        esc_html__('Will display products that belong in all of the chosen categories.', 'azh') => 'AND',
                        esc_html__('Will display products that are not in the chosen attributes.', 'azh') => 'NOT IN',
                    ),
                    'description' => esc_html__('Operator to compare category terms.', 'AZEXO'),
                    'admin_label' => true,
                    'dependency' => array(
                        'element' => 'category',
                        'not_empty' => true,
                    ),
                ),
            )
        ));
    }
}

add_action('azh_autocomplete_products_category_labels', 'azh_get_category_terms_labels');

function azh_get_category_terms_labels() {
    azh_get_terms_labels('product_cat', true);
}

add_action('azh_autocomplete_products_category', 'azh_search_category_terms');

function azh_search_category_terms() {
    azh_search_terms('product_cat', true);
}

add_action('azh_autocomplete_products_terms_labels', 'azh_get_attribute_terms_labels');

function azh_get_attribute_terms_labels() {
    if (isset($_POST['attrs']['attribute'])) {
        azh_get_terms_labels(sanitize_text_field($_POST['attrs']['attribute']), true);
    }
}

add_action('azh_autocomplete_products_terms', 'azh_search_attribute_terms');

function azh_search_attribute_terms() {
    if (isset($_POST['attrs']['attribute'])) {
        azh_search_terms(sanitize_text_field($_POST['attrs']['attribute']), true);
    }
}

add_action('azh_autocomplete_products_ids_labels', 'azh_get_products_labels');
add_action('azh_autocomplete_add_to_cart_id_labels', 'azh_get_products_labels');

function azh_get_products_labels() {
    azh_get_posts_labels('product');
}

add_action('azh_autocomplete_products_ids', 'azh_search_products');
add_action('azh_autocomplete_add_to_cart_id', 'azh_search_products');

function azh_search_products() {
    azh_search_posts('product');
}
