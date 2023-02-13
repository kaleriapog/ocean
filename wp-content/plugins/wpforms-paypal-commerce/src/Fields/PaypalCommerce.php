<?php

namespace WPFormsPaypalCommerce\Fields;

use WPForms_Field;
use WPFormsPaypalCommerce\Connection;
use WPFormsPaypalCommerce\Helpers;

/**
 * PayPal Commerce credit card field.
 *
 * @since 1.0.0
 */
class PaypalCommerce extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'PayPal Commerce', 'wpforms-paypal-commerce' );
		$this->type  = 'paypal-commerce';
		$this->icon  = 'fa-credit-card';
		$this->order = 89;
		$this->group = 'payment';

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		add_filter( 'wpforms_field_properties_paypal-commerce', [ $this, 'field_properties' ], 5, 3 );
		add_filter( 'wpforms_field_new_required', [ $this, 'default_required' ], 10, 2 );
		add_filter( 'wpforms_builder_field_button_attributes', [ $this, 'field_button_atts' ], 10, 3 );
		add_filter( 'wpforms_field_new_display_duplicate_button', [ $this, 'field_display_duplicate_button' ], 10, 2 );
		add_filter( 'wpforms_field_preview_display_duplicate_button', [ $this, 'field_display_duplicate_button' ], 10, 2 );
		add_filter( 'wpforms_pro_fields_entry_preview_is_field_support_preview_paypal-commerce_field', [ $this, 'entry_preview_availability' ], 10, 4 );

		add_action( 'wpforms_display_submit_after', [ $this, 'submit_button' ], 9, 2 );
		add_action( 'wpforms_frontend_foot_submit_classes', [ $this, 'submit_button_classes' ], 10, 2 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Remove primary for expanded formats since we have first, middle, last.
		unset( $properties['inputs']['primary'] );

		if ( ! isset( $field['credit_card'] ) ) {
			$properties['container']['class'][] = 'wpforms-field-paypal-commerce';
			$properties['label']['disabled']    = empty( $field['description'] );

			return $properties;
		}

		$default_labels        = $this->get_default_labels();
		$form_id               = absint( $form_data['id'] );
		$field_id              = absint( $field['id'] );
		$is_card_holder_enable = isset( $field['card_holder_enable'] );

		$props = [
			'inputs' => [
				'number' => [
					'attr'     => [
						'name'  => '',
						'value' => '',
					],
					'block'    => [
						'wpforms-field-paypal-commerce-number',
					],
					'class'    => [
						'wpforms-field-paypal-commerce-cardnumber',
					],
					'data'     => [],
					'id'       => "wpforms-{$form_id}-field_{$field_id}-cardnumber",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => [
						'hidden'   => ! empty( $field['sublabel_hide'] ),
						'value'    => ! empty( $field['card_number'] ) ? esc_html( $field['card_number'] ) : $default_labels['card_number'],
						'position' => 'after',
					],
				],
				'date'   => [
					'attr'     => [
						'name'  => '',
						'value' => '',
					],
					'block'    => [
						'wpforms-field-paypal-commerce-date',
					],
					'class'    => [
						'wpforms-field-paypal-commerce-carddate',
					],
					'data'     => [],
					'id'       => "wpforms-{$form_id}-field_{$field_id}-carddate",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => [
						'hidden'   => ! empty( $field['sublabel_hide'] ),
						'value'    => ! empty( $field['expiration_date'] ) ? esc_html( $field['expiration_date'] ) : $default_labels['expiration_date'],
						'position' => 'after',
					],
				],
				'code'   => [
					'attr'     => [
						'name'  => '',
						'value' => '',
					],
					'block'    => [
						'wpforms-field-paypal-commerce-code',
					],
					'class'    => [
						'wpforms-field-paypal-commerce-cardcode',
					],
					'data'     => [],
					'id'       => "wpforms-{$form_id}-field_{$field_id}-cardcode",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => [
						'hidden'   => ! empty( $field['sublabel_hide'] ),
						'value'    => ! empty( $field['security_code'] ) ? esc_html( $field['security_code'] ) : $default_labels['security_code'],
						'position' => 'after',
					],
				],
			],
		];

		if ( $is_card_holder_enable ) {
			$props['inputs']['name'] = [
				'attr'     => [
					'name'        => "wpforms[fields][{$field_id}][cardname]",
					'placeholder' => ! empty( $field['cardname_placeholder'] ) ? $field['cardname_placeholder'] : '',
				],
				'block'    => [
					'wpforms-field-paypal-commerce-name',
				],
				'class'    => [
					'wpforms-field-paypal-commerce-cardname',
				],
				'data'     => [],
				'id'       => "wpforms-{$form_id}-field_{$field_id}-cardname",
				'required' => ! empty( $field['required'] ) ? 'required' : '',
				'sublabel' => [
					'hidden'   => ! empty( $field['sublabel_hide'] ),
					'value'    => ! empty( $field['card_holder_name'] ) ? esc_html( $field['card_holder_name'] ) : $default_labels['card_holder_name'],
					'position' => 'after',
				],
			];
		}

		$properties = array_merge_recursive( $properties, $props );

		// If this field is required we need to make some adjustments.
		if ( ! empty( $field['required'] ) ) {

			// Add required class if needed (for multi-page validation).
			$properties['inputs']['number']['class'][] = 'wpforms-field-required';
			$properties['inputs']['date']['class'][]   = 'wpforms-field-required';
			$properties['inputs']['code']['class'][]   = 'wpforms-field-required';

			if ( $is_card_holder_enable ) {
				$properties['inputs']['name']['class'][] = 'wpforms-field-required';
			}
		}

		return $properties;
	}

	/**
	 * Default to required.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $required Required status, true if required.
	 * @param array $field    Field settings.
	 *
	 * @return bool
	 */
	public function default_required( $required, $field ) {

		return $this->type === $field['type'] ? true : $required;
	}

	/**
	 * Define additional "Add Field" button attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts      Add Field button attributes.
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function field_button_atts( $atts, $field, $form_data ) {

		if ( $field['type'] !== $this->type ) {
			return $atts;
		}

		if ( Helpers::has_paypal_commerce_field( $form_data ) ) {
			$atts['atts']['disabled'] = 'true';
			$atts['class'][]          = 'wpforms-add-fields-button-disabled';

			return $atts;
		}

		if ( ! Connection::get() ) {
			$atts['class'][] = 'warning-modal';
			$atts['class'][] = 'paypal-commerce-connection-required';
		}

		return $atts;
	}

	/**
	 * Disallow field preview "Duplicate" button.
	 *
	 * @since 1.0.0
	 *
	 * @param bool  $display Display switch.
	 * @param array $field   Field settings.
	 *
	 * @return bool
	 */
	public function field_display_duplicate_button( $display, $field ) {

		return $field['type'] === $this->type ? false : $display;
	}

	/**
	 * The field value availability for the Entry Preview field.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $is_supported The field availability.
	 * @param string $value        The submitted Credit Card detail.
	 * @param array  $field        Field data.
	 * @param array  $form_data    Form data.
	 *
	 * @return bool
	 */
	public function entry_preview_availability( $is_supported, $value, $field, $form_data ) {

		return ! empty( $value ) && $value !== '-';
	}

	/**
	 * Disallow dynamic population.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Current field specific data.
	 *
	 * @return bool
	 */
	public function is_dynamic_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * Disallow fallback population.
	 *
	 * @since 1.0.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Current field specific data.
	 *
	 * @return bool
	 */
	public function is_fallback_population_allowed( $properties, $field ) {

		return false;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field settings.
	 */
	public function field_options( $field ) {

		$this->basic_options( $field );

		$this->advanced_options( $field );
	}

	/**
	 * Basic options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function basic_options( $field ) {

		$this->field_option( 'basic-options', $field, [ 'markup' => 'open' ] );

		$this->field_option( 'label', $field );

		$this->field_option( 'description', $field );

		$this->payment_methods_options( $field );

		$this->supported_credit_cards_options( $field );

		$this->sublabels_options( $field );

		$this->field_option( 'required', $field );

		$this->field_option( 'basic-options', $field, [ 'markup' => 'close' ] );
	}

	/**
	 * Advanced options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function advanced_options( $field ) {

		$this->field_option( 'advanced-options', $field, [ 'markup' => 'open' ] );

		$this->field_option( 'size', $field );

		$this->button_size_option( $field );

		$this->shape_option( $field );

		$this->color_option( $field );

		$this->field_option( 'css', $field );

		$this->field_option( 'label_hide', $field );

		$this->field_option( 'advanced-options', $field, [ 'markup' => 'close' ] );
	}

	/**
	 * Button size option.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function button_size_option( $field ) {

		$output = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'button_size',
				'value'   => esc_html__( 'Button Size', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'PayPal checkout button size.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$output .= $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'button_size',
				'value'   => ! empty( $field['button_size'] ) ? esc_attr( $field['button_size'] ) : '',
				'options' => [
					'responsive' => esc_html__( 'Responsive', 'wpforms-paypal-commerce' ),
					'small'      => esc_html__( 'Small', 'wpforms-paypal-commerce' ),
					'medium'     => esc_html__( 'Medium', 'wpforms-paypal-commerce' ),
					'large'      => esc_html__( 'Large', 'wpforms-paypal-commerce' ),
				],
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'button_size',
				'content' => $output,
			]
		);
	}

	/**
	 * Shape option.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function shape_option( $field ) {

		$output = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'shape',
				'value'   => esc_html__( 'Button Shape', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'PayPal checkout button shape.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$output .= $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'shape',
				'value'   => ! empty( $field['shape'] ) ? esc_attr( $field['shape'] ) : '',
				'options' => [
					'pill' => esc_html__( 'Pill', 'wpforms-paypal-commerce' ),
					'rect' => esc_html__( 'Rectangle', 'wpforms-paypal-commerce' ),
				],
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'shape',
				'content' => $output,
			]
		);
	}

	/**
	 * Color option.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function color_option( $field ) {

		$output = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'color',
				'value'   => esc_html__( 'Button Color', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'PayPal checkout button color.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$output .= $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'color',
				'value'   => ! empty( $field['color'] ) ? esc_attr( $field['color'] ) : '',
				'options' => [
					'blue'   => esc_html__( 'Blue', 'wpforms-paypal-commerce' ),
					'black'  => esc_html__( 'Black', 'wpforms-paypal-commerce' ),
					'white'  => esc_html__( 'White', 'wpforms-paypal-commerce' ),
					'gold'   => esc_html__( 'Gold', 'wpforms-paypal-commerce' ),
					'silver' => esc_html__( 'Silver', 'wpforms-paypal-commerce' ),
				],
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'color',
				'content' => $output,
			]
		);
	}

	/**
	 * Display payment methods options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function payment_methods_options( $field ) {

		$payment_methods = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'payment_methods',
				'value'   => esc_html__( 'Supported Payment Methods', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'Select payment methods to enable.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$payment_methods .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'paypal_checkout',
				'value' => isset( $field['paypal_checkout'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'PayPal Checkout', 'wpforms-paypal-commerce' ),
				'class' => 'wpforms-field-option-paypal-checkout',
			],
			false
		);

		$payment_methods .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'credit_card',
				'value' => isset( $field['credit_card'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'Credit Card', 'wpforms-paypal-commerce' ),
				'class' => 'wpforms-field-option-credit-card',
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'payment_methods',
				'content' => $payment_methods,
			]
		);

		$default_method_field = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'default_method',
				'value'   => esc_html__( 'Default Payment Method', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'Select the default payment method.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$default_method_field .= $this->field_element(
			'select',
			$field,
			[
				'slug'    => 'default_method',
				'value'   => ! empty( $field['default_method'] ) ? esc_attr( $field['default_method'] ) : '',
				'options' => [
					'paypal_checkout' => esc_html__( 'PayPal Checkout', 'wpforms-paypal-commerce' ),
					'credit_card'     => esc_html__( 'Credit Card', 'wpforms-paypal-commerce' ),
				],
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'default_method',
				'content' => $default_method_field,
				'class'   => ! isset( $field['paypal_checkout'], $field['credit_card'] ) && $this->form_id ? 'wpforms-hidden' : '',
			]
		);
	}

	/**
	 * Display supported credit cards options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function supported_credit_cards_options( $field ) {

		$credit_cards = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'credit_cards',
				'value'   => esc_html__( 'Supported Credit Cards', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'Select supported credit cards.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$credit_cards .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'amex',
				'value' => isset( $field['amex'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'American Express', 'wpforms-paypal-commerce' ),
				'data'  => [ 'card' => 'amex' ],
			],
			false
		);

		$credit_cards .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'discover',
				'value' => isset( $field['discover'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'Discover', 'wpforms-paypal-commerce' ),
				'data'  => [ 'card' => 'discover' ],
			],
			false
		);

		$credit_cards .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'maestro',
				'value' => isset( $field['maestro'] ) && $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'Maestro', 'wpforms-paypal-commerce' ),
				'data'  => [ 'card' => 'maestro' ],
			],
			false
		);

		$credit_cards .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'mastercard',
				'value' => isset( $field['mastercard'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'Mastercard', 'wpforms-paypal-commerce' ),
				'data'  => [ 'card' => 'mastercard' ],
			],
			false
		);

		$credit_cards .= $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'visa',
				'value' => isset( $field['visa'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => esc_html__( 'Visa', 'wpforms-paypal-commerce' ),
				'data'  => [ 'card' => 'visa' ],
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'credit_cards',
				'content' => $credit_cards,
				'class'   => ! isset( $field['credit_card'] ) && $this->form_id ? 'wpforms-hidden' : '',
			]
		);
	}

	/**
	 * Display sublabel_options options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Current field specific data.
	 */
	private function sublabels_options( $field ) {

		$default_labels = $this->get_default_labels();

		$sublabels = $this->field_element(
			'label',
			$field,
			[
				'slug'    => 'sublabels',
				'value'   => esc_html__( 'Sublabels', 'wpforms-paypal-commerce' ),
				'tooltip' => esc_html__( 'Additional credit card fields.', 'wpforms-paypal-commerce' ),
			],
			false
		);

		$card_number = $this->field_element(
			'text',
			$field,
			[
				'slug'        => 'card_number',
				'value'       => ! empty( $field['card_number'] ) ? esc_attr( $field['card_number'] ) : '',
				'before'      => $default_labels['card_number'],
				'placeholder' => $default_labels['card_number'],
				'data'        => [ 'sublabel' => 'card-number' ],
			],
			false
		);

		$sublabels .= $this->field_element(
			'row',
			$field,
			[
				'slug'    => 'card_number',
				'content' => $card_number,
			],
			false
		);

		$expiration_date = $this->field_element(
			'text',
			$field,
			[
				'slug'        => 'expiration_date',
				'value'       => ! empty( $field['expiration_date'] ) ? esc_attr( $field['expiration_date'] ) : '',
				'before'      => $default_labels['expiration_date'],
				'placeholder' => $default_labels['expiration_date'],
				'data'        => [ 'sublabel' => 'expiration-date' ],
			],
			false
		);

		$sublabels .= $this->field_element(
			'row',
			$field,
			[
				'slug'    => 'expiration_date',
				'content' => $expiration_date,
			],
			false
		);

		$security_code = $this->field_element(
			'text',
			$field,
			[
				'slug'        => 'security_code',
				'value'       => ! empty( $field['security_code'] ) ? esc_attr( $field['security_code'] ) : '',
				'before'      => $default_labels['security_code'],
				'placeholder' => $default_labels['security_code'],
				'data'        => [ 'sublabel' => 'security-code' ],
			],
			false
		);

		$sublabels .= $this->field_element(
			'row',
			$field,
			[
				'slug'    => 'security_code',
				'content' => $security_code,
			],
			false
		);

		$card_holder = $this->field_element(
			'toggle',
			$field,
			[
				'slug'  => 'card_holder_enable',
				'value' => isset( $field['card_holder_enable'] ) || ! $this->form_id ? '1' : '0',
				'desc'  => $default_labels['card_holder_name'],
			],
			false
		);

		$card_holder .= $this->field_element(
			'text',
			$field,
			[
				'slug'        => 'card_holder_name',
				'value'       => ! empty( $field['card_holder_name'] ) ? esc_attr( $field['card_holder_name'] ) : '',
				'placeholder' => $default_labels['card_holder_name'],
				'class'       => ! isset( $field['card_holder_enable'] ) && $this->form_id ? 'wpforms-hidden' : '',
				'data'        => [ 'sublabel' => 'card-holder-name' ],
			],
			false
		);

		$sublabels .= $this->field_element(
			'row',
			$field,
			[
				'slug'    => 'card_holder',
				'content' => $card_holder,
			],
			false
		);

		$this->field_element(
			'row',
			$field,
			[
				'slug'    => 'sublabels',
				'content' => $sublabels,
				'class'   => ! isset( $field['credit_card'] ) && $this->form_id ? 'wpforms-hidden' : '',
			]
		);
	}

	/**
	 * Get default labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_default_labels() {

		return [
			'card_number'      => __( 'Card Number', 'wpforms-paypal-commerce' ),
			'expiration_date'  => __( 'Expiration Date', 'wpforms-paypal-commerce' ),
			'security_code'    => __( 'Security Code', 'wpforms-paypal-commerce' ),
			'card_holder_name' => __( 'Card Holder Name', 'wpforms-paypal-commerce' ),
		];
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field ) {

		$default_labels = $this->get_default_labels();

		// Define data.
		$name_label      = ! empty( $field['card_holder_name'] ) ? $field['card_holder_name'] : $default_labels['card_holder_name'];
		$code_label      = ! empty( $field['security_code'] ) ? $field['security_code'] : $default_labels['security_code'];
		$date_label      = ! empty( $field['expiration_date'] ) ? $field['expiration_date'] : $default_labels['expiration_date'];
		$card_label      = ! empty( $field['card_number'] ) ? $field['card_number'] : $default_labels['card_number'];
		$selected_method = ! empty( $field['default_method'] ) && $field['default_method'] === 'credit_card' ? __( 'Credit Card', 'wpforms-paypal-commerce' ) : __( 'PayPal Checkout', 'wpforms-paypal-commerce' );

		// Label.
		$this->field_preview_option( 'label', $field );
		?>

		<div class="format-selected format-selected-full">

			<div class="wpforms-paypal-commerce-payment-method <?php echo ( ! isset( $field['credit_card'], $field['paypal_checkout'] ) && isset( $field['default_method'] ) ) ? 'wpforms-hidden' : ''; ?>">
				<select class="primary-input" readonly>
					<option><?php echo esc_html( $selected_method ); ?></option>
				</select>
			</div>

			<p class="wpforms-alert wpforms-alert-danger wpforms-paypal-commerce-no-payment-method-warning <?php echo isset( $field['paypal_checkout'] ) || isset( $field['credit_card'] ) || ! $this->form_id ? 'wpforms-hidden' : ''; ?>"><?php esc_html_e( 'Please enable at least one payment method.', 'wpforms-paypal-commerce' ); ?></p>
			<p class="wpforms-alert wpforms-alert-warning wpforms-paypal-commerce-paypal-checkout-warning <?php echo isset( $field['paypal_checkout'] ) && ! isset( $field['credit_card'] ) && $this->form_id ? '' : 'wpforms-hidden'; ?>"><?php esc_html_e( 'PayPal Checkout is enabled. The form’s submit button has been replaced by PayPal’s smart buttons.', 'wpforms-paypal-commerce' ); ?></p>
			<p class="wpforms-alert wpforms-alert-warning wpforms-paypal-commerce-credit-card-warning <?php echo isset( $field['credit_card'], $field['paypal_checkout'] ) && $field['default_method'] === 'paypal_checkout' && $this->form_id ? '' : 'wpforms-hidden'; ?>"><?php esc_html_e( 'Credit card fields will only be displayed when you select credit card as the payment method.', 'wpforms-paypal-commerce' ); ?></p>

			<div class="wpforms-paypal-commerce-credit-card-fields <?php echo ! isset( $field['credit_card'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>">

				<p class="wpforms-alert wpforms-alert-danger wpforms-paypal-commerce-no-credit-card-type-warning <?php echo isset( $field['amex'] ) || isset( $field['discover'] ) || isset( $field['maestro'] ) || isset( $field['mastercard'] ) || isset( $field['visa'] ) || ! $this->form_id ? 'wpforms-hidden' : ''; ?>"><?php esc_html_e( 'Please enable at least one credit card type.', 'wpforms-paypal-commerce' ); ?></p>

				<div class="wpforms-field-row">
					<div class="wpforms-paypal-commerce-supported-cards">
						<div class="wpforms-paypal-commerce-amex-icon <?php echo ! isset( $field['amex'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>"></div>
						<div class="wpforms-paypal-commerce-discover-icon <?php echo ! isset( $field['discover'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>"></div>
						<div class="wpforms-paypal-commerce-maestro-icon <?php echo ! isset( $field['maestro'] ) || ! $this->form_id ? 'wpforms-hidden' : ''; ?>"></div>
						<div class="wpforms-paypal-commerce-mastercard-icon <?php echo ! isset( $field['mastercard'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>"></div>
						<div class="wpforms-paypal-commerce-visa-icon <?php echo ! isset( $field['visa'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>"></div>
					</div>
				</div>

				<div class="wpforms-field-row">
					<div class="wpforms-paypal-commerce-card-number">
						<input type="text" disabled>
						<label class="wpforms-sub-label"><?php echo esc_html( $card_label ); ?></label>
					</div>
				</div>

				<div class="wpforms-field-row">
					<div class="wpforms-paypal-commerce-expiration-date wpforms-one-half">
						<input type="text" disabled>
						<label class="wpforms-sub-label"><?php echo esc_html( $date_label ); ?></label>
					</div>
					<div class="wpforms-paypal-commerce-security-code wpforms-one-half last">
						<input type="text" disabled>
						<label class="wpforms-sub-label"><?php echo esc_html( $code_label ); ?></label>
					</div>
				</div>

				<div class="wpforms-field-row">
					<div class="wpforms-paypal-commerce-card-holder-name <?php echo ! isset( $field['card_holder_enable'] ) && $this->form_id ? 'wpforms-hidden' : ''; ?>">
						<input type="text" disabled>
						<label class="wpforms-sub-label"><?php echo esc_html( $name_label ); ?></label>
					</div>
				</div>
			</div>
		</div>

		<?php
		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Add submit button.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data Form data and settings.
	 * @param string $button    Button type.
	 */
	public function submit_button( $form_data, $button = '' ) {

		if ( $button !== 'submit' ) {
			return;
		}

		if ( wpforms_is_admin_page( 'builder' ) ) {
			$this->builder_submit_button( $form_data );

			return;
		}

		if ( wpforms_paypal_commerce()->get_integrations()->is_integration_page_loaded() ) {

			if ( $this->is_credit_card_method_selected( $form_data['fields'] ) ) {
				return;
			}

			$this->builder_submit_button( $form_data );

			return;
		}

		$this->frontend_submit_button( $form_data );
	}

	/**
	 * Add submit button hidden class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes   Button classes.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public function submit_button_classes( $classes, $form_data ) {

		if ( ! wpforms_paypal_commerce()->get_integrations()->is_integration_page_loaded() ) {
			return $classes;
		}

		if ( $this->is_credit_card_method_selected( $form_data['fields'] ) ) {
			return $classes;
		}

		$classes[] = 'wpforms-hidden';

		return $classes;
	}

	/**
	 * Check if credit card method is selected for the field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Form fields data.
	 *
	 * @return bool
	 */
	private function is_credit_card_method_selected( $fields ) {

		$field = Helpers::get_paypal_field( $fields );

		return empty( $field['paypal_checkout'] ) ||
			( ! empty( $field['credit_card'] ) && $field['default_method'] === 'credit_card' ) ||
			( ! empty( $field['conditional_logic'] ) && $field['conditional_type'] === 'show' );
	}

	/**
	 * Display a submit button on the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 */
	private function builder_submit_button( $form_data ) {

		if ( ! isset( $form_data['fields'] ) ) {
			return;
		}

		$field = Helpers::get_paypal_field( $form_data['fields'] );

		if ( ! isset( $field['shape'] ) ) {
			$field['shape'] = 'pill';
		}

		if ( ! isset( $field['color'] ) ) {
			$field['color'] = 'blue';
		}

		$wrapper_hide  = isset( $field['paypal_checkout'] ) && ! empty( $form_data['fields'] ) ? '' : 'wpforms-hidden';
		$size_class    = ! isset( $field['button_size'] ) || $field['button_size'] === 'responsive' ? 'size-medium' : 'size-' . $field['button_size'];
		$shape_class   = 'wpforms-paypal-commerce-paypal-shape-' . $field['shape'];
		$color_class   = 'wpforms-paypal-commerce-paypal-checkout-button-' . $field['color'];
		$checkout_logo = in_array( $field['color'], [ 'white', 'silver', 'gold' ], true ) ? 'wpforms-paypal-commerce-paypal-checkout-button-logo-blue' : 'wpforms-paypal-commerce-paypal-checkout-button-logo-white';

		?>

		<div id="wpforms-paypal-commerce-buttons-wrapper" class="<?php echo esc_attr( implode( ' ', [ $wrapper_hide, $size_class ] ) ); ?>">
			<div id="wpforms-paypal-commerce-paypal-checkout-button" class=" <?php echo esc_attr( implode( ' ', [ $color_class, $shape_class ] ) ); ?>">
				<span id="wpforms-paypal-commerce-paypal-checkout-button-logo" class="<?php echo esc_attr( $checkout_logo ); ?>"></span>
			</div>
		</div>

	<?php
	}

	/**
	 * Display a submit button on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 */
	private function frontend_submit_button( $form_data ) {

		$connection = Connection::get();

		if ( ! $connection || ! $connection->is_usable() || ! Helpers::is_paypal_commerce_enabled( $form_data ) || ! Helpers::is_subscriptions_configured( $form_data ) ) {
			return;
		}

		$field = Helpers::get_paypal_field( $form_data['fields'] );

		if ( empty( $field ) ) {
			return;
		}

		if ( ! isset( $field['credit_card'] ) && ! isset( $field['paypal_checkout'] ) ) {
			return;
		}

		$size_class        = ! isset( $field['button_size'] ) || $field['button_size'] === 'responsive' ? 'size-medium' : 'size-' . $field['button_size'];
		$single_spinner    = $this->get_submit_spinner( $form_data, 'wpforms-paypal-commerce-single-spinner' );
		$recurring_spinner = $this->get_submit_spinner( $form_data, 'wpforms-paypal-commerce-recurring-spinner' );

		echo '<div id="wpforms-paypal-commerce-single-submit-button-' . esc_attr( $form_data['id'] ) . '" class="wpforms-paypal-commerce-single-submit-button ' . esc_attr( $size_class ) . '">' . $single_spinner . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div id="wpforms-paypal-commerce-subscriptions-submit-button-' . esc_attr( $form_data['id'] ) . '" class="wpforms-paypal-commerce-subscriptions-submit-button ' . esc_attr( $size_class ) . '">' . $recurring_spinner . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get submit spinner html.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data Form data and settings.
	 * @param string $class     Spinner class.
	 *
	 * @return string
	 */
	private function get_submit_spinner( $form_data, $class ) {

		/** This filter is documented in includes/class-frontend.php. */
		$src = apply_filters( 'wpforms_display_submit_spinner_src', WPFORMS_PLUGIN_URL . 'assets/images/submit-spin.svg', $form_data ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		return sprintf(
			'<img src="%s" class="%s" style="display: none;" width="26" height="26" alt="%s">',
			esc_url( $src ),
			esc_attr( $class ),
			esc_attr__( 'Loading', 'wpforms-paypal-commerce' )
		);
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes. Use field properties.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Display warning for non SSL pages.
		if ( ! is_ssl() ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
				esc_html_e( 'This page is not secure. PayPal Commerce payments should be used for testing purposes only.', 'wpforms-paypal-commerce' );
			echo '</div>';
		}

		$connection = Connection::get();

		if ( ! $connection ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
				esc_html_e( 'PayPal Commerce payments are disabled. Please set up a connection with PayPal in your form’s settings.', 'wpforms-paypal-commerce' );
			echo '</div>';

			return;
		}

		if ( ! $connection->is_usable() ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
				esc_html_e( 'PayPal Commerce payments are disabled because your connection is not set up correctly. Please ask your site administrator to check the connection settings.', 'wpforms-paypal-commerce' );
			echo '</div>';

			return;
		}

		if ( ! Helpers::is_paypal_commerce_enabled( $form_data ) ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
				esc_html_e( 'PayPal Commerce payments are not enabled in the form settings.', 'wpforms-paypal-commerce' );
			echo '</div>';

			return;
		}

		if ( ! isset( $field['credit_card'] ) && ! isset( $field['paypal_checkout'] ) ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
				esc_html_e( 'PayPal Commerce payments are disabled. Please enable at least one payment method in the form settings.', 'wpforms-paypal-commerce' );
			echo '</div>';

			return;
		}

		if ( ! Helpers::is_subscriptions_configured( $form_data ) ) {
			echo '<div class="wpforms-cc-warning wpforms-error-alert">';
			esc_html_e( 'PayPal Commerce payments are disabled because details are missing from one of the recurring plans. Please ask your site administrator to check the form settings.', 'wpforms-paypal-commerce' );
			echo '</div>';

			return;
		}

		echo '<input type="hidden" name="wpforms[fields][' . esc_attr( $field['id'] ) . '][orderID]" class="wpforms-paypal-commerce-order-id" />';
		echo '<input type="hidden" name="wpforms[fields][' . esc_attr( $field['id'] ) . '][subscriptionID]" class="wpforms-paypal-commerce-subscription-id" />';
		echo '<input type="hidden" name="wpforms[fields][' . esc_attr( $field['id'] ) . '][source]" class="wpforms-paypal-commerce-source" />';

		$this->field_display_default_payment( $field );

		$this->field_display_credit_card( $field, $form_data );
	}

	/**
	 * Field display default payment on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	private function field_display_default_payment( $field ) {

		if ( isset( $field['credit_card'], $field['paypal_checkout'] ) ) {

			$is_selected_card     = $field['default_method'] === 'credit_card' ? 'selected="selected"' : '';
			$is_selected_checkout = $field['default_method'] === 'paypal_checkout' ? 'selected="selected"' : '';

			// Row wrapper.
			echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';
			echo '<select class="wpforms-paypal-commerce-payment-method">';
			echo '<option value="checkout" ' . esc_attr( $is_selected_checkout ) . '>' . esc_html__( 'PayPal Checkout', 'wpforms-paypal-commerce' ) . '</option>';
			echo '<option value="card" ' . esc_attr( $is_selected_card ) . '>' . esc_html__( 'Credit Card', 'wpforms-paypal-commerce' ) . '</option>';
			echo '</select>';
			echo '</div>';
		}
	}

	/**
	 * Field display default payment on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field     Field data and settings.
	 * @param array $form_data Form data and settings.
	 */
	private function field_display_credit_card( $field, $form_data ) {

		if ( ! isset( $field['credit_card'] ) ) {
			return;
		}

		$hide_class = isset( $field['credit_card'], $field['paypal_checkout'] ) && $field['default_method'] !== 'credit_card' ? 'wpforms-hidden' : '';

		// Define data.
		$number = ! empty( $field['properties']['inputs']['number'] ) ? $field['properties']['inputs']['number'] : [];
		$date   = ! empty( $field['properties']['inputs']['date'] ) ? $field['properties']['inputs']['date'] : [];
		$code   = ! empty( $field['properties']['inputs']['code'] ) ? $field['properties']['inputs']['code'] : [];
		$name   = ! empty( $field['properties']['inputs']['name'] ) ? $field['properties']['inputs']['name'] : [];

		echo '<div class="wpforms-paypal-commerce-card-fields ' . sanitize_html_class( $hide_class ) . '">';

		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';
		echo '<div class="wpforms-paypal-commerce-supported-cards">';
		echo isset( $field['amex'] ) ? '<div class="wpforms-paypal-commerce-amex-icon"></div>' : '';
		echo isset( $field['discover'] ) ? '<div class="wpforms-paypal-commerce-discover-icon"></div>' : '';
		echo isset( $field['maestro'] ) ? '<div class="wpforms-paypal-commerce-maestro-icon"></div>' : '';
		echo isset( $field['mastercard'] ) ? '<div class="wpforms-paypal-commerce-mastercard-icon"></div>' : '';
		echo isset( $field['visa'] ) ? '<div class="wpforms-paypal-commerce-visa-icon"></div>' : '';
		echo '</div>';
		echo '</div>';

		// Row wrapper.
		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';
		echo '<div ' . wpforms_html_attributes( false, $number['block'] ) . '>';
		$this->field_display_sublabel( 'number', 'before', $field );
		printf(
			'<div %s data-required="%s"></div>',
			wpforms_html_attributes( $number['id'], $number['class'], $number['data'], $number['attr'] ),
			esc_attr( $number['required'] )
		);

		// Hidden input is needed for validation.
		printf( '<input type="text" class="wpforms-paypal-commerce-credit-card-hidden-input" name="wpforms[paypal-commerce-credit-card-hidden-input-%1$d]" id="wpforms-paypal-commerce-credit-card-hidden-input-%1$d" disabled style="display: none;">', (int) $form_data['id'] );
		$this->field_display_sublabel( 'number', 'after', $field );
		$this->field_display_error( 'number', $field );
		echo '</div>';
		echo '</div>';

		// Row wrapper.
		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';
		echo '<div class="wpforms-field-row-block wpforms-one-half wpforms-first" ' . wpforms_html_attributes( false, $date['block'] ) . '>';
		$this->field_display_sublabel( 'date', 'before', $field );
		printf(
			'<div %s data-required="%s"></div>',
			wpforms_html_attributes( $date['id'], $date['class'], $date['data'], $date['attr'] ),
			esc_attr( $date['required'] )
		);

		// Hidden input is needed for validation.
		printf( '<input type="text" class="wpforms-paypal-commerce-credit-card-hidden-input" name="wpforms[paypal-commerce-credit-card-hidden-input-%1$d]" id="wpforms-paypal-commerce-credit-card-hidden-input-%1$d" disabled style="display: none;">', (int) $form_data['id'] );
		$this->field_display_sublabel( 'date', 'after', $field );
		$this->field_display_error( 'date', $field );
		echo '</div>';

		echo '<div class="wpforms-field-row-block wpforms-one-half"' . wpforms_html_attributes( false, $code['block'] ) . '>';
		$this->field_display_sublabel( 'code', 'before', $field );
		printf(
			'<div %s data-required="%s"></div>',
			wpforms_html_attributes( $code['id'], $code['class'], $code['data'], $code['attr'] ),
			esc_attr( $code['required'] )
		);

		// Hidden input is needed for validation.
		printf( '<input type="text" class="wpforms-paypal-commerce-credit-card-hidden-input" name="wpforms[paypal-commerce-credit-card-hidden-input-%1$d]" id="wpforms-paypal-commerce-credit-card-hidden-input-%1$d" disabled style="display: none;">', (int) $form_data['id'] );
		$this->field_display_sublabel( 'code', 'after', $field );
		$this->field_display_error( 'code', $field );
		echo '</div>';
		echo '</div>';

		if ( ! isset( $field['card_holder_enable'] ) ) {
			echo '</div>';

			return;
		}

		// Row wrapper.
		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';
		// Name.
		echo '<div ' . wpforms_html_attributes( false, $name['block'] ) . '>';
		$this->field_display_sublabel( 'name', 'before', $field );
		printf(
			'<input type="text" %s %s>',
			wpforms_html_attributes( $name['id'], $name['class'], $name['data'], $name['attr'] ),
			esc_attr( $name['required'] )
		);
		$this->field_display_sublabel( 'name', 'after', $field );
		$this->field_display_error( 'name', $field );
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Currently validation happens on the front end. We do not do
	 * generic server-side validation because we do not allow the card
	 * details to POST to the server.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {}

	/**
	 * Format field.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		$field_name = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';
		$card_name  = ! empty( $field_submit['cardname'] ) ? $field_submit['cardname'] : '';

		// Set final field details.
		wpforms()->get( 'process' )->fields[ $field_id ] = [
			'name'     => sanitize_text_field( $field_name ),
			'cardname' => sanitize_text_field( $card_name ),
			'value'    => '',
			'id'       => absint( $field_id ),
			'type'     => $this->type,
		];
	}
}
