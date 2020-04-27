<?php

/**
 *
 * Filter custom post types and return them is needed order.
 *
 * @param $custom_types
 *
 * @return array
 */
function pmwi_pmxi_custom_types($custom_types) {

    if (class_exists('WooCommerce')) {
        if (!empty($custom_types['product'])) {
            $custom_types['product']->labels->name = __('WooCommerce Products', PMWI_Plugin::TEXT_DOMAIN);
        }
        if (!empty($custom_types['shop_order'])) {
            $custom_types['shop_order']->labels->name = __('WooCommerce Orders', PMWI_Plugin::TEXT_DOMAIN);
        }
        if (!empty($custom_types['shop_coupon'])) {
            $custom_types['shop_coupon']->labels->name = __('WooCommerce Coupons', PMWI_Plugin::TEXT_DOMAIN);
        }
        if (!empty($custom_types['product_variation'])) {
            unset($custom_types['product_variation']);
        }
        if (!empty($custom_types['shop_order_refund'])) {
            unset($custom_types['shop_order_refund']);
        }

        $order = ['shop_order', 'shop_coupon', 'product'];

        $ordered_custom_types = [];

        foreach ($order as $type) {
            if (isset($ordered_custom_types[$type])) {
                continue;
            }
            foreach ($custom_types as $key => $custom_type) {
                if (isset($ordered_custom_types[$key])) {
                    continue;
                }
                if (in_array($key, $order)) {
                    if ($key == $type) {
                        $ordered_custom_types[$key] = $custom_type;
                    }
                }
                else {
                    $ordered_custom_types[$key] = $custom_type;
                }
            }
        }
        return $ordered_custom_types;
    }
    return $custom_types;
}
