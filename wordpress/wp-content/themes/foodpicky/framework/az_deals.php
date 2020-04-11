<?php
global $azexo_azd_templates;

$azexo_azd_templates = array(
    'best_rated_product' => esc_html__('Best rated product', 'foodpicky'),
    'latest_product' => esc_html__('Latest product', 'foodpicky'),
);

add_filter('azexo_templates', 'azexo_azd_templates');

function azexo_azd_templates($azexo_templates) {
    global $azexo_azd_templates;
    return array_merge($azexo_templates, $azexo_azd_templates);
}

add_filter('azexo_post_template_path', 'azexo_azd_post_template_path', 10, 2);

function azexo_azd_post_template_path($template, $template_name) {
    global $azexo_azd_templates;
    if (in_array($template_name, array_keys($azexo_azd_templates)) && function_exists('WC')) {
        return WC()->template_path() . "content-product.php";
    } else {
        return $template;
    }
}

global $azexo_azd_fields;

$azexo_azd_fields = array(
    'group_deal_purchases_left' => esc_html__('Deals: Group deal purchases left', 'foodpicky'),
    'deal_time_left' => esc_html__('Deals: Deal time left', 'foodpicky'),
    'price_offer' => esc_html__('Deals: Price offer', 'foodpicky'),
    'price_deal' => esc_html__('Deals: Price deal', 'foodpicky'),
    'price' => esc_html__('Deals: Price', 'foodpicky'),
    'price_trimmed' => esc_html__('Deals: Price (trimmed zeros)', 'foodpicky'),
    'product_rating' => esc_html__('Deals: Average product rating', 'foodpicky'),
    'add_to_cart' => esc_html__('Deals: Add to cart link', 'foodpicky'),
    'purchased_deals' => esc_html__('Deals: Single purchased deals', 'foodpicky'),
    'seller_info' => esc_html__('Deals: Single seller info', 'foodpicky'),
    'sold_by' => esc_html__('Deals: Sold by info', 'foodpicky'),
    'single_title' => esc_html__('Deals: Single product title', 'foodpicky'),
    'single_summary' => esc_html__('Deals: Single product summary', 'foodpicky'),
);

add_filter('azexo_fields', 'azexo_azd_fields');

function azexo_azd_fields($azexo_fields) {
    global $azexo_azd_fields;
    return array_merge($azexo_fields, $azexo_azd_fields);
}

add_filter('azexo_fields_post_types', 'azexo_azd_fields_post_types');

function azexo_azd_fields_post_types($azexo_fields_post_types) {
    global $azexo_azd_fields;
    $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_azd_fields), array_fill(0, count(array_keys($azexo_azd_fields)), 'product')));
    return $azexo_fields_post_types;
}

function azexo_azd_tgmpa_register() {

    $plugins = array(
        array(
            'name' => esc_html__('WC Vendors', 'foodpicky'),
            'slug' => 'wc-vendors',
            'required' => true,
        ),
    );
    tgmpa($plugins, array());
}

add_action('tgmpa_register', 'azexo_azd_tgmpa_register');

add_action('init', 'azexo_azd_init_azexo', 12);

function azexo_azd_init_azexo() {
    if (class_exists('WooCommerce')) {

        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
        add_action('woocommerce_after_main_content', 'woocommerce_pagination');
        add_action('woocommerce_share', 'azexo_entry_share');


        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

        add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    }

    if (class_exists('WCV_Vendor_Shop')) {
        remove_filter('woocommerce_after_shop_loop_item', array('WCV_Vendor_Shop', 'template_loop_sold_by'), 9);

        remove_filter('woocommerce_product_tabs', array('WCV_Vendor_Shop', 'seller_info_tab'));

        remove_filter('post_type_archive_link', array('WCV_Vendor_Shop', 'change_archive_link')); //function make infinite recursion
        add_filter('admin_init', array('WCV_Vendor_Shop', 'change_archive_link')); //FIX from https://wordpress.org/support/topic/nesting-level
    }
}

