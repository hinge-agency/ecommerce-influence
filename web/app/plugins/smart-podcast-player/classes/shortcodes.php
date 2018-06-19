<?php
class SPP_Shortcodes {

	/**
	 * Generate the HTML for a full player shortcode
	 * 
	 * @param  array  $atts Shortcode arguments array
	 * @return string $html Generated HTML
	 */
	public static function shortcode_smart_podcast_player( $shortcode_atts ) {
		
		// Upgrade the options when a new version is detected
		SPP_Core::upgrade_options();
		
		// Initialize the frontend data and save the original
		// shortcode attributes for debugging purposes
		$js_obj = array();
		$js_obj[ 'shortcode_options' ] = $shortcode_atts;
		$js_obj[ 'errors' ] = array();
		
		// For the empty shortcode [smart_podcast_player], use an empty list of atts
		if( ! is_array( $shortcode_atts ) )
			$shortcode_atts = array();
		
		// Set the social options if they weren't set
		$shortcode_atts = self::spp_social_customize( $shortcode_atts, true );
		
		// Get the attribute values in order:
		//   1) Static defaults 2) Settings page 3) Shortcode
		// Later definitions override earlier ones
		$default_atts = self::get_spp_attribute_defaults();
		$settings_page = self::get_spp_attribute_settings( $default_atts );
		$processed_atts = array_merge( $default_atts, $settings_page, $shortcode_atts );
		
		// Check and/or set the player's unique ID
		if( $processed_atts['uid'] !== '' ) {
			if( ! preg_match( '/^[A-Za-z0-9_]*$/', $processed_atts['uid'] ) ) {
				$js_obj[ 'errors' ][] = 'Invalid UID: ' . $processed_atts['uid'];
				$processed_atts['uid'] = '';
			}
		}
		if( $processed_atts['uid'] === '' ) {
			$processed_atts['uid'] = self::get_uid($processed_atts);
		}
		
		extract( $processed_atts );

		// Check URL to see if it is an html link or a url
		if( strpos( $url, ' href="' ) !== false ) {
			preg_match( '/href="(.+)"/', $url, $match);
			$url = parse_url( $match[1] );
		}
		
		// If the user put in the name of a known color, replace it with the hex code
		if( is_string( $color ) ) {
			$known_colors = SPP_Core::get_free_colors();
			$color_lower = strtolower( $color );
			if( array_key_exists( $color_lower, $known_colors ) )
				$color = $known_colors[ $color_lower ];
		}
		
		// If this is showing as an unlicensed copy, force certain options
		if( !SPP_Core::is_paid_version() ) {
			$color = SPP_Core::SPP_DEFAULT_PLAYER_COLOR;
			$download = false;
			$social = false;
			$speedcontrol = false;
			$poweredby = true;
			$sort = 'newest';
		}
		
		// If the highlight color is too close to the background color,
		// make the play/pause button contrast by setting it to black or white
		$play_pause_color = $color;
		if( $style === 'light' && SPP_Utils_Color::get_brightness($color) < 0.1 )
			$play_pause_color = '#FFFFFF';
		else if( $style === 'dark' && SPP_Utils_Color::get_brightness($color) > 0.9 )
			$play_pause_color = '#000000';
		
		SPP_Dynamic_Css::add_color_array( array(
				'$color' => $color,
				'$background_color' => $color,
				'$play_pause_color' => $play_pause_color,
				'$email_button_bg_color' => $email_button_bg_color,
				'$email_button_text_color' => $email_button_text_color,
			));
		
		// v2.4: No more Zapier, remove basic (SPP) CTA
		if (isset($email_portal) && $email_portal === 'zp') {
				$email_portal = 'none';
		}
		if (isset($email_use_spp_cta) && $email_use_spp_cta) {
			$email_use_spp_cta = false;
		}
		// If there's no embedded HTML for a provider, remove the email button entirely
		if (isset($email_portal)) {
			if (($email_portal == 'mc' && empty($email_mc_html))
					|| ($email_portal == 'ck' && empty($email_ck_html))
					|| ($email_portal == 'enable' && empty($email_embed_html))) {
				$email_portal = 'none';
			}
		}
		
		// Put the JS and CSS on the page
		SPP_Core::enqueue_assets( $html_assets );

		// Create a div where the SPP will go
		$html = '<div class="smart-podcast-player-container ';
		
		// The class also includes the parts necessary for CSS: color, style, and view (mobile or responsive)
		$html .= ' smart-podcast-player-' . str_replace( '#', '', $color ) . '  spp-color-' . str_replace( '#', '', $color ) . ' ';
		if( $style != 'light' )
			$html .= 'smart-podcast-player-' . $style . ' ';
		if( $view === 'mobile' )
			$html .= 'smart-podcast-player-mobile-view ';
		$html .= '" ';
		$html .= 'data-uid="' . $uid . '" ';

		$html .= '></div>';
		
		// Put all of the attributes back into an array for insertion onto the page
		$js_obj[ 'options' ] = array();
		foreach( $default_atts as $attr=>$default ) {
			if( $attr === 'image' ) {
				// Rename 'image' to 'show_image' for HTML portability
				$js_obj[ 'options' ]['show_image'] = $$attr;
			} else {
				$js_obj[ 'options' ][$attr] = $$attr;
			}
		}
		if( SPP_Core::is_paid_version() )
			$js_obj[ 'options' ][ 'paid' ] = "true";
		
		// If the tracks are cached, put the data for 20 of them onto the page
		if( $cache = SPP_Ajax_Feed::get_cached_tracks( $url, $episode_limit ) ) {
			if( is_array( $cache ) && isset( $cache["tracks"] ) && ! is_wp_error( $cache["tracks"] ) ) {
				if( $sort === 'oldest' )
					$cache["tracks"] = array_slice( $cache["tracks"], -20, 20 );
				else
					$cache["tracks"] = array_slice( $cache["tracks"], 0, 20 );
				$js_obj[ 'cached_tracks' ] = $cache;
			}
		}
		
		// If the user has requested direct insertion, do that.  Otherwise, do it the normal way.
		// We use the html_cached_tracks option because that's the only part that used
		//   to be on the page this way (HS6819).  All of js_obj are suitable for this treatment.
		if( $html_cached_tracks === 'true' ) {
			$html .= '<script type="text/javascript">';
			$html .=     'SmartPodcastPlayer_uid_' . $uid . ' = ';
			$html .=     json_encode( $js_obj );
			$html .= '</script>';
		} else {
			wp_localize_script( SPP_Core::PLUGIN_SLUG . '-plugin-script',
					'SmartPodcastPlayer_uid_' . $uid, $js_obj );
			// Also save the data, just in case we need to get it from AJAX
			list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
					'purpose' => 'jsobj from uid',
					'uid' => $uid) );
			set_transient( $transient_name, $js_obj, $timeout );
		}

		// Return the HTML div we've built
		if( SPP_Core::is_thrive_content_builder() ) {
			// On Thrive Content Builder, there's no JS, so we put some visible stuff in
			return $html . '<p>Smart Podcast Player</p><p>Feed URL: ' . $url . '</p>';
		}
		return $html;	

	}
	
	/**
     * This function maintains backwards compatibility with users' old
	 * shortcodes.  If no "social_" attributes have been set, we set
	 * Twitter, Facebook, Google+, and possibly Email.  Otherwise, we use
	 * the users' settings.
	 */
	private static function spp_social_customize( $shortcode_atts, $is_full_player ) {
		
		$user_customized = false;
		$social_off = ( isset( $shortcode_atts[ 'social' ] ) &&
				$shortcode_atts[ 'social' ] === 'false' );
		
		if( $social_off ) {
			$user_customized = true;
			// If the user set "social" to false, then all social_ are false
			foreach( $shortcode_atts as $key => $value )
				if( substr( $key, 0, 7 ) === 'social_' )
					$shortcode_atts[ $key ] = 'false';
		} else {
			// Find out if the user has customized anything
			foreach( $shortcode_atts as $key => $value )
				if( substr( $key, 0, 7 ) === 'social_' )
					$user_customized = true;
		}
		
		if( ! $user_customized ) {
			// No social_s were set, so set these four.
			$shortcode_atts[ 'social' ] = 'true';
			$shortcode_atts[ 'social_twitter' ] = 'true';
			$shortcode_atts[ 'social_facebook' ] = 'true';
			$shortcode_atts[ 'social_gplus' ] = 'true';
			if( $is_full_player )
				$shortcode_atts[ 'social_email' ] = 'true';
		}
		
		return $shortcode_atts;
	}
	
	private static function get_spp_attribute_defaults() {
		return array(
			'ajax_delay'            => 0,
			'background'            => 'default',
			'color'                 => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
			'download'              => 'true',
			'episode_limit'         => '0',
			'featured_episode'      => '',
			'hashtag'               => '',
			'hide_listens'          => 'false',
			'hover_timestamp'       => 'true',
			'html_assets'           => 'false',
			'html_cached_tracks'    => 'false',
			'image'                 => 'default',
			'numbering'             => '',
			'permalink'             => '',
			'poweredby'             => 'false',
			'show_episode_numbers'  => 'true',
			'show_name'             => '',
			'show_tags'             => 'true',
			'social'                => 'true',
			'social_twitter'        => 'false',
			'social_facebook'       => 'false',
			'social_linkedin'       => 'false',
			'social_gplus'          => 'false',
			'social_pinterest'      => 'false',
			'social_email'          => 'false',
			'sort'                  => 'newest',
			'speedcontrol'          => 'true',
			'style'                 => 'light',
			'subscribe_itunes'      => '',
			'subscribe_buzzsprout'  => '',
			'subscribe_googleplay'  => '',
			'subscribe_iheartradio' => '',
			'subscribe_pocketcasts' => '',
			'subscribe_soundcloud'  => '',
			'subscribe_stitcher'    => '',
			'subscribe_rss'         => 'true',
			'tweet_text'            => '',
			'twitter_username'      => '',
			'uid'                   => '',
			'url'                   => '',
			'view'                  => 'responsive',
			'email_portal'                => 'none',
			'email_outer_button_text'     => 'Sign me up!',
			'email_button_bg_color'       => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
			'email_button_text_color'     => '#FFFFFF',
			'email_cta_image_url'         => '',
			'email_cta_text_large'        => 'Sign up to receive email updates',
			'email_cta_text_small'        => "Enter your name and email address below and I'll send you periodic updates about the podcast.",
			'email_cta_request_first_name'=> 'true',
			'email_cta_require_first_name'=> 'false',
			'email_cta_request_last_name' => 'true',
			'email_cta_require_last_name' => 'false',
			'email_cta_button_text'       => 'Subscribe',
			'email_cta_open'              => 'manual',
			'email_cta_elapsed_seconds'   => '60',
			'email_cta_remaining_seconds' => '60',
			'email_use_spp_cta'           => 'true',
			'email_ck_form_id'            => '',
			'email_ck_html'               => '',
			'email_mc_list_id'            => '',
			'email_mc_html'               => '',
			'email_embed_html'            => '',
		);
	}
	
	private static function get_spp_attribute_settings( $atts ) {
	
		$options = get_option( 'spp_player_defaults' );
		$advanced = get_option( 'spp_player_advanced' );
		$news = get_option( 'spp_player_email' );
		$out = array();
		foreach( $atts as $key => $value ) {
			if( isset( $options[$key] ) )
				$out[$key] = $options[$key];
			else if ( isset( $advanced[$key] ) )
				$out[$key] = $advanced[$key];
			else if ( substr( $key, 0, 6 ) === 'email_' ) {
				$key_short = substr( $key, 6 );
				if( isset( $news[$key_short] ) )
					$out[$key] = $news[$key_short];
			}
		}
		
		// bg_color got changed to color, but we've kept the setting name
		if( isset( $options['bg_color'] ) )
			$out['color'] = $options['bg_color'];
		
		// background is stored as spp_background
		if( isset( $options['spp_background'] ) )
			$out['background'] = $options['spp_background'];
		
		// Shortcode "sort" is settings "sort_order"
		if( isset( $options['sort_order'] ) )
			$out['sort'] = $options['sort_order'];
		
		return $out;
	}
	
	

	/**
	 * Output the shortcode for the track player
	 * @param  array  $atts Shortcode arguments, needs to be extracted
	 * @return string $html Shortcode HTML
	 */
	public static function shortcode_smart_track_player( $atts = array() ) {
	
		return self::get_track_mp3_html( $atts, false );

	}
	

	/**
	 * Output the shortcode for the latest episode track player
	 * @param  array  $atts Shortcode arguments, needs to be extracted
	 * @return string $html Shortcode HTML
	 */
	public static function shortcode_smart_track_player_latest( $atts = array() ) {

		// Check if we've already gotten the feed stored in a transient
		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'tracks from feed url',
				'url' => $atts['url'],
				'episode_limit' => 1 ) );
		$data = get_transient( $transient_name );
		
		// If we have the transient data
		if( $data != null
				&& isset( $data['tracks'] )
				&& isset( $data['tracks'][0]->stream_url )
				&& isset( $data['tracks'][0]->title )
				&& isset( $data['tracks'][0]->show_name ) ) {
				
			// Set the URL and the track title/artist based on what's in the feed
			$atts['url'] = $data['tracks'][0]->stream_url;
			if( !isset( $atts['title'] ) ) {
				$atts['title'] = $data['tracks'][0]->title;
			}
			if( !isset( $atts['artist'] ) ) {
				$options = get_option( 'spp_player_defaults' );
				if( isset( $options['artist_name'] ) ) {
					$atts['artist'] = $options['artist_name'];
				} else {
					$atts['artist'] = $data['tracks'][0]->show_name;
				}
			}
			// Run it as a normal STP
			return self::get_track_mp3_html( $atts, false);
		} else {
			// Run as an STP with a "latest" flag
			return self::get_track_mp3_html( $atts, true );
		}

	}
	
	private static function get_stp_attribute_defaults() {
		return array(
			// 'artist' has special treatment; it's left out here
			'background'       => 'default',
			'color'            => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
			'download'         => 'true',
			'feed_url'         => '',
			'hashtag'          => '',
			'hover_timestamp'  => 'true',
			'html_assets'      => 'false',
			'image'            => '',
			'marquee'          => 'auto',
			'social'           => 'true',
			'social_twitter'   => 'true',
			'social_facebook'  => 'true',
			'social_gplus'     => 'true',
			'social_linkedin'  => 'false',
			'social_stumble'   => 'false',
			'social_pinterest' => 'false',
			'social_email'     => 'false',
			'speedcontrol'     => 'true',
			'permalink'        => '',
			'show_numbering'   => '',
			'style'            => 'light',
			// 'title' has special treatment; it's left out here
			'tweet_text'       => '',
			'twitter_username' => '',
			'uid'              => '',
			'url'              => '',
			'view'             => 'responsive',
			'email_portal'                => 'none',
			'email_outer_button_text'     => 'Sign me up!',
			'email_button_bg_color'       => SPP_Core::SPP_DEFAULT_PLAYER_COLOR,
			'email_button_text_color'     => '#FFFFFF',
			'email_ck_html'               => '',
			'email_cta_image_url'         => '',
			'email_cta_text_large'        => 'Sign up to receive email updates',
			'email_cta_text_small'        => "Enter your name and email address below and I'll send you periodic updates about the podcast.",
			'email_cta_request_first_name'=> 'true',
			'email_cta_require_first_name'=> 'false',
			'email_cta_request_last_name' => 'true',
			'email_cta_require_last_name' => 'false',
			'email_cta_button_text'       => 'Subscribe',
			'email_cta_open'              => 'manual',
			'email_cta_elapsed_seconds'   => '60',
			'email_cta_remaining_seconds' => '60',
			'email_use_spp_cta'           => 'true',
			'email_ck_form_id'            => '',
			'email_mc_list_id'            => '',
			'email_mc_html'               => '',
			'email_embed_html'            => '',
		);
	}
	
	private static function get_stp_attribute_settings( $atts ) {
		
		$settings = get_option( 'spp_player_defaults' );
		$advanced = get_option( 'spp_player_advanced' );
		$news = get_option( 'spp_player_email' );
		$out = array();
		
		foreach( $atts as $key => $value ) {
			if( isset( $settings[$key] ) )
				$out[$key] = $settings[$key];
			else if ( isset( $advanced[$key] ) )
				$out[$key] = $advanced[$key];
			else if ( substr( $key, 0, 6 ) === 'email_' ) {
				$key_short = substr( $key, 6 );
				if( isset( $news[$key_short] ) )
					$out[$key] = $news[$key_short];
			}
		}
		
		// These items have different names in the settings vs the shortcode
		if( isset( $settings['bg_color'] ) )
			$out['color'] = $settings['bg_color'];
		if( isset( $settings['stp_image'] ) )
			$out['image'] = $settings['stp_image'];
		if( isset( $settings['artist_name'] ) )
			$out['artist'] = $settings['artist_name'];
		if( isset( $settings['stp_background'] ) )
			$out['background'] = $settings['stp_background'];
		if( isset( $settings['stp_background_color'] ) )
			$out['stp_background_color'] = $settings['stp_background_color'];
		
		// If the artist is the empty string, we treat it as null
		if( isset( $out['artist'] ) && $out['artist'] === '' )
			unset( $out['artist'] );
		
		return $out;
	}

	/**
	 * Output HTML for a single 
	 * @param  string 	$audio_url 	Link to an MP3
	 * @param  array 	$atts      	Array of shortcode attributes
	 * @return string 	$html 		HTML output for shortcode
	 */
	private static function get_track_mp3_html( $shortcode_atts, $is_latest_player ) {
		
		// Upgrade the options when a new version is detected
		SPP_Core::upgrade_options();

		// Include the MP3 class to handle MP3 data
		require_once( SPP_PLUGIN_BASE . 'classes/mp3.php' );
		
		$js_obj = array();
		$js_obj[ 'errors' ] = array();

		$settings = get_option( 'spp_player_defaults' );
		if( $settings == false )
			$settings = array();
		
		// Get the attribute values in order:
		//   1) Static defaults 2) Settings page 3) Shortcode
		// Later definitions override earlier ones
		$default_atts = self::get_stp_attribute_defaults();
		$settings_page = self::get_stp_attribute_settings( $default_atts );
		$atts = array_merge( $default_atts, $settings_page, $shortcode_atts );
		
		SPP_Core::enqueue_assets( $atts['html_assets'] );

		// If the URL is an HTML link, extract the URL
		if( strpos( $atts['url'], ' href="' ) !== false ) {
			$xml = simplexml_load_string( $atts['url'] );
			$list = $xml->xpath("//@href");
			$preparedUrls = array();
			foreach($list as $item) {
				$i = $item;
				$item = parse_url($item);
				$preparedUrls[] = $item['scheme'] . '://' .  $item['host'] . $item['path'];
			}
			if( $preparedUrls[0] )
				$atts['url'] = $preparedUrls[0];
		}
		
		// If the user typed the name of a known color, replace it with the hex code
		$known_colors = SPP_Core::get_free_colors();
		$known_colors = array_change_key_case( $known_colors, CASE_LOWER );
		if( array_key_exists( strtolower($atts['color']), $known_colors ) ) 
			$atts['color'] = $known_colors[ $atts['color'] ];
		if( array_key_exists( strtolower($atts['background']), $known_colors ) )
			$atts['background'] = $known_colors[ $atts['background'] ];

		// If this is showing as an unlicensed copy, force certain options
		if( !SPP_Core::is_paid_version() ) {
			$atts['color'] = SPP_Core::SPP_DEFAULT_PLAYER_COLOR;
			$atts['download'] = false;
			$atts['social'] = false;
			$atts['speedcontrol'] = false;
		}
		
		// v2.4: No more Zapier, remove basic (SPP) CTA
		if (array_key_exists('email_portal', $atts) && $atts['email_portal'] === 'zp')
			$atts['email_portal'] = 'none';
		if (array_key_exists('email_use_spp_cta', $atts) && $atts['email_use_spp_cta']) {
			$atts['email_use_spp_cta'] = false;
		}
		// If there's no embedded HTML for a provider, remove the email button entirely
		if (array_key_exists('email_portal', $atts)) {
			if (($atts['email_portal'] == 'mc' &&
						(!array_key_exists('email_mc_html', $atts) || $atts['email_mc_html'] == ''))
					|| ($atts['email_portal']== 'ck' &&
						(!array_key_exists('email_ck_html', $atts) || $atts['email_ck_html'] == ''))
					|| ($atts['email_portal']== 'enable' &&
						(!array_key_exists('email_embed_html', $atts) || $atts['email_embed_html'] == ''))) {
				$atts['email_portal'] = 'none';
			}
		}
		
		$atts = self::spp_social_customize( $atts, false );
		
		// Check and/or set the player's unique ID
		if( $atts['uid'] !== '' ) {
			if( ! preg_match( '/^[A-Za-z0-9_]*$/', $atts['uid'] ) ) {
				$js_obj[ 'errors' ][] = 'Invalid UID: ' . $atts['uid'];
				$atts['uid'] = '';
			}
		}
		if( $atts['uid'] === '' ) {
			$atts['uid'] = self::get_uid($atts);
		}
		
		// Set the background_type and background_color
		//    based on background and possibly stp_background_color
		if( $atts['background'] == 'default' ) {
			$atts['background_type'] = 'default';
			$atts['background_color'] = $atts['style'] === 'dark' ? '#2A2A2A' : '#EEEEEE';
		} elseif( $atts['background'] === 'blurred_logo' || $atts['background'] === 'blurry_logo'
				|| $atts['background'] === 'blurred logo' || $atts['background'] === 'blurry logo' ) {
			$atts['background_type'] = 'blurred_logo';
			$atts['background_color'] = $atts['style'] === 'dark' ? '#2A2A2A' : '#EEEEEE';
		} elseif( $atts['background'] === 'color' && isset( $atts['stp_background_color'] ) ) {
			$atts['background_type'] = 'color';
			$atts['background_color'] = $atts['stp_background_color'];
		} else {
			require_once( SPP_PLUGIN_BASE . 'classes/utils/color.php' );
			if( SPP_Utils_Color::is_hex( $atts['background'] ) ) {
				$atts['background_type'] = 'default';
				$atts['background_color'] = $atts['background'];
			} else {
				$atts['background_type'] = 'default';
				$atts['background_color'] = $atts['style'] === 'dark' ? '#2A2A2A' : '#EEEEEE';
			}
		}
		$atts['color'] = str_replace( '#', '', $atts['color'] );
		$atts['background_color'] = str_replace( '#', '', $atts['background_color'] );
		unset( $atts['background'] );
		unset( $atts['stp_background_color'] );
		
		if( $is_latest_player == false ) {
			// Verify the URL is for an MP3 or M4A file
			$is_audio = false;
			if( strpos( $atts['url'], 'soundcloud.com' ) !== false ) {
				$test = rtrim( $atts['url'], '/' );
				$count = substr_count( $test, '/' );
				if( $count > 3 && strpos( $atts['url'], '/sets/' ) === false ) {
					$is_audio = true;
				}		
			} else {
				if( strpos( $atts['url'], '.mp3' ) !== false || strpos( $atts['url'], '.m4a' ) !== false ) {
					$is_audio = true;
				}
			}
			// If it's not an MP3 or M4A, we give nothing out so as to not crash the page.
			if( !$is_audio )
				return;
		}

		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'track data from track url',
				'url' => $atts['url'] ) );
		
		$data = get_transient( $transient_name );
		if ( false === $data )
			$data = array();

		if( !isset( $atts['title'] ) ) {
			if( isset( $data['title'] ) )
				$atts['title'] = $data['title'];
			elseif( isset( $data['album'] ) )
				$atts['title'] = $data['album'];
			elseif( isset( $data['artist'] ) )
				$atts['title'] = $data['artist'];
			elseif( isset( $settings['show_name']  ) && $settings['show_name'] != '' && !empty( $data ) )
				$atts['title'] = $settings['show_name'];
		}
		if( !isset( $atts['artist'] ) ) {
			if( isset( $data['artist'] ) )
				 $atts['artist'] = $data['artist'];
			elseif( isset( $data['album'] ) )
				 $atts['artist'] = $data['album'];
			elseif( isset( $settings['show_name']  ) && $settings['show_name'] != '' && !empty( $data ) )
				 $atts['artist'] = $settings['show_name'];
		}
		
		$play_pause_color = $atts['color'];
		require_once( SPP_PLUGIN_BASE . 'classes/utils/color.php' );
		if( $atts['style'] === 'light' && SPP_Utils_Color::get_brightness($atts['color']) < 0.1 ) {
			$play_pause_color = '#FFFFFF';
		}
		if( $atts['style'] === 'dark' && SPP_Utils_Color::get_brightness($atts['color']) > 0.9 ) {
			$play_pause_color = '#000000';
		}
		SPP_Dynamic_Css::add_color_array( array(
				'$color' => $atts['color'],
				'$play_pause_color' => $play_pause_color,
				'$background_color' => $atts['background_color'],
				'$email_button_bg_color' => $atts['email_button_bg_color'],
				'$email_button_text_color' => $atts['email_button_text_color'],
			));


		$class = 'smart-track-player-container';
		// Add the color class every time
		$class .= ' stp-color-' . $atts['color'] . '-' . $atts['background_color'];
		$html = '<div class="' . trim( $class ) . '"';
		$html .= ' data-uid="' . $atts['uid'] . '"';
		$html .= '></div>';
		
		// Rename 'image' to 'show_logo' for HTML portability
		if( !empty( $atts[ 'image' ] ) )
			$atts['show_logo'] = $atts['image'];
		unset( $atts['image'] );
		
		if( SPP_Core::is_paid_version() )
			$atts[ 'paid' ] = "true";
		if( $is_latest_player ) {
			$atts[ 'latest' ] = "true";
			$atts[ 'feed_url' ] = $atts['url'];
		}
		
		// For the regular STP, we have the file's URL, so we note it for download.
		// For the latest STP, the file's URL will be noted during the AJAX call.
		if( !$is_latest_player ) {
			require_once( SPP_PLUGIN_BASE . 'classes/download.php' );
			$atts['download_id'] = SPP_Download::save_download_id($atts['url']);
		}
		
		// Put all of the attributes back into an array and onto the page
		$js_obj[ 'options' ] = $atts;
		$js_obj[ 'shortcode_options' ] = $shortcode_atts;
		
		wp_localize_script( SPP_Core::PLUGIN_SLUG . '-plugin-script',
				'SmartPodcastPlayer_uid_' . $atts['uid'], $js_obj );
				
		// Also save the data, just in case we need to get it from AJAX
		list( $transient_name, $timeout ) = SPP_Transients::spp_transient_info( array(
				'purpose' => 'jsobj from uid',
				'uid' => $atts['uid']) );
		set_transient( $transient_name, $js_obj, $timeout );
		
		if( SPP_Core::is_thrive_content_builder() ) {
			return $html . '<p>Smart Track Player</p><p>URL: ' . $atts['url'] . '</p>';
		} else {
			return $html;
		}
	}
	
	/**
	 * Gives a four-character ID generated from all the options
	 */
	private static function get_uid($atts) {
		$hash = md5(serialize($atts));
		return substr($hash, -8); // 8 characters should do
	}
}
