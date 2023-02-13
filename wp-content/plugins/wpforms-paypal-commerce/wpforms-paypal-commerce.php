<?php
/**
 * Plugin Name:       WPForms PayPal Commerce
 * Plugin URI:        https://wpforms.com
 * Description:       PayPal Commerce integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Text Domain:       wpforms-paypal-commerce
 * Domain Path:       languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPFormsPaypalCommerce\Plugin;

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_PAYPAL_COMMERCE_VERSION', '1.1.0' );

/**
 * Plugin FILE.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_PAYPAL_COMMERCE_FILE', __FILE__ );

/**
 * Plugin PATH.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_PAYPAL_COMMERCE_PATH', plugin_dir_path( WPFORMS_PAYPAL_COMMERCE_FILE ) );

/**
 * Plugin URL.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_PAYPAL_COMMERCE_URL', plugin_dir_url( WPFORMS_PAYPAL_COMMERCE_FILE ) );

/**
 * Load the plugin files.
 *
 * @since 1.0.0
 */
function wpforms_paypal_commerce_load() {

	// Check requirements.
	if ( ! wpforms_paypal_commerce_required() ) {
		return;
	}

	wpforms_paypal_commerce();
}
add_action( 'wpforms_loaded', 'wpforms_paypal_commerce_load' );

/**
 * Check requirements.
 *
 * @since 1.0.0
 */
function wpforms_paypal_commerce_required() {

	if ( PHP_VERSION_ID < 50600 ) {
		add_action( 'admin_init', 'wpforms_paypal_commerce_deactivation' );
		add_action( 'admin_notices', 'wpforms_paypal_commerce_fail_php_version' );

		return false;
	}

	if ( ! function_exists( 'wpforms' ) ) {
		return false;
	}

	if ( version_compare( wpforms()->version, '1.7.7', '<' ) ) {
		add_action( 'admin_init', 'wpforms_paypal_commerce_deactivation' );
		add_action( 'admin_notices', 'wpforms_paypal_commerce_fail_wpforms_version' );

		return false;
	}

	if (
		! function_exists( 'wpforms_get_license_type' ) ||
		! in_array( wpforms_get_license_type(), [ 'pro', 'agency', 'ultimate', 'elite' ], true )
	) {
		return false;
	}

	return true;
}

/**
 * Deactivate the plugin.
 *
 * @since 1.0.0
 */
function wpforms_paypal_commerce_deactivation() {

	deactivate_plugins( plugin_basename( WPFORMS_PAYPAL_COMMERCE_FILE ) );
}

/**
 * Admin notice for minimum PHP version.
 *
 * @since 1.0.0
 */
function wpforms_paypal_commerce_fail_php_version() {

	echo '<div class="notice notice-error"><p>';
	printf(
		wp_kses( /* translators: %s - WPForms.com documentation page URL. */
			__( 'The WPForms PayPal Commerce plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the PayPal Commerce plugin. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-paypal-commerce' ),
			[
				'a' => [
					'href'   => [],
					'rel'    => [],
					'target' => [],
				],
			]
		),
		esc_url( wpforms_utm_link( 'https://wpforms.com/docs/supported-php-version/', 'all-plugins', 'PayPal Commerce PHP Notice' ) )
	);
	echo '</p></div>';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/**
 * Admin notice for minimum WPForms version.
 *
 * @since 1.0.0
 */
function wpforms_paypal_commerce_fail_wpforms_version() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'The WPForms PayPal Commerce plugin has been deactivated, because it requires WPForms v1.7.7 or later to work.', 'wpforms-paypal-commerce' );
	echo '</p></div>';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
}

/**
 * Get the instance of the `\WPFormsPayPalCommerce\Plugin` class.
 * This function is useful for quickly grabbing data used throughout the plugin.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function wpforms_paypal_commerce() {

	// Actually, load the PayPal Commerce addon now, as we met all the requirements.
	require_once WPFORMS_PAYPAL_COMMERCE_PATH . 'vendor/autoload.php';

	return Plugin::get_instance();
}
