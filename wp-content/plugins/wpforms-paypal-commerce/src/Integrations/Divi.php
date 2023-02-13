<?php

namespace WPFormsPaypalCommerce\Integrations;

/**
 * Integration with Divi.
 *
 * @since 1.0.0
 */
class Divi implements IntegrationInterface {

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_load() {

		if ( function_exists( 'et_divi_builder_init_plugin' ) ) {
			return true;
		}

		$allow_themes = [ 'Divi', 'Extra' ];
		$theme        = wp_get_theme();
		$theme_name   = $theme->get_template();
		$theme_parent = $theme->parent();

		return (bool) array_intersect( [ $theme_name, $theme_parent ], $allow_themes );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_frontend_css', [ $this, 'frontend_styles' ], 12 );

		if ( $this->is_integration_page_loaded() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'builder_styles' ], 12 );
		}
	}

	/**
	 * Determine whether integration page is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_integration_page_loaded() {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		return ! empty( $_GET['et_fb'] ) || ( ! empty( $_POST['action'] ) && $_POST['action'] === 'wpforms_divi_preview' );
	}

	/**
	 * Load builder styles.
	 *
	 * @since 1.0.0
	 */
	public function builder_styles() {

		// Do not include styles if the "Include Form Styling > No Styles" is set.
		if ( wpforms_setting( 'disable-css', '1' ) === '3' ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-paypal-commerce-divi-editor-integrations',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/integrations/divi-editor-paypal-commerce{$min}.css",
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);
	}

	/**
	 * Load frontend styles.
	 *
	 * @since 1.0.0
	 */
	public function frontend_styles() {

		if ( ! $this->is_divi_plugin_loaded() ) {
			return;
		}

		// Do not include styles if the "Include Form Styling > No Styles" is set.
		if ( wpforms_setting( 'disable-css', '1' ) === '3' ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-paypal-commerce-divi-frontend-integrations',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/integrations/divi-frontend-paypal-commerce{$min}.css",
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);
	}

	/**
	 * Determine if the Divi Builder plugin is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_divi_plugin_loaded() {

		if ( ! is_singular() ) {
			return false;
		}

		return function_exists( 'et_is_builder_plugin_active' ) && et_is_builder_plugin_active();
	}
}
