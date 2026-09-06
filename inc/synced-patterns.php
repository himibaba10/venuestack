<?php
/**
 * Synced patterns (theme-owned design system cards).
 *
 * Theme pattern files are unsynced by default. VenueStack seeds matching
 * `wp_block` posts so templates can reference them via `core/block`.
 * Some patterns also use per-instance `content` overrides; others are
 * fully synced (e.g. Query Loop cards with dynamic bindings).
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pattern slug => relative path under /patterns/.
 *
 * @return array<string, string>
 */
function venuestack_synced_pattern_map(): array {
	return array(
		'venuestack/capability-card' => 'capability-card.php',
		'venuestack/space-card'      => 'space-card.php',
		'venuestack/step-card'       => 'step-card.php',
	);
}

/**
 * Human titles for seeded synced patterns.
 *
 * @return array<string, string>
 */
function venuestack_synced_pattern_titles(): array {
	return array(
		'venuestack/capability-card' => __( 'Capability card', 'venuestack' ),
		'venuestack/space-card'      => __( 'Space card', 'venuestack' ),
		'venuestack/step-card'       => __( 'Step card', 'venuestack' ),
	);
}

/**
 * Allow Icon block `icon` attribute in Block Bindings / pattern overrides.
 *
 * @param string[] $attributes Supported attribute names.
 * @return string[]
 */
function venuestack_icon_bindings_supported_attributes( array $attributes ): array {
	if ( ! in_array( 'icon', $attributes, true ) ) {
		$attributes[] = 'icon';
	}

	return $attributes;
}
add_filter( 'block_bindings_supported_attributes_core/icon', 'venuestack_icon_bindings_supported_attributes' );

/**
 * Register VenueStack pattern category.
 */
function venuestack_register_pattern_categories(): void {
	register_block_pattern_category(
		'venuestack',
		array(
			'label'       => __( 'VenueStack', 'venuestack' ),
			'description' => __( 'Reusable VenueStack layout patterns.', 'venuestack' ),
		)
	);
}
add_action( 'init', 'venuestack_register_pattern_categories', 9 );

/**
 * Load raw markup from a theme pattern file.
 */
function venuestack_load_pattern_file_content( string $relative_path ): string {
	$path = get_template_directory() . '/patterns/' . ltrim( $relative_path, '/' );

	if ( ! is_readable( $path ) ) {
		return '';
	}

	ob_start();
	include $path;
	return trim( (string) ob_get_clean() );
}

/**
 * Option key for synced pattern ID map.
 */
function venuestack_synced_pattern_option_key(): string {
	return 'venuestack_synced_pattern_ids';
}

/**
 * Resolve a synced pattern post ID by theme slug.
 */
function venuestack_get_synced_pattern_id( string $slug ): int {
	$map = get_option( venuestack_synced_pattern_option_key(), array() );

	if ( ! empty( $map[ $slug ] ) ) {
		$id = (int) $map[ $slug ];
		if ( $id && 'wp_block' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
			return $id;
		}
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'wp_block',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => '_venuestack_synced_pattern',
			'meta_value'             => $slug,
		)
	);

	$id = ! empty( $query->posts[0] ) ? (int) $query->posts[0] : 0;

	if ( $id ) {
		$map[ $slug ] = $id;
		update_option( venuestack_synced_pattern_option_key(), $map, false );
	}

	return $id;
}

/**
 * Create or refresh theme-owned synced patterns from /patterns files.
 *
 * Theme markup is the source of truth for structure/styles. Instance
 * overrides live on template usages and are preserved.
 */
