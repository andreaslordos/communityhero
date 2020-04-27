<?php

/**
 *
 * Check if product allowed to be deleted.
 *
 * @param $to_delete
 * @param $pid
 * @param $import
 *
 * @return bool
 */
function pmwi_wp_all_import_is_post_to_delete($to_delete, $pid, $import) {
	if ( $import->options['custom_type'] == 'product') {
		$post_to_delete = get_post($pid);
		if ( $to_delete && $post_to_delete->post_type == 'product' && class_exists('WooCommerce')) {
			$children = get_posts( array(
				'post_parent' 	=> $pid,
				'posts_per_page'=> -1,
				'post_type' 	=> 'product_variation',
				'fields' 		=> 'ids',
				'orderby'		=> 'ID',
				'order'			=> 'ASC',
				'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
			) );	

			if ( count($children) ){	
				$to_delete = false;
                $maybe_to_delete = get_option('wp_all_import_products_maybe_to_delete_' . $import->id, array());
                if ( ! in_array($pid, $maybe_to_delete) ){
                    $maybe_to_delete[] = $pid;
                    update_option('wp_all_import_products_maybe_to_delete_' . $import->id, $maybe_to_delete);
                }
			}
		}
	}
	return $to_delete;
}