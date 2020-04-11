<?php
wp_enqueue_script('select2');
wp_enqueue_style('select2', str_replace(array('http:', 'https:'), '', WC()->plugin_url()) . '/assets/' . 'css/select2.css');
?>

<form method="get" class="product-searchform" action="<?php print esc_url(home_url('/')); ?>">
    <div class="searchform-wrapper">
        <div class="product-cat-wrapper">
            <select name="product_cat">
                <option value=""><?php print esc_html__('Select your category', 'foodpicky') ?></option>
                <?php
                $categories = get_terms('product_cat', array('hide_empty' => false));
                if (!empty($categories)) {
                    foreach ($categories as $key => $category) {
                        azexo_display_select_tree($category, isset($_GET['product_cat']) ? $_GET['product_cat'] : '');
                    }
                }
                ?>
            </select>
        </div>
        <div class="s-wrapper">
            <input type="text" name="s" value="<?php print get_search_query(); ?>" placeholder="<?php print esc_attr__('Enter keywords here', 'foodpicky'); ?>" />
        </div>
        <div class="submit">
            <input type="submit" value="<?php print esc_attr__('Search products', 'foodpicky'); ?>">
        </div>
        <input type="hidden" name="post_type" value="product" />
    </div>
</form>