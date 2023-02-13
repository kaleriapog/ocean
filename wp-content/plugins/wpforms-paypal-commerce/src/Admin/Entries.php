<?php

namespace WPFormsPaypalCommerce\Admin;

use WPFormsPaypalCommerce\Helpers;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce admin entries.
 *
 * @since 1.0.0
 */
class Entries {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_has_payment_gateway', [ $this, 'has_payment_gateway' ], 10, 2 );

		// Adjustments for the Entry Payment Details metabox.
		add_filter( 'wpforms_entry_details_payment_gateway',      [ $this, 'entry_details_payment_gateway' ], 10, 4 );
		add_filter( 'wpforms_entry_details_payment_transaction',  [ $this, 'entry_details_payment_transaction' ], 10, 4 );
		add_filter( 'wpforms_entry_details_payment_subscription', [ $this, 'entry_details_payment_subscription' ], 10, 4 );
		add_filter( 'wpforms_entry_details_payment_customer',     [ $this, 'entry_details_payment_customer' ], 10, 4 );
		add_filter( 'wpforms_entry_details_payment_total',        [ $this, 'entry_details_payment_total' ], 10, 4 );
	}

	/**
	 * Make PayPal Commerce payment gateway work on the Entries page.
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
	 * Set a Gateway name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $gateway    Initial gateway name.
	 * @param array  $entry_meta Entry meta data.
	 * @param object $entry      Submitted entry values.
	 * @param array  $form_data  Form data and settings.
	 *
	 * @return string
	 */
	public function entry_details_payment_gateway( $gateway, $entry_meta, $entry, $form_data ) {

		if ( ! $this->is_paypal_commerce_payment_type( $entry_meta ) ) {
			return $gateway;
		}

		return sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( $this->get_seller_dashboard_url( $entry_meta ) ),
			esc_html__( 'PayPal Commerce', 'wpforms-paypal-commerce' )
		);
	}

	/**
	 * Set a Transaction ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $transaction Initial transaction ID.
	 * @param array  $entry_meta  Entry meta data.
	 * @param object $entry       Submitted entry values.
	 * @param array  $form_data   Form data and settings.
	 *
	 * @return string
	 */
	public function entry_details_payment_transaction( $transaction, $entry_meta, $entry, $form_data ) {

		if (
			! $this->is_paypal_commerce_payment_type( $entry_meta ) ||
			empty( $entry_meta['payment_transaction'] )
		) {
			return $transaction;
		}

		return $this->get_transaction_link( $entry_meta );
	}

	/**
	 * Set a Subscription ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subscription Initial subscription ID.
	 * @param array  $entry_meta   Entry meta data.
	 * @param object $entry        Submitted entry values.
	 * @param array  $form_data    Form data and settings.
	 *
	 * @return string
	 */
	public function entry_details_payment_subscription( $subscription, $entry_meta, $entry, $form_data ) {

		if (
			! $this->is_paypal_commerce_payment_type( $entry_meta ) ||
			empty( $entry_meta['payment_subscription'] )
		) {
			return $subscription;
		}

		return $this->get_subscription_link( $entry_meta );
	}

	/**
	 * Maybe add recurring period to transaction total.
	 *
	 * @since 1.0.0
	 *
	 * @param string $total      Initial transaction total.
	 * @param array  $entry_meta Entry meta data.
	 * @param object $entry      Submitted entry values.
	 * @param array  $form_data  Form data and settings.
	 *
	 * @return string
	 */
	public function entry_details_payment_total( $total, $entry_meta, $entry, $form_data ) {

		if ( ! $this->is_paypal_commerce_payment_type( $entry_meta ) ) {
			return $total;
		}

		if ( ! empty( $entry_meta['payment_period'] ) ) {
			$total .= ' <span style="font-weight:400; color:#999; display:inline-block; margin-left:4px;"><i class="fa fa-refresh" aria-hidden="true"></i> ' . esc_html( $entry_meta['payment_period'] ) . '</span>';
		}

		return $total;
	}

	/**
	 * Set a Customer ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $customer_id Customer id.
	 * @param array  $entry_meta  Entry meta data.
	 * @param object $entry       Submitted entry values.
	 * @param array  $form_data   Form data and settings.
	 *
	 * @return string
	 */
	public function entry_details_payment_customer( $customer_id, $entry_meta, $entry, $form_data ) {

		if ( ! $this->is_paypal_commerce_payment_type( $entry_meta ) ) {
			return $customer_id;
		}

		return isset( $entry_meta['payment_customer'] ) ? $entry_meta['payment_customer'] : '';
	}

	/**
	 * Check if PayPal Commerce is set as a payment type inside entry meta.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry_meta Entry meta data.
	 *
	 * @return bool
	 */
	private function is_paypal_commerce_payment_type( $entry_meta ) {

		return ! empty( $entry_meta['payment_type'] ) && $entry_meta['payment_type'] === Plugin::SLUG;
	}

	/**
	 * Retrieve a seller dashboard URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry_meta Entry data.
	 *
	 * @return string
	 */
	private function get_seller_dashboard_url( $entry_meta ) {

		return ! empty( $entry_meta['payment_mode'] ) && $entry_meta['payment_mode'] === 'test' ? 'https://www.sandbox.paypal.com/' : 'https://www.paypal.com/';
	}

	/**
	 * Retrieve a transaction link.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry_meta Entry data.
	 *
	 * @return string
	 */
	private function get_transaction_link( $entry_meta ) {

		$id  = $entry_meta['payment_transaction'];
		$url = $this->get_seller_dashboard_url( $entry_meta ) . 'activity/payment/' . $id;

		return sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $url ),
			esc_html( $id )
		);
	}

	/**
	 * Retrieve a subscription link.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry_meta Entry data.
	 *
	 * @return string
	 */
	private function get_subscription_link( $entry_meta ) {

		$id  = $entry_meta['payment_subscription'];
		$url = $this->get_seller_dashboard_url( $entry_meta ) . 'billing/subscriptions/' . $id;

		return sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $url ),
			esc_html( $id )
		);
	}
}
