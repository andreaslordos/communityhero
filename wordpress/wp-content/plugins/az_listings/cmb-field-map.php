<?php

/**
 * Class PW_CMB2_Field_Google_Maps
 */
class PW_CMB2_Field_Google_Maps {

    /**
     * Current version number
     */
    const VERSION = '2.1.1';

    /**
     * Initialize the plugin by hooking into CMB2
     */
    public function __construct() {
        add_filter('cmb2_render_pw_map', array($this, 'render_pw_map'), 10, 5);
        add_filter('cmb2_sanitize_pw_map', array($this, 'sanitize_pw_map'), 10, 4);
    }

    /**
     * Render field
     */
    public function render_pw_map($field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object) {
        $this->setup_admin_scripts();

        echo '<input type="text" class="large-text pw-map-search" id="' . $field->args('id') . '" />';

        echo '<div class="pw-map"></div>';

        $field_type_object->_desc(true, true);

        echo $field_type_object->input(array(
            'type' => 'hidden',
            'name' => $field->args('_name') . '[latitude]',
            'value' => isset($field_escaped_value['latitude']) ? $field_escaped_value['latitude'] : '',
            'class' => 'pw-map-latitude',
            'desc' => '',
        ));
        echo $field_type_object->input(array(
            'type' => 'hidden',
            'name' => $field->args('_name') . '[longitude]',
            'value' => isset($field_escaped_value['longitude']) ? $field_escaped_value['longitude'] : '',
            'class' => 'pw-map-longitude',
            'desc' => '',
        ));
    }

    public function sanitize_pw_map($override_value, $value, $object_id, $field_args) {
        if (isset($value['latitude'])) {
            $value['latitude'] = sanitize_text_field($value['latitude']);
        }

        if (isset($value['longitude'])) {
            $value['longitude'] = sanitize_text_field($value['longitude']);
        }
        return $value;
    }

    /**
     * Enqueue scripts and styles
     */
    public function setup_admin_scripts() {
        azl_google_maps_js();
        wp_enqueue_script('pw-map', plugins_url('js/pw-map.js', __FILE__), array('google-maps'), self::VERSION);
        wp_enqueue_style('pw-map', plugins_url('css/pw-map.css', __FILE__), array(), self::VERSION);
    }

}

$pw_cmb2_field_google_maps = new PW_CMB2_Field_Google_Maps();


add_filter('cmb2_override_meta_save', 'cmb2_override_meta_save_pw_map_callback', 10, 4);

function cmb2_override_meta_save_pw_map_callback($null, $a, $field_args, $field) {
    if ($field_args['type'] == 'pw_map' && $a['id']) {
        if (isset($a['value']) && isset($a['value']['latitude']) && $a['value']['longitude']) {
            if (isset($field_args['meta_key'])) {
                update_post_meta($a['id'], $field_args['meta_key'], $a['value']['latitude'] . ',' . $a['value']['longitude']);
            }
            if (isset($field_args['lat_meta_key'])) {
                update_post_meta($a['id'], $field_args['lat_meta_key'], $a['value']['latitude']);
            }
            if (isset($field_args['lng_meta_key'])) {
                update_post_meta($a['id'], $field_args['lng_meta_key'], $a['value']['longitude']);
            }
        }
    }
}
