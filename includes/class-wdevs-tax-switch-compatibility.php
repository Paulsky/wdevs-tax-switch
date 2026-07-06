<?php

/**
 * The third party compatibility functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.1.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The third party compatibility functionality of the plugin.
 *
 * Defines the hooks and functions for third party compatibility functionality.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Compatibility {

	use Wdevs_Tax_Switch_Helper, Wdevs_Tax_Switch_Plugins, Wdevs_Tax_Switch_Display;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.1.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_compatibility_scripts() {
		if ( is_product() ) {
			$tax_rate = $this->get_product_tax_rate( wc_get_product() );

			// WooCommerce Measurement Price Calculator
			if ( $this->is_plugin_active( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php' ) ) {
				$wmpc_handle = 'wdevs-tax-switch-woocommerce-measurement-price-calculator';
				$wmpc_asset  = $this->enqueue_script( $wmpc_handle, 'switch', 'woocommerce-measurement-price-calculator' );

				wp_localize_script(
					$wmpc_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// YITH WooCommerce Product Add-Ons (both free and premium)
			if ( $this->is_any_plugin_active( [
				'yith-woocommerce-product-add-ons/init.php',
				'yith-woocommerce-advanced-product-options-premium/init.php'
			] ) ) {
				$ywpado_handle = 'wdevs-tax-switch-yith-woocommerce-product-add-ons';
				$ywpado_asset  = $this->enqueue_script( $ywpado_handle, 'switch', 'yith-woocommerce-product-add-ons', [ 'yith_wapo_front' ] );

				wp_localize_script(
					$ywpado_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// WooCommerce Product Addons
			if ( $this->is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
				$wpado_handle = 'wdevs-tax-switch-woocommerce-product-addons';
				$wpado_asset  = $this->enqueue_script( $wpado_handle, 'switch', 'woocommerce-product-addons', [ 'accounting' ] );

				wp_localize_script(
					$wpado_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Advanced Product Fields Pro/Extended for WooCommerce
			if ( $this->is_any_plugin_active( [
				'advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php',
				'advanced-product-fields-for-woocommerce-extended/advanced-product-fields-for-woocommerce-extended.php'
			] ) ) {
				$apffw_handle = 'wdevs-tax-switch-advanced-product-fields-for-woocommerce';
				$apffw_asset  = $this->enqueue_script( $apffw_handle, 'switch', 'advanced-product-fields-for-woocommerce', [
					'wapf-frontend',
					'accounting'
				] );
			}

			// Woocommerce Quantity Manager
			if ( $this->is_plugin_active( 'woocommerce-quantity-manager-pro/woocommerce-quantity-manager-pro.php' ) ) {
				$wqm_handle = 'wdevs-tax-switch-woocommerce-quantity-manager';
				$wqm_asset  = $this->enqueue_script( $wqm_handle, 'switch', 'woocommerce-quantity-manager', [ 'accounting' ] );

				wp_localize_script(
					$wqm_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Product Extras for Woocommerce (Woocommerce Product Add-Ons Ultimate)
			if ( $this->is_plugin_active( 'product-extras-for-woocommerce/product-extras-for-woocommerce.php' ) ) {
				$pewc_handle        = 'wdevs-tax-switch-product-extras-for-woocommerce';
				$pewc_asset         = $this->enqueue_script( $pewc_handle, 'switch', 'product-extras-for-woocommerce', [ 'accounting' ] ); //'pewc-script',breaks things, but I wonder if that correct....
				$including_tax_text = $this->get_tax_text( true );
				$excluding_tax_text = $this->get_alternate_tax_text( true );

				wp_localize_script(
					$pewc_handle,
					'wtsCompatibilityObject',
					[
						'baseTaxRate'       => $tax_rate,
						'includingTaxText'  => $including_tax_text,
						'excludingTaxText'  => $excluding_tax_text,
						'includingVatText'  => $including_tax_text, // Deprecated since 1.8.0.
						'excludingVatText'  => $excluding_tax_text, // Deprecated since 1.8.0.
					]
				);
			}

			//Kapee theme
			if ( $this->is_theme_active( 'Kapee' ) ) {
				$kapee_handle = 'wdevs-tax-switch-kapee-theme';
				$kapee_asset  = $this->enqueue_script( $kapee_handle, 'switch', 'kapee-theme', [
					'accounting',
					'kapee-script'
				] );

				wp_localize_script(
					$kapee_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// Extra Product Options & Add-Ons for WooCommerce
			if ( $this->is_plugin_active( 'woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php' ) ) {
				$tmtepo_handle = 'wdevs-tax-switch-woocommerce-tm-extra-product-options';
				$tmtepo_asset  = $this->enqueue_script( $tmtepo_handle, 'switch', 'woocommerce-tm-extra-product-options', [ 'themecomplete-epo' ] );

				wp_localize_script(
					$tmtepo_handle,
					'wtsCompatibilityObject',
					[ 'baseTaxRate' => $tax_rate ]
				);
			}

			// WooCommerce Fees & Discounts
			if ( $this->is_plugin_active( 'woocommerce-fees-discounts/woocommerce-fees-discounts.php' ) ) {
				$wcfad_handle       = 'wdevs-tax-switch-woocommerce-fees-discounts';
				$wcfad_asset        = $this->enqueue_script(
					$wcfad_handle,
					'switch',
					'woocommerce-fees-discounts',
					[ 'wcfad-script' ]
				);
				$including_tax_text = $this->get_tax_text( true );
				$excluding_tax_text = $this->get_alternate_tax_text( true );

				wp_localize_script(
					$wcfad_handle,
					'wtsCompatibilityObject',
					[
						'baseTaxRate'       => $tax_rate,
						'includingTaxText'  => $including_tax_text,
						'excludingTaxText'  => $excluding_tax_text,
						'includingVatText'  => $including_tax_text, // Deprecated since 1.8.0.
						'excludingVatText'  => $excluding_tax_text, // Deprecated since 1.8.0.
					]
				);
			}
		}

		// Tier Pricing Table (both free and premium)
		if ( $this->is_any_plugin_active( [
			'tier-pricing-table/tier-pricing-table.php',
			'tier-pricing-table-premium/tier-pricing-table.php'
		] ) ) {
			$wctpt_handle = 'wdevs-tax-switch-woocommerce-tiered-price-table';
			$wctpt_asset  = $this->enqueue_script( $wctpt_handle, 'switch', 'woocommerce-tiered-price-table' );
		}

		// FiboFilters
		if ( $this->is_plugin_active( 'fibofilters-pro/fibofilters.php' ) ) {
			$ffilters_handle = 'wdevs-tax-switch-fibofilters';
			$ffilters_asset  = $this->enqueue_script( $ffilters_handle, 'switch', 'fibofilters', [ 'fibofilters' ] );
		}

		// WoodMart theme
		if ( $this->is_theme_active( 'woodmart' ) ) {
			$woodmart_handle = 'wdevs-tax-switch-woodmart-theme';
			$woodmart_asset  = $this->enqueue_script( $woodmart_handle, 'switch', 'woodmart-theme' );
		}

		// FacetWP
		if ( $this->is_plugin_active( 'facetwp/index.php' ) ) {
			$facetwp_handle = 'wdevs-tax-switch-facetwp';
			$facetwp_asset  = $this->enqueue_script( $facetwp_handle, 'switch', 'facetwp' );

			// Estimate tax rate for FacetWP (no specific product context available)
			$estimated_tax_rate = $this->estimate_tax_rate();

			wp_localize_script(
				$facetwp_handle,
				'wtsCompatibilityObject',
				[ 'baseTaxRate' => $estimated_tax_rate ]
			);
		}

		// WP Grid Builder
		if ( $this->is_plugin_active( 'wp-grid-builder/wp-grid-builder.php' ) ) {
			$wp_grid_builder_handle = 'wdevs-tax-switch-wp-grid-builder';
			$wp_grid_builder_asset  = $this->enqueue_script( $wp_grid_builder_handle, 'switch', 'wp-grid-builder', [ 'wpgb' ] );
		}

	}

	public function activate_wc_product_table_compatibility( $element ) {
		if ( isset( $element ) && isset( $element['type'] ) && $element['type'] === 'price' ) {
			$element['use_default_template'] = true;
		}

		return $element;
	}

	/**
	 * Includes these properties in the AJAX response for a variation
	 */
	public function add_prices_to_variation( $variation_data, $product, $variation ) {
		$variation_data['price_incl_tax'] = wc_get_price_including_tax( $variation );
		$variation_data['price_excl_tax'] = wc_get_price_excluding_tax( $variation );
//		$variation_data['price_incl_vat'] = $variation_data['price_incl_tax']; // Deprecated since 1.8.0.
//		$variation_data['price_excl_vat'] = $variation_data['price_excl_tax']; // Deprecated since 1.8.0.

		return $variation_data;
	}

	/**
	 * Includes these properties in the AJAX response for a variation
	 * @since 1.1.7
	 */
	public function add_tax_rate_to_variation( $variation_data, $product, $variation ) {
		$variation_data['tax_rate'] = $this->get_product_tax_rate( $variation );

		return $variation_data;
	}

	/**
	 * Adds the alternate price to the Advanced Product Fields Pro hints
	 * @since 1.4.1
	 */
	public function render_wapf_pricing_hint( $original_output, $product, $amount, $type, $field = null, $option = null ) {
		if ( $this->is_in_cart_or_checkout() && ! $this->should_switch_in_mini_cart() ) {
			return $original_output;
		}

		if ( ! class_exists( 'SW_WAPF_PRO\Includes\Classes\Helper' ) ) {
			return $original_output;
		}

		$alternate_amount = $this->calculate_alternate_price( $amount, $product );

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'wapf/html/pricing_hint', [ $this, 'render_wapf_pricing_hint' ], 10 );

		//get the pricing format for the alternate amount
		$alternate_hint = \SW_WAPF_PRO\Includes\Classes\Helper::format_pricing_hint(
			$type,
			$alternate_amount,
			$product,
			'shop',
			$field,
			$option
		);

		//Re-enable this filter and function
		add_filter( 'wapf/html/pricing_hint', [ $this, 'render_wapf_pricing_hint' ], 10, 6 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $original_output, $alternate_hint, $shop_prices_include_tax );
	}

	/**
	 * Adds the alternate price to the Product Extras for Woocommerce (WooCommerce Product Add-Ons Ultimate) price field
	 * @since 1.5.5
	 */
	public function render_pewc_price_field( $original_output, $item, $product, $price = false ) {
		if ( ! $price ) {
			return $original_output;
		}

		$alternate_amount = $this->calculate_alternate_price( $price, $product );

		//Temporarily disable this filter and function to prevent infinite loop
		remove_filter( 'pewc_field_formatted_price', [ $this, 'render_pewc_price_field' ], PHP_INT_MAX );

		//get the pricing format for the alternate amount
		$alternate_field = apply_filters(
			'pewc_field_formatted_price',
			pewc_wc_format_price( $alternate_amount ),
			$item,
			$product,
			$alternate_amount
		);

		//Re-enable this filter and function
		add_filter( 'pewc_field_formatted_price', [ $this, 'render_pewc_price_field' ], PHP_INT_MAX, 4 );

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Combine both price displays into one HTML string
		return $this->combine_price_displays( $original_output, $alternate_field, $shop_prices_include_tax );
	}

	/**
	 * Allow span with classes in certain price html like menus or mini carts.
	 * See @combine_price_displays. That's needed to wrap the prices. It only contains spans with a couple of classes
	 *
	 * @param $tags
	 * @param $context
	 *
	 * @return array
	 * @since 1.5.11
	 */
	public function kses_allow_span_classes_for_prices( $tags, $context ) {
		//filters and actions
		$allowed_contexts = [
			'woocommerce_add_to_cart_fragments', //Default WooCommerce AJAX cart response
			'generate_menu_bar_items', //GeneratePress theme compatibility,
			'entr_header_cart' //Entr theme compatibility
		];

		if ( in_array( $context, $allowed_contexts ) ) {
			$tags['span'] = array(
				'class' => true,
			);
		}

		return $tags;
	}

	/**
	 * Filters FacetWP slider facet render arguments to add tax label text to price sliders
	 *
	 * @param array $args Facet render arguments containing facet settings
	 *
	 * @return array Modified render arguments with tax label text added to suffix
	 * @since 1.6.0
	 */
	public function filter_facetwp_slider_label( $args ) {
		// Only process slider facets
		if ( ! isset( $args['facet']['type'] ) || $args['facet']['type'] !== 'slider' ) {
			return $args;
		}

		// Only process WooCommerce price-related sources
		$price_sources = [ 'woo/price', 'woo/sale_price', 'woo/regular_price' ];
		if ( ! isset( $args['facet']['source'] ) || ! in_array( $args['facet']['source'], $price_sources ) ) {
			return $args;
		}

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		// Get tax text options
		$tax_text           = $this->get_tax_text( $shop_prices_include_tax );
		$alternate_tax_text = $this->get_alternate_tax_text( $shop_prices_include_tax );

		// Get current suffix (may be empty)
		$current_suffix = isset( $args['facet']['suffix'] ) ? $args['facet']['suffix'] : '';

		// Wrap the suffix with tax label text
		$args['facet']['suffix'] = $this->wrap_price_displays(
			$current_suffix,
			$shop_prices_include_tax,
			$tax_text,
			$alternate_tax_text
		);

		return $args;
	}

	/**
	 * Enable dynamic price loading for FiboSearch to ensure tax-inclusive/exclusive prices are calculated based on the current customer. Normally, The price is saved in the search index statically.
	 *
	 * @param bool $loadDynamically Current dynamic loading state.
	 *
	 * @return bool Always true to force dynamic price loading.
	 * @since 1.6.0
	 */
	public function enable_ajax_search_for_woocommerce_dynamic_prices( $loadDynamically ) {
		return true;
	}

	/**
	 * Use bundled item product as current product context for WooCommerce Product Bundles.
	 *
	 * @param WC_Product|null $product Current product context.
	 *
	 * @return WC_Product|null
	 * @since 1.6.2
	 */
	public function set_product_for_woocommerce_product_bundles( $product ) {
		if ( ! class_exists( 'WC_PB_Product_Prices' ) || empty( WC_PB_Product_Prices::$bundled_item ) ) {
			return $product;
		}

		$bundled_item = WC_PB_Product_Prices::$bundled_item;

		if ( ! $bundled_item instanceof WC_Bundled_Item ) {
			return $product;
		}

		$bundled_product = $bundled_item->get_product();

		if ( $bundled_product instanceof WC_Product ) {
			return $bundled_product;
		}

		return $product;
	}

	/**
	 * Wrap YITH Role Based Prices suffix with alternate tax display.
	 *
	 * @param string $suffix Existing suffix HTML.
	 * @param YITH_Role_Based_Prices_Product $yith_product Instance used by YITH.
	 *
	 * @return string
	 * @since 1.6.4
	 */
	public function wrap_yith_price_suffix( $suffix, $yith_product ) {
		if ( empty( trim( $suffix ) ) ) {
			return $suffix;
		}

		if ( str_contains( $suffix, 'wts-price-wrapper' ) || str_contains( $suffix, 'wts-price-container' ) ) {
			return $suffix;
		}

		if ( $this->should_hide_on_current_page() ) {
			return $suffix;
		}

		$shop_prices_include_tax = $this->shop_displays_price_including_tax_by_default();

		$tax_text           = $this->get_tax_text( $shop_prices_include_tax );
		$alternate_tax_text = $this->get_alternate_tax_text( $shop_prices_include_tax );

		$html = $this->wrap_price_displays( '', $shop_prices_include_tax, $tax_text, $alternate_tax_text );

		return $html;
	}

}
