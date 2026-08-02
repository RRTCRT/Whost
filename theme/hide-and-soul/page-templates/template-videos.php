<?php
/**
 * Template Name: Videos
 *
 * Replaces the Wix Video app with YouTube embeds. Everything is click-to-load,
 * so the page costs almost nothing until someone presses play.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header(
		get_the_title(),
		__( 'Leather care, riding dogs and the people who wear our work', 'hide-and-soul' )
	);
	hs_breadcrumbs();

	$hs_channel = hs_video_channel();
	$hs_chan_url = 'https://www.youtube.com/' . ltrim( $hs_channel, '/' );
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<?php if ( get_the_content() ) : ?>
				<div class="hs-entry__content" style="margin-bottom:2.5rem;">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<?php /* Playlists do the heavy lifting — YouTube keeps them current. */ ?>
			<div class="hs-grid hs-grid--2">
				<?php foreach ( hs_video_playlists() as $hs_list ) : ?>
					<div data-hs-reveal>
						<h2 style="font-size:1.4rem;"><?php echo esc_html( $hs_list['title'] ); ?></h2>
						<?php hs_video_embed( $hs_list['id'], 'playlist', $hs_list['title'] ); ?>
						<p style="margin-top:0.9rem;color:var(--hs-muted-text);font-size:0.97rem;">
							<?php echo esc_html( $hs_list['intro'] ); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* What's on the channel, as scannable text. */ ?>
			<div class="hs-grid hs-grid--3" style="margin-top:clamp(2.5rem,6vw,4rem);">
				<?php foreach ( hs_video_topics() as $hs_topic => $hs_titles ) : ?>
					<div class="hs-material" data-hs-reveal>
						<h3><?php echo esc_html( $hs_topic ); ?></h3>
						<ul style="margin:0.75rem 0 0;padding-left:1.1rem;font-size:0.95rem;color:var(--hs-muted-text);">
							<?php foreach ( $hs_titles as $hs_title ) : ?>
								<li><?php echo esc_html( $hs_title ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* Everything else, as thumbnails. */ ?>
			<div class="hs-section__head" style="margin-top:clamp(3rem,7vw,4.5rem);">
				<span class="hs-eyebrow"><?php esc_html_e( 'The whole channel', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'Every video', 'hide-and-soul' ); ?></h2>
				<p><?php esc_html_e( 'Tap any of them to play here.', 'hide-and-soul' ); ?></p>
			</div>

			<div class="hs-video-grid">
				<?php foreach ( hs_video_ids() as $hs_vid ) : ?>
					<?php hs_video_embed( $hs_vid, 'video' ); ?>
				<?php endforeach; ?>
			</div>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'New videos land on the channel first.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( $hs_chan_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Subscribe on YouTube', 'hide-and-soul' ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
