<?php
/**
 * Block style variations (Styles panel).
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register paragraph + heading block styles for marketing copy.
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

	$heading_styles = array(
		'hero'    => __( 'Hero', 'venuestack' ),
		'section' => __( 'Section', 'venuestack' ),
		'card'    => __( 'Card', 'venuestack' ),
	);

	foreach ( $heading_styles as $name => $label ) {
		register_block_style(
			'core/heading',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}

	register_block_style(
		'core/post-title',
		array(
			'name'  => 'card',
			'label' => __( 'Card', 'venuestack' ),
		)
	);

	register_block_style(
		'core/post-title',
		array(
			'name'  => 'hero',
			'label' => __( 'Hero', 'venuestack' ),
		)
	);

	register_block_style(
		'core/post-excerpt',
		array(
			'name'  => 'lede',
			'label' => __( 'Lede', 'venuestack' ),
		)
	);

	register_block_style(
		'core/post-terms',
		array(
			'name'  => 'eyebrow',
			'label' => __( 'Eyebrow', 'venuestack' ),
		)
	);

	register_block_style(
		'core/post-terms',
		array(
			'name'  => 'chips',
			'label' => __( 'Chips', 'venuestack' ),
		)
	);
}
add_action( 'init', 'venuestack_register_block_styles' );
