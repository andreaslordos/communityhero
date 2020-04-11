(function($) {
    "use strict";

    $(function() {
        $('.yt-color-picker').each(function(index, element) {
            var $el = $(this);
            var myOptions = {
                defaultColor: $el.data('std'),
                change: function(event, ui) {
                },
                clear: function() {
                },
                hide: true,
                palettes: true
            };
            $el.wpColorPicker(myOptions);
        });

        setTimeout(function() {
            if ('redux' in $ && 'ajax_save' in $.redux) {
                var redux_ajax_save = $.redux.ajax_save;
                $.redux.ajax_save = function(button) {
                    $('fieldset.redux-container-media input.upload-height, fieldset.redux-container-media input.upload-width, fieldset.redux-container-media input.upload-thumbnail').remove();
                    redux_ajax_save(button);
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
                if ('template' in $.QueryString) {
                    $('.group_title:contains("' + $.QueryString['template'] + '")').each(function() {
                        if ($(this).text() == $.QueryString['template']) {
                            $(this).closest('a').trigger('click');
                        }
                    });
                }
            }
            if (!('framework_confirmation' in azexo) || azexo.framework_confirmation == '0') {
                $('.redux-group-tab-link-a').on('click', function() {
                    if ($(this).find('.group_title').text() == azexo.templates_configuration ||
                            $(this).find('.group_title').text() == azexo.fields_configuration ||
                            $(this).find('.group_title').text() == azexo.post_types_settings ||
                            $(this).find('.group_title').text() == azexo.woocommerce_templates_configuration) {
                        alert(azexo.section_alert);
                    }
                });
                $(document).on('click', function(event) {
                    if ($(event.target).closest('.vc_control-btn-edit, [data-vc-control="edit"]').length) {
                        $(event.target).closest('[data-model-id]').each(function() {
                            if ($(this).is('[data-element_type*="azexo"]')) {
                                alert(azexo.element_alert);
                            }
                        });
                    }
                });
            }
        }, 0);
    });
})(jQuery);
