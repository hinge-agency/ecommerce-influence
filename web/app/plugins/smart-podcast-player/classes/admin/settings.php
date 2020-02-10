<?php

class SPP_Admin_Settings {

	public $plugin_slug;
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register' ) );
	}


	public function register() {
		
		$plugin = SPP_Core::get_instance();
		$plugin->upgrade_options();
		
		register_setting( 'spp-player', 'spp_player_social' );
		register_setting( 'spp-player-general', 'spp_player_general',
				array( 'SPP_Admin_Settings', 'general_sanitize' ) );
		register_setting( 'spp-player-defaults', 'spp_player_defaults',
				array( 'SPP_Admin_Settings', 'defaults_sanitize' ) );
		register_setting( 'spp-player-email', 'spp_player_email',
				array( 'SPP_Admin_Settings', 'email_sanitize' ) );
		register_setting( 'spp-player-sticky', 'spp_player_sticky',
				array( 'SPP_Admin_Settings', 'sticky_sanitize' ) );
		register_setting( 'spp-player-advanced', 'spp_player_advanced' );
		register_setting( 'spp-player-timestamps', 'spp_player_timestamps',
				array( 'SPP_Admin_Settings', 'timestamps_sanitize' ) );
		
		add_options_page( 'Smart Podcast Player Settings', 'Smart Podcast Player',
				'manage_options', 'spp-player', array( $this, 'settings_page' ) );

	}

	public function settings_page() {
		require_once( SPP_ASSETS_PATH . 'views/settings.php' );
	}
	
	public static function general_sanitize( $general_opts ) {
		SPP_Admin_Core::license_key_reset();
		return $general_opts;
	}
	
	public static function email_sanitize( $news_opts ) {
		$checkbox_names = array(
				'cta_request_first_name',
				'cta_require_first_name',
				'cta_request_last_name',
				'cta_require_last_name',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $news_opts[ $name ] ) && $news_opts[ $name ] === 'true' ) {
				$news_opts[ $name ] = 'true';
			} else {
				$news_opts[ $name ] = 'false';
			}
		}
		
		// Add "http://" to links without it
		if (isset($news_opts['link']))
			if (preg_match('/^https?:\/\//', $news_opts['link']) === 0)
				$news_opts['link'] = "http://" . $news_opts['link'];
		return $news_opts;
	}
	
	public static function sticky_sanitize( $sticky_opts ) {
		$checkbox_names = array(
				'social_twitter',
				'social_facebook',
				'social_gplus',
				'social_linkedin',
				'social_pinterest',
				'social_email',
				'post_type_page',
				'post_type_post',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $sticky_opts[ $name ] ) ) {
				$sticky_opts[ $name ] = 'true';
			} else {
				$sticky_opts[ $name ] = 'false';
			}
		}
		return $sticky_opts;
	}
	
	public static function defaults_sanitize( $defaults ) {
		$checkbox_names = array(
				'subscribe_in_stp',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $defaults[ $name ] ) && $defaults[ $name ] === 'true' || $defaults[ $name ] === 'on' ) {
				$defaults[ $name ] = 'true';
			} else {
				$defaults[ $name ] = 'false';
			}
		}
		return $defaults;
	}
	
	public static function timestamps_sanitize( $timestamps ) {
		
		$adv = get_option('spp_player_advanced');
		if ($adv && isset($adv['timestamps_input']) && $adv['timestamps_input'] == 'true')
			$timestamps = self::timestamps_input_workaround();
		
		$checkbox_names = array(
				'show_times',
			);
		foreach( $checkbox_names as $name ) {
			if( isset( $timestamps[ $name ] ) && $timestamps[ $name ] === 'true' || $timestamps[ $name ] === 'on' ) {
				$timestamps[ $name ] = 'true';
			} else {
				$timestamps[ $name ] = 'false';
			}
		}
		
		// When the timestamps have changed, we invalidate the cache
		$timestamps['invalid_cache'] = 'true';
		
		// Change the times from with colons to seconds
		foreach ($timestamps['stamps'] as $url => $ts_array) {
			foreach ($ts_array as $time => $text) {
				unset($timestamps['stamps'][$url][$time]);
				$parts = array_reverse(explode(":", $time));
				$sec = $parts[0];
				if (isset($parts[1]))
					$sec += 60 * $parts[1];
				if (isset($parts[2]))
					$sec += 3600 * $parts[2];
				$timestamps['stamps'][$url][$sec] = $text;
				// If a timestamp for "0" is present without text, remove it
				// (Fixes bug when users try to delete "0:00" timestamps)
				if ($time == "0" && $text == "")
					unset($timestamps['stamps'][$url][0]);
			}
		}
		
		// Swap key and value for references
		if (isset($timestamps['refs'])) {
			foreach ($timestamps['refs'] as $url => $ref) {
				$timestamps['refs'][$ref] = $url;
				unset($timestamps['refs'][$url]);
			}
		}
		
		// Save which timestamp we're setting, so we can select it in the dropdown
		// Older PHP versions don't allow the construct array_keys(...)[0]
		$current_url = '';
		$stamps_keys = array_keys($timestamps['stamps']);
		if (count($stamps_keys) > 0)
			$current_url = $stamps_keys[0];
		$current_ref = '';
		$refs_keys = array_keys($timestamps['refs']);
		if (count($refs_keys) > 0)
			$current_ref = $refs_keys[0];
		$timestamps['last_set'] = $current_url;
		
		// The frontend is only sending the stamps and refs for one track, so we have
		// to put back in all of the other tracks
		$old_ts = get_option('spp_player_timestamps');
		if (!$old_ts)
			return $timestamps;
		
		if (isset($old_ts['stamps']))
			foreach ($old_ts['stamps'] as $url => $ts)
				if (!isset($timestamps['stamps'][$url]))
					$timestamps['stamps'][$url] = $ts;
				
		if (isset($old_ts['refs'])) {
			foreach ($old_ts['refs'] as $ref => $url) {
				if (isset($timestamps['refs'][$ref]) && $timestamps['refs'][$ref] !== $url) {
					// This ref already exists for a different URL.
					// Add a number until it's a unique ref.
					$i = 1;
					$orig_ref = $ref;
					while (in_array($ref, array_keys($old_ts['refs']))) {
						$ref = $orig_ref . "-" . strval($i);
						$i = $i + 1;
					}
					add_settings_error('spp-player-timestamps', 'used-ref',
						'The reference "' . $orig_ref . '" has already been used.'
						. '  The new reference "' . $ref . '" has been substitued.');
					$timestamps['refs'][$ref] = $timestamps['refs'][$orig_ref];
					$timestamps['refs'][$orig_ref] = $url;
				} else if ($url == $current_url) {
					// We're changing the ref for this URL.
					$timestamps['refs'][$current_ref] = $url;
				} else {
					// Just copy it from the old entry.
					$timestamps['refs'][$ref] = $url;
				}
			}
		}
		
		return $timestamps;
	}
	
	// HS 10331 had an issue where timestamp settings wouldn't take.  The settings in the
	// multidimensional arrays spp_player_timestamps[refs] and spp_player_timestamps[stamps]
	// were not present in $_POST, but did make it into php://input.  This function uses that
	// instead of the normal way (Wordpress settings API uses $_POST).  My best guess is his
	// use of the Suhosin extension.  Much of this is from https://stackoverflow.com/questions/5077969/
	public static function timestamps_input_workaround() {
		$pairs = explode("&", file_get_contents("php://input"));
		$vars = array();
		$refs = array();
		$stamps = array();
		foreach ($pairs as $pair) {
			$nv = explode("=", $pair);
			$name = urldecode($nv[0]);
			$value = urldecode($nv[1]);
			if (preg_match('/spp_player_timestamps\[refs\]\[(.*)\]/', $name, $matches)) {
				$url = $matches[1];
				$refs[$url] = $value;
			} else if (preg_match('/spp_player_timestamps\[stamps\]\[(.*)\]\[(.*)\]/', $name, $matches)) {
				$url = $matches[1];
				$time = $matches[2];
				if ($time === "")  // To copy a quirk
					$time = "0";   // of the normal way
				$stamps[$url][$time] = $value;
			} else {
				$vars[$name] = $value;
			}
		}
		$fixed_opt = array(
			'feed_url' => $vars['spp_player_timestamps[feed_url]'],
			'show_times' => $vars['spp_player_timestamps[show_times]'],
			'refs' => $refs,
			'stamps' => $stamps,
		);
		return $fixed_opt;
	}

}
