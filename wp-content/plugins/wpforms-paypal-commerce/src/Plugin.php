<?php

namespace WPFormsPaypalCommerce;

use WPForms_Updater;

/**
 * WPForms PayPal Commerce main class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Payment slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'paypal_commerce';

	/**
	 * Connect handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Admin\Connect
	 */
	private $connect;

	/**
	 * Integrations loader instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Integrations\Loader
	 */
	private $integrations;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public function init() {

		$this->hooks();

		return $this;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		add_action( 'wpforms_loaded', [ $this, 'setup' ], 15 );
		add_action( 'wpforms_updater', [ $this, 'updater' ] );
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	public function setup() {

		$this->load_admin_entries();
		$this->load_connect();
		$this->load_field();
		$this->load_builder();
		$this->load_settings();
		$this->load_frontend();
		$this->load_processing();
		$this->load_integrations();
	}

	/**
	 * Load admin entries functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_admin_entries() {

		if ( wpforms_is_admin_page( 'entries' ) ) {
			( new Admin\Entries() )->hooks();
		}
	}

	/**
	 * Load settings page functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_settings() {

		if ( wpforms_is_admin_page( 'settings', 'payments' ) ) {
			( new Admin\Settings() )->hooks();
		}
	}

	/**
	 * Load connect handler.
	 *
	 * @since 1.0.0
	 */
	private function load_connect() {

		$this->connect = new Admin\Connect();

		$this->connect->hooks();
	}

	/**
	 * Load field functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_field() {

		( new Fields\PaypalCommerce() );
	}

	/**
	 * Load builder functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_builder() {

		if ( wp_doing_ajax() || wpforms_is_admin_page( 'builder' ) ) {
			( new Admin\PaypalCommercePayment() );
			( new Admin\Builder() )->hooks();
		}
	}

	/**
	 * Load frontend functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_frontend() {

		if ( ! is_admin() ) {
			( new Frontend() )->hooks();
		}
	}

	/**
	 * Load processing functionality.
	 *
	 * @since 1.0.0
	 */
	private function load_processing() {

		if ( ! is_admin() || wpforms_is_frontend_ajax() ) {
			( new Process\Process() )->hooks();
		}
	}

	/**
	 * Load integrations.
	 *
	 * @since 1.0.0
	 */
	private function load_integrations() {

		$this->integrations = new Integrations\Loader();
	}

	/**
	 * Retrieve integrations loader.
	 *
	 * @since 1.0.0
	 *
	 * @return Integrations\Loader
	 */
	public function get_integrations() {

		return $this->integrations;
	}

	/**
	 * Load a plugin updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms PayPal Commerce',
				'plugin_slug' => 'wpforms-paypal-commerce',
				'plugin_path' => plugin_basename( WPFORMS_PAYPAL_COMMERCE_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_PAYPAL_COMMERCE_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_PAYPAL_COMMERCE_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Retrieve a connect handler.
	 *
	 * @since 1.0.0
	 *
	 * @return Admin\Connect
	 */
	public function get_connect() {

		return $this->connect;
	}

	/**
	 * Retrieve an API instance.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Connection object.
	 *
	 * @return Api\Api|null
	 */
	public function get_api( $connection ) {

		if ( ! $connection instanceof Connection ) {
			return null;
		}

		return new Api\Api( $connection );
	}

	/**
	 * Retrieve a single instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance() {

		static $instance = null;

		if ( ! $instance instanceof self ) {
			$instance = ( new self() )->init();
		}

		return $instance;
	}
}
