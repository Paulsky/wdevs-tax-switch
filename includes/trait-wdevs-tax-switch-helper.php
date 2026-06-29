<?php

/**
 * The helper functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The helper functionality of the plugin.
 *
 * Defines helper methods for retrieving switch status,
 * checking shop display settings, and getting option text.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Wdevs_Tax_Switch_Helper {

	public function shop_prices_include_tax() {
		return $this->get_original_tax_display() === 'incl';
	}

	public function get_original_tax_display() {
		$current_value = get_option( 'woocommerce_tax_display_shop' );

		return $current_value;
	}

	public function get_option_text( $key, $default ) {
		$text = get_option( $key, $default );

		// Check if WPML is active
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$text = apply_filters( 'wpml_translate_single_string', $text, 'tax-switch-for-woocommerce', $key );
		}

		return esc_html( $text );
	}

	public function is_woocommerce_product( $product ) {
		if ( ! isset( $product ) ) {
			return false;
		}

		if ( ! ( $product instanceof WC_Product ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $product
	 *
	 * @return float|int
	 * @since 1.1.7
	 */
	public function get_product_tax_rate( $product ) {
		if ( ! $product ) {
			return 0;
		}

		$price_excl_tax = wc_get_price_excluding_tax( $product );

		// Prevent division by zero - use fallback for 0.00 products
		if ( $price_excl_tax <= 0 ) {
			return $this->get_fallback_tax_rate( $product );
		}

		$price_incl_tax = wc_get_price_including_tax( $product );

		$tax_rate = ( ( $price_incl_tax - $price_excl_tax ) / $price_excl_tax ) * 100;

		return $tax_rate;
		//return round($tax_rate, 2);
	}

	/**
	 * Get fallback tax rate for products with €0.00 price
	 * Creates a temporary product with €1.00 price to calculate tax rate
	 *
	 * @param WC_Product $product
	 *
	 * @return float|int
	 * @since 1.5.13
	 */
	public function get_fallback_tax_rate( $product ) {
		if ( ! $product ) {
			return 0;
		}

		// Create a temporary product clone
		$calculator = clone $product;

		// Set price to 1.00 to avoid division by zero
		$calculator->set_price( 1.00 );

		$price_excl_tax = wc_get_price_excluding_tax( $calculator );

		// Prevent division by zero (should not happen with 1.00 but safety first)
		if ( $price_excl_tax <= 0 ) {
			return 0;
		}

		$price_incl_tax = wc_get_price_including_tax( $calculator );

		$tax_rate = ( ( $price_incl_tax - $price_excl_tax ) / $price_excl_tax ) * 100;

		return $tax_rate;
	}

	/**
	 * Estimate the tax rate without a specific product context
	 * Creates a temporary product with the given tax class to calculate the applicable tax rate
	 * Uses customer location if available, otherwise falls back to shop base location
	 *
	 * @param string $tax_class Tax class (empty string for standard rate)
	 *
	 * @return float Tax rate as percentage (e.g., 21.0 for 21%)
	 * @since 1.6.0
	 */
	public function estimate_tax_rate( $tax_class = 'standard' ) {
		$calculator = new WC_Product_Simple();
		$calculator->set_price( 100 );

		if ( ! empty( $tax_class ) ) {
			$calculator->set_tax_class( $tax_class );
		}

		return $this->get_product_tax_rate( $calculator );
	}

	public function calculate_alternate_price( $price, $product = null ) {
		$prices_include_tax      = wc_prices_include_tax();
		$shop_prices_include_tax = $this->shop_prices_include_tax();
		$is_vat_exempt           = ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt();

		$calculator = new WC_Product_Simple();
		$calculator->set_price( $price );

		$pricesIncludeTaxFilter = false;

		if ( ! $product ) {
			$product = $this->get_current_product();
		}

		if ( $product instanceof WC_Product ) {
			$calculator->set_tax_class( $product->get_tax_class() );
			$calculator->set_tax_status( $product->get_tax_status() );
		} else {
			$calculator->set_tax_status( 'taxable' );
		}

		//Is not vat exempt (B2B customer)
		if ( ! $is_vat_exempt ) {
			//If the original price doesn't have any tax. For example; country with 0 tax.
			$tax_rate = $this->get_product_tax_rate( $calculator );
			if ( $tax_rate <= 0 ) {
				return $price;
			}
		}

		if ( $is_vat_exempt ) {
			WC()->customer->set_is_vat_exempt( false );
			$pre_option_woocommerce_tax_display_shop_filter = 'get_incl_option';
		} else {
			if ( $shop_prices_include_tax ) {
				$pre_option_woocommerce_tax_display_shop_filter = 'get_excl_option';
			} else {
				$pre_option_woocommerce_tax_display_shop_filter = 'get_incl_option';
			}
		}

		// Temporarily change the tax display setting
		add_filter( 'pre_option_woocommerce_tax_display_shop', [
			$this,
			$pre_option_woocommerce_tax_display_shop_filter
		], 1, 3 );

		// Temporarily change the prices_include_tax setting if necessary
		if ( $shop_prices_include_tax !== $prices_include_tax || $is_vat_exempt ) {
			if ( $is_vat_exempt ) {
				$woocommerce_prices_include_tax_filter = 'get_prices_exclude_tax_option';
			} else {
				if ( $prices_include_tax ) {
					$woocommerce_prices_include_tax_filter = 'get_prices_exclude_tax_option';
				} else {
					$woocommerce_prices_include_tax_filter = 'get_prices_include_tax_option';
				}
			}
			$pricesIncludeTaxFilter = true;
			add_filter( 'woocommerce_prices_include_tax', [ $this, $woocommerce_prices_include_tax_filter ], 99, 1 );
		}

		$price = wc_get_price_to_display( $calculator, [ 'price' => $price ] );

		// Remove our temporary filters
		remove_filter( 'pre_option_woocommerce_tax_display_shop', [
			$this,
			$pre_option_woocommerce_tax_display_shop_filter
		], 1 );

		if ( $pricesIncludeTaxFilter ) {
			remove_filter( 'woocommerce_prices_include_tax', [ $this, $woocommerce_prices_include_tax_filter ], 99 );
		}

		if ( $is_vat_exempt ) {
			WC()->customer->set_is_vat_exempt( true );
		}

		unset( $calculator );

		return $price;
	}

	public function get_prices_include_tax_option( $include_tax ) {
		return true;
	}

	public function get_prices_exclude_tax_option( $exclude_tax ) {
		return false;
	}

	public function get_incl_option( $pre_option, $option, $default_value ) {
		return 'incl';
	}

	public function get_excl_option( $pre_option, $option, $default_value ) {
		return 'excl';
	}

	public function is_mail_context() {
		if (
			did_action( 'woocommerce_email_header' ) ||
			did_action( 'woocommerce_email_order_details' )
		) {
			return true;
		}

		//Compatibility for YayMail - WooCommerce Email Customizer
		return did_action( 'yaymail_before_email_content' );
	}

	/**
	 * @return bool
	 * @since 1.6.1
	 */
	public function is_file_context() {
		//Compatibility for PDF Invoices & Packing Slips for WooCommerce
		// wcpdf_get_document is also called sometimes via AJAX in frontend
		// This would probably return false or null, but this would pass this if incorrectly
		//if (did_filter( 'wcpdf_get_document' ) ) {
		//In 1.6.11 switched to:
		if ( did_filter( 'wpo_wcpdf_html_filters' ) || did_filter( 'wpo_wcpdf_pdf_filters' ) ) {
			return true;
		}


		return false;
	}

	/**
	 * @return bool
	 * @since 1.2.0
	 */
	public function is_in_cart_or_checkout() {
		if ( is_cart() || is_checkout() ) {
			return true;
		}
		//in shopping cart widget
		if ( Wdevs_Tax_Switch_Mini_Cart_Context::is_in_mini_cart() ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 * @since 1.2.5
	 */
	public function shop_displays_price_including_tax_by_default() {
		if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
			return false;
		}

		return $this->shop_prices_include_tax();
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_non_wc_pages() {
		return ( get_option( 'wdevs_tax_switch_location', 'all' ) === 'woocommerce' );
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_non_price_pages() {
		return ( get_option( 'wdevs_tax_switch_location', 'all' ) === 'prices' );
	}

	/**
	 * @return bool
	 * @since 1.5,2
	 */
	public function should_hide_on_current_page() {
		$should_hide = false;

		// During AJAX requests, conditional tags like is_woocommerce() and is_account_page()
		// don't work reliably because there's no global $post context.
		// Always allow rendering during AJAX, let JavaScript handle visibility.
		if ( $this->is_doing_ajax() ) {
			$should_hide = false;
		} elseif ( $this->should_hide_on_non_wc_pages() ) {
			if ( ! is_woocommerce() && ! is_account_page() ) {
				//Already always disabled on cart and checkout: && ! is_cart() && ! is_checkout()
				$should_hide = true;
			}
		}

		/**
		 * Filter whether to hide the tax switch/label on the current page
		 *
		 * Allows developers to override the default visibility logic.
		 *
		 * Use cases:
		 * - Hide on specific pages (e.g., homepage even during AJAX)
		 * - Show on custom post types
		 * - Complex conditional logic based on user roles, etc.
		 *
		 * @since 1.6.0
		 * @param bool $should_hide Whether to hide the component. Default is based on plugin settings.
		 * @return bool True to hide the component, false to show it.
		 */
		return apply_filters( 'wdevs_tax_switch_should_hide_on_current_page', $should_hide );
	}

	/**
	 * @return array
	 * @since 1.5.4
	 *
	 */
	public function register_script( $handle, $build_dir, $asset_name, $extra_dependencies = [] ) {
		$script_path  = plugin_dir_url( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.js';
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.asset.php' );

		wp_register_script(
			$handle,
			$script_path,
			array_merge( $script_asset['dependencies'], [ 'wdevs-tax-switch-shared-script' ], $extra_dependencies ),
			$script_asset['version']
		);

		return $script_asset;
	}

	/**
	 * @return array
	 * @since 1.5.4
	 *
	 */
	public function enqueue_script( $handle, $build_dir, $asset_name, $extra_dependencies = [] ) {
		$script_path  = plugin_dir_url( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.js';
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/' . $build_dir . '/' . $asset_name . '.asset.php' );

		wp_enqueue_script(
			$handle,
			$script_path,
			array_merge( $script_asset['dependencies'], [ 'wdevs-tax-switch-shared-script' ], $extra_dependencies ),
			$script_asset['version']
		);

		return $script_asset;
	}

	/**
	 * Get the current product, handling AJAX variation requests and product loop contexts.
	 *
	 * During AJAX variation requests, there is no global $post context,
	 * so we need to manually retrieve the variation product using the
	 * same logic as WooCommerce uses in WC_AJAX::get_variation()
	 *
	 * @return WC_Product|null The current product or null if not available
	 * @since 1.5.18
	 */
	public function get_current_product() {
		$product           = null;
		$post_product_id   = 0;
		$is_variation_ajax = (
			doing_action( 'wc_ajax_get_variation' ) ||
			doing_action( 'wp_ajax_woocommerce_get_variation' ) ||
			doing_action( 'wp_ajax_nopriv_woocommerce_get_variation' )
		) && ! empty( $_POST['product_id'] );

		if ( $is_variation_ajax ) {
			$variable_product = wc_get_product( absint( $_POST['product_id'] ) );

			if ( $variable_product ) {
				$data_store   = WC_Data_Store::load( 'product' );
				$variation_id = $data_store->find_matching_product_variation( $variable_product, wp_unslash( $_POST ) );

				if ( $variation_id ) {
					$product = wc_get_product( $variation_id );
				}
			}
		}

		if ( ! $product && ! $is_variation_ajax ) {
			global $post;

			if ( isset( $post, $post->ID ) && in_array( get_post_type( $post->ID ), [ 'product', 'product_variation' ], true ) ) {
				$post_product_id = absint( $post->ID );
				$product         = wc_get_product( $post );
			}
		}

		if (
			! $product &&
			! $is_variation_ajax &&
			isset( $GLOBALS['product'] ) &&
			$GLOBALS['product'] instanceof WC_Product &&
			( ! $post_product_id || $GLOBALS['product']->get_id() === $post_product_id )
		) {
			$product = $GLOBALS['product'];
		}

		if ( ! $product ) {
			$product = wc_get_product();
		}

		if ( ! $product && ! $is_variation_ajax && is_product() ) {
			$product_id = get_queried_object_id();

			if ( $product_id && 'product' === get_post_type( $product_id ) ) {
				$product = wc_get_product( $product_id );
			}
		}

		/**
		 * Allow third party code to override the current product context.
		 *
		 * @since 1.6.2
		 */
		return apply_filters( 'wdevs_tax_switch_current_product', $product );
	}

	/**
	 * Check for AJAX requests.
	 *
	 * @link https://gist.github.com/zitrusblau/58124d4b2c56d06b070573a99f33b9ed#file-lazy-load-responsive-images-php-L193
	 * @since 1.6.0
	 */
	public function is_doing_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	/**
	 * Check if price switching should be allowed in the mini cart.
	 *
	 * @return bool True if we're in mini cart and the setting is enabled, false otherwise.
	 * @since 1.6.0
	 */
	public function should_switch_in_mini_cart() {
		if ( ! Wdevs_Tax_Switch_Mini_Cart_Context::is_in_mini_cart() ) {
			return false;
		}

		return get_option( 'wdevs_tax_switch_enable_mini_cart', 'no' ) === 'yes';
	}

	/**
	 * Get the label for the current tax display status.
	 *
	 * @param bool $shop_prices_include_tax Whether the shop shows prices including tax.
	 * @return string
	 * @since 1.8.0
	 */
	public function get_tax_text( $shop_prices_include_tax ) {
		if ( $shop_prices_include_tax ) {
			return $this->get_option_text( 'wdevs_tax_switch_incl_vat', __( 'Incl. VAT', 'tax-switch-for-woocommerce' ) );
		}

		return $this->get_option_text( 'wdevs_tax_switch_excl_vat', __( 'Excl. VAT', 'tax-switch-for-woocommerce' ) );
	}

	/**
	 * Get the label for the alternate tax display.
	 *
	 * @param bool $shop_prices_include_tax Whether the shop shows prices including tax.
	 * @return string
	 * @since 1.8.0
	 */
	public function get_alternate_tax_text( $shop_prices_include_tax ) {
		return $this->get_tax_text( ! $shop_prices_include_tax );
	}

	/**
	 * @since 1.6.7
	 * @deprecated 1.8.0 Use get_tax_text().
	 * @param bool $shop_prices_include_tax Whether the shop shows prices including tax.
	 * @return string
	 */
	public function get_vat_text( $shop_prices_include_tax ) {
		_deprecated_function( __METHOD__, '1.8.0', 'get_tax_text' );

		return $this->get_tax_text( $shop_prices_include_tax );
	}

	/**
	 * @since 1.6.7
	 * @deprecated 1.8.0 Use get_alternate_tax_text().
	 * @param bool $shop_prices_include_tax Whether the shop shows prices including tax.
	 * @return string
	 */
	public function get_alternate_vat_text( $shop_prices_include_tax ) {
		_deprecated_function( __METHOD__, '1.8.0', 'get_alternate_tax_text' );

		return $this->get_alternate_tax_text( $shop_prices_include_tax );
	}

}
