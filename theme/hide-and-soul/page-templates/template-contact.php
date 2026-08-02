<?php
/**
 * Template Name: Contact
 *
 * Contact details plus the enquiry form.
 *
 * The form itself is left to a plugin — put a [fluentform] or [wpforms]
 * shortcode in the page content and it renders in the panel below, inheriting
 * the theme's input styling. A hand-rolled mail() form would be a spam magnet
 * and would lose every enquiry the moment Hostinger throttled it.
 *
 * Fields to recreate, matching the Wix form: Name, Email, Phone, Subject,
 * Message. All five were required.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'Call, email, or send us a message', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap hs-split">

			<div>
				<span class="hs-eyebrow"><?php esc_html_e( 'Get in touch', 'hide-and-soul' ); ?></span>
				<h2><?php echo esc_html( hs_info( 'owners' ) ); ?></h2>

				<ul class="hs-location__meta" style="margin-bottom:1.75rem;">
					<li>
						<strong><?php esc_html_e( 'Shop', 'hide-and-soul' ); ?></strong>
						<span><a href="<?php echo esc_url( hs_phone_href() ); ?>"><?php echo esc_html( hs_info( 'phone' ) ); ?></a></span>
					</li>
					<li>
						<strong><?php esc_html_e( 'Cell', 'hide-and-soul' ); ?></strong>
						<span><a href="<?php echo esc_url( hs_phone_href( 'custom_phone' ) ); ?>"><?php echo esc_html( hs_info( 'custom_phone' ) ); ?></a></span>
					</li>
					<li>
						<strong><?php esc_html_e( 'Email', 'hide-and-soul' ); ?></strong>
						<span><a href="mailto:<?php echo esc_attr( hs_info( 'email' ) ); ?>"><?php echo esc_html( hs_info( 'email' ) ); ?></a></span>
					</li>
					<li>
						<strong><?php esc_html_e( 'Address', 'hide-and-soul' ); ?></strong>
						<span><?php echo esc_html( hs_address_line() ); ?></span>
					</li>
				</ul>

				<p style="font-size:0.92rem;color:var(--hs-muted-text);">
					<?php
					printf(
						/* translators: %s: email address */
						esc_html__( 'Replies come from %s — worth adding to your contacts so we do not land in spam.', 'hide-and-soul' ),
						esc_html( hs_info( 'email' ) )
					);
					?>
				</p>

				<h3 style="margin-top:2rem;"><?php esc_html_e( 'Hours', 'hide-and-soul' ); ?></h3>
				<?php hs_hours_list(); ?>
				<p style="font-size:0.9rem;color:var(--hs-muted-text);margin-top:0.75rem;">
					<?php echo esc_html( hs_season_note() ); ?>
				</p>

				<?php hs_social_links(); ?>
			</div>

			<div>
				<div class="hs-enquire" style="max-width:none;">
					<h2 style="margin-bottom:0.75rem;"><?php esc_html_e( 'Send us a message', 'hide-and-soul' ); ?></h2>

					<?php
					if ( get_the_content() ) {
						// The form shortcode lives in the page content.
						the_content();
					} else {
						echo '<p>' . esc_html__( 'Add your contact form shortcode to this page in the editor — Name, Email, Phone, Subject and Message. Until then, the quickest route is the phone.', 'hide-and-soul' ) . '</p>';
						echo '<a class="hs-btn" href="' . esc_url( hs_phone_href() ) . '">'
							. esc_html( sprintf( __( 'Call %s', 'hide-and-soul' ), hs_info( 'phone' ) ) )
							. '</a>';
					}
					?>
				</div>

				<iframe
					class="hs-map"
					style="margin-top:1.5rem;"
					src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( hs_address_line() ) . '&output=embed' ); ?>"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="<?php esc_attr_e( 'Map to the Deadwood shop', 'hide-and-soul' ); ?>"></iframe>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
