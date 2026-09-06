/**
 * Homepage scroll reveals via motion (architecture.md).
 */
import { animate, inView } from 'motion';

const EASE = [ 0.16, 0.84, 0.24, 1 ];

/**
 * Reveal marketing sections when they enter the viewport.
 */
export function initHomeReveals() {
	const nodes = document.querySelectorAll( '.venuestack-home-reveal' );
	if ( ! nodes.length ) {
		return;
	}

	const reduce = window.matchMedia(
		'(prefers-reduced-motion: reduce)'
	).matches;
	if ( reduce ) {
		nodes.forEach( ( el ) => {
			el.style.opacity = '1';
			el.style.transform = 'none';
		} );
		return;
	}

	document.documentElement.classList.add( 'venuestack-reveal-enabled' );

	nodes.forEach( ( el, index ) => {
		const delayAttr = el.getAttribute( 'data-reveal-delay' );
		const delay = delayAttr
			? Number( delayAttr ) / 1000
			: Math.min( index * 0.06, 0.3 );

		inView(
			el,
			() => {
				animate(
					el,
					{ opacity: [ 0, 1 ], y: [ 28, 0 ] },
					{ duration: 0.9, delay, ease: EASE }
				);
			},
			{ amount: 0.15 }
		);
	} );
}
