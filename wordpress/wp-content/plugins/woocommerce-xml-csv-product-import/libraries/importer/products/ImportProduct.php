<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\helpers\ImporterOptions;

require_once dirname(__FILE__) . '/ImportProductBase.php';

/**
 * Class ImportProduct
 * @package wpai_woocommerce_add_on\libraries\importer
 */
abstract class ImportProduct extends ImportProductBase {

    /**
     * @var \WC_Product
     */
    public $product;

    /**
     * @var array
     */
    public $productProperties = array();

    /**
     * @var bool
     */
    public $isNewProduct;

    /**
     * @var bool
     */
    public $downloadable;

    /**
     * @var bool
     */
    public $virtual;

    /**
     * @var bool
     */
    public $featured;

    /**
     * @var string
     */
    protected $productType;

    /**
     * ImportProduct constructor.
     * @param \wpai_woocommerce_add_on\libraries\importer\ImporterIndex $index
     * @param \wpai_woocommerce_add_on\libraries\helpers\ImporterOptions $options
     * @param array $data
     */
    public function __construct(ImporterIndex $index, ImporterOptions $options, array $data) {
        parent::__construct($index, $options, $data);
        $productID = $this->getArticleData('ID');
        $this->isNewProduct = empty($productID) ? TRUE : FALSE;
        $this->downloadable = $this->getValue('product_downloadable') == 'yes';
        $this->virtual = $this->getValue('product_virtual') == 'yes';
        $this->featured = $this->getValue('product_featured') == 'yes';
    }

    /**
     *
     * @return mixed
     */
    public function import() {
        $this->setProperties();
        $this->save();
        if (!in_array($this->productType, ['variable', 'product_variation']) && $this->getImportService()->isUpdateDataAllowed('is_update_attributes', $this->isNewProduct())) {
            $this->getImportService()->recountAttributes($this->product);
        }
    }

    /**
     *  Save product data into database.
     */
    public function save() {
        $this->product->save();
        wc_delete_product_transients($this->product->get_id());
    }

    /**
     * @return mixed
     */
    public function setProperties() {
        $this->prepareProperties();
        foreach ($this->productProperties as $property => $value) {
            $this->log(sprintf(__('Property `%s` updated with value `%s`', \PMWI_Plugin::TEXT_DOMAIN), $property, maybe_serialize($value)));
        }
        $errors = $this->product->set_props($this->productProperties);
        if (is_wp_error($errors)) {
            $this->log('<b>ERROR:</b> ' . $errors->get_error_message());
        }
    }

    /**
     * @return array
     */
    public function getProperties() {
        return $this->productProperties;
    }

    /*
	|--------------------------------------------------------------------------
	| Product Properties Methods
	|--------------------------------------------------------------------------
	*/

    /**
     *
     * Define all product properties.
     *
     * @return mixed
     */
    public function prepareProperties(){
        $this->prepareGeneralProperties();
        $this->prepareInventoryProperties();
        $this->prepareShippingProperties();
        $this->prepareLinkedProducts();
        $this->prepareAttributesProperties();
        $this->prepareAdvancedProperties();
    }

    /**
     *  Define general properties.
     */
    public function prepareGeneralProperties(){
        // Prices.
        $this->setProperty('price', wc_clean( $this->getValue('product_regular_price') ));
        $this->setProperty('regular_price', wc_clean( $this->getValue('product_regular_price') ));
        $this->setProperty('sale_price', wc_clean( $this->getValue('product_sale_price') ));
        $this->setProperty('date_on_sale_from', wc_clean( $this->getValue('product_sale_price_dates_from') ));
        $this->setProperty('date_on_sale_to', wc_clean( $this->getValue('product_sale_price_dates_to') ));
        // Product properties.
        $this->setProperty('downloadable', $this->isDownloadable());
        $this->setProperty('virtual', $this->isVirtual());
        $this->setProperty('featured', $this->isFeatured());
        // Validate catalog visibility.
        $catalog_visibility = strtolower(wc_clean( $this->getValue('product_visibility') ));
        $options = array_keys( wc_get_product_visibility_options() );
        if ( ! in_array( $catalog_visibility, $options, true ) ) {
            $catalog_visibility = 'visible';
        }
        $this->setProperty('catalog_visibility', $catalog_visibility);
        $this->prepareDownloadableProperties();
        $this->prepareTaxProperties();
    }

