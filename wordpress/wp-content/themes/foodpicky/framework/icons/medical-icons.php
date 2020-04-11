<?php

/*
 * DEPENDENCIES:
 * less/.../medical-icons.less
 * fonts/webfont-medical-icons.eot
 * fonts/webfont-medical-icons.svg
 * fonts/webfont-medical-icons.ttf
 * fonts/webfont-medical-icons.woff
 */
add_action('admin_enqueue_scripts', 'azexo_admin_enqueue_medical_icons');

function azexo_admin_enqueue_medical_icons() {
    if (class_exists('WPLessPlugin')) {
        $less = WPLessPlugin::getInstance();
        $less->dispatch();
        if (file_exists(get_template_directory() . '/less/' . azexo_get_skin() . '/medical-icons.less')) {
            wp_enqueue_style('azexo-vc_medicalicons', get_template_directory_uri() . '/less/' . azexo_get_skin() . '/medical-icons.less');
        }
    }
}

add_filter('vc_iconpicker-type-medicalicons', 'azexo_vc_iconpicker_type_medicalicons');

function azexo_vc_iconpicker_type_medicalicons($icons) {
    $medicalicons = array(
        array('icon-i-womens-health' => 'i-womens-health'),
        array('icon-i-waiting-area' => 'i-waiting-area'),
        array('icon-i-volume-control' => 'i-volume-control'),
        array('icon-i-ultrasound' => 'i-ultrasound'),
        array('icon-i-text-telephone' => 'i-text-telephone'),
        array('icon-i-surgery' => 'i-surgery'),
        array('icon-i-stairs' => 'i-stairs'),
        array('icon-i-radiology' => 'i-radiology'),
        array('icon-i-physical-therapy' => 'i-physical-therapy'),
        array('icon-i-pharmacy' => 'i-pharmacy'),
        array('icon-i-pediatrics' => 'i-pediatrics'),
        array('icon-i-pathology' => 'i-pathology'),
        array('icon-i-outpatient' => 'i-outpatient'),
        array('icon-i-mental-health' => 'i-mental-health'),
        array('icon-i-medical-records' => 'i-medical-records'),
        array('icon-i-medical-library' => 'i-medical-library'),
        array('icon-i-mammography' => 'i-mammography'),
        array('icon-i-laboratory' => 'i-laboratory'),
        array('icon-i-labor-delivery' => 'i-labor-delivery'),
        array('icon-i-immunizations' => 'i-immunizations'),
        array('icon-i-imaging-root-category' => 'i-imaging-root-category'),
        array('icon-i-imaging-alternative-pet' => 'i-imaging-alternative-pet'),
        array('icon-i-imaging-alternative-mri' => 'i-imaging-alternative-mri'),
        array('icon-i-imaging-alternative-mri-two' => 'i-imaging-alternative-mri-two'),
        array('icon-i-imaging-alternative-ct' => 'i-imaging-alternative-ct'),
        array('icon-i-fire-extinguisher' => 'i-fire-extinguisher'),
        array('icon-i-family-practice' => 'i-family-practice'),
        array('icon-i-emergency' => 'i-emergency'),
        array('icon-i-elevators' => 'i-elevators'),
        array('icon-i-ear-nose-throat' => 'i-ear-nose-throat'),
        array('icon-i-drinking-fountain' => 'i-drinking-fountain'),
        array('icon-i-cardiology' => 'i-cardiology'),
        array('icon-i-billing' => 'i-billing'),
        array('icon-i-anesthesia' => 'i-anesthesia'),
        array('icon-i-ambulance' => 'i-ambulance'),
        array('icon-i-alternative-complementary' => 'i-alternative-complementary'),
        array('icon-i-administration' => 'i-administration'),
        array('icon-i-social-services' => 'i-social-services'),
        array('icon-i-smoking' => 'i-smoking'),
        array('icon-i-restrooms' => 'i-restrooms'),
        array('icon-i-restaurant' => 'i-restaurant'),
        array('icon-i-respiratory' => 'i-respiratory'),
        array('icon-i-registration' => 'i-registration'),
        array('icon-i-oncology' => 'i-oncology'),
        array('icon-i-nutrition' => 'i-nutrition'),
        array('icon-i-nursery' => 'i-nursery'),
        array('icon-i-no-smoking' => 'i-no-smoking'),
        array('icon-i-neurology' => 'i-neurology'),
        array('icon-i-mri-pet' => 'i-mri-pet'),
        array('icon-i-interpreter-services' => 'i-interpreter-services'),
        array('icon-i-internal-medicine' => 'i-internal-medicine'),
        array('icon-i-intensive-care' => 'i-intensive-care'),
        array('icon-i-inpatient' => 'i-inpatient'),
        array('icon-i-information-us' => 'i-information-us'),
        array('icon-i-infectious-diseases' => 'i-infectious-diseases'),
        array('icon-i-hearing-assistance' => 'i-hearing-assistance'),
        array('icon-i-health-services' => 'i-health-services'),
        array('icon-i-health-education' => 'i-health-education'),
        array('icon-i-gift-shop' => 'i-gift-shop'),
        array('icon-i-genetics' => 'i-genetics'),
        array('icon-i-first-aid' => 'i-first-aid'),
        array('icon-i-dermatology' => 'i-dermatology'),
        array('icon-i-dental' => 'i-dental'),
        array('icon-i-coffee-shop' => 'i-coffee-shop'),
        array('icon-i-chapel' => 'i-chapel'),
        array('icon-i-cath-lab' => 'i-cath-lab'),
        array('icon-i-care-staff-area' => 'i-care-staff-area'),
        array('icon-i-accessibility' => 'i-accessibility'),
        array('icon-i-diabetes-education' => 'i-diabetes-education'),
        array('icon-i-hospital' => 'i-hospital'),
        array('icon-i-kidney' => 'i-kidney'),
        array('icon-i-ophthalmology' => 'i-ophthalmology'),
        array('icon-womens-health' => 'womens-health'),
        array('icon-waiting-area' => 'waiting-area'),
        array('icon-volume-control' => 'volume-control'),
        array('icon-ultrasound' => 'ultrasound'),
        array('icon-text-telephone' => 'text-telephone'),
        array('icon-surgery' => 'surgery'),
        array('icon-stairs' => 'stairs'),
        array('icon-radiology' => 'radiology'),
        array('icon-physical-therapy' => 'physical-therapy'),
        array('icon-pharmacy' => 'pharmacy'),
        array('icon-pediatrics' => 'pediatrics'),
        array('icon-pathology' => 'pathology'),
        array('icon-outpatient' => 'outpatient'),
        array('icon-ophthalmology' => 'ophthalmology'),
        array('icon-mental-health' => 'mental-health'),
        array('icon-medical-records' => 'medical-records'),
        array('icon-medical-library' => 'medical-library'),
        array('icon-mammography' => 'mammography'),
        array('icon-laboratory' => 'laboratory'),
        array('icon-labor-delivery' => 'labor-delivery'),
        array('icon-kidney' => 'kidney'),
        array('icon-immunizations' => 'immunizations'),
        array('icon-imaging-root-category' => 'imaging-root-category'),
        array('icon-imaging-alternative-pet' => 'imaging-alternative-pet'),
        array('icon-imaging-alternative-mri' => 'imaging-alternative-mri'),
        array('icon-imaging-alternative-mri-two' => 'imaging-alternative-mri-two'),
        array('icon-imaging-alternative-ct' => 'imaging-alternative-ct'),
        array('icon-hospital' => 'hospital'),
        array('icon-fire-extinguisher' => 'fire-extinguisher'),
        array('icon-family-practice' => 'family-practice'),
        array('icon-emergency' => 'emergency'),
        array('icon-elevators' => 'elevators'),
        array('icon-ear-nose-throat' => 'ear-nose-throat'),
        array('icon-drinking-fountain' => 'drinking-fountain'),
        array('icon-diabetes-education' => 'diabetes-education'),
        array('icon-cardiology' => 'cardiology'),
        array('icon-billing' => 'billing'),
        array('icon-anesthesia' => 'anesthesia'),
        array('icon-ambulance' => 'ambulance'),
        array('icon-alternative-complementary' => 'alternative-complementary'),
        array('icon-administration' => 'administration'),
        array('icon-accessibility' => 'accessibility'),
        array('icon-social-services' => 'social-services'),
        array('icon-smoking' => 'smoking'),
        array('icon-restrooms' => 'restrooms'),
        array('icon-restaurant' => 'restaurant'),
        array('icon-respiratory' => 'respiratory'),
        array('icon-oncology' => 'oncology'),
        array('icon-nutrition' => 'nutrition'),
        array('icon-nursery' => 'nursery'),
        array('icon-no-smoking' => 'no-smoking'),
        array('icon-neurology' => 'neurology'),
        array('icon-mri-pet' => 'mri-pet'),
        array('icon-interpreter-services' => 'interpreter-services'),
        array('icon-internal-medicine' => 'internal-medicine'),
        array('icon-intensive-care' => 'intensive-care'),
        array('icon-inpatient' => 'inpatient'),
        array('icon-information-us' => 'information-us'),
        array('icon-infectious-diseases' => 'infectious-diseases'),
        array('icon-hearing-assistance' => 'hearing-assistance'),
        array('icon-health-services' => 'health-services'),
        array('icon-health-education' => 'health-education'),
        array('icon-gift-shop' => 'gift-shop'),
        array('icon-genetics' => 'genetics'),
        array('icon-first-aid' => 'first-aid'),
        array('icon-dental' => 'dental'),
        array('icon-coffee-shop' => 'coffee-shop'),
        array('icon-chapel' => 'chapel'),
        array('icon-cath-lab' => 'cath-lab'),
        array('icon-care-staff-area' => 'care-staff-area'),
        array('icon-registration' => 'registration'),
        array('icon-dermatology' => 'dermatology'),
    );
    return array_merge($icons, $medicalicons);
}

