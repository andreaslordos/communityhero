<?php if ( ! $isWizard  or ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' or $isWizard and "new" != $post['wizard_type']): ?>
<h4><?php _e('When WP All Import finds new or changed data...', PMWI_Plugin::TEXT_DOMAIN); ?></h4>
<?php else: ?>
<h4><?php _e('If this import is run again and WP All Import finds new or changed data...', PMWI_Plugin::TEXT_DOMAIN); ?></h4>
<?php endif; ?>
<div class="input">
	<input type="hidden" name="create_new_records" value="0" />
	<input type="checkbox" id="create_new_records" name="create_new_records" value="1" <?php echo $post['create_new_records'] ? 'checked="checked"' : '' ?> />
	<label for="create_new_records"><?php _e('Create new orders from records newly present in your file', PMWI_Plugin::TEXT_DOMAIN) ?></label>
	<?php if ( ! empty(PMXI_Plugin::$session->deligate) and PMXI_Plugin::$session->deligate == 'wpallexport' ): ?>
	<a href="#help" class="wpallimport-help" title="<?php _e('New orders will only be created when ID column is present and value in ID column is unique.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="top: -1px;">?</a>
	<?php endif; ?>
</div>
<div class="switcher-target-auto_matching">
	<div class="input">
		<input type="hidden" name="is_delete_missing" value="0" />
		<input type="checkbox" id="is_delete_missing" name="is_delete_missing" value="1" <?php echo $post['is_delete_missing'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
		<label for="is_delete_missing" <?php if ( "new" != $post['wizard_type']): ?>style="color:#ccc;"<?php endif; ?>><?php _e('Delete orders that are no longer present in your file', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		<?php if ( "new" != $post['wizard_type']): ?>
		<a href="#help" class="wpallimport-help" title="<?php _e('Records removed from the import file can only be deleted when importing into New Items. This feature cannot be enabled when importing into Existing Items.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top: -1px;">?</a>
		<?php endif; ?>	
	</div>
	<div class="switcher-target-is_delete_missing" style="padding-left:17px;">
		<div class="input">
			<input type="hidden" name="is_keep_attachments" value="0" />
			<input type="checkbox" id="is_keep_attachments" name="is_keep_attachments" value="1" <?php echo $post['is_keep_attachments'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_keep_attachments"><?php _e('Do not remove attachments', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>
		<div class="input">
			<input type="hidden" name="is_keep_imgs" value="0" />
			<input type="checkbox" id="is_keep_imgs" name="is_keep_imgs" value="1" <?php echo $post['is_keep_imgs'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_keep_imgs"><?php _e('Do not remove images', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>
		<div class="input">
			<input type="hidden" name="is_update_missing_cf" value="0" />
			<input type="checkbox" id="is_update_missing_cf" name="is_update_missing_cf" value="1" <?php echo $post['is_update_missing_cf'] ? 'checked="checked"': '' ?> class="switcher" <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="is_update_missing_cf"><?php _e('Instead of deletion, set Custom Field', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			<div class="switcher-target-is_update_missing_cf" style="padding-left:17px;">
				<div class="input">
					<?php _e('Name', PMWI_Plugin::TEXT_DOMAIN) ?>
					<input type="text" name="update_missing_cf_name" value="<?php echo esc_attr($post['update_missing_cf_name']) ?>" />
					<?php _e('Value', PMWI_Plugin::TEXT_DOMAIN) ?>
					<input type="text" name="update_missing_cf_value" value="<?php echo esc_attr($post['update_missing_cf_value']) ?>" />									
				</div>
			</div>
		</div>
		<div class="input">
			<input type="hidden" name="set_missing_to_draft" value="0" />
			<input type="checkbox" id="set_missing_to_draft" name="set_missing_to_draft" value="1" <?php echo $post['set_missing_to_draft'] ? 'checked="checked"': '' ?> <?php if ( "new" != $post['wizard_type']): ?>disabled="disabled"<?php endif; ?>/>
			<label for="set_missing_to_draft"><?php _e('Instead of deletion, change post status to Draft', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>
	</div>	
</div>	
<div class="input">
	<input type="hidden" id="is_keep_former_posts" name="is_keep_former_posts" value="yes" />				
	<input type="checkbox" id="is_not_keep_former_posts" name="is_keep_former_posts" value="no" <?php echo "yes" != $post['is_keep_former_posts'] ? 'checked="checked"': '' ?> class="switcher" />
	<label for="is_not_keep_former_posts"><?php _e('Update existing orders with changed data in your file', PMWI_Plugin::TEXT_DOMAIN) ?></label>
	<?php if ( $isWizard and "new" == $post['wizard_type'] and empty(PMXI_Plugin::$session->deligate)): ?>
	<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('These options will only be used if you run this import again later. All data is imported the first time you run an import.<br/><br/>Note that WP All Import will only update/remove posts created by this import. If you want to match to posts that already exist on this site, use Existing Items in Step 1.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a>
	<?php endif; ?>
	<div class="switcher-target-is_not_keep_former_posts" style="padding-left:17px;">
		<input type="radio" id="update_all_data" class="switcher" name="update_all_data" value="yes" <?php echo 'no' != $post['update_all_data'] ? 'checked="checked"': '' ?>/>
		<label for="update_all_data"><?php _e('Update all data', PMWI_Plugin::TEXT_DOMAIN )?></label><br>
		
		<input type="radio" id="update_choosen_data" class="switcher" name="update_all_data" value="no" <?php echo 'no' == $post['update_all_data'] ? 'checked="checked"': '' ?>/>
		<label for="update_choosen_data"><?php _e('Choose which data to update', PMWI_Plugin::TEXT_DOMAIN )?></label><br>
		<div class="switcher-target-update_choosen_data"  style="padding-left:27px;">
			<div class="input">
				<h4 class="wpallimport-trigger-options wpallimport-select-all" rel="<?php _e("Unselect All", PMWI_Plugin::TEXT_DOMAIN); ?>"><?php _e("Select All", PMWI_Plugin::TEXT_DOMAIN); ?></h4>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_status" value="0" />
				<input type="checkbox" id="is_update_status" name="is_update_status" value="1" <?php echo $post['is_update_status'] ? 'checked="checked"': '' ?> />
				<label for="is_update_status"><?php _e('Order status', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<a href="#help" class="wpallimport-help" style="position: relative; top: -2px;" title="<?php _e('Hint: uncheck this box to keep trashed orders in the trash.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a>
			</div>						
			<div class="input">
				<input type="hidden" name="is_update_excerpt" value="0" />
				<input type="checkbox" id="is_update_excerpt" name="is_update_excerpt" value="1" <?php echo $post['is_update_excerpt'] ? 'checked="checked"': '' ?> />
				<label for="is_update_excerpt"><?php _e('Customer Note', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_dates" value="0" />
				<input type="checkbox" id="is_update_dates" name="is_update_dates" value="1" <?php echo $post['is_update_dates'] ? 'checked="checked"': '' ?> />
				<label for="is_update_dates"><?php _e('Dates', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_billing_details" value="0" />
				<input type="checkbox" id="is_update_billing_details" name="is_update_billing_details" value="1" <?php echo $post['is_update_billing_details'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_billing_details"><?php _e('Billing Details', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_shipping_details" value="0" />
				<input type="checkbox" id="is_update_shipping_details" name="is_update_shipping_details" value="1" <?php echo $post['is_update_shipping_details'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_shipping_details"><?php _e('Shipping Details', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_payment" value="0" />
				<input type="checkbox" id="is_update_payment" name="is_update_payment" value="1" <?php echo $post['is_update_payment'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_payment"><?php _e('Payment Details', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_notes" value="0" />
				<input type="checkbox" id="is_update_notes" name="is_update_notes" value="1" <?php echo $post['is_update_notes'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_notes"><?php _e('Order Notes', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_products" value="0" />
				<input type="checkbox" id="is_update_products" name="is_update_products" value="1" <?php echo $post['is_update_products'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_products"><?php _e('Product Items', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<div class="switcher-target-is_update_products" style="padding-left:17px;">
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_products_logic_full_update" name="update_products_logic" value="full_update" <?php echo ( "full_update" == $post['update_products_logic'] ) ? 'checked="checked"': '' ?> />
						<label for="update_products_logic_full_update"><?php _e('Update all products', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					</div>					
					<div class="input" style="margin-bottom:3px;">								
						<input type="radio" id="update_products_logic_add_new" name="update_products_logic" value="add_new" <?php echo ( "add_new" == $post['update_products_logic'] ) ? 'checked="checked"': '' ?> />
						<label for="update_products_logic_add_new"><?php _e('Don\'t touch existing products, append new products', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					</div>					
				</div>
			</div>			
			<div class="input">
				<input type="hidden" name="is_update_fees" value="0" />
				<input type="checkbox" id="is_update_fees" name="is_update_fees" value="1" <?php echo $post['is_update_fees'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_fees"><?php _e('Fees Items', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_coupons" value="0" />
				<input type="checkbox" id="is_update_coupons" name="is_update_coupons" value="1" <?php echo $post['is_update_coupons'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_coupons"><?php _e('Coupon Items', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_shipping" value="0" />
				<input type="checkbox" id="is_update_shipping" name="is_update_shipping" value="1" <?php echo $post['is_update_shipping'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_shipping"><?php _e('Shipping Items', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_taxes" value="0" />
				<input type="checkbox" id="is_update_taxes" name="is_update_taxes" value="1" <?php echo $post['is_update_taxes'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_taxes"><?php _e('Tax Items', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>			
			<div class="input">
				<input type="hidden" name="is_update_refunds" value="0" />
				<input type="checkbox" id="is_update_refunds" name="is_update_refunds" value="1" <?php echo $post['is_update_refunds'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_refunds"><?php _e('Refunds', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<div class="input">
				<input type="hidden" name="is_update_total" value="0" />
				<input type="checkbox" id="is_update_total" name="is_update_total" value="1" <?php echo $post['is_update_total'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_total"><?php _e('Order Total', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<!-- Do not update order custom fields -->
			<!-- <input type="hidden" name="is_update_custom_fields" value="0" /> -->

			<div class="input">			
				<input type="hidden" name="custom_fields_list" value="0" />			
				<input type="hidden" name="is_update_custom_fields" value="0" />
				<input type="checkbox" id="is_update_custom_fields" name="is_update_custom_fields" value="1" <?php echo $post['is_update_custom_fields'] ? 'checked="checked"': '' ?>  class="switcher"/>
				<label for="is_update_custom_fields"><?php _e('Custom Fields', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<!--a href="#help" class="wpallimport-help" title="<?php _e('If Keep Custom Fields box is checked, it will keep all Custom Fields, and add any new Custom Fields specified in Custom Fields section, as long as they do not overwrite existing fields. If \'Only keep this Custom Fields\' is specified, it will only keep the specified fields.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a-->
				<div class="switcher-target-is_update_custom_fields" style="padding-left:17px;">
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_full_update" name="update_custom_fields_logic" value="full_update" <?php echo ( "full_update" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_full_update"><?php _e('Update all Custom Fields', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					</div>					
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_only" name="update_custom_fields_logic" value="only" <?php echo ( "only" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_only"><?php _e('Update only these Custom Fields, leave the rest alone', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-update_custom_fields_logic_only pmxi_choosen" style="padding-left:17px;">								
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "only" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_only_list"/>										
						</div>						
					</div>
					<div class="input">
						<input type="radio" id="update_custom_fields_logic_all_except" name="update_custom_fields_logic" value="all_except" <?php echo ( "all_except" == $post['update_custom_fields_logic'] ) ? 'checked="checked"': '' ?> class="switcher"/>
						<label for="update_custom_fields_logic_all_except"><?php _e('Leave these fields alone, update all other Custom Fields', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-update_custom_fields_logic_all_except pmxi_choosen" style="padding-left:17px;">						
							<span class="hidden choosen_values"><?php if (!empty($existing_meta_keys)) echo esc_html(implode(',', $existing_meta_keys));?></span>
							<input class="choosen_input" value="<?php if (!empty($post['custom_fields_list']) and "all_except" == $post['update_custom_fields_logic']) echo esc_html(implode(',', $post['custom_fields_list'])); ?>" type="hidden" name="custom_fields_except_list"/>																				
						</div>						
					</div>
				</div>
			</div>
			<?php

			// add-ons re-import options
			do_action('pmxi_reimport', $post_type, $post);

			?>
		</div>
	</div>
</div>	