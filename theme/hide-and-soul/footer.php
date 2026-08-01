<?php
/**
 * Site footer.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;
?>
</main><!-- #hs-main -->

<footer class="hs-footer">
	<div class="hs-wrap">
		<div class="hs-footer__cols">

			<div>
				<h2><?php echo esc_html( hs_info( 'legal_name' ) ); ?></h2>
				<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php hs_social_links(); ?>
			</div>

			<div>
				<h3><?php esc_html_e( 'Visit the shop', 'hide-and-soul' ); ?></h3>
				<p>
					<?php echo esc_html( hs_info( 'street' ) ); ?><br>
					<?php echo esc_html( hs_info( 'city' ) . ', ' . hs_info( 'region' ) . ' ' . hs_info( 'postal' ) ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( hs_phone_href() ); ?>"><?php echo esc_html( hs_info( 'phone' ) ); ?></a><br>
					<a href="mailto:<?php echo esc_attr( hs_info( 'email' ) ); ?>"><?php echo esc_html( hs_info( 'email' ) ); ?></a>
				</p>
			</div>

			<div>
				<h3><?php esc_html_e( 'Hours', 'hide-and-soul' ); ?></h3>
				<?php hs_hours_list(); ?>
				<p style="margin-top:1rem;font-size:0.88rem;"><?php echo esc_html( hs_info( 'hours_note' ) ); ?></p>
			</div>

			<div>
				<h3><?php esc_html_e( 'Explore', 'hide-and-soul' ); ?></h3>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					) );
				} else {
					hs_nav_fallback();
				}
				?>
			</div>

		</div>

		<div class="hs-footer__bottom">
			<p>
				<?php
				printf(
					/* translators: 1: year, 2: business name */
					esc_html__( '© %1$s %2$s. Handmade in South Dakota.', 'hide-and-soul' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( hs_info( 'legal_name' ) )
				);
				?>
			</p>

			<?php
			if ( has_nav_menu( 'legal' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'legal',
					'container'      => false,
					'depth'          => 1,
				) );
			}
			?>
		</div>
	</div>
</footer>

<div class="hs-callbar">
	<a href="<?php echo esc_url( hs_phone_href() ); ?>"><?php esc_html_e( 'Call', 'hide-and-soul' ); ?></a>
	<a href="<?php echo esc_url( 'https://maps.google.com/?q=' . rawurlencode( hs_address_line() ) ); ?>" target="_blank" rel="noopener">
		<?php esc_html_e( 'Directions', 'hide-and-soul' ); ?>
	</a>
</div>

<?php wp_footer(); ?>
</body>
</html>
