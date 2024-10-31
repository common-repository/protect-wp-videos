<?php

/**
 * S3 helper functions
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 */

/**
 * S3 helper functions
 *
 * Defines functions to work with S3.
 *
 * @since      1.0.0
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 * @author     Buildwps <gaupoit@outlook.com>
 */

class Aws_S3_Helper {

	protected $s3;

	protected $configs = null;

	public function __construct() {

		$this->configs = include(plugin_dir_path( __FILE__ ) . '/config.php');

		$this->s3 = new Aws\S3\S3Client([
			'version' => 'latest',
			'region' => $this->configs->s3_asia_region,
			'credentials' => [
				'key' => $this->configs->s3_asia_key,
				'secret' => $this->configs->s3_asia_secret
			]
		]);
	}

	/**
	 * Check file size for reading to upload
	 * @param $filename
	 *
	 * @return bool
	 */
	private function check_file_size ( $filename ) {

		$file_size = '314572800';
		$actualSize = filesize($filename);
		return $actualSize <= $file_size;

	}

	/**
	 * Upload to s3 private bucket
	 * @param $wp_url File path
	 *
	 * @return mixed|null
	 * @throws Exception
	 */
	public function upload_to_s3( $wp_url ) {

		$file_path = str_replace(site_url(), untrailingslashit(get_home_path()), $wp_url);

		if( is_file( $file_path )) {
			if( $this->check_file_size( $file_path) )  {
				$file_name = basename( $file_path );
				$customer_guid = get_option( 'puv_customer_guid' );
				$key = "${customer_guid}/${file_name}";
				$result = $this->s3->putObject(array(
					'Bucket' => $this->configs->s3_asia,
					'Key' => $key,
					'SourceFile' => $file_path,
					'region' => $this->configs->s3_asia_region
				));
				return $result->get('ObjectURL');
			} else {
				throw new Exception("Please upload the file which's size is less than 300mb");
			}
		}
	}

}