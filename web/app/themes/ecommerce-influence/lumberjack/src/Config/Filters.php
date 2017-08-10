<?php

namespace Lumberjack\Config;

class Filters
{
    public static function register(){
       add_filter('embed_oembed_html', [get_called_class(), 'my_embed_oembed_html'], 99, 4);
    }
   
    public static function my_embed_oembed_html($html, $url, $attr, $post_id) {
        return '<div class="iframeWrapper">' . $html . '</div>';
    }
}
