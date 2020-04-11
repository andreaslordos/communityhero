<?php

/*
  Field Name: Profile video iframe
 */
?>
<?php

$url = get_post_meta(get_post_id(), 'video', true);
if ($url) {
    print wp_oembed_get($url);
}
?>