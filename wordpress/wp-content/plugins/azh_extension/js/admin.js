(function($) {
    "use strict";
    function get_fonts() {
        var fonts = {};
        if ('dirs_options' in azh) {
            for (var dir in azh.dirs_options) {
                if ('google-fonts' in azh.dirs_options[dir]) {
                    var families = azh.dirs_options[dir]['google-fonts'].split("\n");
                    for (var i = 0; i < families.length; i++) {
                        var familiy = families[i];
                        var font = familiy.split(':');
                        if (!(font[0] in fonts)) {
                            fonts[font[0]] = {};
                        }
                        if (font.length == 2) {
                            font[1].split(',').map(function(weight) {
                                fonts[font[0]][weight] = 1;
                            });
                        }
                    }
                }
            }
        }
        return fonts;
    }
    var options = {};
    Object.keys(get_fonts()).map(function(font) {
        options[font] = font.replace(/\+/g, ' ');
        return true;
    });
    var fonts_pattern = {
        options: options,
        pattern: "font-family: ?['\"]([^\'\"]+)['\"]",
    };    
    $(function() {
        azh.options.dropdown_patterns.push(fonts_pattern);
    });
})(window.jQuery);