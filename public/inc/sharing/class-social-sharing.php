<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'Social_Sharing_LA' ) ) {

    /**
     * Class responsible for Social Sharing functionality
     */
    class Social_Sharing_LA {

        /**
         * Social_Sharing_LA calss instance
         *
         * @var string
         */
        private static $instance = null;

        /**
         * Get singleton object for class Social_Sharing_LA
         *
         * @return object Social_Sharing_LA
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if ( null == self::$instance ) {
                self::$instance = new Social_Sharing_LA();
            }

            return self::$instance;
        }

        /**
         * Constructor for class Social_Sharing_LA
         */
        public function __construct() {
            require_once "class-sharing-helper.php";

            if ( Login_App_Common:: scripts_in_footer_enabled() ) {
                //Adding Sharing script in footer
                add_action( 'wp_footer', array('SharingLA_Helper', 'login_app_sharing_get_sharing_script') );
            } else {
                //By default adding script in header
                add_action( 'wp_enqueue_scripts', array('SharingLA_Helper', 'login_app_sharing_get_sharing_script') );
            }
            add_filter( 'the_content', array('SharingLA_Helper', 'login_app_sharing_content') );
            add_filter( 'get_the_excerpt', array('SharingLA_Helper', 'login_app_sharing_content') );
        }

    }

}
