<?php
/**
 * The Template for displaying all single posts
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;
use Lumberjack\PostTypes\Episode;

$context = Timber::get_context();

$post = new Episode();

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

Timber::render(['episode.twig', 'generic-page.twig'], $context);
