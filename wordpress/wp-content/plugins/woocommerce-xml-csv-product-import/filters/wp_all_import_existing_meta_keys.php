<?php

/**
 *
 * Filter available to import custom fields.
 *
 * @param $existing_meta_keys
 * @param $custom_type
 *
 * @return array
 */
function pmwi_wp_all_import_existing_meta_keys($existing_meta_keys, $custom_type) {
	if ( $custom_type == 'product') {
        $hide_fields = array('_regular_price_tmp', '_sale_price_tmp', '_sale_price_dates_from_tmp', '_sale_price_dates_from_tmp', '_sale_price_dates_to_tmp', '_price_tmp', '_stock_tmp', '_stock_status_tmp', '_product_image_gallery_tmp');
        if (version_compare(WOOCOMMERCE_VERSION, '3.0') >= 0){
            $hide_fields[] = '_stock_status';
            $hide_fields[] = '_visibility';
            $hide_fields[] = '_featured';
        }
        foreach ($existing_meta_keys as $key => $value) {
            if ( in_array($value, $hide_fields) || strpos($value, '_v_') === 0 || strpos($value, 'attribute_') === 0 ) {
                unset($existing_meta_keys[$key]);
            }
        }
        $existing_meta_keys = array_values($existing_meta_keys);
	}	
	return $existing_meta_keys;
}