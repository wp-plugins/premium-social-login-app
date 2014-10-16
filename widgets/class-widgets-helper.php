<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'Login_App_Widget_Helper' ) ) {

    class Login_App_Widget_Helper {

        /**
         * Display social login interface in widget area.
         */
        public static function login_app_widget_connect_button() {
            global $loginAppSettings, $user_ID;
            if ( !is_user_logged_in() ) {
                LoginApp_Helper:: get_loginapp_interface_container();
            }
            // On user Login show user details.
            if ( is_user_logged_in() && !is_admin() ) {

                $size = '60';
                $user = get_userdata( $user_ID );
                $currentSocialId = get_user_meta( $user_ID, 'loginapp_current_id', true );
                // hold the value of avatar option
                $socialAvatar = '';
                $avatarType = 'thumbnail';
                if ( isset( $loginAppSettings['LoginApp_socialavatar'] ) ) {
                    $socialAvatar = $loginAppSettings['LoginApp_socialavatar'];
                    if ( $socialAvatar == 'largeavatar' ) {
                        $avatarType = 'picture';
                    }
                }
                echo "<div style='height:80px;width:180px'><div style='width:63px;float:left;'>";
                if ( $socialAvatar && ( $userAvatar = get_user_meta( $user_ID, 'loginapp_' . $currentSocialId . '_' . $avatarType, true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    echo '<img alt="user social avatar" src="' . $userAvatar . '" height = "' . $size . '" width = "' . $size . '" title="' . $user->user_login . '" style="border:2px solid #e7e7e7;"/>';
                } elseif ( $socialAvatar && ( $userAvatar = get_user_meta( $user_ID, 'loginapp_' . $avatarType, true ) ) !== false && strlen( trim( $userAvatar ) ) > 0 ) {
                    echo '<img alt="user social avatar" src="' . $userAvatar . '" height = "' . $size . '" width = "' . $size . '" title="' . $user->user_login . '" style="border:2px solid #e7e7e7;"/>';
                } else {
                    echo @get_avatar( $user_ID, $size, $default, $alt );
                }
                echo "</div><div style='width:100px; float:left; margin-left:10px'>";
                // username separator
                if ( !isset( $loginAppSettings['username_separator'] ) || $loginAppSettings['username_separator'] == 'dash' ) {
                    echo $user->user_login;
                } elseif ( isset( $loginAppSettings['username_separator'] ) && $loginAppSettings['username_separator'] == 'dot' ) {
                    echo str_replace( '-', '.', $user->user_login );
                } else {
                    echo str_replace( '-', ' ', $user->user_login );
                }
                if ( $loginAppSettings['LoginApp_loutRedirect'] == 'custom' && !empty( $loginAppSettings['custom_loutRedirect'] ) ) {
                    $redirect = htmlspecialchars( $loginAppSettings['custom_loutRedirect'] );
                } else {
                    $redirect = home_url();
                    ?>
                    <?php
                }

                ?>
                <br/><a href="<?php echo wp_logout_url( $redirect ); ?>"><?php _e( 'Log Out', 'LoginApp' ); ?></a><?php
            }
        }

    }

}