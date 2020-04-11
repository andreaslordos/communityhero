<?php

/*
  Field Name: Product event time left
 */
?>
<?php

$product = wc_get_product();
$event_date_time = get_post_meta($product->get_id(), 'event-date-time', true);
if ($event_date_time) {
    azexo_time_left($event_date_time, (isset($options['product-time-left_prefix']) && !empty($options['product-time-left_prefix'])) ? esc_html($options['product-time-left_prefix']) : '');
}
?>