import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class AdvancedProductFieldsForWoocommerce {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		this.registerWooCommerceEvents();
		this.registerFilters();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( document ).on(
			'wapf/pricing',
			( e, productTotal, optionsTotal, grandTotal, productElement ) => {
				if ( ! productElement ) {
					return;
				}

				const taxFactor = productElement.data( 'taxFactor' );
				if ( ! taxFactor || taxFactor <= 1 ) {
					return;
				}

				const taxRateAsPercentage = ( taxFactor - 1 ) * 100;
				const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
					vm.originalTaxDisplay
				);

				jQuery( '.wapf-product-total' ).html(
					vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						vm.formatPrice( productTotal ),
						vm.formatPrice(
							TaxSwitchHelper.calculateAlternatePrice(
								productTotal,
								vm.originalTaxDisplay,
								taxRateAsPercentage
							)
						),
						null
					)
				);

				jQuery( '.wapf-options-total' ).html(
					vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						vm.formatPrice( optionsTotal ),
						vm.formatPrice(
							TaxSwitchHelper.calculateAlternatePrice(
								optionsTotal,
								vm.originalTaxDisplay,
								taxRateAsPercentage
							)
						),
						null
					)
				);

				jQuery( '.wapf-grand-total' ).html(
					vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						vm.formatPrice( grandTotal ),
						vm.formatPrice(
							TaxSwitchHelper.calculateAlternatePrice(
								grandTotal,
								vm.originalTaxDisplay,
								taxRateAsPercentage
							)
						),
						null
					)
				);
			}
		);
	}

	registerFilters() {
		const vm = this;

		if (
			typeof window.WAPF !== 'undefined' &&
			typeof window.WAPF.Filter !== 'undefined'
		) {
			window.WAPF.Filter.add( 'wapf/html/fxhint', function ( hint ) {
				if ( ! window.accounting ) {
					return hint;
				}

				const taxElement =
					document.querySelector( '[data-tax-factor]' );
				const taxFactor = taxElement ? taxElement.dataset.taxFactor : 1;
				if ( ! taxFactor || taxFactor <= 1 ) {
					return;
				}

				//brackets become negative 'replace bracketed values with negatives'
				const noBrackets = hint.replace( /[()]/g, '' );

				const unformatted = window.accounting.unformat(
					noBrackets,
					window.wapf_config?.display_options.decimal
				);

				const taxRateAsPercentage = ( taxFactor - 1 ) * 100;

				const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
					vm.originalTaxDisplay
				);

				const alternatePrice = vm.formatPrice(
					TaxSwitchHelper.calculateAlternatePrice(
						unformatted,
						vm.originalTaxDisplay,
						taxRateAsPercentage
					)
				);

				let alternateHint = window.WAPF.Filter.apply(
					'wapf/fx/hint',
					alternatePrice
				);

				alternateHint = window.wapf_config.hint.replace(
					'{x}',
					alternateHint
				);

				const newHint = vm.taxSwitchElementBuilder.build(
					displayIncludingTax,
					hint,
					alternateHint,
					null
				);

				return newHint;
			} );
		}
	}

	formatPrice( amount ) {
		if ( window.WAPF && window.WAPF.Util && window.WAPF.Util.formatMoney ) {
			return window.WAPF.Util.formatMoney(
				amount,
				window.wapf_config?.display_options
			);
		}
		return amount;
	}
}

export default AdvancedProductFieldsForWoocommerce;
