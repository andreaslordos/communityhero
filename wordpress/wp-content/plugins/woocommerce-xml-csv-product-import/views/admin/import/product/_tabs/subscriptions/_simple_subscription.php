<div class="options_group show_if_subscription show_if_variable_subscription">
    <p class="form-field">
        <label><?php printf(__("Subscription price (%s)", PMWI_Plugin::TEXT_DOMAIN), get_woocommerce_currency_symbol()); ?></label>
        <input type="text" class="short" name="single_product_subscription_price" value="<?php echo esc_attr($post['single_product_subscription_price']) ?>"/> <strong class="options_group" style="position:relative; top:4px; left:4px;">(<?php _e('required', PMWI_Plugin::TEXT_DOMAIN); ?>)</strong>
    </p>
    <span class="wpallimport-clear"></span>
    <div class="subscription-billing-settings">
        <p class="form-field woocommerce-group-label"><?php _e("Billing interval", PMWI_Plugin::TEXT_DOMAIN); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Set how much time between each renewal for recurring billing schedules.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></p>
        <div class="subscription-billing-interval">
            <div class="form-field wpallimport-radio-field">
                <input type="radio" id="multiple_product_subscription_period_interval_yes" class="switcher" name="is_multiple_product_subscription_period_interval" value="yes" <?php echo 'no' != $post['is_multiple_product_subscription_period_interval'] ? 'checked="checked"': '' ?>/>
                <select class="select short" name="multiple_product_subscription_period_interval">
                    <?php foreach ( wcs_get_subscription_period_interval_strings() as $value => $label ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post['multiple_product_subscription_period_interval'], true ) ?>><?php echo esc_html( $label ); ?></option>
                    <?php } ?>
                </select>
            </div>
            <span class="wpallimport-clear"></span>
            <div class="form-field wpallimport-radio-field">
                <input type="radio" id="multiple_product_subscription_period_interval_no" class="switcher" name="is_multiple_product_subscription_period_interval" value="no" <?php echo 'no' == $post['is_multiple_product_subscription_period_interval'] ? 'checked="checked"': '' ?>/>
                <label for="multiple_product_subscription_period_interval_no"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN ); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Return a number from 1 to 6.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></label>
                <span class="wpallimport-clear"></span>
                <div class="switcher-target-multiple_product_subscription_period_interval_no set_with_xpath">
                    <span class="wpallimport-slide-content" style="padding-left:0;">
                        <input type="text" class="smaller-text" name="single_product_subscription_period_interval" style="max-width:300px;" value="<?php echo esc_attr($post['single_product_subscription_period_interval']) ?>"/>
                    </span>
                </div>
            </div>
        </div>
        <div class="subscription-billing-period">
            <div class="form-field wpallimport-radio-field">
                <input type="radio" id="multiple_product_subscription_period_yes" class="switcher" name="is_multiple_product_subscription_period" value="yes" <?php echo 'no' != $post['is_multiple_product_subscription_period'] ? 'checked="checked"': '' ?>/>
                <select class="select short" name="multiple_product_subscription_period">
                    <?php foreach ( wcs_get_subscription_period_strings() as $value => $label ) { ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $post['multiple_product_subscription_period'], true ) ?>><?php echo esc_html( $label ); ?></option>
                    <?php } ?>
                </select>
            </div>
            <span class="wpallimport-clear"></span>
            <div class="form-field wpallimport-radio-field">
                <input type="radio" id="multiple_product_subscription_period_no" class="switcher" name="is_multiple_product_subscription_period" value="no" <?php echo 'no' == $post['is_multiple_product_subscription_period'] ? 'checked="checked"': '' ?>/>
                <label for="multiple_product_subscription_period_no"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN ); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Return one of the following: day, week, month, year.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></label>
                <span class="wpallimport-clear"></span>
                <div class="switcher-target-multiple_product_subscription_period_no set_with_xpath">
                    <span class="wpallimport-slide-content" style="padding-left:0;">
                        <input type="text" class="smaller-text" name="single_product_subscription_period" style="max-width:300px;" value="<?php echo esc_attr($post['single_product_subscription_period']) ?>"/>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <span class="wpallimport-clear"></span>
    <div class="subscription-expiration">
        <p class="form-field woocommerce-group-label"><?php _e("Expire after", PMWI_Plugin::TEXT_DOMAIN); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></p>
        <div class="form-field wpallimport-radio-field">
            <input type="radio" id="multiple_product_subscription_length_yes" class="switcher" name="is_multiple_product_subscription_length" value="yes" <?php echo 'no' != $post['is_multiple_product_subscription_length'] ? 'checked="checked"': '' ?>/>
            <?php $intervals = wcs_get_subscription_ranges( $post['multiple_product_subscription_length'] ); ?>
            <?php foreach ( $intervals as $group => $options ) { ?>
                <select class="subscription_length-<?php echo $group;?>" style="display: none;">
                    <option value="0" <?php selected( 0, $post['multiple_product_subscription_length'], true ) ?>><?php _e('Never expire', PMWI_Plugin::TEXT_DOMAIN); ?></option>
                    <?php foreach ( $options as $value => $label ) { ?>
                        <?php if (!empty($value)): ?>
                            <option value="<?php echo $group . '-' . esc_attr( $value ); ?>" <?php selected( $value, $post['multiple_product_subscription_length'], true ) ?>><?php echo esc_html( $label ); ?></option>
                        <?php endif; ?>
                    <?php } ?>
                </select>
            <?php } ?>
            <select class="subscription_length-xpath" style="display: none;">
                <option value="0" <?php selected( 0, $post['multiple_product_subscription_length'], true ) ?>><?php _e('Never expire', PMWI_Plugin::TEXT_DOMAIN); ?></option>
                <?php foreach ( $intervals as $group => $options ) { ?>
                    <optgroup label="<?php echo $group;?>">
                        <?php foreach ( $options as $value => $label ) { ?>
                            <?php if (!empty($value)): ?>
                                <option value="<?php echo $group . '-' . esc_attr( $value ); ?>" <?php selected( $value, $post['multiple_product_subscription_length'], true ) ?>><?php echo esc_html( $label ); ?></option>
                            <?php endif; ?>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
            <select class="select short" name="multiple_product_subscription_length">
                <option value="0" <?php selected( 0, $post['multiple_product_subscription_length'], true ) ?>><?php _e('Never expire', PMWI_Plugin::TEXT_DOMAIN); ?></option>
                <?php foreach ( $intervals as $group => $options ) { ?>
                    <optgroup label="<?php echo $group;?>">
                        <?php foreach ( $options as $value => $label ) { ?>
                            <?php if (!empty($value)): ?>
                                <option value="<?php echo $group . '-' . esc_attr( $value ); ?>" <?php selected( $value, $post['multiple_product_subscription_length'], true ) ?>><?php echo esc_html( $label ); ?></option>
                            <?php endif; ?>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>
        <span class="wpallimport-clear"></span>
        <div class="form-field wpallimport-radio-field">
            <input type="radio" id="multiple_product_subscription_length_no" class="switcher" name="is_multiple_product_subscription_length" value="no" <?php echo 'no' == $post['is_multiple_product_subscription_length'] ? 'checked="checked"': '' ?>/>
            <label for="multiple_product_subscription_length_no"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN ); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Return a value matching one of the options in the pulldown: 1 week, 2 months, etc. Use 0 for \'Never expire\'. <br><br>The period interval must match the values chosen for the billing interval. So if the subscription renews every week, it must expire after a given number of weeks.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></label>
            <span class="wpallimport-clear"></span>
            <div class="switcher-target-multiple_product_subscription_length_no set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_subscription_length" style="max-width:300px;" value="<?php echo esc_attr($post['single_product_subscription_length']) ?>"/>
				</span>
            </div>
        </div>
    </div>
    <hr>
    <p class="form-field">
        <label><?php printf(__("Sign-up fee (%s)", PMWI_Plugin::TEXT_DOMAIN), get_woocommerce_currency_symbol()); ?></label>
        <input type="text" class="short" name="single_product_subscription_sign_up_fee" value="<?php echo esc_attr($post['single_product_subscription_sign_up_fee']) ?>"/>
    </p>
    <span class="wpallimport-clear"></span>
    <div class="subscription-trial">
        <p class="form-field woocommerce-group-label"><?php _e("Free trial", PMWI_Plugin::TEXT_DOMAIN); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('An optional period of time to wait before charging the first recurring payment. Any sign up fee will still be charged at the outset of the subscription. The trial period can not exceed: 90 days, 52 weeks, 24 months or 5 years.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></p>
        <div class="form-field wpallimport-radio-field">
            <input type="radio" id="multiple_product_subscription_trial_period_yes" class="switcher" name="is_multiple_product_subscription_trial_period" value="yes" <?php echo 'no' != $post['is_multiple_product_subscription_trial_period'] ? 'checked="checked"': '' ?>/>

            <select class="select short" name="multiple_product_subscription_trial_period">
                <option value="0" <?php selected( 0, $post['multiple_product_subscription_trial_period'], true ) ?>><?php _e('No free trial', PMWI_Plugin::TEXT_DOMAIN); ?></option>
                <?php foreach ( $intervals as $group => $options ) { ?>
                    <optgroup label="<?php echo $group;?>">
                        <?php foreach ( $options as $value => $label ) { ?>
                            <?php if (!empty($value)): ?>
                                <option value="<?php echo $group . '-' . esc_attr( $value ); ?>" <?php selected( $group . '-' . esc_attr( $value ), $post['multiple_product_subscription_trial_period'], true ) ?>><?php echo esc_html( $label ); ?></option>
                            <?php endif; ?>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>
        <span class="wpallimport-clear"></span>
        <div class="form-field wpallimport-radio-field">
            <input type="radio" id="multiple_product_subscription_trial_period_no" class="switcher" name="is_multiple_product_subscription_trial_period" value="no" <?php echo 'no' == $post['is_multiple_product_subscription_trial_period'] ? 'checked="checked"': '' ?>/>
            <label for="multiple_product_subscription_trial_period_no"><?php _e('Set with XPath', PMWI_Plugin::TEXT_DOMAIN ); ?><a href="#help" class="wpallimport-help" style="top: 0;" title="<?php _e('Return a value matching one of the options in the pulldown: 1 week, 2 months, etc.', PMWI_Plugin::TEXT_DOMAIN) ?>">?</a></label>
            <span class="wpallimport-clear"></span>
            <div class="switcher-target-multiple_product_subscription_trial_period_no set_with_xpath">
				<span class="wpallimport-slide-content" style="padding-left:0;">
					<input type="text" class="smaller-text" name="single_product_subscription_trial_period" style="max-width:300px;" value="<?php echo esc_attr($post['single_product_subscription_trial_period']) ?>"/>
				</span>
            </div>
        </div>
    </div>
</div>