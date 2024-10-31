<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       buildwps.com
 * @since      1.0.0
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Protect_Ur_Videos
 * @subpackage Protect_Ur_Videos/admin
 * @author     Buildwps <gaupoit@outlook.com>
 */
class Protect_Ur_Videos_Admin {

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
	 * @var The private video utilities
	 */
	private $private_video;

	private $s3;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->private_video = new Private_Video();
		$this->s3 = new Aws_S3_Helper();
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/protect-ur-videos-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		$none_obj = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'pur_nonce' )
		);
		$this->register_videojs_lib();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/protect-ur-videos-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'ajax_obj',  $none_obj);
	}

	/**
	 * Register videojs library
	 */
	private function register_videojs_lib() {
		/**
		 * Use CDN
		 */
		wp_register_script( 'videojs', '//vjs.zencdn.net/6.2.4/video.js' );
		wp_register_script( 'videojs', '//vjs.zencdn.net/6.2.4/video-js.css' );
		wp_register_style( 'hls', '//cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.8.2/videojs-contrib-hls.js' );

		wp_enqueue_script( 'videojs' );
		wp_enqueue_script( 'hls' );
		wp_enqueue_style( 'videojs' );
	}

	/**
	 * Register videojs button
	 */
	public function add_videojs_btn() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			return;
		} else if ( get_user_option('rich_editing') == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'videojs_mce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_videojs_button' ) );
		}
	}

	/**
	 * Register mce-button javascript file
	 * @param $plugin_array
	 *
	 * @return mixed
	 */
	public function videojs_mce_plugin( $plugin_array ) {
		$plugin_array['purvideojs'] = plugins_url( 'admin/js/', dirname(__FILE__) ) . 'pur-mce-button.js';
		return $plugin_array;
	}

	public function register_videojs_button( $buttons ) {
		array_push( $buttons, "|", "purvideojs" );
		echo('<div style="display:none"><input type="hidden" id="videojs-autoplay-default"/><input type="hidden" id="videojs-autoplay-default"/></div>');
		return $buttons;
	}

	/**
	 * Create video track
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function video_track( $atts, $content=null ){
		extract(shortcode_atts(array(
			'kind' => '',
			'src' => '',
			'srclang' => '',
			'label' => '',
			'default' => ''
		), $atts));

		if($kind)
			$kind = " kind='" . $kind . "'";

		if($src)
			$src = " src='" . $src . "'";

		if($srclang)
			$srclang = " srclang='" . $srclang . "'";

		if($label)
			$label = " label='" . $label . "'";

		if($default == "true" || $default == "default")
			$default = " default";
		else
			$default = "";

		$track = "<track" . $kind . $src . $srclang . $label . $default . " />";

		return $track;
	}

	/**
	 * Create video js short code
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 */
	public function video_shortcode( $atts, $content=null ) {
		extract(shortcode_atts(array(
			'mp4' => '',
			'webm' => '',
			'ogg' => '',
			'youtube' => '',
			'poster' => '',
			'width' => 512,
			'height' => 308,
			'preload' => true,
			'autoplay' => false,
			'loop' => '',
			'controls' => '',
			'id' => '',
			'class' => '',
			'muted' => ''
		), $atts));

		$dataSetup = array(
			'chromecast' => array(
				'appId' => 'testMac',
				'metadata'=> array(
					'title'
				)
			)
		);

		if ( $id == '' ) {
			$id = 'protect_video_id_' . rand();
		}

		if ( $mp4 ) {
			$folder = get_option( 'puv_customer_guid' );
			$name = basename($mp4);
			$file_name = urlencode("${folder}/${name}");
			$mp4_signed_video = $this->private_video->getSignedURL( 'https://d234bnnhz96rwd.cloudfront.net/' . $file_name, 5 );
			$mp4_source = '<source src="' . $mp4_signed_video . '" type=\'video/mp4\' />';
		} else {
			$mp4_source = '';
		}

		// Preload the video?
		$preload_attribute = ' preload="true"';

		// Autoplay the video?
		if ($autoplay == "true" || $autoplay == "on") {
			$autoplay_attribute = " autoplay";
		}
		else {
			$autoplay_attribute = "false";
		}

		// Tracks
		if(!is_null( $content )) {
			$track = do_shortcode($content);
		}
		else {
			$track = "";
		}

		if ($class) {
			$class = ' ' . $class;
		}

		if ($poster) {
			$poster_attribute = ' poster="'.$poster.'"';
		}
		else {
			$poster_attribute = '';
		}

		if ($controls == "false")
			$controls_attribute = "";
		else
			$controls_attribute = " controls";

		if ($loop == "true")
			$loop_attribute = " loop";
		else
			$loop_attribute = "";

		if ($muted == "true")
			$muted_attribute = " muted";
		else
			$muted_attribute = "";


		$jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

		//Output the <video> tag
		$videojs = <<<_end_

		<!-- Begin Video.js -->
		<video  oncontextmenu="return false" onselectstart="return false" ondragstart="return false" id="{$id}" class="video-js vjs-default-skin{$class}" width="{$width}" height="{$height}"{$poster_attribute}{$controls_attribute}{$preload_attribute}{$autoplay_attribute}{$loop_attribute}{$muted_attribute} data-setup='{$jsonDataSetup}'>
			{$mp4_source}
			{$track}
		</video>
		<!-- End Video.js -->
_end_;

		return $videojs;
	}

	/**
	 * Upload file to protected storage
	 */
	public function upload_to_storage() {

		$configs = include(plugin_dir_path(__DIR__) . '/includes/config.php');
		$mfl = get_option( 'puv_mfl' );

		if($mfl < $configs->mfl) {
			check_ajax_referer( 'pur_nonce', 'security' );

			try {
				$file_url = urldecode($_POST['file_url']);
				$result = $this->s3->upload_to_s3($file_url);
				update_option( 'puv_mfl', ++$mfl);
				wp_send_json_success($result);
			} catch (Exception $e) {
				wp_send_json_error($e->getMessage());
			}
		} else {
			wp_send_json_error(
				sprintf(__('You can only protect up to 3 video files on our free version. Please <a href="%s">upgrade to Pro version</a> to protect more files with bigger sizes.'),
					'mailto:hello@buildwps.com?subject=Protect%20WordPress%20Videos%20Pro%20version')
			);
		}

		exit;
	}

}
