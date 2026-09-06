<?php
/**
 * Title: Space card
 * Slug: venuestack/space-card
 * Categories: venuestack, featured
 * Description: Directory card for a venue space. Content is dynamic from the Query Loop; edit this pattern to change styling globally.
 * Viewport Width: 360
 * Block Types: core/post-template
 */
?>
<!-- wp:group {"className":"venuestack-home-space-card venuestack-home-reveal","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"backgroundColor":"plaster"} -->
<div class="wp-block-group venuestack-home-space-card venuestack-home-reveal has-plaster-background-color has-background" style="margin-top:0;margin-bottom:0"><!-- wp:group {"className":"venuestack-home-space-card__media","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-group venuestack-home-space-card__media" style="margin-top:0;margin-bottom:0"><!-- wp:post-terms {"term":"space_type","separator":"","className":"venuestack-home-space-type","style":{"typography":{"fontSize":"0.65rem","fontWeight":"600","letterSpacing":"0.16em","textTransform":"uppercase","lineHeight":"1.2"},"spacing":{"padding":{"top":"0.4rem","bottom":"0.4rem","left":"0.75rem","right":"0.75rem"},"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}},"backgroundColor":"brass","textColor":"plaster"} /-->

<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"venuestack-home-space-card__body","style":{"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group venuestack-home-space-card__body" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontSize":"clamp(1.25rem, 1.1rem + 0.8vw, 1.5rem)","lineHeight":"1.15"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"ink"} /-->

<!-- wp:group {"className":"venuestack-home-space-meta","style":{"spacing":{"blockGap":"0.15rem","margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group venuestack-home-space-meta" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0">Capacity</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/space-field","args":{"key":"max_capacity"}}}},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"textAlign":"right"}},"textColor":"ink","fontSize":"small"} -->
<p class="has-text-align-right has-ink-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0">Footprint</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/space-field","args":{"key":"square_footage"}}}},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"textAlign":"right"}},"textColor":"ink","fontSize":"small"} -->
<p class="has-text-align-right has-ink-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0">From</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/space-field","args":{"key":"hourly_rate"}}}},"className":"is-rate","style":{"typography":{"fontWeight":"600","textAlign":"right"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"brass","fontSize":"small"} -->
<p class="has-text-align-right is-rate has-brass-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0;font-weight:600"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
