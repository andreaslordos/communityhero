<?php

add_filter('azh_set_post_content', 'azh_templates_set_post_content', 10, 2);

function azh_templates_set_post_content($content, $post_id) {
    return azh_extract_templates($content, $post_id);
}

add_filter('wp_insert_post_data', 'azh_insert_post_data', 10, 2);

function azh_insert_post_data($data, $postarr) {
    $user = wp_get_current_user();
    if (in_array('administrator', (array) $user->roles)) {
        //only for import
        if ($postarr['import_id'] && $postarr['post_type'] == 'azh_widget') {
            //$data['post_content'] = azh_remove_comments($data['post_content']);
            $data['post_content'] = azh_extract_templates($data['post_content'], $postarr['import_id']);
            update_post_meta($postarr['import_id'], 'azh', 'azh');
        }
    }
    return $data;
}

function azh_is_empty($var) {
    $var = trim($var);
    return empty($var);
}

function azh_extract_text_template($html, &$templates, &$shortcodes, $selector, $template_name, $shortcode_name) {
    foreach ($html->find($selector) as $wrapper) {
        if (!isset($templates[$template_name])) {
            $templates[$template_name] = array();
        }
        $md5 = md5($wrapper->outertext);
        if (!isset($templates[$template_name][$md5])) {
            $wrapper->{'data-shortcode'} = $md5;
            foreach ($wrapper->find('text') as $text) {
                if (!azh_is_empty($text->innertext)) {
                    $text->innertext = '{{text}}';
                    break;
                }
            }
            $templates[$template_name][$md5] = $wrapper->outertext;
        }
        $wrapper->outertext = '[' . $shortcode_name . ' source="' . $md5 . '"]';
        $shortcodes[$md5] = $wrapper->outertext;
    }
}

function azh_replace_text_field($wrapper) {
    if (count($wrapper->find('.az-text')) > 0) {
        foreach ($wrapper->find('.az-text') as $text) {
            $text->innertext = '{{text}}';
        }
    } else {
        foreach ($wrapper->find('text') as $text) {
            if (!azh_is_empty($text->innertext)) {
                $text->innertext = '{{text}}';
                break;
            }
        }
    }
}

function azh_replace_link_field($wrapper) {
    if (isset($wrapper->href)) {
        $wrapper->href = '{{link_url}}';
    } elseif (count($wrapper->find('.az-link')) > 0) {
        foreach ($wrapper->find('.az-link') as $link) {
            $link->href = '{{link_url}}';
        }
    } else {
        if (count($wrapper->find('a[href]')) > 0) {
            $wrapper->find('a[href]', 0)->href = '{{link_url}}';
        }
    }
}

function azh_replace_iframe_field($wrapper) {
    if (isset($wrapper->src)) {
        $wrapper->src = '{{iframe_url}}';
    } elseif (count($wrapper->find('.az-iframe')) > 0) {
        foreach ($wrapper->find('.az-iframe') as $iframe) {
            $iframe->src = '{{iframe_url}}';
        }
    } else {
        if (count($wrapper->find('iframe[src]')) > 0) {
            $wrapper->find('iframe[src]', 0)->src = '{{iframe_url}}';
        }
    }
}

function azh_replace_image_field($wrapper) {
    if (isset($wrapper->src)) {
        $wrapper->src = '{{image_url}}';
    } elseif (count($wrapper->find('.az-image')) > 0) {
        foreach ($wrapper->find('.az-image') as $image) {
            if (isset($image->src)) {
                $image->src = '{{image_url}}';
            } elseif (isset($image->style)) {
                $image->style = preg_replace_callback('/background-image\:[^;]*url\([\'\"]?([^\'\"\)]+)[\'\"]?\)/i', function($m) {
                    return "background-image: url('{{image_url}}')";
                }, $image->style);
            }
        }
    } elseif (isset($wrapper->style) && (strpos($wrapper->style, 'background-image') !== false)) {
        $wrapper->style = preg_replace_callback('/background-image\:[^;]*url\([\'\"]?([^\'\"\)]+)[\'\"]?\)/i', function($m) {
            return "background-image: url('{{image_url}}')";
        }, $wrapper->style);
    } elseif (count($wrapper->find('img[src]')) > 0) {
        $wrapper->find('img[src]', 0)->src = '{{image_url}}';
    } elseif (count($wrapper->find('[style*="background-image"]')) > 0) {
        $image = $wrapper->find('[style*="background-image"]', 0);
        $image->style = preg_replace_callback('/background-image\:[^;]*url\([\'\"]?([^\'\"\)]+)[\'\"]?\)/i', function($m) {
            return "background-image: url('{{image_url}}')";
        }, $image->style);
    }
}

function azh_extract_boolean_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_class_field_template($element, &$fields_templates, $class_prefix, $field_name) {
    foreach ($element->find('[class*="' . $class_prefix . '"]') as $e) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        $classes = explode(' ', $e->class);
        foreach ($classes as &$class) {
            if (strpos($class, $class_prefix) == 0) {
                $fields_templates[$field_name] = str_replace($class_prefix, '', $class);
                $class = '{{' . $field_name . '}}';
                break;
            }
        }
        $e->class = implode(' ', $classes);
    }
}

function azh_extract_text_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_text_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_link_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_link_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_image_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_image_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_link_text_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_link_field($wrapper);
        azh_replace_text_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_link_image_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_link_field($wrapper);
        azh_replace_image_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_link_image_text_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_link_field($wrapper);
        azh_replace_image_field($wrapper);
        azh_replace_text_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_iframe_link_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = '';
        }
        azh_replace_iframe_field($wrapper);
        azh_replace_link_field($wrapper);
        $fields_templates[$field_name] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_images_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = array('root' => '', 'item' => '');
        }
        if (isset($wrapper->{'data-cloneable'}) || isset($wrapper->{'data-cloneable-inline'})) {
            $items = $wrapper;
        } else {
            $items = $wrapper->find('[data-cloneable], [data-cloneable-inline]', 0);
        }
        foreach ($items->children() as $item) {
            azh_replace_image_field($item);
            $fields_templates[$field_name]['item'] = $item->outertext;
            break;
        }
        $items->innertext = '{{items}}';
        $fields_templates[$field_name]['root'] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_links_texts_field_template($element, &$fields_templates, $selector, $field_name) {
    foreach ($element->find($selector) as $wrapper) {
        if (!isset($fields_templates[$field_name])) {
            $fields_templates[$field_name] = array('root' => '', 'item' => '');
        }
        if (isset($wrapper->{'data-cloneable'}) || isset($wrapper->{'data-cloneable-inline'})) {
            $items = $wrapper;
        } else {
            $items = $wrapper->find('[data-cloneable], [data-cloneable-inline]', 0);
        }
        foreach ($items->children() as $item) {
            azh_replace_link_field($item);
            azh_replace_text_field($item);
            $fields_templates[$field_name]['item'] = $item->outertext;
            break;
        }
        $items->innertext = '{{items}}';
        $fields_templates[$field_name]['root'] = $wrapper->outertext;
        $wrapper->outertext = '{{' . $field_name . '}}';
    }
}

function azh_extract_entry_fields_templates($entry, &$fields_templates) {
    azh_extract_class_field_template($entry, $fields_templates, 'az-sticky-', 'sticky-class');
    azh_extract_class_field_template($entry, $fields_templates, 'az-liked-', 'liked-class');

    azh_extract_link_text_field_template($entry, $fields_templates, '.az-title', 'title');
    azh_extract_text_field_template($entry, $fields_templates, '.az-excerpt', 'excerpt');
    azh_extract_text_field_template($entry, $fields_templates, '.az-content', 'content');
    azh_extract_text_field_template($entry, $fields_templates, '.az-day', 'day');
    azh_extract_text_field_template($entry, $fields_templates, '.az-month', 'month');
    azh_extract_text_field_template($entry, $fields_templates, '.az-year', 'year');
    azh_extract_text_field_template($entry, $fields_templates, '.az-date', 'date');
    azh_extract_link_text_field_template($entry, $fields_templates, '.az-comments-count', 'comments-count');

    azh_extract_link_field_template($entry, $fields_templates, '.az-read-more', 'read-more');
    azh_extract_link_field_template($entry, $fields_templates, '.az-permalink', 'permalink');

    azh_extract_iframe_link_field_template($entry, $fields_templates, '.az-video', 'video');
    azh_extract_link_image_field_template($entry, $fields_templates, '.az-thumbnail', 'thumbnail');
    azh_extract_images_field_template($entry, $fields_templates, '.az-gallery', 'gallery');

    azh_extract_link_image_text_field_template($entry, $fields_templates, '.az-author', 'author');

    azh_extract_links_texts_field_template($entry, $fields_templates, '.az-category', 'category');
    azh_extract_links_texts_field_template($entry, $fields_templates, '.az-tags', 'tags');
    azh_extract_links_texts_field_template($entry, $fields_templates, '[data-taxonomy-field]', 'taxonomy-field');

    azh_extract_text_field_template($entry, $fields_templates, '[data-meta-field]', 'meta-field');

    azh_extract_boolean_field_template($entry, $fields_templates, '.az-sticky', 'sticky');

    azh_extract_text_field_template($entry, $fields_templates, '.az-likes-count', 'likes-count');
}

