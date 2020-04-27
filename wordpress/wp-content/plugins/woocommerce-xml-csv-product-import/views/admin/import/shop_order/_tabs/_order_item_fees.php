<div class="panel woocommerce_options_panel" id="order_fees" style="display:none;">
	<div class="options_group hide_if_grouped">				
		<!-- Fees matching mode -->
		<!-- <div class="form-field wpallimport-radio-field wpallimport-clear">
			<input type="radio" id="fees_repeater_mode_fixed" name="pmwi_order[fees_repeater_mode]" value="fixed" <?php echo 'fixed' == $post['pmwi_order']['fees_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
			<label for="fees_repeater_mode_fixed" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div> -->																				
		<table class="form-field wpallimport_variable_table" style="width:98%;" cellspacing="5">
			<?php 			
			foreach ($post['pmwi_order']['fees'] as $i => $fee): 

				$fee += array('name' => '', 'amount' => '');
				
				if (empty($fee['name'])) continue;
				
				?>
				
				<tr class="form-field">
					<td>
						<label><?php _e('Fee Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<div class="clear">
							<input type="text" class="rad4" name="pmwi_order[fees][<?php echo $i; ?>][name]" value="<?php echo esc_attr($fee['name']) ?>" style="width:100%;"/>	
						</div>
					</td>
					<td>
						<label><?php _e('Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<div class="clear">
							<input type="text" class="rad4" name="pmwi_order[fees][<?php echo $i; ?>][amount]" value="<?php echo esc_attr($fee['amount']) ?>" style="width:100%;"/>	
						</div>
					</td>
					<td class="action remove"><!--a href="#remove" style="top: 33px;"></a--></td>
				</tr>

			<?php endforeach; ?>
			<tr class="form-field template">
				<td>
					<label><?php _e('Fee Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear">
						<input type="text" class="rad4" name="pmwi_order[fees][ROWNUMBER][name]" value="" style="width:100%;"/>	
					</div>
				</td>
				<td>
					<label><?php _e('Amount', PMWI_Plugin::TEXT_DOMAIN); ?></label>
					<div class="clear">
						<input type="text" class="rad4" name="pmwi_order[fees][ROWNUMBER][amount]" value="" style="width:100%;"/>	
					</div>
				</td>
				<td class="action remove"><!--a href="#remove" style="top: 33px;"></a--></td>
			</tr>
			<tr class="wpallimport-row-actions" style="display:none;">
				<td colspan="3">
					<hr>
					<a class="add-new-line" title="Add Another Fee" href="javascript:void(0);" style="width:200px;"><?php _e("Add Another Fee", PMWI_Plugin::TEXT_DOMAIN); ?></a>
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
						<input type="radio" id="fees_repeater_mode_variable_csv" name="pmwi_order[fees_repeater_mode]" value="csv" <?php echo 'csv' == $post['pmwi_order']['fees_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="fees_repeater_mode_variable_csv" style="width:auto; float: none;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-fees_repeater_mode_variable_csv wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple fees separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4 order-separator-input" name="pmwi_order[fees_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['fees_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
								<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two fees would be imported like this 'Fee 1|Fee 2' and the fee amounts like this 10|20">?</a>							
							</span>
						</div>
					</div>						
					<div class="form-field wpallimport-radio-field wpallimport-clear">
						<input type="radio" id="fees_repeater_mode_variable_xml" name="pmwi_order[fees_repeater_mode]" value="xml" <?php echo 'xml' == $post['pmwi_order']['fees_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="fees_repeater_mode_variable_xml" style="width:auto; float: none;"><?php _e('Variable Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-fees_repeater_mode_variable_xml wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label style="width: 60px; line-height: 30px;"><?php _e('For each', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[fees_repeater_mode_foreach]" value="<?php echo esc_attr($post['pmwi_order']['fees_repeater_mode_foreach']) ?>" style="width:50%;"/>							
								<label class="foreach-do" style="padding-left: 10px; line-height: 30px;"><?php _e('do...', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							</span>
						</div>		
					</div>						
					<?php else: ?>
					<input type="hidden" name="pmwi_order[fees_repeater_mode]" value="csv"/>
					<div class="form-field input" style="margin-bottom: 20px;">
						<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple fees separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<input type="text" class="short rad4 order-separator-input" name="pmwi_order[fees_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['fees_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>	
						<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two fees would be imported like this 'Fee 1|Fee 2' and the fee amounts like this 10|20">?</a>						
					</div>
					<?php endif; ?>		
				</div>	
			</div>	
		</div>	
	</div>		
</div>