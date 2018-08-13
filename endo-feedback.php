<?php
/**
 * Plugin Name: Endo Feedback
 * Plugin URI: http://www.endocreative.com
 * Description: A simple plugin to collect feedback from users.
 * Version: 1.0.0
 * Author: Endo Creative
 * Author URI: http://www.endocreative.com
 * Text Domain: mytextdomain
 * License: GPL2
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-loader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_endo_feedback() {
	$plugin = new Endo_Feedback();
	$plugin->run();
}
run_endo_feedback();