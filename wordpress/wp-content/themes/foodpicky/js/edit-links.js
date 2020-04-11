(function($) {
    "use strict";
    $(function() {
        function edit_links_refresh() {
            function show_edit_link(element) {
                $($(element).data('edit-link-control')).css({
                    "top": $(element).offset().top,
                    "left": $(element).offset().left,
                    "width": $(element).outerWidth(),
                    "height": $(element).outerHeight(),
                }).show();
            }
            function hide_edit_link(element) {
                $($(element).data('edit-link-control')).hide();
            }
            function is_visible(element) {
                var visible = true;
                if ($(window).width() < $(element).offset().left + $(element).outerWidth()) {
                    visible = false;
                }
                if (!$(element).is(":visible")) {
                    visible = false;
                }
                $(element).parents().each(function() {
                    var parent = this;

                    var elements = $(parent).data('elements-with-edit-link');
                    if (!elements) {
                        elements = [];
                    }
                    elements = elements.concat($(element).get());
                    elements = $.unique(elements);
                    $(parent).data('elements-with-edit-link', elements);

                    if ($(parent).css("display") == 'none' || $(parent).css("opacity") == '0' || $(parent).css("visibility") == 'hidden') {
                        visible = false;
                        $(parent).off('click.azexo-edit-links mouseenter.azexo-edit-links mouseleave.azexo-edit-links').on('click.azexo-edit-links mouseenter.azexo-edit-links mouseleave.azexo-edit-links', function() {
                            var elements = $(parent).data('elements-with-edit-link');
                            $(elements).each(function() {
                                if (is_visible(this)) {
                                    show_edit_link(this);
                                } else {
                                    hide_edit_link(this);
                                }
                            });
                        });
                    }
                });
                return visible;
            }
            var $container = false;
            if ($('.azexo-edit-links').length) {
                $container = $('.azexo-edit-links');
                $container.detach();
            } else {
                $container = $('<div class="azexo-edit-links"></div>');
            }
            for (var links_type in azexo.edit_links) {
                var selectors = Object.keys(azexo.edit_links[links_type].links);
                selectors.sort(function(a, b) {
                    return b.length - a.length;
                });
                for (var i = 0; i < selectors.length; i++) {
                    var selector = selectors[i];
                    $(selector).each(function() {
                        if (!$(this).data('edit-link-control') && !$(this).is('.no-results')) {
                            var control = $('<div class="azexo-edit-link"><a href="' + azexo.edit_links[links_type].links[selector] + '" target="' + azexo.edit_links[links_type].target + '">' + azexo.edit_links[links_type].text + '</a></div>').appendTo($container).css({
                                "top": "0",
                                "left": "0",
                                "width": "0",
                                "height": "0",
                                "z-index": "9999999",
                                "pointer-events": "none",
                                "position": "absolute"
                            }).hide();
                            control.find('a').css({
                                "display": "inline-block",
                                "padding": "5px 10px",
                                "color": "black",
                                "font-weight": "bold",
                                "background-color": "white",
                                "box-shadow": "0px 5px 5px rgba(0, 0, 0, 0.1)",
                                "pointer-events": "all"
                            }).on('mouseenter', function() {
                                $(this).parent().css("background-color", "rgba(0, 255, 0, 0.1)");
                                edit_links_refresh();
                            }).on('mouseleave', function() {
                                $(this).parent().css("background-color", "transparent");
                            });
                            $(this).data('edit-link-control', control);
                        }
                        if (is_visible(this)) {
                            show_edit_link(this);
                        } else {
                            hide_edit_link(this);
                        }
                    });
                }
            }
            $('[data-content-id]').each(function() {
                if (!$(this).parent().data('edit-link-control')) {
                    if (!$(this).data('edit-link-control')) {
                        var id = $(this).data('content-id');
                        var control = $('<div class="azexo-edit-link"><a href="' + azexo.edit_url.replace('post=0', 'post=' + id) + '" target="_blank">' + azexo.edit_button + '</a></div>').appendTo($container).css({
                            "top": "0",
                            "left": "0",
                            "width": "0",
                            "height": "0",
                            "z-index": "9999999",
                            "pointer-events": "none",
                            "text-align": "right",
                            "position": "absolute"
                        }).hide();
                        control.find('a').css({
                            "display": "inline-block",
                            "padding": "5px 10px",
                            "color": "black",
                            "font-weight": "bold",
                            "background-color": "white",
                            "box-shadow": "0px 5px 5px rgba(0, 0, 0, 0.1)",
                            "pointer-events": "all"
                        }).on('mouseenter', function() {
                            $(this).parent().css("background-color", "rgba(0, 255, 0, 0.1)");
                            $(window).trigger('scroll');
                        }).on('mouseleave', function() {
                            $(this).parent().css("background-color", "transparent");
                        });
                        $(this).data('edit-link-control', control);
                    }
                    if (is_visible(this)) {
                        show_edit_link(this);
                    } else {
                        hide_edit_link(this);
                    }
                }
            });
            $container.appendTo('body');
        }
        var parse_query_string = function(a) {
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
        $.QueryString = parse_query_string(window.location.search.substr(1).split('&'));
        if (!('azh' in $.QueryString)) {
            if ('azexo' in window && 'edit_links' in azexo) {
                $(window).on('resize.edit-links scroll.edit-links', _.throttle(function() {
                    edit_links_refresh();
                }, 1000));
                setTimeout(function() {
                    edit_links_refresh();
                }, 100);
                $('#wp-admin-bar-edit-links').addClass('azexo-edit-links-active');
                $('#wp-admin-bar-edit-links').off('click.edit-links').on('click.edit-links', function(event) {
                    if ($(this).is('.azexo-edit-links-active')) {
                        $('.azexo-edit-links div.azexo-edit-link[style] a[href][style][target]').each(function() {
                            if ($(this).is(':visible')) {
                                $(this).data('visible', true);
                                $(this).hide();
                            }
                        });
                        $('.azexo-edit-links div.azexo-edit-link[style] a[href][style][target]').hide();
                        $(this).removeClass('azexo-edit-links-active');
                        $(this).css('opacity', '0.4');
                        $(window).off('resize.edit-links scroll.edit-links');
                    } else {
                        $('.azexo-edit-links div.azexo-edit-link[style] a[href][style][target]').each(function() {
                            if ($(this).data('visible')) {
                                $(this).show();
                            }
                        });
                        $(this).addClass('azexo-edit-links-active');
                        $(this).css('opacity', '1');
                        $(window).on('resize.edit-links scroll.edit-links', function() {
                            edit_links_refresh();
                        });
                    }
                    event.preventDefault();
                });
            }
        }
    });
})(window.jQuery);