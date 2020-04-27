<div class="wpallimport-collapsed closed">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3><?php _e('Billing & Shipping Details',PMWI_Plugin::TEXT_DOMAIN);?></h3>
		</div>
		<div class="wpallimport-collapsed-content" style="padding:0;">
			<div class="wpallimport-collapsed-content-inner">
				<table class="form-table" style="max-width:none;">
					<tr>
						<td colspan="3">
							<div class="postbox woocommerce-order-data" id="woocommerce-product-data">								
								<div class="clear"></div>
								<div style="min-height: 200px;">
									<div class="panel-wrap product_data">										

										<ul class="product_data_tabs wc-tabs shop_order_tabs">

											<li class="woocommerce-order-billing-data active">
												<a href="javascript:void(0);" rel="billing_order_data"><?php _e('Billing',PMWI_Plugin::TEXT_DOMAIN);?></a>
											</li>

											<li class="woocommerce-order-shipping-data">
												<a href="javascript:void(0);" rel="shipping_order_data"><?php _e('Shipping', PMWI_Plugin::TEXT_DOMAIN);?></a>
											</li>

											<li class="woocommerce-order-payment-data">
												<a href="javascript:void(0);" rel="payment_order_data"><?php _e('Payment', PMWI_Plugin::TEXT_DOMAIN);?></a>
											</li>											

										</ul>

										<!-- BILLING -->

										<?php include( '_tabs/_order_billing.php' ); ?>

										<!-- SHIPPING -->

										<?php include( '_tabs/_order_shipping.php' ); ?>

										<!-- PAYMENT -->

										<?php include( '_tabs/_order_payment.php' ); ?>																				
										
									</div>
								</div>
							</div>

							<div class="clear"></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>