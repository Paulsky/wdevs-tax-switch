import jQuery from 'jquery';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';

class WoocommerceQuantityManager {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
		this.observer = null;
	}

	init() {
		this.registerWooCommerceEvents();
		this.registerObservers();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( document ).on(
			'found_variation',
			function ( event, variation ) {
				if ( variation ) {
					if ( variation.taxFactor ) {
						const taxRateAsPercentage =
							( variation.taxFactor - 1 ) * 100;
						vm.taxRate = taxRateAsPercentage;
					} else if ( variation.tax_rate ) {
						vm.taxRate = variation.tax_rate;
					}
				}
			}
		);
	}

	registerObservers() {
		const vm = this;
		const baseSelectors = [
			'[data-item="price"]',
			'[data-item="sale_price"]',
			'[data-item="total_price"]',
			'[data-item="total_sale_price"]',
		];

		const discountSelectors = vm.shouldUseDiscountSelectors()
			? [ '[data-item="discount"]', '[data-item="total_discount"]' ]
			: [];

		const allSelectors = [ ...baseSelectors, ...discountSelectors ];

		const callback = ( mutationsList, observer ) => {
			mutationsList.forEach( ( mutation ) => {
				if (
					mutation.type === 'childList' ||
					mutation.type === 'characterData'
				) {
					const targetElement = mutation.target;

					if (
						mutation.target.querySelector( '.wts-price-container' )
					) {
						return;
					}

					if ( targetElement.matches( allSelectors ) ) {
						vm.updatePriceElement( targetElement );
					} else {
						const parentMatch = targetElement.closest(
							allSelectors.join( ',' )
						);
						if ( parentMatch ) {
							vm.updatePriceElement( parentMatch );
						}
					}
				}
			} );
		};

		const config = { childList: true, subtree: true, characterData: false };

		vm.observer = new MutationObserver( callback );

		const tableWrappers = document.querySelectorAll(
			'.wqm-pricing-table-wrapper'
		);

		tableWrappers.forEach( ( wrapper ) => {
			vm.observer.observe( wrapper, config );
		} );
	}

	updatePriceElement( element ) {
		const vm = this;
		const $element = jQuery( element );
		const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
			vm.originalTaxDisplay
		);

		const processPrice = ( priceString ) => {
			const unformatted = window.accounting.unformat(
				priceString,
				window.wqm_config?.display_options.decimal
			);
			return vm.formatPrice(
				TaxSwitchHelper.calculateAlternatePrice(
					unformatted,
					vm.originalTaxDisplay,
					vm.taxRate
				)
			);
		};

		if ( $element.find( 'del' ).length ) {
			const originalPriceContent = $element.find( 'del' ).text().trim();
			const salePriceContent = $element.find( 'ins' ).text().trim();

			$element
				.find( 'del' )
				.html(
					vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						originalPriceContent,
						processPrice( originalPriceContent ),
						null
					)
				);

			$element
				.find( 'ins' )
				.html(
					vm.taxSwitchElementBuilder.build(
						displayIncludingTax,
						salePriceContent,
						processPrice( salePriceContent ),
						null
					)
				);
		} else {
			const originalPriceContent = $element.text().trim();
			$element.html(
				vm.taxSwitchElementBuilder.build(
					displayIncludingTax,
					originalPriceContent,
					processPrice( originalPriceContent ),
					null
				)
			);
		}
	}

	formatPrice( amount ) {
		if ( window.WAPF?.Util?.formatMoney ) {
			return window.WAPF.Util.formatMoney(
				amount,
				window.wapf_config?.display_options
			);
		}
		return amount;
	}

	shouldUseDiscountSelectors() {
		const wqmConfig = document.querySelector( '.wqm-config' );
		return wqmConfig?.dataset.percentOnTotal === '1';
	}

	destroy() {
		this.observer?.disconnect();
	}
}

export default WoocommerceQuantityManager;
