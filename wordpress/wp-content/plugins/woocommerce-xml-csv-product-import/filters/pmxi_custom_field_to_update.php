<?php

/**
 *
 * Check if custom field needs to be updated.
 *
 * @param $field_to_update
 * @param $post_type
 * @param $options
 * @param $m_key
 *
 * @return bool
 */
function pmwi_pmxi_custom_field_to_update($field_to_update, $post_type, $options, $m_key) {

    if ($field_to_update === FALSE || $post_type != 'product' || strpos($m_key, 'attribute_') === FALSE) {
        return $field_to_update;
    }
    // Do not update attributes.
    if ($options['update_all_data'] == 'no' && !$options['is_update_attributes'] && (!in_array($m_key, [
                '_default_attributes',
                '_product_attributes',
            ]) || strpos($m_key, "attribute_") === FALSE)) {
        return TRUE;
    }
    if ($options['is_update_attributes'] && $options['update_attributes_logic'] == 'full_update') {
        return TRUE;
    }
    if ($options['is_update_attributes'] && $options['update_attributes_logic'] == "only" && !empty($options['attributes_list']) && is_array($options['attributes_list']) && in_array(str_replace("attribute_", "", $m_key), $options['attributes_list'])) {
        return TRUE;
    }
    if ($options['is_update_attributes'] && $options['update_attributes_logic'] == "all_except" && (empty($options['attributes_list']) || !in_array(str_replace("attribute_", "", $m_key), $options['attributes_list']))) {
        return TRUE;
    }
    return FALSE;
}