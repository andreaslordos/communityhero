(function ($) {
    "use strict";
    var $window = $(window);
    var $body = $('body');
    var $document = $(document);
    $(function () {
        if (elementor) {
            elementor.channels.data.on('element:before:remove', function (model) {
                if (model && model.attributes && 'widgetType' in model.attributes) {
                    //alert('remove ' + model.attributes.settings.attributes['post-id']);
                    if (model.attributes.widgetType === "azexo_form" || model.attributes.widgetType === "azexo_container" || model.attributes.widgetType.indexOf('azh-') === 0) {
                        $.post(window.ajaxurl, {
                            action: 'azh_remove_post',
                            post_id: model.attributes.settings.attributes['post-id']
                        });
                    }
                }
            });
            elementor.channels.data.on('element:after:add', function (itemData) {
                if (itemData && 'widgetType' in itemData) {
                    if (itemData.widgetType === "azexo_form" || itemData.widgetType === "azexo_container" || itemData.widgetType.indexOf('azh-') === 0) {
                        if ('settings' in itemData && Object.keys(itemData.settings).length) {
                            $.post(window.ajaxurl, {
                                action: 'azh_duplicate_post',
                                post_id: itemData.settings['post-id']
                            }, function (data) {
                                //alert('duplicate ' + data);
                                itemData.settings['post-id'] = data;
                                var $button = $('.azh-elementor-button');
                                if ($button.length) {
                                    var $input = $button.closest('.elementor-control-field').find('.azexo-post-id');
                                    var old_id = $input.val();
                                    $input.val(data).trigger('input');
                                    $button.closest('.elementor-control-field').find('[data-url]').each(function () {
                                        var $this = $(this);
                                        var url = $this.attr('data-url');
                                        url = url.replace(old_id, data);
                                        $this.attr('data-url', url);
                                    });
                                }
                            });
                        } else {
                            setTimeout(function () {
                                var $button = $('.azh-elementor-button');
                                if ($button.length) {
                                    var $input = $button.closest('.elementor-control-field').find('.azexo-post-id');
                                    var url = $button.attr('data-url');
                                    if (url.indexOf('{{{data.controlValue}}}') > 0) {
                                        $.post(window.ajaxurl, {
                                            action: 'azh_add_post',
                                            post_title: 'Elementor widget',
                                            element: $button.attr('data-element'),
                                            post_status: 'hidden',
                                            post_type: 'azh_widget'
                                        }, function (data) {
                                            //alert('add ' + data);
                                            $input.val(data).trigger('input');
                                            url = url.replace('{{{data.controlValue}}}', data);
                                            $button.closest('.elementor-control-field').find('[data-url]').each(function () {
                                                var $this = $(this);
                                                var url = $this.attr('data-url');
                                                url = url.replace('{{{data.controlValue}}}', data);
                                                $this.attr('data-url', url);
                                            });
                                        });
                                    }
                                }
                            });
                        }
                    }
                }
            });
            var button = false;
            elementor.saver.on('after:save', function () {
                var $button = $(button);
                if ($button.length) {
                    var $input = $button.closest('.elementor-control-field').find('.azexo-post-id');
                    var url = $button.attr('data-url');
                    if (url.indexOf('{{{data.controlValue}}}') > 0) {
                        $.post(window.ajaxurl, {
                            action: 'azh_add_post',
                            post_title: 'Elementor widget',
                            element: $button.attr('data-element'),
                            post_status: 'hidden',
                            post_type: 'azh_widget'
                        }, function (data) {
                            $input.val(data).trigger('input');
                            url = url.replace('{{{data.controlValue}}}', data);
                            $button.closest('.elementor-control-field').find('[data-url]').each(function () {
                                var $this = $(this);
                                var url = $this.attr('data-url');
                                url = url.replace('{{{data.controlValue}}}', data);
                                $this.attr('data-url', url);
                            });
                            elementor.saver.update();
                            var azh_window = window.open(url);
                            azh_window.addEventListener("DOMContentLoaded", function () {
                                azh_window.$(azh_window).on('azh-saved', function () {
                                    elementor.reloadPreview();
                                });
                            });
                        });
                    } else {
                        var azh_window = window.open(url);
                        azh_window.addEventListener("DOMContentLoaded", function () {
                            azh_window.$(azh_window).on('azh-saved', function () {
                                elementor.reloadPreview();
                            });
                        });
                    }
                }
                button = false;
            });
            window.azh_elementor_click = function(current_button) {
                button = current_button;
                elementor.saver.update();
            }
        }
    });
})(window.jQuery);