if (function_exists('vc_get_shortcode') && function_exists('vc_map_update')) {

    $settings = vc_get_shortcode('azexo_generic_content');

    if (isset($settings['params']) && is_array($settings['params'])) {
        $params = $settings['params'];
        $i = 0;
        foreach ($params as &$param) {
            if ($param['param_name'] == 'icon_library') {
                $param['value'][esc_html__('Medical icons', 'foodpicky')] = 'medicalicons';
                break;
            }
            $i++;
        }
        array_splice($params, $i + 1, 0, array(array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'foodpicky'),
                'param_name' => 'icon_medicalicons',
                'group' => esc_html__('Media', 'foodpicky'),
                'value' => 'i-womens-health',
                'settings' => array(
                    'emptyIcon' => false,
                    'type' => 'medicalicons',
                    'iconsPerPage' => 4000,
                ),
                'dependency' => array(
                    'element' => 'icon_library',
                    'value' => 'medicalicons',
                ),
                'description' => esc_html__('Select icon from library.', 'foodpicky'),
        )));
        vc_map_update('azexo_generic_content', 'params', $params);
    }

    add_filter("shortcode_atts_azexo_generic_content", 'azexo_shortcode_atts_azexo_generic_content', 10, 3);

    function azexo_shortcode_atts_azexo_generic_content($out, $pairs, $atts) {
        if (isset($atts['icon_medicalicons'])) {
            $out['icon_medicalicons'] = $atts['icon_medicalicons'];
        }
        return $out;
    }

}