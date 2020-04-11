(function($) {
    "use strict";
    $(function() {
        function create_editor(parent) {
            var unique_id_property = {"title": "Unique ID", "type": "string", "format": "not-query-var", "description": "Unique ID for HTML rendering and address value in POST"};
            var show_option_none_property = {"title": "None option text", "type": "string", description: 'Helper text for none selected value'};
            var taxonomy_property = {"title": "Taxonomy", "type": "string", required: true, "enum": Object.keys(azl.taxonomies), description: "", "options": {"enum_titles": $.map(azl.taxonomies, function(val, key) {
                        return val;
                    })}};
            var predefined_properties = {
                id: {"propertyOrder": 1, "title": "Key", required: true, "type": "string", "format": "not-query-var", "description": "Meta key to save to"},
                name: {"propertyOrder": 2, "title": "Name", required: true, "type": "string", "description": "Name showed as label before field control"},
                desc: {"propertyOrder": 3, "title": "Description", "type": "string", "description": "Description showed as text paragraph after field control", "options": {"input_width": "500px"}},
                required: {"propertyOrder": 4, "title": "Required", "type": "boolean", "format": "checkbox", description: "Check field required on server side"},
                attributes: {
                    "propertyOrder": 5,
                    "title": "Attributes for HTML element",
                    "type": "object",
                    "options": {"remove_empty_properties": true},
                    "properties": {
                        "required": {"propertyOrder": 1, "type": "string", "options": {"hidden": true}},
                        "class": {"propertyOrder": 2, "title": "CSS classes", "type": "string", description: "CSS classes override"},
                        "data-validation": {"propertyOrder": 3, "title": "Validation", "type": "string", description: 'Validation rule ("required", "int", "float", "email", "checked", "length_conditional"). Can be combined with "|" separator'},
                        "data-field_number_val": {"propertyOrder": 4, "title": "Length conditional field", "type": "string", description: 'Selector to field with integer value'},
                        "data-error": {"propertyOrder": 6, "title": "Error message", "type": "string", "description": "Error message if value not valid", "options": {"input_width": "500px"}}
                    },
                    "additionalProperties": false
                }
            };
            if ($(parent).data('editor') === undefined) {
                $(parent).find('.json-editor').remove();
                $(parent).find(' > *').hide();
                var element = $('<div class="json-editor"></div>').appendTo(parent).get(0);
                var schema = {
                    "type": "array",
                    "title": "Fields",
                    "uniqueItems": true,
                    items: {
                        "title": "Field",
                        "oneOf": [
                            {
                                "title": "Hidden field",
                                "type": "object",
                                "properties": {
                                    "type": {"type": "string", required: true, default: "hidden", "enum": ["hidden"], "options": {"hidden": true}},
                                    "id": predefined_properties.id,
                                    "default": {"title": "Value", "type": "string", "description": "Meta value"}
                                },
                                "additionalProperties": false
                            },
                            {
                                "title": "Wrapper for bottom fields (up to another wrapper)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['class']},
                                "properties": {
                                    "type": {"type": "string", required: true, default: "wrapper", "enum": ["wrapper"], "options": {"hidden": true}},
                                    "id": unique_id_property,
                                    "class": {"title": "Class", "type": "string", "description": "CSS class for wrapper"}
                                },
                                "additionalProperties": false
                            },
                            {
                                "title": "Tab wrapper for bottom fields (up to another wrapper)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc']},
                                "properties": {
                                    "type": {"type": "string", required: true, default: "title", "enum": ["title"], "options": {"hidden": true}},
                                    "id": unique_id_property,
                                    attributes: {
                                        "title": "Attributes for tab content wrapper",
                                        "type": "object",
                                        "options": {"remove_empty_properties": true},
                                        "properties": {
                                            "class": {"title": "CSS classes", "type": "string", description: "CSS classes override"},
                                        },
                                        "additionalProperties": false
                                    },
                                    "name": {"title": "Tab name", required: true, "type": "string", "description": "Name showed inside tab button"},
                                    "desc": {"title": "Tab description", "type": "string", "description": "Description showed inside tab button after name"}
                                },
                                "additionalProperties": false
                            },
                            {
                                "title": "Text",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "text", "enum": ["text"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "URL",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "text_url", "enum": ["text_url"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Number",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "text_number", "enum": ["text_number"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Money",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "text_money", "enum": ["text_money"], "options": {"hidden": true}},
                                    "before_field": {"type": "string", "template": " ", "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Textarea",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "textarea", "enum": ["textarea"], "options": {"hidden": true}},
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Date timestamp",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "text_date_timestamp", "enum": ["text_date_timestamp"], "options": {"hidden": true}},
                                    "date_format": {"type": "string", "options": {"hidden": true}}
                                }, predefined_properties, {
                                    attributes: {
                                        "properties": {
                                            "type": {"type": "string", "options": {"hidden": true}}
                                        }
                                    }
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Date/Time timestamp",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "text_datetime_timestamp", "enum": ["text_datetime_timestamp"], "options": {"hidden": true}},
                                    "before_field": {"type": "string", "template": " ", "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Taxonomy select",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "taxonomy_select", "enum": ["taxonomy_select"], "options": {"hidden": true}},
                                    "show_option_none": show_option_none_property,
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Hierarchical taxonomy select",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "hierarchical_taxonomy_select", "enum": ["hierarchical_taxonomy_select"], "options": {"hidden": true}},
                                    "show_option_none": {"type": "string", "template": " ", "options": {"hidden": true}},
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property,
                                    attributes: {
                                        "properties": {
                                            "class": {"type": "string", "template": "hierarchical", "options": {"hidden": true}},
                                            "data-placeholders": {"title": "Hierarchy levels placeholders", "type": "string", "description": 'Must be separated by " | " symbol'}
                                        }
                                    }
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Taxonomy radio inline",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "taxonomy_radio_inline", "enum": ["taxonomy_radio_inline"], "options": {"hidden": true}},
                                    "show_option_none": show_option_none_property,
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Taxonomy radio",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "taxonomy_radio", "enum": ["taxonomy_radio"], "options": {"hidden": true}},
                                    "show_option_none": show_option_none_property,
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Taxonomy multicheck",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "taxonomy_multicheck", "enum": ["taxonomy_multicheck"], "options": {"hidden": true}},
                                    "show_option_none": show_option_none_property,
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Taxonomy multicheck inline",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "taxonomy_multicheck_inline", "enum": ["taxonomy_multicheck_inline"], "options": {"hidden": true}},
                                    "show_option_none": show_option_none_property,
                                    "taxonomy": taxonomy_property
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Image",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "azl_image", "enum": ["azl_image"], "options": {"hidden": true}},
                                    "meta_key": predefined_properties.id,
                                    "options": {
                                        "type": "object",
                                        "properties": {
                                            "url": {
                                                "type": "boolean",
                                                "constant": false
                                            }
                                        },
                                        "options": {"hidden": true}
                                    }
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Images (IDs separated by comma)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "azl_images", "enum": ["azl_images"], "options": {"hidden": true}},
                                    "meta_key": predefined_properties.id,
                                    "preview_size": {
                                        "type": "array",
                                        "items": {
                                            "type": "integer",
                                        },
                                        "default": [100, 100],
                                        "options": {"hidden": true}
                                    }
                                }, predefined_properties, {
                                    id: unique_id_property
                                }),
                                "additionalProperties": false
                            },
                            {
                                "title": "Geo-location",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "pw_map", "enum": ["pw_map"], "options": {"hidden": true}},
                                    "meta_key": {"title": "Latitude,Longitude meta key", "type": "string", description: "Meta key for save latitude and longitude separated by comma"},
                                    "lat_meta_key": {"title": "Latitude meta key", required: true, "type": "string", default: "latitude", description: "Meta key for separately latitude save"},
                                    "lng_meta_key": {"title": "Longitude meta key", required: true, "type": "string", default: "longitude", description: "Meta key for separately longitude save"}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "File (only via media library)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "file", "enum": ["file"], "options": {"hidden": true}},
                                    "options": {
                                        "type": "object",
                                        "properties": {
                                            "url": {
                                                "type": "boolean",
                                                "constant": false
                                            }
                                        },
                                        "options": {"hidden": true}
                                    }
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "File (via media library or native HTML file upload)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "azl_file", "enum": ["azl_file"], "options": {"hidden": true}},
                                    "options": {
                                        "type": "object",
                                        "properties": {
                                            "url": {
                                                "type": "boolean",
                                                "constant": false
                                            }
                                        },
                                        "options": {"hidden": true}
                                    }
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Files (only via media library)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "file_list", "enum": ["file_list"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Files (via media library or native HTML file upload)",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend(true, {
                                    "type": {"type": "string", required: true, default: "azl_file", "enum": ["azl_files"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Working hours",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "working_hours", "enum": ["working_hours"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Availability calendar",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "availability_calendar", "enum": ["availability_calendar"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "Daily prices calendar",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "prices_calendar", "enum": ["prices_calendar"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            },
                            {
                                "title": "WooCommerce variations",
                                "type": "object",
                                "options": {"remove_empty_properties": ['desc', 'required', 'attributes']},
                                "properties": $.extend({
                                    "type": {"type": "string", required: true, default: "wc_variations", "enum": ["wc_variations"], "options": {"hidden": true}}
                                }, predefined_properties),
                                "additionalProperties": false
                            }
                        ]
                    }
                };
                $(document).trigger("azl-schema", schema);
                var editor = new JSONEditor(element, {
                    theme: 'jqueryui',
                    disable_edit_json: true,
                    disable_properties: true,
                    disable_collapse: true,
                    schema: schema
                });
                editor.on('change', function() {
                    $(parent).find('textarea.cmb2-textarea-code').val(JSON.stringify(editor.getValue()));
                });
                $(parent).find('textarea.cmb2-textarea-code').on('change', function() {
                    function boolean_values_fix(data) {
                        var keys = ['required', 'url'];
                        $.map(keys, function(key, i) {
                            data = data.replace(new RegExp('"' + key + '": 0,', 'g'), '"' + key + '": false,');
                            data = data.replace(new RegExp('"' + key + '": 1,', 'g'), '"' + key + '": true,');
                        });
                        return data;
                    }
                    function integer_values_fix(data) {
                        var keys = [];
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
                if (value in azl.taxonomies || azl.registered_query_vars.indexOf(value) >= 0) {
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