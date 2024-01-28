<?php
namespace DolticsValidator;

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
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

		add_filter( 'plugin_action_links_' . DOLTICS_VALIDATOR_PLUGIN , array( $this, 'add_action_links' ) );

		Doltics_Validator_Logger::init_directory();
		Doltics_Validator_Admin::instance();
		Doltics_Validator_Integrations::instance();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init_plugin() {

		// Set up the languages.
		load_plugin_textdomain(
			'doltics-validator',
			false,
			DOLTICS_VALIDATOR_PLUGIN_LANG_DIR
		);
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array $actions The default actions.
	 *
	 * @return array
	 */
	public function add_action_links( $actions ) {
		$mylinks = array(
			'<a href="' . admin_url( 'options-general.php?page=doltics-validator' ) . '">' . __( 'Settings', 'doltics-validator' ) . '</a>',
		);
		$actions = array_merge( $actions, $mylinks );
		return $actions;
	}
}
