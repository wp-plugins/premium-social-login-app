<?php

/**
 * class responsible for setting default settings for LoginApp Social Login and Share plugin
 */
class Login_App_Install
{

    /**
     * Function for adding default plugin settings at activation
     */
    public static function set_default_options()
    {
        if (version_compare( get_bloginfo( 'version' ), LOGINAPP_MIN_WP_VERSION, '<' )) {
            $message = "Plugin could not be activated because ";
            $message .= "WordPress version is lower than ";
            $message .= LOGINAPP_MIN_WP_VERSION;
            die( $message );
        }
        if ( !get_option( 'loginapp_db_version' ) || !get_option( 'LoginApp_settings' )) {
            // If plugin loginapp_db_version option not exist, it means plugin is not latest and update options.
            $options = array(
                'LoginApp_loginform'            => '1',
                'LoginApp_regform'              => '1',
                'LoginApp_regformPosition'      => 'embed',
                'LoginApp_commentEnable'        => '0',
                'horizontalSharing_theme'          => '32',
                'horizontal_shareEnable'           => '0',
                'horizontal_shareTop'              => '1',
                'horizontal_shareBottom'           => '1',
                'horizontal_sharehome'             => '1',
                'horizontal_sharepost'             => '1',
                'horizontal_sharepage'             => '1',
                'horizontal_shareexcerpt'          => '1',
                'vertical_shareEnable'             => '0',
                'verticalSharing_theme'            => 'counter_vertical',
                'vertical_sharehome'               => '1',
                'vertical_sharepost'               => '1',
                'vertical_sharepage'               => '1',
                'sharing_verticalPosition'         => 'top_left',
                'LoginApp_noProvider'           => '1',
                'LoginApp_enableUserActivation' => '1',
                'scripts_in_footer'                => '0',
                'delete_options'                   => '1',
                'username_separator'               => 'dash',
                'LoginApp_redirect'             => 'samepage',
                'LoginApp_regRedirect'          => 'samepage',
                'LoginApp_loutRedirect'         => 'homepage',
                'LoginApp_socialavatar'         => 'socialavatar',
                'LoginApp_title'                => 'Login with Social ID',
                'enable_degugging'                 => '0',
                'LoginApp_sendemail'            => 'notsendemail',
                'LoginApp_dummyemail'           => 'notdummyemail'
            );
            if (file_exists( dirname( __FILE__ ) . '/api_key.php' )) {
                $api_data = include "api_key.php";

                $options['LoginApp_apikey'] = $api_data['api_key'];
                $options['LoginApp_secret'] = $api_data['api_secret'];
            }
            update_option( 'LoginApp_settings', $options );
            update_option( 'loginapp_db_version', LOGINAPP_SOCIALLOGIN_VERSION );
        } else if (LOGINAPP_SOCIALLOGIN_VERSION != get_option( 'loginapp_db_version' )) {
            update_option( 'loginapp_db_version', LOGINAPP_SOCIALLOGIN_VERSION );
        }
    }

}
