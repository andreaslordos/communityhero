<?php

namespace wpai_woocommerce_add_on\libraries\importer;

use wpai_woocommerce_add_on\libraries\parser\ParserInterface;

/**
 * Created by PhpStorm.
 * User: cmd
 * Date: 11/15/17
 * Time: 2:10 PM
 */
abstract class ImportProductBase extends ImportBase {

    /**
     * @var
     */
    public $product_data;

    /**
     * @return mixed
     */
    public function getProductID() {
        return $this->index->getPid();
    }

    /**
     * @return bool|\WC_Product
     */
    public function getProduct() {
        return new \WC_Product($this->getProductID());
    }

    /**
     * @return ParserInterface
     */
    public function getParser() {
        return $this->getOptions()->getParser();
    }

    /**
     * @param $product \WC_Product_Variation
     * @return mixed|void
     */
    protected function generateProductTitle($product) {

        $attributes = (array) $product->get_attributes();

        // Don't include attributes if the product has 3+ attributes.
        $should_include_attributes = count( $attributes ) < 3;

        // Don't include attributes if an attribute name has 2+ words.
        if ( $should_include_attributes ) {
            foreach ( $attributes as $name => $value ) {
                if ( false !== strpos( $name, '-' ) ) {
                    $should_include_attributes = false;
                    break;
                }
            }
        }

        $should_include_attributes = apply_filters( 'woocommerce_product_variation_title_include_attributes', $should_include_attributes, $product );
        $separator = apply_filters( 'woocommerce_product_variation_title_attributes_separator', ' - ', $product );
        $title_base = get_post_field( 'post_title', $product->get_parent_id() );
        $title_suffix = $should_include_attributes ? wc_get_formatted_variation( $product, true, false ) : '';

        return apply_filters( 'woocommerce_product_variation_title', rtrim( $title_base . $separator . $title_suffix, $separator ), $product, $title_base, $title_suffix );
    }

    /**
     * @param $variation \WC_Product_Variation
     * @param $parentProduct \WC_Product
     */
    protected function duplicatePostMeta($variation, $parentProduct) {
        $properties = $parentProduct->get_data();
        $variation->set_props($properties);
        if ($this->getImport()->options['put_variation_image_to_gallery']) {
            $post_thumbnail_id = get_post_thumbnail_id($parentProduct->get_id());
            do_action('pmxi_gallery_image', $variation->get_id(), $post_thumbnail_id, FALSE);
        }
    }

    /**
     *
     * Get list of Linked Product IDs.
     *
     * @param $pid - Current product ID.
     * @param $products - Products which needs to be linked.
     * @param $type
     *
     * @return array
     */
    protected function getLinkedProducts($pid, $products, $type) {
        $linked_products = array();
        if (!empty($products)) {
            $not_found = [];
            $ids = array_filter(explode(',', $products), 'trim');
            foreach ($ids as $id) {
                // Do not link product to himself.
                if ($id == $pid) continue;
                // Search linked product by _SKU.
                $args = [
                    'post_type' => ['product', 'product_variation'],
                    'meta_query' => [
                        [
                            'key' => '_sku',
                            'value' => $id,
                        ]
                    ]
                ];
                $query = new \WP_Query($args);
                $linked_product = FALSE;
                if ($query->have_posts()) {
                    $linked_product = get_post($query->post->ID);
                }
                wp_reset_postdata();
                if (!$linked_product) {
                    if (is_numeric($id)) {
                        // Search linked product by ID.
                        $query = new \WP_Query([
                            'post_type' => [
                                'product',
                                'product_variation'
                            ],
                            'post__in' => [$id]
                        ]);
                        if ($query->have_posts()) {
                            $linked_product = get_post($query->post->ID);
                        }
                        wp_reset_postdata();
                    }
                    if (!$linked_product) {
                        // Search linked product by slug.
                        $args = [
                            'name' => $id,
                            'post_type' => 'product',
                            'post_status' => 'publish',
                            'numberposts' => 1
                        ];
                        $query = get_posts($args);
                        if ($query) {
                            $linked_product = $query[0];
                        }
                        wp_reset_postdata();
                        if (!$linked_product) {
                            // Search linked product by title.
                            $linked_product = get_page_by_title( $id, OBJECT, 'product' );
                        }
                    }
                }
                if ($linked_product) {
                    // Do not link product to himself.
                    if ($pid == $linked_product->ID) {
                        continue;
                    }
                    $linked_products[] = $linked_product->ID;
                    $this->getLogger() and call_user_func($this->getLogger(), sprintf(__('Product `%s` with ID `%d` added to %s list.', \PMWI_Plugin::TEXT_DOMAIN), $linked_product->post_title, $linked_product->ID, $type == '_upsell_ids' ? 'Up-Sells' : 'Cross-Sells'));
                }
                else {
                    $not_found[] = $id;
                }
            }

            // Not all linked products founded.
            if (!empty($not_found)) {
                $not_founded_linked_products = get_option('wp_all_import_not_linked_products_' . $this->getImport()->id);
                if (empty($not_founded_linked_products)) {
                    $not_founded_linked_products = [];
                }
                $not_founded_linked_products[] = [
                    'pid' => $pid,
                    'type' => $type,
                    'not_linked_products' => $not_found
                ];
                update_option('wp_all_import_not_linked_products_' . $this->getImport()->id, $not_founded_linked_products, false);
            }
        }
        return $linked_products;
    }

