<div class="panel woocommerce_options_panel" id="order_refunds" style="display:none;">
	<div class="options_group hide_if_grouped">																						
		<table class="form-field" style="width:98%;" cellspacing="5">															
			<tr>
				<td>
					<label><?php _e('Refund Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear">
						<input type="text" class="rad4" name="pmwi_order[order_refund_amount]" value="<?php echo esc_attr($post['pmwi_order']['order_refund_amount']) ?>" style="width:100%;"/>	
					</div>
				</td>
				<td>
					<label><?php _e('Reason', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear">
						<input type="text" class="rad4" name="pmwi_order[order_refund_reason]" value="<?php echo esc_attr($post['pmwi_order']['order_refund_reason']) ?>" style="width:100%;"/>	
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label><?php _e('Date', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear">
						<input type="text" class="datepicker rad4" name="pmwi_order[order_refund_date]" value="<?php echo esc_attr($post['pmwi_order']['order_refund_date']) ?>"/>																
					</div>
					<span class="wpallimport-clear"></span>

					<label style="padding-top:10px;"><?php _e('Refund Issued By', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear" style="margin-top:0;">
						<div class="wpallimport-radio-field">
							<input type="radio" id="order_refund_issued_source_existing" name="pmwi_order[order_refund_issued_source]" value="existing" <?php echo 'existing' == $post['pmwi_order']['order_refund_issued_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
							<label for="order_refund_issued_source_existing" style="width:auto;"><?php _e('Load details from existing user', PMWI_Plugin::TEXT_DOMAIN) ?></label>
							<a href="#help" class="wpallimport-help" title="<?php _e('If no user is matched, refund issuer will be left blank.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top: 3px;">?</a>
						</div>
						<span class="wpallimport-clear"></span>
						<div class="switcher-target-order_refund_issued_source_existing" style="padding-left:7px;">				
							<span class="wpallimport-slide-content" style="padding-left:0;">
								<p class="form-field"><?php _e('Match user by:', PMWI_Plugin::TEXT_DOMAIN); ?></p>
								<!-- Match user by Username -->
								<div class="form-field wpallimport-radio-field">
									<input type="radio" id="order_refund_issued_match_by_username" name="pmwi_order[order_refund_issued_match_by]" value="username" <?php echo 'username' == $post['pmwi_order']['order_refund_issued_match_by'] ? 'checked="checked"' : '' ?> class="switcher"/>
									<label for="order_refund_issued_match_by_username"><?php _e('Username', PMWI_Plugin::TEXT_DOMAIN) ?></label>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_refund_issued_match_by_username set_with_xpath">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<input type="text" class="short rad4" name="pmwi_order[order_refund_issued_username]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_refund_issued_username']) ?>"/>			
										</span>
									</div>
								</div>
								<div class="clear"></div>
								<!-- Match user by Email -->
								<div class="form-field wpallimport-radio-field">
									<input type="radio" id="order_refund_issued_match_by_email" name="pmwi_order[order_refund_issued_match_by]" value="email" <?php echo 'email' == $post['pmwi_order']['order_refund_issued_match_by'] ? 'checked="checked"' : '' ?> class="switcher"/>
									<label for="order_refund_issued_match_by_email"><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN) ?></label>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_refund_issued_match_by_email set_with_xpath">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<input type="text" class="short rad4" name="pmwi_order[order_refund_issued_email]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_refund_issued_email']) ?>"/>			
										</span>
									</div>
								</div>
								<div class="clear"></div>
								<!-- Match user by Custom Field -->
								<div class="form-field wpallimport-radio-field">
									<input type="radio" id="order_refund_issued_by_cf" name="pmwi_order[order_refund_issued_match_by]" value="cf" <?php echo 'cf' == $post['pmwi_order']['order_refund_issued_match_by'] ? 'checked="checked"' : '' ?> class="switcher"/>
									<label for="order_refund_issued_by_cf"><?php _e('Custom Field', PMWI_Plugin::TEXT_DOMAIN) ?></label>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_refund_issued_by_cf set_with_xpath">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<p>
												<label style="line-height: 30px;"><?php _e('Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<input type="text" class="short rad4" name="pmwi_order[order_refund_issued_cf_name]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_refund_issued_cf_name']) ?>"/>			
											</p>
											<p>
												<label style="line-height: 30px;"><?php _e('Value', PMWI_Plugin::TEXT_DOMAIN); ?></label>
												<input type="text" class="short rad4" name="pmwi_order[order_refund_issued_cf_value]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_refund_issued_cf_value']) ?>"/>			
											</p>
										</span>
									</div>
								</div>
								<div class="clear"></div>
								<!-- Match user by user ID -->
								<div class="form-field wpallimport-radio-field">
									<input type="radio" id="order_refund_issued_by_id" name="pmwi_order[order_refund_issued_match_by]" value="id" <?php echo 'id' == $post['pmwi_order']['order_refund_issued_match_by'] ? 'checked="checked"' : '' ?> class="switcher"/>
									<label for="order_refund_issued_by_id"><?php _e('User ID', PMWI_Plugin::TEXT_DOMAIN) ?></label>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_refund_issued_by_id set_with_xpath">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<input type="text" class="short rad4" name="pmwi_order[order_refund_issued_id]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_refund_issued_id']) ?>"/>
										</span>
									</div>
								</div>
							</span>																	
						</div>
					</div>
					<div class="clear"></div>
					<div style="margin-top:0;">
						<div class="wpallimport-radio-field">
							<input type="radio" id="order_refund_issued_source_blank" name="pmwi_order[order_refund_issued_source]" value="blank" <?php echo 'blank' == $post['pmwi_order']['order_refund_issued_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
							<label for="order_refund_issued_source_blank" style="width:auto;"><?php _e('Leave refund issuer blank', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						</div>
					</div>
					
				</td>
			</tr>
		</table>														
	</div>
</div>