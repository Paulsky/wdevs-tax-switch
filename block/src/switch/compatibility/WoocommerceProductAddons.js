import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class WoocommerceProductAddOns {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		const vm = this;
		vm.registerWooCommerceEvents();
	}

	registerWooCommerceEvents() {
		// Check if accounting en woocommerce_addons_params are available
		//from woocommerce-product-addons/assets/js/frontend/addons.js
		if (
			typeof window.accounting === 'undefined' ||
			typeof window.accounting.unformat === 'undefined' ||
			typeof window.woocommerce_addons_params === 'undefined'
		) {
			return;
		}

		const vm = this;

		jQuery( 'form.cart' ).on( 'updated_addons', function () {
			const $amounts = jQuery( this ).find(
				'.product-addon-totals .amount'
			);
			const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
				this.originalTaxDisplay
			);
			let price,
				priceText,
				alternatePrice,
				alternatePriceText,
				template,
				$element;

			$amounts.each( function () {
				priceText = jQuery( this ).text();
				price = window.accounting.unformat(
					priceText,
					window.woocommerce_addons_params.currency_format_decimal_sep
				);
				if ( price > 0 ) {
					alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
						price,
						vm.originalTaxDisplay,
						vm.taxRate
					);
					alternatePriceText = window.accounting.formatMoney(
						alternatePrice,
						{
							symbol: window.woocommerce_addons_params
								.currency_format_symbol,
							decimal:
								window.woocommerce_addons_params
									.currency_format_decimal_sep,
							thousand:
								window.woocommerce_addons_params
									.currency_format_thousand_sep,
							precision:
								window.woocommerce_addons_params
									.currency_format_num_decimals,
							format: window.woocommerce_addons_params
								.currency_format,
						}
					);

					template = vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						priceText,
						alternatePriceText,
						null
					);
					$element = jQuery( this ).parent();
					$element.html( template );
				}
			} );

			TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
		} );
	}
}

export default WoocommerceProductAddOns;
