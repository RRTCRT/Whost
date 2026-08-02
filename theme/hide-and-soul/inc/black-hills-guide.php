<?php
/**
 * The Black Hills visitor guide.
 *
 * Kept as data rather than page content for three reasons: the town is a real
 * field worth grouping and searching by, the "not RV friendly" warnings need to
 * render as warnings rather than as a parenthesis buried in a line of text, and
 * two of the shops listed also stock our work — which the page should say.
 *
 * Edit the arrays here to add or remove entries.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Guide sections, in the order they appear on the page.
 *
 * Each entry: name, town, and optional flags —
 *   'rv'   => false  the route is not suitable for 25'+ RVs or trailers
 *   'ours' => true   this shop carries Hide and Soul products
 *
 * @return array<string, array{intro: string, items: array<int, array<string, mixed>>}>
 */
function hs_black_hills_guide() {
	$guide = array(
		'Restaurants' => array(
			'intro' => __( 'We are all in on delicious eats. Sifting through Google reviews is a daunting task, so here are some of our team’s favourites in the Black Hills.', 'hide-and-soul' ),
			'items' => array(
				array( 'name' => "Killian's Food & Drink", 'town' => 'Spearfish' ),
				array( 'name' => "Barbacoa's", 'town' => 'Spearfish' ),
				array( 'name' => "Jacob's Brewhouse & Bakery", 'town' => 'Deadwood' ),
				array( 'name' => 'Chubby Chipmunk', 'town' => 'Deadwood' ),
				array( 'name' => 'Firehouse Brewery', 'town' => 'Rapid City' ),
				array( 'name' => "Tally's Silver Spoon", 'town' => 'Rapid City' ),
				array( 'name' => 'Alpine Inn', 'town' => 'Hill City' ),
				array( 'name' => 'Red Garter Saloon', 'town' => 'Keystone' ),
				array( 'name' => 'Turtle Town Chocolate', 'town' => 'Keystone' ),
				array( 'name' => 'Black Hills Burger & Pizza Co.', 'town' => 'Custer' ),
				array( 'name' => "Buglin' Bull", 'town' => 'Custer' ),
				array( 'name' => "Maria's Mexican Restaurant", 'town' => 'Custer' ),
				array( 'name' => 'Game Lodge', 'town' => 'Custer State Park' ),
				array( 'name' => 'Bluebell Lodge', 'town' => 'Custer State Park' ),
			),
		),

		'Scenic Drives' => array(
			'intro' => __( 'However you roll — bike, RV, classic car or anything in between — the Black Hills are drop-dead gorgeous. Here are our favourite drives, and we will tell you when a 25-foot-plus RV or trailer should not attempt the route.', 'hide-and-soul' ),
			'items' => array(
				array( 'name' => 'Spearfish Canyon', 'town' => 'Lead to Spearfish' ),
				array( 'name' => 'Ice Box Canyon', 'town' => 'Lead to Buckhorn, WY' ),
				array( 'name' => 'Nemo Road', 'town' => 'US 385 to Rapid City' ),
				array( 'name' => 'Highway 44', 'town' => 'US 385 to Rapid City' ),
				array( 'name' => 'Sheridan Lake Road', 'town' => 'US 385 to Rapid City' ),
				array( 'name' => 'Highway 40', 'town' => 'Hill City to Rapid City' ),
				array( 'name' => 'Old Hill City Road', 'town' => 'Keystone to Hill City' ),
				array( 'name' => 'Needles Highway 87', 'town' => 'Custer State Park', 'rv' => false ),
				array( 'name' => 'Highway 89', 'town' => 'Custer to Sylvan Lake' ),
				array( 'name' => 'Custer State Park & Wildlife Loop', 'town' => 'Custer State Park' ),
				array( 'name' => 'Neck Yoke Road', 'town' => 'Black Hills' ),
				array( 'name' => 'Iron Mountain Road', 'town' => 'Keystone to Mount Rushmore', 'rv' => false ),
				array( 'name' => 'Play House Road (North & South)', 'town' => 'Black Hills' ),
			),
		),

		'Activities' => array(
			'intro' => __( 'Level up your Black Hills road trip. These are our local go-to activities — the ones we do with friends and family — so we want to share them with you. Get ready to explore beyond the pavement.', 'hide-and-soul' ),
			'items' => array(
				array( 'name' => 'Hikes', 'town' => 'Black Hills' ),
				array( 'name' => 'Sturgis Motorcycle Museum', 'town' => 'Sturgis' ),
				array( 'name' => 'ATVs', 'town' => 'Deadwood' ),
				array( 'name' => 'Gold panning', 'town' => 'Lead' ),
				array( 'name' => 'Journey Museum', 'town' => 'Rapid City' ),
				array( 'name' => 'Reptile Gardens', 'town' => 'Rapid City' ),
				array( 'name' => 'Mickelson Trail bicycles', 'town' => 'Hill City' ),
				array( 'name' => 'Water sport rentals', 'town' => 'Hill City' ),
				array( 'name' => 'Lakes', 'town' => 'Hill City' ),
				array( 'name' => '1880 Steam Train', 'town' => 'Hill City / Keystone' ),
				array( 'name' => 'Horse riding', 'town' => 'Keystone' ),
				array( 'name' => 'Rushmore Adventure Park & Cave', 'town' => 'Keystone' ),
				array( 'name' => 'Helicopter rides over Mount Rushmore', 'town' => 'Keystone' ),
				array( 'name' => 'Wind Cave & Jewel Cave', 'town' => 'Custer / Hot Springs' ),
				array( 'name' => 'Hot air balloon rides', 'town' => 'Custer' ),
				array( 'name' => "Evan's Plunge", 'town' => 'Hot Springs' ),
				array( 'name' => 'The Mammoth Site', 'town' => 'Hot Springs' ),
			),
		),

		'Shopping' => array(
			'intro' => __( 'Even though we hope you love shopping with us, we understand you might like to look at something besides leather. Here are our go-to spots.', 'hide-and-soul' ),
			'items' => array(
				array( 'name' => 'Main Street Antiques & Decor', 'town' => 'Spearfish' ),
				array( 'name' => 'Black Hills Rally & Gold', 'town' => 'Sturgis' ),
				array( 'name' => 'Sturgis Photo & Gift', 'town' => 'Sturgis' ),
				array( 'name' => "Jacob's Gallery", 'town' => 'Deadwood', 'ours' => true ),
				array( 'name' => "Madame Peacock's", 'town' => 'Deadwood' ),
				array( 'name' => "Pam's Purple Door", 'town' => 'Deadwood' ),
				array( 'name' => "Miss Kitty's Mercantile", 'town' => 'Deadwood' ),
				array( 'name' => 'Prairie Edge Trading Co & Galleries', 'town' => 'Rapid City' ),
				array( 'name' => 'Dakota Drum Co.', 'town' => 'Rapid City' ),
				array( 'name' => 'Jewel of the West', 'town' => 'Hill City' ),
				array( 'name' => 'Heart of the Hills Antiques & Firearms', 'town' => 'Hill City' ),
				array( 'name' => 'Claw, Antler & Hide', 'town' => 'Custer', 'ours' => true ),
				array( 'name' => 'A Walk in the Woods', 'town' => 'Custer' ),
			),
		),

		'Events' => array(
			'intro' => __( 'The Black Hills offers plenty beyond the well-known sites. The Buffalo Roundup in Custer State Park each September — cowboys herding the buffalo, followed by a wonderful art show — is a good example. Outdoor adventures, concerts, car shows, rodeos, fairs, arts and community gatherings: there is something here for everyone.', 'hide-and-soul' ),
			'items' => array(
				array( 'name' => 'Black Hills Roundup', 'town' => 'Belle Fourche' ),
				array( 'name' => 'Corvette Car Rally', 'town' => 'Spearfish' ),
				array( 'name' => 'Sturgis Mustang Rally', 'town' => 'Sturgis' ),
				array( 'name' => 'Camaro Rally', 'town' => 'Sturgis' ),
				array( 'name' => 'Stratobowl Historic Hot Air Balloon Launch', 'town' => 'Rapid City' ),
				array( 'name' => 'Black Hills Gun Show', 'town' => 'Deadwood' ),
				array( 'name' => 'Black Hills Motorcycle Show', 'town' => 'Deadwood' ),
				array( 'name' => 'Deadwood PBR', 'town' => 'Deadwood' ),
				array( 'name' => '3-Wheeler Rally', 'town' => 'Deadwood' ),
				array( 'name' => "Days of '76 Rodeo", 'town' => 'Deadwood' ),
				array( 'name' => 'Kool Deadwood Nites', 'town' => 'Deadwood' ),
				array( 'name' => 'Deadwood Jam', 'town' => 'Deadwood' ),
				array( 'name' => 'Oktoberfest', 'town' => 'Deadwood' ),
				array( 'name' => 'Deadweird', 'town' => 'Deadwood' ),
				array( 'name' => 'Black Hills Renaissance Festival', 'town' => 'Lead' ),
				array( 'name' => 'Mickelson Trail Trek', 'town' => 'Hill City' ),
				array( 'name' => 'Volksmarch', 'town' => 'Crazy Horse, Custer' ),
				array( 'name' => 'Gold Discovery Days', 'town' => 'Custer' ),
				array( 'name' => 'Off-Road Rally', 'town' => 'Custer' ),
				array( 'name' => 'Buffalo Roundup', 'town' => 'Custer State Park' ),
			),
		),
	);

	/**
	 * Filter the Black Hills guide.
	 *
	 * @param array $guide Sections keyed by heading.
	 */
	return apply_filters( 'hs_black_hills_guide', $guide );
}
