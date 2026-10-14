<?php
/**
 * Uninstall cleanup for Better Notifications for WP.
 *
 * Removes the add-on licensing data and the scheduled re-validation event that
 * this plugin created. Notifications and other BNFW settings are intentionally
 * left intact, so uninstalling does not wipe a site's notification setup.
 *
 * @package bnfw
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove the scheduled add-on licence re-validation event.
wp_clear_scheduled_hook( 'bnfw_revalidate_licenses' );

// Load the add-on catalog. The licensing file guards its init() against the
// WP_UNINSTALL_PLUGIN constant, so including it here registers no hooks.
require_once __DIR__ . '/includes/license/class-bnfw-licensing.php';

if ( class_exists( 'BNFW_Licensing' ) ) {
	foreach ( BNFW_Licensing::get_addons() as $addon ) {
		if ( empty( $addon['basename'] ) ) {
			continue;
		}

		$slug = dirname( ltrim( $addon['basename'], '/' ) );

		delete_option( $slug . '_license_key' );
		delete_option( $slug . '_license' );
		delete_option( $slug . '_license_allow_tracking' );
		delete_option( $slug . '_bnfw_license_migrated' );
	}

	// The SDK's cached "recently failed request" marker for our store.
	delete_option( 'eddsdk_failed_request_' . md5( BNFW_Licensing::STORE_URL ) );
}
