<?php

namespace wpai_woocommerce_add_on\libraries\parser;

use wpai_woocommerce_add_on\libraries\helpers\ParserOptions;
use XmlImportWooCommerceService;

require_once dirname(__FILE__) . '/ParserInterface.php';

/**
 * Class Parser
 * @package wpai_woocommerce_add_on\libraries\parser
 */
abstract class Parser implements ParserInterface {

    /**
     * @var ParserOptions
     */
    public $options;

    /**
     * An array with parsed data.
     *
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $tmp_files;

    /**
     * @var XmlImportWooCommerceService
     */
    public $importService;

    /**
     * ProductsParser constructor.
     *
     * @param ParserOptions $options
     */
    public function __construct(ParserOptions $options) {
        $this->options = $options;
        $this->importService = XmlImportWooCommerceService::getInstance();
    }

    /**
     * @return \XmlImportWooCommerceService
     */
    public function getImportService() {
        return $this->importService;
    }

    /**
     * @param $option
     * @param $index
     * @return mixed
     */
    public function getValue($option, $index) {
        return $this->data[$option][$index];
    }

    /**
     * @return ParserOptions
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return \PMXI_Import_Record
     */
    public function getImport() {
        return $this->getOptions()->import;
    }

    /**
     * @param \PMXI_Import_Record $import
     */
    public function setImport($import) {
        $this->getOptions()->import = $import;
    }

    /**
     * @return string
     */
    public function getXml() {
        return $this->getOptions()->xml;
    }

    /**
     * @param string $xml
     */
    public function setXml($xml) {
        $this->getOptions()->xml = $xml;
    }

    /**
     * @return mixed
     */
    public function getLogger() {
        return $this->getOptions()->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger) {
        $this->getOptions()->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getCount() {
        return $this->getOptions()->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count) {
        $this->getOptions()->count = $count;
    }

    /**
     * @return mixed
     */
    public function getChunk() {
        return $this->getOptions()->chunk;
    }

    /**
     * @param mixed $chunk
     */
    public function setChunk($chunk) {
        $this->getOptions()->chunk = $chunk;
    }

    /**
     * @return mixed
     */
    public function getXpath() {
        return $this->getOptions()->xpath;
    }

    /**
     * @param mixed $xpath
     */
    public function setXpath($xpath) {
        $this->getOptions()->xpath = $xpath;
    }

    /**
     * @return mixed
     */
    public function getWpdb() {
        return $this->getOptions()->wpdb;
    }

    /**
     * @param mixed $wpdb
     */
    public function setWpdb($wpdb) {
        $this->getOptions()->wpdb = $wpdb;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @param $price
     * @return mixed
     */
    protected function preparePrice($price) {
        return $this->getImportService()->getPriceService()->preparePrice( $price,
            $this->getImport()->options['disable_prepare_price'],
            $this->getImport()->options['prepare_price_to_woo_format'],
            $this->getImport()->options['convert_decimal_separator']
        );
    }

    /**
     * @param $price
     * @param $field
     * @return float|int|string
     */
    protected function adjustPrice($price, $field) {
        return $this->getImportService()->getPriceService()->adjustPrice($price, $field);
    }

    /**
     * Remove all temporary created files.
     */
    public function unlinkTempFiles() {
        foreach ($this->tmp_files as $file) { // remove all temporary files created
            unlink($file);
        }
        $this->tmp_files = array();
    }

    /**
     * Add message into import log.
     *
     * @param $msg
     */
    public function log($msg) {
        $this->getLogger() and call_user_func($this->getLogger(), $msg);
    }
}