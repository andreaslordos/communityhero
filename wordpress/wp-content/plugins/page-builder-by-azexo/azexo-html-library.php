<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <title><?php esc_html_e('AZEXO HTML Library', 'azh'); ?></title>
        <meta charset="<?php bloginfo('charset'); ?>">
        <?php
        global $azh_content;
        $azh_content = false;

        if (isset($_GET['files'])) {
            $azh_content .= azh_get_page_template($_GET['files']);
        } else {
            azh_icon_font_enqueue('fontawesome');
        }
        wp_enqueue_style('azexo_html_library', plugins_url('css/azexo_html_library.css', __FILE__));            
        wp_enqueue_script('azexo_html_library', plugins_url('js/azexo_html_library.js', __FILE__), array('jquery'), true);
        wp_localize_script('azexo_html_library', 'azh', azh_get_object());
        wp_head();
        ?>
    </head>    
    <body class="azexo-html-library azen-background-inverted">          
        <div class="az-container">
            <?php if ($azh_content): ?>
                <div class="azh-content <?php (isset($_GET['files']) && count(explode('|', $_GET['files'])) == 1) ? 'azh-center' : '' ?>">
                    <?php print $azh_content; ?>
                </div>
                <div class="azh-variations">                    
                    <div class="azh-variation">
                        <input id="inverted-styles" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                        <div class="azh-checkbox"><label for="inverted-styles"></label></div>
                        <span><?php esc_html_e('Inverted styles', 'azh'); ?></span>
                    </div>
                    <div class="azh-variation">
                        <input id="alternative-styles" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                        <div class="azh-checkbox"><label for="alternative-styles"></label></div>
                        <span><?php esc_html_e('Alternative styles', 'azh'); ?></span>
                    </div>
                    
                    <div class="azh-variation">
                        <input id="shadow-border" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                        <div class="azh-checkbox"><label for="shadow-border"></label></div>
                        <span><?php esc_html_e('Shadow / border', 'azh'); ?></span>
                    </div>
                </div>
            <?php else: ?>                
                <div id="azexo-html-library">
                    <input id="sections" type="radio" name="sections-elements" checked="" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <input id="elements" type="radio" name="sections-elements" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <div class="sections-elements">                        
                        <label for="sections"><?php esc_html_e('Sections', 'azh'); ?></label>
                        <label for="elements"><?php esc_html_e('Elements', 'azh'); ?></label>
                    </div>
                    <?php azh_meta_box(); ?>
                </div>
            <?php endif; ?>        
        </div>        
        <?php
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('imagesloaded');
        wp_enqueue_script('isotope');
        wp_enqueue_script('waypoints');
        wp_footer();
        ?>
    </body>
</html>