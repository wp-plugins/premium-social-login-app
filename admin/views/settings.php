<?php
/**
 * @file
 * The Admin Panel and related tasks are handled in this file.
 */
// Exit if called directly
if ( !defined( 'ABSPATH' )) {
    exit();
}

/**
 * The main class and initialization point of the plugin settings page.
 */
if ( !class_exists( 'Login_App_Admin_Settings' )) {

    class Login_App_Admin_Settings
    {

        /**
         * Render settings page
         */
        public static function render_options_page()
        {
            require_once LOGINAPP_PLUGIN_DIR . 'admin/views/admin-header.php';

            $loginAppSettings = get_option( 'LoginApp_settings' );
            // rendering LoginApp plugin admin header
            render_admin_header();
            if ( !isset( $loginAppSettings['LoginApp_apikey'] ) || !isset( $loginAppSettings['LoginApp_secret'] ) || trim( $loginAppSettings['LoginApp_apikey'] ) == '' || trim( $loginAppSettings['LoginApp_secret'] ) == '') {
                Admin_Helper_LA:: display_notice_to_insert_api_and_secret();
            }
            // print javascript for plugin settings page 
            Admin_Helper_LA:: render_admin_ui_script();
            ?>
            <div class="wrapper">
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'LoginApp_setting_options' );
                    settings_errors();
                    ?>
                    <div class="metabox-holder columns-2" id="post-body">
                        <div class="menu_div" id="tabs">
                            <h2 class="nav-tab-wrapper" style="height:36px">
                                <ul>
                                    <li style="margin-left:9px">
                                        <a style="margin:0; height:23px" class="nav-tab"
                                           href="#tabs-1"><?php _e( 'API Settings',
                                                'LoginApp' ) ?></a>
                                    </li>
                                    <li><a style="margin:0; height:23px" class="nav-tab"
                                           href="#tabs-2"><?php _e( 'Social Login', 'LoginApp' ) ?></a></li>
                                    <!--<li><a style="margin:0; height:23px" class="nav-tab"
                                           href="#tabs-3"><?php /*_e( 'Social Sharing', 'LoginApp' ) */ ?></a></li>
                                    <li><a style="margin:0; height:23px" class="nav-tab"
                                           href="#tabs-4"><?php /*_e( 'Social Commenting', 'LoginApp' ) */ ?></a></li>-->
                                    <li><a style="margin:0; height:23px" class="nav-tab"
                                           href="#tabs-5"><?php _e( 'Advanced Settings', 'LoginApp' ) ?></a></li>
                                    <!--<li><a style="margin:0; height:23px"
                                           class="nav-tab"
                                           href="#tabs-6"><?php /*_e( 'Help',
                                                'LoginApp' ) */
                                    ?></a></li>-->
                                </ul>
                            </h2>
                            <?php

                            include 'social-login/social-login-view.php';
                            login_app_render_social_login_options( $loginAppSettings );

                            //include 'social-sharing/social-sharing-view.php';
                            //login_app_render_social_sharing_options( $loginAppSettings );

                            //include 'social-commenting/social-commenting-view.php';
                            //login_app_render_social_commenting_options( $loginAppSettings );

                            include 'api-settings/api-settings-view.php';
                            login_app_render_api_settings_options( $loginAppSettings );


                            include 'advance-settings/advance-settings-view.php';
                            login_app_render_advance_settings_options( $loginAppSettings );

                            //include 'help/help-view.php';
                            //login_app_render_help_options();
                            ?>
                        </div>
                        <p class="submit">
                            <?php
                            // Build Preview Link
                            $preview_link = get_option( 'home' ) . '/';
                            if (is_ssl()) {
                                $preview_link = str_replace( 'http://', 'https://', $preview_link );
                            }
                            $stylesheet   = get_option( 'stylesheet' );
                            $template     = get_option( 'template' );
                            $preview_link = htmlspecialchars( add_query_arg( array(
                                'preview'        => 1,
                                'template'       => $template,
                                'stylesheet'     => $stylesheet,
                                'preview_iframe' => true,
                                'TB_iframe'      => 'true'
                            ), $preview_link ) );
                            ?>
                            <input style="margin-left:8px" type="submit" name="save" class="button button-primary"
                                   value="<?php _e( 'Save Changes', 'LoginApp' ); ?>"/>
                            <a href="<?php echo $preview_link; ?>" class="thickbox thickbox-preview"
                               id="preview"><?php _e( 'Preview', 'LoginApp' ); ?></a>
                        </p>
                    </div>
                </form>
            </div>
        <?php
        }

    }

}