    /**
     * @param $url
     */
    protected function autoCloakLinks(&$url){

        $url = apply_filters('pmwi_cloak_affiliate_url', trim($url), $this->getImport()->id);

        // cloak urls with `WP Wizard Cloak` if corresponding option is set
        if ( ! empty($this->getImport()->options['is_cloak']) and class_exists('PMLC_Plugin')) {
            if (preg_match('%^\w+://%i', $url)) { // mask only links having protocol
                // try to find matching cloaked link among already registered ones
                $list = new PMLC_Link_List(); $linkTable = $list->getTable();
                $rule = new PMLC_Rule_Record(); $ruleTable = $rule->getTable();
                $dest = new PMLC_Destination_Record(); $destTable = $dest->getTable();
                $list->join($ruleTable, "$ruleTable.link_id = $linkTable.id")
                    ->join($destTable, "$destTable.rule_id = $ruleTable.id")
                    ->setColumns("$linkTable.*")
                    ->getBy(array(
                        "$linkTable.destination_type =" => 'ONE_SET',
                        "$linkTable.is_trashed =" => 0,
                        "$linkTable.preset =" => '',
                        "$linkTable.expire_on =" => '0000-00-00',
                        "$ruleTable.type =" => 'ONE_SET',
                        "$destTable.weight =" => 100,
                        "$destTable.url LIKE" => $url,
                    ), NULL, 1, 1)->convertRecords();
                if ($list->count()) { // matching link found
                    $link = $list[0];
                } else { // register new cloaked link
                    global $wpdb;
                    $slug = max(
                        intval($wpdb->get_var("SELECT MAX(CONVERT(name, SIGNED)) FROM $linkTable")),
                        intval($wpdb->get_var("SELECT MAX(CONVERT(slug, SIGNED)) FROM $linkTable")),
                        0
                    );
                    $i = 0; do {
                        is_int(++$slug) and $slug > 0 or $slug = 1;
                        $is_slug_found = ! intval($wpdb->get_var("SELECT COUNT(*) FROM $linkTable WHERE name = '$slug' OR slug = '$slug'"));
                    } while( ! $is_slug_found and $i++ < 100000);
                    if ($is_slug_found) {
                        $link = new PMLC_Link_Record(array(
                            'name' => strval($slug),
                            'slug' => strval($slug),
                            'header_tracking_code' => '',
                            'footer_tracking_code' => '',
                            'redirect_type' => '301',
                            'destination_type' => 'ONE_SET',
                            'preset' => '',
                            'forward_url_params' => 1,
                            'no_global_tracking_code' => 0,
                            'expire_on' => '0000-00-00',
                            'created_on' => date('Y-m-d H:i:s'),
                            'is_trashed' => 0,
                        ));
                        $link->insert();
                        $rule = new PMLC_Rule_Record(array(
                            'link_id' => $link->id,
                            'type' => 'ONE_SET',
                            'rule' => '',
                        ));
                        $rule->insert();
                        $dest = new PMLC_Destination_Record(array(
                            'rule_id' => $rule->id,
                            'url' => $url,
                            'weight' => 100,
                        ));
                        $dest->insert();
                    } else {
                        $this->getLogger() and call_user_func($this->getLogger(), sprintf(__('- <b>WARNING</b>: Unable to create cloaked link for %s', \PMWI_Plugin::TEXT_DOMAIN), $url));
                        $link = NULL;
                    }
                }
                if ($link) { // cloaked link is found or created for url
                    $url = preg_replace('%' . preg_quote($url, '%') . '(?=([\s\'"]|$))%i', $link->getUrl(), $url);
                }
            }
        }
    }
}