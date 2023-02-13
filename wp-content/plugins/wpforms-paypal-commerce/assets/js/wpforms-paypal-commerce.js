/* global wpforms_paypal_single, wpforms_paypal_subscriptions, wpforms_paypal_commerce, wpforms, wpforms_settings, wpformsconditionals */

'use strict';

/**
 * WPForms PayPal Commerce function.
 *
 * @since 1.0.0
 */
var WPFormsPaypalCommerce = window.WPFormsPaypalCommerce || ( function( document, window, $ ) {

	/**
	 * Save original form submit handler.
	 *
	 * @since 1.0.0
	 *
	 * @type {Function}
	 */
	var originalSubmitHandler;

	/**
	 * Created Order ID.
	 *
	 * @since 1.0.0
	 *
	 * @type {string}
	 */
	var orderId = '';

	/**
	 * Matched Plan ID.
	 *
	 * @since 1.0.0
	 *
	 * @type {string}
	 */
	var planId = '';

	/**
	 * Signifies whether the Credit Card field is valid.
	 *
	 * @since 1.0.0
	 *
	 * @type {boolean}
	 */
	var isCreditCardFieldValid = true;

	/**
	 * Signifies whether the Conditional Logic fails for payments.
	 *
	 * @since 1.0.0
	 *
	 * @type {boolean}
	 */
	var isConditionalLogicFails = false;

	/**
	 * Number of page locked to switch.
	 *
	 * @since 1.0.0
	 *
	 * @type {int}
	 */
	var lockedPageToSwitch = 0;

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

			$( document )
				.on( 'wpformsReady', app.updateSubmitHandler )
				.on( 'wpformsProcessConditionalsField', function( e, formID, fieldID, pass, action ) {
					app.processConditionalsPayPalField( formID, fieldID, pass, action );
				} );
		},

		/**
		 * Initialized once the DOM is fully loaded.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			app.bindUIActions();

			$( '.wpforms-paypal-commerce .wpforms-form' ).each( function() {
				app.initPaypalScript( $( this ) );
			} );
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			$( document )
				.on( 'change', '.wpforms-paypal-commerce-payment-method', function() {
					app.paymentMethodToggle( $( this ).closest( '.wpforms-form' ) );
				} )
				.on( 'change', '.wpforms-paypal-commerce-conditional-trigger input, .wpforms-paypal-commerce-conditional-trigger select', function() {
					app.checkPaymentsConditionalLogic( $( this ).closest( '.wpforms-form' ) );
				} )
				.on( 'input', '.wpforms-paypal-commerce-conditional-trigger input[type=text], .wpforms-paypal-commerce-conditional-trigger input[type=email], .wpforms-paypal-commerce-conditional-trigger input[type=url], .wpforms-paypal-commerce-conditional-trigger input[type=number], .wpforms-paypal-commerce-conditional-trigger textarea', function() {
					app.checkPaymentsConditionalLogic( $( this ).closest( '.wpforms-form' ) );
				} )
				.on( 'wpformsBeforePageChange', app.pageChange );
		},

		/**
		 * Update submitHandler for forms containing PayPal Commerce.
		 *
		 * @since 1.0.0
		 */
		updateSubmitHandler: function() {

			if ( typeof $.fn.validate === 'undefined' ) {
				return;
			}

			$( '.wpforms-paypal-commerce form.wpforms-validate' ).each( function() {

				var $form = $( this ),
					validator = $form.data( 'validator' );

				if ( ! app.isPaymentsEnabled( $form ) ) {
					return;
				}

				if ( ! validator ) {
					return true;
				}

				// Store the original submitHandler.
				originalSubmitHandler = validator.settings.submitHandler;

				// Replace the default submit handler.
				validator.settings.submitHandler = app.submitHandler;
			} );
		},

		/**
		 * Update submitHandler for forms containing PayPal Commerce.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} form JS form element.
		 */
		submitHandler: function( form ) {},

		/**
		 * Handle Process Conditionals for PayPal Commerce field.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} formID  Form ID.
		 * @param {string} fieldID Field ID.
		 * @param {bool}   pass    Pass logic.
		 * @param {string} action  Action to execute.
		 */
		// eslint-disable-next-line complexity
		processConditionalsPayPalField: function( formID, fieldID, pass, action ) {

			var $form = $( '#wpforms-form-' + formID ),
				$field = $form.find( '.wpforms-field-paypal-commerce' );

			if ( ! app.isPaymentsEnabled( $form ) ) {
				return;
			}

			if (
				$field.data( 'field-id' ).toString() !== fieldID
			) {
				return;
			}

			if (
				( pass && action === 'hide' ) ||
				( ! pass && action !== 'hide' )
			) {
				app.hidePayPalSubmitButtons( $form );

				return;
			}

			app.checkPaymentsConditionalLogic( $form );
		},

		/**
		 * Hide PayPal Commerce submit buttons.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form Object.
		 */
		hidePayPalSubmitButtons: function( $form ) {

			var validator = $form.data( 'validator' );

			$form.find( '.wpforms-paypal-commerce-single-submit-button' ).addClass( 'wpforms-hidden' );
			$form.find( '.wpforms-paypal-commerce-subscriptions-submit-button' ).addClass( 'wpforms-hidden' );
			$form.find( '.wpforms-submit' ).removeClass( 'wpforms-hidden' );

			validator.settings.submitHandler = originalSubmitHandler;
		},

		/**
		 * Toggle visibility of the PayPal Commerce payment method.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		paymentMethodToggle: function( $form ) {

			var isCheckoutSelected = app.isCheckoutSelected( $form );

			$form.find( '.wpforms-paypal-commerce-payment-method' ).removeClass( 'wpforms-hidden' );
			$form.find( '.wpforms-paypal-commerce-card-fields' ).toggleClass( 'wpforms-hidden', isCheckoutSelected );
			$form.find( '.wpforms-submit' ).toggleClass( 'wpforms-hidden', isCheckoutSelected );
			$form.find( '.wpforms-paypal-commerce-single-submit-button' ).toggleClass( 'wpforms-hidden', ! isCheckoutSelected );
		},

		/**
		 * Determine if Checkout payment method is selected.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 *
		 * @returns {boolean} True if Checkout method is selected.
		 */
		isCheckoutSelected: function( $form ) {

			var $cardFields = $form.find( '.wpforms-paypal-commerce-card-fields' ),
				selectedMethod = $form.find( '.wpforms-paypal-commerce-payment-method' ).val();

			return ( typeof selectedMethod === 'undefined' && ! $cardFields.length ) || selectedMethod === 'checkout';
		},

		/**
		 * Check if Payments Enabled.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 *
		 * @returns {boolean} True if enabled at least one method.
		 */
		isPaymentsEnabled( $form ) {

			var formId = $form.data( 'formid' ),
				formOptions = wpforms_paypal_commerce.payment_options[formId];

			if (
				typeof formOptions === 'undefined' ||
				(
					! formOptions['credit_card'] &&
					! formOptions['paypal_checkout']
				)
			) {
				return false;
			}

			return true;
		},

		/**
		 * Initialize PayPal Commerce script.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		// eslint-disable-next-line complexity,max-lines-per-function
		initPaypalScript: function( $form ) {

			var formId = $form.data( 'formid' ),
				formOptions = wpforms_paypal_commerce.payment_options[formId];

			if ( ! app.isPaymentsEnabled( $form ) ) {
				return;
			}

			// Init subscription script if enabled.
			if ( formOptions['enable_recurring'] ) {
				app.initSubscriptions( $form );
			}

			// Init single script if enabled.
			if ( formOptions['enable_one_time'] ) {
				app.initSingle( $form );
			}

			app.checkPaymentsConditionalLogic( $form );
		},

		/**
		 * Check Payments Conditional Logic and show a payment button if passed.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		// eslint-disable-next-line complexity,max-lines-per-function
		checkPaymentsConditionalLogic: function( $form ) {

			var formId = $form.data( 'formid' ),
				$field = $form.find( '.wpforms-field-paypal-commerce' ),
				$fieldPaymentMethod = $field.find( '.wpforms-paypal-commerce-payment-method' ),
				$fieldCardFields = $field.find( '.wpforms-paypal-commerce-card-fields' ),
				validator = $form.data( 'validator' ),
				$singleButton = $form.find( '.wpforms-paypal-commerce-single-submit-button' ),
				$subscriptionsButton = $form.find( '.wpforms-paypal-commerce-subscriptions-submit-button' ),
				$formSubmit = $form.find( '.wpforms-submit' );

			if ( ! app.isPaymentsEnabled( $form ) ) {
				return;
			}

			if ( $field.hasClass( 'wpforms-conditional-hide' ) ) {
				app.hidePayPalSubmitButtons( $form );
				return;
			}

			validator.settings.submitHandler = app.submitHandler;

			planId = '';
			isConditionalLogicFails = false;

			// Init subscription script if enabled or CL matched.
			if ( app.isSubscriptionsToExecute( formId ) ) {
				$subscriptionsButton.removeClass( 'wpforms-hidden' );

				if ( $field.find( '.wpforms-field-description' ).length ) {
					$field.show();
					$fieldPaymentMethod.addClass( 'wpforms-hidden' );
					$fieldCardFields.addClass( 'wpforms-hidden' );
				} else {
					$field.hide();
				}

				$singleButton.addClass( 'wpforms-hidden' );
				$formSubmit.addClass( 'wpforms-hidden' );

				return;
			}

			if ( app.isSingleToExecute( formId ) ) {
				$field.show();
				$fieldPaymentMethod.removeClass( 'wpforms-hidden' );

				app.paymentMethodToggle( $form );

				$subscriptionsButton.addClass( 'wpforms-hidden' );

				return;
			}

			// If no script to load, then hide field and checkout button.
			$field.hide();
			isConditionalLogicFails = true;
			app.hidePayPalSubmitButtons( $form );
		},

		/**
		 * Check if Subscription payment needs to be executed.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} formId Form ID.
		 *
		 * @returns {boolean} True if matched.
		 */
		// eslint-disable-next-line complexity
		isSubscriptionsToExecute: function( formId ) {

			var $form = $( '#wpforms-form-' + formId ),
				formOptions = wpforms_paypal_commerce.payment_options[formId];

			if ( ! formOptions['paypal_checkout'] ) {
				return false;
			}

			// Plan with matched CL have a higher priority.
			if (
				formOptions['enable_recurring'] &&
				typeof wpforms_paypal_commerce.conditional_rules[formId] !== 'undefined' &&
				typeof wpforms_paypal_commerce.conditional_rules[formId].recurring !== 'undefined' &&
				app.processConditionals( $form, wpforms_paypal_commerce.conditional_rules[formId].recurring )
			) {
				return true;
			}

			if ( planId !== '' ) {
				return true;
			}

			return formOptions['recurring_no_rules'];
		},

		/**
		 * Check if Single payment needs to be executed.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} formId Form ID.
		 *
		 * @returns {boolean} True if matched.
		 */
		isSingleToExecute: function( formId ) {

			var $form = $( '#wpforms-form-' + formId ),
				formOptions = wpforms_paypal_commerce.payment_options[formId];

			return ( formOptions['enable_one_time'] &&
					(
						typeof wpforms_paypal_commerce.conditional_rules[formId] === 'undefined' ||
						typeof wpforms_paypal_commerce.conditional_rules[formId]['one_time'] === 'undefined'
					)
			) ||
				(
					typeof wpforms_paypal_commerce.conditional_rules[formId] !== 'undefined' &&
					typeof wpforms_paypal_commerce.conditional_rules[formId]['one_time'] !== 'undefined' &&
					app.processConditionals( $form, wpforms_paypal_commerce.conditional_rules[formId]['one_time'] )
				);
		},

		/**
		 * Init Single.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		// eslint-disable-next-line max-lines-per-function
		initSingle: function( $form ) {

			var formID = $form.data( 'formid' ),
				$field = $form.find( '.wpforms-field-paypal-commerce' ),
				$hiddenInput = $form.find( '.wpforms-paypal-commerce-credit-card-hidden-input' ),
				fieldID = $field.data( 'field-id' ),
				formOptions = wpforms_paypal_commerce.payment_options[formID],
				buttonArgs = {},
				cardArgs = {};

			if ( typeof wpforms_paypal_single === 'undefined' ) {
				app.displaySdkError( $form );

				return;
			}

			if ( formOptions['paypal_checkout'] ) {
				buttonArgs.style = app.checkoutStyle( formOptions );
				buttonArgs.onClick = function( data, actions ) {
					return app.onCheckoutClick( $form, data, actions );
				};
				buttonArgs.createOrder = function() {
					return app.createSingleOrder( $form );
				};
				buttonArgs.onApprove = function( data ) {
					app.onCheckoutApprove( $form, data );
				};
				buttonArgs.onCancel = function() {
					app.onCancel( $form );
				};
				buttonArgs.onError = function( err ) {
					app.onError( $form, err );
				};

				$( document ).trigger( 'loadPPScript', [ formID, buttonArgs ] );

				// Render Checkout button.
				// eslint-disable-next-line new-cap
				wpforms_paypal_single.Buttons( buttonArgs ).render( '#wpforms-paypal-commerce-single-submit-button-' + formID );
			}

			if ( ! formOptions['credit_card'] ) {
				return;
			}

			cardArgs.styles = {
				'input': {
					'font-family' : $hiddenInput.css( 'font-family' ),
					'font-size'   : $hiddenInput.css( 'font-size' ),
					'font-weight' : $hiddenInput.css( 'font-weight' ),
					'color'       : '#333',
				},
			};

			cardArgs.fields = {
				number: {
					selector: '#wpforms-' + formID + '-field_' + fieldID + '-cardnumber',
				},
				cvv: {
					selector: '#wpforms-' + formID + '-field_' + fieldID + '-cardcode',
					placeholder: '•••',
				},
				expirationDate: {
					selector: '#wpforms-' + formID + '-field_' + fieldID + '-carddate',
				},
			};

			cardArgs.createOrder = function() {
				return app.createSingleOrder( $form );
			};

			app.renderCreditCard( $form, cardArgs );
		},

		/**
		 * Render Credit Card fields.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {object} cardArgs Card Args.
		 */
		// eslint-disable-next-line max-lines-per-function
		renderCreditCard: function( $form, cardArgs ) {

			var validator = $form.data( 'validator' ),
				$field = $form.find( '.wpforms-field-paypal-commerce' ),
				formID = $form.data( 'formid' ),
				formOptions = wpforms_paypal_commerce.payment_options[formID];

			// eslint-disable-next-line max-lines-per-function
			wpforms_paypal_single.HostedFields.render( cardArgs ).then( ( cardFields ) => {

				var cardError = '';

				cardFields.on( 'cardTypeChange', function( event ) {

					cardError = '';

					if ( typeof event.cards[0] === 'undefined' || event.cards.length !== 1 ) {
						return;
					}

					var cardType = event.cards[0].type;

					if ( ! formOptions['supported_cards'].includes( cardType ) ) {
						cardError = event.cards[0].niceType + ' ' + wpforms_paypal_commerce.i18n.card_not_supported;

						app.onFieldError( $form, cardArgs.fields.number.selector, cardError );
					}
				} );

				cardFields.on( 'focus', function( event ) {

					var selector = event.emittedBy;

					$form.find( cardArgs.fields[selector].selector ).parent().find( '.wpforms-error' ).remove();
				} );

				// Process card fields on form submit.
				// eslint-disable-next-line complexity,max-lines-per-function
				$form.find( '.wpforms-submit, .wpforms-page-button' ).on( 'click', function( event ) {

					if ( $field.hasClass( 'wpforms-conditional-hide' ) || isConditionalLogicFails ) {

						validator.settings.submitHandler = originalSubmitHandler;

						return;
					}

					var state = cardFields.getState();

					if (
						! $field.find( '.wpforms-field-paypal-commerce-name input' ).hasClass( 'wpforms-field-required' ) &&
						state.fields.number.isEmpty &&
						state.fields.expirationDate.isEmpty &&
						state.fields.cvv.isEmpty
					) {
						validator.settings.submitHandler = originalSubmitHandler;

						return;
					}

					if ( $( this ).hasClass( 'wpforms-page-button' ) ) {

						var $creditFields = $( this ).closest( '.wpforms-page' ).find( '.wpforms-paypal-commerce-card-fields' );

						if ( ! $creditFields.length || ! $creditFields.is( ':visible' ) ) {
							return;
						}

						isCreditCardFieldValid = true;
					}

					event.preventDefault();

					var cardFieldsValid = Object.keys( state.fields ).every( function( key ) {
						return state.fields[key].isValid;
					} );

					if ( ! cardFieldsValid ) {

						for ( var key of Object.keys( state.fields ) ) {
							if ( ! state.fields[key].isValid ) {
								app.onFieldError( $form, cardArgs.fields[key].selector, wpforms_paypal_commerce.i18n[key] );

								isCreditCardFieldValid = false;
							}
						}

						return;
					}

					if ( cardError ) {
						app.onFieldError( $form, cardArgs.fields.number.selector, cardError );
						return;
					}

					if ( ! $( this ).hasClass( 'wpforms-submit' ) ) {
						return;
					}

					if ( ! $form.validate().form() ) {
						return;
					}

					$form.find( '.wpforms-submit-spinner' ).show();
					$form.find( '.wpforms-submit' ).prop( 'disabled', true );

					cardFields
						.submit( {

							// Cardholder's first and last name.
							cardholderName: $form.find( '.wpforms-field-paypal-commerce-cardname' ).val(),

							// Trigger 3D Secure authentication.
							contingencies: [ 'SCA_WHEN_REQUIRED' ],
						} )
						.then( function( payload ) {

							if ( payload.liabilityShift && payload.liabilityShift !== 'POSSIBLE' ) {
								app.onError( $form, wpforms_paypal_commerce.i18n.secure_error );

								return;
							}

							// 3D Secure passed successfully.
							$form.find( '.wpforms-paypal-commerce-order-id' ).val( orderId );

							originalSubmitHandler( $form );
						} )
						.catch( ( err ) => {
							$form.find( '.wpforms-submit-spinner' ).hide();
							$form.find( '.wpforms-submit' ).prop( 'disabled', false );

							app.onError( $form, err );
						} );
				} );
			} );
		},

		/**
		 * Init Subscriptions.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		initSubscriptions: function( $form ) {

			var formID = $form.data( 'formid' ),
				formOptions = wpforms_paypal_commerce.payment_options[formID],
				buttonArgs = {};

			if ( ! formOptions['paypal_checkout'] ) {
				return;
			}

			if ( typeof wpforms_paypal_subscriptions === 'undefined' ) {
				app.displaySdkError( $form );

				return;
			}

			buttonArgs.style = app.checkoutStyle( formOptions );
			buttonArgs.onClick = function( data, actions ) {
				return app.onCheckoutClick( $form, data, actions );
			};
			buttonArgs.createSubscription = function() {
				return app.createSubscription( $form );
			};
			buttonArgs.onApprove = function( data ) {
				app.approveSubscription( $form, data );
			};
			buttonArgs.onCancel = function() {
				app.onCancel( $form );
			};
			buttonArgs.onError = function() {
				app.onError( $form, wpforms_paypal_commerce.i18n.subscription_error );
			};

			// Render Checkout button.
			// eslint-disable-next-line new-cap
			wpforms_paypal_subscriptions.Buttons( buttonArgs ).render( '#wpforms-paypal-commerce-subscriptions-submit-button-' + formID );
		},

		/**
		 * Get Checkout Style.
		 *
		 * @param {Array} formOptions Options.
		 *
		 * @returns {Array} Checkout Style Options.
		 */
		checkoutStyle: function( formOptions ) {

			return {
				color: formOptions.color,
				shape: formOptions.shape,
			};
		},

		/**
		 * On PayPal Checkout Click.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form   Form element.
		 * @param {object} data    Checkout data.
		 * @param {object} actions Checkout actions.
		 *
		 * @returns {object} Action.
		 */
		onCheckoutClick: function( $form, data, actions ) {

			$form.find( '.wpforms-paypal-commerce-error' ).remove();

			if ( ! $form.validate().form() ) {
				return actions.reject();
			}

			return actions.resolve();
		},

		/**
		 * On PayPal Cancel.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 */
		onCancel: function( $form ) {

			var error = document.createElement( 'div' );

			error.classList.add( 'wpforms-error-container' );
			error.classList.add( 'wpforms-paypal-commerce-error' );
			error.innerHTML = wpforms_paypal_commerce.i18n.on_cancel;

			$form.find( '.wpforms-submit' ).before( error.cloneNode( true ) );
		},

		/**
		 * On PayPal Error.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {string|object} err Error text.
		 */
		// eslint-disable-next-line complexity
		onError: function( $form, err ) {

			var error = $form.find( '.wpforms-paypal-commerce-error' ),
				message = wpforms_paypal_commerce.i18n.on_error;

			if ( ! wpforms.amountTotalCalc( $form ) ) {
				message = wpforms_paypal_commerce.i18n.empty_amount;
			} else if ( typeof err === 'string' ) {
				message = err;
			} else if ( typeof err.details !== 'undefined' && typeof err.details[0].issue !== 'undefined' ) {
				message = wpforms_paypal_commerce.i18n.api_error + ' ' + err.details[0].issue + '<br>' + message;
			} else if ( typeof err.name !== 'undefined' && err.name !== 'INVALID' ) {
				message = wpforms_paypal_commerce.i18n.api_error + ' ' + err.name + '<br>' + message;
			}

			if ( error.length ) {
				error.html( message );

				return;
			}

			error = document.createElement( 'div' );
			error.classList.add( 'wpforms-error-container' );
			error.classList.add( 'wpforms-paypal-commerce-error' );

			error.innerHTML = message;

			$form.find( '.wpforms-submit' ).before( error.cloneNode( true ) );
		},

		/**
		 * On PayPal Error.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {string} selector Selector ID.
		 * @param {string} err Error text.
		 */
		onFieldError: function( $form, selector, err ) {

			var error   = $form.find( selector ).parent().find( '.wpforms-error' ),
				message = typeof err.message === 'undefined' ? err : err.message;

			if ( error.length ) {
				error.html( message );

				return;
			}

			error = document.createElement( 'label' );

			error.classList.add( 'wpforms-error' );
			error.innerHTML = message;

			$form.find( selector ).after( error.cloneNode( true ) );
		},

		/**
		 * Display a SDK error.
		 *
		 * @param {jQuery} $form Form element.
		 *
		 * @since 1.0.0
		 */
		displaySdkError: function( $form ) {

			$form
				.find( '.wpforms-field-paypal-commerce' )
				.append( $( '<label></label>', {
					text: wpforms_paypal_commerce.i18n.missing_sdk_script,
					class: 'wpforms-error',
				} ) );
		},

		/**
		 * On Create Single Order.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 *
		 * @returns {string} Order ID.
		 */
		createSingleOrder: function( $form ) {

			var formData = new FormData( $form.get( 0 ) );

			formData.append( 'total', wpforms.amountTotalCalc( $form ) );
			formData.append( 'nonce', wpforms_paypal_commerce.nonces.create );

			return fetch( wpforms_settings.ajaxurl + '?action=wpforms_paypal_commerce_create_order', {
				method: 'POST',
				body: formData,
			} ).then( function( res ) {
				return res.json();
			} ).then( function( orderData ) {

				orderId = orderData.data.id;

				return orderData.data.id;
			} );
		},

		/**
		 * On Approve Checkout Order.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {object} data  Checkout data.
		 */
		onCheckoutApprove: function( $form, data ) {

			$form.find( '.wpforms-paypal-commerce-source' ).val( data.paymentSource );
			$form.find( '.wpforms-paypal-commerce-order-id' ).val( data.orderID );

			$form.find( '.wpforms-submit-spinner' ).remove();
			$form.find( '.wpforms-paypal-commerce-single-spinner' ).show();

			originalSubmitHandler( $form );
		},

		/**
		 * On Create Subscription.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 *
		 * @returns {string} Subscription ID.
		 */
		createSubscription: function( $form ) {

			var formData = new FormData( $form.get( 0 ) );

			formData.append( 'total', wpforms.amountTotalCalc( $form ) );
			formData.append( 'planId', planId );
			formData.append( 'nonce', wpforms_paypal_commerce.nonces.create_subscription );

			return fetch( wpforms_settings.ajaxurl + '?action=wpforms_paypal_commerce_create_subscription', {
				method: 'POST',
				body: formData,
			} ).then( function( res ) {
				return res.json();
			} ).then( function( orderData ) {
				return orderData.data.id;
			} );
		},

		/**
		 * On Approve Subscription.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {object} data  Checkout data.
		 */
		approveSubscription: function( $form, data ) {

			$form.find( '.wpforms-paypal-commerce-source' ).val( data.paymentSource );
			$form.find( '.wpforms-paypal-commerce-subscription-id' ).val( data.subscriptionID );

			$form.find( '.wpforms-submit-spinner' ).remove();
			$form.find( '.wpforms-paypal-commerce-recurring-spinner' ).show();

			originalSubmitHandler( $form );
		},

		/**
		 * Callback for a page changing.
		 *
		 * @since 1.0.0
		 *
		 * @param {Event}  event       Event.
		 * @param {int}    currentPage Current page.
		 * @param {jQuery} $form       Current form.
		 */
		pageChange: function( event, currentPage, $form ) {

			// Stop navigation through page break pages.
			if (
				! isCreditCardFieldValid &&
				! app.isCheckoutSelected( $form ) &&
				(
					! lockedPageToSwitch ||
					lockedPageToSwitch === currentPage
				)
			) {

				lockedPageToSwitch = currentPage;
				event.preventDefault();
			}
		},

		/**
		 * Process conditionals.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $form Form element.
		 * @param {Array}  rules Fields array.
		 *
		 * @returns {boolean} Returns false if something wrong.
		 */
		// eslint-disable-next-line complexity,max-lines-per-function
		processConditionals: function( $form, rules ) {

			if ( ! rules.length ) {
				return false;
			}

			for ( var id in rules ) {

				var field = rules[ id ].logic,
					action = rules[ id ].action,
					pass = false;

				if ( ! field.length ) {
					pass = true;
				}

				// Groups.
				for ( var fieldID in field )  {
					// eslint-disable-next-line no-prototype-builtins
					if ( ! field.hasOwnProperty( fieldID ) ) {
						continue;
					}

					var group = field[ fieldID ],
						passGroup = true;

					// Rules.
					for ( var groupID in group ) {
						// eslint-disable-next-line no-prototype-builtins,max-depth
						if ( ! group.hasOwnProperty( groupID ) ) {
							continue;
						}

						var rule = group[groupID],
							val = '',
							passRule = false,
							left = '',
							right = '';

						// eslint-disable-next-line max-depth
						if ( ! rule.field ) {
							continue;
						}

						val = wpformsconditionals.getElementValueByRule( rule, $form );

						// eslint-disable-next-line max-depth
						if ( null === val ) {
							val = '';
						}

						left = $.trim( val ).toString().toLowerCase();
						right = $.trim( rule.value ).toString().toLowerCase();

						// eslint-disable-next-line max-depth
						switch ( rule.operator ) {
							case '==' :
								passRule = ( left === right );
								break;
							case '!=' :
								passRule = ( left !== right );
								break;
							case 'c' :
								passRule = ( left.indexOf( right ) > -1 && left.length > 0 );
								break;
							case '!c' :
								passRule = ( left.indexOf( right ) === -1 && right.length > 0 );
								break;
							case '^' :
								passRule = ( left.lastIndexOf( right, 0 ) === 0 );
								break;
							case '~' :
								passRule = ( left.indexOf( right, left.length - right.length ) !== -1 );
								break;
							case 'e' :
								passRule = ( left.length === 0 );
								break;
							case '!e' :
								passRule = ( left.length > 0 );
								break;
							case '>' :
								left = left.replace( /[^-0-9.]/g, '' );
								passRule = ( '' !== left ) && ( app.floatval( left ) > app.floatval( right ) );
								break;
							case '<' :
								left = left.replace( /[^-0-9.]/g, '' );
								passRule = ( '' !== left ) && ( app.floatval( left ) < app.floatval( right ) );
								break;
						}

						// eslint-disable-next-line max-depth
						if ( ! passRule ) {
							passGroup = false;
							break;
						}
					}

					if ( passGroup ) {
						pass = true;
						break;
					}
				}

				if ( pass && action === 'go' ) {
					planId = id;

					return pass;
				}
			}

			return pass;
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsPaypalCommerce.init();
