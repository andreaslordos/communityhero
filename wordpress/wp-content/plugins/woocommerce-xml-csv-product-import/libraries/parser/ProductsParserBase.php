<?php

namespace wpai_woocommerce_add_on\libraries\parser;

use XmlImportParser;

require_once dirname(__FILE__) . '/Parser.php';

/**
 * Class ProductsParserBase
 * @package wpai_woocommerce_add_on\libraries\parser
 */
abstract class ProductsParserBase extends Parser {

    /**
     * Get complete XPath expression for parser factory.
     *
     * @return string
     */
    public function getCompleteXPath() {
        return $this->getXpath() . $this->getImport()->xpath;
    }

    /**
     * @param $option
     * @param $m_option
     * @param $s_option
     */
    public function parseXPathOption($option, $m_option, $s_option) {
        try {
            if ($this->getImport()->options[$m_option] == 'xpath' and "" != $this->getImport()->options[$s_option]) {
                $this->data[$option] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options[$s_option], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            else {
                $this->getCount() and $this->data[$option] = array_fill(0, $this->getCount(), $this->getImport()->options[$m_option]);
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse options type #1.
     *
     * @param $option
     */
    public function parseOptionType_1($option) {
        $this->parseXPathOption($option, 'is_' . $option, 'single_' . $option);
    }

    /**
     * Parse options type #2.
     *
     * @param $option
     */
    public function parseOptionType_2($option) {
        try {
            if (isset($this->getImport()->options['single_' . $option]) && "" != $this->getImport()->options['single_' . $option]) {
                $this->data['single_' . preg_replace("%id$%", "ID", $option)] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_' . $option], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            else {
                $this->getCount() and $this->data['single_' . preg_replace("%id$%", "ID", $option)] = array_fill(0, $this->getCount(), "");
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse options type #3.
     *
     * @param $option
     */
    public function parseOptionType_3($option) {
        try {
            if ("" != $this->getImport()->options['single_' . $option]) {
                $this->data[$option] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_' . $option], $file)
                    ->parse();

                $this->tmp_files[] = $file;

                switch ($option) {
                    case 'product_regular_price':
                    case 'product_sale_price':
                        $this->data[$option] = array_map(array(
                            $this,
                            'adjustPrice'
                        ), array_map(array(
                            $this,
                            'preparePrice'
                        ), $this->data[$option]), array_fill(0, $this->getCount(), preg_replace('%product_%', '', $option)));
                        break;
                    default:
                        break;
                }
            }
            else {
                $this->getCount() and $this->data[$option] = array_fill(0, $this->getCount(), "");
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse options type #4.
     *
     * @param $option
     */
    public function parseOptionType_4($option) {
        try {
            if ($this->getImport()->options['is_regular_price_shedule'] and "" != $this->getImport()->options['single_' . $option]) {
                $this->data['product_' . $option] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_' . $option], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            else {
                $this->getCount() and $this->data['product_' . $option] = array_fill(0, $this->getCount(), "");
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse options type #5.
     *
     * @param $option
     */
    public function parseOptionType_5($option) {
        try {
            $option_name = ($option == 'type') ? 'types' : $option;
            if ($this->getImport()->options['is_multiple_product_' . $option] != 'yes' and "" != $this->getImport()->options['single_product_' . $option]) {
                $this->data['product_' . $option_name] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_product_' . $option], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            else {
                $this->getCount() and $this->data['product_' . $option_name] = array_fill(0, $this->getCount(), $this->getImport()->options['multiple_product_' . $option]);
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse options type #6.
     *
     * @param $m_option
     * @param $s_option
     */
    public function parseOptionType_6($m_option, $s_option) {
        $this->parseXPathOption($m_option, $m_option, $s_option);
    }

    /**
     *  Parse Product Matching.
     */
    public function parseProductMatching() {
        if ($this->getImport()->options['matching_parent'] != "auto") {
            switch ($this->getImport()->options['matching_parent']) {
                case 'first_is_parent_id':
                    $this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_ID'];
                    break;
                case 'first_is_parent_title':
                    $this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_title'];
                    break;
                case 'first_is_variation':
                    $this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_variation'];
                    break;
            }
        }
    }

    /**
     *  Parse Variations Manage Stock.
     */
    public function parseVariationsManageStock() {
        $this->parseXPathOption('v_product_manage_stock', 'is_variation_product_manage_stock', 'single_variation_product_manage_stock');
    }

    /**
     *  Parse Variations Enabled.
     */
    public function parseVariationsEnabled() {
        $this->parseXPathOption('v_product_enabled', 'is_variable_product_enabled', 'single_variable_product_enabled');
    }

    /**
     *  Parse Variations Stock Quantity.
     */
    public function parseVariationsStockQty() {
        try {
            if ($this->getImport()->options['variation_stock'] != "") {
                $this->data['v_stock'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['variation_stock'], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            else {
                $this->getCount() and $this->data['v_stock'] = array_fill(0, $this->getCount(), '');
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse Grouping products.
     */
    public function parseGroupingProducts() {
        try {
            if ($this->getImport()->options['is_multiple_grouping_product'] != 'yes') {
                if ($this->getImport()->options['grouping_indicator'] == 'xpath') {
                    if ("" != $this->getImport()->options['single_grouping_product']) {
                        $this->data['product_grouping_parent'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_grouping_product'], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                    }
                    else {
                        $this->getCount() and $this->data['product_grouping_parent'] = array_fill(0, $this->getCount(), $this->getImport()->options['multiple_grouping_product']);
                    }
                }
                else {
                    if ("" != $this->getImport()->options['custom_grouping_indicator_name'] and "" != $this->getImport()->options['custom_grouping_indicator_value']) {
                        $this->data['custom_grouping_indicator_name'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['custom_grouping_indicator_name'], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                        $this->data['custom_grouping_indicator_value'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['custom_grouping_indicator_value'], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                    }
                    else {
                        $this->getCount() and $this->data['custom_grouping_indicator_name'] = array_fill(0, $this->getCount(), "");
                        $this->getCount() and $this->data['custom_grouping_indicator_value'] = array_fill(0, $this->getCount(), "");
                    }
                }
            }
            else {
                $this->getCount() and $this->data['product_grouping_parent'] = array_fill(0, $this->getCount(), $this->getImport()->options['multiple_grouping_product']);
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse matching existing parent products.
     */
    public function parseMatchingExistingParentProducts() {
        try {
            if ($this->getImport()->options['existing_parent_product_matching_logic'] == 'title') {
                if ("" != $this->getImport()->options['existing_parent_product_title']) {
                    $this->data['existing_parent_product'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['existing_parent_product_title'], $file)
                        ->parse();
                    $this->tmp_files[] = $file;
                }
            }
            else {
                if ("" != $this->getImport()->options['existing_parent_product_cf_name'] and "" != $this->getImport()->options['existing_parent_product_cf_value']) {
                    $this->data['existing_parent_product_cf_name'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['existing_parent_product_cf_name'], $file)
                        ->parse();
                    $this->tmp_files[] = $file;
                    $this->data['existing_parent_product_cf_value'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['existing_parent_product_cf_value'], $file)
                        ->parse();
                    $this->tmp_files[] = $file;
                }
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse product's stock status.
     */
    public function parseStockStatus() {
        try {
            // Composing product Stock status.
            if ($this->getImport()->options['product_stock_status'] == 'xpath' and "" != $this->getImport()->options['single_product_stock_status']) {
                $this->data['product_stock_status'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_product_stock_status'], $file)
                    ->parse();
                $this->tmp_files[] = $file;
            }
            elseif ($this->getImport()->options['product_stock_status'] == 'auto') {
                $this->getCount() and $this->data['product_stock_status'] = array_fill(0, $this->getCount(), $this->getImport()->options['product_stock_status']);
                $nostock = absint(max(get_option('woocommerce_notify_no_stock_amount'), 0));
                foreach ($this->data['product_stock_qty'] as $key => $value) {
                    if ($this->data['product_manage_stock'][$key] == 'yes') {
                        $this->data['product_stock_status'][$key] = (((int) $value === 0 or (int) $value <= $nostock) and $value != "") ? 'outofstock' : 'instock';
                    }
                    else {
                        $this->data['product_stock_status'][$key] = 'instock';
                    }
                }
            }
            else {
                $this->getCount() and $this->data['product_stock_status'] = array_fill(0, $this->getCount(), $this->getImport()->options['product_stock_status']);
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse Stock Status for variations.
     */
    public function parseVariationsStockStatus() {
        try {
            if ($this->getImport()->options['variation_stock_status'] == 'xpath' and "" != $this->getImport()->options['single_variation_stock_status']) {
                $this->data['v_stock_status'] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['single_variation_stock_status'], $file)->parse();
                $this->tmp_files[] = $file;
            }
            elseif ($this->getImport()->options['variation_stock_status'] == 'auto') {
                $this->getCount() and $this->data['v_stock_status'] = array_fill(0, $this->getCount(), $this->getImport()->options['variation_stock_status']);
                $nostock = absint(max(get_option('woocommerce_notify_no_stock_amount'), 0));
                foreach ($this->data['v_stock'] as $key => $value) {
                    if ($this->data['v_product_manage_stock'][$key] == 'yes') {
                        $this->data['v_stock_status'][$key] = (((int) $value === 0 or (int) $value <= $nostock) and $value != "") ? 'outofstock' : 'instock';
                    }
                    else {
                        $this->data['v_stock_status'][$key] = 'instock';
                    }
                }
            }
            else {
                $this->getCount() and $this->data['v_stock_status'] = array_fill(0, $this->getCount(), $this->getImport()->options['variation_stock_status']);
            }
        }
        catch (\Exception $e) {
            $this->log('<b>ERROR:</b> ' . $e->getMessage());
        }
    }

    /**
     * Parse Product Attributes.
     */
    public function parseAttributes() {

        $attribute_keys = array();
        $attribute_values = array();

        $attribute_options = array(
            'in_variations' => array(),
            'is_visible' => array(),
            'is_taxonomy' => array(),
            'is_create_terms' => array()
        );

        if (!empty($this->getImport()->options['attribute_name'][0])) {
            foreach ($this->getImport()->options['attribute_name'] as $j => $attribute_name) {
                if ($attribute_name == "") {
                    continue;
                }
                try {
                    $attribute_keys[$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $attribute_name, $file)
                        ->parse();
                    $this->tmp_files[] = $file;
                    $attribute_values[$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['attribute_value'][$j], $file)
                        ->parse();
                    $this->tmp_files[] = $file;

                    if (empty($this->getImport()->options['is_advanced'][$j])) {
                        $attribute_options['in_variations'][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['in_variations'][$j], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                        $attribute_options['is_visible'][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['is_visible'][$j], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                        $attribute_options['is_taxonomy'][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['is_taxonomy'][$j], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                        $attribute_options['is_create_terms'][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['create_taxonomy_in_not_exists'][$j], $file)
                            ->parse();
                        $this->tmp_files[] = $file;
                    }
                    else {
                        $options = array(
                            'in_variations',
                            'is_visible',
                            'is_taxonomy',
                            'is_create_terms'
                        );

                        foreach ($options as $option) {
                            if ($this->getImport()->options['advanced_' . $option][$j] == 'xpath' and "" != $this->getImport()->options['advanced_' . $option . '_xpath'][$j]) {
                                $attribute_options[$option][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['advanced_' . $option . '_xpath'][$j], $file)
                                    ->parse();
                                $this->tmp_files[] = $file;
                            }
                            else {
                                $attribute_options[$option][$j] = XmlImportParser::factory($this->getXml(), $this->getCompleteXPath(), $this->getImport()->options['advanced_' . $option][$j], $file)
                                    ->parse();
                                $this->tmp_files[] = $file;
                            }

                            foreach ($attribute_options[$option][$j] as $key => $value) {
                                if (!in_array($value, array('yes', 'no'))) {
                                    $attribute_options[$option][$j][$key] = 1;
                                }
                                else {
                                    $attribute_options[$option][$j][$key] = ($value == 'yes') ? 1 : 0;
                                }
                            }
                        }
                    }
                }
                catch(\Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        }

        // Serialized attributes for product variations.
        $this->data['serialized_attributes'] = array();

        if (!empty($attribute_keys)) {
            foreach ($attribute_keys as $j => $attribute_name) {
                $this->data['serialized_attributes'][] = array(
                    'names' => $attribute_name,
                    'value' => $attribute_values[$j],
                    'is_visible' => $attribute_options['is_visible'][$j],
                    'in_variation' => $attribute_options['in_variations'][$j],
                    'in_taxonomy' => $attribute_options['is_taxonomy'][$j],
                    'is_create_taxonomy_terms' => $attribute_options['is_create_terms'][$j]
                );
            }
        }
    }
}