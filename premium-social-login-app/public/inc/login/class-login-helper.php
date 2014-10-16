<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'LoginApp_Helper' ) ) {

	/**
	 * Helper class for Social Login functionality
	 */
	class LoginApp_Helper {

		/**
		 * Verify user when user clicks on verfification link or paste that link in browser
		 */
		public static function verify_user_after_email_confirmation() {
			global $wpdb, $loginAppSettings;
			$verificationKey = mysql_real_escape_string( trim( $_GET['loginRadiusVk'] ) );
			if ( isset( $_GET['loginRadiusProvider'] ) && trim( $_GET['loginRadiusProvider'] ) != '' ) {
				$provider = mysql_real_escape_string( trim( $_GET['loginRadiusProvider'] ) );
				$userId   = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key = '" . $provider . "LoginAppVkey' and meta_value = %s",
						$verificationKey ) );
				if ( ! empty( $userId ) ) {
					update_user_meta( $userId, $provider . 'LAVerified', '1' );
					delete_user_meta( $userId, $provider . 'LoginAppVkey', $verificationKey );
				} else {
					wp_redirect( site_url() );
					exit();
				}
			} else {
				$userId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key = 'loginapp_verification_key' and meta_value = %s",
						$verificationKey ) );
				if ( ! empty( $userId ) ) {
					update_user_meta( $userId, 'loginapp_isVerified', '1' );
					delete_user_meta( $userId, 'loginapp_verification_key', $verificationKey );
				} else {
					wp_redirect( site_url() );
					exit();
				}
			}
			// new user notification
			if ( isset( $loginAppSettings['LoginApp_sendemail'] ) && $loginAppSettings['LoginApp_sendemail'] == 'sendemail' ) {
				$userPassword = wp_generate_password();
				wp_update_user( array( 'ID' => $userId, 'user_pass' => $userPassword ) );
				wp_new_user_notification( $userId, $userPassword );
			} else {
				// notification to admin
				Login_App_Common::login_app_send_verification_email( trim( get_option( 'admin_email' ) ), '', '',
					'admin notification', $userId );
			}
			if ( get_user_meta( $userId, 'loginapp_status', true ) === '0' ) {
				self::login_app_notify( __( 'Your account is currently inactive. You will be notified through email, once Administrator activates your account.',
						'LoginApp' ), 'isAccountInactive' );
			} else {
				self::login_app_notify( __( 'Your email has been successfully verified. Now you can login into your account.',
						'LoginApp' ), 'isEmailVerified' );
			}
		}

		/**
		 * Function for displaying Front end notification
		 */
		public static function login_app_notify( $loginAppMsg, $noticeType = '', $redirection = '' ) {
			$key = mt_rand();
			update_user_meta( $key, 'loginapp_tmpKey', $loginAppMsg );
			if ( $redirection ) {
				update_user_meta( $key, 'loginapp_tmpRedirection', $redirection );
			}
			$queryString = '?' . $noticeType . '=1&loginRadiusKey=' . $key;
			wp_redirect( site_url() . $queryString );
			exit();
		}


		/**
		 * Check if user account associated with the ID passed is verified or not.
		 */
		public static function is_socialid_exists_in_wordpress( $socialId, $provider ) {
			global $wpdb;
			$userId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='" . $provider . "Lrid' AND meta_value = %s",
					$socialId ) );
			if ( ! empty( $userId ) ) {     // id exists
				return $userId;
			} else {                  // id doesn't exist
				return false;
			}
		}

		/**
		 * Provide loin to user, if user is inactive, then provide notification to user
		 */
		public static function login_user( $userId, $socialId, $firstLogin = false, $isRegister = false ) {
			if ( get_user_meta( $userId, 'loginapp_status', true ) === '0' ) {
				self::login_app_notify( __( 'Your account is currently inactive. You will be notified through email, once Administrator activates your account.',
						'LoginApp' ), 'isAccountInactive' );

				return;
			}
			// update user profile data if option is set
			if ( ! $firstLogin ) {
				global $loginAppSettings;
				if ( isset( $loginAppSettings['profileDataUpdate'] ) && $loginAppSettings['profileDataUpdate'] == '1' ) {
					self::update_profile_data( $userId );
				}
			}
			// set the current social login id
			update_user_meta( $userId, 'loginapp_current_id', $socialId );
			self::set_cookies( $userId );
			// WP login hook
			$_user = get_user_by( 'id', $userId );
			do_action( 'wp_login', $_user->user_login, $_user );
			self::login_app_redirect( $userId, $isRegister );
		}

		/**
		 * Register new WordPress user using data fetched from social networks.
		 */
		public static function register_user( $profileData, $loginAppPopup = false ) {
			global $loginAppSettings;

			$dummyEmail   = $loginAppSettings['LoginApp_dummyemail'];
			$userPassword = wp_generate_password();
			$bio          = $profileData['Bio'];
			$profileUrl   = $profileData['ProfileUrl'];
			$socialId     = $profileData['SocialId'];
			$thumbnail    = $profileData['Thumbnail'];
			if ( isset( $socialId ) && ! empty( $socialId ) ) {
				if ( ! empty( $profileData['Email'] ) ) {
					$email = $profileData['Email'];
				}
				// create username, firstname and lastname
				$usernameFirstnameLastname = explode( '|LR|', self::create_user_name( $profileData ) );
				$userName                  = $usernameFirstnameLastname[0];
				$firstName                 = $usernameFirstnameLastname[1];
				$lastName                  = $usernameFirstnameLastname[2];

				$role      = get_option( 'default_role' );
				$sendemail = $loginAppSettings['LoginApp_sendemail'];

				//look for user with username match
				$seperator = array(
					"dash"  => '-',
					"dot"   => '.',
					"space" => ' '
				);
				if ( isset( $loginAppSettings['username_separator'] ) ) {
					$userName = str_replace( ' ', $seperator[ $loginAppSettings['username_separator'] ], $userName );
				} else {
					$userName = str_replace( ' ', '-', $userName );
				}
				$username = self:: create_another_username_if_already_exists( $userName );
				$userdata = array(
					'user_login'    => $username,
					'user_pass'     => $userPassword,
					'user_nicename' => sanitize_title( $firstName ),
					'user_email'    => $email,
					'display_name'  => $firstName,
					'nickname'      => $firstName,
					'first_name'    => $firstName,
					'last_name'     => $lastName,
					'description'   => $bio,
					'user_url'      => $profileUrl,
					'role'          => $role,
				);
				$user_id  = wp_insert_user( $userdata );
				// check if error due to empty user_login
				if ( isset( $user_id->errors ) && isset( $user_id->errors['empty_user_login'] ) ) {
					$userdata['user_login'] = strtoupper( $profileData['Provider'] ) . $socialId;
					$user_id                = wp_insert_user( $userdata );
				}
				self::login_app_delete_temporary_data( $profileData );
				if ( ! is_wp_error( $user_id ) ) {
					if ( ! empty( $socialId ) ) {
						update_user_meta( $user_id, 'loginapp_provider_id', $socialId );
					}
					if ( ! empty( $thumbnail ) ) {
						update_user_meta( $user_id, 'loginapp_thumbnail', $thumbnail );
					}
					if ( ! empty( $profileData['PictureUrl'] ) ) {
						update_user_meta( $user_id, 'loginapp_picture', $profileData['PictureUrl'] );
					}
					if ( ! empty( $profileData['Provider'] ) ) {
						update_user_meta( $user_id, 'loginapp_provider', $profileData['Provider'] );
					}
					if ( $loginAppPopup ) {
						$loginAppKey = $user_id . time() . mt_rand();
						update_user_meta( $user_id, 'loginapp_verification_key', $loginAppKey );
						update_user_meta( $user_id, 'loginapp_isVerified', '0' );
						Login_App_Common::login_app_send_verification_email( $email, $loginAppKey );
						// set status
						if ( isset( $loginAppSettings['LoginApp_defaultUserStatus'] ) && $loginAppSettings['LoginApp_defaultUserStatus'] == '0' ) {
							update_user_meta( $user_id, 'loginapp_status', '0' );
						} else {
							update_user_meta( $user_id, 'loginapp_status', '1' );
						}
						self::login_app_notify( __( 'Confirmation link has been sent to your email address. Please verify your email by clicking on confirmation link.',
								'LoginApp' ), 'isConfirmationLinkSent' );

						return;
					}
					if ( ( $sendemail == 'sendemail' ) ) {
						if ( ( $dummyEmail == 'notdummyemail' ) && ( $loginAppPopup == true ) ) {

						} else {
							wp_new_user_notification( $user_id, $userPassword );
						}
					} else {
						// notification to admin
						Login_App_Common::login_app_send_verification_email( trim( get_option( 'admin_email' ) ), '',
							'', 'admin notification', $user_id );
					}
					// set status if option is enabled
					if ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == '1' ) {
						if ( isset( $loginAppSettings['LoginApp_defaultUserStatus'] ) && $loginAppSettings['LoginApp_defaultUserStatus'] == '0' ) {
							update_user_meta( $user_id, 'loginapp_status', '0' );
							self::login_app_notify( __( 'Your account is currently inactive. You will be notified through email, once Administrator activates your account.',
									'LoginApp' ), 'isAccountInactive' );
							exit();
						} else {
							update_user_meta( $user_id, 'loginapp_status', '1' );
						}
					}
					self::login_user( $user_id, $socialId, true, true );
				} else {
					self::login_app_redirect( $user_id );
				}
			}
		}

		/**
		 * Adding index to username if username already exists in WordPress
		 */
		public static function create_another_username_if_already_exists( $name ) {
			$isUserNameExists = true;
			$index            = 0;
			while ( $isUserNameExists == true ) {
				if ( username_exists( $name ) != 0 ) {
					$index ++;
					$name = $name . $index;
				} else {
					$isUserNameExists = false;
				}
			}

			return $name;
		}

		/**
		 * Update user profile data after login each time, if option for updating profile is selected
		 */
		public static function update_profile_data( $userId ) {
			// create username, firstname and lastname
			$usernameFirstnameLastname = explode( '|LR|',
				self::create_user_name( Social_Login_LA::$loginAppProfileData ) );
			$firstName                 = $usernameFirstnameLastname[1];
			$lastName                  = $usernameFirstnameLastname[2];
			// fields going to be updated
			$profileData = array(
				'ID'          => $userId,
				'first_name'  => $firstName,
				'last_name'   => $lastName,
				'description' => Social_Login_LA:: $loginAppProfileData['Bio'],
				'user_url'    => Social_Login_LA:: $loginAppProfileData['ProfileUrl'],
			);
			if ( wp_update_user( $profileData ) ) {
				update_user_meta( $userId, 'loginapp_thumbnail', Social_Login_LA:: $loginAppProfileData['Thumbnail'] );
				update_user_meta( $userId, 'loginapp_picture', Social_Login_LA:: $loginAppProfileData['PictureUrl'] );
			}
		}

		/**
		 * Generate a dummy email if auto generate email option is selected as plugin settings
		 */
		public static function generate_dummy_email( $profileData ) {
			$tempArray = array( 'twitter', 'linkedin', 'renren' );
			if ( in_array( $profileData['Provider'], $tempArray ) ) {
				return $profileData['SocialId'] . '@' . $profileData['Provider'] . '.com';
			} else {
				$email     = substr( $profileData['SocialId'], 7 );
				$tempEmail = str_replace( '/', '_', $email );

				return str_replace( '.', '_', $tempEmail ) . '@' . $profileData['Provider'] . '.com';
			}
		}

		/**
		 * Store temporary data in database before displaying email popup
		 */
		public static function login_app_store_temporary_data( $profileData ) {
			$tmpdata                   = array();
			$tmpdata['tmpsession']     = $profileData['UniqueId'];
			$tmpdata['tmpid']          = $profileData['SocialId'];
			$tmpdata['tmpFullName']    = $profileData['FullName'];
			$tmpdata['tmpProfileName'] = $profileData['ProfileName'];
			$tmpdata['tmpNickName']    = $profileData['NickName'];
			$tmpdata['tmpFname']       = $profileData['FirstName'];
			$tmpdata['tmpLname']       = $profileData['LastName'];
			$tmpdata['tmpProvider']    = $profileData['Provider'];
			$tmpdata['tmpthumbnail']   = $profileData['Thumbnail'];
			$tmpdata['tmpaboutme']     = $profileData['Bio'];
			$tmpdata['tmpwebsite']     = $profileData['ProfileUrl'];
			$tmpdata['tmpEmail']       = $profileData['Email'];
			$uni_id                    = $tmpdata['tmpsession'];
			$uniqu_id                  = explode( '.', $uni_id );
			$unique_id                 = $uniqu_id[1];
			if ( ! is_numeric( $unique_id ) ) {
				$unique_id = rand();
			}
			foreach ( $tmpdata as $key => $value ) {
				update_user_meta( $unique_id, $key, $value );
			}

			return $profileData['UniqueId'];
		}

		/**
		 * Delete temporary data, which was saved in case email was not provided by Social Network
		 */
		public static function login_app_delete_temporary_data( $profileData ) {
			$uni_id    = $profileData['UniqueId'];
			$uniqu_id  = explode( '.', $uni_id );
			$unique_id = $uniqu_id[1];
			$keys      = array(
				'tmpid',
				'tmpsession',
				'tmpEmail',
				'tmpFullName',
				'tmpProfileName',
				'tmpNickName',
				'tmpFname',
				'tmpLname',
				'tmpProvider',
				'tmpthumbnail',
				'tmpaboutme',
				'tmpwebsite'
			);
			foreach ( $keys as $key ) {
				delete_user_meta( $unique_id, $key );
			}
		}

		/**
		 * Redirect users after login and register according to plugin settings.
		 */
		public static function login_app_redirect( $user_id, $register = false ) {
			global $loginAppSettings, $loginAppLoginIsBpActive;
			if ( $register ) {
				$loginRedirect     = $loginAppSettings['LoginApp_regRedirect'];
				$customRedirectUrl = trim( $loginAppSettings['custom_regRedirect'] );
			} else {
				$loginRedirect     = $loginAppSettings['LoginApp_redirect'];
				$customRedirectUrl = trim( $loginAppSettings['custom_redirect'] );
			}
			$redirectionUrl  = site_url();
			$safeRedirection = false;
			if ( ! empty( $_GET['redirect_to'] ) ) {
				$redirectionUrl  = $_GET['redirect_to'];
				$safeRedirection = true;
			} else {
				if ( isset( $loginRedirect ) ) {
					switch ( strtolower( $loginRedirect ) ) {
						case 'homepage':
							$redirectionUrl = site_url() . '/';
							break;
						case 'dashboard':
							$redirectionUrl = admin_url();
							break;
						case 'bp':
							if ( $loginAppLoginIsBpActive ) {
								$redirectionUrl = bp_core_get_user_domain( $user_id );
							} else {
								$redirectionUrl = admin_url();
							}
							break;
						case 'custom':
							if ( isset( $loginRedirect ) && strlen( $customRedirectUrl ) > 0 ) {
								$redirectionUrl = trim( $customRedirectUrl );
								if ( strpos( $redirectionUrl, 'http' ) !== 0 ) {
									$redirectionUrl = Login_App_Common:: get_protocol() . $redirectionUrl;
								}

							} else {
								$redirectionUrl = site_url() . '/';
							}
							break;
						default:
						case 'samepage':
							$redirectionUrl = Login_App_Common:: get_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
							break;
					}
				}
			}

			wp_redirect( $redirectionUrl );
			exit();
		}

		/**
		 * Get callback parameter of the social login iframe.
		 */
		public static function get_callback_url_for_redirection( $http ) {
			$loc = urlencode( $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			if ( urldecode( $loc ) == wp_login_url() OR urldecode( $loc ) == site_url() . '/wp-login.php?action=register' OR urldecode( $loc ) == site_url() . '/wp-login.php?loggedout=true' ) {
				$loc = site_url() . '/';
			} elseif ( isset( $_GET['redirect_to'] ) && ( urldecode( $_GET['redirect_to'] ) == admin_url() ) ) {
				$loc = site_url() . '/';
			} elseif ( isset( $_GET['redirect_to'] ) ) {
				if ( self:: validate_url( urldecode( $_GET['redirect_to'] ) ) && ( strpos( urldecode( $_GET['redirect_to'] ),
							'http://' ) !== false || strpos( urldecode( $_GET['redirect_to'] ), 'https://' ) !== false )
				) {
					$loc = $_GET['redirect_to'];
				} else {
					$loc = site_url() . '/';
				}
			} else {
				$loc = urlencode( $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			}

			return $loc;
		}

		/**
		 * Create username, firstname and lastname with profile fetched from Social Networks
		 */
		public static function create_user_name( $profileData ) {
			$username  = '';
			$firstName = '';
			$lastName  = '';
			if ( ! empty( $profileData['FirstName'] ) && ! empty( $profileData['LastName'] ) ) {
				$username  = $profileData['FirstName'] . ' ' . $profileData['LastName'];
				$firstName = $profileData['FirstName'];
				$lastName  = $profileData['LastName'];
			} elseif ( ! empty( $profileData['FullName'] ) ) {
				$username  = $profileData['FullName'];
				$firstName = $profileData['FullName'];
			} elseif ( ! empty( $profileData['ProfileName'] ) ) {
				$username  = $profileData['ProfileName'];
				$firstName = $profileData['ProfileName'];
			} elseif ( ! empty( $profileData['NickName'] ) ) {
				$username  = $profileData['NickName'];
				$firstName = $profileData['NickName'];
			} elseif ( ! empty( $profileData['Email'] ) ) {
				$user_name = explode( '@', $profileData['Email'] );
				$username  = $user_name[0];
				$firstName = str_replace( '_', ' ', $user_name[0] );
			} else {
				$username  = $profileData['SocialId'];
				$firstName = $profileData['SocialId'];
			}

			return $username . '|LR|' . $firstName . '|LR|' . $lastName;
		}

		/**
		 * Set cookies to provide login to user.
		 */
		public static function set_cookies( $userId = 0, $remember = true ) {
			wp_clear_auth_cookie();
			wp_set_auth_cookie( $userId, $remember );
			wp_set_current_user( $userId );

			return true;
		}

		/**
		 * Filter the data fetched from LoginApp.
		 */
		public static function filter_loginapp_data_for_wordpress_use( $userProfileObject ) {

			$profileData['SocialId']    = ( ! empty( $userProfileObject->ID ) ? $userProfileObject->ID : '' );
			$profileData['UniqueId']    = uniqid( 'LoginApp_', true );
			$profileData['Email']       = isset( $userProfileObject->Email[0]->Value ) ? $userProfileObject->Email[0]->Value : '';
			$profileData['FullName']    = ( ! empty( $userProfileObject->FullName ) ? $userProfileObject->FullName : '' );
			$profileData['ProfileName'] = ( ! empty( $userProfileObject->ProfileName ) ? $userProfileObject->ProfileName : '' );
			$profileData['NickName']    = ( ! empty( $userProfileObject->NickName ) ? $userProfileObject->NickName : '' );
			$profileData['FirstName']   = ( ! empty( $userProfileObject->FirstName ) ? $userProfileObject->FirstName : '' );
			$profileData['LastName']    = ( ! empty( $userProfileObject->LastName ) ? $userProfileObject->LastName : '' );
			$profileData['Provider']    = ( ! empty( $userProfileObject->Provider ) ? $userProfileObject->Provider : '' );
			$profileData['Thumbnail']   = ( ! empty( $userProfileObject->ThumbnailImageUrl ) ? trim( $userProfileObject->ThumbnailImageUrl ) : '' );
			$profileData['PictureUrl']  = ( ! empty( $userProfileObject->ImageUrl ) ? trim( $userProfileObject->ImageUrl ) : '' );
			if ( empty( $profileData['Thumbnail'] ) && $profileData['Provider'] == 'facebook' ) {
				$profileData['Thumbnail'] = self:: facebook_profile_pic_creation( $profileData['SocialId'] );
			}
			$profileData['Bio']        = ( ! empty( $userProfileObject->About ) ? $userProfileObject->About : '' );
			$profileData['ProfileUrl'] = ( ! empty( $userProfileObject->ProfileUrl ) ? $userProfileObject->ProfileUrl : '' );

			return $profileData;
		}

		/**
		 * create facebook profile pic link for using as avatar
		 */
		public static function facebook_profile_pic_creation( $socialId ) {
			$fbThumbnail = 'http://graph.facebook.com/' . $socialId . '/picture?type=square';

			return $fbThumbnail;
		}

		/**
		 * Validate url.
		 */
		public static function validate_url( $url ) {
			$validUrlExpression = '/^ ( http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/| ) ?[a-z0-9_\-]+[a-z0-9_\-\.]+\.[a-z]{2,4} ( \/+[a-z0-9_\.\-\/]* ) ?$/i';

			return ( bool ) preg_match( $validUrlExpression, $url );
		}

		/**
		 * Display Social Login interface.
		 */
		public static function display_social_login_interface( $newInterface = false ) {
			global $loginAppSettings;
			$title = isset( $loginAppSettings['LoginApp_title'] ) ? $loginAppSettings['LoginApp_title'] : '';
			if ( ! is_user_logged_in() ) {
				if ( $newInterface ) {
					$result = "<div style='margin-bottom: 3px;'>";
					if ( trim( $loginAppSettings['LoginApp_apikey'] ) != '' && trim( $loginAppSettings['LoginApp_secret'] ) != '' ) {
						$result .= '<label>' . $title . '</label>';
					}
					$result .= '</div>' . self:: get_loginapp_interface_container( $newInterface );

					return $result;
				} else {
					?>
					<div>
						<div
							style='margin-bottom: 3px;'><?php if ( trim( $loginAppSettings['LoginApp_apikey'] ) != '' && trim( $loginAppSettings['LoginApp_secret'] ) != '' ) { ?>
								<label><?php _e( $title, 'LoginApp' ) ?></label><?php } ?></div>
						<?php
						self::get_loginapp_interface_container( $newInterface );
						?>
					</div>
				<?php
				}
			}
		}

		/**
		 * Add container for Social Login Interface
		 */
		public static function get_loginapp_interface_container( $isLinkingWidget = false ) {
			global $loginAppSettings, $loginAppObject;
			$loginAppApiKey = isset( $loginAppSettings['LoginApp_apikey'] ) ? trim( $loginAppSettings['LoginApp_apikey'] ) : '';
			$loginAppSecret = isset( $loginAppSettings['LoginApp_secret'] ) ? trim( $loginAppSettings['LoginApp_secret'] ) : '';
			$loginAppError  = "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><p style ='color:red;'>Your LoginApp API key or secret is not valid, please correct it or contact LoginApp support at <b><a href ='http://loginapp.io' target = '_blank'>LoginApp.io</a></b></p></div>";
			if ( empty( $loginAppSecret ) ) {
				$loginAppResult = '';
			} elseif ( ! $loginAppObject->validate_key( $loginAppApiKey ) || ! $loginAppObject->validate_key( $loginAppSecret ) ) {
				$loginAppResult = $loginAppError;
			} else {
				$loginAppResult = "<div class='interfacecontainerdiv'></div>";
			}
			// return/print interface HTML
			if ( ! $isLinkingWidget ) {
				echo $loginAppResult;
			} else {
				return $loginAppResult;
			}
		}

		/**
		 * Delete the field holding current provider information.
		 */
		public static function delete_social_login_meta() {
			global $user_ID;
			delete_user_meta( $user_ID, 'loginapp_current_id' );
		}

		/*
		 * Function is called if buddypress is active, it sets
		 * LoginApp global variable $loginAppLoginIsBpActive to true
		 */

		public static function set_budddy_press_status_variable() {
			global $loginAppLoginIsBpActive;
			$loginAppLoginIsBpActive = true;
		}

		/*
		 * Function is called when email popup is submitted.
		 * Takes appropriate action for submit and cancel button
		 */

		public static function response_to_popup_submission() {
			global $wpdb, $loginAppSettings;

			if ( $_POST['LoginApp_popupSubmit'] == 'Submit' ) {
				// If submit button is clicked.
				$loginAppEmail           = sanitize_email( $_POST['email'] );
				$profileData             = array();
				$loginAppTempUserId      = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key=\'tmpsession\' AND meta_value = %s',
						$_POST['session'] ) );
				$profileData['UniqueId'] = get_user_meta( $loginAppTempUserId, 'tmpsession', true );
				$loginAppProvider        = get_user_meta( $loginAppTempUserId, 'tmpProvider', true );

				if ( isset( $profileData['UniqueId'] ) && isset( $_POST['session'] ) && $profileData['UniqueId'] == $_POST['session'] ) {
					if ( $loginAppUserId = email_exists( $loginAppEmail ) ) {

						if ( get_user_meta( $loginAppUserId, 'loginapp_provider', true ) == $loginAppProvider ) {

							$directorySeparator = DIRECTORY_SEPARATOR;
							require_once( getcwd() . $directorySeparator . 'wp-admin' . $directorySeparator . 'inc' . $directorySeparator . 'user.php' );
							wp_delete_user( $loginAppUserId );
							// New user.
							$profileData = self:: fetch_temp_data_from_usermeta( $loginAppTempUserId );

							//If registration is disabled from WordPress settings, redirect him to login page.
							if ( ! get_option( 'users_can_register' ) ) {
								wp_redirect( site_url( '/wp-login.php?registration=disabled' ) );
								exit();
							}
							self::register_user( $profileData, true );

							return;
						} else {
							// email is already registered!
							$queryString = '?lrid=' . $profileData['UniqueId'];
							wp_redirect( site_url() . $queryString . '&LoginRadiusMessage="emailExists"' );
							exit();
						}
					} else {
						// New user.
						$profileData = self:: fetch_temp_data_from_usermeta( $loginAppTempUserId );

						self::register_user( $profileData, true );
					}
				}
			} else {
				self::login_app_delete_temporary_data( array( 'UniqueId' => trim( $_POST['session'] ) ) );
				wp_redirect( site_url() );
				exit();
			}
		}

		/*
		 * Fetch temporary data, which was saved in case email was not provided by Social Network
		 */

		public static function fetch_temp_data_from_usermeta( $loginAppTempUserId ) {
			$profileData['UniqueId']    = get_user_meta( $loginAppTempUserId, 'tmpsession', true );
			$profileData['SocialId']    = get_user_meta( $loginAppTempUserId, 'tmpid', true );
			$profileData['FullName']    = get_user_meta( $loginAppTempUserId, 'tmpFullName', true );
			$profileData['ProfileName'] = get_user_meta( $loginAppTempUserId, 'tmpProfileName', true );
			$profileData['NickName']    = get_user_meta( $loginAppTempUserId, 'tmpNickName', true );
			$profileData['FirstName']   = get_user_meta( $loginAppTempUserId, 'tmpFname', true );
			$profileData['LastName']    = get_user_meta( $loginAppTempUserId, 'tmpLname', true );
			$profileData['Provider']    = get_user_meta( $loginAppTempUserId, 'tmpProvider', true );
			$profileData['Thumbnail']   = get_user_meta( $loginAppTempUserId, 'tmpthumbnail', true );
			$profileData['Bio']         = get_user_meta( $loginAppTempUserId, 'tmpaboutme', true );
			$profileData['ProfileUrl']  = get_user_meta( $loginAppTempUserId, 'tmpwebsite', true );
			$profileData['Email']       = sanitize_email( $_POST['email'] );

			return $profileData;
		}

		/**
		 * This function renders SOcial Login Interface on WordPress registration page
		 */
		public static function social_login_interface_beside_registration() {
			global $loginAppSettings;
			$lrLogin    = ( $loginAppSettings['LoginApp_loginform'] == 1 ) && ( $loginAppSettings['LoginApp_loginformPosition'] == 'beside' );
			$lrRegister = ( $loginAppSettings['LoginApp_regform'] == 1 ) && ( $loginAppSettings['LoginApp_regformPosition'] == 'beside' );
			$script     = '<script type="text/javascript">';
			$script .= 'jQuery ( document ) .ready ( function(){ ' .
			           'var loginDiv = jQuery ( "div#login" );';
			if ( $lrLogin && $lrRegister ) {
				$script .= 'if ( jQuery ( "#loginform" ) .length || jQuery ( "#registerform" ) .length || jQuery ( "#lostpasswordform" ) .length )
                        {
                            jQuery ( "#loginform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "#lostpasswordform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "#registerform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "div#login" ) .css ( \'width\', \'910px\' );
                            loginDiv.append ( "<div class=\"login-sep-text float-left\"><h3>OR</h3></div>" );

                            if ( jQuery ( "#registerform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                            } else if ( jQuery ( "#lostpasswordform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#lostpasswordform" ) .css ( "height", "178px" );
                            } else if ( jQuery ( "#loginform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#loginform" ) .css ( "height", "178px" );
                            }
                        }';
			} elseif ( $lrLogin ) {
				$script .= 'if ( jQuery ( "#loginform" ) .length || jQuery ( "#lostpasswordform" ) .length ) {
                            jQuery ( "#loginform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "#lostpasswordform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "div#login" ) .css ( \'width\', \'910px\' );
                            loginDiv.append ( "<div class=\"login-sep-text float-left\"><h3>OR</h3></div>" );

                            if ( jQuery ( "#lostpasswordform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#lostpasswordform" ) .css ( "height", "178px" );
                            } else if ( jQuery ( "#loginform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#loginform" ) .css ( "height", "178px" );
                            }
                        }';
			} elseif ( $lrRegister ) {
				$script .= 'if ( jQuery ( "#registerform" ) .length || jQuery ( "#lostpasswordform" ) .length ) {
                            jQuery ( "#registerform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "#lostpasswordform" ) .wrap ( "<div style=\'float:left; width:400px\'></div>" ) .after ( jQuery ( "#nav" )  ) .after ( jQuery ( "#backtoblog" )  );
                            jQuery ( "div#login" ) .css ( \'width\', \'910px\' );
                            loginDiv.append ( "<div class=\"login-sep-text float-left\"><h3>OR</h3></div>" );

                            if ( jQuery ( "#lostpasswordform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#lostpasswordform" ) .css ( "height", "178px" );
                            } else if ( jQuery ( "#registerform" ) .length ) {
                                loginDiv.append ( "<div class=\"login-panel-lr\" style=\"min-height:178px\" >' . self:: display_social_login_interface( true ) . '</div>" );
                                jQuery ( "#loginform" ) .css ( "height", "178px" );
                            }
                        }';
			}

			$script .= ' } ); </script>';
			echo $script;
		}

	}

}