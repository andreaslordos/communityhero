<?php

namespace wpai_woocommerce_add_on\libraries\parser;

require_once dirname(__FILE__) . '/ProductsParserBase.php';

/**
 * Class ProductsParser
 * @package wpai_woocommerce_add_on\libraries\parser
 */
class ProductsParser extends ProductsParserBase  {

    /**
     *
     * Parse WooCommerce Products Import Template.
     *
     * @return array
     */
    public function parse() {
        $this->data = array();
        $this->getChunk() == 1 && $this->log(__('Composing product data...', \PMWI_Plugin::TEXT_DOMAIN));
        foreach ($this->getParsingWorkflow() as $callback => $options) {
            if (!empty($options)) {
                array_map(array($this, $callback), $options);
                continue;
            }
            call_user_func(array($this, $callback));
        }
        $options = $this->getOptionsType_6();
        foreach ($options as $m_option => $s_option) {
            $this->parseOptionType_6($m_option, $s_option);
        }
        // Remove all temporary files created.
        $this->unlinkTempFiles();
        return $this->data;
    }

    /**
     * Get parsing workflow, where keys are callbacks for values.
     *
     * @return array
     */
    public function getParsingWorkflow() {
        return array(
            'parseOptionType_1' => array(
                'product_virtual',
                'product_downloadable',
                'product_enabled',
                'product_featured',
                'product_visibility',
                'product_enable_reviews',
                'product_manage_stock'
            ),
            'parseOptionType_2' => array(
                'product_id',
                'product_parent_id',
                'product_id_first_is_parent_id',
                'product_id_first_is_parent_title',
                'product_id_first_is_variation',
                'product_first_is_parent_id_parent_sku',
                'product_first_is_parent_title_parent_sku'
            ),
            'parseOptionType_3' => array(
                'product_sku',
                'product_variation_description',
                'product_url',
                'product_button_text',
                'product_regular_price',
                'product_sale_price',
                'product_whosale_price',
                'product_files',
                'product_files_names',
                'product_download_limit',
                'product_download_expiry',
                'product_download_type',
                'product_stock_qty',
                'product_low_stock_amount',
                'product_weight',
                'product_length',
                'product_width',
                'product_height',
                'product_up_sells',
                'product_cross_sells',
                'product_purchase_note',
                'product_menu_order',
                'product_subscription_price',
                'product_subscription_sign_up_fee',
            ),
            'parseOptionType_4' => array(
                'sale_price_dates_from',
                'sale_price_dates_to'
            ),
            'parseOptionType_5' => array(
                'type',
                'tax_status',
                'tax_class',
                'shipping_class',
                'subscription_period',
                'subscription_period_interval',
                'subscription_trial_period',
                'subscription_length',
                'subscription_limit',
            ),
            'parseStockStatus' => array(),
            'parseGroupingProducts' => array(),
            'parseVariationsManageStock' => array(),
            'parseVariationsStockQty' => array(),
            'parseVariationsStockStatus' => array(),
            'parseVariationsEnabled' => array(),
            'parseProductMatching' => array(),
            'parseMatchingExistingParentProducts' => array(),
            'parseAttributes' => array()
        );
    }

    /**
     * @return array
     */
    protected function getOptionsType_6() {
        return array(
            'product_allow_backorders' => 'single_product_allow_backorders',
            'product_sold_individually' => 'single_product_sold_individually'
        );
    }
}