<?php

namespace wpai_woocommerce_add_on\libraries\helpers;

use wpai_woocommerce_add_on\libraries\parser\ParserInterface;

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/14/17
 * Time: 11:49 AM
 */
class ImporterOptions {

    /**
     * @var ParserInterface
     */
    public $parser;

    /**
     * ParserOptions constructor.
     * @param ParserInterface $parser
     */
    public function __construct($parser) {
        $this->parser = $parser;
    }

    /**
     * @return ParserInterface
     */
    public function getParser() {
        return $this->parser;
    }

    /**
     * @param ParserInterface $parser
     */
    public function setParser($parser) {
        $this->parser = $parser;
    }

    /**
     * @return mixed
     */
    public function getParsedData() {
        return $this->getParser()->getData();
    }
}
