<?php
/**
 * KV Navigation — Uninstall cleanup.
 *
 * Removes plugin post meta and options when the plugin is deleted.
 *
 * @package KV_Menu
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Remove per nav-menu-item mega settings and per-page header mode meta.
$meta_keys = [
	'_kv_menu_mega_enabled',
	'_kv_menu_mega_template_id',
	'_kv_menu_mega_keep_mobile_sub',
	'_kv_menu_mega_width_type',
	'_kv_menu_mega_custom_width',
	'_kv_menu_mega_alignment',
	'_kv_header_mode',
	'_kv_header_hide',
];

foreach ( $meta_keys as $meta_key ) {
	$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => $meta_key ] );
}

// Note: kv_mega_menu CPT posts (user-created templates) are intentionally left
// in place so content is not destroyed on plugin removal.
