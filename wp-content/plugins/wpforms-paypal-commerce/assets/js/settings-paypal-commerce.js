/* global wpforms_admin */

'use strict';

/**
 * WPForms PayPal Commerce settings function.
 *
 * @since 1.0.0
 */
var WPFormsSettingsPaypalCommerce = window.WPFormsSettingsPaypalCommerce || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 1.0.0
	 *
	 * @type {object}
	 */
	var $el = {
		sandboxModeCheckbox: $( '#wpforms-setting-paypal-commerce-sandbox-mode' ),
		sandboxConnectionStatusBlock: $( '#wpforms-setting-row-paypal-commerce-connection-status-sandbox' ),
		productionConnectionStatusBlock: $( '#wpforms-setting-row-paypal-commerce-connection-status-live' ),
	};

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

			$el.sandboxModeCheckbox.on( 'change', app.credentialsFieldsDisplay );
		},

		/**
		 * Conditionally show the warning about switching mode.
		 *
		 * @since 1.0.0
		 */
		credentialsFieldsDisplay: function() {

			var isSandbox = $el.sandboxModeCheckbox.is( ':checked' );

			if ( isSandbox ) {
				$el.sandboxConnectionStatusBlock.show();
				$el.productionConnectionStatusBlock.hide();
			} else {
				$el.sandboxConnectionStatusBlock.hide();
				$el.productionConnectionStatusBlock.show();
			}

			if ( isSandbox && $el.sandboxConnectionStatusBlock.find( '.wpforms-paypal-commerce-connected' ).length ) {
				return;
			}

			if ( ! isSandbox && $el.productionConnectionStatusBlock.find( '.wpforms-paypal-commerce-connected' ).length ) {
				return;
			}

			app.modeChangedWarning();
		},

		/**
		 * Show the warning modal when mode is changed.
		 *
		 * @since 1.0.0
		 */
		modeChangedWarning: function() {

			$.confirm( {
				title: wpforms_admin.heads_up,
				content: wpforms_admin.paypal_commerce.mode_update,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_admin.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPFormsSettingsPaypalCommerce.init();

/**
 * Complete authorization onboard.
 *
 * @since 1.0.0
 *
 * @param {string} authCode Authorization code.
 * @param {string} sharedId Shared ID.
 */
// eslint-disable-next-line no-unused-vars
function wpformsPaypalOnboardCompleted( authCode, sharedId ) {

	fetch( wpforms_admin.ajax_url + '?action=wpforms_paypal_commerce_onboarding', {
		method: 'POST',
		headers: {
			'content-type': 'application/json',
		},
		body: JSON.stringify( {
			nonce: wpforms_admin.paypal_commerce.nonce,
			authCode: authCode,
			sharedId: sharedId,
		} ),
	} ).then( function( response ) {
		if ( ! response.ok ) {
			jQuery.alert( {
				title: false,
				content: wpforms_admin.paypal_commerce.connection_error,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_admin.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		}
	} );
}