function azh_array_remove_comments_recursive($input) {
    if (is_array($input)) {
        foreach ($input as &$value) {
            $value = azh_array_remove_comments_recursive($value);
        }
        return $input;
    } else {
        return azh_remove_comments($input);
    }
}

function azh_get_parents($element) {
    $parents = array();
    if ($element->parent()) {
        $parents = array_merge(array($element->parent()), azh_get_parents($element->parent()));
    }
    return $parents;
}

function azh_filter_elements_by_class($elements, $class) {
    $filtered_elements = array();
    foreach ((array) $elements as $element) {
        if (isset($element->class)) {
            if (in_array($class, explode(' ', $element->class))) {
                $filtered_elements[] = $element;
            }
        }
    }
    return $filtered_elements;
}

function azh_replace_ids($wrapper) {
    $id_attrs = array('href', 'data-target', 'data-id', 'for', 'id');
    $elements_with_id = array();
    $ids_number = 0;

    foreach ($id_attrs as $id_attr) {
        foreach ($wrapper->find('[' . $id_attr . ']') as $element_with_id) {
            if (!isset($elements_with_id[$element_with_id->{$id_attr}])) {
                $elements_with_id[$element_with_id->{$id_attr}] = array();
            }
            $elements_with_id[$element_with_id->{$id_attr}][] = $element_with_id;
        }
    }
    foreach ($elements_with_id as $id => $elements) {
        if (count($elements) > 1) {
            $id = '{{id-' . $ids_number . '}}';
            $ids_number++;
            foreach ($elements as $element) {
                foreach ($id_attrs as $id_attr) {
                    if (isset($element->{$id_attr})) {
                        $element->{$id_attr} = $id;
                    }
                }
            }
        }
    }
}

function azh_get_shortcode_matches($content) {
    global $shortcode_tags;
    preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches);
    $tagnames = array_intersect(array_keys($shortcode_tags), $matches[1]);
    if (!empty($tagnames)) {
        $pattern = get_shortcode_regex($tagnames);
        $matches = array();
        preg_match_all("/$pattern/", $content, $matches);
        return $matches;
    }
    return false;
}

function azh_extract_templates($content, $post_id) {
    include_once(AZH_DIR . 'simple_html_dom.php' );
    $settings = get_post_meta($post_id, '_azh_settings', true);
    if (!$settings) {
        $settings = array(
            'templates' => array(),
            'shortcodes' => array(),
        );
    }
    $templates = $settings['templates'];
    //$content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content));
    $html = str_get_html(wp_unslash($content));
    if ($html) {
        $duplicates_removed = false;
        foreach ($html->find('[data-element]') as $element) {
            //remove data-element duplicates caused by azh_wrap_shortcodes            
            if (count($element->children()) === 1) {
                $e = $element->children(0);
                if (is_string($e->{'data-element'})) {
                    $element->innertext = $e->innertext;
                    $e->outertext = '';
                    $element->children = array();
                    $duplicates_removed = true;
                }
            }
        }
//        if ($duplicates_removed) {
//            $html = str_get_html($html->save());
//        }

        foreach ($html->find('[data-element]') as $element) {
            $matches = azh_get_shortcode_matches($element->innertext);
            if ($matches) {
                if (count($matches[0]) == 1) {
                    if (trim($matches[0][0]) == trim($element->innertext)) {
                        if (!isset($settings['shortcodes'][$element->{'data-element'}])) {
                            $name = 'shortcode: ' . uniqid();
                            $settings['shortcodes'][$name] = $element->innertext;
                            $element->{'data-element'} = $name;
                        }
                    }
                }
            } else {
                if (isset($settings['shortcodes'][$element->{'data-element'}])) {
                    $element->innertext = $settings['shortcodes'][$element->{'data-element'}];
                }
            }
        }
        $shortcodes_replaced = false;
        foreach ($html->find('[data-shortcode]') as $shortcode) {
            if (isset($settings['shortcodes'][$shortcode->{'data-shortcode'}])) {
                $shortcode->outertext = $settings['shortcodes'][$shortcode->{'data-shortcode'}];
                $shortcodes_replaced = true;
            }
        }
        if ($shortcodes_replaced) {
            $html = str_get_html($html->save());
        }
        foreach ($html->find('.az-logo') as $logo) {
            if (!isset($templates['logo'])) {
                $templates['logo'] = array();
            }
            $md5 = md5($logo->outertext);
            if (!isset($templates['logo'][$md5])) {
                $logo->{'data-shortcode'} = $md5;
                azh_replace_link_field($logo);
                azh_replace_image_field($logo);
                $templates['logo'][$md5] = $logo->outertext;
            }
            $logo->outertext = '[azh_logo source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $logo->outertext;
        }
        azh_extract_text_template($html, $templates, $settings['shortcodes'], '.az-page-title', 'page-title', 'azh_page_title');
        azh_extract_text_template($html, $templates, $settings['shortcodes'], '.az-page-subtitle', 'page-subtitle', 'azh_page_subtitle');
        azh_extract_text_template($html, $templates, $settings['shortcodes'], '.az-page-meta', 'page-meta', 'azh_page_meta');
        foreach ($html->find('.az-menu') as $menu) {
            if (!isset($templates['menu'])) {
                $templates['menu'] = array();
            }
            $md5 = md5($menu->outertext);
            if (!isset($templates['menu'][$md5])) {
                $menu->{'data-shortcode'} = $md5;
                azh_replace_ids($menu);
                $menu_items = false;
                if (isset($menu->{'data-cloneable'}) || isset($menu->{'data-cloneable-inline'})) {
                    $menu_items = $menu;
                } else {
                    $menu_items = $menu->find('[data-cloneable], [data-cloneable-inline]', 0);
                }
                if ($menu_items) {
                    foreach ($menu_items->children() as $menu_item) {
                        if (count($menu_item->find('[data-cloneable], [data-cloneable-inline]')) == 0) {
                            $menu_item->find('a', 0)->href = '{{url}}';
                            foreach ($menu_item->find('a', 0)->find('text') as $text) {
                                if (!azh_is_empty($text->innertext)) {
                                    $text->innertext = '{{title}}';
                                    break;
                                }
                            }
                            $templates['menu'][$md5]['item'] = $menu_item->outertext;
                            $templates['menu'][$md5]['item'] = preg_replace_callback('/az-active-([\w\d\-\_]+)/i', function($m) use (&$templates, $md5) {
                                $templates['menu'][$md5]['active'] = $m[1];
                                return '{{active}}';
                            }, $templates['menu'][$md5]['item']);
                        } else {
                            $menu_item->find('[data-cloneable], [data-cloneable-inline]', 0)->innertext = '{{children}}';
                            $menu_item->find('a', 0)->href = '{{url}}';
                            foreach ($menu_item->find('a', 0)->find('text') as $text) {
                                if (!azh_is_empty($text->innertext)) {
                                    $text->innertext = '{{title}}';
                                    break;
                                }
                            }
                            $templates['menu'][$md5]['item_with_children'] = $menu_item->outertext;
                            $templates['menu'][$md5]['item_with_children'] = preg_replace_callback('/az-active-([\w\d\-\_]+)/i', function($m) use (&$templates, $md5) {
                                $templates['menu'][$md5]['active'] = $m[1];
                                return '{{active}}';
                            }, $templates['menu'][$md5]['item_with_children']);
                        }
                    }
                    $menu_items->innertext = '{{items}}';
                }
                $templates['menu'][$md5]['root'] = $menu->outertext;
            }
            $menu->outertext = '[azh_menu source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $menu->outertext;
        }
        foreach ($html->find('.az-taxonomy') as $taxonomy) {
            if (!isset($templates['taxonomy'])) {
                $templates['taxonomy'] = array();
            }
            $md5 = md5($taxonomy->outertext);
            if (!isset($templates['taxonomy'][$md5])) {
                $taxonomy->{'data-shortcode'} = $md5;
                $terms = false;
                if (isset($taxonomy->{'data-cloneable'}) || isset($taxonomy->{'data-cloneable-inline'})) {
                    $terms = $taxonomy;
                } else {
                    $terms = $taxonomy->find('[data-cloneable], [data-cloneable-inline]', 0);
                }
                if ($terms) {
                    foreach ($terms->children() as $term) {
                        if (count($term->find('[data-cloneable], [data-cloneable-inline]')) == 0) {
                            azh_replace_link_field($term);
                            azh_replace_image_field($term);
                            azh_replace_text_field($term);
                            $templates['taxonomy'][$md5]['item'] = $term->outertext;
                            $templates['taxonomy'][$md5]['item'] = preg_replace_callback('/az-active-([\w\d\-\_]+)/i', function($m) use (&$templates, $md5) {
                                $templates['taxonomy'][$md5]['active'] = $m[1];
                                return '{{active}}';
                            }, $templates['taxonomy'][$md5]['item']);
                        } else {
                            $term->find('[data-cloneable], [data-cloneable-inline]', 0)->innertext = '{{children}}';
                            azh_replace_link_field($term);
                            azh_replace_image_field($term);
                            azh_replace_text_field($term);
                            $templates['taxonomy'][$md5]['item_with_children'] = $term->outertext;
                            $templates['taxonomy'][$md5]['item_with_children'] = preg_replace_callback('/az-active-([\w\d\-\_]+)/i', function($m) use (&$templates, $md5) {
                                $templates['taxonomy'][$md5]['active'] = $m[1];
                                return '{{active}}';
                            }, $templates['taxonomy'][$md5]['item_with_children']);
                        }
                    }
                    $terms->innertext = '{{items}}';
                }
                $templates['taxonomy'][$md5]['root'] = $taxonomy->outertext;
            }
            $taxonomy->outertext = '[azh_taxonomy source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $taxonomy->outertext;
        }
        foreach ($html->find('.az-breadcrumbs') as $breadcrumbs) {
            if (!isset($templates['breadcrumbs'])) {
                $templates['breadcrumbs'] = array();
            }
            $md5 = md5($breadcrumbs->outertext);
            if (!isset($templates['breadcrumbs'][$md5])) {
                $breadcrumbs->{'data-shortcode'} = $md5;
                $items = false;
                if (isset($breadcrumbs->{'data-cloneable'}) || isset($breadcrumbs->{'data-cloneable-inline'})) {
                    $items = $breadcrumbs;
                } else {
                    $items = $breadcrumbs->find('[data-cloneable], [data-cloneable-inline]', 0);
                }
                if ($items) {
                    foreach ($items->children() as $item) {
                        if (count($item->find('a')) == 0 && !isset($item->href)) {
                            azh_replace_text_field($item);
                            $templates['breadcrumbs'][$md5]['item_without_link'] = $item->outertext;
                        } else {
                            azh_replace_link_field($item);
                            azh_replace_text_field($item);
                            $templates['breadcrumbs'][$md5]['item'] = $item->outertext;
                        }
                    }
                    $items->innertext = '{{items}}';
                }
                $templates['breadcrumbs'][$md5]['root'] = $breadcrumbs->outertext;
            }
            $breadcrumbs->outertext = '[azh_breadcrumbs source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $breadcrumbs->outertext;
        }
        foreach ($html->find('.az-entries') as $entries) {
            if (!isset($templates['entries'])) {
                $templates['entries'] = array();
            }
            $md5 = md5($entries->outertext);
            if (!isset($templates['entries'][$md5])) {
                $entries->{'data-shortcode'} = $md5;
                $templates['entries'][$md5] = array('root' => '', 'fields' => array());
                $items = false;
                if (isset($entries->{'data-cloneable'}) || isset($entries->{'data-cloneable-inline'})) {
                    $items = $entries;
                } else {
                    $items = $entries->find('[data-cloneable], [data-cloneable-inline]', 0);
                }
                if ($items) {
                    foreach ($items->children() as $item) {
                        azh_extract_entry_fields_templates($item, $templates['entries'][$md5]['fields']);
                        if (count($item->find('.az-video')) > 0) {
                            $templates['entries'][$md5]['video_item'] = $item->outertext;
                        } elseif (count($item->find('.az-gallery')) > 0) {
                            $templates['entries'][$md5]['gallery_item'] = $item->outertext;
                        } elseif (count($item->find('.az-thumbnail')) > 0) {
                            $templates['entries'][$md5]['thumbnail_item'] = $item->outertext;
                        } else {
                            $templates['entries'][$md5]['item'] = $item->outertext;
                        }
                        break;
                    }
                    $items->innertext = '{{items}}';
                    $templates['entries'][$md5]['root'] = $items->outertext;
                }
            }
            $entries->outertext = '[azh_entries source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $entries->outertext;
        }
        foreach ($html->find('.az-entry') as $entry) {
            if (!isset($templates['entry'])) {
                $templates['entry'] = array();
            }
            $md5 = md5($entry->outertext);
            if (!isset($templates['entry'][$md5])) {
                $entry->{'data-shortcode'} = $md5;
                $templates['entry'][$md5] = array('root' => '', 'fields' => array());
                azh_extract_entry_fields_templates($entry, $templates['entry'][$md5]['fields']);
                $templates['entry'][$md5]['root'] = $entry->outertext;
            }
            $entry->outertext = '[azh_entry source="' . $md5 . '"]';
            $settings['shortcodes'][$md5] = $entry->outertext;
        }
        foreach ($html->find('.az-like') as $like) {
            if (!isset($templates['like'])) {
                $templates['like'] = array();
            }
            $id = uniqid();
            $like->{'data-shortcode'} = $id;
            foreach ($like->find('.az-hidden') as $hidden) {
                $hidden->outertext = '';
            }
            foreach ($like->find('text') as $text) {
                if (!azh_is_empty($text->innertext)) {
                    $text->innertext = '{{text}}';
                    break;
                }
            }
            $templates['like'][$id] = $like->outertext;
            $like->outertext = '[azh_like id="' . $id . '"]';
            $settings['shortcodes'][$id] = $like->outertext;
        }
        $settings['templates'] = azh_array_remove_comments_recursive($templates);

        update_post_meta($post_id, '_azh_settings', $settings);

        $templates = get_option('azh-templates', array());
        if (empty($templates)) {
            $templates = array();
        }
        $templates = array_replace_recursive($templates, $settings['templates']);
        if (is_array($templates)) {
            update_option('azh-templates', $templates);
        }
        $shortcodes = get_option('azh-shortcodes', array());
        if (empty($shortcodes)) {
            $shortcodes = array();
        }
        $shortcodes = array_replace_recursive($shortcodes, $settings['shortcodes']);
        if (is_array($shortcodes)) {
            update_option('azh-shortcodes', $shortcodes);
        }

        return $html->save();
    }
    return $content;
}

