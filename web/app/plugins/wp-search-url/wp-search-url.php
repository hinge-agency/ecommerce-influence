<?php
/*
Plugin Name: Wp Search Url
Version: 2.0
Plugin URI: http://odrasoft.com/wp-search-url/
Description: Redirects ?s=swadesh searches to /search/swadesh
Author: swadeshswain
Author URI: http://odrasoft.com/
*/
if (is_admin()) {
   
add_action('admin_menu', 'wp_search_urll');

function wp_search_urll() {
    add_options_page('Wp Search Url', 'Wp Search Url', 'manage_options',  basename(__FILE__), 'wp_config_url_page');
}
function wp_config_url_page() {
?>
<div class="wrap">
			<h3>Wp Search Url Option</h3>
            
            <?php

	
    if ( isset($_POST['submit']) ) { 
        $nonce = $_REQUEST['_wpnonce'];
        if (! wp_verify_nonce($nonce, 'php-search-updatesettings' ) ) {
            die('security error');
        }
        $searchurl = $_POST['searchurl'];
        
   
        update_option( 'od_searchurl', $searchurl );
    
    } 
    $od_searchurl = get_option( 'od_searchurl' );

	?>
 
    
			<form method="post" action="" id="php_config_page">
				<?php wp_nonce_field('php-search-updatesettings'); ?>
              
                 
				<table class="form-table">
					<tbody>
						
                    <tr>
						<th><label>Allow to change default wordpres Search Url </label></th>
					
						<td>
                                         <Input type = 'Radio' Name ='searchurl' value= 'yes'
 <?php if ($od_searchurl == 'yes') echo 'checked="checked"'; ?>>
Yes

<Input type = 'Radio' Name ='searchurl' value= 'no'
 <?php if ($od_searchurl == 'no') echo 'checked="checked"'; ?>>
No
                        </td>
                      
                    </tr>
                    
                     
                                       
                    
                     
                    
                   
					</tbody>
				</table>
				<p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit" /></p>  
			</form>
		</div>
<?php
} // get_option('od_phpcontent');
} 


function wp_search_url() {
	global $wp_rewrite;
	if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() )
		return;

	$search_base = $wp_rewrite->search_base;
	if ( is_search() && !is_admin() && strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) === false ) {
		wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
		exit();
	}
}
?>
<?php if ( get_option('od_searchurl') == 'yes') {?>
<?php  add_action( 'template_redirect', 'wp_search_url' );?>
<?php } ?> 