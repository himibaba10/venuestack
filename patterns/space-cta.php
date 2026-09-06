<?php
/**
 * Title: Space CTA
 * Slug: venuestack/space-cta
 * Categories: venuestack, call-to-action
 * Description: Full-bleed booking call-to-action band for a single venue space.
 * Viewport Width: 1200
 *
 * @package Venuestack
 */
?>
<!-- wp:group {"align":"full","anchor":"book","className":"venuestack-space-section venuestack-space-cta","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"},"blockGap":"var:preset|spacing|30"}},"backgroundColor":"evergreen","textColor":"plaster","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull venuestack-space-section venuestack-space-cta has-plaster-color has-evergreen-background-color has-text-color has-background" id="book" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"align":"wide","className":"venuestack-home-reveal","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"42rem","justifyContent":"left"}} -->
<div class="wp-block-group alignwide venuestack-home-reveal"><!-- wp:paragraph {"className":"is-style-eyebrow","textColor":"brass"} -->
<p class="is-style-eyebrow has-brass-color has-text-color"><?php echo esc_html__( 'Reserve', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-section","textColor":"plaster"} -->
<h2 class="wp-block-heading is-style-section has-plaster-color has-text-color"><?php echo esc_html__( 'Hold this room before the calendar fills.', 'venuestack' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lede","textColor":"plaster-muted"} -->
<p class="is-style-lede has-plaster-muted-color has-text-color"><?php echo esc_html__( 'Confirm capacity and rate above, then start a booking. Soft holds and live checkout land next.', 'venuestack' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button {"backgroundColor":"brass","textColor":"plaster","className":"is-style-fill","style":{"border":{"radius":"0px"}}} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-plaster-color has-brass-background-color has-text-color has-background wp-element-button" href="/spaces/" style="border-radius:0px"><?php echo esc_html__( 'Browse all spaces', 'venuestack' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"textColor":"plaster","className":"is-style-outline","style":{"border":{"radius":"0px","width":"1px"},"color":{"background":"transparent"}},"borderColor":"plaster"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-plaster-color has-text-color has-background has-border-color has-plaster-border-color wp-element-button" href="/packages/" style="border-width:1px;border-radius:0px;background-color:transparent"><?php echo esc_html__( 'Talk to events team', 'venuestack' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
