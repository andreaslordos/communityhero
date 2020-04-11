(function($) {
    "use strict";

    function initProducts() {
        if ('select2' in $.fn) {
            $('select[name="product_cat"]:not(.hierarchical)').select2();
            $('select[name="product_category"]:not(.hierarchical)').select2();
        }
    }
    function initProductCategoriesWidget() {
        $('ul.product-categories li.cat-parent a').on('click', function(event) {
            event.stopPropagation();
        });
        $('ul.product-categories li.cat-parent').on('click', function(event) {
            var item = this;
            var children = $(this).find('> ul.children');
            if (children.css('display') == 'none') {
                children.stop(true, true).slideDown();
                children.show();
                $(item).find('> a').addClass('open');
            } else {
                children.stop(true, true).slideUp(400, function() {
                    children.hide();
                    $(item).find('> a').removeClass('open');
                });
            }
            event.stopPropagation();
            return false;
        });
    }
    function initQuantity() {
        $('.quantity input[name="quantity"]').each(function() {
            var qty_el = this;
            $(qty_el).parent().find('.qty-increase').off('click.azexo-woo').on('click.azexo-woo', function() {
                var qty = qty_el.value;
                if (!isNaN(qty))
                    qty_el.value++;
            });
            $(qty_el).parent().find('.qty-decrease').off('click.azexo-woo').on('click.azexo-woo', function() {
                var qty = qty_el.value;
                if (!isNaN(qty) && qty > 1)
                    qty_el.value--;
            });
        });
        return false;
    }
    function initReviewRatings() {
        $('.comment-form-mark select').hide().before('<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>');

        $('body').on('click', '.comment-form-mark p.stars a', function() {
            var $star = $(this),
                    $rating = $(this).closest('.comment-form-mark').find('select'),
                    $container = $(this).closest('.stars');

            $rating.val($star.text());
            $star.siblings('a').removeClass('active');
            $star.addClass('active');
            $container.addClass('selected');

            return false;
        });
    }
    function initInfiniteScroll() {
        if ('infinitescroll' in $.fn) {
            if ($('#content.infinite-scroll > ul.products').length) {
                $('#content.infinite-scroll > ul.products').infinitescroll({
                    navSelector: "nav.woocommerce-pagination",
                    nextSelector: "nav.woocommerce-pagination a.next",
                    itemSelector: "#content > ul.products > li",
                    loading: {
                        img: azexo.templateurl + "/images/infinitescroll-loader.svg",
                        msgText: '<em class="infinite-scroll-loading">Loading ...</em>',
                        finishedMsg: '<em class="infinite-scroll-done">Done</em>',
                    },
                    errorCallback: function() {
                    }
                }, function(arrayOfNewElems) {
                    $(document).trigger('infinitescroll', {new_elems: arrayOfNewElems});
                    window.azexo.refresh();
                    $('#content.infinite-scroll .image.lazy').each(function() {
                        if ($(this).data('waypoint_handler'))
                            $(this).data('waypoint_handler')();
                    });
                });
                $('#content.infinite-scroll .image.lazy').each(function() {
                    if ($(this).data('waypoint_handler'))
                        $(this).data('waypoint_handler')();
                });
                $('#content.infinite-scroll nav.woocommerce-pagination').hide();
            }
        }
    }
    function initSingleAddToCartAJAX() {
        /* global wc_add_to_cart_params */
        if (typeof wc_add_to_cart_params === 'undefined') {
            return false;
        }
        // Ajax add to cart
        $(document).on('click', '.single_add_to_cart_button', function() {

            // AJAX add to cart request
            var $thisbutton = $(this);

            if (!$('body').is('.woocommerce')) {


                if ($thisbutton.closest('form').find('input[name="product_id"], input[name="add-to-cart"], [name="add-to-cart"][value]').length == 0) {
                    return true;
                }

                $thisbutton.removeClass('added');
                $thisbutton.addClass('loading');

                var data = {};

                $thisbutton.closest('form').find('input[name]').each(function() {
                    data[$(this).attr('name')] = $(this).attr('value');
                });
                if ('variation_id' in data) {
                    data['product_id'] = data['variation_id'];
                }
                if (!('product_id' in data) && 'add-to-cart' in data) {
                    data['product_id'] = data['add-to-cart'];
                }
                if (!('product_id' in data) && $thisbutton.closest('form').find('[name="add-to-cart"][value]').length) {
                    data['product_id'] = $thisbutton.closest('form').find('[name="add-to-cart"][value]').attr('value');
                }
                if ('add-to-cart' in data) {
                    delete data['add-to-cart'];
                }
                $.each(data, function(key, value) {
                    $thisbutton.data(key, value);
                });

                // Trigger event
                $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

                // Ajax action
                $.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function(response) {

                    if (!response) {
                        return;
                    }

                    var this_page = window.location.toString();

                    this_page = this_page.replace('add-to-cart', 'added-to-cart');

                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    // Redirect to cart option
                    if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {

                        window.location = wc_add_to_cart_params.cart_url;
                        return;

                    } else {

                        $thisbutton.removeClass('loading');

                        var fragments = response.fragments;
                        var cart_hash = response.cart_hash;

                        // Block fragments class
                        if (fragments) {
                            $.each(fragments, function(key) {
                                $(key).addClass('updating');
                            });
                        }

                        // Block widgets and fragments
                        $('.shop_table.cart, .updating, .cart_totals').fadeTo('400', '0.6').block({
                            message: null,
                            overlayCSS: {
                                opacity: 0.6
                            }
                        });

                        // Changes button classes
                        $thisbutton.addClass('added');

                        // View cart text
                        if (!wc_add_to_cart_params.is_cart && $thisbutton.parent().find('.added_to_cart').length === 0) {
                            $thisbutton.after(' <a href="' + wc_add_to_cart_params.cart_url + '" class="added_to_cart wc-forward" title="' +
                                    wc_add_to_cart_params.i18n_view_cart + '">' + wc_add_to_cart_params.i18n_view_cart + '</a>');
                        }

                        // Replace fragments
                        if (fragments) {
                            $.each(fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Unblock
                        $('.widget_shopping_cart, .updating').stop(true).css('opacity', '1').unblock();

                        // Cart page elements
                        $('.shop_table.cart').load(this_page + ' .shop_table.cart:eq(0) > *', function() {

                            $('.shop_table.cart').stop(true).css('opacity', '1').unblock();

                            $(document.body).trigger('cart_page_refreshed');
                        });

                        $('.cart_totals').load(this_page + ' .cart_totals:eq(0) > *', function() {
                            $('.cart_totals').stop(true).css('opacity', '1').unblock();
                        });

                        // Trigger event so themes can refresh other areas
                        $(document.body).trigger('added_to_cart', [fragments, cart_hash, $thisbutton]);
                    }
                });

                return false;

            }

            return true;
        });
    }
    function initMiniCartRemoveAJAX() {
        $(document.body).on('added_to_cart', function(event, button, data) {
            setTimeout(function(){
                initMiniCartRemoveAJAX();
            });            
        });
        $('.widget_shopping_cart .mini_cart_item a.remove').off('click').on('click', function() {
            $('.widget_shopping_cart_content').addClass('updating');
            $.ajax({
                type: 'GET',
                url: $(this).attr('href'),
                dataType: 'html',
                complete: function() {
                    $.post(azexo.ajaxurl, {
                        'action': 'azexo_update_mini_cart'
                    }, function(response) {
                        $('.widget_shopping_cart_content').removeClass('updating');
                        $('.widget_shopping_cart_content').html(response);
                        initMiniCartRemoveAJAX();
                    });
                }
            });
            return false;
        });
    }
    $(function() {
        function msieversion() {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0)      // If Internet Explorer, return version number
                return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));

            return false;
        }

        initProducts();
        initProductCategoriesWidget();
        initQuantity();
        initReviewRatings();
        initInfiniteScroll();
        initSingleAddToCartAJAX();
        initMiniCartRemoveAJAX();
        $(document).ajaxComplete(function() {
            initQuantity();
        });
        $(document.body).on('adding_to_cart', function(event, button, data) {
            $('.menu-item.cart .count').each(function() {
                var count = parseInt($(this).text(), 10);
                count++;
                $(this).text(count);
            });
        });
        $(document.body).on('updated_checkout', function(sender, data) {
            $('label > input[type="checkbox"]').each(function() {
                var $this = $(this);
                var $label = $this.parent();
                $this.detach();
                $this.insertBefore($label);
                if ((!$this.attr('id') && !$label.attr('for')) || ($('#' + $this.attr('id')).length > 1)) {
                    $this.attr('id', makeid());
                    $label.attr('for', $this.attr('id'));
                } else {
                    if ($this.attr('id')) {
                        $label.attr('for', $this.attr('id'));
                    } else {
                        if ($label.attr('for')) {
                            $this.attr('id', $label.attr('for'));
                        }
                    }
                }
            });
        });


        if (!msieversion()) {
            var current_url = window.location.href;
            current_url = current_url.replace('?registered=true', '');
            current_url = current_url.replace('?verify=false', '');
            current_url = current_url.replace('?verify=true', '');
            current_url = current_url.replace('?resend=true', '');
            window.history.replaceState('', '', current_url);
        }

        $('.header-my-account .form form').attr('action', azexo_woo.myaccounturl);
        $('.header-my-account .form form [name="_wp_http_referer"]').val(azexo_woo.myaccounturl);
    });
})(jQuery);