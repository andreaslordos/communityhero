<?php

namespace wpai_woocommerce_add_on\libraries\helpers;

/**
 * Class ParserOptions
 * @package wpai_woocommerce_add_on\libraries\helpers
 */
class ParserOptions {

    /**
     * @var \PMXI_Import_Record
     */
    public $import;

    /**
     * @var string
     */
    public $xml;

    /**
     * @var mixed
     */
    public $logger;

    /**
     * @var mixed
     */
    public $count;

    /**
     * @var mixed
     */
    public $chunk;

    /**
     * @var mixed
     */
    public $xpath;

    /**
     * @var \wpdb
     */
    public $wpdb;

    /**
     * ParserOptions constructor.
     * @param array $options
     */
    public function __construct($options) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->import = $options['import'];
        $this->count = $options['count'];
        $this->xml = $options['xml'];
        $this->logger = $options['logger'];
        $this->chunk = $options['chunk'];
        $this->xpath = $options['xpath_prefix'];
    }
}
