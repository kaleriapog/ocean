<?php

namespace WPFormsPaypalCommerce\Process;

use stdClass;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce Subscription payment processing.
 *
 * @since 1.0.0
 */
class ProcessSubscriptionAjax extends Base {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wp_ajax_wpforms_paypal_commerce_create_subscription', [ $this, 'create_subscription_order_ajax' ] );
		add_action( 'wp_ajax_nopriv_wpforms_paypal_commerce_create_subscription', [ $this, 'create_subscription_order_ajax' ] );
	}

	/**
	 * Create subscription order.
	 *
	 * @since 1.0.0
	 */
	public function create_subscription_order_ajax() {

		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wpforms-paypal-commerce-create-subscription' )
		) {
			wp_send_json_error( esc_html__( 'You are not allowed to perform this action.', 'wpforms-paypal-commerce' ) );
		}

		$this->form_id = isset( $_POST['wpforms']['id'] ) ? absint( $_POST['wpforms']['id'] ) : 0;

		if ( empty( $this->form_id ) || ! isset( $_POST['wpforms'], $_POST['total'] ) ) {
			wp_send_json_error( esc_html__( 'Something went wrong. Please contact site administrator.', 'wpforms-paypal-commerce' ) );
		}

		$this->connection = Connection::get();

		$this->form_data = wpforms()->get( 'form' )->get( $this->form_id, [ 'content_only' => true ] );

		$subscription_data = $this->prepare_subscription_order_data();

		if ( ! $this->is_form_ok() ) {
			wp_send_json_error( $this->errors );
		}

		$error_title = esc_html__( 'This subscription cannot be created because there was an error with the create subscription API call.', 'wpforms-paypal-commerce' );

		$api = wpforms_paypal_commerce()->get_api( $this->connection );

		if ( is_null( $api ) ) {

			wp_send_json_error( $error_title );
		}

		$subscription_response = $api->create_subscription( $subscription_data );

		if ( $subscription_response->has_errors() ) {

			$this->log_errors( $error_title, $subscription_response->get_response_message() );

			wp_send_json_error( $error_title );
		}

		$subscription = $subscription_response->get_body();

		wp_send_json_success( $subscription );
	}

	/**
	 * Prepare subscription payment order data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function prepare_subscription_order_data() {

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$submitted_data = wp_unslash( $_POST['wpforms'] );
		$this->amount   = wpforms_sanitize_amount( sanitize_text_field( wp_unslash( $_POST['total'] ) ) );
		$plan_id        = isset( $_POST['planId'] ) && $_POST['planId'] !== '' ? sanitize_text_field( wp_unslash( $_POST['planId'] ) ) : Helpers::get_subscription_plan_id_without_rule( $this->form_data );
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		$error_title = esc_html__( 'This subscription cannot be processed because there was an error with the subscription processing API call.', 'wpforms-paypal-commerce' );

		if ( $plan_id === '' ) {

			wp_send_json_error( $error_title );

			$this->log_errors(
				$error_title,
				'This subscription cannot be processed because the plan does not exist.'
			);
		}

		$this->fields   = $submitted_data['fields'];
		$this->currency = $this->get_currency();
		$recurring_plan = $this->form_data['payments'][ Plugin::SLUG ]['recurring'][ $plan_id ];

		if ( empty( $recurring_plan['pp_plan_id'] ) ) {

			wp_send_json_error( $error_title );

			$this->log_errors(
				$error_title,
				sprintf(
					'This subscription cannot be processed because the plan named %s does not exist.',
					$recurring_plan['name']
				)
			);
		}

		$subscription_data = [];

		$plan                = new stdClass();
		$payment_preferences = new stdClass();
		$billing_cycle       = new stdClass();
		$taxes               = new stdClass();

		$billing_cycle->sequence     = 1;
		$billing_cycle->total_cycles = $recurring_plan['total_cycles'];

		$billing_cycle->pricing_scheme = new stdClass();

		$billing_cycle->pricing_scheme->fixed_price                = new stdClass();
		$billing_cycle->pricing_scheme->fixed_price->value         = $this->amount;
		$billing_cycle->pricing_scheme->fixed_price->currency_code = $this->currency;

		$plan->billing_cycles[] = $billing_cycle;

		$subscription_data['plan_id']                   = $recurring_plan['pp_plan_id'];
		$payment_preferences->payment_failure_threshold = isset( $recurring_plan['bill_retry'] ) ? 2 : 1;

		$plan->payment_preferences = $payment_preferences;

		$taxes->inclusive  = true;
		$taxes->percentage = 0;

		$plan->taxes = $taxes;

		$subscription_data['plan'] = $plan;

		$application_context              = new stdClass();
		$application_context->user_action = 'CONTINUE';

		$is_shipping_address = isset( $recurring_plan['shipping_address'] ) && $recurring_plan['shipping_address'] !== '';

		if ( $is_shipping_address ) {
			$subscriber                            = new stdClass();
			$subscriber->shipping_address          = new stdClass();
			$subscriber->shipping_address->address = new stdClass();
			$subscriber->shipping_address->name    = new stdClass();

			$subscriber->shipping_address->address->address_line_1 = sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['address1'] );
			$subscriber->shipping_address->address->address_line_2 = sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['address2'] );
			$subscriber->shipping_address->address->admin_area_1   = sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['state'] );
			$subscriber->shipping_address->address->admin_area_2   = sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['city'] );
			$subscriber->shipping_address->address->postal_code    = sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['postal'] );
			$subscriber->shipping_address->address->country_code   = isset( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['country'] ) ? sanitize_text_field( $submitted_data['fields'][ $recurring_plan['shipping_address'] ]['country'] ) : 'US';
			$subscriber->shipping_address->name->full_name         = '';

			$subscription_data['subscriber'] = $subscriber;
		}

		$application_context->shipping_preference = $is_shipping_address ? 'SET_PROVIDED_ADDRESS' : 'NO_SHIPPING';

		$subscription_data['application_context'] = $application_context;

		return $subscription_data;
	}
}
