<?php
/**
 * Homepage.
 *
 * Sections are ordered to answer, in order: what is this, what can I buy,
 * what is it made of, can you fix mine, where are you. That mirrors how the
 * Wix homepage read while dropping its slideshow-heavy structure.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

$hero_id  = (int) get_theme_mod( 'hs_hero_image' );
$hero_img = $hero_id ? wp_get_attachment_image_url( $hero_id, 'hs-hero' ) : '';
$hero_bg  = $hero_img ? ' style="background-image:url(' . esc_url( $hero_img ) . ')"' : '';
?>

<section class="hs-hero"<?php echo $hero_bg; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>>
	<div class="hs-wrap hs-hero__inner">
		<span class="hs-eyebrow"><?php esc_html_e( 'Deadwood, South Dakota', 'hide-and-soul' ); ?></span>
		<h1><?php echo esc_html( get_theme_mod( 'hs_hero_heading', 'Leather that outlives the trip.' ) ); ?></h1>
		<p class="hs-hero__sub"><?php echo esc_html( get_theme_mod( 'hs_hero_sub', 'Deer, elk and bison hides cut, sewn and fitted by our family in the Black Hills of South Dakota — for over forty years.' ) ); ?></p>

		<div class="hs-hero__actions">
			<a class="hs-btn" href="<?php echo esc_url( get_theme_mod( 'hs_hero_cta_url', '/shop/' ) ); ?>">
				<?php echo esc_html( get_theme_mod( 'hs_hero_cta_text', 'Shop the collection' ) ); ?>
			</a>
			<a class="hs-btn hs-btn--ghost" href="<?php echo esc_url( get_theme_mod( 'hs_hero_cta2_url', '/custom-orders/' ) ); ?>">
				<?php echo esc_html( get_theme_mod( 'hs_hero_cta2_text', 'Custom orders' ) ); ?>
			</a>
		</div>
	</div>
</section>

<?php
/* ---------------------------------------------------------------------
 * Shop categories
 *
 * Pulls live WooCommerce product categories when the store exists, and
 * falls back to static tiles so the page is never empty pre-import.
 * ------------------------------------------------------------------- */
?>
<section class="hs-section">
	<div class="hs-wrap">
		<div class="hs-section__head">
			<span class="hs-eyebrow"><?php esc_html_e( 'The collection', 'hide-and-soul' ); ?></span>
			<h2><?php esc_html_e( 'Built to be worn hard', 'hide-and-soul' ); ?></h2>
			<p><?php esc_html_e( 'Vests, jackets, chaps and bags — every one of them cut in our shop and sized from 2XS to 5XL.', 'hide-and-soul' ); ?></p>
		</div>

		<div class="hs-grid hs-grid--4">
			<?php
			$terms = array();

			if ( taxonomy_exists( 'product_cat' ) ) {
				$terms = get_terms( array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 8,
					'parent'     => 0,
					'orderby'    => 'menu_order',
				) );
			}

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) :
				foreach ( $terms as $term ) :
					$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
					?>
					<a class="hs-tile" href="<?php echo esc_url( get_term_link( $term ) ); ?>" data-hs-reveal>
						<?php
						if ( $thumb_id ) {
							echo wp_get_attachment_image( $thumb_id, 'hs-tile', false, array( 'alt' => esc_attr( $term->name ) ) );
						}
						?>
						<span class="hs-tile__label"><?php echo esc_html( $term->name ); ?></span>
					</a>
					<?php
				endforeach;
			else :
				// Pre-import placeholders. These slugs match the Wix shop sections.
				$fallback = array(
					'shop-mens'    => __( 'Men’s', 'hide-and-soul' ),
					'shop-womens'  => __( 'Women’s', 'hide-and-soul' ),
					'chaps'        => __( 'Chaps & Half Chaps', 'hide-and-soul' ),
					'accessories'  => __( 'Bags & Accessories', 'hide-and-soul' ),
				);

				foreach ( $fallback as $slug => $label ) :
					?>
					<a class="hs-tile" href="<?php echo esc_url( home_url( '/' . $slug . '/' ) ); ?>" data-hs-reveal>
						<span class="hs-tile__label"><?php echo esc_html( $label ); ?></span>
					</a>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>

<?php /* Story */ ?>
<section class="hs-section hs-section--alt">
	<div class="hs-wrap hs-split">
		<div class="hs-split__media" data-hs-reveal>
			<?php
			$story_img = get_theme_mod( 'hs_story_image' );
			if ( $story_img ) {
				echo wp_get_attachment_image( (int) $story_img, 'hs-card', false, array( 'alt' => '' ) );
			}
			?>
		</div>

		<div data-hs-reveal>
			<span class="hs-eyebrow"><?php esc_html_e( 'Forty years in', 'hide-and-soul' ); ?></span>
			<h2><?php esc_html_e( 'A family shop, not a factory', 'hide-and-soul' ); ?></h2>
			<p><?php esc_html_e( 'Every bag, chap, vest and jacket is made start to finish in our shop, by our family. Nothing is drop-shipped and nothing is sewn overseas. If it does not fit right, bring it back and we will make it fit.', 'hide-and-soul' ); ?></p>
			<p><!-- TODO: replace with the real About copy from the Wix /about-us page. --></p>
			<a class="hs-btn hs-btn--outline" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">
				<?php esc_html_e( 'Our story', 'hide-and-soul' ); ?>
			</a>
		</div>
	</div>
