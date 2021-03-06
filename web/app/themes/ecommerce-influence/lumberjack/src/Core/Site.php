<?php

namespace Lumberjack\Core;

use Timber\Timber as Timber;
use Timber\Site as TimberSite;
use Timber\Helper as TimberHelper;
use Timber\FunctionWrapper as TimberFunctionWrapper;
use Lumberjack\Core\Menu;
use Lumberjack\Functions\Blocks;
use Lumberjack\PostTypes\Post as Post;
use Lumberjack\Helpers\Acf as AcfHelper;

class Site extends TimberSite
{
    public function __construct()
    {
        add_filter('get_twig', [$this, 'addToTwig']);
        add_filter('timber_context', [$this, 'addToContext']);

        parent::__construct();
    }

    public function addToContext($data)
    {
        $data['is_home'] = is_home();
        $data['is_front_page'] = is_front_page();
        $data['is_logged_in'] = is_user_logged_in();

        // Get the page title, and prefix it with ' | ' if it exists (for use in html title)
        $data['wp_title'] = new TimberFunctionWrapper('wp_title', ['|', false, 'right']);

        // In Timber, you can use TimberMenu() to make a standard Wordpress menu available to the
        // Twig template as an object you can loop through. And once the menu becomes available to
        // the context, you can get items from it in a way that is a little smoother and more
        // versatile than Wordpress's wp_nav_menu. (You need never again rely on a
        // crazy "Walker Function!")
        $data['main_menu'] = new Menu('main-nav');
        $data['featured_menu'] = new Menu('featured-nav');

        //Get Site Settings
        $data['site_settings'] = get_fields('options');

        // Get asset dir for loading inline images
        $data['assets'] = get_template_directory_uri().'/assets';

        // Get categories for use later
        $data['categories'] = Timber::get_terms('category', ['hide_empty' => true, 'include' => array(get_cat_ID('articles'),get_cat_ID('podcasts'))]);
        $data['tags'] = Timber::get_terms('tag', ['hide_empty' => true]);

        // Add ACF block layout fields
        $data['blocks'] = new Blocks();

        // Get ACF sidebar
        $data['sidebar'] = get_field('sidebar');

        // Get ACF sidebar
        $data['top_drip_form'] = get_field('hide_top_drip_form');

        // ACF Helper functions
        $data['acf_helper'] = new AcfHelper();

        $data['hide_top_drip_form'] = (isset($_COOKIE['hidetopdripform']) ? true : false);

        $data['post_obj'] = new Post();

        return $data;
    }

    public function addToTwig($twig)
    {
        // this is where you can add your own functions to twig
        // $twig->addExtension(new Twig_Extension_StringLoader());
        // $twig->addFilter('myfoo', new Twig_Filter_Function('myfoo'));

        return $twig;
    }
}
