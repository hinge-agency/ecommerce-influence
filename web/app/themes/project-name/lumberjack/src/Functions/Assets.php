<?php

namespace Lumberjack\Functions;

class Assets
{
    /**
     * En-queue required assets
     *
     * @param  string  $filter   The name of the filter to hook into
     * @param  integer $priority The priority to attach the filter with
     */
    public static function load($filter = 'wp_enqueue_scripts', $priority = 10)
    {
        // Register the filter
        add_filter($filter, function ($paths) {

            $templateDirectory = get_stylesheet_directory();
            $templateDirectoryUri = get_template_directory_uri();
            $bowerDirectory = $templateDirectoryUri.'/bower_components/';

            // Styles
            $styles = '/assets/css/style.min.css';
            wp_enqueue_style('styles', $templateDirectoryUri.$styles, [], filemtime($templateDirectory.$styles));
            
            // Scripts
            wp_deregister_script('jquery');
            wp_register_script('jquery', $bowerDirectory.'jquery/dist/jquery.min.js', false, '2.2.3', false);
            wp_enqueue_script('jquery');

            // Vendor Scripts
            $vendorScripts = '/assets/js/vendor.min.js';
            if (file_exists($templateDirectory.$vendorScripts)){
                wp_register_script('vendor-scripts', $templateDirectoryUri.$vendorScripts, [], filemtime($templateDirectory.$vendorScripts), true);
                wp_enqueue_script('vendor-scripts');
            }

            // Custom Scripts
            $customScripts = '/assets/js/custom.min.js';
            if (file_exists($templateDirectory.$customScripts)){
                wp_register_script('custom-scripts', $templateDirectoryUri.$customScripts, [], filemtime($templateDirectory.$customScripts), true);
                wp_enqueue_script('custom-scripts');
            }
        });
    }
}
