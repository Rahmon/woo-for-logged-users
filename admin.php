<?php
/**
 * Admin page
 *
 * @package WooCommerce_For_Logged_In_Users
 */

$wflu_settings = 'wflu_settings';

$wflu_redirect_page_option             = 'wflu_redirect_page_option';
$wflu_redirect_page_after_login_option = 'wflu_redirect_page_after_login_option';

/**
 * Enqueue scripts to admin
 *
 * @param string $hook The current admin page.
 */
function wflu_enqueue_admin( $hook ) {
	if ( 'woocommerce_page_woocommerce_for_logged_users' !== $hook ) {
		return;
	}

	$wflu_admin = 'wooCommerceForLoggedInUsersAdmin';

	wp_enqueue_script(
		$wflu_admin,
		plugins_url( 'dist/index.js', __FILE__ ),
		array( 'wp-i18n' ),
		'1.0.0',
		true
	);

	wp_localize_script(
		$wflu_admin,
		'wfluSettings',
		array(
			'restURL'        => rest_url( '/' ),
			'shopPageId'     => wc_get_page_id( 'shop' ),
			'cartPageId'     => wc_get_page_id( 'cart' ),
			'checkoutPageId' => wc_get_page_id( 'checkout' ),
		)
	);

}
add_action( 'admin_enqueue_scripts', 'wflu_enqueue_admin' );

/**
 * Add submenu in WooCommerce -> WooCommerce for logged users
 */
function wflu_add_admin_menu() {
	add_submenu_page( 'woocommerce', 'WooCommerce for logged-in users', 'WooCommerce for logged-in users', 'manage_options', 'woocommerce_for_logged_users', 'wflu_options_page' );
}
add_action( 'admin_menu', 'wflu_add_admin_menu' );

/**
 * Add settings field
 */
function wflu_settings_init() {
	global $wflu_settings;

	register_setting( 'wooForLoggedUsersPage', $wflu_settings );
}
add_action( 'admin_init', 'wflu_settings_init' );

/**
 * Render "Redirect to shop after login" checkbox field
 */
function wflu_checkbox_redirect_to_shop_after_login_render() {
	global $wflu_settings;

	$options        = get_option( $wflu_settings );
	$checkbox_value = false;

	if ( ! empty( $options ) ) {
		$checkbox_value = $options['wflu_checkbox_redirect_to_shop_after_login'];
	}

	?>
	<input type='checkbox' name='wflu_settings[wflu_checkbox_redirect_to_shop_after_login]' <?php checked( $checkbox_value, 1 ); ?> value='1'>
	<?php
}

/**
 * Render form of settings page
 */
function wflu_options_page() {
	?>
	<div id="wflu-admin"></div>
	<?php
}

/**
 * Get the settings
 */
function wflu_get_settings() {
	global $wflu_settings, $wflu_redirect_page_option, $wflu_redirect_page_after_login_option;

	$my_account_page_id = wc_get_page_id( 'myaccount' );

	$my_account_page = array(
		'value' => -1 !== $my_account_page_id ? $my_account_page_id : '',
		'label' => -1 !== $my_account_page_id ? esc_html( get_the_title( $my_account_page_id ) ) : '',
	);

	$default_value = array(
		$wflu_redirect_page_option             => $my_account_page,
		$wflu_redirect_page_after_login_option => $my_account_page,
	);

	$options = get_option( $wflu_settings );

	if ( $options ) {
		if ( isset( $options[ $wflu_redirect_page_option ] ) && isset( $options[ $wflu_redirect_page_after_login_option ] ) ) {
			$options_value = array();

			if ( get_the_title( $options[ $wflu_redirect_page_option ] ) ) {
				$options_value[ $wflu_redirect_page_option ] = array(
					'value' => $options[ $wflu_redirect_page_option ],
					'label' => esc_html( get_the_title( $options[ $wflu_redirect_page_option ] ) ),
				);
			}

			if ( get_the_title( $options[ $wflu_redirect_page_option ] ) ) {
				$options_value[ $wflu_redirect_page_after_login_option ] = array(
					'value' => $options[ $wflu_redirect_page_after_login_option ],
					'label' => esc_html( get_the_title( $options[ $wflu_redirect_page_after_login_option ] ) ),
				);
			}

			if ( ! empty( $options_value ) ) {
				return array_merge( $default_value, $options_value );
			}
		}
	}

	return $default_value;
}

/**
 * Set the settings
 *
 * @param WP_REST_Request $request REST request object.
 */
function wflu_set_settings( $request ) {
	global $wflu_settings, $wflu_redirect_page_option, $wflu_redirect_page_after_login_option;

	$option_name    = $wflu_settings;
	$current_values = get_option( $option_name );

	$redirect_page_value                = sanitize_text_field( $request->get_param( $wflu_redirect_page_option ) );
	$redirect_to_shop_after_login_value = sanitize_text_field( $request->get_param( $wflu_redirect_page_after_login_option ) );

	if ( ! empty( $redirect_page_value ) && ! empty( $redirect_to_shop_after_login_value ) ) {
		$new_values = array(
			$wflu_redirect_page_option             => $redirect_page_value,
			$wflu_redirect_page_after_login_option => $redirect_to_shop_after_login_value,
		);

		if ( false !== $current_values ) {
			// phpcs:ignore
			if ( $current_values == $new_values ) {
				$response = true;
			} else {
				$response = update_option( $option_name, $new_values );
			}
		} else {
			$response = add_option(
				$option_name,
				$new_values
			);
		}

		if ( $response ) {
			return new WP_REST_Response( true, 200 );
		}
	}

	return new WP_Error( 'cant-update', __( 'Something went wrong. The settings were not updated.', 'woo-for-logged-in-users' ) );
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'wflu/v1',
			'/settings',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'wflu_get_settings',
			)
		);

		register_rest_route(
			'wflu/v1',
			'/settings',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => 'wflu_set_settings',
			)
		);
	}
);
