<?php
/*
Plugin Name: WP All Import - WooCommerce Add-On
Plugin URI: http://www.wpallimport.com/woocommerce-product-import/?utm_source=import-wooco-products-addon-free&utm_medium=wp-plugins-page&utm_campaign=upgrade-to-pro
Description: An extremely easy, drag & drop importer to import WooCommerce simple products. A paid upgrade is available for premium support and support for Variable, Grouped, and External/Affiliate products
Version: 1.4.4
Author: Soflyy
WC tested up to: 4.0.1
*/

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( is_plugin_active('wpai-woocommerce-add-on/wpai-woocommerce-add-on.php') ) {
    function pmwi_free_notice() {
        ?>
        <div class="error"><p>
            <?php printf(__('The Pro version of the WooCommerce Add-On is already installed. If you intended to replace it with the free version, please de-activate and remove the paid version before activating the free version.', 'wpai_woocommerce_addon_plugin')); ?>
        </p></div>
        <?php
    }
    add_action('admin_notices', 'pmwi_free_notice');
    deactivate_plugins(str_replace('\\', '/', dirname(__FILE__)) . '/plugin.php');
}
else {

    define('PMWI_FREE_VERSION', '1.4.4');

    define('PMWI_EDITION', 'free');

    /**
     * Plugin root dir with forward slashes as directory separator regardless of actuall DIRECTORY_SEPARATOR value
     * @var string
     */
    define('PMWI_ROOT_DIR', str_replace('\\', '/', dirname(__FILE__)));

    /**
     * Plugin root url for referencing static content
     * @var string
     */
    define('PMWI_ROOT_URL', rtrim(plugin_dir_url(__FILE__), '/'));

    /**
     * Plugin prefix for making names unique (be aware that this variable is used in conjuction with naming convention,
     * i.e. in order to change it one must not only modify this constant but also rename all constants, classes and functions which
     * names composed using this prefix)
     * @var string
     */
    define('PMWI_PREFIX', 'pmwi_');

    /**
     * Main plugin file, Introduces MVC pattern
     *
     * @singletone
     * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
     */
    final class PMWI_Plugin {
        /**
         * Singletone instance
         * @var PMWI_Plugin
         */
        protected static $instance;

        /**
         * Plugin root dir
         * @var string
         */
        const ROOT_DIR = PMWI_ROOT_DIR;
        /**
         * Plugin root URL
         * @var string
         */
        const ROOT_URL = PMWI_ROOT_URL;
        /**
         * Prefix used for names of shortcodes, action handlers, filter functions etc.
         * @var string
         */
        const PREFIX = PMWI_PREFIX;
        /**
         * Plugin file path
         * @var string
         */
        const FILE = __FILE__;
        /**
         * Plugin text domain
         * @var string
         */
        const TEXT_DOMAIN = 'wpai_woocommerce_addon_plugin';

        /**
         * Return singletone instance
         * @return PMWI_Plugin
         */
        static public function getInstance() {
            if (self::$instance == NULL) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Common logic for requestin plugin info fields
         */
        public function __call($method, $args) {
            if (preg_match('%^get(.+)%i', $method, $mtch)) {
                $info = get_plugin_data(self::FILE);
                if (isset($info[$mtch[1]])) {
                    return $info[$mtch[1]];
                }
            }
            throw new Exception("Requested method " . get_class($this) . "::$method doesn't exist.");
        }

        /**
         * Get path to plagin dir relative to wordpress root
         * @param bool[optional] $noForwardSlash Whether path should be returned withot forwarding slash
         * @return string
         */
        public function getRelativePath($noForwardSlash = false) {
            $wp_root = str_replace('\\', '/', ABSPATH);
            return ($noForwardSlash ? '' : '/') . str_replace($wp_root, '', self::ROOT_DIR);
        }

        /**
         * Check whether plugin is activated as network one
         * @return bool
         */
        public function isNetwork() {
            if ( !is_multisite() )
                return false;

            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins[plugin_basename(self::FILE)]))
                return true;

            return false;
        }

        /**
         * Check whether permalinks is enabled
         * @return bool
         */
        public function isPermalinks() {
            global $wp_rewrite;

            return $wp_rewrite->using_permalinks();
        }

        /**
         * Return prefix for plugin database tables
         * @return string
         */
        public function getTablePrefix() {
            global $wpdb;
            return ($this->isNetwork() ? $wpdb->base_prefix : $wpdb->prefix) . self::PREFIX;
        }

        /**
         * Return prefix for wordpress database tables
         * @return string
         */
        public function getWPPrefix() {
            global $wpdb;
            return ($this->isNetwork() ? $wpdb->base_prefix : $wpdb->prefix);
        }

        /**
         * Class constructor containing dispatching logic
         * @param string $rootDir Plugin root dir
         * @param string $pluginFilePath Plugin main file
         */
        protected function __construct() {

            // create/update required database tables

            // register autoloading method
            spl_autoload_register(array($this, 'autoload'));

            // register helpers
            if (is_dir(self::ROOT_DIR . '/helpers')) foreach (PMWI_Helper::safe_glob(self::ROOT_DIR . '/helpers/*.php', PMWI_Helper::GLOB_RECURSE | PMWI_Helper::GLOB_PATH) as $filePath) {
                require_once $filePath;
            }

            if (is_dir(self::ROOT_DIR . '/libraries')) foreach (PMWI_Helper::safe_glob(self::ROOT_DIR . '/libraries/*.php', PMWI_Helper::GLOB_RECURSE | PMWI_Helper::GLOB_PATH | PMWI_Helper::GLOB_NOSORT) as $filePath) {
                require_once $filePath;
            }

            register_activation_hook(self::FILE, array($this, 'activation'));

            // register action handlers
            if (is_dir(self::ROOT_DIR . '/actions')) if (is_dir(self::ROOT_DIR . '/actions')) foreach (PMWI_Helper::safe_glob(self::ROOT_DIR . '/actions/*.php', PMWI_Helper::GLOB_RECURSE | PMWI_Helper::GLOB_PATH) as $filePath) {
                require_once $filePath;
                $function = $actionName = basename($filePath, '.php');
                if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
                    $actionName = $m[1];
                    $priority = intval($m[2]);
                } else {
                    $priority = 10;
                }
                add_action($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
            }

            // register filter handlers
            if (is_dir(self::ROOT_DIR . '/filters')) foreach (PMWI_Helper::safe_glob(self::ROOT_DIR . '/filters/*.php', PMWI_Helper::GLOB_RECURSE | PMWI_Helper::GLOB_PATH) as $filePath) {
                require_once $filePath;
                $function = $actionName = basename($filePath, '.php');
                if (preg_match('%^(.+?)[_-](\d+)$%', $actionName, $m)) {
                    $actionName = $m[1];
                    $priority = intval($m[2]);
                } else {
                    $priority = 10;
                }
                add_filter($actionName, self::PREFIX . str_replace('-', '_', $function), $priority, 99); // since we don't know at this point how many parameters each plugin expects, we make sure they will be provided with all of them (it's unlikely any developer will specify more than 99 parameters in a function)
            }

            // register shortcodes handlers
            if (is_dir(self::ROOT_DIR . '/shortcodes')) foreach (PMWI_Helper::safe_glob(self::ROOT_DIR . '/shortcodes/*.php', PMWI_Helper::GLOB_RECURSE | PMWI_Helper::GLOB_PATH) as $filePath) {
                $tag = strtolower(str_replace('/', '_', preg_replace('%^' . preg_quote(self::ROOT_DIR . '/shortcodes/', '%') . '|\.php$%', '', $filePath)));
                add_shortcode($tag, array($this, 'shortcodeDispatcher'));
            }

            // register admin page pre-dispatcher
            add_action('admin_init', array($this, 'adminInit'));
            add_action('admin_init', array($this, 'migrate_options'));
            add_action('init', array($this, 'init'));

        }

        public function migrate_options(){

            $installed_ver = get_option( "wp_all_import_woocommerce_addon_db_version" );

            if ( $installed_ver == PMWI_FREE_VERSION || ! class_exists( 'PMXI_Plugin' ) ) return true;

            $imports   = new PMXI_Import_List();

            $templates = new PMXI_Template_List();

            foreach ($imports->setColumns($imports->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $imp){
                $imp->getById($imp->id);
                if ( ! $imp->isEmpty() ){
                    $options = $imp->options;
                    $this->migrate($options, $installed_ver);
                    $imp->set(array(
                        'options' => $options
                    ))->update();
                }
            }

            foreach ($templates->setColumns($templates->getTable() . '.*')->getBy(array('id !=' => ''))->convertRecords() as $tpl){
                $tpl->getById($tpl->id);
                if ( ! $tpl->isEmpty() ) {
                    $options = ( empty($tpl->options) ) ? array() : $tpl->options;
                    $this->migrate($options, $installed_ver);
                    $tpl->set(array(
                        'options' => $options
                    ))->update();
                }
            }
            update_option( "wp_all_import_woocommerce_addon_db_version", PMWI_FREE_VERSION );
        }

        private function migrate(&$options, $version){

            // Update _featured, _visibility and _stock_status options according to WooCommerce 3.0
            if ( version_compare($version, '2.3.7-beta-2.1') < 0  ){

                $remove_cf = array('_featured', '_visibility', '_stock_status');

                if ($options['is_keep_former_posts'] == 'no'
                    && $options['update_all_data'] == 'no'){

                    if ($options['is_update_custom_fields']){
                        if (in_array($options['update_custom_fields_logic'], array('only', 'all_except'))){
                            // Update Options
                            switch ($options['update_custom_fields_logic']){
                                case 'only':
                                    $fields_list = explode(',', $options['custom_fields_only_list']);
                                    if ( ! in_array('_featured', $fields_list) ){
                                        $options['is_update_featured_status'] = 0;
                                    }
                                    if ( ! in_array('_visibility', $fields_list) ){
                                        $options['is_update_catalog_visibility'] = 0;
                                    }
                                    break;
                                case 'all_except':
                                    $fields_list = explode(',', $options['custom_fields_except_list']);
                                    if ( in_array('_featured', $fields_list) ){
                                        $options['is_update_featured_status'] = 0;
                                    }
                                    if ( in_array('_visibility', $fields_list) ){
                                        $options['is_update_catalog_visibility'] = 0;
                                    }
                                    break;
                            }
                        }
                    }
                    else{
                        $options['is_update_advanced_options'] = 0;
                        $options['is_update_featured_status'] = 0;
                        $options['is_update_catalog_visibility'] = 0;
                    }
                }

                // remove deprecated fields from custom fields list
                $options_to_update = array('custom_fields_list', 'custom_fields_only_list', 'custom_fields_except_list');
                foreach ($options_to_update as $option){
                    if ( ! empty($options[$option])){
                        $fields_list = is_array($options[$option]) ? $options[$option] : explode(',', $options[$option]);
                        foreach ($fields_list as $key => $value){
                            if (in_array($value, $remove_cf)){
                                unset($fields_list[$key]);
                            }
                        }
                        $options[$option] = is_array($options[$option]) ? $fields_list : implode(',', $fields_list);
                    }
                }
            }
        }

        public function init()
        {
            $this->load_plugin_textdomain();
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present
         *
         * @access public
         * @return void
         */
        public function load_plugin_textdomain() {

            $locale = apply_filters( 'plugin_locale', get_locale(), self::TEXT_DOMAIN );

            load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . "/i18n/languages" );
        }

        /**
         * pre-dispatching logic for admin page controllers
         */
        public function adminInit() {
            $input = new PMWI_Input();
            $page = strtolower($input->getpost('page', ''));
            if (preg_match('%^' . preg_quote(str_replace('_', '-', self::PREFIX), '%') . '([\w-]+)$%', $page)) {
                $this->adminDispatcher($page, strtolower($input->getpost('action', 'index')));
            }
            add_filter('plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

        }

        /**
         * Dispatch shorttag: create corresponding controller instance and call its index method
         * @param array $args Shortcode tag attributes
         * @param string $content Shortcode tag content
         * @param string $tag Shortcode tag name which is being dispatched
         * @return string
         */
        public function shortcodeDispatcher($args, $content, $tag) {

            $controllerName = self::PREFIX . preg_replace_callback('%(^|_).%', array($this, "replace_callback"), $tag);// capitalize first letters of class name parts and add prefix
            $controller = new $controllerName();
            if ( ! $controller instanceof PMWI_Controller) {
                throw new Exception("Shortcode `$tag` matches to a wrong controller type.");
            }
            ob_start();
            $controller->index($args, $content);
            return ob_get_clean();
        }

        public function replace_callback($matches){
            return strtoupper($matches[0]);
        }

        /**
         * Dispatch admin page: call corresponding controller based on get parameter `page`
         * The method is called twice: 1st time as handler `parse_header` action and then as admin menu item handler
         * @param string[optional] $page When $page set to empty string ealier buffered content is outputted, otherwise controller is called based on $page value
         */
        public function adminDispatcher($page = '', $action = 'index') {
            static $buffer = NULL;
            static $buffer_callback = NULL;
            if ('' === $page) {
                if ( ! is_null($buffer)) {
                    echo '<div class="wrap">';
                    echo $buffer;
                    do_action('PMWI_action_after');
                    echo '</div>';
                } elseif ( ! is_null($buffer_callback)) {
                    echo '<div class="wrap">';
                    call_user_func($buffer_callback);
                    do_action('PMWI_action_after');
                    echo '</div>';
                } else {
                    throw new Exception('There is no previousely buffered content to display.');
                }
            } else {
                $controllerName = preg_replace_callback('%(^' . preg_quote(self::PREFIX, '%') . '|_).%', array($this, "replace_callback"),str_replace('-', '_', $page));
                $actionName = str_replace('-', '_', $action);
                if (method_exists($controllerName, $actionName)) {

                    if ( ! get_current_user_id() or ! current_user_can('manage_options')) {
                        // This nonce is not valid.
                        die( 'Security check' );

                    } else {

                        $this->_admin_current_screen = (object)array(
                            'id' => $controllerName,
                            'base' => $controllerName,
                            'action' => $actionName,
                            'is_ajax' => isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest',
                            'is_network' => is_network_admin(),
                            'is_user' => is_user_admin(),
                        );
                        add_filter('current_screen', array($this, 'getAdminCurrentScreen'));

                        $controller = new $controllerName();
                        if ( ! $controller instanceof PMWI_Controller_Admin) {
                            throw new Exception("Administration page `$page` matches to a wrong controller type.");
                        }

                        if ($this->_admin_current_screen->is_ajax) { // ajax request
                            $controller->$action();
                            do_action('PMWI_action_after');
                            die(); // stop processing since we want to output only what controller is randered, nothing in addition
                        } elseif ( ! $controller->isInline) {
                            ob_start();
                            $controller->$action();
                            $buffer = ob_get_clean();
                        } else {
                            $buffer_callback = array($controller, $action);
                        }

                    }

                } else { // redirect to dashboard if requested page and/or action don't exist
                    wp_redirect(admin_url()); die();
                }
            }
        }

        protected $_admin_current_screen = NULL;
        public function getAdminCurrentScreen()
        {
            return $this->_admin_current_screen;
        }

        /**
         * Autoloader
         * It's assumed class name consists of prefix folloed by its name which in turn corresponds to location of source file
         * if `_` symbols replaced by directory path separator. File name consists of prefix folloed by last part in class name (i.e.
         * symbols after last `_` in class name)
         * When class has prefix it's source is looked in `models`, `controllers`, `shortcodes` folders, otherwise it looked in `core` or `library` folder
         *
         * @param string $className
         * @return bool
         */
        public function autoload($className) {
            $is_prefix = false;
            $filePath = str_replace('_', '/', preg_replace('%^' . preg_quote(self::PREFIX, '%') . '%', '', strtolower($className), 1, $is_prefix)) . '.php';
            if ( ! $is_prefix) { // also check file with original letter case
                $filePathAlt = $className . '.php';
            }
            foreach ($is_prefix ? array('models', 'controllers', 'shortcodes', 'classes') : array('libraries') as $subdir) {
                $path = self::ROOT_DIR . '/' . $subdir . '/' . $filePath;
                if (is_file($path)) {
                    require $path;
                    return TRUE;
                }
                if ( ! $is_prefix) {
                    $pathAlt = self::ROOT_DIR . '/' . $subdir . '/' . $filePathAlt;
                    if (is_file($pathAlt)) {
                        require $pathAlt;
                        return TRUE;
                    }
                }
            }

            return FALSE;
        }

        /**
         * Plugin activation logic
         */
        public function activation() {
            // Uncaught exception doesn't prevent plugin from being activated, therefore replace it with fatal error so it does.
            set_exception_handler(function($e){trigger_error($e->getMessage(), E_USER_ERROR);});
        }

        public function plugin_row_meta($links, $file){
            if ( $file == plugin_basename( __FILE__ ) ) {
                $row_meta = array(
                    'pro'    => '<a href="http://www.wpallimport.com/woocommerce-product-import/?utm_source=import-wooco-products-addon-free&utm_medium=wp-plugins-page&utm_campaign=upgrade-to-pro" target="_blank" title="' . esc_attr( __( 'WP All Import - WooCommerce Add-On Pro Version', self::TEXT_DOMAIN ) ) . '">' . __( 'Pro Version', self::TEXT_DOMAIN ) . '</a>',
                );

                return array_merge( $links, $row_meta );
            }

            return (array) $links;
        }

        /**
         * Method returns default import options, main utility of the method is to avoid warnings when new
         * option is introduced but already registered imports don't have it
         */
        public static function get_default_import_options() {
            return array(
                'is_multiple_product_type' => 'yes',
                'multiple_product_type' => 'simple',
                'single_product_type' => '',
                'is_product_virtual' => 'no',
                'single_product_virtual' => '',
                'is_product_downloadable' => 'no',
                'single_product_downloadable' => '',
                'is_product_enabled' => 'yes',
                'single_product_enabled' => '',
                'is_variation_enabled' => 'yes',
                'single_variation_enabled' => '',
                'is_product_featured' => 'no',
                'single_product_featured' => '',
                'is_product_visibility' => 'visible',
                'single_product_visibility' => '',
                'single_product_sku' => '',
                'single_product_url' => '',
                'single_product_button_text' => '',
                'single_product_regular_price' => '',
                'single_product_sale_price' => '',
                'single_product_files' => '',
                'single_product_files_names' => '',
                'single_product_download_limit' => '',
                'single_product_download_expiry' => '',
                'single_product_download_type' => '',
                'is_multiple_product_tax_status' => 'yes',
                'multiple_product_tax_status' => 'none',
                'single_product_tax_status' => '',
                'is_multiple_product_tax_class' => 'yes',
                'multiple_product_tax_class' => '',
                'single_product_tax_class' => '',
                'is_product_manage_stock' => 'no',
                'single_product_manage_stock' => '',
                'single_product_stock_qty' => '',
                'product_stock_status' => 'auto',
                'single_product_stock_status' => '',
                'product_allow_backorders' => 'no',
                'single_product_allow_backorders' => '',
                'product_sold_individually' => 'no',
                'single_product_sold_individually' => '',
                'single_product_weight' => '',
                'single_product_length' => '',
                'single_product_width' => '',
                'single_product_height' => '',
                'is_multiple_product_shipping_class' => 'yes',
                'multiple_product_shipping_class' => '',
                'single_product_shipping_class' => '',
                'is_multiple_grouping_product' => 'yes',
                'multiple_grouping_product' => '',
                'single_grouping_product' => '',
                'single_product_up_sells' => '',
                'single_product_cross_sells' => '',
                'attribute_name' => array(),
                'attribute_value' => array(),
                'in_variations' => array(),
                'is_visible' => array(),
                'is_taxonomy' => array(),
                'create_taxonomy_in_not_exists' => array(),

                'is_advanced' => array(),
                'advanced_in_variations' => array(),
                'advanced_in_variations_xpath' => array(),
                'advanced_is_visible' => array(),
                'advanced_is_visible_xpath' => array(),
                'advanced_is_taxonomy' => array(),
                'advanced_is_taxonomy_xpath' => array(),
                'advanced_is_create_terms' => array(),
                'advanced_is_create_terms_xpath' => array(),

                'single_product_purchase_note' => '',
                'single_product_menu_order' => 0,
                'is_product_enable_reviews' => 'no',
                'single_product_enable_reviews' => '',
                'single_product_id' => '',
                'single_product_parent_id' => '',
                'single_product_id_first_is_parent_id' => '',
                'single_product_first_is_parent_id_parent_sku' => '',
                'single_product_id_first_is_parent_title' => '',
                'single_product_first_is_parent_title_parent_sku' => '',
                'single_product_id_first_is_variation' => '',
                '_virtual' => 0,
                '_downloadable' => 0,
                'is_regular_price_shedule' => 0,
                'single_sale_price_dates_from' => 'now',
                'single_sale_price_dates_to' => 'now',
                'product_files_delim' => ',',
                'product_files_names_delim' => ',',
                'matching_parent' => 'auto',
                'parent_indicator' => 'custom field',
                'custom_parent_indicator_name' => '',
                'custom_parent_indicator_value' => '',
                'missing_records_stock_status' => 0,
                'variations_xpath' => '',
                '_variable_virtual' => '',
                '_variable_downloadable' => '',
                'variable_stock' => '',
                'variable_regular_price' => '',
                'variable_sale_price' => '',
                'is_variable_sale_price_shedule' => 0,
                'variable_sale_price_dates_from' => '',
                'variable_sale_price_dates_to' => '',
                'variable_weight' => '',
                'variable_length' => '',
                'variable_width' => '',
                'variable_height' => '',
                'variable_shipping_class' => '',
                'variable_tax_class' => '',
                'variable_file_paths' => '',
                'variable_file_names' => '',
                'variable_download_limit' => '',
                'variable_download_expiry' => '',
                'is_variable_product_virtual' => 'no',
                'is_variable_product_manage_stock' => 'no',
                'is_multiple_variable_product_shipping_class' => 'yes',
                'multiple_variable_product_shipping_class' => '',
                'single_variable_product_shipping_class' => '',
                'is_multiple_variable_product_tax_class' => 'yes',
                'multiple_variable_product_tax_class' => 'parent',
                'single_variable_product_tax_class' => '',
                'variable_stock_status' => 'instock',
                'single_variable_stock_status' => '',
                'variable_allow_backorders' => 'no',
                'single_variable_allow_backorders' => '',
                'is_variable_product_downloadable' => 'no',
                'single_variable_product_downloadable' => '',
                'variable_attribute_name' => array(),
                'variable_attribute_value' => array(),
                'variable_in_variations' => array(),
                'variable_is_visible' => array(),
                'variable_is_taxonomy' => array(),
                'variable_create_taxonomy_in_not_exists' => array(),
                'variable_product_files_delim' => ',',
                'variable_product_files_names_delim' => ',',
                'variable_image' => '',
                'variable_sku' => '',
                'is_variable_product_enabled' => 'yes',
                'single_variable_product_enabled' => '',
                'link_all_variations' => 0,
                'variable_stock_use_parent' => 0,
                'variable_regular_price_use_parent' => 0,
                'variable_sale_price_use_parent' => 0,
                'variable_sale_dates_use_parent' => 0,
                'variable_weight_use_parent' => 0,
                'single_variable_product_virtual' => '',
                'single_variable_product_virtual_use_parent' => 0,
                'single_variable_product_manage_stock' => '',
                'single_variable_product_manage_stock_use_parent' => 0,
                'variable_dimensions_use_parent' => 0,
                'variable_image_use_parent' => 0,
                'single_variable_product_shipping_class_use_parent' => 0,
                'single_variable_product_tax_class_use_parent' => 0,
                'single_variable_product_downloadable_use_parent' => 0,
                'variable_download_limit_use_parent' => 0,
                'variable_download_expiry_use_parent' => 0,

                'single_product_variation_description' => '',
                'variable_description' => '',
                'variable_description_use_parent' => 0,

                'first_is_parent' => 'yes',
                'single_product_whosale_price' => '',
                'variable_whosale_price' => '',
                'variable_whosale_price_use_parent' => 0,
                'disable_auto_sku_generation' => 0,
                'is_default_attributes' => 0,
                'default_attributes_type' => 'first',
                'disable_sku_matching' => 1,
                'disable_prepare_price' => 1,
                'prepare_price_to_woo_format' => 0,
                'convert_decimal_separator' => 1,
                'grouping_indicator' => 'xpath',
                'custom_grouping_indicator_name' => '',
                'custom_grouping_indicator_value' => '',
                'is_update_product_type' => 1,
                'make_simple_product' => 1,
                'variable_sku_add_parent' => 0,
                'set_parent_stock' => 0,
                'single_product_regular_price_adjust' => '',
                'single_product_regular_price_adjust_type' => '%',
                'single_product_sale_price_adjust' => '',
                'single_product_sale_price_adjust_type' => '%',

                'is_update_attributes' => 1,
                'update_attributes_logic' => 'full_update',
                'attributes_list' => array(),
                'attributes_only_list' => array(),
                'attributes_except_list' => array(),

                'is_variation_product_manage_stock' => 'no',
                'single_variation_product_manage_stock' => '',
                'variation_stock' => '',
                'variation_stock_status' => 'auto',
                'put_variation_image_to_gallery' => 0,
                'import_additional_variation_images' => 0,
                'single_variation_stock_status' => '',
                'single_product_low_stock_amount' => '',
                'pmwi_order' => array(
                    'status' => 'wc-pending',
                    'status_xpath' => '',
                    'date' => 'now',
                    'billing_source' => 'guest',
                    'billing_source_match_by' => 'username',
                    'billing_source_username' => '',
                    'billing_source_email' => '',
                    'billing_source_id' => '',
                    'billing_source_cf_name' => '',
                    'billing_source_cf_value' => '',
                    'billing_first_name' => '',
                    'billing_last_name' => '',
                    'billing_company' => '',
                    'billing_address_1' => '',
                    'billing_address_2' => '',
                    'billing_city' => '',
                    'billing_postcode' => '',
                    'billing_country' => '',
                    'billing_state' => '',
                    'billing_email' => '',
                    'billing_phone' => '',
                    'guest_billing_first_name' => '',
                    'guest_billing_last_name' => '',
                    'guest_billing_company' => '',
                    'guest_billing_address_1' => '',
                    'guest_billing_address_2' => '',
                    'guest_billing_city' => '',
                    'guest_billing_postcode' => '',
                    'guest_billing_country' => '',
                    'guest_billing_state' => '',
                    'guest_billing_email' => '',
                    'guest_billing_phone' => '',
                    'is_guest_matching' => 0,
                    'shipping_source' => 'copy',
                    'shipping_first_name' => '',
                    'shipping_last_name' => '',
                    'shipping_company' => '',
                    'shipping_address_1' => '',
                    'shipping_address_2' => '',
                    'shipping_city' => '',
                    'shipping_postcode' => '',
                    'shipping_country' => '',
                    'shipping_state' => '',
                    'shipping_email' => '',
                    'shipping_phone' => '',
                    'copy_from_billing' => 0,
                    'customer_provided_note' => '',
                    'payment_method' => '',
                    'payment_method_xpath' => '',
                    'transaction_id' => '',
                    'products_repeater_mode' => 'csv',
                    'products_repeater_mode_separator' => '|',
                    'products_repeater_mode_foreach' => '',
                    'products_source' => 'existing',
                    'products' => array(),
                    'manual_products' => array(),
                    'fees_repeater_mode' => 'csv',
                    'fees_repeater_mode_separator' => '|',
                    'fees_repeater_mode_foreach' => '',
                    'fees' => array(),
                    'coupons_repeater_mode' => 'csv',
                    'coupons_repeater_mode_separator' => '|',
                    'coupons_repeater_mode_foreach' => '',
                    'coupons' => array(),
                    'shipping_repeater_mode' => 'csv',
                    'shipping_repeater_mode_separator' => '|',
                    'shipping_repeater_mode_foreach' => '',
                    'shipping' => array(),
                    'taxes_repeater_mode' => 'csv',
                    'taxes_repeater_mode_separator' => '|',
                    'taxes_repeater_mode_foreach' => '',
                    'taxes' => array(),
                    'order_total_logic' => 'auto',
                    'order_total_xpath' => '',
                    'order_refund_amount' => '',
                    'order_refund_reason' => '',
                    'order_refund_date' => 'now',
                    'order_refund_issued_source' => 'existing',
                    'order_refund_issued_match_by' => 'username',
                    'order_refund_issued_username' => '',
                    'order_refund_issued_email' => '',
                    'order_refund_issued_cf_name' => '',
                    'order_refund_issued_cf_value' => '',
                    'order_refund_issued_id' => '',
                    'notes_repeater_mode' => 'csv',
                    'notes_repeater_mode_separator' => '|',
                    'notes_repeater_mode_foreach' => '',
                    'notes' => array(),
                ),
                'is_update_billing_details' => 1,
                'is_update_shipping_details' => 1,
                'is_update_payment' => 1,
                'is_update_notes' => 1,
                'is_update_products' => 1,
                'update_products_logic' => 'full_update',
                'is_update_fees' => 1,
                'is_update_coupons' => 1,
                'is_update_shipping' => 1,
                'is_update_taxes' => 1,
                'is_update_refunds' => 1,
                'is_update_total' => 1,
                'do_not_send_order_notifications' => 1,
                'is_update_advanced_options' => 1,
                'is_update_catalog_visibility' => 1,
                'is_update_featured_status' => 1,
                'existing_parent_product_matching_logic' => 'custom field',
                'existing_parent_product_title' => '',
                'existing_parent_product_cf_name' => '_sku',
                'existing_parent_product_cf_value' => ''
            );
        }
    }

    PMWI_Plugin::getInstance();
}