<?php
/**
 * Single source of truth for NAP (name / address / phone), hours and locations.
 *
 * Everything here is editable in Appearance > Customize > Business Info, so the
 * shop's phone number or seasonal hours are never hard-coded into a template.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults for every business field.
 *
 * @return array<string, string>
 */
function hs_info_defaults() {
	return array(
		'legal_name'   => 'Hide and Soul Leatherworks',
		// The homepage says "est. 1983" — not 1984, as directory listings claim.
		'tagline'      => 'Handcrafted leather since 1983',
		// The public number. Formerly a landline, now a Google Voice line that
		// rings Jennifer's mobile — so it reaches her at either shop, in either
		// season. Keep this one on directories and print; it is the number the
		// business is known by.
		'phone'        => '(605) 578-9746',
		// Jennifer's mobile direct. The custom-orders page sends callers here.
		'custom_phone' => '(406) 788-5352',
		// Confirmed from the Wix site's contact settings.
		'email'        => 'roadkillleather@gmail.com',
		'street'       => '21576 US HWY 385',
		'city'         => 'Deadwood',
		'region'       => 'SD',
		'postal'       => '57732',
		'country'      => 'US',
		'latitude'     => '44.2861',
		'longitude'    => '-103.7691',
		'hours_note'   => 'Closed Mondays unless you arrange a time with us in advance — give us a call and we will open up.',
		'season_note'  => 'Winters in Cave Creek, AZ — call ahead for December through March.',
		'facebook'     => 'https://www.facebook.com/hideandsoulleather',
		'instagram'    => 'https://www.instagram.com/hideandsoul/',
		'pinterest'    => 'https://www.pinterest.com/HideandSoulLeatherworks/',
		'youtube'      => 'https://www.youtube.com/@hideandsoulleatherworks',
		'price_range'  => '$$',
	);
}

/**
 * Fetch one business field, falling back to the default.
 *
 * @param string $key Field key.
 * @return string
 */
function hs_info( $key ) {
	$defaults = hs_info_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return (string) get_theme_mod( 'hs_' . $key, $default );
}

/**
 * Phone number reduced to digits for tel: links.
 *
 * @param string $key Which number — 'phone' (shop) or 'custom_phone' (Jenn).
 * @return string
 */
function hs_phone_href( $key = 'phone' ) {
	$digits = preg_replace( '/\D+/', '', hs_info( $key ) );

	if ( 10 === strlen( $digits ) ) {
		$digits = '1' . $digits;
	}

	return 'tel:+' . $digits;
}

/**
 * Weekly opening hours. Confirmed by the owner; identical at both shops.
 *
 * Keys are schema.org day names so the same array feeds both the visible
 * hours table and the LocalBusiness JSON-LD.
 *
 * A value may be:
 *   array  — an open window, e.g. array( 'opens' => '09:00', 'closes' => '17:00' )
 *   string — a custom label for a day that isn't a normal open window
 *   null   — closed
 *
 * These are confirmed correct by the owner. The Wix site carried two other
 * versions — homepage "Wed-Sat 10-5", locations page "Tue-Sat 9-6" — both wrong,
 * left behind by other people editing the site over the years. Don't reinstate
 * either from the old pages.
 *
 * Only array values reach the structured data. Monday is deliberately a label
 * rather than a window: "by appointment" is not an opening time, and telling
 * Google the shop is open would send people to a locked door.
 *
 * @return array<string, array{opens: string, closes: string}|string|null>
 */
function hs_hours() {
	$default = array(
		'Monday'    => 'By appointment',
		'Tuesday'   => array( 'opens' => '09:00', 'closes' => '17:00' ),
		'Wednesday' => array( 'opens' => '09:00', 'closes' => '17:00' ),
		'Thursday'  => array( 'opens' => '09:00', 'closes' => '17:00' ),
		'Friday'    => array( 'opens' => '09:00', 'closes' => '17:00' ),
		'Saturday'  => array( 'opens' => '09:00', 'closes' => '17:00' ),
		'Sunday'    => array( 'opens' => '10:00', 'closes' => '16:00' ),
	);

	/**
	 * Filter the weekly hours. Return null for a closed day.
	 *
	 * @param array $default Weekly hours keyed by schema.org day name.
	 */
	return apply_filters( 'hs_hours', $default );
}

/**
 * Format a 24h time as a display string.
 *
 * @param string $time 24-hour "HH:MM".
 * @return string
 */
function hs_format_time( $time ) {
	$stamp = strtotime( $time );

	return $stamp ? date_i18n( 'g:i a', $stamp ) : $time;
}

/**
 * Stores in the area that carry some of our products.
 *
 * Confirmed by the owner. These are other people's shops — keep the details
 * accurate or drop the entry rather than letting it go stale.
 *
 * @return array<int, array<string, string>>
 */
function hs_wholesalers() {
	$stockists = array(
		array(
			'name'   => "Trevino's Leather",
			'street' => '21412 US HWY 385',
			'city'   => 'Deadwood',
			'region' => 'SD',
			'postal' => '57732',
			'phone'  => '(605) 578-1271',
		),
		array(
			'name'   => "Scott Jacob's Gallery",
			'street' => '670 Main St',
			'city'   => 'Deadwood',
			'region' => 'SD',
			'postal' => '57732',
			'phone'  => '(605) 559-1876',
		),
		array(
			'name'   => 'Claw, Antler & Hide',
			'street' => '735 Mt Rushmore Rd',
			'city'   => 'Custer',
			'region' => 'SD',
			'postal' => '57730',
			'phone'  => '(605) 673-4345',
		),
	);

	/**
	 * Filter the stockist list.
	 *
	 * @param array $stockists Wholesaler rows.
	 */
	return apply_filters( 'hs_wholesalers', $stockists );
}

