<?php

namespace Lumberjack\Helpers;
use Lumberjack\PostTypes\Post as Post;

class Acf
{
    public static function getUserData($id)
    {
        return [
            'image' => get_field('sidebar_profile_image', 'user_'.$id),
            'link' => get_field('sidebar_profile_link', 'user_'.$id)
        ];
    }

    public static function getPostFromId($id)
    {	
    	return new Post($id);
    }
}
