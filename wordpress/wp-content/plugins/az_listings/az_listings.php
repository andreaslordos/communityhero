<?php

/**
 * Plugin Name:  AZEXO Listings
 * Plugin URI:   http://www.azexo.com
 * Description:  Frontend submission
 * Author:       AZEXO
 * Author URI:   http://www.azexo.com
 * Version: 1.27
 * Text Domain:  azl
 * Domain Path:  languages
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('AZL_URL', plugins_url('', __FILE__));
define('AZL_DIR', trailingslashit(dirname(__FILE__)));
define('AZL_LISTING_LIMIT', 'Listing limit');
define('AZL_FRONTEND_OBJECT_ID', 'azl-frontend-object-id');


if (file_exists(AZL_DIR . 'azexo.php')) {
    include_once(AZL_DIR . 'azexo.php' );
}

if (file_exists(AZL_DIR . 'subscriptions.php')) {
    include_once(AZL_DIR . 'subscriptions.php' );
}

if (file_exists(AZL_DIR . 'profiles.php')) {
    include_once(AZL_DIR . 'profiles.php' );
}

if (file_exists(AZL_DIR . 'options.php') && is_admin()) {
    include_once(AZL_DIR . 'cmb2-post-search-field.php' );
    include_once(AZL_DIR . 'options.php' );
}
if (is_admin()) {
    include_once(AZL_DIR . 'admin.php');
}

include_once(AZL_DIR . 'cmb2-conditionals.php' );
include_once(AZL_DIR . 'cmb-field-hierarchical-taxonomy-select.php' );
include_once(AZL_DIR . 'cmb-field-wrapper.php' );
include_once(AZL_DIR . 'cmb-field-select2.php' );
include_once(AZL_DIR . 'cmb-field-map.php');
include_once(AZL_DIR . 'cmb-field-working-hours.php' );
include_once(AZL_DIR . 'cmb-field-availability-calendar.php' );
include_once(AZL_DIR . 'cmb-field-prices-calendar.php' );
include_once(AZL_DIR . 'cmb-field-wc-variations.php' );


add_action('plugins_loaded', 'azl_plugins_loaded');

function azl_plugins_loaded() {
    load_plugin_textdomain('azl', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('init', 'azl_init');

function azl_init() {
    if (!current_user_can('manage_options') && !is_admin()) {
        show_admin_bar(false);
    }
}

add_action('pre_get_posts', 'azl_pre_get_posts');

function azl_pre_get_posts($wp_query_obj) {
    global $pagenow;
    if (!is_a(wp_get_current_user(), 'WP_User')) {
        return;
    }
    if ('upload.php' != $pagenow && ('admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments')) {
        return;
    }
    if (!current_user_can('manage_options')) {
        $wp_query_obj->set('author', wp_get_current_user()->ID);
    }
    return;
}

add_action('wp_enqueue_scripts', 'azl_enqueue_scripts', 9);

function azl_enqueue_scripts() {
    wp_register_script('infobox', plugins_url('js/infobox.js', __FILE__), array(), false, true);
    wp_register_script('markerclusterer', plugins_url('js/markerclusterer.js', __FILE__), array(), false, true);
    wp_register_script('richmarker', plugins_url('js/richmarker.js', __FILE__), array(), false, true);
    wp_register_script('mustache', plugins_url('js/mustache.js', __FILE__), array(), false, true);
    wp_register_script('azl', plugins_url('js/azl.js', __FILE__), array('mustache'), false, true);
    wp_register_style('azl', plugins_url('css/azl.css', __FILE__));

    $translation_array = array(
        'previous' => __('Previous', 'azl'),
        'next' => __('Next', 'azl'),
        'delete' => __('Delete ?', 'azl'),
    );
    wp_localize_script('azl', 'azl_translate', $translation_array);
    wp_enqueue_style('azl');
    add_filter('cmb2_enqueue_css', '__return_false');
}

add_action('wp_loaded', 'azl_wp_loaded');

function azl_wp_loaded() {
    //WMPL
    if (class_exists('SitePress')) {
        if (is_admin() && function_exists('cmb2_get_option')) {
            $forms = cmb2_get_option('azl_options', 'forms');
            if (is_array($forms)) {
                foreach ($forms as $form) {
                    if (isset($form['form'])) {
                        $fiels = json_decode($form['form'], true);
                        foreach ($fiels as $field) {
                            if (isset($field['name'])) {
                                do_action('wpml_register_single_string', 'AZEXO Listings', $form['name'] . '-' . $field['id'] . ' name', $field['name']);
                            }
                            if (isset($field['desc'])) {
                                do_action('wpml_register_single_string', 'AZEXO Listings', $form['name'] . '-' . $field['id'] . ' desc', $field['desc']);
                            }
                            if (isset($field['attributes']) && is_array($field['attributes'])) {
                                if (isset($field['attributes']['data-error'])) {
                                    do_action('wpml_register_single_string', 'AZEXO Listings', $form['name'] . '-' . $field['id'] . ' error', $field['attributes']['data-error']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function azl_tgmpa_register() {

    $plugins = array(
        array(
            'name' => 'CMB2',
            'slug' => 'cmb2',
            'required' => true,
        ),
    );
    tgmpa($plugins, array());
}

add_action('tgmpa_register', 'azl_tgmpa_register');

function azl_floatval_sanitization($meta_value, $field_args, $field) {
    return floatval($meta_value);
}

function azl_get_default($args, $field) {
    if (is_numeric($field->object_id)) {
        $post = get_post($field->object_id);
        switch ($args['id']) {
            case 'post_title':
                return $post->post_title;
                break;
            case 'post_content':
                return $post->post_content;
                break;
            case 'post_excerpt':
                return $post->post_excerpt;
                break;
        }
        if (isset($args['meta_key'])) {
            switch ($args['type']) {
                case 'file':
                    return wp_get_attachment_url(get_post_meta($field->object_id, $args['meta_key'], true));
                    break;
                case 'file_list':
                    $ids = explode(',', get_post_meta($field->object_id, $args['meta_key'], true));
                    $meta_value = array();
                    foreach ($ids as $id) {
                        if (is_numeric($id)) {
                            $meta_value[$id] = wp_get_attachment_url($id);
                        }
                    }
                    return $meta_value;
                    break;
            }
        }
    } else {
        if (isset($_POST[$args['id']])) {
            return $field->sanitization_cb($_POST[$args['id']]);
        }
    }
    return '';
}

function azl_add_custom_field($cmb, $params) {
    if (!isset($params['default_cb'])) {
        $params['default_cb'] = 'azl_get_default';
    }

    if (isset($params['required']) && $params['required']) {
        $attributes = array();
        $attributes['required'] = 'required';
        if (isset($params['attributes']) && is_array($params['attributes'])) {
            $params['attributes'] = array_merge($attributes, $params['attributes']);
        } else {
            $params['attributes'] = $attributes;
        }
    }
    if ($params['type'] == 'azl_file') {
        if (is_user_logged_in()) {
            $params['type'] = 'file';
        } else {
            $params['type'] = 'text';
            $attributes = array(
                'type' => 'file', // Let's use a standard file upload field
            );
            if (isset($params['attributes']) && is_array($params['attributes'])) {
                $params['attributes'] = array_merge($attributes, $params['attributes']);
            } else {
                $params['attributes'] = $attributes;
            }
        }
    }
    if ($params['type'] == 'azl_files') {
        if (is_user_logged_in()) {
            $params['type'] = 'file_list';
        } else {
            $params['type'] = 'text';
            $attributes = array(
                'type' => 'file', // Let's use a standard file upload field
                'multiple' => 'multiple',
            );
            if (isset($params['attributes']) && is_array($params['attributes'])) {
                $params['attributes'] = array_merge($attributes, $params['attributes']);
            } else {
                $params['attributes'] = $attributes;
            }
        }
    }
    if ($params['type'] == 'azl_image') {
        if (is_user_logged_in()) {
            $params['type'] = 'file';
        } else {
            $params['type'] = 'text';
            $attributes = array(
                'type' => 'file', // Let's use a standard file upload field
            );
            if (isset($params['attributes']) && is_array($params['attributes'])) {
                $params['attributes'] = array_merge($attributes, $params['attributes']);
            } else {
                $params['attributes'] = $attributes;
            }
        }
    }
    if ($params['type'] == 'azl_images') {
        if (is_user_logged_in()) {
            $params['type'] = 'file_list';
        } else {
            $params['type'] = 'text';
            $attributes = array(
                'type' => 'file', // Let's use a standard file upload field
                'multiple' => 'multiple',
            );
            if (isset($params['attributes']) && is_array($params['attributes'])) {
                $params['attributes'] = array_merge($attributes, $params['attributes']);
            } else {
                $params['attributes'] = $attributes;
            }
        }
    }
    if ($params['type'] == 'text_money') {
        //$params['sanitization_cb'] = 'azl_floatval_sanitization';
    }
    if (isset($params['taxonomy'])) {
        $cache_key = 'cmb-cache-' . $params['taxonomy'] . '-' . AZL_FRONTEND_OBJECT_ID;
        add_filter('transient_' . $cache_key, '__return_false');
        add_filter('wp_get_object_terms', 'azl_cmb2_taxonomy_fix', 10, 4);
    }

    //WMPL
    if (class_exists('SitePress')) {
        if (isset($params['name'])) {
            $params['name'] = apply_filters('wpml_translate_single_string', $params['name'], 'AZEXO Listings', $cmb->__get('cmb_id') . '-' . $params['id'] . ' name');
        }
        if (isset($params['desc'])) {
            $params['desc'] = apply_filters('wpml_translate_single_string', $params['desc'], 'AZEXO Listings', $cmb->__get('cmb_id') . '-' . $params['id'] . ' desc');
        }

        if (isset($params['attributes']) && is_array($params['attributes'])) {
            if (isset($params['attributes']['data-error'])) {
                $params['attributes']['data-error'] = apply_filters('wpml_translate_single_string', $params['attributes']['data-error'], 'AZEXO Listings', $cmb->__get('cmb_id') . '-' . $params['id'] . ' error');
            }
        }
    }

    $cmb->add_field($params);
}

function azl_cmb2_taxonomy_fix($terms, $object_ids, $taxonomies, $args) {
    if (in_array(AZL_FRONTEND_OBJECT_ID, (array) $object_ids)) {
        return array();
    }
    return $terms;
}

add_filter('cmb2_sanitize_taxonomy_select', 'azl_cmb2_sanitize_taxonomy_fix', 10, 5);
add_filter('cmb2_sanitize_taxonomy_radio', 'azl_cmb2_sanitize_taxonomy_fix', 10, 5);
add_filter('cmb2_sanitize_taxonomy_radio_inline', 'azl_cmb2_sanitize_taxonomy_fix', 10, 5);
add_filter('cmb2_sanitize_taxonomy_multicheck', 'azl_cmb2_sanitize_taxonomy_fix', 10, 5);
add_filter('cmb2_sanitize_taxonomy_multicheck_inline', 'azl_cmb2_sanitize_taxonomy_fix', 10, 5);

function azl_cmb2_sanitize_taxonomy_fix($override_value, $value, $object_id, $field_args, $sanitize_object) {
    if ($object_id == AZL_FRONTEND_OBJECT_ID) {
        return $value;
    }
    return $override_value;
}

add_filter('cmb2_sanitize_text_url', 'azl_cmb2_sanitize_text_url_fix', 10, 5);

function azl_cmb2_sanitize_text_url_fix($override_value, $value, $object_id, $field_args, $sanitize_object) {
    $protocols = $sanitize_object->field->args('protocols');
    $default = '';
    if ($field_args['default_cb'] != 'azl_get_default') {
        //recoursion: get_default -> sanitize_text_url -> get_default
        //$default = $sanitize_object->field->args('default_cb');
    }
    // for repeatable
    if (is_array($sanitize_object->value)) {
        foreach ($sanitize_object->value as $key => $val) {
            $sanitize_object->value[$key] = $val ? esc_url_raw($val, $protocols) : $default;
        }
    } else {
        $sanitize_object->value = $sanitize_object->value ? esc_url_raw($sanitize_object->value, $protocols) : $default;
    }
    return $sanitize_object->value;
}

add_action('cmb2_render_text_number', 'sm_cmb_render_text_number', 10, 5);

function sm_cmb_render_text_number($field, $escaped_value, $object_id, $object_type, $field_type_object) {
    echo $field_type_object->input(array('class' => 'cmb2-text-small', 'type' => 'number'));
}

add_filter('cmb2_sanitize_text_number', 'sm_cmb2_sanitize_text_number', 10, 2);

function sm_cmb2_sanitize_text_number($null, $value) {
    return floatval($value);
}

function azl_get_form_fields($name) {
    $forms = cmb2_get_option('azl_options', 'forms');
    if (is_array($forms)) {
        foreach ($forms as $form) {
            if ($form['name'] == $name) {
                if (isset($form['form'])) {
                    return json_decode($form['form'], true);
                }
            }
        }
    }
}

function azl_get_form_settings($name) {
    $forms = cmb2_get_option('azl_options', 'forms');
    if (is_array($forms)) {
        foreach ($forms as &$form) {
            if ($form['name'] == $name) {
                return $form;
            }
        }
    }
    return null;
}

add_filter('user_has_cap', 'azd_user_has_cap', 10, 4);

function azd_user_has_cap($allcaps, $caps, $args, $user) {
    if (count($args) == 3) {
        if ($args[0] == 'edit_post') {
            if ($args[1] == get_current_user_id()) {
                if (is_user_logged_in()) {
                    $submit_post = get_post($args[2]);
                    if (strpos($submit_post->post_content, '[azl-frontend-submission') !== false) {
                        $allcaps['edit_others_pages'] = 1;
                        $allcaps['edit_published_pages'] = 1;
                    }
                }
            }
        }
    }
    return $allcaps;
}

add_shortcode('azl-frontend-submission', 'azl_frontend_submission_shortcode');

function azl_frontend_submission_shortcode($atts = array()) {
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('azl');
    wp_enqueue_style('azl');

    // Current user
    $user_id = get_current_user_id();

    // Parse attributes
    $atts = shortcode_atts(array(
        'name' => '',
        'product_type' => 'simple',
        'post_author' => $user_id ? $user_id : 1, // Current user, or admin
        'post_status' => 'pending',
        'post_type' => 'product', // Only use first object_type in array
            ), $atts, 'azl-frontend-submission');

    if (!function_exists('new_cmb2_box')) {
        return '<p class="cmb-error">' . __('Please install <strong>CMB2</strong> plugin.', 'azl') . '</p>';
    }

    // since post ID will not exist yet, just need to pass it something    
    $object_id = AZL_FRONTEND_OBJECT_ID;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $object_id = $_GET['id'];
        $post = get_post($object_id);
        $atts['post_type'] = $post->post_type;
        if (!current_user_can('manage_options')) {
            if (!is_user_logged_in() || $post->post_author != $user_id) {
                return '<p class="cmb-error">' . __('You do not have permission to edit this post.', 'azl') . '</p>';
            }
        }
    }


    $metabox_id = 'azl-frontend-submit-form';
    $forms = cmb2_get_option('azl_options', 'forms');
    if (is_array($forms)) {
        foreach ($forms as &$form) {
            if ($form['name'] == $atts['name']) {
                $metabox_id = $form['name'];
                if (!isset($form['page']) || empty($form['page'])) {
                    if (is_page()) {
                        global $post;
                        $form['page'] = $post->ID;
                    }
                }
                if (!isset($form['post_type']) || empty($form['post_type'])) {
                    $form['post_type'] = $atts['post_type'];
                }
                cmb2_update_option('azl_options', 'forms', $forms);
            }
        }
    }
    $form_settings = azl_get_form_settings($atts['name']);



    $cmb = new_cmb2_box(array(
        'id' => $metabox_id,
        'object_types' => array($atts['post_type']),
        'hookup' => false,
        'save_fields' => false,
    ));

    if (isset($form_settings['general_title']) && !empty($form_settings['general_title'])) {
        $cmb->add_field(array(
            'name' => isset($form_settings['general_title']) ? $form_settings['general_title'] : '',
            'desc' => isset($form_settings['general_desc']) ? $form_settings['general_desc'] : '',
            'type' => 'title',
            'id' => 'general',
            'attributes' => array(
                'class' => isset($form_settings['general_class']) ? $form_settings['general_class'] : ''
            ),
        ));
    }

    if (!is_user_logged_in()) {
        $cmb->add_field(array(
            'name' => __('Your Email', 'azl'),
            'desc' => __('Please enter your email so we can contact you.', 'azl'),
            'id' => 'author_email',
            'type' => 'text_email',
            'default_cb' => 'azl_get_default',
            'required' => true,
            'attributes' => array(
                'required' => 'required',
                'data-validation' => 'required|email',
                'data-error' => __('Please input your email', 'azl'),
            ),
        ));
    }

    if (isset($form_settings['general_fields']) && is_array($form_settings['general_fields'])) {

        if (isset($form_settings['general_wrapper_class']) && !empty($form_settings['general_wrapper_class'])) {
            $cmb->add_field(array(
                'class' => $form_settings['general_wrapper_class'],
                'type' => 'wrapper',
                'id' => 'general_fields'
            ));
        }


        if (in_array('title', $form_settings['general_fields'])) {
            $cmb->add_field(array(
                'name' => __('Title', 'azl'),
                'id' => 'post_title',
                'type' => 'text',
                'default_cb' => 'azl_get_default',
                'required' => true,
                'attributes' => array(
                    'required' => 'required',
                    'data-validation' => 'required',
                    'data-error' => __('Please input title', 'azl'),
                ),
            ));
        }

        if (in_array('description', $form_settings['general_fields'])) {
            $cmb->add_field(array(
                'name' => __('Description', 'azl'),
                'id' => 'post_content',
                'type' => 'wysiwyg',
                'default_cb' => 'azl_get_default',
                'options' => array(
                    'textarea_rows' => 8,
                    'media_buttons' => true,
                ),
                'required' => true,
                'attributes' => array(
                    'required' => 'required',
                    'data-validation' => 'required',
                    'data-error' => __('Please input description', 'azl'),
                ),
            ));
        }

        if (in_array('short_description', $form_settings['general_fields'])) {
            $cmb->add_field(array(
                'name' => __('Short description', 'azl'),
                'id' => 'post_excerpt',
                'type' => 'textarea_small',
                'default_cb' => 'azl_get_default',
                'options' => array(
                    'textarea_rows' => 8,
                ),
                'required' => true,
                'attributes' => array(
                    'required' => 'required',
                    'data-validation' => 'required',
                    'data-error' => __('Please input description in short', 'azl'),
                ),
            ));
        }
    }

    if (!empty($atts['name'])) {
        $fields = azl_get_form_fields($atts['name']);
        if (is_array($fields)) {
            foreach ($fields as $field) {
                azl_add_custom_field($cmb, $field);
            }
        }
    }


    // Get CMB2 metabox object
    $cmb_metabox = cmb2_get_metabox($metabox_id, $object_id);

    // Initiate our output variable
    $output = '';

    // Handle form saving (if form has been submitted)
    $new_id = azl_frontend_submission_handle($cmb_metabox, $atts);

    if ($new_id) {

        if (is_wp_error($new_id)) {
            // If there was an error with the submission, add it to our ouput.
            $messages = $new_id->get_error_messages();
            $output .= '<p class="cmb-error">' . sprintf(__('There was an errors in the submission: %s', 'azl'), '<strong><br>' . implode('<br>', $messages) . '</strong>') . '</p>';
        } else {
            do_action('azl_post_updated', $new_id);
            // Add notice of submission
            $post_type_object = get_post_type_object($atts['post_type']);
            if ($object_id == $new_id) {
                $output .= '<p class="cmb-success">' . sprintf(__('Thank you, your %1$s has been updated.', 'azl'), $post_type_object->labels->singular_name) . '</p>';
                return $output;
            } else {
                if ($atts['post_status'] == 'publish') {
                    $output .= '<p class="cmb-success">' . sprintf(__('Thank you, your %1$s has been submitted and is published.', 'azl'), $post_type_object->labels->singular_name) . '</p>';
                } else {
                    $output .= '<p class="cmb-success">' . sprintf(__('Thank you, your %1$s has been submitted and is pending review.', 'azl'), $post_type_object->labels->singular_name) . '</p>';
                }
                return $output;
            }
        }
    }

    // Get our form
    $output .= cmb2_get_metabox_form($cmb_metabox, $object_id, array('save_button' => __('Submit', 'azl')));

    return $output;
}

function azl_create_vendor($customer_id, $username, $email) {
    global $wc_product_vendors;
    if (term_exists($username, $wc_product_vendors->token)) {
        $append = 1;
        $o_username = $username;
        while (term_exists($username, $wc_product_vendors->token)) {
            $username = $o_username . $append;
            $append ++;
        }
    }
    $return = wp_insert_term($username, $wc_product_vendors->token, array(
        'description' => sprintf(__('The vendor %s', 'azl'), $username),
        'slug' => sanitize_title($username)
    ));
    if (is_wp_error($return)) {
        return $return;
    } else {
        $vendor_data['paypal_email'] = $email;
        $vendor_data['commission'] = get_option('woocommerce_product_vendors_base_commission', '50');
        $vendor_data['admins'][] = $customer_id;
        update_option($wc_product_vendors->token . '_' . $return['term_id'], $vendor_data);
        update_user_meta($customer_id, 'product_vendor', $return['term_id']);
    }
    return $return['term_id'];
}

function azl_price_refresh($post_ID) {
    $date_from = get_post_meta($post_ID, '_sale_price_dates_from', true);
    $date_to = get_post_meta($post_ID, '_sale_price_dates_to', true);
    $regular_price = get_post_meta($post_ID, '_regular_price', true);
    $sale_price = get_post_meta($post_ID, '_sale_price', true);
    if ('' !== $sale_price && '' == $date_to && '' == $date_from) {
        update_post_meta($post_ID, '_price', wc_format_decimal($sale_price));
    } else {
        update_post_meta($post_ID, '_price', $regular_price);
    }
    if ('' !== $sale_price && $date_from && $date_from <= strtotime('NOW', current_time('timestamp'))) {
        update_post_meta($post_ID, '_price', wc_format_decimal($sale_price));
    }
    if ($date_to && $date_to < strtotime('NOW', current_time('timestamp'))) {
        update_post_meta($post_ID, '_price', $regular_price);
        update_post_meta($post_ID, '_sale_price_dates_from', '');
        update_post_meta($post_ID, '_sale_price_dates_to', '');
    }
}

function azl_frontend_submission_handle($cmb, $post_data = array()) {
    // If no form submission, bail
    if (empty($_POST)) {
        return false;
    }
    // check required $_POST variables and security nonce    
    if (!isset($_POST['submit-cmb'], $_POST['object_id'], $_POST[$cmb->nonce()]) || !wp_verify_nonce($_POST[$cmb->nonce()], $cmb->nonce())) {
        return new WP_Error('security_fail', __('Security check failed.', 'azl'));
    }

    $validation_messages = array();
    if (!is_user_logged_in()) {
        if (empty($_POST['author_email'])) {
            $validation_messages[] = __('Your Email is required.', 'azl');
        }
    }

    $form_settings = azl_get_form_settings($post_data['name']);
    if (isset($form_settings['general_fields']) && is_array($form_settings['general_fields'])) {
        if (in_array('title', $form_settings['general_fields'])) {
            if (empty($_POST['post_title'])) {
                $validation_messages[] = __('Title is required.', 'azl');
            }
        }
        if (in_array('description', $form_settings['general_fields'])) {
            if (empty($_POST['post_content'])) {
                $validation_messages[] = __('Description is required.', 'azl');
            }
        }
        if (in_array('short_description', $form_settings['general_fields'])) {
            if (empty($_POST['post_excerpt'])) {
                $validation_messages[] = __('Short description is required.', 'azl');
            }
        }
    }

    $fields = array();
    if (!empty($post_data['name'])) {
        $fields = azl_get_form_fields($post_data['name']);
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    if (empty($_POST[$field['id']])) {
                        $validation_messages[] = $field['name'] . ' ' . __('is required.', 'azl');
                    }
                }
            }
        }
    }
    unset($post_data['name']);
    if (!empty($validation_messages)) {
        $error = new WP_Error;
        foreach ($validation_messages as $message) {
            $error->add('post_data_missing', $message);
        }
        return $error;
    }

    /**
     * Fetch sanitized values
     */
    $sanitized_values = $cmb->get_sanitized_values($_POST);
    $author_email = isset($sanitized_values['author_email']) ? $sanitized_values['author_email'] : '';

    if (is_user_logged_in()) {
        $post_data['post_author'] = get_current_user_id();
    } else {
        $user_login = sanitize_user(sanitize_email($author_email), true);
        $user_login = explode('@', $user_login);
        $user_login = $user_login[0];
        if (username_exists($user_login)) {
            $i = 1;
            $user_login_tmp = $user_login;
            do {
                $user_login_tmp = $user_login . '_' . ($i++);
            } while (username_exists($user_login_tmp));
            $user_login = $user_login_tmp;
        }

        $post_data['post_author'] = register_new_user($user_login, $author_email);
        if (is_wp_error($post_data['post_author'])) {
            return $post_data['post_author'];
        }
    }

    // Set our post data arguments
    if (isset($form_settings['general_fields']) && is_array($form_settings['general_fields'])) {
        if (in_array('title', $form_settings['general_fields'])) {
            $post_data['post_title'] = $sanitized_values['post_title'];
        } else {
            if (isset($form_settings['title_template'])) {
                $template = $form_settings['title_template'];
                if (preg_match_all("/{{(.*?)}}/", $template, $m)) {
                    foreach ($m[1] as $i => $varname) {
                        if (isset($sanitized_values[$varname])) {
                            $field = $cmb->get_field($varname);
                            if ($field) {
                                $taxonomy = $field->args('taxonomy');
                                if ($taxonomy) {
                                    $slugs = explode(',', $sanitized_values[$varname]);
                                    $names = array();
                                    foreach ($slugs as $slug) {
                                        $term = get_term_by('slug', $slug, $taxonomy);
                                        if ($term) {
                                            $names[] = $term->name;
                                        }
                                    }
                                    $template = str_replace($m[0][$i], implode(',', $names), $template);
                                }
                            }
                            $template = str_replace($m[0][$i], $sanitized_values[$varname], $template);
                        }
                    }
                }
                $post_data['post_title'] = $template;
            }
        }
        if (in_array('description', $form_settings['general_fields']) && in_array('short_description', $form_settings['general_fields'])) {
            $post_data['post_content'] = isset($sanitized_values['post_content']) ? $sanitized_values['post_content'] : '';
            $post_data['post_excerpt'] = isset($sanitized_values['post_excerpt']) ? $sanitized_values['post_excerpt'] : '';
        }
        if (in_array('description', $form_settings['general_fields']) && !in_array('short_description', $form_settings['general_fields'])) {
            $post_data['post_content'] = isset($sanitized_values['post_content']) ? $sanitized_values['post_content'] : '';
        }
        if (!in_array('description', $form_settings['general_fields']) && in_array('short_description', $form_settings['general_fields'])) {
            $post_data['post_content'] = isset($sanitized_values['post_excerpt']) ? $sanitized_values['post_excerpt'] : '';
            $post_data['post_excerpt'] = isset($sanitized_values['post_excerpt']) ? $sanitized_values['post_excerpt'] : '';
        }
    }

    if (is_numeric($cmb->object_id())) {
        $post_data['ID'] = $cmb->object_id();
        $submission_id = wp_update_post($post_data, true);
    } else {
        // Create the new post
        $submission_id = wp_insert_post($post_data, true);
    }

    // If we hit a snag, update the user
    if (is_wp_error($submission_id)) {
        return $submission_id;
    }

    $is_product = ($post_data['post_type'] == 'product');

    if ($is_product) {
        wp_set_object_terms($submission_id, $post_data['product_type'], 'product_type');

        if (class_exists('WooCommerce_Product_Vendors')) {
            $vendor_id = is_vendor($post_data['post_author']);
            if (!$vendor_id) {
                $user = get_userdata($post_data['post_author']);
                $vendor_id = azl_create_vendor($post_data['post_author'], $user->user_login, $author_email);
                if (is_wp_error($vendor_id)) {
                    return $vendor_id;
                }
            }
            global $wc_product_vendors;
            wp_set_object_terms($submission_id, $vendor_id, $wc_product_vendors->token, false);
        }
    }

    if (is_array($fields)) {
        foreach ($fields as $field) {
            if ($field['type'] == 'azl_file') {
                if (is_user_logged_in()) {
                    $id = $field['id'] . '_id';
                    if (isset($sanitized_values[$id]) && is_numeric($sanitized_values[$id])) {
                        update_post_meta($submission_id, $field['meta_key'], $sanitized_values[$id]);
                    }
                } else {
                    unset($post_data['post_type']);
                    unset($post_data['post_status']);
                    $file_id = azl_frontend_submission_handle_upload($field['id'], $submission_id, $post_data);
                    if ($file_id && !is_wp_error($file_id)) {
                        $file_id = reset($file_id);
                        update_post_meta($submission_id, $field['meta_key'], $file_id);
                    }
                }
            } else if ($field['type'] == 'azl_files') {
                if (is_user_logged_in()) {
                    if (isset($sanitized_values[$field['id']]) && is_array($sanitized_values[$field['id']])) {
                        $downloads = array();
                        foreach ($sanitized_values[$field['id']] as $id => $url) {
                            if (is_numeric($id)) {
                                $downloads[$id] = array('name' => basename(get_attached_file($id)), 'url' => $url);
                            }
                        }
                    }
                } else {
                    unset($post_data['post_type']);
                    unset($post_data['post_status']);
                    $ids = azl_frontend_submission_handle_upload($field['id'], $submission_id, $post_data);
                    if (isset($field['meta_key'])) {
                        if (!empty($ids)) {
                            update_post_meta($submission_id, $field['meta_key'], implode(',', $ids));
                        }
                    }
                    $downloads = array();
                    if (!empty($downloads)) {
                        foreach ($ids as $id) {
                            if (is_numeric($id)) {
                                $downloads[$id] = array('name' => basename(get_attached_file($id)), 'url' => wp_get_attachment_url($id));
                            }
                        }
                    }
                }
                if ($is_product && function_exists('WC')) {
                    WC()->api->includes();
                    WC()->api->register_resources(new WC_API_Server('/'));
                    WC()->api->WC_API_Products->save_downloadable_files($submission_id, $downloads);
                }
            } else if ($field['type'] == 'azl_image') {
                if (is_user_logged_in()) {
                    $id = $field['id'] . '_id';
                    if (isset($sanitized_values[$id]) && is_numeric($sanitized_values[$id])) {
                        update_post_meta($submission_id, $field['meta_key'], $sanitized_values[$id]);
                    } else {
                        delete_post_meta($submission_id, $field['meta_key']);
                    }
                } else {
                    unset($post_data['post_type']);
                    unset($post_data['post_status']);
                    $img_id = azl_frontend_submission_handle_upload($field['id'], $submission_id, $post_data);
                    if ($img_id && !is_wp_error($img_id)) {
                        $img_id = reset($img_id);
                        update_post_meta($submission_id, $field['meta_key'], $img_id);
                    }
                }
            } else if ($field['type'] == 'azl_images') {
                if (is_user_logged_in()) {
                    if (isset($sanitized_values[$field['id']]) && is_array($sanitized_values[$field['id']])) {
                        $gallery_images = array();
                        foreach ($sanitized_values[$field['id']] as $id => $url) {
                            if (is_numeric($id)) {
                                $gallery_images[] = $id;
                            }
                        }
                        update_post_meta($submission_id, $field['meta_key'], implode(',', $gallery_images));
                    } else {
                        delete_post_meta($submission_id, $field['meta_key']);
                    }
                } else {
                    unset($post_data['post_type']);
                    unset($post_data['post_status']);
                    $gallery_images = azl_frontend_submission_handle_upload($field['id'], $submission_id, $post_data);
                    if (!empty($gallery_images)) {
                        update_post_meta($submission_id, $field['meta_key'], implode(',', $gallery_images));
                    }
                }
            } else {
                $field = $cmb->get_field($field['id']);
                $field->object_id($submission_id);
                $field->save_field_from_data($_POST);
            }
        }
    }

    if ($is_product) {
        azl_price_refresh($submission_id);
    }



    return $submission_id;
}

