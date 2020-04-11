(function($) {
    "use strict";
    $.QueryString = (function(a) {
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
    })(window.location.search.substr(1).split('&'));
    //merge form parameters array as single parameter with comma separated values
    $.azqf_serialize = function(form) {
        var fields = $(form).serializeArray();
        var params = {};
        $.each(fields, function(i, field) {
            if (field.name.indexOf('[]') > 0 && field.name in params) {
                if (field.value != '') {
                    params[field.name] += ',' + field.value;
                }
            } else {
                params[field.name] = field.value;
            }
        });
        fields = [];
        $.each(Object.keys(params), function(i, name) {
            fields.push({
                name: name.replace('[]', ''),
                value: params[name]
            });
        });
        fields = fields.map(function(elem) {
            if(elem.value != '') {
                return elem.name + '=' + elem.value;
            } else {
                return '';
            }            
        }).join("&");
        return fields;
    };
    $.azqf_unique_id = {
        counter: 0,
        get: function(prefix) {
            if (!prefix) {
                prefix = "uniqid";
            }
            var id = prefix + "" + $.azqf_unique_id.counter++;
            if ($("#" + id).length == 0)
                return id;
            else
                return  $.azqf_unique_id.get()

        }
    }
    $(function() {
        $('.geo-location').each(function() {
            var geo_location = this;
            var searchElement = $(geo_location).find('input[name="location"]');
            var latElement = $(geo_location).find('input[name="latitude"]');
            var lngElement = $(geo_location).find('input[name="longitude"]');
            if ('google' in window) {
                var autocomplete = new google.maps.places.Autocomplete(searchElement[0]);
                google.maps.event.addListener(autocomplete, 'place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }
                    $(latElement).val(place.geometry.location.lat());
                    $(lngElement).val(place.geometry.location.lng());
                });
            }

            $('<span class="locate"></span>').appendTo($(geo_location).find('.location')).on('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        $(latElement).val(parseFloat(position.coords.latitude));
                        $(lngElement).val(parseFloat(position.coords.longitude));
                        var geocoder = new google.maps.Geocoder;
                        var latlng = {lat: parseFloat(position.coords.latitude), lng: parseFloat(position.coords.longitude)};
                        geocoder.geocode({'location': latlng}, function(results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    $(searchElement).val(results[0].formatted_address);
                                }
                            }
                        });
                    });
                }
            });

            $(searchElement).keypress(function(event) {
                if (13 === event.keyCode) {
                    event.preventDefault();
                }
            });
            if ('slider' in $.fn) {
                $(geo_location).find(".slider").slider({
                    value: $(geo_location).find('input[name="radius"]').val(),
                    step: 1,
                    min: 1,
                    max: 100,
                    range: "min",
                    slide: function(event, ui) {
                        $(geo_location).find('span.radius').text(ui.value);
                        $(geo_location).find('input[name="radius"]').val(ui.value);
                    }
                });
            }
            $(geo_location).find('input[name="radius"]').on('change', function() {
                $(geo_location).find('span.radius').text($(this).val());
            });
        });
        $('.date-range.datepicker').each(function() {
            var field = this;
            $(field).find('input.min').datepicker({
                minDate: new Date(),
                onClose: function(selectedDate) {
                    $(field).find('input.max').datepicker("option", "minDate", selectedDate);
                }
            });
            $(field).find('input.max').datepicker({
                minDate: new Date(),
                onClose: function(selectedDate) {
                    $(field).find('input.min').datepicker("option", "maxDate", selectedDate);
                }
            });
        });
        $('.number-range.slider').each(function() {
            var field = this;
            $(field).find('.slider').slider({
                range: true,
                step: $(field).find('.slider').data('step'),
                min: $(field).find('.slider').data('min'),
                max: $(field).find('.slider').data('max'),
                values: [$(field).find('input.min').val(), $(field).find('input.max').val()],
                slide: function(event, ui) {
                    $(field).find('input.min').val(ui.values[0]);
                    $(field).find('input.max').val(ui.values[1]);
                }
            });
        });
        $('.date-range-dropdown').each(function() {
            var field = this;
            $(field).find('select').on('change', function() {
                if ($(this).val() != '') {
                    var rule = $(this).find(':selected').data('rule');
                    if (rule) {
                        rule = rule.split('|');
                        $(field).find('input.min').val(rule[0]);
                        $(field).find('input.max').val(rule[1]);
                    }
                }
            });
            $(field).find('select').trigger('change');
        });
        $('.number-range-radio').each(function() {
            var field = this;
            $(field).find('input[type="radio"]').on('change', function() {
                if ($(this).val() != '') {
                    var rule = $(field).find(':checked').data('rule');
                    if (rule) {
                        rule = rule.split('|');
                        $(field).find('input.min').val(rule[0]);
                        $(field).find('input.max').val(rule[1]);
                    }
                }
            });
            $(field).find('input[type="radio"]:checked').trigger('change');
        });
        $('form.azqf-query-form').each(function() {
            var form = this;
            while ($(form).find('.group').length) {
                var fields = [];
                var group = $(form).find('.group').get(0);
                var field = group;
                do {
                    field = $(field).next().get(0);
                    if (field && !$(field).is('.group')) {
                        fields.push(field);
                    }
                } while (field && !$(field).is('.group'));
                var wrapper = $(fields).wrapAll('<div class="' + $(group).data('class') + '" />').parent();
                if ($(group).data('label') && $(group).data('label') != '') {
                    var id = $.azqf_unique_id.get();
                    $(wrapper).wrap("<div class='section'></div>").parent().prepend('<input id="' + id + '" type="checkbox" style="position: absolute; clip: rect(0, 0, 0, 0);"><div></div><label for="' + id + '">' + $(group).data('label') + '</label>');
                }
                $(group).remove();
            }
            $(form).find('[data-taxonomy]').each(function() {
                var field = this;
                var taxonomy = $(this).data('taxonomy');
                if (taxonomy in $.QueryString) {
                    var values = $.QueryString[taxonomy].split(',');
                    $(values).each(function() {
                        $(field).find('input[value="' + this + '"]').prop('checked', true);
                    });
                }
            });
            $(form).on('submit', function(event) {
                if($(form).find('.geo-location').length) {
                    if($(form).find('.geo-location input[name="location"]').val() && !($(form).find('.geo-location input[name="latitude"]').val() && $(form).find('.geo-location input[name="longitude"]').val())) {
                        return false;
                    }                                        
                }
                window.location.href = $(form).attr('action') + '?' + $.azqf_serialize(form);
                return false;
            });
            $(form).find('.toggle').on('click', function() {
                if ($(form).find('.wrapper').is(':visible')) {
                    $(form).find('.wrapper').slideUp(200);
                } else {
                    $(form).find('.wrapper').slideDown(200);
                }
            });
        });
        $('form.azqf-query-form .alert a').on('click', function() {
            var alert = $(this).closest('.alert');
            var email = $(alert).find('.email');
            if (email.length) {
                if ($(email).val() == '' || !$(email).get(0).checkValidity()) {
                    $(email).addClass('highlight');
                    if ('reportValidity' in $(email).get(0)) {
                        $(email).get(0).reportValidity();
                    }
                    return false;
                } else {
                    $(email).removeClass('highlight');
                }
            }
            $.post(azqf.ajaxurl, {
                'action': 'azqf_alert',
                'name': $(this).closest('form.azqf-query-form').data('name'),
                'email': $(email).val(),
                'query': $.azqf_serialize($(this).closest('form.azqf-query-form'))
            }, function(response) {
                if (response && response != '') {
                    $(alert).addClass('done');
                    $(alert).append('<div class="response">' + response + '</div>');
                }
            });
            return false;
        });
        $('.azqf-saved-searches a.remove').on('click', function() {
            var form = $(this).closest('.form');
            $.post(azqf.ajaxurl, {
                'action': 'azqf_alert_remove',
                'name': $(form).data('name'),
                'query': $(form).data('query')
            }, function(response) {
                $(form).remove();
            });
            return false;
        });
    });
})(jQuery);