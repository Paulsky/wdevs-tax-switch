<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks,
 * public-facing site hooks, and block-related functionality.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch {

	use Wdevs_Tax_Switch_Plugins;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wdevs_Tax_Switch_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site, block functionality, and AJAX hooks.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WDEVS_TAX_SWITCH_VERSION' ) ) {
			$this->version = WDEVS_TAX_SWITCH_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wdevs-tax-switch';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_woocommerce_hooks();
		$this->define_block_hooks();
		$this->define_compatibility_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wdevs_Tax_Switch_Loader. Orchestrates the hooks of the plugin.
	 * - Wdevs_Tax_Switch_i18n. Defines internationalization functionality.
	 * - Wdevs_Tax_Switch_Admin. Defines all hooks for the admin area.
	 * - Wdevs_Tax_Switch_Public. Defines all hooks for the public side of the site.
	 * - Wdevs_Tax_Switch_WooCommerce. Defines all hooks for the WooCommerce functionality.
	 * - Wdevs_Tax_Switch_Block. Defines all hooks for the block functionality.
	 * - Wdevs_Tax_Switch_Compatibility. Defines all functions for third party compatibility.
	 * - Wdevs_Tax_Switch_Mini_Cart_Context. Controls the mini cart context state.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The trait with helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/trait-wdevs-tax-switch-helper.php';

		/**
		 * The trait with display functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/trait-wdevs-tax-switch-display.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wdevs-tax-switch-admin.php';

		/**
		 * The class responsible for tracking whether the code is currently in the mini cart context
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-mini-cart-context.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wdevs-tax-switch-public.php';

		/**
		 * The class responsible for defining the WooCommerce functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-woocommerce.php';

		/**
		 * The class responsible for shared block functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-block-shared.php';

		/**
		 * The class responsible for common shortcode and Gutenberg block functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-block.php';

		/**
		 * The class responsible for the shortcode and Gutenberg block rendering of the Switch component
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-block-switch.php';

		/**
		 * The class responsible for the shortcode and Gutenberg block rendering of the Label component
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-block-label.php';

		/**
		 * The class responsible for defining all functionality from adding compatibility with third party code
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tax-switch-compatibility.php';

		$this->loader = new Wdevs_Tax_Switch_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			$plugin_admin = new Wdevs_Tax_Switch_Admin( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_scripts' );
			$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) . '/' . $this->plugin_name . '.php' ), $plugin_admin, 'add_action_links' );
			$this->loader->add_filter( 'woocommerce_display_admin_footer_text', $plugin_admin, 'hide_woocommerce_footer_text' );
			$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer_text', 20 );

			//AJAX Requests
			$this->loader->add_action( 'wp_ajax_' . Wdevs_Tax_Switch_Admin::AJAX_ACTION_RENDER, $plugin_admin, Wdevs_Tax_Switch_Admin::AJAX_ACTION_RENDER . '_action' );
			$this->loader->add_action( 'wp_ajax_' . Wdevs_Tax_Switch_Admin::AJAX_ACTION_FOOTER_RATED, $plugin_admin, Wdevs_Tax_Switch_Admin::AJAX_ACTION_FOOTER_RATED . '_action' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		if ( ! $this->is_admin_request() || $this->is_post_editor() ) {
			$this->loader->add_action( 'woocommerce_before_mini_cart', Wdevs_Tax_Switch_Mini_Cart_Context::class, 'before_mini_cart' );
			$this->loader->add_action( 'woocommerce_after_mini_cart', Wdevs_Tax_Switch_Mini_Cart_Context::class, 'after_mini_cart' );

			$plugin_public = new Wdevs_Tax_Switch_Public( $this->get_plugin_name(), $this->get_version() );
			if ( ! $this->is_doing_ajax() ) {
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			}

			$this->loader->add_filter( 'wc_price', $plugin_public, 'wrap_wc_price', PHP_INT_MAX, 5 );
			$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_public, 'get_price_html', PHP_INT_MIN, 2 );
			$this->loader->add_filter( 'woocommerce_countries_inc_tax_or_vat', $plugin_public, 'wrap_inc_label', PHP_INT_MAX, 1 );
			$this->loader->add_filter( 'woocommerce_countries_ex_tax_or_vat', $plugin_public, 'wrap_ex_label', PHP_INT_MAX, 1 );
			$this->loader->add_filter( 'woocommerce_coupon_get_minimum_amount', $plugin_public, 'is_coupon_error_message', 10, 1 );
			$this->loader->add_filter( 'woocommerce_coupon_get_maximum_amount', $plugin_public, 'is_coupon_error_message', 10, 1 );

			// B2B Market
			if ( $this->is_plugin_active( 'b2b-market/b2b-market.php' ) ) {
				$this->loader->add_filter( 'bm_filter_woocommerce_get_price_html', $plugin_public, 'get_price_html', PHP_INT_MIN, 2 );
			}
		}
	}

	/**
	 * Register all of the hooks related to the Woocommerce functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_woocommerce_hooks() {
		$plugin_woocommerce = new Wdevs_Tax_Switch_Woocommerce( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_filter( 'before_woocommerce_init', $plugin_woocommerce, 'declare_compatibility' );
		if ( is_admin() ) {
			$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_woocommerce, 'add_settings_tab', 50 );
			$this->loader->add_action( 'woocommerce_settings_tabs_wdevs_tax_switch', $plugin_woocommerce, 'settings_tab' );
		}
	}

	/**
	 * Register all of the hooks related to the block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks() {
		$shared_block = new Wdevs_Tax_Switch_Block_Shared( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $shared_block, 'register_frontend_scripts' );

		$switch_block = new Wdevs_Tax_Switch_Block_Switch( $this->get_plugin_name(), $this->get_version() );
		$label_block  = new Wdevs_Tax_Switch_Block_Label( $this->get_plugin_name(), $this->get_version() );

		$blocks = [ $switch_block, $label_block ];

		foreach ( $blocks as $block_class ) {
			$this->loader->add_action( 'init', $block_class, 'register_frontend_scripts' );
			$this->loader->add_action( 'init', $block_class, 'init_block' );
			$this->loader->add_action( 'init', $block_class, 'register_shortcode' );
		}

		$this->loader->add_action( 'block_type_metadata', $label_block, 'set_default_block_attributes' );

	}

	/**
	 * Register all the hooks related to the third party functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_compatibility_hooks() {
		if ( ! $this->is_admin_request() ) {
			$plugin_compatibility = new Wdevs_Tax_Switch_Compatibility( $this->get_plugin_name(), $this->get_version() );
			if ( ! $this->is_doing_ajax() ) {
				$this->loader->add_action( 'wp_enqueue_scripts', $plugin_compatibility, 'enqueue_compatibility_scripts' );
			}
			//wc product table compatibility
			$this->loader->add_filter( 'wcpt_element', $plugin_compatibility, 'activate_wc_product_table_compatibility', 10, 1 );

			//Allow certain HTML elements in certain contexts
			$this->loader->add_filter( 'wp_kses_allowed_html', $plugin_compatibility, 'kses_allow_span_classes_for_prices', 10, 2 );

			// Check for WooCommerce Measurement Price Calculator plugin
			if ( $this->is_plugin_active( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php' ) ) {
				$this->loader->add_filter(
					'woocommerce_available_variation',
					$plugin_compatibility,
					'add_prices_to_variation',
					10,
					3
				);
			}

			// Check for YITH WooCommerce Product Add-Ons (both free and premium versions),
			// Woocommerce Quantity Manager, and Extra Product Options & Add-Ons for WooCommerce
			if ( $this->is_any_plugin_active( [
				'yith-woocommerce-product-add-ons/init.php',
				'yith-woocommerce-advanced-product-options-premium/init.php',
				'woocommerce-quantity-manager-pro/woocommerce-quantity-manager-pro.php',
				'woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php'
			] ) ) {
				$this->loader->add_filter(
					'woocommerce_available_variation',
					$plugin_compatibility,
					'add_tax_rate_to_variation',
					10,
					3
				);
			}

			// Advanced Product Fields Pro/Extended for WooCommerce
			if ( $this->is_any_plugin_active( [
				'advanced-product-fields-for-woocommerce-pro/advanced-product-fields-for-woocommerce-pro.php',
				'advanced-product-fields-for-woocommerce-extended/advanced-product-fields-for-woocommerce-extended.php'
			] ) ) {
				$this->loader->add_filter(
					'wapf/html/pricing_hint',
					$plugin_compatibility,
					'render_wapf_pricing_hint',
					10,
					6
				);
			}

			// Product Extras for Woocommerce (WooCommerce Product Add-Ons Ultimate)
			if ( $this->is_plugin_active( 'product-extras-for-woocommerce/product-extras-for-woocommerce.php' ) ) {
				$this->loader->add_filter(
					'pewc_field_formatted_price',
					$plugin_compatibility,
					'render_pewc_price_field',
					PHP_INT_MAX,
					4
				);
			}

			// FacetWP - Add tax label to price sliders
			if ( $this->is_plugin_active( 'facetwp/index.php' ) ) {
				$this->loader->add_filter(
					'facetwp_facet_render_args',
					$plugin_compatibility,
					'filter_facetwp_slider_label',
					10,
					1
				);
			}

			//FiboSearch - AJAX Search for WooCommerce Pro
			if ( $this->is_plugin_active( 'ajax-search-for-woocommerce-premium/ajax-search-for-woocommerce.php' ) ) {
				$this->loader->add_filter( 'dgwt/wcas/tnt/dynamic_prices', $plugin_compatibility, 'enable_ajax_search_for_woocommerce_dynamic_prices', 10, 1 );
			}

			//WooCommerce Product Bundles
			if ( $this->is_plugin_active( 'woocommerce-product-bundles/woocommerce-product-bundles.php' ) ) {
				$this->loader->add_filter( 'wdevs_tax_switch_current_product', $plugin_compatibility, 'set_product_for_woocommerce_product_bundles', 10, 1 );
			}

			// YITH WooCommerce Role Based Prices
			if ( $this->is_plugin_active( 'yith-woocommerce-role-based-prices-premium/init.php' ) ) {
				$this->loader->add_filter(
					'yith_role_based_prices_get_price_suffix',
					$plugin_compatibility,
					'wrap_yith_price_suffix',
					PHP_INT_MAX,
					2
				);
			}
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wdevs_Tax_Switch_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Check for enabling the switch in the editor
	 *
	 * @return bool
	 * @since     1.0.0
	 */
	private function is_post_editor() {

		if ( ! is_admin() ) {
			return false;
		}

		global $pagenow;

		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : null;
		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
		} else {
			$post_type = get_post_type();
		}

		if ( $post_type == 'post' || $post_type == 'page' ) {
			return true;
		}

		//In older versions of Woocommerce, the URL is like this:
		//wp-admin/edit.php?post_type=shop_order
		//wp-admin/post.php?post=orderId&action=edit

		//but new versions are like this (will not enter this condition)
		//wp-admin/admin.php?page=wc-orders
		//wp-admin/admin.php?page=wc-orders&action=edit&id=orderId
		if ( $post_type == 'shop_order' ) {
			return false;
		}

		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if this is a request at the backend.
	 *
	 * @link https://florianbrinkmann.com/en/wordpress-backend-request-3815/
	 * @return bool true if is admin request, otherwise false.
	 * @since 1.1.5
	 */
	private function is_admin_request() {
		/**
		 * Get current URL.
		 *
		 * @link https://wordpress.stackexchange.com/a/237498/178511
		 */
		$parts       = wp_parse_url( home_url() );
		$current_url = $parts['scheme'] . '://' . $parts['host'];

		if ( array_key_exists( 'port', $parts ) ) {
			$current_url .= ':' . $parts['port'];
		}

		$current_url .= add_query_arg( [] );

		/**
		 * Get admin URL and referrer.
		 *
		 * @link https://core.trac.wordpress.org/browser/tags/4.8/src/wp-includes/pluggable.php#L1076
		 */
		$admin_url = strtolower( admin_url() );
		$referrer  = strtolower( wp_get_referer() );

		/**
		 * Check if this is an admin request. If true, it
		 * could also be an AJAX request from the frontend.
		 */
		if ( 0 === strpos( $current_url, $admin_url ) ) {
			/**
			 * Check if the user comes from an admin page.
			 */
			if ( 0 === strpos( $referrer, $admin_url ) ) {
				return true;
			} else {
				return ! $this->is_frontend_ajax_request();
			}
		} else {
			return false;
		}
	}

	/**
	 * Check for AJAX requests.
	 *
	 * @link https://gist.github.com/zitrusblau/58124d4b2c56d06b070573a99f33b9ed#file-lazy-load-responsive-images-php-L193
	 * @since 1.1.5
	 */
	private function is_doing_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	/**
	 * @return bool
	 * @since 1.1.9
	 */
	private function is_frontend_ajax_request() {

		$script_filename = isset( $_SERVER['SCRIPT_FILENAME'] ) ? $_SERVER['SCRIPT_FILENAME'] : '';

		if ( $this->is_doing_ajax() ) {
			$referrer_raw = wp_get_raw_referer();
			if ( $referrer_raw === false ) {
				return false;
			}

			//If referer does not contain admin URL and we are using the admin-ajax.php endpoint, this is likely a frontend AJAX request
			if ( ( ( strpos( $referrer_raw, admin_url() ) === false ) && ( basename( $script_filename ) === 'admin-ajax.php' ) ) ) {
				return true;
			}
		}

		//If no checks triggered, we end up here - not an AJAX request.
		return false;
	}

}
