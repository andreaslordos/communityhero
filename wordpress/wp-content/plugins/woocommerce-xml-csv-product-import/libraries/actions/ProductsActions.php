<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\parser\Parser;
use wpai_woocommerce_add_on\libraries\parser\ProductsParser;

require_once dirname(__FILE__) . '/Actions.php';

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/16/17
 * Time: 3:44 PM
 */

class ProductsActions extends Actions {

    /**
     * ProductsActions constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {
        parent::__construct($parser);
        // Allow import products with duplicate SKU.
        if ($parser->getImport()->options['disable_sku_matching']) {
            add_filter('wc_product_has_unique_sku', array(
                &$this,
                'wc_product_has_unique_sku'
            ), 10, 3);
        }
    }

    /**
     * @param $sku_found
     * @param $product_id
     * @param $sku
     *
     * @return bool
     */
    function wc_product_has_unique_sku($sku_found, $product_id, $sku){
        return FALSE;
    }

    /**
     * @return ProductsParser
     */
    public function getParser() {
        parent::getParser();
    }

    /**
     * @return ImporterInterface
     */
    public function getImporter() {
        return parent::getImporter();
    }
}