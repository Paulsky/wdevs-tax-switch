import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class TieredPriceTable {
	constructor( originalTaxDisplay ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.isInclTaxDisplay = originalTaxDisplay === 'incl';
		this.priceBackups = new Map(); // Store backups for multiple elements
		this.taxTexts = null;
	}

	init() {
		this.registerWooCommerceEvents();
		this.initializePriceBackups();
		this.registerThirdPartyEvents();
	}

	initializePriceBackups() {
		const vm = this;
		// Store original price HTML for all relevant price wrappers
		jQuery( '.tiered-pricing-dynamic-price-wrapper' ).each(
			( _, element ) => {
				const $element = jQuery( element );
				const productId = $element.data( 'product-id' );
				if ( ! vm.priceBackups.has( productId ) ) {
					vm.priceBackups.set( productId, $element.html() );
				}
			}
		);
	}

	registerWooCommerceEvents() {
		const vm = this;
		// Listen on document so updates from dynamically inserted wrappers also reach us.
		jQuery( document )
			.off( 'tiered_price_update.wtsTieredPriceTable' )
			.on( 'tiered_price_update.wtsTieredPriceTable', ( event, data ) => {
				vm.updateAllPrices( data );
			} );

		const mainElement = jQuery( '.tpt__tiered-pricing' ).first();
		//mainElement is always inserted if the plugin is active...
		//but do NOT have children if there are no prices
		if ( mainElement.children().length ) {
			// Handle variation resets
			jQuery( document )
				.off( 'reset_data.wtsTieredPriceTable' )
				.on( 'reset_data.wtsTieredPriceTable', ( e ) => {
					const $form = jQuery( e.target ).closest(
						'.variations_form'
					);
					if ( $form.length ) {
						const productId = $form.data( 'product_id' );
						vm.resetPrices( productId );
					}
				} );

			// Handle variation changes
			jQuery( document )
				.off( 'show_variation.wtsTieredPriceTable' )
				.on( 'show_variation.wtsTieredPriceTable', ( e, variation ) => {
					const productId = variation.variation_id;
					vm.resetPrices( productId );
				} );
		}
	}

	registerThirdPartyEvents() {
		if ( typeof window.fiboFilters === 'undefined' ) {
			return;
		}

		window.fiboFilters.hooks.addAction(
			'fiboFilters.renderer.product_placeholders_overwritten',
			'tiered-price-table',
			() => {
				if (
					typeof document.__tieredPricing === 'undefined' ||
					typeof document.__tieredPricing.initFunction !== 'function'
				) {
					return;
				}

				jQuery( '.tpt__tiered-pricing' ).each( ( _, wrapper ) => {
					if ( this.isTieredPricingWrapperInitialized( wrapper ) ) {
						return;
					}

					document.__tieredPricing.initFunction( wrapper );
					this.triggerSelectedVariation( wrapper );
				} );
			}
		);
	}

	isTieredPricingWrapperInitialized( wrapper ) {
		const instances = document.__tieredPricing?.activeInstances;

		if ( ! Array.isArray( instances ) ) {
			return false;
		}

		return instances.some(
			( instance ) => instance?.$wrapper?.get( 0 ) === wrapper
		);
	}

	triggerSelectedVariation( wrapper ) {
		const $product = jQuery( wrapper ).closest( '.product' );
		const $form = $product.find( '.variations_form' );
		const $variationWrap = $product.find( '.single_variation_wrap' );

		if ( ! $form.length || ! $variationWrap.length ) {
			return;
		}

		const variationId = parseInt( $form.find( '.variation_id' ).val() );

		if ( Number.isNaN( variationId ) || ! variationId ) {
			return;
		}

		const productVariations = $form.data( 'product_variations' );

		if ( Array.isArray( productVariations ) ) {
			const variation = productVariations.find(
				( item ) => parseInt( item.variation_id ) === variationId
			);

			if ( variation ) {
				$variationWrap.trigger( 'show_variation', [ variation ] );
				return;
			}
		}

		$form.trigger( 'check_variations' );
	}

	updateAllPrices( data ) {
		if ( ! data || ! data.__instance ) {
			return;
		}

		const vm = this;
		const displayIncludingTax = TaxSwitchHelper.displayIncludingTax(
			vm.originalTaxDisplay
		);
		const showOriginalPrice = vm.shouldShowOriginalPrice( data );

		const current = data.__instance.formatting.formatPrice( data.price );
		const alternate = data.__instance.formatting.formatPrice(
			vm.getAlternatePrice( data )
		);
		const original = showOriginalPrice
			? data.__instance.formatting.formatPrice(
					vm.getOriginalPrice( data, true )
			  )
			: null;
		const alternateOriginal = showOriginalPrice
			? data.__instance.formatting.formatPrice(
					vm.getOriginalPrice( data, false )
			  )
			: null;

		const $priceContainers = jQuery(
			'.tiered-pricing-dynamic-price-wrapper'
		).filter( ( _, container ) => {
			const $container = jQuery( container );
			const productId = parseInt( $container.data( 'product-id' ) );
			const parentId = parseInt( $container.data( 'parent-id' ) );

			return productId === parentId
				? parentId === data.parentId
				: productId === data.productId;
		} );

		$priceContainers.each( ( _, container ) => {
			const $container = jQuery( container );
			if ( $container.data( 'price-type' ) !== 'dynamic' ) {
				return;
			}
			$container.html(
				vm.getWtsHtml(
					displayIncludingTax,
					current,
					alternate,
					true,
					original,
					alternateOriginal
				)
			);
		} );

		vm.updateSummaryTable( data, displayIncludingTax );

		TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
	}

	shouldShowOriginalPrice( data ) {
		// Show original price if product is in sale
		if ( data.__instance.dataProvider.isProductOnSale() ) {
			return true;
		}

		return (
			data.__instance.dataProvider.showTieredPriceAsDiscount() &&
			data.pricing.tieredQuantity >
				data.__instance.dataProvider.getMinimum()
		);
	}

	getOriginalPrice( data, getOriginalTaxPrice = true ) {
		const alternatePrice = this.getAlternatePrice( data );
		if ( data.__instance.dataProvider.isProductOnSale() ) {
			return this.getOriginalRegularPrice( data, getOriginalTaxPrice );
		}
		return getOriginalTaxPrice
			? data.__instance.dataProvider.getOriginalPrice()
			: data.__instance.dataProvider.getOriginalPrice() *
					( alternatePrice / data.price );
	}

	getOriginalRegularPrice( data, getOriginalTaxPrice = true ) {
		const alternatePrice = this.getAlternatePrice( data );
		return getOriginalTaxPrice
			? data.__instance.dataProvider.getRegularPrice()
			: data.__instance.dataProvider.getRegularPrice() *
					( alternatePrice / data.price );
	}

	updateSummaryTable( data, displayIncludingTax ) {
		const vm = this;
		const summaryTable = vm.getSummaryTable( data.parentId );
		if ( ! summaryTable || ! summaryTable.length ) {
			return;
		}

		const alternatePrice = vm.getAlternatePrice( data );

		const productPriceHtml = vm.getWtsHtml(
			displayIncludingTax,
			data.__instance.formatting.formatPrice( data.price ),
			data.__instance.formatting.formatPrice( alternatePrice ),
			true
		);

		const totalHtml = vm.getWtsHtml(
			displayIncludingTax,
			data.__instance.formatting.formatPrice(
				data.price * data.quantity
			),
			data.__instance.formatting.formatPrice(
				alternatePrice * data.quantity
			),
			true
		);

		const totalWithTaxHtml = vm.getWtsHtml(
			displayIncludingTax,
			data.__instance.formatting.formatPrice(
				data.price * data.quantity
			),
			data.__instance.formatting.formatPrice(
				alternatePrice * data.quantity
			),
			true
		);

		const regularPrice = data.__instance.dataProvider.getRegularPrice();
		let oldPriceHtml = '';
		if ( data.price !== regularPrice ) {
			const alternateRegularPrice = vm.getOriginalRegularPrice(
				data,
				false
			);
			oldPriceHtml = vm.getWtsHtml(
				displayIncludingTax,
				data.__instance.formatting.formatPrice( regularPrice ),
				data.__instance.formatting.formatPrice( alternateRegularPrice ),
				false
			);
		}

		setTimeout( function () {
			summaryTable
				.find( '[data-tier-pricing-table-summary-product-price]' )
				.html( productPriceHtml );
			summaryTable
				.find( '[data-tier-pricing-table-summary-total]' )
				.html( totalHtml );
			summaryTable
				.find( '[data-tier-pricing-table-summary-total-with-tax]' )
				.html( totalWithTaxHtml );
			summaryTable
				.find( '[data-tier-pricing-table-summary-product-old-price]' )
				.html( oldPriceHtml );

			TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
		}, 10 );
	}

	getSummaryTable( productId ) {
		return productId
			? jQuery( '.tier-pricing-summary-table' ).filter(
					`[data-product-id=${ productId }]`
			  )
			: jQuery( '.tier-pricing-summary-table' );
	}

	resetPrices( productId ) {
		const vm = this;
		const backup = this.priceBackups.get( productId );
		if ( backup ) {
			jQuery( '.tiered-pricing-dynamic-price-wrapper' )
				.filter( `[data-product-id=${ productId }]` )
				.html( backup );
		}
		TaxSwitchHelper.setPriceClasses( vm.originalTaxDisplay );
	}

	getWtsHtml(
		displayIncludingTax,
		tieredTaxHTML,
		tieredAlternateTaxHTML,
		setText = false,
		originalPriceHTML = null,
		originalPriceAlternateHTML = null
	) {
		const vm = this;

		const getPriceHtml = ( currentPrice, originalPrice ) => {
			if ( originalPrice ) {
				return `<del>${ originalPrice }</del> <ins>${ currentPrice }</ins>`;
			}
			return currentPrice;
		};

		// If original tax display is exclusive, we need to switch the order of the prices
		const [ inclPrice, exclPrice ] = vm.isInclTaxDisplay
			? [ tieredTaxHTML, tieredAlternateTaxHTML ]
			: [ tieredAlternateTaxHTML, tieredTaxHTML ];

		const [ inclOriginalPrice, exclOriginalPrice ] = vm.isInclTaxDisplay
			? [ originalPriceHTML, originalPriceAlternateHTML ]
			: [ originalPriceAlternateHTML, originalPriceHTML ];

		const priceSection = `
        <span class="wts-price-wrapper">
            <span class="wts-price-incl ${
				displayIncludingTax ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml( inclPrice, inclOriginalPrice ) }
            </span>
            <span class="wts-price-excl ${
				! displayIncludingTax ? 'wts-active' : 'wts-inactive'
			}">
                ${ getPriceHtml( exclPrice, exclOriginalPrice ) }
            </span>
        </span>
    `;

		if ( setText ) {
			if ( ! vm.taxTexts ) {
				vm.taxTexts = TaxSwitchElementBuilder.getTaxTexts();
			}

			if ( vm.taxTexts ) {
				const textSection = TaxSwitchElementBuilder.getTaxTextElement(
					displayIncludingTax,
					vm.taxTexts.including,
					vm.taxTexts.excluding
				);

				return `
                <span class="wts-price-container">
                    ${ priceSection }
                    ${ textSection }
                </span>
            `;
			}
		}

		return `
        <span class="wts-price-container">
            ${ priceSection }
        </span>
    `;
	}

	getAlternatePrice( data ) {
		if ( this.isInclTaxDisplay ) {
			return data.pricing.price_excl_tax;
		}
		return data.pricing.price_incl_tax;
	}
}

export default TieredPriceTable;
