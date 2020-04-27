<div class="panel woocommerce_options_panel" id="shipping_order_data" style="display:none;">
	<div class="options_group hide_if_grouped">																						
		<div class="input">
			<div class="form-field wpallimport-radio-field">
				<input type="radio" id="shipping_source_copy" name="pmwi_order[shipping_source]" value="copy" <?php echo 'copy' == $post['pmwi_order']['shipping_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
				<label for="shipping_source_copy" style="width:auto;"><?php _e('Copy from billing', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>																		
		</div>
		<div class="clear"></div>
		<div style="margin-top:0; padding-left:8px;">
			<div class="form-field wpallimport-radio-field">
				<input type="radio" id="shipping_source_guest" name="pmwi_order[shipping_source]" value="guest" <?php echo 'guest' == $post['pmwi_order']['shipping_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
				<label for="shipping_source_guest" style="width:auto;"><?php _e('Import shipping address', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-shipping_source_guest" style="padding-left:45px;">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<div class="form-field wpallimport-radio-field" style="padding-left: 3px;">
						<input type="hidden" name="pmwi_order[copy_from_billing]" value="0"/>
						<input type="checkbox" id="shipping_copy_from_billing" name="pmwi_order[copy_from_billing]" value="1" <?php echo $post['pmwi_order']['copy_from_billing'] ? 'checked="checked"' : '' ?> class="switcher"/>
						<label for="shipping_copy_from_billing" style="width:auto;"><?php _e('If order has no shipping info, copy from billing', PMWI_Plugin::TEXT_DOMAIN) ?></label>
					</div>
					<table cellspacing="5" class="wpallimport-order-billing-fields">
						<tr>
							<td>																		
								<label><?php _e('First Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_first_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_first_name']) ?>"/>									
								</div>
							</td>
							<td>																		
								<label><?php _e('Last Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_last_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_last_name']) ?>"/>									
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">																		
								<label><?php _e('Company', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_company]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_company']) ?>"/>
								</div>																		
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Address 1', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_address_1]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_address_1']) ?>"/>					
								</div>
							</td>
							<td>																		
								<label><?php _e('Address 2', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_address_2]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_address_2']) ?>"/>					
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('City', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_city]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_city']) ?>"/>
								</div>
							</td>
							<td>																		
								<label><?php _e('Postcode', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_postcode]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_postcode']) ?>"/>					
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_country]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_country']) ?>"/>
								</div>
							</td>
							<td>																		
								<label><?php _e('State/Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_state]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_state']) ?>"/>							
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_email]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_email']) ?>"/>							
								</div>
							</td>
							<td>																		
								<label><?php _e('Phone', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping_phone]" style="" value="<?php echo esc_attr($post['pmwi_order']['shipping_phone']) ?>"/>							
								</div>
							</td>
						</tr>
					</table>
				</span>
			</div>
		</div>	
		<div class="clear"></div>
		<div class="input">
			<div class="form-field">
				<label><?php _e('Customer Provided Note', PMWI_Plugin::TEXT_DOMAIN); ?></label>
				<textarea name="pmwi_order[customer_provided_note]" class="rad4" style="width:97%;"><?php echo esc_attr($post['pmwi_order']['customer_provided_note']) ?></textarea>
			</div>																												
		</div>
	</div>
</div>