(function ($) {
    "use strict";
    function filter_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        var default_filter = "blur(0px) brightness(100%) contrast(100%) grayscale(0%) hue-rotate(0deg) invert(0%) opacity(100%) saturate(100%) sepia(0%)";
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*blur\()(\d+)(px\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-blur-filter",
                "control_type": "blur-filter",
                "control_text": "Blur"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*brightness\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-brightness-filter",
                "control_type": "brightness-filter",
                "control_text": "Brightness"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*contrast\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-contrast-filter",
                "control_type": "contrast-filter",
                "control_text": "Contrast"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*grayscale\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-grayscale-filter",
                "control_type": "grayscale-filter",
                "control_text": "Grayscale"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*hue-rotate\()(-?\d+)(deg\).*)/,
                "default": default_filter,
                "min": -180,
                "max": 180,
                "step": 1,
                "control_class": "azh-filter azh-hue-rotate-filter",
                "control_type": "hue-rotate-filter",
                "control_text": "HUE rotate"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*invert\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-invert-filter",
                "control_type": "invert-filter",
                "control_text": "Invert"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*opacity\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-opacity-filter",
                "control_type": "opacity-filter",
                "control_text": "Opacity"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*saturate\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-saturate-filter",
                "control_type": "saturate-filter",
                "control_text": "Saturate"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "filter",
                "pattern": /(.*sepia\()(\d+)(%\).*)/,
                "default": default_filter,
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-filter azh-sepia-filter",
                "control_type": "sepia-filter",
                "control_text": "Sepia"
            },
        ]);
    }
    function absolute_position_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "top",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-top",
                "control_type": "top",
                "control_text": "Top"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "bottom",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-bottom",
                "control_type": "bottom",
                "control_text": "Bottom"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "left",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-left",
                "control_type": "left",
                "control_text": "Left"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "right",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-right",
                "control_type": "right",
                "control_text": "Right"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "height",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-height",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "width",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-width",
                "control_type": "width",
                "control_text": "Width"
            },
        ]);
    }
    function transition_utility(selector, group, subgroup, refresh, rule_selector) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "selector": selector,
                "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "min": "0",
                "max": "1",
                "step": "0.05",
                "units": "s",
                "control_class": "azh-transition-duration",
                "control_type": "transition-duration",
                "control_text": "Transition duration",
                "property": "transition-duration"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "min": "0",
                "max": "1",
                "step": "0.05",
                "units": "s",
                "control_class": "azh-transition-delay",
                "control_type": "transition-delay",
                "control_text": "Transition delay",
                "property": "transition-delay"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "options": {
                    "linear": "linear",
                    "ease": "ease",
                    "ease-in": "ease-in",
                    "ease-out": "ease-out",
                    "cubic-bezier(0.2, 0.7, 0, 1)": "ease-out-1",
                    "ease-in-out": "ease-in-out",
                    "cubic-bezier(0.5, 0, 0.2, 1)": "ease-in-out-1",
                    "cubic-bezier(0.7, 0, 0.3, 1)": "ease-in-out-2",
                    "cubic-bezier(0.8, 0, 0.2, 1)": "ease-in-out-3"
                },
                "property": "transition-timing-function",
                "control_class": "azh-transition-timing-function",
                "control_type": "transition-timing-function",
                "control_text": "Transition timing function"
            }
        ]);
    }
    function background_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        background_menu('utility', selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition);
    }
    function background_menu(menu, selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "normal": "normal",
                    "multiply": "multiply",
                    "screen": "screen",
                    "overlay": "overlay",
                    "darken": "darken",
                    "lighten": "lighten",
                    "color-dodge": "color-dodge",
                    "color-burn": "color-burn",
                    "difference": "difference",
                    "exclusion": "exclusion",
                    "hue": "hue",
                    "saturation": "saturation",
                    "color": "color",
                    "luminosity": "luminosity"
                },
                "property": "mix-blend-mode",
                "control_class": "azh-mix-blend-mode",
                "control_type": "mix-blend-mode",
                "control_text": "Blend mode"
            },
            {
                "refresh": refresh,
                "type": "exists-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "isolation",
                "value": "isolate",
                "control_class": "azh-toggle azh-isolation",
                "control_type": "isolation",
                "control_text": "Blend isolation"
            },
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                    if ($control.attr('data-value') === 'classic') {
                        $control.parent().find('.azh-background-image img').trigger('contextmenu');
                    }
                    if ($control.attr('data-value') === 'gradient') {
                        $control.parent().find('.azh-background-gradient-type select').trigger('change');
                    }
                },
                "type": "radio-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "classic": "Classic",
                    "gradient": "Gradient"
                },
                "property": "background-type",
                "control_class": "azh-background-type",
                "control_type": "background-type",
                "control_text": "Background type"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-color",
                "control_class": "azh-background-color",
                "control_type": "background-color",
                "control_text": "Background color"
            },
            {
                "refresh": refresh,
                "type": "background-image",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "control_class": "azh-background-image",
                "control_type": "background-image",
                "control_text": "Background image"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "options": {
                    "": "Default",
                    "top left": "Top Left",
                    "top center": "Top Center",
                    "top right": "Top Right",
                    "center left": "Center Left",
                    "center center": "Center Center",
                    "center right": "Center Right",
                    "bottom left": "Bottom Left",
                    "bottom center": "Bottom Center",
                    "bottom right": "Bottom Right",
                    "0% 0%": "Custom"
                },
                "property": "background-position",
                "control_class": "azh-background-position",
                "control_type": "background-position",
                "control_text": "Position"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-position",
                "responsive": true,
                "pattern": /()(-?\d+[%px]+)( -?\d+[%px]+)/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-x-position",
                "control_type": "x-position",
                "control_text": "X position"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-position",
                "responsive": true,
                "pattern": /(-?\d+[%px]+ )(-?\d+[%px]+)()/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-y-position",
                "control_type": "y-position",
                "control_text": "Y position"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "scroll": "Scroll",
                    "fixed": "Fixed"
                },
                "property": "background-attachment",
                "control_class": "azh-background-attachment",
                "control_type": "background-attachment",
                "control_text": "Attachment"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "no-repeat": "No repeat",
                    "repeat": "Repeat",
                    "repeat-x": "Repeat-x",
                    "repeat-y": "Repeat-y"
                },
                "property": "background-repeat",
                "control_class": "azh-background-repeat",
                "control_type": "background-repeat",
                "control_text": "Repeat"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "auto": "Auto",
                    "cover": "Cover",
                    "contain": "Contain",
                    "100% 100%": "Custom"
                },
                "property": "background-size",
                "responsive": true,
                "control_class": "azh-background-size",
                "control_type": "background-size",
                "control_text": "Size"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-size",
                "responsive": true,
                "pattern": /()(\d+[%px]+)( \d+[%px]+)/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-width",
                "control_type": "background-width",
                "control_text": "Width"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-size",
                "responsive": true,
                "pattern": /(\d+[%px]+ )(\d+[%px]+)()/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-height",
                "control_type": "background-height",
                "control_text": "Height"
            },
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                },
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "linear-gradient": "Linear",
                    "radial-gradient": "Radial"
                },
                "property": "background-image",
                "pattern": /()([-\w]+)(\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "control_class": "azh-background-gradient-type",
                "control_type": "background-gradient-type",
                "control_text": "Type"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "top left": "Top Left",
                    "top center": "Top Center",
                    "top right": "Top Right",
                    "center left": "Center Left",
                    "center center": "Center Center",
                    "center right": "Center Right",
                    "bottom left": "Bottom Left",
                    "bottom center": "Bottom Center",
                    "bottom right": "Bottom Right"
                },
                "property": "background-image",
                "pattern": /(radial-gradient\(at )([ \w]+)(, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "control_class": "azh-background-radial-gradient-position",
                "control_type": "background-radial-gradient-position",
                "control_text": "Position"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(linear-gradient\()(\d+)(deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 360,
                "step": 1,
                "control_class": "azh-background-linear-gradient-angle",
                "control_type": "background-linear-gradient-angle",
                "control_text": "Angle"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-color",
                "control_type": "background-linear-gradient-first-color",
                "control_text": "First color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-location",
                "control_type": "background-linear-gradient-first-location",
                "control_text": "First location"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-color",
                "control_type": "background-linear-gradient-second-color",
                "control_text": "Second color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-location",
                "control_type": "background-linear-gradient-second-location",
                "control_text": "Second location"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-color",
                "control_type": "background-radial-gradient-first-color",
                "control_text": "First color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-location",
                "control_type": "background-radial-gradient-first-location",
                "control_text": "First location"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-color",
                "control_type": "background-radial-gradient-second-color",
                "control_text": "Second color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-location",
                "control_type": "background-radial-gradient-second-location",
                "control_text": "Second location"
            }
        ]);
    }
    function background_image_settings(menu, selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "background-image",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "control_class": "azh-background-image",
                "control_type": "background-image",
                "control_text": "Background image"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "options": {
                    "": "Default",
                    "top left": "Top Left",
                    "top center": "Top Center",
                    "top right": "Top Right",
                    "center left": "Center Left",
                    "center center": "Center Center",
                    "center right": "Center Right",
                    "bottom left": "Bottom Left",
                    "bottom center": "Bottom Center",
                    "bottom right": "Bottom Right",
                    "0% 0%": "Custom"
                },
                "property": "background-position",
                "control_class": "azh-background-position",
                "control_type": "background-position",
                "control_text": "Position"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-position",
                "responsive": true,
                "pattern": /()(-?\d+[%px]+)( -?\d+[%px]+)/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-x-position",
                "control_type": "x-position",
                "control_text": "X position"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-position",
                "responsive": true,
                "pattern": /(-?\d+[%px]+ )(-?\d+[%px]+)()/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-y-position",
                "control_type": "y-position",
                "control_text": "Y position"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "scroll": "Scroll",
                    "fixed": "Fixed"
                },
                "property": "background-attachment",
                "control_class": "azh-background-attachment",
                "control_type": "background-attachment",
                "control_text": "Attachment"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "no-repeat": "No repeat",
                    "repeat": "Repeat",
                    "repeat-x": "Repeat-x",
                    "repeat-y": "Repeat-y"
                },
                "property": "background-repeat",
                "control_class": "azh-background-repeat",
                "control_type": "background-repeat",
                "control_text": "Repeat"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "": "Default",
                    "auto": "Auto",
                    "cover": "Cover",
                    "contain": "Contain",
                    "100% 100%": "Custom"
                },
                "property": "background-size",
                "responsive": true,
                "control_class": "azh-background-size",
                "control_type": "background-size",
                "control_text": "Size"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-size",
                "responsive": true,
                "pattern": /()(\d+[%px]+)( \d+[%px]+)/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-width",
                "control_type": "background-width",
                "control_text": "Width"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "background-size",
                "responsive": true,
                "pattern": /(\d+[%px]+ )(\d+[%px]+)()/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-height",
                "control_type": "background-height",
                "control_text": "Height"
            }
        ]);
    }
    function border_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        border_menu('utility', selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition);
    }
    function border_menu(menu, selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": menu,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "none": "None",
                    "solid": "Solid",
                    "double": "Double",
                    "dotted": "Dotted",
                    "dashed": "Dashed"
                },
                "property": "border-style",
                "control_class": "azh-border-style",
                "control_type": "border-style",
                "control_text": "Border type"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": menu,
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "border-color",
                "control_class": "azh-border-color",
                "control_type": "border-color",
                "control_text": "Border color"
            },
            {
                "refresh": refresh,
                "type": "integer-list-style",
                "menu": menu,
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "properties": {
                    "border-top-width": "Top",
                    "border-right-width": "Right",
                    "border-bottom-width": "Bottom",
                    "border-left-width": "Left"
                },
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-border-width",
                "control_type": "border-width",
                "control_text": "Border width"
            },
            {
                "refresh": refresh,
                "type": "integer-list-style",
                "menu": menu,
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "properties": {
                    "border-top-left-radius": "Top Left",
                    "border-top-right-radius": "Top Right",
                    "border-bottom-left-radius": "Bottom Left",
                    "border-bottom-right-radius": "Bottom Right"
                },
                "slider": true,
                "units": {
                    "px": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "%": {
                        "min": "0",
                        "max": "50",
                        "step": "1"
                    }
                },
                "control_class": "azh-border-radius",
                "control_type": "border-radius",
                "control_text": "Border radius"
            }
        ]);
    }
    function text_stroke_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-stroke-color",
                "prefixes": ['-webkit-'],
                "control_class": "azh-text-stroke-color",
                "control_type": "text-stroke-color",
                "control_text": "Text stroke color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-stroke-width",
                "prefixes": ['-webkit-'],
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-text-stroke-width",
                "control_type": "text-stroke-width",
                "control_text": "Text stroke width"
            },
        ]);
    }
    function top_bottom_border_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "options": {
                    "none": "None",
                    "solid": "Solid",
                    "double": "Double",
                    "dotted": "Dotted",
                    "dashed": "Dashed"
                },
                "property": "border-style",
                "control_class": "azh-border-style",
                "control_type": "border-style",
                "control_text": "Border type"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "border-color",
                "control_class": "azh-border-color",
                "control_type": "border-color",
                "control_text": "Border color"
            },
            {
                "refresh": refresh,
                "type": "integer-list-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "properties": {
                    "border-top-width": "Top",
                    "border-bottom-width": "Bottom"
                },
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-border-width",
                "control_type": "border-width",
                "control_text": "Border width"
            }
        ]);
    }
    function box_shadow_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "exists-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "value": "0px 0px 0px 0px rgba(0,0,0,1)",
                "control_class": "azh-toggle azh-box-shadow",
                "control_type": "box-shadow",
                "control_text": "Box shadow"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "pattern": /(-?\d+px -?\d+px \d+px -?\d+px )(rgba\(\d+,\d+,\d+,\d.?\d*\))()/,
                "default": "0px 0px 0px 0px rgba(0,0,0,1)",
                "control_class": "azh-box-shadow-color",
                "control_type": "box-shadow-color",
                "control_text": "Color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "pattern": /(-?\d+px -?\d+px )(\d+)(px -?\d+px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px 0px rgba(0,0,0,1)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-box-shadow-blur",
                "control_type": "box-shadow-blur",
                "control_text": "Blur"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "pattern": /(-?\d+px -?\d+px \d+px )(-?\d+)(px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px 0px rgba(0,0,0,1)",
                "min": -100,
                "max": 100,
                "step": 1,
                "control_class": "azh-box-shadow-spread",
                "control_type": "box-shadow-spread",
                "control_text": "Spread"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "pattern": /()(-?\d+)(px -?\d+px \d+px -?\d+px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px 0px rgba(0,0,0,1)",
                "min": -100,
                "max": 100,
                "step": 1,
                "control_class": "azh-box-shadow-horizontal",
                "control_type": "box-shadow-horizontal",
                "control_text": "Horizontal"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "box-shadow",
                "pattern": /(-?\d+px )(-?\d+)(px \d+px -?\d+px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px 0px rgba(0,0,0,1)",
                "min": -100,
                "max": 100,
                "step": 1,
                "control_class": "azh-box-shadow-vertical",
                "control_type": "box-shadow-vertical",
                "control_text": "Vertical"
            }
        ]);
    }
    function font_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "font-family",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "font-family",
                "control_class": "azh-font-family",
                "control_type": "font-family",
                "control_text": "Font family"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "font-size",
                "responsive": true,
                "slider": true,
                "units": {
                    "px": {
                        "min": "1",
                        "max": "200",
                        "step": "1"
                    },
                    "em": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    },
                    "rem": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "font-size",
                "control_text": "Font size"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "font-weight",
                "options": {
                    "100": "100",
                    "200": "200",
                    "300": "300",
                    "400": "400",
                    "500": "500",
                    "600": "600",
                    "700": "700",
                    "800": "800",
                    "900": "900"
                },
                "control_class": "azh-dropdown",
                "control_type": "font-weight",
                "control_text": "Font weight"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "font-style",
                "options": {
                    "": "Default",
                    "normal": "Normal",
                    "italic": "Italic",
                    "oblique": "Oblique"
                },
                "control_class": "azh-dropdown",
                "control_type": "font-style",
                "control_text": "Font style"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-decoration",
                "options": {
                    "": "Default",
                    "line-through": "Line-through",
                    "overline": "Overline",
                    "underline": "Underline",
                    "none": "Normal"
                },
                "control_class": "azh-dropdown",
                "control_type": "text-decoration",
                "control_text": "Decoration"
            },
            {
                "refresh": refresh,
                "type": "dropdown-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-transform",
                "options": {
                    "": "Default",
                    "uppercase": "Uppercase",
                    "lowercase": "Lowercase",
                    "capitalize": "Capitalize",
                    "none": "Normal"
                },
                "control_class": "azh-dropdown",
                "control_type": "text-transform",
                "control_text": "Transform"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "color",
                "control_class": "azh-color",
                "control_type": "color",
                "control_text": "Color"
            }
        ]);
    }
    function text_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "integer-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "property": "line-height",
                "slider": true,
                "units": {
                    "px": {
                        "min": "1",
                        "max": "100",
                        "step": "1"
                    },
                    "%": {
                        "min": "1",
                        "max": "300",
                        "step": "1"
                    },
                    "em": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "line-height",
                "control_text": "Line height"
            },
            {
                "refresh": refresh,
                "type": "radio-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "property": "text-align",
                "options": {
                    "left": "Left",
                    "center": "Center",
                    "right": "Right",
                    "justify": "Justify"
                },
                "control_class": "azh-text-align",
                "control_type": "text-align",
                "control_text": "Text align"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "property": "word-spacing",
                "min": "-20",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "word-spacing",
                "control_text": "Word-spacing"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "menu": "utility",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "responsive": true,
                "property": "letter-spacing",
                "min": "-5",
                "max": "10",
                "step": "0.1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "letter-spacing",
                "control_text": "Letter-spacing"
            }
        ]);
    }
    function text_shadow_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": refresh,
                "type": "exists-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-shadow",
                "value": "0px 0px 0px rgba(0,0,0,1)",
                "control_class": "azh-toggle azh-text-shadow",
                "control_type": "text-shadow",
                "control_text": "Text shadow"
            },
            {
                "refresh": refresh,
                "type": "color-style",
                "menu": "utility",
                "group": group,
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-shadow",
                "pattern": /(-?\d+px -?\d+px \d+px )(rgba\(\d+,\d+,\d+,\d.?\d*\))()/,
                "default": "0px 0px 0px rgba(0,0,0,1)",
                "control_class": "azh-text-shadow-color",
                "control_type": "text-shadow-color",
                "control_text": "Color"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-shadow",
                "pattern": /(-?\d+px -?\d+px )(\d+)(px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px rgba(0,0,0,1)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-text-shadow-blur",
                "control_type": "text-shadow-blur",
                "control_text": "Blur"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-shadow",
                "pattern": /()(-?\d+)(px -?\d+px \d+px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px rgba(0,0,0,1)",
                "min": -100,
                "max": 100,
                "step": 1,
                "control_class": "azh-text-shadow-horizontal",
                "control_type": "text-shadow-horizontal",
                "control_text": "Horizontal"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "text-shadow",
                "pattern": /(-?\d+px )(-?\d+)(px \d+px rgba\(\d+,\d+,\d+,\d.?\d*\))/,
                "default": "0px 0px 0px rgba(0,0,0,1)",
                "min": -100,
                "max": 100,
                "step": 1,
                "control_class": "azh-text-shadow-vertical",
                "control_type": "text-shadow-vertical",
                "control_text": "Vertical"
            }
        ]);
    }
    function transform_utility(selector, group, subgroup, attribute, refresh, multiplying_selector, rule_selector, personal_transition) {
        if (attribute === 'data-hover' || attribute === 'data-reveal') {
            if (personal_transition) {
                transition_utility(selector, group, subgroup, refresh);
            } else {
                transition_utility(selector, "Transition parameters", false, refresh);
            }
        }
        azh.controls_options = azh.controls_options.concat([
//            {
//                "refresh": refresh,
//                "type": "exists-style",
//                "selector": selector,
//                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
//                "menu": "utility",
//                "group": group,
//                "subgroup": subgroup,
//                "attribute": attribute,
//                "property": "transform",
//                "value": "transform: translate(0px, 0px) rotate(0deg) scale(1)",
//                "control_class": "azh-toggle azh-transform",
//                "control_type": "transform",
//                "control_text": "Transform"
//            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "transform",
                "pattern": /(translate\()(-?\d+[px%]+)(, -?\d+[px%]+\) rotate\(-?\d+deg\) scale\(\d.?\d*\))/,
                "default": "translate(0px, 0px) rotate(0deg) scale(1)",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-transform-translate-x",
                "control_type": "transform-translate-x",
                "control_text": "Translate X"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "transform",
                "pattern": /(translate\(-?\d+[px%]+, )(-?\d+[px%]+)(\) rotate\(-?\d+deg\) scale\(\d.?\d*\))/,
                "default": "translate(0px, 0px) rotate(0deg) scale(1)",
                "responsive": true,
                "units": {
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-transform-translate-y",
                "control_type": "transform-translate-y",
                "control_text": "Translate Y"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "transform",
                "pattern": /(translate\(-?\d+px, -?\d+px\) rotate\()(-?\d+)(deg\) scale\(\d.?\d*\))/,
                "default": "translate(0px, 0px) rotate(0deg) scale(1)",
                "responsive": true,
                "min": -180,
                "max": 180,
                "step": 1,
                "control_class": "azh-transform-rotate",
                "control_type": "transform-rotate",
                "control_text": "Rotate (deg)"
            },
            {
                "refresh": refresh,
                "type": "integer-style",
                "selector": selector,
                "multiplying_selector": multiplying_selector, "rule_selector": rule_selector,
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "attribute": attribute,
                "property": "transform",
                "pattern": /(translate\(-?\d+px, -?\d+px\) rotate\(-?\d+deg\) scale\()(\d.?\d*)(\))/,
                "default": "translate(0px, 0px) rotate(0deg) scale(1)",
                "responsive": true,
                "min": 0.1,
                "max": 10,
                "step": 0.1,
                "control_class": "azh-transform-scale",
                "control_type": "transform-scale",
                "control_text": "Scale"
            }
        ]);
    }

    function background_effects_utility(selector, group, video, parallax) {
        var types = {
            "classic": "Classic",
            "gradient": "Gradient"
        };
        if (video) {
            types["video"] = "Video";
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                    if ($control.attr('data-value') === 'classic') {
                        $control.parent().find('.azh-background-image img').trigger('contextmenu');
                    }
                    if ($control.attr('data-value') === 'gradient') {
                        $control.parent().find('.azh-background-gradient-type select').trigger('change');
                    }
                },
                "type": "radio-attribute",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": types,
                "attribute": "data-background-type",
                "control_class": "azh-background-type",
                "control_type": "background-type",
                "control_text": "Background type"
            },
            {
                "type": "color-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-color",
                "control_class": "azh-background-color",
                "control_type": "background-color",
                "control_text": "Background color"
            },
            {
                "type": "background-image",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_class": "azh-background-image",
                "control_type": "background-image",
                "control_text": "Background image"
            }
        ]);
        if (parallax) {
            azh.controls_options = azh.controls_options.concat([
                {
                    "refresh": true,
                    "type": "toggle-attribute",
                    "selector": selector,
                    "menu": "utility",
                    "group": group,
                    "attribute": "data-parallax",
                    "control_class": "azh-parallax azh-toggle",
                    "control_type": "parallax",
                    "control_text": "Parallax"
                },
                {
                    "refresh": true,
                    "type": "integer-attribute",
                    "selector": selector,
                    "menu": "utility",
                    "group": group,
                    "min": "0",
                    "max": "100",
                    "step": "1",
                    "attribute": "data-parallax-speed",
                    "control_class": "azh-parallax-speed",
                    "control_type": "parallax-speed",
                    "control_text": "Parallax speed"
                }
            ]);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "": "Default",
                    "top left": "Top Left",
                    "top center": "Top Center",
                    "top right": "Top Right",
                    "center left": "Center Left",
                    "center center": "Center Center",
                    "center right": "Center Right",
                    "bottom left": "Bottom Left",
                    "bottom center": "Bottom Center",
                    "bottom right": "Bottom Right",
                    "0% 0%": "Custom"
                },
                "property": "background-position",
                "control_class": "azh-background-position",
                "control_type": "background-position",
                "control_text": "Position"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-position",
                "responsive": true,
                "pattern": /()(-?\d+[%px]+)( -?\d+[%px]+)/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-x-position",
                "control_type": "x-position",
                "control_text": "X position"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-position",
                "responsive": true,
                "pattern": /(-?\d+[%px]+ )(-?\d+[%px]+)()/,
                "default": "0% 0%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-y-position",
                "control_type": "y-position",
                "control_text": "Y position"
            },
            {
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "": "Default",
                    "scroll": "Scroll",
                    "fixed": "Fixed"
                },
                "property": "background-attachment",
                "control_class": "azh-background-attachment",
                "control_type": "background-attachment",
                "control_text": "Attachment"
            },
            {
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "": "Default",
                    "no-repeat": "No repeat",
                    "repeat": "Repeat",
                    "repeat-x": "Repeat-x",
                    "repeat-y": "Repeat-y"
                },
                "property": "background-repeat",
                "control_class": "azh-background-repeat",
                "control_type": "background-repeat",
                "control_text": "Repeat"
            },
            {
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "": "Default",
                    "auto": "Auto",
                    "cover": "Cover",
                    "contain": "Contain",
                    "100% 100%": "Custom"
                },
                "property": "background-size",
                "responsive": true,
                "control_class": "azh-background-size",
                "control_type": "background-size",
                "control_text": "Size"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-size",
                "responsive": true,
                "pattern": /()(\d+[%px]+)( \d+[%px]+)/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-width",
                "control_type": "background-width",
                "control_text": "Width"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-size",
                "responsive": true,
                "pattern": /(\d+[%px]+ )(\d+[%px]+)()/,
                "default": "100% 100%",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-height",
                "control_type": "background-height",
                "control_text": "Height"
            },
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                },
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "linear-gradient": "Linear",
                    "radial-gradient": "Radial"
                },
                "property": "background-image",
                "pattern": /()([-\w]+)(\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "control_class": "azh-background-gradient-type",
                "control_type": "background-gradient-type",
                "control_text": "Type"
            },
            {
                "type": "dropdown-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    "top left": "Top Left",
                    "top center": "Top Center",
                    "top right": "Top Right",
                    "center left": "Center Left",
                    "center center": "Center Center",
                    "center right": "Center Right",
                    "bottom left": "Bottom Left",
                    "bottom center": "Bottom Center",
                    "bottom right": "Bottom Right"
                },
                "property": "background-image",
                "pattern": /(radial-gradient\(at )([ \w]+)(, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "control_class": "azh-background-radial-gradient-position",
                "control_type": "background-radial-gradient-position",
                "control_text": "Position"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(linear-gradient\()(\d+)(deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 360,
                "step": 1,
                "control_class": "azh-background-linear-gradient-angle",
                "control_type": "background-linear-gradient-angle",
                "control_text": "Angle"
            },
            {
                "type": "color-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-color",
                "control_type": "background-linear-gradient-first-color",
                "control_text": "First color"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-location",
                "control_type": "background-linear-gradient-first-location",
                "control_text": "First location"
            },
            {
                "type": "color-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-color",
                "control_type": "background-linear-gradient-second-color",
                "control_text": "Second color"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(linear-gradient\(\d+deg, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%\))/,
                "default": "linear-gradient(180deg, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-linear-gradient-location",
                "control_type": "background-linear-gradient-second-location",
                "control_text": "Second location"
            },
            {
                "type": "color-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-color",
                "control_type": "background-radial-gradient-first-color",
                "control_text": "First color"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-location",
                "control_type": "background-radial-gradient-first-location",
                "control_text": "First location"
            },
            {
                "type": "color-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, )(rgba\(\d+,\d+,\d+,\d.?\d*\))( \d+%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-color",
                "control_type": "background-radial-gradient-second-color",
                "control_text": "Second color"
            },
            {
                "type": "integer-style",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "property": "background-image",
                "pattern": /(radial-gradient\(at [ \w]+, rgba\(\d+,\d+,\d+,\d.?\d*\) \d+%, rgba\(\d+,\d+,\d+,\d.?\d*\) )(\d+)(%\))/,
                "default": "radial-gradient(at center center, rgba(255,0,0,1) 50%, rgba(0,255,0,1) 50%)",
                "min": 0,
                "max": 100,
                "step": 1,
                "control_class": "azh-background-radial-gradient-location",
                "control_type": "background-radial-gradient-second-location",
                "control_text": "Second location"
            }
        ]);
        if (video) {
            azh.controls_options = azh.controls_options.concat([
                {
                    "refresh": true,
                    "type": "input-attribute",
                    "input_type": "text",
                    "selector": selector,
                    "menu": "utility",
                    "group": group,
                    "control_text": "Video URL",
                    "control_class": "azh-background-video",
                    "control_type": "background-video",
                    "attribute": "data-background-video",
                    "filter": "convert_to_embed",
                    "description": "Insert YouTube link or mp4 video file"
                }
            ]);
        }
    }
    function svg_utility() {
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "svg_content",
                "menu": "utility",
                "control_class": "azh-svg",
                "control_type": "svg",
                "control_text": "SVG content",
                "selector": '.az-svg:not(.az-polygone)'
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "SVG style",
                "control_text": "Shape Aspect Ratio",
                "control_class": "azh-shape azh-aspect-ratio",
                "control_type": "preserveAspectRatio",
                "attribute": "preserveAspectRatio",
                "options": {
                    "none": "none",
                    "xMinYMin": "xMinYMin",
                    "xMidYMin": "xMidYMin",
                    "xMaxYMin": "xMaxYMin",
                    "xMinYMid": "xMinYMid",
                    "xMidYMid": "xMidYMid",
                    "xMaxYMid": "xMaxYMid",
                    "xMinYMax": "xMinYMax",
                    "xMidYMax": "xMidYMax",
                    "xMaxYMax": "xMaxYMax"
                },
                "selector": ".az-svg:not(.az-polygone) svg"
            },
            {
                "type": "integer-style",
                "selector": ".az-svg:not(.az-polygone) svg",
                "menu": "utility",
                "group": "SVG style",
                "property": "width",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "width",
                "control_text": "Shape width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": ".az-svg:not(.az-polygone) svg",
                "group": "SVG style",
                "property": "height",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "height",
                "control_text": "Shape height"
            },
            {
                "type": "color-style",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Normal",
                "property": "fill",
                "selector": '.az-svg:not(.az-polygone) svg',
                "control_class": "azh-fill",
                "control_type": "fill",
                "control_text": "Fill color"
            },
            {
                "refresh": hover_refresh,
                "type": "color-style",
                "attribute": "data-hover",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Hover",
                "property": "fill",
                "selector": '.az-svg:not(.az-polygone) svg',
                "control_class": "azh-fill",
                "control_type": "hover-fill",
                "control_text": "Fill color"
            },

            {
                "type": "color-style",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Normal",
                "property": "fill",
                "control_class": "azh-fill",
                "control_type": "fill",
                "control_text": "Fill color"
            },
            {
                "type": "color-style",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Normal",
                "property": "stroke",
                "control_class": "azh-stroke",
                "control_type": "stroke",
                "control_text": "Stroke color"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Normal",
                "property": "stroke-width",
                "min": "0",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "stroke-width",
                "control_text": "Stroke width"
            },
            {
                "refresh": hover_refresh,
                "type": "color-style",
                "attribute": "data-hover",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Hover",
                "property": "fill",
                "control_class": "azh-fill",
                "control_type": "hover-fill",
                "control_text": "Fill color"
            },
            {
                "refresh": hover_refresh,
                "type": "color-style",
                "attribute": "data-hover",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Hover",
                "property": "stroke",
                "control_class": "azh-stroke",
                "control_type": "hover-stroke",
                "control_text": "Stroke color"
            },
            {
                "refresh": hover_refresh,
                "type": "integer-style",
                "attribute": "data-hover",
                "menu": "utility",
                "group": "SVG style",
                "subgroup": "Hover",
                "property": "stroke-width",
                "min": "0",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "hover-stroke-width",
                "control_text": "Stroke width"
            }
        ]);
        transition_utility('.az-svg:not(.az-polygone) svg', "Transition parameters", false, hover_refresh);
    }
    function triggers_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "post-autocomplete",
                "menu": "utility",
                "group": "Triggers",
                "selector": "[data-fill-from-post]",
                "attribute": "data-fill-from-post",
                "control_class": "azh-dropdown",
                "control_type": "fill-from-post",
                "control_text": "Fill triggered content from post"
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Triggers",
                "options": "data-element",
                "attribute": "data-click-trigger",
                "control_class": "azh-dropdown",
                "control_type": "click-trigger",
                "control_text": "Trigger on click"
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Triggers",
                "options": "data-element",
                "attribute": "data-hover-trigger",
                "control_class": "azh-dropdown",
                "control_type": "hover-trigger",
                "control_text": "Trigger on hover"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Triggers",
                "attribute": "data-class-from-post-meta",
                "control_class": "azh-class",
                "control_type": "class-from-post-meta",
                "control_text": "Class from post meta"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Triggers",
                "attribute": "data-file-meta-field",
                "control_class": "azh-metakey",
                "control_type": "file-meta-field",
                "control_text": "Metakey - value as URL to file"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Triggers",
                "attribute": "data-video-meta-field",
                "control_class": "azh-metakey",
                "control_type": "video-meta-field",
                "control_text": "Metakey - value as URL to video"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Triggers",
                "attribute": "data-image-meta-field",
                "control_class": "azh-metakey",
                "control_type": "image-meta-field",
                "control_text": "Metakey - value as URL to image"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Triggers",
                "attribute": "data-meta-field",
                "control_class": "azh-metakey",
                "control_type": "meta-field",
                "control_text": "Metakey - value as text"
            }
        ]);
    }
    function section_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": true,
                "type": "toggle-attribute",
                "selector": "[data-full-width]",
                "menu": "utility",
                "group": "Layout",
                "attribute": "data-full-width",
                "control_class": "azh-toggle",
                "control_type": "full-width",
                "control_text": "Full width section"
            },
            {
                "refresh": true,
                "type": "toggle-attribute",
                "selector": "[data-stretch-content]",
                "menu": "utility",
                "group": "Layout",
                "attribute": "data-stretch-content",
                "control_class": "azh-toggle",
                "control_type": "stretch-content",
                "control_text": "Stretch section content"
            },
            {
                "refresh": function ($control, $element) {
                    if ($control.attr('data-value') === 'container-boxed') {
                        $element.addClass('az-container');
                    } else {
                        $element.removeClass('az-container');
                        $element.css('padding', '');
                        $element.css('max-width', '');
                    }
                },
                "type": "dropdown-attribute",
                "selector": "[data-content-width]",
                "menu": "utility",
                "group": "Layout",
                "options": {
                    "full-width": "Full width",
                    "container-boxed": "Container boxed",
                    "boxed": "Boxed"
                },
                "attribute": "data-content-width",
                "control_class": "azh-content-width",
                "control_type": "content-width",
                "control_text": "Content Width"
            },
            {
                "type": "integer-style",
                "selector": "[data-content-width] > div:not(.az-overlay):not(.az-shape-top):not(.az-shape-bottom)",
                "menu": "utility",
                "group": "Layout",
                "property": "max-width",
                "min": "300",
                "max": "1600",
                "step": "1",
                "units": "px",
                "control_class": "azh-max-width",
                "control_type": "max-width",
                "control_text": "Maximum content width"
            },
            {
                "type": "dropdown-attribute",
                "selector": "[data-column-padding]",
                "menu": "utility",
                "group": "Layout",
                "options": {
                    "0": "0px",
                    "1": "1px",
                    "2": "2px",
                    "3": "3px",
                    "5": "5px",
                    "10": "10px",
                    "15": "15px",
                    "20": "20px",
                    "25": "25px",
                    "30": "30px",
                    "40": "40px",
                    "50": "50px",
                    "60": "60px",
                    "70": "70px"
                },
                "attribute": "data-column-padding",
                "control_class": "azh-dropdown",
                "control_type": "column-padding",
                "control_text": "Column padding"
            },
            {
                "type": "dropdown-attribute",
                "selector": "[data-row-height]",
                "menu": "utility",
                "group": "Layout",
                "options": {
                    "": "Default",
                    "fit-to-screen": "Fit to screen",
                    "min-height": "Min height"
                },
                "attribute": "data-row-height",
                "control_class": "azh-row-height",
                "control_type": "row-height",
                "control_text": "Row height"
            },
            {
                "type": "integer-style",
                "selector": "[data-row-height] > .azh-row",
                "menu": "utility",
                "group": "Layout",
                "property": "min-height",
                "responsive": true,
                "slider": true,
                "units": {
                    "vh": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1440",
                        "step": "1"
                    }
                },
                "control_class": "azh-min-height",
                "control_type": "min-height",
                "control_text": "Minimum Height"
            },
            {
                "type": "dropdown-attribute",
                "selector": "[data-column-position]",
                "menu": "utility",
                "group": "Layout",
                "options": {
                    "stretch": "Stretch",
                    "top": "Top",
                    "middle": "Middle",
                    "bottom": "Bottom"
                },
                "attribute": "data-column-position",
                "control_class": "azh-column-position",
                "control_type": "column-position",
                "control_text": "Column Position"
            },
            {
                "type": "dropdown-attribute",
                "selector": "[data-content-position]",
                "menu": "utility",
                "group": "Layout",
                "options": {
                    "top": "Top",
                    "middle": "Middle",
                    "bottom": "Bottom",
                    "space-around": "Space around",
                    "space-between": "Space between"
                },
                "attribute": "data-content-position",
                "control_class": "azh-content-position",
                "control_type": "content-position",
                "control_text": "Content Position in Column"
            },
            {
                "type": "integer-style",
                "selector": "[data-column-padding][style]",
                "menu": "utility",
                "group": "Layout",
                "property": "padding-top",
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "padding-top",
                "control_text": "Padding top"
            },
            {
                "type": "integer-style",
                "selector": "[data-column-padding][style]",
                "menu": "utility",
                "group": "Layout",
                "property": "padding-bottom",
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "padding-bottom",
                "control_text": "Padding bottom"
            },
            {
                "type": "integer-style",
                "selector": "[data-column-padding][style]",
                "menu": "utility",
                "group": "Layout",
                "property": "margin-top",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "margin-top",
                "control_text": "Margin top"
            },
            {
                "type": "integer-style",
                "selector": "[data-column-padding][style]",
                "menu": "utility",
                "group": "Layout",
                "property": "margin-bottom",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "margin-bottom",
                "control_text": "Margin bottom"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Layout",
                "selector": "[data-full-width][style]",
                "property": "z-index",
                "control_class": "azh-integer",
                "control_type": "z-index",
                "control_text": "Section z-index"
            },
            {
                "type": "toggle-attribute",
                "selector": "[data-overflow-hidden]",
                "menu": "utility",
                "group": "Layout",
                "attribute": "data-overflow-hidden",
                "control_class": "azh-overflow-hidden azh-toggle",
                "control_type": "overflow-hidden",
                "control_text": "Overflow hidden"
            },
        ]);
    }
    function section_background_utility() {
        background_effects_utility('[data-full-width][data-background-type]', 'Background', true, true);
        background_utility('[data-full-width] > .az-overlay > div', 'Background overlay');
        transform_utility('[data-full-width] > .az-overlay > div', 'Background overlay');
        filter_utility('[data-full-width] > .az-overlay > div', 'Background overlay');
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": function ($control, $element) {
                    azh.frontend_init($element.closest('.az-overlay'));
                },
                "type": "integer-attribute",
                "menu": "utility",
                "group": 'Background overlay',
                "attribute": "data-depth",
                "step": "0.01",
                "min": "0",
                "max": "1",
                "selector": "[data-full-width] > .az-overlay > div",
                "control_class": "azh-parallax-depth",
                "control_type": "parallax-depth",
                "control_text": "Parallax depth"
            },
            {
                "refresh": function ($control, $element) {
                    azh.frontend_init($element.closest('.az-overlay'));
                },
                "type": "integer-attribute",
                "menu": "utility",
                "group": 'Background overlay',
                "attribute": "data-rellax-speed",
                "step": "1",
                "min": "-10",
                "max": "10",
                "selector": "[data-full-width] > .az-overlay > div",
                "control_class": "azh-rellax-speed",
                "control_type": "rellax-speed",
                "control_text": "Rellax speed"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": 'Background overlay',
                "min": "0",
                "max": "1",
                "step": "0.01",
                "control_class": "azh-overlay-opacity",
                "control_type": "overlay-opacity",
                "control_text": "Overlay opacity",
                "property": "opacity",
                "selector": '[data-full-width] > .az-overlay > div'
            }
        ]);
        //shape_overlay_utility("[data-full-width] > .az-overlay > div", "Background overlay");
    }
    function column_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "dropdown-style",
                "selector": "[data-column-position] > .azh-row > [class*='azh-col-']",
                "menu": "utility",
                "responsive": true,
                "options": {
                    "": "Default",
                    "flex-start": "Top",
                    "center": "Middle",
                    "flex-end": "Bottom"
                },
                "property": "align-self",
                "control_class": "azh-column-position",
                "control_type": "column-position",
                "control_text": "Column Position"
            },
            {
                "type": "dropdown-style",
                "selector": "[data-content-position] > .azh-row > [class*='azh-col-']",
                "menu": "utility",
                "responsive": true,
                "options": {
                    "": "Default",
                    "flex-start": "Top",
                    "center": "Middle",
                    "flex-end": "Bottom",
                    "space-around": "Space around",
                    "space-between": "Space between"
                },
                "property": "justify-content",
                "control_class": "azh-content-position",
                "control_type": "content-position",
                "control_text": "Content Position"
            },
            {
                "type": "dropdown-style",
                "selector": "[data-content-position] > .azh-row > [class*='azh-col-']",
                "menu": "utility",
                "responsive": true,
                "options": {
                    "": "Default",
                    "flex-start": "Left",
                    "center": "Center",
                    "flex-end": "Right"
                },
                "property": "align-items",
                "control_class": "azh-content-align",
                "control_type": "content-align",
                "control_text": "Content Align"
            },
            {
                "type": "integer-style",
                "selector": "[data-content-position] > .azh-row > [class*='azh-col-']",
                "menu": "utility",
                "property": "padding-top",
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "padding-top",
                "control_text": "Padding top"
            },
            {
                "type": "integer-style",
                "selector": "[data-content-position] > .azh-row > [class*='azh-col-']",
                "menu": "utility",
                "property": "padding-bottom",
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "padding-bottom",
                "control_text": "Padding bottom"
            }
        ]);
        background_utility('[data-column-position] > .azh-row > [class*="azh-col-"]', 'Column background');
        border_utility('[data-column-position] > .azh-row > [class*="azh-col-"]', 'Column border');
    }
    function iframe_video_utility(selector, group) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "dropdown-attribute",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "options": {
                    'youtube': 'YouTube',
                    'vimeo': 'Vimeo'
                },
                "control_text": "Video Type",
                "control_class": "azh-video-type",
                "control_type": "video-type",
                "attribute": "data-video-type"
            },
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                },
                "type": "input-attribute",
                "input_type": "text",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "filter": "convert_to_embed",
                "control_text": "Video URL",
                "control_class": "azh-video-url",
                "control_type": "video-url"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "autoplay",
                "true_value": "1",
                "false_value": "0",
                "default": false,
                "control_text": "Autoplay",
                "control_class": "azh-toggle azh-youtube-autoplay",
                "control_type": "youtube-autoplay"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "rel",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Suggested Videos",
                "control_class": "azh-toggle azh-youtube-rel",
                "control_type": "youtube-rel"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "controls",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Player Controls",
                "control_class": "azh-toggle azh-youtube-controls",
                "control_type": "youtube-controls"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "showinfo",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Player Title & Actions",
                "control_class": "azh-toggle azh-youtube-showinfo",
                "control_type": "youtube-showinfo"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "loop",
                "true_value": "1",
                "false_value": "0",
                "default": false,
                "control_text": "Loop",
                "control_class": "azh-toggle azh-youtube-loop",
                "control_type": "youtube-loop"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "mute",
                "true_value": "1",
                "false_value": "0",
                "default": false,
                "control_text": "Mute",
                "control_class": "azh-toggle azh-youtube-mute",
                "control_type": "youtube-mute"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "autoplay",
                "true_value": "1",
                "false_value": "0",
                "default": false,
                "control_text": "Autoplay",
                "control_class": "azh-toggle azh-vimeo-autoplay",
                "control_type": "vimeo-autoplay"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "byline",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Intro byline",
                "control_class": "azh-toggle azh-vimeo-byline",
                "control_type": "vimeo-byline"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "title",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Intro title",
                "control_class": "azh-toggle azh-vimeo-title",
                "control_type": "vimeo-title"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "portrait",
                "true_value": "1",
                "false_value": "0",
                "default": true,
                "control_text": "Intro portrait",
                "control_class": "azh-toggle azh-vimeo-portrait",
                "control_type": "vimeo-portrait"
            },
            {
                "type": "toggle-url-argument",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "argument": "loop",
                "true_value": "1",
                "false_value": "0",
                "default": false,
                "control_text": "Loop",
                "control_class": "azh-toggle azh-vimeo-loop",
                "control_type": "vimeo-loop"
            },
        ]);
    }
    function video_utility(selector, group) {
        //http://localhost/wordpress4/wp-content/uploads/2018/09/multi-step.mp4#t=2,4
        //controlslist="nodownload"
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "video-url-attribute",
                "attribute": "src",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Video file URL",
                "control_class": "azh-video-url",
                "control_type": "video-url"
            },
            {
                "type": "exists-attribute",
                "attribute": "autoplay",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Autoplay",
                "control_class": "azh-toggle azh-video-autoplay",
                "control_type": "video-autoplay"
            },
            {
                "type": "exists-attribute",
                "attribute": "playsinline",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Plays inline",
                "control_class": "azh-toggle azh-video-playsinline",
                "control_type": "video-playsinline"
            },
            {
                "type": "exists-attribute",
                "attribute": "controls",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Player Controls",
                "control_class": "azh-toggle azh-video-controls",
                "control_type": "video-controls"
            },
            {
                "type": "exists-attribute",
                "attribute": "loop",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Loop",
                "control_class": "azh-toggle azh-video-loop",
                "control_type": "video-loop"
            },
            {
                "type": "exists-attribute",
                "attribute": "muted",
                "selector": selector,
                "menu": "utility",
                "group": group,
                "control_text": "Mute",
                "control_class": "azh-toggle azh-video-mute",
                "control_type": "video-mute"
            }
        ]);
    }
    function box_utility(selector, group) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-list-style",
                "menu": "utility",
                "group": group,
                "responsive": true,
                "properties": {
                    "margin-top": "Top",
                    "margin-right": "Right",
                    "margin-bottom": "Bottom",
                    "margin-left": "left"
                },
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-margin",
                "control_text": "Margin",
                "selector": selector
            },
            {
                "type": "integer-list-style",
                "menu": "utility",
                "group": group,
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-padding",
                "control_text": "Padding",
                "selector": selector
            }
        ]);
    }
    function element_box_utility() {
        box_utility('[data-element]:not([data-element=""]):not([data-element=" "])', "Element-box styles");
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "radio-style",
                "selector": '[data-element]:not([data-element=""]):not([data-element=" "])',
                "menu": "utility",
                "group": "Element-box styles",
                "property": "display",
                "options": {
                    "none": "Hidden",
                    "block": "Visible",
                },
                "responsive": true,
                "control_class": "azh-visibility azh-default",
                "control_type": "visibility",
                "control_text": "Visibility"
            },
            {
                "refresh": true,
                "type": "exists-class",
                "menu": "utility",
                "control_text": "Element pack",
                "control_class": "azh-toggle",
                "control_type": "pack",
                "selector": '[data-element]:not([data-element=""]):not([data-element=" "])',
                "class": "az-pack"
            }
        ]);
    }
    function animation_utility(group, selector) {
        var in_animation_types = {
            "none": "No animation",
            "bounceIn": "bounceIn",
            "bounceInDown": "bounceInDown",
            "bounceInLeft": "bounceInLeft",
            "bounceInRight": "bounceInRight",
            "bounceInUp": "bounceInUp",
            "fadeIn": "fadeIn",
            "fadeInDown": "fadeInDown",
            "fadeInDownBig": "fadeInDownBig",
            "fadeInLeft": "fadeInLeft",
            "fadeInLeftBig": "fadeInLeftBig",
            "fadeInRight": "fadeInRight",
            "fadeInRightBig": "fadeInRightBig",
            "fadeInUp": "fadeInUp",
            "fadeInUpBig": "fadeInUpBig",
            "rotateIn": "rotateIn",
            "rotateInDownLeft": "rotateInDownLeft",
            "rotateInDownRight": "rotateInDownRight",
            "rotateInUpLeft": "rotateInUpLeft",
            "rotateInUpRight": "rotateInUpRight",
            "slideInUp": "slideInUp",
            "slideInDown": "slideInDown",
            "slideInLeft": "slideInLeft",
            "slideInRight": "slideInRight",
            "zoomIn": "zoomIn",
            "zoomInDown": "zoomInDown",
            "zoomInLeft": "zoomInLeft",
            "zoomInRight": "zoomInRight",
            "zoomInUp": "zoomInUp",
            "flipInX": "flipInX",
            "flipInY": "flipInY",
            "lightSpeedIn": "lightSpeedIn",
        };
        var out_animation_types = {
            "none": "No animation",
            "bounceOut": "bounceOut",
            "bounceOutDown": "bounceOutDown",
            "bounceOutLeft": "bounceOutLeft",
            "bounceOutRight": "bounceOutRight",
            "bounceOutUp": "bounceOutUp",
            "fadeOut": "fadeOut",
            "fadeOutDown": "fadeOutDown",
            "fadeOutDownBig": "fadeOutDownBig",
            "fadeOutLeft": "fadeOutLeft",
            "fadeOutLeftBig": "fadeOutLeftBig",
            "fadeOutRight": "fadeOutRight",
            "fadeOutRightBig": "fadeOutRightBig",
            "fadeOutUp": "fadeOutUp",
            "fadeOutUpBig": "fadeOutUpBig",
            "rotateOut": "rotateOut",
            "rotateOutDownLeft": "rotateOutDownLeft",
            "rotateOutDownRight": "rotateOutDownRight",
            "rotateOutUpLeft": "rotateOutUpLeft",
            "rotateOutUpRight": "rotateOutUpRight",
            "slideOutUp": "slideOutUp",
            "slideOutDown": "slideOutDown",
            "slideOutLeft": "slideOutLeft",
            "slideOutRight": "slideOutRight",
            "zoomOut": "zoomOut",
            "zoomOutDown": "zoomOutDown",
            "zoomOutLeft": "zoomOutLeft",
            "zoomOutRight": "zoomOutRight",
            "zoomOutUp": "zoomOutUp",
            "flipOutX": "flipOutX",
            "flipOutY": "flipOutY",
            "lightSpeedOut": "lightSpeedOut",
        };
        var timing = {
            "linear": "linear",
            "ease": "ease",
            "ease-in": "ease-in",
            "ease-out": "ease-out",
            "ease-in-out": "ease-in-out",
        };
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "In animation",
                "control_text": "Type",
                "control_class": "azh-in-animation-type",
                "control_type": "in-animation-type",
                "attribute": "data-in-animation-type",
                "options": in_animation_types,
                "selector": selector
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "In animation",
                "control_text": "Duration (milliseconds)",
                "control_class": "azh-in-animation-duration",
                "control_type": "in-animation-duration",
                "attribute": "data-in-animation-duration",
                "min": "0",
                "max": "1000",
                "step": "100",
                "selector": selector
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "In animation",
                "control_text": "Delay (milliseconds)",
                "control_class": "azh-in-animation-delay",
                "control_type": "in-animation-delay",
                "attribute": "data-in-animation-delay",
                "min": "0",
                "max": "1000",
                "step": "100",
                "selector": selector
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "In animation",
                "control_text": "Timing function",
                "control_class": "azh-in-animation-timing",
                "control_type": "in-animation-timing",
                "attribute": "data-in-animation-timing",
                "options": timing,
                "selector": selector
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "Out animation",
                "control_text": "Type",
                "control_class": "azh-out-animation-type",
                "control_type": "out-animation-type",
                "attribute": "data-out-animation-type",
                "options": out_animation_types,
                "selector": selector
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "Out animation",
                "control_text": "Duration (milliseconds)",
                "control_class": "azh-out-animation-duration",
                "control_type": "out-animation-duration",
                "attribute": "data-out-animation-duration",
                "min": "0",
                "max": "1000",
                "step": "100",
                "selector": selector
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "Out animation",
                "control_text": "Delay (milliseconds)",
                "control_class": "azh-out-animation-delay",
                "control_type": "out-animation-delay",
                "attribute": "data-out-animation-delay",
                "min": "0",
                "max": "1000",
                "step": "100",
                "selector": selector
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": "Out animation",
                "control_text": "Timing function",
                "control_class": "azh-out-animation-timing",
                "control_type": "out-animation-timing",
                "attribute": "data-out-animation-timing",
                "options": timing,
                "selector": selector
            },
        ]);
    }
    function scroll_reveal_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": function ($control, $element) {
                    $control.parent().find('.azh-control').trigger('azh-init');
                },
                "type": "toggle-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "control_class": "azh-sr azh-toggle",
                "control_type": "sr",
                "true_value": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "false_value": '',
                "control_text": "Scroll reveal"
            },
            {
                "type": "dropdown-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "options": {
                    "top": "Top",
                    "bottom": "Bottom",
                    "right": "Right",
                    "left": "Left",
                },
                "pattern": /(enter )(\w+)(, [\w-]+ \d+px, over \d.?\d*s, wait \d.?\d*s)/,
                "default": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "control_class": "azh-sr-enter",
                "control_type": "sr-enter",
                "control_text": "Enter"
            },
            {
                "type": "integer-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "pattern": /(enter \w+, [\w-]+ )(\d+)(px, over \d.?\d*s, wait \d.?\d*s)/,
                "default": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "min": 0,
                "max": 300,
                "step": 1,
                "control_class": "azh-sr-move",
                "control_type": "sr-move",
                "control_text": "Move (px)"
            },
            {
                "type": "integer-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "pattern": /(enter \w+, [\w-]+ \d+px, over )(\d.?\d*)(s, wait \d.?\d*s)/,
                "default": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "min": 0,
                "max": 1,
                "step": 0.1,
                "control_class": "azh-sr-over",
                "control_type": "sr-over",
                "control_text": "Over (seconds)"
            },
            {
                "type": "integer-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "pattern": /(enter \w+, [\w-]+ \d+px, over \d.?\d*s, wait )(\d.?\d*)(s)/,
                "default": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "min": 0,
                "max": 1,
                "step": 0.1,
                "control_class": "azh-sr-wait",
                "control_type": "sr-wait",
                "control_text": "Wait (seconds)"
            },
            {
                "type": "dropdown-attribute",
                "selector": '[data-sr]',
                "menu": "utility",
                "group": 'Scroll reveal',
                "attribute": "data-sr",
                "options": {
                    "move": "move",
                    "ease": "ease",
                    "ease-in": "ease-in",
                    "ease-out": "ease-out",
                    "ease-in-out": "ease-in-out",
                    "hustle": "hustle",
                },
                "pattern": /(enter \w+, )([\w-]+)( \d+px, over \d.?\d*s, wait \d.?\d*s)/,
                "default": 'enter bottom, move 8px, over 0.6s, wait 0.0s',
                "control_class": "azh-sr-easing",
                "control_type": "sr-easing",
                "control_text": "Easing"
            },
        ]);
    }
    function shape_utility(selector, group, subgroup) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "html-switcher",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "options": {
                    "": "None",
                    "blob.svg": "blob",
                    "square.svg": "square",
                    "circle.svg": "circle"
                },
                "control_class": "azh-shape",
                "control_type": "shape-overlay",
                "control_text": "Shape type",
                "selector": selector
            },
            {
                "type": "color-style",
                "selector": selector + " .az-shape-fill",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "fill",
                "control_class": "azh-shape azh-fill",
                "control_type": "fill",
                "control_text": "Shape color"
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Shape Aspect Ratio",
                "control_class": "azh-shape azh-aspect-ratio",
                "control_type": "preserveAspectRatio",
                "attribute": "preserveAspectRatio",
                "options": {
                    "none": "none",
                    "xMinYMin": "xMinYMin",
                    "xMidYMin": "xMidYMin",
                    "xMaxYMin": "xMaxYMin",
                    "xMinYMid": "xMinYMid",
                    "xMidYMid": "xMidYMid",
                    "xMaxYMid": "xMaxYMid",
                    "xMinYMax": "xMinYMax",
                    "xMidYMax": "xMidYMax",
                    "xMaxYMax": "xMaxYMax"
                },
                "selector": selector + " svg:not(.az-negative)"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg:not(.az-negative)",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "width",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "width",
                "control_text": "Shape width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": selector + " svg:not(.az-negative)",
                "group": group,
                "subgroup": subgroup,
                "property": "height",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "height",
                "control_text": "Shape height"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg.az-negative",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "transform",
                "pattern": /(translate\(-50%, -50%\) scale\()(\d.?\d*)(\))/,
                "default": "translate(-50%, -50%) scale(1)",
                "responsive": true,
                "min": 1,
                "max": 10,
                "step": 0.1,
                "control_class": "azh-shape azh-transform-scale",
                "control_type": "transform-scale",
                "control_text": "Size"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Flip",
                "control_class": "azh-shape azh-toggle",
                "control_type": "flip",
                "selector": selector,
                "class": "az-flip"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Invert",
                "control_class": "azh-shape azh-toggle",
                "control_type": "invert",
                "selector": selector,
                "class": "az-invert"
            }
        ]);
    }
    function shape_overlay_utility(selector, group, subgroup) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "html-switcher",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "options": {
                    "": "None",
                    "blob-negative.svg": "blob-negative",
                    "blob.svg": "blob",
                    "square.svg": "square",
                    "circle-negative.svg": "circle-negative",
                    "circle.svg": "circle"
                },
                "control_class": "azh-shape",
                "control_type": "shape-overlay",
                "control_text": "Shape type",
                "selector": selector
            },
            {
                "type": "color-style",
                "selector": selector + " .az-shape-fill",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "fill",
                "control_class": "azh-shape azh-fill",
                "control_type": "fill",
                "control_text": "Shape color"
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Shape Aspect Ratio",
                "control_class": "azh-shape azh-aspect-ratio",
                "control_type": "preserveAspectRatio",
                "attribute": "preserveAspectRatio",
                "options": {
                    "none": "none",
                    "xMinYMin": "xMinYMin",
                    "xMidYMin": "xMidYMin",
                    "xMaxYMin": "xMaxYMin",
                    "xMinYMid": "xMinYMid",
                    "xMidYMid": "xMidYMid",
                    "xMaxYMid": "xMaxYMid",
                    "xMinYMax": "xMinYMax",
                    "xMidYMax": "xMidYMax",
                    "xMaxYMax": "xMaxYMax"
                },
                "selector": selector + " svg:not(.az-negative)"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg:not(.az-negative)",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "width",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "width",
                "control_text": "Shape width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": selector + " svg:not(.az-negative)",
                "group": group,
                "subgroup": subgroup,
                "property": "height",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "400",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-integer",
                "control_type": "height",
                "control_text": "Shape height"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg.az-negative",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "transform",
                "pattern": /(translate\(-50%, -50%\) scale\()(\d.?\d*)(\))/,
                "default": "translate(-50%, -50%) scale(1)",
                "responsive": true,
                "min": 1,
                "max": 10,
                "step": 0.1,
                "control_class": "azh-shape azh-transform-scale",
                "control_type": "transform-scale",
                "control_text": "Size"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "left",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-x-position",
                "control_type": "x-position",
                "control_text": "Shape X position"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "top",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "-500",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-shape azh-y-position",
                "control_type": "y-position",
                "control_text": "Shape Y position"
            },
            {
                "type": "dropdown-classes",
                "selector": selector + " svg:not(.az-negative)",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "responsive": true,
                "classes": {
                    "az-top-left": "Top Left",
                    "az-top-center": "Top Center",
                    "az-top-right": "Top Right",
                    "az-center-left": "Center Left",
                    "az-center-center": "Center Center",
                    "az-center-right": "Center Right",
                    "az-bottom-left": "Bottom Left",
                    "az-bottom-center": "Bottom Center",
                    "az-bottom-right": "Bottom Right"
                },
                "control_class": "azh-shape azh-position-origin",
                "control_type": "position-origin",
                "control_text": "Shape position origin"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Flip",
                "control_class": "azh-shape azh-toggle",
                "control_type": "flip",
                "selector": selector,
                "class": "az-flip"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Invert",
                "control_class": "azh-shape azh-toggle",
                "control_type": "invert",
                "selector": selector,
                "class": "az-invert"
            }
        ]);
    }
    function shape_divider_utility(selector, group, subgroup) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "html-switcher",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "options": {
                    "": "None",
                    "waves-pattern.svg": "waves-pattern",
                    "pyramids-negative.svg": "pyramids-negative",
                    "book-negative.svg": "book-negative",
                    "triangle-asymmetrical.svg": "triangle-asymmetrical",
                    "triangle-negative.svg": "triangle-negative",
                    "clouds-negative.svg": "clouds-negative",
                    "opacity-fan.svg": "opacity-fan",
                    "mountains.svg": "mountains",
                    "book.svg": "book",
                    "split.svg": "split",
                    "curve.svg": "curve",
                    "waves.svg": "waves",
                    "waves-big.svg": "waves-big",
                    "split-negative.svg": "split-negative",
                    "curve-asymmetrical-negative.svg": "curve-asymmetrical-negative",
                    "zigzag.svg": "zigzag",
                    "opacity-tilt.svg": "opacity-tilt",
                    "curve-negative.svg": "curve-negative",
                    "arrow-negative.svg": "arrow-negative",
                    "arrow.svg": "arrow",
                    "tilt.svg": "tilt",
                    "curve-asymmetrical.svg": "curve-asymmetrical",
                    "triangle-asymmetrical-negative.svg": "triangle-asymmetrical-negative",
                    "clouds.svg": "clouds",
                    "drops.svg": "drops",
                    "drops-negative.svg": "drops-negative",
                    "triangle.svg": "triangle",
                    "waves-negative.svg": "waves-negative",
                    "pyramids.svg": "pyramids",
                    "mountain-negative.svg": "mountain-negative",
                    "mountain.svg": "mountain",
                    "brush.svg": "brush",
                    "clipping.svg": "clipping",
                    "wave-brush.svg": "wave-brush"
                },
                "control_class": "azh-shape-divider",
                "control_type": "shape-divider",
                "control_text": "Type",
                "selector": selector
            },
            {
                "type": "color-style",
                "selector": selector + " .az-shape-fill",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "fill",
                "control_class": "azh-fill",
                "control_type": "fill",
                "control_text": "Color"
            },
            {
                "type": "integer-style",
                "selector": selector + " svg",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "property": "width",
                "responsive": true,
                "pattern": /(calc\()(\d+)(% \+ 1px\))/,
                "default": "calc(100% + 1px)",
                "slider": true,
                "min": 100,
                "max": 400,
                "step": 1,
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Width (%)"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": selector + " svg",
                "group": group,
                "subgroup": subgroup,
                "property": "height",
                "responsive": true,
                "slider": true,
                "min": "1",
                "max": "1000",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Flip",
                "control_class": "azh-toggle",
                "control_type": "flip",
                "selector": selector,
                "class": "az-flip"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Invert",
                "control_class": "azh-toggle",
                "control_type": "invert",
                "selector": selector,
                "class": "az-invert"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": group,
                "subgroup": subgroup,
                "control_text": "Bring to front",
                "control_class": "azh-toggle",
                "control_type": "bring-to-front",
                "selector": selector,
                "class": "az-bring-to-front"
            }
        ]);
    }
    function splitted_section_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "toggle-attribute",
                "selector": "[data-reverse]",
                "menu": "utility",
                "attribute": "data-reverse",
                "control_class": "reverse-toggle",
                "control_type": "grid",
                "control_text": "Reverse"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "property": "height",
                "min": "0",
                "max": "600",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "background-height",
                "control_text": "BG height for mobile (px)",
                "selector": '.az-splitted-section .az-background-column, .az-half-stretched-section .az-stretch-column'
            },
            {
                "type": "toggle-attribute",
                "menu": "utility",
                "control_class": "azh-toggle",
                "selector": "[data-content-reverse]",
                "attribute": "data-content-reverse",
                "control_type": "data-content-reverse",
                "control_text": "Reverse content"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": 'Offsets',
                "responsive": true,
                "property": "width",
                "pattern": /(calc\(50% \+ )(-?\d+)(px\))/,
                "default": "calc(50% + 0px)",
                "min": "-300",
                "max": "300",
                "step": "1",
                "control_class": "azh-integer",
                "control_type": "background-offset",
                "control_text": "Background offset (px)",
                "selector": '.az-splitted-section .az-background-column, .az-half-stretched-section .az-stretch-column'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": 'Offsets',
                "responsive": true,
                "property": "top",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "background-top",
                "control_text": "Background top (px)",
                "selector": '.az-splitted-section .az-background-column, .az-half-stretched-section .az-stretch-column'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": 'Offsets',
                "responsive": true,
                "property": "bottom",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "background-bottom",
                "control_text": "Background bottom (px)",
                "selector": '.az-splitted-section .az-background-column, .az-half-stretched-section .az-stretch-column'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": 'Offsets',
                "responsive": true,
                "property": "width",
                "pattern": /(calc\(50% \+ )(-?\d+)(px\))/,
                "default": "calc(50% + 0px)",
                "min": "-300",
                "max": "300",
                "step": "1",
                "control_class": "azh-integer",
                "control_type": "content-offset",
                "control_text": "Content offset (px)",
                "selector": '.az-splitted-section .az-content-column, .az-half-stretched-section .az-content-column'
            },
            {
                "type": "integer-list-style",
                "menu": "utility",
                "group": 'Offsets',
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "content-padding",
                "control_text": "Content padding",
                "selector": '.az-splitted-section .az-content-column, .az-half-stretched-section .az-content-column'
            }
        ]);
        background_utility('.az-background-column', 'Half-Background');
        background_utility('.az-background-column > .az-overlay > div', 'Half-Background overlay');
        filter_utility('.az-background-column > .az-overlay > div', 'Half-Background overlay');
        //shape_overlay_utility(".az-background-column > .az-overlay > div", "Half-Background overlay");
    }
    function hotspot_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "exists-attribute",
                "menu": "utility",
                "group": "Hotspot",
                "control_text": "Showed",
                "control_class": "azh-toggle",
                "control_type": "checked",
                "selector": ".az-hotspot > input[type='checkbox']",
                "attribute": "checked"
            },
            {
                "type": "integer-style",
                "selector": ".az-hotspot > .az-wrapper > .az-lines > .az-line",
                "menu": "utility",
                "group": "Hotspot",
                "property": "height",
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Line 1 length"
            },
            {
                "type": "integer-style",
                "selector": ".az-hotspot > .az-wrapper > .az-lines > .az-line",
                "menu": "utility",
                "group": "Hotspot",
                "property": "transform",
                "pattern": /(rotate\()(-?\d+)(deg\))/,
                "default": "rotate(0deg)",
                "responsive": true,
                "min": -180,
                "max": 180,
                "step": 1,
                "control_class": "azh-transform-rotate",
                "control_type": "transform-rotate",
                "control_text": "Line 1 angle (deg)"
            },
            {
                "type": "integer-style",
                "selector": ".az-hotspot > .az-wrapper > .az-lines > .az-line > .az-line",
                "menu": "utility",
                "group": "Hotspot",
                "property": "height",
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Line 2 length"
            },
            {
                "type": "integer-style",
                "selector": ".az-hotspot > .az-wrapper > .az-lines > .az-line > .az-line",
                "menu": "utility",
                "group": "Hotspot",
                "property": "transform",
                "pattern": /(rotate\()(-?\d+)(deg\))/,
                "default": "rotate(0deg)",
                "responsive": true,
                "min": -180,
                "max": 180,
                "step": 1,
                "control_class": "azh-transform-rotate",
                "control_type": "transform-rotate",
                "control_text": "Line 2 angle (deg)"
            },
        ]);
    }
    function reveal_trigger_utility() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "exists-class",
                "menu": "utility",
                "control_text": "Reveal trigger",
                "control_class": "azh-toggle",
                "control_type": "reveal-trigger",
                "selector": '.az-reveal-trigger',
                "class": "az-visible"
            }
        ]);
    }
    function template_utility() {
        if ('azt' in window && 'fields' in azt && Object.keys(azt.fields).length) {
            azh.html['none'] = 'none';
            azh.html['azt-count'] = 'azt-count';
            for (var field in azt.fields) {
                azh.html['azt-' + field] = 'azt-' + field;
                azh.html['azt-sum-' + field] = 'azt-sum-' + field;
            }
            var switcher_options = {"none": "Disabled", "azt-count": "Show rows count"};
            for (var field in azt.fields) {
                switcher_options['azt-' + field] = "Show '" + field + "' field value";
                switcher_options['azt-sum-' + field] = "Show '" + field + "' field sum";
            }
            var key_options = Object.assign.apply({}, [{'': 'None'}].concat(Object.keys(azt.fields).map((v, i) => ({[v]: Object.keys(azt.fields)[i]}))));
            var value_options = function ($control, $element) {
                $element.data('azt-value-control', $control);
                if ($element.attr('data-azt-key') && azt.fields[$element.attr('data-azt-key')]) {
                    return Object.assign.apply({}, [{'': 'None'}].concat(azt.fields[$element.attr('data-azt-key')].values.map((v, i) => ({[v]: azt.fields[$element.attr('data-azt-key')].values[i]}))));
                }
                if ($element.parent().attr('data-azt-key') && azt.fields[$element.parent().attr('data-azt-key')]) {
                    return Object.assign.apply({}, [{'': 'None'}].concat(azt.fields[$element.parent().attr('data-azt-key')].values.map((v, i) => ({[v]: azt.fields[$element.parent().attr('data-azt-key')].values[i]}))));
                }
                return {};
            };
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "dropdown-attribute",
                    "selector": '.azt-place-autocomplete',
                    "menu": "utility",
                    "options": {
                        '': 'All',
                        'regions': 'Regions',
                        'cities': 'Cities'
                    },
                    "attribute": "data-place-types",
                    "control_class": "azh-place-types",
                    "control_type": "azt-place-types",
                    "control_text": "Place types"
                },
                {
                    "type": "input-attribute",
                    "selector": '.azt-place-autocomplete',
                    "menu": "utility",
                    "attribute": "data-country",
                    "control_class": "azh-country",
                    "control_type": "azt-country",
                    "control_text": "Country (two letter code)"
                },
                {
                    "type": "exists-class",
                    "menu": "utility",
                    "group": 'Template',
                    "control_text": "Fill table rows",
                    "control_class": "azh-toggle",
                    "control_type": "exists-class",
                    "selector": '.az-table',
                    "class": "azt-table"
                },
                {
                    "type": "url-attribute",
                    "menu": "utility",
                    "group": 'Template',
                    "attribute": "href",
                    "control_class": "azh-link",
                    "control_type": "link",
                    "control_text": "Link URL",
                    "selector": '.az-url-back-button > [href], .az-url-breadcurmbs > [href], .az-url-field-up-down > div > [href]'
                },
                {
                    "refresh": function ($control, $element) {
                        $element.children().each(function () {
                            var $this = azh.$(this);
                            if ($this.data('azt-value-control')) {
                                $this.data('azt-value-control').trigger('azh-init');
                            }
                        });
                    },
                    "type": "dropdown-attribute",
                    "selector": '.az-url-back-button > [data-azt-key], .az-url-breadcurmbs > [data-azt-key], .az-url-field-up-down[data-azt-key]',
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-key",
                    "control_class": "azh-field",
                    "control_type": "azt-key",
                    "control_text": "Field for operation"
                },
                {
                    "type": "dropdown-attribute",
                    "selector": '.az-url-field-up-down > [data-azt-value]',
                    "menu": "utility",
                    "group": 'Template',
                    "options": value_options,
                    "attribute": "data-azt-value",
                    "control_class": "azh-value",
                    "control_type": "azt-value",
                    "control_text": "Value for operation"
                },
                {
                    "refresh": function ($control, $element) {
                        if ($element.data('azt-value-control')) {
                            $element.data('azt-value-control').trigger('azh-init');
                        }
                    },
                    "type": "dropdown-attribute",
                    "selector": 'a[href]:not(.az-js-click), [data-click-url]',
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-key",
                    "control_class": "azh-azt-key",
                    "control_type": "azt-key",
                    "control_text": "Field for clicked url"
                },
                {
                    "refresh": function ($control, $element) {
                    },
                    "options": value_options,
                    "select2": true,
                    "type": "dropdown-attribute",
                    "selector": 'a[href]:not(.az-js-click), [data-click-url]',
                    "menu": "utility",
                    "group": 'Template',
                    "attribute": "data-azt-value",
                    "control_class": "azh-azt-value",
                    "control_type": "azt-value",
                    "control_text": "Value for clicked url"
                },
                {
                    "refresh": function ($control, $element) {
                        if ($element.data('azt-value-control')) {
                            $element.data('azt-value-control').trigger('azh-init');
                        }
                    },
                    "type": "dropdown-attribute",
                    "selector": '[data-element]',
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-key",
                    "control_class": "azh-azt-key",
                    "control_type": "azt-key",
                    "control_text": "Field for inner content"
                },
                {
                    "refresh": function ($control, $element) {
                    },
                    "options": value_options,
                    "select2": true,
                    "type": "dropdown-attribute",
                    "selector": '[data-element]',
                    "menu": "utility",
                    "group": 'Template',
                    "attribute": "data-azt-value",
                    "control_class": "azh-azt-value",
                    "control_type": "azt-value",
                    "control_text": "Value for inner content"
                },
                {
                    "type": "exists-class",
                    "menu": "utility",
                    "group": 'Template',
                    "control_text": "Exists class",
                    "control_class": "azh-toggle",
                    "control_type": "exists-class",
                    "selector": '[data-element]',
                    "class": "azt-exists"
                },
                {
                    "type": "exists-class",
                    "menu": "utility",
                    "group": 'Template',
                    "control_text": "Hide if empty value",
                    "control_class": "azh-toggle",
                    "control_type": "hide-if-empty",
                    "selector": '[data-element]',
                    "class": "azt-hide-if-empty"
                },
                {
                    "type": "html-switcher",
                    "menu": "context",
                    "group": 'Template',
                    "options": switcher_options,
                    "control_class": "azh-azt-text",
                    "control_type": "azt-text",
                    "control_text": "Dynamic text",
                    "selector": '[contenteditable]'
                },
                {
                    "type": "dropdown-attribute",
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-background-image-key",
                    "control_text": "Background-image from field",
                    "control_class": "azh-azt-background-image-key",
                    "control_type": "azt-background-image-key",
                    "selector": '.az-free-positioning, .az-background',
                },
                {
                    "type": "dropdown-attribute",
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-src-key",
                    "control_text": "Image from field",
                    "control_class": "azh-azt-src-key",
                    "control_type": "azt-src-key",
                    "selector": 'img[src]',
                },
                {
                    "type": "dropdown-attribute",
                    "selector": 'a[href]:not(.az-js-click), [data-click-url]',
                    "menu": "utility",
                    "group": 'Template',
                    "options": key_options,
                    "attribute": "data-azt-url-key",
                    "control_class": "azh-azt-url-key",
                    "control_type": "azt-url-key",
                    "control_text": "Click-url from field"
                },
            ]);

            return;
            for (var field in azt.fields) {
                azh.controls_options = azh.controls_options.concat([
                    {
                        "type": "exists-class",
                        "menu": "utility",
                        "group": 'Template',
                        "control_text": "Class from '" + field + "' field",
                        "control_class": "azh-toggle",
                        "control_type": "field-class",
                        "selector": '[data-element]',
                        "class": "azt-" + field
                    },
                    {
                        "type": "exists-attribute",
                        "menu": "utility",
                        "group": 'Template',
                        "control_text": "'data-" + field + "' attribute",
                        "control_class": "azh-toggle",
                        "control_type": "data-" + field + "-attribute",
                        "selector": '[data-element]',
                        "attribute": 'data-' + field
                    },
                ]);
            }
        }
    }



    //ELEMENTS
    function google_map_utility() {
        azh.standard_elements_start_classes['.az-gmap'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "selector": ".az-gmap",
                "menu": "utility",
                "group": "Map settings",
                "property": "height",
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Google Map height"
            },
            {
                "refresh": true,
                "type": "integer-attribute",
                "selector": ".az-gmap[data-zoom]",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-zoom",
                "control_class": "azh-integer",
                "control_type": "zoom",
                "control_text": "Google Map Zoom"
            },
            {
                "refresh": true,
                "type": "image-attribute",
                "selector": ".az-gmap[data-marker]",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-marker",
                "control_class": "azh-image",
                "control_type": "marker",
                "control_text": "Google Map location marker image"
            },
            {
                "refresh": true,
                "type": "integer-attribute",
                "selector": ".az-gmap",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-offset-y",
                "min": "-300",
                "max": "300",
                "step": "1",
                "control_class": "azh-integer",
                "control_type": "offset-y",
                "control_text": "Marker offset-y"
            },
            {
                "refresh": true,
                "type": "integer-attribute",
                "selector": ".az-gmap",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-offset-x",
                "min": "-300",
                "max": "300",
                "step": "1",
                "control_class": "azh-integer",
                "control_type": "offset-x",
                "control_text": "Marker offset-x"
            },
            {
                "refresh": true,
                "type": "input-attribute",
                "selector": ".az-gmap[data-gmap-api-key]",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-gmap-api-key",
                "control_class": "azh-text",
                "control_type": "gmap-api-key",
                "control_text": "Google Map API key"
            },
            {
                "refresh": true,
                "type": "textarea-attribute",
                "selector": ".az-gmap",
                "menu": "utility",
                "group": "Map settings",
                "attribute": "data-styles",
                "control_class": "azh-text",
                "control_type": "styles",
                "description": 'Use <a href="https://snazzymaps.com/" target="_blank">styles generator</a> to make JSON',
                "control_text": "Google Map styles JSON code"
            }
        ]);
        //
        azh.modal_options = azh.modal_options.concat([
            {
                "refresh": true,
                "menu": "utility",
                "group": "Map settings",
                "button_text": "Edit Google Map Location",
                "button_class": "azh-gmap-location",
                "button_type": "azh-gmap-location",
                "title": "Edit Google Map Location",
                "desc": "Specify the latitude and longitude of the google map",
                "selector": ".az-gmap",
                "attributes": {
                    'data-latitude': {
                        "label": "Latitude"
                    },
                    'data-longitude': {
                        "label": "Longitude"
                    }
                }
            }
        ]);
    }
    function free_positioning_utility() {
        azh.standard_elements_start_classes['.az-free-positioning'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Free positioning settings",
                "selector": ".az-free-positioning",
                "property": "width",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Free positioning settings",
                "selector": ".az-free-positioning",
                "property": "height",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "type": "exists-class",
                "selector": ".az-free-positioning",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_class": "azh-overflow-hidden azh-toggle",
                "control_type": "overflow-hidden",
                "control_text": "Overflow hidden",
                "class": "az-overflow-hidden",
            },            
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Free positioning element",
                "selector": ".az-free-positioning > [data-element]",
                "property": "z-index",
                "control_class": "azh-integer",
                "control_type": "z-index",
                "control_text": "Element z-index"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning element",
                "control_text": "Max-width of container width",
                "control_class": "azh-toggle",
                "control_type": "container",
                "selector": ".az-free-positioning > [data-element]",
                "class": "az-container"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning element",
                "control_text": "Full width",
                "control_class": "azh-toggle",
                "control_type": "full-width",
                "selector": ".az-free-positioning > [data-element]",
                "class": "az-full-width"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning element",
                "control_text": "Full height",
                "control_class": "azh-toggle",
                "control_type": "full-height",
                "selector": ".az-free-positioning > [data-element]",
                "class": "az-full-height"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Auto rescale",
                "control_class": "azh-toggle",
                "control_type": "auto-rescale",
                "selector": ".az-free-positioning",
                "class": "az-auto-rescale"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Full width auto upscale",
                "control_class": "azh-toggle",
                "control_type": "upscale",
                "selector": ".az-free-positioning",
                "class": "az-auto-upscale"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Percentage positions",
                "control_class": "azh-toggle",
                "control_type": "percentage",
                "selector": ".az-free-positioning",
                "class": "az-percentage"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Full screen height",
                "control_class": "azh-toggle azh-full-screen-height",
                "control_type": "full-height",
                "selector": ".az-free-positioning",
                "class": "az-full-screen-height"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "As layer for parent",
                "control_class": "azh-toggle azh-free-positioning-layer",
                "control_type": "layer",
                "selector": ".az-free-positioning",
                "class": "az-layer"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Free positioning settings",
                "selector": ".az-free-positioning",
                "property": "z-index",
                "control_class": "azh-integer azh-free-positioning-z-index",
                "control_type": "z-index",
                "control_text": "Layer z-index"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Disable mouse events",
                "control_class": "azh-toggle azh-free-positioning-pointer-events",
                "control_type": "pointer-events",
                "selector": ".az-free-positioning",
                "class": "az-disable-pointer-events"
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Free positioning settings",
                "attribute": "data-full-screen-height-offset",
                "control_class": "azh-full-screen-height-offset",
                "control_type": "full-screen-height-offset",
                "selector": ".az-free-positioning",
                "control_text": "Full screen height offset"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning settings",
                "control_text": "Full screen height cover mode",
                "control_class": "azh-toggle azh-full-screen-height-cover",
                "control_type": "full-screen-height-cover",
                "selector": ".az-free-positioning",
                "class": "az-full-screen-height-cover"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Free positioning element",
                "control_text": "Enable mouse events",
                "control_class": "azh-toggle azh-free-positioning-pointer-events",
                "control_type": "pointer-events",
                "selector": ".az-free-positioning > [data-element], .az-free-positioning > .az-elements-list > [data-element]",
                "class": "az-enable-pointer-events"
            },
        ]);
        transform_utility('.az-free-positioning > [data-element] > *, .az-free-positioning > .az-elements-list > [data-element] > *', "Free positioning element");
        background_utility('.az-free-positioning', 'Background');
        background_utility('.az-free-positioning > .az-overlay > div', 'Background overlay');
    }
    function button_utility() {
        azh.standard_elements_start_classes['.az-button'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "url-attribute",
                "menu": "utility",
                "attribute": "href",
                "control_class": "azh-link",
                "control_type": "link",
                "control_text": "Button URL",
                "selector": '.az-button a'
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "options": {
                    "_self": "Current window",
                    "_blank": "New window"
                },
                "attribute": "target",
                "control_class": "azh-target",
                "control_type": "target",
                "control_text": "Open window",
                "selector": '.az-button a'
            },
            {
                "type": "dropdown-attribute",
                "selector": ".az-button",
                "menu": "utility",
                "options": {
                    "none": "No icon",
                    "left": "Left",
                    "right": "Right"
                },
                "attribute": "data-icon",
                "control_class": "azh-button-icon",
                "control_type": "button-icon",
                "control_text": "Icon"
            },
            {
                "type": "icon-class",
                "menu": "utility",
                "control_class": "azh-left-icon",
                "control_type": "left-icon",
                "control_text": "Icon",
                "selector": '.az-button .az-icon:first-child'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "responsive": true,
                "property": "margin-right",
                "min": "0",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-left-icon-spacing",
                "control_type": "icon-spacing",
                "control_text": "Icon Spacing",
                "selector": '.az-button .az-icon:first-child'
            },
            {
                "type": "icon-class",
                "menu": "utility",
                "control_class": "azh-right-icon",
                "control_type": "right-icon",
                "control_text": "Icon",
                "selector": '.az-button .az-icon:last-child'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "responsive": true,
                "property": "margin-left",
                "min": "0",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-right-icon-spacing",
                "control_type": "icon-spacing",
                "control_text": "Icon Spacing",
                "selector": '.az-button .az-icon:last-child'
            },
            {
                "type": "radio-classes",
                "menu": "utility",
                "selector": ".az-button",
                "classes": {
                    "az-left": "Left",
                    "az-center": "Center",
                    "az-right": "Right",
                    "az-full-width": "Full width",
                },
                "control_class": "azh-horizontal-align",
                "control_type": "horizontal-align",
                "control_text": "Button horizontal align"
            }
        ]);
        font_utility('.az-button a', 'Font styles', 'Normal');
        font_utility('.az-button a', 'Font styles', 'Hover', 'data-hover', hover_refresh);
        font_utility('.az-button a', 'Font styles', 'Reveal', 'data-reveal', reveal_refresh);
        text_utility('.az-button', 'Text styles');
        background_utility('.az-button a', 'Background', 'Normal');
        background_utility('.az-button a', 'Background', 'Hover', 'data-hover', hover_refresh);
        background_utility('.az-button a', 'Background', 'Reveal', 'data-reveal', reveal_refresh);
        border_utility('.az-button a', 'Border', 'Normal');
        border_utility('.az-button a', 'Border', 'Hover', 'data-hover', hover_refresh);
        border_utility('.az-button a', 'Border', 'Reveal', 'data-reveal', reveal_refresh);
        box_shadow_utility('.az-button a', 'Shadow', 'Normal');
        box_shadow_utility('.az-button a', 'Shadow', 'Hover', 'data-hover', hover_refresh);
        box_shadow_utility('.az-button a', 'Shadow', 'Reveal', 'data-reveal', reveal_refresh);
        box_utility('.az-button a', "Button-box styles");
    }
    function icon_utility() {
        azh.standard_elements_start_classes['.az-icon-element'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "icon-class",
                "menu": "utility",
                "control_class": "azh-icon",
                "control_type": "icon",
                "control_text": "Icon",
                "selector": '.az-icon-element .az-icon'
            },
        ]);
        text_utility('.az-icon-element', 'Text styles');
        text_stroke_utility('.az-icon-element', 'Text stroke', 'Normal');
        text_stroke_utility('.az-icon-element', 'Text stroke', 'Hover', 'data-hover', hover_refresh);
        text_stroke_utility('.az-icon-element', 'Text stroke', 'Reveal', 'data-reveal', reveal_refresh);
        font_utility('.az-icon-element .az-icon', 'Font styles', 'Normal');
        font_utility('.az-icon-element .az-icon', 'Font styles', 'Hover', 'data-hover', hover_refresh);
        font_utility('.az-icon-element .az-icon', 'Font styles', 'Reveal', 'data-reveal', reveal_refresh);
        background_utility('.az-icon-element .az-icon', 'Background', 'Normal');
        background_utility('.az-icon-element .az-icon', 'Background', 'Hover', 'data-hover', hover_refresh);
        background_utility('.az-icon-element .az-icon', 'Background', 'Reveal', 'data-reveal', reveal_refresh);
        border_utility('.az-icon-element .az-icon', 'Border', 'Normal');
        border_utility('.az-icon-element .az-icon', 'Border', 'Hover', 'data-hover', hover_refresh);
        border_utility('.az-icon-element .az-icon', 'Border', 'Reveal', 'data-reveal', reveal_refresh);
        box_shadow_utility('.az-icon-element .az-icon', 'Shadow', 'Normal');
        box_shadow_utility('.az-icon-element .az-icon', 'Shadow', 'Hover', 'data-hover', hover_refresh);
        box_shadow_utility('.az-icon-element .az-icon', 'Shadow', 'Reveal', 'data-reveal', reveal_refresh);
        box_utility('.az-icon-element .az-icon', "Icon-box styles");
    }
    function hyperlink_utility() {
        azh.standard_elements_start_classes['.az-hyperlink'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "url-attribute",
                "menu": "utility",
                "attribute": "href",
                "control_class": "azh-link",
                "control_type": "link",
                "control_text": "Link URL",
                "selector": 'a.az-hyperlink'
            },
            {
                "type": "dropdown-attribute",
                "menu": "utility",
                "options": {
                    "_self": "Current window",
                    "_blank": "New window"
                },
                "attribute": "target",
                "control_class": "azh-target",
                "control_type": "target",
                "control_text": "Open window",
                "selector": 'a.az-hyperlink'
            },
        ]);
    }
    function image_utility() {
        azh.standard_elements_start_classes['.az-image'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "image-attribute",
                "menu": "utility",
                "group": "Image",
                "attribute": "src",
                "control_class": "azh-image",
                "control_type": "image",
                "control_text": "Image URL",
                "selector": '.az-image img'
            },
            {
                "type": "radio-style",
                "menu": "utility",
                "group": "Image",
                "selector": '.az-image',
                "responsive": true,
                "property": "text-align",
                "options": {
                    "left": "Left",
                    "center": "Center",
                    "right": "Right"
                },
                "control_class": "azh-text-align",
                "control_type": "text-align",
                "control_text": "Image alignment"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Image",
                "responsive": true,
                "property": "max-width",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-size",
                "control_type": "size",
                "control_text": "Size",
                "selector": '.az-image img'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Image",
                "min": "0",
                "max": "1",
                "step": "0.01",
                "control_class": "azh-opacity",
                "control_type": "opacity",
                "control_text": "Opacity",
                "property": "opacity",
                "selector": '.az-image img'
            }
        ]);
        border_utility('.az-image img', 'Border');
        box_shadow_utility('.az-image img', 'Shadow');
        filter_utility('.az-image img', 'Image filters', 'Normal');
        filter_utility('.az-image img', 'Image filters', 'Hover', 'data-hover', hover_refresh);
        filter_utility('.az-image img', 'Image filters', 'Reveal', 'data-reveal', reveal_refresh);
    }
    function video_element_utility() {
        azh.standard_elements_start_classes['.az-video'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "selector": ".az-video",
                "menu": "utility",
                "group": "Video",
                "property": "height",
                "responsive": true,
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Video height"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Video",
                "responsive": true,
                "property": "width",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "%",
                "control_class": "azh-size",
                "control_type": "size",
                "control_text": "Video size",
                "selector": '.az-video'
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Video Overlay",
                "control_class": "azh-toggle azh-show",
                "control_type": "show",
                "control_text": "Show Overlay",
                "class": "az-show",
                "selector": '.az-video-wrapper .az-video-overlay'
            },
        ]);
        iframe_video_utility("iframe.az-video", "Video");
        video_utility("video.az-video", "Video");
        background_utility('.az-video-wrapper .az-video-overlay', 'Video Overlay');
    }
    function background_element_utility() {
        azh.standard_elements_start_classes['.az-background'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }

        background_utility('.az-background', 'Background', 'Normal');
        background_utility('.az-background', 'Background', 'Hover', 'data-hover', hover_refresh);
        background_utility('.az-background', 'Background', 'Reveal', 'data-reveal', reveal_refresh);
        filter_utility('.az-background', 'Background filters', 'Normal');
        filter_utility('.az-background', 'Background filters', 'Hover', 'data-hover', hover_refresh);
        filter_utility('.az-background', 'Background filters', 'Reveal', 'data-reveal', reveal_refresh);
        background_utility('.az-background > .az-overlay > div', 'Background overlay');
        transform_utility('.az-background > .az-overlay > div', 'Background overlay');
        filter_utility('.az-background > .az-overlay > div', 'Background overlay');
        //shape_overlay_utility(".az-background > .az-overlay > div", "Background overlay");

        border_utility('.az-background', 'Border', 'Normal');
        border_utility('.az-background', 'Border', 'Hover', 'data-hover', hover_refresh);
        border_utility('.az-background', 'Border', 'Reveal', 'data-reveal', reveal_refresh);
        box_shadow_utility('.az-background', 'Shadow', 'Normal');
        box_shadow_utility('.az-background', 'Shadow', 'Hover', 'data-hover', hover_refresh);
        box_shadow_utility('.az-background', 'Shadow', 'Reveal', 'data-reveal', reveal_refresh);
//        font_utility('.az-background', 'Font styles', 'Normal');
//        font_utility('.az-background', 'Font styles', 'Hover', 'data-hover', hover_refresh);
//        font_utility('.az-background', 'Font styles', 'Reveal', 'data-reveal', reveal_refresh);
//        text_utility('.az-background', 'Text styles');
        box_utility('.az-background', "Background-box styles");
    }
    function form_utility() {
        //azh.standard_elements_start_classes['.az-form'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        function focus_refresh($control, $element) {
            azh.window.get(0).azh.refresh_focus_css_rules($element);
        }
        function placeholder_refresh($control, $element) {
            azh.window.get(0).azh.refresh_placeholder_css_rules($element);
        }
        function checkboxes_utility() {
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Checkbox-field name",
                    "pattern": /()(.+)(\[\])/,
                    "default": "checkbox[]",
                    "control_class": "azh-name",
                    "control_type": "name",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "input[type='checkbox'][name]",
                    "attribute": "name",
                    "unique_wrapper": 'form, [data-section]',
                    "unique": '[name="{name}"]',
                    "unique_exception": '[name*="[]"]'
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Checkbox value",
                    "control_class": "azh-checkbox-value",
                    "control_type": "value",
                    "selector": ".az-checkboxes input[type='checkbox']",
                    "attribute": "value"
                },
                {
                    "type": "input-innertext",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Checkbox label",
                    "control_class": "azh-checkbox-label",
                    "control_type": "label",
                    "selector": ".az-checkboxes.az-textual label > span:not(.az-tick)"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Horizontal spacing",
                    "control_class": "azh-checkbox-h-spacing",
                    "control_type": "h-spacing",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-right",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "label"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Vertical spacing",
                    "control_class": "azh-checkbox-v-spacing",
                    "control_type": "v-spacing",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-bottom",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "label"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick indent",
                    "control_class": "azh-checkbox-indent",
                    "control_type": "indent",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-right",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick width",
                    "control_class": "azh-checkbox-width",
                    "control_type": "width",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "width",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick height",
                    "control_class": "azh-checkbox-height",
                    "control_type": "height",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "height",
                    "selector": ".az-checkboxes",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "icon-class",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_class": "azh-icon",
                    "control_type": "icon",
                    "control_text": "Tick icon",
                    "selector": '.az-checkboxes',
                    "multiplying_selector": 'span.az-tick .az-icon'
                }
            ]);
            font_utility('.az-checkboxes.az-textual', 'Font styles');
            text_utility('.az-checkboxes.az-textual', 'Text styles');

            font_utility('.az-checkboxes', 'Tick font styles', false, false, false, 'span.az-tick');
            border_utility('.az-checkboxes', 'Tick border', false, false, false, 'span.az-tick');
            background_utility('.az-checkboxes', 'Tick background', false, false, false, 'span.az-tick');
            box_shadow_utility('.az-checkboxes', 'Tick shadow', false, false, false, 'span.az-tick');
        }
        function radio_button_utility() {
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Radio-field name",
                    "control_class": "azh-name",
                    "control_type": "name",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "input[type='radio'][name]",
                    "attribute": "name",
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Radio button value",
                    "control_class": "azh-radio-value",
                    "control_type": "value",
                    "selector": ".az-radio-buttons input[type='radio']",
                    "attribute": "value"
                },
                {
                    "type": "input-innertext",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Radio button label",
                    "control_class": "azh-radio-label",
                    "control_type": "label",
                    "selector": ".az-radio-buttons.az-textual label > span:not(.az-tick)"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Horizontal spacing",
                    "control_class": "azh-radio-h-spacing",
                    "control_type": "h-spacing",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-right",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "label"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Vertical spacing",
                    "control_class": "azh-radio-v-spacing",
                    "control_type": "v-spacing",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-bottom",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "label"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick indent",
                    "control_class": "azh-radio-indent",
                    "control_type": "indent",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "margin-right",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick width",
                    "control_class": "azh-radio-width",
                    "control_type": "width",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "width",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "integer-style",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_text": "Tick height",
                    "control_class": "azh-radio-height",
                    "control_type": "height",
                    "min": "0",
                    "max": "50",
                    "step": "1",
                    "units": "px",
                    "property": "height",
                    "selector": ".az-radio-buttons",
                    "multiplying_selector": "label .az-tick"
                },
                {
                    "type": "icon-class",
                    "menu": "utility",
                    "group": "Form field styles",
                    "control_class": "azh-icon",
                    "control_type": "icon",
                    "control_text": "Tick icon",
                    "selector": '.az-radio-buttons',
                    "multiplying_selector": 'span.az-tick .az-icon'
                }
            ]);
            font_utility('.az-radio-buttons.az-textual', 'Font styles');
            text_utility('.az-radio-buttons.az-textual', 'Text styles');

            font_utility('.az-radio-buttons', 'Tick font styles', false, false, false, 'span.az-tick');
            border_utility('.az-radio-buttons', 'Tick border', false, false, false, 'span.az-tick');
            background_utility('.az-radio-buttons', 'Tick background', false, false, false, 'span.az-tick');
            box_shadow_utility('.az-radio-buttons', 'Tick shadow', false, false, false, 'span.az-tick');
        }
        function ion_range_utility() {
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "dropdown-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "options": {
                        "single": "Single",
                        "double": "Double"
                    },
                    "attribute": "data-type",
                    "control_class": "azh-data-type",
                    "control_type": "data-type",
                    "control_text": "Slider type",
                    "selector": 'input[data-type].ion-range-slider'
                },
                {
                    "type": "toggle-attribute",
                    "selector": "input[data-grid].ion-range-slider",
                    "menu": "utility",
                    "group": "Form field settings",
                    "attribute": "data-grid",
                    "control_class": "azh-toggle",
                    "control_type": "grid",
                    "control_text": "Slider grid"
                },
                {
                    "type": "dropdown-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "options": {
                        "flat": "flat",
                        "big": "big",
                        "modern": "modern",
                        "sharp": "sharp",
                        "round": "round",
                        "square": "square",
                    },
                    "attribute": "data-skin",
                    "control_class": "azh-data-skin",
                    "control_type": "data-skin",
                    "control_text": "Slider skin",
                    "selector": 'input[data-skin].ion-range-slider'
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Default 'from' value",
                    "control_class": "azh-from-value",
                    "control_type": "from-value",
                    "selector": "input[data-from].ion-range-slider",
                    "attribute": "data-from"
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Default 'to' value",
                    "control_class": "azh-to-value",
                    "control_type": "to-value",
                    "selector": "input[data-to].ion-range-slider",
                    "attribute": "data-to"
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Minimum",
                    "control_class": "azh-minimum",
                    "control_type": "minimum",
                    "selector": "input[data-min].ion-range-slider",
                    "attribute": "data-min"
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Maximum",
                    "control_class": "azh-maximum",
                    "control_type": "maximum",
                    "selector": "input[data-max].ion-range-slider",
                    "attribute": "data-max"
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Step",
                    "control_class": "azh-step",
                    "control_type": "step",
                    "selector": "input[data-step].ion-range-slider",
                    "attribute": "data-step"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Prefix",
                    "control_class": "azh-prefix",
                    "control_type": "prefix",
                    "selector": "[data-prefix].ion-range-slider",
                    "attribute": "data-prefix"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Postfix",
                    "control_class": "azh-postfix",
                    "control_type": "prefix",
                    "selector": "[data-postfix].ion-range-slider",
                    "attribute": "data-postfix"
                },
            ]);

        }
        function select_utility() {
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Multiselect-field name",
                    "pattern": /()(.+)(\[\])/,
                    "default": "multiselect[]",
                    "control_class": "azh-name",
                    "control_type": "name",
                    "selector": "select[multiple][name].az-select",
                    "attribute": "name",
                    "unique_wrapper": 'form, [data-section]',
                    "unique": '[name="{name}"]',
                    "unique_exception": '[name*="[]"]'
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Dropdown-field name",
                    "control_class": "azh-name",
                    "control_type": "name",
                    "selector": "select:not([multiple])[name].az-select",
                    "attribute": "name",
                    "unique_wrapper": 'form, [data-section]',
                    "unique": '[name="{name}"]',
                    "unique_exception": '[name*="[]"]'
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Select-option value",
                    "control_class": "azh-option-value",
                    "control_type": "value",
                    "selector": "select.az-select option",
                    "attribute": "value"
                },
                {
                    "type": "input-innertext",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Select-option label",
                    "control_class": "azh-option-label",
                    "control_type": "label",
                    "selector": "select.az-select option"
                }
            ]);
            font_utility('.az-select', 'Form field font styles');
            font_utility('.az-select', 'Placeholder styles', false, 'data-placeholder', placeholder_refresh);
            text_utility('.az-select', 'Form field text styles');
            background_utility('.az-select', 'Form field background', 'Normal');
            background_utility('.az-select', 'Form field background', 'Focus', 'data-focus', focus_refresh);
            border_utility('.az-select', 'Form field border', 'Normal');
            border_utility('.az-select', 'Form field border', 'Focus', 'data-focus', focus_refresh);
            box_shadow_utility('.az-select', 'Form field shadow', 'Normal');
            box_shadow_utility('.az-select', 'Form field shadow', 'Focus', 'data-focus', focus_refresh);
            box_utility('.az-select', "Form field-box styles");
        }
        function field_utility() {
            azh.controls_options = azh.controls_options.concat([
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Field name",
                    "control_class": "azh-name",
                    "control_type": "name",
                    "selector": "[name].az-field, [name].az-file-field",
                    "attribute": "name",
                    "unique_wrapper": 'form, [data-section]',
                    "unique": '[name="{name}"]',
                    "unique_exception": '[name*="[]"]'
                },
                {
                    "type": "dropdown-attribute",
                    "selector": 'input.az-field:not([type="file"]):not([type="range"])',
                    "menu": "utility",
                    "group": "Form field settings",
                    "options": {
                        "text": 'text',
                        "color": 'color',
                        "date": 'date',
                        "datetime": 'datetime',
                        "datetime-local": 'datetime-local',
                        "email": 'email',
                        "number": 'number',
                        "tel": 'tel',
                        "time": 'time',
                        "url": 'url',
                        "month": 'month',
                        "week": 'week'
                    },
                    "attribute": "type",
                    "control_class": "azh-dropdown",
                    "control_type": "input-type",
                    "control_text": "Input type"
                },
                {
                    "type": "exists-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Required",
                    "control_class": "azh-toggle",
                    "control_type": "required",
                    "selector": "form input:not([type='submit']).az-field, form .az-file-field",
                    "attribute": "required"
                },
                {
                    "type": "integer-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "attribute": "maxlength",
                    "control_class": "azh-maxlength",
                    "control_type": "maxlength",
                    "selector": "input[maxlength].az-field",
                    "control_text": "Maximum input length"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "attribute": "pattern",
                    "control_class": "azh-pattern",
                    "control_type": "pattern",
                    "selector": "input[pattern].az-field",
                    "control_text": "Input regular expression"
                },
                {
                    "refresh": true,
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "attribute": "data-mask",
                    "control_class": "azh-mask",
                    "control_type": "mask",
                    "selector": "input[data-mask].az-field",
                    "control_text": "Value mask, use: 'a', '9', '*'"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Default field value",
                    "control_class": "azh-value",
                    "control_type": "value",
                    "selector": "input[value].az-field",
                    "attribute": "value"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Minimum",
                    "control_class": "azh-minimum",
                    "control_type": "minimum",
                    "selector": "input[min].az-field",
                    "attribute": "min"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Maximum",
                    "control_class": "azh-maximum",
                    "control_type": "maximum",
                    "selector": "input[max].az-field",
                    "attribute": "max"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Step",
                    "control_class": "azh-step",
                    "control_type": "step",
                    "selector": "input[step].az-field",
                    "attribute": "step"
                },
                {
                    "type": "input-attribute",
                    "menu": "utility",
                    "group": "Form field settings",
                    "control_text": "Field placeholder",
                    "control_class": "azh-field-placeholder",
                    "control_type": "field-placeholder",
                    "selector": "[placeholder].az-field",
                    "attribute": "placeholder"
                }
            ]);
            font_utility('.az-field', 'Font styles');
            font_utility('.az-field', 'Placeholder styles', false, 'data-placeholder', placeholder_refresh);
            text_utility('.az-field', 'Text styles');
            background_utility('.az-field', 'Background', 'Normal');
            background_utility('.az-field', 'Background', 'Focus', 'data-focus', focus_refresh);
            border_utility('.az-field', 'Border', 'Normal');
            border_utility('.az-field', 'Border', 'Focus', 'data-focus', focus_refresh);
            box_shadow_utility('.az-field', 'Shadow', 'Normal');
            box_shadow_utility('.az-field', 'Shadow', 'Focus', 'data-focus', focus_refresh);
            box_utility('.az-field', "Form field-box styles");
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "radio-classes",
                "menu": "utility",
                "selector": ".az-field, .az-select",
                "classes": {
                    "az-left": "Left",
                    "az-center": "Center",
                    "az-right": "Right",
                    "az-full-width": "Full width",
                },
                "control_class": "azh-horizontal-align",
                "control_type": "horizontal-align",
                "control_text": "Horizontal align"
            },
        ]);
        field_utility();
        checkboxes_utility();
        radio_button_utility();
        select_utility();
        ion_range_utility();
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "exists-attribute",
                "menu": "utility",
                "group": "Form field settings",
                "control_text": "Required",
                "control_class": "azh-toggle",
                "control_type": "required",
                "selector": "form input[type='checkbox'], form input[type='radio']",
                "attribute": "required"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Form field settings",
                "control_text": "Hidden field name",
                "control_class": "azh-name",
                "control_type": "name",
                "selector": "[type='hidden'][name]:not([name='form_title'])",
                "not_selector": ".az-liquid-container [type='hidden']",
                "attribute": "name",
                "unique_wrapper": 'form, [data-section]',
                "unique": '[name="{name}"]',
                "unique_exception": '[name*="[]"]'
            },
            {
                "type": "exists-attribute",
                "menu": "utility",
                "group": "Form field settings",
                "control_text": "Checked",
                "control_class": "azh-toggle",
                "control_type": "checked",
                "selector": "form input[type='checkbox'], form input[type='radio']",
                "attribute": "checked"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Form settings",
                "control_text": "Backend form identifier",
                "control_class": "azh-form-title",
                "control_type": "hidden",
                "selector": "[type='hidden'][name='form_title']",
                "attribute": "value"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Form field settings",
                "control_text": "Hidden field value",
                "control_class": "azh-hidden",
                "control_type": "hidden",
                "selector": "input[type='hidden'][value]:not([name='form_title'])",
                "not_selector": ".az-liquid-container [type='hidden']",
                "attribute": "value"
            },
            {
                "type": "url-attribute",
                "menu": "utility",
                "group": "Form settings",
                "control_text": "Confirmation redirect",
                "control_class": "azh-redirect",
                "control_type": "redirect",
                "selector": "form[data-success-redirect]",
                "attribute": "data-success-redirect"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Form settings",
                "control_text": "Confirmation message",
                "control_class": "azh-success",
                "control_type": "success",
                "selector": "form[data-success]",
                "attribute": "data-success"
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Form settings",
                "control_text": "Error message",
                "control_class": "azh-error",
                "control_type": "error",
                "selector": "form[data-error]",
                "attribute": "data-error"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Form settings",
                "control_text": "Horizontal layout",
                "control_class": "azh-toggle azh-horizontal",
                "control_type": "horizontal",
                "selector": ".az-form",
                "class": "az-horizontal"
            },
            {
                "type": "radio-classes",
                "menu": "utility",
                "selector": 'form button[type="submit"]',
                "classes": {
                    "az-left": "Left",
                    "az-center": "Center",
                    "az-right": "Right",
                    "az-full-width": "Full width",
                },
                "control_class": "azh-horizontal-align",
                "control_type": "horizontal-align",
                "control_text": "Submit button horizontal align"
            },
            {
                "refresh": true,
                "type": "input-attribute",
                "input_type": "text",
                "menu": "utility",
                "group": "Form field settings",
                "control_text": "re-CAPTCHA sitekey",
                "control_class": "azh-sitekey",
                "control_type": "sitekey",
                "selector": ".g-recaptcha",
                "attribute": "data-sitekey"
            },
        ]);

        text_utility('form button[type="submit"]', 'Submit button text styles');
        font_utility('form button[type="submit"]', 'Submit button font styles', 'Normal');
        font_utility('form button[type="submit"]', 'Submit button font styles', 'Hover', 'data-hover', hover_refresh);
        font_utility('form button[type="submit"]', 'Submit button font styles', 'Reveal', 'data-reveal', reveal_refresh);
        background_utility('form button[type="submit"]', 'Submit button background', 'Normal');
        background_utility('form button[type="submit"]', 'Submit button background', 'Hover', 'data-hover', hover_refresh);
        background_utility('form button[type="submit"]', 'Submit button background', 'Reveal', 'data-reveal', reveal_refresh);
        border_utility('form button[type="submit"]', 'Submit button border', 'Normal');
        border_utility('form button[type="submit"]', 'Submit button border', 'Hover', 'data-hover', hover_refresh);
        border_utility('form button[type="submit"]', 'Submit button border', 'Reveal', 'data-reveal', reveal_refresh);
        box_shadow_utility('form button[type="submit"]', 'Submit button shadow', 'Normal');
        box_shadow_utility('form button[type="submit"]', 'Submit button shadow', 'Hover', 'data-hover', hover_refresh);
        box_shadow_utility('form button[type="submit"]', 'Submit button shadow', 'Reveal', 'data-reveal', reveal_refresh);
        box_utility('form button[type="submit"]', "Submit button-box styles");
    }
    function remark_utility() {
        azh.standard_elements_start_classes['.az-remarkable'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "exists-class",
                "selector": ".az-remarkable",
                "menu": "utility",
                "group": "Remark",
                "control_class": "azh-overflow-hidden azh-toggle",
                "control_type": "overflow-hidden",
                "control_text": "Overflow hidden",
                "class": "az-overflow-hidden",
            },
            {
                "type": "dropdown-classes",
                "selector": ".az-remarkable > [data-cloneable] > .az-remark-wrapper",
                "menu": "utility",
                "group": "Remark",
                "responsive": true,
                "classes": {
                    "az-top-left-outside": "Top Left Outside",
                    "az-top-center-outside": "Top Center Outside",
                    "az-top-right-outside": "Top Right Outside",
                    "az-middle-left-outside": "Middle Left Outside",
                    "az-middle-center-outside": "Middle Center Outside",
                    "az-middle-right-outside": "Middle Right Outside",
                    "az-bottom-left-outside": "Bottom Left Outside",
                    "az-bottom-center-outside": "Bottom Center Outside",
                    "az-bottom-right-outside": "Bottom Right Outside",
                    "az-top-left-inside": "Top Left Inside",
                    "az-top-center-inside": "Top Center Inside",
                    "az-top-right-inside": "Top Right Inside",
                    "az-middle-left-inside": "Middle Left Inside",
                    "az-middle-center-inside": "Middle Center Inside",
                    "az-middle-right-inside": "Middle Right Inside",
                    "az-bottom-left-inside": "Bottom Left Inside",
                    "az-bottom-center-inside": "Bottom Center Inside",
                    "az-bottom-right-inside": "Bottom Right Inside"
                },
                "control_class": "azh-shape azh-position-origin",
                "control_type": "position-origin",
                "control_text": "Remark position origin"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Remark",
                "control_text": "Full width",
                "control_class": "azh-toggle",
                "control_type": "full-width",
                "selector": ".az-remarkable > [data-cloneable] > .az-remark-wrapper",
                "class": "az-full-width"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Remark",
                "control_text": "Full height",
                "control_class": "azh-toggle",
                "control_type": "full-height",
                "selector": ".az-remarkable > [data-cloneable] > .az-remark-wrapper",
                "class": "az-full-height"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Remark",
                "selector": ".az-remarkable > [data-cloneable] > .az-remark-wrapper",
                "property": "z-index",
                "control_class": "azh-integer",
                "control_type": "z-index",
                "control_text": "Remark z-index"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Remark",
                "selector": ".az-remarkable > [data-cloneable] > .az-remark-wrapper > .az-remark",
                "property": "width",
                "responsive": true,
                "slider": true,
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Remark width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Remark",
                "min": "0",
                "max": "1",
                "step": "0.01",
                "control_class": "azh-opacity",
                "control_type": "opacity",
                "control_text": "Opacity",
                "property": "opacity",
                "selector": '.az-remarkable > [data-cloneable] > .az-remark-wrapper > .az-remark'
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Remark",
                "control_text": "Disable pointer events",
                "control_class": "azh-toggle azh-disable-pointer-events",
                "control_type": "disable-pointer-events",
                "selector": '.az-remarkable > [data-cloneable] > .az-remark-wrapper',
                "class": "az-disable-pointer-events"
            },
        ]);
        absolute_position_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper', 'Remark', 'Normal');
        absolute_position_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper', 'Remark', 'Hover', 'data-hover', hover_refresh, false, false, true);
        absolute_position_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper', 'Remark', 'Reveal', 'data-reveal', reveal_refresh, false, false, true);
        transform_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper > .az-remark', 'Remark', 'Normal');
        transform_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper > .az-remark', 'Remark', 'Hover', 'data-hover', hover_refresh, false, false, true);
        transform_utility('.az-remarkable > [data-cloneable] > .az-remark-wrapper > .az-remark', 'Remark', 'Reveal', 'data-reveal', reveal_refresh, false, false, true);
        //animation_utility("Remark", ".az-remarkable > [data-cloneable] > .az-remark-wrapper");
    }
    function polygone_utility() {
        azh.standard_elements_start_classes['.az-polygone'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "url-attribute",
                "menu": "utility",
                "attribute": "data-click-url",
                "group": "Polygone",
                "control_class": "azh-link",
                "control_type": "link",
                "control_text": "Click URL",
                "selector": ".az-polygone"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Polygone",
                "selector": ".az-polygone",
                "property": "padding",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "padding",
                "control_text": "Inner space"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Polygone",
                "selector": ".az-polygone",
                "property": "width",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Polygone",
                "selector": ".az-polygone",
                "property": "height",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Polygone tooltip",
                "control_text": "Show tooltip",
                "control_class": "azh-toggle azh-show",
                "control_type": "show",
                "selector": ".az-polygone > .az-tooltip-wrapper",
                "class": "az-show"
            },
            {
                "type": "dropdown-classes",
                "selector": ".az-polygone > .az-tooltip-wrapper",
                "menu": "utility",
                "group": "Polygone tooltip",
                "responsive": true,
                "classes": {
                    "az-top-left": "Top Left",
                    "az-top-center": "Top Center",
                    "az-top-right": "Top Right",
                    "az-middle-left": "Middle Left",
                    "az-middle-center": "Middle Center",
                    "az-middle-right": "Middle Right",
                    "az-bottom-left": "Bottom Left",
                    "az-bottom-center": "Bottom Center",
                    "az-bottom-right": "Bottom Right"
                },
                "control_class": "azh-shape azh-position-origin",
                "control_type": "position-origin",
                "control_text": "Tooltip position origin"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Polygone tooltip",
                "selector": ".az-polygone > .az-tooltip-wrapper > .az-tooltip",
                "property": "width",
                "responsive": true,
                "slider": true,
                "min": "0",
                "max": "1000",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Tooltip width"
            },
        ]);
        transform_utility('.az-polygone > .az-tooltip-wrapper > .az-tooltip', 'Polygone tooltip');
    }
    function text_element_utility() {
        azh.standard_elements_start_classes['.az-text'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        font_utility('.az-text', 'Font styles', 'Normal');
        font_utility('.az-text', 'Font styles', 'Hover', 'data-hover', hover_refresh);
        font_utility('.az-text', 'Font styles', 'Reveal', 'data-reveal', reveal_refresh);
        text_utility('.az-text', 'Text styles');
    }
    function heading_element_utility() {
        azh.standard_elements_start_classes['.az-heading'] = true;
        function hover_refresh($control, $element) {
            azh.window.get(0).azh.refresh_hover_css_rules($element);
        }
        function reveal_refresh($control, $element) {
            azh.window.get(0).azh.refresh_reveal_css_rules($element);
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "html-tag",
                "selector": ".az-heading",
                "menu": "utility",
                "options": {
                    "h1": "h1",
                    "h2": "h2",
                    "h3": "h3",
                    "h4": "h4",
                    "h5": "h5",
                    "h6": "h6",
                },
                "control_class": "azh-tag",
                "control_type": "tag",
                "control_text": "HTML tag"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Background clip",
                "control_text": "Enable",
                "control_class": "azh-toggle azh-background-clip",
                "control_type": "background-clip",
                "selector": ".az-heading",
                "class": "az-background-clip-text"
            },
        ]);
        font_utility('.az-heading', 'Font styles', 'Normal');
        font_utility('.az-heading', 'Font styles', 'Hover', 'data-hover', hover_refresh);
        font_utility('.az-heading', 'Font styles', 'Reveal', 'data-reveal', reveal_refresh);
        text_utility('.az-heading', 'Text styles');
        text_stroke_utility('.az-heading', 'Text stroke', 'Normal');
        text_stroke_utility('.az-heading', 'Text stroke', 'Hover', 'data-hover', hover_refresh);
        text_stroke_utility('.az-heading', 'Text stroke', 'Reveal', 'data-reveal', reveal_refresh);
        background_image_settings('utility', '.az-heading', 'Background clip');
        box_utility('.az-heading', "Heading-box styles");
    }
    function separator_utility() {
        azh.standard_elements_start_classes['.az-separator'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "color-style",
                "selector": ".az-separator div",
                "menu": "utility",
                "property": "background-color",
                "control_class": "azh-background-color",
                "control_type": "background-color",
                "control_text": "Background color"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": ".az-separator div",
                "property": "width",
                "slider": true,
                "responsive": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Width"
            },
            {
                "type": "radio-style",
                "menu": "utility",
                "selector": ".az-separator div",
                "responsive": true,
                "property": "margin",
                "options": {
                    "0 auto 0 0": "Left",
                    "0 auto 0 auto": "Center",
                    "0 0 0 auto": "Right",
                },
                "control_class": "azh-horizontal-align",
                "control_type": "horizontal-align",
                "control_text": "Horizontal align"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "selector": ".az-separator div",
                "property": "height",
                "slider": true,
                "responsive": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "type": "integer-list-style",
                "menu": "utility",
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-padding",
                "control_text": "Padding",
                "selector": '.az-separator'
            },
            {
                "type": "integer-list-style",
                "menu": "utility",
                "selector": '.az-separator div',
                "properties": {
                    "border-top-left-radius": "Top Left",
                    "border-top-right-radius": "Top Right",
                    "border-bottom-left-radius": "Bottom Left",
                    "border-bottom-right-radius": "Bottom Right"
                },
                "min": "0",
                "max": "100",
                "step": "1",
                "units": 'px',
                "control_class": "azh-border-radius",
                "control_type": "border-radius",
                "control_text": "Border radius"
            }
        ]);
    }
    function slider_utility() {
        //azh.standard_elements_start_classes['.az-swiper'] = true;
        function slider_refresh($control, $element) {
            if ($element.data('swiper') && $element.data('swiper_get_params')) {
                var swiper = $element.data('swiper');
                var params = $element.data('swiper_get_params')($element);
                swiper.params = $.extend(swiper.params, params);
                swiper.update();
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "toggle-attribute",
                "menu": "utility",
                "group": "Slides",
                "attribute": "data-hashnavigation",
                "control_class": "azh-hashNavigation azh-toggle",
                "control_type": "hashNavigation",
                "control_text": "Enable hash navigation",
                "selector": '.az-swiper'
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Slides",
                "attribute": "data-hash",
                "control_class": "azh-hash",
                "control_type": "hash",
                "control_text": "Slide hash",
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide'
            },
            {
                "refresh": slider_refresh,
                "type": "toggle-attribute",
                "selector": '.az-swiper ',
                "menu": "utility",
                "group": "Slides",
                "attribute": "data-slidesperview",
                "control_class": "azh-slidesperview azh-toggle",
                "control_type": "slidesPerView-auto",
                "true_value": 'auto',
                "false_value": '',
                "control_text": "Enable slide width"
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Slides",
                "property": "width",
                "responsive": true,
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "%",
                "control_class": "azh-width",
                "control_type": "width",
                "control_text": "Slide width",
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide'
            },
            {
                "refresh": true,
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "options": {
                    "horizontal": "Horizontal",
                    "vertical": "Vertical"
                },
                "attribute": "data-direction",
                "control_class": "azh-direction",
                "control_type": "direction",
                "control_text": "Direction",
                "selector": '.az-swiper'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Slider settings",
                "property": "height",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "selector": '.az-swiper[data-direction="vertical"]',
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "responsive": true,
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-slidesperview",
                "control_class": "azh-slidesperview",
                "control_type": "slidesPerView",
                "control_text": "Slides per view",
                "min": "1",
                "max": "10",
                "step": "1",
                "selector": '.az-swiper '
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "responsive": true,
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-spacebetween",
                "control_class": "azh-spaceBetween",
                "control_type": "spaceBetween",
                "control_text": "Space between slides",
                "min": "0",
                "max": "100",
                "step": "1",
                "selector": '.az-swiper '
            },
            {
                "refresh": slider_refresh,
                "type": "toggle-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-centeredslides",
                "control_class": "azh-centeredSlides azh-toggle",
                "control_type": "centeredSlides",
                "control_text": "Centered slides",
                "selector": '.az-swiper'
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-speed",
                "control_class": "azh-speed",
                "control_type": "speed",
                "control_text": "Slide speed",
                "min": "0",
                "max": "1000",
                "step": "10",
                "selector": '.az-swiper '
            },
            {
                "refresh": slider_refresh,
                "type": "toggle-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-loop",
                "control_class": "azh-loop azh-toggle",
                "control_type": "loop",
                "control_text": "Loop",
                "selector": '.az-swiper'
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "attribute": "data-autoplay-delay",
                "control_class": "azh-autoplay-delay",
                "control_type": "autoplay-delay",
                "control_text": "Autoplay delay",
                "min": "0",
                "max": "10000",
                "step": "100",
                "selector": '.az-swiper'
            },
            {
                "refresh": true,
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Slider settings",
                "options": {
                    "slide": "slide",
                    "fade": "fade",
                    "cube": "cube",
                    "coverflow": "coverflow",
                    "flip": "flip"
                },
                "attribute": "data-effect",
                "control_class": "azh-effect",
                "control_type": "effect",
                "control_text": "Effect",
                "selector": '.az-swiper'
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slide settings",
                "attribute": "data-swiper-parallax",
                "control_class": "azh-parallax",
                "control_type": "parallax",
                "control_text": "Parallax",
                "slider": true,
                "units": {
                    "px": {
                        "min": "-300",
                        "max": "+300",
                        "step": "1"
                    },
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    }
                },
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide [data-element]:not([data-element=""]):not([data-element=" "])'
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slide settings",
                "attribute": "data-swiper-parallax-x",
                "control_class": "azh-parallax-x",
                "control_type": "parallax-x",
                "control_text": "X-axis parallax",
                "slider": true,
                "units": {
                    "px": {
                        "min": "-300",
                        "max": "+300",
                        "step": "1"
                    },
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    }
                },
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide [data-element]:not([data-element=""]):not([data-element=" "])'
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slide settings",
                "attribute": "data-swiper-parallax-y",
                "control_class": "azh-parallax-y",
                "control_type": "parallax-y",
                "control_text": "Y-axis parallax",
                "slider": true,
                "units": {
                    "px": {
                        "min": "-300",
                        "max": "+300",
                        "step": "1"
                    },
                    "%": {
                        "min": "-100",
                        "max": "100",
                        "step": "1"
                    }
                },
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide [data-element]:not([data-element=""]):not([data-element=" "])'
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slide settings",
                "attribute": "data-swiper-parallax-scale",
                "control_class": "azh-parallax-scale",
                "control_type": "parallax-scale",
                "control_text": "Scale parallax",
                "min": "0",
                "max": "3",
                "step": "0.1",
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide [data-element]:not([data-element=""]):not([data-element=" "])'
            },
            {
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Slide settings",
                "attribute": "data-swiper-parallax-opacity",
                "control_class": "azh-parallax-opacity",
                "control_type": "parallax-opacity",
                "control_text": "Opacity parallax",
                "min": "0",
                "max": "1",
                "step": "0.1",
                "selector": '.az-swiper > .az-wrapper[data-cloneable] > .az-slide [data-element]:not([data-element=""]):not([data-element=" "])'
            }
        ]);
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Slider thumbs settings",
                "property": "height",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "selector": '.az-swiper-thumbs',
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "responsive": true,
                "menu": "utility",
                "group": "Slider thumbs settings",
                "attribute": "data-slidesperview",
                "control_class": "azh-slidesperview",
                "control_type": "slidesPerView",
                "control_text": "Slides per view",
                "min": "1",
                "max": "10",
                "step": "1",
                "selector": '.az-swiper-thumbs '
            },
            {
                "refresh": slider_refresh,
                "type": "integer-attribute",
                "responsive": true,
                "menu": "utility",
                "group": "Slider thumbs settings",
                "attribute": "data-spacebetween",
                "control_class": "azh-spaceBetween",
                "control_type": "spaceBetween",
                "control_text": "Space between slides",
                "min": "0",
                "max": "100",
                "step": "1",
                "selector": '.az-swiper-thumbs '
            },
            {
                "refresh": slider_refresh,
                "type": "toggle-attribute",
                "menu": "utility",
                "group": "Slider thumbs settings",
                "attribute": "data-centeredslides",
                "control_class": "azh-centeredSlides azh-toggle",
                "control_type": "centeredSlides",
                "control_text": "Centered slides",
                "selector": '.az-swiper-thumbs'
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Slider thumbs settings",
                "control_text": "Max-width of container width",
                "control_class": "azh-toggle",
                "control_type": "container",
                "selector": ".azen-swiper-thumbs-container",
                "class": "az-container"
            },
        ]);
        background_utility('.azen-swiper-thumbs-container > .az-swiper-thumbs', 'Thumbs Background');
        border_utility('.azen-swiper-thumbs-container > .az-swiper-thumbs', 'Thumbs Border');
        box_shadow_utility('.azen-swiper-thumbs-container > .az-swiper-thumbs', 'Thumbs Shadow');
        box_utility('.azen-swiper-thumbs-container > .az-swiper-thumbs', "Thumbs-box styles");
    }
    function isotope_utility() {
        azh.standard_elements_start_classes['.az-isotope'] = true;
        function isotope_refresh($control, $element) {
            var $items = $element.find('.az-isotope-items');
            if (!$items.length) {
                $items = $element.closest('.az-isotope-items');
            }
            $items.isotope('layout');
        }
        function isotope_active_filter_refresh($control, $element) {
            var $filters = $element.find('.az-isotope-filters');
            if (!$filters.length) {
                $filters = $element.closest('.az-isotope-filters');
            }
            if ($filters.data('refresh_active_css_rules')) {
                $filters.data('refresh_active_css_rules')($filters);
            }
        }
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": isotope_refresh,
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Grid items",
                "attribute": "data-columns",
                "responsive": true,
                "min": "1",
                "max": "10",
                "step": "1",
                "control_class": "azh-columns",
                "control_type": "columns",
                "control_text": "Item width (columns number)",
                "selector": '.az-isotope .az-isotope-items > .az-item'
            },
            {
                "refresh": true,
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Grid settings",
                "attribute": "data-layoutmode",
                "control_class": "azh-layout-mode",
                "control_type": "layout-mode",
                "options": {
                    "masonry": "Masonry",
                    "fitRows": "Fit Rows",
                    "vertical": "Vertical"
                },
                "control_text": "Layout mode",
                "selector": '.az-isotope .az-isotope-items'
            },
            {
                "refresh": isotope_refresh,
                "type": "integer-attribute",
                "menu": "utility",
                "group": "Grid settings",
                "attribute": "data-columns",
                "control_class": "azh-columns",
                "control_type": "columns",
                "responsive": true,
                "min": "1",
                "max": "10",
                "step": "1",
                "control_text": "Columns number",
                "selector": '.az-isotope .az-isotope-items'
            },
            {
                "refresh": isotope_refresh,
                "type": "dropdown-attribute",
                "menu": "utility",
                "group": "Grid settings",
                "attribute": "data-gutter",
                "control_class": "azh-gutter",
                "control_type": "gutter",
                "options": {
                    "0": "0px",
                    "1": "1px",
                    "2": "2px",
                    "3": "3px",
                    "4": "4px",
                    "5": "5px",
                    "10": "10px",
                    "15": "15px",
                    "20": "20px",
                    "25": "25px",
                    "30": "30px",
                },
                "control_text": "Gutter",
                "selector": '.az-isotope .az-isotope-items'
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Grid settings",
                "property": "margin-right",
                "control_class": "azh-filters-space",
                "control_type": "filters-space",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_text": "Filters space",
                "multiplying_selector": "[data-filter]",
                "selector": '.az-isotope .az-isotope-filters'
            }
        ]);
        text_utility('.az-isotope .az-isotope-filters', 'Filter text styles');
        font_utility('.az-isotope .az-isotope-filters', 'Filter font styles', 'Normal', false, false, '[data-filter] span');
        font_utility('.az-isotope .az-isotope-filters', 'Filter font styles', 'Active', 'data-active', isotope_active_filter_refresh);
        box_utility('.az-isotope .az-isotope-filters', "Filters-box styles");
    }
    function hover_overlay_utility() {
        azh.standard_elements_start_classes['.az-hover-overlay'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "init": function ($control, $element) {
                    $element.off('click.azh-toggle').on('click.azh-toggle', function (event) {
                        var $checkbox = $control.find('input[type=checkbox]');
                        if ($checkbox.prop('checked')) {
                            if (azh.$(event.target).parentsUntil($element).length === 1) {
                                $checkbox.trigger('click');
                            }
                        } else {
                            if (azh.$(event.target).parentsUntil($element).length === 3) {
                                $checkbox.trigger('click');
                            }
                        }
                    });
                },
                "type": "exists-class",
                "menu": "utility",
                "group": "Mouse hover overlay",
                "control_text": "Show overlay",
                "control_class": "azh-toggle azh-hover",
                "control_type": "hover",
                "selector": ".az-hover-overlay",
                "class": "az-hover"
            },
            {
                "type": "exists-class",
                "menu": "utility",
                "group": "Mouse hover overlay",
                "control_text": "Disable pointer events",
                "control_class": "azh-toggle azh-hover",
                "control_type": "hover",
                "selector": ".az-hover-overlay > div:last-child",
                "class": "az-disable-pointer-events"
            },
        ]);
        animation_utility("Mouse hover overlay", ".az-hover-overlay > div > .az-free-positioning");
        animation_utility("Mouse hover animation", ".az-hover-overlay > div > .az-free-positioning > [data-element], .az-hover-overlay > div > .az-free-positioning > .az-elements-list > [data-element]");
    }
    function modal_utility() {
        azh.standard_elements_start_classes['.az-modal'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "init": function ($control, $element) {
                    $element.off('az-change.fco').on('az-change.fco', function () {
                        azh.controls_container.children().removeClass('azh-hidden-control');
                        if ($element.is('.az-active')) {
                            azh.controls_container.children().not(azh.get_wrapper_controls($element)).addClass('azh-hidden-control');
                        }
                    });
                    $element.trigger('az-change.fco');
                },
                "refresh": function ($control, $element) {
                    azh.controls_container.children().removeClass('azh-hidden-control');
                    if ($element.is('.az-active')) {
                        azh.controls_container.children().not(azh.get_wrapper_controls($element)).addClass('azh-hidden-control');
                    }
                },
                "type": "exists-class",
                "menu": "utility",
                "control_text": "Show modal",
                "control_class": "azh-toggle azh-hover",
                "control_type": "modal",
                "selector": ".az-modal",
                "class": "az-active"
            },
        ]);
    }
    function magnific_popup_utility() {
        azh.standard_elements_start_classes['.az-magnific-popup'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "image-attribute",
                "menu": "utility",
                "attribute": "href",
                "control_class": "azh-image",
                "control_type": "image",
                "control_text": "Image for popup",
                "selector": 'a.az-magnific-popup'
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "attribute": "href",
                "filter": "convert_to_embed",
                "control_class": "azh-video",
                "control_type": "video",
                "control_text": "Video or Google Map URL for popup",
                "selector": 'a.az-magnific-popup'
            }
        ]);
    }
    function anchors_menu_utility() {
        azh.standard_elements_start_classes['.az-anchors-menu'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Menu settings",
                "control_text": "Hash",
                "control_class": "azh-id",
                "control_type": "id",
                "attribute": "id",
                "selector": '.az-anchor',
            },
            {
                "type": "input-attribute",
                "menu": "utility",
                "group": "Menu settings",
                "control_text": "Menu item hash",
                "control_class": "azh-href",
                "control_type": "href",
                "attribute": "href",
                "pattern": /(#)(.*)()/,
                "default": "#anchor",
                "selector": '.az-anchors-menu div a',
            },
            {
                "type": "input-innertext",
                "menu": "utility",
                "group": "Menu settings",
                "control_text": "Menu item title",
                "control_class": "azh-text",
                "control_type": "text",
                "selector": '.az-anchors-menu div a',
            },
            {
                "type": "integer-style",
                "menu": "utility",
                "group": "Menu settings",
                "property": "margin-right",
                "control_class": "azh-item-space",
                "control_type": "item-space",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "responsive": true,
                "control_text": "Menu items space",
                "multiplying_selector": "div",
                "selector": '.az-anchors-menu'
            },
            {
                "type": "radio-style",
                "menu": "utility",
                "group": "Menu settings",
                "selector": ".az-anchors-menu",
                "responsive": true,
                "property": "justify-content",
                "options": {
                    "flex-start": "Left",
                    "center": "Center",
                    "flex-end": "Right",
                    "space-around": "Space around",
                    "space-between": "Space between"
                },
                "control_class": "azh-horizontal-align",
                "control_type": "justify-content",
                "control_text": "Menu align"
            },
        ]);
        text_utility('.az-anchors-menu', 'Menu text styles');
        font_utility('.az-anchors-menu', 'Menu font styles', false, false, false, 'div a');
    }
    function sticky_header_utility() {
        azh.standard_elements_start_classes['.az-sticky-header'] = true;
        background_utility('.az-sticky-header > [data-sticky-style] > [data-cloneable]', 'Background');
    }
    function horizontal_elements_utility() {
        azh.standard_elements_start_classes['.az-horizontal-elements'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "selector": ".az-horizontal-elements > div",
                "menu": "utility",
                "property": "width",
                //"group": "Horizontal elements",
                //"target_utility": ".az-horizontal-elements",
                "responsive": true,
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "500",
                        "step": "1"
                    }
                },
                "control_class": "azh-width",
                "control_type": "width",
                "control_text": "Width"
            },
        ]);
    }
    function countdown_utility() {
        azh.standard_elements_start_classes['.az-countdown'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "refresh": true,
                "type": "input-attribute",
                "input_type": "date",
                "menu": "utility",
                "control_text": "Countdown date",
                "control_class": "azh-countdown",
                "control_type": "countdown",
                "selector": ".az-countdown",
                "attribute": "data-time"
            }
        ]);
        font_utility('.az-countdown', 'Font styles', false, false, false, '.az-days, .az-hours, .az-minutes, .az-seconds');
        text_utility('.az-countdown', 'Text styles', false, false, false, '.az-days, .az-hours, .az-minutes, .az-seconds');

        font_utility('.az-countdown', 'Count font styles', false, false, false, '.az-days .az-count, .az-hours .az-count, .az-minutes .az-count, .az-seconds .az-count');
        text_utility('.az-countdown', 'Count text styles', false, false, false, '.az-days .az-count, .az-hours .az-count, .az-minutes .az-count, .az-seconds .az-count');
    }
    function accordion_utility() {
        azh.standard_elements_start_classes['.az-accordion-element'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-list-style",
                "menu": "utility",
                "group": "Accordion styles",
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-padding",
                "control_text": "Padding",
                "rule_selector": " > .az-item > .az-title, > .az-item > .az-content",
                "selector": ".az-accordion-element"
            }
        ]);
        border_utility('.az-accordion-element', 'Accordion border', false, false, false, false, '> .az-item');
        background_utility('.az-accordion-element', 'Title Background', 'Normal', false, false, false, '> .az-item > .az-title');
        background_utility('.az-accordion-element', 'Title Background', 'Active', false, false, false, '> .az-item.az-active > .az-title');
        background_utility('.az-accordion-element', 'Content Background', 'Normal', false, false, false, '> .az-item > .az-content');
        background_utility('.az-accordion-element', 'Content Background', 'Active', false, false, false, '> .az-item.az-active > .az-content');
        font_utility('.az-accordion-element', 'Title Font Styles', 'Normal', false, false, false, '> .az-item > .az-title');
        font_utility('.az-accordion-element', 'Title Font Styles', 'Active', false, false, false, '> .az-item.az-active > .az-title');
        text_utility('.az-accordion-element', 'Title Text Styles', 'Normal', false, false, false, '> .az-item > .az-title');
        text_utility('.az-accordion-element', 'Title Text Styles', 'Active', false, false, false, '> .az-item.az-active > .az-title');
        transition_utility('.az-accordion-element', "Transition parameters", false, false, '> .az-item > .az-title');//, > .az-item > .az-content - нарушает анимацию
    }
    function tabs_utility() {
        azh.standard_elements_start_classes['.az-tabs-element'] = true;
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-list-style",
                "menu": "utility",
                "group": "Tabs styles",
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-padding",
                "control_text": "Padding",
                "rule_selector": " > .az-titles > .az-title, > .az-content > .az-item",
                "selector": ".az-tabs-element"
            }
        ]);
        border_utility('.az-tabs-element', 'Tabs border', false, false, false, false, '> .az-titles > .az-title.az-active, > .az-content > .az-item');
        background_utility('.az-tabs-element', 'Tab Background', false, false, false, false, '> .az-titles > .az-title.az-active, > .az-content > .az-item');
        font_utility('.az-tabs-element', 'Title Font Styles', 'Normal', false, false, false, '> .az-titles > .az-title');
        font_utility('.az-tabs-element', 'Title Font Styles', 'Active', false, false, false, '> .az-titles > .az-title.az-active');
        text_utility('.az-tabs-element', 'Title Text Styles', 'Normal', false, false, false, '> .az-titles > .az-title');
        text_utility('.az-tabs-element', 'Title Text Styles', 'Active', false, false, false, '> .az-titles > .az-title.az-active');
        transition_utility('.az-tabs-element', "Transition parameters", false, false, '> .az-titles > .az-title, > .az-content > .az-item');
    }


    function form_context() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "input-attribute",
                "menu": "context",
                "control_text": "Field name",
                "control_class": "azh-name",
                "control_type": "name",
                "selector": "[name]",
                "attribute": "name",
                "unique_wrapper": 'form, [data-section]',
                "unique": '[name="{name}"]',
                "unique_exception": '[name*="[]"]'
            },
            {
                "type": "dropdown-attribute",
                "selector": "input[type='text'], input[type='color'], input[type='date'], input[type='datetime'], input[type='datetime-local'], input[type='email'], input[type='number'], input[type='tel'], input[type='time'], input[type='url'], input[type='month'], input[type='week']",
                "menu": "context",
                "options": {
                    "text": 'text',
                    "color": 'color',
                    "date": 'date',
                    "datetime": 'datetime',
                    "datetime-local": 'datetime-local',
                    "email": 'email',
                    "number": 'number',
                    "tel": 'tel',
                    "time": 'time',
                    "url": 'url',
                    "month": 'month',
                    "week": 'week'
                },
                "attribute": "type",
                "control_class": "azh-dropdown",
                "control_type": "input-type",
                "control_text": "Input type"
            },
            {
                "type": "exists-attribute",
                "menu": "context",
                "control_text": "Required",
                "control_class": "azh-toggle",
                "control_type": "required",
                "selector": "form input:not([type='submit']), form textarea, form select",
                "attribute": "required"
            },
            {
                "type": "integer-attribute",
                "menu": "context",
                "attribute": "maxlength",
                "control_class": "azh-maxlength",
                "control_type": "maxlength",
                "selector": "input[maxlength]",
                "control_text": "Maximum input length"
            },
            {
                "type": "input-attribute",
                "menu": "context",
                "attribute": "pattern",
                "control_class": "azh-pattern",
                "control_type": "pattern",
                "selector": "input[pattern]",
                "control_text": "Input regular expression"
            },
            {
                "type": "input-attribute",
                "menu": "context",
                "attribute": "data-mask",
                "control_class": "azh-mask",
                "control_type": "mask",
                "selector": "input[data-mask]",
                "control_text": "Value mask, use: 'a', '9', '*'"
            },
            {
                "type": "input-attribute",
                "menu": "context",
                "control_text": "Default field value",
                "control_class": "azh-value",
                "control_type": "value",
                "selector": "input[value]",
                "attribute": "value"
            },
            {
                "type": "input-attribute",
                "menu": "context",
                "control_text": "Field placeholder",
                "control_class": "azh-field-placeholder",
                "control_type": "field-placeholder",
                "selector": "[placeholder]",
                "attribute": "placeholder"
            },
            {
                "type": "input-attribute",
                "menu": "context",
                "control_text": "Submit button text",
                "control_class": "azh-submit-value",
                "control_type": "submit-value",
                "selector": "input[type='submit']",
                "attribute": "value"
            }
        ]);
        azh.modal_options = azh.modal_options.concat([
            {
                "menu": 'context',
                "button_text": "Integer range settings",
                "button_class": "azh-integer-range",
                "button_type": "azh-integer-range",
                "title": "Integer range settings",
                "selector": "input[type='range'], input[type='number']",
                "attributes": {
                    'value': {
                        "type": "number",
                        "label": "Default value"
                    },
                    'min': {
                        "type": "number",
                        "label": "Minimum"
                    },
                    'max': {
                        "type": "number",
                        "label": "Maximum"
                    },
                    'step': {
                        "type": "number",
                        "label": "Step"
                    }
                }
            }
        ]);
    }
    function font_context(selector) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "font-family",
                "menu": "context",
                "group": "Font style",
                "property": "font-family",
                "selector": selector,
                "control_class": "azh-font-family",
                "control_type": "font-family",
                "control_text": "Font family"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "group": "Font style",
                "property": "font-size",
                "selector": selector,
                "responsive": true,
                "slider": true,
                "units": {
                    "px": {
                        "min": "1",
                        "max": "200",
                        "step": "1"
                    },
                    "em": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    },
                    "rem": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "font-size",
                "control_text": "Font size"
            },
            {
                "type": "dropdown-style",
                "menu": "context",
                "group": "Font style",
                "property": "font-weight",
                "selector": selector,
                "options": {
                    "100": "100",
                    "200": "200",
                    "300": "300",
                    "400": "400",
                    "500": "500",
                    "600": "600",
                    "700": "700",
                    "800": "800",
                    "900": "900"
                },
                "control_class": "azh-dropdown",
                "control_type": "font-weight",
                "control_text": "Font weight"
            },
            {
                "type": "dropdown-style",
                "menu": "context",
                "group": "Font style",
                "property": "font-style",
                "selector": selector,
                "options": {
                    "": "Default",
                    "normal": "Normal",
                    "italic": "Italic",
                    "oblique": "Oblique"
                },
                "control_class": "azh-dropdown",
                "control_type": "font-style",
                "control_text": "Font style"
            },
            {
                "type": "dropdown-style",
                "menu": "context",
                "group": "Font style",
                "property": "text-transform",
                "selector": selector,
                "options": {
                    "": "Default",
                    "uppercase": "Uppercase",
                    "lowercase": "Lowercase",
                    "capitalize": "Capitalize",
                    "none": "Normal"
                },
                "control_class": "azh-dropdown",
                "control_type": "text-transform",
                "control_text": "Transform"
            },
            {
                "type": "color-style",
                "menu": "context",
                "group": "Font style",
                "property": "color",
                "selector": selector,
                "control_class": "azh-color",
                "control_type": "color",
                "control_text": "Color"
            }
        ]);
    }
    function text_context(selector) {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-style",
                "menu": "context",
                "group": "Text style",
                "property": "line-height",
                "selector": selector,
                "responsive": true,
                "slider": true,
                "units": {
                    "px": {
                        "min": "1",
                        "max": "100",
                        "step": "1"
                    },
                    "%": {
                        "min": "1",
                        "max": "300",
                        "step": "1"
                    },
                    "em": {
                        "min": "0.1",
                        "max": "10",
                        "step": "0.1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "line-height",
                "control_text": "Line height"
            },
            {
                "type": "radio-style",
                "menu": "context",
                "group": "Text style",
                "property": "text-align",
                "selector": selector,
                "responsive": true,
                "options": {
                    "left": "Left",
                    "center": "Center",
                    "right": "Right",
                    "justify": "Justify",
                },
                "control_class": "azh-text-align",
                "control_type": "text-align",
                "control_text": "Text align"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "selector": selector,
                "group": "Text style",
                "responsive": true,
                "property": "word-spacing",
                "min": "-20",
                "max": "50",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "word-spacing",
                "control_text": "Word-spacing"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "group": "Text style",
                "property": "letter-spacing",
                "selector": selector,
                "responsive": true,
                "min": "-5",
                "max": "10",
                "step": "0.1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "letter-spacing",
                "control_text": "Letter-spacing"
            }
        ]);
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "integer-list-style",
                "menu": "context",
                "group": "Text-box styles",
                "responsive": true,
                "properties": {
                    "margin-top": "Top",
                    "margin-right": "Right",
                    "margin-bottom": "Bottom",
                    "margin-left": "left"
                },
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-margin",
                "control_text": "Margin",
                "selector": selector
            },
            {
                "type": "integer-list-style",
                "menu": "context",
                "group": "Text-box styles",
                "responsive": true,
                "properties": {
                    "padding-top": "Top",
                    "padding-right": "Right",
                    "padding-bottom": "Bottom",
                    "padding-left": "Left"
                },
                "min": "0",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer-list",
                "control_type": "box-padding",
                "control_text": "Padding",
                "selector": selector
            }
        ]);

    }
    function border_styles_context() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "color-style",
                "menu": "context",
                "group": "Border style",
                "property": "border-color",
                "control_class": "azh-color",
                "control_type": "border-color",
                "control_text": "Border color"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "group": "Border style",
                "property": "border-width",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "border-width",
                "control_text": "Border width"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "group": "Border style",
                "property": "border-radius",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "50",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "border-radius",
                "control_text": "Border radius"
            }
        ]);
    }
    function styles_context() {
        azh.controls_options = azh.controls_options.concat([
            {
                "type": "color-style",
                "menu": "context",
                "property": "color",
                "control_class": "azh-color",
                "control_type": "color",
                "control_text": "Color"
            },
            {
                "type": "color-style",
                "menu": "context",
                "property": "background-color",
                "control_class": "azh-background-color",
                "control_type": "background-color",
                "control_text": "Background color"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "not_selector": ".az-background",
                "property": "padding",
                "min": "0",
                "max": "100",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "padding",
                "control_text": "Inner space"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "width",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "width",
                "control_text": "Width"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "height",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "height",
                "control_text": "Height"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "max-height",
                "units": "px",
                "min": "0",
                "max": "1000",
                "step": "1",
                "control_class": "azh-integer",
                "control_type": "max-height",
                "control_text": "Max Height"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "max-width",
                "slider": true,
                "units": {
                    "%": {
                        "min": "0",
                        "max": "100",
                        "step": "1"
                    },
                    "px": {
                        "min": "0",
                        "max": "1000",
                        "step": "1"
                    }
                },
                "control_class": "azh-integer",
                "control_type": "max-width",
                "control_text": "Max width"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "margin-top",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "margin-top",
                "control_text": "Margin top"
            },
            {
                "type": "integer-style",
                "menu": "context",
                "property": "margin-bottom",
                "min": "-300",
                "max": "300",
                "step": "1",
                "units": "px",
                "control_class": "azh-integer",
                "control_type": "margin-bottom",
                "control_text": "Margin bottom"
            }
        ]);
    }
    window.azh = $.extend({}, window.azh);
    window.azh.standard_elements_start_classes = $.extend({}, window.azh.standard_elements_start_classes);
    if (!('controls_options' in azh)) {
        azh.controls_options = [];
    }
    if (!('modal_options' in azh)) {
        azh.modal_options = [];
    }
    template_utility();
    countdown_utility();
    accordion_utility();
    tabs_utility();
    shape_utility(".az-shape", "Shape");
    form_utility();
    text_element_utility();
    heading_element_utility();
    form_context();
    font_context();
    font_context('[contenteditable] .az-inline, [contenteditable]');
    font_context('.az-icon');
    text_context();
    text_context('[contenteditable]');
    background_menu('context', 'td, th', 'Cell Background');
    border_menu('context', 'td, th', 'Cell Border');
    hotspot_utility();
    svg_utility();
    polygone_utility();
    remark_utility();
    reveal_trigger_utility();
    horizontal_elements_utility();
    //triggers_utility();
    border_styles_context();
    section_utility();
    top_bottom_border_utility('[data-full-width]', 'Border');
    section_background_utility();
    sticky_header_utility();
    column_utility();
    styles_context();
    box_shadow_utility('.az-box-shadow', 'Shadow');
    background_image_settings('context', '.az-background-image', 'Background image');
    hover_overlay_utility();
    modal_utility();
    free_positioning_utility();
    google_map_utility();
    video_element_utility();
    background_element_utility();
    button_utility();
    icon_utility();
    hyperlink_utility();
    magnific_popup_utility();
    image_utility();
    separator_utility();
    slider_utility();
    isotope_utility();
    anchors_menu_utility();
    scroll_reveal_utility();
    element_box_utility();
    shape_divider_utility(".az-shape-top", "Shape divider", "Top");
    shape_divider_utility(".az-shape-bottom", "Shape divider", "Bottom");
    splitted_section_utility();

    azh.controls_options = azh.controls_options.concat([
        {
            "type": "color-style",
            "selector": ".az-background-color",
            "menu": "utility",
            "property": "background-color",
            "control_class": "azh-background-color",
            "control_type": "background-color",
            "control_text": "Background color"
        },
        {
            "type": "radio-style",
            "menu": "utility",
            "property": "text-align",
            "selector": ".az-text-align",
            "responsive": true,
            "options": {
                "left": "Left",
                "center": "Center",
                "right": "Right",
            },
            "control_class": "azh-text-align",
            "control_type": "horizontal-align",
            "control_text": "Horizontal align"
        },
        {
            "type": "dropdown-style",
            "selector": ".az-justify-content",
            "menu": "utility",
            "responsive": true,
            "options": {
                "": "Default",
                "flex-start": "Start",
                "center": "Center",
                "flex-end": "End",
                "space-around": "Space around",
                "space-between": "Space between"
            },
            "property": "justify-content",
            "control_class": "azh-justify-content",
            "control_type": "justify-content",
            "control_text": "Justify content"
        },
        {
            "type": "dropdown-style",
            "selector": ".az-align-content",
            "menu": "utility",
            "responsive": true,
            "options": {
                "": "Default",
                "flex-start": "Start",
                "center": "Center",
                "flex-end": "End"
            },
            "property": "align-content",
            "control_class": "azh-align-content",
            "control_type": "align-content",
            "control_text": "Align content"
        },
        {
            "type": "dropdown-style",
            "selector": ".az-align-items",
            "menu": "utility",
            "responsive": true,
            "options": {
                "": "Default",
                "flex-start": "Start",
                "center": "Center",
                "flex-end": "End"
            },
            "property": "align-items",
            "control_class": "azh-align-items",
            "control_type": "align-items",
            "control_text": "Align items"
        },
        {
            "type": "dropdown-style",
            "selector": ".az-flex-wrap",
            "menu": "utility",
            "responsive": true,
            "options": {
                "": "Default",
                "nowrap": "No wrap",
                "wrap": "Wrap",
                "wrap-reverse": "Wrap reverse"
            },
            "property": "flex-wrap",
            "control_class": "azh-flex-wrap",
            "control_type": "flex-wrap",
            "control_text": "Wrap"
        },
        {
            "type": "radio-style",
            "selector": ".az-hide",
            "menu": "utility",
            "group": "Element-box styles",
            "property": "display",
            "options": {
                "none": "Hidden",
                "block": "Visible",
            },
            "responsive": true,
            "control_class": "azh-visibility azh-default",
            "control_type": "visibility",
            "control_text": "Visibility"
        },                
        {
            "type": "input-attribute",
            "menu": "context",
            "control_text": "Popup tooltip",
            "control_class": "azh-title",
            "control_type": "title",
            "attribute": "title"
        },
        {
            "type": "url-attribute",
            "menu": "utility",
            "attribute": "href",
            "control_class": "azh-link",
            "control_type": "link",
            "control_text": "Link URL",
            "selector": '.az-link'
        },
        {
            "type": "exists-class",
            "menu": "utility",
            "control_text": "Show side-navbar",
            "control_class": "azh-toggle",
            "control_type": "navbar-side",
            "selector": '#az-navbar-side',
            "class": "open"
        },
//        {
//            "type": "input-attribute",
//            "menu": "utility",
//            "control_text": "Unique ID",
//            "control_class": "azh-id",
//            "control_type": "id",
//            "attribute": "id",
//            "not_selector": '[type="checkbox"], :not("[data-hover]")',
//            "restriction": '[href="#{id}"], [data-target="#{id}"], [data-id="#{id}"], [data-for="{id}"]'
//        }
    ]);
    window.azh_get_options_strings = function () {
        var i18n = {};
        for (var i = 0; i < azh.controls_options.length; i++) {
            var options = azh.controls_options[i];
            i18n[options['control_text']] = true;
            i18n[options['description']] = true;
            i18n[options['group']] = true;
            i18n[options['subgroup']] = true;
            if (options['options'] && $.isPlainObject(options['options'])) {
                for (var key in options['options']) {
                    i18n[options['options'][key]] = true;
                }
            }
            if (options['properties'] && $.isPlainObject(options['properties'])) {
                for (var key in options['properties']) {
                    i18n[options['properties'][key]] = true;
                }
            }
        }
        for (var i = 0; i < azh.modal_options.length; i++) {
            var options = azh.modal_options[i];
            i18n[options['button_text']] = true;
            i18n[options['title']] = true;
            i18n[options['desc']] = true;
            i18n[options['label']] = true;
        }
        var strings = [];
        for (var str in i18n) {
            str = str.replace(/'/g, "\\'");
            strings.push("'" + str + "' => esc_html__('" + str + "', 'azh'),");
        }
        return strings.join("\n");
    };
    $(window).on("azh-customizer-before-init", function (event, data) {
        if (azh.options_i18n) {
            for (var i = 0; i < azh.controls_options.length; i++) {
                var options = azh.controls_options[i];
                if (options['control_text'] && azh.options_i18n[options['control_text']]) {
                    options['control_text'] = azh.options_i18n[options['control_text']];
                }
                if (options['description'] && azh.options_i18n[options['description']]) {
                    options['description'] = azh.options_i18n[options['description']];
                }
                if (options['group'] && azh.options_i18n[options['group']]) {
                    options['group'] = azh.options_i18n[options['group']];
                }
                if (options['subgroup'] && azh.options_i18n[options['subgroup']]) {
                    options['subgroup'] = azh.options_i18n[options['subgroup']];
                }
                if (options['options'] && $.isPlainObject(options['options'])) {
                    for (var key in options['options']) {
                        if (options['options'][key] && azh.options_i18n[options['options'][key]]) {
                            options['options'][key] = azh.options_i18n[options['options'][key]];
                        }
                    }
                }
                if (options['properties'] && $.isPlainObject(options['properties'])) {
                    for (var key in options['properties']) {
                        if (options['properties'][key] && azh.options_i18n[options['properties'][key]]) {
                            options['properties'][key] = azh.options_i18n[options['properties'][key]];
                        }
                    }
                }
            }
            for (var i = 0; i < azh.modal_options.length; i++) {
                var options = azh.modal_options[i];
                if (options['button_text'] && azh.options_i18n[options['button_text']]) {
                    options['button_text'] = azh.options_i18n[options['button_text']];
                }
                if (options['title'] && azh.options_i18n[options['title']]) {
                    options['title'] = azh.options_i18n[options['title']];
                }
                if (options['desc'] && azh.options_i18n[options['desc']]) {
                    options['desc'] = azh.options_i18n[options['desc']];
                }
                if (options['label'] && azh.options_i18n[options['label']]) {
                    options['label'] = azh.options_i18n[options['label']];
                }
            }
        }
    });
})(window.jQuery);