<?php
/**
 * Template Name: Repairs & Patches
 *
 * Repair and alteration services, the pricing caveats, and a before/after
 * gallery drawn from the page's own content.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header(
		get_the_title(),
		__( 'You break it, we fix it', 'hide-and-soul' )
	);
	hs_breadcrumbs();
	?>

	<div class="hs-section">
		<div class="hs-wrap">

			<?php /* The caveats come first — they set expectations before anyone asks a price. */ ?>
			<div class="hs-entry__content" style="margin-bottom:clamp(2.5rem,6vw,3.5rem);">
				<p><?php esc_html_e( 'Please keep in mind that all prices are general estimates, and each project is as unique as you are. In most cases we can only give you a firm price once we have seen the project ourselves — patch placement on the vest or jacket, and size and complexity, are the two biggest factors.', 'hide-and-soul' ); ?></p>

				<ul>
					<li><?php esc_html_e( 'Patch sewing starts at $5.', 'hide-and-soul' ); ?></li>
					<li><?php esc_html_e( 'We can sew patches onto pockets without closing them, for an additional upcharge.', 'hide-and-soul' ); ?></li>
					<li><?php esc_html_e( 'We do not make patches, and we have no embroidery capabilities.', 'hide-and-soul' ); ?></li>
					<li><?php esc_html_e( 'We work on more than leather — bring in canvas or other heavy materials and we will tell you what we can do.', 'hide-and-soul' ); ?></li>
				</ul>
			</div>

			<div class="hs-grid hs-grid--2">

				<?php
				$hs_services = array(
					array(
						'eyebrow' => __( 'You break it, we fix it', 'hide-and-soul' ),
						'title'   => __( 'Repairs', 'hide-and-soul' ),
						'items'   => array(
							__( 'Snaps & conchos', 'hide-and-soul' ),
							__( 'Zippers & zipper pulls', 'hide-and-soul' ),
							__( 'Tears', 'hide-and-soul' ),
							__( 'Buckles', 'hide-and-soul' ),
							__( 'Grommets', 'hide-and-soul' ),
							__( 'Rivets', 'hide-and-soul' ),
							__( 'Stitching & seams', 'hide-and-soul' ),
							__( 'Purse hardware', 'hide-and-soul' ),
						),
					),
					array(
						'eyebrow' => __( 'Let’s get your leather fitting correctly', 'hide-and-soul' ),
						'title'   => __( 'Alterations', 'hide-and-soul' ),
						'items'   => array(
							__( 'Sleeve shortening', 'hide-and-soul' ),
							__( 'Down sizing', 'hide-and-soul' ),
							__( 'Adding sidelaces', 'hide-and-soul' ),
							__( 'Taking out sidelaces', 'hide-and-soul' ),
							__( 'Downsizing a chap waist', 'hide-and-soul' ),
							__( 'Chap waist extension', 'hide-and-soul' ),
							__( 'Adding chap length', 'hide-and-soul' ),
							__( 'Shortening chaps', 'hide-and-soul' ),
						),
					),
				);

				foreach ( $hs_services as $hs_service ) :
					?>
					<div class="hs-material" data-hs-reveal>
						<span class="hs-eyebrow"><?php echo esc_html( $hs_service['eyebrow'] ); ?></span>
						<h2 style="font-size:1.5rem;"><?php echo esc_html( $hs_service['title'] ); ?></h2>
						<ul style="margin:0.9rem 0 0;padding-left:1.1rem;">
							<?php foreach ( $hs_service['items'] as $hs_item ) : ?>
								<li><?php echo esc_html( $hs_item ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

			</div>

			<?php
			/* Before/after gallery.
			 *
			 * The Wix page showed roughly a dozen paired photos — dog-chewed
			 * sandal, sleeve shortening, length extension, chap extension and
			 * so on. Wix loads galleries with JavaScript, so the saved page
			 * carried the captions but not the images; they need re-uploading.
			 * Put a WordPress gallery block in the page content and it renders
			 * here.
			 */
			if ( get_the_content() ) :
				?>
				<div style="margin-top:clamp(3rem,7vw,4.5rem);">
					<div class="hs-section__head">
						<span class="hs-eyebrow"><?php esc_html_e( 'Before & after', 'hide-and-soul' ); ?></span>
						<h2><?php esc_html_e( 'Work we have done', 'hide-and-soul' ); ?></h2>
					</div>

					<div class="hs-entry__content hs-entry__content--wide">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Bring it in, or send us a photo and we will tell you straight whether it is worth fixing.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( hs_phone_href() ); ?>">
					<?php echo esc_html( hs_info( 'phone' ) ); ?>
				</a>
			</div>

		</div>
	</div>

	<?php
endwhile;

get_footer();
