<?php

global $azexo_azgb_fields;

$azexo_azgb_fields = array(
    'deal_purchases_left' => esc_html__('Deal purchases left', 'foodpicky'),
);

add_filter('azexo_fields', 'azexo_azgb_fields');

function azexo_azgb_fields($azexo_fields) {
    global $azexo_azgb_fields;
    return array_merge($azexo_fields, $azexo_azgb_fields);
}

add_filter('azexo_fields_post_types', 'azexo_azgb_fields_post_types');

function azexo_azgb_fields_post_types($azexo_fields_post_types) {
    global $azexo_azgb_fields;
    $azexo_fields_post_types = array_merge($azexo_fields_post_types, array_combine(array_keys($azexo_azgb_fields), array_fill(0, count(array_keys($azexo_azgb_fields)), 'product')));
    return $azexo_fields_post_types;
}

add_filter('azexo_entry_field', 'azexo_azgb_entry_field', 10, 2);

function azexo_azgb_entry_field($output, $name) {
    if (class_exists('WooCommerce')) {
        global $product;
        switch ($name) {
            case 'deal_purchases_left':
                if (azgb_is_group_off(get_the_ID())) {
                    $minimum_sales = get_post_meta(get_the_ID(), 'minimum_sales', true);
                    $total_sales = (int) get_post_meta(get_the_ID(), 'total_sales', true);
                    $total_presales = (int) get_post_meta(get_the_ID(), 'total_presales', true);
                    $output = '<div class="deal-sales-left"><span class="sales-left">' . esc_html($minimum_sales - $total_sales - $total_presales) . '</span> <span class="helper">' . esc_html__('purchases left to be deal on', 'foodpicky') . '</span></div>' . "\n";
                    return $output;
                }
                break;
        }
    }
    return $output;
}
