<?php
/*
Template Name: Episode Template
*/

use Timber\Timber;
use Lumberjack\PostTypes\Post;

$context = Timber::get_context();
$post = new Post();

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

$episodes = Timber::get_posts(array(
    'posts_per_page' => 3,
    'orderby' => 'post__in'
));

$context['episodes'] = $episodes;

Timber::render('episode.twig', $context);
