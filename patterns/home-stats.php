<?php
/**
 * Title: Home stats
 * Slug: venuestack/home-stats
 * Categories: venuestack, featured
 * Description: Live inventory stats strip for the homepage (spaces, capacity, hold TTL). Values are bound and locked.
 * Viewport Width: 1200
 * Inserter: no
 *
 * @package Venuestack
 */

$spaces_label = esc_html( venuestack_get_home_stat_display( 'spaces' ) );
$max_label    = esc_html( venuestack_get_home_stat_display( 'max_guests' ) );
$hold_label   = esc_html( venuestack_get_home_stat_display( 'hold' ) );
$zero_label   = esc_html( venuestack_get_home_stat_display( 'double_bookings' ) );
?>
<!-- wp:group {"align":"full","className":"venuestack-home-stats","templateLock":"all","style":{"border":{"bottom":{"color":"var:preset|color|rule","width":"1px","style":"solid"}},"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"blockGap":"0"}},"backgroundColor":"plaster","layout":{"type":"constrained","contentSize":"1200px"},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-group alignfull venuestack-home-stats has-plaster-background-color has-background" style="border-bottom-color:var(--wp--preset--color--rule);border-bottom-style:solid;border-bottom-width:1px;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:columns {"className":"venuestack-home-stats__row","templateLock":"all","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-columns venuestack-home-stats__row"><!-- wp:column {"className":"venuestack-home-stats__cell","templateLock":"all","style":{"border":{"right":{"color":"var:preset|color|rule","width":"1px","style":"solid"}},"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-column venuestack-home-stats__cell" style="border-right-color:var(--wp--preset--color--rule);border-right-style:solid;border-right-width:1px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--20);padding-bottom:0;padding-left:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"align":"center","className":"is-style-display","textColor":"brass","lock":{"move":true,"remove":true},"metadata":{"bindings":{"content":{"source":"venuestack/home-stat","args":{"key":"spaces"}}}}} -->
<p class="has-text-align-center is-style-display has-brass-color has-text-color"><?php echo $spaces_label; ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"is-style-label","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","lock":{"move":true,"remove":true}} -->
<p class="has-text-align-center is-style-label has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0"><?php echo esc_html__( 'Bookable spaces', 'venuestack' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"venuestack-home-stats__cell","templateLock":"all","style":{"border":{"right":{"color":"var:preset|color|rule","width":"1px","style":"solid"}},"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-column venuestack-home-stats__cell" style="border-right-color:var(--wp--preset--color--rule);border-right-style:solid;border-right-width:1px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--20);padding-bottom:0;padding-left:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"align":"center","className":"is-style-display","textColor":"brass","lock":{"move":true,"remove":true},"metadata":{"bindings":{"content":{"source":"venuestack/home-stat","args":{"key":"max_guests"}}}}} -->
<p class="has-text-align-center is-style-display has-brass-color has-text-color"><?php echo $max_label; ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"is-style-label","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","lock":{"move":true,"remove":true}} -->
<p class="has-text-align-center is-style-label has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0"><?php echo esc_html__( 'Max seated guests', 'venuestack' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"venuestack-home-stats__cell","templateLock":"all","style":{"border":{"right":{"color":"var:preset|color|rule","width":"1px","style":"solid"}},"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-column venuestack-home-stats__cell" style="border-right-color:var(--wp--preset--color--rule);border-right-style:solid;border-right-width:1px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--20);padding-bottom:0;padding-left:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"align":"center","className":"is-style-display","textColor":"brass","lock":{"move":true,"remove":true},"metadata":{"bindings":{"content":{"source":"venuestack/home-stat","args":{"key":"hold"}}}}} -->
<p class="has-text-align-center is-style-display has-brass-color has-text-color"><?php echo $hold_label; ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"is-style-label","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","lock":{"move":true,"remove":true}} -->
<p class="has-text-align-center is-style-label has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0"><?php echo esc_html__( 'Checkout hold', 'venuestack' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"venuestack-home-stats__cell","templateLock":"all","style":{"spacing":{"padding":{"left":"var:preset|spacing|20","right":"var:preset|spacing|20","top":"0","bottom":"0"},"margin":{"top":"0","bottom":"0"}}},"lock":{"move":true,"remove":true}} -->
<div class="wp-block-column venuestack-home-stats__cell" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--20);padding-bottom:0;padding-left:var(--wp--preset--spacing--20)"><!-- wp:paragraph {"align":"center","className":"is-style-display","textColor":"brass","lock":{"move":true,"remove":true},"metadata":{"bindings":{"content":{"source":"venuestack/home-stat","args":{"key":"double_bookings"}}}}} -->
<p class="has-text-align-center is-style-display has-brass-color has-text-color"><?php echo $zero_label; ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"is-style-label","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","lock":{"move":true,"remove":true}} -->
<p class="has-text-align-center is-style-label has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0"><?php echo esc_html__( 'Double bookings', 'venuestack' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
