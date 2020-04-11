<?php
/*
  Field Name: Product OpenTable booking form
 */
?>
<?php
global $post;
$restaurantID = esc_attr(get_post_meta($post->ID, 'restaurantID', true));
$options = get_option(AZEXO_FRAMEWORK);

wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('azexo-fields', get_template_directory_uri() . '/js/fields.js', array('jquery'), AZEXO_FRAMEWORK_VERSION, true);
if (!empty($restaurantID)):
    ?>
    <div class="azot-form-wrap">
        <?php print (isset($options['product-open-table_prefix']) && !empty($options['product-open-table_prefix'])) ? '<label>' . esc_html($options['product-open-table_prefix']) . '</label>' : '';?>
        <form method="get" class="azot-form" action="//www.opentable.com/restaurant-search.aspx" target="_blank">
            <div class="azot-wrapper">
                <div class="azot-date-wrap">
                    <label for="date-<?php print esc_attr($post->ID); ?>"><?php esc_html_e('Date', 'foodpicky');?></label>
                    <input id="date-<?php print esc_attr($post->ID); ?>" name="startDate" class="azot-reservation-date" type="text" value="" placeholder="<?php esc_html_e('Date', 'foodpicky');?>" readonly="readonly">
                </div>
                <div class="azot-time-wrap">
                    <label for="time-<?php print esc_attr($post->ID); ?>"><?php esc_html_e('Time', 'foodpicky');?></label>
                    <select id="time-<?php print esc_attr($post->ID); ?>" name="ResTime" class="azot-reservation-time">
                        <?php
                        //Time Loop
                        //@SEE: http://stackoverflow.com/questions/6530836/php-time-loop-time-one-and-half-of-hour
                        $inc = 30 * 60;
                        $start = ( strtotime('8AM') ); // 6  AM
                        $end = ( strtotime('11:59PM') ); // 10 PM
                        for ($i = $start; $i <= $end; $i += $inc) {
                            // to the standart format
                            $time = date('g:i a', $i);
                            $timeValue = date('g:ia', $i);
                            $default = "7:00pm";
                            print "<option value=\"$timeValue\" " . ( ( $timeValue == $default ) ? ' selected="selected" ' : "" ) . ">$time</option>" . PHP_EOL;
                        }
                        ?>
                    </select>
                </div>
                <div class="azot-party-size-wrap">
                    <label for="party-<?php print esc_attr($post->ID); ?>"><?php esc_html_e('Party Size', 'foodpicky'); ?></label>
                    <select id="party-<?php print esc_attr($post->ID); ?>" name="partySize" class="azot-party-size-select">
                        <option value="1">1 <?php esc_html_e('Person', 'foodpicky'); ?></option>
                        <option value="2" selected="selected">2 People</option>
                        <option value="3">3 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="4">4 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="5">5 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="6">6 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="7">7 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="8">8 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="9">9 <?php esc_html_e('People', 'foodpicky'); ?></option>
                        <option value="10">10 <?php esc_html_e('People', 'foodpicky'); ?></option>
                    </select>

                </div>

                <div class="azot-button-wrap">
                    <input type="submit" class="" value="<?php esc_html_e('Find a Table', 'foodpicky'); ?>" />
                </div>
                <input type="hidden" name="RestaurantID" class="RestaurantID" value="<?php print esc_attr($restaurantID); ?>">
                <input type="hidden" name="rid" class="rid" value="<?php print esc_attr($restaurantID); ?>">
                <input type="hidden" name="GeoID" class="GeoID" value="15">
                <input type="hidden" name="txtDateFormat" class="txtDateFormat" value="MM/dd/yyyy">
                <input type="hidden" name="RestaurantReferralID" class="RestaurantReferralID" value="<?php print esc_attr($restaurantID); ?>">
            </div>
        </form>
    </div>
<?php endif; ?>