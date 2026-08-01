<?php
/**
 * Plugin Name: Hide and Soul — Legacy URL Redirects
 * Description: 301s from the old Wix URLs to their WordPress equivalents. Lives in mu-plugins so it keeps working if the theme is ever switched.
 * Version: 1.0.0
 * Author: Hide and Soul Leatherworks
 *
 * Most of the old site's URLs are preserved by simply reusing the same page
 * slugs, and products keep working because the WooCommerce permalink base is
 * set to /product-page. This file covers the leftovers: Wix slugs that were
 * abbreviated, duplicated, or ended in .html.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Old path (no leading slash, no query string) => new path or full URL.
 *
 * Keys are compared lowercase with surrounding slashes trimmed.
 *
 * @return array<string, string>
 */
function hs_legacy_redirect_map() {
	$map = array(
		// Custom-order pages: Wix accumulated several near-duplicate slugs.
		'customorders'       => '/custom-orders/',
		'custom-orders'      => '/custom-orders/',
		'customladiesvests'  => '/custom-orders/',
		'customlegging'      => '/custom-orders/',

		// Shop sections.
		'shop-mens'          => '/product-category/mens/',
		'shop-womens'        => '/product-category/womens/',
		'home/mens.html'     => '/product-category/mens/',
		'home/womens.html'   => '/product-category/womens/',

		// Services and info.
		'repairs-patches'    => '/repairs-patches/',
		'care'               => '/care/',
		'bh-guide'           => '/black-hills-guide/',
		'crp'                => '/referrals/',

		// Wix system paths that have no WordPress equivalent.
		'account/my-account' => '/my-account/',
		'cart-page'          => '/cart/',
		'checkout-page'      => '/checkout/',
	);

	/**
	 * Filter the legacy redirect table.
	 *
	 * @param array $map Old path => new path.
	 */
	return apply_filters( 'hs_legacy_redirect_map', $map );
}

/**
 * Send a 301 when the request matches a legacy path.
 */
function hs_handle_legacy_redirects() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	// Only act on requests WordPress could not resolve.
	if ( ! is_404() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path    = strtolower( trim( wp_parse_url( $request, PHP_URL_PATH ) ?? '', '/' ) );

	if ( '' === $path ) {
		return;
	}

	$map = hs_legacy_redirect_map();

	if ( isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( $map[ $path ] ), 301 );
		exit;
	}

	// Wix product URLs sometimes carried a trailing numeric variant suffix,
	// e.g. /product-page/deerskin-chaps-1. Try the base slug before 404ing.
	if ( 0 === strpos( $path, 'product-page/' ) ) {
		$slug = substr( $path, strlen( 'product-page/' ) );
		$base = preg_replace( '/-\d+$/', '', $slug );

		if ( $base && $base !== $slug ) {
			$product = get_page_by_path( $base, OBJECT, 'product' );

			if ( $product ) {
				wp_safe_redirect( get_permalink( $product ), 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'hs_handle_legacy_redirects', 1 );

/**
 * Keep Wix's ?lightbox= and tracking params from creating duplicate URLs.
 *
 * @param string $canonical Canonical URL.
 * @return string
 */
function hs_strip_wix_query_args( $canonical ) {
	return remove_query_arg( array( 'lightbox', 'wixads', '_ga' ), $canonical );
}
add_filter( 'get_canonical_url', 'hs_strip_wix_query_args' );
