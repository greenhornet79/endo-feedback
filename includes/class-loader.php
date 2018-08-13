<?php 

/**
* Load the base class
*/
class Endo_Feedback {

	/**
	 * Kick it off
	 * 
	 */
	public function run() {

		self::setup_constants();
		self::includes();
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

	}

	public function load_scripts() {

		wp_enqueue_script( 'endo_feedback_script', ENDO_FEEDBACK_PLUGIN_URL . 'js/script.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_style( 'endo_feedback_style', ENDO_FEEDBACK_PLUGIN_URL . 'css/style.css' );

		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

		$params = array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $protocol )
		);

		wp_localize_script( 'endo_feedback_script', 'endo_feedback_script', $params );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'ENDO_FEEDBACK_VERSION' ) ) {
			define( 'ENDO_FEEDBACK_VERSION', '1.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'ENDO_FEEDBACK_PLUGIN_DIR' ) ) {
			define( 'ENDO_FEEDBACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'ENDO_FEEDBACK_PLUGIN_URL' ) ) {
			define( 'ENDO_FEEDBACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'ENDO_FEEDBACK_PLUGIN_FILE' ) ) {
			define( 'ENDO_FEEDBACK_PLUGIN_FILE', __FILE__ );
		}

	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
		// global $this_plugins_options;

		require_once ENDO_FEEDBACK_PLUGIN_DIR . 'form.php';

		// require_once ENDO_FEEDBACK_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
		// $this_plugins_options = this_plugin_get_settings();

	}

}