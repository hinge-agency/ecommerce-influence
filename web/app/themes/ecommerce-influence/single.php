<?php

/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;
use Lumberjack\PostTypes\Post;

$context = Timber::get_context();

$post = new Post();

$context['post'] = $post;

$context['title'] = $post->title;
$context['content'] = $post->content;

$context['sidebar'] = ($context['site_settings']['sidebar'] ? $context['site_settings']['sidebar'] : '');

$latest_posts = Timber::get_posts(array(
    'posts_per_page' => 3,
    'orderby' => 'post__in'
));

$context['latest_posts'] = $latest_posts;

Timber::render(['episode.twig'], $context);
