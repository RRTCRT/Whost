<?php
/**
 * Front-end trimming.
 *
 * Wix shipped a lot of runtime for what is a mostly-static brochure + shop.
 * These removals are the cheap wins that keep Hostinger's shared hosting fast.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Drop head cruft that this site does not use.
 */
function hs_clean_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
}
add_action( 'init', 'hs_clean_head' );

/**
 * Remove the emoji polyfill — modern browsers render emoji natively.
 */
function hs_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', function ( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
	} );
}
add_action( 'init', 'hs_disable_emojis' );

/**
 * The theme ships its own styles for core blocks; skip the global sheet.
 */
function hs_dequeue_block_library() {
	if ( is_admin() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'hs_dequeue_block_library', 100 );

/**
 * Lazy-load and async-decode images that WordPress has not already flagged.
 *
 * @param string $html Image markup.
 * @return string
 */
function hs_image_attributes( $html ) {
	if ( false === strpos( $html, 'decoding=' ) ) {
		$html = str_replace( '<img ', '<img decoding="async" ', $html );
	}

	return $html;
}
add_filter( 'post_thumbnail_html', 'hs_image_attributes' );
add_filter( 'get_avatar', 'hs_image_attributes' );

/**
 * Preconnect to the payment gateway only where checkout actually runs.
 *
 * @param string[] $urls Resource hints.
 * @param string   $rel  Hint relationship.
 * @return string[]
 */
function hs_resource_hints( $urls, $rel ) {
	if ( 'preconnect' !== $rel ) {
		return $urls;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$urls[] = array( 'href' => 'https://js.stripe.com', 'crossorigin' );
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'hs_resource_hints', 10, 2 );
