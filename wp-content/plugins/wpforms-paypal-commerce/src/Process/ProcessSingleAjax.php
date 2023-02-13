<?php

namespace WPFormsPaypalCommerce\Process;

use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce Single Ajax payment processing.
 *
 * @since 1.0.0
 */
class ProcessSingleAjax extends Base {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wp_ajax_wpforms_paypal_commerce_create_order', [ $this, 'single_checkout_create_order_ajax' ] );
		add_action( 'wp_ajax_nopriv_wpforms_paypal_commerce_create_order', [ $this, 'single_checkout_create_order_ajax' ] );
	}

	/**
	 * Create single checkout order.
	 *
	 * @since 1.0.0
	 */
	public function single_checkout_create_order_ajax() {

		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wpforms-paypal-commerce-create-order' )
		) {
			wp_send_json_error( esc_html__( 'You are not allowed to perform this action.', 'wpforms-paypal-commerce' ) );
		}

		$this->form_id = isset( $_POST['wpforms']['id'] ) ? absint( $_POST['wpforms']['id'] ) : 0;

		if ( empty( $this->form_id ) || ! isset( $_POST['wpforms'], $_POST['total'] ) ) {
			wp_send_json_error( esc_html__( 'Something went wrong. Please contact site administrator.', 'wpforms-paypal-commerce' ) );
		}

		$this->connection = Connection::get();

		$this->form_data = wpforms()->get( 'form' )->get( $this->form_id, [ 'content_only' => true ] );

		$order_data = $this->prepare_single_order_data();

		if ( ! $this->is_form_ok() ) {
			wp_send_json_error( $this->errors );
		}

		$error_title = esc_html__( 'This order cannot be created because there was an error with the create order API call.', 'wpforms-paypal-commerce' );

		$api = wpforms_paypal_commerce()->get_api( $this->connection );

		if ( is_null( $api ) ) {
			wp_send_json_error( $error_title );
		}

		$order_response = $api->create_order( $order_data );

		if ( $order_response->has_errors() ) {

			$this->log_errors( $error_title, $order_response->get_response_message() );

			wp_send_json_error( $error_title );
		}

		wp_send_json_success( $order_response->get_body() );
	}

	/**
	 * Prepare single payment order data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function prepare_single_order_data() {

		$settings       = $this->form_data['payments'][ Plugin::SLUG ];
		$submitted_data = wp_unslash( $_POST['wpforms'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$this->fields   = $submitted_data['fields'];

		$is_shipping_address = isset( $settings['shipping_address'] ) && $settings['shipping_address'] !== '';

		$order_data = [];

		$order_data['intent']                                     = 'CAPTURE';
		$order_data['application_context']['shipping_preference'] = $is_shipping_address ? 'SET_PROVIDED_ADDRESS' : 'NO_SHIPPING';
		$order_data['application_context']['user_action']         = 'CONTINUE';

		if ( isset( $settings['billing_email'] ) && $settings['billing_email'] !== '' && ! empty( $submitted_data['fields'][ $settings['billing_email'] ] ) ) {

			$email = is_array( $submitted_data['fields'][ $settings['billing_email'] ] ) ? $submitted_data['fields'][ $settings['billing_email'] ]['primary'] : $submitted_data['fields'][ $settings['billing_email'] ];

			$order_data['payer']['email_address'] = sanitize_email( $email );
		}

		if ( isset( $settings['billing_address'] ) && $settings['billing_address'] !== '' ) {

			$order_data['payer']['address'] = $this->map_address_field( $submitted_data, $settings['billing_address'] );
		}

		$this->currency = $this->get_currency();
		$this->amount   = wpforms_sanitize_amount( sanitize_text_field( wp_unslash( $_POST['total'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		$order_data['purchase_units'][0] = [
			'amount'      => [
				'value'         => $this->amount,
				'currency_code' => $this->currency,
				'breakdown'     => [
					'item_total' => [
						'value'         => $this->amount,
						'currency_code' => $this->currency,
					],
					'shipping'   => [
						'value'         => 0,
						'currency_code' => $this->currency,
					],
				],
			],
			'description' => empty( $settings['payment_description'] ) ? $this->get_form_name() : html_entity_decode( $settings['payment_description'], ENT_COMPAT, 'UTF-8' ),
			'items'       => $this->get_order_items(),
			'shipping'    => [
				'name' => [
					'full_name' => '',
				],
			],
		];

		if ( isset( $settings['name'] ) && $settings['name'] !== '' ) {

			$name = ! is_array( $submitted_data['fields'][ $settings['name'] ] ) ? sanitize_text_field( $submitted_data['fields'][ $settings['name'] ] ) : sanitize_text_field( implode( ' ', $submitted_data['fields'][ $settings['name'] ] ) );

			$order_data['payer']['name']['given_name']                        = $name;
			$order_data['purchase_units'][0]['shipping']['name']['full_name'] = $name;
		}

		if ( $is_shipping_address ) {

			$order_data['purchase_units'][0]['shipping']['address'] = $this->map_address_field( $submitted_data, $settings['shipping_address'] );
		}

		return $order_data;
	}

	/**
	 * Map our address field to PayPal format.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $submitted_data Submitted data.
	 * @param string $address_field  Address field id.
	 *
	 * @return array
	 */
	private function map_address_field( $submitted_data, $address_field ) {

		return [
			'address_line_1' => sanitize_text_field( $submitted_data['fields'][ $address_field ]['address1'] ),
			'address_line_2' => sanitize_text_field( $submitted_data['fields'][ $address_field ]['address2'] ),
			'admin_area_1'   => sanitize_text_field( $submitted_data['fields'][ $address_field ]['state'] ),
			'admin_area_2'   => sanitize_text_field( $submitted_data['fields'][ $address_field ]['city'] ),
			'postal_code'    => sanitize_text_field( $submitted_data['fields'][ $address_field ]['postal'] ),
			'country_code'   => isset( $submitted_data['fields'][ $address_field ]['country'] ) ? sanitize_text_field( $submitted_data['fields'][ $address_field ]['country'] ) : 'US',
		];
	}

	/**
	 * Retrieve order items.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_order_items() {

		$types = wpforms_payment_fields();
		$items = [];

		foreach ( $this->form_data['fields'] as $field_id => $field ) {

			if (
				empty( $field['type'] ) ||
				! in_array( $field['type'], $types, true )
			) {
				continue;
			}

			// Skip payment field that is not filled in.
			if (
				! isset( $this->fields[ $field_id ] ) ||
				wpforms_is_empty_string( $this->fields[ $field_id ] )
			) {
				continue;
			}

			$items = $this->prepare_order_line_item( $items, $field );
		}

		return $items;
	}

	/**
	 * Prepare order line item.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items Items.
	 * @param array $field Field data.
	 *
	 * @return array
	 */
	private function prepare_order_line_item( $items, $field ) {

		$field_id = absint( $field['id'] );
		$name     = empty( $field['label'] ) ? sprintf( /* translators: %d - Field ID. */ esc_html__( 'Field #%d', 'wpforms-paypal-commerce' ), $field_id ) : $field['label'];

		if ( empty( $field['choices'] ) ) {
			$items[] = [
				'name'        => $name,
				'quantity'    => 1,
				'unit_amount' => [
					'value'         => wpforms_sanitize_amount( $this->fields[ $field_id ] ),
					'currency_code' => $this->currency,
				],
			];

			return $items;
		}

		$choices = ! is_array( $this->fields[ $field_id ] ) ? [ $this->fields[ $field_id ] ] : $this->fields[ $field_id ];

		foreach ( $choices as $choice ) {

			if ( empty( $field['choices'][ $choice ] ) ) {
				continue;
			}

			$choice_name = empty( $field['choices'][ $choice ]['label'] ) ? sprintf( /* translators: %d - Choice ID. */ esc_html__( 'Choice %d', 'wpforms-paypal-commerce' ), absint( $choice ) ) : $field['choices'][ $choice ]['label'];

			$items[] = [
				'name'        => $name . ': ' . $choice_name,
				'quantity'    => 1,
				'unit_amount' => [
					'value'         => wpforms_sanitize_amount( $field['choices'][ $choice ]['value'] ),
					'currency_code' => $this->currency,
				],
			];
		}

		return $items;
	}

	/**
	 * Retrieve a Form Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_form_name() {

		if ( ! empty( $this->form_data['settings']['form_title'] ) ) {
			return sanitize_text_field( $this->form_data['settings']['form_title'] );
		}

		$form = wpforms()->get( 'form' )->get( $this->form_data['id'] );

		return $form instanceof \WP_Post ? $form->post_title : sprintf( /* translators: %d - Form ID. */ esc_html__( 'Form #%d', 'wpforms-paypal-commerce' ), $this->form_data['id'] );
	}

}
