/**
 * VenueStack theme — frontend entry (motion).
 *
 * Icons use core/icon + WP 7.1 Icons API (see inc/icons.php).
 * Editor variations live in src/editor.js (eyebrow + buttons).
 * FormKit auto-animate is installed for the Available Rooms grid
 * (directory filtering) — wired when that Interactivity surface ships.
 */
import { initHomeReveals } from './frontend/home-reveals';

const boot = () => {
	if (
		document.body?.classList.contains( 'home' ) ||
		document.querySelector( '.venuestack-home' ) ||
		document.querySelector( '.venuestack-space' )
	) {
		initHomeReveals();
	}
};

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', boot, { once: true } );
} else {
	boot();
}
