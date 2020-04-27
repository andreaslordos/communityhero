<?php

namespace wpai_woocommerce_add_on\libraries\importer;

require_once dirname(__FILE__) . '/ImportProduct.php';

/**
 *
 * Import Simple Product
 *
 * Class ImportSimpleProduct
 * @package wpai_woocommerce_add_on\libraries\importer
 */
class ImportSimpleProduct extends ImportProduct {

    /**
     * @var string
     */
    protected $productType = 'simple';

    /**
     * @return void
     */
    public function import() {
        parent::import();
    }
}