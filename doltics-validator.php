<?php
/**
 * Plugin Name:         Doltics Validator
 * Plugin URI:          https://docs.doltics.com/validator
 * Description:         Validator API for your website.
 * Version:             1.0.0
 * Author:              doltics
 * Author URI:          https://www.doltics.com
 * License:             GPLv2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         doltics-validator
 * Domain Path:         /languages/
 *
 * @package DolticsValidator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'DOLTICS_VALIDATOR_PLUGIN_FILE' ) ) {
	define( 'DOLTICS_VALIDATOR_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'DOLTICS_VALIDATOR_PLUGIN_' ) ) {
	define( 'DOLTICS_VALIDATOR_PLUGIN', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'DOLTICS_VALIDATOR_PLUGIN_DIR' ) ) {
	define( 'DOLTICS_VALIDATOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Define the languages directory.
if ( ! defined( 'DOLTICS_VALIDATOR_PLUGIN_LANG_DIR' ) ) {
	define( 'DOLTICS_VALIDATOR_PLUGIN_LANG_DIR', DOLTICS_VALIDATOR_PLUGIN_DIR . 'languages' );
}

// Define the logs directory.
if ( ! defined( 'DOLTICS_VALIDATOR_PLUGIN_LOG_DIR' ) ) {
	$upload_dir = wp_upload_dir();
	define( 'DOLTICS_VALIDATOR_PLUGIN_LOG_DIR', $upload_dir['basedir'] . '/doltics-validator-logs/' );
}

/**
 * Autoload callback.
 *
 * @param string $class_name Name of the class to load.
 */
function doltics_validator_autoload( $class_name ) {

	$pools = explode( '\\', $class_name );

	if ( 'DolticsValidator' !== $pools[0] ) {
		return;
	}

	// Make sure the classname is set.
	if ( ! isset( $pools[1] ) ) {
		return;
	}

	/*
	 * Load plugin classes:
	 * - Class name: Doltics_Validator.
	 * - File name: class-doltics-validator.php.
	 */
	$class_file = 'class-' . strtolower( str_replace( '_', '-', $pools[1] ) ) . '.php';
	$class_path = DOLTICS_VALIDATOR_PLUGIN_DIR . '/includes/' . $class_file;

	if ( file_exists( $class_path ) ) {
		require $class_path;

		return;
	}
}

spl_autoload_register( 'doltics_validator_autoload' );

// If constant exists, another plugin is already initiated.
if ( defined( 'DOLTICS_VALIDATOR_PLUGIN_VERSION' ) ) {
	return;
}

// Set the plugin version.
define( 'DOLTICS_VALIDATOR_PLUGIN_VERSION', '1.0.0' );

/**
 * Get the validator options.
 *
 * @return array
 */
function doltics_validator_get_options() {
	return get_option(
		'doltics_validator_options',
		array(
			'debug'   => 0,
			'enabled' => 0,
			'apikey'  => '',
		)
	);
}

// Initiate the plugin.
\DolticsValidator\Doltics_Validator::instance();
