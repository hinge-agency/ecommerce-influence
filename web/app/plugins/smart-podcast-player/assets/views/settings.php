<?php

$tabs = array( 
  'general' => array( 'label' => 'General', 'settings' => 'spp-player-general' ), 
  'defaults' => array( 'label' => 'Player Defaults', 'settings' => 'spp-player-defaults'),
  'news' => array( 'label' => 'Featured Button', 'settings' => 'spp-player-email' ),
  'sticky' => array( 'label' => 'Sticky Player', 'settings' => 'spp-player-sticky' ),
  'timestamps' => array( 'label' => 'Timestamps', 'settings' => 'spp-player-timestamps' ),
  'advanced' => array( 'label' => 'Advanced', 'settings' => 'spp-player-advanced'),
);


$current_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : 'general';

?>
<div class="wrap settings-<?php echo $current_tab; ?> smart-podcast-player-settings">
	<?php
	if( ! SPP_Core::is_paid_version() ) {
	?>
		<div class="notice notice-error">
			<p style="line-height: 30px;"><?php _e( 'Please enter your Smart Podcast Player license key to get updates and support! <a href="' . SPP_SETTINGS_URL . '" class="button" style="float: right;">Go to Settings</a>', 'smart-podcast-player' ); ?></p>
		</div>
	<?php
	}
	if( ! extension_loaded("xml") ) {
	?>
		<div class="notice notice-error">
			<p style="line-height: 30px;">This PHP installation is missing XML support.  Without it, Smart Podcast Player
			will be unable to read your RSS feed.  Please contact your web host's support and ask them to install to install the "xml" PHP extension.</p>
		</div>
	<?php }
	if( ! extension_loaded("mbstring") ) {
		$gen = get_option("spp_player_general");
		if (!$gen || !isset($gen["mbstring_notice_dismissed"]) || $gen["mbstring_notice_dismissed"] !== true) {
		?>
			<div class="notice notice-warning spp-mbstring-notice">
				<p style="line-height: 30px;">This PHP installation is missing multibyte support.  Without it, Smart Podcast Player
				may not properly display characters such as quotation marks, dashes, or symbols.
				If you see unusual characters in SPP, please contact your web host's support and ask them to install the "mbstring" PHP extension.</p>
				<a href="#" class="spp-mbstring-dismiss">Dismiss this notice</a>
			</div>
		<?php } ?>
	<?php } ?>

  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <div class="nav-tabs">
    <h2 class="nav-tab-wrapper">
    <?php foreach( $tabs as $key => $tab ) : ?>
          <a href="<?php echo SPP_SETTINGS_URL; ?>&tab=<?php echo $key; ?>" class="<?php echo $current_tab == $key ? 'nav-tab nav-tab-active' : 'nav-tab'; ?>"><?php echo $tab['label']; ?></a>    
    <?php endforeach; ?>
    </h2>
  </div>
   
  <?php if( isset( $tabs[ $current_tab ] ) ) {
  
	  // "Advanced" has a special action.
	  if( $current_tab == 'advanced' ) { ?>
		<?php $redirect = urlencode( $_SERVER['REQUEST_URI'] ); ?>
		<form method="POST" action="<?php echo admin_url( 'admin-post.php' ); ?>">
		  <?php if( isset( $_GET["spp_cache"] ) && $_GET["spp_cache"] == 'cleared' ) { ?>
			<div class="updated">
			  <p>Smart Podcast Player cache cleared.</p>
			</div>
		  <?php } ?>
		  <input type="hidden" name="action" value="clear_spp_cache">
		  <?php wp_nonce_field( "clear_spp_cache", "clear_spp_cache_nonce", FALSE ); ?>
		  <input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
		  <table class="form-table">
			<tr>
			  <th scope="row">Clear SPP Cache: </th>
			  <td>
				<input type="submit" name="submit" id="submit" class="button button-secondary" value="Clear Cache">
			  </td>
			</tr>
		  </table>
		</form>
			
	  <?php } ?>
	  
	<form method="POST" action="options.php" class="spp-settings-form">
		<?php
			settings_fields( $tabs[ $current_tab ]['settings'] );
			if ($current_tab == 'news')
				include 'settings_news.php';
			else if ($current_tab == 'sticky')
				include 'settings_sticky.php';
			else if ($current_tab == 'timestamps')
				include 'settings_timestamps.php';
			else if ($current_tab == 'defaults')
				include 'settings_defaults.php';
			else if ($current_tab == 'advanced')
				include 'settings_advanced.php';
			else if ($current_tab == 'general')
				include 'settings_general.php';
			
			if ($current_tab !== 'timestamps') {
				submit_button();
			}
		?>
	</form>
	
  <?php } ?>

</div>
