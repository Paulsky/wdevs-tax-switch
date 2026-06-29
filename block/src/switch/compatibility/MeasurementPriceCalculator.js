import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';
import jQuery from 'jquery';
import { select, subscribe } from '@wordpress/data';

class MeasurementPriceCalculator {
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.isSwitched = false;
		this.unsubscribe = null;
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.currentVariation = null;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
			this.originalTaxDisplay
		);
	}

	init() {
		const vm = this;

		vm.unsubscribe = subscribe( () => {
			const newIsSwitched = select(
				'wdevs-tax-switch/store'
			).getIsSwitched();

			if ( vm.isSwitched !== newIsSwitched ) {
				vm.isSwitched = newIsSwitched;
				vm.handleSwitchChange();
			}
		} );

		vm.registerWooCommerceEvents();
		vm.registerWCMeasurementEvents();
	}

	registerWooCommerceEvents() {
		const vm = this;
		jQuery( '.single_variation, .single_variation_wrap' ).bind(
			'show_variation',
			function ( event, variation ) {
				setTimeout( function () {
					vm.currentVariation = variation;

					if ( variation && variation.tax_rate ) {
						vm.taxRate = variation.tax_rate;
					}
					if ( window.wc_price_calculator_params ) {
						const currentPrice = vm.getCurrentPrice();
						//const minimumPrice = parseFloat( variation.minimum_price );

						window.wc_price_calculator_params.product_price =
							currentPrice; // set the current variation product price
						//window.wc_price_calculator_params.minimum_price = minimumPrice;

						variation.price = currentPrice;

						jQuery( '.qty' ).trigger( 'change' );
					}
				}, 500 );
			}
		);
	}

	handleSwitchChange() {
		if ( window.wc_price_calculator_params ) {
			const currentPrice = this.getCurrentPrice();
			if ( currentPrice ) {
				window.wc_price_calculator_params.product_price = currentPrice;
				jQuery( '.qty' ).trigger( 'change' );
			}
		}
	}

	registerWCMeasurementEvents() {
		const vm = this;

		jQuery( document ).on(
			'wc-measurement-price-calculator-total-price-change',
			function ( event, quantity, productPrice ) {
				if ( quantity && productPrice ) {
					const totalPrice = quantity * productPrice;
					vm.handleTotalPriceUpdate( totalPrice );
				}
			}
		);

		jQuery( document ).on(
			'wc-measurement-price-calculator-quantity-total-price-change',
			function ( event, quantity, productPrice ) {
				if ( quantity && productPrice ) {
					const totalPrice = quantity * productPrice;
					vm.handleTotalPriceUpdate( totalPrice );
				}
			}
		);
	}

	handleTotalPriceUpdate( totalPrice ) {
		if ( ! totalPrice || ! this.taxRate ) {
			return;
		}

		const alternatePrice = TaxSwitchHelper.calculateAlternatePrice(
			totalPrice,
			this.originalTaxDisplay,
			this.taxRate
		);

		const formattedOriginal = this.woocommerce_price( totalPrice );
		const formattedAlternate = this.woocommerce_price( alternatePrice );

		this.replaceTotalPriceDisplay( formattedOriginal, formattedAlternate );
	}

	replaceTotalPriceDisplay( originalPrice, alternatePrice ) {
		const $totalPriceElement = jQuery( '.total_price' );
		if ( $totalPriceElement.length ) {
			const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
				this.originalTaxDisplay
			);

			const template = this.taxSwitchElementBuilder.build(
				displayIncludingTax,
				originalPrice,
				alternatePrice
			);

			$totalPriceElement.html( template );
		}
	}

	getCurrentPrice() {
		if (
			this.currentVariation &&
			this.currentVariation.price_incl_tax &&
			this.currentVariation.price_excl_tax
		) {
			// const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
			// 	this.originalTaxDisplay
			// );
			// if ( displayIncludingTax ) {
			if ( this.originalTaxDisplay === 'incl' ) {
				return parseFloat( this.currentVariation.price_incl_tax );
			}

			return parseFloat( this.currentVariation.price_excl_tax );
		}

		return null;
	}

	cleanup() {
		if ( this.unsubscribe ) {
			this.unsubscribe();
		}
	}

	/**
	 * Price formatting functions duplicated from wc-measurement-price-calculator.js
	 * These functions are not globally available, so we duplicate them for compatibility
	 */

	/**
	 * Returns the price formatted according to the WooCommerce settings
	 * Duplicated from wc-measurement-price-calculator.js (lines 784-814)
	 */
	woocommerce_price( price ) {
		let formatted_price = '';

		const num_decimals =
			window.wc_price_calculator_params.woocommerce_price_num_decimals;
		const currency_pos =
			window.wc_price_calculator_params.woocommerce_currency_pos;
		const currency_symbol =
			window.wc_price_calculator_params.woocommerce_currency_symbol;

		price = this.number_format(
			price,
			num_decimals,
			window.wc_price_calculator_params.woocommerce_price_decimal_sep,
			window.wc_price_calculator_params.woocommerce_price_thousand_sep
		);

		if (
			'yes' ===
				window.wc_price_calculator_params
					.woocommerce_price_trim_zeros &&
			num_decimals > 0
		) {
			price = this.woocommerce_trim_zeros( price );
		}

		switch ( currency_pos ) {
			case 'left':
				formatted_price =
					'<span class="amount">' +
					currency_symbol +
					price +
					'</span>';
				break;
			case 'right':
				formatted_price =
					'<span class="amount">' +
					price +
					currency_symbol +
					'</span>';
				break;
			case 'left_space':
				formatted_price =
					'<span class="amount">' +
					currency_symbol +
					'&nbsp;' +
					price +
					'</span>';
				break;
			case 'right_space':
				formatted_price =
					'<span class="amount">' +
					price +
					'&nbsp;' +
					currency_symbol +
					'</span>';
				break;
		}

		return formatted_price;
	}

	/**
	 * Trim trailing zeros off prices
	 * Duplicated from wc-measurement-price-calculator.js (lines 820-822)
	 */
	woocommerce_trim_zeros( price ) {
		return price.replace(
			new RegExp(
				this.preg_quote(
					window.wc_price_calculator_params
						.woocommerce_price_decimal_sep,
					'/'
				) + '0+$'
			),
			''
		);
	}

	/**
	 * Number formatting function
	 * Duplicated from wc-measurement-price-calculator.js (lines 611-634)
	 */
	number_format( number, decimals, dec_point, thousands_sep ) {
		// Strip all characters but numerical ones.
		number = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );

		const n = ! isFinite( +number ) ? 0 : +number;
		const prec = ! isFinite( +decimals ) ? 0 : Math.abs( decimals );
		const sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep;
		const dec = typeof dec_point === 'undefined' ? '.' : dec_point;
		let s = '';

		// Note: BigNumber is available in the measurement calculator context
		s = new BigNumber( n ).toFixed( prec ).split( '.' );

		if ( s[ 0 ].length > 3 ) {
			s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
		}

		if ( ( s[ 1 ] || '' ).length < prec ) {
			s[ 1 ] = s[ 1 ] || '';
			s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
		}

		return s.join( dec );
	}

	/**
	 * Regex quote function
	 * Duplicated from wc-measurement-price-calculator.js (lines 640-642)
	 */
	preg_quote( str, delimiter ) {
		return ( str + '' ).replace(
			new RegExp(
				'[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\\\' +
					( delimiter || '' ) +
					'-]',
				'g'
			),
			'\\$&'
		);
	}
}

export default MeasurementPriceCalculator;
