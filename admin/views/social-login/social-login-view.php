<?php

/**
 * Function for rendering Social Login tab on plugin on settings page
 */
function login_app_render_social_login_options() {
    global $loginAppSettings, $loginAppLoginIsBpActive;
    ?>
    <div class="menu_containt_div" id="tabs-2">
        <div class="stuffbox">
            <h3><label><?php _e( 'Redirection Settings', 'LoginApp' ); ?></label></h3>
            <div class="inside">
                <table class="form-table editcomment menu_content_table">
                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Redirection settings after login ', 'LoginApp' ); ?></div>
                            <input type="radio" class="loginRedirectionRadio" name="LoginApp_settings[LoginApp_redirect]" value="samepage" <?php echo Admin_Helper_LA:: is_radio_checked( 'login', 'samepage' ); ?>/> <?php _e( 'Redirect to the \'Same Page\' where the user logged in', 'LoginApp' ); ?><br />
                            <input type="radio" class="loginRedirectionRadio" name="LoginApp_settings[LoginApp_redirect]" value="homepage" <?php echo Admin_Helper_LA:: is_radio_checked( 'login', 'homepage' ); ?> /> <?php _e( 'Redirect to \'Home Page\' of your WP site', 'LoginApp' ); ?><br />
                            <input type="radio" class="loginRedirectionRadio" name="LoginApp_settings[LoginApp_redirect]" value="dashboard" <?php echo Admin_Helper_LA:: is_radio_checked( 'login', 'dashboard' ); ?>/> <?php _e( 'Redirect to \'Account Dashboard\'', 'LoginApp' ); ?> <br />
                            <?php
                            if ( isset( $loginAppLoginIsBpActive ) && $loginAppLoginIsBpActive ) {
                                ?>
                                <input type="radio" name="LoginApp_settings[LoginApp_redirect]" value="bp" <?php echo Admin_Helper_LA:: is_radio_checked( 'login', 'bp' ); ?>/> <?php _e( 'Redirect to Buddypress profile page', 'LoginApp' ); ?><br />
                                <?php
                            }
                            ?>
                            <input type="radio" class="loginRedirectionRadio" name="LoginApp_settings[LoginApp_redirect]" value="custom" <?php echo Admin_Helper_LA:: is_radio_checked( 'login', 'custom' ); ?> /> <?php _e( 'Redirect to \'Custom URL\'', 'LoginApp' ); ?><br />

                            <?php
                            if ( isset( $loginAppSettings['LoginApp_redirect'] ) && $loginAppSettings['LoginApp_redirect'] == 'custom' ) {
                                $inputBoxValue = htmlspecialchars( $loginAppSettings['custom_redirect'] );
                            } else {
                                $inputBoxValue = site_url();
                            }
                            ?>
                            <input type="text" id="loginAppCustomLoginUrl" name="LoginApp_settings[custom_redirect]" size="60" value="<?php echo $inputBoxValue; ?>" />
                            <div class="loginAppBorder"></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Redirection settings after registration', 'LoginApp' ); ?></div>
                            <input type="radio" class="registerRedirectionRadio" name="LoginApp_settings[LoginApp_regRedirect]" value="samepage" <?php echo Admin_Helper_LA:: is_radio_checked( 'register', 'samepage' ); ?>/> <?php _e( 'Redirect to the \'Same page\' where the user registered', 'LoginApp' ); ?><br />
                            <input type="radio" class="registerRedirectionRadio" name="LoginApp_settings[LoginApp_regRedirect]" value="homepage" <?php echo Admin_Helper_LA:: is_radio_checked( 'register', 'homepage' ); ?> /> <?php _e( 'Redirect to \'Home Page\' of your WP site', 'LoginApp' ); ?><br />
                            <input type="radio" class="registerRedirectionRadio" name="LoginApp_settings[LoginApp_regRedirect]" value="dashboard" <?php echo Admin_Helper_LA:: is_radio_checked( 'register', 'dashboard' ); ?>/> <?php _e( 'Redirect to \'Account Dashboard\'', 'LoginApp' ); ?><br />
                            <?php
                            if ( isset( $loginAppLoginIsBpActive ) && $loginAppLoginIsBpActive ) {
                                ?>
                                <input type="radio" class="registerRedirectionRadio" name="LoginApp_settings[LoginApp_regRedirect]" value="bp" <?php echo Admin_Helper_LA:: is_radio_checked( 'register', 'bp' ); ?>/> <?php _e( 'Redirect to Buddypress profile page', 'LoginApp' ); ?><br />
                                <?php
                            }
                            ?>
                            <input type="radio" class="registerRedirectionRadio" id="loginRadiusCustomRegRadio" name="LoginApp_settings[LoginApp_regRedirect]" value="custom" <?php echo Admin_Helper_LA:: is_radio_checked( 'register', 'custom' ); ?> /><?php _e( 'Redirect to \'Custom URL\'', 'LoginApp' ); ?><br />
                            <?php
                            if ( isset( $loginAppSettings['custom_regRedirect'] ) && $loginAppSettings['LoginApp_regRedirect'] == 'custom' ) {
                                $inputBoxValue = htmlspecialchars( $loginAppSettings['custom_regRedirect'] );
                            } else {
                                $inputBoxValue = site_url();
                            }
                            ?>

                            <input type="text" id="loginAppCustomRegistrationUrl" name="LoginApp_settings[custom_regRedirect]" size="60" value="<?php echo $inputBoxValue; ?>" />
                            <div class="loginAppBorder"></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Redirection settings after logging out', 'LoginApp' ) ?></div>
                            <strong><?php _e( "Note: Logout function works only when clicking 'logout' in the social login widget area. In all other cases, WordPress's default logout function will be applied.", 'LoginApp' ); ?></strong><br />
                            <input type="radio" class="logoutRedirectionRadio" name="LoginApp_settings[LoginApp_loutRedirect]" value="homepage" <?php echo Admin_Helper_LA:: is_radio_checked( 'logoutUrl', 'homepage' ); ?>/> <?php _e( 'Redirect to \'Home Page\'', 'LoginApp' ); ?><br />
                            <input type="radio" class="logoutRedirectionRadio" name="LoginApp_settings[LoginApp_loutRedirect]" value="custom" <?php echo Admin_Helper_LA:: is_radio_checked( 'logoutUrl', 'custom' ); ?> /> <?php _e( 'Redirect to \'Custom URL\'', 'LoginApp' ); ?><br />
                            <?php
                            if ( isset( $loginAppSettings['LoginApp_loutRedirect'] ) && $loginAppSettings['LoginApp_loutRedirect'] == 'custom' ) {
                                $inputBoxValue = htmlspecialchars( $loginAppSettings['custom_loutRedirect'] );
                            } else {
                                $inputBoxValue = site_url();
                            }
                            ?>
                            <input type="text" id="loginAppCustomLogoutUrl" name="LoginApp_settings[custom_loutRedirect]" size="60" value="<?php echo $inputBoxValue; ?>" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}
