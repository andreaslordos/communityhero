<?php

/**
 * @param $post_to_update_id
 * @param $import_id
 * @param $iteration
 *
 * @throws \Exception
 */
function pmwi_pmxi_do_not_update_existing($post_to_update_id, $import_id, $iteration) {
	
	$args = array(
		'post_type' => 'product_variation',
		'meta_query' => array(
			array(
				'key' => '_sku',
				'value' => get_post_meta($post_to_update_id, '_sku', true),
			)
		)
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ){
		$duplicate_id = $query->post->ID;
		if ($duplicate_id) {
			$postRecord = new PMXI_Post_Record();	
			$postRecord->clear();
			$postRecord->getBy(array(
				'post_id'   => $duplicate_id,
				'import_id' => $import_id
			));	
			if ( ! $postRecord->isEmpty() ) $postRecord->set(array('iteration' => $iteration))->update();
		}
	}	
}
