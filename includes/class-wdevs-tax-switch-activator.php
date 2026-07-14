<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tax_Switch_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );

			wp_die( esc_html__( 'This plugin requires WooCommerce. Please install and activate WooCommerce before activating this plugin.', 'tax-switch-for-woocommerce' ) );
		}
	}

}
