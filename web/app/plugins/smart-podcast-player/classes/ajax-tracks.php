<?php
		
class SPP_Ajax_Tracks {

	public static function fetch_track_data() {
		
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}
		
		header('Content-Type: application/json');
		
		$url_array = isset( $_POST['urls'] ) ? $_POST['urls'] : false;
		$uid_array = isset( $_POST['uids'] ) ? $_POST['uids'] : false;

		if( !$url_array || !$uid_array || count($url_array) !== count($uid_array) ) {
			trigger_error( 'URLs and UIDs required' );
			die();
		}

		$advanced = get_option( 'spp_player_advanced' );
		$track_array = array();
		$len = count($url_array);
		for( $i = 0; $i < $len; $i++ ) {
		
			$url = $url_array[$i];
			$uid = $uid_array[$i];
			$source = 'none';
			
			// If we have the data, use that
			list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
					'purpose' => 'track data from track url',
					'url' => $url ) );
			$data = get_transient( $transient_name );
			$source = 'cache';
			
			if( false === $data ) {
			
				if( isset( $advanced['stp_data_source'] ) && $advanced['stp_data_source'] == "mp3" ) {
					// Try to get the data on this backend via SPP_MP3
					require_once( SPP_PLUGIN_BASE . 'classes/mp3.php' );
					$data = SPP_MP3::get_data( $url );
					$source = 'mp3';
					
					// If that didn't work, try to get the data from the feed, if any
					if( ! is_array( $data ) || empty( $data) ) {
						$data = self::get_track_data_from_feed( $url );
						$source = 'feed';
					}
				} else {
					// Try to get the data from the feed, if any
					$data = self::get_track_data_from_feed( $url );
					$source = 'feed';

					// If that didn't work, try to get the data on this backend via SPP_MP3
					if( ! is_array( $data ) || empty( $data) ) {
						require_once( SPP_PLUGIN_BASE . 'classes/mp3.php' );
						$data = SPP_MP3::get_data( $url );
						$source = 'mp3';
					}
				}
				
				// If that didn't work, get the data from spp.com
				if( ! is_array( $data ) || empty( $data) ) {
					$data = self::fetch_track_data_fallback( $url );
					$source = 'fallback';
				}
				
				// If any of these worked, cache the data
				if( is_array( $data ) && ( isset( $data["artist"] ) || isset( $data["title"] ) ) ) {
					set_transient( $transient_name, $data, $timeout );
				} else {
					// Prevent continuous re-fetching
					set_transient( $transient_name, '0', MINUTE_IN_SECONDS );
					$source = 'failed';
					if( SPP_Core::debug_output() )
						trigger_error( 'Unable to retrieve track data' );
				}
			}

			$data_out = array();
			$data_out[ "uid" ] = $uid;
			if ( is_array( $data ) && ( isset( $data[ "artist" ] ) || isset( $data[ "title" ] ) ) ) {
				if( isset( $data[ "artist" ] ) )
					$data_out[ "artist" ] = $data[ "artist" ];
				if( isset( $data[ "title" ] ) )
					$data_out[ "title" ] = $data[ "title" ];
				$data_out[ "source" ] = $source;
			}
			$track_array[] = $data_out;
		}
		
		if( version_compare( phpversion(), '5.5.0', '>' ) ) {
			$ret = json_encode( $track_array, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			$ret = json_encode( $track_array );
		}
		echo $ret;

		die();

	}

	public static function fetch_track_data_fallback ( $url = null ) {

		// If we've gotten a response recently, return that response
		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'fallback response from track url',
				'url' => $url ) );
		$data = get_transient( $transient_name );
		if( $data )
			return $data;

		$settings = get_option( 'spp_player_general' );
		$license_key = isset( $settings[ 'license_key' ] ) ? trim($settings[ 'license_key' ]) : 'nokey';

		$response = wp_remote_get(
		    "https://go.smartpodcastplayer.com/trackdata/?url=" . $url . "&license_key=" . $license_key,
		    array(
		        'timeout' => 10,
		        'sslverify' => false
		    )
		);

		if( !is_wp_error( $response ) && ( $response['response']['code'] < 400 ) ) {
			$data = json_decode( wp_remote_retrieve_body( $response ) , true );
			set_transient( $transient_name, $data, $timeout );
			return $data;
		}
		return null;

	}
	
	public static function get_track_data_from_feed( $url ) {
	
		$options = get_option( 'spp_player_defaults' );
		if( !isset( $options[ 'url' ] ) || empty( $options[ 'url' ] ) )
			return null;
		
		$feed_url = $options[ 'url' ];
		
		$feed_data = SPP_Ajax_Feed::get_and_cache_tracks( $feed_url, 0 );
		if( !isset( $feed_data[ 'numTracks' ] ) || !isset( $feed_data[ 'tracks' ] ) ) {
			return null;
		}
		$data_out = self::search_feed_for_track_data( $feed_data, $url );
		
		if( is_null( $data_out ) ) {
			// Try again without the cache
			$feed_data = SPP_Ajax_Feed::get_and_cache_tracks( $feed_url, 0, true );
			if( !isset( $feed_data[ 'numTracks' ] ) || !isset( $feed_data[ 'tracks' ] ) ) {
				return null;
			}
			$data_out = self::search_feed_for_track_data( $feed_data, $url );
		}
		return $data_out;
	}
	
	public static function search_feed_for_track_data( $feed_data, $url ) {
		
		// Remove the protocol and query string from the STP's URL
		$url_norm = preg_replace( '/\?.*/', '', $url );
		$url_norm = preg_replace( '/^https?/', '', $url_norm );
		$url_norm = strtolower( $url_norm );
		for ( $i = 0; $i < $feed_data[ 'numTracks' ]; $i++ ) {
			if( isset( $feed_data[ 'tracks' ][ $i ] ) ) {
				$track = $feed_data[ 'tracks' ][ $i ];
				if( isset( $track->stream_url ) && isset( $track->title ) ) {
					// Remove the protocol and query string from the URL in the feed
					$feed_url_norm = preg_replace( '/\?.*/', '', $track->stream_url );
					$feed_url_norm = preg_replace( '/^https?/', '', $feed_url_norm );
					$feed_url_norm = strtolower( $feed_url_norm );
					if( $feed_url_norm === $url_norm ) {
						$data_out[ 'title' ] = $track->title;
						if( isset( $options['artist_name'] ) && $options['artist_name'] !== "" ) {
							$data_out[ 'artist' ] = $options['artist_name'];
						} else {
							$data_out[ 'artist' ] = $track->show_name;
						}
						return $data_out;
					}
				}
			}
		}
		return null;
	}
	
	/**
	 * Return the data for an array of Soundcloud stream URLs
	 * 
	 * @return JSON Array
	 */
	public static function ajax_get_soundcloud_track() {
	
		if( SPP_Core::debug_output() ) {
			ini_set( 'display_errors', '1' );
			error_reporting( E_ALL );
		}
		header('Content-Type: application/json');
		
		$api_options = get_option( 'spp_player_soundcloud', array( 'consumer_key' => '' ) );
		$api_consumer_key = isset( $api_options['consumer_key'] ) ? $api_options['consumer_key'] : '';
		if( $api_consumer_key == '' ) {
			$api_consumer_key = 'b38b3f6ee1cdb01e911c4d393c1f2f6e';
		}

		$url_array = isset( $_POST['streams'] ) ? $_POST['streams'] : '';

		$track_array = array();
		foreach( $url_array as $url ) {
		
			// If there's no URL, put in a placeholder for this track
			if( empty( $url ) ) {
				$track_array[] = '0';
				continue;
			}
			
			// If we have the data cached, use that
			list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
					'purpose' => 'soundcloud data from track url',
					'url' => $url ) );
			$track = get_transient( $transient_name );
			if( $track !== false ) {
				$track_array[] = $track;
				continue;
			}
			
			// Form the Soundcloud request URL.
			// User in HS 3788 had a feed in which each enclosure matched the regexp below.  Using the resolve
			// URL didn't work for this one, so I added this specific match.  There is likely a better way.
			// It would involve finding out all of the possible Soundcloud URLs.
			if( 1 == preg_match( '/feeds\.soundcloud\.com\/stream\/(\d+)/', $url, $matches ) ) {
				$rq_url = SPP_Core::SPP_SOUNDCLOUD_API_URL . '/tracks/' . $matches[1] . '?consumer_key=' . $api_consumer_key;
			} else {
				$rq_url = SPP_Core::SPP_SOUNDCLOUD_API_URL . '/resolve.json?url=' . urlencode( $url ) . '&consumer_key=' . $api_consumer_key;
			}

			// Get the data from Soundcloud
			$response = wp_remote_get( $rq_url );
			if( !is_wp_error( $response ) && ( $response['response']['code'] < 400 ) ) {
				$track = json_decode( $response['body'] );
				if ( !empty ( $track  ) ) {
					set_transient( $transient_name, $track, $timeout );
				}
			} else {
				$track = $response->get_error_message();
			}
			$track_array[] = $track;
		}
		
		echo json_encode( $track_array );

		exit;

	}
}
