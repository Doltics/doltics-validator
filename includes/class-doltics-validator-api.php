<?php
namespace DolticsValidator;

/**
 * API integrations.
 *
 * @package DolticsValidator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base API handler.
 */
class Doltics_Validator_Api {

	/**
	 * Validate the email.
	 * This performs remote validations.
	 *
	 * @param string $email The email to validate.
	 *
	 * @return boolean
	 */
	public static function validate( $email ) {
		// Remote connection to validate email.
		// terms of service available at https://doltics.com/terms-of-service/
		// Docs available at https://docs.doltics.com/validator
		$response = wp_remote_get( 'https://validator.doltics.com/api/validate/email?email=' . $email );

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$status = str_replace( '"', '', $response['body'] );
			self::log( $email, $status );
			return 'valid' === $status;
		}

		self::log( $email, $response->get_error_message() );

		return apply_filters( 'doltics_validation_api_default_validate_response', false, $email );
	}

	/**
	 * Log the response for debugging purposes.
	 * Depending on the settings used, logging will happen.
	 *
	 * @param string $email The email.
	 * @param string $response The API response.
	 *
	 * @return void
	 */
	public static function log( $email, $response ) {
		$validator_options = doltics_validator_get_options();
		if ( 1 === $validator_options['debug'] ) {
			Doltics_Validator_Logger::log( $email . ' :: ' . $response );
		}
	}
}
