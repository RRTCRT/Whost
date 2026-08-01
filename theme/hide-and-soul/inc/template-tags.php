<?php
/**
 * Reusable output helpers.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site logo, or the business name typeset as a wordmark.
 */
function hs_brand() {
	$url = home_url( '/' );

	echo '<a class="hs-brand" href="' . esc_url( $url ) . '" rel="home">';

	if ( has_custom_logo() ) {
		$id  = get_theme_mod( 'custom_logo' );
		$img = wp_get_attachment_image( $id, 'full', false, array(
			'alt'      => esc_attr( hs_info( 'legal_name' ) ),
			'loading'  => 'eager',
			'decoding' => 'async',
		) );
		echo $img; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image escapes.
	} else {
		echo '<span class="hs-brand__name">' . esc_html( get_bloginfo( 'name' ) );
		echo '<span class="hs-brand__tag">' . esc_html( hs_info( 'tagline' ) ) . '</span>';
		echo '</span>';
	}

	echo '</a>';
}

/**
 * Page banner used on every template except the front page.
 *
 * @param string $title    Heading. Defaults to the queried object title.
 * @param string $subtitle Optional supporting line.
 */
function hs_page_header( $title = '', $subtitle = '' ) {
	if ( '' === $title ) {
		$title = wp_strip_all_tags( get_the_archive_title() );
	}

	$style = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$src = get_the_post_thumbnail_url( null, 'hs-hero' );
		if ( $src ) {
			$style = ' style="background-image:url(' . esc_url( $src ) . ')"';
		}
	}

	echo '<header class="hs-page-header"' . $style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
	echo '<div class="hs-wrap">';
	echo '<h1>' . esc_html( $title ) . '</h1>';

	if ( $subtitle ) {
		echo '<p>' . esc_html( $subtitle ) . '</p>';
	}

	echo '</div></header>';
}

/**
 * Lightweight breadcrumb trail. Yoast's version is used when available.
 */
function hs_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="hs-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'hide-and-soul' ) . '"><div class="hs-wrap">';

	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb();
		echo '</div></nav>';
		return;
	}

	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'hide-and-soul' ) . '</a>';

	if ( is_singular() ) {
		$post   = get_post();
		$chain  = array();
		$parent = $post->post_parent;

		while ( $parent ) {
			$chain[] = $parent;
			$parent  = wp_get_post_parent_id( $parent );
		}

		foreach ( array_reverse( $chain ) as $ancestor ) {
			echo ' <span aria-hidden="true">/</span> <a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
		}

		echo ' <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_archive() || is_search() ) {
		echo ' <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	echo '</div></nav>';
}

/**
 * Weekly hours list. The current day is highlighted client-side.
 */
function hs_hours_list() {
	echo '<ul class="hs-hours">';

	foreach ( hs_hours() as $day => $window ) {
		$label = __( 'Closed', 'hide-and-soul' );

		if ( is_array( $window ) ) {
			$label = sprintf(
				/* translators: 1: opening time, 2: closing time */
				__( '%1$s – %2$s', 'hide-and-soul' ),
				hs_format_time( $window['opens'] ),
				hs_format_time( $window['closes'] )
			);
		}

		printf(
			'<li data-hs-day="%1$s"><span class="hs-hours__day">%2$s</span><span class="%3$s">%4$s</span></li>',
			esc_attr( $day ),
			esc_html( $day ),
			is_array( $window ) ? '' : 'hs-hours__closed',
			esc_html( $label )
		);
	}

	echo '</ul>';
}

/**
 * Social icon row.
 */
function hs_social_links() {
	$links = array(
		'facebook'  => array(
			'label' => __( 'Facebook', 'hide-and-soul' ),
			'path'  => 'M22 12a10 10 0 1 0-11.6 9.9v-7h-2.5V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12Z',
		),
		'instagram' => array(
			'label' => __( 'Instagram', 'hide-and-soul' ),
			'path'  => 'M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2-.1-1.3-.1-1.7-.1-4.9s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4 1.3-.1 1.7-.1 4.9-.1Zm0 3.8a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm0 9.9a3.9 3.9 0 1 1 0-7.8 3.9 3.9 0 0 1 0 7.8Zm7.6-10.1a1.4 1.4 0 1 1-2.8 0 1.4 1.4 0 0 1 2.8 0Z',
		),
	);

	$out = '';

	foreach ( $links as $key => $meta ) {
		$url = hs_info( $key );

		if ( ! $url ) {
			continue;
		}

		$out .= sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer"><span class="hs-screen-reader-text">%2$s</span><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="%3$s"/></svg></a>',
			esc_url( $url ),
			esc_html( $meta['label'] ),
			esc_attr( $meta['path'] )
		);
	}

	if ( $out ) {
		echo '<div class="hs-social">' . $out . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
	}
}

/**
 * Paged navigation styled to match the theme.
 */
function hs_pagination() {
	$links = paginate_links( array(
		'type'      => 'array',
		'prev_text' => __( '&larr; Previous', 'hide-and-soul' ),
		'next_text' => __( 'Next &rarr;', 'hide-and-soul' ),
	) );

	if ( ! $links ) {
		return;
	}

	echo '<nav class="hs-pagination" aria-label="' . esc_attr__( 'Pagination', 'hide-and-soul' ) . '">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}

/**
 * "Call to order" panel shown in catalog mode and on custom-order pages.
 *
 * @param string $subject Optional pre-filled email subject.
 */
function hs_enquiry_panel( $subject = '' ) {
	$subject = $subject ? $subject : get_the_title();
	$mailto  = 'mailto:' . hs_info( 'email' ) . '?subject=' . rawurlencode(
		sprintf(
			/* translators: %s: product or page title */
			__( 'Order enquiry: %s', 'hide-and-soul' ),
			$subject
		)
	);

	echo '<div class="hs-enquire">';
	echo '<p>' . esc_html__( 'Every piece is cut and fitted to order. Call or message us with your sizes and we will walk you through hide, colour and options.', 'hide-and-soul' ) . '</p>';
	echo '<a class="hs-btn" href="' . esc_url( hs_phone_href() ) . '">' . esc_html( sprintf( __( 'Call %s', 'hide-and-soul' ), hs_info( 'phone' ) ) ) . '</a>';
	echo '<a class="hs-btn hs-btn--outline" href="' . esc_url( $mailto ) . '">' . esc_html__( 'Email the shop', 'hide-and-soul' ) . '</a>';
	echo '</div>';
}
