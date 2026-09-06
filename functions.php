<?php
/**
 * VenueStack theme functions.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

define( 'VENUESTACK_VERSION', wp_get_theme()->get( 'Version' ) ?: '0.1.0' );

require_once get_template_directory() . '/inc/icons.php';

/**
 * Silence the Site Editor false-positive for global CSS custom properties.
 *
 * Core still hooks wp_enqueue_global_styles_css_custom_properties on
 * enqueue_block_editor_assets; Gutenberg's iframe compat layer then warns
 * because the inline CSS contains ".wp-block". Re-hook onto enqueue_block_assets
 * (editor only) until WordPress fixes this upstream.
 *
 * @see https://github.com/WordPress/gutenberg/issues/76082
 */
function venuestack_fix_global_styles_css_custom_properties_hook(): void {
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_global_styles_css_custom_properties' );

	add_action(
		'enqueue_block_assets',
		static function (): void {
			if ( ! is_admin() ) {
				return;
			}

			wp_enqueue_global_styles_css_custom_properties();
		}
	);
}
add_action( 'after_setup_theme', 'venuestack_fix_global_styles_css_custom_properties_hook' );

/**
 * Silence the Site Editor false-positive for WooCommerce block theme CSS.
 *
 * WooCommerce correctly uses enqueue_block_assets, but Gutenberg's iframe
 * compat layer still warns for any non-`wp-` style handle that contains
 * ".wp-block" selectors. Re-enqueue under a wp- prefixed handle.
 *
 * @see https://github.com/woocommerce/woocommerce/issues/62028
 */
function venuestack_fix_woocommerce_blocktheme_style_handle(): void {
	if ( ! wp_style_is( 'woocommerce-blocktheme', 'enqueued' ) ) {
		return;
	}

	$styles = wp_styles();
	$style  = $styles->registered['woocommerce-blocktheme'] ?? null;

	if ( ! $style ) {
		return;
	}

	wp_dequeue_style( 'woocommerce-blocktheme' );
	wp_deregister_style( 'woocommerce-blocktheme' );

	wp_enqueue_style(
		'wp-woocommerce-blocktheme',
		$style->src,
		$style->deps,
		$style->ver,
		$style->args
	);

	if ( ! empty( $style->extra['rtl'] ) ) {
		wp_style_add_data( 'wp-woocommerce-blocktheme', 'rtl', $style->extra['rtl'] );
	}
}
add_action( 'enqueue_block_assets', 'venuestack_fix_woocommerce_blocktheme_style_handle', 20 );

/**
 * Styles for frontend + Site Editor canvas.
 *
 * enqueue_block_assets runs in both contexts. Template CSS was previously
 * gated on is_front_page() / is_singular(), which are false in the editor —
 * so the canvas rendered unstyled markup.
 */
function venuestack_enqueue_block_assets(): void {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'venuestack-header-footer',
		$theme_uri . '/assets/css/header-footer.css',
		array(),
		VENUESTACK_VERSION
	);

	wp_enqueue_style(
		'venuestack-interactive',
		$theme_uri . '/assets/css/interactive.css',
		array( 'venuestack-header-footer' ),
		VENUESTACK_VERSION
	);

	// Scoped to .venuestack-home — safe in editor and on other templates.
	wp_enqueue_style(
		'venuestack-home',
		$theme_uri . '/assets/css/home.css',
		array( 'venuestack-interactive' ),
		VENUESTACK_VERSION
	);

	// Scoped to .venuestack-space / .single-venue_space.
	wp_enqueue_style(
		'venuestack-single-space',
		$theme_uri . '/assets/css/single-space.css',
		array( 'venuestack-interactive' ),
		VENUESTACK_VERSION
	);
}
add_action( 'enqueue_block_assets', 'venuestack_enqueue_block_assets' );

/**
 * Block editor scripts (variations for eyebrow + buttons).
 */
function venuestack_enqueue_block_editor_assets(): void {
	$asset_file = get_template_directory() . '/build/editor.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'venuestack-editor',
		get_template_directory_uri() . '/build/editor.js',
		$asset['dependencies'] ?? array(),
		$asset['version'] ?? VENUESTACK_VERSION,
		true
	);

	wp_set_script_translations( 'venuestack-editor', 'venuestack' );
}
add_action( 'enqueue_block_editor_assets', 'venuestack_enqueue_block_editor_assets' );

/**
 * Front-end only scripts (and optional built assets).
 */
function venuestack_enqueue_assets(): void {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'venuestack-style',
		get_stylesheet_uri(),
		array(),
		VENUESTACK_VERSION
	);

	// Homepage marketing: motion scroll reveals (architecture.md).
	if ( ! is_front_page() ) {
		return;
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
