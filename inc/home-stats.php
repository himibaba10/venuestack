<?php
/**
 * Homepage stats: live inventory + product constants.
 *
 * @package Venuestack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Transient key for aggregated homepage stats.
 */
function venuestack_home_stats_transient_key(): string {
	return 'venuestack_home_stats_v2';
}

/**
 * Clear cached homepage stats when venue inventory changes.
 *
 * @param int $post_id Post ID.
 */
function venuestack_flush_home_stats_cache( int $post_id = 0 ): void {
	if ( $post_id > 0 && 'venue_space' !== get_post_type( $post_id ) ) {
		return;
	}

	delete_transient( venuestack_home_stats_transient_key() );
}
add_action( 'save_post_venue_space', 'venuestack_flush_home_stats_cache' );
add_action( 'deleted_post', 'venuestack_flush_home_stats_cache' );
add_action( 'trashed_post', 'venuestack_flush_home_stats_cache' );
add_action( 'untrashed_post', 'venuestack_flush_home_stats_cache' );

/**
 * Flush stats when space_type terms change.
 *
 * @param int    $term_id Term ID.
 * @param int    $tt_id   Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 */
function venuestack_flush_home_stats_on_term_change( int $term_id, int $tt_id, string $taxonomy ): void {
	unset( $term_id, $tt_id );

	if ( 'space_type' === $taxonomy ) {
		venuestack_flush_home_stats_cache();
	}
}
add_action( 'created_term', 'venuestack_flush_home_stats_on_term_change', 10, 3 );
add_action( 'edited_term', 'venuestack_flush_home_stats_on_term_change', 10, 3 );
add_action( 'delete_term', 'venuestack_flush_home_stats_on_term_change', 10, 3 );

/**
 * Soft-hold duration in seconds (plugin constant when available).
 */
function venuestack_home_stats_hold_ttl(): int {
	if ( defined( 'VENUESTACK_HOLD_TTL' ) ) {
		return (int) VENUESTACK_HOLD_TTL;
	}

	return 15 * MINUTE_IN_SECONDS;
}

/**
 * Human-readable checkout hold label (e.g. "15 min").
 */
function venuestack_home_stats_hold_label(): string {
	$seconds = venuestack_home_stats_hold_ttl();
	$minutes = (int) max( 1, (int) round( $seconds / MINUTE_IN_SECONDS ) );

	if ( $minutes < 60 ) {
		/* translators: %d: hold duration in minutes */
		return sprintf( _n( '%d min', '%d min', $minutes, 'venuestack' ), $minutes );
	}

	$hours = (int) round( $minutes / 60 );

	/* translators: %d: hold duration in hours */
	return sprintf( _n( '%d hr', '%d hr', $hours, 'venuestack' ), $hours );
}

/**
 * Count of published space_type terms in use (or all if none assigned yet).
 */
function venuestack_home_stats_space_type_count(): int {
	if ( ! taxonomy_exists( 'space_type' ) ) {
		return 0;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'space_type',
			'hide_empty' => true,
			'fields'     => 'count',
		)
	);

	if ( is_wp_error( $terms ) ) {
		return 0;
	}

	$count = (int) $terms;

	// Fresh installs may have types seeded before spaces are assigned.
	if ( $count < 1 ) {
		$all = get_terms(
			array(
				'taxonomy'   => 'space_type',
				'hide_empty' => false,
				'fields'     => 'count',
			)
		);
		$count = is_wp_error( $all ) ? 0 : (int) $all;
	}

	return $count;
}

/**
 * Aggregated homepage stats for the marketing strip.
 *
 * @return array{spaces:int,max_guests:int,hold:string,space_types:int}
 */
function venuestack_get_home_stats(): array {
	$cached = get_transient( venuestack_home_stats_transient_key() );

	if ( is_array( $cached ) && isset( $cached['spaces'], $cached['max_guests'], $cached['hold'], $cached['space_types'] ) ) {
		return $cached;
	}

	$spaces = 0;
	$max    = 0;

	if ( post_type_exists( 'venue_space' ) ) {
		$counts = wp_count_posts( 'venue_space' );
		$spaces = isset( $counts->publish ) ? (int) $counts->publish : 0;

		if ( $spaces > 0 ) {
			$query = new WP_Query(
				array(
					'post_type'              => 'venue_space',
					'post_status'            => 'publish',
					'posts_per_page'         => -1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => true,
					'update_post_term_cache' => false,
				)
			);

			foreach ( $query->posts as $space_id ) {
				$max = max( $max, (int) get_post_meta( (int) $space_id, 'max_capacity', true ) );
			}
		}
	}

	$stats = array(
		'spaces'      => $spaces,
		'max_guests'  => $max,
		'hold'        => venuestack_home_stats_hold_label(),
		'space_types' => venuestack_home_stats_space_type_count(),
	);

	set_transient( venuestack_home_stats_transient_key(), $stats, 5 * MINUTE_IN_SECONDS );

	return $stats;
}

/**
 * Formatted display string for one homepage stat key.
 *
 * @param string $key One of spaces|max_guests|hold|space_types.
 */
function venuestack_get_home_stat_display( string $key ): string {
	$stats = venuestack_get_home_stats();

	switch ( $key ) {
		case 'spaces':
		case 'max_guests':
		case 'space_types':
			return number_format_i18n( (int) ( $stats[ $key ] ?? 0 ) );

		case 'hold':
			return (string) ( $stats['hold'] ?? '' );

		default:
			return '';
	}
}

/**
 * Register Block Bindings source for locked homepage stats.
 */
function venuestack_register_home_stat_bindings(): void {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return;
	}

	register_block_bindings_source(
		'venuestack/home-stat',
		array(
			'label'              => __( 'Homepage stat', 'venuestack' ),
			'get_value_callback' => 'venuestack_get_home_stat_binding',
			'uses_context'       => array(),
		)
	);
}
add_action( 'init', 'venuestack_register_home_stat_bindings' );

/**
 * Binding callback for homepage stats.
 *
 * @param array    $source_args    Binding args (key).
 * @param WP_Block $block_instance Block instance.
 * @param string   $attribute_name Bound attribute.
 */
function venuestack_get_home_stat_binding( array $source_args, WP_Block $block_instance, string $attribute_name ): string {
	unset( $block_instance, $attribute_name );

	$key = isset( $source_args['key'] ) ? (string) $source_args['key'] : '';

	return venuestack_get_home_stat_display( $key );
}
