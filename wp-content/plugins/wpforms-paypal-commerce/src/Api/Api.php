<?php

namespace WPFormsPaypalCommerce\Api;

use WPFormsPaypalCommerce\Api\Http\Request;
use WPFormsPaypalCommerce\Api\Http\Response;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;

/**
 * API class.
 *
 * @since 1.0.0
 */
class Api {

	/**
	 * Active connection.
	 *
	 * @since 1.0.0
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * Request instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Request
	 */
	private $request;

	/**
	 * API constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Active connection.
	 */
	public function __construct( $connection ) {

		$this->connection = $connection;
		$this->request    = new Request( $connection );
	}

	/**
	 * Generate an access token.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function generate_access_token() {

		$args = [
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode( $this->connection->get_client_id() . ':' . $this->connection->get_client_secret() ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			'body'    => [
				'grant_type' => 'client_credentials',
			],
		];

		$token_response = $this->request->request( 'POST', 'v1/oauth2/token', $args );

		if ( $token_response->has_errors() ) {
			Helpers::log_errors(
				'PayPal Access Token error.',
				'',
				$token_response->get_response_message()
			);

			return [];
		}

		return $token_response->get_body();
	}

	/**
	 * Generate a client token.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function generate_client_token() {

		$token_response = $this->request->post( 'v1/identity/generate-token' );

		if ( $token_response->has_errors() ) {
			Helpers::log_errors(
				'PayPal Client Token error.',
				'',
				$token_response->get_response_message()
			);

			return [];
		}

		return $token_response->get_body();
	}

	/**
	 * Get merchant information.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_merchant_info() {

		static $merchant;

		$mode = $this->connection->get_mode();

		if ( ! isset( $merchant[ $mode ] ) ) {
			$merchant_response = $this->request->get( 'v1/customer/partners/' . $this->connection->get_partner_merchant_id() . '/merchant-integrations/' . $this->connection->get_merchant_id() );

			if ( $merchant_response->has_errors() ) {
				Helpers::log_errors(
					'PayPal Merchant Info error.',
					'',
					$merchant_response->get_response_message()
				);

				return [];
			}

			$merchant[ $mode ] = $merchant_response->get_body();
		}

		return $merchant[ $mode ];
	}

	/**
	 * Create a new order.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Order data.
	 *
	 * @return Response
	 */
	public function create_order( $data ) {

		return $this->request->post( 'v2/checkout/orders', $data );
	}

	/**
	 * Capture an order.
	 *
	 * @since 1.0.0
	 *
	 * @param string $order_id Order ID.
	 *
	 * @return Response
	 */
	public function capture( $order_id ) {

		return $this->request->post( 'v2/checkout/orders/' . $order_id . '/capture' );
	}

	/**
	 * Get an order details by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $order_id Order ID.
	 *
	 * @return array
	 */
	public function get_order( $order_id ) {

		$order_response = $this->request->get( 'v2/checkout/orders/' . $order_id );

		if ( $order_response->has_errors() ) {
			Helpers::log_errors(
				'PayPal Get Order error.',
				$order_id,
				$order_response->get_response_message()
			);

			return [];
		}

		return $order_response->get_body();
	}

	/**
	 * Create a new product.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Product data.
	 *
	 * @return Response
	 */
	public function create_product( $data ) {

		return $this->request->post( 'v1/catalogs/products', $data );
	}

	/**
	 * Create a new plan.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Plan data.
	 *
	 * @return Response
	 */
	public function create_plan( $data ) {

		return $this->request->post( 'v1/billing/plans', $data );
	}

	/**
	 * Get a subscription.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Subscription ID.
	 *
	 * @return array
	 */
	public function get_subscription( $id ) {

		$subscription_response = $this->request->get( 'v1/billing/subscriptions/' . $id );

		if ( $subscription_response->has_errors() ) {
			Helpers::log_errors(
				'PayPal Get Subscription error.',
				$id,
				$subscription_response->get_response_message()
			);

			return [];
		}

		return $subscription_response->get_body();
	}

	/**
	 * Create a new subscription.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Subscription data.
	 *
	 * @return Response
	 */
	public function create_subscription( $data ) {

		return $this->request->post( 'v1/billing/subscriptions', $data );
	}

	/**
	 * Activate an already approved subscription.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subscription_id Approved subscription ID.
	 *
	 * @return Response
	 */
	public function activate_subscription( $subscription_id ) {

		return $this->request->post( 'v1/billing/subscriptions/' . $subscription_id . '/activate' );
	}
}
