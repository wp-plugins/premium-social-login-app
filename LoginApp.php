<?php

/**
 * Plugin Name: Premium social login app
 * Plugin URI: http://loginapp.io
 * Description: Add Social Login to your WordPress website and also get accurate User Profile Data and Social Analytics.
 * Version: 1.0
 * Author: LoginApp Team
 * Author URI: http://loginapp.io
 * License: GPL2+
 */
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


if ( ! class_exists( 'Login_App' ) ) {

	/**
	 * The main class and initialization point of the plugin.
	 */
	class Login_App {

		/**
		 * Login_App calss instance
		 *
		 * @var string
		 */
		private static $instance;


		/**
		 * Mininmum required version of WordPress for this plug-in to function correctly.
		 *
		 * @var string
		 */
		public static $wp_min_version = "3.4";

		/**
		 * Get singleton object for class Login_App
		 *
		 * @return object Login_App
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Login_App ) ) {
				self::$instance = new Login_App();
			}

			return self::$instance;
		}

		/**
		 * Construct and start plug-in's other functionalities
		 */
		public function __construct() {

			if ( ! $this->is_requirements_met() ) {
				//Return if requirements are not met.
				return;
			}

			//Declare constants and load dependencies
			$this->define_constants();
			$this->load_dependencies();

			//Load Language translation files.
			add_filter( 'init', array( $this, 'localization' ) );
		}

		/**
		 * Checks that the WordPress setup meets the plugin requirements
		 *
		 * @global string $wp_version
		 *
		 * @return boolean
		 */
		private function is_requirements_met() {
			global $wp_version;

			if ( ! version_compare( $wp_version, self:: $wp_min_version, '>=' ) ) {
				add_action( 'admin_notices', array( $this, 'notify_admin' ) );

				return false;
			}

			return true;
		}

		/**
		 * Display admin notice if requirements are not made
		 */
		public static function notify_admin() {
			echo '<div id="message" class="error"><p><strong>';
			echo __( 'Sorry, LoginApp Social Login requires WordPress ' . self:: $wp_min_version . ' or higher.Please upgrade your WordPress setup',
				'LoginApp' );
			echo '</strong></p></div>';
		}

		/**
		 * Define constants needed across the plug-in.
		 */
		private function define_constants() {

			define( 'LOGINAPP_SOCIALLOGIN_VERSION', '1.0' );
			define( 'LOGINAPP_MIN_WP_VERSION', '3.4' );
			define( 'LOGINAPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'LOGINAPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			define( 'LOGINAPP_VALIDATION_API_URL', 'https://api.loginradius.com/api/v2/app/validate' );
		}

		/**
		 * Loads PHP files that required by the plug-in
		 *
		 * @global loginAppSettings , loginAppObject
		 */
		private function load_dependencies() {
			global $loginAppSettings, $loginAppObject;

			//Load required files.
			require_once( 'lib/LoginAppSDK.php' );
			require_once( 'common/class-loginapp-common.php' );
			require_once( 'common/loginapp-ajax.php' );
			require_once( 'widgets/loginapp-social-login-widget.php' );
			require_once( 'widgets/loginapp-social-linking-widget.php' );
			require_once( 'widgets/loginapp-horizontal-share-widget.php' );
			require_once( 'widgets/loginapp-vertical-share-widget.php' );

			// Get objetc for LoginApp Sdk
			$loginAppObject = new Login_App_SDK();

			// Get LoginApp plugin options
			$loginAppSettings = get_option( 'LoginApp_settings' );

			// Admin Panel
			if ( is_admin() ) {
				// load admin functionality
				require_once( 'admin/class-loginapp-admin.php' );
			}
			// Front-End
			if ( ! is_admin() ) {
				// Load public functionality
				require_once( 'public/class-loginapp-front.php' );
			}
		}

		/**
		 * Function for setting default options while plgin is activating.
		 */
		public static function install() {
			global $wpdb;
			require_once( dirname( __FILE__ ) . '/install.php' );
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				// check if it is a network activation - if so, run the activation function for each blog id
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					Login_App_Install:: set_default_options();
				}
				switch_to_blog( $old_blog );

				return;
			} else {
				Login_App_Install:: set_default_options();
			}
		}

		/**
		 * Load the plugin's translated scripts.
		 */
		public function localization() {

			//load_plugin_textdomain( 'LoginApp', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

	}

}
// Register Activation hook callback.
register_activation_hook( __FILE__, array( 'Login_App', 'install' ) );

// return object so that other plugins can use it as global.
$GLOBALS['loginradius'] = Login_App::get_instance();
