(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    window.azh = $.extend({}, window.azh);
    window.azh.$ = $;
    var load_script_waiting_callbacks = {};
    var scripts_loaded = {};
    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    $.fn.parallax = function (xpos, speedFactor, outerHeight) {
        var $this = $(this);
        var getHeight;
        var firstTop;
        var paddingTop = 0;
        $this.each(function () {
            firstTop = $this.offset().top;
        });
        if (outerHeight) {
            getHeight = function ($jqo) {
                return $jqo.outerHeight(true);
            };
        } else {
            getHeight = function ($jqo) {
                return $jqo.height();
            };
        }
        if (arguments.length < 1 || xpos === null) {
            xpos = "50%";
        }
        if (arguments.length < 2 || speedFactor === null) {
            speedFactor = 0.1;
        }
        if (arguments.length < 3 || outerHeight === null) {
            outerHeight = true;
        }
        function update() {
            var pos = $window.scrollTop();
            $this.each(function () {
                var $element = $(this);
                var top = $element.offset().top;
                var height = getHeight($element);
                if (top + height < pos || top > pos + $window.height()) {
                    return;
                }
                $this.css('backgroundPosition', xpos + " " + Math.round((firstTop - pos) * speedFactor) + "px");
            });
        }
        $window.on('scroll', update);
        $window.on('resize', update);
        update();
    };
    $.fn.equalizeHeights = function () {
        var max = Math.max.apply(this, $(this).map(function (i, e) {
            return $(e).height();
        }).get());
        if (max > 0)
            this.height(max);
        return max;
    };
    $.fn.equalizeWidths = function () {
        var max = Math.max.apply(this, $(this).map(function (i, e) {
            return $(e).width();
        }).get());
        if (max > 0)
            this.width(max);
        return max;
    };
    function fullWidthSection($wrapper) {
        $wrapper.find('[data-full-width="true"]').each(function (key, item) {
            var $el = $(this);
            $el.removeClass('az-full-width');
            var fixed = false;
            $el.parents().addBack().each(function () {
                if ($(this).css('position') === 'fixed' && !$(this).is('.az-fixed')) {
                    fixed = true;
                    return false;
                }
            });
            if (!fixed) {
                var $el_full = $("<div></div>");
                $el.after($el_full);
                $el.css({
                    left: 0,
                    width: 0
                });
                var el_margin_left = parseInt($el.css("margin-left"), 10);
                var el_margin_right = parseInt($el.css("margin-right"), 10);
                var offset = parseInt($body.css('padding-left'), 10) - $el_full.offset().left - el_margin_left;
                var width = $body.prop("clientWidth") - parseInt($body.css('padding-left'), 10) - parseInt($body.css('padding-right'), 10);
                var container_width = $el_full.width();
                var direction = getComputedStyle($el.get(0)).direction;
                if ($el.css({
                    position: "relative",
                    left: (direction === 'rtl') ? -offset : offset,
                    "box-sizing": "border-box",
                    width: width
                }), !$el.data("stretch-content")) {
                    var padding = -1 * offset;
                    if (padding < 0) {
                        padding = 0;
                    }
                    var paddingRight = width - padding - container_width + el_margin_left + el_margin_right;
                    if (paddingRight < 0) {
                        paddingRight = 0;
                    }
                    $el.css({
                        "padding-left": padding + "px",
                        "padding-right": paddingRight + "px"
                    });
                }
                $el.addClass('az-full-width');
                $el.animate({
                    opacity: 1
                }, 400);
                $el.triggerHandler("az-full-width", {
                    container_width: container_width
                });
                $window.trigger("az-full-width", {
                    element: $el,
                    container_width: container_width
                });
                $el.find('.az-container').css({
                    'padding-right': '0',
                    'padding-left': '0',
                    'max-width': container_width
                });
                $el_full.remove();
            }
        });
        $wrapper.find('[data-full-width="false"]').each(function (key, item) {
            var $el = $(this);
            var $el_full = $("<div></div>");
            $el.after($el_full);
            var container_width = $el_full.width();
            $el.find('.az-container').css('width', container_width);
            $el_full.remove();
            $el.css({
                visibility: "visible",
                opacity: 1
            });
        });
    }
    function auto_rescale($wrapper) {
        if (!customize) {
            setTimeout(function () {
                $wrapper.find('.az-auto-rescale[data-width][data-height]:not(.az-full-screen-height)').each(function () {
                    function rescale($container) {
                        $container.css({
                            'margin': 0,
                            'width': $container.data('width') + 'px',
                            'transform': 'scale(' + $container.parent().width() / $container.data('width') + ')',
                            'transform-origin': 'top left'
                        });
                        $container.parent().css({
                            'height': ($container.height() * $container.parent().width() / $container.data('width')) + 'px'
                        });
                    }
                    var $container = $(this);
                    if ($container.is('.az-auto-upscale') || $container.parent().width() < $container.data('width') || $container.width() < $container.parent().width()) {
                        if ($container.is(':visible')) {
                            rescale($container);
                        } else {
                            var intervalID = setInterval(function () {
                                if ($container.is(':visible')) {
                                    rescale($container);
                                    clearInterval(intervalID);
                                }
                            }, 100);
                        }
                    }
                });
                $wrapper.find('.az-auto-rescale[data-width][data-height].az-full-screen-height').each(function () {
                    var $container = $(this);
                    var offset = 0;
                    if ($container.attr('data-full-screen-height-offset')) {
                        offset = offset + parseInt($container.attr('data-full-screen-height-offset'), 10);
                    }
                    if (!customize) {
                        if ($body.children('#wpadminbar').length) {
                            offset = offset - $body.children('#wpadminbar').height();
                        }
                    }
                    $container.parent().css({
                        'overflow': 'hidden',
                        'height': $window.height() + offset
                    });
                    var height = $container.data('height');
                    var width = $container.data('width');
                    var scale = 1;
                    var shift_x = 0;
                    var shift_y = 0;
                    var scale_h = $container.parent().height() / height;
                    var scale_w = $container.parent().width() / width;
                    if (scale_h > scale_w) {
                        scale = scale_h;
                    } else {
                        scale = scale_w;
                    }
                    if ($container.is('.az-full-screen-height-cover') && $container.css('background-image') != 'none') {
                        var background_width = 1;
                        var background_height = 1;
                        var match = /(\d+)px (\d+)px/.exec($container.css('background-size'));
                        if (match) {
                            background_width = parseInt(match[1], 10);
                            background_height = parseInt(match[2], 10);
                        } else {
                            var img = new Image;
                            img.src = $container.css('background-image').replace(/url\(|'|"|\)$/ig, "");
                            var background_width = img.width;
                            var background_height = img.height;
                        }
                        var background_scale = 1;
                        var background_scale_h = $container.parent().height() / background_height;
                        var background_scale_w = $container.parent().width() / background_width;
                        if (background_scale_h > background_scale_w) {
                            background_scale = background_scale_h;
                        } else {
                            background_scale = background_scale_w;
                        }
                        if (scale < background_scale) {
                            scale = background_scale;
                        }
                    }
                    shift_y = Math.round(height * scale - $container.parent().height()) / 2;
                    shift_x = Math.round(width * scale - $container.parent().width()) / 2;
                    $container.css({
                        'margin': 0,
                        'width': width + 'px',
                        'transform': 'translate(-' + shift_x + 'px, -' + shift_y + 'px) scale(' + scale + ')',
                        'transform-origin': 'top left'
                    });
                    $container.get(0).style.setProperty("height", height + 'px', "important");
                });
            }, 0);
        }
    }
    function loadScript(url, callback) {
        function handleLoad() {
            if (!scripts_loaded[url]) {
                //callback(url, "ok");
                scripts_loaded[url] = true;
                while (url in load_script_waiting_callbacks) {
                    var callbacks = load_script_waiting_callbacks[url];
                    load_script_waiting_callbacks[url] = undefined;
                    delete load_script_waiting_callbacks[url];
                    for (var i = 0; i < callbacks.length; i++) {
                        callbacks[i](url, "ok");
                    }
                }
            }
        }
        function handleReadyStateChange() {
            var state;
            if (!scripts_loaded[url]) {
                state = scr.readyState;
                if (state === "complete") {
                    handleLoad();
                }
            }
        }
        function handleError() {
            if (!scripts_loaded[url]) {
                //callback(url, "error");
                scripts_loaded[url] = true;
                while (url in load_script_waiting_callbacks) {
                    var callbacks = load_script_waiting_callbacks[url];
                    load_script_waiting_callbacks[url] = undefined;
                    delete load_script_waiting_callbacks[url];
                    for (var i = 0; i < callbacks.length; i++) {
                        callbacks[i](url, "error");
                    }
                }
            }
        }
        if (url in load_script_waiting_callbacks) {
            load_script_waiting_callbacks[url].push(callback);
            return;
        } else {
            if (url in scripts_loaded) {
                callback(url, "ok");
                return;
            }
        }
        load_script_waiting_callbacks[url] = [callback];

        var scr = document.createElement('script');
        scr.onload = handleLoad;
        scr.onreadystatechange = handleReadyStateChange;
        scr.onerror = handleError;
        scr.src = url;
        document.body.appendChild(scr);
    }
    function make_css_rule(selector, styles) {
        var style = document.createElement('style');
        style.type = 'text/css';
        style.innerHTML = selector + ' { ' + styles + ' }';
        document.getElementsByTagName('head')[0].appendChild(style);
        return style;
    }
    function set_styles_important(styles) {
        styles = styles.split(';');
        $(styles).each(function (index) {
            if ($.trim(this) && this.indexOf('!important') < 0) {
                styles[index] = this + ' !important';
            }
        });
        styles = styles.join(';');
        return styles;
    }
    azh.refresh_hover_css_rules = function ($wrapper) {
        $wrapper.find('[data-hover]').addBack().filter('[data-hover]').each(function () {
            var $hover = $(this);
            if ($hover.attr('data-hover')) {
                $hover.css('transition-property', 'all');
                var hover_style = set_styles_important($hover.attr('data-hover'));
                var id = makeid();
                $hover.attr('data-hid', id);
                if ($hover.data('hover-rules')) {
                    $hover.data('hover-rules').remove();
                }
                if ($hover.is('.az-svg')) {
                    $hover.data('hover-rules', make_css_rule('[data-hid="' + id + '"].az-hover > svg > *', hover_style));
                } else {
                    var $trigger = $hover.closest('.az-hover-trigger');
                    if ($trigger.length) {
                        $hover.data('hover-rules', make_css_rule('.az-hover-trigger:hover [data-hid="' + id + '"]', hover_style));
                    } else {
                        $hover.data('hover-rules', make_css_rule('[data-hid="' + id + '"]:hover', hover_style));
                    }
                }
            }
        });
    };
    azh.refresh_placeholder_css_rules = function ($wrapper) {
        $wrapper.find('[data-placeholder]').addBack().filter('[data-placeholder]').each(function () {
            var $placeholder = $(this);
            if ($placeholder.attr('data-placeholder')) {
                var placeholder_style = set_styles_important($placeholder.attr('data-placeholder'));
                var id = makeid();
                $placeholder.attr('data-pid', id);
                if ($placeholder.data('placeholder-rules')) {
                    $placeholder.data('placeholder-rules').remove();
                }
                if ($placeholder.data('webkit-placeholder-rules')) {
                    $placeholder.data('webkit-placeholder-rules').remove();
                }
                if ($placeholder.data('moz-placeholder-rules')) {
                    $placeholder.data('moz-placeholder-rules').remove();
                }
                if ($placeholder.data('ms-placeholder-rules')) {
                    $placeholder.data('ms-placeholder-rules').remove();
                }
                $placeholder.data('placeholder-rules', make_css_rule('[data-pid="' + id + '"]::placeholder', placeholder_style));
                $placeholder.data('webkit-placeholder-rules', make_css_rule('[data-pid="' + id + '"]::-webkit-input-placeholder', placeholder_style));
                $placeholder.data('moz-placeholder-rules', make_css_rule('[data-pid="' + id + '"]::-moz-placeholder', placeholder_style));
                $placeholder.data('ms-placeholder-rules', make_css_rule('[data-pid="' + id + '"]:-ms-input-placeholder', placeholder_style));
            }
        });
    };
    azh.refresh_focus_css_rules = function ($wrapper) {
        $wrapper.find('[data-focus]').addBack().filter('[data-focus]').each(function () {
            var $focus = $(this);
            if ($focus.attr('data-focus')) {
                var focus_style = set_styles_important($focus.attr('data-focus'));
                var id = makeid();
                $focus.attr('data-fid', id);
                if ($focus.data('focus-rules')) {
                    $focus.data('focus-rules').remove();
                }
                $focus.data('focus-rules', make_css_rule('[data-fid="' + id + '"]:focus', focus_style));
            }
        });
    };
    azh.refresh_reveal_css_rules = function ($wrapper) {
        $wrapper.find('[data-reveal]').addBack().filter('[data-reveal]').each(function () {
            var $reveal = $(this);
            if ($reveal.attr('data-reveal')) {
                var reveal_style = set_styles_important($reveal.attr('data-reveal'));
                var id = makeid();
                $reveal.attr('data-rid', id);
                if ($reveal.data('reveal-rules')) {
                    $reveal.data('reveal-rules').remove();
                }
                var $trigger = $reveal.closest('.az-reveal-trigger');
                if ($trigger.length) {
                    $('.az-reveal-trigger [data-rid="' + id + '"]').addClass('az-end-transition');
                    $reveal.data('reveal-rules', make_css_rule('.az-reveal-trigger:not(.az-visible) [data-rid="' + id + '"]', reveal_style));
                    setTimeout(function () {
                        $('.az-reveal-trigger [data-rid="' + id + '"]').removeClass('az-end-transition');
                    });
                } else {
                    $('[data-rid="' + id + '"]').addClass('az-end-transition');
                    $reveal.data('reveal-rules', make_css_rule('[data-rid="' + id + '"]:not(.az-visible)', reveal_style));
                    setTimeout(function () {
                        $('[data-rid="' + id + '"]').removeClass('az-end-transition');
                    });
                }
            }
        });
    };
    azh.refresh_responsive_css_rules = function ($wrapper) {
        for (var prefix in azh.device_prefixes) {
            var data = 'responsive-' + prefix;
            $wrapper.find('[data-' + data + ']').addBack().filter('[data-' + data + ']').each(function () {
                var $responsive = $(this);
                if ($responsive.attr('data-' + data)) {
                    var responsive_style = set_styles_important($responsive.attr('data-' + data));
                    var id = makeid();
                    $responsive.attr('data-' + prefix + '-rid', id);
                    if ($responsive.data('responsive-' + prefix + '-rules')) {
                        $responsive.data('responsive-' + prefix + '-rules').remove();
                    }

                    var style = document.createElement('style');
                    style.type = 'text/css';
                    style.innerHTML = '@media screen ' + (('min' in azh.device_prefixes[prefix]) ? ' and (min-width: ' + azh.device_prefixes[prefix]['min'] + 'px)' : '') + (('max' in azh.device_prefixes[prefix]) ? ' and (max-width: ' + azh.device_prefixes[prefix]['max'] + 'px)' : '') + ' {[data-' + prefix + '-rid="' + id + '"] { ' + responsive_style + ' }}';
                    document.getElementsByTagName('head')[0].appendChild(style);
                    $responsive.data('responsive-' + prefix + '-rules', style);
                }
            });
        }
    };
    azh.parse_query_string = function (a) {
        if (a == "")
            return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p = a[i].split('=');
            if (p.length != 2)
                continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    };
    azh.set_url_argument = function (url, argument, value) {
        url = url.split('?');
        var args = {};
        if (url.length === 2) {
            args = azh.parse_query_string(url[1].split('&'));
        }

        var query = {};
        for (var arg in args) {
            if (arg === argument) {
                query[arg] = encodeURIComponent(value);
            } else {
                query[arg] = encodeURIComponent(args[arg]);
            }
        }
        if (!(argument in args)) {
            query[argument] = encodeURIComponent(value);
        }
        query = Object.entries(query).map(function callback(currentValue, index, array) {
            return currentValue[0] + '=' + currentValue[1];
        });
        if (url.length === 1) {
            url.push(query.join('&'));
        } else {
            url[1] = query.join('&');
        }
        if (url[1] !== '') {
            url = url.join('?');
        } else {
            url = url[0];
        }
        return url;
    };
    azh.get_url_argument = function (url, argument) {
        url = url.split('?');
        var args = {};
        if (url.length === 2) {
            args = azh.parse_query_string(url[1].split('&'));
        }
        if (argument in args) {
            return args[argument];
        }
        return false;
    };
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    var customize = ('azh' in $.QueryString && $.QueryString['azh'] == 'customize');
    window.azh.frontend_init = function ($wrapper) {
        function sticky() {
            $wrapper.find('[data-sticky-style], [data-sticky-class]').each(function () {
                var $sticky = $(this);
                if (!$sticky.data('top')) {
                    if ($sticky.offset().top < 0) {
                        $sticky.data('top', 0)
                    } else {
                        $sticky.data('top', $sticky.offset().top)
                    }
                }
                $body.imagesLoaded(function () {
                    //add class to container with "az-sticky" class or body
                    if ($sticky.data('sticky-class')) {
                        var $container = $body;
                        if ($sticky.closest('.az-sticky').length) {
                            $container = $sticky.closest('.az-sticky');
                        }
                        var interval = setInterval(function () {
                            if (!$container.hasClass($sticky.data('sticky-class'))) {
                                if ($sticky.offset().top < 0) {
                                    $sticky.data('top', 0);
                                } else {
                                    $sticky.data('top', $sticky.offset().top);
                                }
                                clearInterval(interval);
                            }
                        }, 100);
                    }
                    if ($sticky.data('namespace') === undefined) {
                        $sticky.data('namespace', makeid());
                    }
                    $window.off('scroll.' + $sticky.data('namespace')).on('scroll.' + $sticky.data('namespace'), function () {
                        if ($window.scrollTop() > $sticky.data('top')) {
                            //add class to container with "az-sticky" class or body
                            if ($sticky.data('sticky-class')) {
                                if ($sticky.closest('.az-sticky').length) {
                                    $sticky.closest('.az-sticky').addClass($sticky.data('sticky-class'));
                                } else {
                                    $body.addClass($sticky.data('sticky-class'));
                                }
                            }
                            //add styles to container/child with "az-sticky" class or self
                            if ($sticky.data('sticky-style')) {
                                if ($sticky.closest('.az-sticky').length) {
                                    $sticky.closest('.az-sticky').attr('style', $sticky.data('sticky-style'));
                                } else {
                                    if ($sticky.find('.az-sticky').length) {
                                        $sticky.find('.az-sticky').attr('style', $sticky.data('sticky-style'));
                                    } else {
                                        $sticky.attr('style', $sticky.data('sticky-style'));
                                    }
                                }
                            }
                        } else {
                            //add class to container with "az-sticky" class or body
                            if ($sticky.data('sticky-class')) {
                                if ($sticky.closest('.az-sticky').length) {
                                    $sticky.closest('.az-sticky').removeClass($sticky.data('sticky-class'));
                                } else {
                                    $body.removeClass($sticky.data('sticky-class'));
                                }
                            }
                            //add styles to container/child with "az-sticky" class or self
                            if ($sticky.data('sticky-style')) {
                                if ($sticky.closest('.az-sticky').length) {
                                    $sticky.closest('.az-sticky').attr('style', '');
                                } else {
                                    if ($sticky.find('.az-sticky').length) {
                                        $sticky.find('.az-sticky').attr('style', '');
                                    } else {
                                        $sticky.attr('style', '');
                                    }
                                }
                            }
                        }
                    });
                });
            });
            $wrapper.find('.az-children-height').each(function () {
                $(this).height($(this).children().height());
                $(this).off('azh-refresh').on('azh-refresh', function () {
                    $(this).height($(this).children().height());
                });
            });
        }
        function fill_entry($element, id) {
            function fill_text($field, text) {
                $field.contents().filter(function () {
                    return this.nodeType === 3;
                }).each(function () {
                    if ($.trim(this.textContent)) {
                        this.textContent = text;
                    }
                });
                $field.find('*').contents().filter(function () {
                    return this.nodeType === 3;
                }).each(function () {
                    if ($.trim(this.textContent)) {
                        this.textContent = text;
                    }
                });
            }
            function fill_image($field, url) {
                if ($field.is('img[src]')) {
                    $field.attr('src', url);
                } else {
                    $field.find('img[src]').attr('src', url);
                }
            }
            function fill_link($field, url) {
                if ($field.is('a[href]')) {
                    $field.attr('href', url);
                } else {
                    $field.find('a[href]').attr('href', url);
                }
            }
            function fill_video($field, url) {
                if ($field.is('iframe[src]')) {
                    $field.attr('src', url);
                } else {
                    $field.find('iframe[src]').attr('src', url);
                }
            }
            var $entry = $element.is('.az-entry-ajax') ? $element : false;
            if (!$entry) {
                $entry = $element.find('.az-entry-ajax').first();
                if ($entry.is('[data-class-from-post-meta]:not([data-class-from-post-meta=""])')) {
                    var meta = $entry.data('class-from-post-meta');
                    $entry.removeClass($entry.data('dynamic-class'));
                    $entry.addClass(azh.entries[id].meta[meta]);
                    $entry.data('dynamic-class', azh.entries[id].meta[meta]);
                }

                $entry.find('.az-title').each(function () {
                    fill_text($(this), azh.entries[id].post.post_title);
                });
                $entry.find('.az-content').each(function () {
                    fill_text($(this), azh.entries[id].post.post_content);
                });
                $entry.find('.az-thumbnail').each(function () {
                    fill_image($(this), azh.entries[id].post_thumbnail);
                });
                $entry.find('[data-meta-field]').each(function () {
                    fill_text($(this), azh.entries[id].meta[$(this).data('meta-field')]);
                });
                $entry.find('[data-file-meta-field]').each(function () {
                    fill_link($(this), azh.entries[id].meta[$(this).data('file-meta-field')]);
                });
                $entry.find('[data-video-meta-field]').each(function () {
                    fill_video($(this), azh.entries[id].meta[$(this).data('video-meta-field')]);
                });
                $entry.find('[data-image-meta-field]').each(function () {
                    fill_image($(this), azh.entries[id].meta[$(this).data('image-meta-field')]);
                });
            }
        }
        function entries_load() {
            //load if it visible or can be visible on hover/click which is currently visible
            window.azh.entries = $.extend({}, window.azh.entries);
            var ids = [];
            $wrapper.find('[data-fill-from-post]:visible').each(function () {
                var id = $(this).data('fill-from-post');
                if (!(id in window.azh.entries)) {
                    ids.push(id);
                }
            });
            if (ids.length) {
                $.post(azh.ajaxurl, {
                    'action': 'azh_get_posts',
                    'ids': ids
                }, function (data) {
                    if (data) {
                        var entries = JSON.parse(data);
                        for (var id in entries) {
                            azh.entries[id] = entries[id];
                        }
                    }
                });
            }
        }
        function magnific_gallery($wrapper) {
            if ('magnificPopup' in $.fn) {
                if ($wrapper.children().length === $wrapper.find('img, [style*="background-image"]').length) {
                    var gallery_items = $.makeArray($wrapper.find('img, [style*="background-image"]').map(function () {
                        var $this = $(this);
                        if ($this.attr('src')) {
                            return {src: $this.attr('src')};
                        }
                        if ($this.css('background-image') != 'none') {
                            return {src: $this.css('background-image').replace(/url\(|'|"|\)$/ig, "")};
                        }
                        return {};
                    }));
                    $wrapper.find('img, [style*="background-image"]').each(function () {
                        var $this = $(this);
                        if ($this.parentsUntil($wrapper).filter('a').length === 0 && $this.find('a').length === 0 || $this.parentsUntil($wrapper).filter('a').length && $this.parentsUntil($wrapper).filter('a').attr('href') == '/') {
                            if (gallery_items.length > 0) {
                                $this.css('cursor', 'zoom-in');
                            } else {
                                if ($this.attr('src')) {
                                    $this.css('cursor', 'zoom-in');
                                }
                                if ($this.css('background-image') != 'none') {
                                    $this.css('cursor', 'zoom-in');
                                }
                            }
                            $this.on('click', function () {
                                var $this = $(this);
                                if (gallery_items.length > 0) {
                                    $.magnificPopup.open({
                                        items: gallery_items,
                                        gallery: {
                                            enabled: true
                                        },
                                        type: 'image'
                                    }, $this.parentsUntil($wrapper).last().index());
                                } else {
                                    if ($this.attr('src')) {
                                        $.magnificPopup.open({
                                            items: {
                                                src: $this.attr('src')
                                            },
                                            type: 'image'
                                        });
                                    }
                                    if ($this.css('background-image') != 'none') {
                                        $.magnificPopup.open({
                                            items: {
                                                src: $this.css('background-image').replace(/url\(|'|"|\)$/ig, "")
                                            },
                                            type: 'image'
                                        });
                                    }
                                }
                                return false;
                            });
                        }
                    });

                }
            }
        }
        function swiper_slider($slider) {
            function swiper_get_params($slider) {
                var options = {
                    observer: customize,
                    simulateTouch: !customize,
                    parallax: true,
                    containerModifierClass: 'az-container-', // NEW
                    slideClass: 'az-slide',
                    slideBlankClass: 'az-slide-invisible-blank',
                    slideActiveClass: 'az-slide-active',
                    slideDuplicateActiveClass: 'az-slide-duplicate-active',
                    slideVisibleClass: 'az-slide-visible',
                    slideDuplicateClass: 'az-slide-duplicate',
                    slideNextClass: 'az-slide-next',
                    slideDuplicateNextClass: 'az-slide-duplicate-next',
                    slidePrevClass: 'az-slide-prev',
                    slideDuplicatePrevClass: 'az-slide-duplicate-prev',
                    wrapperClass: 'az-wrapper',
                    navigation: {
                        nextEl: '.az-button-next',
                        prevEl: '.az-button-prev',
                        disabledClass: 'az-button-disabled',
                        hiddenClass: 'az-button-hidden',
                        lockClass: 'az-button-lock',
                    },
                    pagination: {
                        el: '.az-pagination',
                        bulletClass: 'az-pagination-bullet',
                        bulletActiveClass: 'az-pagination-bullet-active',
                        modifierClass: 'az-pagination-', // NEW
                        currentClass: 'az-pagination-current',
                        totalClass: 'az-pagination-total',
                        hiddenClass: 'az-pagination-hidden',
                        progressbarFillClass: 'az-pagination-progressbar-fill',
                        clickableClass: 'az-pagination-clickable', // NEW
                        lockClass: 'az-pagination-lock',
                        clickable: true
                    }
                };
                var data_attributes = {
                    direction: 'horizontal',
                    spaceBetween: 0,
                    slidesPerView: 1,
                    centeredSlides: false,
                    speed: 300,
                    loop: false,
                    autoplay: {
                        delay: 5000
                    },
                    effect: 'slide', //"slide", "fade", "cube", "coverflow" or "flip"

                    freeMode: true,
                    watchSlidesVisibility: true,
                    watchSlidesProgress: true,

                    hashNavigation: false
                };
                for (var key in data_attributes) {
                    if (typeof data_attributes[key] === 'object') {
                        for (var k in data_attributes[key]) {
                            var value = $slider.attr('data-' + key + '-' + k);
                            if (typeof value !== typeof undefined) {
                                if (!options[key]) {
                                    options[key] = {};
                                }
                                $slider.removeData((key + '-' + k).toLocaleLowerCase());
                                options[key][k] = $slider.data((key + '-' + k).toLocaleLowerCase());
                            }
                        }
                    } else {
                        var value = $slider.attr('data-' + key);
                        if (typeof value !== typeof undefined) {
                            $slider.removeData(key.toLocaleLowerCase());
                            options[key] = $slider.data(key.toLocaleLowerCase());
                        }
                        for (var prefix in azh.device_prefixes) {
                            var value = $slider.attr('data-' + key + '-' + prefix);
                            if (typeof value !== typeof undefined) {
                                $slider.removeData(key.toLocaleLowerCase() + '-' + prefix);
                                if (!('breakpoints' in options)) {
                                    options['breakpoints'] = {};
                                }
                                var width = azh.device_prefixes[prefix].max;
                                if (typeof width !== typeof undefined) {
                                    if (!(width in options['breakpoints'])) {
                                        options['breakpoints'][width] = {};
                                    }
                                    options['breakpoints'][width][key] = $slider.data(key.toLocaleLowerCase() + '-' + prefix);
                                }
                            }
                        }
                    }
                }
                if (customize) {
                    options['autoplay'] = false;
                    options['loop'] = false;
                }
                var thumbs = $slider.data('thumbs');
                if (thumbs) {
                    options['thumbs'] = {
                        'swiper': thumbs,
                        slideThumbActiveClass: 'az-slide-active',
                        thumbsContainerClass: 'az-container-thumbs',
                    };
                }
                return options;
            }
            var swiper = new Swiper($slider.get(0), swiper_get_params($slider));
            $slider.data('swiper_get_params', swiper_get_params);
            $slider.data('swiper', swiper);
            $slider.on('azh-active', function (event) {
                var $slider = $(this);
                $slider.data('swiper').slideTo($slider.find('> .az-wrapper').children().index(event.target));
            });
            $slider.on('azh-refresh', function () {
                $(this).closest('.az-swiper').data('swiper').update();
            });
            if (!customize && !$slider.is('.az-swiper-thumbs')) {
                magnific_gallery($slider.find('> .az-wrapper'));
            }
            return swiper;
        }
        $window.trigger("az-frontend-before-init", {
            wrapper: $wrapper
        });
        fullWidthSection($wrapper);
        auto_rescale($wrapper);
        azh.refresh_hover_css_rules($wrapper);
        azh.refresh_placeholder_css_rules($wrapper);
        azh.refresh_focus_css_rules($wrapper);
        azh.refresh_responsive_css_rules($wrapper);
        azh.refresh_reveal_css_rules($wrapper);
        if (!customize) {
            $wrapper.find('.az-free-positioning.az-percentage[data-width][data-height]').each(function () {
                var $container = $(this);
                var container_width = $container.data('width');
                var container_height = $container.data('height');
                $container.children().each(function () {
                    if (this.style['left'].match(/^\d+px$/)) {
                        var left = parseInt(this.style['left'], 10);
                        this.style['left'] = (left / container_width * 100) + '%';
                    }
                    if (this.style['right'].match(/^\d+px$/)) {
                        var right = parseInt(this.style['right'], 10);
                        this.style['right'] = (right / container_width * 100) + '%';
                    }
                    if (this.style['top'].match(/^\d+px$/)) {
                        var top = parseInt(this.style['top'], 10);
                        this.style['top'] = (top / container_height * 100) + '%';
                    }
                    if (this.style['bottom'].match(/^\d+px$/)) {
                        var bottom = parseInt(this.style['bottom'], 10);
                        this.style['bottom'] = (bottom / container_height * 100) + '%';
                    }
                    if (this.style['width'].match(/^\d+px$/)) {
                        var width = parseInt(this.style['width'], 10);
                        this.style['width'] = (width / container_width * 100) + '%';
                    }
                    if (this.style['height'].match(/^\d+px$/)) {
                        var height = parseInt(this.style['height'], 10);
                        this.style['height'] = (height / container_height * 100) + '%';
                    }
                });
            });
        }
        if ('imagesLoaded' in $.fn) {
            if (document.documentElement.clientWidth > 768) {
                $wrapper.imagesLoaded(function () {
                    $wrapper.find('[data-parallax="true"]').each(function () {
                        var $this = $(this);
                        $this.css({
                            "background-size": "cover",
                            "background-repeat": "no-repeat",
                            "background-attachment": "fixed"
                        });
                        $this.parallax("50%", $this.data('parallax-speed') / 100);
                    });
                });
            }
        }
        if ('tabs' in $.fn) {
            $wrapper.find('.azexo-tabs').each(function () {
                var $this = $(this);
                if (!$this.tabs('instance')) {
                    $this.tabs();
                }
            });
        }
        if ('accordion' in $.fn) {
            $wrapper.find('.azexo-accordion').each(function () {
                var $this = $(this);
                if (!$this.accordion('instance')) {
                    $this.accordion({
                        header: ".accordion-section > h3",
                        autoHeight: false,
                        heightStyle: "content",
                        active: $this.data('active-section'),
                        collapsible: $this.data('collapsible'),
                        navigation: true,
                        animate: 200
                    });
                }
            });
        }
        $wrapper.find('.az-tabs-element').each(function () {
            var $tabs = $(this);
            if (!$tabs.data('az-tabs')) {
                $tabs.on('azh-refresh', function () {
                    $(this).find('> .az-titles [data-target^="#"]').off('click').on('click', function (event) {
                        var $this = $(this);
                        event.preventDefault();
                        $this.addClass("az-active");
                        $this.siblings().removeClass("az-active");
                        var tab = $this.attr("data-target");
                        $this.closest('.az-tabs-element').find('> .az-content > .az-item').not(tab).css("display", "none");
                        $(tab).stop().fadeIn(function () {
                            $(tab).find('.az-isotope-items').isotope('layout');
                        });
                    }).off('azh-clone').on('azh-clone', function () {
                        var $this = $(this);
                        setTimeout(function () {
                            $this.trigger('click');
                        });
                    });
                }).trigger('azh-refresh');
                $tabs.find('> .az-titles [data-target^="#"]:first-child').click();
                $tabs.data('az-tabs', true);
            }
        });
        $wrapper.find('.az-accordion-element').each(function () {
            var $accordion = $(this);
            if (!$accordion.data('az-accordion')) {
                $accordion.on('azh-refresh', function () {
                    $(this).find('> div > div:first-child').off('click').on('click', function (event) {
                        var $this = $(this);
                        if ($this.parent().is('.az-active')) {
                            $this.parent().removeClass("az-active").find('> div:last-child').slideUp();
                        } else {
                            $this.parent().addClass("az-active").find('> div:last-child').slideDown(function () {
                                $this.parent().addClass("az-active").find('> div:last-child').find('.az-isotope-items').isotope('layout');
                            });
                            $this.parent().siblings().removeClass("az-active").find('> div:last-child').slideUp();
                        }
                    });
                }).trigger('azh-refresh');
                $accordion.children().find('> div:last-child').slideUp(0);
                $accordion.find('> div.az-active').find('> div:last-child').slideDown(0, function () {
                    $accordion.find('> div.az-active').find('> div:last-child').find('.az-isotope-items').isotope('layout');
                });
                $accordion.data('az-accordion', true);
            }
        });
        $wrapper.find('.az-gmap').each(function () {
            function gmap_init() {
                if ($gmap.data('latitude') && $gmap.data('longitude') && !$gmap.data('map')) {
                    var styles = null;
                    if ($gmap.data('styles')) {
                        styles = $gmap.data('styles');
                        if (typeof styles === 'string') {
                            styles = JSON.parse(styles.replace(/'/g, '"'));
                        }
                    }
                    var map = new google.maps.Map($gmap.get(0), {
                        scrollwheel: false,
                        disableDefaultUI: true,
                        styles: styles,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });
                    var location = new google.maps.LatLng(parseFloat($gmap.data('latitude')), parseFloat($gmap.data('longitude')));
                    var marker = new google.maps.Marker({
                        position: location,
                        map: map,
                        icon: $gmap.data('marker') ? $gmap.data('marker') : null
                    });
                    map.refresh = function () {
                        map.setZoom($gmap.data('zoom') ? $gmap.data('zoom') : 14);
                        map.setCenter(location);
                        google.maps.event.trigger(map, 'resize');
                        if ($gmap.data('offset-x') !== undefined || $gmap.data('offset-y') !== undefined) {
                            var overlay = new google.maps.OverlayView();
                            overlay.draw = function () {
                            };
                            overlay.onAdd = function () {
                                var projection = this.getProjection();
                                var aPoint = projection.fromLatLngToContainerPixel(location);
                                if ($gmap.data('offset-x')) {
                                    aPoint.x = aPoint.x - parseFloat($gmap.data('offset-x'));
                                }
                                if ($gmap.data('offset-y')) {
                                    aPoint.y = aPoint.y - parseFloat($gmap.data('offset-y'));
                                }
                                map.setCenter(projection.fromContainerPixelToLatLng(aPoint));
                                google.maps.event.trigger(map, 'resize');
                            };
                            overlay.setMap(map);
                        }
                    };
                    map.refresh();
                    $gmap.data('map', map);
                }
            }
            var $gmap = $(this);
            if ('google' in window && 'maps' in google) {
                gmap_init();
            } else {
                if ($gmap.find('script').length) {
                    $gmap.find('script').get(0).onload = gmap_init;
                    var i = setInterval(function () {
                        if ('google' in window && 'maps' in google) {
                            gmap_init();
                            clearInterval(i);
                        }
                    }, 100);
                } else {
                    if ($gmap.data('gmap-api-key')) {
                        loadScript('//maps.googleapis.com/maps/api/js?key=' + $gmap.data('gmap-api-key'), function (path, status) {
                            gmap_init();
                        });
                    } else {
                        loadScript('//maps.googleapis.com/maps/api/js', function (path, status) {
                            gmap_init();
                        });
                    }
                }
            }
        });
        $wrapper.find('.az-like').each(function () {
            var $like = $(this);
            $like.contents().filter(function () {
                return this.nodeType === 3;
            }).each(function () {
                if ($.trim(this.textContent)) {
                    $like.data('count', this);
                }
            });
            $like.find('*').contents().filter(function () {
                return this.nodeType === 3;
            }).each(function () {
                if ($.trim(this.textContent)) {
                    $like.data('count', this);
                }
            });
            $like.on('click', function () {
                $like.addClass('az-ajax');
                $.ajax({
                    type: 'POST',
                    url: azh.ajaxurl,
                    data: {
                        action: 'azh_process_like',
                        id: $like.find('[data-id]').data('id'),
                        nonce: $like.find('[data-nonce]').data('nonce')
                    },
                    success: function (response) {
                        $like.removeClass('az-ajax');
                        if (response.indexOf('unliked') >= 0) {
                            $like.removeClass('az-liked');
                        } else {
                            $like.addClass('az-liked');
                        }
                        $like.data('count').textContent = parseInt(response, 10);
                    }
                });
                return false;
            });
        });
        $wrapper.find('.az-menu').each(function () {
            var $menu = $(this);
            $menu.find('.az-current').each(function () {
                var $current = $(this);
                do {
                    $current = $current.closest('[data-cloneable], [data-cloneable-inline]').parentsUntil('[data-cloneable], [data-cloneable-inline]').last();
                    $current.addClass('az-current-ancestor');
                } while ($menu.has($current).length);
            });
        });
        $wrapper.find('[data-content-width="container-boxed"]').each(function () {
            $(this).addClass('az-container');
        });
        //az-liked-azen-liked
        $wrapper.find('form').each(function () {
            var $form = $(this);
            $form.find('.az-enter-submit').on("keydown", function (event) {
                if (event.keyCode === 13) {
                    if ($form.find('[type="submit"]').length) {
                        $form.submit();
                    }
                }
            });
            $form.find('input, textarea').on('change', function () {
                var $this = $(this);
                if ($this.val() === '') {
                    $this.parent().removeClass('az-filled');
                } else {
                    $this.parent().addClass('az-filled');
                }
            });
            if ($form.is('[data-azh-form]')) {
                if (!customize) {
                    $form.find('.az-confirmation').hide();
                    if ($form.find('[type="file"]').length === 0) {
                        $form.find('[type="submit"]').on('click', function () {
                            function report_validity(form) {
                                var valid = true;
                                if ('reportValidity' in form) {
                                    valid = form.reportValidity();
                                } else {
                                    $(form).find('[name]').each(function () {
                                        var $this = $(this);
                                        $this.off('change.az-report-validity').on('change.az-report-validity', function () {
                                            $(this).removeClass('az-not-valid');
                                        });
                                        $this.removeClass('az-not-valid');
                                        if (!this.checkValidity()) {
                                            valid = false;
                                            $this.addClass('az-not-valid');
                                        }
                                    });
                                }
                                return valid;
                            }
                            var data = {
                                fields: {},
                                cancel: false
                            };
                            $form.trigger("azh-before-submit", data);
                            if (data.cancel) {
                                return false;
                            }
                            if ($form.find('.g-recaptcha').length) {
                                if (grecaptcha.getResponse().length === 0) {
                                    return false;
                                }
                            }
                            if (report_validity($form.get(0))) {
                                var $button = $(this);
                                $button.css({
                                    "pointer-events": "none",
                                    "opacity": "0.5"
                                });
                                data = data.fields;
                                $.map($form.serializeArray(), function (n, i) {
                                    if (n['name']) {
                                        if (data[n['name']]) {
                                            if (!$.isArray(data[n['name']])) {
                                                data[n['name']] = [data[n['name']]];
                                            }
                                            data[n['name']].push(n['value']);
                                        } else {
                                            data[n['name']] = n['value'];
                                        }
                                    }
                                });
                                data['action'] = 'azh_process_form';
                                data['post_id'] = azh.post_id;
                                var $azh_widget = $form.closest('[data-post-id]');
                                if ($azh_widget.length) {
                                    data['post_id'] = $azh_widget.attr('data-post-id');
                                }
                                data['nonce'] = $form.data('azh-form');
                                $.ajax({
                                    type: 'POST',
                                    url: azh.ajaxurl,
                                    data: data,
                                    success: function (response) {
                                        $button.css({
                                            "pointer-events": "",
                                            "opacity": ""
                                        });
                                        if (response) {
                                            var data = JSON.parse(response);
                                            $form.trigger("azh-after-submit", data);
                                            if ($form.find('.az-confirmation').length) {
                                                $form.find('.az-confirmation').show();
                                            } else {
                                                if (data.status === 'success') {
                                                    $form.trigger('reset');
                                                }
                                                if (data.status === 'redirect') {
                                                    window.location = data.url;
                                                } else {
                                                    if ($form.data(data.status + '-redirect')) {
                                                        window.location = $form.data(data.status + '-redirect');
                                                    }
                                                }
                                                if ($form.data(data.status)) {
                                                    alert($form.data(data.status));
                                                } else {
                                                    if (data.message) {
                                                        alert(data.message);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                            return false;
                        });
                    } else {
                        $form.attr('action', azh.ajaxurl + '?action=azh_process_form');
                        var post_id = azh.post_id;
                        var $azh_widget = $form.closest('[data-post-id]');
                        if ($azh_widget.length) {
                            post_id = $azh_widget.attr('data-post-id');
                        }
                        $('<input type="hidden" name="post_id" value="' + post_id + '">').appendTo($form);
                        $('<input type="hidden" name="nonce" value="' + $form.data('azh-form') + '">').appendTo($form);
                        if ($form.data('success-redirect')) {
                            $('<input type="hidden" name="success-redirect" value="' + $form.data('success-redirect') + '">').appendTo($form);
                        }
                        if ($form.data('error-redirect')) {
                            $('<input type="hidden" name="error-redirect" value="' + $form.data('error-redirect') + '">').appendTo($form);
                        }
                    }
                }
            }
        });
        $wrapper.find('.az-swiper-thumbs').each(function () {
            var swiper = swiper_slider($(this));
            $(this).parents().each(function () {
                var $parent = $(this);
                var $slides = $parent.find('.az-swiper');
                if ($slides.length) {
                    $slides.data('thumbs', swiper);
                    return false;
                }
            });
        });
        $wrapper.find('.az-swiper').each(function () {
            swiper_slider($(this));
        });
        if ('isotope' in $.fn) {
            $wrapper.find('.az-isotope-items').each(function () {
                function isotope_get_params($grid) {
                    var options = {
                    };
                    var data_attributes = {
                        layoutMode: 'masonry',
                        masonry: {
                            gutter: 30
                        }
                    };
                    for (var key in data_attributes) {
                        if (typeof data_attributes[key] === 'object') {
                            for (var k in data_attributes[key]) {
                                var value = $grid.attr('data-' + key + '-' + k);
                                if (typeof value !== typeof undefined) {
                                    if (!options[key]) {
                                        options[key] = {};
                                    }
                                    $grid.removeData((key + '-' + k).toLocaleLowerCase());
                                    options[key][k] = $grid.data((key + '-' + k).toLocaleLowerCase());
                                }
                            }
                        } else {
                            var value = $grid.attr('data-' + key);
                            if (typeof value !== typeof undefined) {
                                $grid.removeData(key.toLocaleLowerCase());
                                options[key] = $grid.data(key.toLocaleLowerCase());
                            }
                        }
                    }
                    return options;
                }
                function refresh_active_css_rules($filters) {
                    var active_style = set_styles_important($filters.attr('data-active'));
                    var id = $filters.attr('id');
                    if (!id) {
                        id = makeid();
                        $filters.attr('id', id);
                    }
                    make_css_rule('#' + id + ' [data-filter].az-active span', active_style);
                }
                var $grid = $(this);
                $grid.isotope(isotope_get_params($grid));
                $grid.imagesLoaded().progress(function () {
                    $grid.isotope('layout');
                });
                $grid.closest('.az-full-width').on("az-full-width", function () {
                    $grid.isotope('layout');
                });
                $grid.one('arrangeComplete', function () {
//                    $window.trigger('resize');
                });
                $grid.on('azh-refresh', function () {
                    $(this).isotope('destroy').isotope(isotope_get_params($(this))).isotope('layout');
                });
                var $filters = $grid.closest('.az-isotope').find('.az-isotope-filters').first();
//                var $filters = false;
//                var filters_closeness = false;
//                $('.az-isotope-filters').each(function () {
//                    var parent = $grid.parents().has(this).first();
//                    if ($filters === false) {
//                        $filters = $(this);
//                        filters_closeness = $grid.parents().index(parent);
//                    } else {
//                        if (filters_closeness > $grid.parents().index(parent)) {
//                            $filters = $(this);
//                            filters_closeness = $grid.parents().index(parent);
//                        }
//                    }
//                });
                if ($filters && $filters.length) {
                    $filters.on('azh-refresh', function () {
                        $(this).data('grid').isotope({
                            filter: '*'
                        }).isotope('layout');
                    });
                    if ($filters.is('[data-active]')) {
                        refresh_active_css_rules($filters)
                    }
                    $filters.data('refresh_active_css_rules', refresh_active_css_rules);
                    $filters.data('grid', $grid);
                    $filters.find('[data-filter]').on('click', function () {
                        var $this = $(this);
                        var $filters = $this.closest('.az-isotope-filters');
                        var $grid = $filters.data('grid');
                        $grid.isotope({filter: $this.attr('data-filter')});
                        $filters.find('[data-filter].az-active').removeClass('az-active');
                        $this.addClass('az-active');
                        return false;
                    });
                    $filters.find('[data-filter].az-active').trigger('click');
                }
                if (!customize) {
                    magnific_gallery($grid);
                }
            });
        }
        $wrapper.find('.az-hotspot > input[type="checkbox"]').each(function () {
            var $checkbox = $(this);
            var $map = $checkbox.closest('.az-free-positioning');
            var all_showed = true;
            $map.find('.az-hotspot > input[type="checkbox"]').each(function () {
                if (!$(this).prop('checked')) {
                    all_showed = false;
                    return false;
                }
            });
            if (!all_showed && !customize) {
                $checkbox.on('change', function () {
                    var $checkbox = $(this);
                    if ($checkbox.prop('checked')) {
                        $map.find('.az-hotspot > input[type="checkbox"]').not($checkbox).prop('checked', false);
                    }
                });
            }
        });
        if (!customize) {
            $wrapper.find('.az-hotspot > .az-wrapper > .az-lines > .az-line > .az-line > .az-dot').on('mouseenter', function () {
                var $hotspot = $(this).closest('.az-hotspot');
                $hotspot.find('> input[type="checkbox"]').prop('checked', true).trigger('change');
            });
        }
        if (customize) {
            $wrapper.find('.az-image-map').each(function () {
                var $map = $(this);
                $map.find('.az-polygone[data-id]').each(function () {
                    var $section_or_element = $(this).closest('[data-section].azh-controls, [data-element].azh-controls');
                    $section_or_element.on('azh-show-utility', function (event) {
                        var $element = $(this);
                        if ($element) {
                            var $polygone = $element.find('.az-polygone[data-id]');
                            var hash = $polygone.attr('data-id');
                            var $map = $polygone.closest('.az-image-map');
                            $map.find('.az-polygone.az-active').removeClass('az-active');
                            $polygone.addClass('az-active');
                            $map.find('.az-polygone[data-id]').each(function () {
                                $map.find($(this).attr('data-id')).hide();
                            });
                            $map.find(hash).stop().fadeIn();
                        }
                    });
                }).first().trigger('azh-show-utility');
                $map.find('.az-polygone[data-hover]').each(function () {
                    var $svg = $(this);
                    var hover_style = set_styles_important($svg.attr('data-hover'));
                    var id = makeid();
                    $svg.attr('data-aid', id);
                    if ($svg.data('active-rules')) {
                        $svg.data('active-rules').remove();
                    }
                    $svg.data('active-rules', make_css_rule('[data-aid="' + id + '"].az-active > svg > *, [data-aid="' + id + '"].az-current > svg > *', hover_style));
                });
            });
        } else {
            $wrapper.find('.az-image-map').each(function () {
                var $map = $(this);
                $map.find('.az-polygone').each(function () {
                    $(this).closest('[data-element]').addClass('az-disable-pointer-events');
                });
                $map.find('.az-polygone[data-id] svg polygon').on('mouseenter', function () {
                    var $polygone = $(this).closest('.az-polygone');
                    var $map = $polygone.closest('.az-image-map');
                    if (!$map.find('.az-polygone.az-current').length) {
                        $map.find('.az-polygone.az-active').removeClass('az-active');
                        $polygone.addClass('az-active');
                        var hash = $polygone.attr('data-id');
                        $map.find('.az-polygone[data-id]').each(function () {
                            $map.find($(this).attr('data-id')).hide();
                        });
                        $map.find(hash).stop().fadeIn();
                    }
                }).first().trigger('mouseenter').closest('.az-svg').addClass('az-active');
                $map.find('.az-polygone[data-id] svg polygon').on('click', function () {
                    var $polygone = $(this).closest('.az-polygone');
                    var $map = $polygone.closest('.az-image-map');

                    $map.find('.az-polygone.az-active').removeClass('az-active');
                    $map.find('.az-polygone.az-current').removeClass('az-current');
                    $polygone.addClass('az-active').addClass('az-current');

                    var hash = $polygone.attr('data-id');
                    $map.find('.az-polygone[data-id]').each(function () {
                        $map.find($(this).attr('data-id')).hide();
                    });
                    $map.find(hash).stop().fadeIn();
                });
                $map.find('.az-polygone[data-hover]').each(function () {
                    var $svg = $(this);
                    var hover_style = set_styles_important($svg.attr('data-hover'));
                    var id = makeid();
                    $svg.attr('data-aid', id);
                    if ($svg.data('active-rules')) {
                        $svg.data('active-rules').remove();
                    }
                    $svg.data('active-rules', make_css_rule('[data-aid="' + id + '"].az-active > svg > *, [data-aid="' + id + '"].az-current > svg > *', hover_style));
                });
            });
            $wrapper.find('.az-polygone').each(function () {
                var $this = $(this);
                $this.closest('[data-element]').addClass('az-disable-pointer-events');
                $this.find('svg > *').addClass('az-enable-pointer-events');
            });
            $wrapper.find('.az-polygone[data-click-url]:not([data-click-url=""]):not([data-click-url^="#"])').on('click', function (event) {
                $(this).removeClass('az-hover az-current');
                if (event.which === 1) {
                    window.location.href = $(this).attr('data-click-url');
                }
                if (event.which === 2) {
                    var win = window.open($(this).attr('data-click-url'), '_blank');
                    if (win) {
                        win.focus();
                    } else {
                        window.location.href = $(this).attr('data-click-url');
                    }
                }
                return false;
            });
        }
        $wrapper.find('.az-svg[data-hover] > svg > *').on('mouseenter', function () {
            $(this).closest('.az-svg').addClass('az-hover');
        }).on('mouseleave', function () {
            $(this).closest('.az-svg').removeClass('az-hover');
        });

        $wrapper.find('input[data-mask]:not([data-mask=""])').each(function () {
            $(this).mask($(this).data('mask'));
        });
        if ($wrapper.find('.g-recaptcha[class*="az-"]').length) {
            if ('grecaptcha' in window) {
                grecaptcha.render($wrapper.find('.g-recaptcha[class*="az-"]').get(0), {
                    'sitekey': $wrapper.find('.g-recaptcha[class*="az-"]').data('sitekey')
                });
            } else {
                loadScript('//www.google.com/recaptcha/api.js', function (path, status) {
                });
            }
        }
        $wrapper.find('form.az-search').each(function () {
            $(this).attr('action', azh.site_url);
        });
        $wrapper.find('[data-element="general/image.htm"] a[href*="="], [data-element="general/icon.htm"] a[href*="="]').on('click', function () {
            var pair = $(this).attr('href').split('=');
            if (pair.length === 2) {
                var $input = $wrapper.find('[name="' + pair[0] + '"][value="' + pair[1] + '"]');
                if ($input.length) {
                    if ($input.attr('type') == 'checkbox') {
                        var checked = $input.prop('checked');
                        $input.prop('checked', !checked).trigger("change");
                    } else {
                        $input.prop('checked', true).trigger("change");
                    }
                    return false;
                } else {
                    var $select = $wrapper.find('[name="' + pair[0] + '"]');
                    if ($select.length) {
                        var $option = $select.find('[value="' + pair[1] + '"]');
                        if ($option.length) {
                            if ($select.is('[multiple]')) {
                                var selected = $option.prop('selected');
                                $option.prop('selected', !checked).trigger("change");
                            } else {
                                $option.prop('selected', true).trigger("change");
                            }
                            return false;
                        }
                    }
                }
            }
        });
        $wrapper.find('.az-overlay').addBack().filter('.az-overlay').each(function () {
            var $container = $(this);
            if ($container.find('> [data-depth]:not([data-depth=""])').length) {
                $container.find('> [data-depth=""], > :not([data-depth])').attr('data-depth', '0');
                $container.children().each(function () {
                    var shift = '-' + (parseFloat($(this).data('depth')) * 10) + '%';
                    this.style.setProperty("top", shift, "important");
                    this.style.setProperty("bottom", shift, "important");
                    this.style.setProperty("left", shift, "important");
                    this.style.setProperty("right", shift, "important");
                }).addClass('layer');
                if ('Parallax' in window) {
                    var parallax = new Parallax($container.get(0));
                }
            }
            if ($('.az-overlay [data-rellax-speed]:not([data-rellax-speed=""])').length) {
                if ('Rellax' in window) {
                    var rellax = new Rellax('.az-overlay [data-rellax-speed]:not([data-rellax-speed=""])');
                }
            }

        });
        $wrapper.find('[data-background-video]').addBack().filter('[data-background-video]').each(function () {
            function calcVideoSize($backgroundVideoContainer) {
                var containerWidth = $backgroundVideoContainer.outerWidth(),
                        containerHeight = $backgroundVideoContainer.outerHeight(),
                        aspectRatioSetting = '16:9', //TEMP
                        aspectRatioArray = aspectRatioSetting.split(':'),
                        aspectRatio = aspectRatioArray[ 0 ] / aspectRatioArray[ 1 ],
                        ratioWidth = containerWidth / aspectRatio,
                        ratioHeight = containerHeight * aspectRatio,
                        isWidthFixed = containerWidth / containerHeight > aspectRatio;
                return {
                    width: isWidthFixed ? containerWidth : ratioHeight,
                    height: isWidthFixed ? ratioWidth : containerHeight
                };
            }
            function youtube_parser(url) {
                var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
                var match = url.match(regExp);
                return (match && match[7].length == 11) ? match[7] : false;
            }
            var $this = $(this);
            if ($this.is('[data-background-type]')) {
                if ($this.data('background-type') !== 'video') {
                    $this.find('> .az-background-video').remove();
                    return;
                }
            }
            var url = $this.data('background-video');
            if (url) {
                if (url.indexOf('youtube') >= 0) {
                    if (!$this.find('> .az-background-video').length) {
                        url = url.replace('https:', '').replace('http:', '');
                        var id = youtube_parser(url);
                        if (id) {
                            url = '//www.youtube.com/embed/' + id;
                            url = azh.set_url_argument(url, 'autoplay', '1');
                            url = azh.set_url_argument(url, 'rel', '0');
                            url = azh.set_url_argument(url, 'controls', '0');
                            url = azh.set_url_argument(url, 'showinfo', '0');
                            url = azh.set_url_argument(url, 'loop', '1');
                            url = azh.set_url_argument(url, 'playlist', id);
                            url = azh.set_url_argument(url, 'mute', '1');
                            var $background = $('<div class="az-background-video"></div>').prependTo($this);
                            var $iframe = $('<iframe src="' + url + '"></iframe>').prependTo($background).on('load', function () {

                            });
                            var size = calcVideoSize($this);
                            $iframe.width(size.width).height(size.height);
                        }
                    }
                }
                if (url.indexOf('.mp4') >= 0) {
                    var $background = $('<div class="az-background-video"></div>').prependTo($this);
                    var $video = $('<video autoplay loop muted><source src="' + url + '"></video>').prependTo($background).on('load', function () {
                    });
                    var size = calcVideoSize($this);
                    $video.width(size.width).height(size.height);
                }
            } else {
                $this.find('> .az-background-video').remove();
            }
        });
        $wrapper.find('.az-modal').each(function () {
            var $this = $(this);
            if (!customize) {
                $this.children().first().on('click', function () {
                    if ($this.is('.az-active')) {
                        $this.removeClass('az-active');
                    } else {
                        $this.addClass('az-active');
                    }
                    $this.trigger('az-change');
                    return false;
                }).find('*').off('click');
            }
            $this.children().last().on('click', function (event) {
                if ($(this).children().first().is(event.target)) {
                    if ($this.is('.az-active')) {
                        $this.removeClass('az-active');
                        $this.trigger('az-change');
                    }
                    return false;
                }
            });
            $window.off("keydown.az-modal").on("keydown.az-modal", function (event) {
                if (event.keyCode === 27) {
                    $wrapper.find('.az-modal.az-active').removeClass('az-active');
                    $this.trigger('az-change');
                }
            });
        });
        $wrapper.find('.az-video-wrapper .az-video-overlay').on('click', function () {
            var $this = $(this);
            if($this.closest('.az-video-wrapper').find('video').length) {
                $this.closest('.az-video-wrapper').find('video').get(0).play();
            }            
            $this.removeClass('az-show');
        });
        $('.az-search-form').each(function () {
            var $form = $(this);
            $form.find('input.az-search-phrase').attr('placeholder', $form.find('[type="submit"]').val()).on('keydown', function (event) {
                if (event.keyCode === 13) {
                    $form.find('[type="submit"]').trigger('click');
                }
            });
        });
        $window.off('resize.az-sticky').on('resize.az-sticky', sticky);
        sticky();
        $wrapper.find('a[href*="#"].az-roll, .az-roll a[href*="#"]').off('click').on('click', function (e) {
            if (this.href.split('#')[0] === '' || window.location.href.split('#')[0] === this.href.split('#')[0]) {
                e.preventDefault();
                var hash = this.hash;
                var $e = $(hash);
                if (!$e.length) {
                    $e = $('[data-section="' + hash.replace('#', '') + '"]');
                }
                if (!$e.length) {
                    $e = $('[data-element="' + hash.replace('#', '') + '"]');
                }
                if ($e.length) {
                    $('html, body').stop().animate({
                        'scrollTop': $e.offset().top
                    }, 2000);
                }
            }
        });
        $wrapper.find('[data-roll]').off('click').on('click', function (e) {
            var selector = $(this).data('roll');
            $('html, body').stop().animate({
                'scrollTop': $(selector).offset().top
            }, 2000);
            return false;
        });
        $wrapper.find('.az-back').off('click').on('click', function (e) {
            window.history.back();
            return false;
        });

        if ('waypoint' in $.fn) {
            $wrapper.find('.az-lazy-load').each(function () {
                var $image = $(this);
                var waypoint_handler = function (direction) {
                    $('<img src="' + $image.data('src') + '">').load(function () {
                        if ($image.prop('tagName') === 'IMG') {
                            $image.attr('src', $image.data('src'));
                        } else {
                            $image.css('background-image', 'url("' + $image.data('src') + '")');
                        }
                        $image.addClass('loaded');
                    });
                };
                $image.waypoint(waypoint_handler, {offset: '100%', triggerOnce: true});
                $image.data('waypoint_handler', waypoint_handler);
            });
            $wrapper.find('[data-reveal], .az-reveal-trigger').removeClass('az-visible');
            $wrapper.find('[data-reveal]').each(function () {
                var trigger_reveal_handler = function (direction) {
                    setTimeout(function () {
                        $trigger.addClass('az-visible');
                    });
                };
                var reveal_handler = function (direction) {
                    setTimeout(function () {
                        $reveal.addClass('az-visible');
                    });
                };
                var $reveal = $(this);
                var $trigger = $reveal.closest('.az-reveal-trigger');
                if ($trigger.length) {
                    if (!$trigger.data('reveal_handler')) {
                        $trigger.waypoint(trigger_reveal_handler, {offset: 'bottom-in-view', triggerOnce: true});
                        $trigger.data('reveal_handler', trigger_reveal_handler);
                    }
                } else {
                    if (!$reveal.data('reveal_handler')) {
                        $reveal.waypoint(reveal_handler, {offset: 'bottom-in-view', triggerOnce: true});
                        $reveal.data('reveal_handler', reveal_handler);
                    }
                }
            });
        }
        if ('countdown' in $.fn) {
            $wrapper.find('.az-countdown').each(function () {
                var $countdown = $(this);
                if ($countdown.data('countdownInstance') === undefined) {
                    $countdown.countdown($countdown.data('time'), function (event) {
                        $countdown.find('.az-days .az-count').text(event.offset.totalDays);
                        $countdown.find('.az-hours .az-count').text(event.offset.hours);
                        $countdown.find('.az-minutes .az-count').text(event.offset.minutes);
                        $countdown.find('.az-seconds .az-count').text(event.offset.seconds);
                    });
                }
            });
        }
        if (!customize) {
            if ('magnificPopup' in $.fn) {
                $wrapper.find('a.az-magnific-popup').each(function () {
                    var $this = $(this);
                    $("<img>", {
                        src: $this.attr('href'),
                        error: function () {
                            $this.magnificPopup({
                                type: 'iframe',
                                removalDelay: 300,
                                mainClass: 'mfp-fade',
                                overflowY: 'scroll',
                                closeMarkup: '<div title="%title%" type="button" class="mfp-close">&#215;</div>',
                                iframe: {
                                    markup: '<div class="mfp-iframe-scaler">' +
                                            '<div class="mfp-close"></div>' +
                                            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                                            '</div>',
                                    patterns: {
                                        youtube: {
                                            index: 'youtube.com/',
                                            id: 'v=',
                                            src: '//www.youtube.com/embed/%id%?autoplay=1'
                                        },
                                        vimeo: {
                                            index: 'vimeo.com/',
                                            id: '/',
                                            src: '//player.vimeo.com/video/%id%?autoplay=1'
                                        },
                                        gmaps: {
                                            index: '//maps.google.',
                                            src: '%id%&output=embed'
                                        }
                                    },
                                    srcAction: 'iframe_src'
                                }
                            });
                        },
                        load: function () {
                            $this.magnificPopup({
                                type: 'image',
                                removalDelay: 300,
                                mainClass: 'mfp-fade',
                                overflowY: 'scroll',
                                closeMarkup: '<div title="%title%" type="button" class="mfp-close">&#215;</div>'
                            });
                        }
                    });
                });
                $wrapper.find('.az-gallery').each(function () {
                    var $gallery = $(this);
                    $gallery.find('.az-gallery-item a').on('click', function (event) {
                        event.preventDefault();
                    });
                    $gallery.find('.az-gallery-item').on('click', function () {
                        var gallery_items = $.makeArray($gallery.find('.az-gallery-item').map(function () {
                            return {src: $(this).find('img').attr('src')};
                        }));
                        if (gallery_items.length > 0) {
                            $.magnificPopup.open({
                                items: gallery_items,
                                gallery: {
                                    enabled: true
                                },
                                type: 'image'
                            }, $(this).closest('.az-gallery-item').index());
                        } else {
                            $.magnificPopup.open({
                                items: {
                                    src: $(this).find('img').attr('src')
                                },
                                type: 'image'
                            });
                        }
                    });
                });
            }
            $wrapper.find('.az-hover-overlay').each(function () {
                var $this = $(this);
                $this.on('mouseenter', function () {
                    $this.addClass('az-hover');
                }).on('mouseleave', function () {
                    $this.removeClass('az-hover');
                }).children().last();
            });
            $wrapper.find('[data-click-trigger]:not([data-click-trigger=""])').each(function () {
                var $trigger_wrapper = $(this);
                var element = $trigger_wrapper.data('click-trigger');
                var $trigger = $trigger_wrapper;
                if ($trigger.is('.az-svg')) {
                    $trigger = $trigger.find('> svg > *');
                }
                $trigger.on('click', function () {
                    var $element = $('[data-element="' + element + '"]');
                    if ($element.is(':visible')) {
                        $element.hide();
                        $trigger_wrapper.removeClass('az-active');
                    } else {
                        if ($trigger_wrapper.is('[data-fill-from-post]:not([data-fill-from-post=""])')) {
                            fill_entry($element, $trigger_wrapper.data('fill-from-post'));
                        }
                        $element.show();
                        $trigger_wrapper.addClass('az-active');
                        entries_load();
                    }
                    return false;
                });
            });
            $wrapper.find('[data-hover-trigger]:not([data-hover-trigger=""])').each(function () {
                var $trigger_wrapper = $(this);
                var element = $trigger_wrapper.data('hover-trigger');
                var $trigger = $trigger_wrapper;
                if ($trigger.is('.az-svg')) {
                    $trigger = $trigger.find('> svg > *');
                }
                $trigger.on('mouseenter', function () {
                    var $element = $('[data-element="' + element + '"]');
                    if ($trigger_wrapper.is('[data-fill-from-post]:not([data-fill-from-post=""])')) {
                        fill_entry($element, $trigger_wrapper.data('fill-from-post'));
                    }
                    $element.show();
                }).on('mouseleave', function () {
                    $('[data-element="' + element + '"]').hide();
                });
            });
        }
        entries_load();
        $window.trigger("az-frontend-init", {
            wrapper: $wrapper
        });
        $window.trigger("az-frontend-after-init", {
            wrapper: $wrapper
        });
    };
    $(function () {
        var initial_width = document.documentElement.clientWidth;
        if ('_' in window && 'throttle' in _) {
            $window.off("resize.az-fullWidthSection").on("resize.az-fullWidthSection", _.throttle(function () {
                if (initial_width !== document.documentElement.clientWidth) {
                    initial_width = document.documentElement.clientWidth;
                    var scrollTop = $window.scrollTop();
                    fullWidthSection($body);
                    auto_rescale($body);
                    $window.scrollTop(scrollTop);
                }
            }, 1000));
        } else {
            $window.off("resize.az-fullWidthSection").on("resize.az-fullWidthSection", function () {
                if (initial_width !== document.documentElement.clientWidth) {
                    initial_width = document.documentElement.clientWidth;
                    var scrollTop = $window.scrollTop();
                    fullWidthSection($body);
                    auto_rescale($body);
                    $window.scrollTop(scrollTop);
                }
            });
        }
        $body = $('body');
        azh.frontend_init($body);
        if (!customize) {
            if (window.location.hash) {
                var hash = window.location.hash;
                var $e = $(hash);
                if (!$e.length) {
                    $e = $('[data-section="' + hash.replace('#', '') + '"]');
                }
                if (!$e.length) {
                    $e = $('[data-element="' + hash.replace('#', '') + '"]');
                }
                if ($e.length) {
                    $('html, body').stop().animate({
                        'scrollTop': $e.offset().top
                    }, 2000);
                }
            }
            if (document.documentElement.clientWidth > 768 && !('azh' in $.QueryString && $.QueryString['azh'] === 'fullpage')) {
                if (typeof scrollReveal === 'function') {
                    window.scrollReveal = new scrollReveal();
                }
            }
        }
        $body.find('.azh-content-wrapper').addClass('az-enable-transitions');
    });
})(jQuery);