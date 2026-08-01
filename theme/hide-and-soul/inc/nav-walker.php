<?php
/**
 * Menu fallback when no menu has been assigned yet.
 *
 * Keeps a brand-new install navigable instead of rendering nothing, and mirrors
 * the page structure the Wix site used so the slugs line up.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a plain menu from the known page slugs.
 *
 * Mirrors the navigation the Wix site actually used, so a fresh install looks
 * right before anyone builds a menu in Appearance > Menus:
 *
 *   Custom Orders · Products (Mens / Womens / Accessories) · Locations ·
 *   Black Hills Guide · Videos · More (Contact, Repairs & Patches,
 *   Testimonials, Cleaning & Care, Customer Referral Program)
 *
 * Flattened to one level here — build the real nested menu in wp-admin.
 */
function hs_nav_fallback() {
	$pages = array(
		'shop'                => __( 'Shop', 'hide-and-soul' ),
		'custom-orders'       => __( 'Custom Orders', 'hide-and-soul' ),
		'repairs-patches'     => __( 'Repairs & Patches', 'hide-and-soul' ),
		'locations'           => __( 'Locations', 'hide-and-soul' ),
		'black-hills-guide'   => __( 'Black Hills Guide', 'hide-and-soul' ),
		'testimonials'        => __( 'Testimonials', 'hide-and-soul' ),
		'care'                => __( 'Cleaning & Care', 'hide-and-soul' ),
		'contact'             => __( 'Contact', 'hide-and-soul' ),
	);

	echo '<ul>';

	foreach ( $pages as $slug => $label ) {
		$page = get_page_by_path( $slug );
		$url  = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );

		printf(
			'<li class="menu-item"><a href="%1$s">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	echo '</ul>';
}

/**
 * Add a helper class to menu items that have children, so CSS/JS can target them.
 *
 * WordPress already adds menu-item-has-children; this guards against menus
 * built by plugins that skip it.
 *
 * @param string[] $classes Item classes.
 * @param WP_Post  $item    Menu item.
 * @param stdClass $args    Menu args.
 * @param int      $depth   Depth.
 * @return string[]
 */
function hs_nav_item_classes( $classes, $item, $args, $depth ) {
	if ( ! isset( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $classes;
	}

	if ( 0 === $depth && ! in_array( 'menu-item-has-children', $classes, true ) ) {
		$children = get_posts( array(
			'post_type'      => 'nav_menu_item',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_menu_item_menu_item_parent',
			'meta_value'     => (string) $item->ID,
		) );

		if ( $children ) {
			$classes[] = 'menu-item-has-children';
		}
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'hs_nav_item_classes', 10, 4 );
