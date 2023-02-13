<?php

namespace WPFormsPaypalCommerce\Admin;

use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use WPForms\Helpers\Transient;

/**
 * PayPal Commerce Connect functionality.
 *
 * @since 1.0.0
 */
class Connect {

	/**
	 * WPForms website URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const WPFORMS_URL = 'https://wpforms.com';

	/**
	 * Disconnect nonce.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const DISCONNECT_ACTION_NONCE = 'wpforms_paypal_commerce_disconnect';

	/**
	 * Onboarding transient name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const ONBOARDING_TRANSIENT_NAME = 'wpforms_paypal_commerce_onboarding_data';

	/**
	 * Onboarding nonce name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const ONBOARDING_NONCE_ACTION = 'wpforms-paypal-commerce-onboarding';

	/**
	 * Uniq option prefix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const STATE_OPTION_PREFIX = 'wpforms_paypal_commerce_uniq_id_';

	/**
	 * Signup transient name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SIGNUP_TRANSIENT_NAME = 'wpforms_paypal_commerce_signup_link_';

	/**
	 * Lock Signup transient name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const LOCK_SIGNUP_TRANSIENT_NAME = 'wpforms_paypal_commerce_lock_signup_link_';

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'handle_actions' ] );
		add_action( 'wp_ajax_wpforms_paypal_commerce_onboarding', [ $this, 'ajax_onboarding' ] );
	}

	/**
	 * Handle actions.
	 *
	 * @since 1.0.0
	 */
	public function handle_actions() {

		if ( ! wpforms_current_user_can() || wp_doing_ajax() || ! wpforms_is_admin_page( 'settings', 'payments' ) ) {
			return;
		}

		if (
			isset( $_GET['merchantId'], $_GET['merchantIdInPayPal'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			$this->handle_connect();

			return;
		}

		if (
			isset( $_GET['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), self::DISCONNECT_ACTION_NONCE )
		) {
			$this->handle_disconnect();

			return;
		}
	}

	/**
	 * Handle connection.
	 *
	 * @since 1.0.0
	 */
	private function handle_connect() {

		$onboarding_data = Transient::get( self::ONBOARDING_TRANSIENT_NAME );

		if ( empty( $onboarding_data['authCode'] ) || empty( $onboarding_data['sharedId'] ) ) {
			return;
		}

		$connection_data = [];

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$connection_data['merchant_id'] = sanitize_text_field( wp_unslash( $_GET['merchantIdInPayPal'] ) );

		$client_credentials = $this->get_client_credentials( $connection_data['merchant_id'], $onboarding_data['authCode'], $onboarding_data['sharedId'] );

		if ( $client_credentials ) {
			$connection_data['partner_merchant_id'] = $client_credentials['partner_merchant_id'];
			$connection_data['client_id']           = $client_credentials['client_id'];
			$connection_data['client_secret']       = $client_credentials['client_secret'];
		}

		$connection = new Connection( $connection_data );
		$connection = self::refresh_access_token( $connection );
		$connection = self::refresh_client_token( $connection );

		$api = wpforms_paypal_commerce()->get_api( $connection );

		if ( $api ) {
			$status = $connection->validate_permissions( $api->get_merchant_info() );

			$connection->set_status( $status )->save();

			Transient::delete( self::ONBOARDING_TRANSIENT_NAME );

			// Sync the settings mode with a connection mode.
			Helpers::set_mode( $connection->get_mode() );
		}

		wp_safe_redirect( Helpers::get_settings_page_url() );
		exit;
	}

	/**
	 * Handle disconnection.
	 *
	 * @since 1.0.0
	 */
	private function handle_disconnect() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$live_mode  = isset( $_GET['live_mode'] ) ? absint( $_GET['live_mode'] ) : 0;
		$mode       = $live_mode ? Helpers::PRODUCTION : Helpers::SANDBOX;
		$connection = Connection::get( $mode );

		if ( $connection ) {
			$connection->delete();
		}

		wp_safe_redirect( Helpers::get_settings_page_url() );
		exit;
	}

	/**
	 * Refresh access token.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Current Connection.
	 *
	 * @return Connection
	 */
	public static function refresh_access_token( $connection ) {

		$api = wpforms_paypal_commerce()->get_api( $connection );

		if ( is_null( $api ) ) {
			return $connection;
		}

		$access_token = $api->generate_access_token();

		if ( $access_token ) {
			$connection->set_access_token( $access_token['access_token'] )->set_access_token_expires_in( time() + $access_token['expires_in'] )->save();
		} else {
			$connection->set_access_token( '' )->set_access_token_expires_in( time() + MINUTE_IN_SECONDS )->save();
		}

		return $connection;
	}

	/**
	 * Refresh client token.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Current Connection.
	 *
	 * @return Connection
	 */
	public static function refresh_client_token( $connection ) {

		$api = wpforms_paypal_commerce()->get_api( $connection );

		if ( is_null( $api ) ) {
			return $connection;
		}

		$client_token = $api->generate_client_token();

		if ( $client_token ) {
			$connection->set_client_token( $client_token['client_token'] )->set_client_token_expires_in( time() + $client_token['expires_in'] )->save();
		} else {
			$connection->set_client_token( '' )->set_client_token_expires_in( time() + ( 5 * MINUTE_IN_SECONDS ) )->save();
		}

		return $connection;
	}

	/**
	 * Save connection data when onboarding is completed.
	 *
	 * @since 1.0.0
	 */
	public function ajax_onboarding() {

		$body = trim( file_get_contents( 'php://input' ) );
		$data = json_decode( $body, true );

		// Security and permissions check.
		if (
			! isset( $data['nonce'] ) ||
			! wpforms_current_user_can() ||
			! wp_verify_nonce( sanitize_key( $data['nonce'] ),self::ONBOARDING_NONCE_ACTION )
		) {
			wp_send_json_error( esc_html__( 'You are not allowed to perform this action.', 'wpforms-paypal-commerce' ) );
		}

		Transient::set( self::ONBOARDING_TRANSIENT_NAME, $data );

		wp_send_json_success();
	}

	/**
	 * Get Connect URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode Connection mode.
	 *
	 * @return string
	 */
	public function get_connect_url( $mode ) {

		$mode = Helpers::validate_mode( $mode );

		if ( Transient::get( self::LOCK_SIGNUP_TRANSIENT_NAME . $mode ) ) {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_GET['paypal_commerce_refresh_signup'] ) ) {
				return '';
			}

			Transient::delete( self::LOCK_SIGNUP_TRANSIENT_NAME . $mode );
		}

		$link = Transient::get( self::SIGNUP_TRANSIENT_NAME . $mode );

		if ( ! empty( $link ) ) {
			return $link;
		}

		$state = uniqid( '', true );

		update_option( self::STATE_OPTION_PREFIX . $mode, $state );

		$response = wp_remote_post(
			$this->get_server_url() . '/oauth/paypal-commerce-connect',
			[
				'body'      => [
					'action'    => 'signup',
					'state'     => $state,
					'site_url'  => wpforms_current_url(),
					'live_mode' => (int) ( $mode === Helpers::PRODUCTION ),
				],
				'sslverify' => false,
				'timeout'   => 30,
			]
		);

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			$link = isset( $body['links'][1]['href'] ) ? $body['links'][1]['href'] . '&displayMode=minibrowser' : '';

			if ( $link ) {
				Transient::set( self::SIGNUP_TRANSIENT_NAME . $mode, $link, $body['expires_in'] );

				return $link;
			}
		}

		Transient::set( self::LOCK_SIGNUP_TRANSIENT_NAME . $mode, true, HOUR_IN_SECONDS );

		return '';
	}

