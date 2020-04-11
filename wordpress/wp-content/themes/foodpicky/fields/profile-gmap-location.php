<?php
/*
  Field Name: Profile GMAP Location
 */
?>

<?php
if(function_exists('azl_google_map_scripts')) {
    azl_google_map_scripts();
}
global $post;
$latitude = trim(get_post_meta($post->ID, 'latitude', true));
$longitude = trim(get_post_meta($post->ID, 'longitude', true));

if (!empty($latitude) && !empty($longitude)):
    
    ?>
    <div class="azl-map-wrapper single" 
         data-latitude="<?php print esc_attr($latitude) ?>" 
         data-longitude="<?php print esc_attr($longitude) ?>"
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