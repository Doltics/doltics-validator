<?php
/**
 * Base class for plugin.
 *
 * @package DolticsValidator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main plugin class.
 * This loads and initiates the plugin.
 */
class Doltics_Validator {

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
		Doltics_Validator_Logger::init_directory();
		Doltics_Validator_Admin::instance();
		Doltics_Validator_Integrations::instance();
	}
}