	/**
	 * Retrieve the disconnect URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode Connection mode.
	 *
	 * @return string
	 */
	public function get_disconnect_url( $mode ) {

		$mode = Helpers::validate_mode( $mode );
		$url  = add_query_arg(
			[
				'action'    => self::DISCONNECT_ACTION_NONCE,
				'live_mode' => absint( $mode === Helpers::PRODUCTION ),
			],
			Helpers::get_settings_page_url()
		);

		return wp_nonce_url( $url, self::DISCONNECT_ACTION_NONCE );
	}

	/**
	 * Retrieve a connect server URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_server_url() {

		// Use local server if constant set.
		if ( defined( 'WPFORMS_PAYPAL_COMMERCE_LOCAL_CONNECT_SERVER' ) && WPFORMS_PAYPAL_COMMERCE_LOCAL_CONNECT_SERVER ) {
			return home_url();
		}

		return self::WPFORMS_URL;
	}

	/**
	 * Get Client Credentials.
	 *
	 * @since 1.0.0
	 *
	 * @param string $merchant_id Merchant ID.
	 * @param string $code        Client code.
	 * @param string $shared_id   Client Share ID.
	 *
	 * @return array
	 */
	private function get_client_credentials( $merchant_id, $code, $shared_id ) {

		$response = wp_remote_post(
			$this->get_server_url() . '/oauth/paypal-commerce-connect',
			[
				'body'      => [
					'action'      => 'client_credentials',
					'state'       => get_option( self::STATE_OPTION_PREFIX . Helpers::get_mode() ),
					'merchant_id' => $merchant_id,
					'code'        => $code,
					'shared_id'   => $shared_id,
					'live_mode'   => Helpers::is_production_mode(),
				],
				'sslverify' => false,
				'timeout'   => 30,
			]
		);

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			return is_array( $body ) ? $body : [];
		}

		return [];
	}
}
