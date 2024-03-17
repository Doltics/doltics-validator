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

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

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
	 * Load admin assets
	 *
	 * @return void
	 */
	public function admin_assets() {
		$current_screen = get_current_screen();
		if ( $current_screen && strpos( $current_screen->id, 'doltics-validator' ) ) {
			add_thickbox();
		}
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
						<th scope="row"><?php esc_html_e( 'API Key', 'doltics-validator' ); ?></th>
						<td>
							<legend class="screen-reader-text">
								<span><?php esc_html_e( 'API Key', 'doltics-validator' ); ?></span>
							</legend>
							<label for="apikey">
								<input name="apikey" type="text" id="apikey" value="<?php echo isset( $validator_options['apikey'] ) ? esc_attr( $validator_options['apikey'] ) : ''; ?>" class="regular-text">
								<p class="description" id="enabled-apikey">
								<?php
								// translators: The debug directory.
								echo sprintf( esc_html__( 'Generate or copy your API key from %1$syour account%2$s', 'doltics-validator' ), '<a href="https://dolticsvalidator.com/my-account/api-keys/" target="_blank">', '</a>' );
								?>
								</p>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Email Validation', 'doltics-validator' ); ?></th>
						<td>
							<legend class="screen-reader-text">
								<span><?php esc_html_e( 'Enable Email Validation', 'doltics-validator' ); ?></span>
							</legend>
							<label for="enabled">
								<input name="enabled" type="checkbox" id="enabled" value="1" <?php checked( $validator_options['enabled'], 1 ); ?> />
								<p class="description" id="enabled-description">
									<?php
									// translators: 1: Opening link tag 2: Closing link tag.
									echo sprintf( esc_html__( 'Enable this to use our email %1$svalidation API%2$s', 'doltics-validator' ), '<a href="https://doltics.com/docs-category/email-validation/" target="_blank">', '</a>' );
									?>
								</p>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Protect Forms', 'doltics-validator' ); ?></th>
						<td>
							<legend class="screen-reader-text">
								<span><?php esc_html_e( 'Protect Forms', 'doltics-validator' ); ?></span>
							</legend>
							<label for="protect_forms">
								<input name="protect_forms" type="checkbox" id="protect_forms" value="1" <?php checked( isset( $validator_options['protect_forms'] ) ? $validator_options['protect_forms'] : 0, 1 ); ?> />
								<p class="description" id="protect_forms-description">
									<?php
									// translators: 1: Opening link tag 2: Closing link tag.
									echo sprintf( esc_html__( 'Enable this to use our  %1$sSPAM protection API%2$s to %3$sprotect your forms%4$s. An API key is required for this', 'doltics-validator' ), '<a href="https://doltics.com/docs-category/email-validation/" target="_blank">', '</a>' ,'<a href="#TB_inline?width=600&height=550&inlineId=doltics-form-integrations" title="' . __( 'Forms we integrate with', 'doltics-validator' ) . '" class="thickbox">', '</a>' );
									?>
								</p>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Debugging', 'doltics-validator' ); ?></th>
						<td>
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
						</td>
					</tr>
					
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'doltics-validator' ); ?>" />
				</p>
			</form>
			<div id="doltics-form-integrations" style="display:none;">
				<ul>
					<li><?php esc_html_e( 'Login and registration forms', 'doltics-validator' ); ?></li>
					<li><?php esc_html_e( 'Comment forms', 'doltics-validator' ); ?></li>
					<li><?php esc_html_e( 'Forminator', 'doltics-validator' ); ?></li>
				</ul>
			</div>
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
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['doltics_validator_nonce_field'] ) ), 'doltics_validator_nonce' ) &&
			current_user_can( 'manage_options' )
		) {
			$debugging     = isset( $_POST['debugging'] ) ? 1 : 0;
			$enabled       = isset( $_POST['enabled'] ) ? 1 : 0;
			$protect_forms = isset( $_POST['protect_forms'] ) ? 1 : 0;
			$apikey        = sanitize_text_field( wp_unslash( $_POST['apikey'] ) );

			update_option(
				'doltics_validator_options',
				array(
					'debug'         => $debugging,
					'enabled'       => $enabled,
					'apikey'        => $apikey,
					'protect_forms' => $protect_forms,
				),
				false
			);

			$url = add_query_arg( 'updated', 'true', $url );
		}

		wp_safe_redirect( $url );
		exit;
	}
}
