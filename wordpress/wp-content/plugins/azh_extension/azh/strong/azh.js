(function($) {
    "use strict";
    $(window).on('az-frontend-before-init', function(event, data) {
        var $wrapper = data.wrapper;
        $wrapper.find('.azen-strong-blogs-2 .azen-blogs .azen-strong-title-2 span').each(function() {
            var t = $.trim($(this).text()).split(' ');
            if (t.length > 1) {
                t[0] = '<span>' + t[0] + '</span>';
                $(this).html(t.join(' '));
            }
        });
    });
})(jQuery);