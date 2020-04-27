<?php

/**
 *
 * Preset post type and status.
 *
 * @param $articleData
 * @param $import
 * @param $post_to_update
 *
 * @return mixed
 */
function pmwi_pmxi_article_data($articleData, $import, $post_to_update) {
	if (!empty($articleData['post_type']) && $articleData['post_type'] == 'product' && $import->options['update_all_data'] == 'no' && !$import->options['is_update_product_type'] && !empty($post_to_update)) {
		$articleData['post_type'] = $post_to_update->post_type;		
	}
	if (!empty($articleData['post_type']) && $articleData['post_type'] == 'shop_order' && !empty($post_to_update)) {
		$articleData['post_status'] = $post_to_update->post_status;		
	}
    if (!empty($articleData['post_type']) && $articleData['post_type'] == 'product' && !empty($post_to_update) && $post_to_update->post_type == 'product_variation') {
        $articleData['post_parent'] = $post_to_update->post_parent;
        $articleData['post_type'] = 'product_variation';
    }
	return $articleData;
}