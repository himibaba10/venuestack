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
 * Always load shared chrome. Page-specific CSS loads in the editor always,
 * but only on matching templates on the front end.
 */
function venuestack_enqueue_block_assets(): void
{
	$theme_uri = get_template_directory_uri();
	$in_editor = is_admin()
		|| ( defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST );

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

	$load_home = $in_editor
		|| is_front_page()
		|| is_post_type_archive( 'venue_space' );
	$load_single = $in_editor || is_singular( 'venue_space' );
	$load_directory = $in_editor || is_post_type_archive( 'venue_space' );
	$load_contact = $in_editor || is_page( 'contact' );

	if ( $load_home ) {
		wp_enqueue_style(
			'venuestack-home',
			$theme_uri . '/assets/css/home.css',
			array( 'venuestack-interactive' ),
			VENUESTACK_VERSION
		);
	}

	if ( $load_single ) {
		wp_enqueue_style(
			'venuestack-single-space',
			$theme_uri . '/assets/css/single-space.css',
			array( 'venuestack-interactive' ),
			VENUESTACK_VERSION
		);
	}

	if ( $load_directory ) {
		wp_enqueue_style(
			'venuestack-directory',
			$theme_uri . '/assets/css/directory.css',
			array( 'venuestack-interactive', 'venuestack-home' ),
			VENUESTACK_VERSION
		);
	}

	if ( $load_contact ) {
		wp_enqueue_style(
			'venuestack-contact',
			$theme_uri . '/assets/css/contact.css',
			array( 'venuestack-interactive' ),
			VENUESTACK_VERSION
		);
	}
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

/**
 * Drop WP emoji detection/polyfill scripts and related styles.
 *
 * Modern browsers render emoji natively; this script is unused weight.
 */
function venuestack_disable_emojis(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'venuestack_disable_emojis' );

/**
 * Strip unused front-end head tags (tiny, but free).
 */
function venuestack_trim_wp_head(): void {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );
}
add_action( 'init', 'venuestack_trim_wp_head' );
