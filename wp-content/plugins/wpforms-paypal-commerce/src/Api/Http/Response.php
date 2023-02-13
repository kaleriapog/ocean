<?php

namespace WPFormsPaypalCommerce\Api\Http;

/**
 * Wrapper class to parse responses.
 *
 * @since 1.0.0
 */
class Response {

	/**
	 * Input data.
	 *
	 * @since 1.0.0
	 *
	 * @var array|\WP_Error
	 */
	private $input;

	/**
	 * Response constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array|\WP_Error $input The response data.
	 */
	public function __construct( $input ) {

		$this->input = $input;
	}

	/**
	 * Retrieve only the response code from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return int The response code as an integer.
	 */
	public function get_response_code() {

		return absint( wp_remote_retrieve_response_code( $this->input ) );
	}

	/**
	 * Retrieve only the response message from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return array The response error.
	 */
	public function get_response_message() {

		$body  = $this->get_body();
		$error = [];

		if ( is_array( $body ) && ! empty( $body['message'] ) ) {
			$error['message'] = $body['message'];
		} else {
			$message          = wp_remote_retrieve_response_message( $this->input );
			$error['message'] = ! empty( $message ) ? $message : 'Response error';
		}

		$error['message'] .= ' PayPal Debug ID: ' . $this->get_debug_id();

		if ( isset( $body['details'] ) ) {
			$error['details'] = $body['details'];
		}

		return $error;
	}

	/**
	 * Retrieve only the body from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return array The body of the response.
	 */
	public function get_body() {

		$body = wp_remote_retrieve_body( $this->input );

		if ( empty( $body ) ) {
			return [];
		}

		return json_decode( $body, true );
	}

	/**
	 * Retrieve only the headers from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return array The body of the response.
	 */
	private function get_debug_id() {

		$debug_id = wp_remote_retrieve_header( $this->input, 'Paypal-Debug-Id' );

		return ! empty( $debug_id ) ? $debug_id : '';
	}

	/**
	 * Whether we received errors in the response.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if response has errors.
	 */
	public function has_errors() {

		$code = $this->get_response_code();

		return $code < 200 || $code > 299;
	}
}
