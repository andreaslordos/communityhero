<?php

define('AZEXO_EMAIL_SMS_GATEWAY', '@email-sms-gateway.com');

add_filter('wpcf7_form_hidden_fields', 'azexo_form_hidden_fields');

function azexo_form_hidden_fields($hidden_fields) {
    global $post;
    if (is_object($post) && property_exists($post, 'ID')) {
        $hidden_fields['_post_id'] = $post->ID;
    }
    return $hidden_fields;
}

add_action('wpcf7_before_send_mail', 'azexo_before_send_mail');

function azexo_before_send_mail($contact_form) {
    $submission = WPCF7_Submission::get_instance($contact_form);
    if (isset($submission->posted_data['recipient_email'])) {
        $properties = $contact_form->get_properties();
        $properties['mail']['recipient'] = $submission->posted_data['recipient_email'];
        $contact_form->set_properties($properties);
    }
    if (isset($submission->posted_data['recipient_phone'])) {
        $properties = $contact_form->get_properties();
        $properties['mail']['recipient'] = $submission->posted_data['recipient_phone'] . AZEXO_EMAIL_SMS_GATEWAY;
        $contact_form->set_properties($properties);
    }
}

add_filter('wpcf7_special_mail_tags', 'azexo_special_mail_tags', 10, 3);

function azexo_special_mail_tags($output, $name, $html) {

    $name = preg_replace('/^wpcf7\./', '_', $name); // for back-compat

    $submission = WPCF7_Submission::get_instance();

    if (!$submission) {
        return $output;
    }

    if (strpos($name, 'post-meta-') == 0) {
        if (isset($submission->posted_data['_post_id'])) {
            return get_post_meta($submission->posted_data['_post_id'], str_replace('post-meta-', '', $name), true);
        } else {
            return '';
        }
    }

    return $output;
}
