(function($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    window.azh = $.extend({}, window.azh);
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
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    var customize = ('azh' in $.QueryString && $.QueryString['azh'] == 'customize');
    $window.on('az-frontend-init', function(event, data) {
        var $wrapper = data.wrapper;
        function unique_id() {
            return Math.round(new Date().getTime() + (Math.random() * 100));
        }
        function loadScript(path, callback) {
            var done = false;
            var scr = document.createElement('script');
            scr.onload = handleLoad;
            scr.onreadystatechange = handleReadyStateChange;
            scr.onerror = handleError;
            scr.src = path;
            document.body.appendChild(scr);
            function handleLoad() {
                if (!done) {
                    done = true;
                    callback(path, "ok");
                }
            }
            function handleReadyStateChange() {
                var state;

                if (!done) {
                    state = scr.readyState;
                    if (state === "complete") {
                        handleLoad();
                    }
                }
            }
            function handleError() {
                if (!done) {
                    done = true;
                    callback(path, "error");
                }
            }
        }
        $wrapper.find('.az-tabs').each(function() {
            var $tabs = $(this);
            if (!$tabs.data('az-tabs')) {
                $tabs.find('> div:first-child > span > a[href^="#"]').on('click', function(event) {
                    var $this = $(this);
                    event.preventDefault();
                    $this.parent().addClass("az-active");
                    $this.parent().siblings().removeClass("az-active");
                    var tab = $this.attr("href");
                    $this.closest('.az-tabs').find('> div:last-child > div').not(tab).css("display", "none");
                    $(tab).fadeIn();
                });
                $tabs.find('> div:first-child > span:first-child > a[href^="#"]').click();
                $tabs.data('az-tabs', true);
            }
        });
        $wrapper.find('.az-accordion').each(function() {
            var $accordion = $(this);
            if (!$accordion.data('az-accordion')) {
                $accordion.find('> div > div:first-child').on('click', function(event) {
                    var $this = $(this);
                    $this.parent().addClass("az-active").find('> div:last-child').slideDown();
                    $this.parent().siblings().removeClass("az-active").find('> div:last-child').slideUp();
                });
                $accordion.find('> div:first-child > div:first-child').parent().addClass("az-active").find('> div:last-child').slideDown(0);
                $accordion.find('> div:first-child > div:first-child').parent().siblings().removeClass("az-active").find('> div:last-child').slideUp(0);
                $accordion.data('az-accordion', true);
            }
        });
        if ('flexslider' in $.fn) {
            $wrapper.find('.az-slider').each(function() {
                var $slider = $(this);
                if (!$slider.data('az-slider')) {
                    if ($slider.data('thumbnails') !== 'yes') {
                        $slider.find('.az-slides').flexslider({
                            namespace: "az-flex-",
                            selector: '> *',
                            smoothHeight: true,
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: false,
                            controlNav: false,
                            customDirectionNav: $slider.find('.az-flex-direction-nav a')
                        });
                    } else {
                        var $gallery = $slider.find('.az-slides');
                        $gallery.attr('id', unique_id());
                        var $thumbnails = false;
                        if ($slider.find('.az-thumbnails').length) {
                            $thumbnails = $slider.find('.az-thumbnails');
                            $thumbnails.attr('id', $gallery.attr('id') + '-thumbnails');
                        } else {
                            $thumbnails = $('<div id="' + $gallery.attr('id') + '-thumbnails" class="az-thumbnails"></div>');
                            $('<div class="az-flex-thumbnails"></div>').appendTo($thumbnails).append($gallery.children().clone());
                            $('<div class="az-flex-direction-nav"><a href="#" class="az-flex-prev"></a><a href="#" class="az-flex-next"></a></div>').appendTo($thumbnails);
                            $thumbnails.insertAfter($gallery);
                        }
                        var itemWidth = $thumbnails.find('.az-flex-thumbnails').children().first().width();
                        if (!itemWidth) {
                            itemWidth = 150;
                        }
                        var itemHeight = $thumbnails.find('.az-flex-thumbnails').children().first().height();
                        if (!itemHeight) {
                            itemHeight = 150;
                        }
                        $thumbnails.flexslider({
                            namespace: "az-flex-",
                            selector: '.az-flex-thumbnails > *',
                            prevText: '',
                            nextText: '',
                            animation: "slide",
                            controlNav: false,
                            animationLoop: false,
                            pauseOnHover: true,
                            slideshow: false,
                            itemWidth: itemWidth,
                            itemHeight: itemHeight,
                            touch: true,
                            mousewheel: false,
                            customDirectionNav: $thumbnails.find('.az-flex-direction-nav a'),
                            asNavFor: '#' + $gallery.attr('id')
                        });

                        $gallery.flexslider({
                            namespace: "az-flex-",
                            selector: '> *',
                            smoothHeight: true,
                            prevText: '',
                            nextText: '',
                            touch: true,
                            pauseOnHover: true,
                            mousewheel: false,
                            controlNav: false,
                            customDirectionNav: $slider.find('> .az-flex-direction-nav a'),
                            sync: '#' + $gallery.attr('id') + '-thumbnails'
                        });
                    }
                    $slider.data('az-slider', true);
                }
            });
        }
        if ('AZowlCarousel' in $.fn) {
            $wrapper.find('.az-carousel').each(function() {
                var $carousel = $(this);
                if (!$carousel.data('az-carousel')) {
                    var defaults = {
                        items: 3,
                        loop: false,
                        center: false,
                        rewind: false,
                        mouseDrag: true,
                        touchDrag: true,
                        pullDrag: false,
                        freeDrag: false,
                        margin: 0,
                        stagePadding: 0,
                        merge: false,
                        mergeFit: true,
                        autoWidth: false,
                        startPosition: 0,
                        rtl: false,
                        smartSpeed: 250,
                        fluidSpeed: false,
                        dragEndSpeed: false,
                        responsive: {},
                        responsiveRefreshRate: 200,
                        responsiveBaseElement: window,
                        fallbackEasing: 'swing',
                        info: false,
                        nestedItemSelector: false,
                        itemElement: 'div',
                        stageElement: 'div',
                        refreshClass: 'az-owl-refresh',
                        loadedClass: 'az-owl-loaded',
                        loadingClass: 'az-owl-loading',
                        rtlClass: 'az-owl-rtl',
                        responsiveClass: 'az-owl-responsive',
                        dragClass: 'az-owl-drag',
                        itemClass: 'az-owl-item',
                        stageClass: 'az-owl-stage',
                        stageOuterClass: 'az-owl-stage-outer',
                        grabClass: 'az-owl-grab',
                        autoRefresh: true,
                        autoRefreshInterval: 500,
                        lazyLoad: false,
                        autoHeight: false,
                        autoHeightClass: 'az-owl-height',
                        video: false,
                        videoHeight: false,
                        videoWidth: false,
                        animateOut: false,
                        animateIn: false,
                        autoplay: false,
                        autoplayTimeout: 5000,
                        autoplayHoverPause: true,
                        autoplaySpeed: false,
                        nav: true,
                        navText: ['<span></span>', '<span></span>'],
                        navSpeed: false,
                        navElement: 'div',
                        navContainer: false,
                        navContainerClass: 'az-owl-nav',
                        navClass: ['az-owl-prev', 'az-owl-next'],
                        slideBy: 1,
                        dotClass: 'az-owl-dot',
                        dotsClass: 'az-owl-dots',
                        dots: true,
                        dotsEach: false,
                        dotsData: false,
                        dotsSpeed: false,
                        dotsContainer: false,
                        URLhashListener: false
                    };
                    var options = {};
                    for (var key in defaults) {
                        if ($carousel.data(key)) {
                            options[key] = $carousel.data(key);
                            if (options[key] === 'yes') {
                                options[key] = true;
                            }
                            if (options[key] === 'no') {
                                options[key] = false;
                            }
                        }
                    }
                    options = $.extend({}, defaults, options);
                    if (customize) {
                        options['autoplay'] = false;
                        options['loop'] = false;
                        options['mouseDrag'] = false;
                        options['touchDrag'] = false;
                        options['pullDrag'] = false;
                        options['freeDrag'] = false;
                    }
                    $carousel.AZowlCarousel(options);
                    $window.trigger('resize');
                    $carousel.data('az-carousel', true);
                    $carousel.on('azh-active', function(event) {
                        var owlCarousel = $(this).data('owl.carousel');
                        var index = owlCarousel.relative($(event.target).closest('.az-owl-item').index());
                        $(this).trigger('to.owl.carousel', index);
                    });                    
                }
            });
        }
        if ('magnificPopup' in $.fn) {
            $wrapper.find('.az-gallery').each(function() {
                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true
                    }
                });
            });
            $wrapper.find('a.az-image-popup').magnificPopup({
                type: 'image',
                removalDelay: 300,
                mainClass: 'mfp-fade',
                overflowY: 'scroll'
            });
            $wrapper.find('a.az-iframe-popup').magnificPopup({
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
        if ('isotope' in $.fn) {
            $wrapper.find('[data-isotope-items]').each(function() {
                var $grid = $(this);
                $grid.isotope($grid.data('isotope-items'));
                $grid.imagesLoaded().progress(function() {
                    $grid.isotope('layout');
                });
                $grid.one('arrangeComplete', function() {
                    $window.trigger('resize');
                });
                var $filters = false;
                var filters_closeness = false;
                $('[data-isotope-filters]').each(function() {
                    var parent = $grid.parents().has(this).first();
                    if ($filters === false) {
                        $filters = $(this);
                        filters_closeness = $grid.parents().index(parent);
                    } else {
                        if (filters_closeness > $grid.parents().index(parent)) {
                            $filters = $(this);
                            filters_closeness = $grid.parents().index(parent);
                        }
                    }
                });
                if ($filters) {
                    $filters.find('[data-filter]').on('click', function() {
                        var $this = $(this);
                        $grid.isotope({filter: $this.attr('data-filter')});
                        $filters.find('[data-filter].az-is-checked').removeClass('az-is-checked');
                        $this.addClass('az-is-checked');
                        return false;
                    });
                }
            });
        }
        if ('masonry' in $.fn) {
            $wrapper.find('[data-masonry-items]').each(function() {
                var $grid = $(this);
                $grid.masonry($grid.data('masonry-items'));
                $grid.imagesLoaded().progress(function() {
                    $grid.masonry('layout');
                });
                $grid.one('arrangeComplete', function() {
                    $window.trigger('resize');
                });
            });
        }
        $wrapper.find('.az-share').each(function() {
            var $share = $(this);
        });
        $wrapper.find('.az-preloader').each(function() {
            $(this).fadeOut("slow");
        });
        if ('knob' in $.fn) {
            $wrapper.find(".az-knob").knob();
        }
    });
    $window.on('az-frontend-after-init', function(event, data) {
        if ('azh' in $.QueryString && $.QueryString['azh'] === 'fullpage') {
            if ('fullpage' in $.fn) {
                var $content_wrapper = false;
                if ($body.is('.page-template-azexo-html-template')) {
                    $content_wrapper = $body.find('> .page');
                } else {
                    if ($body.is('.page')) {
                        if ('azexo' in window) {
                            $content_wrapper = $body.find('#content > .entry > .entry-content');
                        } else {
                            $content_wrapper = $body.find('[data-section]').parent();
                        }
                    }
                }
                if ($content_wrapper) {
                    $content_wrapper.find('[data-section]').addClass('section');
                    $content_wrapper.fullpage({
                        navigation: true,
                        navigationPosition: 'right',
                    });
                }
            }
        }
    });
    $(function() {
    });
})(jQuery);