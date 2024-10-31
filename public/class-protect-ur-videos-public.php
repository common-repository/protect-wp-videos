<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/public
 * @author     Buildwps <gaupoit@outlook.com>
 */
class Protect_Ur_Videos_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $private_video    Private video utilities.
	 */
	private $private_video;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->private_video = new Private_Video();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Protect_Ur_Videos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Protect_Ur_Videos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/protect-ur-videos-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Protect_Ur_Videos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Protect_Ur_Videos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$this->register_sdks();
		$this->register_videojs_lib();
		$none_obj = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'pur_nonce_public' )
		);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/protect-ur-videos-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '_util', plugin_dir_url( __FILE__ ) . 'js/protect-ur-videos-util.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajax_obj', $none_obj );
		$this->register_videojs_plugin();
	}

	private function register_sdks() {
		wp_register_script( 'chromecast-sdk', 'http://www.gstatic.com/cv/js/sender/v1/cast_sender.js' );
		wp_enqueue_script( 'chromecast-sdk' );
	}

	/**
	 * Register videojs library
	 */
	private function register_videojs_lib() {
		/**
		 * Use CDN
		 */
		wp_register_script( 'videojs', '//vjs.zencdn.net/6.2.4/video.js' );
		wp_register_style( 'videojs', '//vjs.zencdn.net/6.2.4/video-js.css' );
		wp_enqueue_script( 'videojs' );
		wp_enqueue_style( 'videojs' );

		wp_register_style( 'videojs.errors', plugin_dir_url( __FILE__ ) . 'js/videojs-errors-3.0.2/videojs-errors.css' );
		wp_enqueue_style( 'videojs.errors' );
		wp_register_script( 'videojs.errors', plugin_dir_url( __FILE__ ) . 'js/videojs-errors-3.0.2/videojs-errors.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'videojs.errors' );
	}

	private function register_videojs_plugin() {
		wp_register_style( 'videojs-chromecast', plugin_dir_url( __FILE__ ) . 'js/video.js-chromecast/videojs-chromecast.css' );
		wp_enqueue_style( 'videojs-chromecast' );
		wp_register_script( 'videojs-chromecast', plugin_dir_url( __FILE__ ) . 'js/video.js-chromecast/videojs-chromecast.js' );
		wp_enqueue_script( 'videojs-chromecast' );
	}

	public function regenerate_private_url() {

		check_ajax_referer( 'pur_nonce_public', 'security' );
		$current_src = urldecode($_POST['src']);
		$is_casting = urldecode($_POST['isCasting']);

		if($is_casting) {
			$timeout = 3600;
		} else {
			$timeout = 5;
		}

		$url_arr = parse_url($current_src);
		$folder = get_option( 'puv_customer_guid' );
		$file_name = basename($url_arr['path']);
		$file_path = "${folder}/${file_name}";
		$url = $url_arr['scheme'] . '://' . $url_arr['host'] . '/' . urlencode($file_path);
		$mp4_signed_video = $this->private_video->getSignedURL( $url, $timeout );

		wp_send_json( $mp4_signed_video );
		wp_die();

	}

}