        /**
         *  Define general -> downloadable properties.
         */
        public function prepareDownloadableProperties(){
            // Downloadable options.
            if ( $this->isDownloadable() ) {
                $_download_limit = absint( $this->getValue('product_download_limit') );
                if (!$_download_limit) {
                    $_download_limit = ''; // 0 or blank = unlimited
                }
                $this->setProperty('download_limit', $_download_limit);
                $_download_expiry = absint( $this->getValue('product_download_expiry') );
                if (!$_download_expiry) {
                    $_download_expiry = ''; // 0 or blank = unlimited
                }
                $this->setProperty('download_expiry', $_download_expiry);
                // File paths will be stored in an array keyed off md5(file path).
                if ($this->getValue('product_files')) {
                    $_file_paths = array();
                    $file_paths = explode( $this->getImport()->options['product_files_delim'] , $this->getValue('product_files') );
                    $file_names = explode( $this->getImport()->options['product_files_names_delim'] , $this->getValue('product_files_names') );
                    foreach ( $file_paths as $fn => $file_path ) {
                        $file_path = trim( $file_path );
                        $_file_paths[ md5( $file_path ) ] = array(
                            'download_id' => md5( $file_path ),
                            'name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)),
                            'file' => $file_path
                        );
                    }
                    $this->setProperty('downloads', $_file_paths);
                    // Sync file download permissions in related orders.
                    if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_downloads')) {
                        // Get all product related orders.
                        $orders = $this->getImportService()->getOrdersIdsByProductId($this->getPid());
                        if (!empty($orders)) {
                            global $wpdb;
                            foreach ($orders as $orderID) {
                                $order = new \WC_Order($orderID);
                                foreach ($_file_paths as $download_id => $download_data) {
                                    // Grant permission if it doesn't already exist.
                                    if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1=1 FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $this->getPid(), $download_id ) ) ) {
                                        wc_downloadable_file_permission( $download_id, $this->getPid(), $order );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         *  Define general -> tax properties.
         */
        public function prepareTaxProperties(){
            $tax_status = wc_clean($this->getValue('product_tax_status'));
            $tax_class = strtolower($this->getValue('product_tax_class') == 'standard' ? '' : wc_clean($this->getValue('product_tax_class')));
            $this->setProperty('tax_status', $tax_status != '' ? $tax_status : null);
            $this->setProperty('tax_class', $tax_class);
        }

    /**
     *  Define inventory properties.
     */
    public function prepareInventoryProperties() {
        $this->prepareSKU();
        $this->setProperty('manage_stock', $this->getValue('product_manage_stock') == 'yes');
        $backorders = $this->getValue('product_allow_backorders');
        $this->setProperty('backorders', $backorders != '' ? wc_clean($backorders) : null);
        $this->setProperty('stock_status', wc_clean($this->getValue('product_stock_status')));
        $this->setProperty('stock_quantity', $this->getStockQuantity());
        $this->setProperty('sold_individually', 'yes' == $this->getValue('product_sold_individually'));
        // Do not set Low stock threshold for variations.
        if (!$this->getProduct() instanceof \WC_Product_Variation) {
            $this->setProperty('low_stock_amount', $this->getValue('product_low_stock_amount'));
        }
    }

    /**
     * @return float|int
     */
    public function getStockQuantity() {
        return wc_stock_amount($this->getValue('product_stock_qty'));
    }

        /**
         *  Define product SKU.
         */
        public function prepareSKU() {
            $this->setProperty('sku', $this->generateSKU() );
        }

    /**
     *  Define shipping properties.
     */
    public function prepareShippingProperties() {
        $shippingClass = $this->getShippingClass();
        if (empty($shippingClass)) {
            $shippingClass = -1;
        }
        $this->setProperty('shipping_class_id', absint($shippingClass));
        $this->prepareDimensions();
    }

        /**
         *  Define dimensions properties.
         */
        protected function prepareDimensions() {
            if ( ! $this->isVirtual() ) {
                $this->setProperty('weight', stripslashes($this->getValue('product_weight')));
                $this->setProperty('length', stripslashes($this->getValue('product_length')));
                $this->setProperty('width', stripslashes($this->getValue('product_width')));
                $this->setProperty('height', stripslashes($this->getValue('product_height')));
            } else {
                $this->setProperty('weight', '' );
                $this->setProperty('length', '' );
                $this->setProperty('width', '' );
                $this->setProperty('height', '' );
            }
        }

    /**
     *  Define linked properties.
     */
    public function prepareLinkedProducts() {
        // Upsells.
        if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_upsell_ids')) {
            $linked = $this->getLinkedProducts($this->getPid(), $this->getValue('product_up_sells'), '_upsell_ids');
            $this->productProperties['upsell_ids'] = $linked;
        }
        // Cross sells.
        if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_crosssell_ids')) {
            $linked = $this->getLinkedProducts($this->getPid(), $this->getValue('product_cross_sells'), '_crosssell_ids');
            $this->productProperties['cross_sell_ids'] = $linked;
        }
        // Grouping.
        $this->importGrouping();
    }

    /**
     *  Define attributes properties.
     */
    public function prepareAttributesProperties() {
        if (!$this->getImportService()->isUpdateDataAllowed('is_update_attributes', $this->isNewProduct())) {
            return TRUE;
        }
        $attributes = $this->getAttributesProperties();
        $this->setProperty('attributes', $attributes);
    }

    /**
     * @return array \WC_Product_Attribute
     */
    public function getAttributesProperties() {
        $attributes = $this->getAttributesData();
        if (!empty($attributes['attribute_names'])) {
            $attributes = \WC_Meta_Box_Product_Data::prepare_attributes($attributes);
        }
        else {
            $attributes = array();
        }
        return $attributes;
    }

    /**
     * Get attributes data.
     *
     * @return array
     */
    public function getAttributesData() {
        $data = array(
            'attribute_names' => array(),
            'attribute_values' => array(),
            'attribute_visibility' => array(),
            'attribute_variation' => array(),
            'attribute_position' => array()
        );
        $is_any_attribute = apply_filters('wp_all_import_variation_any_attribute', false, $this->getImport()->id);
        $max_attribute_length = apply_filters('wp_all_import_max_woo_attribute_term_length', 199);
        $parsedAttributes = array();
        $attributesToImport = $this->getParsedDataOption('serialized_attributes');
        if (!empty($attributesToImport)) {
            $attribute_position = 0;
            $attributes = [];
            foreach ($attributesToImport as $attributeData) {
                $attributes[] = [
                    'name' => $attributeData['names'][$this->getIndex()],
                    'value' => $attributeData['value'][$this->getIndex()],
                    'in_taxonomy' => $attributeData['in_taxonomy'][$this->getIndex()],
                    'is_create_taxonomy_terms' => $attributeData['is_create_taxonomy_terms'][$this->getIndex()],
                    'is_visible' => $attributeData['is_visible'][$this->getIndex()],
                    'in_variation' => $attributeData['in_variation'][$this->getIndex()],
                ];
            }
            $attributes = apply_filters('wp_all_import_parsed_product_attributes', $attributes, $this->getPid(), $this->getImport()->id);
            $attributes_delimiter = apply_filters('wp_all_import_product_attributes_delimiter', "|", $this->getPid(), $this->getImport()->id);
            foreach ($attributes as $attribute) {
                $real_attr_name = $attribute['name'];
                if (empty($real_attr_name)) {
                    continue;
                }
                $isTaxonomy = intval($attribute['in_taxonomy']);
                $attributeName = ($isTaxonomy) ? wc_attribute_taxonomy_name( $real_attr_name ) : $real_attr_name;
                $isUpdateAttributes = $this->getImportService()->isUpdateAttribute($attributeName, $this->isNewProduct());
                $attribute_position++;
                if ($isUpdateAttributes) {
                    $values = $attribute['value'];
                    if ( $isTaxonomy ) {
                        if ( isset( $attribute['value']) ) {
                            $values = array_map('stripslashes', array_map( 'strip_tags', explode( $attributes_delimiter, $attribute['value'])));
                            // Remove empty items in the array.
                            $values = array_filter( $values, array($this, "filtering") );
                            if (intval($attribute['is_create_taxonomy_terms'])){
                                $real_attr_name = $this->getImportService()->getTaxonomiesService()->createTaxonomy($real_attr_name);
                                $attributeName = wc_attribute_taxonomy_name( $real_attr_name );
                            }
                            if ( ! empty($values) && taxonomy_exists( wc_attribute_taxonomy_name( $real_attr_name ) )){
                                $attr_values = array();
                                foreach ($values as $key => $val) {
                                    $value = substr($val, 0, $max_attribute_length);
                                    $term = get_term_by('name', $value, wc_attribute_taxonomy_name( $real_attr_name ), ARRAY_A);
                                    // For compatibility with WPML plugin.
                                    $term = apply_filters('wp_all_import_term_exists', $term, wc_attribute_taxonomy_name( $real_attr_name ), $value, null);
                                    if ( empty($term) && !is_wp_error($term) ){
                                        $term = is_exists_term($value, wc_attribute_taxonomy_name( $real_attr_name ));
                                        if ( empty($term) && !is_wp_error($term) ){
                                            $term = is_exists_term(htmlspecialchars($value), wc_attribute_taxonomy_name( $real_attr_name ));
                                            if ( empty($term) && !is_wp_error($term) && intval($attribute['is_create_taxonomy_terms'])){
                                                $term = wp_insert_term(
                                                    $value, // the term
                                                    wc_attribute_taxonomy_name( $real_attr_name ) // the taxonomy
                                                );
                                            }
                                        }
                                    }
                                    if (!is_wp_error($term)) {
                                        $attr_values[] = (int) $term['term_id'];
                                    }
                                }
                                $values = $attr_values;
                                $values = array_map( 'intval', $values );
                                $values = array_unique( $values );
                            }
                            else{
                                $values = array();
                            }
                        }
                    }
                    if ($is_any_attribute && count($values) > 1 && $this->getProduct() instanceof \WC_Product_Variation) {
                        $values = '';
                    }
                    $parsedAttributes[strtolower(urlencode($attributeName))] = array(
                        'name' => $attributeName,
                        'value' => $values,
                        'is_visible' => intval($attribute['is_visible']),
                        'in_variation' => intval($attribute['in_variation']),
                        'position' => $attribute_position
                    );
                }
            }
        }

        if (!$this->getProduct() instanceof \WC_Product_Variation) {
            $productAttributes = array();
            $attributes = $this->getProduct()->get_attributes();
            $currentAttributes = get_post_meta($this->getPid(), '_product_attributes', true);
            /** @var \WC_Product_Attribute $attribute */
            foreach ($attributes as $attributeName => $attribute) {
                $isAddNew = FALSE;
                // Don't touch existing attributes, add new attributes.
                if ( ! $this->isNewProduct() && $this->getImport()->options['update_all_data'] == "no" && $this->getImport()->options['is_update_attributes'] && $this->getImport()->options['update_attributes_logic'] == 'add_new') {
                    if (isset($currentAttributes[$attributeName])) {
                        $isAddNew = TRUE;
                    }
                }
                $name = $attribute->is_taxonomy() ? $attributeName : $attribute->get_name();
                if (!$this->getImportService()->isUpdateAttribute($name, $this->isNewProduct()) || $isAddNew) {
                    $productAttributes[$attributeName] = array(
                        'name' => $attribute->is_taxonomy() ? urldecode_deep($attributeName) : $attribute->get_name(),
                        'value' => $attribute->is_taxonomy() ? $attribute->get_options() : implode("|", $attribute->get_options()),
                        'is_visible' => $attribute->get_visible(),
                        'in_variation' => $attribute->get_variation(),
                        'position' => $attribute->get_position()
                    );
                }
                if ($isAddNew && isset($parsedAttributes[$attributeName])) {
                    $productAttributes[$attributeName]['value'] = array_merge($productAttributes[$attributeName]['value'], $parsedAttributes[$attributeName]['value']);
                }
            }
            $parsedAttributes = array_merge($parsedAttributes, $productAttributes);
        }
        // Prepare attributes for response.
        foreach ($parsedAttributes as $parsedAttribute) {
            $data['attribute_names'][] = $parsedAttribute['name'];
            $data['attribute_values'][] = $parsedAttribute['value'];
            $data['attribute_visibility'][] = empty($parsedAttribute['is_visible']) ? NULL : TRUE;
            $data['attribute_variation'][] = empty($parsedAttribute['in_variation']) ? NULL : TRUE;
            $data['attribute_position'][] = $parsedAttribute['position'];
        }
        return $data;
    }

    /**
     *  Define advanced properties.
     */
    public function prepareAdvancedProperties() {
        // Import product comment status.
        $this->setProperty('purchase_note', wp_kses_post(stripslashes($this->getValue('product_purchase_note'))));
        $this->setProperty('reviews_allowed', $this->getValue('product_enable_reviews') == 'yes');
        // Import product menu order.
        $this->setProperty('menu_order', $this->getValue('product_menu_order') != '' ? (int) $this->getValue('product_menu_order') : 0);
        // Import total sales.
        $total_sales = get_post_meta($this->getPid(), 'total_sales', true);
        if ( empty($total_sales)) {
            update_post_meta($this->getPid(), 'total_sales', '0');
        }
    }

    /*
	|--------------------------------------------------------------------------
	| Product Import Methods
	|--------------------------------------------------------------------------
	*/

    /**
     *  Get shipping class.
     */
    private function getShippingClass() {
        $shipping_class = $this->getValue('product_shipping_class');
        if ( $shipping_class != '' && $shipping_class != '-1') {
            if (!is_numeric($shipping_class)){
                $term = is_exists_term( $shipping_class, 'product_shipping_class');
                // For compatibility with WPML plugin.
                $term = apply_filters('wp_all_import_term_exists', $term, 'product_shipping_class', $shipping_class, null);
            }
            else {
                $term = is_exists_term( (int) $shipping_class, 'product_shipping_class');
                if (empty($term) || is_wp_error($term)) {
                    $term = is_exists_term( $shipping_class, 'product_shipping_class');
                }
            }
            // The term to check. Accepts term ID, slug, or name.
            if (!empty($term) && !is_wp_error($term)){
                $shipping_class = (int) $term['term_id'];
            }
            else {
                $term = wp_insert_term($shipping_class, 'product_shipping_class');
                if (!empty($term) && !is_wp_error($term)) {
                    $shipping_class = (int) $term['term_id'];
                }
            }

            if (empty($term) || is_wp_error($term)) {
                $shipping_class = '';
            }
        }
        return $shipping_class;
    }

    /**
     *  Generate product SKU.
     */
    protected function generateSKU() {

        // Unique SKU.
        $newSKU = wc_clean( trim( stripslashes( $this->getValue('product_sku') ) ) );

        if ( ( in_array($this->productType, array('variation', 'variable', 'variable-subscription')) || $this->getValue('product_types') == "variable" || $this->getValue('product_types') == 'variable-subscription' ) && ! $this->getImport()->options['link_all_variations'] ){
            $identity = FALSE;
            switch ($this->getImport()->options['matching_parent']){
                case 'first_is_parent_id':
                    $identity = $this->getValue('single_product_first_is_parent_id_parent_sku');
                    break;
                case 'first_is_variation':
                    $identity = $this->getValue('single_product_first_is_parent_title_parent_sku');
                    break;
            }
            if (!empty($identity)) {
                update_post_meta($this->getPid(), '_parent_sku', $identity);
            }
        }

        if ($newSKU == '' && !$this->getImport()->options['disable_auto_sku_generation']) {
            if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_sku')) {
                if (!empty($this->getImport()->options['unique_key'])) {
                    try {
                        $tmp_files = array();
                        $xpath = $this->getParser()->getXpath() . $this->getImport()->xpath;
                        $unique_keys = \XmlImportParser::factory($this->getParser()
                            ->getXml(), $xpath, $this->getImport()->options['unique_key'], $file)
                            ->parse();
                        $tmp_files[] = $file;
                        foreach ($tmp_files as $file) { // remove all temporary files created
                            @unlink($file);
                        }
                        $newSKU = substr(md5($unique_keys[$this->getIndex()]), 0, 12);
                    }
                    catch(\Exception $e){
                        $this->log('<b>ERROR:</b> ' . $e->getMessage());
                    }
                }

                if ( ( in_array($this->productType, array('variation', 'variable', 'variable-subscription')) || $this->getValue('product_types') == "variable" || $this->getValue('product_types') == "variable-subscription" ) && ! $this->getImport()->options['link_all_variations'] ) {
                    $identity = FALSE;
                    switch ($this->getImport()->options['matching_parent']){
                        case 'first_is_parent_id':
                            $identity = $this->getValue('single_product_first_is_parent_id_parent_sku');
                            break;
                        case 'first_is_variation':
                            $identity = $this->getValue('single_product_first_is_parent_title_parent_sku');
                            break;
                    }
                    if (empty($identity)){
                        update_post_meta($this->getPid(), '_parent_sku', strrev($newSKU));
                    }
                }
            }
        }
        return wc_clean($newSKU);
    }

    /**
     *  Import products grouping.
     */
    protected function importGrouping() {
        // Group products by Parent.
        if (in_array($this->productType, array( 'simple', 'external', 'variable', 'variable-subscription' ))) {
            // Group all product to one parent ( no XPath provided ).
            if ($this->getImport()->options['is_multiple_grouping_product'] != 'yes') {
                // Trying to find parent product according to matching options.
                if ($this->getImport()->options['grouping_indicator'] == 'xpath' && !is_numeric($this->getValue('product_grouping_parent'))) {
                    $post = pmxi_findDuplicates(array(
                        'post_type' => 'product',
                        'ID' => $this->getPid(),
                        'post_parent' => $this->getArticleData('post_parent'),
                        'post_title' => $this->getValue('product_grouping_parent')
                    ));
                    if (!empty($post)) {
                        $this->setValue('product_grouping_parent', $post[0]);
                    }
                    else {
                        $this->setValue('product_grouping_parent', 0);
                    }
                }
                elseif ($this->getImport()->options['grouping_indicator'] != 'xpath') {
                    $post = pmxi_findDuplicates($this->getArticle(), $this->getValue('custom_grouping_indicator_name'), $this->getValue('custom_grouping_indicator_value'), 'custom field');
                    if (!empty($post)) {
                        $this->setValue('product_grouping_parent', array_shift($post));
                    }
                    else {
                        $this->setValue('product_grouping_parent', 0);
                    }
                }
            }
            // Update `children` property of parent product with new item (current product).
            if ($this->getValue('product_grouping_parent') != "" && absint($this->getValue('product_grouping_parent')) > 0) {
                $this->product->set_parent_id(absint( $this->getValue('product_grouping_parent') ));
                $all_grouped_products = get_post_meta($this->getValue('product_grouping_parent'), '_children', true);
                if (empty($all_grouped_products)) {
                    $all_grouped_products = array();
                }
                if (!in_array($this->getPid(), $all_grouped_products)) {
                    $all_grouped_products[] = $this->getPid();
                    update_post_meta($this->getValue('product_grouping_parent'), '_children', $all_grouped_products);
                }
            }
        }
    }

    /*
	|--------------------------------------------------------------------------
	| Getters & Setters
	|--------------------------------------------------------------------------
	*/

    /**
     * @return boolean
     */
    public function isNewProduct() {
        return $this->isNewProduct;
    }

    /**
     * @return boolean
     */
    public function isDownloadable() {
        return $this->downloadable;
    }

    /**
     * @return boolean
     */
    public function isVirtual() {
        return $this->virtual;
    }

    /**
     * @return boolean
     */
    public function isFeatured() {
        return $this->featured;
    }

    /**
     * @return \WC_Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @param \WC_Product $product
     */
    public function setProduct($product) {
        $this->product = $product;
    }

    /**
     * @param $property
     * @param $value
     */
    public function setProperty($property, $value) {
        switch ($property) {
            case 'attributes':
            case 'menu_order':
            case 'catalog_visibility':
                if ($this->getImportService()->isUpdateDataAllowed('is_update_' . $property, $this->isNewProduct())) {
                    $this->productProperties[$property] = $value;
                }
                break;
            case 'featured':
                if ($this->getImportService()->isUpdateDataAllowed('is_update_featured_status', $this->isNewProduct())) {
                    $this->productProperties[$property] = $value;
                }
                break;
            case 'description':
                if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_variation_description')) {
                    $this->productProperties[$property] = $value;
                }
                break;
            case 'stock_status':
                // As of WooCommerce 3.0 stock status is automatically updated when stock is updated.
                if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField('_stock')) {
                    $this->productProperties[$property] = $value;
                }
                break;
            default:
                if ($this->isNewProduct() || $this->getImportService()->isUpdateCustomField($this->getPropertyMetaKey($property))) {
                    $this->productProperties[$property] = $value;
                }
                break;
        }
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    public function getPropertyMetaKey($property) {
        switch ($property) {
            case 'stock_quantity':
                $property = '_stock';
                break;
            case 'date_on_sale_to':
                $property = '_sale_price_dates_to';
                break;
            case 'date_on_sale_from':
                $property = '_sale_price_dates_from';
                break;
        }
        return '_' === substr( $property, 0, 1 ) ? $property : '_' . $property;
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function getProperty($property) {
        return isset($this->productProperties[$property]) ? $this->productProperties[$property] : null;
    }
}
