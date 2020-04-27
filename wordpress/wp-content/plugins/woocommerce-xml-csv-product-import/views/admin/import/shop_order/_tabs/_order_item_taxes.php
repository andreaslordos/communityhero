<div class="panel woocommerce_options_panel" id="order_taxes" style="display:none;">
	<div class="options_group hide_if_grouped">
		<!-- Taxes matching mode -->
		<!-- <div class="form-field wpallimport-radio-field wpallimport-clear">
			<input type="radio" id="taxes_repeater_mode_fixed" name="pmwi_order[taxes_repeater_mode]" value="fixed" <?php echo 'fixed' == $post['pmwi_order']['taxes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
			<label for="taxes_repeater_mode_fixed" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>	 -->		
		<table class="form-field wpallimport_variable_table" style="width:98%;">
			<?php 
			$tax_classes = \WC_Tax::get_tax_classes();
			$classes_options = array();
			$classes_options[''] = __( 'Standard', PMWI_Plugin::TEXT_DOMAIN );
    		if ( $tax_classes )
    		{
    			foreach ( $tax_classes as $class )
    			{
    				$classes_options[ sanitize_title( $class ) ] = esc_html( $class );										
    			}
    		}    			    			
			
			foreach ($post['pmwi_order']['taxes'] as $i => $tax): 

				$tax += array('tax_code' => '', 'tax_amount' => '', 'shipping_tax_amount' => '', 'tax_code_xpath' => '');
				
				if (empty($tax['tax_code'])) continue;

				?>
				
				<tr class="form-field">
					<td>
						<table style="width:100%;" cellspacing="5">																
							<tr>
								<td>
									<label><?php _e('Tax Rate Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<div class="clear">
										<input type="text" class="rad4" name="pmwi_order[taxes][<?php echo $i;?>][tax_amount]" value="<?php echo esc_attr($tax['tax_amount']) ?>" style="width:100%;"/>	
									</div>
								</td>
								<td>
									<label><?php _e('Shipping Tax Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<div class="clear">
										<input type="text" class="rad4" name="pmwi_order[taxes][<?php echo $i;?>][shipping_tax_amount]" value="<?php echo esc_attr($tax['shipping_tax_amount']) ?>" style="width:100%;"/>	
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">									
									<label><?php _e('Tax Rate', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<span class="wpallimport-clear"></span>
									<select name="pmwi_order[taxes][<?php echo $i;?>][tax_code]" id="order_tax_code_<?php echo $i;?>" class="rad4 switcher" style="font-size: 14px !important;">																		
										<?php 
										$taxes_for_tooltip = array();
										foreach ($classes_options as $key => $value):?>
											<optgroup label="<?php echo $value; ?>">
												<?php foreach ( WC_Tax::get_rates_for_tax_class($key) as $rate_key => $rate): $taxes_for_tooltip[] = $rate->tax_rate_id . " - " . $rate->tax_rate_name;?>
												<option value="<?php echo $rate->tax_rate_id;?>" <?php if ($tax['tax_code'] == $rate->tax_rate_id) echo 'selected="selected"';?>><?php echo $rate->tax_rate_id . " - " . $rate->tax_rate_name;?></option>
												<?php endforeach; ?>																		
											</optgroup>
										<?php endforeach; ?>																													
										<option value="xpath" <?php if ("xpath" == $tax['tax_code']) echo 'selected="selected"';?>><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
									</select>
									<span class="wpallimport-clear"></span>
									<div class="switcher-target-order_tax_code_<?php echo $i; ?>" style="margin-top:10px;">
										<span class="wpallimport-slide-content" style="padding-left:0;">
											<input type="text" class="short rad4" name="pmwi_order[taxes][<?php echo $i;?>][tax_code_xpath]" value="<?php echo esc_attr($tax['tax_code_xpath']) ?>"/>
											<a href="#help" class="wpallimport-help" title="<?php printf(__('Tax rate method can be matched by ID: %s. If tax method is not found then no tax information will be imported.', PMWI_Plugin::TEXT_DOMAIN), implode(", ", $taxes_for_tooltip)); ?>" style="position:relative; top:10px;">?</a>
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
								<label><?php _e('Tax Rate Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[taxes][ROWNUMBER][tax_amount]" value="" style="width:100%;"/>	
								</div>
							</td>
							<td>
								<label><?php _e('Shipping Tax Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear">
									<input type="text" class="rad4" name="pmwi_order[taxes][ROWNUMBER][shipping_tax_amount]" value="" style="width:100%;"/>	
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label><?php _e('Tax Rate', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<span class="wpallimport-clear"></span>								
								<select name="pmwi_order[taxes][ROWNUMBER][tax_code]" id="order_tax_code_ROWNUMBER" class="rad4 switcher" style="font-size: 14px !important;">																		
									<option value=""><?php _e("Select",PMWI_Plugin::TEXT_DOMAIN);?></option>
									<?php foreach ($classes_options as $key => $value):?>										
										<optgroup label="<?php echo $value; ?>">
											<?php foreach ( WC_Tax::get_rates_for_tax_class($key) as $rate_key => $rate): ?>
											<option value="<?php echo $rate->tax_rate_id;?>"><?php echo $rate->tax_rate_id . " - " . $rate->tax_rate_name;?></option>
											<?php endforeach; ?>																		
										</optgroup>
									<?php endforeach; ?>																													
									<option value="xpath"><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
								</select>
								<span class="wpallimport-clear"></span>
								<div class="switcher-target-order_tax_code_ROWNUMBER" style="margin-top:10px; display: none;">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<input type="text" class="short rad4" name="pmwi_order[taxes][ROWNUMBER][tax_code_xpath]" value=""/>
										<a href="#help" class="wpallimport-help" title="<?php _e('Tax rate method can be matched by ID. If tax method is not found then no tax information will be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
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
						<input type="radio" id="taxes_repeater_mode_variable_csv" name="pmwi_order[taxes_repeater_mode]" value="csv" <?php echo 'csv' == $post['pmwi_order']['taxes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="taxes_repeater_mode_variable_csv" style="width:auto; float: none;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-taxes_repeater_mode_variable_csv wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple taxes separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4 order-separator-input" name="pmwi_order[taxes_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['taxes_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
								<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two tax rate amounts would be imported like this '10|20'">?</a>						
							</span>
						</div>
					</div>						
					<div class="form-field wpallimport-radio-field wpallimport-clear">
						<input type="radio" id="taxes_repeater_mode_variable_xml" name="pmwi_order[taxes_repeater_mode]" value="xml" <?php echo 'xml' == $post['pmwi_order']['taxes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="taxes_repeater_mode_variable_xml" style="width:auto; float: none;"><?php _e('Variable Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-taxes_repeater_mode_variable_xml wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label style="width: 60px; line-height: 30px;"><?php _e('For each', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[taxes_repeater_mode_foreach]" value="<?php echo esc_attr($post['pmwi_order']['taxes_repeater_mode_foreach']) ?>" style="width:50%;"/>							
								<label class="foreach-do" style="padding-left: 10px; line-height: 30px;"><?php _e('do...', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							</span>
						</div>		
					</div>			
					<?php else: ?>			
					<input type="hidden" name="pmwi_order[taxes_repeater_mode]" value="csv"/>
					<div class="form-field input" style="margin-bottom: 20px;">
						<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple taxes separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<input type="text" class="short rad4 order-separator-input" name="pmwi_order[taxes_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['taxes_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
						<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two tax rate amounts would be imported like this '10|20'">?</a>								
					</div>
					<?php endif; ?>	
				</div>	
			</div>	
		</div>	
	</div>		
</div>