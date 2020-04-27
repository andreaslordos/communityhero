<?php
/**
 * @param $errors
 * @param $post
 * @param $importObj
 *
 * @return mixed
 */
function pmwi_pmxi_options_validation($errors, $post, $importObj) {
    // Validate import template settings for variable products.
    if (isset($post['multiple_product_type']) && $post['multiple_product_type'] == 'variable' && empty($post['link_all_variations'])) {
        switch ($post['matching_parent']) {
            case 'auto':
                if (empty($post['single_product_id'])) {
                    $errors->add('form-validation', __('`SKU element for parent` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                if (empty($post['single_product_parent_id'])) {
                    $errors->add('form-validation', __('`Parent SKU element for variation` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
            case 'first_is_parent_id':
                if (empty($post['single_product_id_first_is_parent_id'])) {
                    $errors->add('form-validation', __('`Unique Value` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
            case 'first_is_parent_title':
                if (empty($post['single_product_id_first_is_parent_title'])) {
                    $errors->add('form-validation', __('`Product Title` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
            case 'first_is_variation':
                if (empty($post['single_product_id_first_is_variation'])) {
                    $errors->add('form-validation', __('`Product Title` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
            case 'xml':
                if (empty($post['variations_xpath'])) {
                    $errors->add('form-validation', __('`Variations XPath` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
            case 'existing':
                if ($post['existing_parent_product_matching_logic'] == 'custom field') {
                    if (empty($post['existing_parent_product_cf_name'])) {
                        $errors->add('form-validation', __('`Custom Field Name` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                    }
                    if (empty($post['existing_parent_product_cf_value'])) {
                        $errors->add('form-validation', __('`Custom Field Value` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                    }
                }
                if ($post['existing_parent_product_matching_logic'] == 'title' && empty($post['existing_parent_product_title'])) {
                    $errors->add('form-validation', __('`Parent Title` must be specified on the Variations Tab', PMWI_Plugin::TEXT_DOMAIN));
                }
                break;
        }
    }
    return $errors;
}