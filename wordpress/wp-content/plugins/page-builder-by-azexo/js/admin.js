(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    var $$ = $;
    var $dialog_body = $body;
    var $dialog_document = $document;
    var $dialog_window = $window;
    var dialog_window = window;
    window.azh = $.extend({}, window.azh);
    azh.parse_query_string = function (a) {
        if (a == "") {
            return {};
        }
        var b = {};
        for (var i = 0; i < a.length; ++i) {
            var p = a[i].split('=');
            if (p.length != 2) {
                continue;
            }
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    };
    azh.elements_cache = {};
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    $.fn.azhSerializeObject = function () {
        var serializedArray = this.serializeArray(),
                data = {};

        var parseObject = function (dataContainer, key, value) {
            var isArrayKey = /^[^\[\]]+\[]/.test(key),
                    isObjectKey = /^[^\[\]]+\[[^\[\]]+]/.test(key),
                    keyName = key.replace(/\[.*/, '');

            if (isArrayKey) {
                if (!dataContainer[ keyName ]) {
                    dataContainer[ keyName ] = [];
                }
            } else {
                if (!isObjectKey) {
                    if (dataContainer.push) {
                        dataContainer.push(value);
                    } else {
                        dataContainer[ keyName ] = value;
                    }

                    return;
                }

                if (!dataContainer[ keyName ]) {
                    dataContainer[ keyName ] = {};
                }
            }

            var nextKeys = key.match(/\[[^\[\]]*]/g);

            nextKeys[ 0 ] = nextKeys[ 0 ].replace(/\[|]/g, '');

            return parseObject(dataContainer[ keyName ], nextKeys.join(''), value);
        };

        $.each(serializedArray, function () {
            parseObject(data, this.name, this.value);
        });
        return data;
    };
    function frontend_editor_frame_init() {
        if ('azh' in $.QueryString && $.QueryString['azh'] == 'customize') {
            if (window === window.parent) {
                if (window.adminpage === 'post-php') {
                    if (azh.edit_post_frontend_link) {
                        azh.post_frontend_frame = false;
                        $window.on('load', function () {
                            $body = $('body');
                            azh.post_frontend_frame = $('<iframe src="' + azh.edit_post_frontend_link + '"></iframe>').appendTo($body);
                            azh.post_frontend_frame.css({
                                'border': '0',
                                'position': 'fixed',
                                'left': '0',
                                'top': '0',
                                'z-index': '0',
                                'height': '100%',
                                'width': '100%'
                            });

                            $body.find('> *').hide();
                            $body.find('#wpwrap > *').hide();
                            $body.find('#wpwrap').show();
                            $body.css({
                                'overflow': 'hidden',
                                'background-color': 'transparent'
                            });

                            azh.post_frontend_frame.show();

                            var win = azh.post_frontend_frame.get(0).contentWindow;
                            var doc = azh.post_frontend_frame.get(0).contentDocument || azh.post_edit_frame.get(0).contentWindow.document;
                            win.addEventListener("DOMContentLoaded", function () {
                                azh.$ = azh.post_frontend_frame.get(0).contentWindow.jQuery;
                                azh.body = azh.post_frontend_frame.contents().find('body');
                                azh.window = azh.$(azh.post_frontend_frame.get(0).contentWindow);
                                azh.document = azh.$(azh.post_frontend_frame.get(0).contentDocument || azh.post_frontend_frame.get(0).contentWindow.document);
                                azh.prepare();
                                var library = azh.body.find('#azexo-html-library').detach().html();
                                if (library) {
                                    $body.append('<div id="azexo-html-library">' + library + '</div>');
                                    azh.customizer_init();
                                    azh.$('#wpadminbar').hide();
                                    azh.body.removeClass('admin-bar');
                                    azh.$('html').get(0).style.setProperty("margin-top", "0", "important");


                                    azh.document.on('mousemove mouseup mousedown', function (event, data) {
                                        event.clientX = event.clientX + (azh.device_left ? azh.device_left : 0);
                                        event.pageX = event.pageX + (azh.device_left ? azh.device_left : 0);
                                        event.pageY = event.pageY - azh.window.scrollTop();
                                        $document.trigger(event, data);
                                    });

                                    $('html').css('background', 'darkgray');
                                    var $devices = $('<div class="azh-devices"></div>').appendTo('#azexo-html-library');
                                    for (var prefix in azh.device_prefixes) {
                                        $('<div data-prefix="' + prefix + '" title="' + azh.device_prefixes[prefix].label + '"></div>').appendTo($devices);
                                    }
                                    $devices.children().first().addClass('azh-active');
                                    $devices.find('[data-prefix]').on('click', function () {
                                        $devices.find('.azh-active').removeClass('azh-active');
                                        $(this).addClass('azh-active');
                                        azh.set_device_width($(this).data('prefix'));
                                    });
                                    azh.minimum_device_left = 0;
                                    azh.set_minimum_device_left = function (left) {
                                        azh.minimum_device_left = left;
                                        azh.set_device_width(azh.device_prefix);
                                    };
                                    azh.set_device_width = function (prefix) {
                                        if (azh.post_frontend_frame) {
                                            var $controls_container = $('.azh-controls-container');
                                            if (prefix === 'lg') {
                                                azh.$('[name="viewport"]').attr('content', 'width=device-width,initial-scale=1.0');
                                                var left = 0;
                                                if (left < azh.minimum_device_left) {
                                                    left = azh.minimum_device_left;
                                                }
                                                azh.device_left = left;
                                                var width = $body.prop("clientWidth") - left;
                                                azh.post_frontend_frame.animate({
                                                    left: left + 'px',
                                                    width: width + 'px'
                                                }, function () {
                                                    $window.trigger('azh-set-device-width');
                                                });
                                                $controls_container.animate({
                                                    left: left + 'px',
                                                    width: width + 'px'
                                                });
                                            } else {
                                                azh.$('[name="viewport"]').attr('content', 'width=' + azh.device_prefixes[prefix].width + (azh.device_prefixes[prefix].height ? ',height=' + azh.device_prefixes[prefix].height : '') + ',initial-scale=1.0');
                                                var left = $body.prop("clientWidth") / 2 - azh.device_prefixes[prefix].width / 2;
                                                var width = azh.device_prefixes[prefix].width;
                                                if (left < azh.minimum_device_left) {
                                                    left = azh.minimum_device_left;
                                                }
                                                azh.device_left = left;
                                                azh.post_frontend_frame.animate({
                                                    left: left + 'px',
                                                    width: (width + 1) + 'px'
                                                }, function () {
                                                    $window.trigger('azh-set-device-width');
                                                });
                                                $controls_container.animate({
                                                    left: left + 'px',
                                                    width: width + 'px'
                                                });
                                            }
                                            azh.$('body').removeClass(Object.keys(azh.device_prefixes).map(function(item){return 'azh-' + item}).join(' ')).addClass('azh-' + prefix);;
                                        }
                                        $('.azh-controls-container > .azh-active .azh-responsive [data-prefix="' + prefix + '"]').trigger('click');
                                        $('.azh-context-menu .azh-responsive [data-prefix="' + prefix + '"]').trigger('click');
                                        $('.azh-context-menu').off('azh-showed').on('azh-showed', function () {
                                            $(this).find('.azh-responsive [data-prefix="' + prefix + '"]').trigger('click');
                                        });
                                        azh.device_prefix = prefix;
                                    };
                                    azh.toolbar_width = 45;
                                    azh.panel_width = 300;
                                    azh.hierarhy_height = 300;
                                    azh.utility_top = 0;
                                    azh.utility_left = 45;
                                    $document.on('azh-show-utility', function (event) {
                                        $('.azh-section-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-column-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-element-controls:not(.azh-active) .azh-utility-wrapper .azh-utility').hide();
                                        $('.azh-controls-container').off('mouseup.azh-show-utility').on('mouseup.azh-show-utility', function () {
                                            $('.azh-section-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-column-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-element-controls:not(.azh-active) .azh-utility-wrapper .azh-utility').hide();
                                        });
                                        $(event.target).css({
                                            top: '0',
                                            width: azh.panel_width + 'px',
                                            height: 'calc(100vh - ' + azh.hierarhy_height + 'px)',
                                            left: azh.toolbar_width + 'px'
                                        });
                                        $('.azh-elements-hierarchy').css({
                                            top: 'auto',
                                            bottom: '0px',
                                            height: azh.hierarhy_height + 'px',
                                            width: azh.panel_width + 'px',
                                            left: azh.toolbar_width + 'px'
                                        });
                                        if ($('.azh-utility-panel').data('closed')) {
                                            $(event.target).hide();
                                        } else {
                                            $(event.target).show();
                                        }
                                    });
                                    $('<div class="azh-utility-panel"></div>').appendTo($body).on('click', function () {
                                        var $this = $(this);
                                        if ($this.data('closed')) {
                                            $this.data('closed', false);
                                            azh.set_minimum_device_left(azh.panel_width + azh.toolbar_width);
                                            $('.azh-section-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-column-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-element-controls.azh-active .azh-utility-wrapper .azh-utility').css('display', '');
                                            $('.azh-section-controls.azh-active, .azh-column-controls.azh-active, .azh-element-controls.azh-active').css('display', '');
                                            $('.azh-section-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-column-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-element-controls.azh-active .azh-utility-wrapper .azh-utility').animate({
                                                left: azh.toolbar_width + 'px'
                                            });
                                            $('.azh-elements-hierarchy').show();
                                            $('.azh-elements-hierarchy').animate({
                                                left: azh.toolbar_width + 'px'
                                            });
                                            $('.azh-utility-panel').animate({
                                                width: (azh.panel_width + azh.toolbar_width) + 'px'
                                            });
                                        } else {
                                            $this.data('closed', true);
                                            azh.set_minimum_device_left(azh.toolbar_width);
                                            $('.azh-section-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-column-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-element-controls.azh-active .azh-utility-wrapper .azh-utility').animate({
                                                left: (azh.toolbar_width - azh.panel_width) + 'px'
                                            }, 400, function () {
                                                $('.azh-section-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-column-controls.azh-active .azh-utility-wrapper .azh-utility, .azh-element-controls.azh-active .azh-utility-wrapper .azh-utility').hide();
                                                $('.azh-section-controls.azh-active, .azh-column-controls.azh-active, .azh-element-controls.azh-active').css('display', 'none');
                                            });
                                            $('.azh-elements-hierarchy').animate({
                                                left: (azh.toolbar_width - azh.panel_width) + 'px'
                                            }, 400, function () {
                                                $('.azh-elements-hierarchy').hide();
                                            });
                                            $('.azh-utility-panel').animate({
                                                width: azh.toolbar_width + 'px'
                                            });
                                        }
                                        $('.azh-section-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-column-controls:not(.azh-active) .azh-utility-wrapper .azh-utility, .azh-element-controls:not(.azh-active) .azh-utility-wrapper .azh-utility').hide();
                                        return false;
                                    }).css({
                                        top: '0',
                                        bottom: '0',
                                        width: (azh.panel_width + azh.toolbar_width) + 'px',
                                        left: '0'
                                    }).show();
                                    setTimeout(function () {
                                        azh.set_minimum_device_left(azh.panel_width + azh.toolbar_width);
                                    });
                                }
                            }, true);
                            azh.post_frontend_frame.on('load', function () {
                                azh.frontend_init = azh.window.get(0).azh.frontend_init;
                                azh.post_frontend_frame.show();
                                azh.body.find('.azexo-edit-links').remove();
                                var $controls_container = $('.azh-controls-container');
                                var $toolbar = $('.azh-editor-toolbar').detach();
                                $controls_container.append($toolbar);
                                azh.window.on('scroll', function () {
                                    azh.scroll_top = azh.window.scrollTop();
                                    $controls_container.css('top', (-azh.window.scrollTop()) + 'px');
                                });


                                $document.on('heartbeat-send', function (event, data) {

                                }).on('heartbeat-tick', function (event, data) {
                                    if (azh.changed) {
                                        azh.save('azh_autosave');
                                    }
                                });

                            });
                        });
                    }
                } else {
                    if (azh.edit_post_link) {
                        azh.post_edit_frame = false;
                        azh.post_edit_frame_loaded = false;
                        $window.on('load', function () {
                            $body = $('body');
                            azh.post_edit_frame = $('<iframe src="' + azh.edit_post_link + '"></iframe>').appendTo($body);
                            azh.post_edit_frame.css('border', '0');
                            azh.post_edit_frame.css('position', 'fixed');
                            azh.post_edit_frame.css('left', '0');
                            azh.post_edit_frame.css('top', '0');
                            azh.post_edit_frame.css('z-index', '9999999');
                            azh.post_edit_frame.css('height', '100%');
                            azh.post_edit_frame.css('width', '100%');
                            azh.post_edit_frame.hide();
                            azh.post_edit_frame.on('load', function () {
                                azh.post_edit_frame_loaded = true;
                            });
                        });
                    }
                }
            }
        }
        if (window !== window.parent) {
            $(window.document).on('click', function (event, data) {
                window.parent.jQuery(window.parent.document).trigger(event, data);
            });
            $(window.document).on('wplink-close', function (event, data) {
                window.parent.jQuery(window.parent.document).trigger(event, data);
            });
        }
    }
    function tabs_init($wrapper) {
        $wrapper.each(function () {
            var $tabs = $$(this);
            if (!$tabs.data('azh-tabs')) {
                $tabs.find('> div:first-child > span > a[href^="#"]').on('click', function (event) {
                    var $this = $$(this);
                    event.preventDefault();
                    $this.parent().addClass("azh-active");
                    $this.parent().siblings().removeClass("azh-active");
                    var tab = $this.attr("href");
                    $tabs.find('> div:last-child > div').not(tab).css("display", "none");
                    $$(tab).fadeIn();
                    $$.simplemodal.update($$('.azh-dialog').outerHeight());
                });
                $tabs.find('> div:first-child > span:first-child > a[href^="#"]').click();
                $tabs.data('azh-tabs', true);
            }
        });
    }
    $(function () {
        function dialog_init() {
            $dialog_body = $body;
            $dialog_document = $document;
            $dialog_window = $window;
            if ('post_edit_frame' in azh) {
                if (azh.post_edit_frame) {
                    if (!azh.post_edit_frame_loaded) {
                        return false;
                    }
                    $dialog_body = azh.post_edit_frame.contents().find('body');
                    $dialog_window = $(azh.post_edit_frame.get(0).contentWindow);
                    $dialog_document = $(azh.post_edit_frame.get(0).contentDocument || azh.post_edit_frame.get(0).contentWindow.document);
                    $dialog_body.find('> *').hide();
                    $dialog_body.find('#wpwrap > *').hide();
                    $dialog_body.find('#wpwrap').show();
                    $dialog_body.css('background-color', 'transparent');
                    azh.post_edit_frame.show();
                } else {
                    return false;
                }
            }
            dialog_window = $dialog_window.get(0);
            $$ = dialog_window.jQuery;
            return true;
        }
        function makeid() {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for (var i = 0; i < 5; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            return text;
        }
        function remove_azh_meta() {
//            if ($('body.post-type-page').length) {
//                $('#list-table input[value="azh"]').each(function() {
//                    $(this).parent().find('input.deletemeta').click();
//                });
//            }
        }
        function add_azh_meta() {
//            if ($('body.post-type-page').length) {
//                if ($('#list-table input[value="azh"]').closest('tr[id]:visible').length == 0) {
//                    $('#metakeyinput').val('azh');
//                    $('#metavalue').val('azh');
//                    $('#newmeta-submit').click();
//                }
//            }
        }
        function get_form_atts(form) {
            var attrs = {};
            $$(form).find('.azh-tabs [data-param-name]').each(function () {
                if ($$(this).data('get_value')) {
                    attrs[$$(this).data('param-name')] = $$(this).data('get_value').call(this);
                }
            });
            return attrs;
        }
        function create_field(settings, value) {
            function set_image(field, url, id) {
                var preview = $$(field).find('.azh-image-preview');
                $$(preview).empty();
                $$('<img src="' + url + '">').appendTo(preview);
                $$(preview).data('id', id);
                $$(field).trigger('change');
                $$('<a href="#" class="remove"></a>').appendTo(preview).on('click', function (event) {
                    $$(preview).empty();
                    $$(preview).data('id', '');
                    $$(field).trigger('change');
                    return false;
                });

            }
            function add_images(field, images) {
                var previews = $$(field).find('.azh-images-preview');

                for (var i = 0; i < images.length; i++) {
                    var preview = $$('<div class="azh-image-preview"></div>').appendTo(previews);
                    $$('<img src="' + images[i]['url'] + '">').appendTo(preview);
                    $$(preview).data('id', images[i]['id']);
                    (function (preview) {
                        $$('<a href="#" class="remove"></a>').appendTo(preview).on('click', function (event) {
                            $$(preview).remove();
                            $$(field).trigger('change');
                            return false;
                        });
                    })(preview);
                }

                $$(previews).sortable();

                $$(field).trigger('change');
            }
            var field = $$('<p data-param-name="' + settings['param_name'] + '"></p>');
            $$(field).data('settings', settings);
            settings['heading'] = (typeof settings['heading'] == 'undefined' ? '' : settings['heading']);

            if ('dependency' in settings) {
                setTimeout(function () {
                    $$('[data-param-name="' + settings['dependency']['element'] + '"]').on('change', function () {
                        if ($$(this).css('display') == 'none') {
                            $$(field).hide();
                            $$(field).trigger('change');
                            return;
                        }
                        var value = $$(this).data('get_value').call(this);
                        if ('is_empty' in settings['dependency']) {
                            if (value == '') {
                                $$(field).show();
                            } else {
                                $$(field).hide();
                            }
                        }
                        if ('not_empty' in settings['dependency']) {
                            if (value == '') {
                                $$(field).hide();
                            } else {
                                $$(field).show();
                            }
                        }
                        if ('value' in settings['dependency']) {
                            var variants = settings['dependency']['value'];
                            if (typeof variants == 'string') {
                                variants = [variants];
                            }
                            if (variants.indexOf(value) >= 0) {
                                $$(field).show();
                            } else {
                                $$(field).hide();
                            }
                        }
                        if ('value_not_equal_to' in settings['dependency']) {
                            var variants = settings['dependency']['value_not_equal_to'];
                            if (typeof variants == 'string') {
                                variants = [variants];
                            }
                            if (variants.indexOf(value) >= 0) {
                                $$(field).hide();
                            } else {
                                $$(field).show();
                            }
                        }
                        $$(field).trigger('change');
                    });
                }, 0);
            }

            switch (settings['type']) {
                case 'textfield':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var textfield = $$('<input type="text">').appendTo(field);
                    if (value != '') {
                        $$(textfield).val(value);
                    } else {
                        $$(textfield).val(settings['value']);
                    }
                    $$(textfield).on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        return $$(this).find('input[type="text"]').val();
                    });
                    break;
                case 'ajax_settings':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var $form_settings = $$('<form></form>').appendTo(field);
                    var id = 0;
                    $form_settings.on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        function htmlEncode(value) {
                            return $('<div/>').text(value).html();
                        }
                        var settings = $form_settings.azhSerializeObject();
                        return btoa(unescape(encodeURIComponent(JSON.stringify(settings))));
                    });
                    $$.post(azh.ajaxurl, {
                        'action': settings['action'],
                        'settings': value
                    }, function (data) {
                        $form_settings.empty();
                        $form_settings.html(data);
                        $$.simplemodal.update($$('.azh-dialog').outerHeight());
                    });
                    break;
                case 'widget_settings':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var $widget_settings = $$('<form></form>').appendTo(field);
                    var id = 0;
                    $widget_settings.on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        function htmlEncode(value) {
                            return $('<div/>').text(value).html();
                        }
                        var settings = $widget_settings.azhSerializeObject();
                        if (settings['widgets']) {
                            return btoa(unescape(encodeURIComponent(JSON.stringify(settings['widgets'][id]))));
                        } else {
                            return btoa(unescape(encodeURIComponent(JSON.stringify(settings))));
                        }
                    });
                    $$(field).on('change', function () {
                        function wp_widget_form(widget, instance) {
                            if (!loading) {
                                loading = true;
                                $$.post(azh.ajaxurl, {
                                    'action': 'wp_widget_form',
                                    'widget': widget,
                                    'instance': instance
                                }, function (data) {
                                    loading = false;
                                    id = Math.floor(Math.random() * 1000000);
                                    var html = data.replace(/{\$id}/g, id);
                                    $widget_settings.empty();
                                    $widget_settings.html(html);
                                    $widget_settings.addClass('open');
                                    if (dialog_window.wp.textWidgets) {
                                        var event = new dialog_window.jQuery.Event('widget-added');
                                        dialog_window.wp.textWidgets.handleWidgetAdded(event, $widget_settings);
                                        dialog_window.wp.mediaWidgets.handleWidgetAdded(event, $widget_settings);
                                        if (dialog_window.wp.customHtmlWidgets) {
                                            dialog_window.wp.customHtmlWidgets.handleWidgetAdded(event, $widget_settings);
                                        }
                                    }
                                    $$.simplemodal.update($$('.azh-dialog').outerHeight());
                                });
                            }
                        }
                        var loading = false;
                        if (!$widget_settings.children().length) {
                            var $from = $$(field).closest('.azh-form');
                            var $class = $from.find('[data-param-name="class"]');
                            if ($class.length) {
                                $class.on('change', function () {
                                    wp_widget_form($class.data('get_value').call($class), '');
                                });
                                wp_widget_form($class.data('get_value').call($class), value);
                            }
                        }
                    });
                    break;
                case 'textarea':
                case 'textarea_html':
                case 'textarea_raw_html':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var textarea = $$('<textarea cols="45" rows="5"></textarea>').appendTo(field);
                    if (value != '') {
                        if (settings['type'] == 'textarea_raw_html') {
                            try {
                                $$(textarea).val(decodeURIComponent(escape(atob(value))));
                            } catch (e) {
                            }
                        } else {
                            $$(textarea).val(value);
                        }
                    } else {
                        $$(textarea).val(settings['value']);
                    }
                    if (settings['type'] == 'textarea_html') {
                        azh.get_rich_text_editor(textarea);
                    }
                    $$(textarea).on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        if (settings['type'] == 'textarea_raw_html') {
                            return btoa(unescape(encodeURIComponent($$(this).find('textarea').val())));
                        } else {
                            return $$(this).find('textarea').val();
                        }
                    });
                    break;
                case 'dropdown':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var select = $$('<select></select>');
                    if ($$.isArray(settings['value'])) {
                        for (var i = 0; i < settings['value'].length; i++) {
                            $$(select).append('<option value="' + settings['value'][i][0] + '" ' + (value == settings['value'][i][0] ? 'selected' : '') + '>' + settings['value'][i][1] + '</option>');
                        }
                    } else {
                        for (var label in settings['value']) {
                            $$(select).append('<option value="' + settings['value'][label] + '" ' + (value == settings['value'][label] ? 'selected' : '') + '>' + label + '</option>');
                        }
                    }
                    $$(select).on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        return $$(this).find('select option:selected').attr('value');
                    });
                    $$(select).appendTo(field);
                    break;
                case 'checkbox':
                    var checkbox = $$('<fieldset class="ui-widget ui-widget-content"><legend>' + settings['heading'] + '</legend></fieldset>').appendTo(field);
                    var values = value.split(',');
                    for (var label in settings['value']) {
                        var id = makeid();
                        $$(checkbox).append('<input id="' + id + '" type="checkbox" ' + (values.indexOf(settings['value'][label]) >= 0 ? 'checked' : '') + ' value="' + settings['value'][label] + '">');
                        $$(checkbox).on('change', function () {
                            $$(field).trigger('change');
                        });
                        $$(checkbox).append('<label for="' + id + '">' + label + '</label>');
                    }
                    $$(field).data('get_value', function () {
                        var values = $$.makeArray($$(this).find('input[type="checkbox"]:checked')).map(function (item) {
                            return $$(item).attr('value')
                        });
                        return values.join(',');
                    });
                    break;
                case 'param_group':
                    var param_group = $$('<fieldset class="ui-widget ui-widget-content"><legend>' + settings['heading'] + '</legend></fieldset>').appendTo(field);
                    var table = $$('<table></table>').appendTo(param_group);
                    var values = JSON.parse(decodeURIComponent(settings['value']));
                    if (value != '') {
                        values = JSON.parse(decodeURIComponent(value));
                    }
                    for (var i = 0; i < values.length; i++) {
                        var row = $$('<tr></tr>').appendTo(table);
                        for (var j = 0; j < settings['params'].length; j++) {
                            var column = $$('<td></td>');
                            $$(column).append(create_field(settings['params'][j], (settings['params'][j]['param_name'] in values[i] ? values[i][settings['params'][j]['param_name']] : '')));
                            row.append(column);
                        }
                        $$('<a href="#" class="button">' + azh.i18n.remove + '</a>').appendTo($$('<td></td>').appendTo(row)).on('click', function () {
                            $$(this).closest('tr').remove();
                            return false;
                        });
                    }
                    $$('<a href="#" class="button">' + azh.i18n.add + '</a>').appendTo(param_group).on('click', function () {
                        var row = $$('<tr></tr>').appendTo(table);
                        for (var j = 0; j < settings['params'].length; j++) {
                            var column = $$('<td></td>');
                            $$(column).append(create_field(settings['params'][j], ''));
                            row.append(column);
                        }
                        $$('<a href="#" class="button">' + azh.i18n.remove + '</a>').appendTo($$('<td></td>').appendTo(row)).on('click', function () {
                            $$(this).closest('tr').remove();
                            return false;
                        });
                        return false;
                    });
                    $$(field).data('get_value', function () {
                        var values = $$.makeArray($$(this).find('tr')).map(function (item) {
                            var params = {};
                            $$(item).find('[data-param-name]').each(function () {
                                if ($$(this).data('get_value')) {
                                    params[$$(this).data('param-name')] = $$(this).data('get_value').call(this);
                                }
                            })
                            return(params);
                        });
                        return encodeURIComponent(JSON.stringify(values));
                    });
                    break;
                case 'attach_image':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var preview = $$('<div class="azh-image-preview"></div>').appendTo(field);
                    $$('<a href="#" class="button">' + azh.i18n.set + '</a>').appendTo(field).on('click', function (event) {
                        azh.open_image_select_dialog.call(field, event, function (url, id) {
                            set_image(this, url, id);
                        });
                        return false;
                    });
                    $$(field).data('get_value', function () {
                        return $$(this).find('.azh-image-preview').data('id');
                    });
                    if (value != '') {
                        azh.get_image_url(value, function (url) {
                            set_image(field, url, value);
                        });
                    }
                    break;
                case 'attach_images':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var previews = $$('<div class="azh-images-preview"></div>').appendTo(field);
                    $$('<a href="#" class="button">' + azh.i18n.add + '</a>').appendTo(field).on('click', function (event) {
                        azh.open_image_select_dialog.call(field, event, function (images) {
                            add_images(this, images);
                        }, true);
                        return false;
                    });
                    $$(field).data('get_value', function () {
                        return $$.makeArray($$(this).find('.azh-images-preview .azh-image-preview')).map(function (item) {
                            return $$(item).data('id');
                        }).join(',');
                    });
                    if (value != '') {
                        var images = value.split(',').map(function (item) {
                            return {id: item};
                        });
                        for (var i = 0; i < images.length; i++) {
                            (function (i) {
                                azh.get_image_url(images[i]['id'], function (url) {
                                    images[i]['url'] = url;
                                    var all = true;
                                    for (var j = 0; j < images.length; j++) {
                                        if (!('url' in images[j])) {
                                            all = false;
                                            break;
                                        }
                                    }
                                    if (all) {
                                        add_images(field, images);
                                    }
                                });
                            })(i);
                        }
                    }
                    break;
                case 'url':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var wrapper = $$('<div class="azh-link-field"></div>').appendTo(field).on('contextmenu', function (event) {
                        event.preventDefault();
                        $$(field).data('url', '');
                        $$(url_span).text('');
                    });
                    $$(field).data('url', value);
                    var button = $$('<a href="#" class="button">' + azh.i18n.select_url + '</a>').appendTo(wrapper).on('click', function (event) {
                        var url = $$(field).data('url');
                        azh.open_link_select_dialog.call(this, event, function (url, target, title) {
                            $$(field).data('url', url);
                            $$(url_span).text(url);
                        }, url, '', '');
                        return false;
                    });
                    var url_span = $$('<span>' + value + '</span>').appendTo(wrapper);
                    $$(field).data('get_value', function () {
                        return $$(field).data('url');
                    });
                    break;
                case 'vc_link':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var wrapper = $$('<div class="azh-link-field"></div>').appendTo(field);
                    var link = {};
                    if (value != '') {
                        value.split('|').map(function (item) {
                            link[item.split(':')[0]] = decodeURIComponent(item.split(':')[1]);
                        });
                    }
                    $$(field).data('link', link);
                    var button = $$('<a href="#" class="button">' + azh.i18n.select_url + '</a>').appendTo(wrapper).on('click', function (event) {
                        var link = $$(field).data('link');
                        azh.open_link_select_dialog.call(this, event, function (url, target, title) {
                            var link = {
                                url: url,
                                target: target,
                                title: title,
                                rel: 'nofollow'
                            };
                            $$(field).data('link', link);
                            $$(title_span).text(title);
                            $$(url_span).text(url);
                        }, ('url' in link ? link['url'] : ''), ('target' in link ? link['target'] : ''), ('title' in link ? link['title'] : ''));
                        return false;
                    });
                    $$(wrapper).append('<label>' + azh.i18n.title + '</label>');
                    var title_span = $$('<span>' + ('title' in link ? link['title'] : '') + '</span>').appendTo(wrapper);
                    $$(wrapper).append('<label>' + azh.i18n.url + '</label>');
                    var url_span = $$('<span>' + ('url' in link ? link['url'] : '') + '</span>').appendTo(wrapper);
                    $$(field).data('get_value', function () {
                        return $$.map($$(this).data('link'), function (value, index) {
                            return [index + ':' + encodeURIComponent(value)];
                        }).join('|');
                    });
                    break;
                case 'iconpicker':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var textfield = $$('<input type="text">').appendTo(field);
                    $$(textfield).val(value);
                    azh.icon_select_dialog(function (icon) {
                        $$(textfield).val(icon);
                    }, settings['settings']['type']).appendTo(field);
                    $$(field).data('get_value', function () {
                        return  $$(this).find('input[type="text"]').val();
                    });
                    break;
                case 'ajax_dropdown':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var $$select = $$('<select multiple></select>');
                    $$select.on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        return $$select.val() ? $$select.val() : '';
                    });
                    $$select.appendTo(field);

                    $$select.select2({
                        ajax: {
                            url: settings['url'],
                            dataType: 'json'
                        },
                        dropdownAutoWidth: 'true'
                    });
                    if (value) {
                        $$.post(settings['url'], {
                            'values': [value]
                        }, function (data) {
                            for (var v in data) {
                                $$('<option value="' + v + '">' + data[v] + '</option>').appendTo($$select);
                            }
                            $$select.val(value).trigger('change');
                        }, 'json');
                    }
                    break;
                case 'ajax_multiselect':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var $$select = $$('<select multiple></select>');
                    $$select.on('change', function () {
                        $$(field).trigger('change');
                    });
                    $$(field).data('get_value', function () {
                        return $$select.val() ? $$select.val().join(',') : '';
                    });
                    $$select.appendTo(field);

                    $$select.select2({
                        ajax: {
                            url: settings['url'],
                            dataType: 'json'
                        },
                        dropdownAutoWidth: 'true'
                    });
                    if (value) {
                        $$.post(settings['url'], {
                            'values': value.split(',')
                        }, function (data) {
                            for (var v in data) {
                                $$('<option value="' + v + '">' + data[v] + '</option>').appendTo($$select);
                            }
                            $$select.val(value.split(',')).trigger('change');
                        }, 'json');
                    }
                    break;
                case 'autocomplete':
                    $$(field).append('<label>' + settings['heading'] + '</label>');
                    var textfield = $$('<input type="text">').appendTo(field);
                    $$(field).data('value', value);
                    var shortcode_settings = {};
                    setTimeout(function () {
                        shortcode_settings = $$(field).closest('.azh-form').data('settings');
                        if ($$.trim(value) != '') {
                            var attrs = get_form_atts($$(field).closest('.azh-form'));
                            $$.post(azh.ajaxurl, {
                                action: 'azh_autocomplete_labels',
                                shortcode: shortcode_settings['base'],
                                attrs: attrs,
                                param_name: settings['param_name'],
                                values: value
                            }, function (data) {
                                $$(textfield).val(Object.keys(data).map(function (item) {
                                    return data[item];
                                }).join(', '));
                                if ('multiple' in settings && !settings['multiple']) {
                                    if (Object.keys(data).length > 0) {
                                        $$(field).data('value', Object.keys(data)[0]);
                                    } else {
                                        $$(field).data('value', '');
                                    }
                                } else {
                                    $$(field).data('value', Object.keys(data).join(','));
                                }
                            }, 'json');
                        }
                    });

                    $$(textfield).on("keydown", function (event) {
                        if (event.keyCode === $$.ui.keyCode.TAB && $$(this).autocomplete("instance").menu.active) {
                            event.preventDefault();
                        }
                    }).autocomplete({
                        minLength: 0,
                        source: function (request, response) {
                            if (request.term.split(/,\s*/).pop() != '') {
                                var attrs = get_form_atts($$(field).closest('.azh-form'));
                                $$.post(azh.ajaxurl, {
                                    action: 'azh_autocomplete',
                                    shortcode: shortcode_settings['base'],
                                    attrs: attrs,
                                    param_name: settings['param_name'],
                                    exclude: $$(field).data('value'),
                                    search: request.term.split(/,\s*/).pop()
                                }, function (data) {
                                    response(data);
                                }, 'json');
                            } else {
                                response();
                            }
                        },
                        focus: function (event, ui) {
                            return false;
                        },
                        select: function (event, ui) {
                            if (ui.item) {
                                var labels = this.value.split(/,\s*/);
                                labels.pop();
                                labels.push(ui.item.label);
                                if ('multiple' in settings && !settings['multiple']) {
                                    if (labels.length > 0) {
                                        this.value = labels[0];
                                    } else {
                                        this.value = '';
                                    }
                                } else {
                                    labels.push('');
                                    this.value = labels.join(', ');
                                }

                                var values = $$(field).data('value').split(/,\s*/);
                                if (!$$(field).data('value')) {
                                    values = [];
                                }
                                values.push(ui.item.value.toString());
                                if ('multiple' in settings && !settings['multiple']) {
                                    if (values.length > 0) {
                                        $$(field).data('value', values[0]);
                                    } else {
                                        $$(field).data('value', '');
                                    }
                                } else {
                                    $$(field).data('value', values.join(',').replace(/,\s*$/, '').replace(/^\s*,/, ''));
                                }
                                $$(field).trigger('change');
                            }
                            return false;
                        }
                    }).on("keydown keyup blur", function (event) {
                        if ($$(textfield).val() == '') {
                            $$(field).data('value', '');
                            $$(field).trigger('change');
                        }
                    }).on("focus", function (event) {
                        var values = $$(field).data('value').split(/,\s*/);
                        if ($$(field).data('value') && values.length) {
                            var labels = $$(textfield).val().split(/,\s*/);
                            if (values.length < labels.length) {
                                labels.pop();
                            }
                            if ('multiple' in settings && !settings['multiple']) {
                                if (labels.length > 0) {
                                    $$(textfield).val(labels[0]);
                                } else {
                                    $$(textfield).val('');
                                }
                            } else {
                                $$(textfield).val(labels.join(', ') + ', ');
                            }
                        }
                    });
                    $$(textfield).autocomplete('instance')._create = function () {
                        this._super();
                        this.widget().menu('option', 'items', '> :not(.ui-autocomplete-group)');
                    };
                    $$(textfield).autocomplete('instance')._renderMenu = function (ul, items) {
                        var that = this, currentGroup = '';
                        $$.each(items, function (index, item) {
                            var li;
                            if ('group' in item && item.group != currentGroup) {
                                ul.append('<div class="ui-autocomplete-group">' + item.group + '</div>');
                                currentGroup = item.group;
                            }
                            li = that._renderItemData(ul, item);
                            if ('group' in item && item.group) {
                                li.attr('aria-label', item.group + ' : ' + item.label);
                            }
                        });
                    };
                    $$(field).data('get_value', function () {
                        return  $$(this).data('value');
                    });
                    break;
            }
            if ($$(field).data('get_value')) {
                if ('description' in settings) {
                    $$(field).append('<em>' + settings['description'] + '</em>');
                }
            }
            return field;
        }
        function create_form(settings, values) {
            var form = $$('<div class="azh-form" title="' + settings['name'] + '"></div>');
            $$(form).data('settings', settings);
            if ('params' in settings) {
                var groups = {};
                groups[azh.i18n.general] = [];
                for (var i = 0; i < settings['params'].length; i++) {
                    if ('group' in settings['params'][i]) {
                        groups[settings['params'][i]['group']] = [];
                    }
                }
                for (var i = 0; i < settings['params'].length; i++) {
                    if ('group' in settings['params'][i]) {
                        groups[settings['params'][i]['group']].push(settings['params'][i]);
                    } else {
                        groups[azh.i18n.general].push(settings['params'][i]);
                    }
                }
                var $$tabs = $$('<div class="azh-tabs"></div>').appendTo(form);
                var $$tabs_buttons = $$('<div class="azh-tabs-buttons"></div>').appendTo($$tabs);
                var $$tabs_content = $$('<div class="azh-tabs-content"></div>').appendTo($$tabs);
                var ids = {};
                for (var group in groups) {
                    var id = makeid();
                    ids[group] = id;
                    if (groups[group].length) {
                        $$('<span><a href="#' + id + '">' + group + '</a></span>').appendTo($$tabs_buttons);
                    }
                }
                for (var group in groups) {
                    if (groups[group].length) {
                        var tab = $$('<div id = "' + ids[group] + '"></div>').appendTo($$tabs_content);
                        for (var i = 0; i < groups[group].length; i++) {
                            create_field(groups[group][i], (groups[group][i]['param_name'] in values ? values[groups[group][i]['param_name']] : '')).appendTo(tab);
                        }
                    }
                }
                tabs_init($$tabs);
                setTimeout(function () {
                    $$(form).find('.azh-tabs [data-param-name]').trigger('change');
                }, 0);
            }
            return form;
        }
        function create_dialog(form, title, callback) {

            var $$modal = $$('<div class="azh-dialog"></div>');
            $$('<div class="azh-dialog-title">' + title + '</div>').appendTo($$modal);
            form.appendTo($$modal);
            var $actions = $$('<div class="azh-dialog-actions"></div>').appendTo($$modal);
            $$('<div class="azh-dialog-ok">' + azh.i18n.ok + '</div>').appendTo($actions).on('click', function () {
                var attrs = get_form_atts(form);
                var settings = $$(form).data('settings');
                var shortcode = '[' + settings['base'];
                var content = false;
//                if ('content_element' in settings && settings['content_element']) {
//                    content = ' ';
//                }
                if ('content' in attrs) {
                    content = attrs['content'];
                }
                shortcode += Object.keys(attrs).map(function (item) {
                    if (item === 'content') {
                        return '';
                    } else {
                        return ' ' + item + '="' + attrs[item] + '"';
                    }
                }).join('');
                shortcode += ']';
                if (content) {
                    shortcode += content + '[/' + settings['base'] + ']';
                }
                callback(shortcode, attrs);
                $$.simplemodal.close();
                return false;
            });
            $$('<div class="azh-dialog-cancel">' + azh.i18n.cancel + '</div>').appendTo($actions).on('click', function () {
                $$.simplemodal.close();
                return false;
            });
            $$modal.simplemodal({
                autoResize: true,
                overlayClose: true,
                opacity: 0,
                overlayCss: {
                    "background-color": "black"
                },
                closeClass: "azh-close",
                onShow: function () {
                    $$.simplemodal.update($$('.azh-dialog').outerHeight());
                },
                onClose: function () {
                    if ('post_edit_frame' in azh) {
                        azh.post_edit_frame.hide();
                    }
                    $$.simplemodal.close();
                }
            });
            return $$modal;
        }
        function create_shortcode(settings) {
            var shortcode = $$('<div class="azh-element ' + ('image' in settings ? '' : 'no-image') + '" ' + ('image' in settings ? 'style="background-image: url(\'' + settings.image + '\')"' : '') + '><div>' + settings.name + '</div><em>' + ('description' in settings ? settings.description : '') + '</em></div>').data('settings', settings);
            return shortcode;
        }
        function html_beautify(html) {
            var results = '';
            var level = 0;
            var ignore = false;
            var ignore_tags = {
                //"script": true,
                //"style": true
            };
            azh.indent_size = 1;
            AZHParser(html, {
                start: function (tag, attrs, unary) {
                    if (ignore_tags[tag]) {
                        ignore = true;
                        return;
                    }
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
                end: function (tag) {
                    if (ignore_tags[tag]) {
                        ignore = false;
                        return;
                    }
                    level--;
                    results += Array(level * azh.indent_size + 1).join("\t") + "</" + tag + ">\n";
                },
                chars: function (text) {
                    if ($$.trim(text) && !ignore) {
                        results += Array(level * azh.indent_size + 1).join("\t") + text.replace(/[\t\r\n]*/g, '') + "\n";
                    }
                },
                comment: function (text) {
                    results += Array(level * azh.indent_size + 1).join("\t") + '<!--' + text.replace(/[\t\r\n]*/g, '') + "-->\n";
                }
            });
            return results;
        }
        function auto_focus() {
            $document.one('azh-store', function () {
                setTimeout(function () {
                    var occurrence = 0;
                    if ('section' in $.QueryString) {
                        $('.azh-group-title:contains("' + $.QueryString['section'] + '")').each(function () {
                            if ($(this).text() == $.QueryString['section'] && $.QueryString['occurrence'] == occurrence.toString()) {
                                var section = $(this).closest('.azh-section');
                                if ($(section).length) {
                                    $('body, html').stop().animate({
                                        'scrollTop': $(section).offset().top - $window.height() / 2 + $(section).height() / 2
                                    }, 300);
                                    setTimeout(function () {
                                        $('<div class="azh-overlay"></div>').appendTo('body');
                                        azh.focus('.azh-overlay', 0);
                                        setTimeout(function () {
                                            $('.azh-overlay').remove();
                                            azh.focus(section, 300);
                                        }, 0);
                                    }, 300);
                                }
                            }
                            occurrence++;
                        });
                    }
                    if ('element' in $.QueryString) {
                        $('.azh-element-title:contains("' + $.QueryString['element'] + '")').each(function () {
                            if ($(this).text() == $.QueryString['element'] && $.QueryString['occurrence'] == occurrence.toString()) {
                                var element = $(this).closest('.azh-element-wrapper');
                                $(element).parents('.azh-section-collapsed').each(function () {
                                    $(this).find('> .azh-controls .azh-section-expand').click();
                                });
                                $(element).parents('.azh-element-collapsed').each(function () {
                                    $(this).find('> .azh-controls .azh-element-expand').click();
                                });
                                if ($(element).length) {
                                    $('body, html').stop().animate({
                                        'scrollTop': $(element).offset().top - $window.height() / 2 + $(element).height() / 2
                                    }, 300);
                                    setTimeout(function () {
                                        $('<div class="azh-overlay"></div>').appendTo('body');
                                        azh.focus('.azh-overlay', 0);
                                        setTimeout(function () {
                                            $('.azh-overlay').remove();
                                            azh.focus(element, 300);
                                        }, 0);
                                    }, 300);
                                }
                            }
                            occurrence++;
                        });
                    }
                }, 100);
            });

        }
        function backend_editor_init() {
            setTimeout(function () {
                if (typeof tinymce != 'undefined' && tinymce.get(window.wpActiveEditor)) {
                    tinymce.get(window.wpActiveEditor).hidden = $('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.html-active');
                }
                setTimeout(function () {
                    if ($('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.tmce-active')) {
                        $('.azh-switcher').text(azh.i18n.switch_to_customizer).removeClass('edit');
                    } else {
                        azh.init($('#wp-content-editor-container #content'), false);
                        azh.azh_show_hide();
                    }
                    $('#wp-content-editor-container .azh-switcher').on('click', function () {
                        if ($('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.tmce-active')) {
                            $('#content-tmce').click();
                            $('#content-html').click();
                        } else {
                            if ($('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.html-active')) {
                                azh.azh_show_hide();
                            }
                        }
                    });
                    $('#content-tmce').on('click', azh.azh_show_hide);
                    $('#content-html').on('click', azh.azh_show_hide);
                }, 0);
            }, 0);
            $document.on('azh-store', function () {
                if ($('.azh-structure').children().length) {
                    $('#content-tmce').hide();
                } else {
                    $('#content-tmce').show();
                }
            });
        }
        function frontend_editor_init() {
            var $textarea = $('#wp-content-editor-container #content');
            var $edit_post_frontend_link = $('<a href="' + azh.edit_post_frontend_link + '" class="azh-frontend-builder">' + azh.i18n.edit_frontend_builder + '</a>').prependTo("#postbox-container-1").hide();
            var $metabox = $('#azh').hide();
            $textarea.on('change', function () {
                if (!azh.edit) {
                    if ($textarea.val() == '') {
                        $edit_post_frontend_link.show();
                        $metabox.show();
                    } else {
                        $edit_post_frontend_link.hide();
                        $metabox.hide();
                    }
                }
            });
            setTimeout(function () {
                if (typeof tinymce != 'undefined' && tinymce.get(window.wpActiveEditor)) {
                    tinymce.get(window.wpActiveEditor).onChange.add(function (ed, e) {
                        if (!azh.edit) {
                            ed.save();
                            if ($textarea.val() == '') {
                                $edit_post_frontend_link.show();
                                $metabox.show();
                            } else {
                                $edit_post_frontend_link.hide();
                                $metabox.hide();
                            }
                        }
                    });
                }
            });
            if ($textarea.val() == '') {
                $edit_post_frontend_link.show();
                $metabox.show();
            }
            $document.on('azh-store', function () {
                setTimeout(function () {
                    var frontend = true;
                    $('.azh-wrapper.azh-section.azh-group > .azh-open-line .azh-group-title').each(function () {
                        if (!$('.azh-library .azh-sections .azh-section[data-path="' + $(this).text() + '"]').length) {
                            frontend = false;
                        }
                    });
                    if ($textarea.val() == '') {
                        frontend = true;
                    }
                    if ((frontend && $('body.post-type-page').length && $('#list-table input[value="azh"]').length || $('body.post-type-azh_widget').length)) {
                        $edit_post_frontend_link.show();
                        $metabox.show();
                    } else {
                        $edit_post_frontend_link.hide();
                        $metabox.hide();
                    }
                }, 100);
            });
        }
        azh.open_element_settings_dialog = function (settings, values, callback) {
            if (dialog_init()) {
                $$.simplemodal.close();
                return create_dialog(create_form(settings, values), settings['name'], callback);
            }
        };
        azh.elements_dialog = false;
        azh.open_elements_dialog = function (callback) {
            if (dialog_init() && $$('#simplemodal-container').length === 0) {
                $$.simplemodal.close();

                var $$modal = false;
                var ids = {};
                azh.elements_dialog_callback = callback;
                if (azh.elements_dialog && (JSON.stringify(azh.elements_dialog.data('child-suggestions')) === JSON.stringify(azh.child_suggestions))) {
                    $$modal = azh.elements_dialog;
                } else {
                    function filters_change() {
                        function tags_select_refresh() {
                            $tags_select.children().attr('hidden', 'hidden');
                            for (var tag in azh.tags) {
                                if (tag && $$('.azh-elements-form .azh-elements-filters ~ .azh-element[data-tags*="' + tag + '"]:visible').length) {
                                    $tags_select.children('[value="' + tag + '"]').removeAttr('hidden');
                                }
                            }
                            $tags_select.children('[value=""]').removeAttr('hidden');
                            if ($tags_select.children('[value="' + $tags_select.val() + '"][hidden]').length) {
                                $tags_select.val('');
                                setTimeout(function () {
                                    filters_change();
                                });
                            }
                        }
                        var category = $categories_select.find('option:selected').val();
                        azh.default_category = category;
                        var tag = $tags_select.find('option:selected').val();
                        if (category == '' && tag == '') {
                            $$('.azh-elements-form .azh-elements-filters ~ .azh-element').css('display', 'inline-block');
                        } else {
                            if (category != '' && tag == '') {
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element').css('display', 'none');
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element[data-path^="' + category + '"]').css('display', 'inline-block');
                                tags_select_refresh();
                            }
                            if (category == '' && tag != '') {
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element').css('display', 'none');
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element[data-tags*="' + tag + '"]').css('display', 'inline-block');
                            }
                            if (category != '' && tag != '') {
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element').css('display', 'inline-block');
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element:not([data-path^="' + category + '"])').css('display', 'none');
                                tags_select_refresh();
                                $$('.azh-elements-form .azh-elements-filters ~ .azh-element:not([data-tags*="' + tag + '"])').css('display', 'none');
                            }
                        }
                    }
                    function button_click(button) {
                        function click_process(data) {
                            data = data.replace(/{{azh-uri}}/g, $$(button).data('dir-uri'));
                            data = azh.do_replaces(data);
                            data = html_beautify(data);
                            azh.elements_dialog_callback($$(button).data('path'), data, !$$(button).is('.general'));
                            $$.simplemodal.close();
                        }
                        if ($$(button).data('url') in azh.elements_cache) {
                            click_process(azh.elements_cache[$$(button).data('url')]);
                        } else {
                            $$.get($$(button).data('url'), function (data) {
                                azh.elements_cache[$$(button).data('url')] = data;
                                click_process(data);
                            });
                        }
                    }
                    $$modal = $$('<div class="azh-dialog"></div>');
                    azh.elements_dialog = $$modal;
                    $$('<div class="azh-dialog-title">' + azh.i18n.select_element + '</div>').appendTo($$modal);

                    var form = $$('<div class="azh-elements-form"></div>').appendTo($$modal);
                    var $categories_select = null;
                    var $tags_select = null;
                    var categories = {};
                    categories['general'] = [];
                    for (var tag in azh.shortcodes) {
                        if ('category' in azh.shortcodes[tag]) {
                            categories[azh.shortcodes[tag]['category']] = [];
                        }
                    }
                    for (var tag in azh.shortcodes) {
                        if (!('show_in_dialog' in azh.shortcodes[tag] && azh.shortcodes[tag]['show_in_dialog'] === false)) {
                            if ('category' in azh.shortcodes[tag]) {
                                categories[azh.shortcodes[tag]['category']].push(azh.shortcodes[tag]);
                            } else {
                                categories['general'].push(azh.shortcodes[tag]);
                            }
                        }
                    }
                    $$('.azh-library .azh-elements .azh-element').each(function () {
                        if (!$$(this).is('.general')) {
                            var path = $$(this).data('path');
                            var category = path.split('/')[0];
                            categories[category] = [];
                            $$(this).attr('data-category', category);
                        }
                    });
                    if ($$('.azh-library .azh-elements .azh-element').length) {
                        categories['elements'] = [];
                    }
                    var $$tabs = $$('<div class="azh-left-tabs"></div>').appendTo(form);
                    var $$tabs_buttons = $$('<div class="azh-tabs-buttons"></div>').appendTo($$tabs);
                    var $$tabs_content = $$('<div class="azh-tabs-content"></div>').appendTo($$tabs);
                    var general_tab = false;
                    if ('categories_order' in azh) {
                        $$(azh.categories_order).each(function () {
                            var category = this;
                            var id = makeid();
                            ids[category] = id;
                            $$('<span><a href="#' + id + '">' + (category in azh.i18n ? azh.i18n[category] : category) + '</a></span>').appendTo($$tabs_buttons);
                        });
                    } else {
                        for (var category in categories) {
                            var id = makeid();
                            ids[category] = id;
                            $$('<span><a href="#' + id + '">' + (category in azh.i18n ? azh.i18n[category] : category) + '</a></span>').appendTo($$tabs_buttons);
                        }
                    }
                    var child_suggestions = [];
                    for (var category in categories) {
                        var tab = $$('<div id = "' + ids[category] + '"></div>').appendTo($$tabs_content);
                        if (category === 'general') {
                            general_tab = tab;
                        } else if (category === 'elements') {
                            var filters = $$('.azh-library .azh-elements .azh-elements-filters').clone();
                            $$(filters).appendTo(tab);
                            $categories_select = $$(filters).find('.azh-categories').on('change', filters_change);
                            $tags_select = $$('<select></select>').appendTo(filters).on('change', filters_change);
                            $$('<option selected value="">' + azh.i18n.filter_by_tag + '</option>').appendTo($tags_select);
                            Object.keys(azh.tags).sort().forEach(function (tag, i) {
                                $('<option value="' + tag + '">' + tag + '</option>').appendTo($tags_select);
                            });
                            $$('.azh-library .azh-elements .azh-element').each(function () {
                                var button = $$(this).clone();
                                button.appendTo(tab).on('click', function () {
                                    button_click(this);
                                });
                                if (general_tab && $$(button).is('.general')) {
                                    var general_button = $$(button).clone(true);
                                    general_button.appendTo(general_tab);
                                }
                                if (azh.child_suggestions.length) {
                                    if (azh.child_suggestions.indexOf($$(this).data('path')) >= 0) {
                                        child_suggestions.push(button);
                                    }
                                }
                            });
                        } else {

                            for (var i = 0; i < categories[category].length; i++) {
                                create_shortcode(categories[category][i]).appendTo(tab).on('click', function () {
//                            if ('content_element' in $$(this).data('settings') && $$(this).data('settings')['content_element']) {
//                                azh.elements_dialog_callback('shortcode', '[' + $$(this).data('settings')['base'] + '] [/' + $$(this).data('settings')['base'] + ']');
//                            } else {
                                    azh.elements_dialog_callback('shortcode', '[' + $$(this).data('settings')['base'] + ']');
//                            }
                                    $$.simplemodal.close();
                                });
                            }

                            $$('.azh-library .azh-elements .azh-element[data-category="' + category + '"]').each(function () {
                                var button = $$(this).clone();
                                button.appendTo(tab).on('click', function () {
                                    button_click(this);
                                });
                            });
                        }
                        var elements = tab.children();
                        elements.detach();
                        elements.sort(function (element1, element2) {
                            var order1 = parseInt($$(element1).attr('data-order'), 10);
                            var order2 = parseInt($$(element2).attr('data-order'), 10);
                            return (order1 < order2) ? -1 : (order1 > order2) ? 1 : 0;
                        });
                        tab.append(elements);
                    }
                    if (general_tab && child_suggestions.length) {
                        $$('<div class="azh-divider" data-order="0"></div>').prependTo(general_tab);
                        $$(child_suggestions).each(function () {
                            var general_button = $$(this).clone(true);
                            general_button.prependTo(general_tab);
                        });
                    }
                    tabs_init($$tabs);
                    azh.elements_dialog.data('child-suggestions', $$.merge([], azh.child_suggestions));

                    var $actions = $$('<div class="azh-dialog-actions"></div>').appendTo($$modal);
                    $$('<div class="azh-primary">' + azh.i18n.paste_from_clipboard + '</div>').appendTo($actions).on('click', function () {
                        if (azh.clipboard) {
                            azh.elements_dialog_callback(azh.clipboard['path'], azh.clipboard['code']);
                            $$.simplemodal.close();
                        } else {
                            $.post(azh.ajaxurl, {
                                'action': 'azh_paste',
                                dataType: 'text',
                            }, function (data) {
                                data = JSON.parse(data);
                                if ('code' in data && 'path' in data) {
                                    azh.elements_dialog_callback(data['path'], data['code']);
                                }
                                $$.simplemodal.close();
                            });
                        }
                        return false;
                    });
                    $$('<div class="azh-dialog-cancel">' + azh.i18n.cancel + '</div>').appendTo($actions).on('click', function () {
                        $$.simplemodal.close();
                        return false;
                    });
                }

                $$modal.simplemodal({
                    autoResize: true,
                    overlayClose: true,
                    opacity: 0,
                    overlayCss: {
                        "background-color": "black"
                    },
                    closeClass: "azh-close",
                    onShow: function () {
                        if(azh.default_category) {
                           var $categories_select = $$modal.find('.azh-elements-filters .azh-categories');
                           if ($categories_select.find('[value="' + azh.default_category + '"]').length) {
                              $categories_select.val(azh.default_category);
                              $$modal.find('.azh-left-tabs .azh-tabs-buttons').find('[href="#' + ids['elements'] + '"]').trigger('click');
                              $categories_select.trigger('change');
                           }
                           if (azh.default_category in ids) {
                              $$modal.find('.azh-left-tabs .azh-tabs-buttons').find('[href="#' + ids[azh.default_category] + '"]').trigger('click');
                           }
                        }
                    },
                    onClose: function () {
                        azh.elements_dialog.detach();
                        if ('post_edit_frame' in azh) {
                            azh.post_edit_frame.hide();
                        }
                        $.simplemodal.impl.d.data = $();
                        $$.simplemodal.close();
                    }
                });
                return $$modal;
            }
        };
        azh.icon_select_dialog = function (callback, type) {
            function show_icons() {
                var keyword = $search.val().toLowerCase();
                $icons.empty();
                for (var key in azh.icons[type]) {
                    if (azh.icons[type][key].toLowerCase().indexOf(keyword) >= 0) {
                        $$('<span class="' + key + '"></span>').appendTo($icons).on('click', {icon: icon}, function (event) {
                            callback.call(event.data.icon, $$(this).attr('class'));
                        });
                    }
                }
            }
            var icon = this;
            var icon_class = '';
            var $dialog = $$('<div class="azh-icon-select-dialog"></div>').appendTo('body');
            var $controls = $$('<div class="type-search"></div>').appendTo($dialog);
            var $search = $$('<input type="text"/>').appendTo($controls).on('change keyup', function () {
                show_icons();
            });
            var $icons = $$('<div class="azh-icons"></div>').appendTo($dialog);
            show_icons();
            return $dialog;
        };
        azh.open_icon_select_dialog = function (event, icon_class, callback) {
            function show_icons() {
                var type = $types.find('option:selected').val();
                var keyword = $$(search).val().toLowerCase();
                $icons.empty();
                for (var key in azh.icons[type]) {
                    if (azh.icons[type][key].toLowerCase().indexOf(keyword) >= 0) {
                        $$('<span class="' + key + '"></span>').appendTo($icons).on('click', {icon: icon}, function (event) {
                            $dialog.remove();
                            $backdrop.remove();
                            $document.off('click.azh-dialog');
                            if ('post_edit_frame' in azh) {
                                azh.post_edit_frame.hide();
                            }
                            callback.call(event.data.icon, $$(this).attr('class'));
                        });
                    }
                }
            }
            if (dialog_init()) {
                var current_type = false;
                for (var type in azh.icons) {
                    var pattern = new RegExp('(' + Object.keys(azh.icons[type]).join('|') + ')', 'i');
                    var match = pattern.exec(icon_class);
                    if (match) {
                        current_type = type;
                        break;
                    }
                }
                var icon = this;
                $dialog_body.find('.azh-icon-select-dialog').remove();
                $dialog_body.find('.azh-backdrop').remove();
                var $backdrop = $$('<div class="azh-backdrop"></div>').appendTo($dialog_body);
                var $dialog = $$('<div class="azh-icon-select-dialog"></div>').appendTo($dialog_body);
                var $controls = $$('<div class="type-search"></div>').appendTo($dialog);
                var $types = $$('<select></select>').appendTo($controls).on('change', function () {
                    show_icons();
                });
                var search = $$('<input type="text"/>').appendTo($controls).on('change keyup', function () {
                    show_icons();
                });
                for (var type in azh.icons) {
                    var option = $$('<option value="' + type + '">' + type + '</option>').appendTo($types);
                }
                var $icons = $$('<div class="azh-icons"></div>').appendTo($dialog);
                $types.val(current_type);
                show_icons();
                $document.on('click.azh-dialog', {icon: icon}, function (event) {
                    if (!$$(event.target).closest('.azh-icon-select-dialog').length) {
                        $dialog.remove();
                        $backdrop.remove();
                        $document.off('click.azh-dialog');
                        if ('post_edit_frame' in azh) {
                            azh.post_edit_frame.hide();
                        }
                        callback.call(event.data.icon, icon_class);
                    }
                });
                event.stopPropagation();
            }
        };
        azh.get_image_url = function (id, callback) {
            var attachment = wp.media.model.Attachment.get(id);
            attachment.fetch().done(function () {
                callback(attachment.attributes.url);
            });
        };
        azh.open_image_select_dialog = function (event, callback, multiple, type) {
            if (dialog_init()) {
                var azh_frame = 'azh-' + multiple + '-' + type;
                var image = this;
                multiple = (typeof multiple == 'undefined' ? false : multiple);
                type = (typeof type == 'undefined' ? 'image' : type);
                // check for media manager instance
                if (dialog_window.wp.media.frames[azh_frame]) {
                    dialog_window.wp.media.frames[azh_frame].image = image;
                    dialog_window.wp.media.frames[azh_frame].callback = callback;
                    dialog_window.wp.media.frames[azh_frame].options.multiple = multiple;
                    dialog_window.wp.media.frames[azh_frame].options.library = {type: type};
                    dialog_window.wp.media.frames[azh_frame].open();
                    return;
                }
                // configuration of the media manager new instance            
                dialog_window.wp.media.frames[azh_frame] = dialog_window.wp.media({
                    multiple: multiple,
                    library: {
                        type: type
                    }
                });
                dialog_window.wp.media.frames[azh_frame].image = image;
                dialog_window.wp.media.frames[azh_frame].callback = callback;
                // Function used for the image selection and media manager closing            
                var azh_media_set_image = function () {
                    var selection = dialog_window.wp.media.frames[azh_frame].state().get('selection');
                    // no selection
                    if (!selection) {
                        return;
                    }
                    // iterate through selected elements
                    if (dialog_window.wp.media.frames[azh_frame].options.multiple) {
                        dialog_window.wp.media.frames[azh_frame].callback.call(dialog_window.wp.media.frames[azh_frame].image, selection.map(function (attachment) {
                            return {url: attachment.attributes.url, id: attachment.attributes.id};
                        }));
                    } else {
                        selection.each(function (attachment) {
                            dialog_window.wp.media.frames[azh_frame].callback.call(dialog_window.wp.media.frames[azh_frame].image, attachment.attributes.url, attachment.attributes.id);
                        });
                    }
                };
//            if (selected) {
//                dialog_window.wp.media.frames[azh_frame].on('open', function() {
//                    var selection = dialog_window.wp.media.frames[azh_frame].state().get('selection');
//                    if (selection) {
//                        $$(selected).each(function() {
//                            selection.add(dialog_window.wp.media.attachment(this));
//                        });
//                    }
//                });
//            }
                // closing event for media manger
                dialog_window.wp.media.frames[azh_frame].on('close', function () {
                    if ('post_edit_frame' in azh) {
                        azh.post_edit_frame.hide();
                    }
                });
                // image selection event
                dialog_window.wp.media.frames[azh_frame].on('select', azh_media_set_image);
                // showing media manager
                dialog_window.wp.media.frames[azh_frame].open();
            }
        }
        azh.open_link_select_dialog = function (event, callback, url, target, text) {
            if (dialog_init()) {
                url = (typeof url == 'undefined' ? '' : url);
                target = (typeof target == 'undefined' ? '' : target);
                text = (typeof text == 'undefined' ? '' : text);
                var link = this;
                if ($$(link).data('url')) {
                    url = $$(link).data('url');
                }
                var original = dialog_window.wpLink.htmlUpdate;
                $document.on('wplink-close.azh', function () {
                    if ('post_edit_frame' in azh) {
                        azh.post_edit_frame.hide();
                    }
                    dialog_window.wpLink.htmlUpdate = original;
                    $dialog_body.find('#wp-link-cancel').off('click.azh');
                    $$(input).remove();
                    $document.off('wplink-close.azh');
                });
                dialog_window.wpLink.htmlUpdate = function () {
                    var attrs = dialog_window.wpLink.getAttrs();
                    if (!attrs.href) {
                        return;
                    }
                    callback.call(link, attrs.href, attrs.target, $dialog_body.find('#wp-link-text').val());
                    dialog_window.wpLink.close('noReset');
                };
                $dialog_body.find('#wp-link-cancel').on('click.azh', function (event) {
                    dialog_window.wpLink.close('noReset');
                    event.preventDefault ? event.preventDefault() : event.returnValue = false;
                    event.stopPropagation();
                    return false;
                });
                dialog_window.wpActiveEditor = true;
                var id = makeid();
                var input = $$('<input id="' + id + '" />').appendTo($dialog_body).hide();
                dialog_window.wpLink.open(id);
                $dialog_body.find('#wp-link-url').val(url);
                $dialog_body.find('#wp-link-target').val(target);
                $dialog_body.find('#wp-link-text').val(text);
            }
        };
        azh.get_rich_text_editor = function (textarea) {
            function init_textarea_html($element) {
                var $wp_link = $$("#wp-link");
                $wp_link.parent().hasClass("wp-dialog") && $wp_link.wpdialog("destroy");
                $element.val($$(textarea).val());
                try {
                    dialog_window._.isUndefined(dialog_window.tinyMCEPreInit.qtInit[textfield_id]) && (dialog_window.tinyMCEPreInit.qtInit[textfield_id] = dialog_window._.extend({}, dialog_window.tinyMCEPreInit.qtInit[dialog_window.wpActiveEditor], {
                        id: textfield_id
                    }));
                    dialog_window.tinyMCEPreInit && dialog_window.tinyMCEPreInit.mceInit[dialog_window.wpActiveEditor] && (dialog_window.tinyMCEPreInit.mceInit[textfield_id] = dialog_window._.extend({}, dialog_window.tinyMCEPreInit.mceInit[dialog_window.wpActiveEditor], {
                        resize: "vertical",
                        height: 200,
                        id: textfield_id,
                        setup: function (ed) {
                            "undefined" != typeof ed.on ? ed.on("init", function (ed) {
                                ed.target.focus(), dialog_window.wpActiveEditor = textfield_id
                            }) : ed.onInit.add(function (ed) {
                                ed.focus(), dialog_window.wpActiveEditor = textfield_id
                            })
                            ed.on('change', function (e) {
                                $$(textarea).val(ed.getContent());
                                $$(textarea).trigger('change');
                            });
                        }
                    }), dialog_window.tinyMCEPreInit.mceInit[textfield_id].plugins = dialog_window.tinyMCEPreInit.mceInit[textfield_id].plugins.replace(/,?wpfullscreen/, ""), dialog_window.tinyMCEPreInit.mceInit[textfield_id].wp_autoresize_on = !1);
                    dialog_window.quicktags(dialog_window.tinyMCEPreInit.qtInit[textfield_id]);
                    dialog_window.QTags._buttonsInit();
                    dialog_window.tinymce && (dialog_window.switchEditors && dialog_window.switchEditors.go(textfield_id, "tmce"), "4" === dialog_window.tinymce.majorVersion && dialog_window.tinymce.execCommand("mceAddEditor", !0, textfield_id));
                    dialog_window.wpActiveEditor = textfield_id
                    dialog_window.setUserSetting('editor', 'html');
                } catch (e) {
                }
                $$.simplemodal.update($$('.azh-dialog').outerHeight());
            }
            var textfield_id = makeid();
            $$.ajax({
                type: 'POST',
                url: azh.ajaxurl,
                data: {
                    action: 'azh_get_wp_editor',
                    id: textfield_id,
                },
                cache: false,
            }).done(function (data) {
                $$(textarea).hide();
                $$(textarea).after(data);
                init_textarea_html($$('#' + textfield_id));
                $$('#' + textfield_id).on('change', function () {
                    $$(textarea).val($$(this).val());
                    $$(textarea).trigger('change');
                });
            });
        }
        azh.azh_show_hide = function () {
            //tinymce.get(window.wpActiveEditor).hidden
            setTimeout(function () {
                if ($('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.tmce-active')) {
                    $('#wp-content-editor-container #content, #wp-customize-posts-content-editor-container #customize-posts-content').show();
                    $('.azh-switcher').text(azh.i18n.switch_to_customizer).removeClass('edit');
                }
                if ($('#wp-content-wrap, #wp-customize-posts-content-wrap').is('.html-active')) {
                    if (azh.edit) {
                        $('#wp-content-editor-container .azh-switcher, #wp-customize-posts-content-editor-container .azh-switcher').css('left', '0');
                        $('.azh-switcher').text(azh.i18n.switch_to_html).addClass('edit');
                        $('#wp-content-editor-container .azexo-html-editor, #wp-customize-posts-content-editor-container .azexo-html-editor').show();
                        $('#wp-content-editor-container #content, #wp-customize-posts-content-editor-container #customize-posts-content').hide();
                        $('#ed_toolbar, #qt_customize-posts-content_toolbar').hide();
                        $('#wp-content-media-buttons, #wp-customize-posts-content-media-buttons').hide();
                        $('#azh').show();
                        add_azh_meta();
                    } else {
                        $('#wp-content-editor-container .azh-switcher, #wp-customize-posts-content-editor-container .azh-switcher').css('left', '110px');
                        $('.azh-switcher').text(azh.i18n.switch_to_customizer).removeClass('edit');
                        $('#wp-content-editor-container .azexo-html-editor, #wp-customize-posts-content-editor-container .azexo-html-editor').hide();
                        $('#wp-content-editor-container #content, #wp-customize-posts-content-editor-container #customize-posts-content').show();
                        $('#ed_toolbar, #qt_customize-posts-content_toolbar').show();
                        $('#wp-content-media-buttons, #wp-customize-posts-content-media-buttons').show();
                        $('#azh').hide();
                        remove_azh_meta();
                    }
                } else {
                    $('#wp-content-editor-container .azh-switcher, #wp-customize-posts-content-editor-container .azh-switcher').css('left', '110px');
                    $('.azh-switcher').text(azh.i18n.switch_to_customizer).removeClass('edit');
                    $('#wp-content-editor-container .azexo-html-editor, #wp-customize-posts-content-editor-container .azexo-html-editor').hide();
                    $('#wp-content-editor-container .mce-tinymce, #wp-customize-posts-content-editor-container .mce-tinymce').show();
                    $('#wp-content-editor-container #content, #wp-customize-posts-content-editor-container #customize-posts-content').hide();
                    $('#ed_toolbar, #qt_customize-posts-content_toolbar').show();
                    $('#wp-content-media-buttons, #wp-customize-posts-content-media-buttons').show();
                    $('#azh').hide();
                    remove_azh_meta();
                }
            }, 0);
        }
        $body = $('body');
        if (!('azh' in $.QueryString) && window === window.parent) {
            if ($('#wp-content-editor-container #content').length) {
                backend_editor_init();
            }
            if ('post' in $.QueryString && 'action' in $.QueryString && 'occurrence' in $.QueryString && ('section' in $.QueryString || 'element' in $.QueryString)) {
                auto_focus();
            }
//            if ($("#postbox-container-1").length) {
//                frontend_editor_init();
//            }
        }
        $('#azh-switch-mode-button').on('click', function () {
            if ($('#azh-switch-mode-input').val()) {
                $body.removeClass('azh-editor-active');
                $('#azh-switch-mode-input').val('');
            } else {
                $body.addClass('azh-editor-active');
                $('#azh-switch-mode-input').val('azh');
                $('#azh-editor-button').trigger('click');
            }
            return false;
        });
        if ('wpColorPicker' in $.fn) {
            $('input.azh-wp-color-picker').wpColorPicker();
        }
    });
    frontend_editor_frame_init();
})(window.jQuery);
