<?php

namespace WPFormsPaypalCommerce\Api\Http;

use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;

/**
 * Wrapper class for HTTP requests.
 *
 * @since 1.0.0
 */
class Request {

	/**
	 * Live API URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const API_LIVE_URL = 'https://api-m.paypal.com/';

	/**
	 * Sandbox API URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const API_SANDBOX_URL = 'https://api-m.sandbox.paypal.com/';

	/**
	 * Active connection.
	 *
	 * @since 1.0.0
	 *
	 * @var Connection
	 */
	private $connection;

	/**
	 * Request constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Active connection.
	 */
	public function __construct( $connection ) {

		$this->connection = $connection;
	}

	/**
	 * Send a GET request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Request arguments.
	 *
	 * @return Response
	 */
	public function get( $url, $args = [] ) {

		return $this->request( 'GET', $url, $args );
	}

	/**
	 * Send a POST request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Request arguments.
	 *
	 * @return Response
	 */
	public function post( $url, $args = [] ) {

		$args = ! empty( $args ) ? [ 'body' => $args ] : [];

		return $this->request( 'POST', $url, $args );
	}

	/**
	 * Send a PATCH request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Arguments for the request.
	 *
	 * @return Response
	 */
	public function patch( $url, $args = [] ) {

		$args = ! empty( $args ) ? [ 'body' => $args ] : [];

		return $this->request( 'PATCH', $url, $args );
	}

	/**
	 * Send a request based on method (main interface).
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Request method.
	 * @param string $uri    Request URI.
	 * @param array  $args   Request options.
	 *
	 * @return Response
	 */
	public function request( $method, $uri, $args ) {

		$base_url = $this->connection->get_mode() === Helpers::PRODUCTION ? self::API_LIVE_URL : self::API_SANDBOX_URL;

		$url = $base_url . $uri;

		$options['method']  = $method;
		$options['timeout'] = 5;
		$options['headers'] = ! empty( $args['headers'] ) ? array_filter( $args['headers'] ) : $this->get_default_headers();
		$options['body']    = ! empty( $args['body'] ) ? array_filter( $args['body'] ) : '';

		// Prepare a request body, as API expect it in a JSON format.
		if (
			! empty( $options['headers']['Content-Type'] ) &&
			$options['headers']['Content-Type'] !== 'application/x-www-form-urlencoded' &&
			! empty( $options['body'] )
		) {
			$options['body'] = wp_json_encode( $options['body'] );
		}

		/**
		 * Filter a request options array before it's sent.
		 *
		 * @since 1.0.0
		 *
		 * @param array   $options  Request options.
		 * @param string  $method   Request method.
		 * @param string  $url      Request URL.
		 * @param Request $instance Instance of Request class.
		 */
		$options = (array) apply_filters( 'wpforms_paypal_commerce_api_http_request_options', $options, $method, $url, $this );

		// Retrieve the raw response from a safe HTTP request.
		return new Response( wp_safe_remote_request( $url, $options ) );
	}

	/**
	 * Retrieve default headers for request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_default_headers() {

		return [
			'Authorization'                 => 'Bearer ' . $this->connection->get_access_token(),
			'PayPal-Partner-Attribution-Id' => $this->connection->get_partner_id(),
			'Content-Type'                  => 'application/json',
		];
	}
}