$azh_templates = get_option('azh-templates', array());

if (isset($azh_templates['logo']) && !empty($azh_templates['logo'])) {
    $logo_params = array();
    $logo_params[] = array(
        'type' => 'attach_image',
        'heading' => esc_html__('Image', 'azh'),
        'param_name' => 'image',
    );
    $logo_params[] = array(
        'type' => 'dropdown',
        'heading' => esc_html__('Template source', 'azh'),
        'param_name' => 'source',
        'value' => array_combine(array_keys($azh_templates['logo']), array_keys($azh_templates['logo'])),
    );
    azh_add_element(array(
        "name" => esc_html__('Site logo', 'azh'),
        "base" => "azh_logo",
        "show_in_dialog" => false,
        'params' => $logo_params,
            ), 'azh_logo');
}

function azh_logo($atts, $content = null, $tag = null) {
    if (!empty($atts['source'])) {
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['logo'][$atts['source']];
        $template = str_replace('{{link_url}}', esc_url(home_url('/')), $template);
        $image_url = false;
        if (isset($atts['image']) && is_numeric($atts['image'])) {
            $image_url = esc_url(wp_get_attachment_image_url($atts['image'], 'full'));
        }
        if (!$image_url && function_exists('has_custom_logo') && has_custom_logo()) {
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $image_url = esc_url(wp_get_attachment_image_url($custom_logo_id, 'full'));
            }
        }
        if (!$image_url && defined('AZEXO_FRAMEWORK')) {
            $options = get_option(AZEXO_FRAMEWORK);
            if (isset($options['logo']['url']) && !empty($options['logo']['url'])) {
                $image_url = esc_url($options['logo']['url']);
            }
        }
        if ($image_url) {
            $template = str_replace('{{image_url}}', $image_url, $template);
        }
        return $template;
    }
}

if (isset($azh_templates['menu']) && !empty($azh_templates['menu'])) {
    $custom_menus = array();
    $menus = get_terms('nav_menu', array('hide_empty' => false));
    if (is_array($menus) && !empty($menus)) {
        foreach ($menus as $single_menu) {
            if (is_object($single_menu) && isset($single_menu->name, $single_menu->term_id)) {
                $custom_menus[$single_menu->name] = $single_menu->term_id;
            }
        }
    }
    azh_add_element(array(
        "name" => esc_html__('Menu', 'azh'),
        "base" => "azh_menu",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Menu', 'azh'),
                'param_name' => 'nav_menu',
                'value' => $custom_menus,
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['menu']), array_keys($azh_templates['menu'])),
            ),
        ),
            ), 'azh_menu');
}

