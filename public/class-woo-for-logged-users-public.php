<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/Rahmon/
 * @since      1.0.0
 *
 * @package    Woo_For_Logged_Users
 * @subpackage Woo_For_Logged_Users/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_For_Logged_Users
 * @subpackage Woo_For_Logged_Users/public
 * @author     Rahmon <ra@gmail.com>
 */
class Woo_For_Logged_Users_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the redirect for users not logged.
	 *
	 * @since 1.0.0
	 */
	public function redirect_not_logged_users() {
		if ( ! is_user_logged_in() && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
			wp_safe_redirect( site_url( 'my-account/' ) );
			exit;
		}
	}
}
