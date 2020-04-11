(function($) {
    "use strict";

    $(function() {
        azsl.getParameterByName = function(name, url) {
            if (!url)
                url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
            if (!results)
                return null;
            if (!results[2])
                return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        if ('hello' in window) {
            if (azsl.logged_in != '1') {
                hello.init(azsl.networks, {
                    scope: 'email',
                    redirect_uri: azsl.homeurl + '?azsl=social-login-redirect'
                });
                hello.logout();
                $('a[href*="social-login"]').on('click', function(event) {
                    var responded = false;
                    var link = this;
                    hello.off('auth.login').on('auth.login', function(auth) {
                        hello(auth.network).api('me').then(function(p) {
                            if (!responded) {
                                if ('email' in p) {
                                    $(window).trigger('azsl-social-login');
                                    $.post(azsl.ajaxurl, {
                                        'action': 'azsl_social_login',
                                        'nonce': azsl.nonce,
                                        'email': p.email,
                                        'first_name': ('first_name' in p ? p.first_name : ''),
                                        'last_name': ('last_name' in p ? p.last_name : ''),
                                        'name': ('name' in p ? p.name : ''),
                                        'picture': ('picture' in p ? p.picture : ''),
                                        'timezone': ('timezone' in p ? p.timezone : '')
                                    }, function(response) {
                                        if (response == '') {
                                            location.href = $(link).attr('href');
                                        } else {
                                            location.href = response;
                                        }
                                    });
                                }
                            }
                            responded = true;
                        });
                    });
                    hello(azsl.getParameterByName('social-login', $(link).attr('href'))).login({force: true});
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });
            }
        }
    });
})(jQuery);