<div class="panel woocommerce_options_panel" id="payment_order_data" style="display:none;">
	<div class="options_group hide_if_grouped">		
		<table class="form-table" style="max-width:none;">
			<tr>
				<td>																				
					<!-- Payment Method -->
					<div class="form-field">
						<p class="form-field"><?php _e('Payment Method', PMWI_Plugin::TEXT_DOMAIN) ?></p>
						<div class="form-field">
							<select id="payment_method" name="pmwi_order[payment_method]" style="width: 200px; font-size: 14px !important;" class="switcher rad4">
								<?php
								$payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();
								$payment_gateways_for_tooltip = array();
								foreach ( $payment_gateways as $id => $gateway ) {
									echo '<option value="' . esc_attr( $id ) . '" ' . selected( $id, $post['pmwi_order']['payment_method'], false ) . '>' . esc_html( $gateway->title ) . '</option>';
									$payment_gateways_for_tooltip[] = esc_attr( $id );
								}
								?>
								<option value="xpath" <?php if ("xpath" == $post['pmwi_order']['payment_method']) echo 'selected="selected"';?>><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
							</select>				
							<span class="wpallimport-clear"></span>
							<div class="switcher-target-payment_method" style="margin-top:10px;">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<input type="text" class="short rad4" name="pmwi_order[payment_method_xpath]" value="<?php echo esc_attr($post['pmwi_order']['payment_method_xpath']) ?>"/>
									<a href="#help" class="wpallimport-help" title="<?php printf(__('Payment method can be matched by title or slug: %s. If payment method is not found \'N/A\' will be applied to order.', PMWI_Plugin::TEXT_DOMAIN), implode(", ", $payment_gateways_for_tooltip)); ?>" style="position:relative; top:10px;">?</a>
								</span>
							</div>
						</div>												
					</div>	
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-field">
						<div class="form-field">
							<label><?php _e('Transaction ID', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							<div class="clear">
								<input type="text" class="rad4" name="pmwi_order[transaction_id]" value="<?php echo esc_attr($post['pmwi_order']['transaction_id']) ?>"/>
							</div>
						</div>																										
					</div>
				</td>
			</tr>
		</table>																								
	</div>
</div>	