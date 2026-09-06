<?php
/**
 * Register VenueStack icons for the core Icon block (WP 7.1 Icons API).
 *
 * Icons are Lucide outlines converted to fill-rule evenodd paths. WP sanitizes
 * registered SVGs and strips stroke attributes, so stroke-only Lucide files
 * cannot be registered as-is.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the theme icon collection and homepage capability icons.
 */
function venuestack_register_icons(): void {
	if ( ! function_exists( 'wp_register_icon_collection' ) || ! function_exists( 'wp_register_icon' ) ) {
		return;
	}

	wp_register_icon_collection(
		'venuestack',
		array(
			'label'       => __( 'VenueStack', 'venuestack' ),
			'description' => __( 'Icons for VenueStack marketing and booking UI.', 'venuestack' ),
		)
	);

	$icons_dir = get_template_directory() . '/assets/icons/';

	$icons = array(
		'layout-grid'    => __( 'Layout grid', 'venuestack' ),
		'calendar-check' => __( 'Calendar check', 'venuestack' ),
		'shield-check'   => __( 'Shield check', 'venuestack' ),
		'utensils'       => __( 'Utensils', 'venuestack' ),
	);

	foreach ( $icons as $slug => $label ) {
		$file = $icons_dir . $slug . '.svg';
		if ( ! is_readable( $file ) ) {
			continue;
		}

		wp_register_icon(
			'venuestack/' . $slug,
			array(
				'label'     => $label,
				'file_path' => $file,
			)
		);
	}
}
add_action( 'init', 'venuestack_register_icons' );
