<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'Login_App_Common' ) ) {
    /**
     * This class contains method which are used by admin as well as front
     */
    class Login_App_Common {

        /**
         * Check if ID can be link or not. if yes the link account.
         */
        public static function link_account_if_possible() {
            global $loginAppObject, $wpdb, $loginAppSettings, $user_ID;

            $loginAppSecret = $loginAppSettings['LoginApp_secret'];
            $loginAppMappingData = array();
            if( null == $loginAppObject){
                $loginAppObject = new Login_App_SDK();
            }
            if ( isset( $_REQUEST['token'] ) && is_user_logged_in() ) {

                $loginAppUserprofile = $loginAppObject->get_user_profiledata( $_REQUEST['token'] );
                $loginAppMappingData['id'] = (!empty( $loginAppUserprofile->ID ) ? $loginAppUserprofile->ID : '' );
                $loginAppMappingData['provider'] = (!empty( $loginAppUserprofile->Provider ) ? $loginAppUserprofile->Provider : '' );
                $loginAppMappingData['thumbnail'] = (!empty( $loginAppUserprofile->ThumbnailImageUrl ) ? trim( $loginAppUserprofile->ThumbnailImageUrl ) : '' );
                if ( empty( $loginAppMappingData['thumbnail'] ) && $loginAppMappingData['provider'] == 'facebook' ) {
                    $loginAppMappingData['thumbnail'] = 'http://graph.facebook.com/' . $loginAppMappingData['id'] . '/picture?type=large';
                }
                $loginAppMappingData['pictureUrl'] = (!empty( $loginAppUserprofile->ImageUrl ) ? trim( $loginAppUserprofile->ImageUrl ) : '' );
                $wp_user_id = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key="loginapp_provider_id" AND meta_value = %s', $loginAppMappingData['id'] ) );
                if ( !empty( $wp_user_id ) ) {
                    // Check if verified field exist or not.
                    $loginAppVfyExist = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = "loginapp_isVerified"', $wp_user_id ) );
                    if ( !empty( $loginAppVfyExist ) ) { // if verified field exists
                        $loginAppVerify = $wpdb->get_var( $wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = "loginapp_isVerified"', $wp_user_id ) );
                        if ( $loginAppVerify != '1' ) {
                            self:: link_account( $user_ID, $loginAppMappingData['id'], $loginAppMappingData['provider'], $loginAppMappingData['thumbnail'], $loginAppMappingData['pictureUrl'] );
                            return true;
                        } else {
                            //account already mapped
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $loginAppMappingProvider = $loginAppMappingData['provider'];
                    $wp_user_lrid = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key="' . $loginAppMappingProvider . 'Lrid" AND meta_value = %s', $loginAppMappingData['id'] ) );
                    if ( !empty( $wp_user_lrid ) ) {
                        $lrVerified = get_user_meta( $wp_user_lrid, $loginAppMappingProvider . 'LAVerified', true );
                        if ( $lrVerified == '1' ) {  // Check if lrid is the same that verified email.
                            // account already mapped
                            return false;
                        } else {
                            // map account
                            self:: link_account( $user_ID, $loginAppMappingData['id'], $loginAppMappingData['provider'], $loginAppMappingData['thumbnail'], $loginAppMappingData['pictureUrl'] );
                            return true;
                        }
                    } else {
                        // map account
                        self:: link_account( $user_ID, $loginAppMappingData['id'], $loginAppMappingData['provider'], $loginAppMappingData['thumbnail'], $loginAppMappingData['pictureUrl'] );
                        return true;
                    }
                }
            }
        }

        /**
         * Get current protocol ( http OR https )
         */
        public static function get_protocol() {
            if ( isset( $_SERVER['HTTPS'] ) && !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
                return 'https://';
            } else {
                return 'http://';
            }
        }

        /**
         * Update usermeta to store linked account information
         */
        public static function link_account( $id, $lrid, $provider, $thumb, $pictureUrl ) {

            add_user_meta( $id, 'loginapp_provider_id', $lrid );
            add_user_meta( $id, 'loginapp_mapped_provider', $provider );
            add_user_meta( $id, 'loginapp_'.$provider.'_id', $lrid );
            if ( $thumb != '' ) {
                add_user_meta( $id, 'loginapp_'.$lrid.'_thumbnail', $thumb );
            }
            if ( $pictureUrl != '' ) {
                add_user_meta( $id, 'loginapp_'.$lrid.'_picture', $pictureUrl );
            }
        }


        /**
         * Check if scripts are to be loaded in footer according to plugin option
         */
        public static function scripts_in_footer_enabled() {
            global $loginAppSettings;

            if ( isset( $loginAppSettings['scripts_in_footer'] ) && $loginAppSettings['scripts_in_footer'] == 1 ) {
                return true;
            }
            return false;
        }

        /**
         * perform linking operation and return parameters if account mapped or not accordingly
         */
        public static function perform_linking_operation() {

            // public function call
            if ( Login_App_Common:: link_account_if_possible() === true ) {
                $linked = 1;
            } else {
                $linked = 0;
            }

            $redirectionUrl = Login_App_Common:: get_protocol() . htmlspecialchars( $_SERVER['HTTP_HOST'] ) . remove_query_arg( 'lrlinked' );
            if ( strpos( $redirectionUrl, '?' ) !== false ) {
                $redirectionUrl .= '&lrlinked=' . $linked;
            } else {
                $redirectionUrl .= '?lrlinked=' . $linked;
            }
            wp_redirect( $redirectionUrl );
            exit();
        }

        /**
         * Loading Login Script for loggedin user to provide account linking
         */
        public static function load_login_script( $isLinkingWidget = false ) {
            global $loginAppSettings;
            $loginAppSettings['LoginApp_apikey'] = isset( $loginAppSettings['LoginApp_apikey'] ) ? trim( $loginAppSettings['LoginApp_apikey'] ) : '';
            if( ! class_exists( "LoginApp_Helper" ) ) {
                 require_once LOGINAPP_PLUGIN_DIR . 'public/inc/login/class-login-helper.php';
            }
            $location = LoginApp_Helper::get_callback_url_for_redirection( Login_App_Common::get_protocol() );
            if ( $isLinkingWidget ) {
                $locationWithoutQueryString = urldecode( $location );
                if ( strpos( $location, '?' ) !== false ) {
                    $locationWithoutQueryString .= '&loginradius_linking=1';
                } else {
                    $location .= '?loginradius_linking=1';
                }
                $location = urlencode( $location );
            }
            ?>
            <!-- Script to enable social login -->
            <script src="//hub.loginradius.com/include/js/LoginRadius.js"></script>
            <script src = '<?php echo LOGINAPP_PLUGIN_URL . "assets/js/LoginRadiusSDK.2.0.0.js";?>' ></script>
            <script type="text/javascript">
            var loginRadiusOptions = {};
            loginRadiusOptions.login = true;
            LoginRadius_SocialLogin.util.ready(function() {
                $ui = LoginRadius_SocialLogin.lr_login_settings;
                $ui.interfacesize = '';
                $ui.apikey = "<?php echo $loginAppSettings['LoginApp_apikey'] ?>";
                $ui.callback = "<?php echo $location ?>";
                $ui.lrinterfacecontainer = "interfacecontainerdiv";
                $ui.is_access_token = true;
                <?php
                if ( isset( $loginAppSettings["LoginApp_interfaceSize"] ) && $loginAppSettings["LoginApp_interfaceSize"] == "small" ) {
                    echo '$ui.interfacesize ="small";';
                }
                if ( isset( $loginAppSettings['LoginApp_numColumns'] ) && trim( $loginAppSettings['LoginApp_numColumns'] ) != '' ) {
                    echo '$ui.noofcolumns = ' . trim( $loginAppSettings['LoginApp_numColumns'] ) . ';';
                }
                if ( isset( $loginAppSettings['LoginApp_backgroundColor'] ) ) {
                    echo '$ui.lrinterfacebackground = "' . trim( $loginAppSettings['LoginApp_backgroundColor'] ) .'";';
                }
                ?>

                LoginRadius_SocialLogin.init(loginRadiusOptions);
            });
            LoginRadiusSDK.setLoginCallback(function() {
            var form = document.createElement('form');
            form.action = "<?php echo urldecode( $location ); ?>";
            form.method = 'POST';
            var hiddenToken = document.createElement('input');
            hiddenToken.type = 'hidden';
            hiddenToken.value = LoginRadiusSDK.getToken();
            hiddenToken.name = "token";
            form.appendChild(hiddenToken);
            document.body.appendChild(form);
            form.submit();
            });

            </script>
            <?php
            // }
        }

        /**
         * Check linking parameters and display message if account linked successfully or not
         */
        public static function check_linking_status_parameters( ) {
            $html = '';
            if ( isset( $_GET['lrlinked'] ) ) {
                if ( $_GET['lrlinked'] == 1 ) {
                    $html .= '<div id="loginAppSuccess" style="background-color: #FFFFE0; border:1px solid #E6DB55; padding:5px; margin:5px; color: #000">';
                    $html .= __( 'Account mapped successfully', 'LoginApp' );
                    $html .= '</div>';
                } else {
                    $html .= '<div id="loginAppError" style="background-color: #FFEBE8; border:1px solid #CC0000; padding:5px; margin:5px; color: #000;">';
                    $html .= __( 'This account is already mapped', 'LoginApp' );
                    $html .= '</div>';
                }
                return $html;
            }
        }

        /**
         * Display connectd/linked providers on user wp profile page
         */
        public static function get_connected_providers_list() {
            global $user_ID;
            $html = '';
            $loginAppMappings = get_user_meta( $user_ID, 'loginapp_mapped_provider', false );
            $loginAppMappings = array_unique( $loginAppMappings );
            $connected = false;
            $loginAppLoggedIn = get_user_meta( $user_ID, 'loginapp_current_id', true );
            $totalAccounts = get_user_meta( $user_ID, 'loginapp_provider_id' );
            $location = Login_App_Common:: get_protocol(). $_SERVER['HTTP_HOST'] . remove_query_arg( array( 'lrlinked', 'loginradius_linking', 'loginradius_post', 'loginradius_invite', 'loginRadiusMappingProvider', 'loginRadiusMap', 'loginRadiusMain' )  );

            if ( count( $loginAppMappings ) > 0 ) {
                foreach ( $loginAppMappings as $map ) {
                    $loginAppMappingId = get_user_meta( $user_ID, 'loginapp_'.$map.'_id' );

                    if ( count( $loginAppMappingId ) > 0 ) {
                        foreach ( $loginAppMappingId as $tempId ) {
                            $html .= '<tr>';

                            if ( $loginAppLoggedIn == $tempId ) {
                                $append    = '<span style=\'color:green\'>Currently </span>';
                                $connected = true;
                            }else {
                                $append = '';
                            }

                            $html .=  '<td>' . $append;
                            $html .=  __( 'Connected with', 'LoginApp' );
                            $html .= '<strong> ' . ucfirst( $map ) . '</strong> <img src=\'' . LOGINAPP_PLUGIN_URL . 'assets/images/linking/' . $map . '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                            if ( count( $totalAccounts ) != 1 ) {
                                $html .= '<a href=' . $location . ( strpos( $location,'?' ) !== false ? '&' : '?' ) . 'loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type=\'button\' class=\'button-primary\' value="' . __( 'Remove', 'LoginApp' ) . '" /></a>';
                            }
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                    }
                }
            }
            $map = get_user_meta( $user_ID, 'loginapp_provider', true );
            if ( $map != false ) {
                $html .= '<tr>';
                $tempId = $loginAppLoggedIn;
                $append = ! $connected ? '<span style=\'color:green\'>Currently </span>' : '';
                $html .=  '<td>' . $append;
                $html .=  __( 'Connected with', 'LoginApp' );
                $html .=  '<strong> ' . ucfirst( $map ) . '</strong> <img src=\'' . LOGINAPP_PLUGIN_URL . 'assets/images/linking/' . $map. '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                if ( count( $totalAccounts ) != 1 ) {
                    $html .= '<a href=' . $location . ( strpos( $location,'?' ) !== false ? '&' : '?' ) . 'loginRadiusMain=1&loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type="button" class="button-primary" value="' . __( 'Remove', 'LoginApp' ) . '" /></a>';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
            return $html;
        }

        /**
         * Display provider , user is currently connected with
         */
        public static function display_currently_connected_provider() {
            global $user_ID;
            $loginAppLoggedIn = get_user_meta( $user_ID, 'loginapp_current_id', true );
            $totalAccounts = get_user_meta( $user_ID, 'loginapp_provider_id' );
            $location = Login_App_Common:: get_protocol() . $_SERVER['HTTP_HOST'] . remove_query_arg( array('lrlinked', 'loginradius_linking', 'loginradius_post', 'loginradius_invite', 'loginRadiusMappingProvider', 'loginRadiusMap', 'loginRadiusMain') );
            $html = '';
            $map = get_user_meta( $user_ID, 'loginapp_provider', true );
            if ( $map != false ) {
                $html .= '<tr>';
                $tempId = $loginAppLoggedIn;
                $append = '<span style=\'color:green\'>Currently </span>';
                $html .= '<td>' . $append;
                $html .= __( 'Connected with', 'LoginApp' );
                $html .= '<strong> ' . ucfirst( $map ) . '</strong> <img src=\'' . LOGINAPP_PLUGIN_URL . 'assets/images/linking/' . $map . '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                if ( count( $totalAccounts ) != 1 ) {
                    $html .= '<a href=' . $location . ( strpos( $location, '?' ) !== false ? '&' : '?' ) . 'loginRadiusMain=1&loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type="button" class="button-primary" value="' . __( 'Remove', 'LoginApp' ) . '" /></a>';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
            return $html;
        }

        /**
         * Function which sends email on user activation to admin and users
         */
        public static function login_app_send_verification_email( $loginAppEmail, $loginAppKey, $loginAppProvider = '', $emailType = '', $username = '' ) {

            $loginAppSubject = '';
            $loginAppMessage = '';
            switch ( $emailType ) {
                case "activation":
                    $loginAppSubject = '[' . htmlspecialchars( trim( get_option( 'blogname' ) ) ) . '] AccountActivation';
                    $loginAppMessage = 'Hi ' . $username . ", \r\n" .
                            'Your account has been activated at ' . site_url() . '. Now you can login to your account.';
                    break;
                case "admin_notification":
                    $user = get_userdata( $username );
                    $loginAppSubject = '[' . htmlspecialchars( trim( get_option( 'blogname' ) ) ) . '] New User Registration';
                    $loginAppMessage = 'New user registration on your site ' . htmlspecialchars( trim( get_option( 'blogname' ) ) ) . ": \r\n" .
                            'Username: ' . $user->user_login . " \r\n" .
                            'E-mail: ' . $user->user_email . '';
                    break;
                default :
                    $loginAppSubject = '[' . htmlspecialchars( trim( get_option( 'blogname' ) ) ) . '] Email Verification';
                    $loginAppUrl = site_url() . '?loginRadiusVk=' . $loginAppKey;
                    if ( !empty( $loginAppProvider ) ) {
                        $loginAppUrl .= '&loginRadiusProvider=' . $loginAppProvider;
                    }
                    $loginAppMessage = "Please click on the following link or paste it in browser to verify your email \r\n" . $loginAppUrl;
                    break;
            }
            $headers = "MIME-Version: 1.0\n" .
                    "Content-Type: text/plain; charset='" .
                    get_option( 'blog_charset' ) . "\"\n" .
                    'From: <no-reply@loginradius.com>';
            wp_mail( $loginAppEmail, $loginAppSubject, $loginAppMessage, $headers );
        }
    }

}