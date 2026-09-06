/**
 * Client-side Block Bindings for locked homepage stats.
 *
 * PHP registers the source for frontend render; the editor needs this
 * getValues() implementation or it shows the source label as a placeholder.
 */
import { registerBlockBindingsSource } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

const stats =
	typeof window !== 'undefined' && window.venuestackHomeStats
		? window.venuestackHomeStats
		: {};

registerBlockBindingsSource( {
	name: 'venuestack/home-stat',
	label: __( 'Homepage stat', 'venuestack' ),
	getValues( { bindings } ) {
		const values = {};

		for ( const [ attributeName, binding ] of Object.entries( bindings ) ) {
			const key = binding?.args?.key;
			values[ attributeName ] =
				typeof key === 'string' && key in stats
					? String( stats[ key ] )
					: '';
		}

		return values;
	},
	canUserEditValue() {
		return false;
	},
} );
