<?php

namespace WPFormsPaypalCommerce\Admin;

use WPForms\Admin\Notice;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce settings.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_settings_enqueue',  [ $this, 'enqueue_scripts' ] );
		add_filter( 'wpforms_admin_strings',     [ $this, 'javascript_strings' ] );

		add_filter( 'wpforms_settings_defaults', [ $this, 'register' ], 12 );
		add_action( 'wpforms_settings_init',     [ $this, 'display_notice' ] );
	}

	/**
	 * Enqueue Settings scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-admin-settings-paypal-commerce',
			WPFORMS_PAYPAL_COMMERCE_URL . "assets/js/settings-paypal-commerce{$min}.js",
			[ 'jquery' ],
			WPFORMS_PAYPAL_COMMERCE_VERSION,
			true
		);

		wp_enqueue_script(
			'wpforms-paypal-commerce-partner-js',
			'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js',
			[],
			WPFORMS_PAYPAL_COMMERCE_VERSION,
			true
		);
	}

	/**
	 * Localize needed strings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $strings JS strings.
	 *
	 * @return array
	 */
	public function javascript_strings( $strings ) {

		$strings[ Plugin::SLUG ] = [
			'mode_update'      => wp_kses(
				__(
					'<p>Switching sandbox/live modes requires PayPal Commerce account reconnection.</p><p>Press the <em>"Connect with PayPal Commerce"</em> button after saving the settings to reconnect.</p>',
					'wpforms-paypal-commerce'
				),
				[
					'p'  => [],
					'em' => [],
				]
			),
			'connection_error' => esc_html__( 'Something went wrong while performing the authorization request.', 'wpforms-paypal-commerce' ),
			'nonce'            => wp_create_nonce( Connect::ONBOARDING_NONCE_ACTION ),
		];

		return $strings;
	}

	/**
	 * Register Settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Array of current form settings.
	 *
	 * @return array
	 */
	public function register( $settings ) {

		$settings['payments']['paypal-commerce-heading'] = [
			'id'       => 'paypal-commerce-heading',
			'content'  => $this->get_heading_content(),
			'type'     => 'content',
			'no_label' => true,
			'class'    => [ 'section-heading' ],
		];

		foreach ( Helpers::get_available_modes() as $mode ) {

			$settings['payments'][ 'paypal-commerce-connection-status-' . $mode ] = [
				'id'        => 'paypal-commerce-connection-status-' . $mode,
				'name'      => esc_html__( 'Connection Status', 'wpforms-paypal-commerce' ),
				'content'   => $this->get_connection_status_content( $mode ),
				'type'      => 'content',
				'is_hidden' => Helpers::get_mode() !== $mode,
			];
		}

		$settings['payments']['paypal-commerce-sandbox-mode'] = [
			'id'   => 'paypal-commerce-sandbox-mode',
			'name' => esc_html__( 'Test Mode', 'wpforms-paypal-commerce' ),
			'desc' => sprintf(
				wp_kses( /* translators: %s - WPForms.com URL for PayPal Commerce payment with more details. */
					__( 'Check this option to prevent PayPal Commerce from processing live transactions. Please see our <a href="%s" target="_blank" rel="noopener noreferrer">PayPal Commerce documentation</a> for full details.', 'wpforms-paypal-commerce' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				wpforms_utm_link( 'https://wpforms.com/docs/testing-payments-with-the-paypal-commerce-addon/', 'Payment Settings', 'PayPal Commerce Test Mode' )
			),
			'type' => 'checkbox',
		];

		return $settings;
	}

	/**
	 * Display admin error notice if something wrong with the PayPal Commerce settings.
	 *
	 * @since 1.0.0
	 *
	 * @param object $settings WPForms_Settings instance.
	 */
	public function display_notice( $settings ) {

		$connection = Connection::get();

		if ( ! $connection ) {
			return;
		}

		if ( $connection->is_access_token_expired() ) {
			$connection = Connect::refresh_access_token( $connection );
		}

		if ( $connection->is_client_token_expired() ) {
			$connection = Connect::refresh_client_token( $connection );
		}

		$this->maybe_display_connection_notice( $connection );
	}

	/**
	 * Display admin error notice if a connection exists, but is not ready to use.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Connection data.
	 */
	private function maybe_display_connection_notice( $connection ) {

		if ( ! $connection->is_configured() ) {
			Notice::error( esc_html__( 'Heads up! Your connection to PayPal Commerce is not complete. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) );

			return;
		}

		if ( ! $connection->is_valid() ) {
			Notice::error( esc_html__( 'Heads up! Your connection to PayPal Commerce is not valid. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) );

			return;
		}
	}

	/**
	 * Retrieve a section header content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_heading_content() {

		return '<h4>' . esc_html__( 'PayPal Commerce', 'wpforms-paypal-commerce' ) . '</h4><p>' .
			sprintf(
				wp_kses( /* translators: %s - WPForms.com PayPal Commerce documentation article URL. */
					__( 'Easily collect PayPal Checkout and credit card payments with PayPal Commerce. To get started, see our <a href="%s" target="_blank" rel="noopener noreferrer">PayPal Commerce documentation</a>.', 'wpforms-paypal-commerce' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				wpforms_utm_link( 'https://wpforms.com/docs/paypal-commerce-addon/#install', 'Payment Settings', 'PayPal Commerce Documentation' )
			) .
			'</p>';
	}

	/**
	 * Retrieve a Connection Status setting content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_connection_status_content( $mode ) {

		$connection = Connection::get( $mode );

		if ( ! $connection ) {
			return $this->get_disconnected_status_content( $mode );
		}

		$content = $this->get_disabled_status_content( $connection );

		if ( ! empty( $content ) ) {
			return $content;
		}

		return $this->get_enabled_status_content( $connection );
	}

	/**
	 * Retrieve setting content when a connection is disabled.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Connection data.
	 *
	 * @return string
	 */
	private function get_disabled_status_content( $connection ) {

		if ( ! $connection->get_access_token() ) {
			return $this->get_missing_access_token_content( $connection->get_mode() );
		}

		if ( ! $connection->get_client_token() ) {
			return $this->get_missing_client_token_content( $connection->get_mode() );
		}

		if ( ! $connection->is_configured() ) {
			return $this->get_missing_status_content( $connection->get_mode() );
		}

		if ( ! $connection->is_valid() ) {
			return $this->get_invalid_status_content( $connection );
		}

		return '';
	}

	/**
	 * Retrieve setting content when a connection is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Connection data.
	 *
	 * @return string
	 */
	private function get_enabled_status_content( $connection ) {

		return '<span class="wpforms-paypal-commerce-connected">✅ ' . $this->get_connected_status_content( $connection->get_mode() ) . $this->get_disconnect_button( $connection->get_mode() ) . '</span>';
	}

	/**
	 * Retrieve a Connected Status setting content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_connected_status_content( $mode ) {

		return sprintf(
			wp_kses( /* translators: %s - PayPal Commerce mode. */
				__( 'Connected to PayPal in <strong>%s</strong> mode.', 'wpforms-paypal-commerce' ),
				[
					'strong' => [],
				]
			),
			$mode === Helpers::SANDBOX ? esc_html__( 'Sandbox', 'wpforms-paypal-commerce' ) : esc_html__( 'Production', 'wpforms-paypal-commerce' )
		);
	}

	/**
	 * Retrieve a Disconnected Status setting content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_disconnected_status_content( $mode ) {

		$connect_url = wpforms_paypal_commerce()->get_connect()->get_connect_url( $mode );

		if ( empty( $connect_url ) ) {
			return '<p>' . $this->get_warning_icon() . sprintf(
				wp_kses( /* translators: %s - WPForms Payments page URL. */
					__( 'There’s a temporary problem with the connection to PayPal Commerce. Please click <a href="%s" rel="noopener noreferrer">here</a> to try again.', 'wpforms-paypal-commerce' ),
					[
						'a' => [
							'href' => [],
							'rel'  => [],
						],
					]
				),
				esc_url(
					add_query_arg(
						[
							'paypal_commerce_refresh_signup' => true,
						],
						Helpers::get_settings_page_url()
					)
				)
			) . '</p>';
		}

		return $this->get_connect_button( $connect_url ) .
			'<p class="desc">' .
			sprintf(
				wp_kses( /* translators: %s - WPForms.com PayPal Commerce documentation article URL. */
					__( 'Securely connect to PayPal Commerce with just a few clicks to begin accepting payments! <a href="%s" target="_blank" rel="noopener noreferrer">Learn more</a> about connecting with PayPal Commerce.', 'wpforms-paypal-commerce' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				wpforms_utm_link( 'https://wpforms.com/docs/paypal-commerce-addon/#connect', 'Payment Settings', 'PayPal Commerce Learn More' )
			) .
			'</p>';
	}

	/**
	 * Retrieve a connection is missing status content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_missing_status_content( $mode ) {

		return $this->get_warning_icon() . esc_html__( 'Your connection to PayPal Commerce is not complete. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) . $this->get_disconnect_button( $mode );
	}

	/**
	 * Retrieve a connection is missing access token content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_missing_access_token_content( $mode ) {

		return $this->get_warning_icon() . esc_html__( 'Your PayPal Commerce access token is not valid. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) . $this->get_disconnect_button( $mode );
	}

	/**
	 * Retrieve a connection is missing client token content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 *
	 * @return string
	 */
	private function get_missing_client_token_content( $mode ) {

		return $this->get_warning_icon() . esc_html__( 'Your PayPal Commerce client token is not valid. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) . $this->get_disconnect_button( $mode );
	}

	/**
	 * Retrieve a connection invalid status content.
	 *
	 * @since 1.0.0
	 *
	 * @param Connection $connection Connection data.
	 *
	 * @return string
	 */
	private function get_invalid_status_content( $connection ) {

		$api = wpforms_paypal_commerce()->get_api( $connection );

		if ( is_null( $api ) ) {
			return '';
		}

		$permissions = $api->get_merchant_info();

		$mode = $connection->get_mode();

		$content = $this->get_warning_icon() . $this->get_connected_status_content( $mode );

		if ( empty( $permissions['payments_receivable'] ) ) {
			$content .= '<p>' . $this->get_warning_icon() . __( 'Payments are disabled.', 'wpforms-paypal-commerce' ) . '</p>';
		}

		if ( empty( $permissions['primary_email_confirmed'] ) ) {
			$content .= '<p>' . $this->get_warning_icon() . __( 'Primary email unconfirmed.', 'wpforms-paypal-commerce' ) . '</p>';
		}

		if ( ! isset( $permissions['products'][0]['vetting_status'] ) || $permissions['products'][0]['vetting_status'] !== 'SUBSCRIBED' ) {
			$content .= '<p>' . $this->get_warning_icon() . __( 'Credit Card field support is disabled.', 'wpforms-paypal-commerce' ) . '</p>';
		}

		if ( ! isset( $permissions['products'][1]['vetting_status'] ) || $permissions['products'][1]['vetting_status'] !== 'SUBSCRIBED' ) {
			$content .= '<p>' . $this->get_warning_icon() . __( 'PayPal Checkout support is disabled.', 'wpforms-paypal-commerce' ) . '</p>';
		}

		$content .= '<p>' . esc_html__( 'Your PayPal Commerce connection is not valid. Please reconnect your PayPal Commerce account.', 'wpforms-paypal-commerce' ) . '</p>';
		$content .= '<p>' . $this->get_disconnect_button( $mode, false ) . '</p>';

		return $content;
	}

	/**
	 * Retrieve the Connect button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $connect_url Connect URL.
	 *
	 * @return string
	 */
	private function get_connect_button( $connect_url ) {

		$button = sprintf(
			'<a target="_blank" class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey" href="%1$s" title="%2$s" data-paypal-onboard-complete="wpformsPaypalOnboardCompleted" data-paypal-button="true">%3$s</a>',
			esc_url( $connect_url ),
			esc_attr__( 'Connect PayPal Commerce account', 'wpforms-paypal-commerce' ),
			esc_html__( 'Connect with PayPal Commerce', 'wpforms-paypal-commerce' )
		);

		return '<p>' . $button . '</p>';
	}

	/**
	 * Retrieve the Disconnect button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode PayPal Commerce mode.
	 * @param bool   $wrap Optional. Wrap a button HTML element or not.
	 *
	 * @return string
	 */
	private function get_disconnect_button( $mode, $wrap = true ) {

		$button = sprintf(
			'<a class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey" href="%1$s" title="%2$s">%3$s</a>',
			esc_url( wpforms_paypal_commerce()->get_connect()->get_disconnect_url( $mode ) ),
			esc_attr__( 'Disconnect PayPal Commerce account', 'wpforms-paypal-commerce' ),
			esc_html__( 'Disconnect', 'wpforms-paypal-commerce' )
		);

		return $wrap ? '<p>' . $button . '</p>' : $button;
	}

	/**
	 * Retrieve the Warning icon emoji.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_warning_icon() {

		return '<span style="font-family: Consolas, Courier New, monospace, Segoe UI Emoji">⚠️ </span>';
	}
}
