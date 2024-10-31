<?php

/**
 * Fired during plugin activation
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 * @author     Buildwps <gaupoit@outlook.com>
 */
class Protect_Ur_Videos_Activator {

	/**
	 * Plugin activation
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		Protect_Ur_Videos_Activator::generate_customer_guid();
		Protect_Ur_Videos_Activator::configure_mfl();
	}

	/**
	 * Generate customer guid
	 */
	private static function generate_customer_guid() {
		$customer_guid = get_option( 'puv_customer_guid' );
		if(!isset($customer_guid) || !$customer_guid) {
			$guid = uniqid("puv");
			$domain = Protect_Ur_Videos_Activator::get_domain_name();
			$curr_time = time();
			add_option( 'puv_customer_guid', "${domain}_${guid}_${curr_time}");
		}
	}

	private static function configure_mfl() {
		$mfl = get_option( 'puv_mfl' );
		if(!isset($mfl) || !$mfl) {
			add_option( 'puv_mfl', 0 );
		}
	}

	/**
	 * Get domain name
	 * @return mixed
	 */
	private static function get_domain_name() {
		$site_url = get_site_url();
		$schema = is_ssl() ? 'https://' : 'http://';
		return str_replace($schema, '', $site_url);
	}

}
