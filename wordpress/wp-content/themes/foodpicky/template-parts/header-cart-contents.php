<?php
$woocommerce_cart_page_id = get_option('woocommerce_cart_page_id');
global $woocommerce;
if (isset($woocommerce) && $woocommerce_cart_page_id) {
    ?><div class="cart"><input id="cart-contents-toggle" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);"><div class="link">
        <a href="<?php print get_permalink($woocommerce_cart_page_id) ?>" class="menu-link">
        <span class="fa fa-shopping-cart"></span>
        <span class="count">
            <?php print esc_html($woocommerce->cart->cart_contents_count) ?>
        </span>
    </a><label for="cart-contents-toggle"></label>
    </div><?php
$type = 'WC_Widget_Cart';
$args = array();
global $wp_widget_factory;
if (is_object($wp_widget_factory) && isset($wp_widget_factory->widgets, $wp_widget_factory->widgets[$type])) {
    the_widget($type, $atts, $args);
}
?></div><?php
}
?>