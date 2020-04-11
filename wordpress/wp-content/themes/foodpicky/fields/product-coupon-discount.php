<?php
/*
  Field Name: Product coupon discount
 */
?>
<?php
$product = wc_get_product();
$discount = get_post_meta($product->get_id(), 'discount', true);
?>
<span class="discount"><?php print esc_html($discount); ?></span>
