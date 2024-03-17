<?php
/**
 * Plugin and site integrations.
 *
 * @package DolticsValidator
 */

namespace DolticsValidator;

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

		$protect_forms = doltics_validator_get_setting( 'protect_forms' );
		if ( 1 === $this->validator_options['protect_forms'] ) {
			add_filter( 'preprocess_comment', array( $this, 'check_comment_data' ) );
			add_filter( 'forminator_spam_protection', array( $this, 'forminator_spam_protection' ), 10, 4 );
		}
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
			$enabled = doltics_validator_get_setting( 'enabled' );
			if ( 1 === $enabled ) {
				// If the request is a valid email, we validate via the API.
				return Doltics_Validator_Api::validate( $email );
			}
		}
		return $is_email;
	}

	/**
	 * Check if content is spam
	 *
	 * @param string $email The email.
	 * @param string $content The content.
	 *
	 * @return boolean
	 */
	public function is_spam( $email, $content ) {
		$response = Doltics_Validator_Api::spam_check( $email, $content );

		// API call failed. Do nothing.
		if ( ! $response ) {
			return false;
		}

		// If the probability of spam if higher, the content is spam.
		return ( $response['spam'] > $response['normal'] );
	}

	/**
	 * Check comment data
	 *
	 * @param array $commentdata The comment data.
	 * 
	 * @return array
	 */
	public function check_comment_data( $commentdata ) {
		$content = $commentdata['comment_content'];
		$email   = $commentdata['comment_author_email'];

		if ( ! empty( $commentdata['user_ID'] ) ) {
			$wp_user = get_userdata( $commentdata['user_ID'] );
			$email   = $user_info->user_email;
		}

		$is_spam = $this->is_spam( $email, $content );

		if ( $is_spam ) {
			$commentdata['comment_approved'] = 'spam';
		}
		
		return $commentdata;
	}

	/**
	 * Handle forminator spam protection.
	 *
	 * @param bool   $is_spam If the data is spam.
	 * @param array  $posted_params The posted parameters.
	 * @param int    $form_id The form id.
	 * @param string $form_type The form type.
	 *
	 * @return bool $is_spam
	 */
	public function forminator_spam_protection( $is_spam, $posted_params, $form_id, $form_type ) {
		$email   = false;
		$content = '';
		foreach ( $posted_params as $param ) {
			if ( isset( $param['name'] ) && isset( $param['value'] ) ) {
				$has_akismet_data = true;
				if ( filter_var( $param['value'], FILTER_VALIDATE_EMAIL ) ) {
					$email = $param['value'];
				}
				if ( is_array( $param['value'] ) ) {
					if (
						isset( $param['field_type'] ) &&
						'signature' === $param['field_type'] &&
						! empty( $param['value']['file']['file_url'] )
					) {
						$content .= "\n\n" . $param['value']['file']['file_url'];
					} else {
						$content .= "\n\n" . implode( ', ', $param['value'] );
					}
				} else {
					$content .= "\n\n" . $param['value'];
				}
			}
		}

		if ( $email && ! empty( $content ) ) {
			$is_spam = $this->is_spam( $email, $content );
		}

		return $is_spam;
	}
}
