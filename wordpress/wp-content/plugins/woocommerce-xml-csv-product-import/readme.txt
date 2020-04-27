=== Import Products from any XML or CSV to WooCommerce ===
Contributors: soflyy, wpallimport 
Requires at least: 4.1
Tested up to: 5.3
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: woocommerce xml import, woocommerce csv import, woocommerce, import, xml, csv, wp all import, csv import, import csv, xml import, import xml, woocommerce csv importer, woocommerce xml importer, csv importer, csv import suite

Easily import products from any XML or CSV file to WooCommerce with the WooCommerce add-on for WP All Import.

== Description ==

*“I've been doing eCommerce sites for almost a decade. The combination of WP All Import and WooCommerce is a game changer! I can now get clients into eCommerce sites that could never afford the time/energy or money it took to administrate a site. It has opened up a whole new client base for me.”*  
**Mike Tidmore** -Founder, Successful Online Stores

The WooCommerce add-on for [WP All Import](http://wordpress.org/plugins/wp-all-import/) makes it easy to bulk import your products to WooCommerce in less than 10 minutes.

The left side of the plugin looks just like WooCommerce, and the right side displays a product from your XML/CSV file. 

**Drag & drop the data from your XML or CSV into the WooCommerce fields to import it.**

The importer is so intuitive it is almost like manually adding a product in WooCommerce.

WooCommerce CSV imports? WooCommerce XML imports? They are EASY with WP All Import.

Here's why you should use the WooCommerce add-on for WP All Import:

 - Supports files in any format and structure. There are no requirements that the data in your file be organized in a certain way. WooCommerce CSV imports are easy, no matter the structure of your file. WooCommerce XML imports are flexible and work with any XML file.
 - Supports files of practically unlimited size by automatically splitting them into chunks. Import 200Mb+ product catalogs with ease, even on shared hosting. 

= WooCommerce Add-On Professional Edition =
[youtube http://www.youtube.com/watch?v=7kCmESmKGro]

The Pro edition of *WP All Import + the WooCommerce add-on* is a paid upgrade that includes premium support and adds the following features:

* [In-depth support for Variable products](http://www.wpallimport.com/documentation/woocommerce/variable-products/?utm_source=import-wooco-products-addon-free&utm_medium=readme&utm_campaign=import-variable-wooco-products) - example CSV files, ability to import variations from properly formatted XML, and much more.

* Import External/Affiliate products

* Import Grouped products

* Import files from a URL - Download and import files from external websites, even if they are password protected with HTTP authentication. 

* Cron Job/Recurring Imports - WP All Import pro can check periodically check a file for updates, and add, edit, delete, and update the stock status of the imported products accordingly.

* Execution of Custom PHP Functions on data, i.e. use something like [my_function({xpath/to/a/field[1]})] in your template, to pass the value of {xpath/to/a/field[1]} to my_function and use whatever it returns.

* Get access to our customer portal with documentation and tutorials, and e-mail technical support.

[Upgrade to the Pro edition of WP All Import + the WooCommerce add-on now.](http://www.wpallimport.com/woocommerce-product-import/?utm_source=import-wooco-products-addon-free&utm_medium=wp-plugins-page&utm_campaign=upgrade-to-pro "WooCommerce XML & CSV Import")

You need the WooCommerce add-on if you need to:

*   Import XML to WooCommerce
*   Import CSV to WooCommerce
*   Are frustrated with the limitations of the official WooThemes Product CSV Import Suite

= WooCommerce CSV Imports =

Of course, XML files can have complex structures, but for CSV files, you can easily edit them and change the column names.

When importing CSV files, you should use UTF-8 encoding (which is very standard) if you are having any trouble with CSV imports containing special characters. But other than that, there are no special requirements.

This importer is the best option for WooCommerce CSV import tasks - our importer is extremely flexible when doing CSV imports because you don't need to edit your CSV files to import them to WooCommerce. WP All Import can import ANY CSV file to WooCommerce. You don't need to layout your data in a specific way, and you don't need your CSV to have specific column names. WP All Import's drag & drop interface provides you with a visual way to map the columns in your CSV file to the appropriate fields in WooCommerce.

== Installation ==

First, install [WP All Import](http://wordpress.org/plugins/wp-all-import "WordPress XML & CSV Import").

Then install the WooCommerce add-on.

To install the WooCommerce add-on, either: -

- Upload the plugin from the Plugins page in WordPress
- Unzip woocommere-product-import-add-on-for-wp-all-import.zip and upload the contents to /wp-content/plugins/, and then activate the plugin from the Plugins page in WordPress

The WooCommerce add-on will appear in the Step 4 of WP All Import.

== Screenshots ==

1. The WooCommerce add-on.

== Changelog ==

= 1.4.4 =
* API: add wp_all_import_regenerate_lookup_tables filter to control lookup tables generation

= 1.4.3 =
* bug fix: lookup table not updating after import
* bug fix: attributes not re-counting after import

= 1.4.2 =
* bug fix: grant incorrect downloadable product permissions
* bug fix: update attributes with non utf-8 characters

= 1.4.1 =
* bug fix: re-import options not rendering correctly

= 1.4.0 =
* improvement: notice on plugin activation when WooCommerce Add-On Pro installed
* improvement: match cross-sell products by title
* bug fix: shipping class not imported properly in some cases

= 1.3.9 =
* bug fix: do not execute product import code when importing other post types

= 1.3.8 =
* bug fix: stock status not importing properly when _backorders custom field is not set
* bug fix: product dimensions won't import if 'Virtual' field is not set
* bug fix: compatibility fix WooCommerce 2.6.x
* bug fix: remove deprecated function calls for PHP 7.2 compatibility
* bug fix: unable to import 0 as a value for attributes
* bug fix: mirror new WooCommerce core behavior that forces all uncategorized products to be assigned to the Uncategorized category
* bug fix: import attributes with special characters
* bug fix: recount product terms when updating post status

= 1.3.7 =
* improvement: added new filter wp_all_import_recount_terms_after_import 
* bug fix: compatibility with woo commerce 2.6
* bug fix: do not update stock_status if _stock is not set to update

= 1.3.6 =
* improvement: added 'WooCommerce Advanced Options' to re-import section
* bug fix: variations title
* bug fix: import first variation image
* bug fix: send order emails after custom fields were imported
* bug fix: updating featured product status
* bug fix: WPML & link all variations option conflict
* bug fix: add _price field for each variation
* bug fix: terms re-count

= 1.3.5 =
* bug fix: import product attributes

= 1.3.4 =
* bug fix: import product visibility WC 3.0
* bug fix: stock threshold

= 1.3.3 =
* improvement: compatibility with WC 3.x

= 1.3.2 =
* bug fix: updating product gallery
* bug fix: import shipping class

= 1.3.1 =
* improvement: compatibility with PHP 7.x

= 1.3.0 =
* improvement: updated post types dropdown

= 1.2.9 =
* fixed ucwords attributes names
* fixed tooltips & css for woo 2.6 compatibility

= 1.2.8 = 
* fixed updating stock qty with disabled manage_stock import

= 1.2.7 =
* fixed compatibility with WPML ( import multilingual attributes )
* added possibility to import up & cross sells by product SKU, ID, Title

= 1.2.6 =
* fixed setting up shipping class to -1 when «No shipping class» option chosen

= 1.2.5 =
* fixed conflict between options [update only these custom fields & update only these attributes]
* added feature to dynamically set attribute options
* added new option "Convert decimal separator to a period"   

= 1.2.4 =
* added Variation Description field
* added auto create shipping classes
* removed 'Virtual' and 'Downloadable' checkboxes
* hide 'Downloadable' settings if product not downloadable

= 1.2.3 =
* fixed import total_sales

= 1.2.2 =
* fixed import stock status for negative qty
* fixed import shipping class when their slugs presented as numeric values

= 1.2.1 =
* fixed css styles
* added compatibility with woocommerce 2.3

= 1.2.0 = 
* IMPORTANT: WP All Import v4 (3.2.0) is a MAJOR update. Read this post before upgrading: (http://www.wpallimport.com/2014/11/free-version-wordpress-org-update-information)
* speed up the import of taxonomies/categories
* updated design
* new option - adjust prices (mark up, mark down, convert currency)
* added preview prices feature
* fixed importing of menu order

= 1.1.6 =
* fixed saving shipping class option
* fixed import product attributes

= 1.1.5 =
* fixed updating shipping class
* fixed updating tax class

= 1.1.4 =
* fixed automatic fixing of improperly formatted prices
* fixed php notices 
* updated css for compatibility with wocommerce 2.1
* added download type option
* added file names option

= 1.1.3 =
* Fixed price conversation

= 1.1.2 =
* Compatibility with WooCommerce 2.1
* updated price filter

= 1.1.1 =
* Compatibility with WooCommerce 2.1
* CSS fixes

= 1.1.0 =
* Compatibility with WP 3.8

= 1.0.1 =
* Fixed import product shipping class
* Fixed import attributes;
* Added SKU auto generation options;
* Updated parsing price, now plugin will delect price correctly even it present with currency symbol, like $100.20

= 1.0 =
* Official release on WP.org. Enhanced session handling.

= 0.9 =
* Initial release on WP.org.

== Support ==

Support for the free version of WP All Import is handled through the WordPress.org community forums.

Support is not guaranteed and is based on ability. For premium support over email, [purchase WP All Import and the WooCommerce add-on.](https://www.wpallimport.com/checkout/?edd_action=purchase_collection&taxonomy=download_category&terms=14&utm_source=import-wooco-products-addon-free&utm_medium=readme&utm_campaign=premium-support)
