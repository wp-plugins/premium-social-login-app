<?php

/**
 *
 * function for rendering Social Commenting tab on settings page.
 */
function login_app_render_social_commenting_options( $loginAppSettings ) {
    ?>
    <div class="menu_containt_div" id="tabs-4">
        <div class="stuffbox">
            <h3><label><?php _e( 'Social Commenting Settings', 'LoginApp' ) ?></label></h3>
            <div class="inside">
                <table class="form-table editcomment menu_content_table">
                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Do you want to enable social commenting for your website' ); ?>?</div>
                            <div class="loginAppYesRadio">
                                <input type="radio" name="LoginApp_settings[LoginApp_commentEnable]" value='1' <?php echo ( ( isset( $loginAppSettings['LoginApp_commentEnable'] ) && $loginAppSettings['LoginApp_commentEnable'] == 1 ) || !isset( $loginAppSettings['LoginApp_commentEnable'] ) ) ? 'checked' : '' ?>/> <?php _e( 'Yes', 'LoginApp' ); ?>
                            </div>
                            <input type="radio" name="LoginApp_settings[LoginApp_commentEnable]" value="0" <?php checked( '0', @$loginAppSettings['LoginApp_commentEnable'] ); ?>/> <?php _e( 'No', 'LoginApp' ); ?>
                            <div class="loginAppBorder"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Where do you want to display the Social login interface on the commenting form?', 'LoginApp' ); ?></div>
                            <input type="radio" name="LoginApp_settings[LoginApp_commentInterfacePosition]" value="after_leave_reply" <?php echo ( ( isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) && $loginAppSettings['LoginApp_commentInterfacePosition'] == 'after_leave_reply' ) || !isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) ) ? 'checked="checked"' : '' ?> ><?php _e( "After the 'Leave a Reply' caption", 'LoginApp' ) ?><br />
                            <input type="radio" name="LoginApp_settings[LoginApp_commentInterfacePosition]" value="very_top" <?php echo ( isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) && $loginAppSettings['LoginApp_commentInterfacePosition'] == 'very_top' ) ? 'checked="checked"' : '' ?> ><?php _e( 'At the very top of the comment form', 'LoginApp' ) ?><br />
                            <input type="radio" name="LoginApp_settings[LoginApp_commentInterfacePosition]" value="very_bottom" <?php echo isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) && $loginAppSettings['LoginApp_commentInterfacePosition'] == 'very_bottom' ? 'checked="checked"' : '' ?> ><?php _e( 'At the very bottom of the comment form', 'LoginApp' ) ?><br />
                            <input type="radio" name="LoginApp_settings[LoginApp_commentInterfacePosition]" value="before_fields" <?php echo isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) && $loginAppSettings['LoginApp_commentInterfacePosition'] == 'before_fields' ? 'checked="checked"' : '' ?> ><?php _e( 'Before the comment form input fields', 'LoginApp' ) ?><br />
                            <input type="radio" name="LoginApp_settings[LoginApp_commentInterfacePosition]" value="after_fields" <?php echo isset( $loginAppSettings['LoginApp_commentInterfacePosition'] ) && $loginAppSettings['LoginApp_commentInterfacePosition'] == 'after_fields' ? 'checked="checked"' : '' ?> ><?php _e( 'Before the comment box', 'LoginApp' ) ?>

                            <div class="loginAppBorder"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}
