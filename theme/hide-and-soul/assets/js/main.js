/**
 * Hide and Soul — front-end behaviour.
 *
 * Deliberately dependency-free: no jQuery, no framework. Everything here
 * degrades to a working site if JS fails to load.
 */
( function () {
	'use strict';

	/* -----------------------------------------------------------------
	 * Mobile navigation
	 * ----------------------------------------------------------------- */

	var toggle = document.querySelector( '.hs-nav-toggle' );
	var nav = document.querySelector( '.hs-nav' );

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = nav.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );

		// Close on Escape and return focus to the button.
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.focus();
			}
		} );

		// Close when a link inside the panel is followed.
		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) ) {
				nav.classList.remove( 'is-open' );
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	}

	/* -----------------------------------------------------------------
	 * Sub-menu toggles for touch devices
	 *
	 * Hover opens sub-menus on desktop; on touch the first tap on a parent
	 * link would otherwise navigate away before the child menu is readable.
	 * ----------------------------------------------------------------- */

	var parents = document.querySelectorAll( '.hs-nav .menu-item-has-children > a' );

	Array.prototype.forEach.call( parents, function ( link ) {
		link.addEventListener( 'click', function ( event ) {
			if ( window.matchMedia( '(min-width: 980px)' ).matches ) {
				return;
			}

			var submenu = link.parentNode.querySelector( '.sub-menu' );

			if ( submenu && ! link.parentNode.classList.contains( 'is-expanded' ) ) {
				event.preventDefault();
				link.parentNode.classList.add( 'is-expanded' );
			}
		} );
	} );

	/* -----------------------------------------------------------------
	 * Reveal-on-scroll for cards and split rows
	 * ----------------------------------------------------------------- */

	var reveals = document.querySelectorAll( '[data-hs-reveal]' );

	if ( reveals.length && 'IntersectionObserver' in window &&
		! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		}, { rootMargin: '0px 0px -60px 0px', threshold: 0.1 } );

		Array.prototype.forEach.call( reveals, function ( el ) {
			el.classList.add( 'hs-reveal' );
			observer.observe( el );
		} );
	}

	/* -----------------------------------------------------------------
	 * Today's hours highlight
	 * ----------------------------------------------------------------- */

	var hoursRows = document.querySelectorAll( '[data-hs-day]' );

	if ( hoursRows.length ) {
		var today = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ][ new Date().getDay() ];

		Array.prototype.forEach.call( hoursRows, function ( row ) {
			if ( row.getAttribute( 'data-hs-day' ) === today ) {
				row.classList.add( 'is-today' );
			}
		} );
	}
}() );
