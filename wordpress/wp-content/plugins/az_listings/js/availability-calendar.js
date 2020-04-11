(function($) {
    "use strict";
    $(function() {
        $(".availability-calendar").each(function() {
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
            function add_notes_to_days() {
                $(calendar).find('tbody td .notes').remove();
                $(calendar).find('tbody td').each(function() {
                    var title = $(this).attr('title');
                    if (title != undefined) {
                        $(this).append('<span class="notes">' + title + '<span>');
                    }
                });
            }
            var calendar = $(this).find('.calendar');
            var availability = window['ac_' + $(this).find('input.availability').attr('name')];
            $(this).find('input.availability').val(JSON.stringify(availability));
            var day_data = $(this).find('.day-data');
            var from_date = null;
            var to_date = null;
            $(day_data).hide();
            $(day_data).find('button.reserve').on('click', function() {
                if ($(day_data).find('.from-date').val() != '' && $(day_data).find('.to-date').val() != '') {

                    for (var d = parse_date_string($(day_data).find('.from-date').val()); d <= parse_date_string($(day_data).find('.to-date').val()); d.setDate(d.getDate() + 1)) {
                        availability[get_date_string(d)] = {
                            'notes': $(day_data).find('.notes').val(),
                        };
                    }

                    $(calendar).parent().find('input.availability').val(JSON.stringify(availability));

                    $(day_data).hide();
                    from_date = null;
                    to_date = null;
                    $(calendar).datepicker('refresh');
                }
                return false;
            });
            $(day_data).find('button.dereserve').on('click', function() {
                if ($(day_data).find('.from-date').val() != '' && $(day_data).find('.to-date').val() != '') {

                    for (var d = parse_date_string($(day_data).find('.from-date').val()); d <= parse_date_string($(day_data).find('.to-date').val()); d.setDate(d.getDate() + 1)) {
                        delete availability[get_date_string(d)];
                    }

                    $(calendar).parent().find('input.availability').val(JSON.stringify(availability));

                    $(day_data).hide();
                    from_date = null;
                    to_date = null;
                    $(calendar).datepicker('refresh');
                }
                return false;
            });

            $(calendar).datepicker({
                minDate: 0,
                numberOfMonths: [$(calendar).data('months-number'), 1],
                beforeShow: function(input, inst) {
                },
                beforeShowDay: function(date) {
                    if (from_date && ((date.getTime() == from_date.getTime()) || (to_date && date >= from_date && date <= to_date))) {
                        return [true, "highlight"];
                    }
                    if(get_date_string(date) in availability) {
                        return [true, "reserved", availability[get_date_string(date)].notes];
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
            }).tooltip({show: {delay: 0}, position: {my: "top", at: "bottom"}});
        });
    });
})(jQuery);