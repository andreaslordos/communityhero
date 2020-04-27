<ul style="padding-left: 35px;">
    <?php if ( $post['is_update_status']): ?>
        <li> <?php _e('Order status', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_excerpt']): ?>
        <li> <?php _e('Customer Note', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_dates']): ?>
        <li> <?php _e('Dates', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_billing_details']): ?>
        <li> <?php _e('Billing Details', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_shipping_details']): ?>
        <li> <?php _e('Shipping Details', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_payment']): ?>
        <li> <?php _e('Payment Details', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_notes']): ?>
        <li> <?php _e('Order Notes', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_products']): ?>
        <li>
            <?php
            switch($post['update_products_logic']){
                case 'full_update':
                    _e('Update all products', PMWI_Plugin::TEXT_DOMAIN);
                    break;
                case 'add_new':
                    _e('Don\'t touch existing products, append new products', PMWI_Plugin::TEXT_DOMAIN);
                    break;
            } ?>
        </li>
    <?php endif; ?>
    <?php if ( $post['is_update_fees']): ?>
        <li> <?php _e('Fees Items', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_coupons']): ?>
        <li> <?php _e('Coupon Items', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_shipping']): ?>
        <li> <?php _e('Shipping Items', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_taxes']): ?>
        <li> <?php _e('Tax Items', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_refunds']): ?>
        <li> <?php _e('Refunds', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( $post['is_update_total']): ?>
        <li> <?php _e('Order Total', PMWI_Plugin::TEXT_DOMAIN); ?></li>
    <?php endif; ?>
    <?php if ( ! empty($post['is_update_acf'])): ?>
        <li>
            <?php
            switch($post['update_acf_logic']){
                case 'full_update':
                    _e('All advanced custom fields', PMWI_Plugin::TEXT_DOMAIN);
                    break;
                case 'mapped':
                    _e('Only ACF presented in import options', PMWI_Plugin::TEXT_DOMAIN);
                    break;
                case 'only':
                    printf(__('Only these ACF : %s', PMWI_Plugin::TEXT_DOMAIN), $post['acf_only_list']);
                    break;
                case 'all_except':
                    printf(__('All ACF except these: %s', PMWI_Plugin::TEXT_DOMAIN), $post['acf_except_list']);
                    break;
            } ?>
        </li>
    <?php endif; ?>
    <?php if ( ! empty($post['is_update_custom_fields'])): ?>
        <li>
            <?php
            switch($post['update_custom_fields_logic']){
                case 'full_update':
                    _e('All custom fields', PMWI_Plugin::TEXT_DOMAIN);
                    break;
                case 'only':
                    printf(__('Only these custom fields : %s', PMWI_Plugin::TEXT_DOMAIN), $post['custom_fields_only_list']);
                    break;
                case 'all_except':
                    printf(__('All custom fields except these: %s', PMWI_Plugin::TEXT_DOMAIN), $post['custom_fields_except_list']);
                    break;
            } ?>
        </li>
    <?php endif; ?>
</ul>