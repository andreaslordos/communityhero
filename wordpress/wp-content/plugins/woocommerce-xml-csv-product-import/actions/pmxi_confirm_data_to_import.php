<?php

/**
 * @param $isWizard
 * @param $post
 */
function pmwi_pmxi_confirm_data_to_import($isWizard, $post ) {
	// render order's view only for bundle and import with WP All Import featured
	if ( $post['custom_type'] == 'shop_order' && class_exists('WooCommerce') ) {
        $pmwi_controller = new PMWI_Admin_Import();
        $pmwi_controller->confirm( $isWizard, $post );
    }
}
