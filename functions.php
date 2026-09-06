<?php
/**
 * VenueStack theme functions.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

define( 'VENUESTACK_VERSION', wp_get_theme()->get( 'Version' ) ?: '0.1.0' );

/**
 * Enqueue theme styles and built assets.
 */
function venuestack_enqueue_assets(): void {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'venuestack-style',
		get_stylesheet_uri(),
		array(),
		VENUESTACK_VERSION
	);

	wp_enqueue_style(
		'venuestack-header-footer',
		$theme_uri . '/assets/css/header-footer.css',
		array( 'venuestack-style' ),
		VENUESTACK_VERSION
	);

	wp_enqueue_style(
		'venuestack-interactive',
		$theme_uri . '/assets/css/interactive.css',
		array( 'venuestack-header-footer' ),
		VENUESTACK_VERSION
	);

	if ( is_singular( 'venue_space' ) ) {
		wp_enqueue_style(
			'venuestack-single-space',
			$theme_uri . '/assets/css/single-space.css',
			array( 'venuestack-style' ),
			VENUESTACK_VERSION
		);
	}

	$asset_file = get_template_directory() . '/build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'venuestack-theme',
		$theme_uri . '/build/index.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? VENUESTACK_VERSION,
		true
	);

	$style_path = get_template_directory() . '/build/index.css';
	if ( file_exists( $style_path ) ) {
		wp_enqueue_style(
			'venuestack-theme',
			$theme_uri . '/build/index.css',
			array( 'venuestack-style' ),
			$asset['version'] ?? VENUESTACK_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'venuestack_enqueue_assets' );