function venuestack_ensure_synced_patterns(): void {
	$map = get_option( venuestack_synced_pattern_option_key(), array() );

	foreach ( venuestack_synced_pattern_map() as $slug => $relative_path ) {
		$content = venuestack_load_pattern_file_content( $relative_path );

		if ( '' === $content ) {
			continue;
		}

		$hash = md5( $content );
		$id   = venuestack_get_synced_pattern_id( $slug );

		if ( $id ) {
			$stored_hash = (string) get_post_meta( $id, '_venuestack_pattern_hash', true );

			if ( $stored_hash !== $hash ) {
				wp_update_post(
					array(
						'ID'           => $id,
						'post_content' => $content,
					)
				);
				update_post_meta( $id, '_venuestack_pattern_hash', $hash );
			}

			$map[ $slug ] = $id;
			continue;
		}

		$titles = venuestack_synced_pattern_titles();
		$title  = $titles[ $slug ] ?? $slug;

		$id = wp_insert_post(
			array(
				'post_type'    => 'wp_block',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => sanitize_title( str_replace( '/', '-', $slug ) ),
				'post_content' => $content,
				'meta_input'   => array(
					'_venuestack_synced_pattern' => $slug,
					'_venuestack_pattern_hash'   => $hash,
					// Empty string = fully synced (not "unsynced").
					'wp_pattern_sync_status'     => '',
				),
			),
			true
		);

		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}

		$map[ $slug ] = (int) $id;
	}

	update_option( venuestack_synced_pattern_option_key(), $map, false );
}
add_action( 'init', 'venuestack_ensure_synced_patterns', 20 );

/**
 * Walk parsed blocks and inject `ref` for VenueStack synced patterns.
 *
 * @param array[] $blocks Parsed blocks.
 * @return array[]
 */
function venuestack_inject_synced_pattern_refs_into_blocks( array $blocks ): array {
	foreach ( $blocks as &$block ) {
		if ( ( $block['blockName'] ?? '' ) === 'core/block' ) {
			$name = $block['attrs']['metadata']['name'] ?? '';

			if ( is_string( $name ) && isset( venuestack_synced_pattern_map()[ $name ] ) ) {
				$id = venuestack_get_synced_pattern_id( $name );
				if ( $id ) {
					$block['attrs']['ref'] = $id;
				}
			}
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = venuestack_inject_synced_pattern_refs_into_blocks( $block['innerBlocks'] );
		}
	}
	unset( $block );

	return $blocks;
}

/**
 * Rewrite template HTML so synced pattern slugs resolve to local post IDs.
 */
function venuestack_inject_synced_pattern_refs( string $content ): string {
	if ( ! str_contains( $content, 'venuestack/' ) ) {
		return $content;
	}

	$blocks = parse_blocks( $content );
	$blocks = venuestack_inject_synced_pattern_refs_into_blocks( $blocks );

	return serialize_blocks( $blocks );
}

/**
 * @param WP_Block_Template|null $template Template object.
 * @return WP_Block_Template|null
 */
function venuestack_filter_block_template_refs( $template ) {
	if ( $template instanceof WP_Block_Template && ! empty( $template->content ) ) {
		$template->content = venuestack_inject_synced_pattern_refs( $template->content );
	}

	return $template;
}
add_filter( 'get_block_file_template', 'venuestack_filter_block_template_refs' );
add_filter( 'get_block_template', 'venuestack_filter_block_template_refs' );

/**
 * @param WP_Block_Template[] $templates Templates.
 * @return WP_Block_Template[]
 */
function venuestack_filter_block_templates_refs( array $templates ): array {
	foreach ( $templates as $template ) {
		venuestack_filter_block_template_refs( $template );
	}

	return $templates;
}
add_filter( 'get_block_templates', 'venuestack_filter_block_templates_refs' );

/**
 * Resolve slug → ref at render time (covers content that bypassed template filters).
 *
 * @param array $parsed_block Parsed block.
 * @return array
 */
function venuestack_resolve_synced_pattern_render_block_data( array $parsed_block ): array {
	if ( ( $parsed_block['blockName'] ?? '' ) !== 'core/block' ) {
		return $parsed_block;
	}

	$name = $parsed_block['attrs']['metadata']['name'] ?? '';

	if ( ! is_string( $name ) || ! isset( venuestack_synced_pattern_map()[ $name ] ) ) {
		return $parsed_block;
	}

	$id = venuestack_get_synced_pattern_id( $name );
	if ( $id ) {
		$parsed_block['attrs']['ref'] = $id;
	}

	return $parsed_block;
}
add_filter( 'render_block_data', 'venuestack_resolve_synced_pattern_render_block_data' );
