<?php

namespace wpai_woocommerce_add_on\libraries\importer;

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/14/17
 * Time: 11:33 AM
 */
/**
 * Interface ImporterInterface
 * @package wpai_woocommerce_add_on\libraries\importer
 */
interface ImporterInterface {

    /**
     * @return mixed
     */
    public function import();

    /**
     * @return mixed
     */
    public function afterPostImport();

}