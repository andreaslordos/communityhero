(function ($) {
    $(function () {
        var hovers = [];
        function init_sections() {
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
                $('.azh-library-filters .azh-counter').text($('.azh-sections .azh-section:visible').length + ' ' + azh.i18n.sections);
                $('.azh-sections').isotope({filter: '.azh-section:visible'});
            }
            $('.azh-add-section').remove();
            $('.azh-actions').remove();
            $('.azh-library').show();
            var categories = $('.azh-library-filters .azh-categories').off('change').on('change', filters_change);
            var tags_select = $('<select class="azh-tags"></select>').appendTo('.azh-library-filters').on('change', filters_change);
            var counter = $('<div class="azh-counter">' + $('.azh-sections .azh-section').length + ' ' + azh.i18n.sections + '</div>').appendTo('.azh-library-filters');
            $('<option selected value="">' + azh.i18n.filter_by_tag + '</option>').appendTo(tags_select);
            Object.keys(azh.tags).sort().forEach(function (tag, i) {
                $('<option value="' + tag + '">' + tag + '</option>').appendTo(tags_select);
            });
            $('.azh-sections').isotope({
                masonry: {
                    gutter: 20
                },
                onLayout: function () {
                }
            });

            $('.azh-sections').imagesLoaded().progress(function () {
                $('.azh-sections').isotope('layout');
            });
            $('[name="sections-elements"]').on('change', function () {
                $('.azh-sections').isotope('layout');
            });
            $('.azh-sections .azh-section').each(function () {
//                $(this).on('mouseenter', function(enter) {
//                    function show_hover(section) {
//                        var hover = $(section).data('hover');
//                        if (!hover) {
//                            hover = $('<div class="azh-hover"><div class="azh-loading"><div class="azh-spinner"></div></div><iframe src="' + azh.site_url + '/?azh=library&files=' + $(section).data('path') + '"></iframe></div>').appendTo('body');
//                            $(section).data('hover', hover);
//                            hovers.push(hover);
//                        }
//                        $(hover).find('iframe').on('load', function() {
//                            $(hover).find('.azh-loading').remove();
//                        });
//                        if ($(section).is(':hover')) {
//                            $(hover).css('visibility', 'visible');
//                            $(hover).css('left', enter.clientX + 'px');
//                            $(hover).css('top', enter.clientY + 'px');
//                            $(section).on('mousemove', function(move) {
//                                $(hover).css('left', move.clientX + 'px');
//                                $(hover).css('top', move.clientY + 'px');
//                            });
//                        } else {
//                            $(hover).css('visibility', 'hidden');
//                        }
//                    }
//                    var section = this;
//                    if ($(section).data('hover')) {
//                        show_hover(section);
//                    } else {
//                        if (!$(section).data('waiting')) {
//                            var waiting = setInterval(function() {
//                                var loading = 0;
//                                $(hovers).each(function() {
//                                    if ($(this).find('.azh-loading').length > 0) {
//                                        loading++;
//                                    }
//                                });
//                                if (loading == 0) {
//                                    clearInterval(waiting);
//                                    $(section).data('waiting', false);
//                                    show_hover(section);
//                                }
//                            }, 100);
//                            $(section).data('waiting', waiting);
//                        }
//                    }
//                });
//                $(this).on('mouseleave', function() {
//                    $(this).off('mousemove');
//                    if ($(this).data('hover')) {
//                        $(this).data('hover').css('visibility', 'hidden');
//                    }
//                });
                $(this).on('click', function () {
                    $(this).clone().appendTo('.azh-structure .azh-sections').on('contextmenu', function () {
                        return false;
                    }).on('mouseup', function (event) {
                        if (event.which == 3) {
                            $(this).remove();
                        }
                    });
                    $('.azh-structure button').show();
                    $('.azh-structure .azh-section').attr('style', function (i, style) {
                        if (style) {
                            return style.replace(/position:[^;]+;?/g, '').replace(/top:[^;]+;?/g, '').replace(/left:[^;]+;?/g, '');
                        }
                    });
                    $('.azh-structure .azh-sections').css('height', '');
                    $('.azh-structure .azh-sections').sortable('refresh');
                });
            });
        }
        function init_elements() {
            function filters_change() {
                var category = $(categories).find('option:selected').val();
                var tag = $(tags_select).find('option:selected').val();
                if (category == '' && tag == '') {
                    $('.azh-library .azh-elements .azh-element').show();
                } else {
                    if (category != '' && tag == '') {
                        $('.azh-library .azh-elements .azh-element').hide();
                        $('.azh-library .azh-elements .azh-element[data-path^="' + category + '"]').show();
                    }
                    if (category == '' && tag != '') {
                        $('.azh-library .azh-elements .azh-element').hide();
                        $('.azh-library .azh-elements .azh-element.' + tag).show();
                    }
                    if (category != '' && tag != '') {
                        $('.azh-library .azh-elements .azh-element').show();
                        $('.azh-library .azh-elements .azh-element:not([data-path^="' + category + '"])').hide();
                        $('.azh-library .azh-elements .azh-element:not(.' + tag + ')').hide();
                    }
                }
                $('.azh-elements-filters .azh-counter').text($('.azh-elements .azh-element:visible').length + ' ' + azh.i18n.elements);
                $('.azh-elements').isotope({filter: '.azh-element:visible'});
            }
            var filters = $('.azh-elements-filters').detach();
            $('.azh-library-filters').after(filters);

            var categories = $('.azh-elements-filters .azh-categories').off('change').on('change', filters_change);
            var tags_select = $('<select class="azh-tags"></select>').appendTo('.azh-elements-filters').on('change', filters_change);
            var counter = $('<div class="azh-counter">' + $('.azh-elements .azh-element').length + ' ' + azh.i18n.elements + '</div>').appendTo('.azh-elements-filters');
            $('<option selected value="">' + azh.i18n.filter_by_tag + '</option>').appendTo(tags_select);
            Object.keys(azh.tags).sort().forEach(function (tag, i) {
                $('<option value="' + tag + '">' + tag + '</option>').appendTo(tags_select);
            });
            $('.azh-elements').isotope({
                masonry: {
                    gutter: 20
                },
                onLayout: function () {
                }
            });
            $('.azh-elements').imagesLoaded().progress(function () {
                $('.azh-elements').isotope('layout');
            });
            $('[name="sections-elements"]').on('change', function () {
                $('.azh-elements').isotope('layout');
            });
            $('.azh-elements .azh-element').each(function () {
//                $(this).on('mouseenter', function(enter) {
//                    function show_hover(element) {
//                        var hover = $(element).data('hover');
//                        if (!hover) {
//                            hover = $('<div class="azh-hover"><div class="azh-loading"><div class="azh-spinner"></div></div><iframe src="' + azh.site_url + '/?azh=library&files=' + $(element).data('path') + '"></iframe></div>').appendTo('body');
//                            $(element).data('hover', hover);
//                            hovers.push(hover);
//                        }
//                        $(hover).find('iframe').on('load', function() {
//                            $(hover).find('.azh-loading').remove();
//                            var w = $(hover).find('iframe').contents().width() / $(hover).find('iframe').contents().find('[data-element]').width();
//                            var h = $(hover).find('iframe').contents().height() / $(hover).find('iframe').contents().find('[data-element]').height();
//                            var s = (w > h) ? h : w;
//                            $(hover).find('iframe').contents().find('[data-element]').css({
//                                "transform": "scale(" + s * 0.8 + ")",
//                                "transform-origin": "center"
//                            });
//                        });
//                        if ($(element).is(':hover')) {
//                            $(hover).css('visibility', 'visible');
//                            $(hover).css('left', enter.clientX + 'px');
//                            $(hover).css('top', enter.clientY + 'px');
//                            $(element).on('mousemove', function(move) {
//                                $(hover).css('left', move.clientX + 'px');
//                                $(hover).css('top', move.clientY + 'px');
//                            });
//                        } else {
//                            $(hover).css('visibility', 'hidden');
//                        }
//                    }
//                    var element = this;
//                    if ($(element).data('hover')) {
//                        show_hover(element);
//                    } else {
//                        if (!$(element).data('waiting')) {
//                            var waiting = setInterval(function() {
//                                var loading = 0;
//                                $(hovers).each(function() {
//                                    if ($(this).find('.azh-loading').length > 0) {
//                                        loading++;
//                                    }
//                                });
//                                if (loading == 0) {
//                                    clearInterval(waiting);
//                                    $(element).data('waiting', false);
//                                    show_hover(element);
//                                }
//                            }, 100);
//                            $(element).data('waiting', waiting);
//                        }
//                    }
//                });
//                $(this).on('mouseleave', function() {
//                    $(this).off('mousemove');
//                    if ($(this).data('hover')) {
//                        $(this).data('hover').css('visibility', 'hidden');
//                    }
//                });
                $(this).on('click', function () {
                    window.open(azh.site_url + '/?azh=library&files=' + $(this).data('path'), '_blank');
                });
            });
        }
        function get_files() {
            var files = '';
            $('.azh-structure .azh-section').each(function () {
                if (files) {
                    files = files + '|' + $(this).data('path');
                } else {
                    files = $(this).data('path');
                }
            });
            return files;
        }

        if ($('.azh-structure').length) {

            $('.azh-structure').attr('style', '');

            $('.azh-library .azh-element:not(.no-image) h4, .azh-library .azh-section:not(.no-image) h4').remove();

            $('.azh-structure').resizable({
                handles: 's',
            });

            $('<div class="azh-handle"></div>').appendTo('.azh-structure');
            $('.azh-structure').draggable({
                handle: ".azh-handle"
            });


            $('<button>' + azh.i18n.preview + '</button>').appendTo('.azh-structure').on('click', function () {
                window.open(azh.site_url + '/?azh=library&files=' + get_files(), '_blank');
            });

//        if (azh.user_logged_in == '1') {
//            $('<button>' + azh.i18n.customize + '</button>').appendTo('.azh-structure').on('click', function() {
//                window.open(azh.site_url + '/?azh=library&customize=' + get_files(), '_blank');
//            });
//        }


            $('<div class="azh-sections"></div>').appendTo('.azh-structure');
            $('.azh-structure .azh-sections').sortable({
                items: "> .azh-section"
            });




            azh.tags = {};
            var files_tags = {};
            for (var dir in azh.dirs_options) {
                if ('tags' in azh.dirs_options[dir]) {
                    for (var file in azh.dirs_options[dir].tags) {
                        var tags = azh.dirs_options[dir].tags[file].split(',').map(function (tag) {
                            azh.tags[$.trim(tag).toLowerCase()] = true;
                            return $.trim(tag).toLowerCase();
                        });
                        files_tags[dir + '/' + file] = tags;
                    }
                }
            }
            $('.azh-library .azh-sections .azh-section').each(function () {
                var key = $(this).data('dir') + '/' + $(this).data('path');
                if (key in files_tags) {
                    $(this).addClass(files_tags[key].join(' '));
                    $(this).addClass('azh-tag');
                }
            });
            $('.azh-library .azh-elements .azh-element').each(function () {
                var key = $(this).data('dir') + '/' + $(this).data('path');
                if (key in files_tags) {
                    $(this).addClass(files_tags[key].join(' '));
                    $(this).addClass('azh-tag');
                }
            });
            $('.azh-library .azh-sections .azh-section:not(.azh-tag), .azh-library .azh-elements .azh-element:not(.azh-tag)').remove();

            init_sections();
            init_elements();

            $('.azh-library-filters .azh-categories option:not([value=""])').each(function () {
                if ($('.azh-library .azh-sections .azh-section[data-path^="' + $(this).val() + '"]').length) {
                    $(this).addClass('azh-category');
                }
            });
            $('.azh-library-filters .azh-categories option:not([value=""]):not(.azh-category)').remove();
            $('.azh-library-filters .azh-tags option:not([value=""])').each(function () {
                if ($('.azh-library .azh-sections .azh-section.' + $(this).val() + '').length) {
                    $(this).addClass('azh-tag');
                }
            });
            $('.azh-library-filters .azh-tags option:not([value=""]):not(.azh-tag)').remove();


            $('.azh-elements-filters .azh-categories option:not([value=""])').each(function () {
                if ($('.azh-library .azh-elements .azh-element[data-path^="' + $(this).val() + '"]').length) {
                    $(this).addClass('azh-category');
                }
            });
            $('.azh-elements-filters .azh-categories option:not([value=""]):not(.azh-category)').remove();
            $('.azh-elements-filters .azh-tags option:not([value=""])').each(function () {
                if ($('.azh-library .azh-elements .azh-element.' + $(this).val() + '').length) {
                    $(this).addClass('azh-tag');
                }
            });
            $('.azh-elements-filters .azh-tags option:not([value=""]):not(.azh-tag)').remove();
        }

        if ($('.azh-content').length) {
            $(window).on('resize', function () {
                if ($(window).height() < $('.azh-content').height()) {
                    $('.azh-content').removeClass('azh-center');
                } else {
                    $('.azh-content').addClass('azh-center');
                }
            });
            setTimeout(function () {
                $(window).trigger('resize');
            });
            $('#inverted-styles').on('change', function () {
                if ($('.azh-content [data-inverted-styles]').length) {
                    if ($(this).prop('checked')) {
                        $('.azh-content [data-inverted-styles]').attr('data-inverted-styles', 'true');
                    } else {
                        $('.azh-content [data-inverted-styles]').attr('data-inverted-styles', 'false');
                    }
                }
                if ($(this).prop('checked')) {
                    $('.azexo-html-library').attr('data-inverted-styles', 'true');
                } else {
                    $('.azexo-html-library').attr('data-inverted-styles', 'false');
                }
            });
            if ($('.azh-content [data-inverted-styles]').length) {
                if ($('.azh-content [data-inverted-styles]').attr('data-inverted-styles') == 'true') {
                    $('#inverted-styles').prop('checked', true);
                }
            } 
            if ($('.azh-content [data-full-width]:not([data-inverted-styles])').length) {
                $('#inverted-styles').closest('.azh-variation').remove();
            }

            $('#alternative-styles').on('change', function () {
                if ($(this).prop('checked')) {
                    $('.azh-content [data-alternative-styles]').attr('data-alternative-styles', 'true');
                } else {
                    $('.azh-content [data-alternative-styles]').attr('data-alternative-styles', 'false');
                }
            });
            if ($('.azh-content [data-alternative-styles]').length) {
                if ($('.azh-content [data-alternative-styles]').attr('data-alternative-styles') == 'true') {
                    $('#alternative-styles').prop('checked', true);
                }
            } else {
                $('#alternative-styles').closest('.azh-variation').remove();
            }
            
            $('#shadow-border').on('change', function () {
                if ($(this).prop('checked')) {
                    $('.azh-content [data-shadow-border]').attr('data-shadow-border', 'true');
                } else {
                    $('.azh-content [data-shadow-border]').attr('data-shadow-border', 'false');
                }
            });
            if ($('.azh-content [data-shadow-border]').length) {
                if ($('.azh-content [data-shadow-border]').attr('data-shadow-border') == 'true') {
                    $('#shadow-border').prop('checked', true);
                }
            } else {
                $('#shadow-border').closest('.azh-variation').remove();
            }
        }
    });
})(window.jQuery);