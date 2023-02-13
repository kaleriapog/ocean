<?php

namespace WPFormsPaypalCommerce;

/**
 * Connection class.
 *
 * @since 1.0.0
 */
class Connection {

	/**
	 * Valid connection status.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const STATUS_VALID = 'valid';

	/**
	 * Invalid connection status.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const STATUS_INVALID = 'invalid';

	/**
	 * Partner ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const PARTNER_ID = 'AwesomeMotive_SP_PPCP';

	/**
	 * Determine if a connection for production mode.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $is_live_mode;

	/**
	 * Client token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Date when access tokens should be renewed.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $access_token_expires_in;

	/**
	 * Client token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $client_token;

	/**
	 * Date when client tokens should be renewed.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $client_token_expires_in;

	/**
	 * Connection status.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $status;

	/**
	 * ID of an application.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Client secret of an application.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $client_secret;

	/**
	 * ID of the partner merchant.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $partner_merchant_id;

	/**
	 * ID of the merchant.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $merchant_id;

	/**
	 * Connection constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Connection data.
	 */
	public function __construct( $data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$data = (array) $data;

		if ( ! empty( $data['access_token'] ) ) {
			$this->access_token = $data['access_token'];
		}

		if ( ! empty( $data['access_token_expires_in'] ) ) {
			$this->access_token_expires_in = $data['access_token_expires_in'];
		}

		if ( ! empty( $data['client_token'] ) ) {
			$this->client_token = $data['client_token'];
		}

		if ( ! empty( $data['client_token_expires_in'] ) ) {
			$this->client_token_expires_in = $data['client_token_expires_in'];
		}

		if ( ! empty( $data['client_id'] ) ) {
			$this->client_id = $data['client_id'];
		}

		if ( ! empty( $data['client_secret'] ) ) {
			$this->client_secret = $data['client_secret'];
		}

		if ( ! empty( $data['partner_merchant_id'] ) ) {
			$this->partner_merchant_id = $data['partner_merchant_id'];
		}

		if ( ! empty( $data['merchant_id'] ) ) {
			$this->merchant_id = $data['merchant_id'];
		}

		$this->is_live_mode = Helpers::is_production_mode();

		$this->set_status( empty( $data['status'] ) ? self::STATUS_VALID : $data['status'] );
	}

	/**
	 * Retrieve a connection instance if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return Connection|null
	 */
	public static function get( $mode = '' ) {

		$mode        = Helpers::validate_mode( $mode );
		$connections = (array) get_option( 'wpforms_paypal_commerce_connections', [] );

		if ( empty( $connections[ $mode ] ) ) {
			return null;
		}

		return new self( (array) $connections[ $mode ] );
	}

	/**
	 * Save connection data into DB.
	 *
	 * @since 1.0.0
	 */
	public function save() {

		$connections = $this->get_connections();

		$connections[ $this->get_mode() ] = $this->get_data();

		$this->update_connections( $connections );
	}

	/**
	 * Delete connection data from DB.
	 *
	 * @since 1.0.0
	 */
	public function delete() {

		$connections = $this->get_connections();

		unset( $connections[ $this->get_mode() ] );

		empty( $connections ) ? delete_option( 'wpforms_paypal_commerce_connections' ) : $this->update_connections( $connections );
	}

	/**
	 * Retrieve a connection in array format, similar to `toArray` method.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_data() {

		return [
			'access_token'            => $this->access_token,
			'access_token_expires_in' => $this->access_token_expires_in,
			'client_token'            => $this->client_token,
			'client_token_expires_in' => $this->client_token_expires_in,
			'client_id'               => $this->client_id,
			'client_secret'           => $this->client_secret,
			'partner_merchant_id'     => $this->partner_merchant_id,
			'merchant_id'             => $this->merchant_id,
			'status'                  => $this->status,
		];
	}

	/**
	 * Retrieve a connection mode.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_mode() {

		return $this->is_live_mode ? Helpers::PRODUCTION : Helpers::SANDBOX;
	}

	/**
	 * Retrieve access token.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		return $this->access_token;
	}

	/**
	 * Set access token.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token Token.
	 *
	 * @return Connection
	 */
	public function set_access_token( $token ) {

		$this->access_token = $token;

		return $this;
	}

