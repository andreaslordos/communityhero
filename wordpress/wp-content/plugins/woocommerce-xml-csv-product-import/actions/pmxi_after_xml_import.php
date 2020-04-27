<?php

/**
 * @param $importID
 *
 * @throws \Exception
 */
function pmwi_pmxi_after_xml_import($importID) {
    $import = new PMXI_Import_Record();
    $import->getById($importID);

    if (!$import->isEmpty() and in_array($import->options['custom_type'], array(
            'product',
            'product_variation'
        ))
    ) {
        // Sync parent products with variations.
        $productStack = get_option('wp_all_import_product_stack_' . $importID, array());
        foreach ($productStack as $parentProductID) {
            $parentProductType = get_post_type($parentProductID);
            if ( $parentProductType != 'product_variation') {
                XmlImportWooCommerceService::getInstance()
                    ->syncVariableProductData($parentProductID);
            }
        }
        delete_option('wp_all_import_product_stack_' . $importID);
        // Re-count WooCommerce Terms.
        $recount_terms_after_import = TRUE;
        $recount_terms_after_import = apply_filters('wp_all_import_recount_terms_after_import', $recount_terms_after_import, $importID);
        if ($recount_terms_after_import) {
            // Re-count categories & tags terms after import process is complete.
            if (($import->options['create_new_records'] && $import->options['is_keep_former_posts'] == 'yes')
                || ($import->options['is_keep_former_posts'] == 'no' && ($import->options['update_all_data'] == 'yes' || $import->options['is_update_categories']))) {
                $product_cats = get_terms('product_cat', array(
                    'hide_empty' => FALSE,
                    'fields' => 'id=>parent'
                ));
                _wc_term_recount($product_cats, get_taxonomy('product_cat'), TRUE, FALSE);
                $product_tags = get_terms('product_tag', array(
                    'hide_empty' => FALSE,
                    'fields' => 'id=>parent'
                ));
                _wc_term_recount($product_tags, get_taxonomy('product_tag'), TRUE, FALSE);
            }
            // Re-count attribute terms after import process is complete.
            if (($import->options['create_new_records'] && $import->options['is_keep_former_posts'] == 'yes')
                || ($import->options['is_keep_former_posts'] == 'no' && ($import->options['update_all_data'] == 'yes' || $import->options['is_update_attributes']))) {
                $attributes = wc_get_attribute_taxonomies();
                foreach ((array) $attributes as $attribute) {
                    $term_ids = get_terms(
                        [
                            'taxonomy' => wc_attribute_taxonomy_name($attribute->attribute_name),
                            'hide_empty' => FALSE,
                            'fields' => 'ids',
                        ]
                    );
                    if (!empty($term_ids)) {
                        wp_update_term_count_now($term_ids, wc_attribute_taxonomy_name($attribute->attribute_name));
                    }
                }
            }
            // Re-count shipping terms after import process is complete.
            if (($import->options['create_new_records'] && $import->options['is_keep_former_posts'] == 'yes')
                || ($import->options['is_keep_former_posts'] == 'no' && ($import->options['update_all_data'] == 'yes' || $import->options['is_update_custom_fields']))) {
                $shipping_terms = get_terms(
                    [
                        'taxonomy' => 'product_shipping_class',
                        'hide_empty' => FALSE,
                        'fields' => 'ids',
                    ]
                );
                if (!empty($shipping_terms)) {
                    wp_update_term_count_now($shipping_terms, 'product_shipping_class');
                }
            }
        }

        // Delete Missing Products.
        $maybe_to_delete = get_option('wp_all_import_products_maybe_to_delete_' . $importID);
        if (!empty($maybe_to_delete)) {
            foreach ($maybe_to_delete as $pid) {
                $children = get_posts(array(
                    'post_parent' => $pid,
                    'posts_per_page' => -1,
                    'post_type' => 'product_variation',
                    'fields' => 'ids',
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'post_status' => array(
                        'draft',
                        'publish',
                        'trash',
                        'pending',
                        'future',
                        'private'
                    )
                ));

                if (empty($children)) {
                    wp_delete_post($pid, TRUE);
                }
            }
            delete_option('wp_all_import_products_maybe_to_delete_' . $importID);
        }
        delete_option('wp_all_import_not_linked_products_' . $importID);
        // Regenerate product lookup tables.
        $regenerate_lookup_tables = TRUE;
        $regenerate_lookup_tables = apply_filters('wp_all_import_regenerate_lookup_tables', $regenerate_lookup_tables, $importID);
        if ( $regenerate_lookup_tables && function_exists('wc_update_product_lookup_tables_is_running') && ! wc_update_product_lookup_tables_is_running() ) {
            try {
                wc_update_product_lookup_tables();
            } catch (Exception $e) {}
        }
    }
}