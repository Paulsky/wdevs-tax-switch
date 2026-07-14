# Changelog
All notable changes to the Tax Switch for WooCommerce plugin will be documented in this file.

## [1.8.2] - 2026-07-14
### Updated
- Improved compatibility for Advanced Product Fields Extended for WooCommerce
- Improved `blueprint.json`

## [1.8.1] - 2026-07-06
### Added
- `blueprint.json` for an interactive WordPress Playground demo
- Compatibility for Advanced Product Fields Extended for WooCommerce
### Updated
- Fixed tax display in mini cart after WooCommerce refreshes cart fragments

## [1.8.0] - 2026-06-29
### Added
- `refresh()` to the public JavaScript API for reapplying the current tax display
### Updated
- Standardized "VAT" terminology to "tax" across text, documentation and code
- Tested WooCommerce 10.9.1

## [1.7.0] - 2026-06-25
### Updated
- Improved product context detection for price calculations with custom tax classes
- Improved React compatibility by reusing existing roots when dynamically rendering tax switches
- Added a public JavaScript API that allows third-party scripts to read and change the tax display via `window.wdevsTaxSwitch` or the `wdevs-tax-switch-context-changed` event.

## [1.6.13] - 2026-05-23
### Updated
- Tested WordPress 7.0
- Tested WooCommerce 10.7.0

## [1.6.12] - 2026-05-01
### Updated
- Fixed swapped VAT label classes

## [1.6.11] - 2026-03-13
### Updated
- Improved compatibility between Tiered Pricing Table and FiboFilters for dynamically loaded prices
- Improved compatibility for PDF Invoices & Packing Slips for WooCommerce

## [1.6.10] - 2026-03-02
### Added
- Compatibility for WP Grid Builder

## [1.6.9] - 2026-02-12
### Added
- New option for setting the tax switch aria-label
- New filter: `wdevs_tax_switch_skip_next_price_wrap` to allow third-parties to skip the next price wrapping