function azl_handle_attachment($field_id, $post_id, $attachment_post_data = array()) {
    if ($_FILES[$field_id]['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', $_FILES[$field_id]['error']);
    }

    // Make sure to include the WordPress media uploader API if it's not (front-end)
    if (!function_exists('media_handle_upload')) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
    }

    return media_handle_upload($field_id, $post_id, $attachment_post_data);
}

function azl_frontend_submission_handle_upload($field_id, $post_id, $attachment_post_data = array()) {
    $ids = array();
    if (!empty($_FILES) || isset($_FILES[$field_id])) {
        $files = $_FILES[$field_id];
        if (is_array($files['name'])) {
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );
                    $_FILES = array($field_id => $file);
                    $id = azl_handle_attachment($field_id, $post_id, $attachment_post_data);
                    if (!is_wp_error($id)) {
                        $ids[] = $id;
                    }
                }
            }
        } else {
            $file = array_filter($_FILES[$field_id]);
            if (!empty($file)) {
                $id = azl_handle_attachment($field_id, $post_id, $attachment_post_data);
                if (!is_wp_error($id)) {
                    $ids[] = $id;
                }
            }
        }
    }
    return $ids;
}

function azl_is_post_type_query($query, $post_type) {
    if (!$query->is_main_query()) {
        return false;
    }
    $post_types = $query->get('post_type');
    if (!is_array($post_types)) {
        $post_types = array($post_types);
    }

    $taxonomy = false;
    $taxonomy_names = get_object_taxonomies($post_type);
    foreach ($taxonomy_names as $taxonomy_name) {
        if ($query->get($taxonomy_name)) {
            $taxonomy = true;
            break;
        }
    }
    return (in_array($post_type, $post_types) && count($post_types) == 1) || $taxonomy;
}