function azexo_azd_deal_time_left() {
    $deal_expire = get_post_meta(get_the_ID(), '_sale_price_dates_to', true);
    if (!empty($deal_expire)) {
        $expire = $deal_expire - current_time('timestamp');
        if ($expire < 0) {
            $expire = 0;
        }
        $days = floor($expire / 60 / 60 / 24);
        $hours = floor(($expire - $days * 60 * 60 * 24) / 60 / 60);
        $minutes = floor(($expire - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = $expire - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60;
        wp_enqueue_script('countdown');
        ?>
        <div class="time-left">
            <div class="call-to-action"><?php esc_html_e('Hurry up! Time left:', 'foodpicky'); ?></div>
            <div class="time" data-time="<?php print date('Y/m/d H:i:s', $deal_expire); ?>">
                <div class="days"><span class="count"><?php print $days; ?></span><span class="title"><?php print esc_html__('d.', 'foodpicky'); ?></span></div>
                <div class="hours"><span class="count"><?php print $hours; ?></span><span class="title"><?php print esc_html__('h.', 'foodpicky'); ?></span></div>
                <div class="minutes"><span class="count"><?php print $minutes; ?></span><span class="title"><?php print esc_html__('m.', 'foodpicky'); ?></span></div>
                <div class="seconds"><span class="count"><?php print $seconds; ?></span><span class="title"><?php print esc_html__('s.', 'foodpicky'); ?></span></div>
            </div>
        </div>
        <?php
    }
}

function azexo_azd_is_on_sale($product) {
    if ($product->get_type() == 'variable') {
        $children = $product->get_children();
        foreach ($children as $child_id) {
            $child = wc_get_product($child_id);
            if($child->is_on_sale()) {
                return true;
            }
        }
        return false;
    } else {
        return $product->is_on_sale();
    }
}

function azexo_azd_price_offer() {
    global $product;
    $price = $product->get_price();
    if (azexo_azd_is_on_sale($product)) {
        if ($product->get_type() == 'variable') {
            $display_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_sale_price()))) . $product->get_price_suffix();
            $display_regular_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price()))) . $product->get_price_suffix();
            $discount = round((wc_get_price_to_display($product, array('price' => $product->get_variation_sale_price())) / wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price())) - 1) * 100) . '%';
            $savings = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price())) - wc_get_price_to_display($product, array('price' => $product->get_variation_sale_price())));
        } else {
            $display_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_sale_price()))) . $product->get_price_suffix();
            $display_regular_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price()))) . $product->get_price_suffix();
            $discount = round((wc_get_price_to_display($product, array('price' => $product->get_sale_price())) / wc_get_price_to_display($product, array('price' => $product->get_regular_price())) - 1) * 100) . '%';
            $savings = wc_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price())) - wc_get_price_to_display($product, array('price' => $product->get_sale_price())));
        }
        ?>
        <div class="price-offer sale">
            <div class="discount">
                <?php print $discount; ?>
            </div>
            <div class="regular-price">
                <?php echo apply_filters('woocommerce_get_price_html', apply_filters('woocommerce_price_html', $display_regular_price, $product), $product); ?>
            </div>
            <div class="price">
                <?php print $display_price; ?>
            </div>
        </div>
        <?php
    } else {
        if ($product->get_type() == 'variable') {
            $display_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_price()))) . $product->get_price_suffix();
        } else {
            $display_price = wc_price(wc_get_price_to_display($product)) . $product->get_price_suffix();
        }
        ?>
        <div class="price-offer">
            <div class="price">
                <?php print $display_price; ?>
            </div>
        </div>
        <?php
    }
}

