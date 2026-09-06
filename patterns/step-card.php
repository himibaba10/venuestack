<?php
/**
 * Title: Step card
 * Slug: venuestack/step-card
 * Categories: venuestack, featured
 * Description: Booking-flow step with a synced layout and overridable number, heading, and body.
 * Viewport Width: 280
 */
?>
<!-- wp:column {"className":"venuestack-home-step venuestack-home-reveal"} -->
<div class="wp-block-column venuestack-home-step venuestack-home-reveal"><!-- wp:paragraph {"className":"is-style-display venuestack-home-step__n","textColor":"brass","metadata":{"name":"number","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p class="is-style-display venuestack-home-step__n has-brass-color has-text-color">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"clamp(1.25rem, 1.1rem + 0.8vw, 1.5rem)","lineHeight":"1.15"},"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"0"}}},"textColor":"ink","metadata":{"name":"heading","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<h3 class="wp-block-heading has-ink-color has-text-color" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:0;font-size:clamp(1.25rem, 1.1rem + 0.8vw, 1.5rem);line-height:1.15">Step title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","metadata":{"name":"description","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0">Step description.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
