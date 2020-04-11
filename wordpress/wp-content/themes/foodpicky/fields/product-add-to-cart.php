<?php

/*
  Field Name: Product add to cart link
 */
?>
<?php

$product = wc_get_product();
$product_id = $product->get_id();
if ($product->get_type() == 'variable') {
    foreach ($product->get_available_variations() as $available_variation) {
        $default = true;
        foreach ($product->get_default_attributes() as $defkey => $defval) {
            if ($available_variation['attributes']['attribute_' . $defkey] != $defval) {
                $default = false;
            }
        }
        if ($default) {
            $product_id = $available_variation['variation_id'];
            break;
        }
    }
}
echo apply_filters('woocommerce_loop_add_to_cart_link', sprintf('<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', esc_url($product->add_to_cart_url()), 1, esc_attr($product_id), esc_attr($product->get_sku()), implode(' ', array_filter(array(
    'button',
    'product_type_' . $product->get_type(),
    $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
    'ajax_add_to_cart'
                ))), esc_html__('Add to cart', 'woocommerce')
        ), $product);
?>