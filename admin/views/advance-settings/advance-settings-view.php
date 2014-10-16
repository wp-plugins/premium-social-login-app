<?php

/**
 * Function for rendering Advance Settings tab on plugin on settings page
 */
function login_app_render_advance_settings_options( $loginAppSettings ) {
	?>
	<div class="menu_containt_div" id="tabs-5">

	<!-- Social Login Interface Customization -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Social Login Interface Customization', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'What text do you want to display above the Social Login interface?',
								'LoginApp' ); ?></div>
						<input type="text" name="LoginApp_settings[LoginApp_title]" size="60"
						       value="<?php echo htmlspecialchars( $loginAppSettings['LoginApp_title'] ); ?>"/>

						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Select the icon size to use in the Social Login interface',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input type="radio" name="LoginApp_settings[LoginApp_interfaceSize]"
							       value='large' <?php echo ( ! isset( $loginAppSettings['LoginApp_interfaceSize'] ) || $loginAppSettings['LoginApp_interfaceSize'] == 'large' ) ? 'checked' : ''; ?>/>
							<label><?php _e( 'Large', 'LoginApp' ); ?></label>
						</div>
						<div>
							<input type="radio" name="LoginApp_settings[LoginApp_interfaceSize]"
							       value="small" <?php echo ( isset( $loginAppSettings['LoginApp_interfaceSize'] ) && $loginAppSettings['LoginApp_interfaceSize'] == 'small' ) ? 'checked' : ''; ?>/>
							<label><?php _e( 'Small', 'LoginApp' ); ?></label>
						</div>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'How many social icons would you like to be displayed per row?',
								'LoginApp' ); ?></div>
						<input type="text" name="LoginApp_settings[LoginApp_numColumns]" style="width:50px"
						       maxlength="2" value="<?php
						if ( isset( $loginAppSettings['LoginApp_numColumns'] ) ) {
							echo sanitize_text_field( trim( $loginAppSettings['LoginApp_numColumns'] ) );
						}
						?>"/>

						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'What background color would you like to use for the Social Login interface?',
								'LoginApp' ); ?></div>
						<?php
						if ( isset( $loginAppSettings['LoginApp_backgroundColor'] ) ) {
							$colorValue = esc_html( trim( $loginAppSettings['LoginApp_backgroundColor'] ) );
						} else {
							$colorValue = '';
						}
						?>
						<input type="text" name="LoginApp_settings[LoginApp_backgroundColor]"
						       value="<?php echo $colorValue; ?>"/>

						<div class="loginAppBorder"></div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Social Login Interface Display Settings -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Social Login Interface Display Settings', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to show the Social Login interface on your WordPress login page?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input type="radio" class="login_app_show_on_login"
							       name="LoginApp_settings[LoginApp_loginform]"
							       value='1' <?php echo isset( $loginAppSettings['LoginApp_loginform'] ) && $loginAppSettings['LoginApp_loginform'] == 1 ? 'checked' : ''; ?> />
							<label><?php _e( 'Yes', 'LoginApp' ); ?></label>
						</div>
						<div>
							<input type="radio" class="login_app_show_on_login"
							       name="LoginApp_settings[LoginApp_loginform]"
							       value="0" <?php echo isset( $loginAppSettings['LoginApp_loginform'] ) && $loginAppSettings['LoginApp_loginform'] == 0 ? 'checked' : ''; ?>/>
							<label><?php _e( 'No', 'LoginApp' ); ?></label>
						</div>
						<div class="loginAppBorder"></div>
					</td>
				</tr>

				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to show Social Login interface on your WordPress registration page?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input type="radio" id="showonregistrationpageyes"
							       name="LoginApp_settings[LoginApp_regform]"
							       value='1' <?php echo isset( $loginAppSettings['LoginApp_regform'] ) && $loginAppSettings['LoginApp_regform'] == 1 ? 'checked' : ''; ?>/><?php _e( 'Yes',
								'LoginApp' ); ?>
						</div>
						<input type="radio" id="showonregistrationpageno" name="LoginApp_settings[LoginApp_regform]"
						       value="0" <?php echo isset( $loginAppSettings['LoginApp_regform'] ) && $loginAppSettings['LoginApp_regform'] == 0 ? 'checked' : ''; ?>/><?php _e( 'No',
							'LoginApp' ); ?>

						<div class="loginAppBorder"></div>
					</td>
				</tr>

				<tr id="registration_interface">
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'If Yes, how do you want the Social Login interface to be shown on your wordpress registration page?',
								'LoginApp' ); ?></div>
						<input type="radio" name="LoginApp_settings[LoginApp_regformPosition]"
						       value="embed" <?php echo $loginAppSettings['LoginApp_regformPosition'] == 'embed' ? 'checked = "checked"' : ''; ?>/> <?php _e( 'Show it below the registration form',
							'LoginApp' ); ?><br/>
						<input type="radio" name="LoginApp_settings[LoginApp_regformPosition]"
						       value="beside" <?php echo $loginAppSettings['LoginApp_regformPosition'] == 'beside' ? 'checked = "checked"' : ''; ?>/> <?php _e( 'Show it beside the registration form',
							'LoginApp' ); ?>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want the plugin Javascript code to be included in the footer for faster loading of website content?',
								'LoginApp' ); ?><a style="text-decoration:none" href="javascript:void ( 0 ) "
						                           title="<?php _e( 'It may break the functionality of the plugin if wp_footer and login_footer hooks do not exist in your theme',
							                           'LoginApp' ) ?>"> (?) </a></div>
						<div class="loginAppYesRadio">
							<input type="radio" name="LoginApp_settings[scripts_in_footer]" value='1'
							       checked <?php echo isset( $loginAppSettings['scripts_in_footer'] ) && $loginAppSettings['scripts_in_footer'] == 1 ? 'checked' : ''; ?>/><?php _e( 'Yes',
								'LoginApp' ); ?>
						</div>
						<input type="radio" name="LoginApp_settings[scripts_in_footer]"
						       value="0" <?php echo ! isset( $loginAppSettings['scripts_in_footer'] ) || $loginAppSettings['scripts_in_footer'] == 0 ? 'checked' : ''; ?>/><?php _e( 'No',
							'LoginApp' ); ?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Social Login Email Settings -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Social Login Email Settings', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to send emails to users with their username and password after user registration?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input name="LoginApp_settings[LoginApp_sendemail]" type="radio"
							       value="sendemail" <?php echo Admin_Helper_LA:: is_radio_checked( 'send_email',
								'sendemail' ); ?> /><?php _e( 'Yes', 'LoginApp' ); ?>
						</div>
						<div>
							<input name="LoginApp_settings[LoginApp_sendemail]" type="radio"
							       value="notsendemail" <?php echo Admin_Helper_LA:: is_radio_checked( 'send_email',
								'notsendemail' ); ?> /><?php _e( 'No', 'LoginApp' ); ?>
							<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'A few Social Networks do not supply user email address as part of user profile data. Do you want users to provide their email before completing the registration process?',
								'LoginApp' ); ?></div>
						<input id="dummyMailYes" name="LoginApp_settings[LoginApp_dummyemail]" type="radio"
						       value="notdummyemail" <?php echo ! isset( $loginAppSettings['LoginApp_dummyemail'] ) ? Admin_Helper_LA::is_radio_checked( 'dummy_email',
							'notdummyemail' ) : 'checked="checked"'; ?> /><?php _e( 'Yes, get real email IDs from the users (Ask users to enter their email IDs in a pop-up) ',
							'LoginApp' ); ?> <br/>
						<input id="dummyMailNo" name="LoginApp_settings[LoginApp_dummyemail]" type="radio"
						       value="dummyemail" <?php echo isset( $loginAppSettings['LoginApp_dummyemail'] ) ? Admin_Helper_LA::is_radio_checked( 'dummy_email',
							'dummyemail' ) : ''; ?>/><?php _e( 'No, just auto-generate random email IDs for users',
							'LoginApp' ); ?>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr id="loginAppPopupMessage">
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Please enter the title of the pop-up asking users to enter their email address ',
								'LoginApp' ); ?><a style="text-decoration:none" href="javascript:void ( 0 ) "
						                           title="<?php _e( 'You may use @provider, it will be replaced by the Provider name.',
							                           'LoginApp' ) ?>"> (?) </a></div>
						<?php
						if ( isset( $loginAppSettings['msg_email'] ) && $loginAppSettings['msg_email'] ) {
							$emailMessageValue = htmlspecialchars( trim( $loginAppSettings['msg_email'] ) );
						} else {
							$emailMessageValue = 'Unfortunately we could not retrieve email from your @provider account Please enter your email in the form below in order to continue.';
						}
						?>
						<textarea name="LoginApp_settings[msg_email]" cols="100"
						          rows="3"><?php echo $emailMessageValue; ?></textarea>

						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr id="loginAppPopupErrorMessage">
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Please enter the message to be shown to the user in case of an already registered email',
								'LoginApp' ); ?></div>
						<?php
						if ( isset( $loginAppSettings['msg_existemail'] ) && $loginAppSettings['msg_existemail'] ) {
							$emailExistsMessageValue = htmlspecialchars( trim( $loginAppSettings['msg_existemail'] ) );
						} else {
							$emailExistsMessageValue = 'This email is already registered. Please choose another one or link this account via account linking on your profile page';
						}
						?>
						<textarea name="LoginApp_settings[msg_existemail]" cols="100"
						          rows="3"><?php echo $emailExistsMessageValue; ?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Social Login User Settings -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Social Login User Settings', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'How would you like Username to be created? Select your desired composition rule for Username.',
								'LoginApp' ); ?><a style="text-decoration:none" href="javascript:void ( 0 ) "
						                           title="<?php _e( 'During account creation, it automatically adds a separator between first name and last name of the user',
							                           'LoginApp' ); ?>"> (?) </a></div>
						<input name="LoginApp_settings[username_separator]"
						       type="radio"  <?php echo ! isset( $loginAppSettings['username_separator'] ) ? 'checked="checked"' : Admin_Helper_LA:: is_radio_checked( 'seperator',
							'dash' ); ?> value="dash"/> <?php _e( 'Firstname-Lastname [Ex: John-Doe]', 'LoginApp' ); ?>
						<br/>
						<input name="LoginApp_settings[username_separator]"
						       type="radio"  <?php echo Admin_Helper_LA:: is_radio_checked( 'seperator', 'dot' ); ?>
						       value="dot"/><?php _e( 'Firstname.Lastname [Ex: John.Doe]', 'LoginApp' ); ?><br/>
						<input name="LoginApp_settings[username_separator]"
						       type="radio"  <?php echo Admin_Helper_LA:: is_radio_checked( 'seperator', 'space' ); ?>
						       value='space'/><?php _e( 'Firstname Lastname [Ex: John Doe]', 'LoginApp' ); ?>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to control user activation/deactivation?',
								'LoginApp' ); ?><a style="text-decoration:none" href="javascript:void ( 0 ) "
						                           title="<?php _e( 'You can enable/disable user from Status column on Users page in admin',
							                           'LoginApp' ); ?>"> (?) </a></div>
						<input type="radio" id="controlActivationYes"
						       name="LoginApp_settings[LoginApp_enableUserActivation]"
						       value='1' <?php echo ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == 1 ) ? 'checked' : ''; ?> /> <?php _e( 'Yes, display activate/deactivate option in the ',
							'LoginApp' ) ?> <a href="<?php echo get_admin_url() ?>users.php"
						                       target="_blank"><?php _e( 'User list', 'LoginApp' ); ?></a><br/>
						<input type="radio" id="controlActivationNo"
						       name="LoginApp_settings[LoginApp_enableUserActivation]"
						       value="0" <?php echo ( ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == 0 ) ) || ! isset( $loginAppSettings['LoginApp_enableUserActivation'] ) ? 'checked' : ''; ?> /> <?php _e( 'No',
							'LoginApp' ); ?><br/>

						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr id="loginAppDefaultStatus">
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'What would you like to set as the default status of the user when he/she registers to your website?',
								'LoginApp' ); ?></div>
						<input type="radio" name="LoginApp_settings[LoginApp_defaultUserStatus]"
						       value='1' <?php echo ( ( isset( $loginAppSettings['LoginApp_defaultUserStatus'] ) && $loginAppSettings['LoginApp_defaultUserStatus'] == 1 ) ) || ! isset( $loginAppSettings['LoginApp_defaultUserStatus'] ) ? 'checked' : ''; ?>/> <?php _e( 'Active',
							'LoginApp' ); ?><br/>
						<input type="radio" name="LoginApp_settings[LoginApp_defaultUserStatus]"
						       value="0" <?php echo ( isset( $loginAppSettings['LoginApp_defaultUserStatus'] ) && $loginAppSettings['LoginApp_defaultUserStatus'] == 0 ) ? 'checked' : ''; ?>/> <?php _e( 'Inactive',
							'LoginApp' ); ?>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to display the social network that the user connected with, in the user list',
								'LoginApp' ); ?></div>
						<input type="radio" name="LoginApp_settings[LoginApp_noProvider]"
						       value="1" <?php echo ( $loginAppSettings['LoginApp_noProvider'] == 1 ) ? 'checked' : ''; ?>/> <?php _e( 'Yes, display the social network that the user connected with, in the user list',
							'LoginApp' ); ?><br/>
						<input type="radio" name="LoginApp_settings[LoginApp_noProvider]"
						       value='0' <?php echo ( $loginAppSettings['LoginApp_noProvider'] == 0 ) ? 'checked' : ''; ?>/> <?php _e( 'No, do not display the social network that the user connected with, in the user list',
							'LoginApp' ); ?>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to update User Profile Data in your Wordpress database, every time user logs into your website?',
								'LoginApp' ); ?><a style="text-decoration:none" href="javascript:void ( 0 ) "
						                           title="<?php _e( 'If you disable this option, user profile data will be saved only once when user logs in first time at your website, user profile details will not be updated in your Wordpress database, even if user changes his/her social account details.',
							                           'LoginApp' ); ?>"> (?) </a></div>
						<div class="loginAppYesRadio">
							<input type="radio" name="LoginApp_settings[profileDataUpdate]"
							       value='1' <?php echo ( ! isset( $loginAppSettings['profileDataUpdate'] ) || $loginAppSettings['profileDataUpdate'] == 1 ) ? 'checked' : ''; ?> /> <?php _e( 'Yes',
								'LoginApp' ) ?> <br/>
						</div>
						<div>
							<input type="radio" name="LoginApp_settings[profileDataUpdate]"
							       value="0" <?php echo ( isset( $loginAppSettings['profileDataUpdate'] ) && $loginAppSettings['profileDataUpdate'] == 0 ) ? 'checked' : ''; ?>  /> <?php _e( 'No',
								'LoginApp' ); ?><br/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to let users use their social profile picture as an avatar on your website?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input name="LoginApp_settings[LoginApp_socialavatar]"
							       type="radio"  <?php echo Admin_Helper_LA:: is_radio_checked( 'avatar',
								'socialavatar' ); ?> value="socialavatar"/><?php _e( 'Yes', 'LoginApp' ); ?> <br/>
						</div>
						<div>
							<input name="LoginApp_settings[LoginApp_socialavatar]"
							       type="radio" <?php echo Admin_Helper_LA:: is_radio_checked( 'avatar',
								'defaultavatar' ); ?> value="defaultavatar"/><?php _e( 'No', 'LoginApp' ); ?>
						</div>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( "Do you want to automatically link your existing users' accounts to their social accounts if their WP account email address matches the email address associated with their social account?",
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input type="radio" name="LoginApp_settings[LoginApp_socialLinking]"
							       value='1' <?php echo ( ( isset( $loginAppSettings['LoginApp_socialLinking'] ) && $loginAppSettings['LoginApp_socialLinking'] == 1 ) || ! isset( $loginAppSettings['LoginApp_socialLinking'] ) ) ? 'checked' : ''; ?>/> <?php _e( 'Yes',
								'LoginApp' ); ?>
						</div>
						<div>
							<input type="radio" name="LoginApp_settings[LoginApp_socialLinking]"
							       value="0" <?php checked( '0',
								@$loginAppSettings['LoginApp_socialLinking'] ); ?>/> <?php _e( 'No', 'LoginApp' ); ?>
						</div>
						<div class="loginAppBorder"></div>
					</td>
				</tr>
				<?php
				if ( is_multisite() && is_main_site() ) {
					?>
					<tr>
						<td>
							<div
								class="loginAppQuestion"><?php _e( 'Do you want to apply the same changes when you update plugin settings in the main blog of multisite network?',
									'LoginApp' ); ?></div>
							<input type="radio" name="LoginApp_settings[multisite_config]"
							       value='1' <?php echo ( ( ! isset( $loginAppSettings['multisite_config'] ) ) || ( isset( $loginAppSettings['multisite_config'] ) && $loginAppSettings['multisite_config'] == 1 ) ) ? 'checked' : ''; ?>/> <?php _e( 'Yes, apply the same changes to plugin settings of each blog in the multisite network when I update plugin settings.',
								'LoginApp' ); ?> <br/>
							<input type="radio" name="LoginApp_settings[multisite_config]"
							       value="0" <?php echo ( isset( $loginAppSettings['multisite_config'] ) && $loginAppSettings['multisite_config'] == 0 ) ? 'checked' : ''; ?>/> <?php _e( 'No, do not apply the changes to other blogs when I update plugin settings.',
								'LoginApp' ); ?>
							<div class="loginAppBorder"></div>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
		</div>
	</div>

	<!-- Plugin Debug option. -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Debug', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div class="loginAppQuestion"><?php _e( 'Do you want to enable debugging mode?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input name="LoginApp_settings[enable_degugging]" type="radio"
							       value="1" <?php echo ( $loginAppSettings['enable_degugging'] == 1 ) ? 'checked = "checked"' : ''; ?> /><?php _e( 'Yes',
								'LoginApp' ); ?>
						</div>
						<div>
							<input name="LoginApp_settings[enable_degugging]" type="radio"
							       value="0" <?php echo ( ! isset( $loginAppSettings['enable_degugging'] ) || $loginAppSettings['enable_degugging'] == 0 ) ? 'checked="checked"' : ''; ?>/><?php _e( 'No',
								'LoginApp' ); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- Plugin deletion options -->
	<div class="stuffbox">
		<h3><label><?php _e( 'Plug-in deletion options', 'LoginApp' ); ?></label></h3>

		<div class="inside">
			<table class="form-table editcomment menu_content_table">
				<tr>
					<td>
						<div
							class="loginAppQuestion"><?php _e( 'Do you want to completely remove the plugin settings and options on plugin deletion ( If you choose Yes, then you will not be able to recover settings again ) ?',
								'LoginApp' ); ?></div>
						<div class="loginAppYesRadio">
							<input type="radio" name="LoginApp_settings[delete_options]"
							       value='1' <?php echo ( ! isset( $loginAppSettings['delete_options'] ) || $loginAppSettings['delete_options'] == 1 ) ? 'checked' : ''; ?> /> <?php _e( 'Yes',
								'LoginApp' ) ?> <br/>
						</div>
						<div>
							<input type="radio" name="LoginApp_settings[delete_options]"
							       value="0" <?php echo ( isset( $loginAppSettings['delete_options'] ) && $loginAppSettings['delete_options'] == 0 ) ? 'checked' : ''; ?>  /> <?php _e( 'No',
								'LoginApp' ); ?><br/>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	</div>
<?php
}
