<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\parser\Parser;

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/16/17
 * Time: 3:44 PM
 */

abstract class Actions {

    /**
     * @var Parser
     */
    public $parser;

    /**
     * @var ImporterInterface
     */
    public $importer;

    /**
     * Actions constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {
        $this->parser = $parser;
    }

    /**
     * @return Parser
     */
    public function getParser() {
        return $this->parser;
    }

    /**
     * @return ImporterInterface
     */
    public function getImporter() {
        return $this->importer;
    }

    /**
     * @param ImporterInterface $importer
     */
    public function setImporter($importer) {
        $this->importer = $importer;
    }
}