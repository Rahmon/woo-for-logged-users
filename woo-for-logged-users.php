<?php

/**
 * @link              https://github.com/Rahmon/woo-for-logged-users
 * @since             1.0.0
 * @package           Woo_For_Logged_Users
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce for Logged Users
 * Plugin URI:        https://github.com/Rahmon/woo-for-logged-users
 * Description:       Set your WooCommerce Shop only for logged users. Just active.
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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-for-logged-users.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_woo_for_logged_users() {

	$plugin = new Woo_For_Logged_Users();
	$plugin->run();

}
run_woo_for_logged_users();
