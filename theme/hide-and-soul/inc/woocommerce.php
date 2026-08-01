<?php
/**
 * WooCommerce integration.
 *
 * Two things matter here beyond styling:
 *
 * 1. URL parity with Wix. Wix served products at /product-page/{slug}. Setting
 *    the Woo permalink base to the same string means every indexed product URL
 *    keeps working with no redirect at all.
 * 2. Catalog mode. Much of this shop's work is fitted to the customer, so the
 *    store can run without a checkout and still show prices and photos.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare Woo support and gallery features.
 */
function hs_woocommerce_setup() {
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 480,
		'single_image_width'    => 960,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 5,
		),
	) );

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'hs_woocommerce_setup' );

/**
 * Products per row in the catalog grid.
 *
 * @return int
 */
function hs_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'hs_loop_columns' );

/**
 * Wrap Woo content in the theme's layout instead of Woo's default sidebar setup.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open the Woo wrapper.
 */
function hs_woo_wrapper_start() {
	if ( is_product() ) {
		hs_page_header( get_the_title() );
	} elseif ( is_shop() ) {
		$shop_id = wc_get_page_id( 'shop' );
		hs_page_header( get_the_title( $shop_id ), __( 'Handcrafted in the Black Hills — sizes 2XS to 5XL', 'hide-and-soul' ) );
	} else {
		hs_page_header( wp_strip_all_tags( woocommerce_page_title( false ) ) );
	}

	hs_breadcrumbs();

	echo '<div class="hs-section"><div class="hs-wrap">';
}
add_action( 'woocommerce_before_main_content', 'hs_woo_wrapper_start', 10 );

/**
 * Close the Woo wrapper.
 */
function hs_woo_wrapper_end() {
	echo '</div></div>';
}
add_action( 'woocommerce_after_main_content', 'hs_woo_wrapper_end', 10 );

/**
 * Woo prints its own page title inside the loop; the banner already has it.
 */
add_filter( 'woocommerce_show_page_title', '__return_false' );

/* =====================================================================
 * Catalog mode
 * ===================================================================== */

/**
 * Strip purchasing UI when the store runs as a catalog.
 */
function hs_catalog_mode_hooks() {
	if ( ! hs_is_catalog_mode() ) {
		return;
	}

	// Nothing is purchasable, which removes add-to-cart buttons everywhere.
	add_filter( 'woocommerce_is_purchasable', '__return_false' );

	// Hide the loop button entirely rather than leaving a dead control.
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// Replace the single-product cart form with a call-to-order panel.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'hs_catalog_enquiry', 30 );

	// Send cart and checkout back to the shop instead of showing empty pages.
	add_action( 'template_redirect', 'hs_block_cart_pages' );
}
add_action( 'wp', 'hs_catalog_mode_hooks' );

/**
 * Call-to-order panel in place of the add-to-cart form.
 */
function hs_catalog_enquiry() {
	hs_enquiry_panel( get_the_title() );
}

/**
 * Bounce cart/checkout traffic to the shop while in catalog mode.
 */
function hs_block_cart_pages() {
	if ( is_cart() || is_checkout() ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ), 302 );
		exit;
	}
}

/* =====================================================================
 * Migration helpers
 * ===================================================================== */

/**
 * One-time: align the product permalink base with the old Wix URLs.
 *
 * Wix used /product-page/{slug}. Matching it means the shop's existing search
 * rankings and any printed/linked product URLs survive the move untouched.
 *
 * Runs once, then records a flag so an admin can change the base later without
 * this fighting them on every load.
 */
function hs_align_product_permalinks() {
	if ( get_option( 'hs_permalinks_aligned' ) ) {
		return;
	}

	$permalinks = (array) get_option( 'woocommerce_permalinks', array() );

	if ( empty( $permalinks['product_base'] ) || 'product' === trim( $permalinks['product_base'], '/' ) ) {
		$permalinks['product_base'] = '/product-page';
		update_option( 'woocommerce_permalinks', $permalinks );
		flush_rewrite_rules();
	}

	update_option( 'hs_permalinks_aligned', 1 );
}
add_action( 'admin_init', 'hs_align_product_permalinks' );

/**
 * Show the hide type on product cards when the attribute exists.
 *
 * Customers shop by material first — surfacing it in the grid saves a click.
 */
function hs_loop_hide_attribute() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$hide = $product->get_attribute( 'hide' );

	if ( ! $hide ) {
		$hide = $product->get_attribute( 'material' );
	}

	if ( $hide ) {
		printf(
			'<p style="margin:0 1rem;font-size:0.82rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--hs-muted);">%s</p>',
			esc_html( $hide )
		);
	}
}
add_action( 'woocommerce_shop_loop_item_title', 'hs_loop_hide_attribute', 11 );

/**
 * Reassure on the single product page — everything is made to order.
 */
function hs_product_made_to_order_note() {
	echo '<p style="font-size:0.92rem;color:var(--hs-muted);border-left:3px solid var(--hs-saddle);padding-left:0.9rem;">'
		. esc_html__( 'Handmade to order in our Deadwood shop. Allow extra time in rally season, and call us if you need it by a date.', 'hide-and-soul' )
		. '</p>';
}
add_action( 'woocommerce_single_product_summary', 'hs_product_made_to_order_note', 25 );
