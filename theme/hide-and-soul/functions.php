<?php
/**
 * Hide and Soul theme bootstrap.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

define( 'HS_VERSION', '1.0.0' );
define( 'HS_DIR', get_template_directory() );
define( 'HS_URI', get_template_directory_uri() );

require_once HS_DIR . '/inc/setup.php';
require_once HS_DIR . '/inc/assets.php';
require_once HS_DIR . '/inc/business-info.php';
require_once HS_DIR . '/inc/customizer.php';
require_once HS_DIR . '/inc/black-hills-guide.php';
require_once HS_DIR . '/inc/videos.php';
require_once HS_DIR . '/inc/testimonials.php';
require_once HS_DIR . '/inc/template-tags.php';
require_once HS_DIR . '/inc/nav-walker.php';
require_once HS_DIR . '/inc/schema.php';
require_once HS_DIR . '/inc/performance.php';

if ( class_exists( 'WooCommerce' ) ) {
	require_once HS_DIR . '/inc/woocommerce.php';
}
