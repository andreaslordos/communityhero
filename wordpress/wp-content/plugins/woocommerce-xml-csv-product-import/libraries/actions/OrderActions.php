<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\parser\OrdersParser;
use wpai_woocommerce_add_on\libraries\parser\Parser;

require_once dirname(__FILE__) . '/Actions.php';

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/16/17
 * Time: 3:44 PM
 */

class OrderActions extends Actions {

    /**
     * OrderActions constructor.
     * @param Parser $parser
     */
    public function __construct(Parser $parser) {

        parent::__construct($parser);

        if (!has_filter('wp_all_import_is_post_to_skip')) {
            add_filter('wp_all_import_is_post_to_skip', array(
                &$this,
                'wp_all_import_is_post_to_skip'
            ), 10, 5);
        }

        if (!has_filter('wp_all_import_combine_article_data')) {
            add_filter('wp_all_import_combine_article_data', array(
                &$this,
                'wp_all_import_combine_article_data'
            ), 10, 4);
        }
    }

    /**
     *
     * When users are matching to existing customers and/or products and no match it found,
     * WP All Import doesn't have enough information to import that order, so the whole order will be skipped.
     *
     * @param $is_post_to_skip
     * @param $import_id
     * @param $current_xml_node
     * @param $index
     * @param $post_to_update_id
     * @return bool
     */
    public function wp_all_import_is_post_to_skip($is_post_to_skip, $import_id, $current_xml_node, $index, $post_to_update_id) {

        $order_title = 'Order &ndash; ' . date_i18n('F j, Y @ h:i A', strtotime($this->getParser()->getData()['pmwi_order']['date'][$index]));

        if (empty($post_to_update_id) or $this->getParser()->getImport()->options['update_all_data'] == 'yes' or $this->getParser()->getImport()->options['is_update_billing_details']) {
            if ($this->getParser()->getImport()->options['pmwi_order']['billing_source'] == 'existing') {
                $customer = $this->getParser()->get_existing_customer('billing_source', $index);
                if (empty($customer) && empty($this->getParser()->getImport()->options['pmwi_order']['is_guest_matching'])) {
                    $this->getParser()->getLogger() and call_user_func($this->getParser()->getLogger(), sprintf(__('<b>SKIPPED</b>: %s Existing customer not found for Order `%s`.', \PMWI_Plugin::TEXT_DOMAIN), $this->getParser()->get_existing_customer_for_logger('billing_source', $index), $order_title));
                    $is_post_to_skip = TRUE;
                }
            }
        }

        if (empty($post_to_update_id) or $this->getParser()->getImport()->options['update_all_data'] == 'yes' or $this->getParser()->getImport()->options['is_update_products']) {
            if (!$is_post_to_skip and $this->getParser()->getImport()->options['pmwi_order']['products_source'] == 'existing') {
                $is_product_founded = FALSE;

                $searching_for_sku = '';

                foreach ($this->getParser()->getData()['pmwi_order']['products'][$index] as $productIndex => $productItem) {
                    if (empty($productItem['sku'])) {
                        continue;
                    }

                    $searching_for_sku = $productItem['sku'];

                    $args = array(
                        'post_type' => 'product',
                        'meta_key' => '_sku',
                        'meta_value' => $productItem['sku'],
                        'meta_compare' => '=',
                    );

                    $product = FALSE;

                    $query = new \WP_Query($args);
                    while ($query->have_posts()) {
                        $query->the_post();
                        $product = WC()->product_factory->get_product($query->post->ID);
                        break;
                    }
                    wp_reset_postdata();

                    if (empty($product)) {
                        $args['post_type'] = 'product_variation';
                        $query = new \WP_Query($args);
                        while ($query->have_posts()) {
                            $query->the_post();
                            $product = WC()->product_factory->get_product($query->post->ID);
                            break;
                        }
                        wp_reset_postdata();
                    }
                    if ($product) {
                        $is_product_founded = TRUE;
                        break;
                    }
                }
                if (!$is_product_founded) {
                    $this->getParser()->getLogger() and call_user_func($this->getParser()->getLogger(), sprintf(__('<b>SKIPPED</b>: Existing product `%s` not found for Order `%s`.', \PMWI_Plugin::TEXT_DOMAIN), $searching_for_sku, $order_title));
                    $is_post_to_skip = TRUE;
                }
            }
        }
        return $is_post_to_skip;
    }

    /**
     * @param $articleData
     * @param $post_type
     * @param $import_id
     * @param $index
     * @return mixed
     */
    public function wp_all_import_combine_article_data($articleData, $post_type, $import_id, $index) {
        if ($post_type == 'shop_order' && empty($articleData['post_title'])) {
            $articleData['post_title'] = 'Order &ndash; ' . date_i18n('F j, Y @ h:i A', strtotime($this->getParser()->getData()['pmwi_order']['date'][$index]));
        }
        return $articleData;
    }
}