<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\helpers\ImporterOptions;

/**
 * interface ImportBaseInterface
 * @package wpai_woocommerce_add_on\libraries\importer
 */
interface ImportBaseInterface {

    /**
     * @return ImporterOptions
     */
    public function getOptions();

    /**
     * @param $option
     * @return mixed
     */
    public function getValue($option);

    /**
     * @param $option
     * @param $value
     * @return mixed
     */
    public function setValue($option, $value);

    /**
     * @return mixed
     */
    public function getArticle();

    /**
     * @param $option
     * @return mixed
     */
    public function getArticleData($option);

    /**
     * @return mixed
     */
    public function getIndex();

    /**
     * @return \PMXI_Import_Record
     */
    public function getImport();

    /**
     * @return mixed
     */
    public function getLogger();

    /**
     * @return mixed
     */
    public function import();

    /**
     * @param $var
     * @return mixed
     */
    public function filtering($var);
}