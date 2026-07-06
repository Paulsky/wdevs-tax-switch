import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';

class ThirdPartyCompatibility {
	static initialize( originalTaxDisplay ) {
		this.registerWooCommerceEvents( originalTaxDisplay );
		this.registerDynamicContentEvents();
	}

	static registerWooCommerceEvents( originalTaxDisplay ) {
		jQuery( '.variations_form' ).on( 'show_variation', function () {
			setTimeout( function () {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			}, 10 );
		} );

		jQuery( '.variations_form' ).on( 'reset_data', function () {
			setTimeout( function () {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			}, 10 );
		} );

		jQuery( document ).ajaxSuccess( function ( event, xhr, settings ) {
			if (
				settings &&
				settings.data &&
				typeof settings.data === 'string'
			) {
				const methods = [
					'get_variable_product_bulk_table', //Flycart Discount Rules for WooCommerce compatibility
				];

				const isMethodMatched = methods.some( ( method ) =>
					settings.data.includes( method )
				);

				if ( isMethodMatched ) {
					setTimeout( function () {
						TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
					}, 10 );
				}
			}
		} );

		const thirdPartyEvents = [
			'jet-engine/listing-grid/after-load-more', //JetEngine Listing Grid 'infinity scroll' compatibility
			'facetwp-loaded', //FacetWP compatibility
			'experimental-flatsome-pjax-request-done', //Flatsome theme compatibility
			'flatsome-relay-request-done', //Flatsome theme compatibility
			'flatsome-infiniteScroll-append', //Flatsome theme compatibility,
			'vpd_after_price_fadein', //Variation Price Display Range for WooCommerce
			'awsShowingResults', //Advanced Woo Search compatibility
			'wc_fragments_loaded', //WooCommerce cart fragments loaded from cache or cart response
			'wc_fragments_refreshed', //WooCommerce cart fragments refreshed via AJAX
			'pjax:success', //PJAX compatibility (WoodMart and other themes using jquery-pjax)
			'fibosearch/show-suggestions', //FiboSearch - AJAX Search for WooCommerce compatibility
			'wc_price_based_country_after_ajax_geolocation', // WooCommerce Price Based on Country compatibility
		];

		thirdPartyEvents.forEach( function ( eventName ) {
			jQuery( document ).on( eventName, function ( event, response ) {
				TaxSwitchHelper.setPriceClasses( originalTaxDisplay );
			} );
		} );
	}

	static registerDynamicContentEvents() {
		const dynamicContentEvents = [
			'elementor/popup/show', //Elementor popup compatibility
		];

		dynamicContentEvents.forEach( function ( eventName ) {
			document.addEventListener( eventName, function ( event ) {
				document.dispatchEvent(
					new CustomEvent( 'wdevs-tax-switch-appeared' )
				);
			} );
		} );
	}
}

export default ThirdPartyCompatibility;