add_action('wp_head', 'azl_wp_head');

function azl_wp_head() {
    print '<script type="text/javascript">';
    print 'window.azl = {};';
    print 'azl.ajaxurl = "' . admin_url('admin-ajax.php') . '";';
    print 'azl.directory = "' . plugins_url('', __FILE__) . '";';
    if (function_exists('cmb2_get_option')) {
        $gmap_styles = cmb2_get_option('azl_options', 'gmap_styles');
        if (!empty($gmap_styles)) {
            print 'azl.mapStyles = ' . $gmap_styles . ';';
        }

        $gmap_marker_image = cmb2_get_option('azl_options', 'gmap_marker_image');
        if (!empty($gmap_marker_image)) {
            print 'azl.markerImage = "' . $gmap_marker_image . '";';
        }

        $gmap_cluster_styles = cmb2_get_option('azl_options', 'gmap_cluster_styles');
        if (!empty($gmap_cluster_styles)) {
            print 'azl.clusterStyles = ' . $gmap_cluster_styles . ';';

            $gmap_cluster_image = cmb2_get_option('azl_options', 'gmap_cluster_image');
            if (!empty($gmap_cluster_image)) {
                print 'azl.clusterStyles[0].url = "' . $gmap_cluster_image . '";';
            }
        }
    }
    print '</script>';
}

