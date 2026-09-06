/**
 * VenueStack block variations for the editor inserter.
 */
import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

const hasClass = ( className, token ) =>
	typeof className === 'string' && className.split( /\s+/ ).includes( token );

/**
 * Register paragraph + button variations used across VenueStack templates.
 */
export function registerVenueStackVariations() {
	registerBlockVariation( 'core/paragraph', {
		name: 'venuestack-eyebrow',
		title: __( 'Eyebrow', 'venuestack' ),
		description: __(
			'Uppercase section label — uses the Eyebrow paragraph style.',
			'venuestack'
		),
		category: 'text',
		icon: 'tag',
		keywords: [ 'label', 'kicker', 'overline', 'eyebrow' ],
		attributes: {
			className: 'is-style-eyebrow',
			textColor: 'brass',
			content: '',
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-eyebrow' ),
	} );

	registerBlockVariation( 'core/paragraph', {
		name: 'venuestack-lede',
		title: __( 'Lede', 'venuestack' ),
		description: __(
			'Supporting lead under a heading — Lede paragraph style.',
			'venuestack'
		),
		category: 'text',
		icon: 'editor-paragraph',
		keywords: [ 'lead', 'dek', 'intro', 'lede' ],
		attributes: {
			className: 'is-style-lede',
			content: '',
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-lede' ),
	} );

	registerBlockVariation( 'core/paragraph', {
		name: 'venuestack-body',
		title: __( 'Body', 'venuestack' ),
		description: __(
			'Small supporting copy — Body paragraph style.',
			'venuestack'
		),
		category: 'text',
		icon: 'editor-paragraph',
		keywords: [ 'body', 'caption', 'support' ],
		attributes: {
			className: 'is-style-body',
			textColor: 'muted',
			content: '',
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-body' ),
	} );

	registerBlockVariation( 'core/heading', {
		name: 'venuestack-heading-hero',
		title: __( 'Hero heading', 'venuestack' ),
		description: __(
			'Page-opening display title — Hero heading style.',
			'venuestack'
		),
		category: 'text',
		icon: 'heading',
		keywords: [ 'hero', 'display', 'title', 'h1' ],
		attributes: {
			level: 1,
			className: 'is-style-hero',
			content: '',
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-hero' ),
	} );

	registerBlockVariation( 'core/heading', {
		name: 'venuestack-heading-section',
		title: __( 'Section heading', 'venuestack' ),
		description: __(
			'Section title under an eyebrow — Section heading style.',
			'venuestack'
		),
		category: 'text',
		icon: 'heading',
		keywords: [ 'section', 'title', 'h2' ],
		attributes: {
			level: 2,
			className: 'is-style-section',
			content: '',
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-section' ),
	} );

	registerBlockVariation( 'core/button', {
		name: 'venuestack-button-primary',
		title: __( 'Primary button', 'venuestack' ),
		description: __(
			'Brass fill with plaster text — default VenueStack CTA.',
			'venuestack'
		),
		category: 'design',
		icon: 'button',
		isDefault: true,
		attributes: {
			className: 'is-style-fill',
			backgroundColor: 'brass',
			textColor: 'plaster',
			style: {
				border: {
					radius: '0px',
				},
			},
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			blockAttributes.backgroundColor === 'brass' &&
			! hasClass( blockAttributes.className, 'is-style-outline' ),
	} );

	registerBlockVariation( 'core/button', {
		name: 'venuestack-button-outline',
		title: __( 'Outline button', 'venuestack' ),
		description: __(
			'Plaster outline for dark surfaces (hero, dusk CTA).',
			'venuestack'
		),
		category: 'design',
		icon: 'button',
		attributes: {
			className: 'is-style-outline',
			textColor: 'plaster',
			borderColor: 'plaster',
			style: {
				border: {
					radius: '0px',
					width: '1px',
				},
				color: {
					background: 'transparent',
				},
			},
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-outline' ) &&
			blockAttributes.borderColor === 'plaster',
	} );

	registerBlockVariation( 'core/button', {
		name: 'venuestack-button-outline-brass',
		title: __( 'Outline button (brass)', 'venuestack' ),
		description: __(
			'Brass outline with plaster text — for dusk footers and space heroes.',
			'venuestack'
		),
		category: 'design',
		icon: 'button',
		attributes: {
			className: 'is-style-outline',
			textColor: 'plaster',
			borderColor: 'brass',
			style: {
				border: {
					radius: '0px',
					width: '1px',
				},
				color: {
					background: 'transparent',
				},
			},
		},
		scope: [ 'inserter', 'block', 'transform' ],
		isActive: ( blockAttributes ) =>
			hasClass( blockAttributes.className, 'is-style-outline' ) &&
			blockAttributes.borderColor === 'brass',
	} );
}
