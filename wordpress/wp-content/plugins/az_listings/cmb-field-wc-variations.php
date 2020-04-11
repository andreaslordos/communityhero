<?php
add_filter('cmb2_render_wc_variations', 'cmb2_render_wc_variations_callback', 10, 5);

function cmb2_render_wc_variations_callback($field, $value, $object_id, $object_type, $field_type) {
    wp_enqueue_script('wc-variations', plugins_url('js/wc-variations.js', __FILE__), array('jquery-ui-datepicker'));
    wp_enqueue_style('wc-variations', plugins_url('css/wc-variations.css', __FILE__), array());
    ?>
    <script type="text/javascript">
    <?php
    print 'window["wcv_' . $field->args('id') . '"] = ' . (empty($value) ? '{}' : htmlspecialchars_decode($value) ) . ';';
    ?>
    </script>
    <div class="wc-variations">
        <input class="variations" type="hidden" name="<?php print $field->args('id') ?>" />
        <div class="attributes">
            <div class="attribute">
                <div class="wrapper">
                    <div class="name">
                        <label><?php esc_html_e('Attribute Name', 'azl') ?></label>
                        <input class="name" type="text" />
                    </div>            
                    <div class="values">
                        <label><?php esc_html_e('Attribute Values', 'azl') ?></label>
                        <div class="wrapper">
                            <div class="value">                    
                                <input class="value" type="text" />
                                <button class="remove-value"><?php esc_html_e('Remove Value', 'azl') ?></button>
                            </div>               
                        </div>
                        <button class="add-value"><?php esc_html_e('Add Value', 'azl') ?></button>
                    </div>        
                </div>
                <button class="remove-attribute"><?php esc_html_e('Remove Attribute', 'azl') ?></button>
            </div>
            <button class="add-attribute"><?php esc_html_e('Add Attribute', 'azl') ?></button>
        </div>
        <div class="variations">
            <div class="variation">
                <label><?php esc_html_e('Variation', 'azl') ?>: <span class="name"></span></label>
                <div class="price">
                    <div class="regular-price">
                        <label><?php esc_html_e('Regular price', 'azl') ?></label>
                        <div class="wrapper">
                            <input class="regular-price" type="text" data-optional="yes" data-validation="float" data-error="<?php esc_html_e('Please enter correct price', 'azl') ?>"/>
                        </div>
                    </div>
                    <div class="sale-price">
                        <label><?php esc_html_e('Sale price', 'azl') ?></label>
                        <div class="wrapper">
                            <input class="sale-price" type="text" data-optional="yes" data-validation="float" data-error="<?php esc_html_e('Please enter correct price', 'azl') ?>"/>
                        </div>
                    </div>
                </div>
                <div class="sale-schedule">
                    <div class="sale-start-date">
                        <label><?php esc_html_e('Sale start date', 'azl') ?></label>
                        <input class="sale-start-date" type="text" />
                    </div>
                    <div class="sale-end-date">
                        <label><?php esc_html_e('Sale end date', 'azl') ?></label>
                        <input class="sale-end-date" type="text" />
                    </div>
                </div>                
                <div class="description">
                    <label><?php esc_html_e('Description', 'azl') ?></label>
                    <textarea class="description" ></textarea>
                </div>
                <button class="remove-variation"><?php esc_html_e('Remove Variation', 'azl') ?></button>
            </div>            
        </div>
    </div>

    <?php
}

add_filter('cmb2_sanitize_wc_variations', 'cmb2_sanitize_wc_variations_callback', 10, 4);

function cmb2_sanitize_wc_variations_callback($override_value, $value, $object_id, $field_args) {
    return htmlspecialchars_decode(sanitize_text_field($value));
}

function azl_add_update_attributes($post_id, $attribute_names, $attribute_values) {
    $attribute_names_max_key = max(array_keys($attribute_names));
    $is_visible = 1;
    $is_variation = 1;
    $is_taxonomy = 0;
    $attributes = array();
    for ($i = 0; $i <= $attribute_names_max_key; $i++) {
        if ($is_taxonomy) {

            $values_are_slugs = false;

            if (isset($attribute_values[$i])) {

                // Select based attributes - Format values (posted values are slugs)
                if (is_array($attribute_values[$i])) {
                    $values = array_map('sanitize_title', $attribute_values[$i]);
                    $values_are_slugs = true;

                    // Text based attributes - Posted values are term names - don't change to slugs
                } else {
                    $values = array_map('stripslashes', array_map('strip_tags', explode(WC_DELIMITER, $attribute_values[$i])));
                }

                // Remove empty items in the array
                $values = array_filter($values, 'strlen');
            } else {
                $values = array();
            }

            // Update post terms
            if (taxonomy_exists($attribute_names[$i])) {

                foreach ($values as $key => $value) {
                    $term = get_term_by($values_are_slugs ? 'slug' : 'name', trim($value), $attribute_names[$i]);

                    if ($term) {
                        $values[$key] = intval($term->term_id);
                    } else {
                        $term = wp_insert_term(trim($value), $attribute_names[$i]);
                        if (isset($term->term_id)) {
                            $values[$key] = intval($term->term_id);
                        }
                    }
                }

                wp_set_object_terms($post_id, $values, $attribute_names[$i]);
            }

            if (!empty($values)) {
                // Add attribute to array, but don't set values
                $attributes[sanitize_title($attribute_names[$i])] = array(
                    'name' => wc_clean($attribute_names[$i]),
                    'value' => '',
                    'position' => 0,
                    'is_visible' => $is_visible,
                    'is_variation' => $is_variation,
                    'is_taxonomy' => $is_taxonomy
                );
            }
        } elseif (isset($attribute_values[$i])) {

            // Text based, separate by pipe
            $values = implode(' ' . WC_DELIMITER . ' ', array_map('wc_clean', wc_get_text_attributes($attribute_values[$i])));

            // Custom attribute - Add attribute to array and set the values
            $attributes[sanitize_title($attribute_names[$i])] = array(
                'name' => wc_clean($attribute_names[$i]),
                'value' => $values,
                'position' => 0,
                'is_visible' => $is_visible,
                'is_variation' => $is_variation,
                'is_taxonomy' => $is_taxonomy
            );
        }
    }
    update_post_meta($post_id, '_product_attributes', $attributes);
}

