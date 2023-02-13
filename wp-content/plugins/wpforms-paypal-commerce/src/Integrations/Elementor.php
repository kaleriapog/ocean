<?php

namespace WPFormsPaypalCommerce\Integrations;

use Elementor\Plugin as ElementorPlugin;

/**
 * Integration with Elementor.
 *
 * @since 1.0.0
 */
class Elementor implements IntegrationInterface {

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_load() {

		return (bool) did_action( 'elementor/loaded' );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_editor_assets' ] );
	}

	/**
	 * Determine whether integration page is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_integration_page_loaded() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
		return ( ! empty( $_POST['action'] ) && $_POST['action'] === 'elementor_ajax' ) || ( ! empty( $_GET['action'] ) && $_GET['action'] === 'elementor' );
	}

	/**
	 * Set editor style for block type editor.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0
	 *
	 * @param array  $args       Array of arguments for registering a block type.
	 * @param string $block_type Block type name including namespace.
	 *
	 * @return array
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function block_editor_assets( $args, $block_type ) {

		_deprecated_function( __METHOD__, '1.1.0 of the PayPal Commerce addon.' );

		return $args;
	}

	/**
	 * Load editor assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets() {

		if ( ! ElementorPlugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		// Do not include styles if the "Include Form Styling > No Styles" is set.
		if ( wpforms_setting( 'disable-css', '1' ) === '3' ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-paypal-commerce-elementor-editor-integrations',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/integrations/elementor-editor-paypal-commerce{$min}.css",
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);
	}
}
