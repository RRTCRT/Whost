<?php
/**
 * Template Name: Black Hills Guide
 *
 * Renders hs_black_hills_guide() beneath the page's own content, with a jump
 * nav, RV warnings called out, and our stockists flagged.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header(
		get_the_title(),
		__( 'Where we eat, drive and wander when we are not in the shop', 'hide-and-soul' )
	);
	hs_breadcrumbs();

	$hs_guide = hs_black_hills_guide();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<?php if ( get_the_content() ) : ?>
				<div class="hs-entry__content" style="margin-bottom:2.5rem;">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<?php /* Jump nav — the page is long. */ ?>
			<nav class="hs-jump" aria-label="<?php esc_attr_e( 'Guide sections', 'hide-and-soul' ); ?>">
				<?php foreach ( $hs_guide as $hs_heading => $hs_section ) : ?>
					<a href="#<?php echo esc_attr( sanitize_title( $hs_heading ) ); ?>">
						<?php echo esc_html( $hs_heading ); ?>
						<span><?php echo esc_html( count( $hs_section['items'] ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php foreach ( $hs_guide as $hs_heading => $hs_section ) : ?>
				<section id="<?php echo esc_attr( sanitize_title( $hs_heading ) ); ?>" class="hs-guide-section">
					<div class="hs-section__head">
						<h2><?php echo esc_html( $hs_heading ); ?></h2>
						<p><?php echo esc_html( $hs_section['intro'] ); ?></p>
					</div>

					<ul class="hs-guide-list">
						<?php
						foreach ( $hs_section['items'] as $hs_item ) :
							$hs_no_rv = isset( $hs_item['rv'] ) && false === $hs_item['rv'];
							$hs_stock = ! empty( $hs_item['ours'] );
							?>
							<li<?php echo $hs_no_rv ? ' class="is-warning"' : ''; ?>>
								<span class="hs-guide-list__name">
									<?php echo esc_html( $hs_item['name'] ); ?>
									<?php if ( $hs_stock ) : ?>
										<span class="hs-badge"><?php esc_html_e( 'Carries our work', 'hide-and-soul' ); ?></span>
									<?php endif; ?>
								</span>
								<span class="hs-guide-list__town"><?php echo esc_html( $hs_item['town'] ); ?></span>
								<?php if ( $hs_no_rv ) : ?>
									<span class="hs-guide-list__warn">
										<?php esc_html_e( 'Not suitable for 25ft+ RVs or trailers', 'hide-and-soul' ); ?>
									</span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endforeach; ?>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Passing through? We are eight miles south of Deadwood on US Hwy 385, just south of Nemo Road.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>">
					<?php esc_html_e( 'Find the shop', 'hide-and-soul' ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
