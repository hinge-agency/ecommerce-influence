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

/* posts */

$context['transcript_id'] = get_cat_ID('transcripts');

$latest_posts = Timber::get_posts(array(
    'posts_per_page' => 3,
    'category__not_in' => $context['transcript_id'],
    'orderby' => 'post__in'
));

$archive_posts = Timber::get_posts(array(
	'offset' => 3,
    'posts_per_page' => 9,
    'category__not_in' => $context['transcript_id'],
    'orderby' => 'post__in'
));

$context['latest_posts'] = $latest_posts;
$context['archive_posts'] = $archive_posts;

Timber::render('home.twig', $context);