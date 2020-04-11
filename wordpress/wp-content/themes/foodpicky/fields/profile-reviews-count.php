<?php
/*
  Field Name: Profile reviews count
 */
?>
<?php
global $post;
$comment_count = get_comment_count(get_the_ID());
?>

<span class="reviews">
    <a href="<?php print esc_url(get_comments_link()); ?>"><span class="count"><?php print esc_html($comment_count['total_comments']); ?></span><span class="label"><?php esc_attr_e('Reviews', 'foodpicky'); ?></span></a>
</span>