	/**
	 * Get access token expires in time.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_access_token_expires_in() {

		return $this->access_token_expires_in;
	}

	/**
	 * Set access token expires in time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $expires_in Expires in time.
	 *
	 * @return Connection
	 */
	public function set_access_token_expires_in( $expires_in ) {

		$this->access_token_expires_in = $expires_in;

		return $this;
	}

	/**
	 * Retrieve client token.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_token() {

		return $this->client_token;
	}

	/**
	 * Set client token.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token Token.
	 *
	 * @return Connection
	 */
	public function set_client_token( $token ) {

		$this->client_token = $token;

		return $this;
	}

	/**
	 * Get client token expires in time.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_token_expires_in() {

		return $this->client_token_expires_in;
	}

	/**
	 * Set client token expires in time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $expires_in Expires in time.
	 *
	 * @return Connection
	 */
	public function set_client_token_expires_in( $expires_in ) {

		$this->client_token_expires_in = $expires_in;

		return $this;
	}

	/**
	 * Retrieve a client ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_id() {

		return $this->client_id;
	}

	/**
	 * Retrieve a client secret.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_secret() {

		return $this->client_secret;
	}

	/**
	 * Retrieve an ID of the partner merchant.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_partner_merchant_id() {

		return $this->partner_merchant_id;
	}

	/**
	 * Retrieve an ID of the partner.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_partner_id() {

		return self::PARTNER_ID;
	}

	/**
	 * Retrieve an ID of the authorized merchant.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_merchant_id() {

		return $this->merchant_id;
	}

	/**
	 * Retrieve a connection status.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_status() {

		return $this->status;
	}

	/**
	 * Set a connection status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status The connection status.
	 *
	 * @return Connection
	 */
	public function set_status( $status ) {

		$this->status = $status;

		return $this;
	}

	/**
	 * Validate granted permissions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $permissions Permissions.
	 *
	 * @return string
	 */
	public function validate_permissions( $permissions ) {

		if (
			empty( $permissions ) ||
			! $permissions['payments_receivable'] ||
			! $permissions['primary_email_confirmed'] ||
			! isset( $permissions['products'][0]['vetting_status'], $permissions['products'][1]['vetting_status'] ) ||
			$permissions['products'][0]['vetting_status'] !== 'SUBSCRIBED' ||
			$permissions['products'][1]['vetting_status'] !== 'SUBSCRIBED'
		) {
			return self::STATUS_INVALID;
		}

		return self::STATUS_VALID;
	}

	/**
	 * Determine whether a connection is configured fully.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		return ! empty( $this->access_token ) && ! empty( $this->client_token ) && ! empty( $this->client_id ) && ! empty( $this->merchant_id );
	}

	/**
	 * Determine whether a connection is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_valid() {

		return $this->get_status() === self::STATUS_VALID;
	}

	/**
	 * Determine whether a connection is ready for use.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_usable() {

		return $this->is_configured() && $this->is_valid() && $this->get_access_token() && $this->get_client_token();
	}

	/**
	 * Determine whether a access token is expired.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_access_token_expired() {

		return time() > $this->get_access_token_expires_in();
	}

	/**
	 * Determine whether a client token is expired.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_client_token_expired() {

		return time() > $this->get_client_token_expires_in();
	}

	/**
	 * Get connections from DB.
	 *
	 * @since 1.0.0
	 *
	 * @return array Connections.
	 */
	private function get_connections() {

		return (array) get_option( 'wpforms_paypal_commerce_connections', [] );
	}

	/**
	 * Update connections DB data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connections Connections.
	 */
	private function update_connections( $connections ) {

		update_option( 'wpforms_paypal_commerce_connections', $connections );
	}
}
