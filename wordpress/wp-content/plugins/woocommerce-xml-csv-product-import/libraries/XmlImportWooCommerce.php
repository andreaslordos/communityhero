<?php

use wpai_woocommerce_add_on\libraries\importer\Importer;
use wpai_woocommerce_add_on\libraries\parser\ParserInterface;

require_once dirname(__FILE__) . '/XmlImportWooCommerceInterface.php';

/**
 * Class XmlImportWooCommerce
 */
abstract class XmlImportWooCommerce implements XmlImportWooCommerceInterface {

    /**
     * @var ParserInterface
     */
    public $parser;

    /**
     * @var Importer
     */
    public $importer;

    /**
     * @var
     */
    public $import;

    /**
     * @var
     */
    public $xml;

    /**
     * @var
     */
    public $logger;

    /**
     * @var
     */
    public $count;

    /**
     * @var
     */
    public $chunk;

    /**
     * @var
     */
    public $xpath;

    /**
     * @var
     */
    public $wpdb;

    /**
     * @var
     */
    public $data;

    /**
     * @var bool
     */
    public $articleData = FALSE;

    /**
     * XmlImportWooCommerceShopOrder constructor.
     * @param $options
     */
    public function __construct($options) {
        global $wpdb;
        $this->import = $options['import'];
        $this->count = $options['count'];
        $this->xml = $options['xml'];
        $this->logger = $options['logger'];
        $this->chunk = $options['chunk'];
        $this->xpath = $options['xpath_prefix'];
        $this->wpdb = $wpdb;
    }
}