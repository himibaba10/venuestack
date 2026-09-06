<?php
/**
 * Title: Capability card
 * Slug: venuestack/capability-card
 * Categories: venuestack, featured
 * Description: Feature card with a synced layout and overridable icon, heading, and paragraph.
 * Viewport Width: 420
 */
?>
<!-- wp:group {"className":"venuestack-home-cap venuestack-home-reveal","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"card","layout":{"type":"default"}} -->
<div class="wp-block-group venuestack-home-cap venuestack-home-reveal has-card-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:icon {"icon":"venuestack/layout-grid","className":"venuestack-home-cap__icon","style":{"spacing":{"padding":{"top":"0.75rem","bottom":"0.75rem","left":"0.75rem","right":"0.75rem"}},"dimensions":{"width":"1.5rem"},"border":{"radius":"0px"}},"backgroundColor":"brass","textColor":"plaster","metadata":{"name":"icon","bindings":{"__default":{"source":"core/pattern-overrides"}}}} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem","fontWeight":"600","lineHeight":"1.15","letterSpacing":"-0.015em"},"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"0"}}},"textColor":"ink","fontFamily":"playfair-display","metadata":{"name":"heading","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<h3 class="wp-block-heading has-ink-color has-text-color has-playfair-display-font-family" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:0;font-size:1.25rem;font-weight:600;letter-spacing:-0.015em;line-height:1.15">Capability title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"0"}}},"textColor":"muted","metadata":{"name":"description","bindings":{"__default":{"source":"core/pattern-overrides"}}}} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:0">Capability description.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
