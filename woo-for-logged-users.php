<?php
/**
 * Plugin Name:       WooCommerce for Logged-in Users
 * Plugin URI:        https://github.com/Rahmon/woo-for-logged-users
 * Description:       Set your WooCommerce Shop only for logged-in users. Just active.
 * Version:           1.2.2
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

		if ( ! is_user_logged_in() && ( is_woocommerce() || is_cart() || is_checkout() ) ) {

			$options = get_option( $wflu_settings );

			if ( ! empty( $options ) ) {
				$redirect_page_value = $options[ $wflu_redirect_page_option ];

				if ( $redirect_page_value ) {
					wp_safe_redirect( get_permalink( $redirect_page_value ) );
					exit;
				}
			}

			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
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

	if ( ! empty( $options ) ) {
		$redirect_after_login = $options[ $wflu_redirect_page_after_login_option ];

		if ( $redirect_after_login ) {
			$shop_page   = get_permalink( $redirect_after_login );
			$redirect_to = $shop_page;
		}
	}

	return $redirect_to;
}
add_filter( 'woocommerce_login_redirect', 'redirect_after_login' );
