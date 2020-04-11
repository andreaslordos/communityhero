<?php
/*
  Field Name: Product GMAP Location
 */
?>

<?php
if(function_exists('azl_google_map_scripts')) {
    azl_google_map_scripts();
}
$product = wc_get_product();
$latitude = trim(get_post_meta($product->get_id(), 'latitude', true));
$longitude = trim(get_post_meta($product->get_id(), 'longitude', true));

$locations = array();
$locations[$product->get_id()] = new stdClass;
$locations[$product->get_id()]->marker_image = '';
if (function_exists('azl_set_locations_marker_image')) {
    $locations = azl_set_locations_marker_image($locations);
}
$marker_image = $locations[$product->get_id()]->marker_image;

if (!empty($latitude) && !empty($longitude)):
    ?>
    <div class="azl-map-wrapper single" 
         data-latitude="<?php print esc_attr($latitude) ?>" 
         data-longitude="<?php print esc_attr($longitude) ?>"
         data-marker_image="<?php print esc_attr($marker_image) ?>"
         >
        <div class="controls">
            <div class="locate"></div>
            <div class="zoom-in"></div>
            <div class="zoom-out"></div>
            <a href="https://www.google.com/maps/dir/Current+Location/<?php print esc_attr($latitude) ?>,<?php print esc_attr($longitude) ?>" class="directions" target="_blank"></a>        
        </div>    
        <div class="azl-map"></div>
    </div>
    <?php



endif;