<?php
$library = azh_get_library();
$all_settings = azh_get_all_settings();
extract($library);
$project = isset($atts['project']) ? $atts['project'] : 'general';
$gutter = isset($atts['gutter']) ? $atts['gutter'] : '5';
$class = isset($atts['class']) ? $atts['class'] : '';
$columns = isset($atts['columns']) ? $atts['columns'] : '10';

if (!function_exists('azh_elements_library_item')) {

    function azh_elements_library_item($content, $id, $tags, $element_columns) {
        ?>
        <div class="az-item <?php print $tags ?>" data-columns="<?php print $element_columns ?>" data-columns-md="<?php print $element_columns ?>" data-columns-sm="<?php print $element_columns ?>" data-columns-xs="1">
            <div class="azh-variations">                    
                <div class="azh-variation">
                    <input class="azh-inverted-styles" id="inverted-styles-<?php print $id ?>" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <div class="azh-checkbox"><label for="inverted-styles-<?php print $id ?>"></label></div>
                    <span><?php esc_html_e('Inverted styles', 'azh'); ?></span>
                </div>
                <div class="azh-variation">
                    <input class="azh-alternative-styles" id="alternative-styles-<?php print $id ?>" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <div class="azh-checkbox"><label for="alternative-styles-<?php print $id ?>"></label></div>
                    <span><?php esc_html_e('Alternative styles', 'azh'); ?></span>
                </div>
                <div class="azh-variation">
                    <input class="azh-shadow-border" id="shadow-border-<?php print $id ?>" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);">
                    <div class="azh-checkbox"><label for="shadow-border-<?php print $id ?>"></label></div>
                    <span><?php esc_html_e('Shadow / border', 'azh'); ?></span>
                </div>
            </div>
            <noscript>
            <div data-inverted-styles="false">
                <?php
                print $content;
                ?>
            </div>
            </noscript>
        </div>
        <?php
    }

}
if (!function_exists('azh_get_element_file')) {

    function azh_get_element_file($file, $azh_uri, $frontend = true) {
        if (file_exists($file)) {
            azh_filesystem();
            global $wp_filesystem;
            $content = $wp_filesystem->get_contents($file);
            $content = azh_replaces($content, $azh_uri);
//            $content = azh_generate_ids($content);
            $content = azh_remove_comments($content);
            $content = str_replace(array("\t", "\r", "\n"), '', $content);
            $content = preg_replace('/> +</', '><', $content);
            if ($frontend) {
                azh_enqueue_icons($content);
                $content = do_shortcode($content);
            }
            return $content;
        } else {
            return '';
        }
    }

}

if (!function_exists('azh_get_element_template')) {

    function azh_get_element_template($file) {
        $content = '';
        $library = azh_get_library();
        if (is_array($library['elements'])) {
            foreach ($library['elements'] as $element_file => $name) {
                if (strlen($element_file) - strlen($file) == strrpos($element_file, $file)) {
                    $content .= '<div data-element="' . esc_attr(ltrim(str_replace($library['elements_dir'][$element_file], '', $element_file), '/')) . '" style="margin: auto; max-width: ' . azh_get_element_width($element_file) . '">';
                    $content .= azh_get_element_file($element_file, $library['elements_uri'][$element_file]);
                    $content .= '</div>';
                    break;
                }
            }
        }
        return $content;
    }

}

