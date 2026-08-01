<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports.
 */
function hs_setup() {
	load_theme_textdomain( 'hide-and-soul', HS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	// Editor palette mirrors the CSS custom properties in style.css.
	add_theme_support( 'editor-color-palette', array(
		array( 'name' => __( 'Hide', 'hide-and-soul' ), 'slug' => 'hide', 'color' => '#2b1d13' ),
		array( 'name' => __( 'Bark', 'hide-and-soul' ), 'slug' => 'bark', 'color' => '#4a3524' ),
		array( 'name' => __( 'Saddle', 'hide-and-soul' ), 'slug' => 'saddle', 'color' => '#8a5a2b' ),
		array( 'name' => __( 'Copper', 'hide-and-soul' ), 'slug' => 'copper', 'color' => '#b5651d' ),
		array( 'name' => __( 'Buckskin', 'hide-and-soul' ), 'slug' => 'buckskin', 'color' => '#c9a227' ),
		array( 'name' => __( 'Parchment', 'hide-and-soul' ), 'slug' => 'parchment', 'color' => '#ede4d3' ),
		array( 'name' => __( 'Cream', 'hide-and-soul' ), 'slug' => 'cream', 'color' => '#f6f1e7' ),
		array( 'name' => __( 'Bone', 'hide-and-soul' ), 'slug' => 'bone', 'color' => '#ffffff' ),
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'hide-and-soul' ),
		'footer'  => __( 'Footer Menu', 'hide-and-soul' ),
		'legal'   => __( 'Legal Menu', 'hide-and-soul' ),
	) );

	// Product / gallery crops.
	add_image_size( 'hs-tile', 720, 960, true );
	add_image_size( 'hs-card', 800, 600, true );
	add_image_size( 'hs-hero', 1920, 1080, true );
}
add_action( 'after_setup_theme', 'hs_setup' );

/**
 * Content width used by embeds.
 */
function hs_content_width() {
	$GLOBALS['content_width'] = 736;
}
add_action( 'after_setup_theme', 'hs_content_width', 0 );

/**
 * Widget areas.
 */
function hs_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Shop Sidebar', 'hide-and-soul' ),
		'id'            => 'shop-sidebar',
		'description'   => __( 'Shown on shop and product archive pages.', 'hide-and-soul' ),
		'before_widget' => '<section id="%1$s" class="hs-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="hs-widget__title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'hs_widgets_init' );