function azl_google_maps_js() {
    $gmap_api_key = '';
    if (function_exists('cmb2_get_option')) {
        $gmap_api_key = cmb2_get_option('azl_options', 'gmap_api_key');
    }
    wp_enqueue_script('google-maps', (is_ssl() ? 'https' : 'http') . '://maps.google.com/maps/api/js?sensor=false&libraries=places&key=' . $gmap_api_key);
}

function azl_google_map_scripts() {
    azl_google_maps_js();
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('infobox');
    wp_enqueue_script('markerclusterer');
    wp_enqueue_script('richmarker');
    wp_enqueue_script('azl');
    wp_enqueue_script('mustache');
    wp_enqueue_style('azl');
}

function azl_terms_closest_images() {

    $terms_closest_images = get_transient('azl_terms_closest_images');

    if (!$terms_closest_images) {
        $terms_closest_images = array();
        if (function_exists('cmb2_get_option') && function_exists('get_term_meta')) {
            $marker_image_taxonomy = cmb2_get_option('azl_options', 'gmap_marker_image_taxonomy');
            if (!empty($marker_image_taxonomy)) {
                $terms_with_image = get_terms(array('taxonomy' => $marker_image_taxonomy, 'meta_query' => array(array('key' => 'thumbnail_id', 'compare' => 'EXISTS'))));
                $images = array();
                foreach ($terms_with_image as $term) {
                    $images[$term->term_id] = true;
                }
                if (!empty($images)) {
                    $all_terms = get_terms(array('taxonomy' => $marker_image_taxonomy));
                    $parents = array();
                    foreach ($all_terms as $term) {
                        $parents[$term->term_id] = $term->parent;
                    }
                    foreach ($all_terms as $term) {
                        $current = $term->term_id;
                        $term_with_image = false;
                        if (isset($images[$current])) {
                            $term_with_image = $current;
                        }
                        while (isset($parents[$current]) && isset($parents[$parents[$current]])) {
                            $current = $parents[$current];
                            if (isset($images[$current])) {
                                $term_with_image = $current;
                            }
                        }
                        if ($term_with_image) {
                            $term_thumbnail_id = get_term_meta($term_with_image, 'thumbnail_id', true);
                            if ($term_thumbnail_id) {
                                $term_image = wp_get_attachment_url($term_thumbnail_id);
                                $terms_closest_images[$term->term_id] = $term_image;
                            }
                        }
                    }
                }
            }
        }
        set_transient('azl_terms_closest_images', $terms_closest_images);
    }

    return $terms_closest_images;
}