</section>

<?php /* Materials — the question every customer asks first */ ?>
<section class="hs-section">
	<div class="hs-wrap">
		<div class="hs-section__head">
			<span class="hs-eyebrow"><?php esc_html_e( 'Choose your hide', 'hide-and-soul' ); ?></span>
			<h2><?php esc_html_e( 'Deer, elk or bison', 'hide-and-soul' ); ?></h2>
		</div>

		<div class="hs-grid hs-grid--3">
			<?php
			$hides = array(
				array(
					'name' => __( 'Deerskin', 'hide-and-soul' ),
					'copy' => __( 'The softest of the three. Light, breathable and it fits like a glove once it warms to you — the usual pick for vests and shirts.', 'hide-and-soul' ),
				),
				array(
					'name' => __( 'Elk', 'hide-and-soul' ),
					'copy' => __( 'Moderately heavy with real insulation. A good middle ground when you want warmth on the bike without the weight of bison.', 'hide-and-soul' ),
				),
				array(
					'name' => __( 'Bison', 'hide-and-soul' ),
					'copy' => __( 'Firmer and the most durable hide we cut. Holds its shape, takes abuse, and breaks in over years rather than weeks.', 'hide-and-soul' ),
				),
			);

			foreach ( $hides as $hide ) :
				?>
				<div class="hs-material" data-hs-reveal>
					<h3><?php echo esc_html( $hide['name'] ); ?></h3>
					<p><?php echo esc_html( $hide['copy'] ); ?></p>
				</div>
				<?php
			endforeach;
			?>
		</div>
	</div>
</section>

<?php /* Services */ ?>
<section class="hs-section hs-section--alt">
	<div class="hs-wrap">
		<div class="hs-grid hs-grid--2">

			<div class="hs-card" data-hs-reveal>
				<div class="hs-card__body">
					<span class="hs-eyebrow"><?php esc_html_e( 'Made to measure', 'hide-and-soul' ); ?></span>
					<h3><?php esc_html_e( 'Custom orders', 'hide-and-soul' ); ?></h3>
					<p><?php esc_html_e( 'Pick the hide, the colour, the fringe, the conchos, the pocket layout. We build to your measurements — tall sizes, solid sides, side lace, all of it.', 'hide-and-soul' ); ?></p>
					<a class="hs-card__link" href="<?php echo esc_url( home_url( '/custom-orders/' ) ); ?>"><?php esc_html_e( 'Start a custom order', 'hide-and-soul' ); ?></a>
				</div>
			</div>

			<div class="hs-card" data-hs-reveal>
				<div class="hs-card__body">
					<span class="hs-eyebrow"><?php esc_html_e( 'While you wait, often', 'hide-and-soul' ); ?></span>
					<h3><?php esc_html_e( 'Repairs & patches', 'hide-and-soul' ); ?></h3>
					<p><?php esc_html_e( 'Zippers, snaps, seams, alterations and patch sewing — on our leather or anyone else’s. Bring it in and we will tell you straight whether it is worth fixing.', 'hide-and-soul' ); ?></p>
					<a class="hs-card__link" href="<?php echo esc_url( home_url( '/repairs-patches/' ) ); ?>"><?php esc_html_e( 'See repair services', 'hide-and-soul' ); ?></a>
				</div>
			</div>

		</div>
	</div>
</section>

<?php /* Visit */ ?>
<section class="hs-section hs-section--dark">
	<div class="hs-wrap hs-split">
		<div>
			<span class="hs-eyebrow"><?php esc_html_e( 'Find us', 'hide-and-soul' ); ?></span>
			<h2><?php esc_html_e( 'Eight miles south of Deadwood', 'hide-and-soul' ); ?></h2>
			<p><?php echo esc_html( hs_address_line() ); ?></p>
			<p><?php echo esc_html( hs_info( 'season_note' ) ); ?></p>

			<div class="hs-hero__actions" style="justify-content:flex-start;">
				<a class="hs-btn" href="<?php echo esc_url( hs_phone_href() ); ?>"><?php echo esc_html( hs_info( 'phone' ) ); ?></a>
				<a class="hs-btn hs-btn--ghost" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>"><?php esc_html_e( 'All locations', 'hide-and-soul' ); ?></a>
			</div>
		</div>

		<div>
			<iframe
				class="hs-map"
				src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( hs_address_line() ) . '&output=embed' ); ?>"
				loading="lazy"
				referrerpolicy="no-referrer-when-downgrade"
				title="<?php esc_attr_e( 'Map to Hide and Soul Leatherworks', 'hide-and-soul' ); ?>"></iframe>
		</div>
	</div>
</section>

<?php get_footer(); ?>
