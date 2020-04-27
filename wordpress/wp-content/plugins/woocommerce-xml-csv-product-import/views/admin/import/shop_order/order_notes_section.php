<div class="wpallimport-collapsed wpallimport-section closed">
	<div class="wpallimport-content-section" style="padding-bottom: 0;">
		<div class="wpallimport-collapsed-header" style="margin-bottom: 15px;">
			<h3><?php _e('Notes',PMWI_Plugin::TEXT_DOMAIN);?></h3>
		</div>
		<div class="wpallimport-collapsed-content" style="padding:0;">
			<div class="wpallimport-collapsed-content-inner options_group">
				<table class="form-table" style="max-width:none;">					
					<tr>
						<td>
							<!-- Notes matching mode -->
							<!-- <div class="form-field wpallimport-radio-field wpallimport-clear">
								<input type="radio" id="notes_repeater_mode_fixed" name="pmwi_order[notes_repeater_mode]" value="fixed" <?php echo 'fixed' == $post['pmwi_order']['notes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
								<label for="notes_repeater_mode_fixed" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
							</div> -->	
							<table class="form-field wpallimport_variable_table" style="width:98%;">
								<?php 
								foreach ($post['pmwi_order']['notes'] as $i => $note): 

									$note += array('content' => '', 'date' => 'now', 'username' => '', 'email' => '', 'visibility' => 'private', 'visibility_xpath' => '');																	
									
									if (empty($note['content'])) continue;

									?>
									
									<tr class="form-field">
										<td>
											<table style="width:100%;" cellspacing="5">
												<tr>
													<td colspan="2">
														<h4 style="margin-top:0;"><?php _e('Content', PMWI_Plugin::TEXT_DOMAIN) ?></h4>
														<div class="input">
															<textarea name="pmwi_order[notes][<?php echo $i; ?>][content]" style="width: 100%;font-size: 15px !important;" class="rad4"><?php echo esc_attr($note['content']); ?></textarea>
														</div>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<h4><?php _e('Date', PMWI_Plugin::TEXT_DOMAIN) ?><a href="#help" class="wpallimport-help" style="position:relative; top: 1px;" title="<?php _e('Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></h4>
														<div class="input">
															<input type="text" class="datepicker" name="pmwi_order[notes][<?php echo $i; ?>][date]" value="<?php echo esc_attr($note['date']) ?>"/>
														</div>	
													</td>													
												</tr>
												<tr>
													<td colspan="2">
														<h4><?php _e('Visibility', PMWI_Plugin::TEXT_DOMAIN) ?></h4>
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="order_note_visibility_privat_<?php echo $i; ?>" name="pmwi_order[notes][<?php echo $i; ?>][visibility]" value="private" <?php echo 'private' == $note['visibility'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="order_note_visibility_privat_<?php echo $i; ?>" style="width:auto;"><?php _e('Private note', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>
														<div class="form-field wpallimport-radio-field clear">
															<input type="radio" id="order_note_visibility_customer_<?php echo $i; ?>" name="pmwi_order[notes][<?php echo $i; ?>][visibility]" value="customer" <?php echo 'customer' == $note['visibility'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="order_note_visibility_customer_<?php echo $i; ?>" style="width:auto;"><?php _e('Note to customer', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>							
														<div class="form-field wpallimport-radio-field clear">
															<input type="radio" id="order_note_visibilityy_xpath_<?php echo $i; ?>" name="pmwi_order[notes][<?php echo $i; ?>][visibility]" value="xpath" <?php echo 'xpath' == $note['visibility'] ? 'checked="checked"' : '' ?> class="switcher"/>
															<label for="order_note_visibilityy_xpath_<?php echo $i; ?>" style="width:auto;"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>	
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-order_note_visibilityy_xpath_<?php echo $i; ?>">										
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short" name="pmwi_order[notes][<?php echo $i; ?>][visibility_xpath]" style="" value="<?php echo esc_attr($note['visibility_xpath']); ?>"/>	
																<a href="#help" class="wpallimport-help" title="<?php _e('Use \'private\' or \'customer\'.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
															</span>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<label><?php _e('Username', PMWI_Plugin::TEXT_DOMAIN); ?></label>
														<div class="clear">
															<input type="text" name="pmwi_order[notes][<?php echo $i; ?>][username]" value="<?php if ( ! empty($note['username']) ) echo $note['username']; ?>" style="width:100%;"/>	
														</div>
													</td>
													<td>
														<label><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></label>
														<div class="clear">
															<input type="text" name="pmwi_order[notes][<?php echo $i; ?>][email]" value="<?php if ( ! empty($note['email']) ) echo $note['email']; ?>" style="width:100%;"/>	
														</div>
													</td>
												</tr>
											</table>
											<!-- <hr style="margin: 20px 0;"> -->
										</td>	
										<td class="action remove"><!--a href="#remove" style="top: 5px;"></a--></td>
									</tr>

								<?php endforeach; ?>
								<tr class="form-field template">
									<td>
										<table style="width:100%;" cellspacing="5">
											<tr>
												<td colspan="2">
													<h4 style="margin-top:0;"><?php _e('Content', PMWI_Plugin::TEXT_DOMAIN) ?></h4>
													<div class="input">
														<textarea name="pmwi_order[notes][ROWNUMBER][content]" style="width: 100%;" class="rad4"></textarea>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<h4><?php _e('Date', PMWI_Plugin::TEXT_DOMAIN) ?><a href="#help" class="wpallimport-help" style="position:relative; top: 1px;" title="<?php _e('Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></h4>
													<div class="input">
														<input type="text" name="pmwi_order[notes][ROWNUMBER][date]" class="date-picker" value="now"/>
													</div>	
												</td>													
											</tr>
											<tr>
												<td colspan="2">
													<h4><?php _e('Visibility', PMWI_Plugin::TEXT_DOMAIN) ?></h4>
													<div class="input">
														<div class="form-field wpallimport-radio-field">
															<input type="radio" id="order_note_visibility_privat_ROWNUMBER" name="pmwi_order[notes][ROWNUMBER][visibility]" value="private" checked="checked" class="switcher"/>
															<label for="order_note_visibility_privat_ROWNUMBER" style="width:auto;"><?php _e('Private note', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>													
														<div class="form-field wpallimport-radio-field clear">
															<input type="radio" id="order_note_visibility_customer_ROWNUMBER" name="pmwi_order[notes][ROWNUMBER][visibility]" value="customer" class="switcher"/>
															<label for="order_note_visibility_customer_ROWNUMBER" style="width:auto;"><?php _e('Note to customer', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>																				
														<div class="form-field wpallimport-radio-field clear">
															<input type="radio" id="order_note_visibilityy_xpath_ROWNUMBER" name="pmwi_order[notes][ROWNUMBER][visibility]" value="xpath" class="switcher"/>
															<label for="order_note_visibilityy_xpath_ROWNUMBER" style="width:auto;"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN) ?></label>
														</div>	
														<span class="wpallimport-clear"></span>
														<div class="switcher-target-order_note_visibilityy_xpath_ROWNUMBER" style="display:none;">										
															<span class="wpallimport-slide-content" style="padding-left:0;">
																<input type="text" class="short" name="pmwi_order[notes][ROWNUMBER][visibility_xpath]" value=""/>	
																<a href="#help" class="wpallimport-help" title="<?php _e('Use \'private\' or \'customer\'.', PMWI_Plugin::TEXT_DOMAIN) ?>" style="position:relative; top:0px;">?</a>
															</span>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<h4><?php _e('Username', PMWI_Plugin::TEXT_DOMAIN); ?></h4>
													<div class="clear">
														<input type="text" name="pmwi_order[notes][ROWNUMBER][username]" value="" style="width:100%;"/>	
													</div>
												</td>
												<td>
													<h4><?php _e('Email', PMWI_Plugin::TEXT_DOMAIN); ?></h4>
													<div class="clear">
														<input type="text" name="pmwi_order[notes][ROWNUMBER][email]" value="" style="width:100%;"/>	
													</div>
												</td>
											</tr>
										</table>
										<!-- <hr style="margin: 20px 0;"> -->
									</td>	
									<td class="action remove"><!--a href="#remove" style="top: 5px;"></a--></td>
								</tr>
								<tr class="wpallimport-row-actions" style="display:none;">
									<td colspan="2">					
										<a class="add-new-line" title="Add Another Note" href="javascript:void(0);" style="width:200px;"><?php _e("Add Another Note", PMWI_Plugin::TEXT_DOMAIN); ?></a>
									</td>
								</tr>
							</table>							
						</td>
					</tr>					
				</table>
				
			</div>
			
			<div class="wpallimport-collapsed closed wpallimport-section">
				<div style="margin:0; border-top:1px solid #ddd; border-bottom: none; border-right: none; border-left: none; background: #f1f2f2;" class="wpallimport-content-section rad0">
					<div class="wpallimport-collapsed-header">
						<h3 style="color:#40acad;"><?php _e("Advanced Options", PMWI_Plugin::TEXT_DOMAIN);?></h3>
					</div>
					<div style="padding: 0px;" class="wpallimport-collapsed-content">										
						<div class="wpallimport-collapsed-content-inner">											
							<?php if ( empty(PMXI_Plugin::$session->options['delimiter']) ): ?>
							<div class="form-field wpallimport-radio-field wpallimport-clear pmxi_option">
								<input type="radio" id="notes_repeater_mode_variable_csv" name="pmwi_order[notes_repeater_mode]" value="csv" <?php echo 'csv' == $post['pmwi_order']['notes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
								<label for="notes_repeater_mode_variable_csv" style="width:auto;"><?php _e('Fixed Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
								<div class="switcher-target-notes_repeater_mode_variable_csv wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
									<span class="order-separator-label wpallimport-slide-content" style="padding-left:0;">	
										<label><?php _e('Multiple notes separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
										<input type="text" class="short rad4 order-separator-input" name="pmwi_order[notes_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['notes_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
										<a href="#help" class="wpallimport-help" style="top:0px;left:8px;" original-title="For example, two notes would be imported like this Note 1|Note 2">?</a>							
									</span>
								</div>
							</div>								
							<div class="form-field wpallimport-radio-field wpallimport-clear pmxi_option">
								<input type="radio" id="notes_repeater_mode_variable_xml" name="pmwi_order[notes_repeater_mode]" value="xml" <?php echo 'xml' == $post['pmwi_order']['notes_repeater_mode'] ? 'checked="checked"' : '' ?> class="switcher variable_repeater_mode"/>
								<label for="notes_repeater_mode_variable_xml" style="width:auto;"><?php _e('Variable Repeater Mode', PMWI_Plugin::TEXT_DOMAIN) ?></label>
								<div class="switcher-target-notes_repeater_mode_variable_xml wpallimport-clear" style="padding: 10px 0 10px 25px; overflow: hidden;">
									<span class="wpallimport-slide-content" style="padding-left:0;">	
										<label style="width: 60px;"><?php _e('For each', PMWI_Plugin::TEXT_DOMAIN); ?></label>
										<input type="text" class="short rad4" name="pmwi_order[notes_repeater_mode_foreach]" value="<?php echo esc_attr($post['pmwi_order']['notes_repeater_mode_foreach']) ?>" style="width:50%;"/>							
										<label style="padding-left: 10px;"><?php _e('do...', PMWI_Plugin::TEXT_DOMAIN); ?></label>
									</span>
								</div>		
							</div>	
							<?php else: ?>
							<input type="hidden" name="pmwi_order[notes_repeater_mode]" value="csv"/>
							<div class="form-field input" style="padding-left: 20px;">
								<label class="order-separator-label" style="line-height: 30px;"><?php _e('Multiple notes separated by', PMWI_Plugin::TEXT_DOMAIN); ?></label>
								<input type="text" class="short rad4 order-separator-input" name="pmwi_order[notes_repeater_mode_separator]" value="<?php echo esc_attr($post['pmwi_order']['notes_repeater_mode_separator']) ?>" style="width:10%; text-align: center;"/>
								<a href="#help" class="wpallimport-help" style="top:0px;left:8px;" original-title="For example, two notes would be imported like this 'Note 1|Note 2'">?</a>							
							</div>
							<?php endif; ?>	
						</div>				
					</div>
				</div>
			</div>																	
		</div>
	</div>
</div>