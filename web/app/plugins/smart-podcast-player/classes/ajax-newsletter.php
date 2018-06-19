<?php
		
class SPP_Ajax_Newsletter {
	
	public static function spp_newsletter_subscribe() {

		$portal = isset( $_POST['portal'] ) ? $_POST['portal'] : '';
		$address = isset( $_POST['address'] ) ? $_POST['address'] : '';
		$first_name = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
		$last_name = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
		
		$news_opts = get_option( 'spp_player_email' );
		
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}
		
		if ( empty( $portal ) ) {
			$ret_data = new stdClass();
			$ret_data->status = 'error';
			$ret_data->error = 'Email service provider not set';
		} else if( $portal === 'ck' ) {
			$ck_form_id = '';
			if( isset( $_POST['ck_form_id'] ) )
				$ck_form_id = $_POST['ck_form_id'];
			elseif( isset( $news_opts['ck_form_id'] ) )
				$ck_form_id = $news_opts['ck_form_id'];
			$ret_data = self::subscribe_ck( $address, $first_name, $last_name, $news_opts, $ck_form_id );
		} else if( $portal === 'mc' ) {
			$mc_list_id = '';
			if( isset( $_POST['mc_list_id'] ) )
				$mc_list_id = $_POST['mc_list_id'];
			elseif( isset( $news_opts['mc_list_id'] ) )
				$mc_list_id = $news_opts['mc_list_id'];
			$ret_data = self::subscribe_mc( $address, $first_name, $last_name, $news_opts, $mc_list_id );
		} else if( $portal === 'zp' ) {
			$ret_data = self::subscribe_zp( $address, $first_name, $last_name, $news_opts );
		} else {
			$ret_data = new stdClass();
			$ret_data->status = 'error';
			$ret_data->error = 'Unknown email service provider';
		}
		
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $ret_data, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $ret_data );
		}
		header('Content-Type: application/json');
		echo $ret;
		exit;
	}
	
	public static function subscribe_ck( $address, $first_name, $last_name, $news_opts, $form_id ) {

		$ret = new stdClass();
		if( !empty( $news_opts['ck_api_key'] ) ) {
			$api_key = $news_opts['ck_api_key'];
		} else {
			$ret->status = 'error';
			$ret->error = 'ConvertKit API key not set';
			return $ret;
		}
		if( empty( $form_id ) ) {
			$ret->status = 'error';
			$ret->error = 'ConvertKit Form ID not set';
			return $ret;
		}
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://api.convertkit.com/v3/forms/' . $form_id . '/subscribe' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json; charset=utf-8" ) );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( array(
				'api_key'    => $api_key,
				'email'      => $address,
				'first_name' => $first_name,
				'fields'     => array( 'last_name' => $last_name ),
		)));
		$response = curl_exec( $ch );
		
		if( $response === false ) {
			$ret->status = 'error';
			$ret->error = curl_error( $ch );
		} else {
			$response = json_decode( $response );
			if( isset( $response->error ) ) {
				$ret->status = 'error';
				$ret->error = $response->error . ': ' . $response->message;
			} else if( isset( $response->subscription ) ) {
				if( isset( $response->subscription->state ) 
						&& $response->subscription->state === "active" ) {
					// ConvertKit's way of telling us this user already exists
					$ret->status = 'exists';
				} else {
					$ret->status = 'success';
				}
			}
		}
		curl_close( $ch );
		
		if( SPP_Core::debug_output() ) {
			$ret->orig_response = $response;
		}
		return $ret;
	}
	
	public static function subscribe_mc( $address, $first_name, $last_name, $news_opts, $list_id ) {
	
		$ret = new stdClass();
		if( isset( $news_opts['mc_api_key'] ) ) {
			$api_key = $news_opts['mc_api_key'];
		} else {
			$ret->status = 'error';
			$ret->error = 'MailChimp API key not set';
			return $ret;
		}
		
		if( preg_match( '/-(us\d+)$/', $api_key, $dc_matches ) )
			$dc = $dc_matches[ 1 ];
		if( ! isset( $dc ) ) {
			$ret->status = 'error';
			$ret->error = 'Invalid format for MailChimp API key';
			return $ret;
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json; charset=utf-8" ) );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_USERPWD, 'SPP:' . $api_key );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( array(
				'email_address' => $address,
				'status'        => 'pending',
				'merge_fields'  => array(
					'FNAME' => $first_name,
					'LNAME' => $last_name,
				),
		)));
		$response = curl_exec( $ch );
		
		if( $response === false ) {
			$ret->status = 'error';
			$ret->error = curl_error( $ch );
		} else {
			$response = json_decode( $response );
			// The response's "status" field will be an HTTP return code like 400 on error
			//   On success, this field is the email address's status, like "subscribed"
			if( isset( $response->status ) && ! is_numeric( $response->status ) ) {
				// Success
				$ret->status = 'success';
			} else {
				// MC has an error message for "already subscribed"
				if( $response->title === 'Member Exists' ) {
					$ret->status = 'exists';
				} else {
					// It's actually an error
					$ret->status = 'error';
					if( isset( $response->detail ) )
						$ret->error = $response->detail;
				}
			}
		}
		curl_close( $ch );
		
		if( SPP_Core::debug_output() ) {
			$ret->orig_response = $response;
		}
		return $ret;
	}
	
	public static function subscribe_zp( $address, $first_name, $last_name, $news_opts ) {
		
		$news_opts = get_option( 'spp_player_email' );
		$url = $news_opts[ 'zp_url' ];
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Content-Type: application/json; charset=utf-8" ) );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( array(
				'email_address' => $address,
				'first_name' => $first_name,
				'last_name' => $last_name,
		)));
		$response = curl_exec( $ch );
		
		$ret = new stdClass();
		if( $response === false ) {
			$ret->status = 'error';
			$ret->error = curl_error( $ch );
		} else {
			$response = json_decode( $response );
			// I haven't found a situation where Zapier returns anything but "success"
			if( isset( $response->status ) && $response->status === 'success' ) {
				$ret->status = 'unknown';
			} else {
				$ret->status = 'error';
			}
		}
		curl_close( $ch );
		
		if( SPP_Core::debug_output() ) {
			$ret->orig_response = $response;
		}
		return $ret;
	}
	
	public static function test_zapier() {
		
		$ret_data = self::subscribe_zp( 'test@example.com', 'Example', 'Name', null );
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $ret_data, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $ret_data );
		}
		header('Content-Type: application/json');
		echo $ret;
		exit;
	}
}
