<?php

/**
 *
 * @link              https://www.buildwps.com/
 * @since             1.1.4
 * @package           Protect_Wordpress_Videos
 *
 * @wordpress-plugin
 * Plugin Name:       Protect WordPress Videos
 * Plugin URI:        https://www.buildwps.com/protect-wordpress-videos-plugin/
 * Description:       Protect WordPress Videos offers a simple, fast and secure way to embed and protect your WordPress videos.
 * Version:           1.1.4
 * Author:            ProFaceoff
 * Author URI:        https://www.profaceoff.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       protect-wordpress-videos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PWV_BASE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-protect-ur-videos-activator.php
 */
function activate_protect_ur_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-protect-ur-videos-activator.php';
	Protect_Ur_Videos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-protect-ur-videos-deactivator.php
 */
function deactivate_protect_ur_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-protect-ur-videos-deactivator.php';
	Protect_Ur_Videos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_protect_ur_videos' );
register_deactivation_hook( __FILE__, 'deactivate_protect_ur_videos' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-protect-ur-videos.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_protect_ur_videos() {

	$plugin = new Protect_Ur_Videos();
	$plugin->run();

}
run_protect_ur_videos();
