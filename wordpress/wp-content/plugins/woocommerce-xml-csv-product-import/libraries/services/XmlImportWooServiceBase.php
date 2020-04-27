<?php

/**
 * Class XmlImportWooService
 */
abstract class XmlImportWooServiceBase {

    /**
     * @var \PMXI_Image_Record
     */
    public $import;

    /**
     * @var \wpdb
     */
    public $wpdb;

    /**
     * XmlImportWooService constructor.
     *
     * @param $import
     */
    public function __construct($import) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->import = $import;
    }

    /**
     * @return \PMXI_Image_Record
     */
    public function getImport() {
        return $this->import;
    }

    /**
     * @return array
     */
    public function getImportOptions() {
        return $this->import->options;
    }

    /**
     * @return bool|\Closure
     */
    public function getLogger() {
        $logger = FALSE;
        if (PMXI_Plugin::is_ajax()) {
            $logger = function($m) {echo "<div class='progress-msg'>[". date("H:i:s") ."] $m</div>\n";flush();};
        }
        return $logger;
    }
}