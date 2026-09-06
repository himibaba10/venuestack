<?php
/**
 * Theme logo: ensure the bundled mark is the Customizer site logo.
 *
 * @package Venuestack
 */

defined('ABSPATH') || exit;

/**
 * Create or refresh the media attachment used as custom_logo.
 */
function venuestack_ensure_site_logo(): void
{
	$path = get_template_directory() . '/assets/images/venuestack-logo.png';

	if (!is_readable($path)) {
		return;
	}

	$hash = md5_file($path);
	$logo_id = (int) get_theme_mod('custom_logo');
	$stored = (string) get_option('venuestack_logo_hash', '');
	$meta_key = '_venuestack_theme_logo';

	if ($logo_id && wp_attachment_is_image($logo_id) && $stored === $hash) {
		return;
	}

	// Prefer an existing attachment we previously created for this theme logo.
	if (!$logo_id || !wp_attachment_is_image($logo_id)) {
		$query = new WP_Query(
			array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'posts_per_page' => 1,
				'fields' => 'ids',
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_key' => $meta_key,
				'meta_value' => '1',
			)
		);
		$logo_id = !empty($query->posts[0]) ? (int) $query->posts[0] : 0;
	}

	if ($logo_id && wp_attachment_is_image($logo_id) && $stored === $hash) {
		set_theme_mod('custom_logo', $logo_id);
		return;
	}

	if (!function_exists('wp_upload_bits')) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	if (!function_exists('wp_generate_attachment_metadata')) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}
	if (!function_exists('media_handle_sideload')) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
	}

	$bits = wp_upload_bits(
		'venuestack-logo.png',
		null,
		file_get_contents($path) // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	);

	if (!empty($bits['error']) || empty($bits['file'])) {
		return;
	}

	$filetype = wp_check_filetype($bits['file'], null);
	$attach = array(
		'post_mime_type' => $filetype['type'] ?? 'image/png',
		'post_title' => 'VenueStack',
		'post_content' => '',
		'post_status' => 'inherit',
	);

	if ($logo_id && wp_attachment_is_image($logo_id)) {
		$attach['ID'] = $logo_id;
		$attachment_id = wp_update_post($attach, true);
		if (is_wp_error($attachment_id)) {
			return;
		}
		update_attached_file($logo_id, $bits['file']);
		$attachment_id = $logo_id;
	} else {
		$attachment_id = wp_insert_attachment($attach, $bits['file']);
		if (is_wp_error($attachment_id) || !$attachment_id) {
			return;
		}
	}

	$metadata = wp_generate_attachment_metadata($attachment_id, $bits['file']);
	wp_update_attachment_metadata($attachment_id, $metadata);
	update_post_meta($attachment_id, $meta_key, '1');
	update_post_meta($attachment_id, '_wp_attachment_image_alt', 'VenueStack');

	set_theme_mod('custom_logo', (int) $attachment_id);
	update_option('venuestack_logo_hash', $hash, false);
}
add_action('init', 'venuestack_ensure_site_logo', 5);
