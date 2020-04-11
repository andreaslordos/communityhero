<?php
/**
 * Woo Checkout Field Editor Settings
 *
 * @author   ThemeHigh
 * @category Admin
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('THWCFD_Settings')) :

class THWCFD_Settings {
	public function __construct() {
		
	}
	
	public function enqueue_styles_and_scripts($hook) {
		/*if(strpos($hook, 'woocommerce_page_checkout_form_designer') === false) {
			return;
		}*/
		if(strpos($hook, 'page_checkout_form_designer') === false) {
			return;
		}

		$deps = array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin', 'select2', 'wp-color-picker');

		wp_enqueue_style('woocommerce_admin_styles');
		wp_enqueue_style('thwcfd-admin-style', THWCFD_ASSETS_URL . 'css/thwcfd-admin.css', THWCFD_VERSION);
		wp_enqueue_script('thwcfd-admin-script', THWCFD_ASSETS_URL . 'js/thwcfd-admin.js', $deps, THWCFD_VERSION, true);
	}

	public function wcfd_capability() {
		$allowed = array('manage_woocommerce', 'manage_options');
		$capability = apply_filters('thwcfd_required_capability', 'manage_woocommerce');

		if(!in_array($capability, $allowed)){
			$capability = 'manage_woocommerce';
		}
		return $capability;
	}
	
	public function admin_menu() {
		$capability = $this->wcfd_capability();
		$this->screen_id = add_submenu_page('woocommerce', __('WooCommerce Checkout Form Designer', 'woo-checkout-field-editor-pro'), __('Checkout Form', 'woo-checkout-field-editor-pro'), $capability, 'checkout_form_designer', array($this, 'output_settings'));

		//add_action('admin_print_scripts-'. $this->screen_id, array($this, 'enqueue_admin_scripts'));
	}
	
	public function add_screen_id($ids){
		$ids[] = 'woocommerce_page_checkout_form_designer';
		$ids[] = strtolower(__('WooCommerce', 'woo-checkout-field-editor-pro')) .'_page_checkout_form_designer';

		return $ids;
	}

	public function plugin_action_links($links) {
		$settings_link = '<a href="'.admin_url('admin.php?page=checkout_form_designer').'">'. __('Settings', 'woo-checkout-field-editor-pro') .'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	/*public function plugin_row_meta( $links, $file ) {
		if(THWCFE_BASE_NAME == $file) {
			$doc_link = esc_url('https://www.themehigh.com/help-guides/woocommerce-checkout-field-editor/');
			$support_link = esc_url('https://www.themehigh.com/help-guides/');
				
			$row_meta = array(
				'docs' => '<a href="'.$doc_link.'" target="_blank" aria-label="'.__('View plugin documentation', 'woo-checkout-field-editor-pro').'">'.__('Docs', 'woo-checkout-field-editor-pro').'</a>',
				'support' => '<a href="'.$support_link.'" target="_blank" aria-label="'. __('Visit premium customer support', 'woo-checkout-field-editor-pro') .'">'. __('Premium support', 'woo-checkout-field-editor-pro') .'</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}*/

	private function output_premium_version_notice(){
		?>
        <div id="message" class="wc-connect updated thpladmin-notice">
            <div class="squeezer">
            	<table>
                	<tr>
                    	<td width="70%">
                        	<p><strong><i>WooCommerce Checkout Field Editor Pro</i></strong> premium version provides more features to design your checkout page.</p>
                            <ul>
                            	<li>17 field types available,  (<i>Text, Hidden, Password, Telephone, Email, Number, Textarea, Radio, Checkbox, Checkbox Group, Select, Multi-select, Date Picker, Time Picker, File Upload, Heading, Label</i>).</li>
                                <li>Conditionally display fields based on cart items and other field(s) values.</li>
                                <li>Add an extra cost to the cart total based on field selection.</li>
                                <li>Custom validation rules using RegEx.</li>
                                <li>Option to add more sections in addition to the core sections (billing, shipping and additional) in checkout page.</li>
                            </ul>
                        </td>
                        <td>
                        	<a target="_blank" href="https://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/">
                            	<img src="<?php echo THWCFD_ASSETS_URL ?>css/upgrade-btn.png" />
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
	}

	private function output_review_request_link(){
		?>
		<p>If you like our <strong>Checkout Field Editor</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-checkout-field-editor-pro/reviews?rate=5#new-post" target="_blank" aria-label="five star" data-rated="Thanks :)">★★★★★</a> rating. A huge thanks in advance!</p>
		<?php 
	}
	
	public function output_settings(){
		$this->output_premium_version_notice();
		$this->output_review_request_link();

		$tab = $this->get_current_tab();
		if($tab === 'fields'){
			$general_settings = THWCFD_Settings_General::instance();	
			$general_settings->render_page();
		}
	}

	public function get_current_tab(){
		return isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
	}
}

endif;

