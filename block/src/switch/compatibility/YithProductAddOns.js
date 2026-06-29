import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class YithProductAddOns {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.initialPriceElement = null;

		//as soon as possible, because Yith will override this really soon
		if (
			window.yith_wapo &&
			window.yith_wapo.replace_product_price_class
		) {
			this.initialPriceElement = jQuery(
				window.yith_wapo.replace_product_price_class
			).clone();
		}
		this.taxTexts = null;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		const vm = this;
		vm.registerWooCommerceEvents();
	}

	registerWooCommerceEvents() {
		const vm = this;
		setTimeout( function () {
			const $container = jQuery( '#yith-wapo-container' );
			//$container is always inserted by Yith, also the form inputs
			//check if there is a div, meaning there are probably options for this product
			//there for, the timeout is needed
			if (
				$container.length &&
				$container.children( 'div' ).length > 0
			) {
				// Listen for price calculations
				jQuery( document ).on(
					'wapo-after-calculate-product-price',
					( event, response ) => {
						vm.handlePriceUpdate();

						// Handle the product price replacement
						if ( response && response.order_price_suffix ) {
							vm.handleProductPriceReplacement(
								response.order_price_suffix
							);
						}
					}
				);

				jQuery( document ).on(
					'found_variation',
					function ( event, variation ) {
						if ( variation && variation.tax_rate ) {
							vm.taxRate = variation.tax_rate;
						}
					}
				);

				// Handle initial price calculation and replacement for simple products
				if ( ! jQuery( 'form.variations_form' ).length ) {
					const priceElementAfterYithUpdate = jQuery(
						window.yith_wapo.replace_product_price_class
					);
					if (
						priceElementAfterYithUpdate &&
						priceElementAfterYithUpdate.length
					) {
						const priceHtml =
							priceElementAfterYithUpdate
								.find( '.woocommerce-Price-amount bdi' )
								.html() ||
							priceElementAfterYithUpdate
								.find( '.woocommerce-Price-amount' )
								.html() ||
							priceElementAfterYithUpdate.html();

						if ( priceHtml ) {
							const price = window.wcPriceToFloat( priceHtml );
							if ( ! isNaN( price ) ) {
								const formattedPrice =
									window.floatToWcPrice( price );
								vm.handleProductPriceReplacement(
									formattedPrice
								);
							}
						}
					}
				}
				vm.handlePriceUpdate();
			}
		}, 10 );
	}

	handleProductPriceReplacement( totalOrderPriceHtml ) {
		// Check if the YITH WAPO settings allow price replacement
		if (
			window.yith_wapo &&
			window.yith_wapo.replace_product_price === 'yes' &&
			window.yith_wapo.replace_product_price_class
		) {
			const $priceElement = jQuery(
				window.yith_wapo.replace_product_price_class
			);

			if ( $priceElement.length > 0 ) {
				const price = window.wcPriceToFloat( totalOrderPriceHtml );

				if ( ! isNaN( parseFloat( price ) ) ) {
					// Calculate alternate price
					const alternatePrice =
						TaxSwitchHelper.calculateAlternatePrice(
							price,
							this.originalTaxDisplay,
							this.taxRate
						);

					// Format prices
					const formattedOriginal = window.floatToWcPrice( price );
					const formattedAlternate =
						window.floatToWcPrice( alternatePrice );

					// Replace the price display
					this.replacePriceDisplay(
						$priceElement,
						formattedOriginal,
						formattedAlternate,
						true
					);
				}
			}
		}
	}

	handlePriceUpdate() {
		const elements = {
			productPrice: jQuery( '#wapo-total-product-price' ),
			options: jQuery( '#wapo-total-options-price' ),
			order: jQuery( '#wapo-total-order-price' ),
		};

		Object.entries( elements ).forEach( ( [ key, $element ] ) => {
			if ( $element.length ) {
				this.updatePriceElement( $element );
			}
		} );

		TaxSwitchHelper.setPriceClasses( this.originalTaxDisplay );
	}

	updatePriceElement( $element ) {
		const originalHtml = $element.html();
		if ( ! originalHtml ) {
			return;
		}

		const price = window.wcPriceToFloat( originalHtml );
		if ( ! price ) {
			return;
		}

		// Calculate alternate price
		const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
			price,
			this.originalTaxDisplay,
			this.taxRate
		);

		// Format prices using YITH WAPO's formatting function
		const formattedOriginal = window.floatToWcPrice( price );
		const formattedAlternate = window.floatToWcPrice( alternatePrice );

		this.replacePriceDisplay(
			$element,
			formattedOriginal,
			formattedAlternate
		);
	}

	replacePriceDisplay(
		$element,
		originalPrice,
		alternatePrice,
		setText = false
	) {
		const vm = this;
		let texts = null;
		if ( setText ) {
			if ( ! vm.taxTexts ) {
				vm.taxTexts = TaxSwitchElementBuilder.getTaxTexts(
					vm.initialPriceElement
				);
			}

			texts = vm.taxTexts;
		}
		const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
			this.originalTaxDisplay
		);
		const template = vm.taxSwitchElementBuilder.build(
			displayIncludingTax,
			originalPrice,
			alternatePrice,
			texts
		);

		$element.html( template );
	}
}

export default YithProductAddOns;
