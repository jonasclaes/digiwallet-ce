<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/jonasclaes/digiwallet-ce/
 * @since             0.0.1
 * @package           DigiWalletCE
 *
 * @wordpress-plugin
 * Plugin Name:       DigiWallet Community Edition
 * Plugin URI:        https://github.com/jonasclaes/digiwallet-ce/
 * Description:       DigiWallet CE plugin enables users to pay with Bancontact, iDEAL, SEPA...
 * Version:           0.0.1
 * Author:            Jonas Claes
 * Author URI:        https://github.com/jonasclaes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digiwallet-ce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.0.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DIGIWALLETCE_VERSION', '0.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/DigiWalletCEActivator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/DigiWalletCEActivator.php';
	\DigiWalletCE\DigiWalletCEActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/DigiWalletCEDeactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/DigiWalletCEDeactivator.php';
	\DigiWalletCE\DigiWalletCEDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/DigiWalletCE.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_plugin_name() {

	$plugin = new DigiWalletCE\DigiWalletCE();
	$plugin->run();

}
run_plugin_name();
