<?php

/**
 * The WooCommerce functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The WooCommerce functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for WooCommerce functionality.
 * This class is responsible for registering and rendering the WooCommerce settings.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Woocommerce {

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
	 * The current settings section.
	 *
	 * @since    1.4.0
	 * @access   private
	 * @var      string $current_section The current settings section.
	 */
	private $current_section;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->current_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '';

		if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] === 'wdevs_tax_switch' ) {
			$this->handle_sections();
		}
	}

	/**
	 * Declare WooCommerce compatibility
	 *
	 * @since 1.0.0
	 */
	public function declare_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'tax-switch-for-woocommerce/wdevs-tax-switch.php', true );
		}
	}

	/**
	 * Add settings tab to WooCommerce settings.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs.
	 *
	 * @return   array    $settings_tabs    Array of WooCommerce setting tabs.
	 * @since    1.0.0
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['wdevs_tax_switch'] = __( 'Tax switch', 'tax-switch-for-woocommerce' );

		return $settings_tabs;
	}

	/**
	 * Get settings for the current section.
	 *
	 * @return   array    Array of settings.
	 * @since    1.0.0
	 */
	public function get_settings() {
		switch ( $this->current_section ) {
			case 'shortcode':
				return $this->get_shortcode_settings();
			default:
				return $this->get_main_settings();
		}
	}

	/**
	 * Output the settings.
	 *
	 * @since    1.0.0
	 */
	public function settings_tab() {
		if ( $this->current_section === 'shortcode' ) {
			$GLOBALS['hide_save_button'] = true;
		}

		woocommerce_admin_fields( $this->get_settings() );

		if ( $this->current_section === 'shortcode' ) {
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/section-wdevs-tax-switch-shortcode.php';
		}
	}

	/**
	 * Save the settings.
	 *
	 * @since    1.0.0
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );

		// Only register translations if we're on the main settings section
		if ( empty( $this->current_section ) ) {
			$this->register_translations();
		}
	}

	/**
	 * Handle sections for the settings tab.
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function handle_sections() {
		add_action( 'woocommerce_sections_wdevs_tax_switch', array( $this, 'output_sections' ) );

		if ( ! empty( $this->current_section ) ) {
			add_action( 'woocommerce_update_options_wdevs_tax_switch_' . $this->current_section, array(
				$this,
				'update_settings'
			) );
		} else {
			add_action( 'woocommerce_update_options_wdevs_tax_switch', array( $this, 'update_settings' ) );
		}
	}

	/**
	 * Get available sections for the settings tab.
	 *
	 * @return array Array of sections.
	 * @since    1.4.0
	 */
	public function get_sections() {
		return array(
			''          => __( 'Settings', 'tax-switch-for-woocommerce' ),
			'shortcode' => __( 'Shortcode', 'tax-switch-for-woocommerce' ),
		);
	}


	/**
	 * Output sections navigation.
	 *
	 * @since    1.4.0
	 */
	public function output_sections() {
		$sections = $this->get_sections();

		$documentationURL = 'https://products.wijnberg.dev/product/wordpress/plugins/tax-switch-for-woocommerce/';

		echo '<ul class="subsubsub">';

		//$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			$url       = admin_url( 'admin.php?page=wc-settings&tab=wdevs_tax_switch&section=' . sanitize_title( $id ) );
			$class     = ( $this->current_section === $id ? 'current' : '' );
			$separator = '|';//( end( $array_keys ) === $id ? '' : '|' );
			$text      = esc_html( $label );
			echo "<li><a href='$url' class='$class'>$text</a> $separator </li>";
		}

		?>

		<li>
			<a href="<?php echo esc_attr( $documentationURL ); ?>" target="_blank">
				<?php esc_html_e( 'Documentation', 'tax-switch-for-woocommerce' ); ?>
				<svg style="width: 0.8rem; height: 0.8rem; stroke: currentColor; fill: none;"
					 xmlns="http://www.w3.org/2000/svg"
					 stroke-width="10" stroke-dashoffset="0"
					 stroke-dasharray="0" stroke-linecap="round"
					 stroke-linejoin="round" viewBox="0 0 100 100">
					<polyline fill="none" points="40 20 20 20 20 90 80 90 80 60"/>
					<polyline fill="none" points="60 10 90 10 90 40"/>
					<line fill="none" x1="89" y1="11" x2="50" y2="50"/>
				</svg>
			</a>
		</li>

		<?php

		echo '</ul><br class="clear" />';
	}

	/**
	 * Output footer info
	 *
	 * @since    1.5.7
	 */
	public function render_footer_info() {
		$text = sprintf(
		/* translators: %s: Link to author site. */
			__( 'Tax Switch for WooCommerce is developed by %s. Your trusted WordPress & WooCommerce plugin partner from the Netherlands.', 'tax-switch-for-woocommerce' ),
			'<a href="https://products.wijnberg.dev" target="_blank" rel="noopener">Wijnberg Developments</a>'
		);

		echo '<span style="padding: 0 30px; background: #f0f0f1; display: block;">' . wp_kses_post( $text ) . '</span>';
	}

	/**
	 * Get main settings fields.
	 *
	 * @return array Array of settings.
	 * @since    1.0.0
	 */
	private function get_main_settings() {
		$settings = array(
			array(
				'name' => __( 'Tax switch settings', 'tax-switch-for-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Customize the tax switch settings.', 'tax-switch-for-woocommerce' ),
				'id'   => 'wdevs_tax_switch_section_title'
			),
			array(
				'name'        => __( 'Including tax text', 'tax-switch-for-woocommerce' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices including tax.', 'tax-switch-for-woocommerce' ),
				'id'          => 'wdevs_tax_switch_incl_vat',
				'placeholder' => __( 'Incl. VAT', 'tax-switch-for-woocommerce' )
			),
			array(
				'name'        => __( 'Excluding tax text', 'tax-switch-for-woocommerce' ),
				'type'        => 'text',
				'desc'        => __( 'Text to append to prices excluding tax.', 'tax-switch-for-woocommerce' ),
				'id'          => 'wdevs_tax_switch_excl_vat',
				'placeholder' => __( 'Excl. VAT', 'tax-switch-for-woocommerce' )
			),
//          Added in 1.5.1, removed in 1.5.2
//			array(
//				'name'    => __( 'Hide on cart and checkout pages', 'tax-switch-for-woocommerce' ),
//				'type'    => 'checkbox',
//				'desc'    => __( 'Hide switches/buttons on the cart and checkout pages', 'tax-switch-for-woocommerce' ),
//				'id'      => 'wdevs_tax_switch_hide_on_checkout',
//				'default' => 'no'
//			),
			array(
				'title'       => __( 'Where enabled', 'tax-switch-for-woocommerce' ),
				'type'        => 'radio',
				'id'          => 'wdevs_tax_switch_location',
				'default'     => 'all',
				'options'     => array(
					'all'         => __( 'On all pages', 'tax-switch-for-woocommerce' ),
					'woocommerce' => __( 'Only on WooCommerce pages', 'tax-switch-for-woocommerce' ),
					'prices'      => __( 'Only on pages displaying prices', 'tax-switch-for-woocommerce' ),
				),
				'desc_tip'    => __( 'WooCommerce pages include: product archives/categories, single product pages and account pages.', 'tax-switch-for-woocommerce' ),
				'desc'        => '<strong>' . __( 'On all pages', 'tax-switch-for-woocommerce' ) . ':</strong> ' . __( 'On all pages where a Tax Switch block/shortcode is placed.', 'tax-switch-for-woocommerce' ) . '<br/>' .
								 '<strong>' . __( 'Only on WooCommerce pages', 'tax-switch-for-woocommerce' ) . ':</strong> ' . __( 'Hidden on pages that are not part of the store. Stops rendering completely.', 'tax-switch-for-woocommerce' ) . '<br/>' .
								 '<strong>' . __( 'Only on pages displaying prices', 'tax-switch-for-woocommerce' ) . ':</strong> ' . __( 'Hidden on pages that do not display any prices. Renders, but hides the components (slightly slower).', 'tax-switch-for-woocommerce' ) . '<br/><br/>' .
								 '<strong>' . __( 'Tax Switch blocks/shortcodes are always hidden on the cart and checkout pages.', 'tax-switch-for-woocommerce' ) . '</strong>',
				'desc_at_end' => true,
			),
			array(
				//added in 1.6.0
				'name'     => __( 'Price switching in mini cart', 'tax-switch-for-woocommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Allow customers to switch between prices including and excluding tax in the default WooCommerce mini cart.', 'tax-switch-for-woocommerce' ),
				'desc_tip' => __( 'By default, price switching is disabled in the mini cart to keep cart totals consistent. After changing this setting, empty the mini cart and reload to see the updated behavior (the mini cart markup is cached).', 'tax-switch-for-woocommerce' ),
				'id'       => 'wdevs_tax_switch_enable_mini_cart',
				'default'  => 'no'
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wdevs_tax_switch_section_end'
			),
		);

		return apply_filters( 'wdevs_tax_switch_settings', $settings );
	}

	/**
	 * Get shortcode generator settings fields.
	 *
	 * @return array Array of settings.
	 * @since    1.4.0
	 */
	private function get_shortcode_settings() {
		$settings = array(
			array(
				'name' => __( 'Shortcode settings', 'tax-switch-for-woocommerce' ),
				'type' => 'title',
				'desc' => __( 'Generate a tax switch shortcode.', 'tax-switch-for-woocommerce' ),
				'id'   => 'wdevs_tax_switch_section_title'
			),
			array(
				'type' => 'sectionend',
				'id'   => 'wdevs_tax_switch_section_end'
			),
		);

		return apply_filters( 'wdevs_tax_switch_settings_shortcode', $settings );
	}


	/**
	 * Register string translations
	 *
	 * @since 1.2.1
	 */
	private function register_translations() {
		// Check if WPML is active
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		$incl_text = get_option( 'wdevs_tax_switch_incl_vat' );
		$excl_text = get_option( 'wdevs_tax_switch_excl_vat' );

		do_action( 'wpml_register_single_string', 'tax-switch-for-woocommerce', 'wdevs_tax_switch_incl_vat', $incl_text );
		do_action( 'wpml_register_single_string', 'tax-switch-for-woocommerce', 'wdevs_tax_switch_excl_vat', $excl_text );
	}
}
