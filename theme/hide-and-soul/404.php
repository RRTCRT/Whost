<?php
/**
 * 404.
 *
 * The Wix site had a deep URL structure, so some old inbound links will land
 * here even with redirects in place. Point people at the shop and the phone
 * rather than a dead end.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

get_header();

hs_page_header( __( 'That page has moved on', 'hide-and-soul' ) );
?>

<div class="hs-section">
	<div class="hs-wrap hs-entry__content">
		<p><?php esc_html_e( 'We rebuilt this site recently and a few old links did not survive the move. Here is where most people were headed:', 'hide-and-soul' ); ?></p>

		<ul>
			<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'The shop', 'hide-and-soul' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/custom-orders/' ) ); ?>"><?php esc_html_e( 'Custom orders', 'hide-and-soul' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/repairs-patches/' ) ); ?>"><?php esc_html_e( 'Repairs & patches', 'hide-and-soul' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/locations/' ) ); ?>"><?php esc_html_e( 'Locations & hours', 'hide-and-soul' ); ?></a></li>
		</ul>

		<?php get_search_form(); ?>

		<p style="margin-top:2rem;">
			<a class="hs-btn" href="<?php echo esc_url( hs_phone_href() ); ?>">
				<?php echo esc_html( sprintf( __( 'Call us: %s', 'hide-and-soul' ), hs_info( 'phone' ) ) ); ?>
			</a>
		</p>
	</div>
</div>

<?php get_footer(); ?>
