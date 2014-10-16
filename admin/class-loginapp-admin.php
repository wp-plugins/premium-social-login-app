<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'Login_App_Admin' ) ) {

	class Login_App_Admin {

		/**
		 * Login_App_Admin calss instance
		 *
		 * @var string
		 */
		private static $instance;

		/**
		 * Get singleton object for class Login_App_Admin
		 *
		 * @return object Login_App_Admin
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Login_App_Admin ) ) {
				self::$instance = new Login_App_Admin();
			}

			return self::$instance;
		}

		/*
		 * Constructor for class Login_App_Admin
		 */

		public function __construct() {
			if ( ! class_exists( 'Admin_Helper_LA' ) ) {
				require_once "helpers/class-admin-helper.php";
			}
			// Registering hooks callback for admin section.
			$this->register_hook_callbacks();
		}

		/*
		 * Register admin hook callbacks
		 */

		public function register_hook_callbacks() {
			global $loginAppSettings;

			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_notices', array( $this, 'account_linking_info_on_profile_page' ) );
			// Filter for changing default WordPress avatar
			if ( isset( $loginAppSettings['LoginApp_socialavatar'] ) && ( $loginAppSettings['LoginApp_socialavatar'] == 'socialavatar' ) ) {
				add_filter( 'get_avatar', array( &$this, 'get_social_avatar' ), 10, 5 );
			}
		}

		/*
		 * Callback for admin_menu hook
		 */

		public function admin_menu() {

			$page = add_menu_page( 'LoginApp', '<b>LoginApp</b>', 'manage_options', 'LoginApp',
				'Login_App_Admin::options_page', LOGINAPP_PLUGIN_URL . 'assets/images/favicon.png' );
			add_action( 'admin_print_scripts-' . $page, array( $this, 'load_scripts' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'load_styles' ) );
		}

		/*
		 * Adding javascrip/Jquery for admin settings page
		 */

		public function load_scripts() {
			$scriptLocation = apply_filters( 'LoginApp_files_uri',
				LOGINAPP_PLUGIN_URL . 'assets/js/loginapp-options-page.js?t=6.0.1' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'LoginApp_options_page_script', $scriptLocation, array(), false, false );
			wp_enqueue_script( 'LoginApp_options_page_script2',
				LOGINAPP_PLUGIN_URL . 'assets/js/loginAppAdmin.js?t=6.0.1', array(), false, false );
		}

		/*
		 * adding style to plugin setting page
		 */

		public function load_styles() {
			?>
			<!--[if IE]>
			<link href="<?php echo LOGINAPP_PLUGIN_URL . 'assets/css/loginAppOptionsPageIE.css' ?>"
			      rel="stylesheet" type="text/css"/>
			<![endif]-->
			<?php
			$styleLocation = apply_filters( 'LoginApp_files_uri',
				LOGINAPP_PLUGIN_URL . 'assets/css/loginAppOptionsPage.css' );
			wp_enqueue_style( 'login_app_options_page_style', $styleLocation . '?t=4.0.0' );
			wp_enqueue_style( 'thickbox' );
		}

		/**
		 * Callback for admin_menu hook,
		 * Register LoginApp_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
		 */
		public function admin_init() {
			global $pagenow, $loginAppSettings;

			register_setting( 'LoginApp_setting_options', 'LoginApp_settings',
				array( $this, 'validate_options' ) );
			// add a callback public function to save any data a user enters in
			$this->meta_box_setup();
			// add a callback public function to save any data a user enters in
			add_action( 'save_post', array( &$this, 'save_meta' ) );

			if ( $pagenow == 'profile.php' && isset( $_REQUEST['token'] ) ) {
				Login_App_Common:: perform_linking_operation();
			}
			if ( ( isset( $loginAppSettings['LoginApp_noProvider'] ) && $loginAppSettings['LoginApp_noProvider'] == '1' ) || ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == '1' ) ) {
				add_filter( 'manage_users_columns', array( 'Admin_Helper_LA', 'add_provider_column_in_users_list' ) );
				add_action( 'manage_users_custom_column', array( 'Admin_Helper_LA', 'login_app_show_provider' ), 10,
					3 );
				if ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == '1' ) {
					add_filter( 'admin_head', array( 'Admin_Helper_LA', 'add_script_for_users_page' ) );
				}
			}
			// replicate Social Login configuration to the subblogs in the multisite network
			if ( is_multisite() && is_main_site() ) {
				add_action( 'wpmu_new_blog', array( $this, 'replicate_loginapp_settings_to_new_blog' ) );
				add_action( 'update_option_LoginApp_settings', array( $this, 'login_app_update_old_blogs' ) );
			}
		}

		// replicate the social login config to the new blog created in the multisite network
		public function replicate_loginapp_settings_to_new_blog( $blogId ) {
			global $loginAppSettings;
			add_blog_option( $blogId, 'LoginApp_settings', $loginAppSettings );
		}

		// update the social login options in all the old blogs
		public function login_app_update_old_blogs( $oldConfig ) {
			$newConfig = get_option( 'LoginApp_settings' );
			if ( isset( $newConfig['multisite_config'] ) && $newConfig['multisite_config'] == '1' ) {
				$blogs = wp_get_sites();
				foreach ( $blogs as $blog ) {
					update_blog_option( $blog['blog_id'], 'LoginApp_settings', $newConfig );
				}
			}
		}

		/*
		 * adding LoginApp meta box on each page and post
		 */

		public function meta_box_setup() {
			foreach ( array( 'post', 'page' ) as $type ) {
				add_meta_box( 'login_app_meta', 'LoginApp', array( $this, 'meta_setup' ), $type );
			}
		}

		/**
		 * Display  metabox information on page and post
		 */
		public function meta_setup() {
			global $post;
			$postType = $post->post_type;
			$lrMeta   = get_post_meta( $post->ID, '_login_app_meta', true );
			?>
			<p>
				<label for="login_app_sharing">
					<input type="checkbox" name="_login_app_meta[sharing]" id="login_app_sharing"
					       value='1' <?php checked( '1', @$lrMeta['sharing'] ); ?> />
					<?php _e( 'Disable Social Sharing on this ' . $postType, 'LoginApp' ) ?>
				</label>
			</p>
			<?php
			// custom nonce for verification later
			echo '<input type="hidden" name="login_app_meta_nonce" value="' . wp_create_nonce( __FILE__ ) . '" />';
		}


		/**
		 * Save login radius meta fields.
		 */
		public function save_meta( $postId ) {
			// make sure data came from our meta box
			if ( ! isset( $_POST['login_app_meta_nonce'] ) || ! wp_verify_nonce( $_POST['login_app_meta_nonce'],
					__FILE__ )
			) {
				return $postId;
			}
			// check user permissions
			if ( $_POST['post_type'] == 'page' ) {
				if ( ! current_user_can( 'edit_page', $postId ) ) {
					return $postId;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $postId ) ) {
					return $postId;
				}
			}
			if ( isset( $_POST['_login_app_meta'] ) ) {
				$newData = $_POST['_login_app_meta'];
			} else {
				$newData = 0;
			}
			update_post_meta( $postId, '_login_app_meta', $newData );

			return $postId;
		}

		/*
		 * Callback for add_menu_page,
		 * This is the first function which is called while plugin admin page is requested
		 */

		public static function options_page() {
			include_once "views/settings.php";
			Login_App_Admin_Settings:: render_options_page();
		}

		/**
		 * Replace default avatar with social avatar
		 */
		public function get_social_avatar( $avatar, $avuser, $size, $default, $alt = '' ) {
			$userId  = null;
			$default = null;
			if ( is_numeric( $avuser ) ) {
				if ( $avuser > 0 ) {
					$userId = $avuser;
				}
			} elseif ( is_object( $avuser ) ) {
				if ( property_exists( $avuser, 'user_id' ) && is_numeric( $avuser->user_id ) ) {
					$userId = $avuser->user_id;
				}
			}
			if ( ! empty( $userId ) ) {

				$currentSocialId = get_user_meta( $userId, 'loginapp_current_id', true );
				if ( ( $userAvatar = get_user_meta( $userId, 'loginapp_picture',
						true ) ) !== false && strlen( trim( $userAvatar ) ) > 0
				) {
					return '<img alt="' . esc_attr( $alt ) . '" src="' . $userAvatar . '" class="avatar avatar-' . $size . ' " height="' . $size . '" width="' . $size . '" />';
				} elseif ( ( $userAvatar = get_user_meta( $userId, 'loginapp_thumbnail',
						true ) ) !== false && strlen( trim( $userAvatar ) ) > 0
				) {
					return '<img alt="' . esc_attr( $alt ) . '" src="' . $userAvatar . '" class="avatar avatar-' . $size . ' " height="' . $size . '" width="' . $size . '" />';
				}
			}

			return $avatar;
		}

		/**
		 * Add a settings link to the Plugins page, so people can go straight from the plugin page to
		 * settings page.
		 */
		public function plugin_action_links( $links, $file ) {
			$settings_link = '<a href="admin.php?page=LoginApp">' . esc_html__( 'Settings', 'LoginApp' ) . '</a>';
			if ( $file == 'loginapp/LoginApp.php' ) {
				array_unshift( $links, $settings_link );
			}

			return $links;
		}

		/**
		 * Validate plugin options,
		 * Function to be called when settings save button is clicked on plugin settings page
		 */
		public static function validate_options( $loginAppSettings ) {
			require_once LOGINAPP_PLUGIN_DIR . 'admin/helpers/class-admin-helper.php';

			if ( empty( $loginAppSettings['LoginApp_apikey'] ) || empty( $loginAppSettings['LoginApp_secret'] ) ) {
				// If empty apikey or secret key, return settings
				return $loginAppSettings;
			} else {
				// if none of apikey and secret are empty
				$apiKey       = sanitize_text_field( $loginAppSettings['LoginApp_apikey'] );
				$apiSecret    = sanitize_text_field( $loginAppSettings['LoginApp_secret'] );
				$encodeString = Admin_Helper_LA:: get_encoded_settings_string( $loginAppSettings );

				if ( self:: api_validation_response( $apiKey, $apiSecret, $encodeString ) ) {
					// Validate settings and return settings to be saved
					$loginAppSettings['LoginApp_sendemail']         = ( ( isset( $loginAppSettings['LoginApp_sendemail'] ) && in_array( $loginAppSettings['LoginApp_sendemail'],
							array(
								'sendemail',
								'notsendemail'
							) ) ) ? $loginAppSettings['LoginApp_sendemail'] : 'sendemail' );
					$loginAppSettings['LoginApp_socialavatar']      = ( ( isset( $loginAppSettings['LoginApp_socialavatar'] ) && in_array( $loginAppSettings['LoginApp_socialavatar'],
							array(
								'socialavatar',
								'largeavatar',
								'defaultavatar'
							) ) ) ? $loginAppSettings['LoginApp_socialavatar'] : 'socialavatar' );
					$loginAppSettings['LoginApp_dummyemail']        = ( ( isset( $loginAppSettings['LoginApp_dummyemail'] ) && in_array( $loginAppSettings['LoginApp_dummyemail'],
							array(
								'notdummyemail',
								'dummyemail'
							) ) ) ? $loginAppSettings['LoginApp_dummyemail'] : 'notdummyemail' );
					$loginAppSettings['LoginApp_redirect']          = ( ( isset( $loginAppSettings['LoginApp_redirect'] ) && in_array( $loginAppSettings['LoginApp_redirect'],
							array(
								'samepage',
								'homepage',
								'dashboard',
								'bp',
								'custom'
							) ) ) ? $loginAppSettings['LoginApp_redirect'] : 'samepage' );
					$loginAppSettings['LoginApp_loutRedirect']      = ( ( isset( $loginAppSettings['LoginApp_loutRedirect'] ) && in_array( $loginAppSettings['LoginApp_loutRedirect'],
							array(
								'homepage',
								'custom'
							) ) ) ? $loginAppSettings['LoginApp_loutRedirect'] : 'homepage' );
					$loginAppSettings['LoginApp_loginformPosition'] = ( ( isset( $loginAppSettings['LoginApp_loginformPosition'] ) && in_array( $loginAppSettings['LoginApp_loginformPosition'],
							array(
								'embed',
								'beside'
							) ) ) ? $loginAppSettings['LoginApp_loginformPosition'] : 'embed' );
					$loginAppSettings['LoginApp_regformPosition']   = ( ( isset( $loginAppSettings['LoginApp_regformPosition'] ) && in_array( $loginAppSettings['LoginApp_regformPosition'],
							array(
								'embed',
								'beside'
							) ) ) ? $loginAppSettings['LoginApp_regformPosition'] : 'embed' );
					$loginAppSettings['LoginApp_commentform']       = ( ( isset( $loginAppSettings['LoginApp_commentform'] ) && in_array( $loginAppSettings['LoginApp_commentform'],
							array( 'old', 'new' ) ) ) ? $loginAppSettings['LoginApp_commentform'] : 'new' );
					$loginAppSettings['LoginApp_numColumns']        = is_numeric( $loginAppSettings['LoginApp_numColumns'] ) ? $loginAppSettings['LoginApp_numColumns'] : '';

					return $loginAppSettings;
				} else {
					// Api or Secret is not validat or something wrong happened while getting response from LoginApp api
					$message = 'please check your php.ini settings to enable CURL or FSOCKOPEN';
					global $currentErrorCode, $currentErrorResponse;
					$errorMessage = array(
						"API_KEY_NOT_VALID"       => 'LoginApp API key is invalid. Get your LoginApp API key from <a href="http://loginapp.io" target="_blank">LoginApp</a>',
						'API_SECRET_NOT_VALID'    => 'LoginApp API Secret is invalid. Get your LoginApp API Secret from <a href="http://loginapp.io" target="_blank">LoginApp</a>',
						'API_KEY_NOT_FORMATED'    => 'LoginApp API Key is not formatted correctly.',
						'API_SECRET_NOT_FORMATED' => 'LoginApp API Secret is not formatted correctly.',
					);
					if ( $currentErrorCode[0] == '0' ) {
						$message = $currentErrorResponse;
					} else {
						$message = $errorMessage[ $currentErrorCode[0] ];
					}
					$loginAppSettingsold = get_option( 'LoginApp_settings' );
					add_settings_error( 'LoginApp_settings', esc_attr( 'settings_updated' ), $message, 'error' );
					add_action( 'admin_notices', array( 'Admin_Helper_LA', 'display_admin_settings_errors' ) );

					return $loginAppSettingsold;
				}
			}
		}

		/**
		 * Get response from LoginApp api
		 */
		public static function api_validation_response( $apiKey, $apiSecret, $string ) {
			global $currentErrorCode, $currentErrorResponse;

			$url = LOGINAPP_VALIDATION_API_URL . '?apikey=' . rawurlencode( $apiKey ) . '&apisecret=' . rawurlencode( $apiSecret );

			$response = wp_remote_post( $url, array(
					'method'  => 'POST',
					'timeout' => 15,
					'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
					'body'    => array(
						'addon'         => 'WordPress',
						'version'       => LOGINAPP_SOCIALLOGIN_VERSION,
						'agentstring'   => $_SERVER['HTTP_USER_AGENT'],
						'clientip'      => $_SERVER['REMOTE_ADDR'],
						'configuration' => $string
					),
					'cookies' => array(),
				)
			);

			if ( is_wp_error( $response ) ) {
				$currentErrorCode     = '0';
				$currentErrorResponse = "Something went wrong: " . $response->get_error_message();

				return false;
			} else {
				if ( json_decode( $response['body'] )->Status ) {
					return true;
				} else {
					$currentErrorCode = json_decode( $response['body'] )->Messages;

					return false;
				}
			}
		}

		/**
		 * Displaying account linking on profile page
		 */
		public static function account_linking_info_on_profile_page() {
			global $pagenow;

			if ( $pagenow == 'profile.php' ) {
				echo Login_App_Common:: check_linking_status_parameters();
				// if remove button clicked
				if ( isset( $_GET['loginRadiusMap'] ) && ! empty( $_GET['loginRadiusMap'] ) && isset( $_GET['loginRadiusMappingProvider'] ) && ! empty( $_GET['loginRadiusMappingProvider'] ) ) {
					self:: unlink_provider();
				}
				Login_App_Common:: link_account_if_possible();
				?>
				<div class="metabox-holder columns-2" id="post-body">
					<div class="stuffbox" style="width:60%; padding-bottom:10px">
						<h3><label><?php _e( 'Link your account', 'LoginApp' ); ?></label></h3>

						<div class="inside" style='padding:0'>
							<table class="form-table editcomment">
								<tr>
									<td colspan="2"><?php _e( 'By adding another account, you can log in with the new account as well!',
											'LoginApp' ) ?></td>
								</tr>
								<tr>
									<td colspan="2">
										<?php
										Login_App_Common:: load_login_script();
										if ( ! class_exists( "LoginApp_Helper" ) ) {
											require_once LOGINAPP_PLUGIN_DIR . 'public/inc/login/class-login-helper.php';
										}
										LoginApp_Helper:: get_loginapp_interface_container();
										?>
									</td>
								</tr>
								<?php
								echo Login_App_Common:: get_connected_providers_list();
								//echo Login_App_Common:: display_currently_connected_provider();
								?>
							</table>
						</div>
					</div>
				</div>
			<?php
			}
		}

		public static function unlink_provider() {
			global $user_ID, $wpdb;
			$loginAppMapId       = trim( $_GET['loginRadiusMap'] );
			$loginAppMapProvider = trim( $_GET['loginRadiusMappingProvider'] );
			// remove account
			delete_user_meta( $user_ID, 'loginapp_provider_id', $loginAppMapId );
			if ( isset( $_GET['loginRadiusMain'] ) ) {
				delete_user_meta( $user_ID, 'loginapp_thumbnail' );
				delete_user_meta( $user_ID, 'loginapp_provider' );
			} else {
				delete_user_meta( $user_ID, 'loginapp_' . $loginAppMapId . '_thumbnail' );
				$wpdb->query( $wpdb->prepare( 'delete FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = \'loginapp_mapped_provider\' AND meta_value = %s limit 1',
					$user_ID, $loginAppMapProvider ) );
				delete_user_meta( $user_ID, 'loginapp_' . $loginAppMapProvider . '_id', $loginAppMapId );
			}
			?>
			<script type="text/javascript">
				location.href = "<?php echo Login_App_Common:: get_protocol(). $_SERVER['HTTP_HOST'] . remove_query_arg( array( 'lrlinked', 'loginradius_linking', 'loginradius_post', 'loginradius_invite', 'loginRadiusMappingProvider', 'loginRadiusMap', 'loginRadiusMain' )  ) ?>";
			</script>
			<?php
			die;
		}

	}

}

Login_App_Admin:: get_instance();
