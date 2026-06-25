=== Tax Switch for WooCommerce ===
Contributors: wijnbergdevelopments
Tags: woocommerce, tax, vat
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 1.7.0
Requires PHP: 7.2
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Let customers toggle between inclusive and exclusive VAT pricing in your WooCommerce store.

== Description ==
Tax Switch for WooCommerce enhances your WooCommerce store by allowing users to toggle between displaying prices including or excluding VAT. This plugin adds a customizable switch component and provides a flexible way to display both price versions.

=== Key features ===

* Display customizable switches where you want
* Gutenberg block support
* Shortcode for easy integration (including shortcode generator)
* Flexible display options for prices with and without VAT
* Remembers the user's preference for future visits
* Choose between a toggle switch or buttons

For more information about this plugin, please visit the [plugin page](https://products.wijnberg.dev/product/wordpress/plugins/tax-switch-for-woocommerce/).

=== Requirements ===

* WooCommerce plugin installed and activated
* WooCommerce tax calculations enabled and configured

=== Configuration ===

Configure the plugin settings below for proper functionality.

= WooCommerce settings =

Ensure these WooCommerce settings are configured first:

1. **Configure tax calculations**
   - Go to: *WooCommerce > Settings > General*
   - Verify your shop address is complete
   - Enable *"Enable tax rates and calculations"*
   - Set *"Default customer location"* to *"Shop base address"*

2. **Set up tax rates**
   - Go to: *WooCommerce > Settings > Tax > Standard Rates*
   - Add your regional tax rates

3. **Recommended: tax calculation method**
   - Go to: *WooCommerce > Settings > Tax*
   - Set *"Calculate tax based on"* to *"Shop base address"*
   *(This provides instant tax calculation. Other methods require customers to enter their address first.)*

4. **Individual product configuration**
   - Edit products at: *Products > [Product]*
   - Under *Product Data > Tax*, set status to *"Taxable"*

= Plugin settings =

Configure these plugin-specific settings:

1. **Main settings**
   - Go to: *WooCommerce > Settings > Tax Switch*
   - Set your preferred text values
   - Optional: Restrict display locations
   - Optional: Generate a shortcode via *WooCommerce > Settings > Tax Switch > Shortcode*

=== Usage ===

After installation and configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Tax Switch for WooCommerce" in your page or post editor.
2. Use the shortcode `[wdevs_tax_switch]` anywhere in your content.

= Shortcode Usage =

**Switch/buttons**

Basic usage:
[wdevs_tax_switch]

Displays a switch to toggle displaying prices including or excluding VAT.

The shortcode accepts several attributes to customize its appearance and behavior:

* `class-name`: Adds custom CSS classes to the switch.
    - Default: is-style-default
    - Options: is-style-default, is-style-inline, is-style-flat-pill, or custom classes
* `switch-type`: Determines the style of the toggle.
 	- Default: `switch`
 	- Options: `switch`, `buttons`
* `switch-color`: Sets the color of the switch handle.
* `switch-color-checked`: Sets the color of the switch when it's in the "on" position.
* `switch-background-color`: Sets the background color of the switch.
* `switch-text-color`: Sets the text color of the switch labels.
* `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.
* `switch-label-incl`: Sets the text for the "including VAT" label.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
* `switch-label-excl`: Sets the text for the "excluding VAT" label.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.
* `switch-aria-label`: Sets the aria label of the switch.
    - Default: Uses the text set in the plugin settings or "Switch between prices including and excluding VAT" if not set.

Example with custom attributes:

`[wdevs_tax_switch class-name="is-style-inline" switch-type="switch" switch-color="#ffffff" switch-color-checked="#000000" switch-background-color="#000000" switch-background-color-checked="#4CAF50" switch-text-color="#FF0000" switch-label-incl="Incl. tax" switch-label-excl="Excl. tax" switch-aria-label="Switch between prices including and excluding VAT"]`


**Label**

Basic usage:
[wdevs_tax_switch_label]

Displays text indicating the currently selected tax setting. The text updates automatically when the tax switch is toggled.

The shortcode accepts several attributes to customize its appearance and behavior:

* `class-name`: Adds custom CSS classes to the label.
    - Default: is-style-default
    - Options: is-style-default or custom classes
* `label-text-incl`: Sets the text to display when "including VAT" is selected.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
* `label-text-excl`: Sets the text to display when "excluding VAT" is selected.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.
* `label-text-color`: Sets the "excluding VAT" text color.
* `label-text-color-checked`: Sets the "including VAT" text color.

Example with custom attributes:

`[wdevs_tax_switch_label class-name="tax-indicator" label-text-incl="Prices include tax" label-text-excl="Prices exclude tax" label-text-color="#FF0000" label-text-color-checked="#4CAF50"]`


= PHP implementation =

You can use these shortcodes with PHP with the do_shortcode() function:

`<?php echo do_shortcode('[wdevs_tax_switch]'); ?>`
`<?php echo do_shortcode('[wdevs_tax_switch_label]'); ?>`


= JavaScript API =

Tax Switch provides a small frontend JavaScript API for custom integrations, such as buttons, preference popups, or other flows where visitors choose whether they want to see prices including or excluding VAT.

`
if ( window.wdevsTaxSwitch && ! window.wdevsTaxSwitch.hasSavedChoice() ) {
	window.wdevsTaxSwitch.setDisplay( 'incl', {
		respectExistingChoice: true,
	} );
}
`

API methods:
* `hasSavedChoice()`: returns whether the visitor already has a saved switch choice.
* `getDisplay()`: returns the current display, either `'incl'` or `'excl'`.
* `setDisplay( display, options )`: changes the display to `'incl'` or `'excl'`.
* `toggle( options )`: toggles between inclusive and exclusive VAT display.

Options:
* `respectExistingChoice`: `boolean` - preserves an existing saved visitor choice.

Set `respectExistingChoice` to `true` when you only want to apply a default if the visitor has not already made a saved choice.

You can also request a display change by dispatching an event. This is useful when your integration is event-driven.

`
document.dispatchEvent(
	new CustomEvent( 'wdevs-tax-switch-context-changed', {
		detail: {
			display: 'incl',
			respectExistingChoice: true,
		},
	} )
);
`

Detail properties:
* `display`: `'incl' | 'excl' | 'toggle'` - requested tax display.
* `respectExistingChoice`: `boolean` - preserves an existing saved visitor choice.

= JavaScript events =

The switch fires a JavaScript event when the tax display changes. You can listen for this event to execute custom code when a user switches between inclusive and exclusive VAT display. This is useful for when you need to perform additional actions based on the tax display state.

`
document.addEventListener( 'wdevs-tax-switch-changed', function( event ) {
	console.log( event.detail );
} );
`

Detail properties:
* `isSwitched`: `boolean` - the raw switch state.
* `displayIncludingVat`: `boolean` - whether prices now display including VAT.

If you are loading the switch dynamically (via AJAX), dispatch this event after rendering to initialize the component:

`
document.dispatchEvent( new CustomEvent( 'wdevs-tax-switch-appeared' ) );
`

=== WPML ===

To translate the option texts via WPML:

1. Save your options first in: WooCommerce -> Settings -> Tax Switch
2. Then translate the texts in: WPML -> String Translations and search for your option values in the domain 'tax-switch-for-woocommerce'

=== Compatibility ===

This plugin integrates with WooCommerce's standard filters and actions for price display and calculation. While most plugins and themes work out of the box, some third-party code use custom price building methods that require specific compatibility integrations.

The following themes have been tested and confirmed compatible:

* GeneratePress
* Blocksy
* Thrive
* Flatsome
* Kapee
* Entr
* Woodmart
* Hello Elementor

The following plugins have been tested and confirmed compatible:

* WooCommerce Product Table Lite (+ PRO)
* Tiered Pricing Table for WooCommerce (+ Premium)
* Measurement Price Calculator for WooCommerce
* Discount Rules for WooCommerce
* YITH WooCommerce Product Add-Ons (+ & Extra Options Premium)
* JetEngine Listing Grid (Elementor)
* Product Add-Ons for WooCommerce
* B2BKing – Ultimate WooCommerce Wholesale and B2B Solution (+ Premium)
* Advanced Product Fields Pro for WooCommerce
* WooCommerce Quantity Discounts, Rules & Swatches
* FacetWP
* Variation Swatches for WooCommerce (+ PRO)
* Variation Price Display Range for WooCommerce (+ PRO)
* WooCommerce Product Add-Ons Ultimate
* Advanced Woo Search (+ PRO)
* B2B Market
* FiboFilters
* Extra Product Options & Add-Ons for WooCommerce
* FiboSearch – Ajax Search for WooCommerce (+ Pro)
* YayMail - WooCommerce Email Customizer
* PDF Invoices & Packing Slips for WooCommerce
* WooCommerce Product Bundles
* LiteSpeed Cache
* Elementor Pro
* YITH WooCommerce Role Based Prices
* WooCommerce Dynamic Pricing and Discount Rules
* Price Based on Country for WooCommerce
* [WP Grid Builder](https://wordpress.org/support/topic/plugin-not-working-with-gridbuilder-on-archive-pages/)

If you encounter any compatibility issues with other plugins or themes, please let us know. Your feedback helps us improve the plugin and extend compatibility to more third-party solutions.

=== Incompatibility ===

After multiple attempts to create compatibility functions, reaching out to the plugin developers several times, and still finding no viable solution, the following plugins remain incompatible:

* Unlimited Elements for Elementor (+ Pro): AJAX pagination and filtering issues
* Barn2: WooCommerce Product Options


== Installation ==

1. Install the plugin through the WordPress plugins screen directly or Upload the plugin files to the `/wp-content/plugins/tax-switch-for-woocommerce` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure your settings as described in the 'Configuration' section.

== Frequently Asked Questions ==

= Are there any known compatibility issues? =

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin. You can fix this by using WooCommerce shortcodes instead of the WooCommerce Blocks.

= Why do prices stay the same after switching? =

Sometimes prices may not appear to change when toggled. This is often related to WooCommerce tax settings. If possible, select 'Shop based' in WooCommerce → Settings → Tax → "Calculate tax based on". Otherwise, WooCommerce requires a billing/shipping address to calculate taxes, which is typically only available after login or during checkout.

= Why don't prices update in the cart/checkout when switching? =

The plugin is designed to keep prices consistent in the cart and checkout process. There are two main reasons for this:

1. **Customer clarity**: This ensures visitors always see the exact final amount they'll pay, without unexpected changes during checkout.
2. **Technical simplicity**: While dynamic price switching could be implemented, it would require compatibility with various third-party cart/checkout plugins. The focus is, at the moment, on maintaining a lightweight and reliable solution rather than this specific feature.


== Changelog ==
= 1.7.0 =
* Improved product context detection for price calculations with custom tax classes
* Improved React compatibility by reusing existing roots when dynamically rendering tax switches
* Added a public JavaScript API that allows third-party scripts to read and change the tax display via `window.wdevsTaxSwitch` or the `wdevs-tax-switch-context-changed` event.

= 1.6.13 =
* Tested WordPress 7.0
* Tested WooCommerce 10.7.0

= 1.6.12 =
* Fixed swapped VAT label classes

= 1.6.11 =
* Improved compatibility between Tiered Pricing Table and FiboFilters for dynamically loaded prices
* Improved compatibility for PDF Invoices & Packing Slips for WooCommerce

= 1.6.10 =
* Added compatibility for WP Grid Builder

= 1.6.9 =
* Added new option for setting the tax switch aria-label
* Added new filter: `wdevs_tax_switch_skip_next_price_wrap` to allow third-parties to skip the next price wrapping

= 1.6.8 =
* Added compatibility for Elementor Popup
* Added compatibility for Price Based on Country for WooCommerce
* Improved AJAX request handling (special thanks to [Peter-Jan Timmermans](https://www.indegymzaal.nl/))

= 1.6.7 =
* Added compatibility for WooCommerce Dynamic Pricing and Discount Rules

= 1.6.6 =
* Improved switch accessibility and mobile UX
* Tested WooCommerce 10.4.3

= 1.6.5 =
* Skip price wrapping in coupon min/max error messages

= 1.6.4 =
* Added compatibility for YITH WooCommerce Role Based Prices

= 1.6.3 =
* Added new style: `flat pill`
* Tested WooCommerce 10.3.6
* Tested WordPress 6.9

= 1.6.2 =
* Added new filter: `wdevs_tax_switch_current_product` to allow third-parties to set the correct product context
* Added compatibility for WooCommerce Product Bundles
* Hardened frontend init so switches/labels still render when scripts load after DOMContentLoaded (e.g. LiteSpeed Delay/Defer JS)

= 1.6.1 =
* Improved FacetWP compatibility
* Added compatibility for YayMail - WooCommerce Email Customizer
* Added compatibility for PDF Invoices & Packing Slips for WooCommerce
* Tested WooCommerce 10.3.5

= 1.6.0 =
* Added option to enable price switching in the mini cart
* Added compatibility for FiboSearch - AJAX Search for WooCommerce (+ Pro)
* Added new filter: `wdevs_tax_switch_should_hide_on_current_page` for custom visibility control
* Improved AJAX compatibility. Price switching should be included in AJAX-loaded content
* Improved compatibility for WoodMart theme
* Added FacetWP slider price switching
* Added 'disabled' state

= 1.5.19 =
* Added PJAX compatibility for themes using jquery-pjax (e.g. WoodMart theme)

= 1.5.18 =
* Fixed incorrect tax calculation for variation products loaded via AJAX

= 1.5.17 =
* Tested WooCommerce 10.2.2
* Improved compatibility for Measurement Price Calculator for WooCommerce

= 1.5.16 =
* Improved compatibility for Measurement Price Calculator for WooCommerce

= 1.5.15 =
* Ignore zero tax price check if customer is vat exempt

= 1.5.14 =
* Fixed price calculation if tax is zero for the current customer (zero tax rate country)

= 1.5.13 =
* Fixed tax calculation if product price is zero

= 1.5.12 =
* Added compatibility for Extra Product Options & Add-Ons for WooCommerce
* Tested WooCommerce 10.1.0

= 1.5.11 =
* Added wp_kses_allowed_html filter to allow spans in certain contexts for price displays
* Added compatibility for Entr theme. [See this issue](https://github.com/Paulsky/tax-switch/issues/3)
* Improved compatibility with GeneratePress

= 1.5.10 =
* Added compatibility for FiboFilters

= 1.5.9 =
* Added compatibility for Kapee theme
* Added compatibility for B2B Market
* Removed load_plugin_textdomain() because it has been discouraged since WordPress version 4.6.
* Tested WooCommerce 10.0.2

= 1.5.8 =
* Added compatibility for Advanced Woo Search (+ PRO)

= 1.5.7 =
* Tested WooCommerce 9.9.4
* Improved translations

= 1.5.6 =
* Improved compatibility for Tiered Pricing Table for WooCommerce

= 1.5.5 =
* Added compatibility for WooCommerce Product Add-Ons Ultimate

= 1.5.4 =
* Introduced 'tree shake' for bundling all commonly shared JavaScript into a separate file as optimisation to prevent duplicated code
* Improved compatibility for Variation Price Display Range for WooCommerce

= 1.5.3 =
* Switched from 'found_variation' to 'show_variation' WooCommerce event for UI updates reliability in variations form
* Above change adds compatibility for Variation Swatches for WooCommerce
* Added preview in shortcode generator
* Added JavaScript 'wdevs-tax-switch-appeared' event listener
* Compatibility for Variation Price Display Range for WooCommerce (1.3.20 and up)
* Added settings link

= 1.5.2 =
* Fix for WooCommerce text tax labels
* Disable price changes in WooCommerce table total rows
* Added option to disable the components on WooCommerce pages or pages without prices
* Possible breaking change: always disable components on the cart and checkout pages. Undo previous (1.5.1) opt-in function.

= 1.5.1 =
* Added option to hide the components on the cart and checkout pages

= 1.5.0 =
* Gutenberg block/shortcode for showing text about the currently selected tax setting. [See this topic](https://wordpress.org/support/topic/shortcode-for-wdevs-tax-switch-label-text/)
* Added compatibility for Flatsome theme
* Added compatibility for FacetWP
* Tested WooCommerce 9.8.2
* Possible breaking change: refactored the (block) code structure to support multiple blocks

= 1.4.3 =
* Tested WordPress 6.8.0
* Tested WooCommerce 9.8.1
* Security improvement: escaped dynamic class names in HTML output (reported by Peter Thaleikis).

= 1.4.2 =
* Added compatibility for WooCommerce Quantity Discounts, Rules & Swatches

= 1.4.1 =
* Added compatibility for Advanced Product Fields Pro for WooCommerce

= 1.4.0 =
* Added shortcode generator

= 1.3.2 =
* Translate shortcode switch labels. [See this topic](https://wordpress.org/support/topic/language-support-in-shortcode/#post-18379279)

= 1.3.1 =
* Tested with WooCommerce 9.7.1

= 1.3.0 =
* Added 'buttons' switch type
* Added Swedish language (special thanks to Martin Hult)
* Added color setting for label text
* Added JavaScript 'wdevs-tax-switch-changed' event

= 1.2.5 =
* Fixed tax calculation if customer is VAT exempt

= 1.2.4 =
* Always register the block/shortcode scripts and styles. Only enqueue them if the block or shortcode is executed
* Fixed price display issues in Tiered Pricing Table for WooCommerce when shop prices are set to display excluding VAT by default

= 1.2.3 =
* Added incompatibility list
* Updated incompatibility for Unlimited Elements for Elementor (+ Pro)

= 1.2.2 =
* Updated compatibility for WooCommerce Product Table Lite / PRO

= 1.2.1 =
* Added compatibility for WPML

= 1.2.0 =
* Disable the plugin in the WooCommerce mini cart widget

= 1.1.11 =
* Added compatibility for Product Add-Ons for WooCommerce

= 1.1.10 =
* Added compatibility for JetEngine Listing Grid 'infinity scroll'

= 1.1.9 =
* Added extra check for filtering backend and frontend AJAX requests (which adds compatibility for [PDF Invoices & Packing Slips for WooCommerce](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/))
* Fixed a bug where the admin request checks failed when Wordpress is installed in a subdirectory

= 1.1.8 =
* Added compatibility for YITH WooCommerce Product Add-ons & Extra Options Premium

= 1.1.7 =
* Added compatibility for [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/)

= 1.1.6 =
* Removed applying 'woocommerce_get_price_html' filter a second time to prevent nested duplications

= 1.1.5 =
* Added extra checks for only executing the filters on the frontend

= 1.1.4 =
* Improved compatibility for Tiered Pricing Table for WooCommerce

= 1.1.3 =
* Migrated filters from only using 'woocommerce_get_price_html' to 'woocommerce_get_price_html' in combination with 'wc_price'. This way, the plugin should be more compatible with other plugins.
* Added compatibility for [Discount Rules for WooCommerce](https://wordpress.org/plugins/woo-discount-rules/)
* Added compatibility for Tiered Pricing Table for WooCommerce on catalog pages

= 1.1.2 =
* Added compatibility for Measurement Price Calculator for WooCommerce.

= 1.1.1 =
* Added compatibility for Tiered Pricing Table for WooCommerce Premium (single product page).

= 1.1.0 =
* Added compatibility for [Tiered Pricing Table for WooCommerce](https://wordpress.org/plugins/tier-pricing-table/) (single product page).
* Added compatibility for [WooCommerce Product Table](https://wordpress.org/plugins/wc-product-table-lite/)

= 1.0.1 =
* Fixed bug where JS was not loaded when element was rendered by shortcode.

= 1.0.0 =
* Initial release of Tax Switch for WooCommerce.

== Additional Information ==

This plugin is fully open source. You can find the source code on [GitHub](https://github.com/Paulsky/tax-switch)

For more information and other WordPress plugins, visit [Wijnberg Developments](https://products.wijnberg.dev/product-category/wordpress/plugins/).

== Screenshots ==

1. This GIF demonstrates the main functionality of the plugin.
