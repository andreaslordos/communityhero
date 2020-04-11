(function($) {
    "use strict";
    $(function() {
        $('a.printable-coupon[data-coupon]').on('click', function() {
            $('body').find('.printable-coupon').remove();
            $('body').addClass('print-coupon');
            $('<img src="' + $(this).data('coupon') + '" class="printable-coupon">').css('display', 'none').appendTo('body');
        });
        $('a.coupon[data-code]').on('click', function() {
            var coupon = this;
            if ($(coupon).hasClass('copied')) {
                if ($(coupon).attr('href') == '#') {
                    return false;
                }
            } else {
                var code = $('<div class="code">' + $(coupon).data('code') + '</div>').insertAfter(coupon);
                $(coupon).addClass('copied');
                $(coupon).closest('.coupon-wrapper').addClass('copied');
                try {
                    window.getSelection().removeAllRanges();
                    var range = document.createRange();
                    range.selectNode($(code).get(0));
                    window.getSelection().addRange(range);
                    if (document.execCommand('copy')) {
                        $(coupon).text($(coupon).data('copied'));
                        alert($(coupon).data('copied'));
                    }
                } catch (err) {
                }
            }
        });
        $('.share-for-coupon').each(function() {
            var field = this;
            $(field).find('.entry-share a').on('click', function() {
                setTimeout(function() {
                    var coupon = $(field).find('.coupon');
                    var code = $('<div class="code">' + $(coupon).data('code') + '</div>').insertAfter(coupon);
                    try {
                        window.getSelection().removeAllRanges();
                        var range = document.createRange();
                        range.selectNode($(code).get(0));
                        window.getSelection().addRange(range);
                        if (document.execCommand('copy')) {
                            $(coupon).text($(coupon).data('copied'));
                        }
                    } catch (err) {
                    }
                }, 3000);
            });
        });
        if ('datepicker' in $.fn) {
            $('.azot-reservation-date').datepicker();
        }
    });
})(jQuery);