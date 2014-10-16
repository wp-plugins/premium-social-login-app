<?php

/**
 * Plugin Name.
 *
 * @package   loginapp
 * @author    LoginApp Team
 * @license   GPL-2.0+
 * @link      http://loginapp.io
 * @copyright 2014 LoginApp
 */

/**
 * Plugin class. This class would ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in administrative or dashboard
 * functionality, then refer to `class-loginappadmin.php`
 *
 *
 * @package LoginApp
 * @author  LoginApp Team
 */
class Login_App_Front {

    /**
     * Instance of this class.
     */
    protected static $instance = null;

    /**
     * Get singleton object for class Login_App_Front
     * 
     * @return object Login_App_Front
     */
    public static function get_instance() {
        if ( !isset( self::$instance ) && !( self::$instance instanceof Login_App_Front ) ) {
            self::$instance = new Login_App_Front();
        }
        return self::$instance;
    }

    /**
     * Constructor which loads required files at front
     */
    private function __construct() {

        require_once "inc/login/class-social-login.php";
        Social_Login_LA:: get_instance();

        require_once "inc/sharing/class-social-sharing.php";
        Social_Sharing_LA:: get_instance();

        require_once "inc/commenting/class-social-commenting.php";
        Social_Commenting_LA:: get_instance();

        require_once "inc/shortcodes/class-shortcode.php";
        Login_App_Shortcode:: get_instance();
    }

}

Login_App_Front:: get_instance();


