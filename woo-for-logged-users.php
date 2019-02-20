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

add_action( 'admin_menu', 'wflu_add_admin_menu' );
add_action( 'admin_init', 'wflu_settings_init' );

function wflu_add_admin_menu(  ) { 

	add_submenu_page( 'woocommerce', 'WooCommerce for logged users', 'WooCommerce for logged users', 'manage_options', 'woocommerce_for_logged_users', 'wflu_options_page' );

}

function wflu_settings_init(  ) { 

	register_setting( 'wooForLoggedUsersPage', 'wflu_settings' );

	add_settings_section(
		'wflu_pluginPage_section', 
		__( 'Settings', 'woo-for-logged-users' ), 
		null, 
		'wooForLoggedUsersPage'
	);

	add_settings_field( 
		'wflu_checkbox_redirect_to_shop_after_login', 
		__( 'Redirect to shop after login (default is to "My account" page)', 'woo-for-logged-users' ), 
		'wflu_checkbox_redirect_to_shop_after_login_render', 
		'wooForLoggedUsersPage', 
		'wflu_pluginPage_section' 
	);

}

function wflu_checkbox_redirect_to_shop_after_login_render(  ) { 

	$options = get_option( 'wflu_settings' );
	$checkbox_value = false;

	if ( ! empty( $options ) ) {
		$checkbox_value = $options['wflu_checkbox_redirect_to_shop_after_login'];
	} 

	?>
	<input type='checkbox' name='wflu_settings[wflu_checkbox_redirect_to_shop_after_login]' <?php checked( $checkbox_value, 1 ); ?> value='1'>
	<?php

}

function wflu_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>WooCommerce for logged users</h2>

		<?php
		settings_fields( 'wooForLoggedUsersPage' );
		do_settings_sections( 'wooForLoggedUsersPage' );
		submit_button();
		?>

	</form>
	<?php

}

/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	// function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woo-for-logged-users',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	// }

function redirect_not_logged_users() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		if ( ! is_user_logged_in() && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}
}

function redirect_after_login( $redirect_to ) {

	$options = get_option( 'wflu_settings' );
	$redirect_after_login = false;

	if ( ! empty( $options ) ) {
		$redirect_after_login = $options['wflu_checkbox_redirect_to_shop_after_login'];

		if ( $redirect_after_login ) {
			$shop_page = wc_get_page_permalink( 'shop' );
			$redirect_to = $shop_page;
		}
	} 

	return $redirect_to;
}

add_action( 'template_redirect', 'redirect_not_logged_users' );
add_filter( 'woocommerce_login_redirect', 'redirect_after_login');