<div class="wpallimport-collapsed wpallimport-section">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3><?php _e('Order Details',PMWI_Plugin::TEXT_DOMAIN);?></h3>
		</div>
		<div class="wpallimport-collapsed-content" style="padding:0;">
			<div class="wpallimport-collapsed-content-inner">
				<table class="form-table" style="max-width:none;">
					<tr>
						<td>
							<!-- Order Status -->
							<div class="input">
								<h4><?php _e('Order Status', PMWI_Plugin::TEXT_DOMAIN) ?></h4>
								<select id="order_status" name="pmwi_order[status]" style="width: 200px;" class="switcher">
									<?php
									$statuses = wc_get_order_statuses();
									$statuses_for_tooltip = array();
									foreach ( $statuses as $status => $status_name ) {
										echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, $post['pmwi_order']['status'], false ) . '>' . esc_html( $status_name ) . '</option>';
										$statuses_for_tooltip[] = esc_attr( $status );
									}
									?>
									<option value="xpath" <?php if ("xpath" == $post['pmwi_order']['status']) echo 'selected="selected"';?>><?php _e("Set with XPath", PMWI_Plugin::TEXT_DOMAIN); ?></option>
								</select>				
								<span class="wpallimport-clear"></span>
								<div class="switcher-target-order_status" style="margin-top:10px;">
									<span class="wpallimport-slide-content" style="padding-left:0;">
										<input type="text" class="short" name="pmwi_order[status_xpath]" value="<?php echo esc_attr($post['pmwi_order']['status_xpath']) ?>"/>
										<a href="#help" class="wpallimport-help" title="<?php printf(__('Order status can be matched by title or slug: %s. If order status is not found \'Pending Payment\' will be applied to order.', PMWI_Plugin::TEXT_DOMAIN), implode(", ", $statuses_for_tooltip)); ?>" style="position:relative; top:-2px;">?</a>
									</span>
								</div>								
							</div>	
						</td>
					</tr>
					<tr>
						<td>
							<!-- Order Date -->
							<div class="input">
								<h4><?php _e('Date', PMWI_Plugin::TEXT_DOMAIN) ?><a href="#help" class="wpallimport-help" style="position:relative; top: 1px;" title="<?php _e('Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></h4>
								<div class="input">
									<input type="text" class="datepicker" name="pmwi_order[date]" value="<?php echo esc_attr($post['pmwi_order']['date']) ?>"/>
								</div>								
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>