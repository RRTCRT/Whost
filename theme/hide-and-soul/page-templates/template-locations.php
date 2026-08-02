<?php
/**
 * Template Name: Locations
 *
 * Renders every storefront from hs_locations() beneath the page's own content,
 * so hours and addresses stay in one place instead of being retyped per page.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'Where to find us, season by season', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<?php if ( get_the_content() ) : ?>
				<div class="hs-entry__content" style="margin-bottom:3rem;">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<div class="hs-callout" style="margin-bottom:2.5rem;">
				<p><?php echo esc_html( hs_season_note() ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( hs_phone_href() ); ?>">
					<?php echo esc_html( hs_info( 'phone' ) ); ?>
				</a>
			</div>

			<div class="hs-grid hs-grid--2">
				<?php
				$hs_season = hs_current_season();

				foreach ( hs_locations() as $loc ) :
					$hs_open_now = isset( $loc['key'] ) && $loc['key'] === $hs_season;
					?>
					<div class="hs-location<?php echo $hs_open_now ? ' is-open-now' : ''; ?>" data-hs-reveal>
						<h2 style="font-size:1.4rem;">
							<?php echo esc_html( $loc['name'] ); ?>
							<?php if ( $hs_open_now ) : ?>
								<span class="hs-badge"><?php esc_html_e( 'Open this season', 'hide-and-soul' ); ?></span>
							<?php endif; ?>
						</h2>

						<ul class="hs-location__meta">
							<?php if ( $loc['street'] ) : ?>
								<li>
									<strong><?php esc_html_e( 'Address', 'hide-and-soul' ); ?></strong>
									<span><?php echo esc_html( trim( $loc['street'] . ', ' . $loc['city'] . ', ' . $loc['region'] . ' ' . $loc['postal'] ) ); ?></span>
								</li>
							<?php else : ?>
								<li>
									<strong><?php esc_html_e( 'Area', 'hide-and-soul' ); ?></strong>
									<span><?php echo esc_html( trim( $loc['city'] . ', ' . $loc['region'] ) ); ?></span>
								</li>
							<?php endif; ?>

							<li>
								<strong><?php esc_html_e( 'Season', 'hide-and-soul' ); ?></strong>
								<span><?php echo esc_html( $loc['season'] ); ?></span>
							</li>

							<li>
								<strong><?php echo esc_html( empty( $loc['cell'] ) ? __( 'Cell', 'hide-and-soul' ) : __( 'Main', 'hide-and-soul' ) ); ?></strong>
								<span><a href="tel:+<?php echo esc_attr( preg_replace( '/\D+/', '', $loc['phone'] ) ); ?>"><?php echo esc_html( $loc['phone'] ); ?></a></span>
							</li>

							<?php if ( ! empty( $loc['cell'] ) ) : ?>
								<li>
									<strong><?php esc_html_e( 'Cell', 'hide-and-soul' ); ?></strong>
									<span><a href="tel:+<?php echo esc_attr( preg_replace( '/\D+/', '', $loc['cell'] ) ); ?>"><?php echo esc_html( $loc['cell'] ); ?></a></span>
								</li>
							<?php endif; ?>

							<li>
								<strong><?php esc_html_e( 'Email', 'hide-and-soul' ); ?></strong>
								<span><a href="mailto:<?php echo esc_attr( hs_info( 'email' ) ); ?>"><?php echo esc_html( hs_info( 'email' ) ); ?></a></span>
							</li>
						</ul>

						<?php if ( $loc['note'] ) : ?>
							<p><?php echo esc_html( $loc['note'] ); ?></p>
						<?php endif; ?>

						<?php if ( $loc['map'] ) : ?>
							<iframe
								class="hs-map"
								src="<?php echo esc_url( $loc['map'] ); ?>"
								loading="lazy"
								referrerpolicy="no-referrer-when-downgrade"
								title="<?php echo esc_attr( sprintf( __( 'Map to %s', 'hide-and-soul' ), $loc['name'] ) ); ?>"></iframe>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* Approved wholesalers — other shops that stock our work. */ ?>
			<div style="margin-top:clamp(3rem,7vw,4.5rem);">
				<div class="hs-section__head">
					<span class="hs-eyebrow"><?php esc_html_e( 'Also stocked at', 'hide-and-soul' ); ?></span>
					<h2><?php esc_html_e( 'Approved wholesalers in the Black Hills', 'hide-and-soul' ); ?></h2>
					<p><?php esc_html_e( 'We have sold wholesale for years. If you have been to a rodeo or a motorcycle event and seen something like our hip bags, there is a fair chance it was ours.', 'hide-and-soul' ); ?></p>
				</div>

				<div class="hs-grid hs-grid--3">
					<?php foreach ( hs_wholesalers() as $hs_shop ) : ?>
						<div class="hs-material" data-hs-reveal>
							<h3 style="font-size:1.1rem;"><?php echo esc_html( $hs_shop['name'] ); ?></h3>
							<p>
								<?php echo esc_html( $hs_shop['street'] ); ?><br>
								<?php echo esc_html( $hs_shop['city'] . ', ' . $hs_shop['region'] . ' ' . $hs_shop['postal'] ); ?><br>
								<a href="tel:+1<?php echo esc_attr( preg_replace( '/\D+/', '', $hs_shop['phone'] ) ); ?>"><?php echo esc_html( $hs_shop['phone'] ); ?></a>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div style="max-width:24rem;margin:3rem auto 0;">
				<h2 style="text-align:center;"><?php esc_html_e( 'Shop hours', 'hide-and-soul' ); ?></h2>
				<p style="text-align:center;font-size:0.9rem;color:var(--hs-muted);margin-top:-0.5rem;">
					<?php esc_html_e( 'The same at both shops.', 'hide-and-soul' ); ?>
				</p>
				<?php hs_hours_list(); ?>
				<p style="text-align:center;font-size:0.9rem;color:var(--hs-muted);margin-top:1rem;">
					<?php echo esc_html( hs_info( 'hours_note' ) ); ?>
				</p>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
