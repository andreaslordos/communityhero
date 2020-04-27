<div class="panel woocommerce_options_panel" id="order_products">
	<div class="options_group hide_if_grouped">																						
		<!-- Product matching mode -->
		<!-- <div class="form-field wpallimport-radio-field wpallimport-clear">
			<input type="radio" id="products_repeater_mode_fixed" name="pmwi_order[products_repeater_mode]" value="fixed" <?php echo 'fixed' == $post['pmwi_order']['products_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
			<label for="products_repeater_mode_fixed" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
		</div> -->	
		
		<div class="form-field wpallimport-radio-field">
			<input type="radio" id="products_source_existing" name="pmwi_order[products_source]" value="existing" <?php echo 'existing' == $post['pmwi_order']['products_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
			<label for="products_source_existing" style="width:auto;"><?php _e('Get data from existing products', PMWI_Plugin::TEXT_DOMAIN) ?></label>
			<a href="#help" class="wpallimport-help" title="<?php _e('If no product is found the order will be skipped.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:3px;">?</a>
		</div>	
		<span class="wpallimport-clear"></span>
		<div class="switcher-target-products_source_existing" style="padding-left:45px;">											
			<span class="wpallimport-slide-content" style="padding-left:0;">					
				<table class="wpallimport_variable_table" style="width:100%;">
					<?php
					foreach ($post['pmwi_order']['products'] as $i => $product): 

						$product += array('unique_key' => '', 'sku' => '', 'qty' => '', 'price_per_unit' => '', 'tax_rates' => array());
						
						if (empty($product['sku'])) continue;
						?>

						<tr>
							<td colspan="2">
								<div style="float:left; width:50%;">
									<label><?php _e('Product Unique Key', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][unique_key]" value="<?php echo esc_attr($product['unique_key']) ?>" style="width:95%;"/>
								</div>
								<div style="float:right; width:50%;">
									<label><?php _e('Quantity', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][qty]" value="<?php echo esc_attr($product['qty']) ?>" style="width:95%;"/>	
								</div>
								<div class="wpallimport-clear"></div>
                                <div style="float:left; width:50%;">
									<label><?php _e('Product SKU', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][sku]" value="<?php echo esc_attr($product['sku']) ?>" style="width:95%;"/>
								</div>
								<div style="float:right; width:50%;">
									<label><?php _e('Price', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][price_per_unit]" value="<?php echo esc_attr($product['price_per_unit']) ?>" style="width:95%;"/>
								</div>
								<div class="wpallimport-clear"></div>
								<!-- Product Taxes -->
								<a class="switcher" id="taxes_existing_products_<?php echo $i; ?>" href="javascript:void(0);" style="display: block;margin: 10px 0 15px;width: 50px;"><span>-</span> <?php _e("Taxes", PMWI_Plugin::TEXT_DOMAIN); ?></a>
								<div class="wpallimport-clear"></div>
								<div class="switcher-target-taxes_existing_products_<?php echo $i; ?>">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<table style="width:45%;" class="taxes-form-table">
											<?php 											
											foreach ($product['tax_rates'] as $j => $tax_rate): 

												$tax_rate += array('code' => '', 'calculate_logic' => 'percentage', 'percentage_value' => '', 'amount_per_unit' => ''); 

												if (empty($tax_rate['code'])) continue;

												?>
												<tr class="form-field">
													<td>
														<div class="form-field">
															<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
															<div class="clear"></div>
															<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][code]" style="width:100%;" value="<?php echo esc_attr($tax_rate['code']) ?>"/>	
														</div>
														
														<span class="wpallimport-clear"></span>

														<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="tax_calculate_logic_percentage_<?php echo $i; ?>_<?php echo $j; ?>" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][calculate_logic]" value="percentage" <?php echo 'percentage' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="tax_calculate_logic_percentage_<?php echo $i; ?>_<?php echo $j; ?>" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<span class="wpallimport-clear"></span>
															<div class="switcher-target-tax_calculate_logic_percentage_<?php echo $i; ?>_<?php echo $j; ?>" style="padding-left:25px;">
																<span class="wpallimport-slide-content" style="padding-left:0;">
																	<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][percentage_value]"  style="width:100%;" value="<?php echo esc_attr($tax_rate['percentage_value']) ?>"/>
																</span>
															</div>
														</div>
														<span class="wpallimport-clear"></span>
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="tax_calculate_logic_per_unit_<?php echo $i; ?>_<?php echo $j; ?>" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][calculate_logic]" value="per_unit" <?php echo 'per_unit' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="tax_calculate_logic_per_unit_<?php echo $i; ?>_<?php echo $j; ?>" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<span class="wpallimport-clear"></span>
															<div class="switcher-target-tax_calculate_logic_per_unit_<?php echo $i; ?>_<?php echo $j; ?>" style="padding-left:25px;">
																<span class="wpallimport-slide-content" style="padding-left:0;">
																	<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][amount_per_unit]"  style="width:100%;" value="<?php echo esc_attr($tax_rate['amount_per_unit']) ?>"/>
																</span>
															</div>
														</div>
														<span class="wpallimport-clear"></span>
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="tax_calculate_logic_lookup_<?php echo $i; ?>_<?php echo $j; ?>" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][<?php echo $i; ?>][calculate_logic]" value="loocup" <?php echo 'loocup' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="tax_calculate_logic_lookup_<?php echo $i; ?>_<?php echo $j; ?>" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
														</div>
														<hr style="margin-left: 20px;">
													</td>
													<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
												</tr>
											<?php endforeach; ?>
											<tr class="form-field template">
												<td>
													<div class="form-field">
														<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
														<div class="clear"></div>
														<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][code]"  style="width:100%;" value=""/>	
													</div>
													
													<span class="wpallimport-clear"></span>

													<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="tax_calculate_logic_percentage_<?php echo $i; ?>_ROWNUMBER" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][calculate_logic]" value="percentage" checked="checked" class="switcher"/>
														<label for="tax_calculate_logic_percentage_<?php echo $i; ?>_ROWNUMBER" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-tax_calculate_logic_percentage_<?php echo $i; ?>_ROWNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][percentage_value]"  style="width:100%;"/>
															</span>
														</div>
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="tax_calculate_logic_per_unit_<?php echo $i; ?>_ROWNUMBER" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][calculate_logic]" value="per_unit" class="switcher"/>
														<label for="tax_calculate_logic_per_unit_<?php echo $i; ?>_ROWNUMBER" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-tax_calculate_logic_per_unit_<?php echo $i; ?>_ROWNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][amount_per_unit]" style="width:100%;"/>
															</span>
														</div>															
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="tax_calculate_logic_lookup_<?php echo $i; ?>_ROWNUMBER" name="pmwi_order[products][<?php echo $i; ?>][tax_rates][ROWNUMBER][calculate_logic]" value="loocup" class="switcher"/>
														<label for="tax_calculate_logic_lookup_<?php echo $i; ?>_ROWNUMBER" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
													</div>
													<hr style="margin-left: 20px;">
												</td>
												<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
											</tr>
											<tr>
												<td colspan="2">
													<div class="form-field">
														<a class="add-new-line" title="Add Tax" href="javascript:void(0);"><?php _e("Add Tax", "	wp_all_import_plugin"); ?></a>
													</div>
												</td>
											</tr>
										</table>
									</span>
								</div>
								<!-- <hr> -->
							</td>
							<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
						</tr>
					<?php endforeach; ?>
					<tr class="template">
						<td colspan="2">
                            <div style="float:left; width:50%;">
                                <label><?php _e('Product Unique Key', PMWI_Plugin::TEXT_DOMAIN); ?></label>
                                <input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][unique_key]" value="" style="width:95%;"/>
                            </div>
							<div style="float:right; width:50%;">
								<label><?php _e('Quantity', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][qty]" value="" style="width:95%;"/>	
							</div>
							<div class="wpallimport-clear"></div>
                            <div style="float:left; width:50%;">
								<label><?php _e('Product SKU', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][sku]" value="" style="width:95%;"/>
							</div>
							<div style="float:right; width:50%;">
								<label><?php _e('Price', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][price_per_unit]" value="" style="width:95%;"/>
							</div>
							<div class="wpallimport-clear"></div>
							<!-- Product Taxes -->
							<a class="switcher" id="taxes_existing_products_ROWNUMBER" href="javascript:void(0);" style="display: block;margin: 10px 0 15px;width: 50px;"><span>-</span> <?php _e("Taxes", PMWI_Plugin::TEXT_DOMAIN); ?></a>
							<div class="wpallimport-clear"></div>
							<div class="switcher-target-taxes_existing_products_ROWNUMBER">
								<span class="wpallimport-slide-content" style="padding-left:0;">
									<table style="width:45%;" class="taxes-form-table">								
										<tr class="form-field template">
											<td>
												<div class="form-field">
													<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
													<div class="clear"></div>
													<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][code]"  style="width:100%;" value=""/>	
												</div>
												
												<span class="wpallimport-clear"></span>

												<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

												<div class="form-field wpallimport-radio-field">
													<input type="radio" id="tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="percentage" checked="checked" class="switcher"/>
													<label for="tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
													<span class="wpallimport-clear"></span>
													<div class="switcher-target-tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" style="padding-left:25px;">
														<span class="wpallimport-slide-content" style="padding-left:0;">
															<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][percentage_value]"  style="width:100%;"/>
														</span>
													</div>
												</div>
												<span class="wpallimport-clear"></span>
												<div class="form-field wpallimport-radio-field">
													<input type="radio" id="tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="per_unit" class="switcher"/>
													<label for="tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
													<span class="wpallimport-clear"></span>
													<div class="switcher-target-tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" style="padding-left:25px;">
														<span class="wpallimport-slide-content" style="padding-left:0;">
															<input type="text" class="short rad4" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][amount_per_unit]" style="width:100%;"/>
														</span>
													</div>
												</div>
												<span class="wpallimport-clear"></span>
												<div class="form-field wpallimport-radio-field">
													<input type="radio" id="tax_calculate_logic_lookup_ROWNUMBER_CELLNUMBER" name="pmwi_order[products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="loocup" class="switcher"/>
													<label for="tax_calculate_logic_lookup_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
													<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
												</div>
												<hr style="margin-left: 20px;">
											</td>
											<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
										</tr>
										<tr>
											<td colspan="2">
												<div class="form-field">
													<a class="add-new-line" title="Add Tax" href="javascript:void(0);"><?php _e("Add Tax", "	wp_all_import_plugin"); ?></a>
												</div>
											</td>
										</tr>
									</table>
								</span>
							</div>
							<!-- <hr> -->
						</td>
						<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
					</tr>
					<tr class="wpallimport-row-actions" style="display:none;">
						<td colspan="3">												
							<a class="add-new-line" title="Add Product" href="javascript:void(0);" style="width:200px;"><?php _e("Add Product", PMWI_Plugin::TEXT_DOMAIN); ?></a>
						</td>
					</tr>
				</table>
			</span>						
		</div>		
		<!-- Manually import product order data -->
		<div class="clear"></div>
		<div style="margin-top:0;">
			<div class="form-field wpallimport-radio-field">
				<input type="radio" id="products_source_new" name="pmwi_order[products_source]" value="new" <?php echo 'new' == $post['pmwi_order']['products_source'] ? 'checked="checked"' : '' ?> class="switcher"/>
				<label for="products_source_new" style="width:auto;"><?php _e('Manually import product order data and do not try to match to existing products', PMWI_Plugin::TEXT_DOMAIN) ?></label>
				<a href="#help" class="wpallimport-help" title="<?php _e('The product in this order will not be linked to any existing products.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:3px;">?</a>
			</div>	
			<span class="wpallimport-clear"></span>
			<div class="switcher-target-products_source_new" style="padding-left:45px;">											
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<table class="wpallimport_variable_table">
						<?php 
						foreach ($post['pmwi_order']['manual_products'] as $i => $product):
							
							$product += array(
								'unique_key' => '',
								'sku' => '',
								'meta_name' => array(),
								'meta_value' => array(), 
								'price_per_unit' => '',
								'qty' => '',
								'tax_rates' => array()
							);		

							if (empty($product['sku'])) continue;

						?>
						<tr class="form-field">
							<td colspan="2">
								
								<label><?php _e('Product Unique Key', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear"></div>
								<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][unique_key]" value="<?php echo esc_attr($product['unique_key']) ?>" style="width:100%;"/>

								<span class="wpallimport-clear"></span>

                                <label><?php _e('Product Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear"></div>
								<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][sku]" value="<?php echo esc_attr($product['sku']) ?>" style="width:100%;"/>	
															
								<span class="wpallimport-clear"></span>

								<table class="form-field">
									<?php foreach ($product['meta_name'] as $j => $meta_name): if (empty($meta_name)) continue; ?>
									<tr class="form-field">
										<td style="padding-right:10px;">
											<label><?php _e('Meta Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i;?>][meta_name][]" value="<?php echo esc_attr($meta_name); ?>" style="width:100%;"/>
										</td>
										<td style="padding-left:10px;">
											<label><?php _e('Meta Value', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i;?>][meta_value][]" value="<?php echo esc_attr($product['meta_value'][$j]); ?>" style="width:100%;"/>
										</td>
										<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
									</tr>	
									<?php endforeach; ?>
									<tr class="form-field template">
										<td style="padding-right:10px;">
											<label><?php _e('Meta Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][0][meta_name][]" value="" style="width:100%;"/>
										</td>
										<td style="padding-left:10px;">
											<label><?php _e('Meta Value', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][0][meta_value][]" value="" style="width:100%;"/>
										</td>
										<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
									</tr>
									<tr>
										<td colspan="3">																			
											<a class="add-new-line" title="Add Product Meta" href="javascript:void(0);" style="display:block;margin:5px 0;width:140px;top:0;padding-top:4px;"><?php empty($product['meta_name']) ? _e("Add Product Meta", PMWI_Plugin::TEXT_DOMAIN): _e("Add More Product Meta", PMWI_Plugin::TEXT_DOMAIN); ?></a>
										</td>
									</tr>
								</table>

								<table>
									<tr>
										<td style="padding-right:10px;">
											<label><?php _e('Price per Unit', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][price_per_unit]" value="<?php echo esc_attr($product['price_per_unit']) ?>" style="width:100%;"/>
										</td>
										<td style="padding-left: 10px;">
											<label><?php _e('Quantity', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][qty]" value="<?php echo esc_attr($product['qty']) ?>" style="width:100%;"/>
										</td>
									</tr>																			
								</table>

								<a class="switcher" id="taxes_manual_products_<?php echo $i; ?>" href="javascript:void(0);" style="display: block;margin: 10px 0 15px;width: 50px;"><span>-</span> <?php _e("Taxes", PMWI_Plugin::TEXT_DOMAIN); ?></a>
								<div class="wpallimport-clear"></div>
								<div class="switcher-target-taxes_manual_products_<?php echo $i; ?>">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<table style="width:45%;" class="taxes-form-table">
											<?php 
											foreach ($product['tax_rates'] as $j => $tax_rate): 

												$tax_rate += array('code' => '', 'calculate_logic' => 'percentage', 'percentage_value' => '', 'amount_per_unit' => ''); 

												if (empty($tax_rate['code'])) continue;

												?>

												<tr class="form-field">
													<td>
														<div class="form-field">
															<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
															<div class="clear"></div>
															<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][code]"  style="width:100%;" value="<?php echo esc_attr($tax_rate['code']) ?>"/>	
														</div>
														
														<span class="wpallimport-clear"></span>

														<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="product_tax_calculate_logic_percentage_<?php echo $i . '_' . $j; ?>" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][calculate_logic]" value="percentage" <?php echo 'percentage' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="product_tax_calculate_logic_percentage_<?php echo $i . '_' . $j; ?>" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<span class="wpallimport-clear"></span>
															<div class="switcher-target-product_tax_calculate_logic_percentage_<?php echo $i . '_' . $j; ?>" style="padding-left:25px;">
																<span class="wpallimport-slide-content" style="padding-left:0;">
																	<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][percentage_value]"  style="width:100%;" value="<?php echo esc_attr($tax_rate['percentage_value']) ?>"/>
																</span>
															</div>
														</div>
														<span class="wpallimport-clear"></span>
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="product_tax_calculate_logic_per_unit_<?php echo $i . '_' . $j; ?>" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][calculate_logic]" value="per_unit" <?php echo 'per_unit' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="product_tax_calculate_logic_per_unit_<?php echo $i . '_' . $j; ?>" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<span class="wpallimport-clear"></span>
															<div class="switcher-target-product_tax_calculate_logic_per_unit_<?php echo $i . '_' . $j; ?>" style="padding-left:25px;">
																<span class="wpallimport-slide-content" style="padding-left:0;">
																	<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][amount_per_unit]"  style="width:100%;" value="<?php echo esc_attr($tax_rate['amount_per_unit']) ?>"/>
																</span>
															</div>
														</div>
														<span class="wpallimport-clear"></span>
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="product_tax_calculate_logic_lookup_<?php echo $i . '_' . $j; ?>" name="pmwi_order[manual_products][<?php echo $i; ?>][tax_rates][<?php echo $j; ?>][calculate_logic]" value="loocup" <?php echo 'loocup' == $tax_rate['calculate_logic'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="product_tax_calculate_logic_lookup_<?php echo $i . '_' . $j; ?>" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
															<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
														</div>
														<hr style="margin-left: 20px;">
													</td>
													<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
												</tr>

											<?php endforeach; ?>
											<tr class="form-field template">
												<td>
													<div class="form-field">
														<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
														<div class="clear"></div>
														<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][code]"  style="width:100%;" value=""/>	
													</div>
													
													<span class="wpallimport-clear"></span>

													<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_percentage_<?php echo $i;?>_ROWNUMBER" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][calculate_logic]" value="percentage" checked="checked" class="switcher"/>
														<label for="product_tax_calculate_logic_percentage_<?php echo $i;?>_ROWNUMBER" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-product_tax_calculate_logic_percentage_<?php echo $i;?>_ROWNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][percentage_value]"  style="width:100%;" value=""/>
															</span>
														</div>
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_per_unit_<?php echo $i;?>_ROWNUMBER" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][calculate_logic]" value="per_unit" class="switcher"/>
														<label for="product_tax_calculate_logic_per_unit_<?php echo $i;?>_ROWNUMBER" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-product_tax_calculate_logic_per_unit_<?php echo $i;?>_ROWNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][amount_per_unit]"  style="width:100%;" value=""/>
															</span>
														</div>
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_lookup_<?php echo $i;?>_ROWNUMBER" name="pmwi_order[manual_products][<?php echo $i;?>][tax_rates][ROWNUMBER][calculate_logic]" value="loocup" class="switcher"/>
														<label for="product_tax_calculate_logic_lookup_<?php echo $i;?>_ROWNUMBER" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
													</div>
													<hr style="margin-left: 20px;">
												</td>
												<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
											</tr>
											<tr>
												<td colspan="2">
													<div class="form-field">
														<a class="add-new-line" title="Add Tax" href="javascript:void(0);"><?php _e("Add Tax", "	wp_all_import_plugin"); ?></a>
													</div>
												</td>
											</tr>
										</table>
									</span>
								</div>
								<!-- <hr> -->
							</td>
							<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
						</tr>

						<?php endforeach; ?>
						<tr class="form-field template">
							<td colspan="2">
								
								<label><?php _e('Product Unique Key', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear"></div>
								<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][unique_key]" value="" style="width:100%;"/>

								<span class="wpallimport-clear"></span>

                                <label><?php _e('Product Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<div class="clear"></div>
								<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][sku]" value="" style="width:100%;"/>	
															
								<span class="wpallimport-clear"></span>

								<table class="form-field">
									<tr class="form-field template">
										<td style="padding-right:10px;">
											<label><?php _e('Meta Name', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][meta_name][]" value="" style="width:100%;"/>
										</td>
										<td style="padding-left:10px;">
											<label><?php _e('Meta Value', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][meta_value][]" value="" style="width:100%;"/>
										</td>
										<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
									</tr>
									<tr>
										<td colspan="3">																			
											<a class="add-new-line" title="Add Product Meta" href="javascript:void(0);" style="display:block; margin: 5px 0; width:140px; top:0;padding-top:4px;"><?php _e("Add Product Meta", PMWI_Plugin::TEXT_DOMAIN); ?></a>
										</td>
									</tr>
								</table>

								<table>
									<tr>
										<td style="padding-right:10px;">
											<label><?php _e('Price per Unit', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][price_per_unit]" value="" style="width:100%;"/>
										</td>
										<td style="padding-left:10px;">
											<label><?php _e('Quantity', PMWI_Plugin::TEXT_DOMAIN); ?></label>
											<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][qty]" value="" style="width:100%;"/>
										</td>
									</tr>																			
								</table>

								<a class="switcher" id="taxes_manual_products_ROWNUMBER" href="javascript:void(0);" style="display: block;margin: 10px 0 15px;width: 50px;"><span>-</span> <?php _e("Taxes", PMWI_Plugin::TEXT_DOMAIN); ?></a>
								<div class="wpallimport-clear"></div>
								<div class="switcher-target-taxes_manual_products_ROWNUMBER">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<table style="width:45%;" class="taxes-form-table">
											<tr class="form-field template">
												<td>
													<div class="form-field">
														<label><?php _e('Tax Rate Code', PMWI_Plugin::TEXT_DOMAIN); ?></label>
														<div class="clear"></div>
														<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][code]" style="width:100%;" value=""/>	
													</div>
													
													<span class="wpallimport-clear"></span>

													<p class="form-field"><?php _e("Calculate Tax Amount By:", PMWI_Plugin::TEXT_DOMAIN);?></p>

													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="percentage" checked="checked" class="switcher"/>
														<label for="product_tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Percentage', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-product_tax_calculate_logic_percentage_ROWNUMBER_CELLNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][percentage_value]"  style="width:100%;" value=""/>
															</span>
														</div>
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="per_unit" class="switcher"/>
														<label for="product_tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Tax amount per unit', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-product_tax_calculate_logic_per_unit_ROWNUMBER_CELLNUMBER" style="padding-left:25px;">
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short rad4" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][amount_per_unit]" style="width:100%;" value=""/>
															</span>
														</div>
													</div>
													<span class="wpallimport-clear"></span>
													<div class="form-field wpallimport-radio-field">
														<input type="radio" id="product_tax_calculate_logic_lookup_ROWNUMBER_CELLNUMBER" name="pmwi_order[manual_products][ROWNUMBER][tax_rates][CELLNUMBER][calculate_logic]" value="loocup" class="switcher"/>
														<label for="product_tax_calculate_logic_lookup_ROWNUMBER_CELLNUMBER" style="width:auto;"><?php _e('Look up tax rate code', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														<a href="#help" class="wpallimport-help" title="<?php _e('If rate code is not found, this tax amount will not be imported.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
													</div>
													<hr style="margin-left: 20px;">
												</td>
												<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
											</tr>
											<tr>
												<td colspan="2">
													<div class="form-field">
														<a class="add-new-line" title="Add Tax" href="javascript:void(0);"><?php _e("Add Tax", "	wp_all_import_plugin"); ?></a>
													</div>
												</td>
											</tr>
										</table>
									</span>
								</div>		
								<!-- <hr>						 -->
							</td>
							<td class="action remove"><a href="#remove" style="top: 33px;"></a></td>
						</tr>																
						<tr class="wpallimport-row-actions" style="display:none;">
							<td colspan="3">																
								<a class="add-new-line" title="Add Product" href="javascript:void(0);" style="width:200px;"><?php _e("Add Product", PMWI_Plugin::TEXT_DOMAIN); ?></a>
							</td>
						</tr>
					</table>
				</span>						
			</div>
		</div>													
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
						<input type="radio" id="products_repeater_mode_variable_csv" name="pmwi_order[products_repeater_mode]" value="csv" <?php echo 'csv' == $post['pmwi_order']['products_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="products_repeater_mode_variable_csv" style="width:auto; float: none;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-products_repeater_mode_variable_csv wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple products separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4 order-separator-input" name="pmwi_order[products_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['products_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>							
							</span>
						</div>
					</div>					
					<div class="form-field wpallimport-radio-field wpallimport-clear">
						<input type="radio" id="products_repeater_mode_variable_xml" name="pmwi_order[products_repeater_mode]" value="xml" <?php echo 'xml' == $post['pmwi_order']['products_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
						<label for="products_repeater_mode_variable_xml" style="width:auto; float: none;"><?php _e('Variable Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
						<div class="switcher-target-products_repeater_mode_variable_xml wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
							<span class="wpallimport-slide-content" style="padding-left:0;">	
								<label style="width: 60px; line-height: 30px;"><?php _e('For each', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4" name="pmwi_order[products_repeater_mode_foreach]" value="<?php echo esc_attr($post['pmwi_order']['products_repeater_mode_foreach']) ?>" style="width:50%;"/>							
								<label class="foreach-do" style="padding-left: 10px; line-height: 30px;"><?php _e('do...', PMWI_Plugin::TEXT_DOMAIN); ?></label>
							</span>
						</div>		
					</div>
					<?php else: ?>
					<input type="hidden" name="pmwi_order[products_repeater_mode]" value="csv"/>
					<div class="form-field input" style="margin-bottom: 20px;">
						<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple products separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
						<input type="text" class="short rad4 order-separator-input" name="pmwi_order[products_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['products_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
						<a href="#help" class="wpallimport-help" style="top:10px;left:8px;" original-title="For example, two products would be imported like this SKU1|SKU2, and their quantities like this 15|20">?</a>							
					</div>
					<?php endif; ?>		
				</div>
			</div>
		</div>
	</div>					
</div>