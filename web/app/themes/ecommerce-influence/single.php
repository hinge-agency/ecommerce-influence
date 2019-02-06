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

// Get content parts
$content_parts = get_extended( $post->content );

$context['post'] = $post;
$context['title'] = $post->title;


$context['transcript_id'] = get_cat_ID('transcripts');

// Before More Tag
$context['excerpt'] = ($content_parts['extended'] != '' ? $content_parts['main'] : false);

// After More Tag
$context['content'] = ($content_parts['extended'] ? $content_parts['extended'] : $content_parts['main']);

//if (!get_field('sidebar')) {
//    $context['sidebar'] = $context['site_settings']['sidebar'];
//}

$context['sidebar'] = !$context['sidebar'] ? $context['site_settings']['sidebar'] : $context['sidebar'];


$latest_posts = Timber::get_posts(array(
    'posts_per_page' => 3,
    'category__not_in' => $context['transcript_id'],
    'orderby' => 'post__in'
));

$context['latest_posts'] = $latest_posts;

Timber::render(['episode.twig'], $context);
