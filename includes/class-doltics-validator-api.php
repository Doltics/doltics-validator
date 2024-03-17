<?php
/**
 * API integrations.
 *
 * @package DolticsValidator
 */

namespace DolticsValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base API handler.
 */
class Doltics_Validator_Api {

	/**
	 * Check the API key
	 *
	 * @param string $apikey The API key.
	 *
	 * @return bool
	 */
	public static function check_api_key( $apikey ) {
		$domain = get_site_url();
		$url    = add_query_arg( array(
			'apikey' => $apikey,
			'domain' => $domain,
		), 'https://dolticsvalidator.com/wp-json/doltics-api/api/validate' );

		$response = wp_remote_get( $url );

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$resp = json_decode( $response['body'] );
			return $resp;
		}

		return apply_filters( 'doltics_validation_api_valid_response', false, $apikey );
	}

	/**
	 * Get the domain.
	 *
	 * @return void
	 */
	public static function get_domain() {
		$url    = get_site_url();
		$pieces = parse_url( $url );
		$domain = isset( $pieces['host'] ) ? $pieces['host'] : $pieces['path'];
		if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
			return $regs['domain'];
		}
		return $url;
	}

	/**
	 * Validate the email.
	 * This performs remote validations.
	 *
	 * @param string $email The email to validate.
	 *
	 * @return boolean
	 */
	public static function validate( $email ) {
		$options = doltics_validator_get_options();
		$api_key = isset( $options['apikey'] ) ? $options['apikey'] : false;

		$args = array();

		if ( $api_key && doltics_validator_is_api_key_valid() ) {
			$args = array(
				'headers' => array(
					'apikey' => $api_key
				)
			);
		}
		// Remote connection to validate email.
		// terms of service available at https://doltics.com/terms-of-service/
		// Docs available at https://doltics.com/docs-category/email-validation/ .
		$response = wp_remote_get(
			'https://validator.doltics.com/api/v2/validate/email?email=' . $email,
			$args
		);

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$resp = json_decode( $response['body'] );
			self::log( $email, $resp );
			return $resp;
		}

		self::log( $email, $response->get_error_message() );

		return apply_filters( 'doltics_validation_api_default_validate_response', false, $email );
	}


	/**
	 * Spam check.
	 *
	 * @param string $email The email
	 * @param string $content The content.
	 *
	 * @return bool|array Return the spam rating or the response status. False if no api key is set
	 */
	public static function spam_check( $email, $content ) {
		if ( ! doltics_validator_is_api_key_valid() ) {
			return false;
		}

		$options = doltics_validator_get_options();
		$api_key = isset( $options['apikey'] ) ? $options['apikey'] : false;

		if ( ! $api_key ) {
			return false;
		}

		// Remote connection to validate email.
		// terms of service available at https://doltics.com/terms-of-service/
		// Docs available at https://doltics.com/docs-category/email-validation/ .
		$response = wp_remote_post(
			'https://validator.doltics.com/api/v2/check/spam',
			array(
				'headers' => array(
					'apikey' => $api_key
				),
				'body'        => array(
					'email'   => $email,
					'content' => $content
				),
			)
		);

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$data = json_decode( $response['body'], true );
			self::log( $email, $data );
			return ( 200 === $data['status'] ) ? $data : false;
		}

		self::log( $email, $response->get_error_message() );

		return apply_filters( 'doltics_validation_api_default_spam_response', false, $email, $content );
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
