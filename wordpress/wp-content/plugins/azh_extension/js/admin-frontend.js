(function($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    $(function() {
        function please_wait_page_reload() {
            var $modal = $('<div class="azh-modal"></div>');
            $('<div class="azh-modal-title">' + azh.i18n.please_wait_page_reload + '</div>').appendTo($modal);
            $('<div class="azh-modal-desc"></div>').appendTo($modal);
            $modal.simplemodal({
                autoResize: true,
                overlayClose: true,
                opacity: 0,
                overlayCss: {
                    "background-color": "black"
                },
                closeClass: "azh-close",
                onClose: function() {
                }
            });
        }
        $window.on('azh-library-init', function() {
            $('<div class="azh-accent-colors" title="' + azh.i18n.accent_colors + '"><span class="dashicons-admin-customizer"></span></div>').insertAfter('.azh-library-actions .azh-builder').on('click', function() {
                $('#azexo-html-library').find('.azh-active').removeClass('azh-active');
                var tab = $.trim($(this).attr('class'));
                $(this).addClass('azh-active');
                $('#azexo-html-library > .' + tab).addClass('azh-active');
            });
            var $tab = $('<div class="azh-panel azh-accent-colors"></div>').insertAfter('.azh-library-actions');

            var $control = $('<div class="azh-control"></div>').appendTo($tab);
            $('<label>' + azh.i18n.brand_color + '</label>').appendTo($control);
            $('<input type="color" value="' + azh.brand_color + '"/>').appendTo($control).on('change', function() {
                var $input = $(this);
                $('.azh-library-actions .azh-save').trigger('click');
                please_wait_page_reload();
                $window.one('azh-saved', function() {
                    $.post(azh.ajaxurl, {
                        action: 'azh_update_post',
                        post: {
                            ID: azh.post_id
                        },
                        meta: {
                            '_brand-color': $input.val()
                        }
                    }, function(data) {
                        location.reload();
                    });
                });
            });

            var $control = $('<div class="azh-control"></div>').appendTo($tab);
            $('<label>' + azh.i18n.accent_1_color + '</label>').appendTo($control);
            $('<input type="color" value="' + azh.accent_1_color + '"/>').appendTo($control).on('change', function() {
                var $input = $(this);
                $('.azh-library-actions .azh-save').trigger('click');
                please_wait_page_reload();
                $window.one('azh-saved', function() {
                    $.post(azh.ajaxurl, {
                        action: 'azh_update_post',
                        post: {
                            ID: azh.post_id
                        },
                        meta: {
                            '_accent-1-color': $input.val()
                        }
                    }, function(data) {
                        location.reload();
                    });
                });
            });

            var $control = $('<div class="azh-control"></div>').appendTo($tab);
            $('<label>' + azh.i18n.accent_2_color + '</label>').appendTo($control);
            $('<input type="color" value="' + azh.accent_2_color + '"/>').appendTo($control).on('change', function() {
                var $input = $(this);
                $('.azh-library-actions .azh-save').trigger('click');
                please_wait_page_reload();
                $window.one('azh-saved', function() {
                    $.post(azh.ajaxurl, {
                        action: 'azh_update_post',
                        post: {
                            ID: azh.post_id
                        },
                        meta: {
                            '_accent-2-color': $input.val()
                        }
                    }, function(data) {
                        location.reload();
                    });
                });
            });
        });
    });
})(jQuery);