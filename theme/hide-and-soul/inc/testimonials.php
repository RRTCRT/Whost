<?php
/**
 * Customer testimonials.
 *
 * ⚠ Most of the reviews on the Wix page are NOT here, and could not be
 * recovered. The page used two Wix widgets — a rotating quote slider and a
 * "Share your experience" comments box — and both load their content from
 * Wix's servers at runtime. The saved HTML carries one slider quote and none
 * of the comments, and the Comments API returned nothing under any of the ten
 * app IDs present in the page.
 *
 * They must be copied off the live site by hand before Wix is cancelled. See
 * docs/CONTENT-ISSUES.md. Reviews are the hardest content on the site to
 * replace — a customer who wrote one six years ago is not going to write it
 * again.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

/**
 * Testimonials to display.
 *
 * 'verify' => true marks an entry transcribed from a screenshot rather than
 * from the page source. Check the wording and the name against the live site
 * before launch, then drop the flag.
 *
 * @return array<int, array<string, mixed>>
 */
function hs_testimonials() {
	$testimonials = array(
		array(
			'quote'  => 'The people at Hide & Soul are fantastic! Very friendly, knowledgeable, and helpful staff. I highly recommend them!',
			'author' => 'Tyler',
			'stars'  => 0,
		),
		array(
			'quote'  => 'My wife and I had the opportunity to stop by while at the Rally and ended up having 3 items made for us, ended up going back 3 times while out in SD, and felt like we found long lost family members. The quality of the leather products is the best I have found, and having the ability to pick the leather for your custom made items is part of something you will treasure forever. If you go to the Black Hills and miss Hide and Soul, you missed an opportunity to see one of a kind leather products and meet an amazing family of true Americans!',
			'author' => 'Skip and Debbie',
			'stars'  => 5,
			'verify' => true,
		),
		array(
			'quote'  => 'The elkskin vest went beyond my expectations. Perfect fit and great service. I will be making future purchases!',
			'author' => 'Kevin Remick',
			'stars'  => 0,
			'verify' => true,
		),
	);

	/**
	 * Filter the testimonial list.
	 *
	 * @param array $testimonials Testimonial rows.
	 */
	return apply_filters( 'hs_testimonials', $testimonials );
}

/**
 * Customer review videos, as they appeared on the testimonials page.
 *
 * Titles are not attached — see the note in inc/videos.php. Thumbnails carry
 * the meaning here anyway: every one of these is a person on camera.
 *
 * @return string[]
 */
function hs_review_video_ids() {
	$ids = array(
		'EUAgrgHG0V4', 'MTEN4XfM7Jg', 'gdBWOX1gNzQ', 'yfoLdgSf3mQ', '0O2sMz0frrY',
		'bSO-9EqxsSI', 'dCum19SsmQ0', 'TVuCuPHEyzE', 'radQ50dSC_w', 'VQJO_NR87jc',
		'K-Q9X_b-LWk',
	);

	/**
	 * Filter the review video list.
	 *
	 * @param string[] $ids YouTube video IDs.
	 */
	return apply_filters( 'hs_review_video_ids', $ids );
}
