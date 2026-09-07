<?php
/**
 * Title: Package card
 * Slug: venuestack/package-card
 * Categories: venuestack, featured
 * Description: Directory card for an event package. Content is dynamic from the Query Loop; edit this pattern to change styling globally.
 * Viewport Width: 360
 * Block Types: core/post-template
 */
?>
<!-- wp:group {"className":"venuestack-home-space-card venuestack-package-card venuestack-home-reveal","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"backgroundColor":"plaster"} -->
<div class="wp-block-group venuestack-home-space-card venuestack-package-card venuestack-home-reveal has-plaster-background-color has-background" style="margin-top:0;margin-bottom:0"><!-- wp:group {"className":"venuestack-home-space-card__media","style":{"spacing":{"blockGap":"0","margin":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-group venuestack-home-space-card__media" style="margin-top:0;margin-bottom:0"><!-- wp:paragraph {"className":"venuestack-home-space-type","style":{"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}},"backgroundColor":"brass","textColor":"plaster"} -->
<p class="venuestack-home-space-type has-plaster-color has-brass-background-color has-text-color has-background" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0"><?php echo esc_html__( 'Package', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"venuestack-home-space-card__body","style":{"spacing":{"blockGap":"0","padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group venuestack-home-space-card__body" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-title {"level":3,"isLink":true,"className":"is-style-card","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"ink"} /-->

<!-- wp:group {"className":"venuestack-home-space-meta","style":{"spacing":{"blockGap":"0.15rem","margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group venuestack-home-space-meta" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted"} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:0;margin-bottom:0"><?php echo esc_html__( 'From', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/package-field","args":{"key":"price_per_head"}}}},"className":"is-rate","style":{"typography":{"textAlign":"right"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"brass","fontSize":"small"} -->
<p class="has-text-align-right is-rate has-brass-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted"} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:0;margin-bottom:0"><?php echo esc_html__( 'Notice', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/package-field","args":{"key":"requires_advance_notice"}}}},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"textAlign":"right"}},"textColor":"ink","fontSize":"small"} -->
<p class="has-text-align-right has-ink-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"1rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-body","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"textColor":"muted"} -->
<p class="is-style-body has-muted-color has-text-color" style="margin-top:0;margin-bottom:0"><?php echo esc_html__( 'Includes', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"venuestack/package-field","args":{"key":"menu_items_count"}}}},"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"textAlign":"right"}},"textColor":"ink","fontSize":"small"} -->
<p class="has-text-align-right has-ink-color has-text-color has-small-font-size" style="margin-top:0;margin-bottom:0"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