add_action('clean_term_cache', 'azl_clean_term_cache', 10, 3);

function azl_clean_term_cache($ids, $taxonomy, $clean_taxonomy) {
    if (function_exists('cmb2_get_option')) {
        $marker_image_taxonomy = cmb2_get_option('azl_options', 'gmap_marker_image_taxonomy');
        if (!empty($marker_image_taxonomy)) {
            if (empty($taxonomy) || $taxonomy == $marker_image_taxonomy) {
                delete_transient('azl_terms_closest_images');
            }
        }
    }
}

function azl_get_location($post) {
    $marker_image_taxonomy = false;
    $image_size = false;
    if (function_exists('cmb2_get_option')) {
        $marker_image_taxonomy = cmb2_get_option('azl_options', 'gmap_marker_image_taxonomy');
        $image_size = cmb2_get_option('azl_options', 'gmap_image_size');
    }
    $post_metas = get_post_meta($post->ID, '', true);
    if (isset($post_metas['latitude']) && isset($post_metas['longitude'])) {
        $latitude = is_array($post_metas['latitude']) ? $post_metas['latitude'][0] : $post_metas['latitude'];
        $longitude = is_array($post_metas['longitude']) ? $post_metas['longitude'][0] : $post_metas['longitude'];
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $allowed = apply_filters('azl_google_map_meta_allowed', array('latitude', 'longitude', '_price', '_regular_price', '_sale_price', '_stock', 'total_sales'));
            $post_metas = array_intersect_key($post_metas, array_flip($allowed));

            $marker_image = '';
            if (!empty($marker_image_taxonomy)) {
                $post_terms = wp_get_post_terms($post->ID, $marker_image_taxonomy);
                if (!empty($post_terms)) {
                    $terms_closest_images = azl_terms_closest_images();
                    foreach ($post_terms as $term) {
                        if (isset($terms_closest_images[$term->term_id])) {
                            $marker_image = $terms_closest_images[$term->term_id];
                            break;
                        }
                    }
                }
            }
            $src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $image_size);
            $location = array_merge(array(
                'id' => $post->ID,
                'marker_image' => $marker_image,
                'url' => esc_attr(get_permalink($post->ID)),
                'title' => $post->post_title,
                'description' => $post->post_excerpt,
                'image' => '<a href="' . esc_attr(get_permalink($post->ID)) . '"><img class="image" src="" data-src="' . esc_attr($src[0]) . '" alt=""></a>'
                    ), $post_metas);
            $location = apply_filters('azl_google_map_location', $location, $post);
            return $location;
        }
    }
    return false;
}

