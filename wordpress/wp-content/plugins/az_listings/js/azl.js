(function($) {
    "use strict";
    window.azl = $.extend({}, window.azl);
    window.azl.init_maps = function() {
        window.azl = $.extend({}, {
            mapStyles: [{"featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{"color": "#333333"}]}, {"featureType": "landscape", "elementType": "all", "stylers": [{"color": "#f5f5f5"}]}, {"featureType": "poi", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "road", "elementType": "all", "stylers": [{"saturation": -100}, {"lightness": 45}]}, {"featureType": "road.highway", "elementType": "all", "stylers": [{"visibility": "simplified"}]}, {"featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{"visibility": "off"}]}, {"featureType": "transit", "elementType": "all", "stylers": [{"visibility": "off"}]}, {"featureType": "water", "elementType": "all", "stylers": [{"color": "#ffffff"}, {"visibility": "on"}]}],
            clusterStyles: [{url: azl.directory + '/images/cluster.png', height: 38, width: 42, textColor: "#ffffff"}],
            markerContent: '<div class="map-marker">' +
                    '<div class="icon">' +
                    '<img class="marker" src="' + (('markerImage' in azl) ? azl.markerImage : azl.directory + '/images/marker.png') + '">' +
                    '<div class="cat" style="background-image: url(\'{{marker_image}}\')"></div>' +
                    '</div>' +
                    '</div>',
            infoboxOptions: {
                disableAutoPan: false,
                pixelOffset: new google.maps.Size(0, 0),
                zIndex: null,
                alignBottom: true,
                boxClass: "infobox-wrapper",
                enableEventPropagation: true,
                closeBoxMargin: "0px 0px -8px 0px",
                closeBoxURL: azl.directory + "/images/close-btn.png",
                infoBoxClearance: new google.maps.Size(1, 1)
            },
            infoboxTemplate: '<div class="entry azl-location">' +
                    '<div class="entry-thumbnail">' +
                    '{{{image}}}' +
                    '{{{thumbnail}}}</div>' +
                    '<div class="entry-data">' +
                    '<div class="entry-header">' +
                    '<div class="entry-extra">{{{extra}}}</div>' +
                    '<div class="entry-title"><a href="{{url}}">{{title}}</a></div>' +
                    '<div class="entry-meta">{{{meta}}}</div>' +
                    '{{{header}}}</div>' +
                    '<div class="entry-content">{{{description}}}</div>' +
                    '<div class="entry-footer">{{{footer}}}</div>' +
                    '{{{data}}}</div>' +
                    '</div>'
        }, window.azl);
        Mustache.parse(azl.markerContent);
        Mustache.parse(azl.infoboxTemplate);

        $('.azl-map-wrapper').each(function() {
            function multiChoice(mc) {
                var cluster = mc.clusters_;
                if (cluster.length == 1 && cluster[0].markers_.length > 1) {
                    return false;
                }
                return true;
            }
            var wrapper = this;
            $(wrapper).find('.controls .fullscreen').off('click').on('click', function() {
                $(wrapper).find('.azl-map').toggleClass('fullscreen');
                google.maps.event.trigger(map, 'resize');
                window.scrollTo(0, 0);
                if ($(wrapper).find('.azl-map').is('.fullscreen'))
                    $('html, body').css('overflow', 'hidden');
                else
                    $('html, body').css('overflow', 'auto');
            });
            $(wrapper).find('.controls .locate').off('click').on('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var location = new google.maps.LatLng(parseFloat(position.coords.latitude), parseFloat(position.coords.longitude));
                        map.setCenter(location);
                        if ('load_locations' in map) {
                            map.load_locations();
                        }
                    });
                }
            });
            $(wrapper).find('.controls .zoom-in').off('click').on('click', function() {
                map.setZoom(map.getZoom() + 1);
            });
            $(wrapper).find('.controls .zoom-out').off('click').on('click', function() {
                map.setZoom(map.getZoom() - 1);
                if ('load_locations' in map) {
                    map.load_locations();
                }
            });
            if (typeof $(wrapper).find('.azl-map').data('markers') !== 'undefined') {
                var markers = $(wrapper).find('.azl-map').data('markers');
                if (typeof $(wrapper).find('.azl-map').data('markerClusterer') !== 'undefined') {
                    $(wrapper).find('.azl-map').data('markerClusterer').removeMarkers(markers);
                }
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
            }
            var map = null;
            if (typeof $(wrapper).find('.azl-map').data('map') === 'undefined') {
                map = new google.maps.Map($(wrapper).find('.azl-map').get(0), {
                    scrollwheel: false,
                    disableDefaultUI: true,
                    styles: azl.mapStyles,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
            } else {
                map = $(wrapper).find('.azl-map').data('map');
            }
            map.refresh = function() {
            }
            $(wrapper).find('.azl-map').data('map', map);
            if ($(wrapper).data('latitude') && $(wrapper).data('longitude')) {
                var location = new google.maps.LatLng(parseFloat($(wrapper).data('latitude')), parseFloat($(wrapper).data('longitude')));
                var markerContent = document.createElement('DIV');
                markerContent.innerHTML = Mustache.render(azl.markerContent, {marker_image: $(wrapper).data('marker_image')});
                var marker = new RichMarker({
                    position: location,
                    map: map,
                    content: markerContent,
                    flat: true
                });
                $(wrapper).find('.azl-map').data('markers', [marker]);
                map.refresh = function() {
                    map.setZoom(14);
                    map.setCenter(location);
                    google.maps.event.trigger(map, 'resize');
                }
                map.refresh();
            } else {
                if ('locations' in azl) {
                    var bounds = new google.maps.LatLngBounds();
                    var markers = [];
                    var activeMarker = false;
                    var lastClicked = false;
                    for (var id in azl.locations) {
                        var location = new google.maps.LatLng(parseFloat(azl.locations[id].latitude), parseFloat(azl.locations[id].longitude));
                        bounds.extend(location);

                        var markerContent = document.createElement('DIV');
                        markerContent.innerHTML = Mustache.render(azl.markerContent, azl.locations[id]);

                        var marker = new RichMarker({
                            position: location,
                            map: map,
                            content: markerContent,
                            flat: true
                        });
                        marker.post_id = id;
                        markers.push(marker);


                        (function(marker) {
                            google.maps.event.addDomListener(marker.content, 'click', function(event) {
                                function infobox_create_open(m, callback) {
                                    function infobox_create() {
                                        var boxText = document.createElement("div");
                                        boxText.innerHTML = Mustache.render(azl.infoboxTemplate, azl.locations[m.post_id]);
                                        azl.infoboxOptions.content = boxText;
                                        m.infobox = new InfoBox(azl.infoboxOptions);
                                        google.maps.event.addListener(m.infobox, 'closeclick', function() {
                                            lastClicked = 0;
                                        });
                                    }
                                    function infobox_open() {
                                        $(m.infobox.content_).find('.image[src=""]').each(function() {
                                            $(this).attr('src', $(this).data('src')).on('load', function() {
                                                m.infobox.open(map, m);
                                            });
                                        });
                                        m.infobox.open(map, m);
                                    }
                                    if (!('infobox' in m)) {
                                        if (!(m.post_id in azl.locations) || (Object.keys(azl.locations[m.post_id]).length <= 4)) {
                                            $.post(azl.ajaxurl, {
                                                'action': 'azl_get_location',
                                                'post_id': m.post_id
                                            }, function(response) {
                                                if (response && response != '') {
                                                    azl.locations[m.post_id] = JSON.parse(response);
                                                    infobox_create();
                                                    infobox_open();
                                                    callback();
                                                }
                                            });
                                        } else {
                                            infobox_create();
                                            infobox_open();
                                            callback();
                                        }
                                    } else {
                                        infobox_open();
                                        callback();
                                    }
                                }
                                activeMarker = marker;
                                if (activeMarker != lastClicked) {
                                    for (var j = 0; j < markers.length; j++) {
                                        if ('infobox' in markers[j]) {
                                            markers[j].infobox.close();
                                        }
                                    }
                                    infobox_create_open(marker, function() {
                                        setTimeout(function() {
                                            if (marker.infobox.pixelOffset_.width == 0) {
                                                marker.infobox.setOptions({pixelOffset: new google.maps.Size(-$(marker.infobox.content_).width() / 2, 0)});
                                                infobox_create_open(marker, function() {
                                                });
                                            }
                                        }, 0);
                                    });
                                }
                                lastClicked = marker;
                                if (event.stopPropagation)
                                    event.stopPropagation();
                                event.returnValue = false;
                            });
                        })(marker);
                    }
                    $(wrapper).find('.azl-map').data('markers', markers);
                    map.refresh = function() {
                        google.maps.event.trigger(map, 'resize');
                        map.fitBounds(bounds);
                    }
                    map.refresh();
                    map.load_locations = function() {
                        $.post(azl.ajaxurl, {
                            'action': 'azl_get_locations',
                            'southwest_latitude': map.getBounds().getSouthWest().lat(),
                            'southwest_longitude': map.getBounds().getSouthWest().lng(),
                            'northeast_latitude': map.getBounds().getNorthEast().lat(),
                            'northeast_longitude': map.getBounds().getNorthEast().lng(),
                            'query': $(wrapper).find('.azl-map').data('query')
                        }, function(response) {
                            if (response && response != '') {
                                var length = Object.keys(azl.locations).length;
                                azl.locations = $.extend(JSON.parse(response), azl.locations);
                                if (Object.keys(azl.locations).length > length) {
                                    azl.init_maps();
                                }
                            }
                        });
                    }
                    google.maps.event.clearListeners(map, 'dragend');
                    google.maps.event.addListener(map, 'dragend', function(event) {
                        map.load_locations();
                    });
                    google.maps.event.clearListeners(map, 'click');
                    google.maps.event.addListener(map, 'click', function(event) {
                        if (activeMarker != false) {
                            setTimeout(function() {
                                if ('infobox' in activeMarker) {
                                    activeMarker.infobox.close();
                                }
                                lastClicked = 0;
                            }, 0);
                        }
                    });
                    google.maps.event.clearListeners(map, 'bounds_changed');
                    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                        if (markers.length == 1)
                            if (this.getZoom()) {
                                this.setZoom(14);
                            }
                    });
                    var markerClusterer = new MarkerClusterer(map, markers, {
                        styles: azl.clusterStyles,
                        maxZoom: 19
                    });
                    $(wrapper).find('.azl-map').data('markerClusterer', markerClusterer);
                    markerClusterer.onClick = function(clickedClusterIcon, sameLatitude, sameLongitude) {
                        return multiChoice(sameLatitude, sameLongitude);
                    };
                }
            }
        });
    }
    window.azl.list_and_map = function() {
        if ($('#content > ul.products').length) {
            $('#colophon').hide();
            $('#tertiary > *').hide();
            $(function() {
                setTimeout(function() {
                    $("#tertiary .sidebar-inner").trigger("sticky_kit:recalc");
                    $("#tertiary .sidebar-inner").trigger("sticky_kit:detach");
                }, 0);
            });
            setTimeout(function() {
                var map_wrapper = $('.azl-map-wrapper.all').detach();
                $('#tertiary').append(map_wrapper);
                $('#main').css('margin', '0');
                $('#main').css('top', $('#main').offset().top + 'px');
                $('#main').addClass('fixed');
                $('#main > .container').removeClass('container');
                $(map_wrapper).find('.azl-map').data('map').refresh();
                if ('infinitescroll' in $.fn) {
                    var content = $('#content');
                    $.infinitescroll.prototype._nearbottom = function() {
                        var opts = this.options;
                        var pixels = $(content).scrollTop() - $(content).height();
                        if ($(content).get(0).scrollHeight == $(content).get(0).clientHeight) {
                            pixels = 0 + $(document).height() - (opts.binder.scrollTop()) - $(window).height();
                        }
                        this._debug('math:', pixels, opts.pixelsFromNavToBottom);
                        return (pixels - opts.bufferPx < opts.pixelsFromNavToBottom);
                    }
                    $('#content').on('smartscroll.azl', function() {
                        if ($('#content.infinite-scroll > ul.products').data('infinitescroll'))
                            $('#content.infinite-scroll > ul.products').data('infinitescroll').scroll();
                    });
                    $('#content').trigger('smartscroll.azl');
                    if ('resizable' in $.fn) {
                        $('#primary').resizable({
                            handles: 'e',
                            resize: function(event, ui) {
                                $('#tertiary').width(($('#main').width() - ui.size.width) + 'px');
                                $('#tertiary').css('left', 'auto');
                                $(map_wrapper).find('.azl-map').data('map').refresh();
                            }
                        });
                    }
                    $(document).on('triggered.azexo', function() {
                        $(map_wrapper).find('.azl-map').data('map').refresh();
                    });
                }
            }, 0);
        }
    };
    window.azl.validate_form = function($container) {
        var form_valid = true;
        $container.find('.validation-error').remove();
        $container.find('select, input, textarea').each(function() {
            var control = this;
            var valid = true;
            if ($(control).hasAttr('data-validation')) {
                if (!($(control).hasAttr('data-optional') && $(control).val() == '')) {
                    var validations = $(control).data('validation').split('|');
                    for (var i = 0; i < validations.length; i++) {
                        switch (validations[i]) {
                            case 'length_conditional' :
                                if ($(control).val() !== '') {
                                    var num = parseInt($($(control).data('field_number_val')).val());
                                    if ($(control).val().split(/\r*\n/).length != num) {
                                        valid = false;
                                    }
                                }
                                break;
                            case 'conditional' :
                                if ($(control).val() == '' && $('#' + $(control).data('conditional-field')).val() == '') {
                                    valid = false;
                                }
                                break;
                            case 'required' :
                                if ($(control).val() == '') {
                                    valid = false;
                                }
                                break;
                            case 'int' :
                                if (isNaN(parseInt($(control).val()))) {
                                    valid = false;
                                }
                                break;
                            case 'float' :
                                if (isNaN(parseFloat($(control).val()))) {
                                    valid = false;
                                }
                                break;
                            case 'email' :
                                if (!/\S+@\S+\.\S+/.test($(control).val())) {
                                    valid = false;
                                }
                                break;
                            case 'match' :
                                if ($(control).val() !== $('input[name="' + $(control).data('match') + '"]').val()) {
                                    valid = false;
                                }
                                break;
                            case 'checked' :
                                if (!$(control).prop('checked')) {
                                    valid = false;
                                }
                                break;
                        }
                    }
                    if (!valid) {
                        $(control).parent().prepend('<div class="validation-error">' + $(control).data('error') + '</div>');
                        while ($(control).parent().find('.validation-error ~ .validation-error').length) {
                            $(control).parent().find('.validation-error ~ .validation-error').remove();
                        }
                        form_valid = false;
                    }
                }
            }
        });
        return form_valid;
    };

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
    $(function() {
        $('input[multiple]').each(function() {
            $(this).attr('name', $(this).attr('name') + '[]');
        });

        $.fn.hasAttr = function(name) {
            return this.attr(name) !== undefined;
        };
        if ($('form.cmb-form').length && 'tabs' in $.fn) {
            var form = $('form.cmb-form');
            var tabs = false;
            if ($(form).find('.cmb-field-list > .cmb-type-title').length) {
                tabs = {};
                while ($(form).find('.cmb-field-list > .cmb-type-title').length) {
                    var title = $(form).find('.cmb-field-list > .cmb-type-title').get(0);
                    var id = /cmb2-id-([\w_-]+)/.exec($(title).attr('class'))[1];
                    var classes = $(title).find('.cmb-td > h5').attr('class');
                    tabs[id] = {
                        title: $(title).find('.cmb-td > h5').text(),
                        desc: $(title).find('.cmb2-metabox-description').text()
                    };
                    var fields = [];
                    var field = title;
                    do {
                        field = $(field).next().get(0);
                        if (field && !$(field).is('.cmb-type-title')) {
                            fields.push(field);
                        }
                    } while (field && !$(field).is('.cmb-type-title'));
                    $(fields).wrapAll('<div id="' + id + '" class="tab-content ' + classes + '" />');
                    $(title).remove();
                }
                var tabs_menu = $('<ul></ul>').prependTo($(form).find('.cmb-field-list'));
                for (var id in tabs) {
                    $(tabs_menu).append('<li><a href="#' + id + '">' + tabs[id].title + '</a><p>' + tabs[id].desc + '</p></li>');
                }
            }

            while ($(form).find('.cmb-type-wrapper').length) {
                var fields = [];
                var group = $(form).find('.cmb-type-wrapper').get(0);
                var field = group;
                do {
                    field = $(field).next().get(0);
                    if (field && !$(field).is('.cmb-type-wrapper')) {
                        fields.push(field);
                    }
                } while (field && !$(field).is('.cmb-type-wrapper'));
                $(fields).wrapAll('<div class="' + $(group).find('.wrapper').data('class') + '" />');
                $(group).remove();
            }


            var submit = $(form).find('[name="submit-cmb"]');
            $(submit).wrap('<div class="buttons" />');

            var new_submission = $(form).find('input[name="object_id"]').val() == 'azl-frontend-object-id';

            if (tabs) {
                var prev = $('<input type="button" name="prev" value="' + azl_translate.previous + '" class="button prev">').insertBefore(submit);
                var next = $('<input type="button" name="next" value="' + azl_translate.next + '" class="button next">').insertBefore(submit);

                $(form).find('.cmb-field-list').tabs({
                    activate: function(event, ui) {
                        if (new_submission) {
                            if (ui.newTab.is(':first-child')) {
                                prev.hide();
                            } else {
                                prev.show();
                            }
                            if (ui.newTab.is(':last-child')) {
                                submit.show();
                                next.hide();
                            } else {
                                next.show();
                                submit.hide();
                            }
                        }
                        $(form).find('.pw-map').each(function() {
                            var mapCenter = $(this).data('map').getCenter();
                            google.maps.event.trigger($(this).data('map'), 'resize');
                            $(this).data('map').setCenter(mapCenter);
                        });
                    },
                    beforeActivate: function(event, ui) {
                        if (!azl.validate_form(ui.oldPanel)) {
                            event.preventDefault();
                        }
                    }
                });
                prev.on('click', function() {
                    $(form).find('.ui-tabs-nav .ui-tabs-active').prev().find('a').click();
                    $('html, body').animate({
                        scrollTop: $(form).offset().top - 100
                    }, 500);
                    return false;
                });
                next.on('click', function() {
                    $(form).find('.ui-tabs-nav .ui-tabs-active').next().find('a').click();
                    $('html, body').animate({
                        scrollTop: $(form).offset().top - 100
                    }, 500);
                    return false;
                });
                if (new_submission) {
                    if (Object.keys(tabs).length == 1) {
                        prev.hide();
                        next.hide();
                    } else {
                        prev.hide();
                        submit.hide();
                    }
                } else {
                    prev.hide();
                    next.hide();
                }
            }
            submit.on('click', function(event) {
                form.find('select[required], input[required], textarea[required]').each(function() {
                    if (!$(this).is(':visible')) {
                        $(this).removeAttr('required');
                    }
                });
            });
            form.on('submit', function(event) {
                if (!azl.validate_form(form)) {
                    event.preventDefault();
                }
            });
        }
        $('.azl-delete a').on('click', function() {
            if (!confirm(azl_translate.delete)) {
                return false;
            }
        });
        if ('google' in window && 'maps' in google) {
            azl.init_maps();
            $(document).on('infinitescroll', function(sender, data) {
                $(data.new_elems).each(function() {
                    var id = $(this).find('script[data-post]').data('post');
                    azl.locations[id] = JSON.parse($(this).find('script[data-post]').html());
                });
                azl.init_maps();
            });
        }
    });
})(jQuery);