<div class="dashboard-buttons">
    <?php
    global $azexo_dashboard_links;
    if (!is_active_widget(false, false, 'azexo_dashboard_links') && !isset($azexo_dashboard_links)) {
        $links = azexo_get_dashboard_links();
        foreach ($links as $link) {
            ?><a href="<?php print esc_url($link['url']); ?>" class="button <?php print ($link['active'] ? 'active' : ''); ?>"><?php print esc_html($link['title']); ?></a> <?php
        }
    }
    ?>
</div>
