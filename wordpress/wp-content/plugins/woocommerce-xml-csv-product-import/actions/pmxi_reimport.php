<?php

/**
 * @param $post_type
 * @param $post
 */
function pmwi_pmxi_reimport($post_type, $post) {

	if ( ! in_array($post_type, array('product')) and empty($post['is_override_post_type']) or ! class_exists('WooCommerce')) {
	    return FALSE;
    }

	switch ($post_type) {
		case 'product':
			$all_existing_attributes = array();
			$hide_taxonomies = array('product_type');
			$post_taxonomies = array_diff_key(get_taxonomies_by_object_type(array($post_type), 'object'), array_flip($hide_taxonomies));
			if (!empty($post_taxonomies)): 
				foreach ($post_taxonomies as $ctx):  if ("" == $ctx->labels->name or strpos($ctx->name, "pa_") === false) continue;
					$all_existing_attributes[] = $ctx->name;												
				endforeach;
			endif;
			if (!empty($existing_attributes)):
				foreach ($existing_attributes as $key => $attr) {
					$all_existing_attributes[] = $attr;												
				}
			endif;

			?>
			<div class="input">
				<input type="hidden" name="is_update_product_type" value="0" />
				<input type="checkbox" id="is_update_product_type_<?php echo $post_type; ?>" name="is_update_product_type" value="1" <?php echo $post['is_update_product_type'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_product_type_<?php echo $post_type; ?>"><?php _e('Product Type', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>

			<?php if ( PMXI_EDITION == 'paid' && version_compare(PMXI_VERSION, '4.4.9-beta-1.7') < 0 || PMXI_EDITION == 'free' && version_compare(PMXI_VERSION, '3.4.5') < 0 ): ?>
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
					<?php _e('As of WooCommerce 3.0 this data is no longer stored as a custom field - use the advanced options above.', PMWI_Plugin::TEXT_DOMAIN); ?>
				</div>
				<div class="wp_all_import_woocommerce_stock_status_notice_template">
					<?php _e('As of WooCommerce 3.0 stock status is automatically updated when stock is updated.', PMWI_Plugin::TEXT_DOMAIN); ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="input">		
				<input type="hidden" name="attributes_list" value="0" />			
				<input type="hidden" name="is_update_attributes" value="0" />
				<input type="checkbox" id="is_update_attributes_<?php echo $post_type; ?>" name="is_update_attributes" value="1" <?php echo $post['is_update_attributes'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_attributes_<?php echo $post_type; ?>"><?php _e('Attributes', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<div class="switcher-target-is_update_attributes_<?php echo $post_type; ?>" style="padding-left:17px;">
					<div class="input">
						<input type="radio" id="update_attributes_logic_full_update_<?php echo $post_type; ?>" name="update_attributes_logic" value="full_update" <?php echo ( "full_update" == $post['update_attributes_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_attributes_logic_full_update_<?php echo $post_type; ?>"><?php _e('Update all Attributes', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					</div>
					<div class="input">
						<input type="radio" id="update_attributes_logic_only_<?php echo $post_type; ?>" name="update_attributes_logic" value="only" <?php echo ( "only" == $post['update_attributes_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_attributes_logic_only_<?php echo $post_type; ?>"><?php _e('Update only these Attributes, leave the rest alone', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-update_attributes_logic_only_<?php echo $post_type; ?> pmxi_choosen" style="padding-left:17px;">										
							
							<span class="hidden choosen_values"><?php if (!empty($all_existing_attributes)) echo implode(',', $all_existing_attributes);?></span>
							<input class="choosen_input" value="<?php if (!empty($post['attributes_list']) and "only" == $post['update_attributes_logic']) echo implode(',', $post['attributes_list']); ?>" type="hidden" name="attributes_only_list"/>																				
						</div>
					</div>
					<div class="input">
						<input type="radio" id="update_attributes_logic_all_except_<?php echo $post_type; ?>" name="update_attributes_logic" value="all_except" <?php echo ( "all_except" == $post['update_attributes_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_attributes_logic_all_except_<?php echo $post_type; ?>"><?php _e('Leave these Attributes alone, update all other Attributes', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-update_attributes_logic_all_except_<?php echo $post_type; ?> pmxi_choosen" style="padding-left:17px;">
							
							<span class="hidden choosen_values"><?php if (!empty($all_existing_attributes)) echo implode(',', $all_existing_attributes);?></span>
							<input class="choosen_input" value="<?php if (!empty($post['attributes_list']) and "all_except" == $post['update_attributes_logic']) echo implode(',', $post['attributes_list']); ?>" type="hidden" name="attributes_except_list"/>																														
						</div>
					</div>
                    <div class="input">
                        <input type="radio" id="update_attributes_logic_add_new_<?php echo $post_type; ?>" name="update_attributes_logic" value="add_new" <?php echo ( "add_new" == $post['update_attributes_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
                        <label for="update_attributes_logic_add_new_<?php echo $post_type; ?>"><?php _e('Don\'t touch existing Attributes, add new Attributes', PMWI_Plugin::TEXT_DOMAIN) ?></label>
                    </div>
				</div>
			</div>	
			<?php
			break;
		default:
			# code...
			break;
	}	
}