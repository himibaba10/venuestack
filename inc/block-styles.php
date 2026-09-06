<?php
/**
 * Block style variations (Styles panel).
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register paragraph block styles for consistent marketing copy.
 */
function venuestack_register_block_styles(): void {
	$paragraph_styles = array(
		'eyebrow' => __( 'Eyebrow', 'venuestack' ),
		'lede'    => __( 'Lede', 'venuestack' ),
		'body'    => __( 'Body', 'venuestack' ),
		'label'   => __( 'Label', 'venuestack' ),
		'display' => __( 'Display', 'venuestack' ),
	);

	foreach ( $paragraph_styles as $name => $label ) {
		register_block_style(
			'core/paragraph',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'venuestack_register_block_styles' );
