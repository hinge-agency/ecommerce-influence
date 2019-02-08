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

/* Use the site_settings sidebar (IT SHOULD BE ALWAYS SETUP), or override it with specific sidebar per page/post */
$context['sidebar'] = !$context['sidebar'] ? $context['site_settings']['sidebar'] : $context['sidebar'];


$latest_posts = Timber::get_posts(array(
    'posts_per_page' => 3,
    'category__not_in' => $context['transcript_id'],
    'orderby' => 'post__in'
));

$context['latest_posts'] = $latest_posts;

$tags = $post->tags();

$tag_ids = [];
foreach ($tags as $tag) {
    array_push($tag_ids,$tag->term_id);
}

$context['related_posts'] = Timber::get_posts(array(
    'posts_per_page' => -1,
    'post__not_in' => array($post->id),
    'cat' => $post->category->id,
    'tag__in' => $tag_ids,
));

Timber::render(['episode.twig'], $context);
