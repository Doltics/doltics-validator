<?php
/**
 * Admin class.
 *
 * @package DolticsValidator
 */

namespace DolticsValidator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main plugin admin class.
 */
class Doltics_Validator_Admin {

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
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_post_doltics_validator_save', array( $this, 'process_admin_actions' ) );
	}

	/**
	 * Set up the admin menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_options_page(
			__( 'Doltics Validator', 'doltics-validator' ),
			__( 'Doltics Validator', 'doltics-validator' ),
			'manage_options',
			'doltics-validator',
			array( $this, 'admin_menu_page' )
		);
	}

	/**
	 * Render the admin menu page.
	 *
	 * @return void
	 */
	public function admin_menu_page() {
		$validator_options = doltics_validator_get_options();
		?>
		<div class="doltics-validator-wrap wrap">
			<h1><?php esc_html_e( 'Doltics Validator Settings', 'doltics-validator' ); ?></h1>
			<form method="POST" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'doltics_validator_nonce', 'doltics_validator_nonce_field' ); ?>
				<input type="hidden" name="action" value="doltics_validator_save" />
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Email Validation', 'doltics-validator' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Enable Email Validation', 'doltics-validator' ); ?></span>
								</legend>
								<label for="enabled">
									<input name="enabled" type="checkbox" id="enabled" value="1" <?php checked( $validator_options['enabled'], 1 ); ?> />
									<p class="description" id="enabled-description">
										<?php
										// translators: 1: Opening link tag 2: Closing link tag.
										echo sprintf( esc_html__( 'Enable this to use our email %1$svalidation API%2$s', 'doltics-validator' ), '<a href="https://docs.doltics.com/validator" target="_blank">', '</a>' );
										?>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Debugging', 'doltics-validator' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Enable Debugging', 'doltics-validator' ); ?></span>
								</legend>
								<label for="debugging">
									<input name="debugging" type="checkbox" id="debugging" value="1" <?php checked( $validator_options['debug'], 1 ); ?> />
									<p class="description" id="enabled-debugging">
									<?php
									// translators: The debug directory.
									echo sprintf( esc_html__( 'Enable debugging. This will generate logs for the API requests in the  %s directory', 'doltics-validator' ), '<em>' . esc_attr( DOLTICS_VALIDATOR_PLUGIN_LOG_DIR ) . '</em>' );
									?>
									</p>
								</label>
							</fieldset>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'doltics-validator' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Process the admin actions.
	 * Save and update options.
	 *
	 * @return void
	 */
	public function process_admin_actions() {
		$url = admin_url( 'options-general.php?page=doltics-validator' );
		if ( isset( $_POST['doltics_validator_nonce_field'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['doltics_validator_nonce_field'] ) ), 'doltics_validator_nonce' )
		) {
			$debugging = isset( $_POST['debugging'] ) ? 1 : 0;
			$enabled   = isset( $_POST['enabled'] ) ? 1 : 0;

			update_option(
				'doltics_validator_options',
				array(
					'debug'   => $debugging,
					'enabled' => $enabled,
				),
				false
			);

			$url = add_query_arg( 'updated', 'true', $url );
		}

		wp_safe_redirect( $url );
		exit;
	}
}
