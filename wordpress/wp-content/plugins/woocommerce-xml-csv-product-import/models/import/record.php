<?php

/**
 * Class PMWI_Import_Record
 */
class PMWI_Import_Record extends PMWI_Model_Record {

    /**
     * Associative array of data which will be automatically available as variables when template is rendered
     * @var array
     */
    public $data = array();

    /**
     * @var XmlImportWooCommerce
     */
    public $engine = FALSE;

    /**
     * Initialize model instance
     * @param array [optional] $data Array of record data to initialize object with
     */
    public function __construct($data = array()) {
        parent::__construct($data);
        $this->setTable(PMXI_Plugin::getInstance()
                ->getTablePrefix() . 'imports');
    }

    /**
     * Perform import operation
     * @param array $parsingData XML string to import
     * @return array|bool
     */
    public function parse($parsingData = array()) {

        if (!$this->isImportAllowed($parsingData)) {
            return FALSE;
        }
        add_filter('user_has_cap', array(
            $this,
            '_filter_has_cap_unfiltered_html'
        ));
        kses_init(); // do not perform special filtering for imported content

        $this->options = $parsingData['import']->options;

        switch ($parsingData['import']->options['custom_type']) {
            case 'product':
                $this->engine = new XmlImportWooCommerceProduct($parsingData);
                break;
            case 'shop_order':
                $this->engine = new XmlImportWooCommerceShopOrder($parsingData);
                break;
            default:
                # code...
                break;
        }

        $this->data = $this->engine->parse();

        remove_filter('user_has_cap', array(
            $this,
            '_filter_has_cap_unfiltered_html'
        ));
        kses_init(); // return any filtering rules back if they has been disabled for import procedure

        if (!empty($this->options['put_variation_image_to_gallery'])) {
            add_action('pmxi_gallery_image', array(
                $this,
                'action_wpai_gallery_image'
            ), 10, 3);
        }
        if (!empty($this->options['import_additional_variation_images'])) {
            add_action('pmxi_saved_post', array(
                $this,
                'action_wpai_additional_variation_images'
            ), 11, 3);
        }
        return $this->data;
    }

    /**
     * @param array $importData
     *
     * @return bool
     */
    public function import($importData = array()) {
        if (!$this->isImportAllowed($importData)) {
            return FALSE;
        }
        $this->engine->import($importData);
    }

    /**
     * @param $importData
     *
     * @return bool
     */
    public function saved_post($importData) {
        if (!$this->isImportAllowed($importData)) {
            return FALSE;
        }
        $this->engine->after_save_post($importData);
    }

    /**
     * @param $pid
     * @param $attid
     * @param $image_filepath
     */
    public function action_wpai_gallery_image($pid, $attid, $image_filepath) {

        $table = $this->wpdb->posts;

        $p = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table WHERE ID = %d;", (int) $pid));

        if ($p && $p->post_parent) {
            $gallery = explode(",", get_post_meta($p->post_parent, '_product_image_gallery_tmp', TRUE));
            if (is_array($gallery)) {
                $gallery = array_filter($gallery);
                if (!in_array($attid, $gallery)) {
                    $gallery[] = $attid;
                }
            }
            else {
                $gallery = array($attid);
            }
            update_post_meta($p->post_parent, '_product_image_gallery_tmp', implode(',', $gallery));
        }
    }

    /**
     * @param $pid
     * @param $xml
     * @param $update
     */
    public function action_wpai_additional_variation_images($pid, $xml, $update) {
        $product = wc_get_product($pid);
        $variationID = get_post_meta($product->get_id(), XmlImportWooCommerceService::FIRST_VARIATION, TRUE);
        if ($product->is_type('variation') || !empty($variationID)) {
            if (empty($variationID)) {
                $variationID = $pid;
            }
            if ($gallery = get_post_meta($product->get_id(), '_product_image_gallery', TRUE)) {
                update_post_meta($variationID, '_wc_additional_variation_images', $gallery);
            }
        }
    }

    /**
     * @param $caps
     * @return mixed
     */
    public function _filter_has_cap_unfiltered_html($caps) {
        $caps['unfiltered_html'] = TRUE;
        return $caps;
    }

    /**
     * @param $importData
     *
     * @return bool
     */
    private function isImportAllowed($importData) {
        if (!in_array($importData['import']->options['custom_type'], array(
            'product',
            'product_variation',
            'shop_order'
        ))
        ) {
            return FALSE;
        }
        return TRUE;
    }
}