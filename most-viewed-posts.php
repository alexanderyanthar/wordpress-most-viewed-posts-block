<?php
/**
 * Plugin Name:       Most Viewed Posts
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       most-viewed-posts
 *
 * @package           create-block
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function most_viewed_posts_most_viewed_posts_block_init()
{
	register_block_type(
		__DIR__ . '/build',
		array(
			'render_callback' => 'render_most_viewed_posts_block'
		)
	);
}
add_action('init', 'most_viewed_posts_most_viewed_posts_block_init');

function get_most_viewed_posts($attributes)
{
	// Query the most viewed posts
	$popular_posts = new WP_Query(
		array(
			'posts_per_page' => 5,
			'post_status' => 'publish',
			'meta_key' => 'post_views', // Use the meta key used for view count tracking
			'orderby' => 'meta_value_num',
			'order' => 'DESC'
		)
	);

	$popular_posts = $popular_posts->get_posts();
	// Initialize empty string to store post markup
	$posts = '';

	$posts = '<ul ' . get_block_wrapper_attributes() . '>';
	foreach ($popular_posts as $post) {
		setup_postdata($post);
		$title = get_the_title($post);
		$title = $title ? $title : __('(No title)', 'latest-posts');
		$permalink = get_permalink($post);
		$excerpt = get_the_excerpt($post);

		$posts .= '<li>';

		if ($attributes["displayFeaturedImage"] && has_post_thumbnail($post)) {
			$posts .= get_the_post_thumbnail($post, 'large');
		}

		$posts .= '<h5><a href="' . esc_url($permalink) . '">' . $title . '</a></h5>';
		$posts .= '<time datetime="' . esc_attr(get_the_date('c', $post)) . '">' . esc_html(get_the_date('', $post)) . '</time>';

		if (!empty($excerpt)) {
			$posts .= '<p>' . $excerpt . '</p>';
		}

		$posts .= '</li>';
	}
	wp_reset_postdata();
	$posts .= '</ul>';

	return $posts;

}

// Render the block output
function render_most_viewed_posts_block($attributes)
{
	$content = get_most_viewed_posts($attributes);
	echo $content;
}

// Hook function to render the block content
add_action('render_block_most-viewed-posts_most-viewed-posts', 'render_most_viewed_posts_block');
