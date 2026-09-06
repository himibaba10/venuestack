<?php
/**
 * Homepage hero media: seed bundled photo into the Media Library.
 *
 * The theme JPG is seed-only (never referenced as Cover `url`/`src`).
 * Templates mark the Cover with `venuestack-home-hero`; runtime injection
 * supplies attachment `id`, uploads URL, alt, and background `<img>` markup.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Relative path to the bundled hero image under the theme.
 */
function venuestack_home_hero_relative_path(): string {
	return 'assets/images/home/hero-hall.jpg';
}

/**
 * Option key for the seeded home hero attachment ID.
 */
function venuestack_home_hero_option_key(): string {
	return 'venuestack_home_hero_id';
}

/**
 * Option key for the bundled hero file hash.
 */
function venuestack_home_hero_hash_option_key(): string {
	return 'venuestack_home_hero_hash';
}

/**
 * Attachment meta flag for the theme-owned home hero.
 */
function venuestack_home_hero_meta_key(): string {
	return '_venuestack_home_hero';
}

/**
 * Default alt text for the home hero.
 */
function venuestack_home_hero_alt(): string {
	return __( 'Banquet hall with gold chairs and chandeliers', 'venuestack' );
}

/**
 * Resolve the seeded home hero attachment ID.
 */
function venuestack_get_home_hero_id(): int {
	$id = (int) get_option( venuestack_home_hero_option_key(), 0 );

	if ( $id && wp_attachment_is_image( $id ) ) {
		return $id;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_key'               => venuestack_home_hero_meta_key(),
			'meta_value'             => '1',
		)
	);

	$id = ! empty( $query->posts[0] ) ? (int) $query->posts[0] : 0;

	if ( $id ) {
		update_option( venuestack_home_hero_option_key(), $id, false );
	}

	return $id;
}

/**
 * Create or refresh the Media Library attachment for the homepage hero.
 */
