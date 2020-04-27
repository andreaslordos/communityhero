<?php 
/** 
 *
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */

class PMWI_Admin_Import extends PMWI_Controller_Admin 
{				
	public function index($post) {			
				
		$this->data['post'] =& $post;

		switch ($post['custom_type']) 
		{
			case 'product':
				$this->render('admin/import/product/index');
				break;

			case 'shop_order':
				$this->render('admin/import/shop_order/index');			
				break;

			default:
				# code...
				break;
		}			
	}			

	public function options( $isWizard = false, $post = array() )
	{
		$this->data['isWizard'] = $isWizard;	

		$this->data['post'] =& $post;				

		$this->data['existing_meta_keys'] = array();	

		if ( ! in_array($post['custom_type'], array('import_users'))){

			global $wpdb;

			// Get all meta keys for requested post type			
			$hide_fields = array('_wp_page_template', '_edit_lock', '_edit_last', '_wp_trash_meta_status', '_wp_trash_meta_time');
			$records = get_posts( array('post_type' => $post['custom_type']) );
			if ( ! empty($records)){			
				foreach ($records as $record) {
					$record_meta = get_post_meta($record->ID, '');
					if ( ! empty($record_meta)){
						foreach ($record_meta as $record_meta_key => $record_meta_value) {
							if ( ! in_array($record_meta_key, $this->data['existing_meta_keys']) and ! in_array($record_meta_key, $hide_fields)) $this->data['existing_meta_keys'][] = $record_meta_key;
						}
					}
				}
			}
		}

		$this->render();
	}

    public function confirm( $isWizard = false, $post = array() )
    {
        $this->data['isWizard'] = $isWizard;

        $this->data['post'] =& $post;

        $this->data['existing_meta_keys'] = array();

        $this->render();
    }
}
