!function($) {
    "use strict";
    var wp_shortcode = {
        // ### Find the next matching shortcode
        //
        // Given a shortcode `tag`, a block of `text`, and an optional starting
        // `index`, returns the next matching shortcode or `undefined`.
        //
        // Shortcodes are formatted as an object that contains the match
        // `content`, the matching `index`, and the parsed `shortcode` object.
        next: function(tag, text, index) {
            var re = wp_shortcode.regexp(tag),
                    match, result;

            re.lastIndex = index || 0;
            match = re.exec(text);

            if (!match) {
                return;
            }

            // If we matched an escaped shortcode, try again.
            if ('[' === match[1] && ']' === match[7]) {
                return wp_shortcode.next(tag, text, re.lastIndex);
            }

            result = {
                index: match.index,
                content: match[0],
                shortcode: wp_shortcode.fromMatch(match)
            };

            // If we matched a leading `[`, strip it from the match
            // and increment the index accordingly.
            if (match[1]) {
                result.content = result.content.slice(1);
                result.index++;
            }

            // If we matched a trailing `]`, strip it from the match.
            if (match[7]) {
                result.content = result.content.slice(0, -1);
            }

            return result;
        },
        // ### Replace matching shortcodes in a block of text
        //
        // Accepts a shortcode `tag`, content `text` to scan, and a `callback`
        // to process the shortcode matches and return a replacement string.
        // Returns the `text` with all shortcodes replaced.
        //
        // Shortcode matches are objects that contain the shortcode `tag`,
        // a shortcode `attrs` object, the `content` between shortcode tags,
        // and a boolean flag to indicate if the match was a `single` tag.
        replace: function(tag, text, callback) {
            return text.replace(wp_shortcode.regexp(tag), function(match, left, tag, attrs, slash, content, closing, right) {
                // If both extra brackets exist, the shortcode has been
                // properly escaped.
                if (left === '[' && right === ']') {
                    return match;
                }

                // Create the match object and pass it through the callback.
                var result = callback(wp_shortcode.fromMatch(arguments));

                // Make sure to return any of the extra brackets if they
                // weren't used to escape the shortcode.
                return result ? left + result + right : match;
            });
        },
        // ### Generate a string from shortcode parameters
        //
        // Creates a `wp_shortcode` instance and returns a string.
        //
        // Accepts the same `options` as the `wp_shortcode()` constructor,
        // containing a `tag` string, a string or object of `attrs`, a boolean
        // indicating whether to format the shortcode using a `single` tag, and a
        // `content` string.
        string: function(options) {
            return new wp_shortcode(options).string();
        },
        // ### Generate a RegExp to identify a shortcode
        //
        // The base regex is functionally equivalent to the one found in
        // `get_shortcode_regex()` in `wp-includes/shortcodes.php`.
        //
        // Capture groups:
        //
        // 1. An extra `[` to allow for escaping shortcodes with double `[[]]`
        // 2. The shortcode name
        // 3. The shortcode argument list
        // 4. The self closing `/`
        // 5. The content of a shortcode when it wraps some content.
        // 6. The closing tag.
        // 7. An extra `]` to allow for escaping shortcodes with double `[[]]`
        regexp: _.memoize(function(tag) {
            return new RegExp('\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)', 'g');
        }),
        // ### Parse shortcode attributes
        //
        // Shortcodes accept many types of attributes. These can chiefly be
        // divided into named and numeric attributes:
        //
        // Named attributes are assigned on a key/value basis, while numeric
        // attributes are treated as an array.
        //
        // Named attributes can be formatted as either `name="value"`,
        // `name='value'`, or `name=value`. Numeric attributes can be formatted
        // as `"value"` or just `value`.
        attrs: _.memoize(function(text) {
            var named = {},
                    numeric = [],
                    pattern, match;

            // This regular expression is reused from `shortcode_parse_atts()`
            // in `wp-includes/shortcodes.php`.
            //
            // Capture groups:
            //
            // 1. An attribute name, that corresponds to...
            // 2. a value in double quotes.
            // 3. An attribute name, that corresponds to...
            // 4. a value in single quotes.
            // 5. An attribute name, that corresponds to...
            // 6. an unquoted value.
            // 7. A numeric attribute in double quotes.
            // 8. An unquoted numeric attribute.
            pattern = /([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*'([^']*)'(?:\s|$)|([\w-]+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/g;

            // Map zero-width spaces to actual spaces.
            text = text.replace(/[\u00a0\u200b]/g, ' ');

            // Match and normalize attributes.
            while ((match = pattern.exec(text))) {
                if (match[1]) {
                    named[ match[1].toLowerCase() ] = match[2];
                } else if (match[3]) {
                    named[ match[3].toLowerCase() ] = match[4];
                } else if (match[5]) {
                    named[ match[5].toLowerCase() ] = match[6];
                } else if (match[7]) {
                    numeric.push(match[7]);
                } else if (match[8]) {
                    numeric.push(match[8]);
                }
            }

            return {
                named: named,
                numeric: numeric
            };
        }),
        // ### Generate a Shortcode Object from a RegExp match
        // Accepts a `match` object from calling `regexp.exec()` on a `RegExp`
        // generated by `wp_shortcode.regexp()`. `match` can also be set to the
        // `arguments` from a callback passed to `regexp.replace()`.
        fromMatch: function(match) {
            var type;

            if (match[4]) {
                type = 'self-closing';
            } else if (match[6]) {
                type = 'closed';
            } else {
                type = 'single';
            }

            return new wp_shortcode({
                tag: match[2],
                attrs: match[3],
                type: type,
                content: match[5]
            });
        }
    };
    // Shortcode Objects
    // -----------------
    //
    // Shortcode objects are generated automatically when using the main
    // `wp_shortcode` methods: `next()`, `replace()`, and `string()`.
    //
    // To access a raw representation of a shortcode, pass an `options` object,
    // containing a `tag` string, a string or object of `attrs`, a string
    // indicating the `type` of the shortcode ('single', 'self-closing', or
    // 'closed'), and a `content` string.
    wp_shortcode = _.extend(function(options) {
        _.extend(this, _.pick(options || {}, 'tag', 'attrs', 'type', 'content'));

        var attrs = this.attrs;

        // Ensure we have a correctly formatted `attrs` object.
        this.attrs = {
            named: {},
            numeric: []
        };

        if (!attrs) {
            return;
        }

        // Parse a string of attributes.
        if (_.isString(attrs)) {
            this.attrs = wp_shortcode.attrs(attrs);

            // Identify a correctly formatted `attrs` object.
        } else if (_.isEqual(_.keys(attrs), ['named', 'numeric'])) {
            this.attrs = attrs;

            // Handle a flat object of attributes.
        } else {
            _.each(options.attrs, function(value, key) {
                this.set(key, value);
            }, this);
        }
    }, wp_shortcode);
    _.extend(wp_shortcode.prototype, {
        // ### Get a shortcode attribute
        //
        // Automatically detects whether `attr` is named or numeric and routes
        // it accordingly.
        get: function(attr) {
            return this.attrs[ _.isNumber(attr) ? 'numeric' : 'named' ][ attr ];
        },
        // ### Set a shortcode attribute
        //
        // Automatically detects whether `attr` is named or numeric and routes
        // it accordingly.
        set: function(attr, value) {
            this.attrs[ _.isNumber(attr) ? 'numeric' : 'named' ][ attr ] = value;
            return this;
        },
        // ### Transform the shortcode match into a string
        string: function() {
            var text = '[' + this.tag;

            _.each(this.attrs.numeric, function(value) {
                if (/\s/.test(value)) {
                    text += ' "' + value + '"';
                } else {
                    text += ' ' + value;
                }
            });

            _.each(this.attrs.named, function(value, name) {
                text += ' ' + name + '="' + value + '"';
            });

            // If the tag is marked as `single` or `self-closing`, close the
            // tag and ignore any additional content.
            if ('single' === this.type) {
                return text + ']';
            } else if ('self-closing' === this.type) {
                return text + ' /]';
            }

            // Complete the opening tag.
            text += ']';

            if (this.content) {
                text += this.content;
            }

            // Add the closing tag.
            return text + '[/' + this.tag + ']';
        }
    });
    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    function rgb2hex(rgb) {
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);
    }
    function hex2rgb(hex) {
        if (hex.lastIndexOf('#') > -1) {
            hex = hex.replace(/#/, '0x');
        } else {
            hex = '0x' + hex;
        }
        var r = hex >> 16;
        var g = (hex & 0x00FF00) >> 8;
        var b = hex & 0x0000FF;
        return [r, g, b];
    }
    function default_get_code() {
        if ($(this).data('get_code')) {
            return $(this).data('get_code').call(this);
        } else {
            var code = '';
            $(this).children().each(function() {
                code = code + default_get_code.call(this);
            });
            return code;
        }
    }
    function htmlDecode(value) {
        return $("<textarea/>").html(value).text();
    }
    function htmlEncode(value) {
        return $('<textarea/>').text(value).html();
    }
    function remove_shortcode_instances(code) {
        if (Object.keys(azh.shortcodes).length) {
            var tags = Object.keys(azh.shortcodes).join('|'),
                    reg = wp_shortcode.regexp(tags),
                    matches = code.match(reg);
            var match = null;
            code = code.replace(reg, function(str, open, name, args, self_closing, content, closing, close, offset, s) {
                var id = name + '-' + makeid();
                azh.shortcode_instances[id] = {
                    raw: str,
                    atts_raw: wp_shortcode.attrs(args),
                    sub_content: content,
                    shortcode: name
                };
                return id;
            });
        }
        return code;
    }
    function make_line(key, indent) {
        var line = $('<div class="azh-line" data-number="' + key + '"><span class="indent">' + Array(indent + 1).join("&nbsp;") + '</span></div>');
        $(line).data('indent', indent);
        $(line).data('get_code', function() {
            var code = '';
            $(this).children().each(function() {
                code = code + default_get_code.call(this);
            });
            return Array($(this).data('indent') + 1).join("\t") + code + "\n";
        });
        return line;
    }
    function make_text(code_line) {
        var text = $('<span class="azh-text"></span>').html(htmlEncode(code_line));
        $(text).data('code_line', code_line);
        $(text).data('get_code', function() {
            return $(this).data('code_line');
        });
        $(text).appendTo(this);
        return text;
    }
    function parse_url(url) {
        var a = document.createElement('a');
        a.href = url;
        return a;
    }
    function remove_description(text) {
        var match = null;
        if ((match = /\[\[([^\]]+)\]\]/gi.exec(text)) != null && match.length == 2) {
            return text.replace(match[0], '');
        }
        return text;
    }
    function try_default_description(element, text) {
        if (!$(element).data('description')) {
            $(azh.options.description_patterns).each(function() {
                var description_pattern = null;
                if (typeof this == 'string') {
                    description_pattern = new RegExp(this, 'gi');
                } else {
                    description_pattern = new RegExp(this);
                }
                var match = null;
                if ((match = description_pattern.exec(text)) != null && match.length == 2) {
                    $(element).data('description', match[1]);
                    return false;
                }
            });
        }
    }
    function wrap_code(code, match, index, wrapper) {
        //this - DOM element with code
        //code - string which need to be splitted
        //match - string which need to be wrapped
        //index - number of first character of match in code
        if ($(this).data('get_code') && match != '') {
            $(this).empty();
            $(this).data('get_code', false);

            var left_text = make_text.call(this, code.slice(0, index));
            var wrapped_code = $(wrapper).html(htmlEncode(remove_description(match))).appendTo(this);
            var right_text = make_text.call(this, code.slice(index + match.length));

            $(document).trigger('azh-wrap-code', {element: left_text});
            $(document).trigger('azh-wrap-code', {element: right_text});

            var m = null;
            if ((m = /\[\[([^\]]+)\]\]/gi.exec(match)) != null && m.length == 2) {
                $(wrapped_code).data('description', m[1]);
            }

            return wrapped_code;
        }
        return null;
    }
    function wrap_content(element, code, match, index) {
        var content = wrap_code.call(element, code, match, index, '<span class="azh-content" placeholder="' + azh.i18n.enter_text_here + '"></span>');
        $(content).addClass('azh-editable');

        var m = $.trim(match);
        if (m in azh.shortcode_instances) {
            $(content).addClass('azh-shortcode');
            $(content).data('description', azh.shortcodes[azh.shortcode_instances[m].shortcode].description);
            $(content).empty();
            $(content).append('<strong>' + azh.shortcodes[azh.shortcode_instances[m].shortcode].name + '</strong><br>');
            var atts = Object.keys(azh.shortcode_instances[m].atts_raw.named).map(function(item) {
                if (item == 'content') {
                    return '';
                } else {
                    return ' ' + item + '="' + azh.shortcode_instances[m].atts_raw.named[item] + '"';
                }
            }).join('');
            $(content).append('<em>' + atts + '</em>');
            $(content).data('content', azh.shortcode_instances[m].raw);
            $(content).data('shortcode_instance', azh.shortcode_instances[m]);
            $(content).data('get_code', function() {
                return $(this).data('content');
            });

            $(content).on('click', function() {
                var content = this;
                var instance = $(content).data('shortcode_instance');
                var values = instance.atts_raw.named;
                if (instance.sub_content != '') {
                    values['content'] = instance.sub_content;
                }
                azh.open_element_settings_dialog(azh.shortcodes[instance.shortcode], values, function(shortcode, attrs) {
                    var instance = $(content).data('shortcode_instance');
                    instance.atts_raw.named = attrs;
                    if ('content' in attrs) {
                        instance.sub_content = attrs['content'];
                    }
                    $(content).data('shortcode_instance', instance);
                    $(content).data('content', shortcode);
                    $(content).empty();
                    $(content).append('<strong>' + azh.shortcodes[instance.shortcode].name + '</strong><br>');
                    var atts = Object.keys(instance.atts_raw.named).map(function(item) {
                        if (item == 'content') {
                            return '';
                        } else {
                            return ' ' + item + '="' + instance.atts_raw.named[item] + '"';
                        }
                    }).join('');
                    $(content).append('<em>' + atts + '</em>');
                    azh.store();
                });

                return false;
            });

        } else {
            $(content).data('content', remove_description(match));
            if (remove_description(match) == '') {
                $(content).addClass('azh-empty');
            }
            $(content).data('get_code', function() {
                if ($(this).data('description')) {
                    return $(this).data('content') + '[[' + $(this).data('description') + ']]';
                } else {
                    return $(this).data('content');
                }
            });
            $(content).attr('contenteditable', 'true');
            $(content).on('blur keyup paste input', function() {
                $(this).data('content', $(this).text());
                if ($(this).text() == '') {
                    $(this).addClass('azh-empty');
                } else {
                    $(this).removeClass('azh-empty');
                }
                $(this).trigger('change');
                azh.store();
            });
            $(content).on('blur', function() {
                $(this).parents('.azh-element-wrapper').removeClass('azh-editing');
            });
            $(content).on('click keyup paste input', function() {
                $(this).parents('.azh-element-wrapper').addClass('azh-editing');
            });
        }

        return content;
    }
    function html_beautify(html) {
        var results = '';
        var level = 0;
        AZHParser(html, {
            start: function(tag, attrs, unary) {
                results += Array(level * azh.indent_size + 1).join("\t") + "<" + tag;
                for (var i = 0; i < attrs.length; i++) {
                    if (attrs[i].value.indexOf('"') >= 0 && attrs[i].value.indexOf("'") < 0) {
                        results += " " + attrs[i].name + "='" + attrs[i].value + "'";
                    } else {
                        results += " " + attrs[i].name + '="' + attrs[i].escaped + '"';
                    }
                }
                results += (unary ? "/" : "") + ">\n";
                if (!unary) {
                    level++;
                }
            },
            end: function(tag) {
                level--;
                results += Array(level * azh.indent_size + 1).join("\t") + "</" + tag + ">\n";
            },
            chars: function(text) {
                if ($.trim(text)) {
                    results += Array(level * azh.indent_size + 1).join("\t") + text.replace(/[\t\r\n]*/g, '') + "\n";
                }
            },
        });
        return results;
    }
    function html_uglify(html) {
        var results = '';
        AZHParser(html, {
            start: function(tag, attrs, unary) {
                results += "<" + tag;
                for (var i = 0; i < attrs.length; i++) {
                    if (attrs[i].value.indexOf('"') >= 0 && attrs[i].value.indexOf("'") < 0) {
                        results += " " + attrs[i].name + "='" + attrs[i].value + "'";
                    } else {
                        results += " " + attrs[i].name + '="' + attrs[i].escaped + '"';
                    }
                }
                results += (unary ? "/" : "") + ">";
            },
            end: function(tag) {
                results += "</" + tag + ">";
            },
            chars: function(text) {
                if ($.trim(text)) {
                    results += text.replace(/[\t\r\n]*/g, '');
                }
            },
        });
        return results;
    }
    function cloneable_inline_refresh(cloneables) {
        $(cloneables).each(function() {
            var cloneable = this;
            if ($(cloneable).is('.azh-grid')) {
                $(cloneable).children().each(function() {
                    $(this).css('white-space', 'normal');
                    $(this).width('');
                });
            } else {
                $(cloneable).children().each(function() {
                    $(this).css('white-space', 'nowrap');
                    $(this).width('');
                });
                $(cloneable).children().each(function() {
                    if ($(this).width() > $(cloneable).width()) {
                        $(this).width($(cloneable).width());
                        $(this).css('white-space', 'normal');
                    }
                });
            }
        });
    }
    function equalize_controls_heights(wrapper) {
        $(wrapper).find('.azh-open-line').each(function() {
            $(this).find('.azh-control').equalizeHeights();
        });
    }
    $.fn.equalizeHeights = function() {
        var max = Math.max.apply(this, $(this).map(function(i, e) {
            return $(e).height();
        }).get());
        if (max > 0)
            this.height(max);
        return max;
    };
    $.fn.equalizeWidths = function() {
        var max = Math.max.apply(this, $(this).map(function(i, e) {
            return $(e).width();
        }).get());
        if (max > 0)
            this.width(max);
        return max;
    };

    var shortcodes_regexp = _.memoize(function(tags) {
        return new RegExp('\\[(\\[?)(' + tags + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)');
    });
    azh = $.extend({
        ids: {}
    }, azh);
    azh.focus = function(target, duration) {
        var focus_padding = 0;
        if ($('.azh-focus').length == 0) {
            $('<div class="azh-focus"><div class="top"></div><div class="right"></div><div class="bottom"></div><div class="left"></div></div>').appendTo('body').on('click', function() {
                $('.azh-focus').remove();
                return false;
            });
            $('.azh-focus .top, .azh-focus .right, .azh-focus .bottom, .azh-focus .left').css({
                'z-index': '999999',
                'position': 'fixed',
                'background-color': 'black',
                'opacity': '0.4'
            });
        }
        var top = $('.azh-focus .top');
        var right = $('.azh-focus .right');
        var bottom = $('.azh-focus .bottom');
        var left = $('.azh-focus .left');
        var target_top = $(target).offset()['top'] - focus_padding - $('body').scrollTop();
        var target_left = $(target).offset()['left'] - focus_padding;
        var target_width = $(target).outerWidth() + focus_padding * 2;
        var target_height = $(target).outerHeight() + focus_padding * 2;
        $(top).stop().animate({
            top: 0,
            left: 0,
            right: 0,
            height: target_top,
        }, duration, 'linear');
        $(right).stop().animate({
            top: target_top,
            left: target_left + target_width,
            right: 0,
            height: target_height,
        }, duration, 'linear');
        $(bottom).stop().animate({
            top: target_top + target_height,
            left: 0,
            right: 0,
            bottom: 0,
        }, duration, 'linear');
        $(left).stop().animate({
            top: target_top,
            left: 0,
            height: target_height,
            width: target_left,
        }, duration, 'linear', function() {
        });
        if (duration > 0) {
            setTimeout(function() {
                $(window).on('scroll.focus', function() {
                    $('.azh-focus').remove();
                    $(window).off('scroll.focus');
                });
                $('.azh-focus .top, .azh-focus .right, .azh-focus .bottom, .azh-focus .left').stop().animate({
                    'opacity': '0'
                }, duration * 10);
                setTimeout(function() {
                    $(window).trigger('scroll');
                }, duration * 10);
            }, duration);
        }
    }
    azh.shortcodes = $.extend({}, azh.shortcodes);
    var description_pattern = /\[\[[^\]]+\]\]/i;
    var options = {
        object_patterns: [
            /[ '"]az-object[ '"]/gi
        ],
        hide_patterns: [
        ],
        open_wrapper_pattern: /^\s*<("[^"]*"|'[^']*'|[^'">])*>\s*$/i,
        close_wrapper_pattern: /^\s*<\/\w+>\s*$/i,
        wrapper_hide_patterns: [
            /[ '"]az-hide[ '"]/gi
        ],
        inline_wrapper_patterns: [
            /[ '"]az-inline[ '"]/gi
        ],
        inline_replacers: [
            [
                {
                    from: /<span /i,
                    to: '<a href="#[[link url]]" '
                },
                {
                    from: /<\/span>/i,
                    to: '</a>'
                }
            ],
            [
                {
                    from: /<a href="[^"]*" /i,
                    to: '<span '
                },
                {
                    from: /<\/a>/i,
                    to: '</span>'
                }
            ]
        ],
        section_patterns: [
            / data-section=['"]([^'"]+)['"]/gi
        ],
        group_patterns: [
            / data-group=['"]([^'"]+)['"]/gi,
            / data-section=['"]([^'"]+)['"]/gi
        ],
        row_patterns: [
            /[ '"-]row[ '"]/gi,
            /[ '"]az-row[ '"]/gi,
            /<tr[> ]/gi
        ],
        column_responsive_prefixes: {
            'lg': azh.i18n.large,
            'md': azh.i18n.medium,
            'sm': azh.i18n.small,
            'xs': azh.i18n.extra_small
        },
        column_patterns: [
            /[ '"-]col-lg-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-md-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-sm-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-xs-([0-9]?[0-9])[ '"]/gi,
            /[ '"]az-column[ '"]/gi,
            /<td[> ]/gi,
            /<th[> ]/gi
        ],
        column_offset_patterns: [
            /[ '"-]col-lg-offset-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-md-offset-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-sm-offset-([0-9]?[0-9])[ '"]/gi,
            /[ '"-]col-xs-offset-([0-9]?[0-9])[ '"]/gi
        ],
        icon_patterns: [
            / class=['"]az-icon (az-oi [\w\d-_]+\[\[[^\]]+\]\]|az-oi [\w\d-_]+)['"]/gi,
            / class=['"]az-icon (az_li [\w\d-_]+\[\[[^\]]+\]\]|az_li [\w\d-_]+)['"]/gi,
            / class=['"]az-icon (typcn [\w\d-_]+\[\[[^\]]+\]\]|typcn [\w\d-_]+)['"]/gi,
            / class=['"]az-icon (az-mono [\w\d-_]+\[\[[^\]]+\]\]|az-mono [\w\d-_]+)['"]/gi,
            / class=['"]az-icon (entypo-icon [\w\d-_]+\[\[[^\]]+\]\]|entypo-icon [\w\d-_]+)['"]/gi,
            / class=['"]az-icon (fa [\w\d-_]+\[\[[^\]]+\]\]|fa [\w\d-_]+)['"]/gi,
            / class=['"]az-icon ([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)[\w\d-_ ]+['"]/gi,
            / class=['"]icon (az-oi [\w\d-_]+\[\[[^\]]+\]\]|az-oi [\w\d-_]+)['"]/gi,
            / class=['"]icon (az_li [\w\d-_]+\[\[[^\]]+\]\]|az_li [\w\d-_]+)['"]/gi,
            / class=['"]icon (typcn [\w\d-_]+\[\[[^\]]+\]\]|typcn [\w\d-_]+)['"]/gi,
            / class=['"]icon (az-mono [\w\d-_]+\[\[[^\]]+\]\]|az-mono [\w\d-_]+)['"]/gi,
            / class=['"]icon (entypo-icon [\w\d-_]+\[\[[^\]]+\]\]|entypo-icon [\w\d-_]+)['"]/gi,
            / class=['"]icon (fa [\w\d-_]+\[\[[^\]]+\]\]|fa [\w\d-_]+)['"]/gi,
            / class=['"]icon ([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)['"]/gi,
            /<i\s+class=['"]([\w\d-_ ]+\[\[[^\]]+\]\]|[\w\d-_ ]+)['"]/gi
        ],
        image_patterns: [
            /background-image\:[^;]*url\(['"]?([^'"\)]+)['"]?\)/gi,
            / data-image-src=['"]([^'"]+)['"]/gi,
            / data-src=['"]([^'"]+)['"]/gi,
            / data-marker=['"]([^'"]+)['"]/gi,
            / src=['"]([^'"]+)['"]/gi
        ],
        id_patterns: [
            / href=['"]#([\w\d-_]+)['"]/gi,
            / data-target=['"]#([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)['"]/gi,
            / data-id=['"]#([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)['"]/gi,
            / for=['"]([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)['"]/gi,
            / id=['"]([\w\d-_]+\[\[[^\]]+\]\]|[\w\d-_]+)['"]/gi
        ],
        link_patterns: [
            /<script src=['"]([^'"]+)['"]/gi,
            /<iframe src=['"]([^'"]+)['"]/gi,
            / href=['"]([^'"]+)['"]/gi
        ],
        integer_patterns: [
            /box-shadow\:[^;]*(-?\d+px\[\[[^\]]+\]\]|-?\d+px)/gi,
            /['" ;][\w-]+\: *(-?\d+px\[\[[^\]]+\]\]|-?\d+px)/gi,
            /['" ;][\w-]+\: *(-?\d+em\[\[[^\]]+\]\]|-?\d+em)/gi,
            /['" ;][\w-]+\: *(-?\d+%\[\[[^\]]+\]\]|-?\d+%)/gi,
            / data-[\w\d-_]+=['"](-?\d+px\[\[[^\]]+\]\]|-?\d+px)['"]/gi,
            / data-[\w\d-_]+=['"](-?\d+%\[\[[^\]]+\]\]|-?\d+%)['"]/gi,
            / data-[\w\d-_]+=['"](-?\d+\[\[[^\]]+\]\]|-?\d+)['"]/gi
        ],
        text_patterns: [
            / data-latitude=['"]([\d\,\.\-]+\[\[[^\]]+\]\]|[\d\,\.\-]+)['"]/gi,
            / data-longitude=['"]([\d\,\.\-]+\[\[[^\]]+\]\]|[\d\,\.\-]+)['"]/gi,
            / data-sitekey=['"]([^'"]+)['"]/gi,
            / data-sr=['"]([^'"]+)['"]/gi,
            / data-time=['"]([^'"]+)['"]/gi,
            / placeholder=['"]([^'"]+)['"]/gi,
            / value=['"]([^'"]+)['"]/gi,
            / name=['"]([^'"]+)['"]/gi,
            / title=['"]([^'"]+)['"]/gi,
            />([^><]+)</gi,
            />([^><]+)$/gi,
            /^([^><]+)</gi
        ],
        exists_patterns: [
            {
                description: azh.i18n.selected,
                pattern: /<option/i,
                exists: /<option (selected[= ]?['"]?(selected)?['"]?)/i,
                after: ' selected=""'
            },
            {
                description: azh.i18n.required,
                pattern: /<select/i,
                exists: /<select (required[= ]?['"]?(required)?['"]?)/i,
                after: ' required=""'
            },
            {
                description: azh.i18n.required,
                pattern: /<textarea/i,
                exists: /<textarea (required[= ]?['"]?(required)?['"]?)/i,
                after: ' required=""'
            },
            {
                description: azh.i18n.checked,
                pattern: / type="checkbox"/i,
                exists: / type="checkbox" (checked[= ]?['"]?(checked)?['"]?)/i,
                after: ' checked=""'
            },
            {
                description: azh.i18n.checked,
                pattern: / type="radio"/i,
                exists: / type="radio" (checked[= ]?['"]?(checked)?['"]?)/i,
                after: ' checked=""'
            },
            {
                description: azh.i18n.required,
                pattern: /<input/i,
                exists: /<input (required[= ]?['"]?(required)?['"]?)/i,
                after: ' required=""'
            }
        ],
        positive_values: ['true', 'yes', '1'],
        negative_values: ['false', 'no', '0'],
        checkbox_patterns: [
            / data-[\w\d-_]+=['"](no\[\[[^\]]+\]\]|no)['"]/gi,
            / data-[\w\d-_]+=['"](yes\[\[[^\]]+\]\]|yes)['"]/gi,
            / data-[\w\d-_]+=['"](false\[\[[^\]]+\]\]|false)['"]/gi,
            / data-[\w\d-_]+=['"](true\[\[[^\]]+\]\]|true)['"]/gi
        ],
        dropdown_patterns: [
            {
                pattern: / type=['"]((text|color|date|datetime|datetime-local|email|number|range|tel|time|url|month|week)\[\[[^\]]+\]\]|(text|color|date|datetime|datetime-local|email|number|range|tel|time|url|month|week))['"]/gi,
                options: {
                    "text": 'text',
                    "color": 'color',
                    "date": 'date',
                    "datetime": 'datetime',
                    "datetime-local": 'datetime-local',
                    "email": 'email',
                    "number": 'number',
                    "range": 'range',
                    "tel": 'tel',
                    "time": 'time',
                    "url": 'url',
                    "month": 'month',
                    "week": 'week'
                }
            }
        ],
        color_patterns: [
            /background-color\: *(\#[A-Fa-f0-9]{6}\[\[[^\]]+\]\]|\#[A-Fa-f0-9]{6}|transparent\[\[[^\]]+\]\]|transparent)/gi,
            /color\: *(\#[A-Fa-f0-9]{6}\[\[[^\]]+\]\]|\#[A-Fa-f0-9]{6}|transparent\[\[[^\]]+\]\]|transparent)/gi,
            /(rgba\( *\d+ *, *\d+ *, *\d+ *, *0.?\d*. *\)\[\[[^\]]+\]\]|rgba\( *\d+ *, *\d+ *, *\d+ *, *\d.?\d*. *\))/gi,
            /(rgb\( *\d, *\d, *\d *\)\[\[[^\]]+\]\]|rgb\( *\d, *\d, *\d *\))/gi
        ],
        cloneable_patterns: [
            /<tbody[> ]/gi,
            /data-cloneable[=> ]/i
        ],
        cloneable_inline_patterns: [
            /<tr[> ]/gi,
            /data-cloneable-inline[=> ]/i
        ],
        element_wrapper_patterns: [
            / data-element=['"]([^'"]*)['"]/gi
        ],
        hidden_shortcode_patterns: [
            / data-azh[\w-]+=['"]([^'"]+)['"]/gi,
        ],
        description_patterns: [
            /['" ;]([\w-]+)\: *\d+px/gi,
            /['" ;]([\w-]+)\: *\d+em/gi,
            /['" ;]([\w-]+)\: *\d+%/gi,
            /(background-image)/i,
            /(background-color)/i,
            /(text-align)/i,
            /(line-height)/i,
            /(font-weight)/i,
            /(font-style)/i,
            /(font-family)/i,
            /(color)/i,
            / (placeholder)=['"][^'"]+['"]/gi,
            / data-([\w\d-_]+)=['"]/i,
            / class=['"](icon) [\w\d-_]*['"]/i
        ]
    };
    for (var key in options) {
        if ($.isArray(options[key])) {
            if (key in azh.options) {
                azh.options[key] = options[key].concat(azh.options[key]);
            }
        }
    }
    azh.options = $.extend(true, options, azh.options);
    azh.store = function() {
        $(azh.textarea).val(html_uglify(default_get_code.call(azh.lines)));
        $(azh.textarea).change();
        setTimeout(function() {
            azh.structure_refresh();
            $(document).trigger('azh-store');
        }, 0);
    }
    azh.append_code = function(code, tree, after, before) {
        function wrap_tree(node) {
            if (node.open_line) {
                $(document).trigger('azh-wrap-tree', {node: node});
            }
            $(node.children).each(function() {
                wrap_tree(this);
            });
        }
        tree = (typeof tree !== 'undefined' ? tree : {open_line: false, close_line: false, children: []});
        after = (typeof after !== 'undefined' ? after : false);
        before = (typeof before !== 'undefined' ? before : false);
        if (after && $(after).length == 0) {
            after = false;
        }
        if (before && $(before).length == 0) {
            before = false;
        }

        var new_lines = [];
        var code = remove_shortcode_instances(code);
        code = code.replace(/(\r\n|\n|\r|\t)/gm, '');
        code = html_beautify(code);

        code = code.replace(/data-element=""/gi, 'data-element=" "');

        var code_lines = code.split("\n");
        code_lines = code_lines.filter(Boolean);
        for (var key in code_lines) {
            var indent = 0;
            var code_line = code_lines[key];
            while (code_line.charAt(0) == "\t") {
                indent++;
                code_line = code_line.substr(1);
            }
            var line = make_line(key, indent);
            if (after) {
                $(after).after(line);
                after = line;
            } else {
                if (before) {
                    $(before).before(line);
                    after = line;
                } else {
                    $(line).appendTo(azh.lines);
                }
            }
            new_lines.push(line);

            var text = make_text.call(line, code_line);
            $(document).trigger('azh-wrap-code', {element: text});
        }
        var data = {lines: new_lines};
        data.tree = tree;
        var added_from = tree.children.length;
        $(document).trigger('azh-lines', data); // make tree
        $(document).trigger('azh-tree', data); // wrap all tree by default wrappers            
        data.added_from = added_from;
        for (var i = data.added_from; i < data.tree.children.length; i++) {
            wrap_tree(data.tree.children[i]);
        }
        $(document).trigger('azh-process', data); // process
        return data;
    }
    azh.add_code = function(code, after, before) {
        azh.lines = $(azh.editor).find('.azh-lines');
        if (azh.lines.length == 0) {
            azh.lines = $('<div class="azh-lines"></div>').appendTo(azh.editor);
        }
        if (!('tree' in azh)) {
            azh.tree = {open_line: false, close_line: false, children: []};
        }
        azh.ids = {};
        azh.shortcode_instances = {};
        azh.append_code(code, azh.tree, after, before);
        azh.store();
    };
    azh.indent_size = 1;
    azh.init = function(textarea, edit) {
        azh.textarea = textarea;
        $(textarea).hide();
        //azh.general_sections = $('<div class="azh-general-sections"><div class="azh-section-paste" title="' + azh.i18n.paste + '"></div></div>');
        //$(textarea).after(azh.general_sections);
        azh.editor = $('<div class="azexo-html-editor"></div>');
        $(textarea).after(azh.editor);
        azh.edit = (typeof edit !== 'undefined' ? edit : true);
        var switcher = $('<a class="azh-switcher button edit">' + azh.i18n.switch_to_html + '</a>').on('click', function() {
            if (azh.edit) {
                $(textarea).show();
                $(azh.editor).empty();
                $(azh.editor).hide();
                $(switcher).text(azh.i18n.switch_to_customizer);
                azh.edit = false;
                $(this).removeClass('edit');
            } else {
                $(azh.textarea).hide();
                azh.add_code($(azh.textarea).val());
                $(azh.editor).show();
                $(switcher).text(azh.i18n.switch_to_html);
                azh.edit = true;
                $(this).addClass('edit');
            }
            return false;
        });
        $(textarea).before(switcher);
        if (azh.edit) {
            azh.add_code($(textarea).val());
            if ($(textarea).val().trim() == '') {
                $('<div class="azh-empty-html">' + azh.i18n.empty_html + '</div>').appendTo(azh.editor);
            }
        }
        $('.azh-library .azh-sections .azh-section.general').each(function() {
            var $section = $(this).clone(true);
            $section.css('display', '');
            $('.azh-general-sections').append($section);
        });
        $('.azh-section-paste').on('click', function() {
            $(azh.editor).find('.azh-empty-html').remove();
            $.post(ajaxurl, {
                'action': 'azh_paste',
                dataType: 'text',
            }, function(data) {
                data = JSON.parse(data);
                if ('code' in data) {
                    if ('editor' in azh) {
                        var section_exists = false;
                        $(azh.options.section_patterns).each(function() {
                            var section_pattern = null;
                            if (typeof this == 'string') {
                                section_pattern = new RegExp(this, 'gi');
                            } else {
                                section_pattern = new RegExp(this);
                            }
                            var match = null;
                            while ((match = section_pattern.exec(data.code)) != null && match.length == 2) {
                                section_exists = true;
                            }
                        });
                        if (section_exists) {
                            azh.add_code(data.code);
                        } else {
                            azh.add_code('<div data-section="' + ('path' in data ? data.path : '') + '">' + data.code + '</div>');
                        }
                    }
                }
                azh.store();
            });
            return false;
        });
    };
    azh.structure_refresh = function() {
        $('.azh-structure').empty();
        $(azh.editor).find('.azh-section').each(function() {
            var section_path = $('<div class="azh-section-path">' + $(this).data('section') + '</div>').appendTo($('.azh-structure'));
            $(section_path).data('section', this);
            $('<div class="azh-remove"></div>').appendTo(section_path).on('click', function() {
                $(section_path).data('section').remove();
                $(section_path).remove();
                azh.store();
                return false;
            });
            $(section_path).on('click', function() {
                var section = $(this).data('section');
                $('body, html').stop().animate({
                    'scrollTop': $(section).offset().top - $(window).height() / 2 + $(section).height() / 2
                }, 300);
                setTimeout(function() {
                    $('<div class="azh-overlay"></div>').appendTo('body');
                    azh.focus('.azh-overlay', 0);
                    setTimeout(function() {
                        $('.azh-overlay').remove();
                        azh.focus(section, 300);
                    }, 0);
                }, 300);
                return false;
            });
        });
        $('.azh-structure').sortable({
            placeholder: 'azh-placeholder',
            forcePlaceholderSize: true,
            update: function(event, ui) {
                var section = $(ui.item).data('section');
                $(section).detach();
                if ($(ui.item).next().length) {
                    var next_section = $(ui.item).next().data('section');
                    $(next_section).before(section);
                } else {
                    if ($(ui.item).prev().length) {
                        var prev_section = $(ui.item).prev().data('section');
                        $(prev_section).after(section);
                    }
                }
                azh.store();
            },
            over: function(event, ui) {
                ui.placeholder.attr('class', ui.helper.attr('class'));
                ui.placeholder.removeClass('ui-sortable-helper');

                ui.placeholder.attr('style', ui.helper.attr('style'));
                ui.placeholder.css('position', 'relative');
                ui.placeholder.css('z-index', 'auto');
                ui.placeholder.css('left', 'auto');
                ui.placeholder.css('top', 'auto');

                ui.placeholder.addClass('azh-placeholder');
            }
        });
        if ($('.azh-structure').length) {
            $('.azh-structure').scrollTop($('.azh-structure')[0].scrollHeight);
        }
    }
    azh.library_init = function() {
        function filters_change() {
            var category = $(categories).find('option:selected').val();
            var tag = $(tags_select).find('option:selected').val();
            if (category == '' && tag == '') {
                $('.azh-library .azh-sections .azh-section').show();
            } else {
                if (category != '' && tag == '') {
                    $('.azh-library .azh-sections .azh-section').hide();
                    $('.azh-library .azh-sections .azh-section[data-path^="' + category + '"]').show();
                }
                if (category == '' && tag != '') {
                    $('.azh-library .azh-sections .azh-section').hide();
                    $('.azh-library .azh-sections .azh-section.' + tag).show();
                }
                if (category != '' && tag != '') {
                    $('.azh-library .azh-sections .azh-section').show();
                    $('.azh-library .azh-sections .azh-section:not([data-path^="' + category + '"])').hide();
                    $('.azh-library .azh-sections .azh-section:not(.' + tag + ')').hide();
                }
            }
        }
        azh.tags = {};
        var files_tags = {};
        for (var dir in azh.dirs_options) {
            if ('tags' in azh.dirs_options[dir]) {
                for (var file in azh.dirs_options[dir].tags) {
                    var tags = azh.dirs_options[dir].tags[file].split(',').map(function(tag) {
                        azh.tags[$.trim(tag).toLowerCase()] = true;
                        return $.trim(tag).toLowerCase();
                    });
                    files_tags[dir + '/' + file] = tags;
                }
            }
        }
        $('.azh-library .azh-sections .azh-section').each(function() {
            var key = $(this).data('dir') + '/' + $(this).data('path');
            if (key in files_tags) {
                $(this).addClass(files_tags[key].join(' '));
            }
        });
        $('.azh-library .azh-elements .azh-element').each(function() {
            var key = $(this).data('dir') + '/' + $(this).data('path');
            if (key in files_tags) {
                $(this).addClass(files_tags[key].join(' '));
            }
        });
        var child_suggestions = {};
        for (var dir in azh.dirs_options) {
            if ('child-suggestions' in azh.dirs_options[dir]) {
                for (var file in azh.dirs_options[dir]['child-suggestions']) {
                    var path = dir + '/' + file;
                    if (!(path in child_suggestions)) {
                        child_suggestions[path] = [];
                    }
                    $(azh.dirs_options[dir]['child-suggestions'][file]).each(function() {
                        child_suggestions[path].push(dir + '/' + this);
                    });
                    if (!(file in child_suggestions)) {
                        child_suggestions[file] = [];
                    }
                    $(azh.dirs_options[dir]['child-suggestions'][file]).each(function() {
                        child_suggestions[file].push(this);
                    });
                }
            }
        }
        var child_suggestions_elements = {};
        for (var path in child_suggestions) {
            $(child_suggestions[path]).each(function() {
                var suggestion = this;
                $('.azh-library .azh-elements .azh-element').each(function() {
                    var key = $(this).data('dir') + '/' + $(this).data('path');
                    if (key == suggestion) {
                        if (!(path in child_suggestions_elements)) {
                            child_suggestions_elements[path] = [];
                        }
                        child_suggestions_elements[path].push(this);
                    }
                    if ($(this).data('path') == suggestion) {
                        if (!(path in child_suggestions_elements)) {
                            child_suggestions_elements[path] = [];
                        }
                        child_suggestions_elements[path].push(this);
                    }
                });
            });
        }
        $('.azh-library .azh-elements .azh-element').each(function() {
            var key = $(this).data('dir') + '/' + $(this).data('path');
            if (key in child_suggestions_elements) {
                $(this).data('child-suggestions', child_suggestions_elements[key]);
            }
            if ($(this).data('path') in child_suggestions_elements) {
                $(this).data('child-suggestions', child_suggestions_elements[$(this).data('path')]);
            }
        });
        $('.azh-add-section').off('click').on('click', function() {
            if ($('.azh-library').css('display') == 'none') {
                $('.azh-structure').animate({
                    'max-height': "100px"
                }, 400, function() {
                    $('.azh-structure').scrollTop($('.azh-structure')[0].scrollHeight);
                });
                $('.azh-library').slideDown();
                $(this).text($(this).data('close'));
            } else {
                $('.azh-structure').animate({
                    'max-height': "600px"
                }, 400);
                $('.azh-library').slideUp();
                $(this).text($(this).data('open'));
            }
            return false;
        });
        $('.azh-actions').show();
        $('.azh-copy-sections-list').off('click').on('click', function() {
            var paths = []
            $('.azh-structure .azh-section-path').each(function() {
                paths.push($(this).text());
            });
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(paths.join('|')).select();
            document.execCommand("copy");
            $temp.remove();
            alert(azh.i18n.copied);
            return false;
        });
        $('.azh-insert-sections-list').off('click').on('click', function() {
            var sections = prompt(azh.i18n.paste_sections_list_here);
            if ($.trim(sections) !== '') {
                sections = sections.split('|');
                $(document).on('azh-store.insert-sections', function() {
                    if (sections.length > 0) {
                        $('.azh-library .azh-sections .azh-section[data-path="' + sections.shift() + '"]').click();
                    } else {
                        $(document).off('azh-store.insert-sections');
                    }
                });
                $(document).trigger('azh-store');
            }
            return false;
        });

        var categories = $('.azh-library .azh-categories').off('change').on('change', filters_change);
        var tags_select = $('<select></select>').appendTo('.azh-library-filters').on('change', filters_change);
        $('<option selected value="">' + azh.i18n.filter_by_tag + '</option>').appendTo(tags_select);

        Object.keys(azh.tags).sort().forEach(function(tag, i) {
            $('<option value="' + tag + '">' + tag + '</option>').appendTo(tags_select);
        });
        if (azh.default_category && categories.find('[value="' + azh.default_category + '"]').length) {
            categories.val(azh.default_category);
            filters_change();
        }

        $('.azh-library .azh-sections .azh-section').off('click').on('click', function() {
            var preview = this;
            $(azh.editor).find('.azh-empty-html').remove();
            $.get($(preview).data('url'), function(data) {
                if ('editor' in azh) {
                    var section_exists = false;
                    data = data.replace(/{{azh-uri}}/g, $(preview).data('dir-uri'));
                    $(azh.options.section_patterns).each(function() {
                        var section_pattern = null;
                        if (typeof this == 'string') {
                            section_pattern = new RegExp(this, 'gi');
                        } else {
                            section_pattern = new RegExp(this);
                        }
                        var match = null;
                        while ((match = section_pattern.exec(data)) != null && match.length == 2) {
                            section_exists = true;
                        }
                    });
                    if (section_exists) {
                        azh.add_code(data);
                    } else {
                        azh.add_code('<div data-section="' + $(preview).data('path') + '">' + data + '</div>');
                    }
                }
            });
            return false;
        });
    }
    azh.parse_query_string = function(a) {
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
    $(document).off('azh-wrap-code.base').on('azh-wrap-code.base', function(sender, data) {
        if ($(data.element).data('get_code')) {
            var code = $(data.element).data('get_code').call(data.element);

            $(azh.options.id_patterns).each(function() {
                var id_pattern = null;
                if (typeof this == 'string') {
                    id_pattern = new RegExp(this, 'gi');
                } else {
                    id_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = id_pattern.exec(code)) != null && match.length == 2) {
                    var content = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                    $(content).addClass('azh-id');
                }
            });

            $(azh.options.link_patterns).each(function() {
                var link_pattern = null;
                if (typeof this == 'string') {
                    link_pattern = new RegExp(this, 'gi');
                } else {
                    link_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = link_pattern.exec(code)) != null && match.length == 2) {
                    var link = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-link"></span>');
                    $(link).addClass('azh-editable');
                    $(link).text(remove_description(match[1]));
                    if (match[1][0] != '#') {
                        if (/^(?:[\w]+:)?\/\//i.test(remove_description(match[1]))) {
                            $(link).text('...' + parse_url(remove_description(match[1])).pathname + parse_url(remove_description(match[1])).search);
                        }
                    }
                    $(link).data('url', remove_description(match[1]));
                    $(link).data('get_code', function() {
                        if ($(this).data('description')) {
                            return $(this).data('url') + '[[' + $(this).data('description') + ']]';
                        } else {
                            return $(this).data('url');
                        }
                    });
                    $(link).on('click', function(event) {
                        var link = this;
                        azh.open_link_select_dialog.call(link, event, function(url) {
                            $(this).data('url', url);
                            $(link).text('...' + parse_url(url).pathname + parse_url(url).search);
                            $(link).trigger('change');
                            azh.store();
                        });
                    });
                }
            });

            $(azh.options.image_patterns).each(function() {
                var image_pattern = null;
                if (typeof this == 'string') {
                    image_pattern = new RegExp(this, 'gi');
                } else {
                    image_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = image_pattern.exec(code)) != null && match.length == 2) {
                    var image = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-image"></span>');
                    try_default_description(image, match[0]);
                    $(image).addClass('azh-editable');
                    $(image).text('.../' + parse_url(remove_description(match[1])).pathname.split('/').pop());
                    $(image).on('mouseenter', function() {
                        var image = this;
                        var url = $(image).data('url');
                        var hover = $('<div class="azh-hover"></div>').css('background-image', 'url("' + url + '")').appendTo('body');
                        $(image).data('hover', hover);
                        $(image).on('mousemove', function(e) {
                            $(hover).css('left', e.clientX + 'px');
                            $(hover).css('top', e.clientY + 'px');
                        });
                    });
                    $(image).on('mouseleave', function() {
                        var image = this;
                        $(image).off('mousemove');
                        var hover = $(image).data('hover');
                        $(hover).remove();
                    });
                    $(image).data('url', remove_description(match[1]));
                    $(image).data('get_code', function() {
                        if ($(this).data('description')) {
                            return $(this).data('url') + '[[' + $(this).data('description') + ']]';
                        } else {
                            return $(this).data('url');
                        }
                    });
                    $(image).on('click', function(event) {
                        var image = this;
                        azh.open_image_select_dialog.call(image, event, function(url) {
                            $(this).data('url', url);
                            $(this).text('.../' + parse_url(url).pathname.split('/').pop());
                            $(this).trigger('change');
                            azh.store();
                        });
                    });
                }
            });

            $(azh.options.icon_patterns).each(function() {
                var icon_pattern = null;
                if (typeof this == 'string') {
                    icon_pattern = new RegExp(this, 'gi');
                } else {
                    icon_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = icon_pattern.exec(code)) != null && match.length == 2) {
                    var icon = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-icon"></span>');
                    try_default_description(icon, match[0]);
                    $(icon).addClass('azh-editable');
                    $(icon).data('class', remove_description(match[1]));
                    $(icon).data('get_code', function() {
                        if ($(this).data('description')) {
                            return $(this).data('class') + '[[' + $(this).data('description') + ']]';
                        } else {
                            return $(this).data('class');
                        }
                    });
                    $(icon).on('mouseenter', function() {
                        var icon = this;
                        var hover = $('<div class="azh-hover ' + $(icon).data('class') + '"></div>').appendTo('body');
                        $(icon).data('hover', hover);
                        $(icon).on('mousemove', function(e) {
                            $(hover).css('left', e.clientX + 'px');
                            $(hover).css('top', e.clientY + 'px');
                        });
                    });
                    $(icon).on('mouseleave', function() {
                        var icon = this;
                        $(icon).off('mousemove');
                        var hover = $(icon).data('hover');
                        $(hover).remove();
                    });

                    $(icon).on('click', function(event) {
                        var icon = this;
                        azh.open_icon_select_dialog.call(icon, event, $(icon).data('class'), function(icon_class) {
                            if (icon_class != '') {
                                $(this).data('class', icon_class);
                                $(this).text(icon_class);
                                $(this).trigger('change');
                                azh.store();
                            }
                        });
                    });
                }
            });

            $(azh.options.exists_patterns).each(function() {
                var exists_pattern = null;
                if (typeof this.pattern == 'string') {
                    exists_pattern = new RegExp(this.pattern, 'i');
                } else {
                    exists_pattern = new RegExp(this.pattern);
                }
                var exists = null;
                if (typeof this.exists == 'string') {
                    exists = new RegExp(this.exists, 'i');
                } else {
                    exists = new RegExp(this.exists);
                }
                var match = exists_pattern.exec(code);
                if (match != null) {
                    var checkbox = null;
                    var m = exists.exec(code);
                    if (m == null) {
                        checkbox = wrap_code.call(data.element, code, match[0], match.index, '<span class="azh-checkbox"></span>');
                        $(checkbox).data('content', match[0]);
                    } else {
                        checkbox = wrap_code.call(data.element, code, m[0], m.index, '<span class="azh-checkbox"></span>');
                        $(checkbox).data('content', m[0]);
                    }
                    $(checkbox).addClass('azh-editable');
                    $(checkbox).empty();
                    var input = $('<input type="checkbox">').appendTo(checkbox);
                    $(checkbox).data('description', this.description);
                    $(checkbox).data('after', this.after);
                    $(checkbox).data('not_exist', match[0]);
                    $(input).prop('checked', m != null);
                    $(input).on('change', function() {
                        var checkbox = $(this).closest('.azh-checkbox');
                        if ($(this).prop('checked')) {
                            $(checkbox).data('content', $(checkbox).data('not_exist') + $(checkbox).data('after'));
                        } else {
                            $(checkbox).data('content', $(checkbox).data('not_exist'));
                        }
                        azh.store();
                    });
                    $(checkbox).data('get_code', function() {
                        return $(this).data('content');
                    });
                }
            });

            $(azh.options.checkbox_patterns).each(function() {
                var checkbox_pattern = null;
                if (typeof this == 'string') {
                    checkbox_pattern = new RegExp(this, 'gi');
                } else {
                    checkbox_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = checkbox_pattern.exec(code)) != null && match.length == 2) {
                    var checkbox = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-checkbox"></span>');
                    try_default_description(checkbox, match[0]);
                    $(checkbox).addClass('azh-editable');
                    $(checkbox).empty();
                    var input = $('<input type="checkbox">').appendTo(checkbox);
                    $(checkbox).data('content', remove_description(match[1]));
                    if (azh.options.positive_values.indexOf(remove_description(match[1])) >= 0) {
                        $(input).prop('checked', true);
                    } else {
                        $(input).prop('checked', false);
                    }
                    $(input).on('change', function() {
                        if ($(this).prop('checked')) {
                            var i = azh.options.negative_values.indexOf($(this).closest('.azh-checkbox').data('content'));
                            if (i >= 0) {
                                $(this).closest('.azh-checkbox').data('content', azh.options.positive_values[i]);
                            }
                        } else {
                            var i = azh.options.positive_values.indexOf($(this).closest('.azh-checkbox').data('content'));
                            if (i >= 0) {
                                $(this).closest('.azh-checkbox').data('content', azh.options.negative_values[i]);
                            }
                        }
                        azh.store();
                    });
                    $(checkbox).data('get_code', function() {
                        if ($(this).data('description')) {
                            return $(this).data('content') + '[[' + $(this).data('description') + ']]';
                        } else {
                            return $(this).data('content');
                        }
                    });
                }
            });

            $(azh.options.dropdown_patterns).each(function() {
                var dropdown_pattern = null;
                if (typeof this.pattern == 'string') {
                    dropdown_pattern = new RegExp(this.pattern, 'gi');
                } else {
                    dropdown_pattern = new RegExp(this.pattern);
                }
                var match = null;
                while ((match = dropdown_pattern.exec(code)) != null && match.length >= 2) {
                    var dropdown = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-dropdown"></span>');
                    try_default_description(dropdown, match[0]);
                    $(dropdown).addClass('azh-editable');
                    $(dropdown).empty();
                    var select = $('<select></select>').appendTo(dropdown);
                    for (var value in this.options) {
                        $(select).append('<option value="' + value + '" ' + (value == remove_description(match[1]) ? 'selected' : '') + '>' + this.options[value] + '</option>');
                    }
                    $(dropdown).data('content', remove_description(match[1]));
                    $(select).on('change', function() {
                        $(this).closest('.azh-dropdown').data('content', $(this).find('option:selected').attr('value'));
                        azh.store();
                    });
                    $(dropdown).data('get_code', function() {
                        if ($(this).data('description')) {
                            return $(this).data('content') + '[[' + $(this).data('description') + ']]';
                        } else {
                            return $(this).data('content');
                        }
                    });
                }
            });

            $(azh.options.color_patterns).each(function() {
                var color_pattern = null;
                if (typeof this == 'string') {
                    color_pattern = new RegExp(this, 'gi');
                } else {
                    color_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = color_pattern.exec(code)) != null && match.length == 2) {
                    var color = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-color"></span>');
                    try_default_description(color, match[0]);
                    $(color).addClass('azh-editable');
                    $(color).empty();
                    if (remove_description(match[1]).indexOf('rgb') >= 0) {
                        var rgba = remove_description(match[1]).replace(/^rgba?\(|\s+|\)$/g, '').split(',');
                        if (rgba.length == 4) {
                            var float_input = $('<span class="azh-float" contenteditable="true">' + Math.round(parseFloat(rgba[3]) * 100) + '%</span>').appendTo(color);
                            $(float_input).on('focus', function() {
                                $(this).data('focus', true);
                            });
                            $(float_input).on('blur', function() {
                                $(this).data('focus', false);
                                var color = $(this).closest('.azh-color');
                                var input = $(color).find('input[type="color"]');
                                $(input).trigger('change');
                            });
                            $(float_input).on('mousewheel', function(e) {
                                if ($(this).data('focus')) {
                                    var d = e.originalEvent.wheelDelta / 120;
                                    var v = $(this).text();
                                    var i = parseInt(v, 10);
                                    v = v.replace(i.toString(), (i + d).toString());
                                    $(this).text(v);
                                    var color = $(this).closest('.azh-color');
                                    var input = $(color).find('input[type="color"]');
                                    $(input).trigger('change');
                                    return false;
                                }
                            });
                            $(float_input).on('keydown', function(e) {
                                if ($(this).data('focus') && ((e.keyCode == 38) || (e.keyCode == 40))) {
                                    var d = 0;
                                    if (e.keyCode == 38) {
                                        d = 1;
                                    }
                                    if (e.keyCode == 40) {
                                        d = -1;
                                    }
                                    var v = $(this).text();
                                    var i = parseInt(v, 10);
                                    v = v.replace(i.toString(), (i + d).toString());
                                    $(this).text(v);
                                    var color = $(this).closest('.azh-color');
                                    var input = $(color).find('input[type="color"]');
                                    $(input).trigger('change');
                                    return false;
                                }
                            });
                        }
                        var input = $('<input type="color">').appendTo(color);
                        $(input).val(rgb2hex(rgba));
                        $(color).data('color', remove_description(match[1]));
                        $(input).on('change', function() {
                            var color = $(this).closest('.azh-color');
                            var alpha = $(color).find('.azh-float');
                            if (alpha.length) {
                                var rgb = hex2rgb($(input).val());
                                $(color).data('color', 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + (parseInt($(alpha).text(), 10) / 100).toFixed(2) + ')');
                            } else {
                                var rgb = hex2rgb($(input).val());
                                $(color).data('color', 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')');
                            }
                            $(color).trigger('change');
                            azh.store();
                        });
                        $(color).data('get_code', function() {
                            if ($(this).data('description')) {
                                return $(this).data('color') + '[[' + $(this).data('description') + ']]';
                            } else {
                                return $(this).data('color');
                            }
                        });
                    } else {
                        var id = makeid();
                        var transparent_input = $('<input class="azh-transparent" id="' + id + '" type="checkbox">').appendTo(color);
                        $(transparent_input).on('change', function() {
                            if ($(transparent_input).prop('checked')) {
                                $(color).data('color', 'transparent');
                            } else {
                                $(color).data('color', $(input).val());
                            }
                            azh.store();
                        });
                        var transparent_label = $('<label for="' + id + '">transparent</label>').appendTo(color);
                        var input = $('<input type="color">').appendTo(color);
                        if (remove_description(match[1]) == 'transparent') {
                            $(transparent_input).prop('checked', true);
                        } else {
                            $(input).val(remove_description(match[1]));
                        }
                        $(color).data('color', remove_description(match[1]));
                        $(input).on('change', function() {
                            var color = $(this).closest('.azh-color');
                            $(color).data('color', $(input).val());
                            $(color).trigger('change');
                            azh.store();
                        });
                        $(color).data('get_code', function() {
                            if ($(this).data('description')) {
                                return $(this).data('color') + '[[' + $(this).data('description') + ']]';
                            } else {
                                return $(this).data('color');
                            }
                        });
                    }
                }
            });

            $(azh.options.column_patterns).each(function() {
                var column_pattern = null;
                if (typeof this == 'string') {
                    column_pattern = new RegExp(this, 'gi');
                } else {
                    column_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = column_pattern.exec(code)) != null && match.length == 2 && $.isNumeric(match[1])) {
                    var column_width = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-column-width azh-hide"></span>');
                    for (var prefix in azh.options.column_responsive_prefixes) {
                        if (match[0].indexOf(prefix) >= 0) {
                            $(column_width).data('responsive-prefix', prefix);
                        }
                    }
                    $(column_width).data('column-width', match[1]);
                    $(column_width).data('get_code', function() {
                        return $(this).data('column-width');
                    });
                }
            });

            $(azh.options.column_offset_patterns).each(function() {
                var column_offset_pattern = null;
                if (typeof this == 'string') {
                    column_offset_pattern = new RegExp(this, 'gi');
                } else {
                    column_offset_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = column_offset_pattern.exec(code)) != null && match.length == 2 && $.isNumeric(match[1])) {
                    var column_offset = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-column-offset azh-hide"></span>');
                    for (var prefix in azh.options.column_responsive_prefixes) {
                        if (match[0].indexOf(prefix) >= 0) {
                            $(column_offset).data('responsive-prefix', prefix);
                        }
                    }
                    $(column_offset).data('column-offset', match[1]);
                    $(column_offset).data('get_code', function() {
                        return $(this).data('column-offset');
                    });
                }
            });

            $(azh.options.integer_patterns).each(function() {
                var content_pattern = null;
                if (typeof this == 'string') {
                    content_pattern = new RegExp(this, 'gi');
                } else {
                    content_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = content_pattern.exec(code)) != null && match.length == 2) {
                    var content = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                    $(content).on('focus', function() {
                        $(this).data('focus', true);
                    });
                    $(content).on('blur', function() {
                        $(this).data('focus', false);
                    });
                    $(content).on('mousewheel', function(e) {
                        if ($(this).data('focus')) {
                            var d = e.originalEvent.wheelDelta / 120;
                            var v = $(this).data('content');
                            var i = parseInt(v, 10);
                            v = v.replace(i.toString(), (i + d).toString());
                            $(this).data('content', v);
                            $(this).text(v);
                            return false;
                        }
                    });
                    $(content).on('keydown', function(e) {
                        if ($(this).data('focus') && ((e.keyCode == 38) || (e.keyCode == 40))) {
                            var d = 0;
                            if (e.keyCode == 38) {
                                d = 1;
                            }
                            if (e.keyCode == 40) {
                                d = -1;
                            }
                            var v = $(this).data('content');
                            var i = parseInt(v, 10);
                            v = v.replace(i.toString(), (i + d).toString());
                            $(this).data('content', v);
                            $(this).text(v);
                            return false;
                        }
                    });
                    try_default_description(content, match[0]);
                }
            });

            $(azh.options.text_patterns).each(function() {
                var content_pattern = null;
                if (typeof this == 'string') {
                    content_pattern = new RegExp(this, 'gi');
                } else {
                    content_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = content_pattern.exec(code)) != null && match.length == 2) {
                    var content = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                    try_default_description(content, match[0]);
                }
            });
            var line = $(data.element).closest('.azh-line');
            var line_code = $(line).data('get_code').call(line);
            if (line_code.trim() == code.trim() && code.match(/^([^><]+)$/gi)) {
                wrap_content(data.element, code, code, 0);
            }

            $(azh.options.group_patterns).each(function() {
                var group_pattern = null;
                if (typeof this == 'string') {
                    group_pattern = new RegExp(this, 'gi');
                } else {
                    group_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = group_pattern.exec(code)) != null && match.length == 2) {
                    var group = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                    $(group).addClass('azh-group-title');
                }
            });

            $(azh.options.element_wrapper_patterns).each(function() {
                var element_pattern = null;
                if (typeof this == 'string') {
                    element_pattern = new RegExp(this, 'gi');
                } else {
                    element_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = element_pattern.exec(code)) != null && match.length == 2) {
                    var element = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                    $(element).removeClass('azh-editable');
                    $(element).hide();
                    $(element).addClass('azh-element-title');
                }
            });

            $(azh.options.hide_patterns).each(function() {
                var hide_pattern = null;
                if (typeof this == 'string') {
                    hide_pattern = new RegExp(this, 'gi');
                } else {
                    hide_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = hide_pattern.exec(code)) != null && match.length == 2) {
                    var content = wrap_code.call(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]), '<span class="azh-hide"></span>');
                    $(content).data('content', match[1]);
                    $(content).data('get_code', function() {
                        return $(this).data('content');
                    });
                }
            });
            $(azh.options.hidden_shortcode_patterns).each(function() {
                var hidden_shortcode_pattern = null;
                if (typeof this == 'string') {
                    hidden_shortcode_pattern = new RegExp(this, 'gi');
                } else {
                    hidden_shortcode_pattern = new RegExp(this);
                }
                var match = null;
                while ((match = hidden_shortcode_pattern.exec(code)) != null && match.length == 2) {
                    var content = wrap_content(data.element, code, match[1], match.index + match[0].lastIndexOf(match[1]));
                }
            });
        }
    });
    $(document).off('azh-lines.base').on('azh-lines.base', function(sender, data) {
        var path = [data.tree];
        var current_level = 0;
        $(data.lines).each(function() {
            var level = Math.round($(this).data('indent') / azh.indent_size);
            var code_line = $(this).data('get_code').call(this);
            var match = azh.options.close_wrapper_pattern.exec(code_line);
            if (match) {
                if (level < current_level) {
                    path[current_level].close_line = this;
                    path.pop();
                    current_level = level;
                } else {
                    path[current_level].children[path[current_level].children.length - 1].close_line = this;
                }
            } else {
                match = azh.options.open_wrapper_pattern.exec(code_line);
                if (match) {
                    if (level > current_level) {
                        path.push(path[current_level].children[path[current_level].children.length - 1]);
                        current_level = level;
                    }
                    if (level < current_level) {
                        path.pop();
                        current_level = level;
                    }
                    var node = {open_line: this, close_line: false, children: []};
                    path[current_level].children.push(node);
                }
            }
        });
    });
    $(document).off('azh-tree.base').on('azh-tree.base', function(sender, data) {
        function wrap_lines(node) {
            if (node.open_line && node.close_line && !('wrapper' in node)) {
                var lines = $(node.open_line).nextUntil(node.close_line);
                lines = $(node.open_line).add(lines).add(node.close_line);
                node.wrapper = lines.wrapAll("<div class='azh-wrapper' />").parent();
                $(node.open_line).addClass('azh-open-line');
                $(node.close_line).addClass('azh-close-line');
                $(node.wrapper).data('node', node); // not correct after cloning - because tree is not rebuild
            }
            if (node.open_line && !node.close_line && !('wrapper' in node)) {
                var code_line = $.trim($(node.open_line).data('get_code').call(node.open_line));
                node.wrapper = $(node.open_line).wrapAll("<div class='azh-wrapper' />").parent();
                $(node.open_line).addClass('azh-open-line');
                $(node.wrapper).data('node', node); // not correct after cloning - because tree is not rebuild                    
            }
            $(node.children).each(function() {
                wrap_lines(this);
            });
        }
        wrap_lines(data.tree);
    });
    $(document).off('azh-wrap-tree.base').on('azh-wrap-tree.base', function(sender, data) {
        function cloneable(horizontal) {
            var cloneable = $(data.node.open_line).nextUntil(data.node.close_line).wrapAll("<div class='azh-cloneable' />").parent();
            if (horizontal) {
                $(cloneable).addClass('azh-inline');
                setTimeout(function() {
                    cloneable_inline_refresh(cloneable);
                }, 0);
            }
            var indent = $(data.node.open_line).data('indent');
            $(data.node.wrapper).find('.azh-line').each(function() {
                $(this).find('.indent').html(Array($(this).data('indent') - indent).join("&nbsp;"));
            });
            var collapsed = $(cloneable).height() > 500;
            $(cloneable).children().each(function() {
                function get_linked_element(element) {
                    var linked_element = false;
                    if ($(element).find('.azh-id').length) {
                        var handle_id = $(element).find('.azh-id');
                        var id = $(handle_id).data('get_code').call(handle_id);
                        if (id in azh.ids && azh.ids[id]) {
                            for (var i = 0; i < azh.ids[id].length; i++) {
                                var linked_id = azh.ids[id][i];
                                if (!$(linked_id).is($(element).find('.azh-id'))) {
                                    if ($(linked_id).closest('.azh-cloneable').length) {
                                        $(linked_id).closest('.azh-cloneable').children().each(function() {
                                            if ($(this).find(linked_id).length) {
                                                linked_element = this;
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    }
                    return linked_element;
                }
                var element = this;
                if ($(element).is('.azh-wrapper')) {
                    $(element).data('node').matched = true;
                }
                var controls = $('<div class="azh-controls"></div>').prependTo(element).on('click', function(e) {
                    e.stopPropagation();
                });
                $('<div class="azh-move" title="' + azh.i18n.move + '"></div>').appendTo(controls);
                $('<div class="azh-clone" title="' + azh.i18n.clone + '"></div>').appendTo(controls).on('click', function() {
                    var element = $(this).parent().parent();
                    var cloneable = $(this).closest('.azh-cloneable');
                    var new_element = $(element).clone(true);
                    $(element).after(new_element);

                    if ($(new_element).find('.azh-id').length) {
                        var handle_id = $(new_element).find('.azh-id');
                        var id = $(handle_id).data('get_code').call(handle_id);
                        if (id in azh.ids && azh.ids[id]) {
                            var unoque_id = makeid();
                            var new_linked_element = false;
                            $(handle_id).data('content', unoque_id);
                            $(handle_id).text(unoque_id);
                            for (var i = 0; i < azh.ids[id].length; i++) {
                                var linked_id = azh.ids[id][i];
                                if (!$(linked_id).is($(element).find('.azh-id'))) {
                                    if ($(linked_id).closest('.azh-cloneable').length) {
                                        $(linked_id).closest('.azh-cloneable').children().each(function() {
                                            if ($(this).find(linked_id).length) {
                                                new_linked_element = $(this).clone(true);
                                                $(this).after(new_linked_element);
                                                var new_linked_id = $(new_linked_element).find('.azh-id');
                                                $(new_linked_id).data('content', unoque_id);
                                                $(new_linked_id).text(unoque_id);
                                            }
                                        });
                                    }
                                }
                            }
                            if (new_linked_element) {
                                azh.ids[unoque_id] = [$(new_element).find('.azh-id'), $(new_linked_element).find('.azh-id')];
                            }
                        }
                    }
                    if ($(cloneable).children().length > 1) {
                        $(cloneable).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').show();
                    }
                    azh.store();
                    return false;
                });
                $('<div class="azh-remove" title="' + azh.i18n.remove + '"></div>').appendTo(controls).on('click', function() {
                    var element = $(this).parent().parent();
                    var cloneable = $(this).closest('.azh-cloneable');

                    var linked_element = get_linked_element(element);
                    if (linked_element) {
                        $(linked_element).remove();
                    }
                    $(element).remove();
                    if ($(cloneable).children().length == 1) {
                        $(cloneable).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').hide();
                    } else {
                        $(cloneable).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').show();
                    }
                    azh.store();
                    return false;
                });
                if (collapsed && !horizontal) {
                    if ($(element).is('.azh-wrapper')) {
                        $(element).addClass('azh-collapsed');
                    }
                }
                $(element).off('click').on('click', function(event) {
                    var element = this;
                    var linked_element = get_linked_element(element);
                    if (collapsed || linked_element) {
                        if ($(element).is('.azh-collapsed')) {
                            $(element).closest('.azh-cloneable:not(.azh-inline)').find('> .azh-wrapper').addClass('azh-collapsed');
                            $(element).removeClass('azh-collapsed');
                        }
                        if (linked_element) {
                            $(linked_element).closest('.azh-cloneable:not(.azh-inline)').find('> .azh-wrapper').addClass('azh-collapsed');
                            $(linked_element).removeClass('azh-collapsed');
                        }
                    }
                    //must be transparent for mouse click events !!!
                    if (!('originalEvent' in event && 'constructor' in event.originalEvent && 'name' in event.originalEvent.constructor && event.originalEvent.constructor.name == 'MouseEvent')) {
                        return false;
                    }
                });

            });
            if ($(cloneable).children().length === 1) {
                $(cloneable).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').hide();
            }
            $(cloneable).sortable({
                handle: '> .azh-controls > .azh-move',
                placeholder: 'azh-placeholder',
                forcePlaceholderSize: true,
                update: function(event, ui) {
                    if ($(this).children().length === 1) {
                        $(this).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').hide();
                    } else {
                        $(this).children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').show();
                    }
                    azh.store();
                },
                over: function(event, ui) {
                    ui.placeholder.attr('class', ui.helper.attr('class'));
                    ui.placeholder.removeClass('ui-sortable-helper');

                    ui.placeholder.attr('style', ui.helper.attr('style'));
                    ui.placeholder.css('position', 'relative');
                    ui.placeholder.css('z-index', 'auto');
                    ui.placeholder.css('left', 'auto');
                    ui.placeholder.css('top', 'auto');

                    ui.placeholder.addClass('azh-placeholder');
                }
            });
        }
        var code_line = $(data.node.open_line).data('get_code').call(data.node.open_line);

        $(azh.options.wrapper_hide_patterns).each(function() {
            var wrapper_hide_pattern = null;
            if (typeof this == 'string') {
                wrapper_hide_pattern = new RegExp(this, 'gi');
            } else {
                wrapper_hide_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = wrapper_hide_pattern.exec(code_line)) != null && match.length == 1) {
                if (!('matched' in data.node)) {
                    $(data.node.wrapper).addClass('azh-hide');
                }
            }
        });

        $(azh.options.cloneable_patterns).each(function() {
            var cloneable_pattern = null;
            if (typeof this == 'string') {
                cloneable_pattern = new RegExp(this, 'gi');
            } else {
                cloneable_pattern = new RegExp(this);
            }
            var match = null;
            if ((match = cloneable_pattern.exec(code_line)) != null && match.length == 1) {
                cloneable(false);
            }
        });
        $(azh.options.cloneable_inline_patterns).each(function() {
            var cloneable_pattern = null;
            if (typeof this == 'string') {
                cloneable_pattern = new RegExp(this, 'gi');
            } else {
                cloneable_pattern = new RegExp(this);
            }
            var match = null;
            if ((match = cloneable_pattern.exec(code_line)) != null && match.length == 1) {
                cloneable(true);
            }
        });

        $(azh.options.row_patterns).each(function() {
            var row_pattern = null;
            if (typeof this == 'string') {
                row_pattern = new RegExp(this, 'gi');
            } else {
                row_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = row_pattern.exec(code_line)) != null) {
                $(data.node.wrapper).addClass('azh-row');
            }
        });

        $(azh.options.column_patterns).each(function() {
            var column_pattern = null;
            if (typeof this == 'string') {
                column_pattern = new RegExp(this, 'gi');
            } else {
                column_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = column_pattern.exec(code_line)) != null) {
                if (!$(data.node.wrapper).is('[class*="azh-col-"]')) {
                    if (match.length == 2 && $.isNumeric(match[1])) {
                        $(data.node.wrapper).addClass("azh-col-" + match[1]);
                        var controls = $('<div class="azh-column-controls"></div>').appendTo(data.node.wrapper);
                        $('<div class="azh-responsive" title="' + azh.i18n.column_responsive + '"></div>').appendTo(controls).on('click', function(event) {

                            $('.azh-responsive-dialog').remove();
                            var dialog = $('<div class="azh-responsive-dialog"></div>').appendTo('body');
                            $(dialog).css('top', event.clientY);
                            $(dialog).css('left', event.clientX);
                            $(document).on('click.azh-dialog', function(event) {
                                if (!$(event.target).closest('.azh-responsive-dialog').length) {
                                    $(dialog).remove();
                                    $(document).off('click.azh-dialog');
                                }
                            });
                            var table = $('<table><thead><tr><th>' + azh.i18n.device + '</th><th>' + azh.i18n.column_width + '</th><th>' + azh.i18n.column_offset + '</th></tr></thead><tbody></tbody></table>').appendTo(dialog);
                            for (var prefix in azh.options.column_responsive_prefixes) {
                                var row = $('<tr></tr>').appendTo($(table).find('tbody'));
                                $('<td>' + azh.options.column_responsive_prefixes[prefix] + '</td>').appendTo(row);
                                var col = $('<td></td>').appendTo(row);
                                $(data.node.open_line).find('.azh-column-width').each(function() {
                                    var width = this;
                                    if ($(width).data('responsive-prefix') == prefix && $(width).data('column-width')) {
                                        $('<input type="number" step="1" min="1" value="' + $(width).data('column-width') + '">').appendTo(col).on('change', function() {
                                            $(width).data('column-width', $(this).val());
                                            azh.store();
                                        });
                                    }
                                });
                                var col = $('<td></td>').appendTo(row);
                                $(data.node.open_line).find('.azh-column-offset').each(function() {
                                    var offset = this;
                                    if ($(offset).data('responsive-prefix') == prefix && $(offset).data('column-offset')) {
                                        $('<input type="number" step="1" min="0" value="' + $(offset).data('column-offset') + '">').appendTo(col).on('change', function() {
                                            $(offset).data('column-offset', $(this).val());
                                            azh.store();
                                        });
                                        ;
                                    }
                                });
                            }

                            event.stopPropagation();
                            return false;
                        });
                    } else {
                        $(data.node.wrapper).addClass("azh-column");
                    }
                    var indent = $(data.node.open_line).data('indent');
                    $(data.node.wrapper).find('.azh-line').each(function() {
                        $(this).find('.indent').html(Array($(this).data('indent') - indent).join("&nbsp;"));
                    });
                }
            }
        });

        $(azh.options.column_offset_patterns).each(function() {
            var column_offset_pattern = null;
            if (typeof this == 'string') {
                column_offset_pattern = new RegExp(this, 'gi');
            } else {
                column_offset_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = column_offset_pattern.exec(code_line)) != null && match.length == 2) {
                if ($(data.node.wrapper).is('[class*="azh-col-"]') && !$(data.node.wrapper).is('[class*="azh-col-offset-"]')) {
                    $(data.node.wrapper).addClass("azh-col-offset-" + match[1]);
                }
            }
        });

        $(azh.options.section_patterns).each(function() {
            var section_pattern = null;
            if (typeof this == 'string') {
                section_pattern = new RegExp(this, 'gi');
            } else {
                section_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = section_pattern.exec(code_line)) != null && match.length == 2) {
                $(data.node.wrapper).data('section', match[1]);
                $(data.node.wrapper).addClass('azh-section');
                var controls = $('<div class="azh-controls"></div>').prependTo(data.node.wrapper);
                var section = $(data.node.wrapper).closest('.azh-section');
                var s = $('.azh-library .azh-sections .azh-section[data-path="' + $(section).data('section') + '"]');
                if (s.length) {
                    $('<div class="azh-upload"  title="' + azh.i18n.upload_text + '"></div>').appendTo(controls).on('click', function() {
                        var code = default_get_code.call($(section).find('> .azh-wrapper'));
                        code = code.replace(new RegExp($(s).data('dir-uri'), 'g'), '{{azh-uri}}');
                        $.post(ajaxurl, {
                            'action': 'azh_upload',
                            'code': code,
                            'dir': $(s).data('dir'),
                            'file': $(section).data('section')
                        }, function(data) {
                            if (data == 1) {
                                alert(azh.i18n.done);
                            }
                        });
                        return false;
                    });
                }
                $('<div class="azh-section-collapse" title="' + azh.i18n.collapse + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-section');
                    if (!$(wrapper).is('.azh-empty')) {
                        var path = $.trim($(wrapper).find('> .azh-open-line .azh-group-title').data('content'));
                        $(wrapper).addClass('azh-section-collapsed');

                        var background_image = false;
                        if (path != '') {
                            var section = $('.azh-library .azh-sections .azh-section[data-path="' + path + '"]');
                            if (section.length > 0) {
                                background_image = $(section).css('background-image');
                            }
                        }
                        if (!background_image || background_image == 'none') {
                            background_image = 'url("' + azh.plugin_url + '/images/box.png")';
                        }

                        $('<div class="azh-section-preview" title="' + azh.i18n.expand + '">' + (background_image ? '<div class="azh-image" style=\'background-image: ' + background_image + ';\'></div>' : '') + '<div class="azh-title">' + path + '</div></div>').insertAfter($(wrapper).find('> .azh-open-line')).click(function() {
                            var wrapper = $(this).closest('.azh-section');
                            $(wrapper).find('> .azh-controls .azh-section-expand').click();
                            return false;
                        });
                    }
                    return false;
                });
                $('<div class="azh-section-expand" title="' + azh.i18n.expand + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-section');
                    $(wrapper).removeClass('azh-section-collapsed');
                    $(wrapper).find('> .azh-section-preview').remove();
                    cloneable_inline_refresh($('.azh-cloneable.azh-inline'));
                    equalize_controls_heights(wrapper);
                    return false;
                });
                $('<div class="azh-section-copy" title="' + azh.i18n.copy + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-section');
                    var path = $.trim($(wrapper).find('> .azh-open-line .azh-group-title').data('content'));
                    var code = default_get_code.call($(wrapper).find('> .azh-wrapper'));
                    $.post(ajaxurl, {
                        'action': 'azh_copy',
                        'code': code,
                        'path': path,
                    }, function(data) {
                        alert(azh.i18n.copied);
                    });
                    return false;
                });
                $('<div class="azh-edit"  title="' + azh.i18n.edit_text + '"></div>').appendTo(controls).on('click', function() {
                    var section = $(this).closest('.azh-section');
                    if ($(section).is('.azh-section-collapsed')) {
                        $(section).find('> .azh-controls .azh-section-expand').click();
                    }
                    $(section).addClass('edit');
                    var id = makeid();
                    var textarea = $('<textarea id="' + id + '"></textarea>');
                    $(textarea).val(default_get_code.call(section));
                    $(section).find('> :not(.azh-controls)').remove();
                    $(section).append(textarea);
                    $(this).closest('.azh-controls').find('.azh-done').show();
                    $(this).hide();

                    var aceeditor = ace.edit(id);
                    $(section).data('aceeditor', aceeditor);
                    aceeditor.setTheme("ace/theme/chrome");
                    aceeditor.getSession().setMode("ace/mode/html");
                    aceeditor.setOptions({
                        minLines: 10,
                        maxLines: 30,
                    });
                    return false;
                });
                $('<div class="azh-done"  title="' + azh.i18n.done + '"></div>').appendTo(controls).on('click', function() {
                    var section = $(this).closest('.azh-section');
                    $(section).removeClass('edit');
                    azh.add_code($(section).data('aceeditor').getSession().getValue(), $(section).prev(), $(section).next());
                    $(section).find('.azh-id').each(function() {
                        var id = $(this).data('get_code').call(this);
                        if (id in azh.ids) {
                            azh.ids[id] = undefined;
                        }
                    });
                    $(section).remove();
                    return false;
                }).hide();
            }
        });

        $(azh.options.group_patterns).each(function() {
            var group_pattern = null;
            if (typeof this == 'string') {
                group_pattern = new RegExp(this, 'gi');
            } else {
                group_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = group_pattern.exec(code_line)) != null && match.length == 2) {
                $(data.node.wrapper).data('group', match[1]);
                $(data.node.wrapper).addClass('azh-group');
            }
        });

        $(azh.options.object_patterns).each(function() {
            var object_pattern = null;
            if (typeof this == 'string') {
                object_pattern = new RegExp(this, 'gi');
            } else {
                object_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = object_pattern.exec(code_line)) != null && match.length == 1) {
                $(data.node.wrapper).addClass('azh-object');
            }
        });

        $(azh.options.inline_wrapper_patterns).each(function() {
            var inline_wrapper_pattern = null;
            if (typeof this == 'string') {
                inline_wrapper_pattern = new RegExp(this, 'gi');
            } else {
                inline_wrapper_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = inline_wrapper_pattern.exec(code_line)) != null && match.length == 1) {
                if (!('matched' in data.node)) {
                    $(data.node.wrapper).addClass('azh-inline');
                    $(data.node.wrapper).data('drug', false);
                    $(data.node.wrapper).on('mousedown', function() {
                        $('.azh-wrapper.azh-inline').each(function() {
                            $(this).data('drug', false);
                            $(this).css('outline', 'none');
                        });
                        $(this).data('drug', true);
                    });
                    $(data.node.wrapper).on('mousemove', function() {
                        if ($(this).data('drug')) {
                            $(this).css('outline', '1px solid black');
                        }
                    });
                    $(data.node.wrapper).on('mouseenter', function() {
                        if (!$(this).data('drug')) {
                            if ($(this).prev().is('.azh-inline') && $(this).prev().data('drug') || $(this).next().is('.azh-inline') && $(this).next().data('drug')) {
                                $(this).css('outline', '1px solid black');
                            }
                        }
                    });
                    $(data.node.wrapper).on('mouseleave', function() {
                        if (!$(this).data('drug')) {
                            if ($(this).prev().is('.azh-inline') && $(this).prev().data('drug') || $(this).next().is('.azh-inline') && $(this).next().data('drug')) {
                                $(this).css('outline', 'none');
                            }
                        }
                    });
                    $(data.node.wrapper).on('contextmenu', function(event) {
                        function getSelectionCharOffsetsWithin(element) {
                            var start = 0, end = 0;
                            var sel, range, priorRange;
                            if (typeof window.getSelection != "undefined") {
                                range = window.getSelection().getRangeAt(0);
                                priorRange = range.cloneRange();
                                priorRange.selectNodeContents(element);
                                priorRange.setEnd(range.startContainer, range.startOffset);
                                start = priorRange.toString().length;
                                end = start + range.toString().length;
                            } else if (typeof document.selection != "undefined" && (sel = document.selection).type != "Control") {
                                range = sel.createRange();
                                priorRange = document.body.createTextRange();
                                priorRange.moveToElementText(element);
                                priorRange.setEndPoint("EndToStart", range);
                                start = priorRange.text.length;
                                end = start + range.text.length;
                            }
                            return {
                                start: start,
                                end: end
                            };
                        }
                        event.preventDefault();
                        var text = $(this).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                        if (text.length) {
                            var offsets = getSelectionCharOffsetsWithin(text.get(0));
                            if (offsets.start != offsets.end) {
                                var content = default_get_code.call(text);

                                $(text).data('content', content.slice(offsets.start, offsets.end));
                                $(text).text($(text).data('content'));

                                if (offsets.start != 0) {
                                    var prev = $(this).clone(true);
                                    var prev_text = $(prev).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    $(this).before(prev);
                                    $(prev_text).data('content', content.slice(0, offsets.start));
                                    $(prev_text).text($(prev_text).data('content'));
                                }
                                if (offsets.end != content.length) {
                                    var next = $(this).clone(true);
                                    var next_text = $(next).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    $(this).after(next);
                                    $(next_text).data('content', content.slice(offsets.end, content.length));
                                    $(next_text).text($(next_text).data('content'));
                                }
                                azh.store();
                            } else {
                                var code = default_get_code.call(this);
                                for (var i = 0; i < azh.options.inline_replacers.length; i++) {
                                    var match = true;
                                    for (var j = 0; j < azh.options.inline_replacers[i].length; j++) {
                                        if (!azh.options.inline_replacers[i][j].from.exec(code)) {
                                            match = false;
                                            break;
                                        }
                                    }
                                    if (match) {
                                        for (var j = 0; j < azh.options.inline_replacers[i].length; j++) {
                                            code = code.replace(azh.options.inline_replacers[i][j].from, azh.options.inline_replacers[i][j].to);
                                        }
                                        azh.append_code(code, undefined, this);
                                        $(this).remove();
                                        azh.store();
                                        break;
                                    }
                                }
                            }
                        }
                    });
                    $(data.node.wrapper).on('mouseup', function(event) {
                        if (!$(this).data('drug')) {
                            if ($(this).prev().is('.azh-inline') && $(this).prev().data('drug')) {
                                var wrapper = $(this).prev();
                                if (wrapper) {
                                    var text = $(this).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    var wrapper_text = $(wrapper).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    if (text && wrapper_text) {
                                        var content = default_get_code.call(text);
                                        $(wrapper_text).data('content', $(wrapper_text).data('content') + content.replace(/[\t\r\n]*/g, ''));
                                        $(wrapper_text).text($(wrapper_text).data('content'));
                                        $(this).remove();
                                        azh.store();
                                    }
                                }
                            }
                            if ($(this).next().is('.azh-inline') && $(this).next().data('drug')) {
                                var wrapper = $(this).next();
                                if (wrapper) {
                                    var text = $(this).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    var wrapper_text = $(wrapper).find('.azh-line:not(.azh-open-line) .azh-content.azh-editable');
                                    if (text && wrapper_text) {
                                        var content = default_get_code.call(text);
                                        $(wrapper_text).data('content', content.replace(/[\t\r\n]*/g, '') + $(wrapper_text).data('content'));
                                        $(wrapper_text).text($(wrapper_text).data('content'));
                                        $(this).remove();
                                        azh.store();
                                    }
                                }
                            }
                        }
                        $('.azh-wrapper.azh-inline').each(function() {
                            $(this).data('drug', false);
                            $(this).css('outline', 'none');
                        });
                    });
                    $(document).off('mouseup.azh-inline').on('mouseup.azh-inline', function() {
                        setTimeout(function() {
                            $('.azh-wrapper.azh-inline').each(function() {
                                $(this).data('drug', false);
                                $(this).css('outline', 'none');
                            });
                        }, 0);
                    });
                }
            }
        });

        $(azh.options.element_wrapper_patterns).each(function() {
            var element_pattern = null;
            if (typeof this == 'string') {
                element_pattern = new RegExp(this, 'gi');
            } else {
                element_pattern = new RegExp(this);
            }
            var match = null;
            while ((match = element_pattern.exec(code_line)) != null && match.length == 2) {
                $(data.node.wrapper).addClass('azh-element-wrapper');
                setTimeout(function() {
                    if ($(data.node.wrapper).find('.azh-shortcode, .azh-editable, .azh-element-wrapper').length == 0) {
                        $(data.node.wrapper).addClass('azh-empty');
                    }
                });
                $(data.node.wrapper).on('click', function() {
                    if ($(this).is('.azh-empty')) {
                        var wrapper = this;
                        var open_line = $(wrapper).find('> .azh-open-line');
                        azh.child_suggestions = [];
                        $($(wrapper).parents('.azh-element-wrapper')).each(function() {
                            var title = $(this).find('> .azh-open-line .azh-element-title');
                            var element = $('.azh-library .azh-elements .azh-element[data-path="' + $(title).data('content') + '"]');
                            if (element.length && element.data('child-suggestions')) {
                                $(element.data('child-suggestions')).each(function() {
                                    azh.child_suggestions.push($(this).data('path'));
                                });
                            }
                        });
                        azh.open_elements_dialog(function(path, element) {
                            var title = $(open_line).find('.azh-element-title');
                            $(title).data('content', path);
                            $(title).text(path);
                            azh.append_code(element, undefined, open_line);
                            $(wrapper).removeClass('azh-empty');
                            $(wrapper).find('> .azh-controls .azh-element-collapse').click();
                            azh.store();
                        });
                        return false;
                    }
                });
                var controls = $('<div class="azh-controls"></div>').appendTo(data.node.wrapper);
                var title = $(data.node.open_line).find('.azh-element-title');
                var e = $('.azh-library .azh-elements .azh-element[data-path="' + $(title).data('content') + '"]');
                if (e.length) {
                    $('<div class="azh-upload"  title="' + azh.i18n.upload_text + '"></div>').appendTo(controls).on('click', function() {
                        var wrapper = $(this).closest('.azh-element-wrapper');
                        var open_line = $(wrapper).find('> .azh-open-line');
                        var title = $(open_line).find('.azh-element-title');
                        var e = $('.azh-library .azh-elements .azh-element[data-path="' + $(title).data('content') + '"]');
                        if (e.length) {
                            var lines = $(wrapper).find('> .azh-open-line').nextUntil($(wrapper).find('> .azh-close-line'));
                            var code = default_get_code.call(lines);
                            code = html_beautify(code);
                            code = code.replace(new RegExp($(e).data('dir-uri'), 'g'), '{{azh-uri}}');
                            $.post(ajaxurl, {
                                'action': 'azh_upload',
                                'code': code,
                                'dir': $(e).data('dir'),
                                'file': $(title).data('content')
                            }, function(data) {
                                if (data == 1) {
                                    alert(azh.i18n.done);
                                }
                            });
                        }
                        return false;
                    });
                }
                $('<div class="azh-element-collapse" title="' + azh.i18n.collapse + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-element-wrapper');
                    if (!$(wrapper).is('.azh-empty')) {
                        var path = $.trim($(wrapper).find('> .azh-open-line .azh-element-title').data('content'));
                        $(wrapper).addClass('azh-element-collapsed');

                        var background_image = false;
                        if (path != '') {
                            var element = $('.azh-library .azh-elements .azh-element[data-path="' + path + '"]');
                            if (element.length > 0) {
                                background_image = $(element).css('background-image');
                            }
                        }
                        if (!background_image || background_image == 'none') {
                            background_image = 'url("' + azh.plugin_url + '/images/box.png")';
                        }

                        $('<div class="azh-element-preview" title="' + azh.i18n.expand + '">' + (background_image ? '<div class="azh-image" style=\'background-image: ' + background_image + ';\'></div>' : '') + '<div class="azh-title">' + path + '</div></div>').insertAfter($(wrapper).find('> .azh-open-line')).click(function() {
                            var wrapper = $(this).closest('.azh-element-wrapper');
                            $(wrapper).find('> .azh-controls .azh-element-expand').click();
                            return false;
                        });
                    }
                    return false;
                });
                $('<div class="azh-element-expand" title="' + azh.i18n.expand + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-element-wrapper');
                    $(wrapper).removeClass('azh-element-collapsed');
                    $(wrapper).find('> .azh-element-preview').remove();
                    cloneable_inline_refresh($('.azh-cloneable.azh-inline'));
                    equalize_controls_heights(wrapper);
                    return false;
                });
                $('<div class="azh-element-clear" title="' + azh.i18n.clear + '"></div>').appendTo(controls).on('click', function() {
                    $(this).closest('.azh-element-wrapper').addClass('azh-empty');
                    var wrapper = $(this).closest('.azh-element-wrapper');
                    $(wrapper).removeClass('azh-element-collapsed');
                    $(wrapper).find('> .azh-open-line .azh-element-title').data('content', ' ');
                    $(wrapper).find('> .azh-open-line').nextUntil($(wrapper).find('> .azh-close-line')).remove();
                    azh.store();
                    return false;
                });
                $('<div class="azh-element-copy" title="' + azh.i18n.copy + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-element-wrapper');
                    var path = $.trim($(wrapper).find('> .azh-open-line .azh-element-title').data('content'));
                    var lines = $(wrapper).find('> .azh-open-line').nextUntil($(wrapper).find('> .azh-close-line'));
                    var code = default_get_code.call(lines);
                    $.post(ajaxurl, {
                        'action': 'azh_copy',
                        'code': code,
                        'path': path,
                    }, function(data) {
                        alert(azh.i18n.copied);
                    });
                    return false;
                });
                $('<div class="azh-element-paste" title="' + azh.i18n.paste + '"></div>').appendTo(controls).on('click', function() {
                    var wrapper = $(this).closest('.azh-element-wrapper');
                    var open_line = $(wrapper).find('> .azh-open-line');
                    $.post(ajaxurl, {
                        'action': 'azh_paste',
                        dataType: 'text',
                    }, function(data) {
                        data = JSON.parse(data);
                        if ('code' in data) {
                            $(wrapper).find('> .azh-open-line').nextUntil($(wrapper).find('> .azh-close-line')).remove();
                            azh.append_code(data.code, undefined, open_line);
                            $(wrapper).removeClass('azh-empty');
                            $(wrapper).removeClass('azh-element-collapsed');
                        }
                        if ('path' in data) {
                            $(wrapper).find('> .azh-open-line .azh-element-title').text(data.path);
                            $(wrapper).find('> .azh-open-line .azh-element-title').data('content', data.path);
                        }
                        azh.store();
                    });
                    return false;
                });
            }
        });
    });
    $(document).off('azh-process.base').on('azh-process.base', function(sender, data) {
        for (var i = data.added_from; i < data.tree.children.length; i++) {
            var added_node = data.tree.children[i];
            $(added_node.wrapper).find('[class*="azh-col-"]').each(function() {
                $(this).parentsUntil('.azh-row', '.azh-wrapper').each(function() {
                    var node = $(this).data('node');
                    if (!('matched' in node)) {
                        if ($(node.open_line).find('.azh-editable').length == 0) {
                            $(this).addClass('azh-clearfix');
                            $(node.open_line).hide();
                            $(node.close_line).hide();
                        }
                    }
                });
            });
            $(added_node.wrapper).find('.azh-wrapper:not(.azh-element-wrapper)').each(function() {
                var node = $(this).data('node');
                if (!('matched' in node)) {
                    if ($(this).find('.azh-shortcode, .azh-editable, .azh-element-wrapper').length == 0) {
                        $(this).hide();
                    }
                }
            });
            $(added_node.wrapper).find('.azh-row').each(function() {
                $(this).find('.azh-column').each(function() {
                    var width = (1 / $(this).parent().find('> .azh-column').length) * 100;
                    $(this).css('width', width + '%');
                });
            });

            $(added_node.wrapper).find('.azh-id').each(function() {
                var id = $(this).data('get_code').call(this);
                if (!(id in azh.ids) || (typeof azh.ids[id] == 'undefined')) {
                    azh.ids[id] = [];
                }
                azh.ids[id].push(this);
            });
            var ids = {};
            for (var id in azh.ids) {
                if ((typeof azh.ids[id] != 'undefined') && azh.ids[id].length > 1) {
                    var unoque_id = makeid();
                    for (var j = 0; j < azh.ids[id].length; j++) {
                        $(azh.ids[id][j]).data('content', unoque_id);
                        $(azh.ids[id][j]).text(unoque_id);
                        $(azh.ids[id][j]).removeClass('azh-editable');
                        $(azh.ids[id][j]).removeClass('azh-content');
                        $(azh.ids[id][j]).attr('contenteditable', 'false');
                    }
                    ids[unoque_id] = azh.ids[id];
                    azh.ids[id] = undefined;
                }
            }
            azh.ids = $.extend(azh.ids, ids);

            $('.azh-cloneable > .azh-collapsed:first-child').each(function() {
                if ($(this).is('.azh-element-wrapper')) {
                    $(this).removeClass('.azh-collapsed');
                } else {
                    $(this).click();
                }
            });
        }
        azh.store();
    });
    $(document).off('azh-process.dialog').on('azh-process.dialog', function(sender, data) {
        function hide(elements) {
            $(elements).each(function() {
                var visible = false;
                $(this).children().each(function() {
                    if ($(this).css('display') != 'none') {
                        visible = true;
                        return false;
                    }
                });
                if (!visible) {
                    $(this).hide();
                }
            });
        }
        for (var i = data.added_from; i < data.tree.children.length; i++) {
            var added_node = data.tree.children[i];
            $(added_node.wrapper).find('.azh-text').each(function() {
                if ($(this).children().length === 0) {
                    $(this).hide();
                }
            });
            hide($(added_node.wrapper).find('.azh-text'));
            hide($(added_node.wrapper).find('.indent'));
            hide($(added_node.wrapper).find('.azh-line'));

            $(added_node.wrapper).find('.azh-editable:not(.azh-group-title)').each(function() {
                var editable = this;
                var control = $(editable).wrap('<div class="azh-control"></div>').parent();
                $(control).prepend('<div class="azh-description">' + ($(editable).data('description') ? $(editable).data('description') : '') + '</div>');
                $(editable).on('mousedown', function(event) {
                    if (event.which == 2) {
                        var description = prompt(azh.description, $(this).closest('.azh-editable').data('description'));
                        if (description != null) {
                            $(this).closest('.azh-editable').data('description', description);
                            $(this).closest('.azh-control').find('.azh-description').text(description);
                            azh.store();
                        }
                        return false;
                    }
                });
            });
            $(added_node.wrapper).find('.azh-editable.azh-group-title').each(function() {
                var editable = this;
                var control = $(editable).wrap('<div class="azh-group-title-control"></div>').parent();
                $(control).prepend('<div class="azh-description">' + ($(editable).data('description') ? $(editable).data('description') : '') + '</div>');
                $(control).on('mousedown', function(event) {
                    if (event.which == 2) {
                        var description = prompt(azh.description, $(this).find('.azh-editable').data('description'));
                        if (description !== null) {
                            $(this).find('.azh-editable').data('description', description);
                            $(this).find('.azh-description').text(description);
                            azh.store();
                        }
                        return false;
                    }
                });
            });

            $(added_node.wrapper).find('.azh-wrapper.azh-inline').andSelf().filter('.azh-wrapper.azh-inline').each(function() {
                if ($(this).find('> .azh-open-line div.azh-control').length) {
                    $(this).addClass('azh-styles');
                }
                if ($(this).find('.azh-link').length) {
                    $(this).addClass('azh-link');
                }
            });

            $(added_node.wrapper).find('.azh-wrapper.azh-object').andSelf().filter('.azh-wrapper.azh-object').each(function() {
                var fields = {
                    '.azh-editable.azh-link': 'url'
                };
                for (var selector in fields) {
                    var data_attr = fields[selector];
                    var linked = {};
                    $(this).find(selector).each(function() {
                        if ($(this).data(data_attr)) {
                            if (!($(this).data(data_attr) in linked)) {
                                linked[$(this).data(data_attr)] = [];
                            }
                            linked[$(this).data(data_attr)].push(this);
                        }
                    });
                    for (var data in linked) {
                        $(linked[data]).each(function() {
                            if ($(this).data('description')) {
                                $(this).data('linked', $(linked[data]).not(this));
                                $(linked[data]).not(this).closest('.azh-control').addClass('azh-hide').hide();
                                $(this).on('change', function() {
                                    var value = $(this).data(data_attr);
                                    $($(this).data('linked')).each(function() {
                                        $(this).data(data_attr, value);
                                    });
                                    azh.store();
                                });
                                return false;
                            }
                        });
                    }
                }
            });
            if ($(added_node.wrapper).is('.azh-wrapper.azh-section')) {
                $(added_node.wrapper).find('> .azh-controls .azh-section-collapse').click();
            }
            $(added_node.wrapper).find('.azh-element-wrapper').each(function() {
                if ($(this).find('.azh-shortcode, .azh-editable, .azh-element-wrapper').length > 0) {
                    $(this).find('> .azh-controls .azh-element-collapse').click();
                }
            });
            $(added_node.wrapper).find('.azh-cloneable.ui-sortable').each(function() {
                if ($(this).find('> .azh-element-wrapper').length > 0) {
                    $(this).addClass('azh-cloneable-elements');
                }
            });
            $(added_node.wrapper).find('.azh-cloneable-elements').each(function() {
                function make_controls($element) {
                    var $controls = $('<div class="azh-controls"></div>').prependTo($element).on('click', function(e) {
                        e.stopPropagation();
                    });
                    $('<div class="azh-move" title="' + azh.i18n.move + '"></div>').appendTo($controls);
                    $('<div class="azh-clone" title="' + azh.i18n.clone + '"></div>').appendTo($controls);
                    $('<div class="azh-remove" title="' + azh.i18n.remove + '"></div>').appendTo($controls);
                    $('<div class="azh-add" title="' + azh.i18n.add + '"></div>').appendTo($controls);
                    return $controls;
                }
                function controls_events($controls) {
                    $controls.find('.azh-clone').off('click').on('click', function() {
                        var $element = $(this).closest('.azh-element-wrapper');
                        var $new_element = $element.clone(true);
                        $element.after($new_element);
                        azh.store();
                        return false;
                    });
                    $controls.find('.azh-remove').off('click').on('click', function() {
                        var $cloneable = $(this).closest('.azh-cloneable-elements');
                        var $element = $(this).closest('.azh-element-wrapper');
                        $element.remove();
                        if ($cloneable.children().length === 0) {
                            azh.append_code('<div data-element=" "></div>', undefined, $cloneable);
                            var $element = $cloneable.parent().find('.azh-element-wrapper');
                            $element.detach().appendTo($cloneable);
                            controls_events(make_controls($element));
                        }
                        azh.store();
                        return false;
                    });
                    $controls.find('.azh-add').off('click').on('click', function() {
                        var $cloneable = $(this).closest('.azh-cloneable-elements');
                        var $element = $(this).closest('.azh-element-wrapper');
                        azh.append_code('<div data-element=" "></div>', undefined, $element);
                        var $new_element = $element.next();
                        controls_events(make_controls($new_element));
                        $new_element.addClass('azh-empty');
                        $new_element.trigger('click');
                        azh.store();
                        return false;
                    });
                }
                var $cloneable = $(this)
                $cloneable.sortable("option", "connectWith", ".azh-cloneable-elements");
                $cloneable.sortable("option", "update", function(event, ui) {
                    if ($(this).children().length === 0) {
                        azh.append_code('<div data-element=" "></div>', undefined, $(this));
                        var $element = $(this).parent().find('.azh-element-wrapper');
                        $element.detach().appendTo(this);
                        controls_events(make_controls($element));
                    }
                    azh.store();
                });
                $cloneable.children().find('> .azh-controls > .azh-move, > .azh-controls > .azh-remove').show();

                $cloneable.children().each(function() {
                    var $controls = $(this).find('> .azh-controls:first-child');
                    $('<div class="azh-add" title="' + azh.i18n.add + '"></div>').appendTo($controls);
                    controls_events($controls);
                });
            });
            equalize_controls_heights(added_node.wrapper);


            //            $(added_node.wrapper).find('.azh-group:not(.azh-section)').andSelf().filter('.azh-group:not(.azh-section)').each(function() {
            //                $(this).find('> .azh-wrapper').slideUp();
            //                $(this).on('mouseenter', function() {
            //                    $(this).find('> .azh-wrapper').slideDown();
            //                });
            //                $(this).on('mouseleave', function() {
            //                    $(this).find('> .azh-wrapper').slideUp();
            //                });
            //            });

            for (var selector in azh.helpers) {
                $(added_node.wrapper).find(selector).andSelf().filter(selector).each(function() {
                    var text = $('<div class="azh-helper-text"></div>').html(azh.helpers[selector]);
                    $(text).html($(text).text());
                    var helper = $('<div class="azh-helper"></div>').appendTo(this).on('click', function() {
                        $(this).remove();
                    });
                    $(helper).append(text);
                });
            }

        }
    });
    $(document).off('azh-process.grid').on('azh-process.grid', function(sender, data) {
        function grid_editor(grid) {
            var prev_column = false;
            $(grid).find('> [class*="azh-col-"]').each(function() {
                var column = this;
                var column_width = $(column).find('> .azh-line > .azh-text > .azh-column-width');
                if (prev_column) {
                    var resizer = $('<div class="azh-width-resizer"></div>').appendTo(prev_column);
                    $(resizer).data('next-column', column);
                    $(resizer).on('mousedown', function(e) {
                        $(resizer).addClass('drag');
                        $(resizer).data('pageX', e.pageX);
                        $(this).closest('.azh-grid').addClass('drag');
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                }
                prev_column = column;
            });
            $(grid).on('click', function(e) {
                if ($(this).is('.drag')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
            $(grid).on('mouseup', function(e) {
                $(this).removeClass('drag');
                $(this).find('.azh-width-resizer.drag').removeClass('drag');
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
            $(grid).on('mousemove', function(e) {
                if (e.buttons == 0) {
                    $(this).removeClass('drag');
                }
                var resizer = $(this).find('.azh-width-resizer.drag');
                if ($(this).is('.drag') && resizer.length) {
                    var column = $(resizer).closest('[class*="azh-col-"]');
                    var current_width = parseInt($(column).attr('class').match(/azh-col-(\d+)/)[1], 10);
                    var column_width = false;
                    $(column).find('> .azh-line .azh-column-width').each(function() {
                        if ($(this).data('column-width') == current_width) {
                            column_width = this;
                        }
                    });
                    var next_column = $(resizer).data('next-column');
                    var next_current_width = parseInt($(next_column).attr('class').match(/azh-col-(\d+)/)[1], 10);
                    var next_column_width = false;
                    $(next_column).find('> .azh-line .azh-column-width').each(function() {
                        if ($(this).data('column-width') == next_current_width) {
                            next_column_width = this;
                        }
                    });
                    if (e.pageX < $(resizer).offset().left && e.pageX < $(resizer).data('pageX')) {
                        if (current_width > 1) {
                            $(column).removeClass('azh-col-' + current_width);
                            $(column).addClass('azh-col-' + (current_width - 1));
                            $(column_width).data('column-width', current_width - 1);
                            $(next_column).removeClass('azh-col-' + next_current_width);
                            $(next_column).addClass('azh-col-' + (next_current_width + 1));
                            $(next_column_width).data('column-width', next_current_width + 1);
                            azh.store();
                        }
                    } else {
                        if (e.pageX > ($(resizer).offset().left + $(resizer).width()) && e.pageX > $(resizer).data('pageX')) {
                            if (next_current_width > 1) {
                                $(column).removeClass('azh-col-' + current_width);
                                $(column).addClass('azh-col-' + (current_width + 1));
                                $(column_width).data('column-width', current_width + 1);

                                $(next_column).removeClass('azh-col-' + next_current_width);
                                $(next_column).addClass('azh-col-' + (next_current_width - 1));
                                $(next_column_width).data('column-width', next_current_width - 1);
                                azh.store();
                            }
                        }
                    }
                }
                $(resizer).data('pageX', e.pageX);
                e.preventDefault();
            });
        }
        for (var i = data.added_from; i < data.tree.children.length; i++) {
            var added_node = data.tree.children[i];
            $(added_node.wrapper).find('[class*="azh-col-"]').each(function() {
                $(this).parent().addClass('azh-grid');
            });
            if ($(added_node.wrapper).is('.azh-grid')) {
                grid_editor(added_node.wrapper);
            }
            $(added_node.wrapper).find('.azh-grid').each(function() {
                grid_editor(this);
            });
        }
    });
    $(document).off('azh-process.isotope').on('azh-process.isotope', function(sender, data) {
        function grid_filters_rebuild(gf) {
            var labels = {};
            $(gf.items_wrapper).find('.azh-item').each(function() {
                var item = this;
                var item_filters = $(item).data('item_filters') || {};
                for (var filter in item_filters) {
                    if (filter.length > 0) {
                        labels[filter] = gf.labels[filter];
                    }
                }
            });
            gf.labels = labels;
            for (var i = 0; i < gf.lines.length; i++) {
                $(gf.lines[i]).remove();
            }
            gf.lines = [];
            var current_line = gf.all;
            for (var c in gf.labels) {
                var filter = $(gf.all).clone(true);
                gf.lines.push(filter);
                $(current_line).after(filter);
                current_line = filter;

                $(filter).find('.azh-text').each(function() {
                    if ($(this).data('get_code')) {
                        var code_line = $(this).data('get_code').call(this);
                        if (code_line.match(/data-filter=['"]\*['"]/i)) {
                            code_line = code_line.replace(/data-filter=['"]\*['"]/i, 'data-filter=".' + c + '"');
                            code_line = code_line.replace(/az-is-checked/g, '');
                            $(this).data('code_line', code_line);
                            $(this).html(htmlEncode(code_line));
                        }
                    }
                });

                var label = $(filter).find('.azh-content');
                $(label).data('content', gf.labels[c]);
                $(label).text(gf.labels[c]);
            }
        }
        for (var i = data.added_from; i < data.tree.children.length; i++) {
            var added_node = data.tree.children[i];

            var grid_filters = {
                lines: [],
                labels: {}
            };
            $(added_node.wrapper).find('.azh-wrapper').each(function() {
                var wrapper = this;
                var code_line = $($(wrapper).data('node').open_line).data('get_code').call($(wrapper).data('node').open_line);
                if (code_line.match(/data-isotope-filters/i)) {
                    grid_filters.filters_wrapper = wrapper;
                    $(wrapper).find('.azh-line').each(function() {
                        var line = this;
                        var code_line = $(line).data('get_code').call(line);
                        var match = code_line.match(/data-filter=['"]\.([\w\d-_]+)['"]/i);
                        if (match) {
                            var w = $(line).parentsUntil(wrapper, '.azh-wrapper').last();
                            if (w.length > 0) {
                                grid_filters.lines.push(w);
                            } else {
                                grid_filters.lines.push(line);
                            }
                            var label = $(grid_filters.lines[grid_filters.lines.length - 1]).find('.azh-content');
                            $(label).attr('contenteditable', 'false');
                            grid_filters.labels[match[1]] = $.trim($(label).data('get_code').call(label));
                        } else {
                            if (code_line.match(/data-filter=['"]\*['"]/i)) {
                                var w = $(line).parentsUntil(wrapper, '.azh-wrapper').last();
                                if (w.length > 0) {
                                    grid_filters.all = w;
                                } else {
                                    grid_filters.all = line;
                                }
                                var label = $(grid_filters.all).find('.azh-content');
                                $(label).attr('contenteditable', 'false');
                            }
                        }
                    });
                    $(wrapper).find('.azh-controls .azh-clone, .azh-controls .azh-remove').hide();
                }
                if (code_line.match(/data-isotope-items/i)) {
                    $(wrapper).addClass('azh-items');
                    grid_filters.items_wrapper = wrapper;
                    $(wrapper).data('filters', grid_filters);
                    $(wrapper).find('.azh-wrapper').each(function() {
                        var item = this;
                        $(item).addClass('azh-item');
                        var code_line = $($(item).data('node').open_line).data('get_code').call($(item).data('node').open_line);
                        var match = code_line.match(/class=['"]([ \w-_]+)['"]/i);
                        if (match) {
                            var classes = match[1].split(' ');
                            for (var filter_class in grid_filters.labels) {
                                if (classes.indexOf(filter_class) >= 0) {
                                    var item_filters = $(item).data('item_filters') || {};
                                    item_filters[filter_class] = true;
                                    $(item).data('item_filters', item_filters);
                                }
                            }
                        }
                        if ($(item).data('item_filters') || $(item).parentsUntil(wrapper, '.azh-wrapper').length == 0) {
                            $('<div class="azh-filters"></div>').appendTo($(item).find('.azh-controls')).on('click', function(event) {
                                var item = $(this).closest('.azh-item');
                                if ($(item).find('.azh-tags-dialog').length) {
                                    $(item).find('.azh-tags-dialog button').click();
                                } else {
                                    var gf = $(this).closest('.azh-items').data('filters');
                                    var dialog = $('<div class="azh-tags-dialog"></div>').appendTo($(item).find('.azh-controls'));
                                    var tags = $('<textarea></textarea>').appendTo(dialog);
                                    var item_filters = $(item).data('item_filters') || {};
                                    $(tags).val($.map(Object.keys(item_filters), function(val, i) {
                                        return grid_filters.labels[val];
                                    }).join("\n"));

                                    var done = $('<button class="button">' + azh.i18n.done + '</button>').appendTo(dialog).on('click', function() {
                                        var item = $(this).closest('.azh-item');
                                        var item_filters = {};
                                        var filters = {};
                                        var labels = $(tags).val().split("\n");
                                        for (var i = 0; i < labels.length; i++) {
                                            var filter = $.trim(labels[i].toLowerCase());
                                            if (filter != '') {
                                                filter = filter.replace(/[^\w]/g, '-');
                                                item_filters[filter] = true;
                                                gf.labels[filter] = labels[i];
                                            }
                                        }
                                        var old_item_filters = $(item).data('item_filters') || {};
                                        $(item).data('item_filters', item_filters);


                                        $($(item).data('node').open_line).find('.azh-text, .azh-hide').each(function() {
                                            if ($(this).data('get_code')) {
                                                var code_line = $(this).data('get_code').call(this);
                                                var match = code_line.match(/class=['"]([ \w-_]+)['"]/i);
                                                if (match) {
                                                    var classes = match[1].split(' ');
                                                    var diff = $(classes).not(Object.keys(old_item_filters)).get();
                                                    classes = diff.concat(Object.keys(item_filters));
                                                    code_line = code_line.replace(match[0], 'class="' + classes.join(' ') + '"');
                                                    if ($(this).is('.azh-hide')) {
                                                        $(this).data('content', code_line);
                                                    } else {
                                                        if ($(this).is('.azh-text')) {
                                                            $(this).data('code_line', code_line);
                                                        }
                                                    }
                                                    $(this).html(htmlEncode(code_line));
                                                }
                                            }
                                        });

                                        $(dialog).remove();
                                        grid_filters_rebuild(gf);
                                        azh.store();
                                        return false;
                                    });
                                }
                                return false;
                            });
                        }
                    });
                    if ($(wrapper).find('.azh-editable').length == 0) {
                        $(wrapper).hide();
                    }
                }
                if (code_line.match(/class="grid-sizer"/i)) {
                    $(wrapper).hide();
                }
            });
        }
    });
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    if (!('azh' in $.QueryString)) {
        $(function() {
            azh.library_init();
        });
    }
}(window.jQuery);