if (isset($atts['tags']) && $atts['tags']) {
    $all_tags = array();
    $files_tags = array();
    foreach ($all_settings as $dir => $settings) {
        $parts = explode('/', $dir);
        if (in_array($project, $parts)) {
            if (isset($settings['tags']) && is_array($settings['tags'])) {
                foreach ($settings['tags'] as $file => $tags) {
                    if (strpos($file, '.html') === false) {
                        $path = $dir . '/' . $file;
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
    ?>
    <div class="az-isotope <?php print $class ?>">
        <div class="az-isotope-filters" data-cloneable="">
            <div data-filter=":not(div)" class="az-active"><?php esc_html_e('All', 'azh') ?></div>
            <?php
            foreach ($all_tags as $tag) {
                ?>
                <div data-filter=".<?php print esc_attr($tag) ?>"><span><?php print esc_html($tag) ?></span></div>
                <?php
            }
            ?>
        </div>
        <div class="az-isotope-items"  data-cloneable="" data-columns="<?php print $columns ?>" data-columns-md="<?php print $columns ?>" data-columns-sm="<?php print $columns ?>" data-columns-xs="1" data-gutter="<?php print $gutter ?>">
            <?php
            foreach ($elements as $path => $name) {
                $dir = $elements_dir[$path];
                $parts = explode('/', str_replace($dir, '', $path));
                $tags = isset($files_tags[$path]) ? implode(' ', $files_tags[$path]) : '';
                if (in_array($project, $parts)) {
                    $element_width = azh_get_element_width($path);
                    $element_columns = (round(($element_width - 1) / 1000) * $columns + $columns) / 2;
                    $id = uniqid();
                    $content = azh_get_page_template(ltrim(str_replace($dir, '', $path), '/'));
                    $content = str_replace('background-type: classic;', '', $content);
                    $content = str_replace('maxlength=""', 'maxlength="100"', $content);
                    $content = str_replace('max=""', 'max="100"', $content);
                    azh_elements_library_item($content, $id, $tags, $element_columns);
                }
            }
            ?>        
        </div>
    </div>
    <?php
} else {
    $filters = array();
    foreach ($elements as $path => $name) {
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
            if (file_exists(str_replace('.htm', '.svg', $path))) {
                $preview = str_replace('.htm', '.svg', $path);
            }
            if (file_exists($preview)) {
                $preview = str_replace($dir, $elements_uri[$path], $preview);
                $filters[$path] = $preview;
            }
        }
    }
    $files = array();
    ob_start();
    foreach ($elements as $path => $name) {
        $dir = $elements_dir[$path];
        $parts = explode('/', str_replace($dir, '', $path));
        $file = end($parts);
        $file = explode('.', $file);
        $file = reset($file);
        if (in_array($project, $parts)) {
            $element_width = azh_get_element_width($path);
            $element_columns = (round(($element_width - 1) / 1000) * $columns + $columns) / 2;
            $content = azh_get_element_template(ltrim(str_replace($dir, '', $path), '/'));
            $content = str_replace('background-type: classic;', '', $content);
            $content = str_replace('maxlength=""', 'maxlength="100"', $content);
            $content = str_replace('max=""', 'max="100"', $content);
            if (preg_match_all('#data-variants="([\w\s\d-\|_]+)"+\s(data-variant="[\w\s\d-_]+")#i', $content, $matches)) {
                for ($i = 0; $i < count($matches[0]); $i++) {
                    $variants = explode('|', $matches[1][$i]);
                    foreach ($variants as $variant) {
                        $s = $matches[0][$i];
                        $s = str_replace($matches[2][$i], 'data-variant="' . $variant . '"', $s);
                        $variant_content = str_replace($matches[0][$i], $s, $content);
                        $variant_content = azh_generate_ids($variant_content);
                        azh_elements_library_item($variant_content, uniqid(), $file, $element_columns);
                    }
                    $files[$file] = count($variants);
                    break;
                }
            } else {
                azh_elements_library_item($content, uniqid(), $file, $element_columns);
                $files[$file] = 1;
            }
        }
    }
    $items = ob_get_clean();
    ?>
    <div class="az-isotope <?php print $class ?>">
        <div class="az-isotope-filters" data-cloneable="">
            <div data-filter=":not(div)" class="az-active"><?php esc_html_e('All', 'azh') ?></div>
            <?php
            foreach ($filters as $path => $preview) {
                $dir = $elements_dir[$path];
                $parts = explode('/', str_replace($dir, '', $path));
                $file = end($parts);
                $file = explode('.', $file);
                $file = reset($file);
                ?>
                <div data-filter=".<?php print esc_attr($file) ?>"><img src="<?php print esc_attr($preview) ?>"><div><?php print esc_html(ucfirst(str_replace('-', ' ', $file))) ?></div><span><?php print $files[$file]; ?></span></div>
                <?php
            }
            ?>
        </div>
        <div class="az-isotope-items"  data-cloneable="" data-columns="<?php print $columns ?>" data-columns-md="<?php print $columns ?>" data-columns-sm="<?php print $columns ?>" data-columns-xs="1" data-gutter="<?php print $gutter ?>">
            <?php
            print $items;
            ?>        
        </div>
    </div>
    <?php
}

