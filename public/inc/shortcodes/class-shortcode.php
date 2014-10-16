<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}


if ( !class_exists( 'Login_App_Shortcode' ) ) {

    /**
     * This class is responsible for adding plugin shortcodes
     */
    class Login_App_Shortcode {

        /**
         * Login_App_Shortcode calss instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Get singleton object for class Login_App_Shortcode
         *
         * @return object Login_App_Shortcode
         */
        public static function get_instance() {

            if ( !isset( self::$instance ) && !( self::$instance instanceof Login_App_Shortcode ) ) {
                self::$instance = new Login_App_Shortcode();
            }
            return self::$instance;
        }

        /*
         * Constructor for class Login_App_Shortcode
         */

        public function __construct() {

            $this->register_shortcodes();
        }

        /*
         * Register all LoginApp plugin shortcodes.
         */

        public function register_shortcodes() {

            add_shortcode( 'LoginApp_Share', array($this, 'sharing_shortcode') );
            add_shortcode( 'LoginApp_Login', array($this, 'login_shortcode') );
            add_shortcode( 'LoginApp_Linking', array($this, 'linking_widget_shortcode') );
        }

        /**
         * Callback for Social Sharing shortcode.
         */
        public static function sharing_shortcode( $params ) {
            $tempArray = array(
                'style' => '',
                'type' => 'horizontal',
            );
            extract( shortcode_atts( $tempArray, $params ) );
            $return = '<div ';
            // sharing theme type
            if ( $type == 'vertical' ) {
                $return .= 'class="loginRadiusVerticalSharing" ';
            } else {
                $return .= 'class="loginRadiusHorizontalSharing" ';
            }
            // style
            if ( $style != '' ) {
                $return .= 'style="' . $style . '"';
            }
            $return .= '></div>';
            return $return;
        }

        /**
         * Callback for social login shortcode.
         */
        public static function login_shortcode( $params ) {
            if ( is_user_logged_in() ) {
                return '';
            }
            $return = '';
            $tempArray = array(
                'style' => '',
            );
            extract( shortcode_atts( $tempArray, $params ) );
            if ( $style != '' ) {
                $return .= '<div style="' . $style . '">';
            }
            $return .= LoginApp_Helper:: get_loginapp_interface_container( true );
            if ( $style != '' ) {
                $return .= '</div>';
            }
            return $return;
        }

        /**
         * Callback for Social Linking widget shortcode
         */
        public static function linking_widget_shortcode() {
            global $loginAppSettings, $loginAppObject;
            if ( !is_user_logged_in() ) {
                return '';
            }
            $html = Login_App_Common:: check_linking_status_parameters();
            if ( !( $loginAppObject->validate_key( trim( $loginAppSettings['LoginApp_apikey'] ) ) && $loginAppObject->validate_key( trim( $loginAppSettings['LoginApp_secret'] ) ) ) ) {
                $html .= '<div style="color:red">' . __( 'Your LoginApp API key or secret is not valid, please correct it or contact LoginApp support at <b><a href ="http://loginapp.io" target = "_blank">LoginApp.io</a></b>', 'LoginApp' ) . '</div>';
            }
            // function call
            Login_App_Common:: link_account_if_possible();
            if ( !( $loginAppObject->validate_key( trim( $loginAppSettings['LoginApp_apikey'] ) ) && $loginAppObject->validate_key( trim( $loginAppSettings['LoginApp_secret'] ) ) ) ) {
                $html .= '<div style="color:red">' . __( 'Your LoginApp API key or secret is not valid, please correct it or contact LoginApp support at <b><a href ="http://loginapp.io" target = "_blank">LoginApp.io</a></b>', 'LoginApp' ) . '</div>';
            }

            Login_App_Common:: load_login_script( true );
            $html .= LoginApp_Helper:: get_loginapp_interface_container( true );
            $html .= '<table class="loginAppLinking">';
            $html .= Login_App_Common:: get_connected_providers_list();
            //$html .= Login_App_Common:: display_currently_connected_provider();
            $html .= '<table>';
            return $html;
        }

    }

}