<?php

namespace Lumberjack\Config;

use Lumberjack\PostTypes\Project;
use Lumberjack\PostTypes\Episode;
use Lumberjack\PostTypes\Test;

class CustomPostTypes
{
    public static function register()
    {
        add_action('init', [get_called_class(), 'types']);
    }

    public static function types()
    {
        // Project
        register_post_type(
            Project::postType(),
            [
                'labels' => [
                    'name' => __('Projects'),
                    'singular_name' => __('Project')
                ],
                'public' => true,
                'has_archive' => false,
                'supports' => [
                    'title',
                    'author',
                    'editor',
                    'thumbnail'
                ],
                'rewrite' => [
                    'slug' => 'project',
                ],
                'show_in_nav_menus' => true,
            ]
        );

        register_post_type(
            Episode::postType(),
            [
                'labels' => [
                    'name' => __('Episodes'),
                    'singular_name' => __('Episode')
                ],
                'public' => true,
                'has_archive' => true,
                'supports' => [
                    'title',
                    'author',
                    'editor',
                    'thumbnail'
                ],
                'taxonomies'  => array( 'category' ),
                'rewrite' => [
                    'slug' => 'episode',
                ],
                'show_in_nav_menus' => true,
            ]
        );
    }
}
