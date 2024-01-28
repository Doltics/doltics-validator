<?php
namespace DolticsValidator;

/**
 * Plugin and site integrations.
 *
 * @package DolticsValidator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base Integrations handler.
 */
class Doltics_Validator_Integrations {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get the instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main plugin constructor.
	 */
	public function __construct() {
		add_filter( 'is_email', array( $this, 'validate_email' ), 10, 3 );
	}

	/**
	 * Validate an email.
	 *
	 * @param boolean $is_email The email address if successfully passed the is_email() checks, false otherwise.
	 * @param string  $email The email address being checked.
	 * @param string  $context Context under which the email was tested.
	 *
	 * @return boolean
	 */
	public function validate_email( $is_email, $email, $context ) {
		if ( $is_email ) {
			$validator_options = doltics_validator_get_options();
			if ( 1 === $validator_options['enabled'] ) {
				// If the request is a valid email, we validate via the API.
				return Doltics_Validator_Api::validate( $email );
			}
		}
		return $is_email;
	}
}