add_action('wp_ajax_azl_get_location', 'azl_get_location_callback');
add_action('wp_ajax_nopriv_azl_get_location', 'azl_get_location_callback');

function azl_get_location_callback() {
    if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
        $post = get_post($_POST['post_id']);
        $location = azl_get_location($post);
        print json_encode($location);
    }
    wp_die();
}

function azl_set_locations_marker_image($locations) {
    if (function_exists('cmb2_get_option')) {
        $marker_image_taxonomy = cmb2_get_option('azl_options', 'gmap_marker_image_taxonomy');
        if (!empty($marker_image_taxonomy)) {
            $terms = wp_get_object_terms(array_keys($locations), $marker_image_taxonomy, array('fields' => 'all_with_object_id'));
            if (!empty($terms)) {
                $terms_closest_images = azl_terms_closest_images();
                foreach ($terms as $term) {
                    if (isset($terms_closest_images[$term->term_id])) {
                        $locations[$term->object_id]->marker_image = $terms_closest_images[$term->term_id];
                    }
                }
            }
        }
    }
    return $locations;
}

function azl_get_all_locations() {
    global $wpdb;
    $sql = "SELECT m1.post_id, m1.meta_value as latitude, m2.meta_value as longitude "
            . "FROM {$wpdb->postmeta} as m1 "
            . "INNER JOIN {$wpdb->postmeta} as m2 ON m1.post_id = m2.post_id "
            . "WHERE m1.meta_key = 'latitude' "
            . "AND m2.meta_key = 'longitude' ";
    $locations = $wpdb->get_results($sql, OBJECT_K);

    $locations = azl_set_locations_marker_image($locations);

    return $locations;
}

