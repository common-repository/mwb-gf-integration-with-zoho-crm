<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_Crm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Create database table for logs.
		self::mwb_zgf_create_log_table();

		// Schedule event to clear logs.
		self::add_scheduled_event();
	}


	/**
	 * Create database table for logs.
	 *
	 * @return void
	 */
	public static function mwb_zgf_create_log_table() {

		global $wpdb;
		$table_name = $wpdb->prefix . 'mwb_zgf_log';

		$query  = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		$result = $wpdb->get_var( $query ); // @codingStandardsIgnoreLine

		if ( empty( $result ) || $result != $table_name ) { // @codingStandardsIgnoreLine

			try {
				global $wpdb;
				$charset_collate = $wpdb->get_charset_collate();
				$table_name      = $wpdb->prefix . 'mwb_zgf_log';

				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`feed` varchar(255) NOT NULL,
					`feed_id` int(11) NOT NULL,
					`zoho_object` varchar(255) NOT NULL,
					`zoho_id` varchar(255) NOT NULL,
					`event` varchar(255) NOT NULL,
					`request` text NOT NULL,
					`response` text NOT NULL,
					`time` int(11) NOT NULL,
					PRIMARY KEY (`id`)
					) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );

			} catch ( \Throwable $th ) {
				wp_die( esc_html( $th->getMessage() ) );
			}
		}

		update_option( 'mwb_zgf_log_table_created', true );
	}

	/**
	 * Schedule clear log event.
	 */
	public static function add_scheduled_event() {
		if ( ! wp_next_scheduled( 'mwb_zgf_clear_log' ) ) {
			wp_schedule_event( time(), 'hourly', 'mwb_zgf_clear_log' );
		}
	}


}
