<?php
/*
Template Name: Home Page Template
*/

use Timber\Timber;
use Lumberjack\PostTypes\Post;
use Lumberjack\Functions\Blocks;


$context = Timber::get_context();

/* PAGE */

$post = new Post();
$blocks = new Blocks();

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

/* EPISODES */

$episodes = Timber::get_posts(array(
    'post_type' => 'episode',
    'posts_per_page' => 3,
    'orderby' => 'post__in'
));

$context['episodes'] = $episodes;

Timber::render('home.twig', $context);
