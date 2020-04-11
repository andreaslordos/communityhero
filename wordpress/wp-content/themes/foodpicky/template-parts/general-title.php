<?php
$options = get_option(AZEXO_FRAMEWORK);
global $azexo_queried_object;
$post_type = get_query_var('post_type');
if (is_array($post_type)) {
    $post_type = reset($post_type);
}
?>

<?php if (is_404()) : ?>
    <div class="page-header">
        <h1 class="page-title"><?php esc_html_e('Not Found', 'foodpicky'); ?></h1>
    </div>
<?php elseif (is_category()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php print single_cat_title('', false); ?></h1>
        <div class="archive-subtitle"><?php print esc_html__('Category archives', 'foodpicky'); ?></div>
        <?php if (category_description()) : // Show an optional category description ?>
            <div class="archive-meta"><?php print category_description(); ?></div>
        <?php endif; ?>
    </div><!-- .archive-header -->
<?php elseif (is_tag()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php print single_tag_title('', false); ?></h1>
        <div class="archive-subtitle"><?php print esc_html__('Tag archives', 'foodpicky'); ?></div>
        <?php if (tag_description()) : // Show an optional tag description  ?>
            <div class="archive-meta"><?php print tag_description(); ?></div>
        <?php endif; ?>
    </div><!-- .archive-header -->
<?php elseif (is_day() || is_month() || is_year()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php
            if (is_day()) :
                print get_the_date();
            elseif (is_month()) :
                print get_the_date(_x('F Y', 'monthly archives date format', 'foodpicky'));
            elseif (is_year()) :
                print get_the_date(_x('Y', 'yearly archives date format', 'foodpicky'));
            endif;
            ?></h1>
        <div class="archive-subtitle"><?php
            if (is_day()) :
                esc_html_e('Daily Archives', 'foodpicky');
            elseif (is_month()) :
                esc_html_e('Monthly Archives', 'foodpicky');
            elseif (is_year()) :
                esc_html_e('Yearly Archives', 'foodpicky');
            endif;
            ?></div>
    </div><!-- .archive-header -->
<?php elseif (is_author()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php
            the_author();
            ?></h1>
        <div class="archive-meta"><?php the_author_meta('description'); ?></div>
    </div><!-- .archive-header -->
<?php elseif (function_exists('is_shop') && is_shop()): ?>    
    <div class="archive-header">
        <h1 class="archive-title"><?php
            if (isset($options['shop_title'])) {
                print esc_html($options['shop_title']);
            } else {
                post_type_archive_title();
            }
            ?></h1>
    </div><!-- .archive-header -->        
<?php elseif (is_archive() && is_tax(get_object_taxonomies($post_type))): $queried_object = get_queried_object(); ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php
            if (isset($queried_object->term_id)) {
                print esc_html($queried_object->name);
            }
            ?></h1>
        <div class="archive-subtitle"><?php
            if (isset($queried_object->taxonomy)) {
                $taxonomy = get_taxonomy($queried_object->taxonomy);
                print esc_html(get_taxonomy_labels($taxonomy)->singular_name);
            }
            ?></div>
        <?php if (isset($queried_object->taxonomy) && term_description($queried_object->term_id, $queried_object->taxonomy)) : // Show an optional term description  ?>
            <div class="archive-meta"><?php print esc_html(term_description($queried_object->term_id, $queried_object->taxonomy)); ?></div>
        <?php endif; ?>
    </div><!-- .archive-header -->    
<?php elseif (is_archive()): ?>
    <div class="archive-header">
        <h1 class="archive-title"><?php
            post_type_archive_title();
            ?></h1>
    </div><!-- .archive-header -->    
<?php elseif (is_search()): ?>
    <div class="page-header">
        <h1 class="page-title"><?php print esc_html__('Search Results for', 'foodpicky'); ?></h1>
        <div class="page-subtitle"><?php print get_search_query(); ?></div>
    </div>
<?php elseif (isset($options['post_page_title']) && !empty($options['post_page_title']) && is_single()) : ?>
    <div class="page-header"><?php print azexo_entry_field($options['post_page_title']); ?></div>
<?php elseif (is_singular() || is_page() || isset($azexo_queried_object)): ?>
    <div class="page-header">
        <h1 class="page-title"><?php
            $current_post = azexo_get_earliest_current_post(array('vc_widget', 'azh_widget'), false);
            if (!$current_post) {
                $current_post = $azexo_queried_object;
            }
            if ($current_post) {
                print esc_html(apply_filters('azexo_page_title', get_the_title($current_post)));
            }
            ?></h1>
        <?php if (isset($options['show_breadcrumbs']) && $options['show_breadcrumbs']): ?>
            <div class="page-subtitle"><?php azexo_breadcrumbs(); ?></div>        
        <?php endif; ?>
    </div>
<?php elseif (!empty($options['default_title'])): ?>
    <div class="page-header">
        <h1 class="page-title"><?php
            print apply_filters('azexo_page_title', esc_html($options['default_title']));
            ?></h1>
    </div>
<?php endif; ?>
