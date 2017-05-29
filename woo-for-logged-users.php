<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Rahmon/
 * @since             1.0.0
 * @package           Woo_For_Logged_Users
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce for Logged Users
 * Plugin URI:        https://github.com/Rahmon/woo-for-logged-users
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Rahmon
 * Author URI:        https://github.com/Rahmon/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-for-logged-users
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-for-logged-users-activator.php
 */
function activate_woo_for_logged_users() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-for-logged-users-activator.php';
	Woo_For_Logged_Users_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-for-logged-users-deactivator.php
 */
function deactivate_woo_for_logged_users() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-for-logged-users-deactivator.php';
	Woo_For_Logged_Users_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_for_logged_users' );
register_deactivation_hook( __FILE__, 'deactivate_woo_for_logged_users' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-for-logged-users.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_for_logged_users() {

	$plugin = new Woo_For_Logged_Users();
	$plugin->run();

}
run_woo_for_logged_users();
