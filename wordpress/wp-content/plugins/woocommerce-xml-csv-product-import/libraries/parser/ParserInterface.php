<?php

namespace wpai_woocommerce_add_on\libraries\parser;

use wpai_woocommerce_add_on\libraries\helpers\ParserOptions;

/**
 * Interface ParserInterface
 * @package wpai_woocommerce_add_on\libraries\parser
 */
interface ParserInterface {

    /**
     * @return mixed
     */
    public function parse();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return \PMXI_Import_Record
     */
    public function getImport();

    /**
     * @return ParserOptions
     */
    public function getOptions();

    /**
     * @return mixed
     */
    public function getLogger();

}