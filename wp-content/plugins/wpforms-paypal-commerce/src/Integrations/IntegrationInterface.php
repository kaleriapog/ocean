<?php

namespace WPFormsPaypalCommerce\Integrations;

/**
 * Interface defines required methods for integrations to work properly.
 *
 * @since 1.0.0
 */
interface IntegrationInterface {

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function allow_load();

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks();

	/**
	 * Determine whether integration page is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_integration_page_loaded();
}