add_action('wp_ajax_azl_get_locations', 'azl_get_locations_callback');
add_action('wp_ajax_nopriv_azl_get_locations', 'azl_get_locations_callback');

function azl_get_locations_callback() {
    if (
            isset($_POST['southwest_latitude']) && is_numeric($_POST['southwest_latitude']) &&
            isset($_POST['southwest_longitude']) && is_numeric($_POST['southwest_longitude']) &&
            isset($_POST['northeast_latitude']) && is_numeric($_POST['northeast_latitude']) &&
            isset($_POST['northeast_longitude']) && is_numeric($_POST['northeast_longitude']) &&
            isset($_POST['query'])
    ) {
        $locations = azl_get_locations($_POST['southwest_latitude'], $_POST['southwest_longitude'], $_POST['northeast_latitude'], $_POST['northeast_longitude']);
        $query_locations = array();
        $query_vars = $_POST['query'];
        if (function_exists('cmb2_get_option')) {
            $location_taxonomy = cmb2_get_option('azl_options', 'gmap_location_taxonomy');
            if (!empty($location_taxonomy)) {
                foreach ($query_vars['tax_query'] as $key => $tax_query) {
                    if (isset($tax_query['taxonomy']) && $tax_query['taxonomy'] == $location_taxonomy) {
                        unset($query_vars['tax_query'][$key]);
                    }
                }
            }
        }
        $query = new WP_Query($query_vars);
        $query->set('post__in', array_keys($locations));
        foreach ($query->posts as $post) {
            if (isset($locations[$post->ID])) {
                $query_locations[$post->ID] = $locations[$post->ID];
            }
        }
        print json_encode($query_locations);
    }
    wp_die();
}

function azl_get_locations($southwest_latitude, $southwest_longitude, $northeast_latitude, $northeast_longitude) {
    global $wpdb;
    $sql = "SELECT m1.post_id, m1.meta_value as latitude, m2.meta_value as longitude "
            . "FROM {$wpdb->postmeta} as m1 "
            . "INNER JOIN {$wpdb->postmeta} as m2 ON m1.post_id = m2.post_id "
            . "WHERE m1.meta_key = 'latitude' "
            . "AND m2.meta_key = 'longitude' "
            . "AND CAST(m1.meta_value AS DECIMAL) BETWEEN %d AND %d "
            . "AND CAST(m2.meta_value AS DECIMAL) BETWEEN %d AND %d";
    $d1 = 0;
    $d2 = 0;
    $d3 = 0;
    $d4 = 0;
    if (floatval($southwest_latitude) < floatval($northeast_latitude)) {
        $d1 = $southwest_latitude;
        $d2 = $northeast_latitude;
    } else {
        $d1 = $northeast_latitude;
        $d2 = $southwest_latitude;
    }
    if (floatval($southwest_longitude) < floatval($northeast_longitude)) {
        $d3 = $southwest_longitude;
        $d4 = $northeast_longitude;
    } else {
        $d3 = $northeast_longitude;
        $d4 = $southwest_longitude;
    }
    $locations = $wpdb->get_results($wpdb->prepare($sql, $d1, $d2, $d3, $d4), OBJECT_K);
    $locations = azl_set_locations_marker_image($locations);
    return $locations;
}

