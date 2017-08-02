<?php

namespace Lumberjack\Helpers;

class Acf
{
    public static function getUserData($id)
    {
        return [
            'image' => get_field('sidebar_profile_image', 'user_'.$id),
            'link' => get_field('sidebar_profile_link', 'user_'.$id)
        ];
    }
}
