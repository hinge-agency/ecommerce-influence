<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

use Timber\Timber;
use Lumberjack\PostTypes\Post;

$templates = ['episodes.twig'];

$data = Timber::get_context();

$data['title'] = 'Archive';

if (is_day()) {
    $data['title'] = 'Archive: '.get_the_date('D M Y');
} elseif (is_month()) {
    $data['title'] = 'Archive: '.get_the_date('M Y');
} elseif (is_year()) {
    $data['title'] = 'Archive: '.get_the_date('Y');
} elseif (is_tag()) {
    $data['title'] = single_tag_title('', false);
} elseif (is_category()) {
    $data['title'] = single_cat_title('', false);
    array_unshift($templates, 'archive-'.get_query_var('cat').'.twig');
} elseif (is_post_type_archive()) {
    $data['title'] = post_type_archive_title('', false);
    array_unshift($templates, 'archive-'.get_post_type().'.twig');
}

$searchQuery = get_search_query();
$count_found = 0;
$data['search'] = '';

// TODO: Currently only works for posts, fix for custom post types
$data['posts'] = Post::query();

//BASE VARIABLES
$url_base = '';
$posts_per_page = 9;
$count_pages = 0;
$data['allPosts'] = false;

//ALL POSTS 
$getPosts = wp_count_posts();
$count_posts = ($getPosts ? $getPosts->publish : 0);

//POSTS IN CATEGORY
$cat = get_queried_object();
$count_cat = ($cat ? $cat->count : '');

//CHECK IF SEARCH FOR ALL OR CATEGORY
if($searchQuery){

    $query_searched_posts = [
        'posts_per_page' => -1,
        's' => $searchQuery
    ];

    $data['search'] = '?s=' . $searchQuery;

    $found = Timber::get_posts($query_searched_posts); 
    $count_found = count($found);

    if ($count_found){
        $count_pages = ceil($count_found / $posts_per_page);
    }

}elseif ($count_cat){
    $count_pages = ceil($count_cat / $posts_per_page);
    $url_base = $url_base . '/' . get_option('category_base');
}else{
    $count_pages = ceil($count_posts / $posts_per_page);
    $url_base = $url_base . '/posts/';

    $data['allPosts'] = true;
}

//URL CONSTRACT FOR PAGINATION
$data['next_page'] = '#';
$data['prev_page'] = '#';

$cat_name = ($cat ? $cat->slug : '');
$data['current_page'] = (get_query_var('paged')) ? get_query_var('paged') : 1;

if ($data['current_page'] == 1){

    if ($count_pages > 1){
        $data['next_page'] = $url_base . $cat_name . '/page/' . ($data['current_page'] + 1);
    }

}elseif ($data['current_page'] < $count_pages){

    $data['next_page'] = $url_base . $cat_name . '/page/' . ($data['current_page'] + 1);
    $data['prev_page'] = $url_base . $cat_name . '/page/' . ($data['current_page'] - 1);

}elseif ($data['current_page'] == $count_pages){

    $data['prev_page'] = $url_base . $cat_name . '/page/' . ($data['current_page'] - 1);

}

$query_args = [
    'posts_per_page' => $posts_per_page,
    'orderby' => 'post__in',
    'category_name' => $cat_name,
    'paged' => $data['current_page']
];

if ($searchQuery){
    $query_args['s'] = $searchQuery;
}

$archive_posts = Post::query($query_args);

$data['archive_posts'] = $archive_posts;

Timber::render($templates, $data);