(function($) {
    "use strict";
    $(window).on('az-frontend-before-init', function(event, data) {
        function parse_time(text) {
            var d = new Date();
            var time = text.match(/(\d+)(?:[\.:](\d\d))?\s*(p?)/);
            d.setHours(parseInt(time[1]) + (time[3] ? 12 : 0));
            d.setMinutes(parseInt(time[2]) || 0);
            d.setSeconds(0);
            return d;
        }
        var customize = ('azh' in $.QueryString && $.QueryString['azh'] == 'customize');
        var $wrapper = data.wrapper;
        var day_selector = '.azen-table-cell';
        var work_selector = '.azen-cell-wrapper';
        var task_selector = '.azen-cell-content';
        var time_selector = '.azen-content-date';
        if (!customize && $(window).width() > 480) {
            $wrapper.find('.azen-univer-timetable').each(function() {
                var week_min = false;
                var week_max = false;
                var min_height = false;
                var min_interval = false;
                $(this).find(day_selector).each(function() {
                    var day_min = false;
                    var day_max = false;
                    $(this).find(task_selector).each(function() {
                        if($(this).outerHeight() > 0) {
                            if (min_height === false) {
                                min_height = $(this).outerHeight();
                            }
                            if (min_height > $(this).outerHeight()) {
                                min_height = $(this).outerHeight();
                            }
                        }
                        var time = $(this).find(time_selector).text();
                        var from = parse_time($.trim(time.split(/[-–]+/)[0]));
                        var to = parse_time($.trim(time.split(/[-–]+/)[1]));
                        if (min_interval === false) {
                            min_interval = to - from;
                        }
                        if (min_interval > (to - from)) {
                            min_interval = to - from;
                        }
                        if (day_min === false) {
                            day_min = from;
                        }
                        if (day_min > from) {
                            day_min = from;
                        }
                        if (day_max === false) {
                            day_max = to;
                        }
                        if (day_max < to) {
                            day_max = to;
                        }
                    });
                    if (week_min === false) {
                        week_min = day_min;
                    }
                    if (week_min > day_min) {
                        week_min = day_min;
                    }
                    if (week_max === false) {
                        week_max = day_max;
                    }
                    if (week_max < day_max) {
                        week_max = day_max;
                    }
                });
                var r = min_interval / min_height;
                $(this).find(day_selector).each(function() {
                    $(this).find(work_selector).height((week_max - week_min) / r);
                    $(this).find(work_selector).css('position', 'relative');
                    $(this).find(task_selector).each(function() {
                        var time = $(this).find(time_selector).text();
                        var from = parse_time($.trim(time.split(/[-–]+/)[0]));
                        var to = parse_time($.trim(time.split(/[-–]+/)[1]));
                        $(this).css({
                            "left": "0",
                            "right": "0",
                            "top": (from - week_min) / r,
                            "height": (to - from) / r,
                            "position": "absolute"
                        });
                    });
                });
            });
        }
        if (customize) {
            $wrapper.find('.azen-univer-people-say-flex .azen-univer-say-slider .azen-univer-slide-wrapper').css('pointer-events', 'all');
        }
    });
})(jQuery);