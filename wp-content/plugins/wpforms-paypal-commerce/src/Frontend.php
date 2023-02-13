<?php

namespace WPFormsPaypalCommerce;

use WPFormsPaypalCommerce\Admin\Connect;

/**
 * Frontend related functionality.
 *
 * @since 1.0.0
 */
class Frontend {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_frontend_container_class', [ $this, 'form_container_class' ], 10, 2 );
		// Load assets on later stage after all our payment addons.
		add_action( 'wpforms_wp_footer', [ $this, 'enqueues' ], PHP_INT_MAX );
		add_filter( 'wpforms_field_properties', [ $this, 'conditional_field_properties' ], 10, 3 );
		add_filter( 'script_loader_tag', [ $this, 'set_script_attributes' ], 10, 3 );
	}

	/**
	 * Add class to a form container if PayPal Commerce is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes   Array of form classes.
	 * @param array $form_data Form data of current form.
	 *
	 * @return array
	 */
	public function form_container_class( $classes, $form_data ) {

		if ( ! Connection::get() ) {
			return $classes;
		}

		if ( ! Helpers::has_paypal_commerce_field( $form_data ) ) {
			return $classes;
		}

		if ( ! Helpers::is_subscriptions_configured( $form_data ) ) {
			return $classes;
		}

		if ( Helpers::is_paypal_commerce_enabled( $form_data ) ) {
			$classes[] = 'wpforms-paypal-commerce';
		}

		return $classes;
	}

	/**
	 * Enqueue assets in the frontend if PayPal Commerce is in use on the page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Form data of forms on current page.
	 */
	public function enqueues( $forms ) {

		$connection = Connection::get();

		if (
			! $connection ||
			! $connection->is_usable() ||
			! Helpers::has_paypal_commerce_field( $forms, true ) ||
			! Helpers::is_paypal_commerce_forms_enabled( $forms )
		) {
			return;
		}

		if ( $connection->is_access_token_expired() ) {
			$connection = Connect::refresh_access_token( $connection );
		}

		if ( $connection->is_client_token_expired() ) {
			$connection = Connect::refresh_client_token( $connection );
		}

		if ( ! $connection->get_access_token() || ! $connection->get_client_token() ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		$this->enqueues_styles();

		$conditional_rules = $this->get_conditional_rules( $forms );

		if ( ! empty( $conditional_rules ) && ! wp_script_is( 'wpforms-builder-conditionals', 'enqueue' ) ) {
			wp_enqueue_script(
				'wpforms-builder-conditionals',
				WPFORMS_PLUGIN_URL . "assets/pro/js/wpforms-conditional-logic-fields{$min}.js",
				[ 'jquery', 'wpforms' ],
				WPFORMS_VERSION,
				true
			);
		}

		wp_enqueue_script(
			'wpforms-paypal-commerce',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/js/wpforms-paypal-commerce{$min}.js",
			[ 'jquery' ],
			WPFORMS_PAYPAL_COMMERCE_VERSION,
			true
		);

		$base_src = add_query_arg(
			[
				'client-id'       => $connection->get_client_id(),
				'currency'        => strtoupper( wpforms_get_currency() ),
				'locale'          => get_user_locale(),
				'disable-funding' => 'credit,paylater,bancontact,blik,eps,giropay,ideal,mercadopago,mybank,p24,sepa,sofort,venmo',
			],
			'https://www.paypal.com/sdk/js'
		);

		$single_args = [
			'components' => 'buttons,hosted-fields',
		];

		wp_enqueue_script(
			'wpforms-paypal-single',
			add_query_arg( $single_args, $base_src ),
			[],
			null
		);

		$subscriptions_args = [
			'vault'      => 'true',
			'intent'     => 'subscription',
			'components' => 'buttons,hosted-fields',
		];

		wp_enqueue_script(
			'wpforms-paypal-subscriptions',
			add_query_arg( $subscriptions_args, $base_src ),
			[],
			null
		);

		wp_localize_script(
			'wpforms-paypal-commerce',
			'wpforms_paypal_commerce',
			[
				'payment_options'   => $this->get_payment_options( $forms ),
				'conditional_rules' => $conditional_rules,
				'nonces'            => [
					'create'              => wp_create_nonce( 'wpforms-paypal-commerce-create-order' ),
					'approve'             => wp_create_nonce( 'wpforms-paypal-commerce-approve-order' ),
					'create_subscription' => wp_create_nonce( 'wpforms-paypal-commerce-create-subscription' ),
				],
				'i18n'              => [
					'missing_sdk_script' => esc_html__( 'PayPal.js failed to load properly.', 'wpforms-paypal-commerce' ),
					'on_cancel'          => esc_html__( 'PayPal payment was canceled.', 'wpforms-paypal-commerce' ),
					'on_error'           => esc_html__( 'There was an error processing this payment. Please contact the site administrator.', 'wpforms-paypal-commerce' ),
					'api_error'          => esc_html__( 'API error:', 'wpforms-paypal-commerce' ),
					'empty_amount'       => esc_html__( 'The payment was not processed because the payment amount is empty.', 'wpforms-paypal-commerce' ),
					'subscription_error' => esc_html__( 'There was an error creating this subscription. Please contact the site administrator.', 'wpforms-paypal-commerce' ),
					'secure_error'       => esc_html__( 'This payment cannot be processed because there was an error with 3D Secure authentication.', 'wpforms-paypal-commerce' ),
					'card_not_supported' => esc_html__( 'is not supported. Please enter the details for a supported credit card.', 'wpforms-paypal-commerce' ),
					'number'             => esc_html__( 'Please enter a valid card number.', 'wpforms-paypal-commerce' ),
					'expirationDate'     => esc_html__( 'Please enter a valid date.', 'wpforms-paypal-commerce' ),
					'cvv'                => esc_html__( 'Please enter the CVV number.', 'wpforms-paypal-commerce' ),
				],
			]
		);
	}

	/**
	 * Enqueue styles in the frontend if PayPal Commerce is in use on the page.
	 *
	 * @since 1.0.0
	 */
	private function enqueues_styles() {

		// Include styles if the "Include Form Styling > No Styles" is not set.
		if ( wpforms_setting( 'disable-css', '1' ) !== '3' ) {

			$min = wpforms_get_min_suffix();

			wp_enqueue_style(
				'wpforms-paypal-commerce',
				WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/wpforms-paypal-commerce{$min}.css",
				[],
				WPFORMS_PAYPAL_COMMERCE_VERSION
			);
		}
	}

	/**
	 * Add attributes to PayPal script tags.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag    HTML for the script tag.
	 * @param string $handle Handle of script.
	 * @param string $src    Src of script.
	 *
	 * @return string
	 */
	public function set_script_attributes( $tag, $handle, $src ) {

		if ( ! in_array( $handle, [ 'wpforms-paypal-single', 'wpforms-paypal-subscriptions' ], true ) ) {
			return $tag;
		}

		$connection = Connection::get();

		if ( ! $connection ) {
			return $tag;
		}

		$attributes  = ' data-namespace="' . esc_attr( str_replace( '-', '_', $handle ) ) . '"';
		$attributes .= ' data-client-token="' . esc_attr( $connection->get_client_token() ) . '"';
		$attributes .= ' data-partner-merchant-id="' . esc_attr( $connection->get_partner_merchant_id() ) . '"';
		$attributes .= ' data-partner-attribution-id="' . esc_attr( $connection->get_partner_id() ) . '"';

		return str_replace( ' src', $attributes . ' src', $tag );
	}

	/**
	 * Mark fields which uses for payment conditional logic.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field data.
	 * @param array $form_data  Form data.
	 *
	 * @return array
	 */
	public function conditional_field_properties( $properties, $field, $form_data ) {

		if ( wpforms_is_admin_page( 'entries', 'edit' ) ) {
			return $properties;
		}

		$conditional_fields = $this->get_conditional_fields( $form_data );

		if ( empty( $conditional_fields ) ) {
			return $properties;
		}

		if ( in_array( $field['id'], $conditional_fields, true ) ) {
			$properties['container']['class'][] = 'wpforms-paypal-commerce-conditional-trigger';
		}

		return $properties;
	}

	/**
	 * Get Conditional fields IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	private function get_conditional_fields( $form_data ) {

		static $conditional_fields;

		if ( empty( $form_data['payments'][ Plugin::SLUG ] ) || ! is_null( $conditional_fields ) ) {
			return $conditional_fields;
		}

		$rules              = [];
		$conditional_fields = [];

		$payment_settings = $form_data['payments'][ Plugin::SLUG ];

		if (
			! empty( $payment_settings['conditionals'] ) &&
			! empty( $payment_settings['conditional_logic'] ) &&
			Helpers::is_paypal_commerce_single_enabled( $form_data )
		) {
			$rules[] = $payment_settings['conditionals'];
		}

		if ( Helpers::is_paypal_commerce_subscriptions_enabled( $form_data ) ) {
			foreach ( $payment_settings['recurring'] as $recurring ) {
				$rules[] = ! empty( $recurring['conditional_logic'] ) ? $recurring['conditionals'] : [];
			}
		}

		array_walk_recursive(
			$rules,
			static function ( $item, $key ) use ( &$conditional_fields ) {
				if ( $key === 'field' ) {
					$conditional_fields[] = $item;
				}
			}
		);

		return $conditional_fields;
	}

	/**
	 * Get Payment Options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Form data of forms on current page.
	 *
	 * @return array
	 */
	private function get_payment_options( $forms ) {

		$options = [];

		foreach ( $forms as $form_id => $form ) {

			if ( ! isset( $form['payments'][ Plugin::SLUG ] ) ) {
				continue;
			}

			$options[ $form_id ]['enable_one_time']    = Helpers::is_paypal_commerce_single_enabled( $form );
			$options[ $form_id ]['enable_recurring']   = Helpers::is_paypal_commerce_subscriptions_enabled( $form );
			$options[ $form_id ]['recurring_no_rules'] = Helpers::get_subscription_plan_id_without_rule( $form ) !== '';

			foreach ( $form['fields'] as $field ) {

				if ( $field['type'] !== 'paypal-commerce' ) {
					continue;
				}

				$options[ $form_id ]['button_size']     = isset( $field['button_size'] ) ? $field['button_size'] : '';
				$options[ $form_id ]['paypal_checkout'] = isset( $field['paypal_checkout'] );
				$options[ $form_id ]['credit_card']     = isset( $field['credit_card'] );
				$options[ $form_id ]['shape']           = $field['shape'];
				$options[ $form_id ]['color']           = $field['color'];

				if ( ! isset( $field['credit_card'] ) ) {
					continue;
				}

				$options[ $form_id ]['supported_cards'] = [
					isset( $field['amex'] ) ? 'american-express' : '',
					isset( $field['maestro'] ) ? 'maestro' : '',
					isset( $field['discover'] ) ? 'discover' : '',
					isset( $field['mastercard'] ) ? 'master-card' : '',
					isset( $field['visa'] ) ? 'visa' : '',
				];

				$options[ $form_id ]['sublabel_hide']      = isset( $field['sublabel_hide'] );
				$options[ $form_id ]['card_number']        = ! empty( $field['card_number'] ) ? $field['card_number'] : esc_html__( 'Card Number', 'wpforms-paypal-commerce' );
				$options[ $form_id ]['expiration_date']    = ! empty( $field['expiration_date'] ) ? $field['expiration_date'] : esc_html__( 'Expiration Date', 'wpforms-paypal-commerce' );
				$options[ $form_id ]['security_code']      = ! empty( $field['security_code'] ) ? $field['security_code'] : esc_html__( 'Security Code', 'wpforms-paypal-commerce' );
				$options[ $form_id ]['card_holder_enable'] = isset( $field['card_holder_enable'] );
				$options[ $form_id ]['card_holder_name']   = ! empty( $field['card_holder_name'] ) ? $field['card_holder_name'] : esc_html__( 'Card Holder Name', 'wpforms-paypal-commerce' );
			}
		}

		return $options;
	}

	/**
	 * Get Conditional Rules.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Form data of forms on current page.
	 *
	 * @return array
	 */
	private function get_conditional_rules( $forms ) {

		$rules = [];

		foreach ( $forms as $form_id => $form ) {

			if ( ! isset( $form['payments'][ Plugin::SLUG ] ) ) {
				continue;
			}

			$settings = $form['payments'][ Plugin::SLUG ];

			if (
				! empty( $settings['conditional_logic'] ) &&
				! empty( $settings['conditionals'] ) &&
				Helpers::is_paypal_commerce_single_enabled( $form )
			) {
				$rules[ $form_id ]['one_time'][0]['logic']  = $this->format_rules( $settings['conditionals'], $form );
				$rules[ $form_id ]['one_time'][0]['action'] = $settings['conditional_type'];
			}

			if ( empty( $settings['recurring'] ) || ! Helpers::is_paypal_commerce_subscriptions_enabled( $form ) ) {
				continue;
			}

			$recurring = $this->get_recurring_rules( $form, $settings );

			if ( ! empty( $recurring ) ) {
				$rules[ $form_id ]['recurring'] = $recurring;
			}
		}

		return $rules;
	}

	/**
	 * Get recurring rules.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form     Form data.
	 * @param array $settings Payment settings.
	 *
	 * @return array
	 */
	private function get_recurring_rules( $form, $settings ) {

		$rules = [];

		foreach ( $settings['recurring'] as $plan_id => $plan ) {

			if ( empty( $plan['conditional_logic'] ) || empty( $plan['conditionals'] ) ) {
				continue;
			}

			$rules[ $plan_id ]['logic']  = $this->format_rules( $plan['conditionals'], $form );
			$rules[ $plan_id ]['action'] = $plan['conditional_type'];
		}

		return $rules;
	}

	/**
	 * Format conditionals.
	 *
	 * @since 1.0.0
	 *
	 * @param array $conditionals Conditional rules.
	 * @param array $form         Form data.
	 *
	 * @return array
	 */
	private function format_rules( $conditionals, $form ) {

		$conditionals = $this->clear_empty_rules( $conditionals );

		foreach ( $conditionals as $group_id => $group ) {

			foreach ( $group as $rule_id => $rule ) {

				if (
					empty( $rule['operator'] ) ||
					( empty( $rule['value'] ) && ! in_array( $rule['operator'], [ 'e', '!e' ], true ) )
				) {
					continue;
				}

				$rule_field = $rule['field'];
				$rule_value = isset( $rule['value'] ) ? $rule['value'] : '';

				$conditionals[ $group_id ][ $rule_id ]['type']  = $form['fields'][ $rule_field ]['type'];
				$conditionals[ $group_id ][ $rule_id ]['value'] = $rule_value;

				if (
					( ! in_array( $rule['operator'], [ 'e', '!e' ], true ) ) &&
					in_array(
						$form['fields'][ $rule_field ]['type'],
						[
							'select',
							'checkbox',
							'radio',
						],
						true
					)
				) {
					$conditionals[ $group_id ][ $rule_id ]['value'] = $this->format_choices_rules( $form, $rule_field, $rule_value );
				}
			}
		}

		return $conditionals;
	}

	/**
	 * Format choices fields value.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form       Form data.
	 * @param string $rule_field Field ID.
	 * @param string $rule_value Field value.
	 *
	 * @return string
	 */
	private function format_choices_rules( $form, $rule_field, $rule_value ) {

		if ( ! empty( $form['fields'][ $rule_field ]['choices'][ $rule_value ]['value'] ) ) {
			return esc_attr( $form['fields'][ $rule_field ]['choices'][ $rule_value ]['value'] );
		}

		if ( ! empty( $form['fields'][ $rule_field ]['choices'][ $rule_value ]['label'] ) ) {
			return esc_attr( $form['fields'][ $rule_field ]['choices'][ $rule_value ]['label'] );
		}

		/* translators: %d - choice number. */
		return sprintf( esc_html__( 'Choice %d', 'wpforms-paypal-commerce' ), (int) $rule_field );
	}

	/**
	 * Clear conditionals array, remove empty rules and groups.
	 *
	 * @since 1.0.0
	 *
	 * @param array $conditionals Conditional rules.
	 *
	 * @return array
	 */
	private function clear_empty_rules( $conditionals ) {

		foreach ( $conditionals as $group_id => $group ) {

			foreach ( $group as $rule_id => $rule ) {

				if ( empty( $rule['field'] ) ) {
					unset( $conditionals[ $group_id ][ $rule_id ] );
				}
			}

			if ( empty( $conditionals[ $group_id ] ) ) {
				unset( $conditionals[ $group_id ] );
			}
		}

		return $conditionals;
	}
}
