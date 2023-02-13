<?php

namespace WPFormsPaypalCommerce\Admin;

use WPForms_Payment;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;
use WPFormsPaypalCommerce\Plugin;

/**
 * PayPal Commerce Form Builder payment registration.
 *
 * @since 1.0.0
 */
class PaypalCommercePayment extends WPForms_Payment {

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_PAYPAL_COMMERCE_VERSION;
		$this->name     = 'PayPal Commerce';
		$this->slug     = Plugin::SLUG;
		$this->priority = 1;
		$this->icon     = WPFORMS_PAYPAL_COMMERCE_URL . 'assets/images/addon-icon-paypal-commerce.png';

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_payment_builder_content_recurring_before', [ $this, 'builder_content_recurring_payment_before_content' ] );
	}

	/**
	 * Display content inside the panel content area.
	 *
	 * @since 1.0.0
	 */
	public function builder_content() {

		if ( ! $this->is_connection_ok() ) {
			return;
		}

		$this->alert_payment_content();

		$this->alert_paypal_standard();

		$hide_class = ! Helpers::has_paypal_commerce_field( $this->form_data ) || $this->is_paypal_standard_enabled() ? 'wpforms-hidden' : '';

		echo '<div id="wpforms-panel-content-section-payment-paypal-commerce" class="' . esc_attr( $hide_class ) . '">';
			parent::builder_content();
		echo '</div>';
	}

	/**
	 * Display alert payment content inside the panel content area.
	 *
	 * @since 1.0.0
	 */
	private function alert_payment_content() {

		$hide_class = Helpers::has_paypal_commerce_field( $this->form_data ) || $this->is_paypal_standard_enabled() ? 'wpforms-hidden' : '';

		?>
		<div id="wpforms-paypal-commerce-credit-card-alert" class="wpforms-alert wpforms-alert-info <?php echo esc_attr( $hide_class ); ?>">

			<?php $this->alert_icon(); ?>
			<div class="wpforms-builder-payment-settings-default-content">
				<p><?php esc_html_e( 'To use PayPal Commerce, first add the PayPal Commerce field to your form.', 'wpforms-paypal-commerce' ); ?></p>
				<p><?php $this->learn_more_link(); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Display alert PayPal Standard content inside the panel content area.
	 *
	 * @since 1.0.0
	 */
	private function alert_paypal_standard() {

		$hide_class = $this->is_paypal_standard_enabled() ? '' : 'wpforms-hidden';

		?>
		<p id="wpforms-paypal-commerce-paypal-standard-alert" class="wpforms-alert wpforms-alert-warning <?php echo esc_attr( $hide_class ); ?>">
			<?php esc_html_e( 'The PayPal Commerce addon can\'t be activated while PayPal Standard is in use. Please deactivate the PayPal Standard addon and try again.', 'wpforms-paypal-commerce' ); ?>
		</p>
		<?php
	}

	/**
	 * Check if PayPal standard enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_paypal_standard_enabled() {

		return ! empty( $this->form_data['payments']['paypal_standard']['enable'] ) && class_exists( 'WPForms_Paypal_Standard' );
	}

	/**
	 * Get content inside the one time payment area.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_builder_content_one_time_content() {

		$content = wpforms_panel_field(
			'select',
			$this->slug,
			'name',
			$this->form_data,
			esc_html__( 'Name', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'name' ],
				'placeholder' => esc_html__( '--- Select a Field ---', 'wpforms-paypal-commerce' ),
				'tooltip'     => esc_html__( "Select the field that contains the buyer's name. This field is optional.", 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'billing_email',
			$this->form_data,
			esc_html__( 'Email', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'email' ],
				'placeholder' => esc_html__( '--- Select a Field ---', 'wpforms-paypal-commerce' ),
				'tooltip'     => esc_html__( "Select the field that contains the buyer's email address. This field is optional.", 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'billing_address',
			$this->form_data,
			esc_html__( 'Billing Address', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'address' ],
				'placeholder' => esc_html__( '--- Select a Field ---', 'wpforms-paypal-commerce' ),
				'tooltip'     => esc_html__( "Select the field that contains the buyer's address. This field is optional.", 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'shipping_address',
			$this->form_data,
			esc_html__( 'Shipping Address', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'payments',
				'field_map'   => [ 'address' ],
				'placeholder' => esc_html__( '--- Select a Field ---', 'wpforms-paypal-commerce' ),
				'tooltip'     => esc_html__( "Select the field that contains the buyer's shipping address. This field is optional.", 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'text',
			$this->slug,
			'payment_description',
			$this->form_data,
			esc_html__( 'Payment Description', 'wpforms-paypal-commerce' ),
			[
				'parent'  => 'payments',
				'tooltip' => esc_html__( 'Enter a description for this payment.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_conditional_logic()->builder_block(
			[
				'form'        => $this->form_data,
				'type'        => 'panel',
				'panel'       => $this->slug,
				'parent'      => 'payments',
				'actions'     => [
					'go' => esc_html__( 'Process', 'wpforms-paypal-commerce' ),
				],
				'action_desc' => esc_html__( 'Process this charge if', 'wpforms-paypal-commerce' ),
				'reference'   => esc_html__( 'PayPal Commerce One-Time payment', 'wpforms-paypal-commerce' ),
			],
			false
		);

		return $content;
	}

	/**
	 * Get content inside the recurring payment area.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plan_id Plan id.
	 *
	 * @return string
	 */
	protected function get_builder_content_recurring_payment_content( $plan_id ) {

		$content = wpforms_panel_field(
			'text',
			$this->slug,
			'pp_product_id',
			$this->form_data,
			'',
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'class'      => 'wpforms-hidden',
			],
			false
		);

		$content .= wpforms_panel_field(
			'text',
			$this->slug,
			'pp_plan_id',
			$this->form_data,
			'',
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'class'      => 'wpforms-hidden',
			],
			false
		);

		$content .= wpforms_panel_field(
			'text',
			$this->slug,
			'name',
			$this->form_data,
			esc_html__( 'Plan Name', 'wpforms-paypal-commerce' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'tooltip'    => esc_html__( 'Enter a name for the recurring plan. Leave this field blank to use the default name.', 'wpforms-paypal-commerce' ),
				'class'      => 'wpforms-panel-content-section-payment-plan-name',
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'product_type',
			$this->form_data,
			esc_html__( 'Product Type', 'wpforms-paypal-commerce' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'default'    => 'digital',
				'options'    => [
					'digital'  => esc_html__( 'Digital', 'wpforms-paypal-commerce' ),
					'physical' => esc_html__( 'Physical', 'wpforms-paypal-commerce' ),
					'service'  => esc_html__( 'Service', 'wpforms-paypal-commerce' ),
				],
				'tooltip'    => esc_html__( 'Select the type of product that this subscription is for.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'recurring_times',
			$this->form_data,
			esc_html__( 'Recurring Times', 'wpforms-paypal-commerce' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'default'    => 'yearly',
				'options'    => [
					'weekly'      => esc_html__( 'Weekly', 'wpforms-paypal-commerce' ),
					'monthly'     => esc_html__( 'Monthly', 'wpforms-paypal-commerce' ),
					'quarterly'   => esc_html__( 'Quarterly', 'wpforms-paypal-commerce' ),
					'semi-yearly' => esc_html__( 'Semi-Yearly', 'wpforms-paypal-commerce' ),
					'yearly'      => esc_html__( 'Yearly', 'wpforms-paypal-commerce' ),
				],
				'tooltip'    => esc_html__( 'Select how often you would like the charge to recur.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'total_cycles',
			$this->form_data,
			esc_html__( 'Total Cycles', 'wpforms-paypal-commerce' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'index'      => $plan_id,
				'default'    => '0',
				'options'    => array_merge( [ '0' => esc_html__( 'Infinite', 'wpforms-paypal-commerce' ) ], range( 1, 99 ) ),
				'tooltip'    => esc_html__( 'Select how often you would like the charge to recur.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'select',
			$this->slug,
			'shipping_address',
			$this->form_data,
			esc_html__( 'Shipping Address', 'wpforms-paypal-commerce' ),
			[
				'parent'      => 'payments',
				'subsection'  => 'recurring',
				'index'       => $plan_id,
				'field_map'   => [ 'address' ],
				'placeholder' => esc_html__( '--- Select a Field ---', 'wpforms-paypal-commerce' ),
				'tooltip'     => esc_html__( "Select the field that contains the buyer's shipping address. This field is optional.", 'wpforms-paypal-commerce' ),
			],
			false
		);

		$content .= wpforms_panel_field(
			'toggle',
			$this->slug,
			'bill_retry',
			$this->form_data,
			esc_html__( 'Try to bill the customer again if the payment fails on the first attempt.', 'wpforms-paypal-commerce' ),
			[
				'parent'     => 'payments',
				'subsection' => 'recurring',
				'class'      => 'wpforms-builder-payment-settings-recurring-bill-retry',
				'index'      => $plan_id,
			],
			false
		);

		$content .= wpforms_conditional_logic()->builder_block(
			[
				'form'        => $this->form_data,
				'type'        => 'panel',
				'panel'       => $this->slug,
				'parent'      => 'payments',
				'subsection'  => 'recurring',
				'index'       => $plan_id,
				'actions'     => [
					'go' => esc_html__( 'Process', 'wpforms-paypal-commerce' ),
				],
				'action_desc' => esc_html__( 'Process this charge if', 'wpforms-paypal-commerce' ),
				'reference'   => esc_html__( 'PayPal Commerce Recurring payment', 'wpforms-paypal-commerce' ),
			],
			false
		);

		return $content;
	}

	/**
	 * Display content before the recurring payment area.
	 *
	 * @since 1.0.0
	 */
	public function builder_content_recurring_payment_before_content() {

		printf(
			'<p class="wpforms-alert wpforms-alert-warning">%s</p>',
			esc_html__( 'Credit card fields are not supported for subscriptions and will not display on your form.', 'wpforms-paypal-commerce' )
		);

		printf(
			'<p class="wpforms-alert wpforms-alert-warning">%s</p>',
			esc_html__( 'It\'s not possible to process multiple plans at the same time. If your conditional logic matches more than one plan, the form will process the first plan that matches your conditions.', 'wpforms-paypal-commerce' )
		);
	}

	/**
	 * Check if a connection exists and is ready to use.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_connection_ok() {

		$connection = Connection::get();

		if ( ! $connection ) {
		?>

			<?php $this->alert_icon(); ?>
			<div class="wpforms-builder-payment-settings-default-content">
				<p><?php esc_html_e( 'Connect to your PayPal account and start receiving payments today.', 'wpforms-paypal-commerce' ); ?></p>
				<p class="wpforms-builder-payment-settings-learn-more"><?php $this->learn_more_link(); ?></p>
				<?php
					printf(
						'<a href="%s" target="_blank" rel="noopener noreferrer" class="wpforms-btn wpforms-btn-md wpforms-btn-orange wpforms-paypal-commerce-auth">%s</a>',
						esc_url( Helpers::get_settings_page_url() . '#wpforms-setting-row-paypal-commerce-heading' ),
						esc_html__( 'Connect to PayPal', 'wpforms-paypal-commerce' )
					);
				?>
			</div>
			<?php

			return false;
		}

		if ( $connection->is_access_token_expired() ) {
			$connection = Connect::refresh_access_token( $connection );
		}

		if ( $connection->is_client_token_expired() ) {
			$connection = Connect::refresh_client_token( $connection );
		}

		if ( ! $connection->is_usable() ) {
			echo '<p class="wpforms-alert wpforms-alert-info">';
			printf(
				wp_kses( /* translators: %s - the WPForms Payments settings page URL. */
					__( "Heads up! PayPal Commerce payments can't be processed because there's a problem with the connection to PayPal. Please visit the <a href='%2\$s'>WPForms Settings</a> page to resolve the issue before trying again.", 'wpforms-paypal-commerce' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				Helpers::is_sandbox_mode() ? esc_html__( 'Sandbox', 'wpforms-paypal-commerce' ) : esc_html__( 'Production', 'wpforms-paypal-commerce' ),
				esc_url( Helpers::get_settings_page_url() . '#wpforms-setting-row-paypal-commerce-heading' )
			);
			echo '</p>';

			return false;
		}

		return true;
	}

	/**
	 * Alert icon.
	 *
	 * @since 1.0.0
	 */
	private function alert_icon() {

		printf(
			'<img src="%s" class="wpforms-builder-payment-settings-alert-icon" alt="%s">',
			esc_url( WPFORMS_PAYPAL_COMMERCE_URL . 'assets/images/addon-icon-paypal-commerce.png' ),
			esc_attr__( 'Connect WPForms to PayPal.', 'wpforms-paypal-commerce' )
		);
	}
	/**
	 * Learn more link.
	 *
	 * @since 1.0.0
	 */
	private function learn_more_link() {

		printf(
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="secondary-text">%s</a>',
			esc_url( wpforms_utm_link( 'https://wpforms.com/docs/paypal-commerce-addon/#install', 'builder-payments', 'PayPal Commerce Documentation' ) ),
			esc_html__( 'Learn more about our PayPal Commerce integration.', 'wpforms-paypal-commerce' )
		);
	}
}
