<?php
/**
 * Post card used in archives and the blog index.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'hs-card' ); ?> data-hs-reveal>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="hs-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'hs-card', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>

	<div class="hs-card__body">
		<h3><a href="<?php the_permalink(); ?>" style="text-decoration:none;color:inherit;"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<a class="hs-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'hide-and-soul' ); ?></a>
	</div>
</article>
