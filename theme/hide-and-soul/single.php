<?php
/**
 * Single post.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title() );
	hs_breadcrumbs();
	?>

	<article <?php post_class( 'hs-entry' ); ?>>
		<div class="hs-wrap">
			<div class="hs-entry__content">
				<p class="hs-entry__meta">
					<?php echo esc_html( get_the_date() ); ?>
					<?php
					$cats = get_the_category_list( ', ' );
					if ( $cats ) {
						echo ' &middot; ' . wp_kses_post( $cats );
					}
					?>
				</p>

				<?php the_content(); ?>
			</div>
		</div>
	</article>

	<?php
	echo '<div class="hs-wrap">';
	the_post_navigation( array(
		'prev_text' => '&larr; %title',
		'next_text' => '%title &rarr;',
	) );

	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
	echo '</div>';

endwhile;

get_footer();
