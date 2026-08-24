import { getIsSwitched } from './store';

class TaxSwitchHelper {
	static togglePriceClasses( originalTaxDisplay, isSwitched ) {
		const displayIncludingTax = this.displayIncludingTax(
			originalTaxDisplay,
			isSwitched
		);
		const elements = document.querySelectorAll( '.wts-price-wrapper' );

		elements.forEach( ( element ) => {
			const inclElement = element.querySelector(
				':scope > .wts-price-incl'
			);
			const exclElement = element.querySelector(
				':scope > .wts-price-excl'
			);

			if ( ! inclElement || ! exclElement ) {
				return;
			}

			if ( displayIncludingTax ) {
				inclElement.classList.remove( 'wts-inactive' );
				inclElement.classList.add( 'wts-active' );
				exclElement.classList.remove( 'wts-active' );
				exclElement.classList.add( 'wts-inactive' );
			} else {
				inclElement.classList.remove( 'wts-active' );
				inclElement.classList.add( 'wts-inactive' );
				exclElement.classList.remove( 'wts-inactive' );
				exclElement.classList.add( 'wts-active' );
			}
		} );
	}

	static displayIncludingTax( originalTaxDisplay, isSwitched ) {
		if ( isSwitched === null || isSwitched === undefined ) {
			isSwitched = getIsSwitched();
		}
		return (
			( originalTaxDisplay === 'incl' && ! isSwitched ) ||
			( originalTaxDisplay === 'excl' && isSwitched )
		);
	}

	/**
	 * @deprecated Since 1.8.0. Use displayIncludingTax().
	 * @param {...*} args Arguments passed to displayIncludingTax().
	 * @return {boolean} Whether prices display including tax.
	 */
	static displayIncludingVat( ...args ) {
		return this.displayIncludingTax( ...args );
	}

	static parseBooleanValue( value ) {
		if ( value ) {
			return JSON.parse( value );
		}
		return false;
	}

	static setPriceClasses( originalTaxDisplay ) {
		return this.togglePriceClasses( originalTaxDisplay, getIsSwitched() );
	}

	static calculateAlternatePrice( price, originalTaxDisplay, taxRate ) {
		// Guard clauses
		if ( ! price || price <= 0 || ! taxRate ) {
			return price;
		}

		const displayIncludingTax = originalTaxDisplay === 'incl';
		const taxMultiplier = 1 + taxRate / 100;

		let alternatePrice;
		if ( displayIncludingTax ) {
			alternatePrice = price / taxMultiplier;
		} else {
			alternatePrice = price * taxMultiplier;
		}

		return Number( alternatePrice.toFixed( 2 ) );
	}

	static calculateOriginalPrice(
		alternatePrice,
		originalTaxDisplay,
		taxRate
	) {
		let alternateTaxDisplay = 'excl';
		if ( originalTaxDisplay === 'excl' ) {
			alternateTaxDisplay = 'incl';
		}
		return this.calculateAlternatePrice(
			alternatePrice,
			alternateTaxDisplay,
			taxRate
		);
	}
}

export default TaxSwitchHelper;
