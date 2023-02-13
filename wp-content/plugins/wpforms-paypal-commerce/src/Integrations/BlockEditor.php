<?php

namespace WPFormsPaypalCommerce\Integrations;

/**
 * Integration with Block Editor.
 *
 * @since 1.0.0
 */
class BlockEditor implements IntegrationInterface {

	/**
	 * Handle name for wp_register_styles handle.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	const HANDLE = 'wpforms-paypal-commerce-integrations';

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_load() {

		return true;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		// Field styles for Gutenberg.
		add_action( 'enqueue_block_editor_assets', [ $this, 'gutenberg_enqueues' ] );

		// Set editor style for block type editor. Must run at 20 in add-ons.
		add_filter( 'register_block_type_args', [ $this, 'block_editor_assets' ], 20, 2 );
	}

	/**
	 * Determine whether integration page is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_integration_page_loaded() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit';
	}

	/**
	 * Load enqueues for the Gutenberg editor.
	 *
	 * @since 1.1.0
	 */
	public function gutenberg_enqueues() {

		if ( version_compare( get_bloginfo( 'version' ), '5.5', '>=' ) ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			self::HANDLE,
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/integrations/integrations-paypal-commerce{$min}.css",
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);
	}

	/**
	 * Set editor style for block type editor.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args       Array of arguments for registering a block type.
	 * @param string $block_type Block type name including namespace.
	 *
	 * @return array
	 */
	public function block_editor_assets( $args, $block_type ) {

		if ( $block_type !== 'wpforms/form-selector' || ! is_admin() ) {
			return $args;
		}

		// Do not include styles if the "Include Form Styling > No Styles" is set.
		if ( wpforms_setting( 'disable-css', '1' ) === '3' ) {
			return $args;
		}

		$min = wpforms_get_min_suffix();

		wp_register_style(
			self::HANDLE,
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/integrations/integrations-paypal-commerce{$min}.css",
			[ $args['editor_style'] ],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);

		$args['editor_style'] = self::HANDLE;

		return $args;
	}
}
