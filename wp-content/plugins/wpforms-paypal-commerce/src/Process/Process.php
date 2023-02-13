<?php

namespace WPFormsPaypalCommerce\Process;

use WPFormsPaypalCommerce\Api\Api;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce payment processing.
 *
 * @since 1.0.0
 */
class Process extends Base {

	/**
	 * PayPal Commerce field.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $field = [];

	/**
	 * Form submission data ($_POST).
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $entry = [];

	/**
	 * Main class that communicates with the PayPal Commerce API.
	 *
	 * @since 1.0.0
	 *
	 * @var Api
	 */
	protected $api;

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		if ( wp_doing_ajax() ) {
			( new ProcessSingleAjax() )->hooks();
			( new ProcessSubscriptionAjax() )->hooks();
		}

		add_action( 'wpforms_process',             [ $this, 'process_entry' ], 10, 3 );
		add_action( 'wpforms_process_complete',    [ $this, 'update_entry_meta' ], 10, 4 );
		add_filter( 'wpforms_entry_email_process', [ $this, 'process_email' ], 70, 5 );
	}

	/**
	 * Check if a payment exists with an entry, if so validate and process.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Final/sanitized submitted field data.
	 * @param array $entry     Copy of original $_POST.
	 * @param array $form_data Form data and settings.
	 */
	public function process_entry( $fields, $entry, $form_data ) {

		if ( ! Helpers::is_paypal_commerce_enabled( $form_data ) ) {
			return;
		}

		$this->form_data  = $form_data;
		$this->fields     = $fields;
		$this->entry      = $entry;
		$this->form_id    = (int) $form_data['id'];
		$this->amount     = $this->get_amount();
		$this->field      = Helpers::get_paypal_field( $this->fields );
		$this->connection = Connection::get();
		$this->api        = wpforms_paypal_commerce()->get_api( $this->connection );

		if ( is_null( $this->api ) ) {
			return;
		}

		// Before proceeding, check if any basic errors were detected.
		if ( ! $this->is_form_processed() ) {
			$this->display_errors();

			return;
		}

		if ( ! empty( $entry['fields'][ $this->field['id'] ]['orderID'] ) ) {
			$this->capture_single();
		}

		if ( ! empty( $entry['fields'][ $this->field['id'] ]['subscriptionID'] ) ) {
			$this->activate_subscription();
		}

		$this->display_errors();
	}

	/**
	 * Capture single order.
	 *
	 * @since 1.0.0
	 */
	private function capture_single() {

		$order_response = $this->api->capture( $this->entry['fields'][ $this->field['id'] ]['orderID'] );

		if ( $order_response->has_errors() ) {
			$error_title    = esc_html__( 'This payment cannot be processed because there was an error with the capture order API call.', 'wpforms-paypal-commerce' );
			$this->errors[] = $error_title;

			$this->log_errors( $error_title, $order_response->get_response_message() );

			return;
		}

		$order_data = $order_response->get_body();

		if ( isset( $order_data['payment_source']['card'] ) ) {
			wpforms()->get( 'process' )->fields[ $this->field['id'] ]['value'] = implode( "\n", array_filter( $order_data['payment_source']['card'] ) );
		} else {
			wpforms()->get( 'process' )->fields[ $this->field['id'] ]['value'] = '-';
		}
	}

	/**
	 * Activate subscription.
	 *
	 * @since 1.0.0
	 */
	private function activate_subscription() {

		$subscription_id = $this->entry['fields'][ $this->field['id'] ]['subscriptionID'];

		$subscription_response = $this->api->activate_subscription( $subscription_id );

		if ( $subscription_response->has_errors() ) {
			$error_title    = esc_html__( 'This subscription cannot be activated because there was an error with the activation API call.', 'wpforms-paypal-commerce' );
			$this->errors[] = $error_title;

			$this->log_errors( $error_title, $subscription_response->get_response_message() );
		}
	}

	/**
	 * Update entry details and add meta for a successful payment.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $fields    Final/sanitized submitted field data.
	 * @param array  $entry     Copy of original $_POST.
	 * @param array  $form_data Form data and settings.
	 * @param string $entry_id  Entry ID.
	 */
	public function update_entry_meta( $fields, $entry, $form_data, $entry_id ) {

		if ( empty( $entry_id ) || $this->errors || ! $this->api || empty( $this->field ) || ( empty( $entry['fields'][ $this->field['id'] ]['orderID'] ) && empty( $entry['fields'][ $this->field['id'] ]['subscriptionID'] ) ) ) {
			return;
		}

		$payment_source = $entry['fields'][ $this->field['id'] ]['source'] === 'paypal' ? esc_html__( 'PayPal Checkout', 'wpforms-paypal-commerce' ) : esc_html__( 'Credit Card', 'wpforms-paypal-commerce' );

		$meta['payment_type']     = Plugin::SLUG;
		$meta['payment_total']    = $this->amount;
		$meta['payment_currency'] = $this->get_currency();
		$meta['payment_mode']     = Helpers::is_sandbox_mode() ? 'test' : 'production';
		$meta['payment_note']     = esc_html__( 'Payment Source: ', 'wpforms-paypal-commerce' ) . $payment_source;

		$order_data = [];

		if ( ! empty( $entry['fields'][ $this->field['id'] ]['orderID'] ) ) {

			$order_data = $this->api->get_order( sanitize_text_field( $entry['fields'][ $this->field['id'] ]['orderID'] ) );

			if ( empty( $order_data ) ) {
				return;
			}

			$meta['payment_transaction'] = sanitize_text_field( $order_data['purchase_units'][0]['payments']['captures'][0]['id'] );

			$data['status'] = ucwords( strtolower( $order_data['status'] ) );
		}

		if ( ! empty( $entry['fields'][ $this->field['id'] ]['subscriptionID'] ) ) {

			$order_data = $this->api->get_subscription( sanitize_text_field( $entry['fields'][ $this->field['id'] ]['subscriptionID'] ) );

			$this->maybe_log_matched_subscriptions( $order_data['plan_id'] );

			if ( empty( $order_data ) ) {
				return;
			}

			$meta['payment_subscription'] = sanitize_text_field( $order_data['id'] );
			$meta['payment_customer']     = sanitize_text_field( $order_data['subscriber']['payer_id'] );
			$meta['payment_period']       = $this->get_subscription_period( $form_data, $order_data['plan_id'] );

			$data['status'] = ucwords( strtolower( $order_data['status'] ) );
		}

		$data['type'] = 'payment';
		$data['meta'] = wp_json_encode( $meta );

		wpforms()->get( 'entry' )->update( $entry_id, $data, '', '', [ 'cap' => false ] );

		wpforms()->get( 'entry' )->insert_payment_meta( $entry_id, $meta );

		/**
		 * Fire when entry details and add meta was successfully updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array   $fields     Final/sanitized submitted field data.
		 * @param array   $form_data  Form data and settings.
		 * @param string  $entry_id   Entry ID.
		 * @param array   $order_data Response order data.
		 * @param Process $process    Process class instance.
		 */
		do_action( 'wpforms_paypal_commerce_process_update_entry_meta', $fields, $form_data, $entry_id, $order_data, $this );
	}

	/**
	 * Logic that helps decide if we should send completed payments notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $process         Whether to process or not.
	 * @param array  $fields          Form fields.
	 * @param array  $form_data       Form data.
	 * @param int    $notification_id Notification ID.
	 * @param string $context         In which context this email is sent.
	 *
	 * @return bool
	 */
	public function process_email( $process, $fields, $form_data, $notification_id, $context ) {

		if ( ! $process ) {
			return false;
		}

		if ( ! Helpers::is_paypal_commerce_enabled( $form_data ) ) {
			return $process;
		}

		if ( empty( $form_data['settings']['notifications'][ $notification_id ][ Plugin::SLUG ] ) ) {
			return $process;
		}

		if ( empty( $this->entry['fields'][ $this->field['id'] ]['orderID'] ) && empty( $this->entry['fields'][ $this->field['id'] ]['subscriptionID'] ) ) {
			return false;
		}

		return ! $this->errors && $this->api;
	}

	/**
	 * Get subscription period by plan id.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data  Form data.
	 * @param string $pp_plan_id Subscription plan id.
	 *
	 * @return string
	 */
	private function get_subscription_period( $form_data, $pp_plan_id ) {

		foreach ( $form_data['payments'][ Plugin::SLUG ]['recurring'] as $recurring ) {

			if ( $recurring['pp_plan_id'] !== $pp_plan_id ) {
				continue;
			}

			return $recurring['recurring_times'];
		}

		return '';
	}

	/**
	 * Log if more than one plan matched on form submission.
	 *
	 * @since 1.0.0
	 *
	 * @param string $matched_plan_id Already matched and executed plan.
	 */
	private function maybe_log_matched_subscriptions( $matched_plan_id ) {

		foreach ( $this->form_data['payments'][ Plugin::SLUG ]['recurring'] as $recurring ) {

			if ( ! $this->is_conditional_logic_ok( $recurring ) || $recurring['pp_plan_id'] === $matched_plan_id ) {
				continue;
			}

			$this->log_errors(
				'PayPal Commerce subscription processing error.',
				sprintf(
					/* translators: %1$s - Plan ID, %2$s - Plan ID. */
					esc_html( 'Plan %1$s processing error. Plan %2$s already matched.' ),
					$recurring['pp_plan_id'],
					$matched_plan_id
				)
			);
		}
	}

	/**
	 * Check if form has errors before payment processing.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_form_processed() {

		// Bail in case there are form processing errors.
		if ( ! empty( wpforms()->get( 'process' )->errors[ $this->form_id ] ) ) {
			return false;
		}

		return $this->is_card_field_visibility_ok();
	}

	/**
	 * Check if there is at least one visible (not hidden by conditional logic) card field in the form.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_card_field_visibility_ok() {

		if ( empty( $this->field ) ) {
			return false;
		}

		// If the form contains no fields with conditional logic the card field is visible by default.
		if ( empty( $this->form_data['conditional_fields'] ) ) {
			return true;
		}

		// If the field is NOT in array of conditional fields, it's visible.
		if ( ! in_array( $this->field['id'], $this->form_data['conditional_fields'], true ) ) {
			return true;
		}

		// If the field IS in array of conditional fields and marked as visible, it's visible.
		if ( ! empty( $this->field['visible'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Display form errors.
	 *
	 * @since 1.0.0
	 */
	private function display_errors() {

		if ( ! $this->errors || ! is_array( $this->errors ) ) {
			return;
		}

		// Check if the form contains a required credit card. If it does
		// and there was an error, return the error to the user and prevent
		// the form from being submitted. This should not occur under normal
		// circumstances.
		if ( empty( $this->field ) || empty( $this->form_data['fields'][ $this->field['id'] ] ) ) {
			return;
		}

		if ( ! empty( $this->form_data['fields'][ $this->field['id'] ]['required'] ) ) {
			wpforms()->get( 'process' )->errors[ $this->form_id ]['footer'] = implode( '<br>', $this->errors );
		}
	}
}
