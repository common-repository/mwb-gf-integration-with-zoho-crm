<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Get settings data.
$settings = get_option( 'mwb_zgf_setting' );

if ( ! empty( $settings ) && is_array( $settings ) ) {
	if ( isset( $settings['data_delete'] ) && 'on' == $settings['data_delete'] ) { // @codingStandardsIgnoreLine

		// Delete all feeds.
		$args = array(
			'post_type'      => 'mwb_zoho_feeds',
			'posts_per_page' => -1,
		);

		$all_feeds = get_posts( $args );

		if ( ! empty( $all_feeds ) && is_array( $all_feeds ) ) {
			foreach ( $all_feeds as $feed ) {
				wp_delete_post( $feed->ID, true );
			}
		}
		unregister_post_type( 'mwb_zoho_feeds' );

		// Drop logs table.
		global $wpdb;
		$table_name = $wpdb->prefix . 'mwb_zgf_log';

		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql ); // @codingStandardsIgnoreLine

		// Delete options at last.
		$options = array(
			'mwb_zgf_setting',
			'mwb-zgf-client-id',
			'mwb-zgf-secret-id',
			'mwb-zgf-domain',
			'mwb_is_crm_active',
			'mwb_zgf_modules',
			'mwb-zgf-log-last-delete',
			'mwb_zgf_log_table_created',
			'mwb_zgf_zoho_token_data',
			'mwb_zgf_synced_form_count',
			'mwb_zgf_synced_forms_count',
			'mwb_zgf_filter_feed_acc_to_form',
		);

		foreach ( $options as $option ) {
			if ( get_option( $option ) ) {
				delete_option( $option );
			}
		}

		// unscedule cron.
		wp_unschedule_event( time(), 'mwb_zgf_clear_log' );
	}
}
