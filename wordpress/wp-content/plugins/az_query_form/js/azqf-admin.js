(function($) {
    "use strict";
    $(function() {
        function create_editor(parent) {
            var predefined_properties = {
                show_option_none: {"title": "Label", "type": "string", description: "Text"},
                label: {"title": "Label", "type": "string", description: "Label which will be showed in form"},
                placeholder: {"title": "Placeholder", "type": "string", description: "Placeholder inside text input box"}
            };
            if ($(parent).data('editor') === undefined) {
                $(parent).find('.json-editor').remove();
                $(parent).find('.open-form-editor').remove();
                $(parent).find(' > *').hide();
                var element = $('<div class="json-editor"></div>').appendTo(parent).get(0);
                var editor = new JSONEditor(element, {
                    theme: 'jqueryui',
                    disable_edit_json: true,
                    disable_properties: true,
                    disable_collapse: true,
                    schema: {
                        "type": "array",
                        "title": "Filters",
                        "uniqueItems": true,
                        items: {
                            "title": "Filter",
                            "oneOf": [
                                {
                                    "title": "Hidden field",
                                    "type": "object",
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "hidden", "enum": ["hidden"], "options": {"hidden": true}},
                                        "name": {"title": "Name", required: true, "type": "string", description: "Meta key"},
                                        "value": {"title": "Value", "type": "string", description: "Meta value (leave empty if need to take it from $_GET)"}
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Wrapper for bottom filters (up to another wrapper)",
                                    "type": "object",
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "group", "enum": ["group"], "options": {"hidden": true}},
                                        "label": predefined_properties.label,
                                        "class": {"title": "Class", "type": "string", description: "CSS class for wrapper"}
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Search keywords",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "search_text", "enum": ["search_text"], "options": {"hidden": true}},
                                        "label": predefined_properties.label,
                                        "placeholder": predefined_properties.placeholder
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Taxonomy dropdown",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder', 'show_option_none']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "dropdown", "enum": ["dropdown"], "options": {"hidden": true}},
                                        "class": {"type": "string", "template": "select2", "options": {"hidden": true}},
                                        "option_none_value": {"type": "string", "template": "", "options": {"hidden": true}},
                                        "show_option_none": {"title": "None option text", "type": "string", description: 'Helper text. In hierarchy mode values must be separated by "|" symbol'},
                                        "hide_empty": {"title": "Hide empty terms", "type": "boolean", "format": "checkbox", description: "Hide terms which not linked with any post"},
                                        "hierarchical": {"title": "Hierarchical", "type": "boolean", "format": "checkbox", description: "Output select control with hierarchy support"},
                                        "taxonomy": {"title": "Taxonomy", "type": "string", required: true, "enum": Object.keys(azqf.taxonomies), description: "", "options": {"enum_titles": $.map(azqf.taxonomies, function(val, key) {
                                                    return val;
                                                })}},
                                        "label": predefined_properties.label,
                                        "placeholder": predefined_properties.placeholder
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Taxonomy radio",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder', 'show_option_none']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "radio", "enum": ["radio"], "options": {"hidden": true}},
                                        "option_none_value": {"type": "string", "template": "", "options": {"hidden": true}},
                                        "show_option_none": {"title": "None option text", "type": "string", description: 'Helper text. In hierarchy mode values must be separated by "|" symbol'},
                                        "hide_empty": {"title": "Hide empty terms", "type": "boolean", "format": "checkbox", description: "Hide terms which not linked with any post"},
                                        "taxonomy": {"title": "Taxonomy", "type": "string", required: true, "enum": Object.keys(azqf.taxonomies), description: "", "options": {"enum_titles": $.map(azqf.taxonomies, function(val, key) {
                                                    return val;
                                                })}},
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Taxonomy checkboxes",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "checkboxes", "enum": ["checkboxes"], "options": {"hidden": true}},
                                        "taxonomy": {"title": "Taxonomy", "type": "string", required: true, "enum": Object.keys(azqf.taxonomies), description: "", "options": {"enum_titles": $.map(azqf.taxonomies, function(val, key) {
                                                    return val;
                                                })}},
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Geolocation",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder', 'default_radius']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "geolocation", "enum": ["geolocation"], "options": {"hidden": true}},
                                        "lat_meta_key": {"title": "Latitude meta key", required: true, "type": "string", default: "latitude", description: "Latitude meta key which will be used for calculate distance"},
                                        "lng_meta_key": {"title": "Longitude meta key", required: true, "type": "string", default: "longitude", description: "Longitude meta key which will be used for calculate distance"},
                                        "default_radius": {"title": "Default radius", "type": "string", default: "300", format: "number", description: "Default radius to filter by distance"},
                                        "units": {"title": "Radius units", "type": "string", default: "mi", "enum": ["mi", "km"], description: "Radius units to filter by distance"},
                                        "max_distance_meta_key": {"title": "Maximum distance meta key", "type": "string", default: "max_distance", description: "Maximum distance meta key which will be used for filter posts by maximum distance"},
                                        "label": predefined_properties.label,
                                        "placeholder": predefined_properties.placeholder
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Number range",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'slider_min', 'slider_max', 'default_min', 'default_max', 'step', 'mode', 'placeholder_min', 'placeholder_max']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "number_range", "enum": ["number_range"], "options": {"hidden": true}},
                                        "meta_key": {"propertyOrder": 1, "title": "Meta key", required: true, "type": "string", description: "Meta key which will be used for filtering"},
                                        "slider": {"propertyOrder": 2, "title": "Show filter as slider", "type": "boolean", "format": "checkbox", description: ""},
                                        "slider_min": {"propertyOrder": 3, "title": "Slider minimal value", "type": "integer", default: "1", description: "Minimal value which can be selected in slider"},
                                        "slider_max": {"propertyOrder": 4, "title": "Slider maximum value", "type": "integer", default: "10", description: "Maximum value which can be selected in slider"},
                                        "default_min": {"propertyOrder": 5, "title": "Default minimal value", "type": "integer", description: "Minimal value selected in filter by default"},
                                        "default_max": {"propertyOrder": 6, "title": "Default maximum value", "type": "integer", description: "Maximum value selected in filter by default"},
                                        "step": {"propertyOrder": 7, "title": "Step", "type": "integer", default: "1", description: "Possible integer step of filter values"},
                                        "mode": {"propertyOrder": 8, "title": "Mode", "type": "string", default: "between", "enum": ["between", "<", ">"], description: "Filtering mode", "options": {"enum_titles": ["Between", "Less than", "Geather than"]}},
                                        "placeholder_min": {"propertyOrder": 9, "title": "Placeholder for minimal value", "type": "string", description: predefined_properties.placeholder.description},
                                        "placeholder_max": {"propertyOrder": 10, "title": "Placeholder for maximum value", "type": "string", description: predefined_properties.placeholder.description},
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Date range dropdown",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "date_range_dropdown", "enum": ["date_range_dropdown"], "options": {"hidden": true}},
                                        "class": {"type": "string", "template": "select2", "options": {"hidden": true}},
                                        "meta_key": {"title": "Meta key", required: true, "type": "string", "format": "not-query-var", description: "Meta key which will be used for filtering"},
                                        "default": {"title": "Number of selected rule by default", required: true, "type": "integer", default: "1", description: "Number of selected rule by default"},
                                        "rules": {
                                            "title": "Rules",
                                            "type": "object",
                                            description: 'Click on "+" button and enter rule in format: <minimum date for strtotime() function>|<maximum date for strtotime() function>.',
                                            "options": {"disable_properties": false},
                                            "patternProperties": {
                                                ".*": {
                                                    "type": "string",
                                                    description: 'Enter label for this rule here',
                                                    "headerTemplate": "{{ key }}"
                                                }
                                            }
                                        },
                                        "label": predefined_properties.label,
                                        "placeholder": predefined_properties.placeholder
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Date range",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder_min', 'placeholder_max']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "date_range", "enum": ["date_range"], "options": {"hidden": true}},
                                        "datepicker": {"title": "Datepicker", "type": "boolean", "format": "checkbox", description: "Show as datepicker"},
                                        "meta_key": {"title": "Meta key", required: true, "type": "string", description: "Meta key which will be used for filtering"},
                                        "placeholder_min": {"title": "Placeholder for minimal date", "type": "string", description: predefined_properties.placeholder.description},
                                        "placeholder_max": {"title": "Placeholder for maximum date", "type": "string", description: predefined_properties.placeholder.description},
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Open now",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "open", "enum": ["open"], "options": {"hidden": true}},
                                        "meta_key": {"title": "Meta key prefix", required: true, "type": "string", default: "working-hours", description: "Meta key prefix which will be used for filtering by current hour"},
                                        "default": {"title": "Checked by default", "type": "boolean", "format": "checkbox"},
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Email alerting",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label', 'placeholder']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "alert", "enum": ["alert"], "options": {"hidden": true}},
                                        "label": predefined_properties.label,
                                        "placeholder": predefined_properties.placeholder
                                    },
                                    "additionalProperties": false
                                },
                                {
                                    "title": "Number range radio",
                                    "type": "object",
                                    "options": {"remove_empty_properties": ['label']},
                                    "properties": {
                                        "type": {"type": "string", required: true, default: "number_range_radio", "enum": ["number_range_radio"], "options": {"hidden": true}},
                                        "meta_key": {"title": "Meta key", required: true, "type": "string", "format": "not-query-var", description: "Meta key which will be used for filtering"},
                                        "default": {"title": "Number of selected rule by default", required: true, "type": "integer", default: "1", description: "Number of selected rule by default"},
                                        "rules": {
                                            "title": "Rules",
                                            "type": "object",
                                            description: 'Click on "+" button and enter rule in format: <minimum number>|<maximum number>.',
                                            "options": {"disable_properties": false},
                                            "patternProperties": {
                                                ".*": {
                                                    "type": "string",
                                                    description: 'Enter label for this rule here',
                                                    "headerTemplate": "{{ key }}"
                                                }
                                            }
                                        },
                                        "label": predefined_properties.label
                                    },
                                    "additionalProperties": false
                                }
                            ]
                        }
                    }
                });
                editor.on('change', function() {
                    $(parent).find('textarea.cmb2-textarea-code').val(JSON.stringify(editor.getValue()));
                });
                $(parent).find('textarea.cmb2-textarea-code').on('change', function() {
                    function boolean_values_fix(data) {
                        var keys = ['datepicker', 'slider', 'hide_empty', 'hierarchical'];
                        $.map(keys, function(key, i) {
                            data = data.replace(new RegExp('"' + key + '": 0,', 'g'), '"' + key + '": false,');
                            data = data.replace(new RegExp('"' + key + '": 1,', 'g'), '"' + key + '": true,');
                        });
                        return data;
                    }
                    function integer_values_fix(data) {
                        var keys = ['default', 'step', 'default_max', 'default_min', 'slider_min', 'slider_max'];
                        $.map(keys, function(key, i) {
                            data = data.replace(new RegExp('"' + key + '": "(\\d+)",', 'g'), '"' + key + '": $1,');
                        });
                        return data;
                    }
                    if ($(this).val() != '') {
                        var data = $(this).val();
                        data = boolean_values_fix(data);
                        data = integer_values_fix(data);
                        editor.setValue(JSON.parse(data));
                    }
                }).trigger('change');
                $(parent).data('editor', editor);
                return editor;
            }
        }
        function create_button(parent) {
            $(parent).find(' > *').hide();
            $(parent).find('.json-editor, .open-form-editor').remove();
            $('<a href="#" class="button button-secondary open-form-editor">Open form editor</a>').appendTo(parent).on('click', function() {
                create_editor(parent);
                return false;
            });
            $(parent).data('open-form-editor', true);
        }
        JSONEditor.defaults.custom_validators.push(function(schema, value, path) {
            var errors = [];
            if (schema.format === "not-query-var") {
                if (value in azqf.taxonomies || azqf.registered_query_vars.indexOf(value) >= 0) {
                    errors.push({
                        path: path,
                        property: 'format',
                        message: 'String must not equal to reserved query var name'
                    });
                }
            }
            return errors;
        });
        $('#forms_repeat .cmb-type-textarea-code .cmb-td').each(function() {
            create_button(this);
        });
        $('#forms_repeat .cmb-add-group-row.button').on('click', function() {
            setTimeout(function() {
                $('#forms_repeat .cmb-type-textarea-code .cmb-td').each(function() {
                    if (!$(this).data('open-form-editor')) {
                        create_button(this);
                    }
                });
            }, 0);
        });
    });
})(jQuery);