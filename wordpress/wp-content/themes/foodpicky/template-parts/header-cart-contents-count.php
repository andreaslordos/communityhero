<?php
$woocommerce_cart_page_id = get_option('woocommerce_cart_page_id');
global $woocommerce;
if (isset($woocommerce) && $woocommerce_cart_page_id) {
    ?><div class="cart"><a href="<?php print get_permalink($woocommerce_cart_page_id) ?>" class="menu-link">
        <span class="fa fa-shopping-cart"></span>
        <span class="count">
            <?php print esc_html($woocommerce->cart->cart_contents_count) ?>
        </span>
    </a></div><?php
}
?>