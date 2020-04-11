<?php

add_action('wxr_importer.processed.post', 'azh_wxr_importer_urls_remap', 10, 3);

function azh_wxr_importer_urls_remap($post_id, $data, $meta) {
    if ('attachment' === $data['post_type']) {
        $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());

        $remote_url = !empty($data['attachment_url']) ? $data['attachment_url'] : $data['guid'];
        $azh_wxr_importer_url_remap[$remote_url] = wp_get_attachment_image_url($post_id, 'full');

        update_option('azh_wxr_importer_url_remap', $azh_wxr_importer_url_remap);
    }
    if ('page' === $data['post_type']) {
        $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());
        $azh_wxr_importer_permalinks = get_option('azh_wxr_importer_permalinks', array());
        $azh_wxr_importer_pages = get_option('azh_wxr_importer_pages', array());

        if (isset($azh_wxr_importer_permalinks[$data['guid']])) {
            $remote_url = rtrim($azh_wxr_importer_permalinks[$data['guid']], "/");
            $azh_wxr_importer_url_remap[$remote_url] = rtrim(get_permalink($post_id), "/");
            $azh_wxr_importer_pages[$post_id] = $remote_url;
        }

        update_option('azh_wxr_importer_url_remap', $azh_wxr_importer_url_remap);
        update_option('azh_wxr_importer_pages', $azh_wxr_importer_pages);
    }
    if (is_array($meta)) {
        foreach ($meta as $meta_item) {
            if ($meta_item['key'] === 'azh' && $meta_item['value'] === 'azh') {
                azh_extract_templates($data['post_content'], $post_id);
            }
        }
    }
}

add_filter('wxr_importer.pre_process.post_meta', 'azh_wxr_importer_replace_urls', 10, 2);

function azh_wxr_importer_replace_urls($meta_item, $post_id) {
    if (is_array($meta_item)) {
        if ($meta_item['key'] === '_azh_content') {
            $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());
            $meta_item['value'] = str_replace(array_keys($azh_wxr_importer_url_remap), $azh_wxr_importer_url_remap, $meta_item['value']);
            $meta_item['value'] = azh_uri_replace($meta_item['value']);
        }
    }
    return $meta_item;
}

add_action('pt-ocdi/before_content_import_execution', 'azh_ocdi_before_import', 10, 3);

function azh_ocdi_before_import($import_files, $predefined_import_files, $predefined_index) {
    $demo = $predefined_import_files[$predefined_index];
    if ($demo['permalinks']) {
        update_option('azh_wxr_importer_permalinks', $demo['permalinks']);
    }
}

add_action('pt-ocdi/after_all_import_execution', 'azh_ocdi_after_import', 10, 3);

function azh_ocdi_after_import($import_files, $predefined_import_files, $predefined_index) {
    $demo = $predefined_import_files[$predefined_index];
    if ($demo['import_file_name']) {
        $azh_wxr_importer_pages = get_option('azh_wxr_importer_pages', array());
        $azh_wxr_importer_url_remap = get_option('azh_wxr_importer_url_remap', array());
        foreach ($azh_wxr_importer_pages as $post_id => $remote_url) {
            $post = get_post($post_id);
            $content = azh_get_post_content($post);
            $content = str_replace(array_keys($azh_wxr_importer_url_remap), $azh_wxr_importer_url_remap, $content);
            azh_set_post_content($content, $post_id);
        }
    }
}

add_filter('import_post_meta_key', 'azh_import_post_meta_key', 10, 3);

function azh_import_post_meta_key($key, $post_id, $post) {
    if (in_array($key, array(
                'azh-widgets',
                'cookie-less-variables',
                'brand-color',
                'accent-1-color',
                'accent-2-color',
                'main-google-font',
                'main-border-color',
                'main-border-radius',
                'main-border-width',
                'main-shadow-color',
                'header-google-font',
                'header-color',
                'header-font-size',
                'header-line-height',
                'header-font-weight',
                'paragraph-color',
                'paragraph-font-size',
                'paragraph-line-height',
                'paragraph-font-weight',
                'paragraph-bold-weight',
                '_brand-color',
                '_accent-1-color',
                '_accent-2-color',
                '_main-google-font',
                '_main-border-color',
                '_main-border-radius',
                '_main-border-width',
                '_main-shadow-color',
                '_header-google-font',
                '_header-color',
                '_header-font-size',
                '_header-line-height',
                '_header-font-weight',
                '_paragraph-color',
                '_paragraph-font-size',
                '_paragraph-line-height',
                '_paragraph-font-weight',
                '_paragraph-bold-weight',
            ))) {
        return false;
    }
    return $key;
}
