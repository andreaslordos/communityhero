=== Cart links for WooCommerce  ===
Contributors: josk79
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5T9XQBCS2QHRY&lc=NL&item_name=Jos%20Koenis&item_number=wordpress%2dplugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: woocommerce, cart, url
Requires at least: 4.0.0
Tested up to: 4.8.3
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create a url that will populate a cart

== Description ==

"Cart links for WooCommerce" allows the shop manager to create urls that will redirect the customer to a fully populated cart. This is extremely useful for email campaigns or call to action buttons. If the customer already has items in the cart, they will be kept.

The shop manager can easily create the url by populating his own shopping cart, the cart url will be displayed on the cart page. The shop manager can copy this url to the clipboard and paste it anywhere he wants, for example in a promotional email.

The url looks like this: `www.example.com?fill_cart=12,2x44`
This url will populate the cart with 1x item with id 12, and 2x item with id 44.

== Installation ==

1. Upload the plugin in the `/wp-content/plugins/` directory, or automatically install it through the 'New Plugin' menu in WordPress
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Shop manager can easily copy a cart link after populating a cart

== Changelog ==

= 1.1.4 =
* FIX: Deprecation warning
* Added russian translation. Thank you Artem!

= 1.1.3 =
* FEATURE: Use ?set_cart= instead of ?fill_cart= to replace the cart contents (empty cart before adding the items)

= 1.1.2 =
* FIX: Fatal error

= 1.1.0 =
* FIX: WooCommerce 3.0 compatibility

= 1.0.1 =
* FIX: Possible fatal error. (get_plugin_data is only available on admin)

= 1.0.0 =
* FIX: jQuery dependency

= 0.1.2 =
* FIX: redirection to wrong url when home_url contained a subdirectory

= 0.1.1 =
* First public release
