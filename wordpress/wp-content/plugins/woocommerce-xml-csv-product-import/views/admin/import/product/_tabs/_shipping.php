<div class="panel woocommerce_options_panel" id="shipping_product_data" style="display:none;">
    <?php if (class_exists('PMWI_Plugin') && PMWI_EDITION == 'free'): ?>
    <div class="woo-add-on-free-edition-notice upgrade_template">
        <a href="https://www.wpallimport.com/checkout/?edd_action=purchase_collection&taxonomy=download_category&terms=14&utm_source=import-wooco-products-addon-free&utm_medium=upgrade-notice&utm_campaign=import-variable-wooco-products" target="_blank" class="upgrade_woo_link"><?php _e('Upgrade to the Pro edition of WP All Import and the WooCommerce Add-On to Import to Variable, Affiliate, and Grouped Products', PMWI_Plugin::TEXT_DOMAIN);?></a>
        <p><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_import_plugin'); ?></p>
    </div>
    <?php endif; ?>
	<div class="options_group">
		<p class="form-field">
			<label><?php printf(__("Weight (%s)", PMWI_Plugin::TEXT_DOMAIN), get_option('woocommerce_weight_unit')); ?></label>
			<input type="text" class="short" placeholder="0.00" name="single_product_weight" style="" value="<?php echo esc_attr($post['single_product_weight']) ?>"/>
		</p>
		<p class="form-field">
			<label><?php printf(__("Dimensions (%s)", PMWI_Plugin::TEXT_DOMAIN), get_option( 'woocommerce_dimension_unit' )); ?></label>
			<input type="text" class="short" placeholder="<?php _e('Length',PMWI_Plugin::TEXT_DOMAIN);?>" name="single_product_length" style="margin-right:5px;" value="<?php echo esc_attr($post['single_product_length']) ?>"/>
			<input type="text" class="short" placeholder="<?php _e('Width',PMWI_Plugin::TEXT_DOMAIN);?>" name="single_product_width" style="margin-right:5px;" value="<?php echo esc_attr($post['single_product_width']) ?>"/>
			<input type="text" class="short" placeholder="<?php _e('Height',PMWI_Plugin::TEXT_DOMAIN);?>" name="single_product_height" style="" value="<?php echo esc_attr($post['single_product_height']) ?>"/>
		</p>
	</div> <!-- End options group -->

	<div class="options_group">
		
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="multiple_product_shipping_class_yes" class="switcher" name="is_multiple_product_shipping_class" value="yes" <?php echo 'no' != $post['is_multiple_product_shipping_class'] ? 'checked="checked"': '' ?>/>
			<label for="multiple_product_shipping_class_yes"><?php _e("Shipping Class", PMWI_Plugin::TEXT_DOMAIN); ?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-multiple_product_shipping_class_yes set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<?php					
					
						$args = array(
							'taxonomy' 			=> 'product_shipping_class',
							'hide_empty'		=> 0,
							'show_option_none' 	=> __( 'No shipping class', PMWI_Plugin::TEXT_DOMAIN ),
							'name' 				=> 'multiple_product_shipping_class',
							'id'				=> 'multiple_product_shipping_class',
							'selected'			=> ( ! empty($post['multiple_product_shipping_class']) and $post['multiple_product_shipping_class'] > 0 ) ? $post['multiple_product_shipping_class'] : '',
							'class'				=> 'select short'
						);

						wp_dropdown_categories( $args );
					?>
				</span>	
			</div>
		</div>
	
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="multiple_product_shipping_class_no" class="switcher" name="is_multiple_product_shipping_class" value="no" <?php echo 'no' == $post['is_multiple_product_shipping_class'] ? 'checked="checked"': '' ?>/>
			<label for="multiple_product_shipping_class_no" style="width: 350px;"><?php _e('Set product shipping class with XPath', PMWI_Plugin::TEXT_DOMAIN )?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-multiple_product_shipping_class_no set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_shipping_class" style="width:300px;" value="<?php echo esc_attr($post['single_product_shipping_class']) ?>"/>
					<a href="#help" class="wpallimport-help" title="<?php _e('Value should be the name, ID, or slug for the shipping class. Default slugs are \'taxable\', \'shipping\' and \'none\'.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a>
				</span>
			</div>
		</div>
					
	</div>	<!-- End options group -->
</div> <!-- End Product Panel -->