<?php
/**
 * Temporary Site Editor / Gutenberg false-positive workarounds.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

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
