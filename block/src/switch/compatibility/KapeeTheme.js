import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class KapeeTheme {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		const vm = this;
		vm.taxTexts = TaxSwitchElementBuilder.getTaxTexts();
		vm.extendOriginalFunctions();
	}

	extendOriginalFunctions() {
		if ( typeof window.kapeePublic.kapee_formated_price !== 'function' ) {
			return;
		}

		const vm = this;
		const originalKapeeFormatedPrice =
			window.kapeePublic.kapee_formated_price;

		window.kapeePublic.kapee_formated_price = function (
			number,
			thousand_sep,
			decimal_sep,
			tofixed,
			symbol,
			woo_price_format
		) {
			const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
				vm.originalTaxDisplay
			);

			const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
				number,
				vm.originalTaxDisplay,
				vm.taxRate
			);

			const originalPriceDisplay = originalKapeeFormatedPrice.apply( vm, [
				number,
				thousand_sep,
				decimal_sep,
				tofixed,
				symbol,
				woo_price_format,
			] );

			const alternatePriceDisplay = originalKapeeFormatedPrice.apply(
				vm,
				[
					alternatePrice,
					thousand_sep,
					decimal_sep,
					tofixed,
					symbol,
					woo_price_format,
				]
			);

			return vm.taxSwitchElementBuilder.build(
				displayIncludingTax,
				originalPriceDisplay,
				alternatePriceDisplay,
				vm.taxTexts
			);
		}.bind( this );
	}
}

export default KapeeTheme;
