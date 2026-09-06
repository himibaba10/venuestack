<?php
/**
 * Title: Step card
 * Slug: venuestack/step-card
 * Categories: venuestack, featured
 * Description: Booking-flow step with a synced layout and overridable number, heading, and body.
 * Viewport Width: 280
 */
?>
<!-- wp:column {"className":"venuestack-home-step venuestack-home-reveal","style":{"spacing":{"padding":{"top":"var:preset|spacing|30"}},"border":{"top":{"color":"var:preset|color|rule","width":"1px","style":"solid"}}}} -->
<div class="wp-block-column venuestack-home-step venuestack-home-reveal" style="border-top-color:var(--wp--preset--color--rule);border-top-style:solid;border-top-width:1px;padding-top:var(--wp--preset--spacing--30)"><!-- wp:paragraph {"className":"is-style-display venuestack-home-step__n","textColor":"brass","metadata":{"name":"number","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p class="is-style-display venuestack-home-step__n has-brass-color has-text-color">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-card","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"0"}}},"textColor":"ink","metadata":{"name":"heading","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<h3 class="wp-block-heading is-style-card has-ink-color has-text-color" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:0">Step title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0"}}},"textColor":"muted","metadata":{"name":"description","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:0.5rem;margin-bottom:0">Step description.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