## [1.6.8] - 2026-02-06
### Added
- Compatibility for Elementor Popup
- Compatibility for Price Based on Country for WooCommerce
### Updated
- Improved AJAX request handling (special thanks to [Peter-Jan Timmermans](https://www.indegymzaal.nl/))

## [1.6.7] - 2026-01-28
### Added
- Compatibility for WooCommerce Dynamic Pricing and Discount Rules

## [1.6.6] - 2026-01-22
### Updated
- Improved switch accessibility and mobile UX
- Tested WooCommerce 10.4.3

## [1.6.5] - 2025-12-22
### Updated
- Skip price wrapping in coupon min/max error messages

## [1.6.4] - 2025-12-11
### Added
- Compatibility for YITH WooCommerce Role Based Prices

## [1.6.3] - 2025-12-04
### Added
 - New style: `flat pill`
### Updated
- Tested WooCommerce 10.3.6
- Tested WordPress 6.9

## [1.6.2] - 2025-11-29
### Added
- New filter: `wdevs_tax_switch_current_product` to allow third-parties to set the correct product context
- Compatibility for WooCommerce Product Bundles
### Updated
- Hardened frontend initialization so switches/labels still render when scripts load after DOMContentLoaded (e.g. LiteSpeed Delay/Defer JS)

## [1.6.1] - 2025-11-26
### Added
- Compatibility for YayMail - WooCommerce Email Customizer
- Compatibility for PDF Invoices & Packing Slips for WooCommerce
### Updated
- Improved FacetWP compatibility
- Tested WooCommerce 10.3.5

## [1.6.0] - 2025-10-25
### Added
- Option to enable price switching in the mini cart
- Compatibility for FiboSearch - AJAX Search for WooCommerce (+ Pro)
- New filter: `wdevs_tax_switch_should_hide_on_current_page` for custom visibility control
- 'disabled' state
- Added FacetWP slider price switching
### Updated
- Improved AJAX compatibility. Price switching should be included in AJAX-loaded content
- Improved compatibility for WoodMart theme

## [1.5.19] - 2025-10-09
### Added
- PJAX compatibility for themes using jquery-pjax (e.g. WoodMart theme)

## [1.5.18] - 2025-10-08
### Updated
- Fixed incorrect tax calculation for variation products loaded via AJAX

## [1.5.17] - 2025-09-30
### Updated
- Tested WooCommerce 10.2.2
- Improved compatibility for Measurement Price Calculator for WooCommerce

## [1.5.16] - 2025-09-25
### Updated
- Improved compatibility for Measurement Price Calculator for WooCommerce

## [1.5.15] - 2025-09-18
### Updated
- Ignore zero tax price check if customer is vat exempt

## [1.5.14] - 2025-09-12
### Updated
- Fixed price calculation if tax is zero for the current customer (zero tax rate country)

## [1.5.13] - 2025-08-22
### Updated
- Fixed tax calculation if product price is zero

## [1.5.12] - 2025-08-22
### Added
- Compatibility for Extra Product Options & Add-Ons for WooCommerce
### Updated
- Tested WooCommerce 10.1.0

## [1.5.11] - 2025-07-29
### Added
- wp_kses_allowed_html filter to allow spans in certain contexts for price displays
- Compatibility for Entr theme [See this issue](https://github.com/Paulsky/tax-switch/issues/3)
### Updated
- Improved compatibility with GeneratePress

## [1.5.10] - 2025-07-29
### Added
- Compatibility for FiboFilters

## [1.5.9] - 2025-07-22
### Added
- Compatibility for Kapee theme (version ≥ 1.6.20)
- Compatibility for B2B Market
### Removed
- Load_plugin_textdomain() because it has been discouraged since WordPress version 4.6.
### Updated
- Tested WooCommerce 10.0.2

## [1.5.8] - 2025-07-04
### Added
- Compatibility for Advanced Woo Search (+ PRO)

## [1.5.7] - 2025-06-18
### Updated
- Tested WooCommerce 9.9.4
- Improved translations

## [1.5.6] - 2025-06-06
### Updated
- Improved compatibility for Tiered Pricing Table for WooCommerce

## [1.5.5] - 2025-06-05
### Added
- Compatibility for WooCommerce Product Add-Ons Ultimate

## [1.5.4] - 2025-05-26
### Added
- Introduced 'tree shake' for bundling all commonly shared JavaScript into a separate file as optimisation to prevent duplicated code
### Updated
- Improved compatibility for Variation Price Display Range for WooCommerce

## [1.5.3] - 2025-05-21
### Added
- Compatibility for Variation Swatches for WooCommerce
- Preview in shortcode generator
- JavaScript 'wdevs-tax-switch-appeared' event listener
- Compatibility for Variation Price Display Range for WooCommerce (1.3.20 and up)
- Added settings link
### Updated
- Switched from 'found_variation' to 'show_variation' WooCommerce event for UI updates reliability in variations form

## [1.5.2] - 2025-05-14
### Added
- Option to disable the components on WooCommerce pages or pages without prices
- Disable price changes in WooCommerce table total rows
- Fix for WooCommerce text tax labels
### Updated
- Possible breaking change: always disable components on the cart and checkout pages. Undo previous (1.5.1) opt-in function.

## [1.5.1] - 2025-05-10
### Added
- Option to hide the components on the cart and checkout pages

## [1.5.0] - 2025-04-26
### Added
- Gutenberg block/shortcode for showing text about the currently selected tax setting. [See this topic](https://wordpress.org/support/topic/shortcode-for-wdevs-tax-switch-label-text/)
- Compatibility for Flatsome theme
- Compatibility for FacetWP
### Updated
- Tested WooCommerce 9.8.2
- Possible breaking change: refactored the (block) code structure to support multiple blocks

## [1.4.3] - 2025-04-16
### Updated
- Tested WordPress 6.8.0
- Tested WooCommerce 9.8.1
- Security improvement: escaped dynamic class names in HTML output (reported by Peter Thaleikis).

## [1.4.2] - 2025-04-03
### Added
- Compatibility for WooCommerce Quantity Discounts, Rules & Swatches

## [1.4.1] - 2025-03-27
### Added
- Compatibility for Advanced Product Fields Pro for WooCommerce

## [1.4.0] - 2025-03-25
### Added
- Shortcode generator

## [1.3.2] - 2025-03-25
### Updated
- Translate shortcode switch labels. [See this topic](https://wordpress.org/support/topic/language-support-in-shortcode/#post-18379279)

## [1.3.1] - 2025-03-24
### Updated
- Tested with WooCommerce 9.7.1

## [1.3.0] - 2025-02-26
### Added
- 'buttons' switch type
- Swedish language (special thanks to Martin Hult)
- Color setting for label text
- JavaScript 'wdevs-tax-switch-changed' event

## [1.2.5] - 2025-02-11
### Updated
- Fixed tax calculation if customer is VAT exempt

## [1.2.4] - 2025-02-06
### Updated
- Always register the block/shortcode scripts and styles. Only enqueue them if the block or shortcode is executed
- Fixed price display issues in Tiered Pricing Table for WooCommerce when shop prices are set to display excluding VAT by default

## [1.2.3] - 2025-02-04
### Added
- Incompatibility list
### Updated
- Incompatibility with Unlimited Elements for Elementor (+ Pro)

## [1.2.2] - 2025-02-03
### Updated
- Compatibility with WooCommerce Product Table Lite / PRO

## [1.2.1] - 2024-11-14
### Added
- Compatibility for WPML

## [1.2.0] - 2024-11-07
### Added
- Disable the plugin in the WooCommerce mini cart widget

## [1.1.11] - 2024-11-06
### Added
- Compatibility for Product Add-Ons for WooCommerce

## [1.1.10] - 2024-11-05
### Added
- Compatibility for JetEngine Listing Grid 'infinity scroll'

## [1.1.9] - 2024-11-04
### Added
- Extra check for filtering backend and frontend AJAX requests (which adds compatibility for [PDF Invoices & Packing Slips for WooCommerce](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/)).
### Updated
- Fixed a bug where the admin request checks failed when Wordpress is installed in a subdirectory.

## [1.1.8] - 2024-10-25
### Added
- Compatibility for YITH WooCommerce Product Add-ons & Extra Options Premium

## [1.1.7] - 2024-10-24
### Added
- Compatibility for [YITH WooCommerce Product Add-Ons](https://wordpress.org/plugins/yith-woocommerce-product-add-ons/)

## [1.1.6] - 2024-10-22
### Updated
- Removed applying 'woocommerce_get_price_html' filter a second time to prevent nested duplications

## [1.1.5] - 2024-10-22
### Added
- Added extra checks for only executing the filters on the frontend

## [1.1.4] - 2024-10-19
### Updated
- Improved compatibility for Tiered Pricing Table for WooCommerce

## [1.1.3] - 2024-10-15
### Added
- Compatibility for [Discount Rules for WooCommerce](https://wordpress.org/plugins/woo-discount-rules/)
### Updated
- Migrated filters from only using 'woocommerce_get_price_html' to 'woocommerce_get_price_html' in combination with 'wc_price'. This way, the plugin should be more compatible with other plugins.
- Compatibility for Tiered Pricing Table for WooCommerce on catalog pages

## [1.1.2] - 2024-10-09
### Added
- Compatibility for Measurement Price Calculator for WooCommerce

## [1.1.1] - 2024-10-05
### Added
- Compatibility for Tiered Pricing Table for WooCommerce Premium

## [1.1.0] - 2024-10-04
### Added
- Compatibility for [Tiered Pricing Table for WooCommerce](https://wordpress.org/plugins/tier-pricing-table/) (Single product page)
- Compatibility for [WooCommerce Product Table](https://wordpress.org/plugins/wc-product-table-lite/)

## [1.0.1] - 2024-10-02
### Updated
- Fixed bug where JS was not loaded when element was rendered by shortcode.

## [1.0.0] - 2024-08-16
### Added
- Initial release of Wdevs Tax Switch plugin.
- Feature to display WooCommerce prices with or without VAT.
- Switch for customers to toggle between price displays.
- Support for WordPress blocks.
- Settings page in WooCommerce for customizing VAT texts.
- Multilingual support.
