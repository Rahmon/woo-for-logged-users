<?php
/**
 * Plugin Name:       WooCommerce for Logged-in Users
 * Plugin URI:        https://github.com/Rahmon/woo-for-logged-users
 * Description:       Set your WooCommerce Shop only for logged-in users. Just active.
 * Version:           1.3.0
 * Author:            Rahmon
 * Author URI:        https://github.com/Rahmon/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-for-logged-in-users
 * Domain Path:       /languages
 *
 * @package WooCommerce_For_Logged_In_Users
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
	global $wflu_settings, $wflu_redirect_page_option;

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

		if ( is_user_logged_in() ) {
			return;
		}

		/**
		 * Filter arguments used to retrieve products from database
		 *
		 * @since 1.3.0
		 * @hook wflu_should_redirect_not_logged_in_user
		 * @param bool $should_redirect Whether the not logged-in user should be redirect. Default: is_woocommerce() || is_cart() || is_checkout().
		 * @return bool New value
		 */
		$should_redirect_not_logged_in_user = apply_filters( 'wflu_should_redirect_not_logged_in_user', is_woocommerce() || is_cart() || is_checkout() );

		if ( ! $should_redirect_not_logged_in_user ) {
			return;
		}

		$options = get_option( $wflu_settings );

		$redirect_page = empty( $options[ $wflu_redirect_page_option ] )
			? wc_get_page_permalink( 'myaccount' )
			: get_permalink( $options[ $wflu_redirect_page_option ] );

		/**
		 * Filter the redirect page URL
		 *
		 * @since 1.3.0
		 * @hook wflu_redirect_page_url
		 * @param string $redirect_page_url The page permalink URL.
		 * @return string New value.
		 */
		$redirect_page = apply_filters( 'wflu_redirect_page_url', $redirect_page );

		wp_safe_redirect( $redirect_page );
		exit;
	}
}
add_action( 'template_redirect', 'redirect_not_logged_users' );

/**
 * Redirect after login
 *
 * @param string $redirect_to Default WooCommerce redirect page.
 */
function redirect_after_login( $redirect_to ) {
	global $wflu_settings, $wflu_redirect_page_after_login_option;

	$options = get_option( $wflu_settings );

	if ( ! empty( $options[ $wflu_redirect_page_after_login_option ] ) ) {
		$redirect_to_option = get_permalink( $options[ $wflu_redirect_page_after_login_option ] );

		if ( $redirect_to_option ) {
			$redirect_to = $redirect_to_option;
		}
	}

	/**
	 * Filter the redirect after login page URL
	 *
	 * @since 1.2.4
	 * @hook wflu_redirect_after_login_page_url
	 * @param string $redirect_after_login_page_url The after login page permalink URL.
	 * @return string New value.
	 */
	$redirect_to = apply_filters( 'wflu_redirect_after_login_page_url', $redirect_to );

	return $redirect_to;
}
add_filter( 'woocommerce_login_redirect', 'redirect_after_login' );
