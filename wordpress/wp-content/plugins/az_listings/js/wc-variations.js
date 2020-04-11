(function($) {
    "use strict";
    $(function() {
        $(".wc-variations").each(function() {
            function init_sales_dates(variation) {
                $(variation).find('input.sale-start-date').datepicker({
                    minDate: new Date(),
                    onClose: function(selectedDate) {
                        $(variation).find('input.sale-end-date').datepicker("option", "minDate", selectedDate);
                    }
                }).on('change', function(){
                    $(variation).find('input.sale-end-date').datepicker("option", "minDate", $(variation).find('input.sale-start-date').val());
                });
                
                $(variation).find('input.sale-end-date').datepicker({
                    minDate: new Date(),
                    onClose: function(selectedDate) {
                        $(variation).find('input.sale-start-date').datepicker("option", "maxDate", selectedDate);
                    }
                }).on('change', function(){
                    $(variation).find('input.sale-start-date').datepicker("option", "maxDate", $(variation).find('input.sale-end-date').val());
                });
            }
            function get_attributes() {
                var attributes = {};
                $(wc_variations).find('.attributes div.attribute').each(function() {
                    var name = $(this).find('input.name').val();
                    attributes[name] = [];
                    $(this).find('.values div.value').each(function() {
                        attributes[name].push($(this).find('input.value').val());
                    });
                });
                for (var name in attributes) {
                    if (Object.keys(attributes[name]).length == 0) {
                        delete attributes[name];
                    }
                }
                return attributes;
            }
            function get_attributes_combinations(attributes, path) {
                var variations = [];
                if (Object.keys(attributes).length == Object.keys(path).length && Object.keys(path).length > 0) {
                    variations = [$.extend({}, path)];
                }
                for (var name in attributes) {
                    if (!(name in path)) {
                        $.each(attributes[name], function(index, value) {
                            path[name] = value;
                            variations = get_attributes_combinations(attributes, path).concat(variations);
                            delete path[name];
                        });
                        break;
                    }
                }
                return variations;
            }
            function refresh_variations() {
                var attributes = get_attributes();
                var attributes_combinations = get_attributes_combinations(attributes, {});
                var dom_variations = {};
                $(wc_variations).find('div.variations > div.variation').each(function() {
                    dom_variations[$(this).data('combination_json')] = this;
                    $(this).detach();
                });
                $.each(attributes_combinations, function(index, value) {
                    var combination_json = JSON.stringify(value);
                    if (combination_json in dom_variations) {
                        $(wc_variations).find('.variations').append(dom_variations[combination_json]);
                    } else {
                        var new_variation = $(empty_variation).clone();
                        var variation_name = '';
                        for (var attribute_name in value) {
                            if (variation_name == '') {
                                variation_name = attribute_name + ': ' + value[attribute_name];
                            } else {
                                variation_name += ', ' + attribute_name + ': ' + value[attribute_name];
                            }
                        }
                        $(new_variation).find('label span.name').text(variation_name);
                        $(new_variation).data('combination_json', combination_json);
                        $(wc_variations).find('.variations').append(new_variation);
                        $(new_variation).find('button.remove-variation').on('click', function() {
                            $(new_variation).addClass('removed');
                            $(new_variation).hide();
                            return false;
                        });
                        $(new_variation).find('.sale-schedule').hide();
                        $(new_variation).find('input.sale-price').on('change', function() {
                            if ($(this).val() == '') {
                                $(new_variation).find('.sale-schedule').hide();
                            } else {
                                $(new_variation).find('.sale-schedule').show();
                            }
                        });
                        init_sales_dates(new_variation);
                    }
                });
            }
            function get_variation_data(dom_element) {
                var variation = {};
                variation['removed'] = $(dom_element).is('.removed');
                variation['regular_price'] = $(dom_element).find('input.regular-price').val();
                variation['sale_price'] = $(dom_element).find('input.sale-price').val();
                variation['sale_start_date'] = $(dom_element).find('input.sale-start-date').val();
                variation['sale_end_date'] = $(dom_element).find('input.sale-end-date').val();
                variation['description'] = $(dom_element).find('textarea.description').val();
                return variation;
            }
            function set_variation_data(dom_element, variation) {
                if (variation['removed']) {
                    $(dom_element).addClass('removed');
                    $(dom_element).hide();
                }
                $(dom_element).find('input.regular-price').val(variation['regular_price']);
                $(dom_element).find('input.sale-price').val(variation['sale_price']).change();
                $(dom_element).find('input.sale-start-date').val(variation['sale_start_date']).change();
                $(dom_element).find('input.sale-end-date').val(variation['sale_end_date']).change();
                $(dom_element).find('textarea.description').val(variation['description']);
            }
            function variations_output() {
                var attributes = get_attributes();
                var variations = [];
                $(wc_variations).find('div.variations > div.variation').each(function() {
                    variations.push({
                        combination: JSON.parse($(this).data('combination_json')),
                        data: get_variation_data(this)
                    });
                });
                $(wc_variations).find('input.variations').val(JSON.stringify({attributes: attributes, variations: variations}));
            }
            function variations_load(variations) {
                var dom_variations = {};
                $(wc_variations).find('div.variations div.variation').each(function() {
                    dom_variations[$(this).data('combination_json')] = this;
                });
                $.each(variations, function(index, value) {
                    var combination_json = JSON.stringify(value.combination);
                    if (combination_json in dom_variations) {
                        set_variation_data(dom_variations[combination_json], value.data);
                    }
                });

            }
            function add_new_attribute(name, values) {
                var new_attribute = $(empty_attribute).clone();
                $(new_attribute).find('button.remove-attribute').on('click', function() {
                    $(new_attribute).remove();
                    refresh_variations();
                    return false;
                });
                $(new_attribute).find('input.name').val(name);
                $(new_attribute).find('input.name').on('change', function() {
                    refresh_variations();
                });
                $(new_attribute).find('.values button.add-value').on('click', function() {
                    $(new_attribute).find('.values .wrapper').append(add_new_value(''));
                    return false;
                });
                $.each(values, function(index, value) {
                    $(new_attribute).find('.values .wrapper').append(add_new_value(value));
                });
                return new_attribute;
            }
            function add_new_value(value) {
                var new_value = $(empty_value).clone();
                $(new_value).find('button.remove-value').on('click', function() {
                    $(new_value).remove();
                    refresh_variations();
                    return false;
                });
                $(new_value).find('input.value').val(value);
                $(new_value).find('input.value').on('change', function() {
                    refresh_variations();
                });
                return new_value;
            }
            var wc_variations = this;
            var variations = window['wcv_' + $(wc_variations).find('input.variations').attr('name')];
            $(wc_variations).find('input.variations').val(JSON.stringify(variations));
            var empty_attribute = $(wc_variations).find('.attributes div.attribute').detach();
            var empty_value = $(empty_attribute).find('.values div.value').detach();
            var empty_variation = $(wc_variations).find('div.variations div.variation').detach();
            $(wc_variations).find('.attributes button.add-attribute').on('click', function() {
                $(wc_variations).find('.attributes').append(add_new_attribute('', []));
                return false;
            });
            if ('attributes' in variations) {
                for (var name in variations.attributes) {
                    $(wc_variations).find('.attributes').append(add_new_attribute(name, variations.attributes[name]));
                }
                refresh_variations();
            }
            if ('variations' in variations) {
                variations_load(variations.variations);
            }
            var form = $(wc_variations).closest('form.cmb-form');
            var submit = $(form).find('[name="submit-cmb"]');
            submit.on('click', function(event) {
                variations_output();
            });
        });
    });
})(jQuery);