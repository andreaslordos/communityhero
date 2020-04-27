/**
 * plugin javascript
 */
(function($){$(function () {

    $('input[name=custom_fields_only_list], input[name=custom_fields_except_list]').on('change', function(){

        var $fields = $(this).val().split(',');

        $('.wp_all_import_deprecated_notice').remove();

        if ( $.inArray( '_featured', $fields ) !== -1 || $.inArray( '_visibility', $fields ) !== -1 ){
            var $deprecated_fields_notice   = $('.wp_all_import_woocommerce_deprecated_fields_notice_template').clone();
            $deprecated_fields_notice.insertBefore($(this)).css('display', 'none').removeClass('wp_all_import_woocommerce_deprecated_fields_notice_template').addClass('wp_all_import_deprecated_notice').fadeIn();
        }

        if ( $.inArray( '_stock_status', $fields ) !== -1 ){
            var $stock_status_fields_notice = $('.wp_all_import_woocommerce_stock_status_notice_template').clone();
            $stock_status_fields_notice.insertBefore($(this)).css('display', 'none').removeClass('wp_all_import_woocommerce_stock_status_notice_template').addClass('wp_all_import_deprecated_notice').fadeIn();
        }

    });

});})(jQuery);