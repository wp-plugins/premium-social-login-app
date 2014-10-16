<?php
/**
 *
 * @param type $loginAppSettingsfunction for rendering Social Share tab on plugin settings page
 */
function login_app_render_social_sharing_options( $loginAppSettings ) {
    ?>
    <div class="menu_containt_div" id="tabs-3">
        <div class="stuffbox">
            <h3><label><?php _e( 'Social Sharing Interface Selection', 'LoginApp' ); ?></label></h3>
            <div class="inside">
                <table class="form-table editcomment menu_content_table">
                    <tr>
                        <td>
                            <div class="loginAppQuestion"><?php _e( 'Please select the social sharing interface, horizontal and vertical interfaces can be enabled simultaneously.', 'LoginApp' ); ?></div>
                            <br />
                            <a href="javascript:void( 0 ) " style="text-decoration:none" id = "show_horizontal_theme_content" ><?php _e( 'Horizontal', 'LoginApp' ); ?></a> | <a href="javascript:void( 0 ) " id = "show_vertical_theme_content" style="text-decoration:none"><?php _e( 'Vertical', 'LoginApp' ); ?></a>
                        </td>
                    </tr>
                    <tr id="login_app_horizontal">
                        <td>
                            <span class="lrsharing_spanwhite"></span>
                            <span class="lrsharing_spangrey"></span>
                            <div style="border:1px solid #ccc; padding:10px; border-radius:5px">
                                <div class="loginAppQuestion"><?php _e( 'Do you want to enable horizontal social sharing for your website?', 'LoginApp' ); ?></div>
                                <div class="loginAppYesRadio">
                                    <input type="radio" name="LoginApp_settings[horizontal_shareEnable]" value='1' <?php echo!isset( $loginAppSettings['horizontal_shareEnable'] ) || $loginAppSettings['horizontal_shareEnable'] == '1' ? 'checked="checked"' : '' ?> /> <?php _e( 'Yes', 'LoginApp' ) ?>
                                </div>
                                <input type="radio" name="LoginApp_settings[horizontal_shareEnable]" value="0" <?php echo isset( $loginAppSettings['horizontal_shareEnable'] ) && $loginAppSettings['horizontal_shareEnable'] == '0' ? 'checked="checked"' : '' ?> /> <?php _e( 'No', 'LoginApp' ) ?>
                                <div class="loginAppBorder2"></div>

                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'Select your Social Sharing Interface:', 'LoginApp' ); ?>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 7px">
                                        <input class="horizontalSharingThemesTop" <?php echo ( isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == '32' ) || !isset( $loginAppSettings['horizontalSharing_theme'] ) ? 'checked="checked"' : '' ?> type="radio" id="login_app_sharing_top_32" name="LoginApp_settings[horizontalSharing_theme]" value='32' />
                                    </span>
                                    <label for="login_app_sharing_top_32">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/horizonSharing32.png'; ?>" align="left" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 1px;">
                                        <input class="horizontalSharingThemesTop" <?php echo isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == '16' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[horizontalSharing_theme]" id="login_app_sharing_top_16" value='16' />
                                    </span>
                                    <label for="login_app_sharing_top_16">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/horizonSharing16.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 3px;">
                                        <input class="horizontalSharingSingle" <?php echo isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == 'single_large' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[horizontalSharing_theme]" value='single_large' id="login_app_sharing_top_slarge" />
                                    </span>
                                    <label for="login_app_sharing_top_slarge">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/single-image-theme-large.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 1px;">
                                        <input class="horizontalSharingSingle" <?php echo isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == 'single_small' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[horizontalSharing_theme]" id="login_app_sharing_top_ssmall" value='single_small' />
                                    </span>
                                    <label for="login_app_sharing_top_ssmall">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/single-image-theme-small.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 20px;">
                                        <input class="horizontalCounters" <?php echo isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == 'counter_vertical' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[horizontalSharing_theme]" id="login_app_counter_top_vertical" value='counter_vertical' />
                                    </span>
                                    <label for="login_app_counter_top_vertical">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/counter/hybrid-horizontal-vertical.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="login_app_select_row">
                                    <span class="radio" style="margin-top: 6px;">
                                        <input class="horizontalCounters" <?php echo isset( $loginAppSettings['horizontalSharing_theme'] ) && $loginAppSettings['horizontalSharing_theme'] == 'counter_horizontal' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[horizontalSharing_theme]" id="login_app_counter_top_horizontal" value='counter_horizontal' />
                                    </span>
                                    <label for="login_app_counter_top_horizontal">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/counter/hybrid-horizontal-horizontal.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class="loginAppBorder2"></div>

                                <div id="login_app_horizontal_providers_container">
                                    <div class="loginAppQuestion" style="margin-top:10px">
                                        <?php _e( 'What sharing networks do you want to show in the sharing interface? ( All other sharing networks will be shown as part of LoginRadius sharing icon ) ', 'LoginApp' ) ?>
                                    </div>
                                    <div id="loginAppHorizontalSharingLimit"><?php _e( 'You can select only nine providers', 'LoginApp' ) ?>.</div>
                                    <div style="width:420px" id="login_app_horizontal_sharing_providers_container"></div>
                                    <div style="width:600px; display: none;" id="login_app_horizontal_counter_providers_container"></div>
                                </div>

                                <div id="login_app_horizontal_rearrange_container">
                                    <div class="loginAppBorder2"></div>

                                    <div class="loginAppQuestion" style="margin-top:10px">
                                        <?php _e( 'What sharing network order do you prefer for your sharing interface? Drag the icons around to set the order', 'LoginApp' ) ?>
                                    </div>
                                    <ul id="loginAppHorizontalSortable">
                                        <?php
                                        if ( isset( $loginAppSettings['horizontal_rearrange_providers'] ) && count( $loginAppSettings['horizontal_rearrange_providers'] ) > 0 ) {
                                            foreach ( $loginAppSettings['horizontal_rearrange_providers'] as $provider ) {
                                                ?>
                                                <li title="<?php echo $provider ?>" id="loginRadiusHorizontalLI<?php echo $provider ?>" class="lrshare_iconsprite32 lrshare_<?php echo strtolower( $provider ) ?>">
                                                    <input type="hidden" name="LoginApp_settings[horizontal_rearrange_providers][]" value="<?php echo $provider ?>" />
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="loginAppBorder2"></div>
                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'Select the position of the social sharing interface', 'LoginApp' ); ?>
                                </div>
                                <input type="checkbox" name="LoginApp_settings[horizontal_shareTop]" value='1' <?php echo isset( $loginAppSettings['horizontal_shareTop'] ) && $loginAppSettings['horizontal_shareTop'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show at the top of content', 'LoginApp' ); ?> <br />
                                <input type="checkbox" name="LoginApp_settings[horizontal_shareBottom]" value='1' <?php echo isset( $loginAppSettings['horizontal_shareBottom'] ) && $loginAppSettings['horizontal_shareBottom'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show at the bottom of content', 'LoginApp' ); ?>                         <div class="loginAppBorder2"></div>

                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'What location do you want to show the social sharing interface?', 'LoginApp' ); ?>
                                </div>
                                <input type="checkbox" name="LoginApp_settings[horizontal_sharehome]" value='1' <?php echo isset( $loginAppSettings['horizontal_sharehome'] ) && $loginAppSettings['horizontal_sharehome'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Home Page\'', 'LoginApp' ); ?> <br />
                                <input type="checkbox" name="LoginApp_settings[horizontal_sharepost]" value='1' <?php echo isset( $loginAppSettings['horizontal_sharepost'] ) && $loginAppSettings['horizontal_sharepost'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Posts\'', 'LoginApp' ); ?>
                                <br />
                                <input type="checkbox" name="LoginApp_settings[horizontal_sharepage]" value='1' <?php echo isset( $loginAppSettings['horizontal_sharepage'] ) && $loginAppSettings['horizontal_sharepage'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Pages\'', 'LoginApp' ); ?> <br />
                                <input type="checkbox" name="LoginApp_settings[horizontal_shareexcerpt]" value='1' <?php echo isset( $loginAppSettings['horizontal_shareexcerpt'] ) && $loginAppSettings['horizontal_shareexcerpt'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Post Excerpts\' ', 'LoginApp' ); ?> <br />
                            </div>
                        </td>
                    </tr>
                    <tr id="login_app_vertical" style="display:none">
                        <td>
                            <span class="lrsharing_spanwhite" style="margin-left:80px"></span>
                            <span class="lrsharing_spangrey" style="margin-left:80px"></span>
                            <div style="border:1px solid #ccc; padding:10px; border-radius:5px">
                                <div class="loginAppQuestion">
                                    <?php _e( 'Do you want to enable vertical social sharing for your website?', 'LoginApp' ); ?>
                                </div>
                                <div class="loginAppYesRadio">
                                    <input type="radio" name="LoginApp_settings[vertical_shareEnable]" value='1' <?php echo!isset( $loginAppSettings['vertical_shareEnable'] ) || $loginAppSettings['vertical_shareEnable'] == '1' ? 'checked="checked"' : '' ?> /> <?php _e( 'Yes', 'LoginApp' ) ?>
                                </div>
                                <input type="radio" name="LoginApp_settings[vertical_shareEnable]" value="0" <?php echo isset( $loginAppSettings['vertical_shareEnable'] ) && $loginAppSettings['vertical_shareEnable'] == '0' ? 'checked="checked"' : '' ?> /> <?php _e( 'No', 'LoginApp' ) ?>
                                <div class="loginAppBorder2"></div>

                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'Select your Social Sharing Interface:', 'LoginApp' ); ?>
                                </div>
                                <div class = "images-wrapper">
                                    <input class="verticalSharingThemesTop" <?php echo ( isset( $loginAppSettings['verticalSharing_theme'] ) && $loginAppSettings['verticalSharing_theme'] == '32' ) || !isset( $loginAppSettings['verticalSharing_theme'] ) ? 'checked="checked"' : '' ?> type="radio" id="login_app_sharing_vertical_32" name="LoginApp_settings[verticalSharing_theme]" value='32' />
                                    <label for="login_app_sharing_vertical_32">
                                        <img src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/vertical/32VerticlewithBox.png'; ?>" align="left" />
                                    </label>
                                    <div class="clear"></div>
                                </div>
                                <div class = "images-wrapper">
                                    <input class="verticalSharingThemesTop" <?php echo isset( $loginAppSettings['verticalSharing_theme'] ) && $loginAppSettings['verticalSharing_theme'] == '16' ? 'checked="checked"' : '' ?> style="float:left" type="radio" name="LoginApp_settings[verticalSharing_theme]" id="login_app_sharing_vertical_16" value='16' />
                                    <label for="login_app_sharing_vertical_16">
                                        <img style = "margin-left: 10px;" src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/sharing/vertical/16VerticlewithBox.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class = "images-wrapper">
                                    <input class="verticalCounters" style = "margin-left: 20px !important;" <?php echo isset( $loginAppSettings['verticalSharing_theme'] ) && $loginAppSettings['verticalSharing_theme'] == 'counter_vertical' ? 'checked="checked"' : '' ?> style="float:left" type="radio" name="LoginApp_settings[verticalSharing_theme]" id="login_app_counter_vertical_vertical" value='counter_vertical' />
                                    <label for="login_app_counter_vertical_vertical">
                                        <img style = "margin-left: 10px;" src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/counter/hybrid-verticle-vertical.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>

                                <div class = "images-wrapper">
                                    <input class="verticalCounters" style = "margin-left: 20px!important; float:left;" <?php echo isset( $loginAppSettings['verticalSharing_theme'] ) && $loginAppSettings['verticalSharing_theme'] == 'counter_horizontal' ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[verticalSharing_theme]" id="login_app_counter_vertical_horizontal" value='counter_horizontal' />
                                    <label for="login_app_counter_vertical_horizontal">
                                        <img style ="margin-left: 10px;" src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/counter/hybrid-verticle-horizontal.png'; ?>" />
                                    </label>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                                <div id="login_app_vertical_providers_container">
                                    <div class="loginAppBorder2"></div>
                                    <div class="loginAppQuestion" style="margin-top:10px">
                                        <?php _e( 'What sharing networks do you want to show in the sharing interface? ( All other sharing networks will be shown as part of LoginRadius sharing icon ) ', 'LoginApp' ) ?>
                                    </div>
                                    <div id="loginAppVerticalSharingLimit" style="color:red; display:none; margin-bottom: 5px;"><?php _e( 'You can select only nine providers', 'LoginApp' ) ?>.</div>
                                    <div id="login_app_vertical_sharing_providers_container"></div>
                                    <div id="login_app_vertical_counter_providers_container"></div>
                                </div>

                                <div id="login_app_vertical_rearrange_container">
                                    <div class="loginAppBorder2"></div>

                                    <div class="loginAppQuestion" style="margin-top:10px">
                                        <?php _e( 'What sharing network order do you prefer for your sharing interface? Drag the icons around to set the order', 'LoginApp' ) ?>
                                    </div>
                                    <ul id="loginAppVerticalSortable">
                                        <?php
                                        if ( isset( $loginAppSettings['vertical_rearrange_providers'] ) && count( $loginAppSettings['vertical_rearrange_providers'] ) > 0 ) {
                                            foreach ( $loginAppSettings['vertical_rearrange_providers'] as $provider ) {
                                                ?>
                                                <li title="<?php echo $provider ?>" id="loginRadiusVerticalLI<?php echo $provider ?>" class="lrshare_iconsprite32 lrshare_<?php echo strtolower( $provider ) ?>">
                                                    <input type="hidden" name="LoginApp_settings[vertical_rearrange_providers][]" value="<?php echo $provider ?>" />
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="loginAppBorder2"></div>
                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'Select the position of the social sharing interface', 'LoginApp' ); ?>
                                </div>
                                <div class="loginAppProviders">
                                    <input <?php echo ( isset( $loginAppSettings['sharing_verticalPosition'] ) && $loginAppSettings['sharing_verticalPosition'] == 'top_left' ) || !isset( $loginAppSettings['sharing_verticalPosition'] ) ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[sharing_verticalPosition]" value="top_left" /> <label><?php _e( "Top left", 'LoginApp' ); ?></label>
                                </div>
                                <div class="loginAppProviders">
                                    <input  <?php echo ( isset( $loginAppSettings['sharing_verticalPosition'] ) && $loginAppSettings['sharing_verticalPosition'] == 'top_right' ) ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[sharing_verticalPosition]" value="top_right" /> <label><?php _e( "Top Right", 'LoginApp' ); ?></label>
                                </div>
                                <div class="loginAppProviders">
                                    <input <?php echo ( isset( $loginAppSettings['sharing_verticalPosition'] ) && $loginAppSettings['sharing_verticalPosition'] == 'bottom_left' ) ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[sharing_verticalPosition]" value="bottom_left" /> <label><?php _e( "Bottom Left", 'LoginApp' ); ?></label>
                                </div>
                                <div class="loginAppProviders">
                                    <input <?php echo ( isset( $loginAppSettings['sharing_verticalPosition'] ) && $loginAppSettings['sharing_verticalPosition'] == 'bottom_right' ) ? 'checked="checked"' : '' ?> type="radio" name="LoginApp_settings[sharing_verticalPosition]" value="bottom_right" /> <label><?php _e( "Bottom Right", 'LoginApp' ); ?></label>
                                </div>
                                <div class="loginAppBorder2"></div>
                                <div style="clear:both"></div>
                                <div class="loginAppQuestion" style="margin-top:10px">
                                    <?php _e( 'What location do you want to show the social sharing interface?', 'LoginApp' ); ?>
                                </div>
                                <input type="checkbox" name="LoginApp_settings[vertical_sharehome]" value='1' <?php echo isset( $loginAppSettings['vertical_sharehome'] ) && $loginAppSettings['vertical_sharehome'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Home Page\'', 'LoginApp' ); ?> <br />
                                <input type="checkbox" name="LoginApp_settings[vertical_sharepost]" value='1' <?php echo isset( $loginAppSettings['vertical_sharepost'] ) && $loginAppSettings['vertical_sharepost'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Posts\'', 'LoginApp' ); ?>
                                <br />
                                <input type="checkbox" name="LoginApp_settings[vertical_sharepage]" value='1' <?php echo isset( $loginAppSettings['vertical_sharepage'] ) && $loginAppSettings['vertical_sharepage'] == 1 ? 'checked' : '' ?>/> <?php _e( 'Show on \'Pages\'', 'LoginApp' ); ?> <br />
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}
