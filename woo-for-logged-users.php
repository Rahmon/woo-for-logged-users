<?php

/**
 * Plugin Name:       WooCommerce for Logged Users
 * Plugin URI:        https://github.com/Rahmon/woo-for-logged-users
 * Description:       Set your WooCommerce Shop only for logged users. Just active.
 * Version:           1.1.0
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
 * Settings page
 */
require_once dirname( __FILE__ ) . '/admin.php';

/**
* Load the plugin text domain for translation.
*/
load_plugin_textdomain(
	'woo-for-logged-users',
	false,
	dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
);

/**
 * Redirect when the user is not logged
 */
function redirect_not_logged_users() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		if ( ! is_user_logged_in() && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'redirect_not_logged_users' );

/**
 * Redirect after login
 */
function redirect_after_login( $redirect_to ) {
	$options = get_option( 'wflu_settings' );

	$redirect_after_login = false;
	if ( ! empty( $options ) ) {
		$redirect_after_login = $options['wflu_checkbox_redirect_to_shop_after_login'];

		if ( $redirect_after_login ) {
			$shop_page   = wc_get_page_permalink( 'shop' );
			$redirect_to = $shop_page;
		}
	}

	return $redirect_to;
}
add_filter( 'woocommerce_login_redirect', 'redirect_after_login' );
