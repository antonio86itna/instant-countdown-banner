<?php
/**
 * Uninstall script for Instant Countdown Banner.
 *
 * @package Instant_Countdown_Banner
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'icb_options' );

// If multisite, also delete from each site.
if ( is_multisite() ) {
	global $wpdb;
	$site_ids = $wpdb->get_col( "SELECT site_id FROM {$wpdb->blogs}" );
	if ( $site_ids ) {
		foreach ( $site_ids as $site_id ) {
			switch_to_blog( (int) $site_id );
			delete_option( 'icb_options' );
			restore_current_blog();
		}
	}
}
