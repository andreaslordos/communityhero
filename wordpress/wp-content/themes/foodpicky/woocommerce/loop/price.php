<?php
/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     10.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$product = wc_get_product();
$options = get_option(AZEXO_FRAMEWORK);
?>

<?php if ($price_html = $product->get_price_html()) : ?>
    <span class="price"><?php print isset($options['loop_price_prefix']) ? '<span class="prefix">' . esc_html($options['loop_price_prefix']) . '</span> ' : ''; ?><?php print $price_html; ?></span>
<?php endif; ?>
