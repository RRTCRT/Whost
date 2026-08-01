<?php
/**
 * Fallback template — blog index and any archive without a more specific file.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_front_page() ) {
	hs_page_header( get_the_title( get_option( 'page_for_posts' ) ) );
} elseif ( is_search() ) {
	hs_page_header(
		/* translators: %s: search term */
		sprintf( __( 'Search: %s', 'hide-and-soul' ), get_search_query() )
	);
} else {
	hs_page_header();
}

hs_breadcrumbs();
?>

<div class="hs-section">
	<div class="hs-wrap">
		<?php if ( have_posts() ) : ?>
			<div class="hs-grid hs-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card' );
				endwhile;
				?>
			</div>

			<?php hs_pagination(); ?>

		<?php else : ?>
			<div class="hs-entry__content">
				<p><?php esc_html_e( 'Nothing found here. Try a different search, or give the shop a call — we can usually answer faster than the website can.', 'hide-and-soul' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
