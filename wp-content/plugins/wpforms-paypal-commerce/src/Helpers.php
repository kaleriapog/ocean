<?php

namespace WPFormsPaypalCommerce;

use stdClass;

/**
 * PayPal Commerce related helper methods.
 *
 * @since 1.0.0
 */
class Helpers {

	/**
	 * Sandbox mode.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SANDBOX = 'sandbox';

	/**
	 * Production mode.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const PRODUCTION = 'live';

	/**
	 * Determine whether PayPal Commerce single payment is enabled for a form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	public static function is_paypal_commerce_single_enabled( $form_data ) {

		return ! empty( $form_data['payments'][ Plugin::SLUG ]['enable_one_time'] );
	}

	/**
	 * Determine whether PayPal Commerce subscriptions payment is enabled for a form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	public static function is_paypal_commerce_subscriptions_enabled( $form_data ) {

		return ! empty( $form_data['payments'][ Plugin::SLUG ]['enable_recurring'] );
	}

	/**
	 * Determine whether PayPal Commerce payment is enabled for a form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	public static function is_paypal_commerce_enabled( $form_data ) {

		return self::is_paypal_commerce_single_enabled( $form_data ) || self::is_paypal_commerce_subscriptions_enabled( $form_data );
	}

	/**
	 * Determine whether subscriptions have all required data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	public static function is_subscriptions_configured( $form_data ) {

		if ( ! self::is_paypal_commerce_subscriptions_enabled( $form_data ) ) {
			return true;
		}

		foreach ( $form_data['payments'][ Plugin::SLUG ]['recurring'] as $plan ) {

			if ( ! empty( $plan['pp_plan_id'] ) ) {
				continue;
			}

			return false;
		}

		return true;
	}

	/**
	 * Determine whether PayPal Commerce is in use on a page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms Forms data (e.g. forms on a current page).
	 *
	 * @return bool
	 */
	public static function is_paypal_commerce_forms_enabled( $forms ) {

		foreach ( $forms as $form_data ) {

			if ( self::is_paypal_commerce_enabled( $form_data ) && self::is_subscriptions_configured( $form_data ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether a form has a PayPal Commerce field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $forms    Form data (e.g. forms on a current page).
	 * @param bool  $multiple Must be 'true' if $forms contain multiple forms.
	 *
	 * @return bool
	 */
	public static function has_paypal_commerce_field( $forms, $multiple = false ) {

		return wpforms_has_field_type( 'paypal-commerce', $forms, $multiple );
	}

	/**
	 * Determine whether PayPal Commerce is in sandbox mode.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_sandbox_mode() {

		return self::get_mode() === self::SANDBOX;
	}

	/**
	 * Determine whether PayPal Commerce is in production mode.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_production_mode() {

		return self::get_mode() === self::PRODUCTION;
	}

	/**
	 * Retrieve PayPal Commerce mode.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_mode() {

		return wpforms_setting( 'paypal-commerce-sandbox-mode' ) ? self::SANDBOX : self::PRODUCTION;
	}

	/**
	 * Set/update PayPal Commerce mode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal mode that will be set.
	 *
	 * @return bool
	 */
	public static function set_mode( $mode ) {

		$key              = 'paypal-commerce-sandbox-mode';
		$settings         = (array) get_option( 'wpforms_settings', [] );
		$settings[ $key ] = $mode === self::SANDBOX;

		return update_option( 'wpforms_settings', $settings );
	}

	/**
	 * Retrieve PayPal Commerce available modes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_available_modes() {

		return [ self::SANDBOX, self::PRODUCTION ];
	}

	/**
	 * Validate PayPal Commerce mode to ensure it's either 'production' or 'sandbox'.
	 * If given mode is invalid, fetches current PayPal mode.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode to validate.
	 *
	 * @return string
	 */
	public static function validate_mode( $mode ) {

		return in_array( $mode, self::get_available_modes(), true ) ? $mode : self::get_mode();
	}

	/**
	 * Retrieve the WPForms > Payments settings page URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_settings_page_url() {

		return add_query_arg(
			[
				'page' => 'wpforms-settings',
				'view' => 'payments',
			],
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Convert time interval.
	 *
	 * @since 1.0.0
	 *
	 * @param string $recurring_times Time interval.
	 *
	 * @return object
	 */
	public static function get_frequency( $recurring_times ) {

		$data = new stdClass();

		switch ( $recurring_times ) {
			case 'weekly':
				$data->interval_unit  = 'WEEK';
				$data->interval_count = 1;
				break;

			case 'monthly':
				$data->interval_unit  = 'MONTH';
				$data->interval_count = 1;
				break;

			case 'quarterly':
				$data->interval_unit  = 'MONTH';
				$data->interval_count = 3;
				break;

			case 'semi-yearly':
				$data->interval_unit  = 'MONTH';
				$data->interval_count = 6;
				break;

			default:
				$data->interval_unit  = 'YEAR';
				$data->interval_count = 1;
				break;
		}

		return $data;
	}

	/**
	 * Get PayPal Commerce field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Form fields.
	 *
	 * @return array
	 */
	public static function get_paypal_field( $fields ) {

		foreach ( $fields as $field ) {

			if ( empty( $field['type'] ) || $field['type'] !== 'paypal-commerce' ) {
				continue;
			}

			return $field;
		}

		return [];
	}

	/**
	 * Log payment errors.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $title    Error title.
	 * @param string       $form_id  Form ID.
	 * @param array|string $messages Error messages.
	 * @param string       $level    Error level to add to 'payment' error level.
	 */
	public static function log_errors( $title, $form_id, $messages = [], $level = 'error' ) {

		wpforms_log(
			$title,
			$messages,
			[
				'type'    => [ 'payment', $level ],
				'form_id' => $form_id,
			]
		);
	}

	/**
	 * Get subscription plan id without rules.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form settings.
	 *
	 * @return string
	 */
	public static function get_subscription_plan_id_without_rule( $form_data ) {

		if ( ! self::is_paypal_commerce_subscriptions_enabled( $form_data ) ) {
			return '';
		}

		foreach ( $form_data['payments'][ Plugin::SLUG ]['recurring'] as $plan_id => $recurring ) {

			if ( empty( $recurring['conditional_logic'] ) ) {
				return $plan_id;
			}
		}

		return '';
	}
}
