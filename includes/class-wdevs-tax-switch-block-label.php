<?php

/**
 * The block functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.5.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The Label block functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the Label block functionality.
 * This class is responsible for registering and rendering the tax switch label block and shortcode.
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Block_Label extends Wdevs_Tax_Switch_Block {

	/**
	 * @since 1.5.0
	 */
	public function init_block() {
		$editor_asset = $this->register_script('wdevs-tax-switch-label-editor-script', 'label', 'index');
		register_block_type( plugin_dir_path( dirname( __FILE__ ) ) . 'build/label/block.json', array(
			'editor_script'   => 'wdevs-tax-switch-label-editor-script',
			'render_callback' => [ $this, 'block_render_callback' ]
		) );

		wp_set_script_translations( 'wdevs-tax-switch-label-editor-script', 'tax-switch-for-woocommerce', plugin_dir_path( dirname( __FILE__ ) ) . 'languages' );
	}

	/**
	 * https://developer.woocommerce.com/2021/11/15/how-does-woocommerce-blocks-render-interactive-blocks-in-the-frontend/
	 *
	 * @since 1.5.0
	 */
	public function block_render_callback( $attributes = [], $content = '' ) {
		$should_render = $this->before_component_render();
		if ( ! $should_render ) {
			return '';
		}

		return $this->add_attributes_to_block( $attributes, $content );
	}

	/**
	 * @since 1.5.0
	 */
	public function register_shortcode() {
		add_shortcode( 'wdevs_tax_switch_label', array( $this, 'shortcode_render_callback' ) );
	}

	/**
	 * @since 1.5.0
	 */
	public function set_default_block_attributes($metadata){
		if ( $metadata['name'] === 'wdevs/tax-switch-label' ) {
			$metadata['attributes']['labelTextIncl']['default'] = $this->get_option_text( 'wdevs_tax_switch_incl_vat', __( 'Incl. VAT', 'tax-switch-for-woocommerce' ) );
			$metadata['attributes']['labelTextExcl']['default'] = $this->get_option_text( 'wdevs_tax_switch_excl_vat', __( 'Excl. VAT', 'tax-switch-for-woocommerce' ) );
		}

		return $metadata;
	}

	/**
	 * @since 1.5.0
	 */
	public function shortcode_render_callback( $attributes = [], $content = '' ) {
		$should_render = $this->before_component_render();
		if ( ! $should_render ) {
			return '';
		}

		$attributes = shortcode_atts( [
			'class-name'               => 'is-style-default',
			'label-text-incl'          => $this->get_option_text( 'wdevs_tax_switch_incl_vat', __( 'Incl. VAT', 'tax-switch-for-woocommerce' ) ),
			'label-text-excl'          => $this->get_option_text( 'wdevs_tax_switch_excl_vat', __( 'Excl. VAT', 'tax-switch-for-woocommerce' ) ),
			'label-text-color'         => '',
			'label-text-color-checked' => '',
		], $attributes );

		$container_class_name = 'wp-block-wdevs-tax-switch-label'; // Important for rendering JS.

		return $this->render_shortcode_html( $attributes, $container_class_name, $content );
	}

	/**
	 * @since 1.5.0
	 */
	public function register_frontend_scripts() {
		$script_asset = $this->register_script('wdevs-tax-switch-label-view-script', 'label', 'view');

		wp_register_style(
			'wdevs-tax-switch-label-style',
			plugin_dir_url( dirname( __FILE__ ) ) . 'build/label/style-index.css',
			[],
			$script_asset['version']
		);
	}

	/**
	 * @since 1.5.0
	 */
	public function enqueue_frontend_scripts() {
		if ( wp_style_is( 'wdevs-tax-switch-label-style', 'registered' ) ) {
			wp_enqueue_style( 'wdevs-tax-switch-label-style' );
		}

		if ( wp_script_is( 'wdevs-tax-switch-label-view-script', 'registered' ) ) {
			wp_enqueue_script( 'wdevs-tax-switch-label-view-script' );

			$original_tax_display = $this->get_original_tax_display();
			$check_price_elements = $this->should_hide_on_non_price_pages();

			wp_localize_script(
				'wdevs-tax-switch-label-view-script',
				'wtsViewObject',
				[
					'originalTaxDisplay' => $original_tax_display,
					'checkPriceElements' => $check_price_elements
				]
			);

			wp_set_script_translations(
				'wdevs-tax-switch-label-view-script',
				'tax-switch-for-woocommerce',
				plugin_dir_path( dirname( __FILE__ ) ) . 'languages'
			);
		}
	}
}
