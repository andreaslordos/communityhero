<?php

/**
 * @param $isWizard
 * @param $post
 */
function pmwi_pmxi_options_tab($isWizard, $post) {
	// Render order's view only for bundle and import with WP All Import featured.
	if ( $post['custom_type'] == 'shop_order' && class_exists('WooCommerce') ) {
        $pmwi_controller = new PMWI_Admin_Import();
        $pmwi_controller->options( $isWizard, $post );
    }
    if ( $post['custom_type'] == 'product' && class_exists('WooCommerce') ) {
        wp_enqueue_script('pmwi-admin-options-script', PMWI_ROOT_URL . '/static/js/admin-options.js', array('jquery'), PMWI_FREE_VERSION);
        wp_enqueue_style('pmwi-admin-options-style', PMWI_ROOT_URL . '/static/css/admin-options.css', array(), PMWI_FREE_VERSION);
    }
}