/**
 * Base prices for custom work, as published on the custom-orders page.
 *
 * Kept here rather than typed into page content so a price rise is one edit,
 * not a hunt through the editor.
 *
 * Heads up: some of these disagree with the WooCommerce catalogue — see
 * docs/MIGRATION-PLAN.md. Where they conflict, decide which is right before
 * launch rather than shipping two prices for the same garment.
 *
 * @return array<string, array<string, int>> Group label => item => price in USD.
 */
function hs_base_prices() {
	$prices = array(
		'Vests' => array(
			'All Ladies Vests'  => 399,
			"Deer Men's Vest"   => 525,
			"Elk Men's Vest"    => 575,
			"Bison Men's Vest"  => 649,
		),
		'Chaps' => array(
			'All Chaps'     => 895,
			'All 1/2 Chaps' => 399,
		),
		'Shirts' => array(
			'Basic Shirt'             => 899,
			'Maverick Pullover Shirt' => 899,
			'Boone Pullover Shirt'    => 999,
			'Snap Front Shirt'        => 1199,
		),
		'Jackets' => array(
			'Western Fringed Jacket' => 1199,
			'Basic Ladies Jacket'    => 1199,
			"Basic Men's Jacket"     => 1299,
		),
	);

	/**
	 * Filter the published base prices.
	 *
	 * @param array $prices Group => item => price.
	 */
	return apply_filters( 'hs_base_prices', $prices );
}

/**
 * Storefront locations.
 *
 * Two shops, run as one seasonal circuit: the Black Hills through the warm
 * months, Arizona over the winter. The Custer, SD shop that appears in older
 * directory listings is closed and is deliberately not represented here.
 *
 * The first entry is the primary location for structured data and the footer.
 *
 * @return array<int, array<string, string>>
 */
function hs_locations() {
	$locations = array(
		array(
			'key'     => 'black-hills',
			'name'    => 'Deadwood — Main Shop',
			'street'  => hs_info( 'street' ),
			'city'    => hs_info( 'city' ),
			'region'  => hs_info( 'region' ),
			'postal'  => hs_info( 'postal' ),
			'phone'   => hs_info( 'phone' ),
			'cell'    => hs_info( 'custom_phone' ),
			'season'  => 'April – November',
			'note'    => 'On US Highway 385, eight miles south of Deadwood — just south of Nemo Road, on the left. Full workshop on site: repairs, fittings and custom orders.',
			'map'     => 'https://maps.google.com/maps?q=21576+US+HWY+385+Deadwood+SD+57732&output=embed',
		),
		array(
			'key'     => 'cave-creek',
			'name'    => 'Cave Creek — Winter Shop',
			'street'  => 'Frontier Town, 6245 E Cave Creek Rd',
			'city'    => 'Cave Creek',
			'region'  => 'AZ',
			'postal'  => '85331',
			// The Wix page listed only the mobile here, presumably from when the
			// 605 was a landline tied to the Deadwood shop. It's a Google Voice
			// number now and rings through in Arizona too, so both are shown.
			'phone'   => hs_info( 'phone' ),
			'cell'    => hs_info( 'custom_phone' ),
			'season'  => 'December – March',
			'note'    => 'We set up in Frontier Town for the winter season. Same leather, same repairs, same hours — just warmer.',
			'map'     => 'https://maps.google.com/maps?q=6245+E+Cave+Creek+Rd+Cave+Creek+AZ+85331&output=embed',
		),
	);

	/**
	 * Filter the storefront list.
	 *
	 * @param array $locations Location rows.
	 */
	return apply_filters( 'hs_locations', $locations );
}

/**
 * Which shop is open right now.
 *
 * The business moves: Black Hills April–November, Cave Creek December–March.
 * A hard-coded "we're in South Dakota" banner is wrong a third of the year,
 * so the header line follows the calendar instead.
 *
 * Uses the site's timezone, not the server's.
 *
 * @return string Either 'black-hills' or 'cave-creek'.
 */
function hs_current_season() {
	$month = (int) current_time( 'n' );

	// December (12) through March (3).
	$season = ( 12 === $month || $month <= 3 ) ? 'cave-creek' : 'black-hills';

	/**
	 * Filter the active season, e.g. to pin it during a changeover week.
	 *
	 * @param string $season 'black-hills' or 'cave-creek'.
	 * @param int    $month  Current month, 1-12.
	 */
	return apply_filters( 'hs_current_season', $season, $month );
}

/**
 * Season-aware line for the top bar.
 *
 * Falls back to the Customizer value if one has been set by hand.
 *
 * @return string
 */
function hs_season_note() {
	$override = get_theme_mod( 'hs_season_note', '' );
	$defaults = hs_info_defaults();

	// Only treat it as an override if it differs from the shipped default.
	if ( $override && $override !== $defaults['season_note'] ) {
		return $override;
	}

	if ( 'cave-creek' === hs_current_season() ) {
		return __( 'Winter season — find us at Frontier Town, N. Cave Creek Rd, Cave Creek, AZ.', 'hide-and-soul' );
	}

	return __( 'Open in the Black Hills — 8 miles south of Deadwood on US Hwy 385.', 'hide-and-soul' );
}

/**
 * Single-line address for the primary location.
 *
 * @return string
 */
function hs_address_line() {
	return sprintf(
		'%s, %s, %s %s',
		hs_info( 'street' ),
		hs_info( 'city' ),
		hs_info( 'region' ),
		hs_info( 'postal' )
	);
}
