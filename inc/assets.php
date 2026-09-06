<?php
/**
 * Theme styles and scripts.
 *
 * @package Venuestack
 */

defined('ABSPATH') || exit;

/**
 * Styles for frontend + Site Editor canvas.
 *
 * enqueue_block_assets runs in both contexts. Template CSS was previously
 * gated on is_front_page() / is_singular(), which are false in the editor —
 * so the canvas rendered unstyled markup.
 */
function venuestack_enqueue_block_assets(): void
{
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
		array('venuestack-header-footer'),
		VENUESTACK_VERSION
	);

	// Scoped to .venuestack-home — safe in editor and on other templates.
	wp_enqueue_style(
		'venuestack-home',
		$theme_uri . '/assets/css/home.css',
		array('venuestack-interactive'),
		VENUESTACK_VERSION
	);

	// Scoped to .venuestack-space / .single-venue_space.
	wp_enqueue_style(
		'venuestack-single-space',
		$theme_uri . '/assets/css/single-space.css',
		array('venuestack-interactive'),
		VENUESTACK_VERSION
	);

	// Scoped to .venuestack-directory / archive-venue_space.
	wp_enqueue_style(
		'venuestack-directory',
		$theme_uri . '/assets/css/directory.css',
		array( 'venuestack-interactive', 'venuestack-home' ),
		VENUESTACK_VERSION
	);
}
add_action('enqueue_block_assets', 'venuestack_enqueue_block_assets');

/**
 * Block editor scripts (variations for eyebrow + buttons).
 */
function venuestack_enqueue_block_editor_assets(): void
{
	$asset_file = get_template_directory() . '/build/editor.asset.php';

	if (!file_exists($asset_file)) {
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

	wp_add_inline_script(
		'venuestack-editor',
		'window.venuestackHomeStats = ' . wp_json_encode(
			array(
				'spaces'      => venuestack_get_home_stat_display( 'spaces' ),
				'max_guests'  => venuestack_get_home_stat_display( 'max_guests' ),
				'hold'        => venuestack_get_home_stat_display( 'hold' ),
				'space_types' => venuestack_get_home_stat_display( 'space_types' ),
			)
		) . ';',
		'before'
	);

	wp_set_script_translations( 'venuestack-editor', 'venuestack' );
}
add_action('enqueue_block_editor_assets', 'venuestack_enqueue_block_editor_assets');

/**
 * Front-end only scripts (and optional built assets).
 */
function venuestack_enqueue_assets(): void
{
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'venuestack-style',
		get_stylesheet_uri(),
		array(),
		VENUESTACK_VERSION
	);

	// Marketing motion: homepage, single space, spaces directory.
	if (
		! is_front_page() &&
		! is_singular( 'venue_space' ) &&
		! is_post_type_archive( 'venue_space' )
	) {
		return;
	}

	$asset_file = get_template_directory() . '/build/index.asset.php';

	if (!file_exists($asset_file)) {
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
	if (file_exists($style_path)) {
		wp_enqueue_style(
			'venuestack-theme',
			$theme_uri . '/build/index.css',
			array('venuestack-style'),
			$asset['version'] ?? VENUESTACK_VERSION
		);
	}
}
add_action('wp_enqueue_scripts', 'venuestack_enqueue_assets');
