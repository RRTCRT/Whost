<?php
/**
 * Static page.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title(), get_post_meta( get_the_ID(), '_hs_subtitle', true ) );
	hs_breadcrumbs();
	?>

	<article <?php post_class( 'hs-entry' ); ?>>
		<div class="hs-wrap">
			<div class="hs-entry__content">
				<?php
				the_content();

				wp_link_pages( array(
					'before' => '<nav class="hs-pagination">',
					'after'  => '</nav>',
				) );
				?>
			</div>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="hs-wrap">';
		comments_template();
		echo '</div>';
	}

endwhile;

get_footer();
