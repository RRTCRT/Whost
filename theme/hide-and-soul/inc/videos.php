<?php
/**
 * YouTube content for the Videos page.
 *
 * The old page ran on the Wix Video app, which has no WordPress equivalent, so
 * this rebuilds it against YouTube directly.
 *
 * On titles: the saved page carries 33 video IDs in one JSON blob and the
 * visible titles in a separate part of the DOM, with no reliable link between
 * them — 33 IDs against 32 titles, so pairing by order would mislabel at least
 * one and possibly all of them after the first mismatch. Rather than guess,
 * playlists carry the load: YouTube supplies the correct title for every video
 * inside them, and stays correct as videos are added. The loose IDs render as
 * thumbnails without captions, which are self-describing and never wrong.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * The YouTube channel handle.
 *
 * @return string
 */
function hs_video_channel() {
	return (string) apply_filters( 'hs_video_channel', '@hideandsoulleatherworks' );
}

/**
 * Playlists to feature, in order.
 *
 * Ordered as the old page ordered them: the Goldens first, How-To second.
 *
 * TODO: both playlist IDs came off the saved page, but which ID belongs to
 * which playlist could not be confirmed — the IDs sit in a JSON blob far from
 * the captions. One look at the channel settles it; if the two are the wrong
 * way round, swap the 'id' values below.
 *
 * @return array<int, array<string, string>>
 */
function hs_video_playlists() {
	$playlists = array(
		array(
			'id'    => 'PL_kwUr2yRrof5yw9vvS-ZH0dQpGPC-I34',
			'title' => __( 'The Motorcycle Riding Goldens', 'hide-and-soul' ),
			'intro' => __( 'Caleb, the original motorcycle riding golden retriever, and the ones who came after him — Sturgis, Spearfish Canyon, Arizona and home.', 'hide-and-soul' ),
		),
		array(
			'id'    => 'PL_kwUr2yRrofr_WEIwhCN0B3b4UctnY5x',
			'title' => __( 'How-To Playlist', 'hide-and-soul' ),
			'intro' => __( 'Washing leather by hand, folding chaps for the bike, cleaning mould off a jacket, upgrading half chaps with conchos, and how to measure yourself for a shirt, jacket or chaps.', 'hide-and-soul' ),
		),
	);

	/**
	 * Filter the featured playlists.
	 *
	 * @param array $playlists Playlist rows.
	 */
	return apply_filters( 'hs_video_playlists', $playlists );
}

/**
 * What's on the channel, by theme.
 *
 * Titles are verbatim from the old page. They are listed as text rather than
 * attached to specific videos — see the note at the top of this file.
 *
 * @return array<string, array<int, string>>
 */
function hs_video_topics() {
	$topics = array(
		__( 'How-to and leather care', 'hide-and-soul' ) => array(
			'How to hand-wash your leather',
			'Upgrade your 1/2 chaps with conchos',
			'Packing your motorcycle: chap folding',
			'Mould on a leather jacket — how to clean it',
			'Leather care after a cross-country trip',
			'How to pack your bike',
			"Skidmore's leather products",
			'How to measure: shirts & jackets',
			'How to measure: 1/2 chaps',
			'How to measure: chaps',
		),
		__( 'Caleb and the riding goldens', 'hide-and-soul' ) => array(
			'Caleb riding the motorcycle',
			'Motorcycle riding dog at Sturgis',
			"Caleb's memorial video",
			'Golden retrievers ride motorcycle',
			'Christmas time bike ride',
			'The original motorcycle riding dog',
			'Spearfish Canyon riding',
			'Caleb riding in Arizona',
		),
		__( 'Customer reviews', 'hide-and-soul' ) => array(
			'Craig & Kellie',
			'Kenny',
			'David & Marlaine',
			'Steve',
			'Mark',
			'Chris — Sturgis 83rd',
			'Tiffany — Sturgis 83rd',
			'Sturgis 80th reviews',
		),
	);

	/**
	 * Filter the channel topic lists.
	 *
	 * @param array $topics Topic => titles.
	 */
	return apply_filters( 'hs_video_topics', $topics );
}

/**
 * Every video ID found on the old page, in document order.
 *
 * Rendered as uncaptioned thumbnails. Drop an ID from this list to remove it
 * from the grid — one of the originals was a private video and will simply not
 * return a thumbnail.
 *
 * @return string[]
 */
function hs_video_ids() {
	$ids = array(
		'IzW7CvfJX7g', '-3M3Iep0_3k', 'pARQhr8UhZQ', 'E2IcT4HtP-4', 'Kw6zex_zVkU',
		'oQIUIQqzAbE', 'fhKgqEgShLE', 'xFuBkgvNq0c', 'I9TWyIekkdY', 'rJjev-naz9M',
		'ODCsUQOL60E', 'bjhfs-TkbuU', 'a6Yfd6DrsKk', 'V6wj8YVPHLA', 'Cld8x0mRGX0',
		'i0442NzQZ0M', 'mE19sgkDtCg', 'L8r-bvKmyA8', '0OBzNl0Bzr0', 'RBXHizYdL0k',
		'OD-1jhI1T-w', 'EUAgrgHG0V4', 'MTEN4XfM7Jg', 'gdBWOX1gNzQ', 'yfoLdgSf3mQ',
		'0O2sMz0frrY', 'bSO-9EqxsSI', 'dCum19SsmQ0', 'TVuCuPHEyzE', 'radQ50dSC_w',
		'VQJO_NR87jc', 'K-Q9X_b-LWk', 'IyZcRy1XxX0',
	);

	/**
	 * Filter the video grid.
	 *
	 * @param string[] $ids YouTube video IDs.
	 */
	return apply_filters( 'hs_video_ids', $ids );
}

/**
 * Render a click-to-load video or playlist.
 *
 * A YouTube iframe pulls well over a megabyte before anyone presses play, and
 * this page would embed dozens. This prints a thumbnail with a play button and
 * swaps in the real iframe on click — one network request instead of thirty.
 *
 * @param string $id    Video or playlist ID.
 * @param string $type  'video' or 'playlist'.
 * @param string $label Accessible label.
 */
function hs_video_embed( $id, $type = 'video', $label = '' ) {
	$is_playlist = 'playlist' === $type;

	$src = $is_playlist
		? 'https://www.youtube-nocookie.com/embed/videoseries?list=' . rawurlencode( $id ) . '&autoplay=1'
		: 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $id ) . '?autoplay=1';

	// Playlists have no thumbnail of their own; YouTube serves the first video's.
	$poster = $is_playlist ? '' : 'https://i.ytimg.com/vi/' . rawurlencode( $id ) . '/hqdefault.jpg';

	$label = $label ? $label : __( 'Play video', 'hide-and-soul' );

	printf(
		'<button type="button" class="hs-video" data-hs-embed="%1$s" aria-label="%2$s">%3$s<span class="hs-video__play" aria-hidden="true"></span></button>',
		esc_url( $src ),
		esc_attr( $label ),
		$poster
			? sprintf(
				'<img src="%s" alt="" loading="lazy" decoding="async" width="480" height="360">',
				esc_url( $poster )
			)
			: '<span class="hs-video__blank" aria-hidden="true"></span>'
	);
}
