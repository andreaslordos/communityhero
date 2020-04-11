<?php
/**
 * Single Product Price, including microdata for SEO
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
<div class="offers">
    <p class="price"><?php print isset($options['single_price_prefix']) ? '<span class="prefix">' . $options['single_price_prefix'] . '</span>' : ''; ?> <?php print $product->get_price_html(); ?></p>    
</div>
