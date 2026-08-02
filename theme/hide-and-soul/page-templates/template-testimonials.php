<?php
/**
 * Template Name: Testimonials
 *
 * Written reviews, then the customer review videos.
 *
 * The Wix page used a rotating slider for the quotes. This lists them instead —
 * a slider shows one review at a time and hides the rest behind a control most
 * visitors never touch, which is the wrong trade for the most persuasive
 * content on the site.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'See what they have to say about us', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<?php if ( get_the_content() ) : ?>
				<div class="hs-entry__content" style="margin-bottom:2.5rem;">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<div class="hs-grid hs-grid--2">
				<?php foreach ( hs_testimonials() as $hs_review ) : ?>
					<figure class="hs-review" data-hs-reveal>
						<?php if ( ! empty( $hs_review['stars'] ) ) : ?>
							<div class="hs-review__stars" role="img"
								aria-label="<?php echo esc_attr( sprintf(
									/* translators: %d: number of stars */
									_n( '%d out of 5 stars', '%d out of 5 stars', (int) $hs_review['stars'], 'hide-and-soul' ),
									(int) $hs_review['stars']
								) ); ?>">
								<?php echo esc_html( str_repeat( '★', (int) $hs_review['stars'] ) ); ?>
							</div>
						<?php endif; ?>

						<blockquote><?php echo esc_html( $hs_review['quote'] ); ?></blockquote>
						<figcaption>— <?php echo esc_html( $hs_review['author'] ); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>

			<?php /* Video reviews. */ ?>
			<div class="hs-section__head" style="margin-top:clamp(3rem,7vw,4.5rem);">
				<span class="hs-eyebrow"><?php esc_html_e( 'On camera', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'Customer reviews', 'hide-and-soul' ); ?></h2>
				<p><?php esc_html_e( 'Filmed at Sturgis and in the shop. Tap any of them to play here.', 'hide-and-soul' ); ?></p>
			</div>

			<div class="hs-video-grid">
				<?php foreach ( hs_review_video_ids() as $hs_vid ) : ?>
					<?php hs_video_embed( $hs_vid, 'video', __( 'Customer review video', 'hide-and-soul' ) ); ?>
				<?php endforeach; ?>
			</div>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Bought from us? We would love to hear how it is wearing.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php esc_html_e( 'Share your experience', 'hide-and-soul' ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
