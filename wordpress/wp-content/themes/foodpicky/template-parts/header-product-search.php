<div class="search-wrapper">
    <form method="get" class="searchform" action="<?php print esc_url(home_url('/')); ?>">
        <span class="toggle"></span>
        <div class="searchform-wrapper">        
            <label class="screen-reader-text"><?php print esc_html__('Search for:', 'foodpicky'); ?></label>
            <input type="text" value="<?php print get_search_query(); ?>" name="s" />
            <input type="hidden" value="product" name="post_type" />
            <div class="submit"><input type="submit" value="<?php print esc_attr__('Search', 'foodpicky'); ?>"></div>
        </div>
    </form>
    <i class="fa fa-search"></i>
</div>
