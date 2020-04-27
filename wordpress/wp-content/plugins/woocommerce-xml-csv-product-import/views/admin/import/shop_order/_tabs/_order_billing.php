<div class="panel woocommerce_options_panel" id="billing_order_data">
	<div class="options_group hide_if_grouped">																						
		<div class="input">
			<div class="form-field wpallimport-radio-field">
				<input type="radio" id="billing_source_existing" name="pmwi_order[billing_source]" value="existing" <?php echo 'existing' == $post['pmwi_order']['billing_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
				<label for="billing_source_existing" style="width:auto;"><?php _e('Try to load data from existing customer', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<a href="#help" class="wpallimport-help" title="<?php _e('If no customer is found the order will be skipped.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top: 3px;">?</a>
			</div>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-billing_source_existing" style="padding-left:27px;">															
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<div class="wpallimport-billing-user-try-match">
						<div class="wpallimport-billing-user-try-match-selector">
							<label for=""><?php _e('Match by:', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							<select name="pmwi_order[billing_source_match_by]" id="billing_source_match_by" class="rad4">
								<option value="username" <?php echo 'username' == $post['pmwi_order']['billing_source_match_by'] ? 'selected="selected"' : '' ?>><?php _e('Username', PMWI_Plugin::TEXT_DOMAIN); ?></option>
								<option value="email" <?php echo 'email' == $post['pmwi_order']['billing_source_match_by'] ? 'selected="selected"' : '' ?>><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></option>
								<option value="cf" <?php echo 'cf' == $post['pmwi_order']['billing_source_match_by'] ? 'selected="selected"' : '' ?>><?php _e('Custom Field', PMWI_Plugin::TEXT_DOMAIN); ?></option>
								<option value="id" <?php echo 'id' == $post['pmwi_order']['billing_source_match_by'] ? 'selected="selected"' : '' ?>><?php _e('User ID', PMWI_Plugin::TEXT_DOMAIN); ?></option>
							</select>
						</div>
						<div class="clear"></div>
						<!-- Match user by Username -->
						<div class="form-field wpallimport-radio-field wpallimport-select-switcher-target" rel="username">
							<span class="wpallimport-clear"></span>
							<div class="switcher-target-billing_source_match_by_username set_with_xpath">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<input type="text" class="short rad4" name="pmwi_order[billing_source_username]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_source_username']) ?>" placeholder="<?php _e('Username', PMWI_Plugin::TEXT_DOMAIN); ?>"/>
								</span>
							</div>
						</div>
						<div class="clear"></div>
						<!-- Match user by Email -->
						<div class="form-field wpallimport-radio-field wpallimport-select-switcher-target" rel="email">
							<span class="wpallimport-clear"></span>
							<div class="switcher-target-billing_source_match_by_email set_with_xpath">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<input type="text" class="short rad4" name="pmwi_order[billing_source_email]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_source_email']) ?>" placeholder="<?php _e('Email Address', PMWI_Plugin::TEXT_DOMAIN); ?>"/>
								</span>
							</div>
						</div>
						<div class="clear"></div>
						<!-- Match user by Custom Field -->
						<div class="form-field wpallimport-radio-field wpallimport-select-switcher-target" rel="cf">
							<span class="wpallimport-clear"></span>
							<div class="switcher-target-billing_source_match_by_cf set_with_xpath">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<p style="padding: 0;">
										<input type="text" class="short rad4" name="pmwi_order[billing_source_cf_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_source_cf_name']) ?>" placeholder="<?php _e('Field Name', PMWI_Plugin::TEXT_DOMAIN); ?>"/>
									</p>
									<p style="padding: 0;">
										<input type="text" class="short rad4" name="pmwi_order[billing_source_cf_value]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_source_cf_value']) ?>" placeholder="<?php _e('Field Value', PMWI_Plugin::TEXT_DOMAIN); ?>"/>
									</p>
								</span>
							</div>
						</div>
						<div class="clear"></div>
						<!-- Match user by user ID -->
						<div class="form-field wpallimport-radio-field wpallimport-select-switcher-target" rel="id">
							<span class="wpallimport-clear"></span>
							<div class="switcher-target-billing_source_match_by_id set_with_xpath">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<input type="text" class="short rad4" name="pmwi_order[billing_source_id]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_source_id']) ?>" placeholder="<?php _e('User ID', PMWI_Plugin::TEXT_DOMAIN); ?>"/>
								</span>
							</div>
						</div>
						<div class="form-field wpallimport-radio-field">
							<input type="hidden" name="pmwi_order[is_guest_matching]" value="0"/>
							<input type="checkbox" id="billing_is_guest_matching" name="pmwi_order[is_guest_matching]" value="1" <?php echo $post['pmwi_order']['is_guest_matching'] ? 'checked="checked"' : '' ?> class="switcher"/>
							<label for="billing_is_guest_matching" style="width:auto;"><?php _e('If no match found, import as guest customer', PMWI_Plugin::TEXT_DOMAIN) ?></label>
							<span class="wpallimport-clear"></span>
								<div class="switcher-target-billing_is_guest_matching">
									<table cellspacing="5" class="wpallimport-order-billing-fields">
										<tr>
											<td>
												<label><?php _e('First Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_first_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_first_name']) ?>"/>
												</div>
											</td>
											<td>
												<label><?php _e('Last Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_last_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_last_name']) ?>"/>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<label><?php _e('Company', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_company]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_company']) ?>"/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label><?php _e('Address 1', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_address_1]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_address_1']) ?>"/>
												</div>
											</td>
											<td>
												<label><?php _e('Address 2', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_address_2]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_address_2']) ?>"/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label><?php _e('City', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_city]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_city']) ?>"/>
												</div>
											</td>
											<td>
												<label><?php _e('Postcode', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_postcode]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_postcode']) ?>"/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label><?php _e('Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_country]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_country']) ?>"/>
												</div>
											</td>
											<td>
												<label><?php _e('State/Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_state]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_state']) ?>"/>
												</div>
											</td>
										</tr>
										<tr>
											<td>
												<label><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_email]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_email']) ?>"/>
												</div>
											</td>
											<td>
												<label><?php _e('Phone', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<div class="clear">
													<input type="text" class="rad4" name="pmwi_order[guest_billing_phone]" style="" value="<?php echo esc_attr($post['pmwi_order']['guest_billing_phone']) ?>"/>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</span>
							<p class="is_guest_matching_notice" style="padding: 5px 0 10px;"><i><?php _e('Orders without a match will be skipped', PMWI_Plugin::TEXT_DOMAIN); ?></i></p>
						</div>
					</div>
				</span>																	
			</div>
		</div>
		<div class="clear"></div>
		<div style="margin-top:0; padding-left:8px;">
			<div class="form-field wpallimport-radio-field">
				<input type="radio" id="billing_source_guest" name="pmwi_order[billing_source]" value="guest" <?php echo 'guest' == $post['pmwi_order']['billing_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
				<label for="billing_source_guest" style="width:auto;"><?php _e('Import as guest customer', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			</div>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-billing_source_guest" style="padding-left:45px;">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<table cellspacing="5" class="wpallimport-order-billing-fields">
						<tr>
							<td>																		
								<label><?php _e('First Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_first_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_first_name']) ?>"/>									
								</div>
							</td>
							<td>																		
								<label><?php _e('Last Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_last_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_last_name']) ?>"/>									
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">																		
								<label><?php _e('Company', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_company]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_company']) ?>"/>
								</div>																		
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Address 1', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_address_1]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_address_1']) ?>"/>					
								</div>
							</td>
							<td>																		
								<label><?php _e('Address 2', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_address_2]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_address_2']) ?>"/>					
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('City', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_city]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_city']) ?>"/>
								</div>
							</td>
							<td>																		
								<label><?php _e('Postcode', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_postcode]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_postcode']) ?>"/>					
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_country]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_country']) ?>"/>
								</div>
							</td>
							<td>																		
								<label><?php _e('State/Country', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_state]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_state']) ?>"/>							
								</div>
							</td>
						</tr>
						<tr>
							<td>																		
								<label><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_email]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_email']) ?>"/>							
								</div>
							</td>
							<td>																		
								<label><?php _e('Phone', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[billing_phone]" style="" value="<?php echo esc_attr($post['pmwi_order']['billing_phone']) ?>"/>							
								</div>
							</td>
						</tr>
					</table>
				</span>
			</div>
		</div>																														
		<div class="clear"></div>
	</div>
</div>