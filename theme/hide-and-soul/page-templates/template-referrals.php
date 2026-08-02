<?php
/**
 * Template Name: Customer Referral Program
 *
 * The Wix page carried a title, the line "sharing rewards.", a photo, a sign-up
 * form, and a success message reading "Your Customer Number is on its way!" —
 * and nothing else. It never said what the reward actually is.
 *
 * That gap is left visible rather than invented: the terms are the one thing
 * this page exists to communicate, and making them up would be worse than
 * leaving a marked placeholder. Write them into the page content in wp-admin
 * and they render below.
 *
 * Form fields to recreate, from the page: First Name, Last Name, Email, Phone,
 * Message. (Note this differs from the Contact form, which used a single Name
 * plus a Subject field.)
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), __( 'Sharing rewards', 'hide-and-soul' ) );
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap hs-split">

			<div>
				<span class="hs-eyebrow"><?php esc_html_e( 'How it works', 'hide-and-soul' ); ?></span>
				<h2><?php esc_html_e( 'Send a friend our way', 'hide-and-soul' ); ?></h2>

				<?php if ( get_the_content() ) : ?>
					<div class="hs-entry__content" style="max-width:none;">
						<?php the_content(); ?>
					</div>
				<?php else : ?>
					<p><?php esc_html_e( 'Share Hide and Soul with your friends and be rewarded with credit toward our leather products.', 'hide-and-soul' ); ?></p>
					<p><?php esc_html_e( 'Sign up and we will send you your customer number. Give it to anyone you send our way, and when they order, the credit comes back to you.', 'hide-and-soul' ); ?></p>

					<div class="hs-callout" style="margin-top:1.5rem;">
						<p>
							<strong><?php esc_html_e( 'To do:', 'hide-and-soul' ); ?></strong>
							<?php esc_html_e( 'the old page never said what the reward is. Add the terms here — how much credit, on what, and whether it expires.', 'hide-and-soul' ); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<div class="hs-enquire" style="max-width:none;">
					<h2 style="margin-bottom:0.5rem;"><?php esc_html_e( 'Sign up', 'hide-and-soul' ); ?></h2>
					<p><?php esc_html_e( 'Send us your details and your customer number is on its way.', 'hide-and-soul' ); ?></p>
					<p style="font-size:0.9rem;">
						<?php esc_html_e( 'Add your sign-up form shortcode to this page in the editor. Until then, the phone works just as well.', 'hide-and-soul' ); ?>
					</p>
					<a class="hs-btn" href="<?php echo esc_url( hs_phone_href() ); ?>">
						<?php echo esc_html( sprintf( __( 'Call %s', 'hide-and-soul' ), hs_info( 'phone' ) ) ); ?>
					</a>
					<a class="hs-btn hs-btn--outline" href="mailto:<?php echo esc_attr( hs_info( 'email' ) ); ?>?subject=<?php echo rawurlencode( __( 'Referral program sign-up', 'hide-and-soul' ) ); ?>">
						<?php esc_html_e( 'Email to sign up', 'hide-and-soul' ); ?>
					</a>
				</div>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