add_shortcode('azl-google-map', 'azl_google_map_shortcode');

function azl_google_map_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'post_type' => 'product',
        'all' => false,
            ), $atts, 'azl-google-map');


    azl_google_map_scripts();

    global $wp_query;
    $locations = array();

    $query_vars = false;
    if (azl_is_post_type_query($wp_query, $atts['post_type'])) {
        $query_vars = $wp_query->query_vars;
        $query_vars['nopaging'] = true;
        $query_vars['posts_per_page'] = -1;
    }
    if ($atts['all']) {
        $query_vars = array(
            'post_type' => $atts['post_type'],
            'nopaging' => true,
            'posts_per_page' => -1,
        );
    }

    if ($query_vars) {
        $query = new WP_Query($query_vars);
        $all_locations = azl_get_all_locations();
        //$query->set('post__in', array_keys($all_locations));
        if (count($query->posts) > 0) {
            foreach ($query->posts as $post) {
                if (isset($all_locations[$post->ID])) {
                    $locations[$post->ID] = array(
                        'latitude' => $all_locations[$post->ID]->latitude,
                        'longitude' => $all_locations[$post->ID]->longitude,
                    );
                    if (property_exists($all_locations[$post->ID], 'marker_image')) {
                        $locations[$post->ID]['marker_image'] = $all_locations[$post->ID]->marker_image;
                    }
                }
            }
            $output = '<div class="azl-map-wrapper all"><input id="azl-map-' . esc_attr($post->ID) . '" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);"><div class="controls">'
                    . '<div class="toggle"><label for="azl-map-' . esc_attr($post->ID) . '"></label></div>'
                    . '<div class="fullscreen"></div>'
                    . '<div class="locate"></div>'
                    . '<div class="zoom-in"></div>'
                    . '<div class="zoom-out"></div>'
                    . '</div><div class="azl-map" data-query="' . esc_attr(json_encode($query_vars)) . '"></div></div>';
            $output .= '<script type="text/javascript">';
            $output .= 'azl.locations = ' . json_encode($locations) . ';';
            $output .= '</script>';
            return $output;
        }
    }
}

function azl_display_select_tree($term, $selected = '', $level = 0) {
    if (is_object($term)) {
        if (!empty($term->children)) {
            echo '<option value="" disabled>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
            $level++;
            foreach ($term->children as $key => $child) {
                azl_display_select_tree($child, $selected, $level);
            }
        } else {
            echo '<option value="' . $term->slug . '" ' . ( $term->slug == $selected ? 'selected="selected"' : '' ) . '>' . str_repeat('&nbsp;&nbsp;', $level) . '' . $term->name . '</option>';
        }
    }
}

function azl_redirect_to_login() {
    if (class_exists('WooCommerce')) {
        exit(wp_redirect(add_query_arg(array('redirect' => urlencode('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])), wc_get_page_permalink('myaccount'))));
    }
}

add_filter('woocommerce_login_redirect', 'azl_login_redirect');

function azl_login_redirect($redirect_to) {
    if (!empty($_GET['redirect'])) {
        return urldecode($_GET['redirect']);
    }
    return $redirect_to;
}

add_action('template_redirect', 'azl_template_redirect');

function azl_template_redirect() {
    if (isset($_GET['azl']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
        switch ($_GET['azl']) {
            case 'edit':
                if (function_exists('cmb2_get_option')) {
                    $post = get_post($_GET['id']);
                    $forms = cmb2_get_option('azl_options', 'forms');
                    foreach ($forms as $form) {
                        if ($form['post_type'] == $post->post_type) {
                            if (isset($form['page']) && is_numeric($form['page'])) {
                                exit(wp_redirect(add_query_arg(array('id' => $_GET['id']), get_permalink($form['page']))));
                            }
                        }
                    }
                }
                break;
            case 'delete':
                if (function_exists('cmb2_get_option')) {
                    $post = get_post($_GET['id']);
                    $forms = cmb2_get_option('azl_options', 'forms');
                    foreach ($forms as $form) {
                        if ($form['post_type'] == $post->post_type) {
                            if ($post->post_type == 'product') {
                                wp_set_object_terms($_GET['id'], array('exclude-from-search', 'exclude-from-catalog'), 'product_visibility');
                            } else {
                                $update['ID'] = $_GET['id'];
                                $update['post_status'] = 'trash';
                                wp_update_post($update);
                            }
                        }
                    }
                    exit(wp_redirect(remove_query_arg(array('azl', 'id'))));
                }
                break;
            case 'claim':
                if (is_user_logged_in()) {
                    $users = get_post_meta($_GET['id'], 'claim');
                    if (!in_array(get_current_user_id(), $users)) {
                        add_post_meta($_GET['id'], 'claim', get_current_user_id());
                    }
                } else {
                    azl_redirect_to_login();
                }
                break;
            case 'abuse':
                if (is_user_logged_in()) {
                    if (function_exists('cmb2_get_option')) {
                        $id = cmb2_get_option('azl_options', 'abuse');
                        if (is_numeric($id)) {
                            exit(wp_redirect(add_query_arg(array('abuse' => $_GET['id']), get_permalink($id))));
                        }
                    }
                } else {
                    azl_redirect_to_login();
                }
                break;
            case 'favorite':
                if (is_user_logged_in()) {

                    $users = get_post_meta($_GET['id'], 'favorite');
                    if (in_array(get_current_user_id(), $users)) {
                        delete_post_meta($_GET['id'], 'favorite', get_current_user_id());
                    } else {
                        add_post_meta($_GET['id'], 'favorite', get_current_user_id());
                    }

                    $posts = get_user_meta(get_current_user_id(), "favorite", true);
                    if (empty($posts)) {
                        $posts = array();
                    }
                    $post_key = array_search($_GET['id'], $posts);
                    if ($post_key !== false) {
                        unset($posts[$post_key]);
                    } else {
                        $posts[] = $_GET['id'];
                        $posts = array_unique($posts);
                    }
                    update_user_meta(get_current_user_id(), "favorite", $posts);

                    exit(wp_redirect(remove_query_arg(array('azl', 'id'))));
                } else {
                    azl_redirect_to_login();
                }
                break;
        }
    }
}

function azl_current_user_favorites($args, $query) {
    global $wpdb;

    if (is_user_logged_in()) {
        $posts = get_user_meta(get_current_user_id(), "favorite", true);
        if (empty($posts)) {
            $posts = array();
        }
        $args['where'] .= " AND ( $wpdb->posts.ID IN (" . implode(',', $posts) . ")) ";
    }

    return $args;
}
