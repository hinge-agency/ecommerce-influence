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
        

    }
}
