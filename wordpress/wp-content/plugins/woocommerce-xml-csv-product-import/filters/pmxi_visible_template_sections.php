<?php

/**
 *
 * Filter sections visibility on import template screen.
 *
 * @param $sections
 * @param $post_type
 *
 * @return array
 */
function pmwi_pmxi_visible_template_sections( $sections, $post_type ) {
	// Render order's template only for bundle and import with WP All Import featured.
	if ( 'shop_order' == $post_type && class_exists('WooCommerce') ) {
        return array('main', 'cf');
    }
	return $sections;
}