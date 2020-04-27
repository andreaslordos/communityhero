<div class="panel woocommerce_options_panel" id="inventory_product_data" style="display:none;">
    <?php if (class_exists('PMWI_Plugin') && PMWI_EDITION == 'free'): ?>
    <div class="woo-add-on-free-edition-notice upgrade_template">
        <a href="https://www.wpallimport.com/checkout/?edd_action=purchase_collection&taxonomy=download_category&terms=14&utm_source=import-wooco-products-addon-free&utm_medium=upgrade-notice&utm_campaign=import-variable-wooco-products" target="_blank" class="upgrade_woo_link"><?php _e('Upgrade to the Pro edition of WP All Import and the WooCommerce Add-On to Import to Variable, Affiliate, and Grouped Products', PMWI_Plugin::TEXT_DOMAIN);?></a>
        <p><?php _e('If you already own it, remove the free edition and install the Pro edition.', 'wp_all_import_plugin'); ?></p>
    </div>
    <?php endif; ?>
	<div class="options_group show_if_simple show_if_variable">
		<p class="form-field"><?php _e("Manage stock?", PMWI_Plugin::TEXT_DOMAIN); ?></p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="is_product_manage_stock_yes" class="switcher" name="is_product_manage_stock" value="yes" <?php echo 'yes' == $post['is_product_manage_stock'] ? 'checked="checked"': '' ?>/>
			<label for="is_product_manage_stock_yes"><?php _e("Yes"); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="is_product_manage_stock_no" class="switcher" name="is_product_manage_stock" value="no" <?php echo 'no' == $post['is_product_manage_stock'] ? 'checked="checked"': '' ?>/>
			<label for="is_product_manage_stock_no"><?php _e("No"); ?></label>
		</p>
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="is_product_manage_stock_xpath" class="switcher" name="is_product_manage_stock" value="xpath" <?php echo 'xpath' == $post['is_product_manage_stock'] ? 'checked="checked"': '' ?>/>
			<label for="is_product_manage_stock_xpath"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN )?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-is_product_manage_stock_xpath set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_manage_stock" style="width:300px;" value="<?php echo esc_attr($post['single_product_manage_stock']) ?>"/>
					<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'yes\', \'no\').', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:2px;">?</a>
				</span>
			</div>
		</div>
		
	</div>
	<div class="options_group stock_fields show_if_simple show_if_variable show_if_subscription">
		<p class="form-field" style="margin-top:0;">
			<label><?php _e("Stock Qty", PMWI_Plugin::TEXT_DOMAIN); ?></label>
			<input type="text" class="short" name="single_product_stock_qty" value="<?php echo esc_attr($post['single_product_stock_qty']) ?>"/>			
		</p>
        <p class="form-field" style="margin-top:0;">
            <label><?php _e("Low stock threshold", PMWI_Plugin::TEXT_DOMAIN); ?></label>
            <input type="text" class="short" name="single_product_low_stock_amount" value="<?php echo esc_attr($post['single_product_low_stock_amount']) ?>"/>
        </p>
	</div>
	<div class="options_group">

		<p class="form-field"><?php _e('Stock status',PMWI_Plugin::TEXT_DOMAIN);?></p>
		
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_stock_status_in_stock" class="switcher" name="product_stock_status" value="instock" <?php echo 'instock' == $post['product_stock_status'] ? 'checked="checked"': '' ?>/>
			<label for="product_stock_status_in_stock"><?php _e("In stock", PMWI_Plugin::TEXT_DOMAIN); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_stock_status_out_of_stock" class="switcher" name="product_stock_status" value="outofstock" <?php echo 'outofstock' == $post['product_stock_status'] ? 'checked="checked"': '' ?>/>
			<label for="product_stock_status_out_of_stock"><?php _e("Out of stock", PMWI_Plugin::TEXT_DOMAIN); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_stock_status_automatically" class="switcher" name="product_stock_status" value="auto" <?php echo 'auto' == $post['product_stock_status'] ? 'checked="checked"': '' ?>/>
			<label for="product_stock_status_automatically" style="width:105px;"><?php _e("Set automatically", PMWI_Plugin::TEXT_DOMAIN); ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('Set the stock status to In Stock for positive or blank Stock Qty values, and Out Of Stock if Stock Qty is 0.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:2px;">?</a>
		</p>
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="product_stock_status_xpath" class="switcher" name="product_stock_status" value="xpath" <?php echo 'xpath' == $post['product_stock_status'] ? 'checked="checked"': '' ?>/>
			<label for="product_stock_status_xpath"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN )?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-product_stock_status_xpath set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_stock_status" style="width:300px;" value="<?php echo esc_attr($post['single_product_stock_status']) ?>"/>
					<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'instock\', \'outofstock\').', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:2px;">?</a>
				</span>
			</div>
		</div>
		
	</div>	

	<div class="options_group show_if_simple show_if_variable show_if_subscription">
		
		<p class="form-field"><?php _e('Allow Backorders?',PMWI_Plugin::TEXT_DOMAIN);?></p>
		
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_allow_backorders_no" class="switcher" name="product_allow_backorders" value="no" <?php echo 'no' == $post['product_allow_backorders'] ? 'checked="checked"': '' ?>/>
			<label for="product_allow_backorders_no"><?php _e("Do not allow", PMWI_Plugin::TEXT_DOMAIN); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_allow_backorders_notify" class="switcher" name="product_allow_backorders" value="notify" <?php echo 'notify' == $post['product_allow_backorders'] ? 'checked="checked"': '' ?>/>
			<label for="product_allow_backorders_notify"><?php _e("Allow, but notify customer", PMWI_Plugin::TEXT_DOMAIN); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_allow_backorders_yes" class="switcher" name="product_allow_backorders" value="yes" <?php echo 'yes' == $post['product_allow_backorders'] ? 'checked="checked"': '' ?>/>
			<label for="product_allow_backorders_yes"><?php _e("Allow", PMWI_Plugin::TEXT_DOMAIN); ?></label>
		</p>
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="product_allow_backorders_xpath" class="switcher" name="product_allow_backorders" value="xpath" <?php echo 'xpath' == $post['product_allow_backorders'] ? 'checked="checked"': '' ?>/>
			<label for="product_allow_backorders_xpath"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN )?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-product_allow_backorders_xpath set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_allow_backorders" style="width:300px;" value="<?php echo esc_attr($post['single_product_allow_backorders']) ?>"/>
					<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'no\', \'notify\', \'yes\').', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:2px;">?</a>
				</span>
			</div>
		</div>
		
	</div>
	<div class="options_group show_if_simple show_if_variable show_if_subscription">
		
		<p class="form-field"><?php _e('Sold Individually?',PMWI_Plugin::TEXT_DOMAIN);?></p>
		
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_sold_individually_yes" class="switcher" name="product_sold_individually" value="yes" <?php echo 'yes' == $post['product_sold_individually'] ? 'checked="checked"': '' ?>/>
			<label for="product_sold_individually_yes"><?php _e("Yes"); ?></label>
		</p>
		<p class="form-field wpallimport-radio-field">
			<input type="radio" id="product_sold_individually_no" class="switcher" name="product_sold_individually" value="no" <?php echo 'no' == $post['product_sold_individually'] ? 'checked="checked"': '' ?>/>
			<label for="product_sold_individually_no"><?php _e("No"); ?></label>
		</p>
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="product_sold_individually_xpath" class="switcher" name="product_sold_individually" value="xpath" <?php echo 'xpath' == $post['product_sold_individually'] ? 'checked="checked"': '' ?>/>
			<label for="product_sold_individually_xpath"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN )?></label>
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-product_sold_individually_xpath set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_sold_individually" style="width:300px;" value="<?php echo esc_attr($post['single_product_sold_individually']) ?>"/>
					<a href="#help" class="wpallimport-help" title="<?php _e('The value of presented XPath should be one of the following: (\'yes\', \'no\').', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:2px;">?</a>
				</span>
			</div>
		</div>	
	</div>
</div>