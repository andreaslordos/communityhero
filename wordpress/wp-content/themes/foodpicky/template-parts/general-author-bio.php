<div class="author-bio-wrapper">
    <div class="header">
        <h3><?php esc_html_e('About the Author', 'foodpicky') ?></h3>
    </div>                        
    <div class="author-bio">
        <?php $options = get_option(AZEXO_FRAMEWORK); echo get_avatar(get_the_author_meta('email'), isset($options['author_avatar_size']) ? $options['author_avatar_size'] : 96); ?>
        <div class="author-info">
            <?php $user_url = get_the_author_meta('user_url'); if (empty($user_url)): ?>
                <span class="author-title"><?php the_author_link(); ?></span>
            <?php else: ?>
                <a class="author-title" href="<?php esc_url(the_author_meta('user_url')); ?>"><?php the_author_link(); ?></a>
            <?php endif; ?>
            <p class="author-description"><?php the_author_meta('description'); ?></p>
            <ul class="icons">
                <?php
                $rss_url = get_the_author_meta('rss_url');
                if ($rss_url && $rss_url != '') {
                    echo '<li class="rss"><a href="' . esc_url($rss_url) . '"></a></li>';
                }

                $google_profile = get_the_author_meta('google_profile');
                if ($google_profile && $google_profile != '') {
                    echo '<li class="google"><a href="' . esc_url($google_profile) . '" rel="author"></a></li>';
                }

                $twitter_profile = get_the_author_meta('twitter_profile');
                if ($twitter_profile && $twitter_profile != '') {
                    echo '<li class="twitter"><a href="' . esc_url($twitter_profile) . '"></a></li>';
                }

                $facebook_profile = get_the_author_meta('facebook_profile');
                if ($facebook_profile && $facebook_profile != '') {
                    echo '<li class="facebook"><a href="' . esc_url($facebook_profile) . '"></a></li>';
                }

                $linkedin_profile = get_the_author_meta('linkedin_profile');
                if ($linkedin_profile && $linkedin_profile != '') {
                    echo '<li class="linkedin"><a href="' . esc_url($linkedin_profile) . '"></a></li>';
                }
                ?>
            </ul>
        </div>
    </div>            
</div>          
