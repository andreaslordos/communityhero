<?php

/**
 * @param $entry
 * @param array $post
 *
 * @return bool
 */
function pmwi_pmxi_extend_options_main($entry, $post = array()) {
	if ( ! in_array($entry, array('product', 'shop_order')) and empty($post['is_override_post_type'])) {
	    return FALSE;
    }
	$woo_controller = new PMWI_Admin_Import();										
	$woo_controller->index($post);
}
