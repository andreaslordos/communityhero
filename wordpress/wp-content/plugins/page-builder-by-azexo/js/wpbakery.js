(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    $(function () {
        $('.azh-widget-edit-button').off('click').on('click', function () {
            var $button = $(this);
            $('#vc_ui-panel-edit-element .vc_ui-panel-footer-container [data-vc-ui-element="button-save"]').trigger('click');
            setTimeout(function () {
                $.post(ajaxurl, {
                    action: 'azh_update_post',
                    post: {
                        post_content: $('#content').val(),
                        ID: $button.data('post-id')
                    }
                }, function (data) {
                    window.open($button.attr('href'), '_blank');
                });
            });
            return false;
        });
    });
})(window.jQuery);


