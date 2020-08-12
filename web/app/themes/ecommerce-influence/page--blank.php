<?php
/*
Template Name: Blank Template
*/

use Timber\Timber;
use Lumberjack\PostTypes\Post;

$context = Timber::get_context();
$post = new Post();

$context['post'] = $post;
$context['title'] = $post->title;
$context['content'] = $post->content;

Timber::render('blank.twig', $context);
