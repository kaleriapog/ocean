<?php

namespace WPFormsPaypalCommerce\Admin;

use WPForms_Builder_Panel_Settings;
use WPFormsPaypalCommerce\Plugin;
use WPFormsPaypalCommerce\Api\Api;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use stdClass;

/**
 * Builder related functionality.
 *
 * @since 1.0.0
 */
class Builder {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueues' ] );
		add_filter( 'wpforms_builder_strings',  [ $this, 'javascript_strings' ], 10, 2 );
		add_action( 'wpforms_form_settings_notifications_single_after', [ $this, 'notification_settings' ], 10, 2 );
		add_filter( 'wpforms_has_payment_gateway', [ $this, 'has_payment_gateway' ], 10, 2 );

		add_filter( 'wpforms_builder_save_form_response_data', [ $this, 'maybe_add_plan_data' ], 10, 3 );
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $view Current view.
	 */
	public function enqueues( $view ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-builder-paypal-commerce',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/js/builder-paypal-commerce{$min}.js",
			[ 'wpforms-builder' ],
			WPFORMS_PAYPAL_COMMERCE_VERSION,
			true
		);

		wp_enqueue_style(
			'wpforms-builder-paypal-commerce',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/css/builder-paypal-commerce{$min}.css",
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION
		);
	}

	/**
	 * Add localized strings.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $strings Form builder JS strings.
	 * @param object $form    Current form.
	 *
	 * @return array
	 */
	public function javascript_strings( $strings, $form ) {

		$strings['paypal_commerce_connection_required'] = wp_kses(
			__( '<p>You must connect to PayPal Commerce before using the PayPal Commerce field.</p><p>To connect your account, please go to <strong>WPForms Settings » Payments » PayPal Commerce</strong> and press <strong>Connect with PayPal Commerce</strong> button.</p>', 'wpforms-paypal-commerce' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		$strings['paypal_commerce_payments_enabled_required'] = wp_kses(
			__( '<p>PayPal Commerce Payments must be enabled when using the PayPal Commerce field.</p><p>To proceed, please go to <strong>Payments » PayPal Commerce</strong> and select <strong>payment option</strong>.</p>', 'wpforms-paypal-commerce' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		$strings['paypal_commerce_ajax_required'] = wp_kses(
			__( '<p>AJAX form submissions are required when using the PayPal Commerce field.</p><p>To proceed, please go to <strong>Settings » General</strong> and check <strong>Enable AJAX form submission</strong>.</p>', 'wpforms-paypal-commerce' ),
			[
				'p'      => [],
				'strong' => [],
			]
		);

		$strings['paypal_commerce_plan_name_disabled']       = esc_html__( 'The plan name can’t be changed once you save it. Please create a new plan.', 'wpforms-paypal-commerce' );
		$strings['paypal_commerce_product_type_disabled']    = esc_html__( 'The product type can’t be changed once you save it. Please create a new plan.', 'wpforms-paypal-commerce' );
		$strings['paypal_commerce_recurring_times_disabled'] = esc_html__( 'The recurring plan can’t be changed once you save it. Please create a new plan.', 'wpforms-paypal-commerce' );

		return $strings;
	}

	/**
	 * Add a checkbox to form notification settings.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $settings WPForms_Builder_Panel_Settings class instance.
	 * @param int                            $id       Subsection ID.
	 */
	public function notification_settings( $settings, $id ) {

		if ( empty( $settings->form_data ) ) {
			return;
		}

		wpforms_panel_field(
			'toggle',
			'notifications',
			Plugin::SLUG,
			$settings->form_data,
			esc_html__( 'Enable for PayPal Commerce completed payments', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'settings',
				'class'       => ! Helpers::is_paypal_commerce_enabled( $settings->form_data ) ? 'wpforms-hidden' : '',
				'input_class' => 'wpforms-radio-group wpforms-radio-group-' . $id . '-notification-by-status wpforms-radio-group-item-paypal_commerce wpforms-notification-by-status-alert',
				'subsection'  => $id,
				'tooltip'     => wp_kses(
					__( 'When enabled this notification will <em>only</em> be sent when a PayPal Commerce payment has been successfully <strong>completed</strong>.', 'wpforms-paypal-commerce' ),
					[
						'em'     => [],
						'strong' => [],
					]
				),
				'data'        => [
					'radio-group'    => $id . '-notification-by-status',
					'provider-title' => esc_html__( 'PayPal Commerce completed payments', 'wpforms-paypal-commerce' ),
				],
			]
		);
	}

	/**
	 * Make PayPal Commerce payment gateway work on the Builder page.
	 * It affects on the `Disable storing entry` setting.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $result    Initial value.
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	public function has_payment_gateway( $result, $form_data ) {

		if ( $result ) {
			return $result;
		}

		return Helpers::is_paypal_commerce_enabled( $form_data );
	}

	/**
	 * Maybe add plan data.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $response_data Response data.
	 * @param string $form_id       Form ID.
	 * @param array  $data          Form data.
	 *
	 * @return array
	 */
	public function maybe_add_plan_data( $response_data, $form_id, $data ) {

		// Check required data, settings, and permissions.
		if (
			empty( $form_id ) ||
			! Helpers::is_paypal_commerce_subscriptions_enabled( $data ) ||
			! wpforms_current_user_can( 'edit_forms' )
		) {
			return $response_data;
		}

		$response_data['paypal_commerce_plans'] = $this->update_subscription_plans( $data );

		return $response_data;
	}

	/**
	 * Update subscription plans data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return array
	 */
	private function update_subscription_plans( $form_data ) {

		$connection = Connection::get();
		$api        = wpforms_paypal_commerce()->get_api( $connection );
		$settings   = $form_data['payments'][ Plugin::SLUG ];
		$data       = [];

		if ( ! isset( $settings['recurring'] ) || is_null( $api ) ) {
			return $data;
		}

		$has_new_plans = false;

		foreach ( $settings['recurring'] as $plan_id => $plan ) {

			if ( ! empty( $plan['pp_plan_id'] ) ) {
				continue;
			}

			$name = sprintf( 'Form ID #%d: %s', absint( $form_data['id'] ), $plan['name'] );

			$plan['pp_product_id'] = $this->create_subscription_product( $name, $plan['product_type'], $api, $form_data['id'] );

			$data[ $plan_id ]['pp_product_id'] = $plan['pp_product_id'];
			$data[ $plan_id ]['pp_plan_id']    = $this->create_subscription_plan( $plan, $name, $api, $form_data['id'] );

			if ( empty( $data[ $plan_id ]['pp_plan_id'] ) ) {
				continue;
			}

			$has_new_plans = true;
		}

		// Update form if new plans were added.
		if ( $has_new_plans ) {
			$form_data['payments'][ Plugin::SLUG ]['recurring'] = array_replace_recursive( $settings['recurring'], $data );

			wp_update_post(
				[
					'ID'           => $form_data['id'],
					'post_content' => wpforms_encode( $form_data ),
				]
			);
		}

		return $data;
	}

	/**
	 * Create subscription product.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name         Product name.
	 * @param string $product_type Product type.
	 * @param Api    $api          Api object.
	 * @param string $form_id      Form ID.
	 *
	 * @return string
	 */
	private function create_subscription_product( $name, $product_type, $api, $form_id ) {

		$product_data = [
			'name' => $name,
			'type' => $product_type,
		];

		$product_response = $api->create_product( $product_data );

		if ( $product_response->has_errors() ) {
			Helpers::log_errors(
				'Create PayPal Subscription Product error.',
				$form_id,
				$product_response->get_response_message()
			);

			return '';
		}

		$body = $product_response->get_body();

		return isset( $body['id'] ) ? $body['id'] : '';
	}

	/**
	 * Create subscription plan.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $plan    Plan data.
	 * @param string $name    Plan name.
	 * @param Api    $api     Api object.
	 * @param string $form_id Form ID.
	 *
	 * @return string
	 */
	private function create_subscription_plan( $plan, $name, $api, $form_id ) {

		if ( empty( $plan['pp_product_id'] ) ) {
			return '';
		}

		$billing_cycle = new stdClass();

		$billing_cycle->frequency    = Helpers::get_frequency( $plan['recurring_times'] );
		$billing_cycle->tenure_type  = 'REGULAR';
		$billing_cycle->sequence     = 1;
		$billing_cycle->total_cycles = 0;

		$billing_cycle->pricing_scheme                             = new stdClass();
		$billing_cycle->pricing_scheme->fixed_price                = new stdClass();
		$billing_cycle->pricing_scheme->fixed_price->value         = 1;
		$billing_cycle->pricing_scheme->fixed_price->currency_code = strtoupper( wpforms_get_currency() );

		$plan_data = [
			'product_id'          => $plan['pp_product_id'],
			'name'                => $name,
			'status'              => 'ACTIVE',
			'billing_cycles'      => [ $billing_cycle ],
			'payment_preferences' => [
				'payment_failure_threshold' => isset( $plan['bill_retry'] ) ? 2 : 1,
			],
		];

		$plan_response = $api->create_plan( $plan_data );

		if ( $plan_response->has_errors() ) {
			Helpers::log_errors(
				'PayPal Subscription Plan creation error.',
				$form_id,
				$plan_response->get_response_message()
			);

			return '';
		}

		$body = $plan_response->get_body();

		return isset( $body['id'] ) ? $body['id'] : '';
	}
}