function azl_add_update_variation($post_id, $variation) {
    $meta_query = array();
    $attributes = maybe_unserialize(get_post_meta($post_id, '_product_attributes', true));
    foreach ($attributes as $attribute) {
        if ($attribute['is_variation']) {
            $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
            if ($attribute['is_taxonomy']) {
                // Don't use wc_clean as it destroys sanitized characters
                $value = isset($variation[$attribute_key]) ? sanitize_title(stripslashes($variation[$attribute_key])) : '';
            } else {
                $value = isset($variation[$attribute_key]) ? wc_clean(stripslashes($variation[$attribute_key])) : '';
            }
            $meta_query[] = array(
                'key' => $attribute_key,
                'value' => $value,
            );
        }
    }
    $posts = get_posts(array(
        'numberposts' => -1,
        'post_type' => 'product_variation',
        'post_parent' => $post_id,
        'meta_query' => $meta_query
    ));
    if (empty($posts)) {
        $variation_post = array(
            'post_title' => sprintf(__('Variation #%s of %s', 'azl'), absint($post_id), esc_html(get_the_title($post_id))),
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_parent' => $post_id,
            'post_type' => 'product_variation',
        );
        $variation_id = wp_insert_post($variation_post);
    } else {
        $variation_post = reset($posts);
        $variation_id = $variation_post->ID;
    }

    update_post_meta($variation_id, '_regular_price', esc_attr($variation['regular_price']));
    update_post_meta($variation_id, '_sale_price', esc_attr($variation['sale_price']));
    update_post_meta($variation_id, '_sale_price_dates_from', strtotime(esc_attr($variation['sale_start_date'])));
    update_post_meta($variation_id, '_sale_price_dates_to', strtotime(esc_attr($variation['sale_end_date'])));
    update_post_meta($variation_id, '_variation_description', wp_kses_post($variation['description']));
    azl_price_refresh($variation_id);

    $updated_attribute_keys = array();
    foreach ($attributes as $attribute) {
        if ($attribute['is_variation']) {
            $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
            $updated_attribute_keys[] = $attribute_key;

            if ($attribute['is_taxonomy']) {
                // Don't use wc_clean as it destroys sanitized characters
                $value = isset($variation[$attribute_key]) ? sanitize_title(stripslashes($variation[$attribute_key])) : '';
            } else {
                $value = isset($variation[$attribute_key]) ? wc_clean(stripslashes($variation[$attribute_key])) : '';
            }

            update_post_meta($variation_id, $attribute_key, $value);
        }
    }

    // Remove old taxonomies attributes so data is kept up to date - first get attribute key names
    global $wpdb;
    $delete_attribute_keys = $wpdb->get_col($wpdb->prepare("SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND meta_key NOT IN ( '" . implode("','", $updated_attribute_keys) . "' ) AND post_id = %d;", $variation_id));

    foreach ($delete_attribute_keys as $key) {
        delete_post_meta($variation_id, $key);
    }
}

add_filter('cmb2_override_meta_save', 'cmb2_override_meta_save_wc_variations_callback', 10, 4);

function cmb2_override_meta_save_wc_variations_callback($null, $a, $field_args, $field) {
    if ($field_args['type'] == 'wc_variations' && $a['id']) {
        $wc_variations = json_decode(stripslashes($a['value']), true);
        if (is_array($wc_variations['attributes']) && is_array($wc_variations['variations'])) {
            $attribute_names = array();
            $attribute_values = array();
            foreach ($wc_variations['attributes'] as $name => $values) {
                $attribute_names[] = $name;
                $attribute_values[] = implode(WC_DELIMITER, $values);
            }
            if (!empty($attribute_names)) {
                azl_add_update_attributes($a['id'], $attribute_names, $attribute_values);
                foreach ($wc_variations['variations'] as $variation) {
                    $new_variation = $variation['data'];
                    if (!$new_variation['removed']) {
                        foreach ($wc_variations['attributes'] as $name => $values) {
                            $attribute_key = 'attribute_' . sanitize_title($name);
                            $new_variation[$attribute_key] = $variation['combination'][$name];
                        }
                        azl_add_update_variation($a['id'], $new_variation);
                    }
                }
                update_post_meta($a['id'], '_default_attributes', $wc_variations['variations'][0]['combination']);
                wc_delete_product_transients($a['id']);
            }
        }
    }
}
