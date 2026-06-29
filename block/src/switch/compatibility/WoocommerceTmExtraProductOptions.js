import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class WoocommerceTmExtraProductOptions {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		const vm = this;
		vm.registerTmEpoEvents();
		vm.registerWooCommerceEvents();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( document ).on(
			'found_variation',
			function ( event, variation ) {
				if ( variation && variation.tax_rate ) {
					vm.taxRate = variation.tax_rate;
				}
			}
		);
	}

	registerTmEpoEvents() {
		if (
			typeof jQuery === 'undefined' ||
			typeof jQuery.epoAPI === 'undefined' ||
			typeof jQuery.epoAPI.addFilter === 'undefined'
		) {
			return;
		}

		const vm = this;

		jQuery.epoAPI.addFilter(
			'tc_formatPrice',
			function ( formattedPrice, data, originalValue ) {
				return vm.handlePriceFormat(
					formattedPrice,
					data,
					originalValue
				);
			}
		);
	}

	handlePriceFormat( formattedPrice, data, originalValue ) {
		if ( ! originalValue || originalValue <= 0 || ! this.taxRate ) {
			return formattedPrice;
		}

		let price = originalValue;
		if ( typeof originalValue === 'string' ) {
			if ( typeof window.wcPriceToFloat === 'function' ) {
				price = window.wcPriceToFloat( originalValue );
			} else {
				// Fallback to manual parsing
				price = parseFloat( originalValue.replace( /[^0-9.-]+/g, '' ) );
			}

			if ( isNaN( price ) || price <= 0 ) {
				return formattedPrice;
			}
		}

		const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
			price,
			this.originalTaxDisplay,
			this.taxRate
		);

		let alternateFormattedPrice;
		if (
			jQuery.epoAPI &&
			jQuery.epoAPI.math &&
			typeof jQuery.epoAPI.math.format === 'function'
		) {
			alternateFormattedPrice = jQuery.epoAPI.math.format(
				alternatePrice,
				data
			);
		} else if ( typeof window.floatToWcPrice === 'function' ) {
			alternateFormattedPrice = window.floatToWcPrice( alternatePrice );
		} else {
			alternateFormattedPrice = alternatePrice.toFixed( 2 );
		}

		const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
			this.originalTaxDisplay
		);

		const template = this.taxSwitchElementBuilder.build(
			displayIncludingTax,
			formattedPrice,
			alternateFormattedPrice,
			null
		);

		TaxSwitchHelper.setPriceClasses( this.originalTaxDisplay );

		return template;
	}
}

export default WoocommerceTmExtraProductOptions;
