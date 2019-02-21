<?php

/**
 * Add submenu in WooCommerce -> WooCommerce for logged users
 */
function wflu_add_admin_menu() {
	add_submenu_page( 'woocommerce', 'WooCommerce for logged users', 'WooCommerce for logged users', 'manage_options', 'woocommerce_for_logged_users', 'wflu_options_page' );
}
add_action( 'admin_menu', 'wflu_add_admin_menu' );

/**
 * Add settings field
 */
function wflu_settings_init() {
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
add_action( 'admin_init', 'wflu_settings_init' );

/**
 * Render "Redirect to shop after login" checkbox field
 */
function wflu_checkbox_redirect_to_shop_after_login_render() {
	$options        = get_option( 'wflu_settings' );
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
