<?php
/**
 * Template Name: About
 *
 * The shop's story. Editor content renders first when present; the timeline and
 * the standing copy follow, so the page is never empty and the dates stay in
 * one place.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'Forty years of handcrafted leather', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<div class="hs-split" style="margin-bottom:clamp(2.5rem,6vw,4rem);">
				<div>
					<span class="hs-eyebrow"><?php esc_html_e( 'The Todds', 'hide-and-soul' ); ?></span>
					<h2><?php esc_html_e( 'Who we are', 'hide-and-soul' ); ?></h2>

					<?php if ( get_the_content() ) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<p><?php esc_html_e( 'Our small family business has a passion to create unique, quality leather garments and accessories that more than exceed your expectations. For over 40 years our leather crafting has served hunters, horsemen, woodsmen and historical reenactors.', 'hide-and-soul' ); ?></p>
						<p><?php esc_html_e( 'In 1998 our focus shifted to meeting the needs of bikers worldwide — practical, comfortable, well-fitting garments. All of our products are individually handcrafted and can be custom-made to order, and every one comes unlined for superior fit and breathability.', 'hide-and-soul' ); ?></p>
						<p><?php esc_html_e( 'Every handmade item — bag, chap, vest or jacket — is made 100% in our shop, by our family, with a satisfaction guarantee. When you buy something from Hide and Soul you are joining the family. And who would not want to, with golden retrievers like Caleb and Cash?', 'hide-and-soul' ); ?></p>
					<?php endif; ?>

					<div class="hs-hero__actions" style="justify-content:flex-start;margin-top:1.5rem;">
						<a class="hs-btn" href="<?php echo esc_url( home_url( '/custom-orders/' ) ); ?>">
							<?php esc_html_e( 'Start a custom order', 'hide-and-soul' ); ?>
						</a>
						<a class="hs-btn hs-btn--outline" href="<?php echo esc_url( home_url( '/videos/' ) ); ?>">
							<?php esc_html_e( 'Meet the dogs', 'hide-and-soul' ); ?>
						</a>
					</div>
				</div>

				<div class="hs-split__media" data-hs-reveal>
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'hs-card', array( 'loading' => 'lazy' ) );
					}
					?>
				</div>
			</div>

			<?php
			/* Milestones. Every date here is drawn from the shop's own pages. */
			$hs_timeline = array(
				array(
					'year' => '1983',
					'copy' => __( 'Rick and Jennifer Todd start cutting leather in the Black Hills. The Ladies Rustic vest is among the very first styles — it is still made today.', 'hide-and-soul' ),
				),
				array(
					'year' => '1998',
					'copy' => __( 'The focus shifts to bikers: practical, comfortable, well-fitting leather built for the road rather than the display case.', 'hide-and-soul' ),
				),
				array(
					'year' => __( 'Early 2000s', 'hide-and-soul' ),
					'copy' => __( 'The Ladies Rustic vest becomes the shop’s top seller, and has stayed there for more than twenty-five years.', 'hide-and-soul' ),
				),
				array(
					'year' => __( 'Today', 'hide-and-soul' ),
					'copy' => __( 'Two shops on a seasonal circuit — the Black Hills from April to November, Cave Creek, Arizona through the winter — still cutting and sewing everything in-house.', 'hide-and-soul' ),
				),
			);
			?>

			<div class="hs-section__head">
				<span class="hs-eyebrow"><?php esc_html_e( 'Over 40 years of work', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'How we got here', 'hide-and-soul' ); ?></h2>
			</div>

			<div class="hs-grid hs-grid--4">
				<?php foreach ( $hs_timeline as $hs_step ) : ?>
					<div class="hs-material" data-hs-reveal>
						<h3 style="font-size:1.5rem;color:var(--hs-link);"><?php echo esc_html( $hs_step['year'] ); ?></h3>
						<p><?php echo esc_html( $hs_step['copy'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* The hides, in the shop's own words. */ ?>
			<div class="hs-section__head" style="margin-top:clamp(3rem,7vw,4.5rem);">
				<span class="hs-eyebrow"><?php esc_html_e( 'What we work in', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'Deer, elk and bison', 'hide-and-soul' ); ?></h2>
				<p><?php esc_html_e( 'Niche leathers, each with its own qualities. Every garment we make is unlined, for fit and for breathability.', 'hide-and-soul' ); ?></p>
			</div>

			<div class="hs-grid hs-grid--3">
				<?php
				$hs_hides = array(
					array( __( 'Deerskin', 'hide-and-soul' ), __( 'Softer, and fits like a glove right off the rack.', 'hide-and-soul' ) ),
					array( __( 'Elk', 'hide-and-soul' ), __( 'Moderately heavy, with great insulation.', 'hide-and-soul' ) ),
					array( __( 'Bison', 'hide-and-soul' ), __( 'Firmer and highly durable — it breaks in fantastically into a piece you will wear for years.', 'hide-and-soul' ) ),
				);

				foreach ( $hs_hides as $hs_hide ) :
					?>
					<div class="hs-material" data-hs-reveal>
						<h3><?php echo esc_html( $hs_hide[0] ); ?></h3>
						<p><?php echo esc_html( $hs_hide[1] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Come and see us — we would love to meet you.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>">
					<?php esc_html_e( 'Find the shop', 'hide-and-soul' ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
