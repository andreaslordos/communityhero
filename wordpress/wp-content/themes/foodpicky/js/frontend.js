(function ($) {
    "use strict";

    window.azexo = $.extend({}, window.azexo);
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);

    var $page = $('#page').css('visibility', 'hidden');
    var $status = $("#status").css('display', 'block');
    var $preloader = $("#preloader").css('display', 'block');
    $(function () {
        $preloader.trigger('before-hide');
        $page.css('visibility', 'visible');
        $status.fadeOut("slow");
        $preloader.fadeOut("slow");
    });

    var windowHeight = $window.height();
    $window.on('resize', function () {
        windowHeight = $window.height();
    });
    $.fn.parallax = function (xpos, speedFactor, outerHeight) {
        var $this = $(this);
        var getHeight;
        var firstTop;
        var paddingTop = 0;
        $this.each(function () {
            firstTop = $this.offset().top;
        });
        if (outerHeight) {
            getHeight = function (jqo) {
                return jqo.outerHeight(true);
            };
        } else {
            getHeight = function (jqo) {
                return jqo.height();
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
                if (top + height < pos || top > pos + windowHeight) {
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
    $.fn.marginBackground = function () {
        var $this = $(this);
        var $bg = $this.find('> .background');
        if ($bg.length === 0) {
            $bg = $('<div class="background"></div>').prependTo(this);
            $this.css('position', 'relative');
            $bg.css('position', 'absolute');
            $bg.css('top', '0');
            $bg.css('bottom', '0');
            $bg.css('background-color', $this.css('background-color'));
            $this.css('background-color', '');
            $bg.css('background-image', $this.css('background-image'));
            $this.css('background-image', '');
        }
        var m = ($this.parent().width() - $this.width()) / 2;
        $bg.css('left', '-' + m + 'px');
        $bg.css('right', '-' + m + 'px');
    };
    $.fn.hierarchicalSelect = function () {
        $(this).each(function () {
            function create_select(before, node, level) {
                if (node) {
                    $base_select.val(node.value);
                } else {
                    $base_select.val('');
                    return false;
                }
                if (node.children.length === 0)
                    return false;
                var $select = $('<select class="hierarchy-level"></select>').insertAfter(before);
                if (placeholders)
                    $select.append('<option>' + placeholders[level] + '</option>');
                $(node.children).each(function () {
                    $('<option value="' + this.value + '">' + this.label + '</option>').appendTo($select).data('node', this);
                });
                $select.data('after', false);
                $select.on('change', function () {
                    var $this = $(this);
                    function remove_after(after) {
                        if (after) {
                            remove_after($(after).data('after'));
                            if ('select2' in $.fn && $base_select.is('.select2')) {
                                $(after).select2('destroy');
                            }
                            $(after).remove();
                        }
                    }
                    remove_after($this.data('after'));
                    var after = create_select($this, $this.find(":selected").data('node'), level + 1);
                    $this.data('after', after);
                });
                if ('select2' in $.fn && $base_select.is('.select2')) {
                    $select.select2();
                }
                return $select;
            }
            var $base_select = $(this);
            $base_select.hide();
            var placeholders = false;
            if ($base_select.data('placeholders')) {
                placeholders = $base_select.data('placeholders').split('|');
            } else {
                if ($base_select.find('option[value=""]'))
                    placeholders = $base_select.find('option[value=""]').text().split('|');
            }
            var tree = {value: '', label: '', children: []};
            var path = [tree];
            var selected_path = false;
            var current_level = 0;
            $base_select.find('option').each(function () {
                var $this = $(this);
                if ($this.val() !== '') {
                    var level = 0;
                    if (($this.text().match(/^-+/g) || []).length) {
                        level = ($this.text().match(/^-+/g) || [])[0].length;
                    }
                    var node = {value: $this.val(), label: $this.text().replace(/^-+/g, ''), children: []};
                    if (level > current_level) {
                        path.push(path[current_level].children[path[current_level].children.length - 1]);
                        current_level = level;
                    }
                    if (level < current_level) {
                        path.pop();
                        current_level = level;
                    }
                    path[current_level].children.push(node);
                    if ($this.is(':selected')) {
                        selected_path = path.slice(0);
                        selected_path.push(selected_path[current_level].children[selected_path[current_level].children.length - 1]);
                        selected_path.shift();
                    }
                }
            });
            var root_select = create_select($base_select, tree, 0);
            if (selected_path) {
                var $select = root_select;
                $(selected_path).each(function () {
                    if ($select) {
                        $select.val(this.value).change();
                    }
                    $select = $select.data('after');
                });
            }
        });
    };
    window.azexo.getParameterByName = function (name, url) {
        if (!url)
            url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
        if (!results)
            return null;
        if (!results[2])
            return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    };
    function common_ancestor(jq) {
        var prnt = $(jq[0]);
        jq.each(function () {
            prnt = prnt.parents().add(prnt).has(this).last();
        });
        return prnt;
    }
    $.fn.horizontal_menu_more = function (step) {
        var from = 0;
        var $items = $(this);
        if ($items.length > step) {
            var $ca = common_ancestor($items);
            var $more_button = $ca.children().last().clone();
            if ($more_button.text()) {
                $more_button.html('<span class="fa fa-bars"></span>');
            }
            $more_button.find('*').each(function () {
                var $this = $(this);
                if ($this.text()) {
                    $this.html('<span class="fa fa-bars"></span>');
                    return false;
                }
            });
            $more_button.addClass('horizontal-menu-more');
            $ca.children().last().after($more_button);
            $more_button.find('span.fa-bars').on('click', function () {
                if (from >= $items.length) {
                    from = 0;
                }
                var to = from + step;
                if (to > $items.length) {
                    to = $items.length;
                }
                var $visible_items = $($items.slice(from, to));
                if ($visible_items.length) {
                    var $hidden_items = $items.not($visible_items);
                    $ca.children().each(function () {
                        var $this = $(this);
                        if ($this.has($visible_items).length || $visible_items.filter($this).length) {
                            $this.show();
                        }
                    });
                    $ca.children().each(function () {
                        var $this = $(this);
                        if ($this.has($hidden_items).length || $hidden_items.filter($this).length) {
                            $this.hide();
                        }
                    });
                    from = from + step;
                }
                return false;
            }).trigger('click');
        }
    };

    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    function initGallery() {
        if ('magnificPopup' in $) {
            $('.gallery').each(function () {
                var $gallery = $(this);
                $gallery.find('.gallery-item a').on('click', function (event) {
                    event.preventDefault();
                });
                $gallery.find('.gallery-item').on('click', function () {
                    var gallery_items = $.makeArray($gallery.find('.gallery-item').map(function () {
                        return {src: $(this).find('img').attr('src')};
                    }));
                    if (gallery_items.length > 0) {
                        $.magnificPopup.open({
                            items: gallery_items,
                            gallery: {
                                enabled: true
                            },
                            type: 'image'
                        }, $(this).closest('.gallery-item').index());
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
    }
    function initEntryGallery() {
        if ('flexslider' in $.fn) {
            $('.entry-gallery .images:not(.carousel):not(.thumbnails), .header-gallery .images:not(.carousel):not(.thumbnails)').each(function () {
                var $gallery = $(this);
                if ($gallery.data('flexslider') === undefined) {
                    if ($gallery.find('.image').length > 1) {
                        $gallery.flexslider({
                            selector: '.image',
                            smoothHeight: true,
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: false,
                            controlNav: false
                        }).show();
                    }
                }
            });
            $('.entry-gallery .images:not(.carousel).thumbnails, .header-gallery .images:not(.carousel).thumbnails').each(function () {
                function unique_id() {
                    return Math.round(new Date().getTime() + (Math.random() * 100));
                }
                var $gallery = $(this);
                if ($gallery.data('flexslider') === undefined) {
                    if ($gallery.find('.image').length > 1) {
                        $gallery.attr('id', unique_id());
                        var $thumbnails = $('<div id="' + $gallery.attr('id') + '-thumbnails" class="thumbnails"></div>').append('<ul class="slides"></ul>').insertAfter($gallery);
                        $gallery.find('.image').each(function () {
                            $(this).clone().removeClass('zoom').appendTo($('<li></li>').appendTo($thumbnails.find('.slides')));
                        });
                        var itemWidth = parseInt($thumbnails.find('ul.slides li').css('width'), 10);
                        if (!itemWidth) {
                            itemWidth = 150;
                        }
                        var itemHeight = parseInt($thumbnails.find('ul.slides li').css('height'), 10);
                        if (!itemHeight) {
                            itemHeight = 150;
                        }
                        $thumbnails.flexslider({
                            prevText: '',
                            nextText: '',
                            animation: "slide",
                            controlNav: false,
                            animationLoop: false,
                            pauseOnHover: true,
                            slideshow: false,
                            itemWidth: itemWidth,
                            itemHeight: itemHeight,
                            direction: $gallery.data('vertical') ? 'vertical' : 'horizontal',
                            touch: true,
                            mousewheel: false,
                            asNavFor: '#' + $gallery.attr('id')
                        });

                        $gallery.flexslider({
                            selector: '.image',
                            smoothHeight: true,
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: false,
                            controlNav: false,
                            sync: '#' + $gallery.attr('id') + '-thumbnails'
                        }).show();
                    }
                }
            });
        }
        if ('owlCarousel' in $.fn) {
            $('.entry-gallery .images.carousel, .header-gallery .images.carousel').each(function () {
                var $carousel = $(this);
                $carousel.data('center', 'yes');
                $carousel.data('loop', 'yes');
                initCarousel($carousel);
                if ('magnificPopup' in $) {
                    var isDragging = false;
                    $carousel.find('.image[data-popup]:not([data-popup=""])').on('mousedown', function () {
                        $window.on('mousemove.popup', function () {
                            isDragging = true;
                            $window.off('mousemove.popup');
                        });
                    }).on('mouseup', function () {
                        var wasDragging = isDragging;
                        isDragging = false;
                        $window.off('mousemove.popup');
                        if (!wasDragging) {
                            var gallery = $.makeArray($carousel.find('.owl-item:not(.cloned) .image[data-popup]:not([data-popup=""])').map(function () {
                                return {src: $(this).data('popup')};
                            }));
                            if (gallery.length > 0) {
                                var owlCarousel = $carousel.data('owlCarousel');
                                $.magnificPopup.open({
                                    items: gallery,
                                    gallery: {
                                        enabled: true
                                    },
                                    type: 'image'
                                }, owlCarousel.relative($(this).closest('.owl-item').index()));
                            } else {
                                $.magnificPopup.open({
                                    items: {
                                        src: $(this).data('popup')
                                    },
                                    type: 'image'
                                });
                            }
                        }
                    });
                }
            });
        }
    }
    function initCarousel($carousel) {
        function show($carousel) {
            if ($carousel.data('owlCarousel') === undefined) {
                while ($carousel.find('> div:not(.item)').length) {
                    $carousel.find('> div:not(.item)').slice(0, contents_per_item).wrapAll('<div class="item" />');
                }
                $carousel.show();
                if (width === '')
                    width = $carousel.width();
                var items = Math.round(($carousel.width() + margin) / (width + margin));
                if (items === 0 || full_width) {
                    items = 1;
                }
                if (lazy && (contents_per_item === 1) && ($carousel.find('.item').length >= items)) {
                    $carousel.find('.item .image.lazy').each(function () {
                        $(this).removeClass('lazy');
                        $(this).addClass('owl-lazy');
                    });
                }
                var options = {
                    items: items,
                    dotsEach: items,
                    responsive: {},
                    smartSpeed: 500,
                    center: ($carousel.data('center') === 'yes'),
                    loop: ($carousel.data('loop') === 'yes') && ($carousel.find('.item').length > items),
                    autoplay: true,
                    autoplayTimeout: 8000,
                    lazyLoad: lazy && (contents_per_item === 1) && ($carousel.find('.item').length >= items),
                    autoplayHoverPause: true,
                    nav: true,
                    dots: true,
                    navText: ['', '']
                };
                if (margin > 0) {
                    options.margin = margin;
                }
                if (stagePadding > 0) {
                    options.stagePadding = stagePadding;
                }
                try {
                    $carousel.owlCarousel(options).on('translated.owl.carousel', function (event) {
                        try {
                            BackgroundCheck.refresh($carousel.find('.owl-controls .owl-prev, .owl-controls .owl-next'));
                        } catch (e) {
                        }
                    });
                } catch (e) {
                }
                $carousel.find('.item').equalizeHeights();
                var height = $carousel.find('.owl-item').height();
                $carousel.find('.owl-stage').css('height', height + 'px');
                try {
                    BackgroundCheck.init({
                        targets: $carousel.find('.owl-controls .owl-prev, .owl-controls .owl-next'),
                        images: $carousel.find('.item .image')
                    });
                } catch (e) {
                }
            }
        }
        function is_visible($carousel) {
            var visible = true;
            $carousel.parents().each(function () {
                var parent = this;
                if ($(parent).css('display') === 'none' && visible) {
                    visible = false;
                }
            });
            return visible;
        }
        if ($carousel.find('> div').length > 0) {
            var width = $carousel.data('width') ? $carousel.data('width') : '';
            var height = $carousel.data('height') ? $carousel.data('height') : '';
            var margin = isNaN(parseInt($carousel.data('margin'), 10)) ? 0 : parseInt($carousel.data('margin'), 10);
            var stagePadding = isNaN(parseInt($carousel.data('stagePadding'), 10)) ? 0 : parseInt($carousel.data('stagePadding'), 10);
            var full_width = $carousel.data('full-width') === 'yes';
            var contents_per_item = $carousel.data('contents-per-item') ? $carousel.data('contents-per-item') : 1;
            if (!$carousel.data('entries')) {
                $carousel.data('entries', $carousel.find('> div'));
            }
            var lazy = ($carousel.find('.image.lazy').length > 0);
            if (typeof width !== typeof undefined && width !== false && typeof height !== typeof undefined && height !== false) {
                if (height !== '') {
                    $carousel.find('.item .image').each(function () {
                        $(this).height(height);
                    });
                }
                if (is_visible($carousel)) {
                    show($carousel);
                } else {
                    $document.on('click.azexo', function () {
                        if ($carousel.data('owlCarousel') === undefined) {
                            if (is_visible($carousel)) {
                                show($carousel);
                            }
                        }
                    });
                }
            }
            return show;
        }
        return false;
    }
    function initAZEXOPostList() {
        if ('owlCarousel' in $.fn) {
            $window.on('resize', function () {
                $('.owl-carousel.posts-list').each(function () {
                    var $carousel = $(this);
                    if ($carousel.data('owlCarousel') !== undefined) {
                        var owl = $carousel.data('owlCarousel');
                        var width = $carousel.data('width') ? $carousel.data('width') : '';
                        var full_width = $carousel.data('full-width') === 'yes';
                        if (width === '')
                            width = $carousel.width();
                        var responsive = (owl.options.responsive ? owl.options.responsive : {});
                        var items = Math.round(($carousel.width() + owl.options.margin) / (width + owl.options.margin));
                        if (items === 0 || full_width) {
                            items = 1;
                        }
                        responsive[$window.width()] = {
                            items: items
                        };
                        owl.options.responsive = responsive;
                        $carousel.find('.item').css('height', '');
                        $carousel.find('.owl-stage').css('height', '');
                        owl.refresh();
                        setTimeout(function () {
                            owl.refresh();
                            $carousel.find('.item').equalizeHeights();
                            owl.refresh();
                        });
                    }
                });
            });
            $('.owl-carousel.posts-list').each(function () {
                var $carousel = $(this);
                var show = initCarousel($carousel);
                if (show !== false) {
                    $carousel.closest('.posts-list-wrapper').find('> .list-header > .list-filter > .filter-term').off('click.posts-list-carousel').on('click.posts-list-carousel', function () {
                        if ($carousel.data('owlCarousel') !== undefined) {
                            $carousel.data('owlCarousel').destroy();
                            $($carousel.data('entries')).detach();
                            $carousel.empty();
                            $carousel.append($carousel.data('entries'));
                            if ($(this).data('term')) {
                                $carousel.find('.filterable:not(.' + $(this).data('term') + ')').detach();
                            }
                            show($carousel);
                        }
                    });
                }
            });
        }
        $('.posts-list-wrapper .list-filter .filter-term').each(function () {
            var $this = $(this);
            $this.data('posts-list-wrapper', $this.closest('.posts-list-wrapper'));
            $this.off('click.posts-list').on('click.posts-list', function () {
                var $this = $(this);
                $this.closest('.list-filter').find('.filter-term').removeClass('active');
                $this.addClass('active');
                if ($this.data('term')) {
                    $this.data('posts-list-wrapper').find('> .posts-list .filterable.' + $this.data('term')).addClass('showed');
                    $this.data('posts-list-wrapper').find('> .posts-list .filterable:not(.' + $this.data('term') + ')').removeClass('showed');
                } else {
                    $this.data('posts-list-wrapper').find('> .posts-list .filterable').addClass('showed');
                }
                $this.data('posts-list-wrapper').find('> .posts-list .filterable').removeClass('even').removeClass('odd');
                $this.data('posts-list-wrapper').find('> .posts-list .filterable.showed:even').addClass('even');
                $this.data('posts-list-wrapper').find('> .posts-list .filterable.showed:odd').addClass('odd');
            });
        });
        $('.posts-list-wrapper .list-filter').addClass('filtering');
        $('.posts-list-wrapper > .posts-list .filterable').addClass('showed');
        $('.posts-list-wrapper > .posts-list .filterable:even').addClass('even');
        $('.posts-list-wrapper > .posts-list .filterable:odd').addClass('odd');
        $('.azexo-posts-list-filters').each(function () {
            var $this = $(this);
            var filters = $('.posts-list-wrapper .list-filter');
            if (filters.length > 0 && $this.children().length === 0) {
                filters.detach();
                $this.append(filters);
            }
        });
        $('.azexo-posts-list-groups').each(function () {
            $(this).empty();
            var groups_menu = $('<ul class="groups-menu"></ul>').appendTo(this);
            $('.posts-list-wrapper .list-group').each(function () {
                var $this = $(this);
                var id = $this.find('[type="checkbox"]').attr('id');
                var header = $this.find('.group-header h4').text();
                $('<li><a class="roll" href="#' + id + '">' + header + '</a></li>').appendTo(groups_menu).find('a').off('click').on('click', function () {
                    var $this = $(this);
                    $this.closest('ul').find('.active').removeClass('active');
                    $this.addClass('active');
                });
            });
        });
    }
    function initAZEXOPostMasonry() {
        if ('masonry' in $.fn) {
            $('.site-content.masonry-post').each(function () {
                var $grid = $(this);
                var width = $grid.find('.entry:not(.no-results) .entry-thumbnail .image[data-width]').attr('data-width');
                if (typeof width === 'undefined') {
                    width = 400;
                }
                var height = $grid.find('.entry:not(.no-results) .entry-thumbnail .image[data-height]').attr('data-height');
                if (typeof height === 'undefined') {
                    height = 400;
                }
                var columns = Math.ceil($grid.width() / width);
                var columnWidth = Math.floor($grid.width() / columns);
                $grid.find('.entry:not(.no-results)').css('width', columnWidth + 'px');
                var ratio = columnWidth / width;
                $grid.find('.entry:not(.no-results) .entry-thumbnail .image').css('height', height * ratio + 'px');
                setTimeout(function () {
                    $grid.masonry('destroy');
                    $grid.masonry({
                        columnWidth: columnWidth,
                        itemSelector: '.entry:not(.no-results)'
                    });
                }, 0);
            });
        }
    }
    function initMobileMenu() {
        $(".mobile-menu-button span").on("tap click", function (e) {
            e.preventDefault();
            var $ul = $(".mobile-menu > div > ul");
            if ($ul.is(":visible")) {
                $ul.slideUp(200);
            } else {
                $ul.slideDown(200);
            }
        });

        $('.mobile-menu ul.nav-menu:not(.vc) > li.menu-item.menu-item-has-children, .mobile-menu ul.sub-menu:not(.vc) > li.menu-item.menu-item-has-children').append('<span class="mobile-arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>');
        $('.mobile-menu ul.nav-menu:not(.vc) > .mega, .mobile-menu ul.sub-menu:not(.vc) > .mega').append('<span class="mobile-arrow"><i class="fa fa-angle-right"></i><i class="fa fa-angle-down"></i></span>');

        $(".mobile-menu ul > li.menu-item-has-children > span.mobile-arrow").on("tap click", function (e) {
            e.preventDefault();
            var $this = $(this);
            var $menu_item_has_children = $this.closest("li.menu-item-has-children");
            if ($menu_item_has_children.find("> ul.sub-menu").is(":visible")) {
                $menu_item_has_children.find("> ul.sub-menu").slideUp(200);
                $menu_item_has_children.removeClass("open-sub");
            } else {
                $menu_item_has_children.addClass("open-sub");
                $menu_item_has_children.find("> ul.sub-menu").slideDown(200);
            }
        });
        $(".mobile-menu > div > ul > li.mega > span.mobile-arrow").on("tap click", function (e) {
            e.preventDefault();
            var $this = $(this);
            var $mega = $this.closest("li.mega");
            if ($mega.find("> .page").is(":visible")) {
                $mega.find("> .page").slideUp(200);
                $mega.removeClass("open-sub");
            } else {
                $mega.addClass("open-sub");
                $mega.find("> .page").slideDown(200);
            }
        });
        $(".mobile-menu ul li > a").on("click", function () {
            var $this = $(this);
            if ($this.attr("href") !== "http://#" && $this.attr("href") !== "#") {
            }
        });
    }
    function initSearchForm() {
        $('.searchform').each(function () {
            var $form = $(this);
            $form.find('input[name="s"]').attr('placeholder', $form.find('[type="submit"]').val()).on('keydown', function (event) {
                if (event.keyCode === 13) {
                    $form.find('[type="submit"]').trigger('click');
                }
            });
            var toggled = false;
            $form.find('.toggle').on('click', function () {
                $form.find('.search-wrapper').show().find('input[name="s"]').focus();
                $(this).hide();
                toggled = true;
            });
            $form.find('input[name="s"]').on('blur', function () {
                if (toggled) {
                    $form.find('.toggle').show();
                    $form.find('.search-wrapper').hide();
                    toggled = false;
                }
            });
        });

    }
    function initMegaMenu() {
        function on_hover($menu, $page) {
            $page.css('width', $menu.closest('.container').width() + 'px');
            $page.css('left', ($menu.closest('.container').offset().left - $page.parent().offset().left) + 'px');
        }
        function widget_on_hover($menu, $page) {
            $page.css('width', $menu.closest('.container').width() - $page.parent().outerWidth() + 'px');
        }
        $('.primary-navigation .nav-menu, .secondary-navigation .nav-menu').each(function () {
            var $menu = $(this);
            $menu.find('.mega:not(.compact) .page').each(function () {
                var $page = $(this);
                $page.parent().on('hover', function () {
                    on_hover($menu, $page);
                });
                on_hover($menu, $page);
                if ('imagesLoaded' in $.fn) {
                    $menu.closest('.container').imagesLoaded(function () {
                        on_hover($menu, $page);
                    });
                }
                $window.on('resize', function () {
                    on_hover($menu, $page);
                });
            });
        });
        $('.widget_nav_menu .menu').each(function () {
            var $menu = $(this);
            $menu.find('.page').each(function () {
                var $page = $(this);
                $page.parent().on('hover', function () {
                    widget_on_hover($menu, $page);
                });
                widget_on_hover($menu, $page);
                if ('imagesLoaded' in $.fn) {
                    $menu.closest('.container').imagesLoaded(function () {
                        widget_on_hover($menu, $page);
                    });
                }
                $window.on('resize', function () {
                    widget_on_hover($menu, $page);
                });
            });
        });
    }
    function initStickyMenu() {
        if ($('.header-main').length && $('.header-main .header-parts').children().length) {
            var $header_main = $('.header-main');
            var header_main_top = 0;
            header_main_top = $header_main.offset().top;
            var $site_header = $('.site-header');
            var $mm = $('nav.mobile-menu');
            $site_header.imagesLoaded(function () {
                var interval = setInterval(function () {
                    if (!$site_header.hasClass('scrolled')) {
                        header_main_top = $header_main.offset().top;
                        clearInterval(interval);
                    }
                }, 100);
                $window.on('scroll', function () {
                    if ($window.scrollTop() > header_main_top) {
                        $site_header.addClass('scrolled');
                        if ($mm.css('display') === 'none')
                            $header_main.addClass('animated fadeInDown');
                    } else {
                        $site_header.removeClass('scrolled');
                        if ($mm.css('display') === 'none')
                            $header_main.removeClass('animated fadeInDown');
                    }
                });
            });
        }
    }
    function initSticky() {
        $('[data-sticky-class]').each(function () {
            var $sticky = $(this);
            var top = $sticky.offset().top;
            var sticky_class = $sticky.data('sticky-class');

            $body.imagesLoaded(function () {
                var interval = setInterval(function () {
                    if (!$body.hasClass(sticky_class)) {
                        top = $sticky.offset().top;
                        clearInterval(interval);
                    }
                }, 100);
                $window.on('scroll', function () {
                    if ($window.scrollTop() > top) {
                        $body.addClass(sticky_class);
                    } else {
                        $body.removeClass(sticky_class);
                    }
                });
            });
        });
    }
    function initStickySidebar() {
        if ('stick_in_parent' in $.fn) {
            if ($window.width() > 1100) {
                setTimeout(function () {
                    $("#tertiary .sidebar-inner, #additional .sidebar-inner").stick_in_parent({
                        recalc_every: 1,
                        offset_top: $('.header-main').outerHeight()
                    });
                    $("#tertiary label[for], #additional label[for]").on('click', function () {
                        $body.trigger("sticky_kit:recalc");
                    });
                }, 0);
            }
        }
    }
    function initImageZoom() {
        $('.image.zoom').each(function () {
            var $image = $(this);
            var img = new Image();
            img.src = $image.css('background-image').replace(/url\(|\)$|"|'/ig, '');
            $(img).imagesLoaded(function () {
                if (img.width > $image.width() || img.height > $image.height()) {
                    $image.off('mouseenter.azexo').on('mouseenter.azexo', function () {
                        $image.css('background-size', 'initial');
                    });
                    $image.off('mouseleave.azexo').on('mouseleave.azexo', function () {
                        $image.css('background-size', '');
                        $image.css('background-position', '');
                    });
                    $image.off('mousemove.azexo').on('mousemove.azexo', function (event) {
                        $image.css('background-position', event.offsetX / $image.width() * 100 + '% ' + event.offsetY / $image.height() * 100 + '%');
                    });
                }
            });
        });
    }
    function initImageLazyLoad() {
        if ('waypoint' in $.fn) {
            $('.image.lazy').each(function () {
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
        }
    }
    function initLinkScrolling() {
        $('a[href*="#"].roll, .roll a[href*="#"]').off('click').on('click', function (e) {
            if (this.href.split('#')[0] === '' || window.location.href.split('#')[0] === this.href.split('#')[0]) {
                e.preventDefault();
                var hash = this.hash;
                $('html, body').stop().animate({
                    'scrollTop': $(hash).offset().top - $('.header-main').height()
                }, 2000);
            }
        });
    }
    function initCountDown() {
        if ('countdown' in $.fn) {
            $('.time-left .time').each(function () {
                var $timer = $(this);
                if ($timer.data('countdownInstance') === undefined) {
                    $timer.countdown($timer.data('time'), function (event) {
                        $timer.find('.days .count').text(event.offset.totalDays);
                        $timer.find('.hours .count').text(event.offset.hours);
                        $timer.find('.minutes .count').text(event.offset.minutes);
                        $timer.find('.seconds .count').text(event.offset.seconds);
                    });
                }
            });
        }
    }
    function initTrigger() {
        $('.trigger').each(function () {
            var $trigger = $(this);
            //$trigger.data('trigger-on');
            //$trigger.data('trigger-off');
            var $triggerable = null;
            if ($trigger.is('a')) {
                $triggerable = $trigger;
            } else {
                $triggerable = $trigger.find('a');
            }
            $triggerable.addClass('triggerable').off('click.azexo').on('click.azexo', function () {
                var $this = $(this);
                $($trigger.data('trigger-on') + ', ' + $trigger.data('trigger-off')).each(function () {
                    $(this).removeClass('end');
                });
                if ($trigger.data('trigger-on') && $trigger.data('trigger-off')) {
                    if ($this.is('.active')) {
                        if ($trigger.closest('.triggers').find('.triggerable').length === 0) {
                            $this.removeClass('active');
                            $($trigger.data('trigger-off')).removeClass($trigger.data('trigger-class'));
                            $document.trigger('triggered.azexo');
                        }
                    } else {
                        $trigger.closest('.triggers').find('.triggerable.active').each(function () {
                            var $this = $(this);
                            $this.removeClass('active');
                            $($this.closest('.trigger').data('trigger-off')).removeClass($trigger.data('trigger-class'));
                            $document.trigger('triggered.azexo');
                        });
                        $this.addClass('active');
                        $($trigger.data('trigger-on')).addClass($trigger.data('trigger-class'));
                        $document.trigger('triggered.azexo');
                    }
                } else {
                    if ($trigger.data('trigger-on') || $trigger.data('trigger-off')) {
                        $($trigger.data('trigger-on')).addClass($trigger.data('trigger-class'));
                        $($trigger.data('trigger-off')).removeClass($trigger.data('trigger-class'));
                        $document.trigger('triggered.azexo');
                    }
                }
                return false;
            });
            $($trigger.data('trigger-on') + ', ' + $trigger.data('trigger-off')).on("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function (e) {
                $(this).addClass('end');
            });
        });
    }
    function initSelect() {
        if ('select2' in $.fn) {
            $('select.select2').select2();
        }
        $('select.hierarchical').hierarchicalSelect();
    }
    function initInfiniteScroll() {
        if ('infinitescroll' in $.fn) {
            $('#content.infinite-scroll').infinitescroll({
                navSelector: "nav.navigation .loop-pagination",
                nextSelector: "nav.navigation .loop-pagination a.next",
                itemSelector: "#content > .entry.post",
                loading: {
                    img: azexo.templateurl + "/images/infinitescroll-loader.svg",
                    msgText: '<em class="infinite-scroll-loading">Loading ...</em>',
                    finishedMsg: '<em class="infinite-scroll-done">Done</em>'
                },
                errorCallback: function () {
                }
            }, function (arrayOfNewElems) {
                $document.trigger('infinitescroll', {new_elems: arrayOfNewElems});
                window.azexo.refresh();
                $('#content.infinite-scroll .image.lazy').each(function () {
                    var $this = $(this);
                    if ($this.data('waypoint_handler')) {
                        $this.data('waypoint_handler')();
                    }
                });
            });
            $('#content.infinite-scroll .image.lazy').each(function () {
                var $this = $(this);
                if ($this.data('waypoint_handler')) {
                    $this.data('waypoint_handler')();
                }
            });
            if ($('#content').is('.infinite-scroll')) {
                $('nav.navigation.paging-navigation').hide();
            }
        }
    }
    function initPopup() {
        if ('magnificPopup' in $.fn) {
            $('a.image-popup').magnificPopup({
                type: 'image',
                removalDelay: 300,
                mainClass: 'mfp-fade',
                overflowY: 'scroll'
            });
            $('a.iframe-popup').magnificPopup({
                type: 'iframe',
                removalDelay: 300,
                mainClass: 'mfp-fade',
                overflowY: 'scroll',
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
        }
    }
    window.azexo.refresh = function () {
        initEntryGallery();
        initAZEXOPostList();
        initLinkScrolling();
        initAZEXOPostMasonry();
        initImageZoom();
        initImageLazyLoad();
        initCountDown();
        initTrigger();
    };
    $(function () {
        initGallery();
        initMobileMenu();
        initMegaMenu();
        initSearchForm();
        initStickySidebar();
        initEntryGallery();
        initStickyMenu();
        //initSticky();
        initAZEXOPostList();
        initAZEXOPostMasonry();
        initImageZoom();
        initImageLazyLoad();
        initLinkScrolling();
        initCountDown();
        initTrigger();
        initSelect();
        initInfiniteScroll();
        initPopup();
        $('form.ordering').on('change', 'select.orderby', function () {
            $(this).closest('form').trigger('submit');
        });
        if ('fitVids' in $.fn) {
            $('.entry-video, .wpb_video_wrapper').fitVids();
        }
        $document.ajaxComplete(function () {
            window.azexo.refresh();
        });
        $window.on('resize', function () {
            initAZEXOPostMasonry();
        });
        $window.on('azsl-social-login', function(){
            $status.fadeIn("slow");
            $preloader.fadeIn("slow");            
        });
        $('select.azexo-taxonomy-dropdown').change(function () {
            var v = $(this).val();
            if (v) {
                location.href = azexo.homeurl + "?cat=" + v;
            }
        });
        //fix checkboxes appearence
        $('label > input[type="checkbox"]').each(function () {
            var $this = $(this);
            var $label = $this.parent();
            if($label.closest('.az-checkboxes').length) {
                return;
            }
            //WC TOC more than 2 children
//            if ($label.children().length === 2) {
                $this.detach();
                $this.insertBefore($label);
                if ((!$this.attr('id') && !$label.attr('for')) || ($('#' + $this.attr('id')).length > 1)) {
                    $this.attr('id', makeid());
                    $label.attr('for', $this.attr('id'));
                } else {
                    if ($this.attr('id')) {
                        $label.attr('for', $this.attr('id'));
                    } else {
                        if ($label.attr('for')) {
                            $this.attr('id', $label.attr('for'));
                        }
                    }
                }
//            }
        });
    });
})(jQuery);