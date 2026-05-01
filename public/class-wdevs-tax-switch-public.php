<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/public
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Public {

	use Wdevs_Tax_Switch_Helper, Wdevs_Tax_Switch_Display;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Skip wrapping the next wc_price() call.
	 *
	 * @since 1.6.5
	 * @var bool
	 */
	private $skip_next_wc_price_wrap = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/wdevs-tax-switch-shared.css', array(), $this->version );
		wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/wdevs-tax-switch-public.css', array(), $this->version );
	}

	public function wrap_wc_price( $return, $price, $args, $unformatted_price, $original_price ) {

		if ( $this->is_in_cart_or_checkout() && ! $this->should_switch_in_mini_cart() ) {
			return $return;
		}

		if ( $this->is_mail_context() ) {
			return $return;
		}

		if ( $this->should_be_disabled_in_action() ) {
			return $return;
		}

		if ( $this->should_hide_on_current_page() ) {
			return $return;
		}

		if ( $this->is_file_context() ) {
			return $return;
		}

		$context = [
			'return'             => $return,
			'price'              => $price,
			'args'               => $args,
			'unformatted_price'  => $unformatted_price,
			'original_price'     => $original_price,
		];

		if ( $this->should_skip_next_price_wrap( $context ) ) {
			return $return;
		}

		//already wrapped?
//		if ( str_contains( $return, 'wts-price-wrapper' ) ) {
//			return $return;
//		}

		if ( empty( $unformatted_price ) ) {
			return $return;
		}

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX );

		$alternate_price = wc_price( $this->calculate_alternate_price( $unformatted_price ) );

		//Re-enable this filter and function
		add_filter( 'wc_price', [ $this, 'wrap_wc_price' ], PHP_INT_MAX, 5 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $return, $alternate_price, $shop_prices_include_tax );
	}

	/**
	 * Add 'excl. vat' or 'incl. vat' text
	 *
	 * @param $price_html
	 * @param $product
	 *
	 * @return mixed|string
	 */
	public function get_price_html( $price_html, $product ) {

		if ( empty( trim( $price_html ) ) ) {
			return $price_html;
		}

		if ( ! str_contains( $price_html, 'amount' ) ) {
			return $price_html;
		}

		if ( ! $this->is_woocommerce_product( $product ) ) {
			return $price_html;
		}

		if ( $this->should_hide_on_current_page() ) {
			return $price_html;
		}

		//Temporarily disable this filter and function to prevent infinite loop
		//Is this still needed?
		//remove_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN );

		//Execute all others filters
		//Causes duplications
		//$price_html = apply_filters( 'woocommerce_get_price_html', $price_html, $product );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Get VAT text options
		$vat_text           = $this->get_vat_text($shop_prices_include_tax);
		$alternate_vat_text = $this->get_alternate_vat_text($shop_prices_include_tax);

		//Re-enable this filter and function
		//Is this still needed?
		//add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], PHP_INT_MIN, 2 );

		// Combine both price displays into one HTML string
		$html = $this->wrap_price_displays( $price_html, $shop_prices_include_tax, $vat_text, $alternate_vat_text );

		return $html;
	}

	/**
	 * @since 1.5.2
	 */
	public function wrap_inc_label($text) {
		return $this->wrap_tax_label($text, 'inc');
	}

	/**
	 * @since 1.5.2
	 */
	public function wrap_ex_label($text) {
		return $this->wrap_tax_label($text, 'ex');
	}

	/**
	 * Helper function to handle both tax label cases
	 *
	 * @since 1.5.2
	 * @param string $text The label text
	 * @param string $type Either 'inc' or 'ex' for including/excluding tax
	 * @return string
	 */
	protected function wrap_tax_label($text, $type) {
		if (empty(trim($text))) {
			return $text;
		}

		// Temporarily disable the opposite filter to prevent infinite loop
		$opposite_filter = ($type === 'inc') ? 'woocommerce_countries_ex_tax_or_vat' : 'woocommerce_countries_inc_tax_or_vat';
		$opposite_callback = ($type === 'inc') ? 'wrap_ex_label' : 'wrap_inc_label';

		remove_filter($opposite_filter, [$this, $opposite_callback], PHP_INT_MAX);

		$opposite_label = ($type === 'inc')
			? WC()->countries->ex_tax_or_vat()
			: WC()->countries->inc_tax_or_vat();

		//Re-enable this filter and function
		add_filter($opposite_filter, [$this, $opposite_callback], PHP_INT_MAX, 1);

		return $this->combine_price_displays(
			$text,
			$opposite_label,
			($type === 'inc')
		);
	}


	/**
	 * @return bool
	 * @since 1.2.0
	 */
	private function should_be_disabled_in_action() {
		//compatibility with YITH WooCommerce Product Add Ons select
		if ( did_filter( 'yith_wapo_option_price' ) ) {
			return true;
		}

		//disable in the total table row. The total, is the total what the customer paid. So this is absolute.
		if ( did_filter( 'woocommerce_order_get_total' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Disable price wrapping in coupon error messages.
	 * Reason 1: mostly called from cart/checkout. Reason 2: HTML is stripped from these messages.
	 *
	 * @param mixed $amount Coupon amount.
	 * @return mixed
	 * @since 1.6.5
	 */
	public function is_coupon_error_message( $amount ) {
		$this->skip_next_wc_price_wrap = true;
		return $amount;
	}

	/**
	 * Check if the next wc_price() call should be skipped.
	 *
	 * @return bool
	 * @since 1.6.5
	 */
	public function should_skip_next_price_wrap( array $context = [] ) {
		$skip = $this->skip_next_wc_price_wrap;

		/**
		 * Allow third-party code to skip the next wc_price() call.
		 *
		 * @since 1.6.9
		 *
		 * @param bool                   $skip    Whether to bypass this filter.
		 * @param array                  $context Context passed from wrap_wc_price().
		 * @param Wdevs_Tax_Switch_Public $public  Current public class instance.
		 */
		$skip = (bool) apply_filters( 'wdevs_tax_switch_skip_next_price_wrap', $skip, $context, $this );

		if ( $skip ) {
			$this->skip_next_wc_price_wrap = false;
			return true;
		}

		return false;
	}

	/**
	 * TODO consider this to be implemented OR deleted
	 * This is for <option> elements with prices included.
	 * $this->loader->add_filter( 'woocommerce_dropdown_variation_attribute_options_html', $plugin_public, 'set_price_texts_for_variation_options', 10, 2 );
	 *
	 * @param string $html Current dropdown HTML.
	 * @param array  $args Arguments passed to the dropdown builder.
	 * @return string Modified or original HTML.
	 * @since 1.6.9
	 */
//	public function set_price_texts_for_variation_options( $html, $args ) {
//		if ( empty( $html ) ) {
//			return $html;
//		}
//
//		$price_html = wc_price( 10.01 );
//		$price_text = wp_strip_all_tags( $price_html );
//
//		$decimal_separator  = preg_quote( wc_get_price_decimal_separator(), '/' );
//		$thousand_separator = preg_quote( wc_get_price_thousand_separator(), '/' );
//
//		if ( '' === $thousand_separator ) {
//			$number_pattern = '\d+(?:' . $decimal_separator . '\d+)?';
//		} else {
//			$number_pattern = '\d{1,3}(?:' . $thousand_separator . '\d{3})*(?:' . $decimal_separator . '\d+)?';
//		}
//
//		$normalized_price_html = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $price_html, ENT_QUOTES, 'UTF-8' ) ) );
//		$number_matches_html  = [];
//		preg_match( '/\d+(?:[.,\s]\d+)*/u', $normalized_price_html, $number_matches_html, PREG_OFFSET_CAPTURE );
//		if ( empty( $number_matches_html ) ) {
//			$pattern_html = '/' . preg_quote( $normalized_price_html, '/' ) . '/u';
//		} else {
//			$start_html = $number_matches_html[0][1];
//			$end_html   = $start_html + strlen( $number_matches_html[0][0] );
//			$pattern_html = '/' . preg_quote( substr( $normalized_price_html, 0, $start_html ), '/' ) . '(' . $number_pattern . ')' . preg_quote( substr( $normalized_price_html, $end_html ), '/' ) . '/u';
//		}
//
//		$normalized_price_text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $price_text, ENT_QUOTES, 'UTF-8' ) ) );
//		$number_matches_text = [];
//		preg_match( '/\d+(?:[.,\s]\d+)*/u', $normalized_price_text, $number_matches_text, PREG_OFFSET_CAPTURE );
//		if ( empty( $number_matches_text ) ) {
//			$pattern_text = '/' . preg_quote( $normalized_price_text, '/' ) . '/u';
//		} else {
//			$start_text = $number_matches_text[0][1];
//			$end_text   = $start_text + strlen( $number_matches_text[0][0] );
//			$pattern_text = '/' . preg_quote( substr( $normalized_price_text, 0, $start_text ), '/' ) . '(' . $number_pattern . ')' . preg_quote( substr( $normalized_price_text, $end_text ), '/' ) . '/u';
//		}
//
//		$option_pattern = '/<option\b([^>]*)>(.*?)<\/option>/is';
//		if ( ! preg_match_all( $option_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
//			return $html;
//		}
//
//		$result    = '';
//		$last_pos  = 0;
//		foreach ( $matches as $match ) {
//			$full_match = $match[0][0];
//			$start      = $match[0][1];
//			$result    .= substr( $html, $last_pos, $start - $last_pos );
//
//			$attributes = $match[1][0];
//			$label_html = $match[2][0];
//
//			$normalized_option_html = trim( preg_replace( '/\s+/u', ' ', $label_html ) );
//			$plain_label            = html_entity_decode( strip_tags( $label_html ), ENT_QUOTES, 'UTF-8' );
//			$normalized_option_text = trim( preg_replace( '/\s+/u', ' ', $plain_label ) );
//
//			$price_matches = [];
//			if ( preg_match_all( $pattern_html, $normalized_option_html, $found_html, PREG_OFFSET_CAPTURE ) ) {
//				foreach ( $found_html[0] as $found ) {
//					$price_matches[] = [
//						'text'  => $found[0],
//						'start' => $found[1],
//						'end'   => $found[1] + strlen( $found[0] ),
//					];
//				}
//			}
//
//			if ( count( $price_matches ) < 2 && preg_match_all( $pattern_text, $normalized_option_text, $found_text, PREG_OFFSET_CAPTURE ) ) {
//				foreach ( $found_text[0] as $found ) {
//					$price_matches[] = [
//						'text'  => $found[0],
//						'start' => $found[1],
//						'end'   => $found[1] + strlen( $found[0] ),
//					];
//				}
//			}
//
//			if ( count( $price_matches ) < 2 ) {
//				$result .= $full_match;
//				$last_pos = $start + strlen( $full_match );
//				continue;
//			}
//
//			$unique = [];
//			foreach ( $price_matches as $price_match ) {
//				$key = $price_match['start'] . ':' . $price_match['end'];
//				if ( ! isset( $unique[ $key ] ) ) {
//					$unique[ $key ] = $price_match;
//				}
//			}
//
//			$price_matches = array_values( $unique );
//			$starts        = array_column( $price_matches, 'start' );
//			array_multisort( $starts, SORT_NUMERIC, $price_matches );
//
//			if ( count( $price_matches ) < 2 ) {
//				$result .= $full_match;
//				$last_pos = $start + strlen( $full_match );
//				continue;
//			}
//
//			$first  = $price_matches[0];
//			$second = $price_matches[1];
//
//			$between_first_and_second = substr( $normalized_option_text, $first['end'], $second['start'] - $first['end'] );
//
//			$primary_label   = substr( $normalized_option_text, 0, $second['start'] ) . substr( $normalized_option_text, $second['end'] );
//			$alternate_label = substr( $normalized_option_text, 0, $first['start'] ) . $second['text'] . $between_first_and_second . substr( $normalized_option_text, $second['end'] );
//
//			$primary_label   = trim( preg_replace( '/\s+/u', ' ', $primary_label ) );
//			$alternate_label = trim( preg_replace( '/\s+/u', ' ', $alternate_label ) );
//
//			if ( '' === $primary_label || '' === $alternate_label ) {
//				$result .= $full_match;
//				$last_pos = $start + strlen( $full_match );
//				continue;
//			}
//
//			$data_attributes = sprintf(
//				' data-vat-price-text="%s" data-vat-price-alternate-text="%s"',
//				esc_attr( $primary_label ),
//				esc_attr( $alternate_label )
//			);
//
//			$result .= sprintf(
//				'<option%s%s>%s</option>',
//				$attributes ? ' ' . trim( $attributes ) : '',
//				$data_attributes,
//				esc_html( $primary_label )
//			);
//
//			$last_pos = $start + strlen( $full_match );
//		}
//
//		$result .= substr( $html, $last_pos );
//
//		return $result;
//	}

}
