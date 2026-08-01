<?php
/**
 * Site header.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="hs-skip-link" href="#hs-main"><?php esc_html_e( 'Skip to content', 'hide-and-soul' ); ?></a>

<div class="hs-utility">
	<div class="hs-wrap">
		<span><?php echo esc_html( hs_info( 'season_note' ) ); ?></span>
		<a href="<?php echo esc_url( hs_phone_href() ); ?>"><?php echo esc_html( hs_info( 'phone' ) ); ?></a>
	</div>
</div>

<header class="hs-header">
	<div class="hs-wrap hs-header__inner">
		<?php hs_brand(); ?>

		<button class="hs-nav-toggle" aria-expanded="false" aria-controls="hs-primary-nav">
			<span class="hs-nav-toggle__bars" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Menu', 'hide-and-soul' ); ?></span>
		</button>

		<nav id="hs-primary-nav" class="hs-nav" aria-label="<?php esc_attr_e( 'Primary', 'hide-and-soul' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => 'hs_nav_fallback',
			) );
			?>
		</nav>

		<div class="hs-header__actions">
			<?php if ( function_exists( 'wc_get_cart_url' ) && ! hs_is_catalog_mode() ) : ?>
				<a class="hs-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php esc_html_e( 'Cart', 'hide-and-soul' ); ?>
					<span class="hs-cart-link__count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
				</a>
			<?php endif; ?>

			<a class="hs-btn hs-btn--outline" href="<?php echo esc_url( hs_phone_href() ); ?>">
				<?php esc_html_e( 'Call the shop', 'hide-and-soul' ); ?>
			</a>
		</div>
	</div>
</header>

<main id="hs-main" class="hs-main">
