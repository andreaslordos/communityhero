<?php

/**
 * @param $unique_key
 * @param $options
 *
 * @return string
 */
function pmwi_pmxi_unique_key($unique_key, $options) {

	if ($options['custom_type'] == 'product') {
		$unique_key .= ( ! empty($options['attribute_value'])) ? implode('-', $options['attribute_value']) : '';
	}
	return $unique_key;
}