<div <?php post_class(array('entry', 'no-results', 'not-found')); ?>>
    <div class="entry-data">
        <div class="entry-content">
            <?php if (is_home() && current_user_can('publish_posts')) : ?>

                <p><?php printf(wp_kses(__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'foodpicky'), array('a' => array('href' => array()))), esc_url(admin_url('post-new.php'))); ?></p>

            <?php elseif (is_search()) : ?>

                <h2><?php esc_html_e('Sorry, but nothing matched your search terms.', 'foodpicky'); ?></h2>
                <p><?php esc_html_e('Please try again with some different keywords.', 'foodpicky'); ?></p>

            <?php else : ?>

                <h2><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'foodpicky'); ?></h2>
                <p><?php esc_html_e('Perhaps searching can help.', 'foodpicky'); ?></p>

            <?php endif; ?>
        </div><!-- .entry-content -->
    </div>    
</div><!-- #post -->
