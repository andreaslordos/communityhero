(function($) {
    "use strict";
    $(function() {
        $(".prices-calendar").each(function() {
            function get_date_string(date) {
                return date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
            }
            function parse_date_string(date) {
                return new Date(date.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
            }
            function input_days_data() {
                if (from_date && to_date) {
                    $(day_data).show();
                    $(day_data).find('.from-date').val(get_date_string(from_date));
                    $(day_data).find('.to-date').val(get_date_string(to_date));
                }
            }
            var calendar = $(this).find('.calendar');
            var prices = window['pc_' + $(this).find('input.prices').attr('name')];
            $(this).find('input.prices').val(JSON.stringify(prices));
            var day_data = $(this).find('.day-data');
            var from_date = null;
            var to_date = null;
            $(day_data).hide();
            $(day_data).find('button.set').on('click', function() {
                if (azl.validate_form(day_data)) {
                    if ($(day_data).find('.from-date').val() != '' && $(day_data).find('.to-date').val() != '') {

                        for (var d = parse_date_string($(day_data).find('.from-date').val()); d <= parse_date_string($(day_data).find('.to-date').val()); d.setDate(d.getDate() + 1)) {
                            prices[get_date_string(d)] = {
                                'price': $(day_data).find('.price').val(),
                            };
                        }

                        $(calendar).parent().find('input.prices').val(JSON.stringify(prices));

                        $(day_data).hide();
                        from_date = null;
                        to_date = null;
                        $(calendar).datepicker('refresh');
                    }
                }
                return false;
            });
            $(day_data).find('button.remove').on('click', function() {
                if ($(day_data).find('.from-date').val() != '' && $(day_data).find('.to-date').val() != '') {

                    for (var d = parse_date_string($(day_data).find('.from-date').val()); d <= parse_date_string($(day_data).find('.to-date').val()); d.setDate(d.getDate() + 1)) {
                        delete prices[get_date_string(d)];
                    }

                    $(calendar).parent().find('input.prices').val(JSON.stringify(prices));

                    $(day_data).hide();
                    from_date = null;
                    to_date = null;
                    $(calendar).datepicker('refresh');
                }
                return false;
            });

            var datepicker = $(calendar).datepicker({
                minDate: 0,
                numberOfMonths: [$(calendar).data('months-number'), 1],
                beforeShowDay: function(date) {
                    (function(d) {
                        setTimeout(function() {
                            $(calendar).find('tbody td[data-year="' + parseInt(d.split('-')[0], 10) + '"][data-month="' + (parseInt(d.split('-')[1], 10) - 1) + '"]').find('a:contains("' + parseInt(d.split('-')[2], 10) + '")').each(function() {
                                if (parseInt($(this).text(), 10) == parseInt(d.split('-')[2], 10)) {
                                    if (d in prices) {
                                        $(this).parent().find('.price').remove();
                                        $(this).after('<span class="price">' + prices[d].price + '</span>');
                                    }
                                }
                            });
                        }, 0)
                    })(get_date_string(date));

                    if (from_date && ((date.getTime() == from_date.getTime()) || (to_date && date >= from_date && date <= to_date))) {
                        return [true, "highlight"];
                    }

                    if (get_date_string(date) in prices) {
                        return [true, "reserved", prices[get_date_string(date)].price];
                    }
                    return [true, ""];
                },
                onSelect: function(dateText, inst) {
                    var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, dateText);
                    if (!from_date || to_date) {
                        from_date = selectedDate;
                        to_date = '';
                        $(this).datepicker();
                        $(day_data).hide();
                    } else if (selectedDate < from_date) {
                        to_date = from_date;
                        from_date = selectedDate;
                        $(calendar).datepicker('refresh');
                        input_days_data();
                    } else {
                        to_date = selectedDate;
                        $(calendar).datepicker('refresh');
                        input_days_data();
                    }
                }
            });
        });
    });
})(jQuery);