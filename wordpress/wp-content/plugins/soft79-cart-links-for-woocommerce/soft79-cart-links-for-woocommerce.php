<?php
/**
 * Plugin Name: SOFT79 Cart Links for WooCommerce
 * Plugin URI: http://www.soft79.nl
 * Text Domain: soft79_wccl
 * Domain Path: /languages
 * Description: Create links that will populate a cart
 * Version: 1.1.4
 * Author: Soft79
 * License: GPL2
 * WC requires at least: 2.3.0
 * WC tested up to: 3.2.3
 */

defined('ABSPATH') or die();

class SOFT79_WCCL {
    protected $version = '1.1.4';

    public function __construct() {    
        add_action('init', array( &$this, 'controller_init' ) );
        add_action('plugins_loaded', array( &$this, 'load_plugin_textdomain' ) );
    }

    /**
     * Singleton Instance
     *
     * @static
     * @return SOFT79_WCCL - Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected static $_instance = null;


    public function controller_init() {
        //Does WooCommerce exists ?
        if ( ! class_exists('WC_Cart') ) {
            return;
        }
        
        //Customer hooks
        add_action( 'wp_loaded', array( &$this, 'check_url' ), 5); //Perform this before autocoupon does it

        //Shop manager hooks
        if ( ! is_ajax() && current_user_can( 'manage_woocommerce' ) ) {
            //Admin
            add_action( 'woocommerce_cart_actions', array( &$this, 'woocommerce_cart_actions' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'action_wp_enqueue_scripts' ), 10 );
        }
    }


    function load_plugin_textdomain() {
        load_plugin_textdomain('soft79_wccl', false, basename(dirname(__FILE__)) . '/languages/' );
    }

    /**
     * Include Javascript
     */
    public function action_wp_enqueue_scripts() {
        if ( is_cart() ) {
            wp_enqueue_script( 'soft79_wccl_js', $this->pluginUrl() . '/assets/js/frontend.js', array( 'jquery' ), $this->pluginVersion(), true ); 
        }
    }

    /**
     * Add 'Link to this cart' to the cart actions
     */
    public function woocommerce_cart_actions() {
        $url = $this->get_fill_cart_url();

        if ( $url != false ) {
            echo '<div class="soft79_fill_cart_url">' 
            . __( 'Link to this cart:', 'soft79_wccl' ) 
            . ' <input type="text" value="' . esc_url( $url ) . '"></div>';
        }
    }

    /**
     * Return the url with &fill_cart= added
     * 
     * @param string (Optional) The base url. If omitted the url to the cart page will be used.
     * @return string|false The url, or false if the cart is empty
     */
    public function get_fill_cart_url( $base_url = false ) {
        $cart_contents = WC()->cart->get_cart();
        $parts = array();
        foreach ( $cart_contents as $cart_item_key => $cart_item ) {
            if ( ! isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
                $pr_id = SOFT79_WCCL_Helpers::get_product_or_variation_id( $cart_item['data'] );
                $qty = $cart_item['quantity'];            
                $parts[] = $qty == 1 ? strval( $pr_id ) : $qty . 'x' . $pr_id;
            }
        }
        if ( empty( $parts ) ) {
            return false;
        } else {            
            return add_query_arg( 'fill_cart', implode( ',', $parts ), $base_url == false ? SOFT79_WCCL_Helpers::wc_get_cart_url() : $base_url );
        }

    }

    /**
     * Handle the fill_cart or set_cart querystring
     * fill_cart: Add the passed items to the cart
     * set_cart: Replace the cart with the passed items (since 1.1.3)
     * @return void
     */
    public function check_url() {

        if ( isset( $_GET['fill_cart'] ) ) {
        	//Since 1.1.3: Append to cart contents
            $this->fill_cart( $_GET['fill_cart'] );
        } elseif ( isset( $_GET['set_cart'] ) ) {
        	//Since 1.1.3: Replace cart contents
            WC()->cart->empty_cart();
            $this->fill_cart( $_GET['set_cart'] );
        } else {
        	//Do nothing
        	return;
        }

        //Refer to url without ?fill_cart to prevent fill_cart to be executed after customer performs a cart update (because WooCommerce also redirects to referer )
        $requested_url  = is_ssl() ? 'https://' : 'http://';
        $requested_url .= $_SERVER['HTTP_HOST'];           
        $requested_url .= $_SERVER['REQUEST_URI'];
        wp_safe_redirect( remove_query_arg( array('set_cart', 'fill_cart'), $requested_url ) );
        exit;
    }    