function azh_menu_item_replaces($item, $menu_item, $template) {
    if (!empty($menu_item->attr_title)) {
        $item = str_replace(' href=', ' title="' . $menu_item->attr_title . '" href=', $item);
    }
    if (!empty($menu_item->target)) {
        $item = str_replace(' href=', ' target="' . $menu_item->target . '" href=', $item);
    }
    $classes = array();
    if (isset($menu_item->classes)) {
        if (is_array($menu_item->classes)) {
            $classes = array_merge($classes, $menu_item->classes);
        } else {
            $classes[] = $menu_item->classes;
        }
    }
    if (isset($template['active']) && $menu_item->object_id == get_queried_object_id()) {
        $classes[] = $template['active'];
        $classes[] = 'az-current';
    }
    $item = str_replace('{{active}}', implode(' ', $classes), $item);
    return $item;
}

function azh_menu_render($template, $menu_items_children, $current_parent = 0) {
    $content = '';
    if (isset($menu_items_children[$current_parent])) {
        foreach ((array) $menu_items_children[$current_parent] as $menu_item) {
            if (isset($menu_items_children[$menu_item->db_id]) && isset($template['item_with_children'])) {
                $item = azh_generate_ids($template['item_with_children']);
                $item = azh_menu_item_replaces($item, $menu_item, $template);
                $item = str_replace('{{url}}', esc_url($menu_item->url), $item);
                $item = str_replace('{{title}}', apply_filters('the_title', $menu_item->title, $menu_item->ID), $item);
                $item = str_replace('{{children}}', azh_menu_render($template, $menu_items_children, $menu_item->db_id), $item);
            } else {
                $item = $template['item'];
                $item = azh_menu_item_replaces($item, $menu_item, $template);
                $item = str_replace('{{url}}', esc_url($menu_item->url), $item);
                $item = str_replace('{{title}}', apply_filters('the_title', $menu_item->title, $menu_item->ID), $item);
            }
            $content .= $item;
        }
    }
    return $content;
}

function azh_menu($atts, $content = null, $tag = null) {
    if (!empty($atts['source'])) {
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['menu'][$atts['source']];
        $template['root'] = azh_generate_ids($template['root']);
        $menu_items = false;
        if (isset($atts['nav_menu']) && is_numeric($atts['nav_menu'])) {
            $menu = wp_get_nav_menu_object($atts['nav_menu']);
            $menu_items = wp_get_nav_menu_items($menu->term_id, array('update_post_term_cache' => false));
        } else {
            $menus = wp_get_nav_menus();
            foreach ($menus as $menu_maybe) {
                if ($menu_items = wp_get_nav_menu_items($menu_maybe->term_id, array('update_post_term_cache' => false))) {
                    $menu = $menu_maybe;
                    break;
                }
            }
        }
        if ($menu_items) {
            $sorted_menu_items = array();
            foreach ((array) $menu_items as $menu_item) {
                $sorted_menu_items[$menu_item->menu_order] = $menu_item;
            }
            $sorted_menu_items = apply_filters('wp_nav_menu_objects', $sorted_menu_items, array());
            $menu_items_children = array();
            foreach ((array) $sorted_menu_items as $menu_item) {
                if (!isset($menu_items_children[$menu_item->menu_item_parent])) {
                    $menu_items_children[$menu_item->menu_item_parent] = array();
                }
                $menu_items_children[$menu_item->menu_item_parent][] = $menu_item;
            }
            $content = str_replace('{{items}}', azh_menu_render($template, $menu_items_children), $template['root']);
            return $content;
        } else {
            return '';
        }
    }
}

function azh_get_taxonomy_options() {
    $taxonomies = get_taxonomies(array(), 'objects');
    $taxonomy_options = array();
    foreach ($taxonomies as $slug => $taxonomy) {
        $taxonomy_options[$taxonomy->label] = $slug;
    }
    return $taxonomy_options;
}

if (isset($azh_templates['taxonomy']) && !empty($azh_templates['taxonomy'])) {
    azh_add_element(array(
        "name" => esc_html__('Taxonomy', 'azh'),
        "base" => "azh_taxonomy",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Taxonomy', 'azh'),
                'param_name' => 'taxonomy',
                'value' => azh_get_taxonomy_options(),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['taxonomy']), array_keys($azh_templates['taxonomy'])),
            ),
        ),
            ), 'azh_taxonomy');
}

function azh_taxonomy_render($template, $terms_children, $current_parent = 0) {
    $content = '';
    foreach ((array) $terms_children[$current_parent] as $term) {
        $term_name = apply_filters('list_cats', esc_attr($term->name), $term);

        $thumbnail_url = '';
        $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
        if ($thumbnail_id) {
            $thumbnail_url = wp_get_attachment_url($thumbnail_id);
        }

        if (isset($terms_children[$term->term_id])) {
            $item = $template['item_with_children'];
            $item = str_replace('{{link_url}}', esc_url(get_term_link($term)), $item);
            $item = str_replace('{{image_url}}', esc_url($thumbnail_url), $item);
            $item = str_replace('{{text}}', $term_name, $item);
            $item = str_replace('{{children}}', azh_taxonomy_render($template, $terms_children, $term->term_id), $item);
        } else {
            $item = $template['item'];
            $item = str_replace('{{link_url}}', esc_url(get_term_link($term)), $item);
            $item = str_replace('{{image_url}}', esc_url($thumbnail_url), $item);
            $item = str_replace('{{text}}', $term_name, $item);
        }
        $current_object = get_queried_object();
        if ((is_category() || is_tax() || is_tag()) && $current_object && ($term->taxonomy === $current_object->taxonomy) && ($term->term_id == get_queried_object_id())) {
            $item = str_replace('{{active}}', $template['active'], $item);
        } else {
            $item = str_replace('{{active}}', '', $item);
        }
        $content .= $item;
    }
    return $content;
}

function azh_taxonomy($atts, $content = null, $tag = null) {
    if (!empty($atts['source'])) {
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['taxonomy'][$atts['source']];
        if (empty($atts['taxonomy'])) {
            $atts['taxonomy'] = 'category';
        }
        $terms = get_terms($atts['taxonomy']);
        $terms_children = array();
        foreach ((array) $terms as $term) {
            if (!isset($terms_children[$term->parent])) {
                $terms_children[$term->parent] = array();
            }
            $terms_children[$term->parent][] = $term;
        }
        $content = str_replace('{{items}}', azh_taxonomy_render($template, $terms_children), $template['root']);
        return $content;
    }
}

if (isset($azh_templates['breadcrumbs']) && !empty($azh_templates['breadcrumbs'])) {
    azh_add_element(array(
        "name" => esc_html__('Breadcrumbs', 'azh'),
        "base" => "azh_breadcrumbs",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['breadcrumbs']), array_keys($azh_templates['breadcrumbs'])),
            ),
        ),
            ), 'azh_breadcrumbs');
}

function azh_breadcrumbs($atts = array()) {
    if (!empty($atts['source'])) {
        include_once(AZH_DIR . 'class.breadcrumb.php' );
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['breadcrumbs'][$atts['source']];
        $content = $template['root'];
        $breadcrumb = new AZH_Breadcrumb();
        $breadcrumb->add_crumb(esc_html__('Home', 'azh'), home_url('/'));
        $breadcrumbs = $breadcrumb->generate();
        $items = '';
        if (!empty($breadcrumbs)) {
            foreach ($breadcrumbs as $key => $crumb) {
                if (!empty($crumb[1])) {
                    if ((count($breadcrumbs) - 1 != $key) || !isset($template['item_without_link'])) {
                        $item = $template['item'];
                        $item = str_replace('{{link_url}}', esc_url($crumb[1]), $item);
                        $item = str_replace('{{text}}', esc_html($crumb[0]), $item);
                        $items .= $item;
                    } else {
                        $items .= str_replace('{{text}}', esc_html($crumb[0]), $template['item_without_link']);
                    }
                } else {
                    if (isset($template['item_without_link'])) {
                        $items .= str_replace('{{text}}', esc_html($crumb[0]), $template['item_without_link']);
                    } else {
                        $item = $template['item'];
                        $item = str_replace('{{link_url}}', '/', $item);
                        $item = str_replace('{{text}}', esc_html($crumb[0]), $item);
                        $items .= $item;
                    }
                }
            }
        }
        $content = str_replace('{{items}}', $items, $content);
        return $content;
    }
}

if (isset($azh_templates['page-title']) && !empty($azh_templates['page-title'])) {
    azh_add_element(array(
        "name" => esc_html__('Page title', 'azh'),
        "base" => "azh_page_title",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['page-title']), array_keys($azh_templates['page-title'])),
            ),
        ),
            ), 'azh_page_title');
}

