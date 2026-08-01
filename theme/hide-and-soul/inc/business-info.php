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
		'tagline'      => 'Handcrafted leather since 1984',
		'phone'        => '(605) 578-9746',
		'email'        => 'info@hideandsoul.com',
		'street'       => '21576 U.S. Hwy 385',
		'city'         => 'Deadwood',
		'region'       => 'SD',
		'postal'       => '57732',
		'country'      => 'US',
		'latitude'     => '44.2861',
		'longitude'    => '-103.7691',
		'hours_note'   => 'Black Hills shop open April through November.',
		'season_note'  => 'Winters in Cave Creek, AZ — call ahead for December through March.',
		'facebook'     => 'https://www.facebook.com/hideandsoulleather/',
		'instagram'    => '',
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
 * @return string
 */
function hs_phone_href() {
	$digits = preg_replace( '/\D+/', '', hs_info( 'phone' ) );

	if ( 10 === strlen( $digits ) ) {
		$digits = '1' . $digits;
	}

	return 'tel:+' . $digits;
}

/**
 * Weekly opening hours.
 *
 * Keys are schema.org day names so the same array feeds both the visible
 * hours table and the LocalBusiness JSON-LD.
 *
 * @return array<string, array{label: string, opens: string, closes: string}|null>
 */
function hs_hours() {
	$default = array(
		'Monday'    => null,
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
 * Storefront locations.
 *
 * Edit this list to add, remove or reorder shops. The first entry is treated
 * as the primary location for structured data and the footer address.
 *
 * @return array<int, array<string, string>>
 */
function hs_locations() {
	$locations = array(
		array(
			'name'    => 'Deadwood — Main Shop',
			'street'  => hs_info( 'street' ),
			'city'    => hs_info( 'city' ),
			'region'  => hs_info( 'region' ),
			'postal'  => hs_info( 'postal' ),
			'phone'   => hs_info( 'phone' ),
			'season'  => 'April – November',
			'note'    => 'On US Highway 385, eight miles south of Deadwood. Full workshop on site — repairs, fittings and custom orders.',
			'map'     => 'https://maps.google.com/maps?q=21576+US-385+Deadwood+SD+57732&output=embed',
		),
		array(
			'name'    => 'Custer',
			'street'  => '', // TODO: confirm street address from the Wix /locations page.
			'city'    => 'Custer',
			'region'  => 'SD',
			'postal'  => '',
			'phone'   => hs_info( 'phone' ),
			'season'  => 'Summer season',
			'note'    => 'TODO: confirm hours and address.',
			'map'     => '',
		),
		array(
			'name'    => 'Cave Creek — Winter Outpost',
			'street'  => '', // TODO: confirm street address.
			'city'    => 'Cave Creek',
			'region'  => 'AZ',
			'postal'  => '',
			'phone'   => hs_info( 'phone' ),
			'season'  => 'December – March',
			'note'    => 'TODO: confirm hours and address.',
			'map'     => '',
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
