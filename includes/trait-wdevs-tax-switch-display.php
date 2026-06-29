<?php

/**
 * Handles all display-related functionality for the Tax Switch plugin.
 *
 * Provides methods for formatting and displaying prices with tax toggling functionality.
 * This includes combining price displays and wrapping prices with tax indicators.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 * @since      1.4.1
 */
trait Wdevs_Tax_Switch_Display {

	/**
	 * Combines both price displays (incl. and excl. tax) into a toggleable HTML structure
	 *
	 * @param string $current_price_text The formatted current price HTML
	 * @param string $alternate_price_text The formatted alternate price HTML
	 *
	 * @return string Combined HTML structure with both prices in toggleable spans
	 * @since 1.4.1
	 *
	 */
	public function combine_price_displays( $current_price_text, $alternate_price_text, $shop_prices_include_tax ) {

		$classes = [ 'wts-price-incl', 'wts-price-excl' ];
		if ( ! $shop_prices_include_tax ) {
			$classes = array_reverse( $classes );
		}

		return sprintf(
			'<span class="wts-price-wrapper"><span class="%s wts-active">%s</span><span class="%s wts-inactive">%s</span></span>',
			$classes[0],
			$current_price_text,
			$classes[1],
			$alternate_price_text
		);
	}

	/**
	 * Wraps price displays with tax indicator text in a toggleable structure
	 *
	 * @param string $price_html The original price HTML
	 * @param bool $shop_prices_include_tax Whether shop prices include tax
	 * @param string $vat_text Text for tax-included state
	 * @param string $alternate_vat_text Text for tax-excluded state
	 *
	 * @return string Price HTML wrapped with tax indicators
	 * @since 1.4.1
	 *
	 */
	public function wrap_price_displays( $price_html, $shop_prices_include_tax, $vat_text, $alternate_vat_text ) {
		$classes = [ 'wts-price-incl', 'wts-price-excl' ];
		if ( ! $shop_prices_include_tax ) {
			$classes = array_reverse( $classes );
		}

		return sprintf(
			'<span class="wts-price-container">%s <span class="wts-price-wrapper"><span class="%s wts-active"><span class="wts-vat-text">%s</span></span><span class="%s wts-inactive"><span class="wts-vat-text">%s</span></span></span></span>',
			$price_html,
			$classes[0],
			$vat_text,
			$classes[1],
			$alternate_vat_text
		);

	}
}
