import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class AdvancedProductFieldsForWoocommerce {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
		this.taxTexts = null;
	}

	init() {
		this.taxTexts = TaxSwitchElementBuilder.getTaxTexts();
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

				const taxFactor =
					productElement.data( 'taxFactor' ) ||
					productElement.data( 'tax' );
				if ( ! taxFactor || taxFactor <= 1 ) {
					return;
				}

				if (
					! window.WAPF ||
					! window.WAPF.Pricing ||
					! window.WAPF.Pricing.addTax
				) {
					return;
				}

				// Advanced Product Fields Extended for WooCommerce 3.2.1 updates
				// calc fields/dependencies after the first wapf/pricing event. The
				// event totals can be stale on page load until WAPF calculates again.
				if ( vm.maybeRecalculateInitialPricing( productElement ) ) {
					return;
				}

				const alternateTaxDisplay =
					vm.originalTaxDisplay === 'incl' ? 'excl' : 'incl';
				const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
					vm.originalTaxDisplay
				);

				const buildTotal = ( amount ) => {
					const originalPrice = window.WAPF.Pricing.addTax(
						amount,
						taxFactor,
						null,
						vm.originalTaxDisplay
					);
					const alternatePrice = window.WAPF.Pricing.addTax(
						amount,
						taxFactor,
						null,
						alternateTaxDisplay
					);

					return vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						vm.formatPrice( originalPrice ),
						vm.formatPrice( alternatePrice ),
						vm.taxTexts
					);
				};

				productElement
					.find( '.wapf-product-total' )
					.html( buildTotal( productTotal ) );

				productElement
					.find( '.wapf-options-total' )
					.html( buildTotal( optionsTotal ) );

				productElement
					.find( '.wapf-grand-total' )
					.html( buildTotal( grandTotal ) );
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

				const taxElement = document.querySelector(
					'.wapf-product-totals[data-tax-factor], .wapf-product-totals[data-tax]'
				);
				const taxFactor = taxElement
					? taxElement.dataset.taxFactor || taxElement.dataset.tax
					: 1;
				if ( ! taxFactor || taxFactor <= 1 ) {
					return hint;
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

	maybeRecalculateInitialPricing( productElement ) {
		if (
			productElement.data( 'wtsWapfInitialPricingRecalculated' ) ||
			! window.WAPF?.Pricing?.calculateAll
		) {
			return false;
		}

		productElement.data( 'wtsWapfInitialPricingRecalculated', true );

		setTimeout(
			() => window.WAPF.Pricing.calculateAll( productElement ),
			0
		);

		return true;
	}
}

export default AdvancedProductFieldsForWoocommerce;
