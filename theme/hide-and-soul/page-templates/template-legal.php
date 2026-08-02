<?php
/**
 * Template Name: Legal
 *
 * Shared by the Privacy Policy and Terms pages. Renders the page content with
 * a generated table of contents and a visible "last updated" date — legal
 * pages are judged partly on whether they look maintained, and a stale one is
 * worse than none.
 *
 * The date comes from the post's modified time, so it updates itself whenever
 * the page is edited. Nobody has to remember.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	hs_page_header( get_the_title() );
	hs_breadcrumbs();

	$hs_content = apply_filters( 'the_content', get_the_content() );

	// Build a contents list from the h2s, and give each one an id to link to.
	$hs_headings = array();
	if ( preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', $hs_content, $hs_matches ) ) {
		foreach ( $hs_matches[1] as $hs_index => $hs_raw ) {
			$hs_text = trim( wp_strip_all_tags( $hs_raw ) );
			if ( '' === $hs_text ) {
				continue;
			}

			$hs_slug = sanitize_title( $hs_text );
			$hs_headings[] = array( 'slug' => $hs_slug, 'text' => $hs_text );

			$hs_content = str_replace(
				$hs_matches[0][ $hs_index ],
				'<h2 id="' . esc_attr( $hs_slug ) . '">' . $hs_raw . '</h2>',
				$hs_content
			);
		}
	}
	?>

	<article <?php post_class( 'hs-section' ); ?>>
		<div class="hs-wrap">

			<p class="hs-entry__meta" style="text-align:center;">
				<?php
				printf(
					/* translators: %s: date the page was last edited */
					esc_html__( 'Last updated %s', 'hide-and-soul' ),
					esc_html( get_the_modified_date() )
				);
				?>
			</p>

			<?php if ( count( $hs_headings ) > 2 ) : ?>
				<nav class="hs-toc" aria-label="<?php esc_attr_e( 'Contents', 'hide-and-soul' ); ?>">
					<h2><?php esc_html_e( 'Contents', 'hide-and-soul' ); ?></h2>
					<ol>
						<?php foreach ( $hs_headings as $hs_heading ) : ?>
							<li>
								<a href="#<?php echo esc_attr( $hs_heading['slug'] ); ?>">
									<?php echo esc_html( $hs_heading['text'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>
			<?php endif; ?>

			<div class="hs-entry__content hs-legal">
				<?php echo wp_kses_post( $hs_content ); ?>
			</div>

			<div class="hs-callout" style="margin-top:2.5rem;">
				<p><?php esc_html_e( 'Questions about any of this? Ask us — we would rather explain it than have you guess.', 'hide-and-soul' ); ?></p>
				<a class="hs-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php esc_html_e( 'Get in touch', 'hide-and-soul' ); ?>
				</a>
			</div>

		</div>
	</article>

	<?php
endwhile;

get_footer();
