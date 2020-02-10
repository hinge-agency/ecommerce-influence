<?php
// Default values for the general settings page
$defaults = array(
		'license_key' => '',
	);
$saved_options = get_option( 'spp_player_general', $defaults );
$processed_options = array_merge( $defaults, $saved_options );
extract( $processed_options );
?>

<table class="form-table"><tbody><tr>
	<th scope="row">License Key:</th>
	<td><input type="text" name="spp_player_general[license_key]" value="<?php echo $license_key ?>" size="50"></td>
</tr></tbody></table>
<p class="description"><small>
	Your license key was delivered to you at the time of purchase, and in your email receipt. If you have any
	difficulty locating your license key, please email <a href="mailto:support@smartpodcastplayer.com">
	support@smartpodcastplayer.com</a>.
</small></p>
