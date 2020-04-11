(function($) {

    "use strict";

    $(document).ready(function() {

        // namespace
        var importer = $('.azexo-import');

        // disable submit button
        $('.button', importer).attr('disabled', 'disabled');

        // select.import change
        $('select.import', importer).change(function() {

            var val = $(this).val();

            // submit button
            if (val) {
                $('.button', importer).removeAttr('disabled');
            } else {
                $('.button', importer).attr('disabled', 'disabled');
            }

            // clear wp
            if (val == 'demo') {
                $('.row-attachments, .row-clear-wp', importer).show();
            } else {
                $('.row-attachments, .row-clear-wp', importer).hide();
            }


            // demo
            if (val == 'demo' || val == 'configuration' || val == 'pages' || val == 'azh_widgets' || val == 'taxonomies') {
                $('.row-demo', importer).show();
            } else {
                $('.row-demo', importer).hide();
            }
        });
        $('select.import', importer).trigger('change');

    });

})(jQuery);