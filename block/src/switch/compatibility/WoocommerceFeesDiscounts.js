import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class WoocommerceFeesDiscounts {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
		this.taxTexts = null;
	}

	init() {
		const vm = this;
		vm.taxTexts = TaxSwitchElementBuilder.getTaxTexts();
		vm.extendOriginalFunction();
		vm.registerWoocommerceEvents();
	}

	extendOriginalFunction() {
		if ( typeof window.wcfad_wc_price !== 'function' ) {
			return;
		}

		const vm = this;
		const originalWcfadPrice = window.wcfad_wc_price;

		window.wcfad_wc_price = function ( price, price_only = false ) {
			if ( price_only ) {
				return originalWcfadPrice.apply( this, [ price, price_only ] );
			}

			const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
				vm.originalTaxDisplay
			);

			const priceValue = Number( price );
			const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
				priceValue,
				vm.originalTaxDisplay,
				vm.taxRate
			);

			const originalPriceDisplay = originalWcfadPrice.apply( this, [
				price,
				price_only,
			] );

			const alternatePriceDisplay = originalWcfadPrice.apply( this, [
				alternatePrice,
				price_only,
			] );

			return vm.taxSwitchElementBuilder.build(
				displayIncludingTax,
				originalPriceDisplay,
				alternatePriceDisplay,
				vm.taxTexts
			);
		}.bind( this );
	}

	registerWoocommerceEvents() {
		const vm = this;

		jQuery( 'body' ).on( 'pewc_do_percentages', function () {
			vm.setMainPriceTaxTexts();
		} );

		jQuery( document ).ready( function () {
			vm.setMainPriceTaxTexts();
		} );
	}

	//TODO; this is duplicated, maybe move to TaxSwitchElementBuilder or TaxSwitchHelper
	getTaxTextElement() {
		const vm = this;
		if ( ! vm.taxTexts ) {
			vm.taxTexts = TaxSwitchElementBuilder.getTaxTexts();
		}
		if ( vm.taxTexts && vm.taxTexts.including && vm.taxTexts.excluding ) {
			const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
				vm.originalTaxDisplay
			);

			const taxTextElement = TaxSwitchElementBuilder.getTaxTextElement(
				displayIncludingTax,
				vm.taxTexts.including,
				vm.taxTexts.excluding
			);

			return taxTextElement;
		}
		return null;
	}

	setMainPriceTaxTexts() {
		const vm = this;
		const mainPrice = jQuery( '.wcfad-main-price' );

		if ( ! mainPrice.length ) {
			return;
		}

		let priceContainer = mainPrice.children( '.wts-price-container' );

		if ( ! priceContainer.length ) {
			const directWrapper = mainPrice.children( '.wts-price-wrapper' );

			if ( directWrapper.length ) {
				mainPrice
					.children()
					.wrapAll( '<div class="wts-price-container"></div>' );
				priceContainer = mainPrice.children( '.wts-price-container' );
			}
		}

		if (
			priceContainer.length &&
			priceContainer.find( '.wts-price-wrapper' ).length === 1 //TODO: this is false/positive when there is a from - to price
		) {
			const taxTextElement = vm.getTaxTextElement();

			if ( taxTextElement ) {
				priceContainer.append( taxTextElement );
			}

			TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
		}
	}
}

export default WoocommerceFeesDiscounts;
