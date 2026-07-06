# Tax Switch for WooCommerce

Enhances WooCommerce by allowing users to toggle between displaying prices including or excluding tax. This plugin adds a customizable switch component and provides a flexible way to display both price versions.

> **See Tax Switch for WooCommerce in action:** [Try the live demo →](https://wordpress.org/plugins/tax-switch-for-woocommerce/?preview=1)

<br/>
<img src="https://github.com/user-attachments/assets/a76b1145-b4c2-4ff8-83c1-2d721caefaa7" width="300" alt="Tax Switch for WooCommerce demo" style="max-width: 300px !important; height: auto !important;" />
<br/>
<br/>

For more WordPress plugins, check out our products at [Wijnberg Developments](https://wijnberg.dev).

## Built with

- [WooCommerce](https://github.com/woocommerce/woocommerce)
- [WordPress](https://github.com/WordPress/WordPress)

## Requirements

- WooCommerce plugin installed and activated
- WordPress 5.0 or higher (for Gutenberg block support)
- WooCommerce tax calculations enabled:
	1. Go to WooCommerce > Settings > General
	2. Check the box for "Enable tax rates and calculations"
	3. Click "Save changes"
- WooCommerce taxes configured:
	1. Go to WooCommerce > Settings > Tax
	2. Set up your tax rates and rules as needed for your store

Without proper tax configuration in WooCommerce, the Tax Switch for WooCommerce plugin will not function as intended.

## Installation

To install the plugin, follow these steps:

1. Download the `.zip` file from the [releases page](https://github.com/Paulsky/tax-switch/releases/).
2. In your WordPress admin dashboard, go to `Plugins` > `Add New`.
3. Click `Upload Plugin` at the top of the page.
4. Click `Choose File`, select the `.zip` file you downloaded, then click `Install Now`.
5. After installation, click `Activate Plugin`.

The plugin is now ready for use.

## Getting started

These instructions will guide you through the installation and basic setup of the Tax Switch for WooCommerce plugin.

### Configuration

Configure the plugin settings below for proper functionality.

#### WooCommerce settings

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

#### Plugin settings

Configure these plugin-specific settings:

1. **Main settings**
	- Go to: *WooCommerce > Settings > Tax Switch*
	- Set your preferred text values
	- Optional: Generate a shortcode via *WooCommerce > Settings > Tax Switch > Shortcode*


### Usage

After configuration, you can add the tax switch to your pages in two ways:

1. Use the Gutenberg block "Tax Switch for WooCommerce" in your page or post editor.
2. Use the shortcode `[wdevs_tax_switch]` anywhere in your content.

The switch will toggle the display of prices including or excluding tax across your site.

### Shortcode Usage

#### Switch/buttons

The plugin provides a shortcode that allows you to easily add the tax switch anywhere on your site.

Basic usage:

[wdevs_tax_switch]

This will display the default tax switch.

The shortcode also accepts several attributes to customize its appearance:

- `class-name`: Adds custom CSS classes to the switch.
    - Default: `is-style-default`
    - Options: `is-style-default`, `is-style-inline`, `is-style-flat-pill`, custom classes
- `switch-type`: Determines the style of the toggle.
	- Default: `switch`
	- Options: `switch`, `buttons`
- `switch-color`: Sets the color of the switch handle.
- `switch-color-checked`: Sets the color of the switch when it's in the "on" position.
- `switch-background-color`: Sets the background color of the switch.
- `switch-background-color-checked`: Sets the background color of the switch when it's in the "on" position.
- `switch-text-color`: Sets the text color of the switch labels.
- `switch-label-incl`: Sets the text for the "including tax" label.
    - Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
- `switch-label-excl`: Sets the text for the "excluding tax" label.
    - Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.
- `switch-aria-label`: Sets the aria label of the switch.
	- Default: Uses the text set in the plugin settings or "Switch between prices including and excluding tax" if not set.

Example with custom attributes:

`[wdevs_tax_switch class-name="is-style-inline" switch-type="switch" switch-color="#ffffff" switch-color-checked="#000000" switch-background-color="#000000" switch-background-color-checked="#4CAF50" switch-text-color="#FF0000" switch-label-incl="Incl. VAT" switch-label-excl="Excl. VAT" switch-aria-label="Switch between prices including and excluding tax"]`

This will display an inline-style switch with a white handle that turns black when on, a black background when off, green background when on, and custom labels for including and excluding tax.

#### Label

Basic usage:
[wdevs_tax_switch_label]

Displays text indicating the currently selected tax setting. The text updates automatically when the tax switch is toggled.

Attributes:
- `class-name`: Adds custom CSS classes to the label.
	- Default: `is-style-default`
	- Options: `is-style-default` or custom classes
- `label-text-incl`: Sets the text to display when "including tax" is selected.
	- Default: Uses the text set in the plugin settings or "Incl. VAT" if not set.
- `label-text-excl`: Sets the text to display when "excluding tax" is selected.
	- Default: Uses the text set in the plugin settings or "Excl. VAT" if not set.
- `label-text-color`: Sets the "excluding tax" text color.
- `label-text-color-checked`: Sets the "including tax" text color.

Example with custom attributes:
`[wdevs_tax_switch_label class-name="tax-indicator" label-text-incl="Prices include tax" label-text-excl="Prices exclude tax" label-text-color="#FF0000" label-text-color-checked="#4CAF50"]`


#### PHP implementation

You can use these shortcodes with PHP with the do_shortcode() function:

```php
<?php echo do_shortcode('[wdevs_tax_switch]'); ?>
<?php echo do_shortcode('[wdevs_tax_switch_label]'); ?>
```

#### JavaScript API

Tax Switch provides a JavaScript API for custom integrations, such as buttons, preference popups, or other flows where visitors choose whether they want to see prices including or excluding tax.

```js
if ( window.wdevsTaxSwitch && ! window.wdevsTaxSwitch.hasSavedChoice() ) {
	window.wdevsTaxSwitch.setDisplay( 'incl', {
		respectExistingChoice: true,
	} );
}
```

API methods:
- `getDisplay()`: returns the current display, either `'incl'` or `'excl'`.
- `setDisplay( display, options )`: changes the display to `'incl'` or `'excl'`.
- `hasSavedChoice()`: returns whether the visitor already has a saved switch choice.
- `refresh()`: reapplies the current display.
- `toggle( options )`: toggles between inclusive and exclusive tax display.

Options:
- `respectExistingChoice`: `boolean` - preserves an existing saved visitor choice.

Set `respectExistingChoice` to `true` when you only want to apply a default if the visitor has not already made a saved choice.

You can also request a display change by dispatching an event. This is useful when your integration is event-driven.

```js
document.dispatchEvent(
	new CustomEvent( 'wdevs-tax-switch-context-changed', {
		detail: {
			display: 'incl',
			respectExistingChoice: true,
		},
	} )
);
```

Detail properties:
- `display`: `'incl' | 'excl' | 'toggle'` - requested tax display.
- `respectExistingChoice`: `boolean` - preserves an existing saved visitor choice.

#### JavaScript events

The switch fires a JavaScript event when the tax display changes. You can listen for this event to execute custom code when a user switches between inclusive and exclusive tax display. This is useful for when you need to perform additional actions based on the tax display state.

```js
document.addEventListener( 'wdevs-tax-switch-changed', function( event ) {
	console.log( event.detail );
} );
```

Detail properties:
- `isSwitched`: `boolean` - the raw switch state.
- `displayIncludingTax`: `boolean` - whether prices now display including tax.

### WPML

To translate the option texts via WPML:

1. Save your options first in: WooCommerce -> Settings -> Tax Switch
2. Then translate the texts in: WPML -> String Translations and search for your option values in the domain 'tax-switch-for-woocommerce'


## Compatibility

This plugin is tested and compatible with the following:

### Themes

- GeneratePress
- Blocksy
- Thrive
- Flatsome
- Kapee
- Entr
- Woodmart
- Hello Elementor

### Plugins

- WooCommerce Product Table Lite (+ PRO)
- Tiered Pricing Table for WooCommerce (+ Premium)
- Measurement Price Calculator for WooCommerce
- Discount Rules for WooCommerce
- YITH WooCommerce Product Add-Ons (+ & Extra Options Premium)
- JetEngine Listing Grid (Elementor)
- Product Add-Ons for WooCommerce
- B2BKing – Ultimate WooCommerce Wholesale and B2B Solution (+ Premium)
- Advanced Product Fields Pro/Extended for WooCommerce
- WooCommerce Quantity Discounts, Rules & Swatches
- FacetWP
- Variation Swatches for WooCommerce (+ PRO)
- Variation Price Display Range for WooCommerce (+ PRO)
- WooCommerce Product Add-Ons Ultimate
- Advanced Woo Search (+ PRO)
- B2B Market
- FiboFilters
- Extra Product Options & Add-Ons for WooCommerce
- FiboSearch – Ajax Search for WooCommerce (+ Pro)
- YayMail - WooCommerce Email Customizer
- PDF Invoices & Packing Slips for WooCommerce
- WooCommerce Product Bundles
- LiteSpeed Cache
- Elementor Pro
- YITH WooCommerce Role Based Prices
- WooCommerce Dynamic Pricing and Discount Rules
- Price Based on Country for WooCommerce
- WP Grid Builder

If you encounter any conflicts with other themes or plugins, please report them by opening an issue or through our website.

## Known Issues

### Compatibility with WooCommerce Blocks

Some WooCommerce Blocks are not fully compatible with this plugin as they do not use standard WooCommerce filters for price display. This is a known limitation of WooCommerce Blocks and not specific to this plugin. You can fix this by using WooCommerce shortcodes instead of the WooCommerce Blocks.

For more information and updates on this issue, please refer to the following GitHub issue:
[WooCommerce Blocks Issue #8972](https://github.com/woocommerce/woocommerce-blocks/issues/8972)

We are monitoring this issue and will update the plugin accordingly when a solution becomes available. In the meantime, the tax switch functionality may not work as expected with certain WooCommerce Blocks.

### Incompatibility

After multiple attempts to create compatibility functions, reaching out to the plugin developers several times, and still finding no viable solution, the following plugins remain incompatible:

- Unlimited Elements for Elementor (+ Pro): AJAX pagination and filtering issues

## Language support

Currently supported languages:
- English
- Dutch
- Swedish

If you would like to add support for a new language or improve existing translations, please let us know by opening an issue or contacting us through our website.

## Contributing

Your contributions are welcome! If you'd like to contribute to the project, feel free to fork the repository, make your changes, and submit a pull request.

## Development and deployment

To prepare your development work for submission, ensure you have `npm` installed and run `npm run build`. This command compiles the React components and prepares the plugin for deployment.

### Steps:

1. Ensure `npm` is installed.
2. Navigate to the project root.
3. Run `npm run build`.

The compiled files are now ready for use. Please ensure your changes adhere to the project's coding standards.

## Security

If you discover any security related issues, please email us instead of using the issue tracker.

## License

This plugin is licensed under the GNU General Public License v2 or later.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
