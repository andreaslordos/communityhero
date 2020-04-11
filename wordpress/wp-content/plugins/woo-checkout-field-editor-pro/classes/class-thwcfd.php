<?php
/**
 * The file that defines the core plugin class.
 *
 * @link       https://themehigh.com
 * @since      1.3.6
 *
 * @package    woo-checkout-field-editor-pro
 * @subpackage woo-checkout-field-editor-pro/classes
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('THWCFD')):

class THWCFD {
	const TEXT_DOMAIN = 'woo-checkout-field-editor-pro';

	public function __construct() {
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		if(!function_exists('is_plugin_active')){
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		require_once THWCFD_PATH . 'classes/class-thwcfd-utils.php';
		require_once THWCFD_PATH . 'classes/class-thwcfd-settings.php';
		require_once THWCFD_PATH . 'classes/class-thwcfd-settings-general.php';
		require_once THWCFD_PATH . 'classes/class-thwcfd-checkout.php';
	}

	private function set_locale() {
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
	}

	public function load_plugin_textdomain(){
		$locale = apply_filters('plugin_locale', get_locale(), self::TEXT_DOMAIN);
	
		load_textdomain(self::TEXT_DOMAIN, WP_LANG_DIR.'/woo-checkout-field-editor-pro/'.self::TEXT_DOMAIN.'-'.$locale.'.mo');
		load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname(THWCFD_BASE_NAME) . '/languages/');
	}
	
	private function define_admin_hooks() {
		$plugin_admin = new THWCFD_Settings();

		add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles_and_scripts'));
		add_action('admin_menu', array($plugin_admin, 'admin_menu'));
		add_filter('woocommerce_screen_ids', array($plugin_admin, 'add_screen_id'));
		add_filter('plugin_action_links_'.THWCFD_BASE_NAME, array($plugin_admin, 'plugin_action_links'));
		//add_filter('plugin_row_meta', array($plugin_admin, 'plugin_row_meta'), 10, 2);

		$general_settings = new THWCFD_Settings_General();
		add_action('after_setup_theme', array($general_settings, 'define_admin_hooks'));
	}

	private function define_public_hooks() {
		//if(!is_admin() || (defined( 'DOING_AJAX' ) && DOING_AJAX)){
			$plugin_checkout = new THWCFD_Checkout();
			add_action('wp_enqueue_scripts', array($plugin_checkout, 'enqueue_styles_and_scripts'));
			add_action('after_setup_theme', array($plugin_checkout, 'define_public_hooks'));
		//}
	}
}

endif;