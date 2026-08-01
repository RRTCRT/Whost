<?php
/**
 * Styles and scripts.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 */
function hs_enqueue_assets() {
	$style_path = HS_DIR . '/style.css';
	$style_ver  = file_exists( $style_path ) ? filemtime( $style_path ) : HS_VERSION;

	wp_enqueue_style( 'hide-and-soul', get_stylesheet_uri(), array(), $style_ver );

	$js_path = HS_DIR . '/assets/js/main.js';

	if ( file_exists( $js_path ) ) {
		wp_enqueue_script(
			'hide-and-soul',
			HS_URI . '/assets/js/main.js',
			array(),
			filemtime( $js_path ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'hs_enqueue_assets' );

/**
 * Self-hosted display font.
 *
 * Drop Bitter (or your chosen display face) as .woff2 into assets/fonts/ and
 * this registers it without any third-party request — no Google Fonts CDN,
 * which keeps the site out of GDPR grey areas and off an extra DNS lookup.
 */
function hs_font_face() {
	$font = HS_DIR . '/assets/fonts/bitter-700.woff2';

	if ( ! file_exists( $font ) ) {
		return;
	}

	$url = HS_URI . '/assets/fonts/bitter-700.woff2';

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>',
		esc_url( $url )
	);

	printf(
		'<style id="hs-fonts">@font-face{font-family:"Bitter";src:url("%s") format("woff2");font-weight:700;font-style:normal;font-display:swap;}</style>',
		esc_url( $url )
	);
}
add_action( 'wp_head', 'hs_font_face', 1 );

/**
 * Body classes that templates and CSS key off.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function hs_body_class( $classes ) {
	if ( function_exists( 'hs_is_catalog_mode' ) && hs_is_catalog_mode() ) {
		$classes[] = 'hs-catalog-mode';
	}

	if ( ! is_active_sidebar( 'shop-sidebar' ) ) {
		$classes[] = 'hs-no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'hs_body_class' );
