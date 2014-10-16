<?php

// Define LoginApp domain
define( 'LA_DOMAIN', 'api.loginradius.com' );

/**
 * Class for Social Authentication.
 *
 * This is the main class to communicate with LogiRadius Unified Social API. It contains functions for Social Authentication with User Profile Data (Basic and Extended).
 *
 * Copyright 2014 LoginApp Inc. - www.loginapp.io
 *
 * This file is part of the LoginApp SDK package.
 *
 */
class Login_App_SDK {

	/**
	 * LoginApp function - It validate against GUID format of keys.
	 *
	 * @param string $key data to validate.
	 *
	 * @return boolean. If valid - true, else - false.
	 */
	public function validate_key( $key ) {
		if ( empty( $key ) || ! preg_match( '/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i',
				$key )
		) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * LoginApp function - To fetch social profile data from the user's social account after authentication. The social profile will be retrieved via oAuth and OpenID protocols. The data is normalized into LoginApp' standard data format.
	 *
	 * @param string $accessToken LoginApp access token
	 * @param boolean $raw If true, raw data is fetched
	 *
	 * @return object User profile data.
	 */
	public function get_user_profiledata( $accessToken, $raw = false ) {
		$ValidateUrl = 'https://' . LA_DOMAIN . '/api/v2/userprofile?access_token=' . $accessToken;
		if ( $raw ) {
			$ValidateUrl = 'https://' . LA_DOMAIN . '/api/v2/userprofile/raw?access_token=' . $accessToken;

			return $this->call_api( $ValidateUrl );
		}

		return $this->call_api( $ValidateUrl );
	}

	/**
	 * LoginApp function - To fetch data from the LoginApp API URL.
	 *
	 * @param string $ValidateUrl - Target URL to fetch data from.
	 *
	 * @return string - data fetched from the LoginApp API.
	 */
	public function call_api( $ValidateUrl ) {
		$args     = array(
			'timeout'   => 15,
			'sslverify' => 'false',
		);
		$response = wp_remote_get( $ValidateUrl, $args );
		if ( is_wp_error( $response ) ) {
			$currentErrorResponse = "Something went wrong: " . $response->get_error_message();

			return $currentErrorResponse;
		} else {
			$JsonResponse = $response['body'];

			return json_decode( $JsonResponse );
		}
	}
}
