<?php
/**
 * The plugin helper methods are defined here here.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * The complete management for the helper Objects.
 *
 * This class defines all code necessary to run helper operations.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_Crm_Helper {


	/**
	 * Get all feeds.
	 *
	 * @return array An array of all feeds.
	 * @since 1.0.0
	 */
	public static function get_feeds() {

		$args = array(
			'post_type'   => 'mwb_zoho_feeds',
			'post_status' => array( 'publish', 'draft' ),
			'numberposts' => -1,
			'order'       => 'ASC',
		);

		return get_posts( $args );
	}
	/**
	 * Create log folder.
	 *
	 * @param string $path name of log folder.
	 * @since 1.0.0
	 */
	public static function create_log_folder( $path ) {

		$basepath = WP_CONTENT_DIR . '/uploads/';
		$fullpath = $basepath . $path;

		if ( ! empty( $fullpath ) ) {

			if ( ! is_dir( $fullpath ) ) {

				$folder = mkdir( $fullpath, 0755, true );

				if ( $folder ) {
					return $fullpath;
				}
			} else {
				return $fullpath;
			}
		}
		return false;
	}

	/**
	 * Get settings of the plugin.
	 *
	 * @param string $setting Setting value to get.
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_settings_details( $setting = false ) {

		if ( empty( $setting ) ) {
			return false;
		}

		$result = '';
		$option = get_option( 'mwb_zgf_setting' );

		if ( ! empty( $option ) && is_array( $option ) ) {

			switch ( $setting ) {

				case 'logs':
					$result = ! empty( $option['enable_logs'] ) ? sanitize_text_field( wp_unslash( $option['enable_logs'] ) ) : '';
					break;

				case 'delete':
					$result = ! empty( $option['data_delete'] ) ? sanitize_text_field( wp_unslash( $option['data_delete'] ) ) : '';
					break;

				case 'notif':
					$result = ! empty( $option['enable_notif'] ) ? sanitize_text_field( wp_unslash( $option['enable_notif'] ) ) : '';
					break;

				case 'email':
					$result = ! empty( $option['email_notif'] ) ? sanitize_email( wp_unslash( $option['email_notif'] ) ) : '';
					break;

				case 'delete_logs':
					$result = ! empty( $option['delete_logs'] ) ? sanitize_text_field( wp_unslash( $option['delete_logs'] ) ) : '';
					break;
			}
		}

		$result = apply_filters( 'mwb_zgf_manage_get_setting', $result, $option, $setting );

		return $result;
	}


	/**
	 * Get token expiry details.
	 */
	public static function get_token_expiry_details() {

		$token_data = get_option( 'mwb_zgf_zoho_token_data' );

		if ( $token_data['expiry'] > time() ) {
			return ceil( ( $token_data['expiry'] - time() ) / 60 );
		}
		return false;
	}


	/**
	 * Get zoho module names
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_modules() {

		$data   = get_option( 'mwb_zgf_modules', false );
		$result = array();

		if ( false == $data ) { // @codingStandardsIgnoreLine
			return false;
		}

		if ( isset( $data['modules'] ) ) {
			foreach ( $data['modules'] as $modules ) {
				if ( isset( $modules['api_supported'] ) && true == $modules['api_supported'] ) { // @codingStandardsIgnoreLine
					if ( isset( $modules['module_name'] ) && isset( $modules['api_name'] ) ) {
						$result[ $modules['api_name'] ] = $modules['module_name'];
					}
				}
			}
		}

		return $result;

	}

	/**
	 * Default settings of the plugin.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function mwb_zgf_default_settings() {

		$default_settings = array(
			'enable_logs'   => 'yes',
			'data_delete'   => 'no',
			'enable_sync'   => 'yes',
			'update_entry'  => 'no',
			'delete_entry'  => 'no',
			'restore_entry' => 'no',
			'enable_notif'  => 'no',
			'email_notif'   => '',
			'delete_logs'   => 30,
		);

		return apply_filters( 'mwb_zgf_default_settings', $default_settings );
	}

	/**
	 * Change post status.
	 *
	 * @param int    $id     Post ID.
	 * @param string $status Status of post.
	 * @return bool;
	 */
	public static function change_post_status( $id, $status ) {

		if ( ! empty( $id ) && ! empty( $status ) ) {

			$post                = get_post( $id, 'ARRAY_A' );
			$post['post_status'] = $status;
			$response            = wp_update_post( $post );

			if ( $response && 0 != $response ) { // @codingStandardsIgnoreLine
				return true;
			}
		}
		return false;
	}


	/**
	 * Get form fields.
	 *
	 * @param array $data An array of form data.
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_form_fields( $data = array() ) {

		$form_data = array();

		$form_fields = $data['fields'];

		if ( ! empty( $form_fields ) && is_array( $form_fields ) ) {
			foreach ( $form_fields as $form_obj ) {
				if ( ! empty( $form_obj->inputs ) ) {
					foreach ( $form_obj->inputs as $fields ) {
						if ( ! isset( $fields['isHidden'] ) || 1 != $fields['isHidden'] ) {       // @codingStandardsIgnoreLine
							$form_data[ $fields['id'] ] = $form_obj->label . '( ' . $fields['label'] . ' )';
						}
					}
				} else {
					$form_data[ $form_obj->id ] = $form_obj->label;
				}
			}
		}
		return $form_data;
	}

	/**
	 * Fetch all logs.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_logs() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mwb_zgf_log';
		$query      = "SELECT * FROM `$table_name`";
		$response   = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
		return $response;
	}

	/**
	 * Get link to data sent over zoho.
	 *
	 * @param array $data An array of data synced over zoho.
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_zoho_link( $data = array() ) {

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		$_domain = get_option( 'mwb-zgf-domain', 'in' );
		$object  = $data['zoho_object'];
		$id      = $data['zoho_id'];

		$link = 'https://crm.zoho.' . $_domain . '/crm/tab/' . $object . '/' . $id;

		return $link;

	}

	/**
	 * Returns count of synced data.
	 *
	 * @since 1.0.0
	 * @return integer
	 */
	public static function get_synced_forms_count() {

		global $wpdb;
		$table_name  = $wpdb->prefix . 'mwb_zgf_log';
		$col_name    = 'zoho_id';
		$count_query = "SELECT COUNT(*) as `total_count` FROM {$table_name} WHERE {$col_name} != '-'"; // @codingStandardsIgnoreLine
		$count_data  = $wpdb->get_col( $count_query ); // @codingStandardsIgnoreLine
		$total_count = isset( $count_data[0] ) ? $count_data[0] : '0';

		return $total_count;
	}

	/**
	 * Returns if pro plugin is active or not.
	 *
	 * @since      1.0.0
	 * @return     bool
	 */
	public static function is_pro_active() {
		// Check if pro plugin exists.
		if ( mwb_zoho_gf_is_plugin_active( 'gf-integration-with-zoho-crm/gf-integration-with-zoho-crm.php' ) ) {

			if ( class_exists( 'Gf_Integration_With_Zoho_Crm_Admin' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if pro is active or if on trial.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function is_pro_license_active_or_on_trial() {

		$active = false;

		if ( self::is_pro_active() ) {
			$day_count        = Gf_Integration_With_Zoho_Crm_Helper::mwb_zgf_pro_check_license_initial_days();
			$license_validity = Gf_Integration_With_Zoho_Crm_Helper::mwb_zgf_pro_check_license_validity();

			if ( $license_validity || 0 <= $day_count ) {

				$active = true;
			}
		}

		return $active;
	}


}
