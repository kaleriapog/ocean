/* global wpforms_builder, wpf */

'use strict';

/**
 * WPForms PayPal Commerce builder function.
 *
 * @since 1.0.0
 */
var WPFormsBuilderPaypalCommerce = window.WPFormsBuilderPaypalCommerce || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 1.0.0
	 *
	 * @type {object}
	 */
	var el = {};

	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Initialized once the DOM is fully loaded.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			// Cache DOM elements.
			el = {
				$AJAXSubmitOption:     $( '#wpforms-panel-field-settings-ajax_submit' ),
				$alert:                $( '#wpforms-paypal-commerce-credit-card-alert' ),
				$alertPaypalStandard:  $( '#wpforms-paypal-commerce-paypal-standard-alert' ),
				$paypalStandardToggle: $( '#wpforms-panel-field-paypal_standard-enable' ),
				$panelContent:         $( '#wpforms-panel-content-section-payment-paypal-commerce' ),
				$buttonsWrapper:       $( '#wpforms-paypal-commerce-buttons-wrapper' ),
				$checkoutButton:       $( '#wpforms-paypal-commerce-paypal-checkout-button' ),
				$checkoutButtonLogo:   $( '#wpforms-paypal-commerce-paypal-checkout-button-logo' ),
			};

			app.bindOptionsActions();
			app.bindUIActions();
			app.hideSubmitDefault();
			app.disableFields();
		},

		/**
		 * Process various events.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			$( document ).on( 'wpformsSaved', function( e, data ) {
				app.ajaxRequiredCheck();
				app.paymentsEnabledCheck();
				app.updatePlanData( data );
				app.disableFields();
			} );

			$( document )
				.on( 'wpformsBeforeSave', app.paypalStandardEnabledCheck )
				.on( 'wpformsFieldAdd', app.fieldAdded )
				.on( 'wpformsFieldDelete', app.fieldDeleted );

			el.$cardButton.on( 'click', app.connectionCheck );
			el.$paypalStandardToggle.on( 'change', app.paypalStandardToggle );
		},

		/**
		 * Process generic constants.
		 *
		 * @since 1.0.0
		 */
		bindGenericConstants: function() {

			el.$fieldOptions = $( '.wpforms-field-option-paypal-commerce' );
			el.$fieldPreview = $( '#wpforms-panel-fields .wpforms-field.wpforms-field-paypal-commerce' );
		},

		/**
		 * Process options events.
		 *
		 * @since 1.0.0
		 */
		bindOptionsActions: function() {

			app.bindGenericConstants();

			el.$singlePaymentControl    = $( '#wpforms-panel-field-paypal_commerce-enable_one_time' );
			el.$recurringPaymentControl = $( '#wpforms-panel-field-paypal_commerce-enable_recurring' );
			el.$singlePaymentSettings   = $( '#paypal-commerce-provider .wpforms-paypal-commerce-panel-fields' );
			el.$cardButton              = $( '#wpforms-add-fields-paypal-commerce' );

			el.$singlePaymentControl.on( 'change', app.singleSettingsToggle );
			el.$fieldOptions.find( '.wpforms-field-option-paypal-checkout' ).on( 'change', app.paypalCheckoutMethodToggle );
			el.$fieldOptions.find( '.wpforms-field-option-credit-card' ).on( 'change', app.creditCardMethodToggle );
			el.$fieldOptions.find( '.wpforms-field-option-row-credit_cards input' ).on( 'change', app.creditCardTypeToggle );
			el.$fieldOptions.find( '.wpforms-field-option-row-default_method select' ).on( 'change', app.defaultMethodSelect );
			el.$fieldOptions.find( '.wpforms-field-option-row-card_holder input[type="checkbox"]' ).on( 'change', app.cardHolderToggle );
			el.$fieldOptions.find( '.wpforms-field-option-row-sublabels input[type="text"]' ).on( 'change', app.cardSublabels );
			el.$fieldOptions.find( '.wpforms-field-option-row-button_size select' ).on( 'change', app.optionButtonSizeChange );
			el.$fieldOptions.find( '.wpforms-field-option-row-shape select' ).on( 'change', app.optionShapeChange );
			el.$fieldOptions.find( '.wpforms-field-option-row-color select' ).on( 'change', app.optionColorChange );
		},

		/**
		 * Notify user if AJAX submission is not required.
		 *
		 * @since 1.0.0
		 */
		ajaxRequiredCheck: function() {

			if ( ! app.isTargetFieldAdded() ) {
				return;
			}

			if ( app.isAJAXSubmitEnabled() ) {
				return;
			}

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.paypal_commerce_ajax_required,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Check if a PayPal Commerce field has been added.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} True if it has been added.
		 */
		isTargetFieldAdded: function() {

			return el.$fieldPreview.length > 0;
		},

		/**
		 * Notify user if PayPal Commerce Payments are not enabled.
		 *
		 * @since 1.0.0
		 */
		paymentsEnabledCheck: function() {

			if ( ! $( '#wpforms-panel-fields .wpforms-field.wpforms-field-paypal-commerce' ).length ) {
				return;
			}

			if ( app.isPaymentsEnabled() ) {
				return;
			}

			el.$panelContent.find( '.wpforms-panel-content-section-payment-one-time' ).hide();
			el.$panelContent.find( '.wpforms-panel-content-section-payment-recurring' ).hide();

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.paypal_commerce_payments_enabled_required,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Disable payments if PayPal Standard enabled.
		 *
		 * @since 1.0.0
		 */
		paypalStandardEnabledCheck: function() {

			if ( ! el.$paypalStandardToggle.prop( 'checked' ) ) {
				return;
			}

			app.unCheckEnabledPayments();
		},

		/**
		 * Notify user if PayPal Commerce connection are missing.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} False if button clicks should be prevented.
		 */
		connectionCheck: function() {

			if ( $( this ).hasClass( 'wpforms-add-fields-button-disabled' ) ) {
				return false;
			}

			if ( ! $( this ).hasClass( 'paypal-commerce-connection-required' ) ) {
				return true;
			}

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.paypal_commerce_connection_required,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );

			return false;
		},

		/**
		 * We have to do several actions when the "PayPal Commerce" field is added.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 * @param {number} id Field ID.
		 * @param {string} type Field type.
		 */
		fieldAdded: function( e, id, type ) {

			if ( type !== 'paypal-commerce' ) {
				app.bindGenericConstants();
				app.hideSubmitDefault();
				return;
			}

			app.paymentsEnabledCheck();
			app.bindOptionsActions();
			app.cardButtonToggle( true );
			app.settingsToggle( true );

			el.$buttonsWrapper.removeClass( 'wpforms-hidden' );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-credit-card-warning' ).removeClass( 'wpforms-hidden' );
			app.hideSubmitDefault();
		},

		/**
		 * We have to do several actions for UI when the "PayPal Commerce" credit card field is deleted.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 * @param {number} id Field ID.
		 * @param {string} type Field type.
		 */
		fieldDeleted: function( e, id, type ) {

			if ( type !== 'paypal-commerce' ) {
				return;
			}

			app.cardButtonToggle( false );
			app.settingsToggle( false );
			app.disableNotifications();

			el.$buttonsWrapper.addClass( 'wpforms-hidden' );

			if ( ! $( '.wpforms-preview .no-fields-preview' ).length ) {
				$( '.wpforms-preview .wpforms-field-submit' ).show();
			}
		},

		/**
		 * Enable or disable the "PayPal Commerce" field in the fields list.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} isDisabled If true then a card button will be disabled.
		 */
		cardButtonToggle: function( isDisabled ) {

			el.$cardButton
				.prop( 'disabled', isDisabled )
				.toggleClass( 'wpforms-add-fields-button-disabled', isDisabled );
		},

		/**
		 * Toggle visibility of the PayPal Commerce payment settings.
		 *
		 * If the "PayPal Commerce" field has been added then reveal the settings,
		 * otherwise hide them.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} display Show or hide settings.
		 */
		settingsToggle: function( display ) {

			if (
				(
					! el.$alert.length &&
					! el.$panelContent.length
				) ||
				el.$paypalStandardToggle.prop( 'checked' )
			) {
				return;
			}

			el.$alert.toggleClass( 'wpforms-hidden', display );
			el.$panelContent.toggleClass( 'wpforms-hidden', ! display );

			if ( ! display ) {
				app.unCheckEnabledPayments();
			}
		},

		/**
		 * Toggle visibility of the PayPal Commerce Single Payments settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		singleSettingsToggle: function( e ) {

			el.$singlePaymentSettings.toggleClass( 'wpforms-hidden', ! e.target.checked );
		},

		/**
		 * Toggle visibility of the PayPal Commerce PayPal Checkout method settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		paypalCheckoutMethodToggle: function( e ) {

			var isCardEnabled           = app.isCreditCardEnabled(),
				isCheckoutDefaultMethod = el.$fieldOptions.find( '.wpforms-field-option-row-default_method select' ).val() === 'paypal_checkout';

			el.$fieldPreview.find( '.wpforms-paypal-commerce-paypal-checkout-warning' ).toggleClass( 'wpforms-hidden', ! e.target.checked || isCardEnabled );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-credit-card-warning' ).toggleClass( 'wpforms-hidden', ! e.target.checked || ! isCardEnabled || ! isCheckoutDefaultMethod );
			el.$fieldOptions.find( '.wpforms-field-option-row-button_size' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
			el.$fieldOptions.find( '.wpforms-field-option-row-button_size' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
			el.$fieldOptions.find( '.wpforms-field-option-row-shape' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
			el.$fieldOptions.find( '.wpforms-field-option-row-color' ).toggleClass( 'wpforms-hidden', ! e.target.checked );

			el.$buttonsWrapper.toggleClass( 'wpforms-hidden', ! e.target.checked );

			app.hideSubmitDefault();
			app.paymentMethodToggle();
		},

		/**
		 * Toggle visibility of the PayPal Standard method settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		paypalStandardToggle: function( e ) {

			var isChecked = e.target.checked && ! $( '#wpforms-panel-field-settings-disable_entries' ).prop( 'checked' );

			el.$alertPaypalStandard.toggleClass( 'wpforms-hidden', ! isChecked );

			if ( ! app.isTargetFieldAdded() ) {
				el.$alert.toggleClass( 'wpforms-hidden', isChecked );

				return;
			}

			el.$panelContent.toggleClass( 'wpforms-hidden', isChecked );
		},

		/**
		 * Toggle visibility of the PayPal Commerce Credit Card method settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		creditCardMethodToggle: function( e ) {

			var isCheckoutEnabled = app.isCheckoutEnabled(),
				isCardDefaultMethod = el.$fieldOptions.find( '.wpforms-field-option-row-default_method select' ).val() === 'credit_card';

			el.$fieldOptions.find( '.wpforms-field-option-row-credit_cards' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
			el.$fieldOptions.find( '.wpforms-field-option-row-sublabels' ).toggleClass( 'wpforms-hidden', ! e.target.checked );

			el.$fieldPreview.find( '.wpforms-paypal-commerce-credit-card-warning' ).toggleClass( 'wpforms-hidden', ! e.target.checked || isCardDefaultMethod || ! isCheckoutEnabled );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-paypal-checkout-warning' ).toggleClass( 'wpforms-hidden', e.target.checked || ! isCheckoutEnabled );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-credit-card-fields' ).toggleClass( 'wpforms-hidden', ! e.target.checked );

			app.paymentMethodToggle();
		},

		/**
		 * Toggle visibility of the PayPal Commerce Credit Card type settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		creditCardTypeToggle: function( e ) {

			el.$fieldPreview.find( '.wpforms-paypal-commerce-' + $( this ).data( 'card' ) + '-icon' ).toggleClass( 'wpforms-hidden', ! e.target.checked );

			el.$fieldOptions.find( '.wpforms-field-option-row-credit_cards input' ).each( function() {

				if ( $( this ).is( ':checked' ) ) {
					el.$fieldPreview.find( '.wpforms-paypal-commerce-no-credit-card-type-warning' ).addClass( 'wpforms-hidden' );
					return false;
				}

				el.$fieldPreview.find( '.wpforms-paypal-commerce-no-credit-card-type-warning' ).removeClass( 'wpforms-hidden' );
			} );
		},

		/**
		 * Toggle visibility of the PayPal Commerce Credit card holder settings.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} e Event object.
		 */
		cardHolderToggle: function( e ) {

			el.$fieldOptions.find( '.wpforms-field-option-row-card_holder input[type="text"]' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-card-holder-name' ).toggleClass( 'wpforms-hidden', ! e.target.checked );
		},

		/**
		 * Toggle visibility of the PayPal Commerce Credit card sublabels settings.
		 *
		 * @since 1.0.0
		 */
		cardSublabels: function() {

			var input = $( this ),
				inputValue = input.val(),
				label = inputValue ? inputValue : input.attr( 'placeholder' );

			el.$fieldPreview.find( '.wpforms-paypal-commerce-' + input.data( 'sublabel' ) + ' .wpforms-sub-label' ).html( label );
		},

		/**
		 * Default method preview.
		 *
		 * @since 1.0.0
		 */
		defaultMethodSelect: function() {

			var isCheckoutSelected = $( this ).val() === 'paypal_checkout';

			el.$fieldPreview.find( '.wpforms-paypal-commerce-payment-method select' ).children( ':selected' ).text( $( this ).children( ':selected' ).text() );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-credit-card-warning' ).toggleClass( 'wpforms-hidden', ! isCheckoutSelected );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-paypal-checkout-warning' ).addClass( 'wpforms-hidden' );
		},

		/**
		 * Toggle visibility of the PayPal Commerce Payment method settings.
		 *
		 * @since 1.0.0
		 */
		paymentMethodToggle: function() {

			var checkoutEnabled = app.isCheckoutEnabled(),
				cardEnabled = app.isCreditCardEnabled();

			el.$fieldOptions.find( '.wpforms-field-option-row-default_method' ).toggleClass( 'wpforms-hidden', ! checkoutEnabled || ! cardEnabled );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-payment-method' ).toggleClass( 'wpforms-hidden', ! checkoutEnabled || ! cardEnabled );
			el.$fieldPreview.find( '.wpforms-paypal-commerce-no-payment-method-warning' ).toggleClass( 'wpforms-hidden', checkoutEnabled || cardEnabled );
		},

		/**
		 * Disable notifications.
		 *
		 * @since 1.0.0
		 */
		disableNotifications: function() {

			var $notificationWrap = $( '.wpforms-panel-content-section-notifications [id*="-paypal-commerce-wrap"]' );

			$notificationWrap.find( 'input[id*="-paypal-commerce"]' ).prop( 'checked', false );
			$notificationWrap.addClass( 'wpforms-hidden' );
		},

		/**
		 * Adjust Buttons size.
		 *
		 * @since 1.0.0
		 */
		optionButtonSizeChange: function() {

			el.$buttonsWrapper.removeClass();
			el.$buttonsWrapper.addClass( 'size-' + $( this ).val() );
		},

		/**
		 * Adjust Shape.
		 *
		 * @since 1.0.0
		 */
		optionShapeChange: function() {

			var shape = 'wpforms-paypal-commerce-paypal-shape-' + $( this ).val();

			var modifyShapeMatcher = function( index, className ) {

				var matchedClasses = className.match( /(^|\s)wpforms-paypal-commerce-paypal-shape-\S+/g );

				return ( matchedClasses || [] ).join( '' );
			};

			el.$checkoutButton.removeClass( modifyShapeMatcher );
			el.$checkoutButton.addClass( shape );
		},

		/**
		 * Adjust Color.
		 *
		 * @since 1.0.0
		 */
		optionColorChange: function() {

			var color = $( this ).val();

			el.$checkoutButton.removeClass();
			el.$checkoutButton.addClass( 'wpforms-paypal-commerce-paypal-checkout-button-' + color );

			el.$checkoutButtonLogo.removeClass();

			if ( [ 'black', 'blue' ].includes( color ) ) {
				el.$checkoutButtonLogo.addClass( 'wpforms-paypal-commerce-paypal-checkout-button-logo-white' );
			} else {
				el.$checkoutButtonLogo.addClass( 'wpforms-paypal-commerce-paypal-checkout-button-logo-blue' );
			}

			el.$fieldOptions.find( '.wpforms-field-option-row-shape select' ).change();
		},

		/**
		 * Maybe hide default Submit button.
		 *
		 * @since 1.0.0
		 */
		hideSubmitDefault: function() {

			if ( ! app.isTargetFieldAdded() ) {
				return;
			}

			var $formSubmit = $( '.wpforms-preview .wpforms-field-submit' );

			if ( app.isCheckoutEnabled() ) {
				$formSubmit.hide();

				return;
			}

			$formSubmit.show();
		},

		/**
		 * Determine whether payments are enabled in the Payments > PayPal Commerce panel.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} True if Payments are enabled.
		 */
		isPaymentsEnabled: function() {

			return el.$singlePaymentControl.is( ':checked' ) || el.$recurringPaymentControl.is( ':checked' );
		},

		/**
		 * Determine whether PayPal Checkout payment method is enabled.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} True if PayPal Checkout payment method is enabled.
		 */
		isCheckoutEnabled: function() {

			return el.$fieldOptions.find( '.wpforms-field-option-paypal-checkout' ).is( ':checked' );
		},

		/**
		 * Determine whether Credit Card payment method is enabled.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} True if Credit Card payment method is enabled.
		 */
		isCreditCardEnabled: function() {

			return el.$fieldOptions.find( '.wpforms-field-option-credit-card' ).is( ':checked' );
		},

		/**
		 * Determine whether AJAX form submission is enabled in the Settings > General.
		 *
		 * @since 1.0.0
		 *
		 * @returns {boolean} True if AJAX form submission is enabled.
		 */
		isAJAXSubmitEnabled: function() {

			return el.$AJAXSubmitOption.is( ':checked' );
		},

		/**
		 * Uncheck the Payments > PayPal Commerce > Enable One-Time/Recurring Payments settings.
		 *
		 * @since 1.0.0
		 */
		unCheckEnabledPayments: function() {

			el.$singlePaymentControl.prop( 'checked', false ).trigger( 'change' );
			el.$recurringPaymentControl.prop( 'checked', false ).trigger( 'change' );

			el.$panelContent.find( '.wpforms-panel-content-section-payment' ).each( function() {
				$( this ).removeClass( 'wpforms-panel-content-section-payment-open' );
			} );
		},

		/**
		 * Update plans data.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} data Form save response data.
		 */
		updatePlanData: function( data ) {

			if ( typeof data.paypal_commerce_plans === 'undefined' ) {
				return;
			}

			for ( var index in data.paypal_commerce_plans ) {
				$( '#wpforms-panel-field-paypal_commerce-recurring-' + index + '-pp_product_id' ).val( data.paypal_commerce_plans[index]['pp_product_id'] );
				$( '#wpforms-panel-field-paypal_commerce-recurring-' + index + '-pp_plan_id' ).val( data.paypal_commerce_plans[index]['pp_plan_id'] );
			}
		},

		/**
		 * Update form state when plans data was updated.
		 * This will prevent a please-save-prompt to appear.
		 *
		 * @since 1.0.0
		 */
		updateFormState: function() {

			wpf.savedState = wpf.getFormState( '#wpforms-builder-form' );
		},

		/**
		 * Maybe disable some fields if plan already saved.
		 *
		 * @since 1.0.0
		 */
		disableFields: function() {

			var needsUpdate = false;

			el.$panelContent.find( '.wpforms-panel-content-section-payment-plan' ).each( function() {

				var $plan = $( this ),
					planId = $plan.data( 'plan-id' );

				if ( $plan.find( '#wpforms-panel-field-paypal_commerce-recurring-' + planId + '-pp_product_id' ).val() ) {

					var $tooltip = $( '<i class="fa fa-question-circle-o wpforms-help-tooltip"></i>' ),
						$hiddenInput = $( '<input type="hidden"></input>' ),
						$planNameWrap = $plan.find( '#wpforms-panel-field-paypal_commerce-recurring-' + planId + '-name-wrap' ),
						$planNameInput = $planNameWrap.find( 'input' ),
						$productTypeWrap = $plan.find( '#wpforms-panel-field-paypal_commerce-recurring-' + planId + '-product_type-wrap' ),
						$productTypeSelect = $productTypeWrap.find( 'select' ),
						$recurringTimesWrap = $plan.find( '#wpforms-panel-field-paypal_commerce-recurring-' + planId + '-recurring_times-wrap' ),
						$recurringTimesSelect = $recurringTimesWrap.find( 'select' );


					$planNameInput.attr( 'disabled', 'disabled' );
					$planNameWrap.prepend( $hiddenInput.clone().attr( 'name', $planNameInput.attr( 'name' ) ).val( $planNameInput.val() ) );
					$planNameWrap.find( '.wpforms-help-tooltip' ).remove();
					$planNameWrap.find( 'label' ).append( $tooltip.clone().attr( 'title', wpforms_builder.paypal_commerce_plan_name_disabled ) );

					$productTypeSelect.attr( 'disabled', 'disabled' );
					$productTypeWrap.prepend( $hiddenInput.clone().attr( 'name', $productTypeSelect.attr( 'name' ) ).val( $productTypeSelect.val() ) );
					$productTypeWrap.find( '.wpforms-help-tooltip' ).remove();
					$productTypeWrap.find( 'label' ).append( $tooltip.clone().attr( 'title', wpforms_builder.paypal_commerce_product_type_disabled ) );

					$recurringTimesSelect.attr( 'disabled', 'disabled' );
					$recurringTimesWrap.prepend( $hiddenInput.clone().attr( 'name', $recurringTimesSelect.attr( 'name' ) ).val( $recurringTimesSelect.val() ) );
					$recurringTimesWrap.find( '.wpforms-help-tooltip' ).remove();
					$recurringTimesWrap.find( 'label' ).append( $tooltip.clone().attr( 'title', wpforms_builder.paypal_commerce_recurring_times_disabled ) );

					needsUpdate = true;
				}
			} );

			if ( needsUpdate ) {

				// Re-init tooltips
				wpf.initTooltips();

				app.updateFormState();
			}
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsBuilderPaypalCommerce.init();
