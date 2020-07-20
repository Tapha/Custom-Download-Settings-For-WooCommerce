<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tapha.xyz
 * @since             1.0.0
 * @package           Custom_Download_Settings
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Download Settings
 * Plugin URI:        https://tapha.xyz/cds
 * Description:       Set custom download preferences for individual WooCommerce products.
 * Version:           1.0.0
 * Author:            E M Ngum
 * Author URI:        https://tapha.xyz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-download-settings
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-custom-download-settings-activator.php
 */
function activate_custom_download_settings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-download-settings-activator.php';
	Custom_Download_Settings_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-custom-download-settings-deactivator.php
 */
function deactivate_custom_download_settings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-download-settings-deactivator.php';
	Custom_Download_Settings_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_custom_download_settings' );
register_deactivation_hook( __FILE__, 'deactivate_custom_download_settings' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-custom-download-settings.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_custom_download_settings() {

	/**
	 * Check if WooCommerce is active
	 **/
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	    // Run the plugin
	    $plugin = new Custom_Download_Settings();
	    $plugin->run();
	}

}
run_custom_download_settings();
