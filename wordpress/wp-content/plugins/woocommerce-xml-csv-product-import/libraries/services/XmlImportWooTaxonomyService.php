<?php

require_once dirname(__FILE__) . '/XmlImportWooServiceBase.php';

/**
 * Class XmlImportWooTaxonomyService
 */
class XmlImportWooTaxonomyService extends XmlImportWooServiceBase {

    /**
     * @var array
     */
    public $reserved_terms = array(
        'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
        'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
        'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
        'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
        'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
        'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
        'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
        'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
    );

    /**
     *
     * Associate terms with product.
     *
     * @param $pid
     * @param $termIDs
     * @param $txName
     */
    public function associateTerms($pid, $termIDs, $txName) {
        $terms = wp_get_object_terms($pid, $txName);
        $term_ids = array();
        $assign_taxes = (is_array($termIDs)) ? array_filter($termIDs) : false;
        if (!empty($terms)) {
            if (!is_wp_error($terms)) {
                foreach ($terms as $term_info) {
                    $term_ids[] = $term_info->term_taxonomy_id;
                    $this->wpdb->query(  $this->wpdb->prepare("UPDATE {$this->wpdb->term_taxonomy} SET count = count - 1 WHERE term_taxonomy_id = %d", $term_info->term_taxonomy_id) );
                }
                $in_tt_ids = "'" . implode( "', '", $term_ids ) . "'";
                $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->term_relationships} WHERE object_id = %d AND term_taxonomy_id IN ($in_tt_ids)", $pid ) );
            }
        }

        if (empty($assign_taxes)) return;

        $values = array();
        $term_order = 0;
        foreach ($assign_taxes as $tt) {
            do_action('wp_all_import_associate_term', $pid, $tt, $txName);
            $values[] = $this->wpdb->prepare( "(%d, %d, %d)", $pid, $tt, ++$term_order);
            $this->wpdb->query( "UPDATE {$this->wpdb->term_taxonomy} SET count = count + 1 WHERE term_taxonomy_id = $tt" );
        }
        if ($values) {
            if ( false === $this->wpdb->query( "INSERT INTO {$this->wpdb->term_relationships} (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $values ) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)" ) ){
                $this->getLogger() and call_user_func($this->getLogger(), __('<b>ERROR</b> Could not insert term relationship into the database', \PMWI_Plugin::TEXT_DOMAIN) . ': '. $this->wpdb->last_error);
            }
        }
        wp_cache_delete($pid, $txName . '_relationships');
    }

    /**
     *
     * Create new taxonomy.
     *
     * @param $attr_name
     * @param int $prefix
     * @return string
     */
    public function createTaxonomy($attr_name, $prefix = 1) {
        $attr_name_real = $prefix > 1 ? $attr_name . " " . $prefix : $attr_name;
        $attribute_name = wc_sanitize_taxonomy_name( stripslashes( (string) $attr_name_real ) );
        $args = array(
            'attribute_label'   => stripslashes( (string) $attr_name ),
            'attribute_name'    => $attribute_name,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 1
        );

        if ( ! taxonomy_exists( wc_attribute_taxonomy_name( $attr_name_real ) ) ) {
            if ( in_array( wc_sanitize_taxonomy_name( stripslashes( (string) $attr_name_real)), $this->reserved_terms ) ) {
                $prefix++;
                return $this->createTaxonomy($attr_name, $prefix);
            }
            else {
                // Register the taxonomy now so that the import works!
                $domain = wc_attribute_taxonomy_name( $attr_name_real );
                if (strlen($domain) < 31){
                    register_taxonomy( $domain,
                        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
                        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
                            'hierarchical' => true,
                            'show_ui' => false,
                            'query_var' => true,
                            'rewrite' => false,
                        ) )
                    );
                    $this->createWooCommerceAttribute($args);
                    $this->getLogger() and call_user_func($this->getLogger(), sprintf(__('- <b>CREATED</b>: Taxonomy attribute “%s” have been successfully created.', \PMWI_Plugin::TEXT_DOMAIN), wc_attribute_taxonomy_name( $attribute_name )));
                }
                else {
                    $this->getLogger() and call_user_func($this->getLogger(), sprintf(__('- <b>WARNING</b>: Taxonomy “%s” name is more than 28 characters. Change it, please.', \PMWI_Plugin::TEXT_DOMAIN), $attr_name));
                }
            }
        }
        else {
            if ( in_array( wc_sanitize_taxonomy_name( stripslashes( (string) $attr_name_real)), $this->reserved_terms ) ) {
                $prefix++;
                return $this->createTaxonomy($attr_name, $prefix);
            }
        }

        if (!wc_attribute_taxonomy_id_by_name($attr_name_real) && strlen($attribute_name) < 31) {
            $this->createWooCommerceAttribute($args);
        }

        return $attr_name_real;
    }

    /**
     * @param $args
     */
    public function createWooCommerceAttribute($args) {
        // Clear WooCommerce attributes cache.
        $prefix = WC_Cache_Helper::get_cache_prefix('woocommerce-attributes');
        foreach (['ids', 'attributes'] as $cache_key) {
            wp_cache_delete($prefix . $cache_key, 'woocommerce-attributes');
        }
        delete_transient('wc_attribute_taxonomies');

        $this->wpdb->insert(
            $this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
            $args
        );
        $attribute_taxonomies = $this->wpdb->get_results( "SELECT * FROM " . $this->wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_name ASC;" );
        set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );
    }
}
