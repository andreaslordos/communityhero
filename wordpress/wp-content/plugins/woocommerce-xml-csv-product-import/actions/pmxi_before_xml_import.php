<?php

/**
 * @param $import_id
 */
function pmwi_pmxi_before_xml_import($import_id ) {
	delete_option('wp_all_import_' . $import_id . '_parent_product');
	delete_option('wp_all_import_not_linked_products_' . $import_id);
	delete_option('wp_all_import_previously_updated_order_' . $import_id);
    delete_option('wp_all_import_products_maybe_to_delete_' . $import_id);
    delete_option('wp_all_import_product_stack_' . $import_id);
}