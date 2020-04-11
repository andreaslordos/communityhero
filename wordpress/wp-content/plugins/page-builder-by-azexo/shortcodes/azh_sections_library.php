<?php
$library = azh_get_library();
$all_settings = azh_get_all_settings();
extract($library);
$project = isset($atts['project']) ? $atts['project'] : 'general';
$all_tags = array();
$files_tags = array();
foreach ($all_settings as $dir => $settings) {
    $parts = explode('/', $dir);
    if (in_array($project, $parts)) {
        if (isset($settings['tags']) && is_array($settings['tags'])) {
            foreach ($settings['tags'] as $file => $tags) {
                if (strpos($file, '.html') !== false) {
                    $tags = explode(',', $tags);
                    $tags = array_map('strtolower', $tags);
                    $tags = array_map('trim', $tags);
                    $files_tags[$dir . '/' . $file] = $tags;
                    $all_tags = array_merge($all_tags, $tags);
                    $all_tags = array_unique($all_tags);
                }
            }
        }
    }
}
sort($all_tags);
$gutter = isset($atts['gutter']) ? $atts['gutter'] : '5';
$class = isset($atts['class']) ? $atts['class'] : '';
$columns = isset($atts['columns']) ? $atts['columns'] : '5';
?>
<div class="az-isotope <?php print $class ?>">
    <div class="az-isotope-filters" data-cloneable="">
        <div data-filter="*" class="az-active"><?php esc_html_e('All', 'azh') ?></div>
        <?php
        foreach ($all_tags as $tag) {
            ?>
            <div data-filter=".<?php print esc_attr($tag) ?>"><span><?php print esc_html($tag) ?></span></div>
            <?php
        }
        ?>
    </div>
    <div class="az-isotope-items" data-cloneable="" data-columns="<?php print $columns ?>" data-columns-md="<?php print $columns ?>" data-columns-sm="<?php print $columns ?>" data-columns-xs="1" data-gutter="<?php print $gutter ?>">
        <?php
        foreach ($sections as $path => $name) {
            $dir = $sections_dir[$path];
            $parts = explode('/', str_replace($dir, '', $path));
            $tags = isset($files_tags[$path]) ? implode(' ', $files_tags[$path]) : '';
            if (in_array($project, $parts)) {
                $preview = '';
                if (file_exists(str_replace('.html', '.jpg', $path))) {
                    $preview = str_replace('.html', '.jpg', $path);
                }
                if (file_exists(str_replace('.html', '.png', $path))) {
                    $preview = str_replace('.html', '.png', $path);
                }
                if (file_exists($preview)) {
                    $preview = str_replace($dir, $sections_uri[$path], $preview);
                    ?><a href="http://azexo.com/?azh=library&files=<?php print ltrim(str_replace($dir, '', $path), '/'); ?>"  data-columns="1" data-columns-md="1" data-columns-sm="1" data-columns-xs="1" target="_blank" class="az-item <?php print $tags; ?>"><div><img src="<?php print esc_attr($preview); ?>" alt=""></div></a><?php
                }
            }
        }
        ?>        
    </div>
</div>
