<?php

use wpai_woocommerce_add_on\libraries\helpers\ImporterOptions;
use wpai_woocommerce_add_on\libraries\importer\ImporterIndex;
use wpai_woocommerce_add_on\libraries\importer\OrderActions;
use wpai_woocommerce_add_on\libraries\importer\OrdersImporter;
use wpai_woocommerce_add_on\libraries\parser\ParserFactory;

require_once dirname(__FILE__) . '/XmlImportWooCommerce.php';

/**
 * Class XmlImportWooCommerceShopOrder
 */
class XmlImportWooCommerceShopOrder extends XmlImportWooCommerce {

    /**
     * @var OrderActions
     */
    public $actions;

    /**
     * @var ImporterIndex
     */
    public $index;

    /**
     * @var OrdersImporter
     */
    public $importer;

    /**
     * XmlImportWooCommerceShopOrder constructor.
     * @param $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->parser = ParserFactory::generate('orders', $options);
        $this->actions  = new OrderActions($this->parser);
    }

    /**
     * @return mixed
     */
    public function parse() {
        $this->data = $this->parser->parse();
        return $this->data;
    }

    /**
     * @param $importData
     */
    public function import($importData) {

        $order_id = $importData['pid'];

        $this->index = new ImporterIndex($order_id, $importData['i'], $importData['articleData']);
        $this->importer = new OrdersImporter($this->index, new ImporterOptions($this->parser));
        $this->actions->setImporter($this->importer);
        $this->importer->import();

        // Generate order unique key in case it doesn't exist.
        $order_key = get_post_meta($order_id, '_order_key', true);
        if (empty($order_key)) {
            update_post_meta($order_id, '_order_key', 'wc_' . apply_filters( 'woocommerce_generate_order_key', uniqid( 'order_' ) ));
        }

        update_post_meta($order_id, '_order_version', WC()->version);
        $_order_tax = get_post_meta($order_id, '_order_tax', TRUE);
        if (empty($_order_tax)) {
            update_post_meta($order_id, '_order_tax', 0);
        }
        update_post_meta($order_id, '_order_shipping_tax', 0);
    }

    /**
     * @param $importData
     */
    public function after_save_post($importData) {
        $this->importer->afterPostImport();
    }
}