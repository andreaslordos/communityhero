<?php
/**
 * Woo Checkout Field Editor Settings General
 *
 * @link       https://themehigh.com
 * @since      1.3.6
 *
 * @package    woo-checkout-field-editor-pro
 * @subpackage woo-checkout-field-editor-pro/classes
 */

defined( 'ABSPATH' ) || exit;

if(!class_exists('THWCFD_Settings_General')):

class THWCFD_Settings_General {
	protected static $_instance = null;

	protected $tabs = '';
	protected $sections = '';

	public function __construct() {
		$this->tabs = array( 'fields' => 'Checkout Fields');
		$this->sections = array('billing' => 'Billing Fields', 'shipping' => 'Shipping Fields', 'additional' => 'Additional Fields');
	}

	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function define_admin_hooks(){
		// Show in order details page
		add_action('woocommerce_admin_order_data_after_order_details', array($this, 'order_data_after_order_details'), 20, 1);
		add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'order_data_after_billing_address'), 20, 1);
		add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'order_data_after_shipping_address'), 20, 1);
	}

	public function reset_to_default() {
		delete_option('wc_fields_billing');
		delete_option('wc_fields_shipping');
		delete_option('wc_fields_additional');
		echo '<div class="updated"><p>'. __('SUCCESS: Checkout fields successfully reset', 'woo-checkout-field-editor-pro') .'</p></div>';
	}

	public function render_page(){
		if(isset($_POST['reset_fields']))
			echo $this->reset_to_default();	
			
		$this->output_tabs();
		$this->output_sections();
		$this->output_content();
	}

	public function render_checkout_fields_heading_row(){
		?>
		<th class="sort"></th>
		<th class="check-column"><input type="checkbox" style="margin:0px 4px -1px -1px;" onclick="thwcfdSelectAllCheckoutFields(this)"/></th>
		<th class="name"><?php _e('Name', 'woo-checkout-field-editor-pro'); ?></th>
		<th class="id"><?php _e('Type', 'woo-checkout-field-editor-pro'); ?></th>
		<th><?php _e('Label', 'woo-checkout-field-editor-pro'); ?></th>
		<th><?php _e('Placeholder', 'woo-checkout-field-editor-pro'); ?></th>
		<th><?php _e('Validations', 'woo-checkout-field-editor-pro'); ?></th>
        <th class="status"><?php _e('Required', 'woo-checkout-field-editor-pro'); ?></th>
		<th class="status"><?php _e('Enabled', 'woo-checkout-field-editor-pro'); ?></th>	
        <th class="action"><?php _e('Edit', 'woo-checkout-field-editor-pro'); ?></th>	
        <?php
	}
	
	public function render_actions_row($section){
		?>
        <th colspan="6">
            <button type="button" class="button button-primary" onclick="thwcfdOpenNewFieldForm('<?php echo $section; ?>')">+ <?php _e( 'Add field', 'woo-checkout-field-editor-pro' ); ?></button>
            <button type="button" class="button" onclick="thwcfdRemoveSelectedFields()"><?php _e('Remove', 'woo-checkout-field-editor-pro'); ?></button>
            <button type="button" class="button" onclick="thwcfdEnableSelectedFields()"><?php _e('Enable', 'woo-checkout-field-editor-pro'); ?></button>
            <button type="button" class="button" onclick="thwcfdDisableSelectedFields()"><?php _e('Disable', 'woo-checkout-field-editor-pro'); ?></button>
        </th>
        <th colspan="4">
        	<input type="submit" name="save_fields" class="button-primary" value="<?php _e( 'Save changes', 'woo-checkout-field-editor-pro' ) ?>" style="float:right" />
            <input type="submit" name="reset_fields" class="button" value="<?php _e( 'Reset to default fields', 'woo-checkout-field-editor-pro' ) ?>" style="float:right; margin-right: 5px;" 
			onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.');"/>
        </th>  
    	<?php 
	}

	public function output_content() {
		$section = $this->get_current_section();
		$action = isset($_POST['f_action']) ? $_POST['f_action'] : false;

		if($action === 'new')
			echo $this->save_or_update_field($section, $action);	
			
		if($action === 'edit')
			echo $this->save_or_update_field($section, $action);
		
		if(isset($_POST['save_fields']))
			echo $this->save_fields($section);

		$fields = THWCFD_Utils::get_fields($section);	
	
		?>            
        <div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>
		<form method="post" id="wcfd_checkout_fields_form" action="">
        	<table id="thwcfd_checkout_fields" class="thpladmin_fields_table" cellspacing="0">
				<thead>
                	<tr><?php $this->render_actions_row($section); ?></tr>
                	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>						
				</thead>
                <tfoot>
                	<tr><?php $this->render_checkout_fields_heading_row(); ?></tr>
					<tr><?php $this->render_actions_row($section); ?></tr>
				</tfoot>
				<tbody class="ui-sortable">
	                <?php 
					$i=0;
					foreach( $fields as $name => $field ) :
						$type = isset($field['type']) ? $field['type'] : '';
						$label = isset($field['label']) ? $field['label'] : '';
						$placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
						$validate = isset($field['validate']) ? $field['validate'] : '';
						$required = isset($field['required']) && $field['required'] ? 1 : 0;
						$enabled = isset($field['enabled']) && $field['enabled'] ? 1 : 0;
						$custom = isset($field['custom']) && $field['custom'] ? 1 : 0;

						$validate = is_array($validate) ? implode(",", $validate) : '';

						$required_status = $required ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';
						$enabled_status = $enabled ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';

						$props_json = htmlspecialchars($this->get_property_set_json($name, $field));
						//$options_json = isset($field['options_json']) && $field['options_json'] ? htmlspecialchars($field['options_json']) : '';

						$options_json = '';
						if($type === 'select' || $type === 'radio'){
							$options = isset($field['options']) ? $field['options'] : '';
							$options_json = THWCFD_Utils::prepare_options_json($options);
						}
					?>
						<tr class="row_<?php echo $i; echo $enabled ? '' : ' thpladmin-disabled' ?>">
	                    	<td width="1%" class="sort ui-sortable-handle">
	                    		<input type="hidden" name="f_name[<?php echo $i; ?>]" class="f_name" value="<?php echo esc_attr($name); ?>" />
	                    		<input type="hidden" name="f_name_new[<?php echo $i; ?>]" class="f_name_new" value="" />
								<input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
								<input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
								<input type="hidden" name="f_enabled[<?php echo $i; ?>]" class="f_enabled" value="<?php echo $enabled; ?>" />
								<input type="hidden" name="f_props[<?php echo $i; ?>]" class="f_props" value='<?php echo $props_json; ?>' />
								<input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value='<?php echo $options_json; ?>' />
	                        </td>
	                        <td class="td_select"><input type="checkbox" name="select_field"/></td>
	                        <td class="td_name"><?php echo esc_attr( $name ); ?></td>
	                        <td class="td_type"><?php echo $type; ?></td>
	                        <td class="td_label"><?php THWCFD_Utils::et($label); ?></td>
	                        <td class="td_placeholder"><?php THWCFD_Utils::et($placeholder); ?></td>
	                        <td class="td_validate"><?php echo $validate; ?></td>
	                        <td class="td_required status"><?php echo $required_status; ?></td>
	                        <td class="td_enabled status"><?php echo $enabled_status; ?></td>
	                        <td class="td_edit action">
	                        	<button type="button" class="button action-btn f_edit_btn" <?php echo($enabled ? '' : 'disabled') ?> 
	                            onclick="thwcfdOpenEditFieldForm(this, <?php echo $i; ?>)"><?php _e('Edit', 'woo-checkout-field-editor-pro'); ?></button>
	                        </td>
	                	</tr>
	                <?php 
	                	$i++; 
	                	endforeach; 
	                ?>
            	</tbody>
			</table> 
        </form>
        <?php
        $this->output_add_field_form_pp();
		$this->output_edit_field_form_pp();
		//$this->output_popup_form_field_fragments();		
	}

	public function get_property_set_json($name, $field){
		$json = '';
		if(is_array($field)){
			foreach($field as $pname => $pvalue){
				$pvalue = is_array($pvalue) ? implode(',', $pvalue) : $pvalue;
				$pvalue = is_string($pvalue) ? esc_attr($pvalue) : $pvalue;
				
				$field[$pname] = $pvalue;
			}

			$field['name'] = $name;
			$json = json_encode($field);
		}
		return $json;
	}

	private function save_or_update_field($section, $action) {
		try {
			$result = false;
			$fields = THWCFD_Utils::get_fields($section);
			$field = $this->prepare_field_from_posted_data($_POST);
			$name = isset($field['name']) ? $field['name'] : false;

			if($name){
				if($action === 'new'){
					$priority = THWCFD_Utils::prepare_field_priority($fields, false, true);
					$field['custom'] = 1;
					$field['priority'] = $priority;
				}else{
					$oname = isset($_POST['i_oname']) ? trim(stripslashes($_POST['i_oname'])) : false;
					if($name && $oname && $name !== $oname ){
						unset($fields[$oname]);
					}
				}

				$fields[$name] = $field;
			}
			
			$result = THWCFD_Utils::update_fields($section, $fields);
			
			if($result == true) {
				echo '<div class="updated"><p>'. __('Your changes were saved.', 'woo-checkout-field-editor-pro') .'</p></div>';
			}else {
				echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).', 'woo-checkout-field-editor-pro') .'</p></div>';
			}
		} catch (Exception $e) {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error.', 'woo-checkout-field-editor-pro') .'</p></div>';
		}
	}
	
	private function save_fields($section) {
		try {
			$f_names = !empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();	
			if(empty($f_names)){
				echo '<div class="error"><p> '. __('Your changes were not saved due to no fields found.', 'woo-checkout-field-editor-pro') .'</p></div>';
				return;
			}
			
			$f_order   = !empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();	
			$f_deleted = !empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
			$f_enabled = !empty( $_POST['f_enabled'] ) ? $_POST['f_enabled'] : array();
						
			$fields = THWCFD_Utils::get_fields($section);
			
			$max = max( array_map( 'absint', array_keys( $f_names ) ) );
			for($i = 0; $i <= $max; $i++) {
				$name = $f_names[$i];
				
				if(isset($fields[$name])){
					$is_deleted = isset($f_deleted[$i]) && $f_deleted[$i] ? true : false;

					if($is_deleted){
						unset($fields[$name]);
						continue;
					}

					$order = isset($f_order[$i]) ? trim(stripslashes($f_order[$i])) : 0;
					$enabled = isset($f_enabled[$i]) ? trim(stripslashes($f_enabled[$i])) : 0;
					$priority = THWCFD_Utils::prepare_field_priority($fields, $order, false);
					
					$field = $fields[$name];
					$field['priority'] = $priority;
					$field['enabled'] = $enabled;
					
					$fields[$name] = $field;
				}
			}
			$fields = THWCFD_Utils::sort_fields($fields);
			$result = THWCFD_Utils::update_fields($section, $fields);

			if($result == true) {
				echo '<div class="updated"><p>'. __('Your changes were saved.', 'woo-checkout-field-editor-pro') .'</p></div>';
			}else {
				echo '<div class="error"><p>'. __('Your changes were not saved due to an error (or you made none!).', 'woo-checkout-field-editor-pro') .'</p></div>';
			}
		} catch (Exception $e) {
			echo '<div class="error"><p>'. __('Your changes were not saved due to an error.', 'woo-checkout-field-editor-pro') .'</p></div>';
		}
	}

	private function prepare_field_from_posted_data($posted){
		$field_props = $this->get_field_form_props();
		$field = array();
		
		foreach ($field_props as $pname => $prop) {
			$iname  = 'i_'.$pname;
			
			$pvalue = '';
			if($prop['type'] === 'checkbox'){
				$pvalue = isset($posted[$iname]) && $posted[$iname] ? 1 : 0;
			}else if(isset($posted[$iname])){
				//$pvalue = is_array($posted[$iname]) ? implode(',', $posted[$iname]) : trim(stripslashes($posted[$iname]));
				$pvalue = is_array($posted[$iname]) ? $posted[$iname] : trim(stripslashes($posted[$iname]));
			}

			if($pname === 'class'){
				//$pvalue = is_string($pvalue) ? array_map('trim', explode(',', $pvalue)) : $pvalue;
				$pvalue = is_string($pvalue) ? preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $pvalue) : $pvalue;
				$pvalue = is_array($pvalue) ? $pvalue : array();
			}

			$field[$pname] = $pvalue;
		}

		$type = isset($field['type']) ? $field['type'] : '';
		if(!$type){
			$type = isset($posted['i_otype']) ? trim(stripslashes($posted['i_otype'])) : '';
			$field['type'] = $type;
		}

		$name = isset($field['name']) ? $field['name'] : '';
		if(!$name){
			$field['name'] = isset($posted['i_oname']) ? trim(stripslashes($posted['i_oname'])) : '';
		}

		if($type === 'select'){
			$field['validate'] = '';

		}else if($type === 'radio'){
			$field['validate'] = '';
			$field['placeholder'] = '';

		}

		if($type === 'select' || $type === 'radio'){
			$options_json = isset($posted['i_options_json']) ? trim(stripslashes($posted['i_options_json'])) : '';
			$options_arr = THWCFD_Utils::prepare_options_array($options_json);
			
			//$field['options_json'] = $options_json;
			$field['options'] = $options_arr;
		}else{
			$field['options'] = '';
		}

		$field['autocomplete'] = isset($posted['i_autocomplete']) ? $posted['i_autocomplete'] : '';
		$field['priority'] = isset($posted['i_priority']) ? $posted['i_priority'] : '';
		//$field['custom'] = isset($posted['i_custom']) ? $posted['i_custom'] : '';
		$field['custom'] = isset($posted['i_custom']) && $posted['i_custom'] ? 1 : 0;

		return $field;
	}

	/******* ADD,EDIT Forms *******/
	/*******************************/
	public function get_field_types(){
		return array(
			'text'   => __('Text', 'woo-checkout-field-editor-pro'),
			'password' => __('Password', 'woo-checkout-field-editor-pro'),
			'email' => __('Email', 'woo-checkout-field-editor-pro'),
			'tel' => __('Phone', 'woo-checkout-field-editor-pro'),
			'select' => __('Select', 'woo-checkout-field-editor-pro'),
			'textarea' => __('Textarea', 'woo-checkout-field-editor-pro'),
			'radio' => __('Radio', 'woo-checkout-field-editor-pro'),
		);
	}

	public function get_field_form_props(){
		$field_types = $this->get_field_types();
		
		$validations = array(
			'email' => 'Email',
			'phone' => 'Phone',
			'postcode' => 'Postcode',
			'state' => 'State',
			'number' => 'Number',
		);

		$display_style = array(
			'full' => 'Full width',
			'half_left' => 'Half width left',
			'ha;lf_right' => 'Half width right',
		);
				
		return array(
			'type' 		  => array('type'=>'select', 'name'=>'type', 'label'=>'Type', 'required'=>1, 'options'=>$field_types, 
								'onchange'=>'thwcfdFieldTypeChangeListner(this)'),
			'name' 		  => array('type'=>'text', 'name'=>'name', 'label'=>'Name', 'required'=>1),
			'label'       => array('type'=>'text', 'name'=>'label', 'label'=>'Label'),
			'default'     => array('type'=>'text', 'name'=>'default', 'label'=>'Default Value'),
			'placeholder' => array('type'=>'text', 'name'=>'placeholder', 'label'=>'Placeholder'),
			//'options'     => array('type'=>'text', 'name'=>'options', 'label'=>'Options', 'placeholder'=>'Seperate options with pipe(|)'),
			'class'       => array('type'=>'text', 'name'=>'class', 'label'=>'Class', 'placeholder'=>'Seperate classes with comma'),
			'validate'    => array('type'=>'select', 'name'=>'validate', 'label'=>'Validation', 'options'=>$validations, 'multiple'=>1),
			'disp_style' => array('type'=>'select', 'name'=>'disp_style', 'label'=>'Field Display', 'options'=>$display_style),
						
			'required' => array('type'=>'checkbox', 'name'=>'required', 'label'=>'Required', 'value'=>'1', 'checked'=>1),
			//'clear'    => array('type'=>'checkbox', 'name'=>'clear', 'label'=>'Clear Row', 'value'=>'1', 'checked'=>1),
			'enabled'  => array('type'=>'checkbox', 'name'=>'enabled', 'label'=>'Enabled', 'value'=>'1', 'checked'=>1),
			
			'show_in_email' => array('type'=>'checkbox', 'name'=>'show_in_email', 'label'=>'Display in Emails', 'value'=>'1', 'checked'=>1),
			'show_in_order' => array('type'=>'checkbox', 'name'=>'show_in_order', 'label'=>'Display in Order Detail Pages', 'value'=>'1', 'checked'=>1),
		);
	}

	private function output_add_field_form_pp(){
		?>
        <div id="thwcfd_new_field_form_pp" title="New Checkout Field" class="thpladmin-popup-wrapper">
          <?php $this->output_popup_form_fields('new'); ?>
        </div>
        <?php
	}
		
	private function output_edit_field_form_pp(){		
		?>
        <div id="thwcfd_edit_field_form_pp" title="Edit Checkout Field" class="thpladmin-popup-wrapper">
          <?php $this->output_popup_form_fields('edit'); ?>
        </div>
        <?php
	}

	private function output_popup_form_fields($form_type){
		$field_props = $this->get_field_form_props();

		?>
		<form method="post" id="thwcfd_<?php echo $form_type ?>_field_form" action="">
          	<input type="hidden" name="f_action" value="<?php echo $form_type ?>" />
          	<input type="hidden" name="i_autocomplete" value="" />
          	<input type="hidden" name="i_priority" value="" />
          	<input type="hidden" name="i_custom" value="" />
          	<input type="hidden" name="i_oname" value="" />
          	<input type="hidden" name="i_otype" value="" />
          	<input type="hidden" name="i_options_json" value="" />

          	<table width="100%">
            	<tr>                
                	<td colspan="2" class="err_msgs"></td>
				</tr>
            	<?php 
            	$this->render_form_field_element($field_props['type']);
            	$this->render_form_field_element($field_props['name']);
            	$this->render_form_field_element($field_props['label']);
            	$this->render_form_field_element($field_props['placeholder']);
            	$this->render_form_field_element($field_props['default']);
            	$this->render_form_field_element($field_props['class']);
            	//$this->render_form_field_element($field_props['disp_style']);
            	$this->render_form_field_element($field_props['validate']);
            	//$this->render_form_field_element($field_props['options']);
            	$this->render_form_element_h_spacing();
            	$this->render_field_form_fragment_options();
            	$this->render_form_element_h_spacing();

            	?>
            	<tr class="row-required">
                	<td>&nbsp;</td>                     
                    <td>
                    	<?php 
		            	$this->render_form_field_element($field_props['required']);
		            	//$this->render_form_field_element($field_props['clear']);
		            	$this->render_form_field_element($field_props['enabled']);
		            	$this->render_form_field_element($field_props['show_in_email']);
		            	$this->render_form_field_element($field_props['show_in_order']);
		            	?>
                    </td>
                </tr>                       
            </table>
        </form>
        <?php
	}

	public function render_form_field_element($props){
		if(is_array($props)){
			$type = isset($props['type']) ? $props['type'] : 'text';

			if($type === 'select'){
				$this->render_form_field_element_select($props);
			}else if($type === 'radio'){
				$this->render_form_field_element_radio($props);
			}else if($type === 'checkbox'){
				$this->render_form_field_element_checkbox($props);
			}else{
				$this->render_form_field_element_inputtext($props);
			}
		}
	}

	private function render_form_field_element_inputtext($props){
		$name  = isset($props['name']) ? $props['name'] : '';
		$fname = 'i_'.$name;
		$label = isset($props['label']) ? __($props['label'], 'woo-checkout-field-editor-pro') : '';

		$field_attr = 'name="'.$fname.'" value=""';
		if(isset($props['placeholder']) && $props['placeholder']){
			$field_attr .= ' placeholder="'.__($props['placeholder'], 'woo-checkout-field-editor-pro').'"';
		}
		$field_attr .= ' style="width:250px;"';
		
		?>
		<tr class="<?php echo 'row-'.$name; ?>">                
        	<td width="30%"><?php echo $label; ?></td>
            <td><input type="text" <?php echo $field_attr; ?> /></td>
		</tr>
		<?php
	}

	private function render_form_field_element_select($props){
		$name  = isset($props['name']) ? $props['name'] : '';
		$fname = isset($props['multiple']) && $props['multiple'] ? 'i_'.$name.'[]' : 'i_'.$name;
		$label = isset($props['label']) ? __($props['label'], 'woo-checkout-field-editor-pro') : '';
		$options = isset($props['options']) ? $props['options'] : array();
		$options = is_array($options) ? $options : $array();

		$field_attr = 'name="'.$fname.'"';
		if(isset($props['onchange']) && $props['onchange']){
			$field_attr .= ' onchange="'.$props['onchange'].'"';
		}

		if(isset($props['placeholder']) && $props['placeholder']){
			$field_attr .= ' data-placeholder="'.__($props['placeholder'], 'woo-checkout-field-editor-pro').'"';
		}

		if(isset($props['multiple']) && $props['multiple']){
			$field_attr .= ' multiple="multiple"';
			$field_attr .= ' class="thwcfd-enhanced-multi-select"';
			$field_attr .= ' style="width:250px; height:30px;"';
		}else{
			$field_attr .= ' style="width:250px;"';
		}

		?>
		<tr class="<?php echo 'row-'.$name; ?>">                
        	<td width="30%"><?php echo $label; ?></td>
            <td>
            	<select <?php echo $field_attr; ?> >
                <?php foreach($options as $key => $value){ ?>
                	<option value="<?php echo trim($key); ?>"><?php echo $value; ?></option>
                <?php } ?>
                </select>
            </td>
		</tr>
		<?php
	}

	private function render_form_field_element_radio($props){
		$name  = isset($props['name']) ? $props['name'] : '';
		$label = isset($props['label']) ? __($props['label'], 'woo-checkout-field-editor-pro') : '';
		$options = isset($props['options']) ? $props['options'] : array();
		$options = is_array($options) ? $options : $array();

		?>
		<tr class="<?php echo 'row-'.$name; ?>">                
        	<td width="30%"><?php echo $label; ?></td>
            <td>

				<?php foreach($options as $key => $value){ ?>
                	<input type="radio" name="<?php echo $name; ?>" value="<?php echo trim($key); ?>"> <?php echo $value; ?>
                <?php } ?>
            </td>
		</tr>
		<?php
	}

	private function render_form_field_element_checkbox($props){
		$name  = isset($props['name']) ? $props['name'] : '';
		$fname = 'i_'.$name;
		$label = isset($props['label']) ? __($props['label'], 'woo-checkout-field-editor-pro') : '';

		$field_attr = 'id="'.$fname.'" name="'.$fname.'" value="1"';
		if(isset($props['checked']) && $props['checked']){
			$field_attr .= ' checked="checked"';
		}

		?>
		<input type="checkbox" <?php echo $field_attr; ?> />
        <label for="<?php echo $fname; ?>"><?php echo $label; ?></label><br/>
		<?php
	}

	private function render_field_form_fragment_options(){
		?>
		<tr class="row-options">
			<td width="30%" valign="top"><?php _e('Options', 'woo-checkout-field-editor-pro'); ?></td>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="thwcfd-option-list thpladmin-dynamic-row-table"><tbody>
					<tr>
						<td style="width:150px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:140px;"/></td>
						<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="thwcfdAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>
						<td class="action-cell"><a href="javascript:void(0)" onclick="thwcfdRemoveOptionRow(this)" class="btn btn-red" title="Remove option">x</a></td>
						<td class="action-cell sort ui-sortable-handle"></td>
					</tr>
				</tbody></table>            	
			</td>
		</tr>
        <?php
	}

	public function render_form_element_h_spacing($padding = 5, $colspan = 2){
		?>
        <tr><td colspan="<?php echo $colspan; ?>" style="padding-top:<?php echo $padding ?>px;"></td></tr>
        <?php
	}

	/******* Display & Update Field Values *******/
	/*********************************************/
	public function order_data_after_order_details($order){
		$fields = THWCFD_Utils::get_fields('additional');
		$this->display_fields_in_admin_order($order, $fields, '<p>&nbsp;</p>');
	}

	public function order_data_after_billing_address($order){
		$fields = THWCFD_Utils::get_fields('billing');
		$this->display_fields_in_admin_order($order, $fields, '');
	}

	public function order_data_after_shipping_address($order){
		$fields = THWCFD_Utils::get_fields('shipping');
		$this->display_fields_in_admin_order($order, $fields, '');
	}

	public function display_fields_in_admin_order($order, $fields, $prefix_html = ''){
		if(is_array($fields)){
			$html = '';
			$order_id = THWCFD_Utils::get_order_id($order);
		
			foreach($fields as $name => $field){
				if(THWCFD_Utils::is_active_custom_field($field) && isset($field['show_in_order']) && $field['show_in_order']){
					$value = get_post_meta( $order_id, $name, true );
					if(!empty($value)){
						$value = THWCFD_Utils::get_option_text($field, $value);
						$label = isset($field['label']) && $field['label'] ? THWCFD_Utils::t($field['label']) : $name;
						$html .= '<p><strong>'. $label .':</strong><br/> '. wptexturize($value) .'</p>';
					}
				}
			}

			if($html){
				echo $prefix_html.$html;	
			}
		}
	}

	/******* TABS & SECTIONS *******/
	/*******************************/
	public function get_current_tab(){
		return isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
	}
	
	public function get_current_section(){
		$tab = $this->get_current_tab();
		$section = '';
		if($tab === 'fields'){
			$section = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'billing';
		}
		return $section;
	}

	public function output_tabs(){
		$current_tab = $this->get_current_tab();

		if(empty($this->tabs)){
			return;
		}
		
		echo '<h2 class="thpladmin-tabs nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $this->tabs as $id => $label ){
			$active = ( $current_tab == $id ) ? 'nav-tab-active' : '';
			$label  = __($label, 'woo-checkout-field-editor-pro');
			echo '<a class="nav-tab '.$active.'" href="'. $this->get_admin_url($id) .'">'.$label.'</a>';
		}
		echo '</h2>';	
	}
	
	public function output_sections() {
		$current_tab = $this->get_current_tab();
		$current_section = $this->get_current_section();

		if(empty($this->sections)){
			return;
		}
		
		$array_keys = array_keys( $this->sections );
		
		echo '<ul class="thpladmin-sections">';
		foreach( $this->sections as $id => $label ){
			$label = __($label, 'woo-checkout-field-editor-pro');
			$url = $this->get_admin_url($current_tab, sanitize_title($id));	
			echo '<li><a href="'. $url .'" class="'. ( $current_section == $id ? 'current' : '' ) .'">'. $label .'</a> '. (end( $array_keys ) == $id ? '' : '|') .' </li>';
		}		
		echo '</ul>';
	}	
	
	public function get_admin_url($tab = false, $section = false){
		$url = 'admin.php?page=checkout_form_designer';
		if($tab && !empty($tab)){
			$url .= '&tab='. $tab;
		}
		if($section && !empty($section)){
			$url .= '&section='. $section;
		}
		return admin_url($url);
	}
}

endif;
