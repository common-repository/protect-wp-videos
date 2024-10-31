<?php

/**
 * The protect video functionality of the plugin.
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 */

/**
 * The protect video functionality of the plugin.
 *
 * Defines functions to create a protected video.
 *
 * @since      1.0.0
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/includes
 * @author     Buildwps <gaupoit@outlook.com>
 */

class Private_Video {

	/**
	 * Create rsa sh1 sign
	 * @param $policy
	 * @param $private_key_filename
	 *
	 * @return string
	 */
	private function rsa_sha1_sign($policy, $private_key_filename) {
		$signature = "";

		// load the private key
		$fp = fopen($private_key_filename, "r");
		$priv_key = fread($fp, 8192);
		fclose($fp);
		$pkeyid = openssl_get_privatekey($priv_key);

		// compute signature
		openssl_sign($policy, $signature, $pkeyid);

		// free the key from memory
		openssl_free_key($pkeyid);

		return $signature;
	}

	private function url_safe_base64_encode($value) {
		$encoded = base64_encode($value);
		// replace unsafe characters +, = and / with the safe characters -, _ and ~
		return str_replace(
			array('+', '=', '/'),
			array('-', '_', '~'),
			$encoded);
	}

	private function create_stream_name($stream, $policy, $signature, $key_pair_id, $expires) {
		$result = $stream;
		// if the stream already contains query parameters, attach the new query parameters to the end
		// otherwise, add the query parameters
		$separator = strpos($stream, '?') == FALSE ? '?' : '&';

		// the presence of an expires time means we're using a canned policy
		if($expires) {
			$result .= $separator . "Expires=" . $expires . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
		}
		// not using a canned policy, include the policy itself in the stream name
		else {
			$result .= $separator . "Policy=" . $policy . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;
		}
		// new lines would break us, so remove them
		return str_replace('\n', '', $result);
	}

	private function encode_query_params($stream_name) {
		// the adobe flash player has trouble with query parameters being passed into it,
		// so replace the bad characters with their url-encoded forms
		return str_replace(
			array('?', '=', '&'),
			array('%3F', '%3D', '%26'),
			$stream_name);
	}

	public function get_canned_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $expires) {
		// this policy is well known by CloudFront, but you still need to sign it, since it contains your parameters
		$canned_policy = '{"Statement":[{"Resource":"' . $video_path . '","Condition":{"DateLessThan":{"AWS:EpochTime":'. $expires . '}}}]}';
		// the policy contains characters that cannot be part of a URL, so we base64 encode it
		$encoded_policy = $this->url_safe_base64_encode($canned_policy);
		// sign the original policy, not the encoded version
		$signature = $this->rsa_sha1_sign($canned_policy, $private_key_filename);

		// make the signature safe to be included in a url
		$encoded_signature = $this->url_safe_base64_encode($signature);

		// combine the above into a stream name
		$stream_name = $this->create_stream_name($video_path, null, $encoded_signature, $key_pair_id, $expires);
		// url-encode the query string characters to work around a flash player bug
		return $stream_name;
	}

	public function get_custom_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $policy) {
		// the policy contains characters that cannot be part of a URL, so we base64 encode it
		$encoded_policy = $this->url_safe_base64_encode($policy);
		// sign the original policy, not the encoded version
		$signature = $this->rsa_sha1_sign($policy, $private_key_filename);

		// make the signature safe to be included in a url
		$encoded_signature = $this->url_safe_base64_encode($signature);

		// combine the above into a stream name
		$stream_name = $this->create_stream_name($video_path, $encoded_policy, $encoded_signature, $key_pair_id, null);
		// url-encode the query string characters to work around a flash player bug
		return $this->encode_query_params($stream_name);
	}

	public function getSignedURL($resource, $timeout)
	{
		$configs = include(plugin_dir_path( __FILE__ ) . '/config.php');
		//This comes from key pair you generated for cloudfront
		$keyPairId = $configs->cf_id;

		$expires = time() + $timeout; //Time out in seconds
		$json = '{"Statement":[{"Resource":"'.$resource.'","Condition":{"DateLessThan":{"AWS:EpochTime":'.$expires.'}}}]}';

		//Read Cloudfront Private Key Pair
		$fp=fopen( PWV_BASE_DIR  . "/keys/pk-${keyPairId}.pem","r" );
		$priv_key=fread($fp,8192);
		fclose($fp);

		//Create the private key
		$key = openssl_get_privatekey($priv_key);
		if(!$key)
		{
			echo "<p>Failed to load private key!</p>";
			return;
		}

		//Sign the policy with the private key
		if(!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1))
		{
			echo '<p>Failed to sign policy: '.openssl_error_string().'</p>';
			return;
		}

		//Create url safe signed policy
		$base64_signed_policy = base64_encode($signed_policy);
		$signature = str_replace(array('+','=','/'), array('-','_','~'), $base64_signed_policy);

		//Construct the URL
		$url = $resource.'?Expires='.$expires.'&Signature='.$signature.'&Key-Pair-Id='.$keyPairId;

		return $url;
	}
}