function azexo_azd_price_deal() {
    global $product;
    $price = $product->get_price();
    if (empty($price)) {
        return;
    }
    if (azexo_azd_is_on_sale($product)) {
        if ($product->get_type() == 'variable') {
            $display_regular_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price()))) . $product->get_price_suffix();
            $discount = round((wc_get_price_to_display($product, array('price' => $product->get_variation_sale_price())) / wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price())) - 1) * 100) . '%';
            $savings = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_regular_price())) - wc_get_price_to_display($product, array('price' => $product->get_variation_sale_price())));
        } else {
            $display_regular_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price()))) . $product->get_price_suffix();
            $discount = round((wc_get_price_to_display($product, array('price' => $product->get_sale_price())) / wc_get_price_to_display($product, array('price' => $product->get_regular_price())) - 1) * 100) . '%';
            $savings = wc_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price())) - wc_get_price_to_display($product, array('price' => $product->get_sale_price())));
        }
        ?>
        <div class="price-deal sale">
            <div class="regular-price">
                <?php echo apply_filters('woocommerce_get_price_html', apply_filters('woocommerce_price_html', $display_regular_price, $product), $product); ?>
                <span><?php esc_html_e('Value', 'foodpicky'); ?></span>
            </div>
            <div class="discount">
                <span class="amount"><?php print $discount; ?></span>
                <span><?php esc_html_e('Discount', 'foodpicky'); ?></span>
            </div>
            <div class="savings">
                <?php print $savings; ?>
                <span><?php esc_html_e('Savings', 'foodpicky'); ?></span>
            </div>
        </div>
        <?php
    } else {
        if ($product->get_type() == 'variable') {
            $display_price = wc_price(wc_get_price_to_display($product, array('price' => $product->get_variation_price()))) . $product->get_price_suffix();
        } else {
            $display_price = wc_price(wc_get_price_to_display($product)) . $product->get_price_suffix();
        }
        ?>
        <div class="price-deal">
            <div class="price">
                <?php echo apply_filters('woocommerce_get_price_html', apply_filters('woocommerce_price_html', $display_price, $product), $product); ?>
                <span><?php esc_html_e('Value', 'foodpicky'); ?></span>
            </div>
        </div>
        <?php
    }
}

add_filter('azexo_entry_field', 'azexo_azd_entry_field', 10, 2);

