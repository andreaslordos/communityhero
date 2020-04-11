<?php

function cmb2_get_hours_options($value = false) {
    $hours = array('00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
    $hours = array_combine($hours, $hours);


    $hours_options = '';
    $hours_options .= '<option value="" ' . selected($value, '', false) . '>' . esc_attr__('Closed', 'azl') . '</option>';
    foreach ($hours as $hour => $hour_name) {
        $hours_options .= '<option value="' . $hour . '" ' . selected($value, $hour, false) . '>' . date_i18n(get_option('time_format'), strtotime($hour)) . '</option>';
    }

    return $hours_options;
}

function cmb2_render_working_hours_field_callback($field, $value, $object_id, $object_type, $field_type) {


    $days = array(
        '1' => __('Monday', 'azl'),
        '2' => __('Tuesday', 'azl'),
        '3' => __('Wednesday', 'azl'),
        '4' => __('Thursday', 'azl'),
        '5' => __('Friday', 'azl'),
        '6' => __('Saturday', 'azl'),
        '7' => __('Sunday', 'azl'),
    );

    $default = array();
    foreach ($days as $day => $day_name) {
        $default['open-' . $day] = '';
        $default['close-' . $day] = '';
    }
    $value = wp_parse_args($value, $default);
    ?>
    <table>
        <thead>
            <tr>
                <th><?php esc_attr_e('Day', 'azl'); ?></th>
                <th><?php esc_attr_e('Open', 'azl'); ?></th>
                <th><?php esc_attr_e('Close', 'azl'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($days as $day => $day_name) {
                ?>
                <tr>
                    <td><label><?php print $day_name; ?></label></td>
                    <td>
                        <?php
                        echo $field_type->select(array(
                            'name' => $field_type->_name('[open-' . $day . ']'),
                            'id' => $field_type->_id('_open_' . $day),
                            'options' => cmb2_get_hours_options($value['open-' . $day]),
                            'desc' => '',
                        ));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $field_type->select(array(
                            'name' => $field_type->_name('[close-' . $day . ']'),
                            'id' => $field_type->_id('_close_' . $day),
                            'options' => cmb2_get_hours_options($value['close-' . $day]),
                            'desc' => '',
                        ));
                        ?>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
    print $field_type->_desc(true);
}

add_filter('cmb2_render_working_hours', 'cmb2_render_working_hours_field_callback', 10, 5);


add_filter('cmb2_override_meta_save', 'cmb2_override_meta_save_working_hours_callback', 10, 4);

function cmb2_override_meta_save_working_hours_callback($null, $a, $field_args, $field) {
    $hours = array('00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00');
    if ($field_args['type'] == 'working_hours' && $a['id']) {
        if (isset($a['value']) && is_array($a['value'])) {
            for ($i = 1; $i <= 7; $i++) {
                if (!empty($a['value']['open-' . $i]) && !empty($a['value']['close-' . $i])) {
                    $key = $a['field_id'] . '-' . $i . '-hours';
                    delete_metadata($a['type'], $a['id'], $key);
                    $open = array_search($a['value']['open-' . $i], $hours);
                    $close = array_search($a['value']['close-' . $i], $hours);
                    if ($open !== false && $close !== false) {
                        if ($open < $close) {
                            for ($h = $open; $h < $close; $h++) {
                                add_metadata($a['type'], $a['id'], $key, $h);
                            }
                        } else {
                            for ($h = $open; $h < count($hours); $h++) {
                                add_metadata($a['type'], $a['id'], $key, $h);
                            }
                            for ($h = 0; $h < $close; $h++) {
                                add_metadata($a['type'], $a['id'], $key, $h);
                            }
                        }
                    }
                }
            }
            if (class_exists('WC_Bookings')) {
                $booking_availability = array();
                for ($i = 1; $i <= 7; $i++) {
                    if (!empty($a['value']['open-' . $i]) && !empty($a['value']['close-' . $i])) {
                        $rule = array(
                            'type' => 'time:' . $i,
                            'bookable' => 'yes',
                            'from' => $a['value']['open-' . $i],
                            'to' => $a['value']['close-' . $i],
                        );
                        $booking_availability[] = $rule;
                    }
                }
                update_post_meta($a['id'], '_wc_booking_availability', $booking_availability);
                update_post_meta($a['id'], '_wc_booking_default_date_availability', 'non-available');
                update_post_meta($a['id'], '_wc_booking_calendar_display_mode', 'always_visible');

                update_post_meta($a['id'], '_wc_booking_duration_unit', 'hour');
                update_post_meta($a['id'], '_wc_booking_duration_type', 'fixed');
                update_post_meta($a['id'], '_wc_booking_duration', '1');

                update_post_meta($a['id'], '_wc_booking_has_resources', 'no');
                update_post_meta($a['id'], '_wc_booking_has_person_types', 'no');

                update_post_meta($a['id'], '_wc_booking_min_date', '0');
                update_post_meta($a['id'], '_wc_booking_min_date_unit', 'month');
                update_post_meta($a['id'], '_wc_booking_max_date', '12');
                update_post_meta($a['id'], '_wc_booking_max_date_unit', 'month');

                wp_set_object_terms($a['id'], 'booking', 'product_type');
            }
        }
    }
}
