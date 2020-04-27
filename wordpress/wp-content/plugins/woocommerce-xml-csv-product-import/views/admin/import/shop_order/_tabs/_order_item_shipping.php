<div class="panel woocommerce_options_panel" id="order_shipping" style="display:none;">
	<div class="options_group hide_if_grouped">			
		<!-- Shipping matching mode -->
		<!-- <div class="form-field wpallimport-radio-field wpallimport-clear">
			<input type="radio" id="shipping_repeater_mode_fixed" name="pmwi_order[shipping_repeater_mode]" value="fixed" <?php echo 'fixed' == $post['pmwi_order']['shipping_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
			<label for="shipping_repeater_mode_fixed" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>	 -->																
		<table class="form-field wpallimport_variable_table" style="width:98%;">
			<?php 						
			foreach ($post['pmwi_order']['shipping'] as $i => $shipping): 

				$shipping += array('name' => '', 'amount' => '', 'class' => '', 'class_xpath' => '');
				
				if (empty($shipping['name'])) continue;

				?>
				
				<tr class="form-field">
					<td>
						<table style="width:100%;" cellspacing="5">																
							<tr>
								<td>
									<label><?php _e('Shipping Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<div class="clear">
										<input type="text" class="rad4" name="pmwi_order[shipping][<?php echo $i;?>][name]" value="<?php echo esc_attr($shipping['name']) ?>" style="width:100%;"/>	
									</div>
								</td>
								<td>
									<label><?php _e('Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<div class="clear">
										<input type="text" class="rad4" name="pmwi_order[shipping][<?php echo $i;?>][amount]" value="<?php echo esc_attr($shipping['amount']) ?>" style="width:100%;"/>	
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">									
									<label><?php _e('Shipping Method', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<span class="wpallimport-clear"></span>
									<select name="pmwi_order[shipping][<?php echo $i;?>][class]" id="order_shipping_class_<?php echo $i; ?>" class="rad4 switcher" style="font-size: 14px !important;">
										<?php 
										$shipping_for_tooltip = array();
										foreach ( WC()->shipping->get_shipping_methods() as $method_key => $method ) {											
										    echo '<option value="'. $method_key .'" '. ( ($shipping['class'] == $method_key) ? 'selected="selected"' : '' ) .'>' . $method->method_title . '</option>';
										    $shipping_for_tooltip[] = $method_key;
										}
										?>
										<option value="xpath" <?php if ("xpath" == $shipping['class']) echo 'selected="selected"';?>><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
									</select>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_shipping_class_<?php echo $i; ?>" style="margin-top:10px;">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<input type="text" class="short rad4" name="pmwi_order[shipping][<?php echo $i;?>][class_xpath]" value="<?php echo esc_attr($shipping['class_xpath']) ?>"/>
											<a href="#help" class="wpallimport-help" title="<?php printf(__('Shipping method can be matched by Name or ID: %s. If shipping method is not found then no shipping information will be imported.', PMWI_Plugin::TEXT_DOMAIN), implode(", ", $shipping_for_tooltip)); ?>" style="position:relative; top:10px;">?</a>
										</span>
									</div>
								</td>
							</tr>
						</table>
					</td>	
					<td class="action remove"><!--a href="#remove" style="top: 39px;"></a--></td>
				</tr>

			<?php endforeach; ?>

			<tr class="form-field template">
				<td>
					<table style="width:100%;" cellspacing="5">																
						<tr>
							<td>
								<label><?php _e('Shipping Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping][ROWNUMBER][name]" value="" style="width:100%;"/>	
								</div>
							</td>
							<td>
								<label><?php _e('Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[shipping][ROWNUMBER][amount]" value="" style="width:100%;"/>	
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><?php _e('Shipping Method', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<span class="wpallimport-clear"></span>
								<select name="pmwi_order[shipping][ROWNUMBER][class]" id="order_shipping_class_ROWNUMBER" class="rad4 switcher" style="font-size: 14px !important;">
									<?php 
									foreach ( WC()->shipping->get_shipping_methods() as $method_key => $method ) {
										$methodTitle = empty($method->method_title) ? $method->title : $method->method_title;
									    echo '<option value="'. $method_key .'">' . $methodTitle . '</option>';
									}									
									?>
									<option value="xpath"><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
								</select>
								<span class="wpallimport-clear"></span>
								<div class="switcher-target-order_shipping_class_ROWNUMBER" style="margin-top:10px; display: none;">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<input type="text" class="short rad4" name="pmwi_order[shipping][ROWNUMBER][class_xpath]" value=""/>
										<a href="#help" class="wpallimport-help" title="<?php _e('Shipping method can be matched by Name or ID. If shipping method is not found then no shipping information will be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
									</span>
								</div>
							</td>
						</tr>
					</table>
				</td>	
				<td class="action remove"><!--a href="#remove" style="top: 39px;"></a--></td>
			</tr>
			<tr class="wpallimport-row-actions" style="display:none;">
				<td colspan="2">
					<hr>
					<a class="add-new-line" title="Add More" href="javascript:void(0);" style="width:200px;"><?php _e("Add More", PMWI_Plugin::TEXT_DOMAIN); ?></a>
				</td>
			</tr>
		</table>
	</div>	
	<div class="wpallimport-collapsed closed wpallimport-section order-imports">
		<div style="margin:0; background: #FAFAFA;" class="wpallimport-content-section rad4 order-imports">
			<div class="wpallimport-collapsed-header">
				<h3 style="color:#40acad; font-size: 14px;"><?php _e("Advanced Options",PMWI_Plugin::TEXT_DOMAIN); ?></h3>
			</div>
			<div style="padding: 0px;" class="wpallimport-collapsed-content">										
				<div class="wpallimport-collapsed-content-inner">
					<?php if ( empty(PMXI_Plugin::$session->options['delimiter']) ): ?>
					<div class="form-field wpallimport-radio-field wpallimport-clear">
						<input type="radio" id="shipping_repeater_mode_variable_csv" name="pmwi_order[shipping_repeater_mode]" value="csv" <?php echo 'csv' == $post['pmwi_order']['shipping_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="shipping_repeater_mode_variable_csv" style="width:auto; float: none;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-shipping_repeater_mode_variable_csv wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple shipping costs separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4 order-separator-input" name="pmwi_order[shipping_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['shipping_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>	
								<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two shipping names would be imported like this 'Shipping 1|Shipping 2' and the shipping amounts like this 10|20">?</a>						
							</span>
						</div>
					</div>						
					<div class="form-field wpallimport-radio-field wpallimport-clear">
						<input type="radio" id="shipping_repeater_mode_variable_xml" name="pmwi_order[shipping_repeater_mode]" value="xml" <?php echo 'xml' == $post['pmwi_order']['shipping_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="shipping_repeater_mode_variable_xml" style="width:auto; float: none;"><?php _e('Variable Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-shipping_repeater_mode_variable_xml wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label style="width: 60px; line-height: 30px;"><?php _e('For each', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[shipping_repeater_mode_foreach]" value="<?php echo esc_attr($post['pmwi_order']['shipping_repeater_mode_foreach']) ?>" style="width:50%;"/>							
								<label class="foreach-do" style="padding-left: 10px; line-height: 30px;"><?php _e('do...', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							</span>
						</div>		
					</div>			
					<?php else: ?>
					<input type="hidden" name="pmwi_order[shipping_repeater_mode]" value="csv"/>
					<div class="form-field input" style="margin-bottom: 20px;">
						<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple shipping costs separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<input type="text" class="short rad4 order-separator-input" name="pmwi_order[shipping_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['shipping_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
						<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two shipping names would be imported like this 'Shipping 1|Shipping 2' and the shipping amounts like this 10|20">?</a>						
					</div>
					<?php endif; ?>						
				</div>	
			</div>	
		</div>	
	</div>		
</div>
