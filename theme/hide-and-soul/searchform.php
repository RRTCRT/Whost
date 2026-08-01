<?php
/**
 * Search form.
 *
 * @package HideAndSoul
 */

defined( 'ABSPATH' ) || exit;

$hs_search_id = 'hs-search-' . wp_unique_id();
?>
<form role="search" method="get" class="hs-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $hs_search_id ); ?>"><?php esc_html_e( 'Search the site', 'hide-and-soul' ); ?></label>
	<div style="display:flex;gap:0.5rem;">
		<input
			id="<?php echo esc_attr( $hs_search_id ); ?>"
			type="search"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Vests, chaps, repairs…', 'hide-and-soul' ); ?>">
		<button class="hs-btn" type="submit"><?php esc_html_e( 'Search', 'hide-and-soul' ); ?></button>
	</div>
</form>
