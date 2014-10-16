<?php
if ( ! class_exists( 'Admin_Helper_LA' ) ) {

	class Admin_Helper_LA {

		/*
		 * Display notice on plugin page, if LR API Key and Secret are empty
		 */
		public static function display_notice_to_insert_api_and_secret() {
			?>
			<div id="loginAppKeySecretNotification"
			     style="background-color: #FFFFE0; border:1px solid #E6DB55; padding:5px; margin-bottom:5px; width: 1050px;">
				<?php _e( 'To activate the <strong>Social Login</strong>, insert LoginApp API Key and Secret in the <strong>API Settings</strong> section below. <strong>Social Sharing does not require API Key and Secret</strong>.',
					'LoginApp' ); ?>
			</div>
		<?php
		}

		/**
		 * Check if LoginApp API Key and Secret are saved
		 *
		 * global $loginAppSettings
		 */
		public static function loginapp_api_secret_saved() {
			global $loginAppSettings;
			if ( ! isset( $loginAppSettings['LoginApp_apikey'] ) || trim( $loginAppSettings['LoginApp_apikey'] ) == '' || ! isset( $loginAppSettings['LoginApp_secret'] ) || trim( $loginAppSettings['LoginApp_secret'] ) == '' ) {
				return false;
			}

			return true;
		}

		/**
		 * Add provider column on users list page
		 *
		 * global $loginAppSettings
		 */
		public static function add_provider_column_in_users_list( $columns ) {
			global $loginAppSettings;
			if ( isset( $loginAppSettings['LoginApp_noProvider'] ) && $loginAppSettings['LoginApp_noProvider'] == '1' ) {
				$columns['loginapp_provider'] = 'LoginApp Provider';
			}
			if ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == '1' ) {
				// Add active/inactive Staus column on users list page
				$columns['loginapp_status'] = 'Status';
			}

			return $columns;
		}

		/**
		 * show social ID provider in the provider column
		 *
		 * global $loginAppSettings
		 */
		public static function login_app_show_provider( $value, $columnName, $userId ) {
			global $loginAppSettings;
			if ( isset( $loginAppSettings['LoginApp_noProvider'] ) && $loginAppSettings['LoginApp_noProvider'] == '1' ) {
				$lrProviderMeta = get_user_meta( $userId, 'loginapp_provider', true );
				$lrProvider     = ( $lrProviderMeta == false ) ? '-' : $lrProviderMeta;
				if ( 'loginapp_provider' == $columnName ) {
					return ucfirst( $lrProvider );
				}
			}
			if ( isset( $loginAppSettings['LoginApp_enableUserActivation'] ) && $loginAppSettings['LoginApp_enableUserActivation'] == '1' ) {
				if ( $userId == 1 ) {
					return;
				}
				if ( ( $lrStatus = get_user_meta( $userId, 'loginapp_status', true ) ) == '' || $lrStatus == '1' ) {
					$lrStatus = '1';
				} else {
					$lrStatus = '0';
				}
				if ( 'loginapp_status' == $columnName ) {
					if ( $lrStatus == '1' ) {
						return '<span id="loginRadiusStatus' . $userId . '"><a alt="Active ( Click to Disable ) " title="Active ( Click to Disable ) " href="javascript:void ( 0 ) " onclick="loginAppChangeStatus ( ' . $userId . ', ' . $lrStatus . ' ) " ><img height="20" width="20" src="' . LOGINAPP_PLUGIN_URL . 'assets/images/enable.png' . '" /></a></span>';
					} else {
						return '<span id="loginRadiusStatus' . $userId . '"><a alt="Inactive ( Click to Enable ) " title="Inactive ( Click to Enable ) " href="javascript:void ( 0 ) " onclick="loginAppChangeStatus ( ' . $userId . ', ' . $lrStatus . ' ) " ><img height="20" width="20" src="' . LOGINAPP_PLUGIN_URL . 'assets/images/disable.png' . '" /></a></span>';
					}
				}
			}
		}

		/**
		 * add javascript on users.php in admin for ajax call to activate/deactivate users
		 *
		 * global $parent_file;
		 */
		public static function add_script_for_users_page() {
			global $parent_file;
			if ( $parent_file == 'users.php' ) {
				?>
				<script type="text/javascript">
					function loginAppChangeStatus(userId, currentStatus) {
						jQuery('#loginRadiusStatus' + userId).html('<img width="20" height="20" title="<?php _e( 'Please wait', 'LoginApp' ) ?>..." src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/loading_icon.gif'; ?>" />');
						jQuery.ajax({
							type: 'POST',
							url: '<?php echo get_admin_url() ?>admin-ajax.php',
							data: {
								action: 'login_app_change_user_status',
								user_id: userId,
								current_status: currentStatus
							},
							success: function (data) {
								if (data == 'done') {
									if (currentStatus == 0) {
										jQuery('#loginRadiusStatus' + userId).html('<span id="loginRadiusStatus' + userId + '"><a href="javascript:void ( 0 ) " alt="<?php _e( 'Active ( Click to Disable ) ', 'LoginApp' ) ?>" title="<?php _e( 'Active ( Click to Disable ) ', 'LoginApp' ) ?>" onclick="loginAppChangeStatus ( ' + userId + ', 1 ) " ><img width="20" height="20" src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/enable.png'; ?>" /></a></span>');
									} else if (currentStatus == 1) {
										jQuery('#loginRadiusStatus' + userId).html('<span id="loginRadiusStatus' + userId + '"><a href="javascript:void ( 0 ) " alt="<?php _e( 'Inactive ( Click to Enable ) ', 'LoginApp' ) ?>" title="<?php _e( 'Inactive ( Click to Enable ) ', 'LoginApp' ) ?>" onclick="loginAppChangeStatus ( ' + userId + ', 0 ) " ><img width="20" height="20" src="<?php echo LOGINAPP_PLUGIN_URL . 'assets/images/disable.png'; ?>" /></a></span>');
									}
								} else if (data == 'error') {
									jQuery('#loginRadiusStatus' + userId).html('<span id="loginRadiusStatus' + userId + '"><a href="javascript:void ( 0 ) " alt="<?php _e( 'Active ( Click to Disable ) ', 'LoginApp' ) ?>" title="<?php _e( 'Active ( Click to Disable ) ', 'LoginApp' ) ?>" onclick="loginAppChangeStatus ( ' + userId + ', 1 ) " ><img width="20" height="20" src="<?php echo plugins_url( 'images/enable.png', __FILE__ ) ?>" /></a></span>');
								}
							},
							error: function (xhr, textStatus, errorThrown) {

							}
						});
					}
				</script>
			<?php
			}
		}

		/**
		 * Function for adding script to display theme specific settings dynamically.
		 */
		public static function render_admin_ui_script() {
			?>
			<script type="text/javascript">var islrsharing = true;
				var islrsocialcounter = true;</script>
			<script type="text/javascript" src='//share.loginradius.com/Content/js/LoginRadius.js'></script>
			<script type="text/javascript">

				function loginAppAdminUI2() {
					var selectedHorizontalSharingProviders = <?php echo Admin_Helper_LA:: get_sharing_providers_josn_arrays( 'horizontal' ); ?>;
					var selectedVerticalSharingProviders = <?php echo Admin_Helper_LA:: get_sharing_providers_josn_arrays( 'vertical' ); ?>;
					var selectedHorizontalCounterProviders = <?php echo Admin_Helper_LA:: get_counters_providers_json_array( 'horizontal' ); ?>;
					var selectedVerticalCounterProviders = <?php echo Admin_Helper_LA:: get_counters_providers_json_array( 'vertical' ); ?>;

					var loginAppSharingHtml = '';
					var checked = false;

					// prepare HTML to be shown as Horizontal Sharing Providers
					for (var i = 0; i < $SS.Providers.More.length; i++) {
						checked = loginAppCheckElement(selectedHorizontalSharingProviders, $SS.Providers.More[i]);
						loginAppSharingHtml += '<div class="loginAppProviders"><input type="checkbox" onchange="loginAppSharingLimit( this, \'horizontal\' ); loginAppRearrangeProviderList( this, \'Horizontal\' ) " ';
						if (checked) {
							loginAppSharingHtml += 'checked="' + checked + '" ';
						}
						loginAppSharingHtml += 'name="LoginApp_settings[horizontal_sharing_providers][]" value="' + $SS.Providers.More[i] + '"> <label>' + $SS.Providers.More[i] + '</label></div>';
					}

					// show horizontal sharing providers list
					jQuery('#login_app_horizontal_sharing_providers_container').html(loginAppSharingHtml);

					loginAppSharingHtml = '';
					checked = false;
					// prepare HTML to be shown as Vertical Sharing Providers
					for (var i = 0; i < $SS.Providers.More.length; i++) {
						checked = loginAppCheckElement(selectedVerticalSharingProviders, $SS.Providers.More[i]);
						loginAppSharingHtml += '<div class="loginAppProviders"><input type="checkbox" onchange="loginAppSharingLimit( this, \'vertical\' ); loginAppRearrangeProviderList( this, \'Vertical\' ) " ';
						if (checked) {
							loginAppSharingHtml += 'checked="' + checked + '" ';
						}
						loginAppSharingHtml += 'name="LoginApp_settings[vertical_sharing_providers][]" value="' + $SS.Providers.More[i] + '"> <label>' + $SS.Providers.More[i] + '</label></div>';
					}
					// show vertical sharing providers list
					jQuery('#login_app_vertical_sharing_providers_container').html(loginAppSharingHtml);
					loginAppSharingHtml = '';
					checked = false;
					// prepare HTML to be shown as Horizontal Counter Providers
					for (var i = 0; i < $SC.Providers.All.length; i++) {
						checked = loginAppCheckElement(selectedHorizontalCounterProviders, $SC.Providers.All[i]);
						loginAppSharingHtml += '<div class="loginRadiusCounterProviders"><input type="checkbox" ';
						if (checked) {
							loginAppSharingHtml += 'checked="' + checked + '" ';
						}
						loginAppSharingHtml += 'name="LoginApp_settings[horizontal_counter_providers][]" value="' + $SC.Providers.All[i] + '"> <label>' + $SC.Providers.All[i] + '</label></div>';
					}
					// show horizontal counter providers list
					jQuery('#login_app_horizontal_counter_providers_container').html(loginAppSharingHtml);

					loginAppSharingHtml = '';
					checked = false;
					// prepare HTML to be shown as Vertical Counter Providers
					for (var i = 0; i < $SC.Providers.All.length; i++) {
						checked = loginAppCheckElement(selectedVerticalCounterProviders, $SC.Providers.All[i]);
						loginAppSharingHtml += '<div class="loginRadiusCounterProviders"><input type="checkbox" ';
						if (checked) {
							loginAppSharingHtml += 'checked="' + checked + '" ';
						}
						loginAppSharingHtml += 'name="LoginApp_settings[vertical_counter_providers][]" value="' + $SC.Providers.All[i] + '"> <label>' + $SC.Providers.All[i] + '</label></div>';
					}
					// show vertical counter providers list
					jQuery('#login_app_vertical_counter_providers_container').html(loginAppSharingHtml);
				}
			</script>
		<?php
		}

		/**
		 * function returns json array of sharing providers on the basis of theme( provided as argument).
		 *
		 * global $loginAppSettings
		 */
		public static function get_sharing_providers_josn_arrays( $themeType ) {
			global $loginAppSettings;

			switch ( $themeType ) {
				case 'vertical':
					if ( isset( $loginAppSettings['vertical_rearrange_providers'] ) && is_array( $loginAppSettings['vertical_rearrange_providers'] ) && count( $loginAppSettings['vertical_rearrange_providers'] ) > 0 ) {
						return json_encode( $loginAppSettings['vertical_rearrange_providers'] );
					} else {
						return self:: get_default_sharing_providers_josn_array();
					}
					break;

				case 'horizontal':
					if ( isset( $loginAppSettings['horizontal_rearrange_providers'] ) && is_array( $loginAppSettings['horizontal_rearrange_providers'] ) && count( $loginAppSettings['horizontal_rearrange_providers'] ) > 0 ) {
						return json_encode( $loginAppSettings['horizontal_rearrange_providers'] );
					} else {
						return self:: get_default_sharing_providers_josn_array();
					}
					break;
			}
		}

		/**
		 * function returns json array of counter providers on the basis of theme( provided as argument).
		 *
		 * global $loginAppSettings;
		 */
		public static function get_counters_providers_json_array( $themeType ) {
			global $loginAppSettings;

			switch ( $themeType ) {
				case 'horizontal':
					if ( isset( $loginAppSettings['horizontal_counter_providers'] ) && is_array( $loginAppSettings['horizontal_counter_providers'] ) && count( $loginAppSettings['vertical_rearrange_providers'] ) > 0 ) {
						return json_encode( $loginAppSettings['horizontal_counter_providers'] );
					} else {
						return self:: get_default_counters_providers_josn_array();
					}
					break;

				case 'vertical':
					if ( isset( $loginAppSettings['vertical_counter_providers'] ) && is_array( $loginAppSettings['vertical_counter_providers'] ) && count( $loginAppSettings['vertical_rearrange_providers'] ) > 0 ) {
						return json_encode( $loginAppSettings['vertical_counter_providers'] );
					} else {
						return self:: get_default_counters_providers_josn_array();
					}
					break;
			}
		}

		/**
		 * function returns default json array of sharing providers.
		 */
		public static function get_default_sharing_providers_josn_array() {
			return '["Facebook", "Twitter", "Pinterest", "Email", "Print"]';
		}

		/**
		 * function returns default json array of counter providers.
		 */
		public static function get_default_counters_providers_josn_array() {
			return '["Facebook Like", "Google+ +1", "Pinterest Pin it", "LinkedIn Share", "Hybridshare"]';
		}

		/**
		 * Encoding LoginApp Plugin settings
		 */
		public static function get_encoded_settings_string( $loginAppSettings ) {

			$string = '~' . '2|';
			$string .= $loginAppSettings['LoginApp_redirect'] . '|';
			if ( $loginAppSettings['LoginApp_redirect'] == "custom" ) {
				$string .= $loginAppSettings['custom_redirect'] . '|';
			}
			$string .= $loginAppSettings['LoginApp_regRedirect'] . '|';
			if ( $loginAppSettings['LoginApp_regRedirect'] == "custom" ) {
				$string .= $loginAppSettings['custom_regRedirect'] . '|';
			}
			$string .= $loginAppSettings['LoginApp_loutRedirect'] . '|';
			if ( $loginAppSettings['LoginApp_loutRedirect'] == "custom" ) {
				$string .= $loginAppSettings['custom_loutRedirect'] . '|';
			}

			$string .= '~3|';
			$string .= $loginAppSettings['horizontal_shareEnable'] . '|';
			$string .= isset( $loginAppSettings['horizontalSharing_theme'] ) ? $loginAppSettings['horizontalSharing_theme'] : '32';
			//generating string for horizontal sharing providers, counter providers and rearrange providers
			$string .= self:: get_horizontal_networks_providers( $loginAppSettings );
			$string .= isset( $loginAppSettings['horizontal_shareTop'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['horizontal_shareBottom'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['horizontal_sharehome'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['horizontal_sharepost'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['horizontal_sharepage'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['horizontal_shareexcerpt'] ) ? '1|' : '0|';
			//Starting Vertical Sharing string encodeing
			$string .= $loginAppSettings['vertical_shareEnable'] . '|' . $loginAppSettings['verticalSharing_theme'];
			//generating string for vertical sharing providers, counter providers and reaarange providers
			$string .= self:: get_vertical_networks_providers( $loginAppSettings );
			$string .= '|' . $loginAppSettings['sharing_verticalPosition'] . '|';
			$string .= isset( $loginAppSettings['vertical_sharehome'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['vertical_sharepost'] ) ? '1|' : '0|';
			$string .= isset( $loginAppSettings['vertical_sharepage'] ) ? '1|' : '0|';

			$string .= '~4|';
			$string .= $loginAppSettings['LoginApp_commentEnable'] . '|' . $loginAppSettings['LoginApp_commentInterfacePosition'] . '|';

			$string .= '~5|';
			$string .= $loginAppSettings['LoginApp_title'] . '|';
			$string .= isset( $loginAppSettings['LoginApp_interfaceSize'] ) ? $loginAppSettings['LoginApp_numColumns'] . '|' : ' ' . '|';
			if ( isset( $loginAppSettings['LoginApp_backgroundColor'] ) && ! empty( $loginAppSettings['LoginApp_backgroundColor'] ) ) {
				$string .= $loginAppSettings['LoginApp_backgroundColor'] . '|';
			}
			$string .= $loginAppSettings['LoginApp_loginform'] . '|' . $loginAppSettings['LoginApp_regform'] . '|' . $loginAppSettings['LoginApp_regformPosition'] . '|';
			$string .= $loginAppSettings['scripts_in_footer'] . '|' . $loginAppSettings['LoginApp_sendemail'] . '|';
			$string .= $loginAppSettings['msg_email'] . '|' . $loginAppSettings['msg_existemail'] . '|';
			$string .= $loginAppSettings['username_separator'] . '|';
			$string .= $loginAppSettings['LoginApp_enableUserActivation'] . '|' . $loginAppSettings['LoginApp_defaultUserStatus'] . '|';
			$string .= $loginAppSettings['LoginApp_noProvider'] . '|';
			$string .= $loginAppSettings['profileDataUpdate'] . '|' . $loginAppSettings['LoginApp_socialavatar'] . '|';
			$string .= $loginAppSettings['LoginApp_socialLinking'] . '|';
			$string .= $loginAppSettings['enable_degugging'] . '|' . $loginAppSettings['delete_options'] . '|';

			return $string;

		}

		/**
		 * Get comma seperated horizontal network providers
		 */
		public static function get_horizontal_networks_providers( $loginAppSettings ) {
			$string = '';
			if ( isset( $loginAppSettings['horizontal_sharing_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['horizontal_sharing_providers'] );
			}
			if ( isset( $loginAppSettings['horizontal_counter_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['horizontal_counter_providers'] );
			}
			if ( isset( $loginAppSettings['horizontal_rearrange_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['horizontal_rearrange_providers'] );
			}

			return $string . '|';
		}

		/**
		 * Get comma seperated vertical network providers
		 */
		public static function get_vertical_networks_providers( $loginAppSettings ) {
			$string = '';

			if ( isset( $loginAppSettings['vertical_sharing_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['vertical_sharing_providers'] );
			}
			if ( isset( $loginAppSettings['vertical_counter_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['vertical_counter_providers'] );
			}
			if ( isset( $loginAppSettings['vertical_rearrange_providers'] ) ) {
				$string .= self:: imploading_arrays( $loginAppSettings['vertical_rearrange_providers'] );
			}

			return $string . '|';
		}

		/**
		 * Changing array to comma seperated string
		 */
		public static function imploading_arrays( $array ) {
			$string = '|["' . implode( '","', $array ) . '"]';

			return $string;
		}

		/**
		 * Display error notice to admin.
		 */
		public static function display_admin_settings_errors() {
			settings_errors( 'LoginApp_settings' );
		}

		/**
		 * This function return checked="checked" if LoginApp setting $optionName is the value of  $tempArray[$settingName],
		 * else return blank string
		 *
		 * @global $loginAppSettings
		 */
		public static function is_radio_checked( $settingName, $optionName ) {
			global $loginAppSettings;

			$tempArray = array(
				'login'       => 'LoginApp_redirect',
				'register'    => 'LoginApp_regRedirect',
				'avatar'      => 'LoginApp_socialavatar',
				'seperator'   => 'username_separator',
				'send_email'  => 'LoginApp_sendemail',
				'dummy_email' => 'LoginApp_dummyemail',
				'logoutUrl'   => 'LoginApp_loutRedirect'
			);

			if ( $loginAppSettings[ $tempArray[ $settingName ] ] == $optionName ) {
				return 'checked="checked"';
			} else {
				return '';
			}
		}

	}

}