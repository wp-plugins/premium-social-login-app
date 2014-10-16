<?php

function login_app_render_api_settings_options( $loginAppSettings ) {
    ?>
    <div class="menu_containt_div" id="tabs-1">
        <div class="inside">
            <table class="form-table editcomment menu_content_table">
                <tr>
                    <td>
                        <p class="loginappKeysLabel"><?php _e( 'API Key', 'LoginApp' ); ?></p>
                        <input type="text" id="login_app_api_key" name="LoginApp_settings[LoginApp_apikey]" value="<?php echo ( isset( $loginAppSettings['LoginApp_apikey'] ) ? htmlspecialchars( $loginAppSettings['LoginApp_apikey'] ) : '' ); ?>" autofill='off' autocomplete='off'  />
                    </td>
                </tr>
                <tr>
                    <td>
                        <p class="loginappKeysLabel"><?php _e( 'API Secret', 'LoginApp' ); ?></p>
                        <input type="text" id="login_app_api_secret" name="LoginApp_settings[LoginApp_secret]" value="<?php echo ( isset( $loginAppSettings['LoginApp_secret'] ) ? htmlspecialchars( $loginAppSettings['LoginApp_secret'] ) : '' ); ?>" autofill='off' autocomplete='off'  />
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}
