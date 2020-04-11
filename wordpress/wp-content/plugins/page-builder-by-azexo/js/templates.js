(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    window.azh = $.extend({}, window.azh);
    window.azt = $.extend({}, window.azt);
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
    $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
    var customize = ('azh' in $.QueryString && $.QueryString['azh'] == 'customize');
    azt.refresh_image_maps = function ($wrapper) {
        $wrapper.find('.az-image-map').each(function () {
            var $map = $(this);
            if ($map.find('[data-element].azt-exists .az-polygone[data-id]').length) {
                $map.find('[data-element].azt-exists .az-polygone[data-id].az-active').removeClass('az-active');
                $map.find('[data-element].az-exists .az-polygone[data-id] svg polygon').first().trigger('mouseenter').closest('.az-svg').addClass('az-active').removeClass('az-hover');
            }
        });
    };
    var load_script_waiting_callbacks = {};
    var scripts_loaded = {};
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
    var dateFormat = (function () {
        var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZWN]|"[^"]*"|'[^']*'/g;
        var timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g;
        var timezoneClip = /[^-+\dA-Z]/g;

        // Regexes and supporting functions are cached through closure
        return function (date, mask, utc, gmt) {

            // You can't provide utc if you skip other args (use the 'UTC:' mask prefix)
            if (arguments.length === 1 && kindOf(date) === 'string' && !/\d/.test(date)) {
                mask = date;
                date = undefined;
            }

            date = date || new Date;

            if (!(date instanceof Date)) {
                date = new Date(date);
            }

            if (isNaN(date)) {
                throw TypeError('Invalid date');
            }

            mask = String(dateFormat.masks[mask] || mask || dateFormat.masks['default']);

            // Allow setting the utc/gmt argument via the mask
            var maskSlice = mask.slice(0, 4);
            if (maskSlice === 'UTC:' || maskSlice === 'GMT:') {
                mask = mask.slice(4);
                utc = true;
                if (maskSlice === 'GMT:') {
                    gmt = true;
                }
            }

            var _ = utc ? 'getUTC' : 'get';
            var d = date[_ + 'Date']();
            var D = date[_ + 'Day']();
            var m = date[_ + 'Month']();
            var y = date[_ + 'FullYear']();
            var H = date[_ + 'Hours']();
            var M = date[_ + 'Minutes']();
            var s = date[_ + 'Seconds']();
            var L = date[_ + 'Milliseconds']();
            var o = utc ? 0 : date.getTimezoneOffset();
            var W = getWeek(date);
            var N = getDayOfWeek(date);
            var flags = {
                d: d,
                dd: pad(d),
                ddd: dateFormat.i18n.dayNames[D],
                dddd: dateFormat.i18n.dayNames[D + 7],
                m: m + 1,
                mm: pad(m + 1),
                mmm: dateFormat.i18n.monthNames[m],
                mmmm: dateFormat.i18n.monthNames[m + 12],
                yy: String(y).slice(2),
                yyyy: y,
                h: H % 12 || 12,
                hh: pad(H % 12 || 12),
                H: H,
                HH: pad(H),
                M: M,
                MM: pad(M),
                s: s,
                ss: pad(s),
                l: pad(L, 3),
                L: pad(Math.round(L / 10)),
                t: H < 12 ? dateFormat.i18n.timeNames[0] : dateFormat.i18n.timeNames[1],
                tt: H < 12 ? dateFormat.i18n.timeNames[2] : dateFormat.i18n.timeNames[3],
                T: H < 12 ? dateFormat.i18n.timeNames[4] : dateFormat.i18n.timeNames[5],
                TT: H < 12 ? dateFormat.i18n.timeNames[6] : dateFormat.i18n.timeNames[7],
                Z: gmt ? 'GMT' : utc ? 'UTC' : (String(date).match(timezone) || ['']).pop().replace(timezoneClip, ''),
                o: (o > 0 ? '-' : '+') + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                S: ['th', 'st', 'nd', 'rd'][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10],
                W: W,
                N: N
            };

            return mask.replace(token, function (match) {
                if (match in flags) {
                    return flags[match];
                }
                return match.slice(1, match.length - 1);
            });
        };
    })();
    dateFormat.masks = {
        'default': 'ddd mmm dd yyyy HH:MM:ss',
        'shortDate': 'm/d/yy',
        'mediumDate': 'mmm d, yyyy',
        'longDate': 'mmmm d, yyyy',
        'fullDate': 'dddd, mmmm d, yyyy',
        'shortTime': 'h:MM TT',
        'mediumTime': 'h:MM:ss TT',
        'longTime': 'h:MM:ss TT Z',
        'isoDate': 'yyyy-mm-dd',
        'isoTime': 'HH:MM:ss',
        'isoDateTime': 'yyyy-mm-dd\'T\'HH:MM:sso',
        'isoUtcDateTime': 'UTC:yyyy-mm-dd\'T\'HH:MM:ss\'Z\'',
        'expiresHeaderFormat': 'ddd, dd mmm yyyy HH:MM:ss Z'
    };
    dateFormat.i18n = {
        dayNames: [
            'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat',
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        ],
        monthNames: [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
        ],
        timeNames: [
            'a', 'p', 'am', 'pm', 'A', 'P', 'AM', 'PM'
        ]
    };
    function pad(val, len) {
        val = String(val);
        len = len || 2;
        while (val.length < len) {
            val = '0' + val;
        }
        return val;
    }
    function getWeek(date) {
        // Remove time components of date
        var targetThursday = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        // Change date to Thursday same week
        targetThursday.setDate(targetThursday.getDate() - ((targetThursday.getDay() + 6) % 7) + 3);

        // Take January 4th as it is always in week 1 (see ISO 8601)
        var firstThursday = new Date(targetThursday.getFullYear(), 0, 4);

        // Change date to Thursday same week
        firstThursday.setDate(firstThursday.getDate() - ((firstThursday.getDay() + 6) % 7) + 3);

        // Check if daylight-saving-time-switch occurred and correct for it
        var ds = targetThursday.getTimezoneOffset() - firstThursday.getTimezoneOffset();
        targetThursday.setHours(targetThursday.getHours() - ds);

        // Number of weeks between target Thursday and first Thursday
        var weekDiff = (targetThursday - firstThursday) / (86400000 * 7);
        return 1 + Math.floor(weekDiff);
    }
    function getDayOfWeek(date) {
        var dow = date.getDay();
        if (dow === 0) {
            dow = 7;
        }
        return dow;
    }
    function kindOf(val) {
        if (val === null) {
            return 'null';
        }

        if (val === undefined) {
            return 'undefined';
        }

        if (typeof val !== 'object') {
            return typeof val;
        }

        if (Array.isArray(val)) {
            return 'array';
        }

        return {}.toString.call(val)
                .slice(8, -1).toLowerCase();
    }
    function makeid(length) {
        var text = "id";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    }
    function reset_template($template) {
        $template.find('.azt-filled').each(function () {
            var $this = $(this);
            if ($this.is('.azt-exists')) {
                $this.removeClass('az-exists');
            }
            if ($this.is('.azt-hide-if-empty')) {
                $this.closest('[data-element]').css('display', 'block');
            }
            if ($this.is('.azt-table')) {
                var $tbody = $this.find('tbody');
                while ($tbody.children().length > 1) {
                    $tbody.children().last().remove();
                }
                $tbody.find('[data-azt-key][data-azt-value]').each(function () {
                    $(this).attr('data-azt-value', '');
                });
            }
            if ($this.is('.azt-list')) {
                while ($this.children(':not(style)').length > 1) {
                    $this.children(':not(style)').last().remove();
                }
                $this.find('[data-azt-key][data-azt-value]').each(function () {
                    $(this).attr('data-azt-value', '');
                });
            }
            if ($this.is('.azt-map-wrapper')) {
                var $map = $this.find('.azt-map');
                $map.empty();
                $map.data('map', false);
            }

            if ($this.is('[data-working_hours="open_closed"]')) {
                $this.removeClass('azt-closed');
                $this.removeClass('azt-open');
            }
            if ($this.is('[data-working_hours="full_table"]')) {
                $this.children().each(function () {
                    $(this).removeClass('azt-open azt-closed');
                    $(this).find('.azt-periods').text('');
                });
            }
//            $this.contents().filter(function () {
//                return this.nodeType === 3;
//            }).each(function () {
//                if ($this.data('azt-original-text')) {
//                    this.textContent = $this.data('azt-original-text');
//                }
//            });
            if ($this.data('azt-original-text')) {
                $this.text($this.data('azt-original-text'));
            }
            $this.removeClass('azt-filled');
        });
    }
    function field_sum(rows, field) {
        var sum = 0;
        $(rows).each(function () {
            if (this[field]) {
                sum = sum + parseFloat(this[field]);
            }
        });
        return sum;
    }
    function field_max(rows, field) {
        var max = 0;
        if (rows[0][field]) {
            max = parseFloat(rows[0][field]);
        }
        $(rows).each(function () {
            if (this[field]) {
                if (max < parseFloat(this[field])) {
                    max = parseFloat(this[field]);
                }
            }
        });
        return max;
    }
    function field_min(rows, field) {
        var min = 0;
        if (rows[0][field]) {
            min = parseFloat(rows[0][field]);
        }
        $(rows).each(function () {
            if (this[field]) {
                if (min > parseFloat(this[field])) {
                    min = parseFloat(this[field]);
                }
            }
        });
        return min;
    }
    function field_value(rows, field) {
        var value = false;
        if (azt.fields[field]) {
            if (!('type' in azt.fields[field]) || ['text', 'number'].indexOf(azt.fields[field].type) >= 0) {
                if (rows.length) {
                    if (rows[0][field]) {
                        value = rows[0][field];
                    }
                    $(rows).each(function () {
                        if (value != this[field]) {
                            value = false;
                            return false;
                        }
                    });
                }
            }
        }
        return value;
    }
    function replace_text($wrapper, from, to) {
        $wrapper.contents().filter(function () {
            return this.nodeType === 3;
        }).each(function () {
            var $this = $(this);
            if (!$wrapper.data('azt-original-text')) {
                $wrapper.data('azt-original-text', this.textContent);
            }
            this.textContent = this.textContent.replace(from, to);
        });
    }
    function replace_field($wrapper, field, value) {
        switch (azt.fields[field].type) {
            case 'datetime':
                var regexp = new RegExp('azt-' + field + '\\((.*)\\)');
                var match = regexp.exec($wrapper.text());
                if (match) {
                    replace_text($wrapper, $wrapper.text(), dateFormat(new Date(value), match[1]));
                }
                break;
            case 'event':
                var event = false;
                try {
                    event = JSON.parse(value);
                } catch (e) {
                }
                if (event) {
                    var regexp = new RegExp('azt-' + field + '\\((.*)\\)');
                    var match = regexp.exec($wrapper.text());
                    if (match) {
                        var format = match[1];
                        if (event.a == '1') {
                            format = format.replace(/[hHMs].+[hHMs]/, '');
                            format = format.replace(/[^\w]+$/g, '');
                            format = format.replace(/^[^\w]+/g, '');
                        }
                        replace_text($wrapper, $wrapper.text(), dateFormat(new Date(event.d + ('t' in event ? event.t : '+0000')), format));
                    }
                }
                break;
            default:
                replace_text($wrapper, 'azt-' + field, value);
        }
    }
    function auto_field(field, $element) {
        if ($element.attr('data-html-switcher')) {
            var text = $.trim($element.text());
            switch (azt.fields[field].type) {
                case 'url':
                    $element.empty().append('<a href="' + text + '">' + text + '</a>').children().on('click', function (e) {e.stopPropagation()});
                    break;
                case 'email':
                    $element.empty().append('<a href="mailto:' + text + '">' + text + '</a>').children().on('click', function (e) {e.stopPropagation()});
                    break;
                case 'phone':
                    $element.empty().append('<a href="tel:' + text + '">' + text + '</a>').children().on('click', function (e) {e.stopPropagation()});
                    break;
                default:
            }
        }        
    }
    function to_utc(date) {
        return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
    }
    function is_open(working_hours, row) {
        var now = new Date();
        var day = now.getDay();

        var timezone_difference = 0;
        for (var field in azt.fields) {
            if (azt.fields[field].type === 'timezone') {
                if (row[field]) {
                    timezone_difference = parseFloat(row[field]) * 60 - now.getTimezoneOffset();
                    break;
                }
            }
        }

        var open = false;
        day = working_hours[day];
        if (day.p.length) {
            day.p.forEach(function (p) {
                var from = new Date(now.getTime());
                from.setHours(p.f.split(":")[0]);
                from.setMinutes(p.f.split(":")[1]);

                var to = new Date(now.getTime());
                to.setHours(p.t.split(":")[0]);
                to.setMinutes(p.t.split(":")[1]);

                if ((from.getTime() + timezone_difference * 60 * 1000) < now.getTime() && (to.getTime() + timezone_difference * 60 * 1000) > now.getTime()) {
                    open = true;
                    return false;
                }
            });
        } else {
            open = day.o;
        }
        return open;
    }
    function fill_template($template, current_fields) {
        $template.find('.azt-table:not(.azt-filled)').each(function () {
            var $table = $(this);
            $table.addClass('azt-filled');
            var $tbody = $table.find('tbody');
            if ($tbody.children().length) {
                var $row = $tbody.children().first();
                $row.detach();
                if (azt.current_rows.length > 0) {
                    $tbody.show();
                    $(azt.current_rows).each(function () {
                        var current_row = this;
                        var $new_row = $row.clone(true);
                        azt.current_row = current_row;
                        fill_template($new_row, current_row);
                        azt.current_row = false;
                        $tbody.append($new_row);
                    });
                } else {
                    $tbody.append($row);
                    $tbody.hide();
                }
            }
            azt.click_urls = false;
        });
        $template.find('.azt-list:not(.azt-filled)').each(function () {
            function get_scrollable_parent($node) {
                if ($node && !$node.is('body')) {
                    if ($node.css('overflow-y') === 'scroll' || $node.css('overflow-y') === 'auto') {
                        return $node;
                    } else {
                        if ($node.parent()) {
                            return get_scrollable_parent($node.parent());
                        }
                    }
                }
                return false;
            }
            var batch_length = 10;
            var $list = $(this);
            $list.addClass('azt-filled');
            if ($list.children(':not(style)').length) {
                var $item = $list.children(':not(style)').first();
                if ($list.is('.az-accordion-element')) {
                    $item.removeClass('az-active').find('> div:last-child').slideUp(0).hide();
                }
                $item.detach();
                if (azt.current_rows.length > 0) {
                    $list.show();
                    if (azt.current_rows.length > batch_length) {
                        setTimeout(function () {
                            var $scrollable_parent = get_scrollable_parent($list);
                            if (!$scrollable_parent) {
                                $scrollable_parent = $list;
                            }
                            $scrollable_parent.off('scroll.azt-list').on('scroll.azt-list', function (e) {
                                if (e.target.scrollHeight - e.target.scrollTop === e.target.clientHeight) {
                                    var l = $list.children(':not(style)').length;
                                    for (var i = l; i < l + batch_length && i < azt.current_rows.length; i++) {
                                        var current_row = azt.current_rows[i];
                                        var $new_item = $item.clone(true);
                                        azt.current_row = current_row;
                                        fill_template($new_item, current_row);
                                        azt.current_row = false;
                                        $new_item.data('azt-row', current_row);
                                        $list.append($new_item);
                                    }
                                    setTimeout(function () {
                                        $list.trigger('azt-refresh').trigger('azh-refresh');
                                    });
                                    if ($list.children(':not(style)').length < azt.current_rows.length) {
                                        if (e.target.scrollHeight - e.target.scrollTop === e.target.clientHeight) {
                                            $scrollable_parent.trigger('scroll.azt-list');
                                        }
                                    }
                                }
                            });
                            $scrollable_parent.trigger('scroll.azt-list');
                        });
                    } else {
                        $(azt.current_rows).each(function () {
                            var current_row = this;
                            var $new_item = $item.clone(true);
                            azt.current_row = current_row;
                            fill_template($new_item, current_row);
                            azt.current_row = false;
                            $new_item.data('azt-row', current_row);
                            $list.append($new_item);
                        });
                        setTimeout(function () {
                            $list.trigger('azt-refresh').trigger('azh-refresh');
                            if ($list.is('.az-accordion-element') && $list.children(':not(style)').length === 1) {
                                $list.children(':not(style)').addClass('az-active');
                                $list.children(':not(style)').find('> div:last-child').slideDown(0);
                            }
                        });
                    }
                } else {
                    $list.append($item);
                    $list.hide();
                }
            }
            azt.click_urls = false;
        });
        $template.find('.azt-map-wrapper:not(.azt-filled)').each(function () {
            if (azt.current_row) {
                var $map_wrapper = $(this);
                fill_gmap($map_wrapper, [azt.current_row]);
                $map_wrapper.addClass('azt-filled');
                setTimeout(function () {
                    $map_wrapper.closest('[data-element]').show();
                });
            } else {
                if (azt.current_rows) {
                    var $map_wrapper = $(this);
                    fill_gmap($map_wrapper, azt.current_rows);
                    $map_wrapper.addClass('azt-filled');
                    setTimeout(function () {
                        $map_wrapper.closest('[data-element]').show();
                    });
                }
            }
        });
        if (azt.current_row) {
            $template.find('[data-azt-key]:not([data-azt-key=""])[data-azt-value=""]').each(function () {
                $(this).attr('data-azt-value', azt.current_row[$(this).attr('data-azt-key')]);
            });
            $template.find('[data-azt-key].azt-hide-if-empty:not(.azt-filled)').each(function () {
                var $this = $(this);
                if (!azt.current_row[$this.attr('data-azt-key')] || azt.current_row[$this.attr('data-azt-key')] === '0') {
                    $this.closest('[data-element]').css('display', 'none');
                }
                $this.addClass('azt-filled');
            });
            $template.find('a[data-facebook-share]').each(function () {
                var $this = $(this);
                if ($this.attr('data-facebook-share') in azt.current_row) {
                    var url = new URL(window.location.href);
                    url.searchParams.set($this.attr('data-facebook-share'), azt.current_row[$this.attr('data-facebook-share')]);
                    var share_url = new URL('https://www.facebook.com/sharer.php');
                    share_url.searchParams.set('u', url.toString());
                    $this.attr('href', share_url.toString());
                }
            });
            $template.find('a[data-twitter-share]').each(function () {
                var $this = $(this);
                if ($this.attr('data-twitter-share') in azt.current_row) {
                    var url = new URL(window.location.href);
                    url.searchParams.set($this.attr('data-twitter-share'), azt.current_row[$this.attr('data-twitter-share')]);
                    var share_url = new URL('http://twitter.com/share');
                    share_url.searchParams.set('url', url.toString());
                    $this.attr('href', share_url.toString());
                }
            });
            $template.find('a[data-url-share]').each(function () {
                var $this = $(this);
                if ($this.attr('data-url-share') in azt.current_row) {
                    var url = new URL(window.location.href);
                    url.searchParams.set($this.attr('data-url-share'), azt.current_row[$this.attr('data-url-share')]);
                    $this.attr('href', url.toString());
                }
            });
            $template.find('a.azt-ics-file').each(function () {
                var $this = $(this);
                var text = [];
                var filename = 'calendar.ics';
                text.push('BEGIN:VCALENDAR');
                text.push('VERSION:2.0');
                text.push('PRODID:-//azexo.com NONSGML v1.0//EN');
                text.push('BEGIN:VEVENT');
                text.push('UID:' + makeid(10));
                if ($this.attr('data-dtstamp') && $this.attr('data-dtstamp') in azt.current_row) {
                    switch (azt.fields[$this.attr('data-dtstamp')].type) {
                        case 'event':
                            var event = azt.current_row[$this.attr('data-dtstamp')];
                            try {
                                event = JSON.parse(event);
                            } catch (e) {
                            }
                            if (event) {
                                text.push('DTSTAMP:' + dateFormat(to_utc(new Date(event.d + ('t' in event ? event.t : '+0000'))), "yyyymmdd'T'HHMMss'Z'"));
                            }
                            break;
                        default:
                            text.push('DTSTAMP:' + dateFormat(azt.current_row[$this.attr('data-dtstamp')], "yyyymmdd'T'HHMMss"));
                    }
                }
                if ($this.attr('data-dtstart') && $this.attr('data-dtstart') in azt.current_row) {
                    switch (azt.fields[$this.attr('data-dtstart')].type) {
                        case 'event':
                            var event = azt.current_row[$this.attr('data-dtstart')];
                            try {
                                event = JSON.parse(event);
                            } catch (e) {
                            }
                            if (event) {
                                text.push('DTSTART:' + dateFormat(to_utc(new Date(event.d + ('t' in event ? event.t : '+0000'))), "yyyymmdd'T'HHMMss'Z'"));
                            }
                            break;
                        default:
                            text.push('DTSTART:' + dateFormat(azt.current_row[$this.attr('data-dtstart')], "yyyymmdd'T'HHMMss'Z'"));
                    }
                }
                if ($this.attr('data-dtend') && $this.attr('data-dtend') in azt.current_row) {
                    switch (azt.fields[$this.attr('data-dtend')].type) {
                        case 'event':
                            var event = azt.current_row[$this.attr('data-dtend')];
                            try {
                                event = JSON.parse(event);
                            } catch (e) {
                            }
                            if (event) {
                                text.push('DTEND:' + dateFormat(to_utc(new Date(event.d + ('t' in event ? event.t : '+0000'))), "yyyymmdd'T'HHMMss'Z'"));
                            }
                            break;
                        default:
                            text.push('DTEND:' + dateFormat(azt.current_row[$this.attr('data-dtend')], "yyyymmdd'T'HHMMss'Z'"));
                    }
                }
                if ($this.attr('data-location') && $this.attr('data-location') in azt.current_row) {
                    text.push('LOCATION:' + azt.current_row[$this.attr('data-location')]);
                }
                if ($this.attr('data-summary') && $this.attr('data-summary') in azt.current_row) {
                    text.push('SUMMARY:' + azt.current_row[$this.attr('data-summary')]);
                }
                if ($this.attr('data-description') && $this.attr('data-description') in azt.current_row) {
                    text.push('DESCRIPTION:' + azt.current_row[$this.attr('data-description')]);
                }
                text.push('END:VEVENT');
                text.push('END:VCALENDAR');
                $this.attr('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text.join("\n")));
                $this.attr('download', filename);
            });
            $template.find('a.azt-googlecal').each(function () {
                var $this = $(this);
                var url = new URL('https://www.google.com/calendar/event');
                url.searchParams.set('action', 'TEMPLATE');
                if ($this.attr('data-text') && $this.attr('data-text') in azt.current_row) {
                    url.searchParams.set('text', azt.current_row[$this.attr('data-text')]);
                }
                if ($this.attr('data-details') && $this.attr('data-details') in azt.current_row) {
                    url.searchParams.set('details', azt.current_row[$this.attr('data-details')]);
                }
                if ($this.attr('data-location') && $this.attr('data-location') in azt.current_row) {
                    url.searchParams.set('location', azt.current_row[$this.attr('data-location')]);
                }
                if ($this.attr('data-dates1') && $this.attr('data-dates1') in azt.current_row && $this.attr('data-dates2') && $this.attr('data-dates2') in azt.current_row) {
                    var dates1 = '';
                    switch (azt.fields[$this.attr('data-dates1')].type) {
                        case 'event':
                            var event = azt.current_row[$this.attr('data-dates1')];
                            try {
                                event = JSON.parse(event);
                            } catch (e) {
                            }
                            if (event) {
                                dates1 = dateFormat(to_utc(new Date(event.d + ('t' in event ? event.t : '+0000'))), "yyyymmdd'T'HHMMss'Z'");
                            }
                            break;
                        default:
                            dates1 = dateFormat(azt.current_row[$this.attr('data-dates1')], "yyyymmdd'T'HHMMss'Z'");
                    }
                    var dates2 = '';
                    switch (azt.fields[$this.attr('data-dates2')].type) {
                        case 'event':
                            var event = azt.current_row[$this.attr('data-dates2')];
                            try {
                                event = JSON.parse(event);
                            } catch (e) {
                            }
                            if (event) {
                                dates2 = dateFormat(to_utc(new Date(event.d + ('t' in event ? event.t : '+0000'))), "yyyymmdd'T'HHMMss'Z'");
                            }
                            break;
                        default:
                            dates2 = dateFormat(azt.current_row[$this.attr('data-dates2')], "yyyymmddTHHMMss'Z'");
                    }
                    url.searchParams.set('dates', dates1 + '/' + dates2);
                }
                $this.attr('href', url.toString());
            });
        }
        for (var field in azt.fields) {
            var unique_value = field_value(azt.current_rows, field);
            $template.find('.azt-' + field + ':not(.azt-filled)').each(function () {
                var $this = $(this);
                switch (azt.fields[field].type) {
                    case 'working_hours':
                        if (azt.current_row && azt.current_row[field]) {
                            var working_hours = false;
                            try {
                                working_hours = JSON.parse(azt.current_row[field]);
                            } catch (e) {
                            }
                            if (working_hours) {
                                $this.removeClass('azt-closed');
                                $this.removeClass('azt-open');
                                if (is_open(working_hours, azt.current_row)) {
                                    $this.addClass('azt-open');
                                } else {
                                    $this.addClass('azt-closed');
                                }
                            }
                        }
                        break;
                    default:
                        if (azt.current_row && azt.current_row[field]) {
                            $this.addClass(azt.current_row[field]);
                        } else {
                            if (unique_value) {
                                $this.addClass(unique_value);
                            }
                        }
                }
                $this.addClass('azt-filled');
            });
            if (field !== 'id') {
                $template.find('[data-' + field + ']:not(.azt-filled)').each(function () {
                    var $this = $(this);
                    switch (azt.fields[field].type) {
                        case 'working_hours':
                            if (azt.current_row && azt.current_row[field]) {
                                var working_hours = false;
                                try {
                                    working_hours = JSON.parse(azt.current_row[field]);
                                } catch (e) {
                                }
                                if (working_hours) {
                                    switch ($this.attr('data-' + field)) {
                                        case 'open_closed':
                                            $this.removeClass('azt-closed');
                                            $this.removeClass('azt-open');
                                            if (is_open(working_hours, azt.current_row)) {
                                                $this.addClass('azt-open');
                                            } else {
                                                $this.addClass('azt-closed');
                                            }
                                            break;
                                        case 'full_table':
                                            $this.children().each(function (index) {
                                                var $day = $(this);
                                                $day.removeClass('azt-open');
                                                $day.removeClass('azt-closed');
                                                $day.find('.azt-periods').text('');
                                                var day = working_hours[index];
                                                if (day.p.length) {
                                                    var periods = [];
                                                    day.p.forEach(function (p) {
                                                        periods.push((('f' in p) ? p.f : '') + '-' + (('t' in p) ? p.t : ''));
                                                    });
                                                    periods = periods.join(', ');
                                                    $day.find('.azt-periods').text(periods);
                                                } else {
                                                    if (day.o) {
                                                        $day.addClass('azt-open');
                                                    } else {
                                                        $day.addClass('azt-closed');
                                                    }
                                                }
                                            });
                                            break;
                                    }
                                }
                            }
                            break;
                        default:
                            if (azt.current_row) {
                                $this.attr('data-' + field, azt.current_row[field]);
                                $this.closest('[data-element]').css('display', 'block');
                            } else {
                                if (unique_value) {
                                    $this.attr('data-' + field, unique_value);
                                    $this.closest('[data-element]').css('display', 'block');
                                } else {
                                    $this.closest('[data-element]').css('display', 'none');
                                }
                            }
                    }
                    $this.addClass('azt-filled');
                });
            }
            $template.find('[name="' + field + '"][type="hidden"]:not(.azt-filled)').each(function () {
                var $this = $(this);
                if (azt.current_row) {
                    $this.val(azt.current_row[field]);
                } else {
                    if (unique_value) {
                        $this.val(unique_value);
                    }
                }
                $this.addClass('azt-filled');
            });
            $template.find(':contains("azt-' + field + '"):not(.azt-filled)').each(function () {
                var $this = $(this);
                if ($this.children().length === 0) {
                    if (azt.current_row) {
                        replace_field($this, field, field in azt.current_row ? azt.current_row[field] : '');
                        $this.closest('[data-element]').css('display', 'block');
                        auto_field(field, $this);
                    } else {
                        if (unique_value) {
                            replace_field($this, field, unique_value);
                            $this.closest('[data-element]').css('display', 'block');
                            auto_field(field, $this);
                        } else {
                            $this.closest('[data-element]').css('display', 'none');
                        }
                    }
                    $this.addClass('azt-filled');
                }
            });
            $template.find('[data-azt-background-image-key]:not([data-azt-background-image-key=""]):not(.azt-filled)').each(function () {
                var $this = $(this);
                if (azt.current_row) {
                    $this.css('background-image', 'url("' + azt.current_row[$this.attr('data-azt-background-image-key')] + '")');
                    $this.closest('[data-element]').css('display', 'block');
                } else {
                    var value = field_value(azt.current_rows, $this.attr('data-azt-background-image-key'));
                    if (value) {
                        $this.css('background-image', 'url("' + value + '")');
                        $this.closest('[data-element]').css('display', 'block');
                    } else {
                        $this.closest('[data-element]').css('display', 'none');
                    }
                }
                $this.addClass('azt-filled');
            });
            $template.find('[data-azt-src-key]:not([data-azt-src-key=""]):not(.azt-filled)').each(function () {
                var $this = $(this);
                if (azt.current_row) {
                    $this.attr('src', azt.current_row[$this.attr('data-azt-src-key')]);
                    $this.closest('[data-element]').css('display', 'block');
                } else {
                    var value = field_value(azt.current_rows, $this.attr('data-azt-src-key'));
                    if (value) {
                        $this.attr('src', value);
                        $this.closest('[data-element]').css('display', 'block');
                    } else {
                        $this.closest('[data-element]').css('display', 'none');
                    }
                }
                $this.addClass('azt-filled');
            });
            $template.find('[data-azt-url-key]:not([data-azt-url-key=""]):not(.azt-filled)').each(function () {
                var $this = $(this);
                if ($this.is('[href]')) {
                    if (azt.current_row) {
                        $this.attr('href', azt.current_row[$this.attr('data-azt-url-key')]);
                        $this.closest('[data-element]').css('display', 'block');
                    } else {
                        var value = field_value(azt.current_rows, $this.attr('data-azt-url-key'));
                        if (value) {
                            $this.attr('href', value);
                            $this.closest('[data-element]').css('display', 'block');
                        } else {
                            $this.closest('[data-element]').css('display', 'none');
                        }
                    }
                }
                if ($this.is('[data-click-url]')) {
                    if (azt.current_row) {
                        $this.attr('data-click-url', azt.current_row[$this.attr('data-azt-url-key')]);
                        $this.closest('[data-element]').css('display', 'block');
                    } else {
                        var value = field_value(azt.current_rows, $this.attr('data-azt-url-key'));
                        if (value) {
                            $this.attr('data-click-url', value);
                            $this.closest('[data-element]').css('display', 'block');
                        } else {
                            $this.closest('[data-element]').css('display', 'none');
                        }
                    }
                }
                $this.addClass('azt-filled');
            });
        }
        if (azt.current_rows.length > 0) {
            for (var field in current_fields) {
                if (typeof current_fields[field] === 'string') {
                    $template.find('.azt-' + field + ':not(.azt-filled)').each(function () {
                        var $this = $(this);
                        $this.addClass(current_fields[field]);
                        $this.addClass('azt-filled');
                    });
                    $template.find('[' + field + ']:not(.azt-filled)').each(function () {
                        var $this = $(this);
                        $this.attr(field, current_fields[field]);
                        $this.addClass('azt-filled');
                    });
                    $template.find(':contains("azt-' + field + '"):not(.azt-filled)').each(function () {
                        var $this = $(this);
                        if ($this.children().length === 0) {
                            replace_field($this, field, current_fields[field]);
                            $this.addClass('azt-filled');
                        }
                    });
                }
            }
        }
        $template.find('.azt-exists:not(.azt-filled)').addBack().filter('.azt-exists:not(.azt-filled)').each(function () {
            var $this = $(this);
            if (azt.current_rows.length > 0) {
                $this.addClass('az-exists');
            }
            $this.addClass('azt-filled');
        });
        $template.find(':contains("azt-count"):not(.azt-filled)').each(function () {
            var $this = $(this);
            if ($this.children().length === 0) {
                replace_text($this, 'azt-count', azt.current_rows.length);
                $this.addClass('azt-filled');
            }
        });
        for (var field in azt.fields) {
            $template.find(':contains("azt-sum-' + field + '"):not(.azt-filled)').each(function () {
                var $this = $(this);
                if ($this.children().length === 0) {
                    replace_text($this, 'azt-sum-' + field, field_sum(azt.current_rows, field));
                    $this.addClass('azt-filled');
                }
            });
            $template.find(':contains("azt-max-' + field + '"):not(.azt-filled)').each(function () {
                var $this = $(this);
                if ($this.children().length === 0) {
                    replace_text($this, 'azt-max-' + field, field_max(azt.current_rows, field));
                    $this.addClass('azt-filled');
                }
            });
            $template.find(':contains("azt-min-' + field + '"):not(.azt-filled)').each(function () {
                var $this = $(this);
                if ($this.children().length === 0) {
                    replace_text($this, 'azt-min-' + field, field_min(azt.current_rows, field));
                    $this.addClass('azt-filled');
                }
            });
        }
    }
    function select_rows(current_fields) {
        azt.current_row = false;
        azt.current_rows = [];
        var current_rows = azt.table;
        for (var field in current_fields) {
            var rows = [];
            $(current_rows).each(function () {
                if (field in this) {
                    if (typeof current_fields[field] === 'object') {
                        if ($.isArray(current_fields[field])) {
                            switch (azt.fields[field].type) {
                                case 'working_hours':
                                    if (current_fields[field].indexOf('open_now') >= 0) {
                                        var working_hours = false;
                                        try {
                                            working_hours = JSON.parse(this[field]);
                                        } catch (e) {
                                        }
                                        if (working_hours) {
                                            if (is_open(working_hours, this)) {
                                                rows.push(this);
                                            }
                                        }
                                    }
                                    break;
                                default:
                                    if (current_fields[field].indexOf(this[field]) >= 0) {
                                        rows.push(this);
                                    }
                            }
                        } else {
                            if ('from' in current_fields[field] && 'to' in current_fields[field]) {
                                if (parseFloat(this[field]) >= parseFloat(current_fields[field]['from']) && parseFloat(this[field]) <= parseFloat(current_fields[field]['to'])) {
                                    rows.push(this);
                                }
                            } else {
                                if ('from' in current_fields[field]) {
                                    if (parseFloat(this[field]) >= parseFloat(current_fields[field]['from'])) {
                                        rows.push(this);
                                    }
                                }
                                if ('to' in current_fields[field]) {
                                    if (parseFloat(this[field]) <= parseFloat(current_fields[field]['to'])) {
                                        rows.push(this);
                                    }
                                }
                            }
                        }
                    } else {
                        switch (azt.fields[field].type) {
                            case 'working_hours':
                                if (current_fields[field] === 'open_now') {
                                    var working_hours = false;
                                    try {
                                        working_hours = JSON.parse(this[field]);
                                    } catch (e) {
                                    }
                                    if (working_hours) {
                                        if (is_open(working_hours, this)) {
                                            rows.push(this);
                                        }
                                    }
                                }
                                break;
                            case 'datetime':
                                if (current_fields[field] === '' || this[field].indexOf(current_fields[field]) === 0) {
                                    rows.push(this);
                                }
                                break;
                            case 'event':
                                var event = false;
                                try {
                                    event = JSON.parse(this[field]);
                                } catch (e) {
                                }
                                if (event) {
                                    if (current_fields[field] === '') {
                                        rows.push(this);
                                    } else {
                                        if (current_fields[field].match(/\d\d\d\d-\d\d/)) {
                                            var date1 = new Date(event.d + ('t' in event ? event.t : '+0000'));
                                            var date1b = new Date(date1.getFullYear(), date1.getMonth(), 1);
                                            var date2 = new Date(current_fields[field]);
                                            var date2b = new Date(date2.getFullYear(), date2.getMonth(), 1);
                                            var date2e = new Date(date2.getFullYear(), date2.getMonth() + 1, 0);
                                            var n = parseInt(event.n, 10);
                                            if (date1b <= date2b) {
                                                switch (event.p) {
                                                    case 'n':
                                                        if (event.d.indexOf(current_fields[field]) === 0) {
                                                            var row = $.extend({}, this);
                                                            rows.push(row);
                                                        }
                                                        break;
                                                    case 'd':
                                                        if (n > 0) {
                                                            var days_b = (date2b.getTime() - date1.getTime()) / 1000;
                                                            days_b /= (60 * 60 * 24);
                                                            var days_e = (date2e.getTime() - date1.getTime()) / 1000;
                                                            days_e /= (60 * 60 * 24);
                                                            var b = Math.floor(days_b / n) + 1;
                                                            while (b <= Math.floor(days_e / n)) {
                                                                if (b >= 0) {
                                                                    var row = $.extend({}, this);
                                                                    var new_event = $.extend({}, event);
                                                                    var new_date = new Date(event.d + ('t' in event ? event.t : '+0000'));
                                                                    new_date = new Date(new_date.getTime() + 60 * 60 * 24 * 1000 * n * b);
                                                                    new_event.d = dateFormat(new_date, 'yyyy-mm-dd\'T\'HH:MM');
                                                                    row[field] = JSON.stringify(new_event);
                                                                    rows.push(row);
                                                                }
                                                                b++;
                                                            }
                                                        }
                                                        break;
                                                    case 'w':
                                                        if (n > 0) {
                                                            var weeks_b = (date2b.getTime() - date1.getTime()) / 1000;
                                                            weeks_b /= (60 * 60 * 24 * 7);
                                                            var weeks_e = (date2e.getTime() - date1.getTime()) / 1000;
                                                            weeks_e /= (60 * 60 * 24 * 7);
                                                            var b = Math.floor(weeks_b / n) + 1;
                                                            while (b <= Math.floor(weeks_e / n)) {
                                                                if (b >= 0) {
                                                                    var row = $.extend({}, this);
                                                                    var new_event = $.extend({}, event);
                                                                    var new_date = new Date(event.d + ('t' in event ? event.t : '+0000'));
                                                                    new_date = new Date(new_date.getTime() + 60 * 60 * 24 * 7 * 1000 * n * b);
                                                                    new_event.d = dateFormat(new_date, 'yyyy-mm-dd\'T\'HH:MM');
                                                                    row[field] = JSON.stringify(new_event);
                                                                    rows.push(row);
                                                                }
                                                                b++;
                                                            }
                                                        }
                                                        break;
                                                    case 'm':
                                                        if (n > 0) {
                                                            var months = (date2.getFullYear() - date1.getFullYear()) * 12;
                                                            months -= date1.getMonth();
                                                            months += date2.getMonth();
                                                            if (months % n === 0) {
                                                                var row = $.extend({}, this);
                                                                var new_event = $.extend({}, event);
                                                                var new_date = new Date(event.d + ('t' in event ? event.t : '+0000'));
                                                                new_date.setFullYear(date2.getFullYear());
                                                                new_date.setMonth(date2.getMonth());
                                                                new_event.d = dateFormat(new_date, 'yyyy-mm-dd\'T\'HH:MM');
                                                                row[field] = JSON.stringify(new_event);
                                                                rows.push(row);
                                                            }
                                                        }
                                                        break;
                                                    case 'y':
                                                        if (n > 0 && ((date2.getFullYear() - date1.getFullYear()) % n === 0) && (date1.getMonth() === date2.getMonth())) {
                                                            var row = $.extend({}, this);
                                                            var new_event = $.extend({}, event);
                                                            var new_date = new Date(event.d + ('t' in event ? event.t : '+0000'));
                                                            new_date.setFullYear(date2.getFullYear());
                                                            new_date.setMonth(date2.getMonth());
                                                            new_event.d = dateFormat(new_date, 'yyyy-mm-dd\'T\'HH:MM');
                                                            row[field] = JSON.stringify(new_event);
                                                            rows.push(row);
                                                        }
                                                        break;
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            default:
                                if (current_fields[field] === '' || this[field] == current_fields[field]) {
                                    rows.push(this);
                                }
                        }
                    }
                }
            });
            current_rows = rows;
        }
        if (current_rows.length === 1) {
            azt.current_row = current_rows[0];
        } else {
            if (azt.sorting_field) {
                if (azt.sorting_field in azt.fields) {
                    current_rows.sort(function (a, b) {
                        switch (azt.fields[azt.sorting_field].type) {
                            case 'number':
                                if (parseFloat(a[azt.sorting_field]) > parseFloat(b[azt.sorting_field])) {
                                    return azt.sorting_asc ? 1 : -1;
                                }
                                if (parseFloat(b[azt.sorting_field]) > parseFloat(a[azt.sorting_field])) {
                                    return azt.sorting_asc ? -1 : 1;
                                }
                                break;
                            case 'event':
                                var a_event = false;
                                var b_event = false;
                                try {
                                    a_event = JSON.parse(a[azt.sorting_field]);
                                    b_event = JSON.parse(b[azt.sorting_field]);
                                } catch (e) {
                                }
                                if (a_event && b_event) {
                                    if (a_event.d > b_event.d) {
                                        return azt.sorting_asc ? 1 : -1;
                                    }
                                    if (b_event.d > a_event.d) {
                                        return azt.sorting_asc ? -1 : 1;
                                    }
                                }
                                break;
                            default:
                                if (a[azt.sorting_field] > b[azt.sorting_field]) {
                                    return azt.sorting_asc ? 1 : -1;
                                }
                                if (b[azt.sorting_field] > a[azt.sorting_field]) {
                                    return azt.sorting_asc ? -1 : 1;
                                }
                        }
                        return 0;
                    });
                }
            }
        }
        azt.current_rows = current_rows;
    }
    function get_controls_fields($wrapper) {
        var controls_fields = {};
        for (var field in azt.fields) {
            $wrapper.find('input[type="checkbox"][name="' + field + '[]"]:checked').each(function () {
                if (typeof controls_fields[field] !== 'object') {
                    controls_fields[field] = [];
                }
                controls_fields[field].push($(this).val());
            });
            $wrapper.find('input[type="radio"][name="' + field + '"]:checked').each(function () {
                controls_fields[field] = $(this).val();
            });
            $wrapper.find('select[name="' + field + '"]').each(function () {
                controls_fields[field] = $(this).val();
            });
            $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                if (!controls_fields[field]) {
                    controls_fields[field] = {};
                }
                controls_fields[field] = get_range_slider_values($(this));
            });
            $wrapper.find('.air-datepicker[name="' + field + '-from"]').each(function () {
                if (!controls_fields[field]) {
                    controls_fields[field] = {};
                }
                controls_fields[field].from = $(this).data('unixtime') ? $(this).data('unixtime') : 0;
            });
            $wrapper.find('.air-datepicker[name="' + field + '-to"]').each(function () {
                if (!controls_fields[field]) {
                    controls_fields[field] = {};
                }
                controls_fields[field].to = $(this).data('unixtime') ? $(this).data('unixtime') : 0;
            });
            $wrapper.find('[data-month-selector="' + field + '"]').each(function () {
                controls_fields[field] = $(this).data('selected-month');
            });
        }
        return controls_fields;
    }
    function get_range_slider_values($range_slider) {
        var from = parseFloat($range_slider.val().split(';')[0]);
        var to = parseFloat($range_slider.val().split(';')[1]);
        if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'k') {
            from = parseFloat($range_slider.val().split(';')[0]) * 1000;
            to = parseFloat($range_slider.val().split(';')[1]) * 1000;
        }
        if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'm') {
            from = parseFloat($range_slider.val().split(';')[0]) * 1000000;
            to = parseFloat($range_slider.val().split(';')[1]) * 1000000;
        }
        return {'from': from, 'to': to};
    }
    function set_range_slider_values($range_slider, values) {
        values.from = Math.floor(values.from);
        values.to = Math.ceil(values.to);
        if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'k') {
            values.from = Math.floor(values.from / 1000);
            values.to = Math.ceil(values.to / 1000);
        }
        if ($range_slider.data("postfix") && $.trim($range_slider.data("postfix")) === 'm') {
            values.from = Math.floor(values.from / 1000000);
            values.to = Math.ceil(values.to / 1000000);
        }
        $range_slider.data("ionRangeSlider").update(values);
    }
    function init_controls($wrapper, url_fields) {
        azt.init_controls = true;
        for (var field in azt.fields) {
            if (field in url_fields) {
                $wrapper.find('input[type="checkbox"][name="' + field + '"][value="' + url_fields[field] + '"]').each(function () {
                    $(this).prop('checked', true);
                });
                $wrapper.find('input[type="radio"][name="' + field + '"][value="' + url_fields[field] + '"]').each(function () {
                    $(this).prop('checked', true);
                });
                $wrapper.find('select[name="' + field + '"]').each(function () {
                    $(this).val(url_fields[field]);
                });
                $wrapper.find('input[type="hidden"][name="' + field + '"]').each(function () {
                    $(this).val(url_fields[field]);
                });
            } else {
                var value = field_value(azt.table, field);
                if (value) {
                    $wrapper.find('input[type="checkbox"][name="' + field + '"][value="' + value + '"]').each(function () {
                        $(this).prop('checked', true);
                    });
                    $wrapper.find('input[type="radio"][name="' + field + '"][value="' + value + '"]').each(function () {
                        $(this).prop('checked', true);
                    });
                    $wrapper.find('select[name="' + field + '"]').each(function () {
                        $(this).val(value);
                    });
                    $wrapper.find('input[type="hidden"][name="' + field + '"]').each(function () {
                        $(this).val(value);
                    });
                }
            }
            $wrapper.find('.air-datepicker[name="' + field + '-from"]').each(function () {
                var datepicker = $(this).data('datepicker');
                if (datepicker) {
                    if (url_fields[field] && 'from' in url_fields[field]) {
                        var value = url_fields[field].from;
                    } else {
                        var value = field_min(azt.table, field);
                    }
                    $(this).data('unixtime', value);
                    datepicker.selectDate(new Date(value * 1000));
                }
            });
            $wrapper.find('.air-datepicker[name="' + field + '-to"]').each(function () {
                var datepicker = $(this).data('datepicker');
                if (datepicker) {
                    if (url_fields[field] && 'to' in url_fields[field]) {
                        var value = url_fields[field].to;
                    } else {
                        var value = field_max(azt.table, field);
                    }
                    $(this).data('unixtime', value);
                    datepicker.selectDate(new Date(value * 1000));
                }
            });
            $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                var $this = $(this);
                if ($this.data("ionRangeSlider")) {
                    $this.data("ionRangeSlider").reset();
                    if (url_fields[field]) {
                        var values = false;
                        if (typeof url_fields[field] === 'object') {
                            values = {
                                from: url_fields[field].from,
                                to: url_fields[field].to
                            };
                        } else {
                            values = {from: url_fields[field], to: url_fields[field]};
                        }
                        set_range_slider_values($this, values);
                    } else {
                        var values = {
                            from: parseFloat(field_min(azt.table, field)),
                            to: parseFloat(field_max(azt.table, field))
                        };
                        set_range_slider_values($this, values);
                    }
                }
            });
        }
        azt.init_controls = false;
    }
    function refresh_controls($wrapper, $initiator) {
        for (var field in azt.fields) {
            $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').not($initiator).each(function () {
                var $this = $(this);
                if ($this.data("ionRangeSlider")) {
                    if (azt.current_rows.length) {
                        var values = get_range_slider_values($this);
                        if (values.from <= field_min(azt.current_rows, field)) {
                            values.from = field_min(azt.current_rows, field);
                        }
                        if (values.to >= field_max(azt.current_rows, field)) {
                            values.to = field_max(azt.current_rows, field);
                        }
                        set_range_slider_values($this, values);
                    } else {
                        $this.data("ionRangeSlider").update({
                            from: $this.data('min'),
                            to: $this.data('max')
                        });
                    }
                }
            });
        }
    }
    function disable_controls($wrapper) {
        $wrapper.find('.az-clear-filters').css('pointer-events', 'none');
        for (var field in azt.fields) {
            $wrapper.find('input[type="checkbox"][name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', 'none');
            });
            $wrapper.find('input[type="radio"][name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', 'none');
            });
            $wrapper.find('select[name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', 'none');
            });
            $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', 'none');
            });
        }
    }
    function enable_controls($wrapper) {
        $wrapper.find('.az-clear-filters').css('pointer-events', '');
        for (var field in azt.fields) {
            $wrapper.find('input[type="checkbox"][name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', '');
            });
            $wrapper.find('input[type="radio"][name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', '');
            });
            $wrapper.find('select[name="' + field + '"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', '');
            });
            $wrapper.find('input.ion-range-slider[name="' + field + '"][data-type="double"]').each(function () {
                $(this).closest('[data-element]').css('pointer-events', '');
            });
        }
    }
    function is_empty_request(fields1, fields2) {
        var intersect = $(Object.keys(fields1)).filter(Object.keys(fields2));
        var empty = false;
        $(intersect).each(function () {
            if (typeof fields2[this] === 'object') {
                if ('from' in fields2[this] && 'to' in fields2[this]) {
                    if (parseFloat(fields1[this]) < parseFloat(fields2[this]['from']) || parseFloat(fields1[this]) > parseFloat(fields2[this]['to'])) {
                        empty = true;
                        return false;
                    }
                } else {
                    if ('from' in fields2[this]) {
                        if (parseFloat(fields1[this]) < parseFloat(fields2[this]['from'])) {
                            empty = true;
                            return false;
                        }
                    }
                    if ('to' in fields2[this]) {
                        if (parseFloat(fields1[this]) > parseFloat(fields2[this]['to'])) {
                            empty = true;
                            return false;
                        }
                    }
                }
            } else {
                if (fields1[this] != fields2[this]) {
                    empty = true;
                    return false;
                }
            }
        });
        return empty;
    }
    function getCaretPosition(editableDiv) {
        var caretPos = 0, sel, range;
        if (window.get(0).getSelection) {
            sel = window.get(0).getSelection();
            if (sel.rangeCount) {
                range = sel.getRangeAt(0);
                if (range.commonAncestorContainer.parentNode == editableDiv) {
                    caretPos = range.endOffset;
                }
            }
        } else if (window.document.get(0).selection && window.document.get(0).selection.createRange) {
            range = window.document.get(0).selection.createRange();
            if (range.parentElement() == editableDiv) {
                var tempEl = window.document.get(0).createElement("span");
                editableDiv.insertBefore(tempEl, editableDiv.firstChild);
                var tempRange = range.duplicate();
                tempRange.moveToElementText(tempEl);
                tempRange.setEndPoint("EndToEnd", range);
                caretPos = tempRange.text.length;
            }
        }
        return caretPos;
    }
    function setCaretPosition(el, pos) {
        var range = window.document.get(0).createRange();
        var sel = window.get(0).getSelection();
        range.setStart(el.childNodes[0], pos);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    }
    function wrapper_detach($wrapper) {
        $wrapper.data('azt-detach-parent', $wrapper.parent());
        $wrapper.data('azt-detach-index', $wrapper.parent().children().index($wrapper));
        $wrapper.data('azt-focus', false);
        if ($wrapper.find('[contenteditable="true"]:focus').length) {
            $wrapper.data('azt-focus', $wrapper.find('[contenteditable="true"]:focus'));
            $wrapper.data('azt-caret-start', getCaretPosition($wrapper.find('[contenteditable="true"]:focus').get(0)));

        }
        $wrapper.detach();
    }
    function wrapper_restore($wrapper) {
        if ($wrapper.data('azt-detach-index') === 0) {
            $wrapper.prependTo($wrapper.data('azt-detach-parent'));
        } else {
            $wrapper.insertAfter($wrapper.data('azt-detach-parent').children().eq($wrapper.data('azt-detach-index') - 1));
        }
        if ($wrapper.data('azt-focus')) {
            setCaretPosition($wrapper.data('azt-focus').get(0), $wrapper.data('azt-caret-start'));
            $wrapper.data('azt-focus').trigger('focus');
        }
    }
    function show_dynamic_template($dynamic_template, hash, azt_key, azt_value) {
        if ($dynamic_template.css('display') === 'none') {
            var controls_fields = get_controls_fields($body);
            var url_fields = get_url_fields();
            if ($dynamic_template.attr('data-hide')) {
                $body.find($dynamic_template.attr('data-hide')).hide();
            }
            $dynamic_template.show();
            if (hash) {
                var url = window.location.href.split('?')[0].split('#')[0];
                var query = make_click_query(url_fields, controls_fields, azt_key, azt_value);
                window.history.pushState(hash, hash, url + (query ? '?' + query : '') + '#' + hash);
            }
            url_fields = get_url_fields();
            init_controls($body, url_fields);
            select_rows(url_fields);
            if (azt.current_row) {
                disable_controls($body);
            } else {
                enable_controls($body);
            }
            azt.templates = false;
            refresh_template($dynamic_template, url_fields);
            refresh_click_urls($body);
            azt.refresh_image_maps($body);
        }
    }
    function refresh_template($wrapper, url_fields) {
        var scrollTop = $(window).scrollTop();
        if (!url_fields) {
            url_fields = get_url_fields();
        }
        var controls_fields = get_controls_fields($body);
        wrapper_detach($wrapper);
        reset_template($wrapper);
        if (!('templates' in azt) || !azt.templates) {
            azt.templates = $wrapper.find('[data-element][data-azt-key][data-azt-value]').sort(function (a, b) {
                return $(b).parents().length - $(a).parents().length;
            });
        }
        azt.templates.each(function () {
            var $template = $(this);
            var current_fields = {};
            if (azt.fields[$template.attr('data-azt-key')]) {
                current_fields[$template.attr('data-azt-key')] = $template.attr('data-azt-value');
            }
            $template.parents('[data-element][data-azt-key][data-azt-value]').each(function () {
                var $this = $(this);
                if (azt.fields[$this.attr('data-azt-key')]) {
                    current_fields[$this.attr('data-azt-key')] = $this.attr('data-azt-value');
                }
            });
            if (is_empty_request(current_fields, controls_fields)) {
                azt.current_row = false;
                azt.current_rows = [];
            } else {
                var merged_fields = $.extend({}, url_fields);
                merged_fields = $.extend(merged_fields, controls_fields);
                current_fields = $.extend(merged_fields, current_fields);
                select_rows(current_fields);
            }
            fill_template($template, current_fields);
            if ($template.attr('data-id')) {
                fill_template($wrapper.find($template.attr('data-id')), current_fields);
            }
            $template.find('[data-id]').each(function () {
                fill_template($wrapper.find($(this).attr('data-id')), current_fields);
            });
        });
        var current_fields = $.extend({}, url_fields);
        current_fields = $.extend(current_fields, controls_fields);
        select_rows(current_fields);
        fill_template($wrapper, current_fields);
        if (window.location.hash) {
            setTimeout(function () {
                var hash = window.location.hash.replace('#', '');
                var $dynamic_template = $wrapper.find('[data-dynamic-template="' + hash + '"]');
                if ($dynamic_template.length) {
                    show_dynamic_template($dynamic_template, hash);
                }
            });
        }
        $wrapper.find('a[href^="#"]').off('click.azt-dynamic-template').on('click.azt-dynamic-template', function () {
            var $this = $(this);
            var hash = $this.attr('href').replace('#', '');
            var $dynamic_template = $body.find('[data-dynamic-template="' + hash + '"]');
            if ($dynamic_template.length) {
                show_dynamic_template($dynamic_template, hash, $this.attr('data-azt-key') ? $this.attr('data-azt-key') : false, $this.attr('data-azt-value') ? $this.attr('data-azt-value') : false);
                return false;
            }
        });
        $wrapper.find('[data-click-url^="#"]').off('click').on('click', function () {
            var $this = $(this);
            $this.removeClass('az-hover az-current');
            var hash = $this.attr('data-click-url').replace('#', '');
            var $dynamic_template = $body.find('[data-dynamic-template="' + hash + '"]');
            if ($dynamic_template.length) {
                show_dynamic_template($dynamic_template, hash, $this.attr('data-azt-key') ? $this.attr('data-azt-key') : false, $this.attr('data-azt-value') ? $this.attr('data-azt-value') : false);
                return false;
            }
        });
        azt.refresh_image_maps($wrapper);
        wrapper_restore($wrapper);
        $(window).scrollTop(scrollTop);
    }
    function get_url_fields() {
        var url_fields = {};
        $.QueryString = azh.parse_query_string(window.location.search.substr(1).split('&'));
        for (var field in azt.fields) {
            if (field in $.QueryString) {
                url_fields[field] = $.QueryString[field];
            }
            var field_from = field + '-from';
            var field_to = field + '-to';
            if (field_from in $.QueryString || field_to in $.QueryString) {
                url_fields[field] = {};
                if (field_from in $.QueryString) {
                    url_fields[field].from = $.QueryString[field_from];
                }
                if (field_to in $.QueryString) {
                    url_fields[field].to = $.QueryString[field_to];
                }
            }
        }
        return url_fields;
    }
    function make_click_query(url_fields, controls_fields, azt_key, azt_value) {
        var query = $.extend({}, url_fields);
        query = $.extend(query, controls_fields);
        if (azt_key && azt_value) {
            query[azt_key] = azt_value;
        }
        var fields = [];
        for (var key in query) {
            if (typeof query[key] === 'object') {
                if ('from' in query[key]) {
                    fields.push(key + '-from=' + query[key].from);
                }
                if ('to' in query[key]) {
                    fields.push(key + '-to=' + query[key].to);
                }
            } else {
                fields.push(key + '=' + query[key]);
            }
        }
        return fields.join('&');
    }
    function refresh_click_urls($wrapper) {
        var controls_fields = get_controls_fields($body);
        var url_fields = get_url_fields();
        if (!('click_urls' in azt) || !azt.click_urls) {
            azt.click_urls = $wrapper.find('a[href]:not([href^="#"])[data-azt-key]:not([data-azt-key=""])[data-azt-value]:not([data-azt-value=""]), [data-click-url]:not([data-click-url^="#"])[data-azt-key]:not([data-azt-key=""])[data-azt-value]:not([data-azt-value=""])');
        }
        azt.click_urls.each(function () {
            var $this = $(this);
            var query = make_click_query(url_fields, controls_fields, $this.attr('data-azt-key'), $this.attr('data-azt-value'));
            if ($this.attr('href')) {
                $this.attr('href', $this.attr('href').split('?')[0] + '?' + query);
            }
            if ($this.attr('data-click-url')) {
                $this.attr('data-click-url', $this.attr('data-click-url').split('?')[0] + '?' + query);
            }
        });
        if (!('back_buttons' in azt)) {
            azt.back_buttons = $wrapper.find('.az-url-back-button');
        }
        azt.back_buttons.each(function () {
            var $this = $(this);
            var fields = [];
            $this.find('> [data-azt-key]').each(function () {
                if ($(this).attr('data-azt-key') in $.QueryString || $(this).attr('data-azt-key') === '') {
                    fields.push($(this).attr('data-azt-key'));
                }
                $(this).hide();
            });
            var current_field = fields.pop();
            var prev_field = fields.pop();
            var query = $.extend({}, url_fields);
            query = $.extend(query, controls_fields);
            $this.find('> a[data-azt-key="' + prev_field + '"]').each(function () {
                var $this = $(this);
                var search = [];
                for (var field in query) {
                    if (field != current_field) {
                        if (typeof query[field] === 'object') {
                            if ('from' in query[field]) {
                                search.push(field + '-from=' + query[field].from);
                            }
                            if ('to' in query[field]) {
                                search.push(field + '-to=' + query[field].to);
                            }
                        } else {
                            search.push(field + '=' + query[field]);
                        }
                    }
                }
                if (search.length) {
                    $this.attr('href', $this.attr('href').split('?')[0] + '?' + search.join('&'));
                } else {
                    $this.attr('href', $this.attr('href').split('?')[0]);
                }

            }).css('display', '');
        });
        if (!('breadcurmbs' in azt)) {
            azt.breadcurmbs = $wrapper.find('.az-url-breadcurmbs');
        }
        azt.breadcurmbs.each(function () {
            var $this = $(this);
            var fields = [];
            $this.find('> [data-azt-key]').each(function () {
                if ($(this).attr('data-azt-key') in $.QueryString || $(this).attr('data-azt-key') === '') {
                    fields.push($(this).attr('data-azt-key'));
                }
                $(this).hide();
            });
            var query = $.extend({}, url_fields);
            query = $.extend(query, controls_fields);
            for (var current_field in $.QueryString) {
                (function (current_field) {
                    $this.find('> a[data-azt-key="' + current_field + '"]').each(function () {
                        var $this = $(this);
                        var search = [];
                        for (var field in query) {
                            if (fields.indexOf(field) <= fields.indexOf(current_field)) {
                                if (typeof query[field] === 'object') {
                                    if ('from' in query[field]) {
                                        search.push(field + '-from=' + query[field].from);
                                    }
                                    if ('to' in query[field]) {
                                        search.push(field + '-to=' + query[field].to);
                                    }
                                } else {
                                    search.push(field + '=' + query[field]);
                                }
                            }
                        }
                        if (search.length) {
                            $this.attr('href', $this.attr('href').split('?')[0] + '?' + search.join('&'));
                        } else {
                            $this.attr('href', $this.attr('href').split('?')[0]);
                        }
                    }).css('display', '');
                })(current_field);
            }
        });
        if (!('field_up_downs' in azt)) {
            azt.field_up_downs = $wrapper.find('.az-url-field-up-down');
        }
        azt.field_up_downs.each(function (event) {
            var $wrapper = $(this);
            if ($wrapper.attr('data-azt-key') in $.QueryString) {
                var query = $.extend({}, url_fields);
                query = $.extend(query, controls_fields);
                $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                $wrapper.find('> [data-azt-value]').each(function () {
                    var $this = $(this);
                    var search = [];
                    for (var field in query) {
                        var value = query[field];
                        if (field == $wrapper.attr('data-azt-key')) {
                            if (value == $this.attr('data-azt-value')) {
                                $this.addClass('az-active');
                                $this.prev().addClass('az-up');
                                $this.next().addClass('az-down');
                            } else {
                                value = $this.attr('data-azt-value');
                            }
                        }
                        search.push(field + '=' + value);
                    }
                    var $a = $this.find('> a[href]');
                    $a.attr('href', $a.attr('href').split('?')[0] + '?' + search.join('&'));
                });
            }
        });
    }
    function fill_gmap($map_wrapper, locations) {
        if (typeof google === 'undefined' || typeof RichMarker === 'undefined' || typeof InfoBox === 'undefined' || typeof MarkerClusterer === 'undefined') {
            return;
        }
        var default_zoom = ('default_zoom' in azt) ? parseInt(azt.default_zoom, 10) : 14;
        var clusterImage = ('cluster_image' in azt) ? azt.cluster_image : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAmCAMAAACF3/kSAAACHFBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAnJycAAAAAAAAAAAAcHBwxMTEAAAAAAAArKysAAAAAAAAAAAAAAAAAAAAQEBAAAAAzMzMAAAAAAAAAAAAAAAAwMDAAAAAAAAALCwswMDAAAAAAAAAxMTEAAAAJCQkwMDAAAAAwMDAAAAAAAAAJCQkwMDAXFxcWFhYyMjIgICAgICAvLy8xMTEnJycqKiozMzMyMjIwMDAyMjIxMTEwMDAyMjIzMzMwMDAxMTEyMjIxMTEwMDAxMTEyMjIyMjIyMjIwMDAxMTEwMDAwMDAwMDAwMDAxMTEwMDAwMDAyMjIyMjIwMDAwMDAzMzMzMzMxMTEwMDAzMzMyMjIyMjIxMTExMTEzMzMzMzMyMjIzMzMxMTEyMjIzMzMyMjIzMzMyMjIzMzMxMTEyMjIzMzMxMTEyMjIzMzMzMzMyMjIzMzMzMzMxMTEyMjIzMzMyMjIzMzMyMjIzMzMzMzMyMjIyMjIzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzM0NDQ1NTU3Nzc4ODg5OTk6Ojo8PDw9PT0/Pz9BQUFDQ0NGRkZISEhJSUlNTU1SUlJWVlZZWVlbW1tgYGBiYmJkZGRmZmZoaGhqampsbGxycnJ0dHR1dXV3d3d4eHh5eXl6enp7e3t8fHx9fX1+fn5/f3+AgICBgYGCgoKDg4OEhISFhYWGhoaIiIj///8OSmhWAAAAhHRSTlMAAQIDBAUGBgcICQkJCgsLDA0ODxAQERESExQVFhcYGBgZGhobGxscHB0eHh8hIiYnKCw1Njg4PD9FSE5OT1FUXF1eYGJkaGpqb3F3eHmPkJOanJ2epKWmp6iztbi5v8DAwcLCw8TGxsjKysvLy83P0NHT1NTV2NnZ3N3f4uTm7/Hy9PwgK4oaAAACw0lEQVR42o2Ud1sTQRDGucslJlEiFzDCRVAiGoO994K9d0XFLnZFxYZd8UxoodpAJBAvcCRcDPMJ3ZnjIeFy8Dh/7O3O/vLOzM5mcwzGcRxPxiY5UxjDFu49V/NKlutqzuyex/BJQUvFTTnDLm+3mMMcv/GxbLB763nOBJxxWjax49M4zkguuqHvNffE1NRfVelu0teX5qBwJrnkLW10xWHc1HZy1c1HNk3OfobexkGARKQjHAyFO/tHAJQG9D6cmcHywnn0tSUh3p1O89cIaC04OSHw46LCZiJHIRrMLCmkQIrYDRZujLRMf4LRkxAx1t8PWoh9btswBRLdhe4hiMpZpoCCn20Cp4vaUbQL4sFstEGDNva5ardwJLoanXGgiozWC0Ns/LycZHnbEbZoRZeZJaCZjftsPMZ3XKBf/zZHIxTtrMPCsfi5L9g8Bu3m6FcYYOPdXIGhVlc9m2vQaI6GYZiN711WjqU6Cz0pCJqjjaCx8WMeS5a3i/+DioS666nQpskSUNn4TrQT+pLa0jFVWQ90VKyiw+qb8rAoAZt4mFqgmpJf9Mz2s7LwsNahb9i8sT0wiI1d5bJSCwofsVUnJEyvC9VwzeMUOGxs/gGZ+vUnO/yYc2c+NhaTnfuGBLKudnAAEni1n0qYag5m4KnAjdZRUEIToscg1YyTrRSfrrboPUmnrcFIbzp4XxIS1JejXpGuti674DrJKADawLdwSzj8PZoEiFKQiyUFJEqytryiFa9Jqn0Ixi3WSq7nSwtd9DfUWYcorbwydj1+RtT4sBr50aCvq5ZJGD79ZDjc3sWnzFpwzCe5HQKX8RBZnW7Jt+W+EazeVIokj1CadYhFJf49dyaAO/zFhSIjDU+hYHcVeEv9aw9V3qr99KG2uvLgGn+JVOCyI2l83W1O0SMV+8oCgfLyQKDMVyx5RKctXdE/vhU4fG5sh7EAAAAASUVORK5CYII=';
        var markerImage = ('marker_image' in azt) ? azt.marker_image : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACoAAAAyCAMAAAAdmLmfAAAB+FBMVEUAAAA0NDQAAAAAAAAAAAAAAAAAAAAAAAAqKioAAAAAAAAAAAAcHBwvLy8AAAAAAAAtLS0AAAAAAAAAAAAAAAAAAAAQEBAAAAAvLy8AAAAAAAAAAAAAAAAAAAAwMDAAAAAwMDAAAAAwMDAAAAAAAAATExMwMDAJCQkwMDAaGhoJCQkZGRkwMDAQEBAWFhYWFhYdHR0bGxsgICAfHx8vLy8mJiYvLy8vLy8wMDAyMjIyMjIyMjIvLy8vLy8yMjIvLy8vLy8wMDAzMzMwMDAyMjIzMzMxMTEzMzMzMzMxMTEwMDAwMDAwMDAwMDAxMTEwMDAwMDAwMDAxMTExMTEzMzMzMzMyMjIzMzMyMjIyMjIzMzMyMjIyMjIzMzMxMTEzMzMzMzMxMTEzMzMxMTExMTEyMjIzMzMyMjI0NDQzMzMzMzM0NDQzMzMzMzM0NDQzMzMzMzMzMzM0NDQ0NDQzMzMzMzMzMzMzMzMzMzMzMzM0NDQzMzM0NDQzMzMzMzM0NDQzMzMzMzMzMzM0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ2NjY4ODhAQEBDQ0NJSUlQUFBcXFxhYWFubm6Ojo6QkJCUlJSWlpaYmJiampqgoKCmpqaoqKirq6utra21tbW7u7vBwcHDw8PHx8fNzc3d3d339/f7+/v9/f3///+bcWbUAAAAiHRSTlMAAAECAwQFBgYHCAkJCQoLCwwNDg8QEBEREhMUFRYWFxcYGBkaGxscHB0eHh8gIiMjJicpLS8wMjVBQkVLTE5RU1NXWVpdYGJocHJzdHd5fX6GlZaWmZqeoKKkpaenqKits7O1uL2/wMHCxMTFysrLzM3N0NHS09TY2dna3N/i4ufq7O70+fr86PgHKAAAAoxJREFUeAGN0vdb00AYB3B6l0tN0MQmRUwbrAi4Bdx7b1Fx762oKCriRlEc0nPvgQOHKP+mb17a5y71Evz++D6f5833+rYMkpBCCKEYQkhxViYiOTp+3cGOW5x3duxbOw64iiLUVp/lUk6v0AhRUUIXXuIlOb+AkjBFOGI3V2THMEokinJiK1emdYxsAzn1Do9I51jJghx9jUfm5EhhE5Qd5jHZyWiREraEx2a+RgpSG345nrYnKSksXcOHyCpGUGpGeOnLj319b5+H1xpYgbB58vTxlz8DkP4PITsL19LkZmn25Bc4zM9H0nh9kgbfN49Js+/IMJ+k8RETGhBmXRejVwMi/U/F/IrFgOr2AzHqHZDyWszv2zqBqqO4yDeZfhbzhykoSw0nivaKed5B6v5HgXuugfTG0M+6OkidQ3ID9Y91AAsknSblCX7IJ2hKASW6PTd02N7f+PX38jA/x9bxBF47l/PiXd/XN8+4nDMenkAz05v4ENmQDg4blJ10N1521wWvKsMGW+Npc2Xwffxru1O64uTNOjf4a0OobnvLY2R+sWfpNJC4tmp/NN3lF5biWquysTtKdjUONi2sTaayK/Nq2bM0A5cq0gRhZrqqWU03VrkmQ4k0EVSo3quS23IVwZsEhQq2N6HlX3mixrPh8xIFaziZGRdL5bn6TApfL9GgrpudfiEs2+qzjlQUKYSC9RvaZHmqwXdBJkopoawcrNT36DTfLWe0lKLVwU7eU5Tba7ODMkyFzdZuwVv0NIE0USooWOibqVkGN769qCbjoFRTtI5XPbvl+Mycl0IZQdEadqWfy/kVtsEQRlG8m+Wk046V1FAijQjRdMM0DSauGR1CNYiQ8RiiGP8FiuV/XhbhuMMAAAAASUVORK5CYII=';
        var mapStyles = ('map_styles' in azt) ? azt.map_styles : [{"featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{"color": "#333333"}]}, {"featureType": "landscape", "elementType": "all", "stylers": [{"color": "#f5f5f5"}]}, {"featureType": "poi", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "road", "elementType": "all", "stylers": [{"saturation": -100}, {"lightness": 45}]}, {"featureType": "road.highway", "elementType": "all", "stylers": [{"visibility": "simplified"}]}, {"featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {"featureType": "transit", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "water", "elementType": "all", "stylers": [{"color": "#ffffff"}, {"visibility": "on"}]}];
        var clusterStyles = ('cluster_styles' in azt) ? azt.cluster_styles : [{url: clusterImage, height: 38, width: 42, textColor: "#ffffff"}];
        var markerHTML = '<div class="azt-map-marker">' +
                '<div class="azt-icon">' +
                '<img class="azt-marker" src="' + markerImage + '">' +
                '</div>' +
                '</div>';
        var infoboxOptions = {
            disableAutoPan: true,
            pixelOffset: new google.maps.Size(0, 0),
            zIndex: null,
            alignBottom: true,
            boxClass: "infobox-wrapper",
            enableEventPropagation: true,
            closeBoxMargin: "0px 0px 0px 0px",
            closeBoxURL: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAVCAMAAACeyVWkAAAAGFBMVEUqKippaWlqampra2tubm60tLT6+vr////njj39AAAANUlEQVQY083QqREAMAwDwZO/9N9xuB0QaMElmjnOa+zXjKrIro4J71oGqqECGxrIiL+3dXUuaeoLgrmbMAAAAAAASUVORK5CYII=',
            infoBoxClearance: new google.maps.Size(1, 1)
        };
        var infoboxHTML = '<div class="azt-location">' + $map_wrapper.children('.azt-location').html() + '</div>';

        var $map = $map_wrapper.find('.azt-map');
        if (typeof $map.data('azt-markers') !== 'undefined') {
            var markers = $map.data('azt-markers');
            if (typeof $map.data('markerClusterer') !== 'undefined') {
                $map.data('markerClusterer').removeMarkers(markers);
            }
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
                if ('infobox' in markers[i]) {
                    markers[i].infobox.close();
                }
            }
        }
        var map = null;
        if (!$map.data('map')) {
            map = new google.maps.Map($map.get(0), {
                //disableDefaultUI: true,
                styles: mapStyles,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
        } else {
            map = $map.data('map');
        }
        $map.data('map', map);
        var bounds = new google.maps.LatLngBounds();
        var markers = [];
        var markers_index = {};
        $map.data('azt-active-marker', false);
        $map.data('azt-last-clicked', false);
        function infobox_show(markers, marker) {
            function infobox_create_open(m) {
                function infobox_create() {
                    var boxText = document.createElement("div");
                    boxText.innerHTML = infoboxHTML;
                    azt.current_row = m.location;
                    fill_template($(boxText), m.location);
                    azt.current_row = false;
                    infoboxOptions.content = boxText;
                    $map_wrapper.children('.azt-location').css('display', 'inline-block');
                    infoboxOptions.pixelOffset = new google.maps.Size(-$map_wrapper.children('.azt-location').width() / 2, 0);
                    $map_wrapper.children('.azt-location').css('display', '');
                    m.infobox = new InfoBox(infoboxOptions);
                    google.maps.event.addListener(m.infobox, 'closeclick', function () {
                        $map.data('azt-last-clicked', false);
                    });
                }
                function infobox_open() {
                    $(m.infobox.content_).find('img[src=""]').each(function () {
                        $(this).attr('src', $(this).data('src')).on('load', function () {
                            m.infobox.open(map, m);
                        });
                    });
                    $(m.infobox.content_).find('[data-background-image]').each(function () {
                        $(this).css('background-image', 'url(' + $(this).data('data-background-image') + ')');
                    });
                    $(m.infobox.content_).on('click', function () {
                        var markers = $map.data('azt-markers');
                        details_show(markers, m);
                        return false;
                    });
                    m.infobox.open(map, m);
                }
                if (!('infobox' in m)) {
                    infobox_create();
                }
                infobox_open();
            }
            $map.data('azt-active-marker', marker);
            if ($map.data('azt-active-marker') !== $map.data('azt-last-clicked')) {
                for (var j = 0; j < markers.length; j++) {
                    if ('infobox' in markers[j]) {
                        markers[j].infobox.close();
                    }
                }
                infobox_create_open(marker);
            }
            $map.data('azt-last-clicked', marker);
        }
        function details_show(markers, marker) {
            $map.data('azt-active-marker', marker);
            for (var i = 0; i < markers.length; i++) {
                $(markers[i].content).removeClass('azt-active');
            }
            $(marker.content).addClass('azt-active');
            $list.children().removeClass('azt-active');
            reset_template($details);
            azt.current_row = marker.location;
            fill_template($details, marker.location);
            azt.current_row = false;
            $list.hide();
            $details.show().triggerHandler('azt-show');
        }
        function hashCode(str) {
            return str.split('').reduce((prevHash, currVal) =>
                (((prevHash << 5) - prevHash) + currVal.charCodeAt(0)) | 0, 0);
        }
        for (var i in locations) {
            if ('lat' in locations[i] && 'lng' in locations[i]) {
                var location = new google.maps.LatLng(parseFloat(locations[i].lat), parseFloat(locations[i].lng));
                bounds.extend(location);

                var markerDIV = document.createElement('DIV');
                markerDIV.innerHTML = markerHTML;

                var marker = new RichMarker({
                    position: location,
                    map: map,
                    content: markerDIV,
                    flat: true
                });
                marker.location = locations[i];
                markers_index[hashCode(JSON.stringify(locations[i]))] = markers.length;
                markers.push(marker);

                (function (marker) {
                    google.maps.event.addDomListener(marker.content, 'mouseenter', function (event) {
                        var markers = $map.data('azt-markers');
                        if (!('ontouchstart' in window)) {
                            infobox_show(markers, marker);
                        }
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        }
                        event.returnValue = false;
                    });
                    google.maps.event.addDomListener(marker.content, 'click', function (event) {
                        var markers = $map.data('azt-markers');
                        if ('ontouchstart' in window) {
                            if (marker === $map.data('azt-last-clicked')) {
                                details_show(markers, marker);
                            } else {
                                infobox_show(markers, marker);
                            }
                        } else {
                            details_show(markers, marker);
                        }
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        }
                        event.returnValue = false;
                    });
                })(marker);
            }
        }
        $map.data('azt-markers', markers);
        $map.data('azt-markers-index', markers_index);
        $map.data('azt-bounds', bounds);
        map.refresh = function () {
            var markers = $map.data('azt-markers');
            var marker = $map.data('azt-active-marker');
            var bounds = $map.data('azt-bounds');
            if (marker) {
                infobox_show(markers, marker);
                map.panTo(marker.getPosition());
            } else {
                if (markers.length) {
                    google.maps.event.trigger(map, 'resize');
                    if (markers.length > 1) {
                        map.fitBounds(bounds);
                    } else {
                        map.setZoom(default_zoom);
                        map.panTo(markers[0].getPosition());
                    }
                }
            }
        };
        var intervalID = setInterval(function () {
            if (!$.contains(document, $map[0])) {
                clearInterval(intervalID);
            }
            if ($map.is(':visible')) {
                map.refresh();
                clearInterval(intervalID);
            }
        }, 100);
        map.load_locations = function () {
            $.post(azh.ajaxurl, {
                'action': 'azt_get_locations',
                'southwest_latitude': map.getBounds().getSouthWest().lat(),
                'southwest_longitude': map.getBounds().getSouthWest().lng(),
                'northeast_latitude': map.getBounds().getNorthEast().lat(),
                'northeast_longitude': map.getBounds().getNorthEast().lng(),
                'query': $map.data('query')
            }, function (response) {
                if (response && response !== '') {
//                        var length = Object.keys(locations).length;
//                        locations = $.extend(JSON.parse(response), locations);
//                        if (Object.keys(locations).length > length) {
//                            fill_gmap($map_wrapper, locations);
//                        }
                }
            });
        };
        var $details = $map_wrapper.closest('[data-section]').find('.azt-map-details');
        var $list = $map_wrapper.closest('[data-section]').find('.azt-map-list');
        if (document.documentElement.clientWidth > 600) {
            $details.hide();
            $list.show();
        }
        $details.off('click').on('click', function (event) {
            var markers = $map.data('azt-markers');
            var marker = $map.data('azt-active-marker');
            map.panTo(marker.getPosition());
            return false;
        });
        $details.find('.azt-show-list').off('click').on('click', function (event) {
            $details.hide();
            $list.show();
            $map.data('azt-active-marker', false);
            return false;
        });
        $list.off('azt-refresh').on('azt-refresh', function () {
            $list.children().removeClass('azt-active');
            $list.children().off('click').on('click', function (event) {
                var $item = $(event.target).parentsUntil('.azt-map-list').last();
                var active = $item.is('.azt-active');
                $item.parent().children().removeClass('azt-active');
                $item.addClass('azt-active');
                if ($item.data('azt-row')) {
                    var markers_index = $map.data('azt-markers-index');
                    var markers = $map.data('azt-markers');
                    var i = markers_index[hashCode(JSON.stringify($item.data('azt-row')))];
                    if (typeof i !== 'undefined') {
                        if (active || document.documentElement.clientWidth < 600) {
                            details_show(markers, markers[i]);
                        } else {
                            infobox_show(markers, markers[i]);
                        }
                        map.setZoom(default_zoom);
                        map.panTo(markers[i].getPosition());
                    }
                }
                return false;
            });
        });
        $map_wrapper.closest('[data-section]').find('.azt-locate').off('click').on('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var location = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
                    map.setZoom(default_zoom);
                    map.panTo(location);
                    //map.load_locations();
                });
            }
            return false;
        });
        $map_wrapper.closest('[data-section]').find('input.azt-place-autocomplete').each(function () {
            var $search = $(this);
            var autocomplete = $search.data('azt-autocomplete');
            if (!autocomplete) {
                var options = {};
                if ($search.data('place-types')) {
                    options.types = [$search.data('place-types')];//(regions) (cities)
                }
                if ($search.data('country')) {
                    options.componentRestrictions = {country: $search.data('country')};//us
                }
                autocomplete = new google.maps.places.Autocomplete($search[0], options);
                autocomplete.bindTo('bounds', map);
                $search.data('azt-autocomplete', autocomplete);
            }

            google.maps.event.clearListeners(autocomplete, 'place_changed');
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setZoom(default_zoom);
                    map.panTo(place.geometry.location);
                }
            });

            $search.off('keypress').on('keypress', function (event) {
                if (13 === event.keyCode) {
                    event.preventDefault();
                }
            });

        });
        google.maps.event.clearListeners(map, 'dragend');
        google.maps.event.addListener(map, 'dragend', function (event) {
            //map.load_locations();
        });
        if (!('ontouchstart' in window)) {
            google.maps.event.clearListeners(map, 'click');
            google.maps.event.addListener(map, 'click', function (event) {
                if ($map.data('azt-active-marker') != false) {
                    setTimeout(function () {
                        if ($map.data('azt-active-marker') && 'infobox' in $map.data('azt-active-marker')) {
                            $map.data('azt-active-marker').infobox.close();
                        }
                        $map.data('azt-last-clicked', false);
                    }, 0);
                }
                var markers = $map.data('azt-markers');
                for (var i = 0; i < markers.length; i++) {
                    $(markers[i].content).removeClass('azt-active');
                }
                if (document.documentElement.clientWidth > 600) {
                    $details.hide();
                    $list.show();
                    $map.data('azt-active-marker', false);
                }
            });
        }

        google.maps.event.clearListeners(map, 'bounds_changed');
        google.maps.event.addListenerOnce(map, 'bounds_changed', function (event) {
            var markers = $map.data('azt-markers');
            if (markers && markers.length === 1) {
                if (this.getZoom()) {
                    this.setZoom(default_zoom);
                }
            }
        });
        var markerClusterer = new MarkerClusterer(map, markers, {
            styles: clusterStyles,
            maxZoom: 19
        });
        $map.data('markerClusterer', markerClusterer);
    }
    azt.google_init = function (callback) {
        if ('gmap_api_key' in azt) {
            if ('google' in window) {
                callback();
            } else {
                loadScript('//maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=' + azt.gmap_api_key, function (path, status) {
                    callback();
                });
            }
        } else {
            callback();
        }
    };
    azt.init = function ($wrapper) {
        if ('fields' in azt && 'table' in azt) {
            var url_fields = false;
            azt.sorting_field = false;
            azt.sorting_asc = false;
            var $new_wrapper = false;
            if ($wrapper.find('[data-section]').length) {
                var $parents = $($wrapper.find('[data-section]').parents().get().reverse());
                $parents.each(function () {
                    var $parent = $(this);
                    var all = true;
                    $wrapper.find('[data-section]').each(function () {
                        if (!$parent.has($(this)).length) {
                            all = false;
                            return false;
                        }
                    });
                    if (all) {
                        $new_wrapper = $parent;
                    } else {
                        return false;
                    }
                });
            }
            if ($new_wrapper) {
                $wrapper = $new_wrapper;
            }
            $wrapper.find('.ion-range-slider').each(function () {
                var $this = $(this);
                $this.ionRangeSlider();
                $this.prev().addClass('az-empty-inner-html');
                $this.on('azh-change', function () {
                    var $this = $(this);
                    if ($this.data("ionRangeSlider")) {
                        $this.data("ionRangeSlider").destroy();
                        $this.removeData(['ionRangeSlider', 'skin', 'type', 'min', 'max', 'from', 'to', 'step', 'prefix', 'postfix', 'grid']);
                    }
                    $this.ionRangeSlider();
                    $this.prev().addClass('az-empty-inner-html');
                });
                $this.on('azh-clone', function () {
                    var $this = $(this);
                    $this.prev().remove();
                    $this.removeData(['ionRangeSlider', 'skin', 'type', 'min', 'max', 'from', 'to', 'step', 'prefix', 'postfix', 'grid']);
                    $this.ionRangeSlider();
                    $this.addClass('irs-hidden-input').prev().addClass('az-empty-inner-html');
                });
            });
            $.fn.air_datepicker.language['en'] = {
                days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                today: 'Today',
                clear: 'Clear',
                dateFormat: 'mm/dd/yyyy',
                timeFormat: 'hh:ii aa',
                firstDay: 0
            };
            $wrapper.find('.air-datepicker').each(function () {
                var $this = $(this);
                $this.air_datepicker({
                    language: 'en',
                    onSelect: function (fd, d, picker) {
                        if (d) {
                            var unixtime = d.getTime() / 1000;
                            $this.data('unixtime', unixtime);
                            //$this.trigger('change');
                            if (!azt.init_controls) {
                                refresh_template($wrapper, get_url_fields());
                                refresh_click_urls($wrapper);
                            }
                        }
                    }
                });
            });
            if (customize) {
                $wrapper.find('.az-url-back-button').each(function () {
                    var $this = $(this);
                    $this.find('> [data-azt-key]').hide();
                    $this.find('> [data-azt-key]').first().show();
                    $this.find('> [data-azt-key]').on('azh-active', function (event) {
                        $this.find('> [data-azt-key]').hide();
                        $(this).show();
                    });
                });
                $wrapper.find('.az-url-breadcurmbs').each(function () {
                    var $this = $(this);
                    $this.find('> [data-azt-key]').show();
                });
                $wrapper.find('.az-url-field-up-down').each(function (event) {
                    var $wrapper = $(this);
                    $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                    $wrapper.find('> [data-azt-value]').first().addClass('az-active').next().addClass('az-down');
                    $wrapper.find('> [data-azt-value]').on('azh-active', function (event) {
                        var $this = $(this);
                        $wrapper.find('> [data-azt-value]').removeClass('az-up az-active az-down');
                        $this.addClass('az-active');
                        $this.prev().addClass('az-up');
                        $this.next().addClass('az-down');
                    });
                });
            } else {
                if (!azt.table.length && azt.i18n && azt.i18n.empty_text) {
                    $wrapper.find('.azt-filters-list-details-map').each(function () {
                        var $fldm = $(this);
                        $fldm.text(azt.i18n.empty_text);
                    });
                }
                $wrapper.find('[data-month-selector]').each(function () {
                    function select_date(date) {
                        $selected.text(dateFormat(date, $selected.attr('data-selected-month')));
                        $selector.data('selected-month', dateFormat(date, 'yyyy-mm'));
                    }
                    var $selector = $(this);
                    var $selected = $selector.find('[data-selected-month]');
                    $selector.find('.azt-minus-month').on('click', function () {
                        var date = new Date($selector.data('selected-month'));
                        date.setMonth(date.getMonth() - 1);
                        select_date(date);
                        $selector.trigger('change');
                    });
                    $selector.find('.azt-plus-month').on('click', function () {
                        var date = new Date($selector.data('selected-month'));
                        date.setMonth(date.getMonth() + 1);
                        select_date(date);
                        $selector.trigger('change');
                    });
                    $selector.find('.azt-current-month').on('click', function () {
                        select_date(new Date());
                        $selector.trigger('change');
                    });
                    select_date(new Date());
                }).on('change', function () {
                    if (!azt.init_controls) {
                        refresh_template($wrapper, get_url_fields());
                        refresh_click_urls($wrapper);
                    }
                });
                $wrapper.find('[data-dynamic-template]:not([data-visible="true"])').hide();
                for (var name in azt.fields) {
                    if ('values' in azt.fields[name]) {
                        $wrapper.find('.az-template select[name="' + name + '"].az-select').each(function () {
                            var $select = $(this);
                            azt.fields[name].values.forEach(function (value, i) {
                                if (!$select.children('[value="' + value + '"]').length) {
                                    $select.append('<option value="' + value + '">' + value + '</option>');
                                }
                            });
                            $select.children('[value]').each(function () {
                                var $this = $(this);
                                if ($this.attr('value')) {
                                    if (azt.fields[name].values.indexOf($this.attr('value')) < 0) {
                                        $this.remove();
                                    }
                                }
                            });
                        });
                        $wrapper.find('.az-template .az-radio-buttons').each(function () {
                            var $buttons = $(this);
                            if ($buttons.find('[name="' + name + '"]').length) {
                                azt.fields[name].values.forEach(function (value, i) {
                                    if (!$buttons.find('[name="' + name + '"][value="' + value + '"]').length) {
                                        var $new_button = $buttons.children().last().clone(true);
                                        $buttons.append($new_button);
                                        $new_button.find('[value]').attr('value', value);
                                        $new_button.find('*').contents().filter(function () {
                                            return this.nodeType === 3;
                                        }).each(function () {
                                            if ($.trim(this.textContent)) {
                                                this.textContent = value;
                                            }
                                        });
                                    }
                                });
                                $buttons.find('[value]').each(function () {
                                    var $this = $(this);
                                    if ($this.attr('value')) {
                                        if (azt.fields[name].values.indexOf($this.attr('value')) < 0) {
                                            $this.parentsUntil('.az-radio-buttons').last().remove();
                                        }
                                    }
                                });
                            }
                        });
                        $wrapper.find('.az-template .az-checkboxes').each(function () {
                            var $checkboxes = $(this);
                            if ($checkboxes.find('[name="' + name + '[]"]').length && azt.fields[name].type !== 'working_hours') {
                                azt.fields[name].values.forEach(function (value, i) {
                                    if (!$checkboxes.find('[name="' + name + '"][value="' + value + '"]').length) {
                                        var $new_button = $checkboxes.children().last().clone(true);
                                        $checkboxes.append($new_button);
                                        $new_button.find('[value]').attr('value', value);
                                        $new_button.find('*').contents().filter(function () {
                                            return this.nodeType === 3;
                                        }).each(function () {
                                            if ($.trim(this.textContent)) {
                                                this.textContent = value;
                                            }
                                        });
                                    }
                                });
                                $checkboxes.find('[value]').each(function () {
                                    var $this = $(this);
                                    if ($this.attr('value')) {
                                        if (azt.fields[name].values.indexOf($this.attr('value')) < 0) {
                                            $this.parentsUntil('.az-checkboxes').last().remove();
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
                $wrapper.find('.azt-table').each(function () {
                    var $table = $(this);
                    var $thead = $table.find('thead');
                    var $tbody = $table.find('tbody');
                    if ($thead.length && $thead.children().length && $thead.children().first().children().length && $tbody.length && $tbody.children().length && $tbody.children().first().children().length) {
                        $tbody.children().first().children().each(function (index) {
                            var $field = $(this);
                            var field = $field.text().replace('azt-', '');
                            if (field in azt.fields) {
                                if (index < $thead.children().first().children().length) {
                                    var $sorting_field = $($thead.children().first().children().get(index));
                                    $sorting_field.addClass('azt-sortable').on('click', function () {
                                        azt.sorting_field = field;
                                        azt.sorting_asc = !$sorting_field.is('.azt-sort-asc');
                                        $thead.find('.azt-sort-asc, .azt-sort-desc').removeClass('azt-sort-asc azt-sort-desc');
                                        if (azt.sorting_asc) {
                                            $sorting_field.addClass('azt-sort-asc');
                                        } else {
                                            $sorting_field.addClass('azt-sort-desc');
                                        }
                                        refresh_template($wrapper);
                                        refresh_click_urls($wrapper);
                                    });
                                }
                            }
                        });
                    }
                });
                var $sorting = $wrapper.find('select[name="sorting"]');
                if ($sorting.length) {
                    function sorting_change() {
                        var sorting = $sorting.val();
                        azt.sorting_asc = true;
                        if (sorting.indexOf('-desc') >= 0) {
                            azt.sorting_asc = false;
                        }
                        azt.sorting_field = sorting.replace('-asc', '').replace('-desc', '');
                    }
                    $sorting.on('change', function () {
                        sorting_change();
                        refresh_template($wrapper);
                        refresh_click_urls($wrapper);
                    });
                    sorting_change();
                }
                url_fields = get_url_fields();
                init_controls($wrapper, url_fields);
                select_rows(url_fields);
                if (azt.current_row) {
                    disable_controls($wrapper);
                }
                refresh_template($wrapper, url_fields);

                for (var field in azt.fields) {
                    $wrapper.find('input[type="checkbox"][name="' + field + '[]"]').on('change', function () {
                        if (!azt.init_controls) {
                            refresh_template($wrapper);
                            refresh_click_urls($wrapper);
                        }
                    });
                    $wrapper.find('input[type="radio"][name="' + field + '"]').on('change', function () {
                        if (!azt.init_controls) {
                            refresh_template($wrapper);
                            refresh_click_urls($wrapper);
                        }
                    });
                    $wrapper.find('select[name="' + field + '"]').on('change', function () {
                        if (!azt.init_controls) {
                            refresh_template($wrapper);
                            refresh_click_urls($wrapper);
                        }
                    });
                    $wrapper.find('input.ion-range-slider[name="' + field + '"]').on('change', function () {
                        if (!azt.init_controls) {
                            $window.off('mouseup.azh').one('mouseup.azh', function () {
                                refresh_template($wrapper);
                                refresh_click_urls($wrapper);
                            });
                        }
                    });
                    $wrapper.find('.air-datepicker[name="' + field + '"]').on('change', function () {
                        if (!azt.init_controls) {
                            refresh_template($wrapper);
                            refresh_click_urls($wrapper);
                        }
                    });
                }
                refresh_click_urls($wrapper);
                $wrapper.find('.az-clear-filters').on('click', function (event) {
                    $wrapper.find('form').each(function () {
                        this.reset();
                    });
                    var reset_fields = {}; //url_fields
                    init_controls($wrapper, reset_fields);
                    select_rows(reset_fields);
                    refresh_template($wrapper, reset_fields);
                    refresh_click_urls($wrapper);
                    return false;
                });
                $(window).on('resize.azt', function () {
                    var vh = window.innerHeight * 0.01;
                    document.documentElement.style.setProperty('--vh', vh + 'px');
                }).trigger('resize.azt');
                if (document.documentElement.clientWidth < 600) {
                    $('.azt-filters-list-details-map').each(function () {
                        var $fldm = $(this);
                        var $map_wrapper = $fldm.find('.azt-map-wrapper');
                        var $map = $map_wrapper.find('.azt-map');
                        var $details = $fldm.find('.azt-map-details');
                        var $list = $fldm.find('.azt-map-list');
                        $details.on('azt-show', function () {
                            $details.css('display', '');
                            $list.css('display', '');
                            $fldm.attr('data-mobile-state', 'details');
                            $(window).trigger('resize.azt');
                        });
                        $list.on('azt-show', function () {
                            $details.css('display', '');
                            $list.css('display', '');
                            $fldm.attr('data-mobile-state', 'list');
                            $(window).trigger('resize.azt');
                        });
                        $fldm.closest('.az-container').removeClass('az-container');
                        $fldm.find('.azt-open-filters').on('click', function () {
                            $details.css('display', '');
                            $list.css('display', '');
                            $fldm.attr('data-mobile-state', 'filters');
                            $(window).trigger('resize.azt');
                        });
                        $fldm.find('.azt-open-list').on('click', function () {
                            $map.data('azt-active-marker', false);
                            $details.css('display', '');
                            $list.css('display', '');
                            $fldm.attr('data-mobile-state', 'list');
                            $(window).trigger('resize.azt');
                        });
                        $fldm.find('.azt-open-map').on('click', function () {
                            $details.css('display', '');
                            $list.css('display', '');
                            $fldm.attr('data-mobile-state', 'map');
                            $(window).trigger('resize.azt');
                            $map.data('map').refresh();
                        });
                    });
                }
                $body.find('.az-template').fadeIn();
            }
        }
    };
    $window.on('az-frontend-before-init', function (event, data) {
        azt.google_init(function () {
            azt.init(data.wrapper);
        });
        window.addEventListener('popstate', function (event) {
            if (event.state) {
                var hash = event.state;
                var $dynamic_template = $body.find('[data-dynamic-template="' + hash + '"]');
                if ($dynamic_template.length) {
                    show_dynamic_template($dynamic_template);
                }
            } else {
                var $dynamic_template = $body.find('[data-dynamic-template][data-visible="true"]');
                if ($dynamic_template.length) {
                    show_dynamic_template($dynamic_template);
                }
            }
        }, false);
    });
    $window.on('az-frontend-after-init', function (event, data) {
        azt.refresh_image_maps(data.wrapper);
    });
    $(function () {
        if (customize && window !== window.parent) {
            if (window.parent.azh.controls_options) {
                setTimeout(function () {
                    var name_controls = [];
                    var name_control = {};
                    window.parent.azh.controls_options.forEach(function (item, i) {
                        if ('attribute' in item && item.attribute === 'name') {
                            var selectors = item.selector.split(',');

                            name_control = $.extend({}, item);
                            name_control.type = "dropdown-attribute";
                            var keys = Object.keys(azt.fields);
                            var values = Object.keys(azt.fields);
                            var options = {};
                            options[''] = '';
                            keys.forEach((key, i) => options[key] = values[i]);
                            name_control.options = options;
                            name_control.selector = selectors.map(function (s) {
                                return '.az-template ' + s;
                            }).join(',');
                            name_controls.push(name_control);

                            item.not_selector = selectors.map(function (s) {
                                return '.az-template ' + s;
                            }).join(',');
                        }
                    });
                    window.parent.azh.controls_options = name_controls.concat(window.parent.azh.controls_options);
                });
            }
        }
    });
})(window.jQuery);
