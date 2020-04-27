<?php

/**
 *
 * Check if custom field needs to be deleted.
 *
 * @param $field_to_delete
 * @param $pid
 * @param $post_type
 * @param $options
 * @param $cur_meta_key
 *
 * @return bool
 */
function pmwi_pmxi_custom_field_to_delete($field_to_delete, $pid, $post_type, $options, $cur_meta_key){

	if ($field_to_delete === false || $post_type != "product") return $field_to_delete;

	if (in_array($cur_meta_key, array('total_sales', '_stock_status'))) return false;

	if ($cur_meta_key == '_is_first_variation_created') {
		delete_post_meta($pid, $cur_meta_key);
		return false;
	}

	// Do not update attributes.
	if ($options['update_all_data'] == 'no' && !$options['is_update_attributes'] && (in_array($cur_meta_key, array('_default_attributes', '_product_attributes')) || strpos($cur_meta_key, "attribute_") === 0)) return false;

    // Don't touch existing, only add new attributes.
    if ($options['update_all_data'] == 'no' && $options['is_update_attributes'] && $options['update_attributes_logic'] == 'add_new'){
        if (in_array($cur_meta_key, array('_default_attributes', '_product_attributes')) || strpos($cur_meta_key, "attribute_") === 0 ){
            return false;
        }
    }

	// Update only these Attributes, leave the rest alone.
	if ($options['update_all_data'] == 'no' && $options['is_update_attributes'] && $options['update_attributes_logic'] == 'only'){
		
		if ($cur_meta_key == '_product_attributes'){
			$current_product_attributes = get_post_meta($pid, '_product_attributes', true);
			if ( ! empty($current_product_attributes) && ! empty($options['attributes_list']) && is_array($options['attributes_list']))
				foreach ($current_product_attributes as $attr_name => $attr_value) {
					if ( in_array($attr_name, array_filter($options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
				}

			update_post_meta($pid, '_product_attributes', $current_product_attributes);
			return false;
		}

		if ( strpos($cur_meta_key, "attribute_") === 0 && !empty($options['attributes_list']) && is_array($options['attributes_list']) && !in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($options['attributes_list'], 'trim'))) return false;

		if (in_array($cur_meta_key, array('_default_attributes'))) return false;

	}

	// Leave these attributes alone, update all other Attributes.
	if ($options['update_all_data'] == 'no' && $options['is_update_attributes'] && $options['update_attributes_logic'] == 'all_except') {
		
		if ($cur_meta_key == '_product_attributes'){
			
			if (empty($options['attributes_list'])) { delete_post_meta($pid, $cur_meta_key); return false; }

			$current_product_attributes = get_post_meta($pid, '_product_attributes', true);
			if ( ! empty($current_product_attributes) && ! empty($options['attributes_list']) && is_array($options['attributes_list']))
				foreach ($current_product_attributes as $attr_name => $attr_value) {
					if ( ! in_array($attr_name, array_filter($options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
				}
				
			update_post_meta($pid, '_product_attributes', $current_product_attributes);
			return false;
		}

		if ( strpos($cur_meta_key, "attribute_") === 0 && !empty($options['attributes_list']) && is_array($options['attributes_list']) && in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($options['attributes_list'], 'trim'))) return false;

		if (in_array($cur_meta_key, array('_default_attributes'))) return false;
	}
	
	return true;		
}
