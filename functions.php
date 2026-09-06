<?php
/**
 * VenueStack theme bootstrap.
 *
 * @package Venuestack
 */

defined('ABSPATH') || exit;

define('VENUESTACK_VERSION', wp_get_theme()->get('Version') ?: '0.1.0');

$venuestack_inc = get_template_directory() . "/inc";

require_once "$venuestack_inc/icons.php";
require_once "$venuestack_inc/logo.php";
require_once "$venuestack_inc/hero-media.php";
require_once "$venuestack_inc/home-stats.php";
require_once "$venuestack_inc/synced-patterns.php";
require_once "$venuestack_inc/block-styles.php";
require_once "$venuestack_inc/editor-compat.php";
require_once "$venuestack_inc/assets.php";
