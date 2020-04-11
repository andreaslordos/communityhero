<?php
add_filter('cmb2_render_prices_calendar', 'cmb2_render_prices_calendar_callback', 10, 5);

function cmb2_render_prices_calendar_callback($field, $value, $object_id, $object_type, $field_type) {
    wp_enqueue_script('prices-calendar', plugins_url('js/prices-calendar.js', __FILE__), array('jquery-ui-datepicker'));
    wp_enqueue_style('prices-calendar', plugins_url('css/prices-calendar.css', __FILE__), array());
    ?>
    <script type="text/javascript">
    <?php
    print 'window["pc_' . $field->args('id') . '"] = ' . (empty($value) ? '{}' : htmlspecialchars_decode($value) ) . ';';
    ?>
    </script>
    <div class="prices-calendar">
        <input class="prices" type="hidden" name="<?php print $field->args('id') ?>" />
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
                <label><?php esc_html_e('Price', 'azl') ?></label>
                <input class="price" type="text" data-validation="float" data-error="<?php esc_html_e('Please input price as number', 'azl') ?>" value="0"/>
            </p>
            <p class="buttons">
                <button class="set"><?php esc_html_e('Set', 'azl') ?></button>
                <button class="remove"><?php esc_html_e('Remove', 'azl') ?></button>
            </p>
        </div>
    </div>

    <?php
}

add_filter('cmb2_sanitize_prices_calendar', 'cmb2_sanitize_prices_calendar_callback', 10, 4);

function cmb2_sanitize_prices_calendar_callback($override_value, $value, $object_id, $field_args) {
    return htmlspecialchars_decode(sanitize_text_field($value));
}