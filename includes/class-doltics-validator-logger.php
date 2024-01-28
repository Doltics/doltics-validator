<?php
namespace DolticsValidator;

/**
 * Plugin logger.
 *
 * @package DolticsValidator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base Logging handler.
 */
class Doltics_Validator_Logger {

	/**
	 * Set up log directory
	 */
	public static function init_directory() {
		if ( ! is_dir( DOLTICS_VALIDATOR_PLUGIN_LOG_DIR ) ) {
			wp_mkdir_p( DOLTICS_VALIDATOR_PLUGIN_LOG_DIR );
		}
	}

	/**
	 * Do logging.
	 *
	 * @param mixed $message The log data.
	 */
	public static function log( $message ) {
		$log_time = gmdate( "Y-m-d\tH:i:s\t" );
		$log_file = gmdate( 'Y-m-d' );
		$log_file = trailingslashit( DOLTICS_VALIDATOR_PLUGIN_LOG_DIR ) . $log_file . '_validator.log';
		foreach ( func_get_args() as $param ) {
			// This is used in debugging purposes only to write to file.
			// phpcs:disable WordPress.PHP.DevelopmentFunctions
			if ( is_scalar( $param ) ) {
				$dump = $param;
			} else {
				$dump = var_export( $param, true );
			}
			error_log( $log_time . $dump . "\n", 3, $log_file );
			// phpcs:enable
		}
	}
}