function venuestack_ensure_home_hero_image(): void {
	$path = get_template_directory() . '/' . venuestack_home_hero_relative_path();

	if ( ! is_readable( $path ) ) {
		return;
	}

	$hash   = md5_file( $path );
	$id     = venuestack_get_home_hero_id();
	$stored = (string) get_option( venuestack_home_hero_hash_option_key(), '' );

	if ( $id && wp_attachment_is_image( $id ) && $stored === $hash ) {
		return;
	}

	if ( ! function_exists( 'wp_upload_bits' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}
	if ( ! function_exists( 'media_handle_sideload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
	}

	$bits = wp_upload_bits(
		'venuestack-home-hero.jpg',
		null,
		file_get_contents( $path ) // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	);

	if ( ! empty( $bits['error'] ) || empty( $bits['file'] ) ) {
		return;
	}

	$filetype = wp_check_filetype( $bits['file'], null );
	$attach   = array(
		'post_mime_type' => $filetype['type'] ?? 'image/jpeg',
		'post_title'     => __( 'Home hero', 'venuestack' ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	if ( $id && wp_attachment_is_image( $id ) ) {
		$attach['ID']    = $id;
		$attachment_id = wp_update_post( $attach, true );
		if ( is_wp_error( $attachment_id ) ) {
			return;
		}
		update_attached_file( $id, $bits['file'] );
		$attachment_id = $id;
	} else {
		$attachment_id = wp_insert_attachment( $attach, $bits['file'] );
		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return;
		}
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $bits['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );
	update_post_meta( $attachment_id, venuestack_home_hero_meta_key(), '1' );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', venuestack_home_hero_alt() );

	update_option( venuestack_home_hero_option_key(), (int) $attachment_id, false );
	update_option( venuestack_home_hero_hash_option_key(), $hash, false );
}
add_action( 'init', 'venuestack_ensure_home_hero_image', 5 );

/**
 * Whether a Cover should receive the theme-seeded home hero.
 *
 * Skips Covers the site owner already pointed at a different attachment.
 *
 * @param array $attrs Cover block attributes.
 */
function venuestack_cover_should_use_seeded_home_hero( array $attrs ): bool {
	$class = $attrs['className'] ?? '';

	if ( ! is_string( $class ) || ! str_contains( $class, 'venuestack-home-hero' ) ) {
		return false;
	}

	$theme_hero_id = venuestack_get_home_hero_id();
	if ( ! $theme_hero_id ) {
		return false;
	}

	$current_id = (int) ( $attrs['id'] ?? 0 );

	if ( $current_id && $current_id !== $theme_hero_id && wp_attachment_is_image( $current_id ) ) {
		return false;
	}

	return true;
}

/**
 * Build Cover background <img> markup for the seeded home hero.
 *
 * Cover's save() adds `wp-image-{id}` when an attachment id is set; injected
 * markup must match or the Site Editor reports a validation error.
 *
 * @param string $url           Attachment URL.
 * @param string $alt           Alt text.
 * @param int    $attachment_id Attachment ID.
 */
function venuestack_home_hero_cover_image_markup( string $url, string $alt, int $attachment_id ): string {
	$class = 'wp-block-cover__image-background';

	if ( $attachment_id > 0 ) {
		$class .= ' wp-image-' . $attachment_id;
	}

	return sprintf(
		'<img class="%s" alt="%s" src="%s" data-object-fit="cover"/>',
		esc_attr( $class ),
		esc_attr( $alt ),
		esc_url( $url )
	);
}

/**
 * Ensure Cover inner markup uses the seeded Media Library background image.
 *
 * Inserts the background <img> when the theme template ships without one
 * (seed-only JPG; no theme-path URL in block markup).
 *
 * @param string $html           Cover inner HTML chunk.
 * @param string $url            Attachment URL.
 * @param string $alt            Alt text.
 * @param int    $attachment_id  Attachment ID (0 skips class rewrite).
 */
function venuestack_rewrite_cover_hero_image_html( string $html, string $url, string $alt, int $attachment_id = 0 ): string {
	$img = venuestack_home_hero_cover_image_markup( $url, $alt, $attachment_id );

	if ( ! str_contains( $html, 'wp-block-cover__image-background' ) ) {
		$inserted = preg_replace(
			'/(<div\b[^>]*\bwp-block-cover\b[^>]*>)/',
			'$1' . $img,
			$html,
			1
		);

		return is_string( $inserted ) ? $inserted : $html;
	}

	if ( $attachment_id > 0 ) {
		$rewritten = preg_replace_callback(
			'/(<img\b[^>]*\bclass=")([^"]*\bwp-block-cover__image-background\b[^"]*)(")/',
			static function ( array $matches ) use ( $attachment_id ): string {
				$class = preg_replace( '/\bwp-image-\d+\b/', '', $matches[2] );
				$class = trim( (string) preg_replace( '/\s+/', ' ', (string) $class ) );
				$class = $class . ' wp-image-' . $attachment_id;

				return $matches[1] . $class . $matches[3];
			},
			$html,
			1
		);

		if ( is_string( $rewritten ) ) {
			$html = $rewritten;
		}
	}

	$html = preg_replace(
		'/(<img\b[^>]*\bclass="[^"]*wp-block-cover__image-background[^"]*"[^>]*\bsrc=")[^"]*(")/',
		'$1' . esc_url( $url ) . '$2',
		$html,
		1
	);

	if ( is_string( $html ) && str_contains( $html, ' alt="' ) ) {
		$html = preg_replace(
			'/(<img\b[^>]*\bclass="[^"]*wp-block-cover__image-background[^"]*"[^>]*\balt=")[^"]*(")/',
			'$1' . esc_attr( $alt ) . '$2',
			$html,
			1
		);
	}

	return is_string( $html ) ? $html : '';
}

/**
 * Inject seeded home hero attachment into Cover blocks.
 *
 * @param array[] $blocks Parsed blocks.
 * @return array[]
 */
function venuestack_inject_home_hero_into_blocks( array $blocks ): array {
	$theme_hero_id = venuestack_get_home_hero_id();
	if ( ! $theme_hero_id ) {
		return $blocks;
	}

	$url = wp_get_attachment_image_url( $theme_hero_id, 'full' );
	if ( ! $url ) {
		return $blocks;
	}

	$alt = (string) get_post_meta( $theme_hero_id, '_wp_attachment_image_alt', true );
	if ( '' === $alt ) {
		$alt = venuestack_home_hero_alt();
	}

	foreach ( $blocks as &$block ) {
		if ( ( $block['blockName'] ?? '' ) === 'core/cover' && venuestack_cover_should_use_seeded_home_hero( $block['attrs'] ?? array() ) ) {
			$block['attrs']['id']  = $theme_hero_id;
			$block['attrs']['url'] = $url;
			$block['attrs']['alt'] = $alt;

			if ( ! empty( $block['innerHTML'] ) && is_string( $block['innerHTML'] ) ) {
				$block['innerHTML'] = venuestack_rewrite_cover_hero_image_html( $block['innerHTML'], $url, $alt, $theme_hero_id );
			}

			if ( ! empty( $block['innerContent'] ) && is_array( $block['innerContent'] ) ) {
				foreach ( $block['innerContent'] as &$chunk ) {
					if ( is_string( $chunk ) ) {
						$chunk = venuestack_rewrite_cover_hero_image_html( $chunk, $url, $alt, $theme_hero_id );
					}
				}
				unset( $chunk );
			}
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = venuestack_inject_home_hero_into_blocks( $block['innerBlocks'] );
		}
	}
	unset( $block );

	return $blocks;
}

/**
 * Rewrite template HTML so the home hero Cover uses Media Library media.
 */
function venuestack_inject_home_hero_media( string $content ): string {
	if ( ! str_contains( $content, 'venuestack-home-hero' ) ) {
		return $content;
	}

	$blocks = parse_blocks( $content );
	$blocks = venuestack_inject_home_hero_into_blocks( $blocks );

	return serialize_blocks( $blocks );
}

/**
 * @param WP_Block_Template|null $template Template object.
 * @return WP_Block_Template|null
 */
function venuestack_filter_block_template_home_hero( $template ) {
	if ( $template instanceof WP_Block_Template && ! empty( $template->content ) ) {
		$template->content = venuestack_inject_home_hero_media( $template->content );
	}

	return $template;
}
add_filter( 'get_block_file_template', 'venuestack_filter_block_template_home_hero', 20 );
add_filter( 'get_block_template', 'venuestack_filter_block_template_home_hero', 20 );

/**
 * @param WP_Block_Template[] $templates Templates.
 * @return WP_Block_Template[]
 */
function venuestack_filter_block_templates_home_hero( array $templates ): array {
	foreach ( $templates as $template ) {
		venuestack_filter_block_template_home_hero( $template );
	}

	return $templates;
}
add_filter( 'get_block_templates', 'venuestack_filter_block_templates_home_hero', 20 );

/**
 * Resolve home hero Cover attrs at render time.
 *
 * @param array $parsed_block Parsed block.
 * @return array
 */
function venuestack_resolve_home_hero_render_block_data( array $parsed_block ): array {
	if ( ( $parsed_block['blockName'] ?? '' ) !== 'core/cover' ) {
		return $parsed_block;
	}

	if ( ! venuestack_cover_should_use_seeded_home_hero( $parsed_block['attrs'] ?? array() ) ) {
		return $parsed_block;
	}

	$id = venuestack_get_home_hero_id();
	$url = $id ? wp_get_attachment_image_url( $id, 'full' ) : false;

	if ( ! $id || ! $url ) {
		return $parsed_block;
	}

	$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
	if ( '' === $alt ) {
		$alt = venuestack_home_hero_alt();
	}

	$parsed_block['attrs']['id']  = $id;
	$parsed_block['attrs']['url'] = $url;
	$parsed_block['attrs']['alt'] = $alt;

	if ( ! empty( $parsed_block['innerHTML'] ) && is_string( $parsed_block['innerHTML'] ) ) {
		$parsed_block['innerHTML'] = venuestack_rewrite_cover_hero_image_html( $parsed_block['innerHTML'], $url, $alt, $id );
	}

	if ( ! empty( $parsed_block['innerContent'] ) && is_array( $parsed_block['innerContent'] ) ) {
		foreach ( $parsed_block['innerContent'] as &$chunk ) {
			if ( is_string( $chunk ) ) {
				$chunk = venuestack_rewrite_cover_hero_image_html( $chunk, $url, $alt, $id );
			}
		}
		unset( $chunk );
	}

	return $parsed_block;
}
add_filter( 'render_block_data', 'venuestack_resolve_home_hero_render_block_data' );
