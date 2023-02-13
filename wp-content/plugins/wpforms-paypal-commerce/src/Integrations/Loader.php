<?php

namespace WPFormsPaypalCommerce\Integrations;

/**
 * Main loader.
 *
 * @since 1.0.0
 */
class Loader {

	/**
	 * Loaded integrations.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $integrations = [];

	/**
	 * Get the instance of a class and store it in itself.
	 *
	 * @since 1.0.0
	 *
	 * @return Loader
	 */
	public function get_instance() {

		static $instance;

		if ( ! $instance ) {
			$instance = new Loader();
		}

		return $instance;
	}

	/**
	 * Loader constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$class_names = [
			'Divi',
			'Elementor',
			'BlockEditor',
		];

		foreach ( $class_names as $class_name ) {
			$integration = $this->register_class( $class_name );

			if ( $integration !== null ) {
				$this->load_integration( $integration );
			}
		}
	}

	/**
	 * Register a new class.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name Class name to register.
	 *
	 * @return IntegrationInterface Instance of class.
	 */
	private function register_class( $class_name ) {

		$class_name = 'WPFormsPaypalCommerce\Integrations\\' . sanitize_text_field( $class_name );

		return class_exists( $class_name ) ? new $class_name() : null;
	}

	/**
	 * Load an integration.
	 *
	 * @param IntegrationInterface $integration Instance of an integration class.
	 *
	 * @since 1.0.0
	 */
	private function load_integration( IntegrationInterface $integration ) {

		if ( ! $integration->allow_load() ) {
			return;
		}

		$integration->hooks();

		$this->integrations[] = $integration;
	}

	/**
	 * Indicate if an integration page is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_integration_page_loaded() {

		static $loaded;

		if ( $loaded !== null ) {
			return $loaded;
		}

		$loaded = false;

		foreach ( $this->integrations as $integration ) {

			if ( $integration->is_integration_page_loaded() ) {
				$loaded = true;

				break;
			}
		}

		return $loaded;
	}
}
