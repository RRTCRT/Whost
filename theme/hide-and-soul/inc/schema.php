<?php
/**
 * Structured data.
 *
 * A leather shop with a physical storefront lives and dies on local search.
 * Wix emitted its own LocalBusiness markup; this replaces it so the Google
 * Business Profile keeps matching the site after the move.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Emit LocalBusiness JSON-LD in the head of every page.
 */
function hs_local_business_schema() {
	$specs = array();

	foreach ( hs_hours() as $day => $window ) {
		if ( ! is_array( $window ) ) {
			continue;
		}

		$specs[] = array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => 'https://schema.org/' . $day,
			'opens'     => $window['opens'],
			'closes'    => $window['closes'],
		);
	}

	$same_as = array_values( array_filter( array(
		hs_info( 'facebook' ),
		hs_info( 'instagram' ),
	) ) );

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'ClothingStore',
		'@id'         => home_url( '/#business' ),
		'name'        => hs_info( 'legal_name' ),
		'url'         => home_url( '/' ),
		'telephone'   => hs_info( 'phone' ),
		'email'       => hs_info( 'email' ),
		'priceRange'  => hs_info( 'price_range' ),
		'description' => get_bloginfo( 'description' ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => hs_info( 'street' ),
			'addressLocality' => hs_info( 'city' ),
			'addressRegion'   => hs_info( 'region' ),
			'postalCode'      => hs_info( 'postal' ),
			'addressCountry'  => hs_info( 'country' ),
		),
		'geo'         => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => hs_info( 'latitude' ),
			'longitude' => hs_info( 'longitude' ),
		),
		'openingHoursSpecification' => $specs,
	);

	if ( $same_as ) {
		$data['sameAs'] = $same_as;
	}

	if ( has_custom_logo() ) {
		$logo = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		if ( $logo ) {
			$data['logo']  = $logo;
			$data['image'] = $logo;
		}
	}

	hs_print_jsonld( $data );
}
add_action( 'wp_head', 'hs_local_business_schema', 5 );

/**
 * Breadcrumb JSON-LD for interior pages.
 */
function hs_breadcrumb_schema() {
	if ( is_front_page() || ! is_singular() ) {
		return;
	}

	// Yoast already emits its own breadcrumb graph — don't duplicate it.
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'hide-and-soul' ),
			'item'     => home_url( '/' ),
		),
	);

	$post     = get_post();
	$chain    = array();
	$parent   = $post->post_parent;
	$position = 2;

	while ( $parent ) {
		$chain[] = $parent;
		$parent  = wp_get_post_parent_id( $parent );
	}

	foreach ( array_reverse( $chain ) as $ancestor ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title( $ancestor ),
			'item'     => get_permalink( $ancestor ),
		);
	}

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => get_the_title( $post ),
		'item'     => get_permalink( $post ),
	);

	hs_print_jsonld( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	) );
}
add_action( 'wp_head', 'hs_breadcrumb_schema', 6 );

/**
 * Print a JSON-LD block.
 *
 * @param array $data Schema graph.
 */
function hs_print_jsonld( array $data ) {
	echo '<script type="application/ld+json">'
		. wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n";
}
