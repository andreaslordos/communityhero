<?php
/*
  Field Name: Profile add a review link
 */
?>
<?php
global $post;
?>

<?php if (is_single()) : ?>
    <a href="#review_form" class="add-review roll"><?php esc_attr_e('Add a review', 'foodpicky'); ?></a>
<?php else: ?>
    <a href="<?php print esc_url(get_permalink()); ?>#review_form" class="add-review"><?php esc_attr_e('Add a review', 'foodpicky'); ?></a>
<?php endif; ?>

