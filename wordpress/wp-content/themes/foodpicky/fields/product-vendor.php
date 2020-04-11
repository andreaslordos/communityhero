<?php
/*
  Field Name: Product vendor
 */
?>
<?php
global $product, $post;
?>

<?php
if (class_exists('WCV_Vendors')) {
    $vendor = WCV_Vendors::get_vendor_from_product($product->get_id());
    if ($vendor) {
        $shop_page = WCV_Vendors::get_vendor_shop_page($vendor);
        $shop_name = WCV_Vendors::get_vendor_shop_name($vendor);
        if (!empty($shop_page) && !empty($shop_name)) {
            ?> <a href="<?php print esc_url($shop_page); ?>" class="vendor"><?php print esc_html($shop_name); ?></a>
            <?php
        }
    }
}
?>

