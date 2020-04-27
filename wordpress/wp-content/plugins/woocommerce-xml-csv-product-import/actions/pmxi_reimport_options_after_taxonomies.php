<?php

/**
 * @param $post_type
 * @param $post
 */
function pmwi_pmxi_reimport_options_after_taxonomies($post_type, $post){

	if ( ! in_array($post_type, array('product')) and empty($post['is_override_post_type']) or ! class_exists('WooCommerce')) {
	    return FALSE;
    }

	switch ($post_type) {
		case 'product':
			if ( version_compare(WOOCOMMERCE_VERSION, '3.0') >= 0 && ( PMXI_EDITION == 'paid' && version_compare(PMXI_VERSION, '4.4.9-beta-1.7') >= 0 || PMXI_EDITION == 'free' && version_compare(PMXI_VERSION, '3.4.5') >= 0 )):
				?>
				<div class="input">
					<input type="hidden" name="attributes_list" value="0" />
					<input type="hidden" name="is_update_advanced_options" value="0" />
					<input type="checkbox" id="is_update_advanced_options_<?php echo $post_type; ?>" name="is_update_advanced_options" value="1" <?php echo $post['is_update_advanced_options'] ? 'checked="checked"': '' ?>  class="switcher"/>
					<label for="is_update_advanced_options_<?php echo $post_type; ?>"><?php _e('Advanced WooCommerce Options', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					<div class="switcher-target-is_update_advanced_options_<?php echo $post_type; ?>" style="padding-left:17px;">
						<div class="input">
							<input type="hidden" name="is_update_catalog_visibility" value="0" />
							<input type="checkbox" id="is_update_catalog_visibility_<?php echo $post_type; ?>" name="is_update_catalog_visibility" value="1" <?php echo $post['is_update_catalog_visibility'] ? 'checked="checked"': '' ?>  class="switcher"/>
							<label for="is_update_catalog_visibility_<?php echo $post_type; ?>"><?php _e('Update Catalog Visibility', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						</div>
						<div class="input">
							<input type="hidden" name="is_update_featured_status" value="0" />
							<input type="checkbox" id="is_update_featured_status_<?php echo $post_type; ?>" name="is_update_featured_status" value="1" <?php echo $post['is_update_featured_status'] ? 'checked="checked"': '' ?>  class="switcher"/>
							<label for="is_update_featured_status_<?php echo $post_type; ?>"><?php _e('Update Featured Status', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						</div>
					</div>
					<div class="wp_all_import_woocommerce_deprecated_fields_notice_template">
						<?php _e('As of WooCommerce 3.0 this data is no longer stored as a custom field - use the advanced options below.', PMWI_Plugin::TEXT_DOMAIN); ?>
					</div>
					<div class="wp_all_import_woocommerce_stock_status_notice_template">
						<?php _e('As of WooCommerce 3.0 stock status is automatically updated when stock is updated.', PMWI_Plugin::TEXT_DOMAIN); ?>
					</div>
				</div>
				<?php
			endif;
			break;
		default:
			# code...
			break;
	}	
}