    /**
     * Populates the cart with the products passed in $fill_string
     * @param string $fill_string eg. 123,2x124 will populate the cart with 1x product 123 and 2x product 124
     * @return void
     */
    public function fill_cart( $fill_string ) {

        $original_notices = wc_get_notices();
        wc_clear_notices();

        ////Clear the cart
        //WC()->cart->empty_cart();

        $cart_contents = WC()->cart->get_cart();

        $pattern = '/^(?:(\d+)x)?(\d+)$/i';
        $fill_strings = explode( ",", $fill_string );
        $my_notices = array();

        //Parse querystring
        $products_to_append = array();
        foreach ( $fill_strings as $fill_string ) {            
            if ( preg_match ( $pattern, $fill_string, $matches ) ) {
                //var_dump($matches);
                $quantity = $matches[1] ? absint( $matches[1] ) : 1;
                $product_id = absint( $matches[2] );
                $products_to_append[$product_id] = $quantity;
            }
        }

        //Append items to the cart
        foreach ( $products_to_append as $product_id => $quantity ) {
            $product = wc_get_product( $product_id );
            if ( $product == false ) {
                $my_notices[] = sprintf( __('Unknown product &quot;%d&quot;.', 'soft79_wccl' ), $product_id );
            } else {
                $available = $this->get_stock_available( $product, $quantity );

                //Is it already in the cart?
                $cart_item_key = $this->get_cart_item_key( $cart_contents, $product_id );
                $qty_in_cart = $cart_item_key === false ? 0 :  $cart_contents[$cart_item_key]['quantity'];

                //Only add to cart if required
                if ( $qty_in_cart < $quantity ) {
                    if ( $available == 0 ) {
                        $my_notices[] = sprintf( __('Can not add %d &quot;%s&quot; because it is not available.', 'soft79_wccl' ), $quantity, $product->get_title() );
                    } else {
                        if ( $available < $quantity && $available > 0 ) {
                            $my_notices[] = sprintf( __('Reduced quantity of &quot;%s&quot; from %d to %d because there\'s not enough stock.', 'soft79_wccl' ), $product->get_title(), $quantity, $available );
                        }

                        if ( $cart_item_key !== false ) {
                            WC()->cart->set_quantity( $cart_item_key, $available );
                        } else {
                            $this->add_to_cart( $product, $available );
                        }
                    }
                }
            }
        }
        $catched_notices = wc_get_notices();

        //Restore original notices
        WC()->session->set( 'wc_notices', $original_notices );

        //Collect notices that were added in the meantime (preferably none)
        foreach ( $catched_notices as $notice_type => $messages ) {
            if ($notice_type != 'success') {
                foreach ( $messages as $message ) {
                    $my_notices[] = $message . "<br>";
                }
            }
        }

        if ( count( $my_notices ) == 1 ) {
            wc_add_notice( $my_notices[0], 'error' );
        } elseif ( count( $my_notices ) > 1 ) {
            wc_add_notice( __( 'Something went wrong populating your cart:', 'soft79_wccl' ) . "<br><ul>\n<li>" . implode( "\n<li>", $my_notices ) . "\n</ul>", 'error' );
        }

    }

    /**
     * Add the product to the cart. Takes care of variations
     *
     * @param $product WC_Product The product or variation to append
     * @param $quantity int The quantity of the products to append
     */
    protected function add_to_cart( $product, $quantity ) {
        if ( SOFT79_WCCL_Helpers::is_variation( $product ) ) {
            $variation = SOFT79_WCCL_Helpers::get_product_variation_attributes( $product );
            $variation_id = SOFT79_WCCL_Helpers::get_variation_id( $product );
            $parent_id =  SOFT79_WCCL_Helpers::get_variable_product_id( $product );
            WC()->cart->add_to_cart( $parent_id, $quantity, $variation_id, $variation );
        } else {
            $product_id = SOFT79_WCCL_Helpers::get_product_id( $product );
            WC()->cart->add_to_cart( $product_id, $quantity );
        }
    }


    /**
     * Checks whether item is in the cart and returns the key.
     * 
     * @returns string|bool false if not found, otherwise the cart_item_key of the product
     */
    protected function get_cart_item_key( $cart_contents, $product_or_variation_id ) {
        foreach ( $cart_contents as $cart_item_key => $cart_item ) {
            if ( ! isset( $cart_item['_wjecf_free_product_coupon'] ) ) {
                $pr_id = SOFT79_WCCL_Helpers::get_product_or_variation_id( $cart_item['data'] );
                if ( $pr_id == $product_or_variation_id ) {
                    return $cart_item_key;
                }                
            }
        }
        return false;
    }


    /**
     * Checks if stock is sufficient for the given product.
     *
     * @returns int|bool Value of $quantity if stock is sufficient or not managed, otherwise the available quantity. Returns false if product does not exist
     */
    protected function get_stock_available( $product, $quantity ) {
        if ( $product == false ) {
            return false;
        } elseif ( $product->managing_stock() && ! $product->backorders_allowed() ) {
            $available = min( $quantity, intval( $product->get_stock_quantity() ) );
        } else {
            $available = $quantity;
        }
        return $available;
    }

    
    /**
     * Get the plugin url without trailing slash.
     * @return string
     */
    public function pluginUrl() {
        return untrailingslashit( plugins_url( '/', $this->pluginFullPath() ) );
    }    
    
    /**
     * Get the plugin path without trailing slash.
     * @return string
     */    
    public function pluginDirectory() {
        return untrailingslashit( dirname( $this->pluginFullPath() ) );
    } 

    /**
     * Plugin filename including path
     * @return string
     */
    public function pluginFullPath() {
        return __FILE__;
    } 

    /**
     * Plugin base name ( path relative to wp-content/plugins/ )
     * @return string
     */
    public function pluginBase() {
        return plugin_basename( $this->pluginFullPath() );
    }     

    public function pluginVersion() {
        return $this->version;
    }      
}

require_once('includes/soft79-wccl-helpers.php');

$soft79_wccl = SOFT79_WCCL::instance();
