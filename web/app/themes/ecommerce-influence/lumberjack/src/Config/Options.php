<?php

namespace Lumberjack\Config;

class Options
{
	public static function register()
    {
        if( function_exists('acf_add_options_page') ) {
			$option_page = acf_add_options_page(array(
				'page_title' 	=> 'Site Settings',
				'menu_title' 	=> 'Site Settings',
				'menu_slug' 	=> 'site-settings',
				'capability' 	=> 'edit_posts',
				'redirect' 	=> false
			));
			// Reorder the menu to foce the options page to be at the top
			self::reorder();
		}
    }

     public static function reorder()
    {
    	add_filter('custom_menu_order', array(__CLASS__, 'customMenuOrder'));
		add_filter('menu_order', array(__CLASS__, 'customMenuOrder'));
    }
      public static function customMenuOrder($menu_ord)
    {

    	if (!$menu_ord) return true;  
		    
	    // vars
	    $menu = 'site-settings';
	    
	    // remove from current menu
	    $menu_ord = array_diff($menu_ord, array( $menu ));
	    
	    // append after index.php [0]
	    array_splice( $menu_ord, 1, 0, array( $menu ) );
	    
	    // return
	    return $menu_ord;
    }
}