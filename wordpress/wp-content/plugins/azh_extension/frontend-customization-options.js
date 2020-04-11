(function($) {
    "use strict";
    window.azh = $.extend({}, window.azh);
    if(!('controls_options' in azh)) {
        azh.controls_options = [];
    }
    
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "input-attribute",
            "menu": "utility",
            "control_text": "Slide name",
            "control_class": "azh-name",
            "control_type": "name",
            "selector": ".az-carousel > div",
            "attribute": "data-name"
        },
        {
            "type": "url-attribute",
            "menu": "utility",
            "control_text": "Soundcloud URL",
            "control_class": "azh-src",
            "control_type": "src",
            "selector": ".azen iframe[src*='soundcloud']",
            "attribute": "src"
        },        
        {
            "type": "background-image",
            "menu": "utility",
            "control_text": "Background image",
            "control_class": "azh-background-image",
            "control_type": "background-image",
            "is_selector": '.azen [style]',
            "property": "background-image"
        },
        {
            "type": "color-style",
            "menu": "context",
            "control_text": "Color",
            "control_class": "azh-color",
            "control_type": "color",
            "is_selector": '.azen [style]',
            "property": "color"
        },        
        {
            "type": "color-style",
            "menu": "context",
            "control_text": "Background color",
            "control_class": "azh-background-color",
            "control_type": "background-color",
            "is_selector": '.azen [style]',
            "property": "background-color"
        },        
        {
            "refresh": true,
            "type": "color-attribute",
            "menu": "context",
            "control_text": "Foreground color",
            "control_class": "azh-fgcolor",
            "control_type": "fgcolor",
            "attribute": "data-fgcolor"
        },
        {
            "refresh": true,
            "type": "color-attribute",
            "menu": "context",
            "control_text": "Background color",
            "control_class": "azh-bgcolor",
            "control_type": "bgcolor",
            "attribute": "data-bgcolor"
        },
        {
            "refresh": true,
            "type": "input-attribute",
            "input_type": "date",
            "menu": "context",
            "control_text": "Countdown date",
            "control_class": "azh-countdown",
            "control_type": "countdown",
            "selector": ".az-countdown",
            "attribute": "data-time"
        }
    ]);
    if(!('modal_options' in azh)) {
        azh.modal_options = [];
    }
    azh.modal_options = azh.modal_options.concat([
        {
            "section_control": false,
            "refresh": false,
            "button_text": "",
            "button_class": "azh-video",
            "button_type": "azh-edit-link",
            "title": "Edit Video URL",
            "desc": "Specify the URL of the video which will be showed in popup",
            "label": "URL",
            "selector": "a.az-iframe-popup",
            "attribute": "href"
        }
    ]);
    //univer
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-dark]",
            "attribute": "data-dark",
            "control_type": "data-dark",
            "control_text": "Styles for white background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-white]",
            "attribute": "data-white",
            "control_type": "data-white",
            "control_text": "Styles for dark background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-completed]",
            "attribute": "data-completed",
            "control_type": "data-completed",
            "control_text": "Completed"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-enable-or-disable-title]",
            "attribute": "data-enable-or-disable-title",
            "control_type": "data-enable-or-disable-title",
            "control_text": "Show title"
        }
    ]);
    //food
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-white-background]",
            "attribute": "data-white-background",
            "control_type": "data-white-background",
            "control_text": "White background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-accent-font]",
            "attribute": "data-accent-font",
            "control_type": "data-accent-font",
            "control_text": "Use accent font"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-brand-color]",
            "attribute": "data-brand-color",
            "control_type": "data-brand-color",
            "control_text": "Use brand color"
        }
    ]);
    //rafale
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-top]",
            "attribute": "data-over-top",
            "control_type": "data-over-top",
            "control_text": "Overlay to above content"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-bottom]",
            "attribute": "data-over-bottom",
            "control_type": "data-over-bottom",
            "control_text": "Overlay to below content"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-best-seller]",
            "attribute": "data-best-seller",
            "control_type": "data-best-seller",
            "control_text": "Best seller"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-text-color]",
            "attribute": "data-text-color",
            "control_type": "data-text-color",
            "control_text": "Black font color"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-darken-background]",
            "attribute": "data-darken-background",
            "control_type": "data-darken-background",
            "control_text": "Darken background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-background-color-black]",
            "attribute": "data-background-color-black",
            "control_type": "data-background-color-black",
            "control_text": "Black background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-black-background]",
            "attribute": "data-black-background",
            "control_type": "data-black-background",
            "control_text": "Black background"
        }
    ]);
    //creo
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-content-revers]",
            "attribute": "data-content-revers",
            "control_type": "data-content-revers",
            "control_text": "Reverse content"
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
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-bottom]",
            "attribute": "data-over-bottom",
            "control_type": "data-over-bottom",
            "control_text": "Overlay to below content"
        }
    ]);
    //doo
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-over-content]",
            "attribute": "data-over-content",
            "control_type": "data-over-content",
            "control_text": "Overlay to below content"
        }
    ]);
    //strong
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-reverse]",
            "attribute": "data-reverse",
            "control_type": "data-reverse",
            "control_text": "Reverse content"
        }
    ]);
    //listino
    azh.controls_options = azh.controls_options.concat([
    ]);
    //citrix
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-inverted-styles]",
            "attribute": "data-inverted-styles",
            "control_type": "data-inverted-styles",
            "control_text": "Inverted styles"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-gray-background]",
            "attribute": "data-gray-background",
            "control_type": "data-gray-background",
            "control_text": "Gray background"
        },
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-light-branded-background]",
            "attribute": "data-light-branded-background",
            "control_type": "data-light-branded-background",
            "control_text": "Light-branded background"
        }
    ]);
    //citra
    azh.controls_options = azh.controls_options.concat([
        {
            "type": "toggle-attribute",
            "menu": "utility",
            "control_class": "azh-toggle",
            "selector": "[data-alternative-styles]",
            "attribute": "data-alternative-styles",
            "control_type": "data-alternative-styles",
            "control_text": "Alternative styles"
        }
    ]);    
})(window.jQuery);