function azexo_azd_entry_field($output, $name) {
    global $product;
    if (class_exists('WooCommerce') && $product) {
        switch ($name) {
            case 'deal_time_left':
                ob_start();
                azexo_azd_deal_time_left();
                return ob_get_clean();
                break;
            case 'price_offer':
                ob_start();
                azexo_azd_price_offer();
                return ob_get_clean();
                break;
            case 'price_deal':
                ob_start();
                add_filter('woocommerce_price_trim_zeros', '__return_true');
                azexo_azd_price_deal();
                remove_filter('woocommerce_price_trim_zeros', '__return_true');
                return ob_get_clean();
                break;
            case 'price':
                return '<span class="price">' . $product->get_price_html() . '</span>';
                break;
            case 'price_trimmed':
                add_filter('woocommerce_price_trim_zeros', '__return_true');
                $price = '<span class="price">' . $product->get_price_html() . '</span>';
                remove_filter('woocommerce_price_trim_zeros', '__return_true');
                return $price;
                break;
            case 'product_rating':
                return wc_get_rating_html($product->get_average_rating());
                break;
            case 'add_to_cart':
                ob_start();
                woocommerce_template_loop_add_to_cart();
                return '<span class="add-to-cart">' . ob_get_clean() . '</span>';
                break;
            case 'single_title':
                ob_start();
                woocommerce_template_single_title();
                return ob_get_clean();
                break;
            case 'single_summary':
                ob_start();
                woocommerce_template_single_excerpt();
                return ob_get_clean();
                break;
            case 'group_deal_purchases_left':
                if (function_exists('azd_is_group_off') && azd_is_group_off(get_the_ID())) {
                    $minimum_sales = get_post_meta(get_the_ID(), 'minimum_sales', true);
                    $total_sales = (int) get_post_meta(get_the_ID(), 'total_sales', true);
                    $total_presales = (int) get_post_meta(get_the_ID(), 'total_presales', true);
                    $output = '<div class="group-deal-sales-left"><span class="sales-left">' . esc_html($minimum_sales - $total_sales - $total_presales) . '</span> <span class="helper">' . esc_html__('purchases left to be deal on', 'foodpicky') . '</span></div>' . "\n";
                    return $output;
                }
                break;
            case 'purchased_deals':
                $deals = esc_html__('items', 'foodpicky');
                if (azexo_azd_is_on_sale($product)) {
                    $deals = esc_html__('deals', 'foodpicky');
                }
                $total_sales = (int) get_post_meta(get_the_ID(), 'total_sales', true);
                $total_presales = (int) get_post_meta(get_the_ID(), 'total_presales', true);
                $output = '<div class="purchased-deals-wrapper">';
                $output .= '<span class="deals">' . esc_html($total_sales + $total_presales) . ' ' . $deals . '</span>' . "\n";
                $output .= '<span class="purchased">' . esc_html__('Purchased', 'foodpicky') . '</span>';
                $output .= '</div>';
                return $output;
                break;
            case 'seller_info':
                $output = '<div class="title"><h3>' . esc_html__('Seller info', 'foodpicky') . '</h3></div>';
                if (class_exists('WC_Vendors')) {
                    global $post, $product, $woocommerce;
                    $seller_info = get_user_meta($post->post_author, 'pv_seller_info', true);
                    $has_html = get_user_meta($post->post_author, 'pv_shop_html_enabled', true);
                    $global_html = get_option('wcvendors_display_shop_description_html');
                    if (!empty($seller_info)) {
                        $seller_info = do_shortcode($seller_info);
                        $output .= '<div class="pv_seller_info">';
                        $output .= apply_filters('wcv_before_seller_info_tab', '');
                        $output .= ( $global_html || $has_html ) ? wpautop(wptexturize(wp_kses_post($seller_info))) : sanitize_text_field($seller_info);
                        $output .= apply_filters('wcv_after_seller_info_tab', '');
                        $output .= '</div>';
                    }
                }
                return $output;
                break;
            case 'sold_by':
                if (class_exists('WCV_Vendor_Shop')) {
                    ob_start();
                    WCV_Vendor_Shop::template_loop_sold_by($product->get_id());
                    return ob_get_clean();
                }
                break;
        }
    }
    return $output;
}

//add_action('pre_get_posts', 'azexo_azd_pre_get_posts', 20);

function azexo_azd_pre_get_posts($query) {
    if (!is_admin() && !$query->is_page()) {
        $post_type = $query->get('post_type');
        if (!is_array($post_type)) {
            $post_type = array($post_type);
        }
        if ((in_array('product', $post_type) && count($post_type) == 1) || $query->get('product_cat') || $query->get('product_tag') || $query->get('location')) {
            $meta_query = $query->get('meta_query');


//            $meta_query[] = array(
//                'relation' => 'OR',
//                array(
//                    'key' => '_sale_price_dates_from',
//                    'value' => '',
//                    'compare' => '=',
//                ),
//                array(
//                    'key' => '_sale_price_dates_from',
//                    'compare' => 'NOT EXISTS',
//                ),
//                array(
//                    'key' => '_sale_price_dates_from',
//                    'value' => current_time('timestamp'),
//                    'compare' => '<'
//                ),
//            );
//
//            $meta_query[] = array(
//                'relation' => 'OR',
//                array(
//                    'key' => '_sale_price_dates_to',
//                    'value' => '',
//                    'compare' => '=',
//                ),
//                array(
//                    'key' => '_sale_price_dates_to',
//                    'compare' => 'NOT EXISTS',
//                ),
//                array(
//                    'key' => '_sale_price_dates_to',
//                    'value' => current_time('timestamp'),
//                    'compare' => '>'
//                ),
//            );
            
            $meta_query[] = array(
                'key' => '_sale_price_dates_from',
                'value' => current_time('timestamp'),
                'compare' => '<'
            );
            $meta_query[] = array(
                'key' => '_sale_price_dates_to',
                'value' => current_time('timestamp'),
                'compare' => '>'
            );

            $query->set('meta_query', $meta_query);
        }
    }
    return $query;
}
