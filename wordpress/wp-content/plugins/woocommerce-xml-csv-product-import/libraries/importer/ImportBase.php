<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\helpers\ImporterOptions;
use XmlImportWooCommerceService;

require_once dirname(__FILE__) . '/ImportBaseInterface.php';

/**
 * Class ImportBase
 * @package wpai_woocommerce_add_on\libraries\importer
 */
abstract class ImportBase implements ImportBaseInterface {

    /**
     * @var array
     */
    public $data;

    /**
     * @var ImporterIndex
     */
    public $index;

    /**
     * @var ImporterOptions
     */
    public $options;

    /**
     * @var \wpdb
     */
    public $wpdb;

    /**
     * @var XmlImportWooCommerceService
     */
    public $importService;

    /**
     * ImportOrderBase constructor.
     * @param ImporterIndex $index
     * @param ImporterOptions $options
     * @param array $data
     */
    public function __construct(ImporterIndex $index, ImporterOptions $options, $data = array()) {
        global $wpdb;
        $this->index = $index;
        $this->options = $options;
        $this->data = $data;
        $this->wpdb = $wpdb;
        $this->importService = XmlImportWooCommerceService::getInstance();
    }

    /**
     * @return \XmlImportWooCommerceService
     */
    public function getImportService() {
        return $this->importService;
    }

    /**
     *  Import WooCommerce Entity.
     */
    public function import() {}

    /**
     * @return ImporterOptions
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     *
     * Get parsed data
     *
     * @return mixed
     */
    public function getParsedData() {
        return $this->getOptions()->getParsedData();
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getParsedDataOption($option) {
        return $this->data[$option];
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getValue($option) {
        return isset($this->data[$option]) ? $this->data[$option][$this->index->getIndex()] : '';
    }

    /**
     * @param $option
     * @param $value
     * @return mixed
     */
    public function setValue($option, $value) {
        $this->data[$option][$this->index->getIndex()] = $value;
    }

    /**
     * @return mixed
     */
    public function getArticle() {
        return $this->index->getArticle();
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getArticleData($option) {
        $articleData = $this->getArticle();
        return isset($articleData[$option]) ? $articleData[$option] : FALSE;
    }

    /**
     * @return mixed
     */
    public function getIndex() {
        return $this->index->getIndex();
    }

    /**
     * @return mixed
     */
    public function getPid(){
        return $this->index->getPid();
    }

    /**
     * @return \PMXI_Import_Record
     */
    public function getImport() {
        return $this->getOptions()->getParser()->getImport();
    }

    /**
     * @return mixed
     */
    public function getLogger() {
        return $this->getOptions()->getParser()->getLogger();
    }

    /**
     * @param $var
     * @return bool
     */
    public function filtering($var) {
        return ("" == $var) ? FALSE : TRUE;
    }

    /**
     *
     * Add message into import log.
     *
     * @param $msg
     */
    public function log($msg) {
        $this->getLogger() and call_user_func($this->getLogger(), $msg);
    }
}