<?php
// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'Social_Login_LA' ) ) {
    /**
     * Main class for providing Social Login functionality
     */
    class Social_Login_LA {

        private static $instance = null;
        public static $loginAppProfileData;

        /**
         * Get singleton object for class Social_Login_LA
         *
         * @return object Social_Login_LA
         */
        public static function get_instance() {

            if ( !isset( self::$instance ) && !( self::$instance instanceof Social_Login_LA ) ) {
                self::$instance = new Social_Login_LA();
            }
            return self::$instance;
        }

        /**
         * Constructor for class Social_Login_LA
         */
        public function __construct() {
            require_once( "class-login-helper.php" );

            $this->register_hook_callbacks();
        }

        /**
         * regeister callbacks for required hooks for social login
         */
        public function register_hook_callbacks() {
            global $loginAppSettings;

            add_action( 'init', array($this, 'social_login_init') );

            // Display Social Login Interface with wp_login form
            if ( isset( $loginAppSettings['LoginApp_loginform'] ) && $loginAppSettings['LoginApp_loginform'] == '1' && isset( $loginAppSettings['LoginApp_loginformPosition'] ) && $loginAppSettings['LoginApp_loginformPosition'] == 'embed' ) {
                add_action( 'login_form', array('LoginApp_Helper', 'display_social_login_interface') );
                add_action( 'bp_before_sidebar_login_form', array('LoginApp_Helper', 'display_social_login_interface') );
            }

            // display Social Login interface on register form in embed mode
            if ( isset( $loginAppSettings['LoginApp_regform'] ) && $loginAppSettings['LoginApp_regform'] == '1' && isset( $loginAppSettings['LoginApp_regformPosition'] ) && $loginAppSettings['LoginApp_regformPosition'] == 'embed' ) {
                add_action( 'register_form', array('LoginApp_Helper', 'display_social_login_interface') );
                add_action( 'after_signup_form', array('LoginApp_Helper', 'display_social_login_interface') );
                add_action( 'bp_before_account_details_fields', array('LoginApp_Helper', 'display_social_login_interface') );
            }

            // Filter for changing default WordPress avatar
            if ( isset( $loginAppSettings['LoginApp_socialavatar'] ) && ( $loginAppSettings['LoginApp_socialavatar'] == 'socialavatar' ) ) {
                add_filter( 'get_avatar', array(&$this, 'replace_default_avatar_with_social_avatar'), 10, 5 );
            }
            //Filter for changing buddypress avatar.
            if ( isset( $loginAppSettings['LoginApp_socialavatar'] ) && $loginAppSettings['LoginApp_socialavatar'] == 'socialavatar' ) {
                add_filter( 'bp_core_fetch_avatar', array(&$this, 'change_buddypress_avatar'), 10, 2 );
            }
            add_action( 'bp_include', array('LoginApp_Helper', 'set_budddy_press_status_variable') );

            // show Social Login interface before buddypress login form and register form
            if ( ( isset( $loginAppSettings['LoginApp_regform'] ) && $loginAppSettings['LoginApp_regform'] == '1' && isset( $loginAppSettings['LoginApp_regformPosition'] ) && $loginAppSettings['LoginApp_regformPosition'] == 'beside' ) ) {
                add_action( 'login_head', array('LoginApp_Helper', 'social_login_interface_beside_registration') );
                if ( $loginAppSettings['LoginApp_loginformPosition'] == 'beside' ) {
                    add_action( 'bp_before_sidebar_login_form', array('LoginApp_Helper', 'display_social_login_interface') );
                }
                if ( $loginAppSettings['LoginApp_regformPosition'] == 'beside' ) {
                    add_action( 'bp_before_account_details_fields', array('LoginApp_Helper', 'display_social_login_interface') );
                }
            }


            add_filter( 'authenticate', array($this, 'Stop_disabled_user_registration'), 40, 2 );
            add_filter( 'login_errors', array($this, 'error_message_for_inactive_user') );
            add_action( 'clear_auth_cookie', array('LoginApp_Helper', 'delete_social_login_meta') );
        }

        /**
         * callback for init hook, it loads plugin script on front
         */
        public function social_login_init() {

            if ( get_option( 'loginapp_version' ) != LOGINAPP_SOCIALLOGIN_VERSION ) {
                $this->update_plugin_meta_if_old_verison();
            }
            if ( Login_App_Common:: scripts_in_footer_enabled() ) {
                add_action( 'wp_footer', array($this, 'front_end_scripts') );
                add_action( 'login_footer', array($this, 'front_end_scripts') );
            } else {
                add_action( 'wp_enqueue_scripts', array($this, 'front_end_scripts') );
            }
            add_action( 'parse_request', array($this, 'login_app_connect') );
            add_action( 'wp_enqueue_scripts', array($this, 'front_end_css') );
            add_filter( 'LR_logout_url', array($this, 'log_out_url'), 20, 2 );
            add_action( 'login_head', 'wp_enqueue_scripts', 1 );
        }

        /**
         * This function is called when token is returned from LoginApp.
         * it checks for query string variable and fetches data using LoginApp api.
         * After fetching data, appropriate action is taken on the basis of LoginApp plugin settings
         */
        public static function login_app_connect() {
            global $wpdb, $loginAppSettings, $loginAppObject;

            if ( isset( $_GET['loginradius_linking'] ) && isset( $_REQUEST['token'] ) ) {
                Login_App_Common:: perform_linking_operation();
            }

            if ( isset( $_GET['loginRadiusVk'] ) && trim( $_GET['loginRadiusVk'] ) != '' ) {
                //If verification link is clicked
                LoginApp_Helper::verify_user_after_email_confirmation();
            }

            if ( isset( $_POST['LoginApp_popupSubmit'] ) ) {
                //If "email required" popup has been submitted
                Login_helper:: response_to_popup_submission();
            }

            if ( !is_user_logged_in() && isset( $_REQUEST['token'] ) ) {
                //Is request token is set
                $loginAppSecret = isset( $loginAppSettings['LoginApp_secret'] ) ? $loginAppSettings['LoginApp_secret'] : '';

                // Fetch user profile using access token ......
                $responseFromLoginApp = $loginAppObject->get_user_profiledata( $_REQUEST['token'] );

                if ( isset( $responseFromLoginApp->ID ) && $responseFromLoginApp->ID != null ) {
                    // If profile data is retrieved successfully
                    self::$loginAppProfileData = LoginApp_Helper::filter_loginapp_data_for_wordpress_use( $responseFromLoginApp );
                } else if ( $loginAppSettings['enable_degugging'] == '0' ) {
                    // if debugging is off and Social profile not recieved, redirect to home page.
                    wp_redirect( site_url() );
                    exit();
                } else {
                    $message = isset( $responseFromLoginApp->description ) ? $responseFromLoginApp->description : $responseFromLoginApp;
                    // If debug option is set and Social Profile not retrieved
                    LoginApp_Helper:: login_app_notify( $message, 'isProfileNotRetrieved' );
                    return;
                }
                $userId = LoginApp_Helper::is_socialid_exists_in_wordpress( self::$loginAppProfileData['SocialId'], self::$loginAppProfileData['Provider'] );
                if ( $userId ) {
                    //if Social id exists in wordpress database
                    if ( 1 == get_user_meta( $userId, self::$loginAppProfileData['Provider'] . 'LAVerified', true ) ) {
                        // if user is verified, provide login.
                        LoginApp_Helper::login_user( $userId, self::$loginAppProfileData['SocialId'] );
                    } else {
                        // If not verified then display pop up.
                        LoginApp_Helper:: login_app_notify( __( 'Please verify your email by clicking the confirmation link sent to you.', 'LoginApp' ), 'isEmailNotVerified' );
                        return;
                    }
                }
                // check if id already exists.
                $loginAppUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='loginapp_provider_id' AND meta_value = %s", self::$loginAppProfileData['SocialId'] ) );
                if ( !empty( $loginAppUserId ) ) {
                    // id exists
                    $tempUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginapp_isVerified'", $loginAppUserId ) );
                    if ( !empty( $tempUserId ) ) {
                        // check if verification field exists.
                        $isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginapp_isVerified'", $loginAppUserId ) );
                        if ( $isVerified == '1' ) {                             // if email is verified
                            LoginApp_Helper::login_user( $loginAppUserId, self::$loginAppProfileData['SocialId'] );
                            return;
                        } else {
                            LoginApp_Helper::login_app_notify( __( 'Please verify your email by clicking the confirmation link sent to you.', 'LoginApp' ), 'isEmailNotVerified' );
                            return;
                        }
                    } else {
                        LoginApp_Helper::login_user( $loginAppUserId, self::$loginAppProfileData['SocialId'] );
                        return;
                    }
                } else {
                    //If registration is disabled from WordPress settings, redirect him to login page.
                    if ( !get_option( 'users_can_register' ) ) {
                        wp_redirect( site_url( '/wp-login.php?registration=disabled' ) );
                        exit();
                    }

                    if ( empty( self::$loginAppProfileData['Email'] ) ) {
                        // email is empty for social profile data
                        $dummyEmail = isset( $loginAppSettings['LoginApp_dummyemail'] ) ? $loginAppSettings['LoginApp_dummyemail'] : '';
                        if ( $dummyEmail == 'dummyemail' ) {
                            // email not required according to plugin settings
                            self::$loginAppProfileData['Email'] = LoginApp_Helper:: generate_dummy_email( self::$loginAppProfileData );
                            LoginApp_Helper::register_user( self::$loginAppProfileData );
                            return;
                        } else {
                            // email required according to plugin settings
                            $lrUniqueId = LoginApp_Helper::login_app_store_temporary_data( self::$loginAppProfileData );
                            $queryString = '?lrid=' . $lrUniqueId;
                            wp_redirect( site_url() . $queryString );
                            exit();
                        }
                    } else {
                        // email is not empty
                        $userObject = get_user_by( 'email', self::$loginAppProfileData['Email'] );
                        $loginAppUserId = is_object( $userObject ) ? $userObject->ID : '';
                        if ( !empty( $loginAppUserId ) ) {        // email exists
                            $isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginapp_isVerified'", $loginAppUserId ) );
                            if ( !empty( $isVerified ) ) {
                                if ( $isVerified == '1' ) {
                                    // social linking
                                    Login_App_Common::link_account( $loginAppUserId, self::$loginAppProfileData['SocialId'], self::$loginAppProfileData['Provider'], self::$loginAppProfileData['Thumbnail'], self::$loginAppProfileData['Provider'], '' );
                                    // Login user
                                    LoginApp_Helper::login_user( $loginAppUserId, self::$loginAppProfileData['SocialId'] );
                                    return;
                                } else {
                                    $directorySeparator = DIRECTORY_SEPARATOR;
                                    require_once( getcwd() . $directorySeparator . 'wp-admin' . $directorySeparator . 'inc' . $directorySeparator . 'user.php' );
                                    wp_delete_user( $loginAppUserId );
                                    LoginApp_Helper::register_user( self::$loginAppProfileData );
                                }
                            } else {
                                if ( get_user_meta( $loginAppUserId, 'loginapp_provider_id', true ) != false ) {
                                    // social linking
                                    Login_App_Common:: link_account( $loginAppUserId, self::$loginAppProfileData['SocialId'], self::$loginAppProfileData['Provider'], self::$loginAppProfileData['Thumbnail'], self::$loginAppProfileData['Provider'], '' );
                                } else {
                                    // traditional account
                                    // social linking
                                    if ( isset( $loginAppSettings['LoginApp_socialLinking'] ) && ( $loginAppSettings['LoginApp_socialLinking'] == '1' ) ) {
                                        Login_App_Common:: link_account( $loginAppUserId, self::$loginAppProfileData['SocialId'], self::$loginAppProfileData['Provider'], self::$loginAppProfileData['Thumbnail'], self::$loginAppProfileData['Provider'], '' );
                                    }
                                }
                                // Login user
                                LoginApp_Helper::login_user( $loginAppUserId, self::$loginAppProfileData['SocialId'] );
                                return;
                            }
                        } else {
                            LoginApp_Helper::register_user( self::$loginAppProfileData );      // create new user
                        }
                    }
                }
            } // Authentication ends
        }

        /**
         * Include necessary stylesheets at front end.
         */
        public function front_end_css() {
            $styleLocation = apply_filters( 'LoginApp_files_uri', LOGINAPP_PLUGIN_URL . 'assets/css/loginAppStyle.css' );
            wp_enqueue_style( 'LoginRadius-plugin-frontpage-css', $styleLocation . '?t=6.0.1' );
        }

        /**
         * Include necessary scripts at front end
         */
        public function front_end_scripts() {
            global $loginAppSettings;

            wp_enqueue_script( 'jquery' );
            if( isset( $_GET['lrid'] ) || isset( $_GET['loginRadiusKey'] ) ) {
                wp_enqueue_script( 'thickbox' );
                wp_enqueue_style( 'thickbox' );
            }

            if ( !is_user_logged_in() ) {
                Login_App_Common:: load_login_script();
            }
            ?>
            <script>

                function loginAppLoadEvent(func) {
                    /**
                     * Call functions on window.onload
                     */
                    var oldOnLoad = window.onload;
                    if (typeof window.onload != 'function') {
                        window.onload = func;
                    } else {
                        window.onload = function() {
                            oldOnLoad();
                            func();
                        }
                    }
                }
            </script>
            <?php
            //loading thickbox script and css for pop up
            if ( isset( $_GET['lrid'] ) && trim( $_GET['lrid'] ) != '' ) {
                self:: add_thickbox_script_for_email_popup();
            }

            if ( isset( $_GET['loginRadiusKey'] ) ) {
                // if user is not verified then display notification
                self:: display_notification_popup();
            }
        }

        /**
         * update usermeta if ita a older version plugin
         */
        public function update_plugin_meta_if_old_verison() {
            global $wpdb;
            $wpdb->query( "update " . $wpdb->usermeta . " set meta_key = 'loginapp_provider_id' where meta_key = 'id'" );
            $wpdb->query( "update " . $wpdb->usermeta . " set meta_key = 'loginapp_thumbnail' where meta_key = 'thumbnail'" );
            $wpdb->query( "update " . $wpdb->usermeta . " set meta_key = 'loginapp_verification_key' where meta_key = 'loginAppVkey'" );
            $wpdb->query( "update " . $wpdb->usermeta . " set meta_key = 'loginapp_isVerified' where meta_key = 'loginAppVerified'" );
            update_option( 'loginapp_version', LOGINAPP_SOCIALLOGIN_VERSION );
        }

        /**
         * Display notification if user is not verified
         */
        public static function display_notification_popup() {
            $message = get_user_meta( $_GET['loginRadiusKey'], 'loginapp_tmpKey', true );
            $redirection = get_user_meta( $_GET['loginRadiusKey'], 'loginapp_tmpRedirection', true );
            delete_user_meta( $_GET['loginRadiusKey'], 'loginapp_tmpKey' );
            delete_user_meta( $_GET['loginRadiusKey'], 'loginapp_tmpRedirection' );
            if ( $message != '' ) {
                $args = array(
                    'height' => 1,
                    'width' => 1,
                    'action' => 'login_app_notification_popup',
                    'key' => '',
                    'message' => urlencode( $message ),
                );
                if ( $redirection != '' ) {
                    $args['redirection'] = $redirection;
                }
                $ajaxUrl = add_query_arg( $args, 'admin-ajax.php' );
                ?>
                <style type="text/css">
                    #TB_window{
                        margin-top: -45px !important;
                    }
                </style>
                <script>
                    // show thickbox on window load
                    loginAppLoadEvent(function() {
                        tb_show('Message', '<?php echo admin_url() . $ajaxUrl; ?>');
                    });
                </script>
                <?php
            }
        }

        /**
         * function for using log_out_url for LoginApp Login widget button
         */
        public function log_out_url() {
            $redirect = get_permalink();
            $link = '<a href="' . wp_logout_url( $redirect ) . '" title="' . _e( 'Logout', 'LoginApp' ) . '">' . _e( 'Logout', 'LoginApp' ) . '</a>';
            echo apply_filters( 'Login_App_log_out_url', $link );
        }

        /**
         * add thickbox script and css for email popup
         */
        public static function add_thickbox_script_for_email_popup() {
            global $wpdb;
            global $loginAppSettings;
            $isError= 'no';

            if ( isset( $_GET['LoginRadiusMessage'] ) &&  trim( $_GET['LoginRadiusMessage'] ) == 'emailExists' )  {
                $key = trim( $_GET['lrid'] );
                $message = 'This email is already registered. Please choose another one or link this account via account linking on your profile page';
                $isError= 'yes';
            } elseif ( isset( $_GET['LoginRadiusMessage'] ) ) {
                $key = trim( $_GET['lrid'] );
                $message = trim( $loginAppSettings['msg_existemail'] );
            } else {
                $loginAppTempUniqueId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='tmpsession' AND meta_value = %s", trim( $_GET['lrid'] ) ) );
                if ( !empty( $loginAppTempUniqueId ) ) {
                    $key = trim( $_GET['lrid'] );
                    $message = trim( $loginAppSettings['msg_email'] );
                }
            }
            $ajaxUrl = add_query_arg(
                    array(
                'height' => 1,
                'width' => 1,
                'action' => 'login_app_email_popup',
                'key' => $key,
                'message' => urlencode( $message ),
                'isError' => $isError,
                    ), 'admin-ajax.php'
            );
            ?>
            <script>
                // show thickbox on window load
                loginAppLoadEvent(function() {
                    tb_show('Email required', '<?php echo admin_url() . $ajaxUrl; ?>');
                });

                // get trim() worked in IE
                if (typeof String.prototype.trim !== 'function') {
                    String.prototype.trim = function() {
                        return this.replace(/^\s+|\s+$/g, '');
                    }
                }
                var loginAppPopupSubmit = true;
                function loginAppValidateEmail() {

                    if (!loginAppPopupSubmit) {
                        return true;
                    }
                    var email = document.getElementById('loginRadiusEmail').value.trim();
                    var loginAppErrorDiv = document.getElementById('loginAppError');
                    var emailRequiredMessageDiv = document.getElementById('textmatter');

                    var atPosition = email.indexOf("@");
                    var dotPosition = email.lastIndexOf(".");
                    if (email == '' || atPosition < 1 || dotPosition < atPosition + 2 || dotPosition + 2 >= email.length) {
                        emailRequiredMessageDiv.style.display = "none";
                        loginAppErrorDiv.style.display = "block";
                        loginAppErrorDiv.innerHTML = "The email you have entered is invalid. Please enter a valid email address.";
                        loginAppErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
                        loginAppErrorDiv.style.border = "1px solid rgb(204, 0, 0)";
                        loginAppErrorDiv.style.padding = "2px 5px";
                        loginAppErrorDiv.style.width = "94%";
                        loginAppErrorDiv.style.textAlign = "left";

                        return false;
                    }
                    return true;
                }
            </script>
            <?php
        }

        /**
         * Replace buddypress default avatar with social avatar.
         */
        public function change_buddypress_avatar( $text, $args ) {
            //Check arguments
            if ( is_array( $args ) ) {
                if ( !empty( $args['object'] ) && strtolower( $args['object'] ) == 'user' ) {
                    if ( !empty( $args['item_id'] ) && is_numeric( $args['item_id'] ) ) {
                        if ( ( $userData = get_userdata( $args['item_id'] ) ) !== false ) {
                            $currentSocialId = get_user_meta( $args['item_id'], 'loginapp_current_id', true );
                            $avatar = '';
                            if ( ( $userAvatar = get_user_meta( $args['item_id'], 'loginapp_' . $currentSocialId . '_thumbnail', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                                $avatar = $userAvatar;
                            } elseif ( ( $userAvatar = get_user_meta( $args['item_id'], 'loginapp_thumbnail', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                                $avatar = $userAvatar;
                            }
                            if ( $avatar != '' ) {
                                $imgAltText = (!empty( $args['alt'] ) ? 'alt="' . esc_attr( $args['alt'] ) . '" ' : '' );
                                $imgAlt = sprintf( $imgAltText, htmlspecialchars( $userData->user_login ) );
                                $imgClass = ( 'class="' . (!empty( $args['class'] ) ? ( $args['class'] . ' ' ) : '' ) . 'avatar-social-login" ' );
                                $imgWidth = (!empty( $args['width'] ) ? 'width="' . $args['width'] . '" ' : 'width="50"' );
                                $imgHeight = (!empty( $args['height'] ) ? 'height="' . $args['height'] . '" ' : 'height="50"' );
                                $text = preg_replace( '#<img[^>]+>#i', '<img src="' . $avatar . '" ' . $imgAlt . $imgClass . $imgHeight . $imgWidth . ' style="float:left; margin-right:10px" />', $text );
                            }
                        }
                    }
                }
            }
            return $text;
        }



        /**
         * Stop disabled user from logging in.
         */
        public function Stop_disabled_user_registration( $user, $username ) {
            $tempUser = get_user_by( 'login', $username );
            if ( isset( $tempUser->data->ID ) ) {
                $id = $tempUser->data->ID;
                if ( get_user_meta( $id, 'loginapp_status', true ) === '0' ) {
                    global $loginAppLoginAttempt;
                    $loginAppLoginAttempt = 1;
                    return null;
                }
            }
            return $user;
        }

        /**
         * Display error message to inactive user
         */
        public static function error_message_for_inactive_user( $error ) {
            global $loginAppLoginAttempt;
            //check if inactive user has attempted to login
            if ( $loginAppLoginAttempt == 1 ) {
                $error = __( 'Your account is currently inactive. You will be notified through email, once Administrator activates your account.', 'LoginApp' );
            }
            return $error;
        }

        /**
         * Replace default avatar with social avatar
         */
        public function replace_default_avatar_with_social_avatar( $avatar, $avuser, $size, $default, $alt = '' ) {
            $userId = null;
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
            if ( !empty( $userId ) ) {

                $currentSocialId = get_user_meta( $userId, 'loginapp_current_id', true );
                if ( ( $userAvatar = get_user_meta( $userId, 'loginapp_picture', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    return '<img alt="' . esc_attr( $alt ) . '" src="' . $userAvatar . '" class="avatar avatar-' . $size . ' " height="' . $size . '" width="' . $size . '" />';
                } elseif ( ( $userAvatar = get_user_meta( $userId, 'loginapp_thumbnail', true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    return '<img alt="' . esc_attr( $alt ) . '" src="' . $userAvatar . '" class="avatar avatar-' . $size . ' " height="' . $size . '" width="' . $size . '" />';
                }
            }
            return $avatar;
        }

    }

}
