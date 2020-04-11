(function($) {
    "use strict";
    $(window).on('az-frontend-before-init', function(event, data) {
        var $wrapper = data.wrapper;
        $wrapper.find('.azen-listino-start').each(function() {
            $(this).on('az-full-width', function(event, data) {
                $(this).find('.az-owl-nav').width(data.container_width);
            });
        });
        $(window).trigger('resize');
    });
})(jQuery);