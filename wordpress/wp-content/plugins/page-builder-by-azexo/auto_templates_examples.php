<?php

$templates = array(
    'logo' => '<a class="az-logo" href="{{home-url}}"><img src="{{image-url}}"></a>', //any wrapper
    'menu' => array(
        'root' => '<ul class="az-menu" data-cloneable="">{{items}}</ul>', //any wrapper
        'item' => '<li><a class="az-active-anyclass" href="{{url}}">{{title}}</a></li>',
        'item_with_children' => '<li><a href="{{url}}">{{title}}</a><ul data-cloneable="">{{children}}</ul></li>',
    ),
    'taxonomy' => array(
        'root' => '<ul class="az-taxonomy" data-cloneable="">{{items}}</ul>', //any wrapper
        'item' => '<li><a href="{{url}}">{{title}}</a></li>',
        'item_with_children' => '<li><a href="{{url}}">{{title}}</a><ul data-cloneable="">{{children}}</ul></li>',
    ),
    'page-title' => '<div class="az-page-title">{{text}}</div>', //any wrapper
    'page-subtitle' => '<div class="az-page-subtitle">{{text}}</div>', //any wrapper
    'page-meta' => '<div class="az-page-meta">{{text}}</div>', //any wrapper
    'breadcrumbs' => array(
        'root' => '<span class="az-breadcrumbs"><span data-cloneable="">{{items}}</span></span>', //any wrapper
        'item' => '<a href="{{url}}">{{title}}</a>',
        'item_without_link' => '<span>{{title}}</span>',
    ),
    //image, gallery and video formats
    'entries' => array(
        'root' => '<div class="az-entries"><div data-cloneable="">{{items}}</div></div>', //any wrapper
        'item' => '',
        'fields' => array(
        ),
    ),
    'entry' => array(
        'root' => '<div class="az-entry">{{item}}</div>', //any wrapper
        'fields' => array(
            'title' => '<div class="az-title">{{title}}</div>', //any wrapper
            'excerpt' => '<div class="az-excerpt" data-length="50">{{excerpt}}</div>', //any wrapper
            'content' => '<div class="az-content">{{content}}</div>', //any wrapper
            'day' => '<span class="az-day">{{day}}</span>', //any wrapper
            'month' => '<span class="az-month">{{month}}</span>', //any wrapper
            'year' => '<span class="az-year">{{year}}</span>', //any wrapper
            'date' => '<span class="az-date">{{date}}</span>', //any wrapper
            'comments-count' => '<span class="az-comments-count">{{comments-count}}</span>', //any wrapper
            'read-more' => '<a href="{{url}}" class="az-read-more"></a>', //any wrapper
            'permalink' => '<a href="{{url}}" class="az-permalink"></a>', //any wrapper
            'video' => '<iframe class="az-video" src="{{url}}"></iframe>', //any wrapper
            'thumbnail' => '<img class="az-thumbnail" data-size="300x300" src="{{url}}">', //any wrapper            
            'author' => '<a href="{{url}}" class="az-author"><img src="{{avatar}}"><span>{{name}}</span></a>', //any wrapper                        
            'gallery' => array(
                'root' => '<div class="az-gallery" data-size="300x300"><div data-cloneable="">{{items}}</div></div>', //any wrapper
                'item' => '<img src="{{url}}">',
            ),
            'meta-field' => '<span data-meta-field="meta-key">{{meta-value}}</span>', //closest wrapper or any wrapper if has '.az-value'
            'taxonomy-field' => array(
                'root' => '<span data-taxonomy-field="taxonomy"><span data-cloneable="">{{items}}</span></span>', //any wrapper
                'item' => '<a href="{{url}}">{{title}}</a>',
            ),
            'category' => array(
                'root' => '<div class="az-category"><div data-cloneable="">{{items}}</div></div>', //any wrapper
                'item' => '<a href="{{url}}">{{title}}</a>',
            ),
            'tags' => array(
                'root' => '<div class="az-tags"><div data-cloneable="">{{items}}</div></div>', //any wrapper
                'item' => '<a href="{{url}}">{{title}}</a>',
            ),
            'sticky' => '<div class="az-sticky"></div>', //any wrapper
            'sticky-class' => '<div class="az-sticky-anyclass"></div>', //exact tag
            'like' => '<div class="az-like">{{number}}</div>', //any wrapper
            
            
            
            'share' => array(
                'root' => '<div class="az-share"><div data-cloneable="">{{items}}</div></div>', //any wrapper
                'item' => '<a href="{{url}}"><span class="{{font-awesome-icon-class}}"></span></a>',
            ),
        ),
    ),
);
