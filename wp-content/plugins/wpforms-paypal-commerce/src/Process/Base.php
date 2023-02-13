<?php

namespace WPFormsPaypalCommerce\Process;

use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;

/**
 * Base payment processing.
 *
 * @since 1.0.0
 */
abstract class Base {

	/**
	 * Form ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * Sanitized submitted field values and data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Form data and settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $form_data = [];

	/**
	 * Payment amount.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $amount = '';

	/**
	 * Payment currency.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $currency = '';

	/**
	 * Connection data.
	 *
	 * @since 1.0.0
	 *
	 * @var Connection
	 */
	protected $connection;

	/**
	 * PayPal Commerce form errors.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Check form settings, fields, etc.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_form_ok() {

		if ( ! $this->is_connection_ok() ) {
			$error_title    = esc_html__( 'This payment cannot be processed because the account connection is missing.', 'wpforms-paypal-commerce' );
			$this->errors[] = $error_title;

			$this->log_errors( $error_title );

			return false;
		}

		if ( empty( $this->amount ) ) {
			$error_title    = esc_html__( 'This payment cannot be processed because the payment amount is not set, or is set to an invalid amount.', 'wpforms-paypal-commerce' );
			$this->errors[] = $error_title;

			$this->log_errors(
				$error_title,
				[
					'amount'   => $this->amount,
					'currency' => $this->currency,
				]
			);

			return false;
		}

		return true;
	}

	/**
	 * Check if conditional logic check passes for the given settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Conditional logic settings to process.
	 *
	 * @return bool
	 */
	protected function is_conditional_logic_ok( $settings ) {

		if (
			empty( $settings['conditional_logic'] ) ||
			empty( $settings['conditional_type'] ) ||
			empty( $settings['conditionals'] )
		) {
			return true;
		}

		$process = wpforms_conditional_logic()->process( $this->fields, $this->form_data, $settings['conditionals'] );

		if ( $settings['conditional_type'] === 'stop' ) {
			$process = ! $process;
		}

		return $process;
	}

	/**
	 * Check if connection is exists, configured and valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_connection_ok() {

		return $this->connection && $this->connection->is_usable();
	}

	/**
	 * Retrieve a payment currency.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_currency() {

		return strtoupper( wpforms_get_currency() );
	}

	/**
	 * Retrieve a payment amount.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_amount() {

		$amount = wpforms_get_total_payment( $this->fields );

		return $amount === false ? wpforms_sanitize_amount( 0 ) : $amount;
	}

	/**
	 * Log payment errors.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $title    Error title.
	 * @param array|string $messages Error messages.
	 * @param string       $level    Error level to add to 'payment' error level.
	 */
	protected function log_errors( $title, $messages = [], $level = 'error' ) {

		Helpers::log_errors( $title, $this->form_id, $messages, $level );
	}
}
