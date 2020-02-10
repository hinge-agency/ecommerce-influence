<?php

// The filename depends on whether versions are in the filename or the query string
$ts_settings_js_file = 'timestamps-settings-' . SPP_Core::VERSION . '.min.js';
$version_string = null;
$advanced_options = get_option( 'spp_player_advanced' );
if( isset( $advanced_options[ 'versioned_assets' ] ) && $advanced_options[ 'versioned_assets' ] === 'false') {
	$ts_settings_js_file = 'timestamps-settings.min.js';
	$version_string = SPP_Core::VERSION;
}
		
wp_register_script(
		SPP_Core::PLUGIN_SLUG . '-timestamps-settings-script',
		SPP_ASSETS_URL . 'js/admin/' . $ts_settings_js_file,
		array('jquery'),
		$version_string,
		false);
wp_enqueue_script(SPP_Core::PLUGIN_SLUG . '-timestamps-settings-script');

// Default values for the timestamps page
$defaults = array(
		'show_times' => 'true',
		'feed_url' => '',
	);
$saved_options = get_option( 'spp_player_timestamps', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
?>

<h4>Create clickable timestamps for your podcast episodes. Set up the timestamps once and they will be available for use on any page within your website. <a href="https://support.smartpodcastplayer.com/article/242-how-to-set-up-clickable-timestamps" target="_blank">Learn more about this feature.</a></h4>
<br>

<table class="form-table"><tbody>
	<tr><th scope="row">RSS feed URL: </th>
	<td><input type="text" name="spp_player_timestamps[feed_url]" id="spp-timestamps-feed-url" value="<?php echo $feed_url ?>" size="50"></td></tr>
	<tr><th scope="row">Include time in brackets:</th>
	<td><input type="checkbox" name="spp_player_timestamps[show_times]" <?php checked($show_times, 'true') ?>></td></tr>
</tbody></table>
<em>Check this box to include the time in brackets before each clickable timestamp.</em>
<br><br><br>

<div class="spp-loading-feed">Loading feed, please wait...</div>
<div class="spp-feed-error">There was an error loading the feed at "<?php echo $feed_url; ?>".
	<input type="button" id="spp-reload-feed" class="button button-secondary" value="Reload feed">
</div>

Select the episode from your feed. <span class="spp-track-selector-helper">Click "Save Changes" or "Revert" to enable.</span>
<br><div class="spp-timestamp-table"></div><br>

To give your episode a custom name, enter a short text string (such as SPI352) in the box below. Otherwise, we will name it for you.
<table class="form-table spp-timestamp-reference-options"><tbody><tr>
	<th scope="row">Reference:</th>
	<td><input type="text" class="spp-timestamp-ref" size="10"></td>
</tr></tbody></table>
Copy the shortcode below and place it where you would like your clickable timestamps to appear.
<table class="form-table spp-timestamp-reference-options"><tbody><tr>
	<th scope="row">Shortcode to use:</th>
	<td>
		<pre class="spp-timestamp-shortcode"></pre>
		<button type="button" class="spp-copy-timestamp-shortcode button button-secondary">
			Copy to clipboard
		</button>
	</td>
</tr></tbody></table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"/>
	<input type="button" id="spp-timestamps-revert" class="button button-secondary" value="Revert">
</p>

<pre style="display:none">
	<?php
		$adv = get_option('spp_player_advanced');
		if ($adv && isset($adv['debug_output']) && $adv['debug_output'] == 'true') {
			print_r($processed_options);
		}
	?>
</pre>
