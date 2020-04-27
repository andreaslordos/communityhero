<div class="panel woocommerce_options_panel" id="order_total" style="display:none;">
	<div class="options_group hide_if_grouped" style="margin-top:10px;">															
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="order_total_logic_auto" name="pmwi_order[order_total_logic]" value="auto" <?php echo 'auto' == $post['pmwi_order']['order_total_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
			<label for="order_total_logic_auto" style="width:auto;"><?php _e('Calculate order total automatically', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>	
		<span class="wpallimport-clear"></span>
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="order_total_logic_manually" name="pmwi_order[order_total_logic]" value="manually" <?php echo 'manually' == $post['pmwi_order']['order_total_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
			<label for="order_total_logic_manually" style="width:auto;"><?php _e('Set order total manually', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div>	
		<span class="wpallimport-clear"></span>
		<div class="switcher-target-order_total_logic_manually" style="padding-left:45px;">											
			<span class="wpallimport-slide-content" style="padding-left:0;">
				<input type="text" class="short rad4" name="pmwi_order[order_total_xpath]" style="" value="<?php echo esc_attr($post['pmwi_order']['order_total_xpath']) ?>"/>	
			</span>
		</div>												
	</div>
</div>