function azh_get_page_info() {
    $post_type = get_query_var('post_type');
    if (is_array($post_type)) {
        $post_type = reset($post_type);
    }

    $page_title = false;
    $page_subtitle = false;
    $page_meta = false;

    if (is_404()) {
        $page_title = esc_html__('Not Found', 'azh');
        $page_subtitle = esc_html__('Not Found', 'azh');
    } elseif (is_category()) {
        $page_title = single_cat_title('', false);
        $page_subtitle = esc_html__('Category archives', 'azh');
        $page_meta = category_description();
    } elseif (is_tag()) {
        $page_title = single_tag_title('', false);
        $page_subtitle = esc_html__('Tag archives', 'azh');
        $page_meta = tag_description();
    } elseif (is_day()) {
        $page_title = get_the_date();
        $page_subtitle = esc_html__('Daily Archives', 'azh');
    } elseif (is_month()) {
        $page_title = get_the_date(_x('F Y', 'monthly archives date format', 'azh'));
        $page_subtitle = esc_html__('Monthly Archives', 'azh');
    } elseif (is_year()) {
        $page_title = get_the_date(_x('Y', 'yearly archives date format', 'azh'));
        $page_subtitle = esc_html__('Yearly Archives', 'azh');
    } elseif (is_author()) {
        $page_title = get_the_author();
        $page_meta = get_the_author_meta('description');
    } elseif (is_search()) {
        $page_title = esc_html__('Search Results for', 'azh');
        $page_subtitle = get_search_query();
    } elseif (is_archive() && is_tax(get_object_taxonomies($post_type))) {
        $queried_object = get_queried_object();
        if (isset($queried_object->term_id)) {
            $page_title = esc_html($queried_object->name);
        }
        if (isset($queried_object->taxonomy)) {
            $taxonomy = get_taxonomy($queried_object->taxonomy);
            $page_subtitle = esc_html(get_taxonomy_labels($taxonomy)->singular_name);
        }
        if (isset($queried_object->taxonomy) && term_description($queried_object->term_id, $queried_object->taxonomy)) : // Show an optional term description 
            $page_meta = esc_html(term_description($queried_object->term_id, $queried_object->taxonomy));
        endif;
    } elseif (is_archive()) {
        $page_title = post_type_archive_title();
    } elseif (is_home() || is_singular() || is_page()) {
        $current_post = azh_get_earliest_current_post(array('vc_widget', 'azh_widget'), false);
        if (!$current_post) {
            $current_post = get_queried_object();
        }
        if ($current_post) {
            $page_title = get_the_title($current_post);
        }
    }
    return compact('page_title', 'page_subtitle', 'page_meta');
}

function azh_page_title($atts = array()) {
    if (!empty($atts['source'])) {
        $page_info = azh_get_page_info();
        if ($page_info['page_title']) {
            $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
            $template = $settings['templates']['page-title'][$atts['source']];
            return str_replace('{{text}}', $page_info['page_title'], $template);
        }
    }
}

if (isset($azh_templates['page-subtitle']) && !empty($azh_templates['page-subtitle'])) {
    azh_add_element(array(
        "name" => esc_html__('Page subtitle', 'azh'),
        "base" => "azh_page_subtitle",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['page-subtitle']), array_keys($azh_templates['page-subtitle'])),
            ),
        ),
            ), 'azh_page_subtitle');
}

function azh_page_subtitle($atts = array()) {
    if (!empty($atts['source'])) {
        $page_info = azh_get_page_info();
        if ($page_info['page_subtitle']) {
            $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
            $template = $settings['templates']['page-subtitle'][$atts['source']];
            return str_replace('{{text}}', $page_info['page_subtitle'], $template);
        }
    }
}

if (isset($azh_templates['page-meta']) && !empty($azh_templates['page-meta'])) {
    azh_add_element(array(
        "name" => esc_html__('Page meta', 'azh'),
        "base" => "azh_page_meta",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['page-meta']), array_keys($azh_templates['page-meta'])),
            ),
        ),
            ), 'azh_page_meta');
}

function azh_page_meta($atts = array()) {
    if (!empty($atts['source'])) {
        $page_info = azh_get_page_info();
        if ($page_info['page_meta']) {
            $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
            $template = $settings['templates']['page-meta'][$atts['source']];
            return str_replace('{{text}}', $page_info['page_meta'], $template);
        }
    }
}

function azh_get_post_types_list() {
    $post_types = get_post_types(array(), 'objects');
    $post_types_list = array();
    if (is_array($post_types) && !empty($post_types)) {
        foreach ($post_types as $slug => $post_type) {
            if ($slug !== 'revision' && $slug !== 'nav_menu_item'/* && $slug !== 'attachment' */) {
                $post_types_list[] = array($slug, $post_type->label);
            }
        }
    }
    return $post_types_list;
}

if (isset($azh_templates['entry']) && !empty($azh_templates['entry'])) {

    azh_add_element(array(
        "name" => esc_html__('Entry', 'azh'),
        "base" => "azh_entry",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Post ID', 'azh'),
                'param_name' => 'post_id',
                'description' => esc_html__('Post ID', 'azh'),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Post type', 'azh'),
                'param_name' => 'post_type',
                'value' => azh_get_post_types_list(),
                'dependency' => array(
                    'element' => 'post_id',
                    'is_empty' => true,
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['entry']), array_keys($azh_templates['entry'])),
            ),
        ),
            ), 'azh_entry');
}

