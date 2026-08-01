<?php
/**
 * Customizer controls for business info and the homepage hero.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hs_customize_register( $wp_customize ) {
	$defaults = hs_info_defaults();

	$wp_customize->add_panel( 'hs_panel', array(
		'title'    => __( 'Hide and Soul', 'hide-and-soul' ),
		'priority' => 20,
	) );

	/* ---------------------------------------------------------------
	 * Business info
	 * --------------------------------------------------------------- */

	$wp_customize->add_section( 'hs_business', array(
		'title'       => __( 'Business Info', 'hide-and-soul' ),
		'panel'       => 'hs_panel',
		'description' => __( 'Phone, address and social links used across the header, footer and search-engine listing.', 'hide-and-soul' ),
	) );

	$fields = array(
		'legal_name'  => array( __( 'Business name', 'hide-and-soul' ), 'text' ),
		'tagline'     => array( __( 'Tagline', 'hide-and-soul' ), 'text' ),
		'phone'        => array( __( 'Shop phone', 'hide-and-soul' ), 'text' ),
		'custom_phone' => array( __( 'Custom orders phone (Jenn)', 'hide-and-soul' ), 'text' ),
		'email'       => array( __( 'Email', 'hide-and-soul' ), 'email' ),
		'street'      => array( __( 'Street address', 'hide-and-soul' ), 'text' ),
		'city'        => array( __( 'City', 'hide-and-soul' ), 'text' ),
		'region'      => array( __( 'State', 'hide-and-soul' ), 'text' ),
		'postal'      => array( __( 'ZIP', 'hide-and-soul' ), 'text' ),
		'latitude'    => array( __( 'Latitude', 'hide-and-soul' ), 'text' ),
		'longitude'   => array( __( 'Longitude', 'hide-and-soul' ), 'text' ),
		'hours_note'  => array( __( 'Hours note', 'hide-and-soul' ), 'text' ),
		'season_note' => array( __( 'Seasonal note (top bar)', 'hide-and-soul' ), 'text' ),
		'facebook'    => array( __( 'Facebook URL', 'hide-and-soul' ), 'url' ),
		'instagram'   => array( __( 'Instagram URL', 'hide-and-soul' ), 'url' ),
		'pinterest'   => array( __( 'Pinterest URL', 'hide-and-soul' ), 'url' ),
		'youtube'     => array( __( 'YouTube URL', 'hide-and-soul' ), 'url' ),
	);

	foreach ( $fields as $key => $field ) {
		list( $label, $type ) = $field;

		$sanitize = 'sanitize_text_field';
		if ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		}

		$wp_customize->add_setting( 'hs_' . $key, array(
			'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( 'hs_' . $key, array(
			'label'   => $label,
			'section' => 'hs_business',
			'type'    => 'url' === $type ? 'url' : ( 'email' === $type ? 'email' : 'text' ),
		) );
	}

	/* ---------------------------------------------------------------
	 * Homepage hero
	 * --------------------------------------------------------------- */

	$wp_customize->add_section( 'hs_hero', array(
		'title' => __( 'Homepage Hero', 'hide-and-soul' ),
		'panel' => 'hs_panel',
	) );

	$hero = array(
		'hero_heading'  => array( __( 'Heading', 'hide-and-soul' ), 'Leather that outlives the trip.' ),
		'hero_sub'      => array( __( 'Sub-heading', 'hide-and-soul' ), 'Deer, elk and bison hides cut, sewn and fitted by our family in the Black Hills of South Dakota — for over forty years.' ),
		'hero_cta_text' => array( __( 'Button text', 'hide-and-soul' ), 'Shop the collection' ),
		'hero_cta_url'  => array( __( 'Button link', 'hide-and-soul' ), '/shop/' ),
		'hero_cta2_text' => array( __( 'Second button text', 'hide-and-soul' ), 'Custom orders' ),
		'hero_cta2_url'  => array( __( 'Second button link', 'hide-and-soul' ), '/custom-orders/' ),
	);

	foreach ( $hero as $key => $field ) {
		$wp_customize->add_setting( 'hs_' . $key, array(
			'default'           => $field[1],
			'sanitize_callback' => false !== strpos( $key, 'url' ) ? 'esc_url_raw' : 'sanitize_text_field',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( 'hs_' . $key, array(
			'label'   => $field[0],
			'section' => 'hs_hero',
			'type'    => 'text',
		) );
	}

	$wp_customize->add_setting( 'hs_hero_image', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'hs_hero_image', array(
		'label'     => __( 'Background image', 'hide-and-soul' ),
		'section'   => 'hs_hero',
		'mime_type' => 'image',
	) ) );

	/* ---------------------------------------------------------------
	 * Shop behaviour
	 * --------------------------------------------------------------- */

	$wp_customize->add_section( 'hs_shop', array(
		'title'       => __( 'Shop Behaviour', 'hide-and-soul' ),
		'panel'       => 'hs_panel',
		'description' => __( 'Switch the store between a full checkout and a call-to-order catalog.', 'hide-and-soul' ),
	) );

	// Defaults on. The Wix shop page told customers directly: "it is hard for
	// our small business to maintain an accurate inventory on our website.
	// Please contact us for our current styles, colors and sizes." A live
	// checkout would sell stock that may not exist, so the faithful migration
	// of current behaviour is a catalog. Untick when you're ready to sell.
	$wp_customize->add_setting( 'hs_catalog_mode', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'hs_catalog_mode', array(
		'label'       => __( 'Catalog mode (no checkout)', 'hide-and-soul' ),
		'description' => __( 'Hides cart and checkout everywhere and replaces "Add to cart" with a call-to-order panel. Products, prices and photos stay visible. On by default, matching how the old site worked — untick it when you are ready to take payments online.', 'hide-and-soul' ),
		'section'     => 'hs_shop',
		'type'        => 'checkbox',
	) );

	// Live-preview the simple text bits.
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->get_setting( 'hs_hero_heading' )->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial( 'hs_hero_heading', array(
			'selector'        => '.hs-hero h1',
			'render_callback' => function () {
				return esc_html( get_theme_mod( 'hs_hero_heading' ) );
			},
		) );
	}
}
add_action( 'customize_register', 'hs_customize_register' );

/**
 * Is the store running as a call-to-order catalog?
 *
 * @return bool
 */
function hs_is_catalog_mode() {
	return (bool) get_theme_mod( 'hs_catalog_mode', true );
}
