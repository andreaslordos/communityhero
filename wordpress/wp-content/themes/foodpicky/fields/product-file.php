<?php
/*
  Field Name: Product attached file link
 */
?>
<?php
$product = wc_get_product();
$url = get_post_meta($product->get_id(), 'file', true);
?>

<?php if (!empty($url)) : ?>
    <a href="<?php print esc_url($url); ?>" class="add-review"><?php esc_attr_e('Download', 'foodpicky'); ?></a>
<?php endif; ?>


