<?php

namespace wpai_woocommerce_add_on\libraries\importer;

require_once dirname(__FILE__) . '/ImportBase.php';
require_once dirname(__FILE__) . '/ImporterInterface.php';

abstract class Importer extends ImportBase implements ImporterInterface {

    /**
     * @return ImporterIndex
     */
    public function getIndexObject() {
        return $this->index;
    }
}