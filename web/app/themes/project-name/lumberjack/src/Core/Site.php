<?php

namespace Lumberjack\Core;

use Timber\Site as TimberSite;
use Timber\Helper as TimberHelper;
use Timber\FunctionWrapper as TimberFunctionWrapper;
use Lumberjack\Core\Menu;
use Lumberjack\Functions\Blocks;

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
        $data['menu'] = new Menu('main-nav');

        // Get asset dir for loading inline images
        $data['assets'] = get_template_directory_uri().'/assets';

        // Add ACF block layout fields
        $data['blocks'] = new Blocks();

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
