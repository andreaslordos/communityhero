<?php
/**
 * Utils functions for woo checkout field editor.
 *
 * @link       https://themehigh.com
 * @since      1.3.6
 *
 * @package    woo-checkout-field-editor-pro
 * @subpackage woo-checkout-field-editor-pro/classes
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('THWCFD_Utils')):

class THWCFD_Utils {
	public function __construct() {
		
	}

	public static function is_address_field($name){
		$address_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
		);

		if($name && in_array($name, $address_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field($name){
		$default_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
			'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field_name($field_name){
		$default_fields = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_reserved_field_name( $field_name ){
		$reserved_names = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);
		
		if($name && in_array($name, $reserved_names)){
			return true;
		}
		return false;
	}

	public static function is_valid_field($field){
		$return = false;
		if(is_array($field)){
			$return = true;
		}
		return $return;
	}

	public static function is_enabled($field){
		$enabled = false;
		if(is_array($field)){
			$enabled = isset($field['enabled']) && $field['enabled'] == false ? false : true;
		}
		return $enabled;
	}

	public static function is_custom_field($field){
		$return = false;
		if(isset($field['custom']) && $field['custom']){
			$return = true;
		}
		return $return;
	}

	public static function is_active_custom_field($field){
		$return = false;
		if(self::is_valid_field($field) && self::is_enabled($field) && self::is_custom_field($field)){
			$return = true;
		}
		return $return;
	}

	public static function update_fields($key, $fields){
		$result = update_option('wc_fields_' . $key, $fields);
		return $result;
	}

	public static function get_fields($key){
		$fields = get_option('wc_fields_'. $key, array());
		$fields = is_array($fields) ? array_filter($fields) : array();
		
		if(empty($fields) || sizeof($fields) == 0){
			if($key === 'billing' || $key === 'shipping'){
				$fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $key . '_');

			} else if($key === 'additional'){
				$fields = array(
					'order_comments' => array(
						'type'        => 'textarea',
						'class'       => array('notes'),
						'label'       => __('Order Notes', 'woocommerce'),
						'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'woocommerce')
					)
				);
			}
			$fields = self::prepare_default_fields($fields);
		}
		return $fields;
	}

	private static function prepare_default_fields($fields){
		foreach ($fields as $key => $value) {
			$fields[$key]['custom'] = 0;
			$fields[$key]['enabled'] = 1;
			$fields[$key]['show_in_email'] = 1;
			$fields[$key]['show_in_order'] = 1;
		}
		return $fields;
	}

	public static function get_checkout_fields($order=false){
		$fields = array();
		$needs_shipping = true;

		if($order){
			$needs_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address() ? true : false;
		}
		
		if($needs_shipping){
			$fields = array_merge(self::get_fields('billing'), self::get_fields('shipping'), self::get_fields('additional'));
		}else{
			$fields = array_merge(self::get_fields('billing'), self::get_fields('additional'));
		}

		return $fields;
	}

	public static function prepare_field_options($options){
		if(is_string($options)){
			$options = array_map('trim', explode('|', $options));
		}
		return is_array($options) ? $options : array();
	}

	public static function prepare_options_array($options_json){
		$options_json = rawurldecode($options_json);
		$options_arr = json_decode($options_json, true);
		$options = array();
		
		if($options_arr){
			foreach($options_arr as $option){
				$okey = isset($option['key']) ? $option['key'] : '';
				$otext = isset($option['text']) ? $option['text'] : '';
				//$okey = $okey ? $okey : $otext;

				//if($okey || $otext){
					$options[$okey] = $otext;
				//}
			}
		}
		return $options;
	}

	public static function prepare_options_json($options){
		$options_json = '';
		if(is_array($options) && !empty($options)){
			$options_arr = array();

			foreach($options as $okey => $otext){
				//$okey = $okey ? $okey : $otext;

				//if($okey && $otext){
					array_push($options_arr, array("key" => $okey, "text" => $otext));
					//array_push($options_arr, array("key" => esc_attr($okey), "text" => esc_attr($otext)));
				//}
			}

			$options_json = json_encode($options_arr);
			$options_json = rawurlencode($options_json);
		}
		return $options_json;
	}

	public static function get_option_text($field, $value){
		$type = isset($field['type']) ? $field['type'] : false;

		if($type === 'select' || $type === 'radio'){
			$options = isset($field['options']) ? $field['options'] : array();

			if(isset($options[$value]) && !empty($options[$value])){
				$value = $options[$value];
			}
		}

		return $value;
	}

	public static function prepare_field_priority($fields, $order, $new=false){
		$priority = '';
		if(!$new){
			$priority = is_numeric($order) ? ($order+1)*10 : false;
		}

		if(!$priority){
			$max_priority = self::get_max_priority($fields);
			$priority = is_numeric($max_priority) ? $max_priority+10 : false;
		}
		return $priority;
	}

	private static function get_max_priority($fields){
		$max_priority = 0;
		if(is_array($fields)){
			foreach ($fields as $key => $value) {
				$priority = isset($value['priority']) ? $value['priority'] : false;
				$max_priority = is_numeric($priority) && $priority > $max_priority ? $priority : $max_priority;
			}
		}
		return $max_priority;
	}

	public static function sort_fields($fields){
		uasort($fields, 'wc_checkout_fields_uasort_comparison');
		return $fields;
	}

	public static function sort_fields_by_order($a, $b){
	    if(!isset($a['order']) || $a['order'] == $b['order']){
	        return 0;
	    }
	    return ($a['order'] < $b['order']) ? -1 : 1;
	}

	public static function get_order_id($order){
		$order_id = false;
		if(self::woo_version_check()){
			$order_id = $order->get_id();
		}else{
			$order_id = $order->id;
		}
		return $order_id;
	}

	public static function woo_version_check( $version = '3.0' ) {
	  	if(function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}

	public static function wcfd_version_check( $version = '1.3.6' ) {
		if(THWCFD_VERSION && version_compare( THWCFD_VERSION, $version, ">=" ) ) {
	  		return true;
		}
	  	return false;
	}

	public static function is_blank($value) {
		return empty($value) && !is_numeric($value);
	}

	/***********************************
	 ----- i18n functions - START ------
	 ***********************************/
	public static function t($text){
		if(!empty($text)){	
			$otext = $text;						
			$text = __($text, 'woo-checkout-field-editor-pro');	
			if($text === $otext){
				$text = __($text, 'woocommerce');
			}
		}
		return $text;
	}

	public static function et($text){
		if(!empty($text)){	
			$otext = $text;						
			$text = __($text, 'woo-checkout-field-editor-pro');	
			if($text === $otext){
				$text = __($text, 'woocommerce');
			}
		}
		echo $text;
	}
}

endif;