<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 * @author     Buildwps <gaupoit@outlook.com>
 */
class Protect_Ur_Videos_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'protect-ur-videos',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