function azh_fill_entry_fields($content, $fields_templates) {
    do {
        preg_match_all('/{{([\w\-\_\d]+)}}/i', $content, $matches);
        $fields = array_intersect(array_keys($fields_templates), $matches[1]);

        foreach ($fields as $field_name) {
            switch ($field_name) {
                case 'title':
                    $title = str_replace('{{text}}', get_the_title(), $fields_templates['title']);
                    $title = str_replace('{{link_url}}', esc_url(get_permalink()), $title);
                    $content = str_replace('{{title}}', $title, $content);
                    break;
                case 'excerpt':
                    $excerpt_length = false;
                    preg_match('/data-length=[\'\"](\d+)[\'\"]/s', $fields_templates['excerpt'], $matches);
                    if ($matches) {
                        $excerpt_length = $matches[1];
                    }
                    if ($excerpt_length) {
                        $text = wp_strip_all_tags(strip_shortcodes(get_the_content('')));
                        $text = mb_substr($text, 0, $excerpt_length) . '...';
                        $excerpt = str_replace('{{text}}', $text, $fields_templates['excerpt']);
                    } else {
                        $excerpt = str_replace('{{text}}', get_the_excerpt(), $fields_templates['excerpt']);
                    }
                    $content = str_replace('{{excerpt}}', $excerpt, $content);
                    break;
                case 'content':
                    $excerpt = str_replace('{{text}}', get_the_content(''), $fields_templates['content']);
                    $content = str_replace('{{content}}', $excerpt, $content);
                    break;
                case 'day':
                    $day = str_replace('{{text}}', get_the_date('d'), $fields_templates['day']);
                    $content = str_replace('{{day}}', $day, $content);
                    break;
                case 'month':
                    $month = str_replace('{{text}}', get_the_date('M'), $fields_templates['month']);
                    $content = str_replace('{{month}}', $month, $content);
                    break;
                case 'year':
                    $year = str_replace('{{text}}', get_the_date('Y'), $fields_templates['year']);
                    $content = str_replace('{{year}}', $year, $content);
                    break;
                case 'date':
                    $date = str_replace('{{text}}', get_the_date(), $fields_templates['date']);
                    $content = str_replace('{{date}}', $date, $content);
                    break;
                case 'comments-count':
                    $comment_count = get_comment_count(get_the_ID());
                    $count = str_replace('{{text}}', $comment_count['all'], $fields_templates['comments-count']);
                    $count = str_replace('{{link_url}}', esc_url(get_comments_link()), $count);
                    $content = str_replace('{{comments-count}}', $count, $content);
                    break;
                case 'read-more':
                    $more = str_replace('{{link_url}}', esc_url(get_permalink()) . '#more-' . get_the_ID(), $fields_templates['read-more']);
                    $content = str_replace('{{read-more}}', $more, $content);
                    break;
                case 'permalink':
                    $permalink = str_replace('{{link_url}}', esc_url(get_permalink()), $fields_templates['permalink']);
                    $content = str_replace('{{permalink}}', $permalink, $content);
                    break;
                case 'video':
                    $video = '';
                    $video_url = azh_post_video_url();
                    if (!empty($video_url)) {
                        $video = str_replace('{{iframe_url}}', $video_url, $fields_templates['video']);
                        $video = str_replace('{{link_url}}', $video_url, $video);
                    }
                    $content = str_replace('{{video}}', $video, $content);
                    break;
                case 'thumbnail':
                    $thumbnail = '';
                    if (has_post_thumbnail()) {
                        $size = 'full';
                        preg_match('/data-size=[\'\"](\d+)[^\d](\d+)[\'\"]/s', $fields_templates['thumbnail'], $matches);
                        if ($matches) {
                            $size = $matches[1] . 'x' . $matches[2];
                        }
                        $thumbnail = str_replace('{{link_url}}', esc_url(get_permalink()), $fields_templates['thumbnail']);
                        $thumbnail = str_replace('{{image_url}}', esc_url(azh_get_attachment_url(get_post_thumbnail_id(), $size)), $thumbnail);
                    }
                    $content = str_replace('{{thumbnail}}', $thumbnail, $content);
                    break;
                case 'author':
                    $size = '96';
                    preg_match('/data-size=[\'\"](\d+)[\'\"]/s', $fields_templates['author'], $matches);
                    if ($matches) {
                        $size = $matches[1];
                    }
                    $author = str_replace('{{text}}', get_the_author(), $fields_templates['author']);
                    $author = str_replace('{{link_url}}', esc_url(get_author_posts_url(get_the_author_meta('ID'))), $author);
                    $author = str_replace('{{image_url}}', esc_url(get_avatar_url(get_the_author_meta('ID'), array('size' => $size))), $author);
                    $content = str_replace('{{author}}', $author, $content);
                    break;
                case 'gallery':
                    $size = 'full';
                    preg_match('/data-size=[\'\"](\d+)[^\d](\d+)[\'\"]/s', $fields_templates['thumbnail'], $matches);
                    if ($matches) {
                        $size = $matches[1] . 'x' . $matches[2];
                    }
                    $urls = azh_get_post_gallery_urls($size);
                    $items = '';
                    foreach ($urls as $url) {
                        $items .= str_replace('{{image_url}}', $url, $fields_templates['gallery']['item']);
                    }
                    $gallery = str_replace('{{items}}', $items, $fields_templates['gallery']['root']);
                    $content = str_replace('{{gallery}}', $gallery, $content);
                    break;
                case 'category':
                    $category = '';
                    $categories = get_the_category();
                    if (!empty($categories)) {
                        $items = '';
                        foreach ($categories as $category) {
                            $item = str_replace('{{text}}', $category->name, $fields_templates['category']['item']);
                            $item = str_replace('{{link_url}}', esc_url(get_category_link($category->term_id)), $item);
                            $items .= $item;
                        }
                        $category = str_replace('{{items}}', $items, $fields_templates['category']['root']);
                    }
                    $content = str_replace('{{category}}', $category, $content);
                    break;
                case 'tags':
                    $tags = '';
                    $terms = get_the_terms(get_the_ID(), 'post_tag');
                    if (!empty($terms)) {
                        $items = '';
                        foreach ($terms as $term) {
                            $item = str_replace('{{text}}', $term->name, $fields_templates['tags']['item']);
                            $item = str_replace('{{link_url}}', esc_url(get_term_link($term, 'post_tag')), $item);
                            $items .= $item;
                        }
                        $tags = str_replace('{{items}}', $items, $fields_templates['tags']['root']);
                    }
                    $content = str_replace('{{tags}}', $tags, $content);
                    break;
                case 'taxonomy-field':
                    $taxonomy = '';
                    preg_match('/data-taxonomy-field=[\'\"]([\w\d\-\_]+)[\'\"]/s', $fields_templates['taxonomy-field'], $matches);
                    if ($matches) {
                        $terms = get_the_terms(get_the_ID(), $matches[1]);
                        if (!empty($terms)) {
                            $items = '';
                            foreach ($terms as $term) {
                                $item = str_replace('{{text}}', $term->name, $fields_templates['taxonomy-field']['item']);
                                $item = str_replace('{{link_url}}', esc_url(get_term_link($term, $matches[1])), $item);
                                $items .= $item;
                            }
                            $taxonomy = str_replace('{{items}}', $items, $fields_templates['taxonomy-field']['root']);
                        }
                    }
                    $content = str_replace('{{taxonomy-field}}', $taxonomy, $content);
                    break;
                case 'meta-field':
                    $meta_field = '';
                    preg_match('/data-meta-field=[\'\"]([\w\d\-\_]+)[\'\"]/s', $fields_templates['meta-field'], $matches);
                    if ($matches) {
                        $value = get_post_meta(get_the_ID(), $matches[1], true);
                        $value = trim($value);
                        if (!empty($value)) {
                            $meta_field = str_replace('{{text}}', $value, $fields_templates['meta-field']);
                        }
                    }
                    $content = str_replace('{{meta-field}}', $meta_field, $content);
                    break;
                case 'sticky':
                    if (is_sticky()) {
                        $content = str_replace('{{sticky}}', $fields_templates['sticky'], $content);
                    } else {
                        $content = str_replace('{{sticky}}', '', $content);
                    }
                    break;
                case 'sticky-class':
                    if (is_sticky()) {
                        $content = str_replace('{{sticky-class}}', $fields_templates['sticky-class'], $content);
                    } else {
                        $content = str_replace('{{sticky-class}}', '', $content);
                    }
                    break;
                case 'likes-count':
                    $like_count = get_post_meta(get_the_ID(), '_post_like_count', true);
                    $like_count = ( isset($like_count) && is_numeric($like_count) ) ? $like_count : 0;
                    $like_count = str_replace('{{text}}', $like_count, $fields_templates['likes-count']);
                    $content = str_replace('{{likes-count}}', $like_count, $content);
                    break;
                case 'liked-class':
                    if (function_exists('azpl_already_liked') && azpl_already_liked(get_the_ID(), false)) {
                        $content = str_replace('{{liked-class}}', $fields_templates['liked-class'], $content);
                    } else {
                        $content = str_replace('{{liked-class}}', '', $content);
                    }
                    break;
                default:
                    break;
            }
        }
    } while (!empty($fields));
    return $content;
}

function azh_entry($atts = array()) {
    $content = '';
    if (!empty($atts['source'])) {
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['entry'][$atts['source']];
        if (is_numeric($atts['post_id'])) {
            global $post, $wp_query;
            $original = $post;
            $post = get_post($atts['post_id']);
            setup_postdata($post);

            $content = $template['root'];
            $content = azh_fill_entry_fields($content, $template['fields']);

            $wp_query->post = $original;
            wp_reset_postdata();
        } else {
            $closest_post = azh_get_closest_current_post($atts['post_type']);
            global $post, $wp_query;
            $original = $post;
            $post = $closest_post;
            setup_postdata($post);

            $content = $template['root'];
            $content = azh_fill_entry_fields($content, $template['fields']);

            $wp_query->post = $original;
            wp_reset_postdata();
        }
    }
    return $content;
}

