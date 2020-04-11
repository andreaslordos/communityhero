<?php
add_filter('cmb2_render_availability_calendar', 'cmb2_render_availability_calendar_callback', 10, 5);

function cmb2_render_availability_calendar_callback($field, $value, $object_id, $object_type, $field_type) {
    wp_enqueue_script('availability-calendar', plugins_url('js/availability-calendar.js', __FILE__), array('jquery-ui-datepicker', 'jquery-ui-tooltip'));
    wp_enqueue_style('availability-calendar', plugins_url('css/availability-calendar.css', __FILE__), array());
    ?>
    <script type="text/javascript">
    <?php
    print 'window["ac_' . $field->args('id') . '"] = ' . (empty($value) ? '{}' : htmlspecialchars_decode($value) ) . ';';
    ?>
    </script>
    <div class="availability-calendar">
        <input class="availability" type="hidden" name="<?php print $field->args('id') ?>" />
        <div class="calendar" data-months-number="1"></div>
        <div class="day-data">
            <p>
                <label><?php esc_html_e('From date', 'azl') ?></label>
                <input class="from-date" type="text" />
            </p>
            <p>
                <label><?php esc_html_e('To date', 'azl') ?></label>
                <input class="to-date" type="text" />
            </p>
            <p>
                <label><?php esc_html_e('Your notes', 'azl') ?></label>
                <textarea class="notes"></textarea>
            </p>
            <p class="buttons">
                <button class="reserve"><?php esc_html_e('Reserve period', 'azl') ?></button>
                <button class="dereserve"><?php esc_html_e('Dereserve period', 'azl') ?></button>
                
            </p>
        </div>
    </div>

    <?php
}

add_filter('cmb2_sanitize_availability_calendar', 'cmb2_sanitize_availability_calendar_callback', 10, 4);

function cmb2_sanitize_availability_calendar_callback($override_value, $value, $object_id, $field_args) {
    return htmlspecialchars_decode(sanitize_text_field($value));
}

add_filter('cmb2_override_meta_save', 'cmb2_override_meta_save_availability_calendar_callback', 10, 4);

function cmb2_override_meta_save_availability_calendar_callback($null, $a, $field_args, $field) {    
    if ($field_args['type'] == 'availability_calendar' && $a['id']) {        
        $dates = json_decode($a['value'], true);
        $key = $a['field_id'] . '-days';
        delete_metadata($a['type'], $a['id'], $key);
        foreach ($dates as $date => $data) {
            add_metadata($a['type'], $a['id'], $key, $date);
        }
    }
}
