<?php
/**
 * Filter import options before saving.
 *
 * @param $post
 * @return mixed
 */
function pmwi_pmxi_save_options($post) {
	if ($post['update_attributes_logic'] == 'only') {
		$post['attributes_list'] = empty($post['attributes_only_list']) ? array() : explode(",", $post['attributes_only_list']);
	}
	elseif ($post['update_attributes_logic'] == 'all_except') {
		$post['attributes_list'] = empty($post['attributes_except_list']) ? array() : explode(",", $post['attributes_except_list']);
	}
	return $post;
}