<?php
/**
 * Template Name: Custom Orders
 *
 * The page's own editor content renders first, then the base-price table and
 * the category breakdown. Prices come from hs_base_prices() so they are edited
 * in one place rather than retyped into the page body.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'See what we can do', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<div class="hs-split" style="margin-bottom:clamp(2.5rem,6vw,4rem);">
				<div>
					<span class="hs-eyebrow"><?php esc_html_e( 'Getting started', 'hide-and-soul' ); ?></span>
					<h2><?php esc_html_e( 'Tell us what you want made', 'hide-and-soul' ); ?></h2>

					<?php if ( get_the_content() ) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<p><?php esc_html_e( 'For over 40 years we have specialised in creating unique, personalised pieces — from purses to jackets — tailored to your specifications. If you do not see what you are looking for on the site, get in touch with your request and we will work to make it real.', 'hide-and-soul' ); ?></p>
						<p><?php esc_html_e( 'With this many styles and options available, the fastest route is a phone call. Ring Jenn directly and she will walk you through hide, colour, fit and detailing.', 'hide-and-soul' ); ?></p>
					<?php endif; ?>
				</div>

				<div>
					<div class="hs-enquire" style="max-width:none;">
						<p style="font-weight:700;color:var(--hs-bark);">
							<?php esc_html_e( 'Call Jenn to start a custom order', 'hide-and-soul' ); ?>
						</p>
						<a class="hs-btn" href="<?php echo esc_url( hs_phone_href( 'custom_phone' ) ); ?>">
							<?php echo esc_html( hs_info( 'custom_phone' ) ); ?>
						</a>
						<p style="font-size:0.9rem;">
							<?php
							printf(
								/* translators: %s: shop phone number */
								esc_html__( 'For repairs, stock enquiries or anything else, the shop line is %s.', 'hide-and-soul' ),
								esc_html( hs_info( 'phone' ) )
							);
							?>
						</p>
						<a class="hs-btn hs-btn--outline" href="mailto:<?php echo esc_attr( hs_info( 'email' ) ); ?>">
							<?php esc_html_e( 'Email us instead', 'hide-and-soul' ); ?>
						</a>
					</div>
				</div>
			</div>

			<?php /* Base pricing */ ?>
			<div class="hs-section__head">
				<span class="hs-eyebrow"><?php esc_html_e( 'Base pricing', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'Where each garment starts', 'hide-and-soul' ); ?></h2>
				<p><?php esc_html_e( 'These are the base prices for each garment we make. Every customisation adds to it — call for a quote on exactly what you have in mind.', 'hide-and-soul' ); ?></p>
			</div>

			<div class="hs-grid hs-grid--4" style="margin-bottom:clamp(2.5rem,6vw,4rem);">
				<?php foreach ( hs_base_prices() as $group => $items ) : ?>
					<div class="hs-material" data-hs-reveal>
						<h3><?php echo esc_html( $group ); ?></h3>
						<ul class="hs-hours" style="margin-top:0.75rem;">
							<?php foreach ( $items as $label => $price ) : ?>
								<li>
									<span class="hs-hours__day"><?php echo esc_html( $label ); ?></span>
									<span><?php echo esc_html( '$' . number_format_i18n( $price ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>

			<?php
			/* What we can customise, by category. */
			$hs_categories = array(
				array(
					'title' => __( 'Jackets & Shirts', 'hide-and-soul' ),
					'from'  => __( '$899 and up', 'hide-and-soul' ),
					'copy'  => __( 'Our most common custom order. Choose the leather, style, colour, fringe or braid detailing, the collar and primary closure, the cuff type — and anything else you can think of.', 'hide-and-soul' ),
				),
				array(
					'title' => __( 'Men’s Vests', 'hide-and-soul' ),
					'from'  => __( '$525 and up', 'hide-and-soul' ),
					'copy'  => __( 'We have made every kind of vest over the years. Pick from our available colours and hides, and tell us if you have a specific idea in mind.', 'hide-and-soul' ),
				),
				array(
					'title' => __( 'Ladies Vests', 'hide-and-soul' ),
					'from'  => __( '$399 and up', 'hide-and-soul' ),
					'copy'  => __( 'A diverse selection crafted from deerskin in an array of colours — classic styles, or something entirely of your own design.', 'hide-and-soul' ),
				),
				array(
					'title' => __( 'Chaps & Half Chaps', 'hide-and-soul' ),
					'from'  => __( 'Chaps $895 · 1/2 chaps $399 and up', 'hide-and-soul' ),
					'copy'  => __( 'Standard shotgun style, with room to add fringe, braid, rustic strips, conchos or pockets — even years after purchase. Fit, colour and length are all yours to set. We have also made a good number of Mountain Man pants.', 'hide-and-soul' ),
				),
				array(
					'title' => __( 'Holsters, Belts & Knife Sheaths', 'hide-and-soul' ),
					'from'  => '',
					'copy'  => __( 'Custom holsters and sheaths built to your needs, with patterns on hand for popular handguns and bespoke work welcome. Rick heads our hard leather department and has years of it behind him.', 'hide-and-soul' ),
				),
				array(
					'title' => __( 'Your Own Hides', 'hide-and-soul' ),
					'from'  => '',
					'copy'  => __( 'Have tanned and dyed hides of your own? Bring them to us. The size and weight of your leather tells us what it can become — we have turned personal hides into mountain man shirts, vests, chaps, purses and moose-hide jackets.', 'hide-and-soul' ),
				),
			);

			foreach ( $hs_categories as $hs_cat ) :
				?>
				<div class="hs-card" style="margin-bottom:1.25rem;" data-hs-reveal>
					<div class="hs-card__body">
						<h3><?php echo esc_html( $hs_cat['title'] ); ?></h3>
						<?php if ( $hs_cat['from'] ) : ?>
							<p class="hs-eyebrow" style="margin-bottom:0.75rem;"><?php echo esc_html( $hs_cat['from'] ); ?></p>
						<?php endif; ?>
						<p style="margin-bottom:0;"><?php echo esc_html( $hs_cat['copy'] ); ?></p>
					</div>
				</div>
				<?php
			endforeach;
			?>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Ready to start? Call Jenn and talk it through.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( hs_phone_href( 'custom_phone' ) ); ?>">
					<?php echo esc_html( hs_info( 'custom_phone' ) ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