if (isset($azh_templates['entries']) && !empty($azh_templates['entries'])) {
    add_action('azh_autocomplete_azh_entries_taxonomies_labels', 'azh_get_terms_labels');
    add_action('azh_autocomplete_azh_entries_taxonomies', 'azh_search_terms');
    add_action('azh_autocomplete_azh_entries_include_labels', 'azh_get_posts_labels');
    add_action('azh_autocomplete_azh_entries_include', 'azh_search_posts');
    add_action('azh_autocomplete_azh_entries_exclude_labels', 'azh_get_posts_labels');
    add_action('azh_autocomplete_azh_entries_exclude', 'azh_search_posts');

    $post_types_list = azh_get_post_types_list();
    $post_types_list[] = array('custom', esc_html__('Custom query', 'azh'));
    $post_types_list[] = array('ids', esc_html__('List of IDs', 'azh'));
    $loop_params = array(
        array(
            'type' => 'checkbox',
            'heading' => esc_html__('Take posts from main query?', 'AZEXO'),
            'param_name' => 'main_query',
            'value' => array(esc_html__('Yes, please', 'AZEXO') => 'yes'),
        ),
//        'dependency' => array(
//            'element' => 'main_query',
//            'is_empty' => true,
//        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Data source', 'azh'),
            'param_name' => 'post_type',
            'value' => $post_types_list,
            'description' => esc_html__('Select content type for your list.', 'azh'),
        ),
        array(
            'type' => 'ajax_multiselect',
            'url' => admin_url('admin-ajax.php') . '?action=azh_search_post',
            'heading' => esc_html__('Include only', 'azh'),
            'param_name' => 'include',
            'description' => esc_html__('Add posts, pages, etc. by title.', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value' => array('ids'),
            ),
        ),
        // Custom query tab
        array(
            'type' => 'textarea',
            'heading' => esc_html__('Custom query', 'azh'),
            'param_name' => 'custom_query',
            'description' => wp_kses(__('Build custom query according to <a href="http://codex.wordpress.org/Function_Reference/query_posts">WordPress Codex</a>.', 'azh'), array('a' => array('href' => array()))),
            'dependency' => array(
                'element' => 'post_type',
                'value' => array('custom'),
            ),
        ),
        array(
            'type' => 'ajax_multiselect',
            'url' => admin_url('admin-ajax.php') . '?action=azh_search_term',
            'heading' => esc_html__('Narrow data source', 'azh'),
            'param_name' => 'taxonomies',
            'description' => esc_html__('Enter categories, tags or custom taxonomies.', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Total items', 'azh'),
            'param_name' => 'max_items',
            'value' => 10, // default value
            'description' => esc_html__('Set max limit for items in list or enter -1 to display all (limited to 1000).', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
        // Data settings
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Order by', 'azh'),
            'param_name' => 'orderby',
            'value' => array(
                esc_html__('Date', 'azh') => 'date',
                esc_html__('Order by post ID', 'azh') => 'ID',
                esc_html__('Author', 'azh') => 'author',
                esc_html__('Title', 'azh') => 'title',
                esc_html__('Last modified date', 'azh') => 'modified',
                esc_html__('Post/page parent ID', 'azh') => 'parent',
                esc_html__('Number of comments', 'azh') => 'comment_count',
                esc_html__('Menu order/Page Order', 'azh') => 'menu_order',
                esc_html__('Meta value', 'azh') => 'meta_value',
                esc_html__('Meta value (numeric)', 'azh') => 'meta_value_num',
                esc_html__('Random order', 'azh') => 'rand',
            ),
            'description' => esc_html__('Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'azh'),
            'group' => esc_html__('Data Settings', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
        array(
            'type' => 'dropdown',
            'heading' => esc_html__('Sorting', 'azh'),
            'param_name' => 'order',
            'group' => esc_html__('Data Settings', 'azh'),
            'value' => array(
                esc_html__('Descending', 'azh') => 'DESC',
                esc_html__('Ascending', 'azh') => 'ASC',
            ),
            'description' => esc_html__('Select sorting order.', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Meta key', 'azh'),
            'param_name' => 'meta_key',
            'description' => esc_html__('Input meta key for list ordering.', 'azh'),
            'group' => esc_html__('Data Settings', 'azh'),
            'dependency' => array(
                'element' => 'orderby',
                'value' => array('meta_value', 'meta_value_num'),
            ),
        ),
        array(
            'type' => 'textfield',
            'heading' => esc_html__('Offset', 'azh'),
            'param_name' => 'offset',
            'description' => esc_html__('Number of list elements to displace or pass over.', 'azh'),
            'group' => esc_html__('Data Settings', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
        array(
            'type' => 'ajax_multiselect',
            'url' => admin_url('admin-ajax.php') . '?action=azh_search_post',
            'heading' => esc_html__('Exclude', 'azh'),
            'param_name' => 'exclude',
            'description' => esc_html__('Exclude posts, pages, etc. by title.', 'azh'),
            'group' => esc_html__('Data Settings', 'azh'),
            'dependency' => array(
                'element' => 'post_type',
                'value_not_equal_to' => array('ids', 'custom'),
            ),
        ),
    );

    azh_add_element(array(
        "name" => "Entries",
        "base" => "azh_entries",
        "show_in_dialog" => false,
        'params' => array_merge($loop_params, array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Template source', 'azh'),
                'param_name' => 'source',
                'value' => array_combine(array_keys($azh_templates['entries']), array_keys($azh_templates['entries'])),
            ),
        ))), 'azh_entries');
}

function azh_array_filter_recursive($input) {
    foreach ($input as &$value) {
        if (is_array($value)) {
            $value = azh_array_filter_recursive($value);
        }
    }
    return array_filter($input);
}

function azh_buildQuery($atts) {

    $atts['items_per_page'] = $atts['query_items_per_page'] = isset($atts['max_items']) ? $atts['max_items'] : '';
    $atts['query_offset'] = isset($atts['offset']) ? $atts['offset'] : '';

    $defaults = array(
        'post_type' => 'post',
        'orderby' => '',
        'order' => 'DESC',
        'meta_key' => '',
        'max_items' => '10',
        'offset' => '0',
        'taxonomies' => '',
        'custom_query' => '',
        'include' => '',
        'exclude' => '',
    );
    $atts = wp_parse_args($atts, $defaults);

    // Set include & exclude
    if ($atts['post_type'] !== 'ids' && !empty($atts['exclude'])) {
        $atts['exclude'] .= ',' . $atts['exclude'];
    } else {
        $atts['exclude'] = $atts['exclude'];
    }
    if ($atts['post_type'] !== 'ids') {
        $settings = array(
            'posts_per_page' => $atts['query_items_per_page'],
            'offset' => $atts['query_offset'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'meta_key' => in_array($atts['orderby'], array(
                'meta_value',
                'meta_value_num',
            )) ? $atts['meta_key'] : '',
            'post_type' => $atts['post_type'],
            'exclude' => $atts['exclude'],
        );
        if (!empty($atts['taxonomies'])) {
            $taxonomies_types = get_taxonomies(array('public' => true));
            $terms = get_terms(array_keys($taxonomies_types), array(
                'hide_empty' => false,
                'include' => $atts['taxonomies'],
            ));
            $settings['tax_query'] = array();
            $tax_queries = array(); // List of taxnonimes
            foreach ($terms as $t) {
                if (!isset($tax_queries[$t->taxonomy])) {
                    $tax_queries[$t->taxonomy] = array(
                        'taxonomy' => $t->taxonomy,
                        'field' => 'id',
                        'terms' => array($t->term_id),
                        'relation' => 'IN'
                    );
                } else {
                    $tax_queries[$t->taxonomy]['terms'][] = $t->term_id;
                }
            }
            $settings['tax_query'] = array_values($tax_queries);
            $settings['tax_query']['relation'] = 'OR';
        }
    } else {
        if (empty($atts['include'])) {
            $atts['include'] = - 1;
        } elseif (!empty($atts['exclude'])) {
            $atts['include'] = preg_replace(
                    '/(('
                    . preg_replace(
                            array('/^\,\*/', '/\,\s*$/', '/\s*\,\s*/'), array('', '', '|'), $atts['exclude']
                    )
                    . ')\,*\s*)/', '', $atts['include']);
        }
        $settings = array(
            'include' => $atts['include'],
            'posts_per_page' => $atts['query_items_per_page'],
            'offset' => $atts['query_offset'],
            'post_type' => 'any',
            'orderby' => 'post__in',
        );
    }

    return $settings;
}

function azh_filterQuerySettings($args) {
    $defaults = array(
        'numberposts' => 5,
        'offset' => 0,
        'category' => 0,
        'orderby' => 'date',
        'order' => 'DESC',
        'include' => array(),
        'exclude' => array(),
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'public' => true
    );

    $r = wp_parse_args($args, $defaults);
    if (empty($r['post_status'])) {
        $r['post_status'] = ( 'attachment' == $r['post_type'] ) ? 'inherit' : 'publish';
    }
    if (!empty($r['numberposts']) && empty($r['posts_per_page'])) {
        $r['posts_per_page'] = $r['numberposts'];
    }
    if (!empty($r['category'])) {
        $r['cat'] = $r['category'];
    }
    if (!empty($r['include'])) {
        $incposts = wp_parse_id_list($r['include']);
        $r['posts_per_page'] = count($incposts);  // only the number of posts included
        $r['post__in'] = $incposts;
    } elseif (!empty($r['exclude'])) {
        $r['post__not_in'] = wp_parse_id_list($r['exclude']);
    }

    $r['ignore_sticky_posts'] = true;
    $r['no_found_rows'] = true;

    return azh_array_filter_recursive($r);
}

function azh_entries($atts = array()) {
    $content = '';
    if (!empty($atts['source'])) {
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['entries'][$atts['source']];
        $items = '';
        if (isset($atts['main_query']) && $atts['main_query']) {
            while (have_posts()) {
                the_post();
                if (((get_post_type() == 'post' && has_post_format('gallery')) || get_post_meta(get_the_ID(), '_gallery')) && isset($template['gallery_item'])) {
                    $items .= azh_fill_entry_fields($template['gallery_item'], $template['fields']);
                } elseif (((get_post_type() == 'post' && has_post_format('video')) || get_post_meta(get_the_ID(), '_video')) && isset($template['video_item'])) {
                    $items .= azh_fill_entry_fields($template['video_item'], $template['fields']);
                } elseif (has_post_thumbnail() && isset($template['thumbnail_item'])) {
                    $items .= azh_fill_entry_fields($template['thumbnail_item'], $template['fields']);
                } else {
                    if (isset($template['item'])) {
                        $items .= azh_fill_entry_fields($template['item'], $template['fields']);
                    } elseif (isset($template['thumbnail_item'])) {
                        $items .= azh_fill_entry_fields($template['thumbnail_item'], $template['fields']);
                    }
                }
            }
        } else {
            $loop_args = azh_filterQuerySettings(azh_buildQuery($atts));
            $query = new WP_Query($loop_args);
            if ($query->have_posts()) {
                global $post, $wp_query;
                $original = $post;
                while ($query->have_posts()) {
                    $query->the_post();
                    if (((get_post_type() == 'post' && has_post_format('gallery')) || get_post_meta(get_the_ID(), '_gallery')) && isset($template['gallery_item'])) {
                        $items .= azh_fill_entry_fields($template['gallery_item'], $template['fields']);
                    } elseif (((get_post_type() == 'post' && has_post_format('video')) || get_post_meta(get_the_ID(), '_video')) && isset($template['video_item'])) {
                        $items .= azh_fill_entry_fields($template['video_item'], $template['fields']);
                    } elseif (has_post_thumbnail() && isset($template['thumbnail_item'])) {
                        $items .= azh_fill_entry_fields($template['thumbnail_item'], $template['fields']);
                    } else {
                        if (isset($template['item'])) {
                            $items .= azh_fill_entry_fields($template['item'], $template['fields']);
                        } elseif (isset($template['thumbnail_item'])) {
                            $items .= azh_fill_entry_fields($template['thumbnail_item'], $template['fields']);
                        }
                    }
                }
                $wp_query->post = $original;
                wp_reset_postdata();
            }
        }
        $content = str_replace('{{items}}', $items, $template['root']);
    }
    return $content;
}

if (isset($azh_templates['entries']) && !empty($azh_templates['entries'])) {
    azh_add_element(array(
        "name" => esc_html__('Pagination', 'azh'),
        "base" => "azh_pagination",
        "show_in_dialog" => false,
            ), 'azh_pagination');
}

function azh_paginate_links($args = '') {
    global $wp_query, $wp_rewrite;

    // Setting up default values based on the current URL.
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $url_parts = explode('?', $pagenum_link);

    // Get max pages and current page out of the current query, if available.
    $total = isset($wp_query->max_num_pages) ? $wp_query->max_num_pages : 1;
    $current = get_query_var('paged') ? intval(get_query_var('paged')) : 1;

    // Append the format placeholder to the base URL.
    $pagenum_link = trailingslashit($url_parts[0]) . '%_%';

    // URL base depends on permalink settings.
    $format = $wp_rewrite->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit($wp_rewrite->pagination_base . '/%#%', 'paged') : '?paged=%#%';

    $defaults = array(
        'base' => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
        'format' => $format, // ?page=%#% : %#% is replaced by the page number
        'total' => $total,
        'current' => $current,
        'show_all' => false,
        'prev_next' => true,
        'prev_text' => '<span class="az-before"></span>' . '<span class="az-label">' . esc_html__('Previous', 'azh') . '</span>',
        'next_text' => '<span class="az-label">' . esc_html__('Next', 'azh') . '</span>' . '<span class="az-after"></span>',
        'end_size' => 1,
        'mid_size' => 2,
        'add_args' => array(), // array of query args to add
        'add_fragment' => '',
        'before_page_number' => '',
        'after_page_number' => ''
    );

    $args = wp_parse_args($args, $defaults);

    if (!is_array($args['add_args'])) {
        $args['add_args'] = array();
    }

    // Merge additional query vars found in the original URL into 'add_args' array.
    if (isset($url_parts[1])) {
        // Find the format argument.
        $format = explode('?', str_replace('%_%', $args['format'], $args['base']));
        $format_query = isset($format[1]) ? $format[1] : '';
        wp_parse_str($format_query, $format_args);

        // Find the query args of the requested URL.
        wp_parse_str($url_parts[1], $url_query_args);

        // Remove the format argument from the array of query arguments, to avoid overwriting custom format.
        foreach ($format_args as $format_arg => $format_arg_value) {
            unset($url_query_args[$format_arg]);
        }

        $args['add_args'] = array_merge($args['add_args'], urlencode_deep($url_query_args));
    }

    // Who knows what else people pass in $args
    $total = (int) $args['total'];
    if ($total < 2) {
        return;
    }
    $current = (int) $args['current'];
    $end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
    if ($end_size < 1) {
        $end_size = 1;
    }
    $mid_size = (int) $args['mid_size'];
    if ($mid_size < 0) {
        $mid_size = 2;
    }
    $add_args = $args['add_args'];
    $page_links = array();
    $dots = false;

    $prev = '';
    if ($args['prev_next'] && $current && 1 < $current) :
        $link = str_replace('%_%', 2 == $current ? '' : $args['format'], $args['base']);
        $link = str_replace('%#%', $current - 1, $link);
        if ($add_args)
            $link = add_query_arg($add_args, $link);
        $link .= $args['add_fragment'];

        /**
         * Filters the paginated links for the given archive pages.
         *
         * @since 3.0.0
         *
         * @param string $link The paginated link URL.
         */
        $prev = '<a class="az-prev" href="' . esc_url(apply_filters('paginate_links', $link)) . '">' . $args['prev_text'] . '</a>';
    endif;
    for ($n = 1; $n <= $total; $n++) :
        if ($n == $current) :
            $page_links[] = "<span class='az-page-number az-current'>" . $args['before_page_number'] . number_format_i18n($n) . $args['after_page_number'] . "</span>";
            $dots = true;
        else :
            if ($args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size )) :
                $link = str_replace('%_%', 1 == $n ? '' : $args['format'], $args['base']);
                $link = str_replace('%#%', $n, $link);
                if ($add_args)
                    $link = add_query_arg($add_args, $link);
                $link .= $args['add_fragment'];

                /** This filter is documented in wp-includes/general-template.php */
                $page_links[] = "<a class='az-page-number' href='" . esc_url(apply_filters('paginate_links', $link)) . "'>" . $args['before_page_number'] . number_format_i18n($n) . $args['after_page_number'] . "</a>";
                $dots = true;
            elseif ($dots && !$args['show_all']) :
                $page_links[] = '<span class="az-page-number az-dot">' . __('&hellip;') . '</span>';
                $dots = false;
            endif;
        endif;
    endfor;
    $next = '';
    if ($args['prev_next'] && $current && $current < $total) :
        $link = str_replace('%_%', $args['format'], $args['base']);
        $link = str_replace('%#%', $current + 1, $link);
        if ($add_args)
            $link = add_query_arg($add_args, $link);
        $link .= $args['add_fragment'];

        /** This filter is documented in wp-includes/general-template.php */
        $next = '<a class="az-next" href="' . esc_url(apply_filters('paginate_links', $link)) . '">' . $args['next_text'] . '</a>';
    endif;
    return $prev . "\n" . join("\n", $page_links) . "\n" . $next;
}

function azh_pagination($atts = array()) {
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $query_args = array();
    $url_parts = explode('?', $pagenum_link);

    if (isset($url_parts[1])) {
        wp_parse_str($url_parts[1], $query_args);
    }
    $links = azh_paginate_links(array(
        'add_args' => array_map('urlencode', $query_args),
    ));
    print $links;
}

if (isset($azh_templates['like']) && !empty($azh_templates['like'])) {
    azh_add_element(array(
        "name" => esc_html__('Like', 'azh'),
        "base" => "azh_like",
        "show_in_dialog" => false,
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => esc_html__('Unique ID', 'azh'),
                'param_name' => 'id',
                'description' => esc_html__('Used for store in Data Base', 'azh'),
            ),
        ),
            ), 'azh_like');
}

function azh_get_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    $ip = ( $ip === false ) ? '0.0.0.0' : $ip;
    return $ip;
}

function azh_like($atts = array()) {
    if (!empty($atts['id'])) {
        $stored_likes = get_option('azh-like', array());
        $settings = get_post_meta(get_the_ID(), '_azh_settings', true);
        $template = $settings['templates']['like'][$atts['id']];
        $likes = 0;
        if (isset($stored_likes[$atts['id']])) {
            if (isset($stored_likes[$atts['id']]['count'])) {
                $likes = $stored_likes[$atts['id']]['count'];
            }
            $user_id = get_current_user_id();
            if (!is_user_logged_in()) {
                $user_id = azh_get_ip();
            }
            if (isset($stored_likes[$atts['id']]['users'])) {
                if (in_array($user_id, $stored_likes[$atts['id']]['users'])) {
                    $template = str_replace('az-like', 'az-like az-liked', $template);
                }
            }
        }
        $nonce = wp_create_nonce('azh-like-nonce');
        $likes .= '<span class="az-hidden" data-nonce="' . $nonce . '" data-id="' . $atts['id'] . '"></span>';
        return str_replace('{{text}}', $likes, $template);
    }
}

add_action('wp_ajax_nopriv_azh_process_like', 'azh_process_like');
add_action('wp_ajax_azh_process_like', 'azh_process_like');

function azh_process_like() {
    $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : 0;
    if (!wp_verify_nonce($nonce, 'azh-like-nonce')) {
        exit(esc_html__('Not permitted', 'azh'));
    }
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
    $stored_likes = get_option('azh-like', array());
    $user_id = get_current_user_id();
    if (!is_user_logged_in()) {
        $user_id = azh_get_ip();
    }
    if (!isset($stored_likes[$id])) {
        $stored_likes[$id] = array('count' => 0, 'users' => array());
    }
    if (in_array($user_id, $stored_likes[$id]['users'])) {
        $key = array_search($user_id, $stored_likes[$id]['users']);
        unset($stored_likes[$id]['users'][$key]);
        $stored_likes[$id]['count'] = count($stored_likes[$id]['users']);
        print $stored_likes[$id]['count'] . '|unliked';
    } else {
        $stored_likes[$id]['users'][] = $user_id;
        $stored_likes[$id]['users'] = array_unique($stored_likes[$id]['users']);
        $stored_likes[$id]['count'] = count($stored_likes[$id]['users']);
        print $stored_likes[$id]['count'] . '|liked';
    }
    update_option('azh-like', $stored_likes);
    wp_die();
}
