<?php
/**
 * VenueStack theme functions.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

define( 'VENUESTACK_VERSION', wp_get_theme()->get( 'Version' ) ?: '0.1.0' );

/**
 * Enqueue built theme assets when present.
 */
function venuestack_enqueue_assets(): void {
	$asset_file = get_template_directory() . '/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'venuestack-theme',
		get_template_directory_uri() . '/build/index.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? VENUESTACK_VERSION,
		true
	);

	$style_path = get_template_directory() . '/build/index.css';
	if ( file_exists( $style_path ) ) {
		wp_enqueue_style(
			'venuestack-theme',
			get_template_directory_uri() . '/build/index.css',
			array(),
			$asset['version'] ?? VENUESTACK_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'venuestack_enqueue_assets' );
