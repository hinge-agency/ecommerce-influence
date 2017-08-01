<?php
/*
Template Name: Home Page Template
*/

use Timber\Timber;
use Lumberjack\PostTypes\Post;

$context = Timber::get_context();

/* PAGE */

$post = new Post();

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

/* EPISODES */

$episodes = Timber::get_posts(array(
    'posts_per_page' => 3,
    'orderby' => 'post__in'
));

$old_episodes = Timber::get_posts(array(
	'offset' => 3,
    'posts_per_page' => 9,
    'orderby' => 'post__in'
));

$context['episodes'] = $episodes;
$context['old_episodes'] = $old_episodes;

Timber::render('home.twig', $context);
