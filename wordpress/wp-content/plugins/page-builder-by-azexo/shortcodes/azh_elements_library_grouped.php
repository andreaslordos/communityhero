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
                if (strpos($file, '.html') === false) {
                    $path = $dir . '/' . $file;
                    $preview = '';
                    if (file_exists(str_replace('.htm', '.jpg', $path))) {
                        $preview = str_replace('.htm', '.jpg', $path);
                    }
                    if (file_exists(str_replace('.htm', '.png', $path))) {
                        $preview = str_replace('.htm', '.png', $path);
                    }
                    if (file_exists($preview)) {
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
}
sort($all_tags);
$gutter = isset($atts['gutter']) ? $atts['gutter'] : '0';
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
    <div class="az-isotope-items" data-cloneable="" data-columns="<?php print $columns ?>" data-columns-md="<?php print ($columns / 2) ?>" data-columns-sm="<?php print ($columns / 2) ?>" data-columns-xs="1" data-gutter="<?php print $gutter ?>">
        <?php
        $groups = array();
        foreach ($elements as $path => $name) {
            $tags = isset($files_tags[$path]) ? $files_tags[$path] : array();
            $tag = reset($tags);
            $dir = $elements_dir[$path];
            $parts = explode('/', str_replace($dir, '', $path));
            if (in_array($project, $parts)) {
                if (!isset($groups[$tag])) {
                    $groups[$tag] = array();
                }
                $groups[$tag][] = $path;
            }
        }
        $sorted_groups = array();
        $big = array();
        foreach ($groups as $tag => $paths) {
            if (count($paths) > $columns) {
                $big[$tag] = $paths;
            } else {
                $sorted_groups[$tag] = $paths;
            }
        }
        $sorted_groups = array_merge($sorted_groups, $big);

        foreach ($sorted_groups as $tag => $paths) {
            $count = 0;
            foreach ($paths as $path) {
                $dir = $elements_dir[$path];
                $parts = explode('/', str_replace($dir, '', $path));
                if (in_array($project, $parts)) {
                    $preview = '';
                    if (file_exists(str_replace('.htm', '.jpg', $path))) {
                        $preview = str_replace('.htm', '.jpg', $path);
                    }
                    if (file_exists(str_replace('.htm', '.png', $path))) {
                        $preview = str_replace('.htm', '.png', $path);
                    }
                    if (file_exists($preview)) {
                        $count++;
                    }
                }
            }
            if ($count > 0) {
                $c = $count;
                if ($count > $columns) {
                    $c = $columns;
                }
                print '<div class="az-item ' . $tag . '" data-columns="' . $c . '" data-columns-md="' . $c . '" data-columns-sm="' . $c . '" data-columns-xs="' . $c . '" data-count="' . count($paths) . '"><div>' . $tag . '</div><div>';
                foreach ($paths as $path) {
                    $dir = $elements_dir[$path];
                    $parts = explode('/', str_replace($dir, '', $path));
                    $tags = isset($files_tags[$path]) ? implode(' ', $files_tags[$path]) : '';
                    if (in_array($project, $parts)) {
                        $preview = '';
                        if (file_exists(str_replace('.htm', '.jpg', $path))) {
                            $preview = str_replace('.htm', '.jpg', $path);
                        }
                        if (file_exists(str_replace('.htm', '.png', $path))) {
                            $preview = str_replace('.htm', '.png', $path);
                        }
                        if (file_exists($preview)) {
                            $preview = str_replace($dir, $elements_uri[$path], $preview);
                            ?><a href="http://azexo.com/?azh=library&files=<?php print ltrim(str_replace($dir, '', $path), '/'); ?>"  target="_blank"  title="<?php print end($parts); ?>"><div><img src="<?php print esc_attr($preview); ?>"></div></a><?php
                        }
                    }
                }
                print '</div></div>';
            }
        }
        ?>        
    </div>
</div>