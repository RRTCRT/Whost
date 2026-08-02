<?php
/**
 * Template Name: Cleaning & Care
 *
 * Buckskin care instructions. The wash steps live in an array rather than in
 * page content so they stay a numbered procedure — this is the page people
 * follow with a wet garment in their hands, and a step lost to a careless edit
 * costs somebody a $900 vest.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'Care and cleaning instructions', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<div class="hs-entry__content">
				<?php if ( get_the_content() ) : ?>
					<?php the_content(); ?>
				<?php else : ?>

					<h2><?php esc_html_e( 'What is buckskin?', 'hide-and-soul' ); ?></h2>
					<p><?php esc_html_e( 'Buckskin, though technically male deer hide, is used as a general term for soft-tanned game hides. Finished without paints or varnishes, they keep their unique imperfections along with a soft, supple feel. If you have ever worn buckskin gloves you know what a superior fit and feel they have — imagine that in your vest, chaps and jacket.', 'hide-and-soul' ); ?></p>
					<p><?php esc_html_e( 'Our buckskin items are handcrafted, so no two are ever identical. Natural markings add to the rustic beauty of our products, and any unavoidable holes are decoratively patched, creating even more character.', 'hide-and-soul' ); ?></p>

					<h2><?php esc_html_e( 'Our products are washable — can you believe it?', 'hide-and-soul' ); ?></h2>
					<p><?php esc_html_e( 'Because buckskin is essentially felted fibres, it can be immersed or spot-cleaned without damage, if it is done properly. It can be risky if it is not. On a medium to light colour, or where the stain is ink or oil, you may not get 100% removal.', 'hide-and-soul' ); ?></p>
					<p><?php esc_html_e( 'Spot cleaning is generally not advised on any colour other than black — water spots can dry and leave chemical or mineral residue, or water rings. Leather is extremely flexible when wet, so wringing it, hanging it or putting excess pressure on it can disfigure the fibres and leave you with a misshapen garment. Be gentle.', 'hide-and-soul' ); ?></p>
					<p><?php esc_html_e( 'Waterproofing is advisable if you wear these every day in every condition. We recommend Skidmore’s Leather Waterproofer for its natural plant oils and beeswax. Airing out, pre-treating with waterproofer, and spot cleaning will all make a full immersive wash a rare occasion. Excessive scrubbing will abrade the grain.', 'hide-and-soul' ); ?></p>

				<?php endif; ?>
			</div>

			<?php
			/* The wash procedure. Ordered, and deliberately not free text. */
			$hs_steps = array(
				array(
					'text' => __( 'Gently spot clean the dry garment’s full grain layer with full-strength Lexol cleaner and a soft cloth or your fingers.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Dirt spots can disappear when wet, so note where they are before wetting. Wash in lukewarm water mixed with 2 tablespoons of Lexol cleaner, gently agitating and working the buckskin in the water.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Rinse in lukewarm water — barely covering the vest — mixed with 3/4 cup of Lexol conditioner. Or apply Skidmore’s once rinsing is complete.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Rinse in cool water and gently squeeze the excess out.', 'hide-and-soul' ),
					'warn' => __( 'Never wring it. Wringing stretches the leather and the garment will end up misshapen.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Lay the garment on a similar-coloured towel. Put another towel inside it and a third on top.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Roll up the towels and garment together, squeezing tightly — never twisting. Unroll, and pat the garment back into shape.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Dry flat, not hung, out of direct sun or heat. Flex the garment occasionally and change how it is laid out as it dries.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'When dry, work the garment soft by hand, or put it in the dryer on “fluff” for five minutes.', 'hide-and-soul' ),
				),
				array(
					'text' => __( 'Re-apply Skidmore’s.', 'hide-and-soul' ),
				),
			);
			?>

			<div style="margin-top:clamp(2.5rem,6vw,3.5rem);">
				<div class="hs-section__head">
					<span class="hs-eyebrow"><?php esc_html_e( 'Step by step', 'hide-and-soul' ); ?></span>
					<h2><?php esc_html_e( 'To wash your buckskin garment', 'hide-and-soul' ); ?></h2>
				</div>

				<ol class="hs-steps">
					<?php foreach ( $hs_steps as $hs_step ) : ?>
						<li>
							<?php echo esc_html( $hs_step['text'] ); ?>
							<?php if ( ! empty( $hs_step['warn'] ) ) : ?>
								<strong class="hs-steps__warn"><?php echo esc_html( $hs_step['warn'] ); ?></strong>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>

			<?php
			/* The page had three video slots — handwashing, machine washing and
			 * Skidmore's aftercare. All three live in the How-To playlist, so it
			 * is embedded whole rather than guessing at individual video IDs.
			 */
			$hs_howto = null;
			foreach ( hs_video_playlists() as $hs_list ) {
				if ( false !== stripos( $hs_list['title'], 'how' ) ) {
					$hs_howto = $hs_list;
					break;
				}
			}

			if ( $hs_howto ) :
				?>
				<div style="margin-top:clamp(3rem,7vw,4.5rem);">
					<div class="hs-section__head">
						<span class="hs-eyebrow"><?php esc_html_e( 'Watch it done', 'hide-and-soul' ); ?></span>
						<h2><?php esc_html_e( 'Hand washing, machine washing and aftercare', 'hide-and-soul' ); ?></h2>
						<p><?php esc_html_e( 'Easier to watch than to read. All three are in our how-to playlist.', 'hide-and-soul' ); ?></p>
					</div>

					<div style="max-width:48rem;margin-inline:auto;">
						<?php hs_video_embed( $hs_howto['id'], 'playlist', $hs_howto['title'] ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Skidmore’s Waterproofer and Leather Cream — what we use, and what we sell.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( home_url( '/product-page/skidmore-s-waterproofer/' ) ); ?>">
					<?php esc_html_e( 'Buy Skidmore’s', 'hide-and-soul' ); ?>
				</a>
			</div>

			<p style="margin-top:2rem;text-align:center;color:var(--hs-muted-text);">
				<?php
				printf(
					/* translators: %s: phone number, linked */
					esc_html__( 'Not sure about your piece? Call us on %s before you put it in water.', 'hide-and-soul' ),
					'<a href="' . esc_url( hs_phone_href() ) . '">' . esc_html( hs_info( 'phone' ) ) . '</a>'
				);
				?>
			</p>

		</div>
	</div>

	<?php
endwhile;

get_footer();
