<?php
/**
 * The complete management for the Zoho-GF plugin through out the site.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * The complete management for the ajax handlers.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_CRM_Ajax_Handler {

	/**
	 * Get default response.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_default_response() {
		return array(
			'status'  => false,
			'message' => esc_html__( 'Something went wrong!!', 'mwb-gf-integration-with-zoho-crm' ),
		);
	}

	/**
	 * Ajax handler :: Handles all ajax callbacks.
	 *
	 * @since 1.0.0
	 */
	public function mwb_zgf_ajax_callback() {
		/* Nonce verification */
		check_ajax_referer( 'mwb_zgf_nonce', 'nonce' );

		$event    = ! empty( $_POST['event'] ) ? sanitize_text_field( wp_unslash( $_POST['event'] ) ) : '';
		$response = $this->get_default_response();

		if ( ! empty( $event ) ) {
			$data = $this->$event( $_POST );
			if ( $data ) { // @codingStandardsIgnoreLine

				$response['status']  = true;
				$response['message'] = esc_html__( 'Success', 'mwb-gf-integration-with-zoho-crm' );

				$response = $this->maybe_add_data( $response, $data );
			}
		}

		wp_send_json( $response );

	}

	/**
	 * Merge additional data to response.
	 *
	 * @param array $response An array of response.
	 * @param array $data     An array of data to merge in response.
	 * @return array
	 */
	public function maybe_add_data( $response, $data ) {

		if ( is_array( $data ) ) {
			$response['data'] = $data;
		}

		return $response;
	}


	/**
	 * Referesh access tokens.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function refresh_access_token() {

		$response        = array( 'success' => false );
		$zoho_api        = Zgf_Api::get_instance();
		$response['msg'] = esc_html__( 'Something went wrong! Check your credentials and authorize again', 'mwb-gf-integration-with-zoho-crm' );

		if ( $zoho_api->renew_access_token() ) {
			$token_expiry  = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_token_expiry_details();
			$token_message = sprintf( 'Access token will expire in %s minutes.', $token_expiry );
			$response      = array(
				'success'       => true,
				'msg'           => esc_html__( 'Success', 'mwb-gf-integration-with-zoho-crm' ),
				'token_message' => $token_message,
			);
		}
		return $response;
	}


	/**
	 * Reauthorize account
	 *
	 * @since 1.0.0
	 * @return string.
	 */
	public function reauthorize_zgf_account() {

		$response = array( 'success' => false );
		$zoho_api = Zgf_Api::get_instance();
		$auth_url = $zoho_api->get_auth_code_url();

		if ( ! $auth_url ) {
			$response['msg'] = esc_html__( 'Something went wrong! Check your credentials and authorize again', 'mwb-gf-integration-with-zoho-crm' );
			delete_option( 'mwb_is_crm_active', false );
			return $response;
		}

		$response = array(
			'success' => true,
			'url'     => $auth_url,
		);

		return $response;
	}


	/**
	 * Revoke account access.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function revoke_zgf_access() {

		$response = delete_option( 'mwb_is_crm_active' );

		if ( $response ) {
			return true;
		}
		return false;
	}


	/**
	 * Fetch zoho objects.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function fetch_zoho_modules() {
		$zoho_api = Zgf_Api::get_instance();
		return $zoho_api->get_modules_data();
	}

	/**
	 * Fetch object fields.
	 *
	 * @param array $data An array of ajax data.
	 * @since 1.0.0
	 * @return array
	 */
	public function fetch_module_fields( $data = array() ) {

		// Get zoho crm module fields.
		$module_data = array();
		$module      = ! empty( $data['object'] ) ? sanitize_text_field( wp_unslash( $data['object'] ) ) : '';
		$force       = ! empty( $data['force'] ) ? sanitize_text_field( wp_unslash( $data['force'] ) ) : false;
		$zoho_api    = Zgf_Api::get_instance();

		$module_data['crm_fields'] = $zoho_api->get_module_fields( $module, $force );

		$restricted_fields = array( 'Lead_Source', 'Lead_Status', 'Mobile', 'Fax', 'Phone', 'Home_Phone', 'Other_Phone', 'Asst_Phone', 'Record_Image', 'Tag' );

		if ( ! Mwb_Gf_Integration_With_Zoho_Crm_Helper::is_pro_license_active_or_on_trial() ) {
			foreach ( $module_data['crm_fields']['fields'] as $k => $v ) {
				if ( in_array( $v['api_name'], $restricted_fields, true ) ) {
					unset( $module_data['crm_fields']['fields'][ $k ] );
				}
			}
			$module_data['crm_fields']['fields'] = array_values( $module_data['crm_fields']['fields'] );
		}

		return $module_data;
	}

	/**
	 * Filter Feed List.
	 *
	 * @param array $data array of data.
	 * @return array
	 */
	public function filter_feed_list( $data = array() ) {

		$form_id = ! empty( $data['form_id'] ) ? sanitize_text_field( wp_unslash( $data['form_id'] ) ) : '';

		$form       = GFAPI::get_form( $form_id );
		$form_title = $form['title'];
		update_option( 'mwb_zgf_filter_feed_acc_to_form', $form_id );

		return array(
			'title' => $form_title,
			'id'    => $form_id,
		);
	}

	/**
	 * Fetch form fields.
	 *
	 * @param array $data An array of ajax posted data.
	 * @since 1.0.0
	 * @return array
	 */
	public function fetch_form_fields( $data = array() ) {

		$form_data = array();
		$form_id   = ! empty( $data['form_id'] ) ? sanitize_text_field( wp_unslash( $data['form_id'] ) ) : '';

		$form        = GFAPI::get_form( $form_id );
		$form_fields = $form['fields'];

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

		$form_data = array(
			'Gravity Form Fields' => $form_data,
		);

		$settings['field_type'] = array(
			'label'   => esc_html__( 'Field Type', 'mwb-gf-integration-with-zoho-crm' ),
			'options' => array(
				'standard_field' => esc_html__( 'Standard Fields', 'mwb-gf-integration-with-zoho-crm' ),
				'custom_value'   => esc_html__( 'Custom Value', 'mwb-gf-integration-with-zoho-crm' ),
			),
		);

		$settings['field_value'] = array(
			'label'   => esc_html__( 'Field Value', 'mwb-gf-integration-with-zoho-crm' ),
			'options' => $form_data,
		);

		$settings['custom_value'] = array(
			'label' => esc_html__( 'Custom Value', 'mwb-gf-integration-with-zoho-crm' ),
		);

		return $settings;
	}


	/**
	 * Toggle feed status.
	 *
	 * @param array $data An array of ajax posted data.
	 * @since 1.0.0
	 * @return bool
	 */
	public function toggle_feed_status( $data = array() ) {

		$feed_id  = ! empty( $data['feed_id'] ) ? sanitize_text_field( wp_unslash( $data['feed_id'] ) ) : '';
		$status   = ! empty( $data['status'] ) ? sanitize_text_field( wp_unslash( $data['status'] ) ) : '';
		$response = Mwb_Gf_Integration_With_Zoho_Crm_Helper::change_post_status( $feed_id, $status );
		return $response;
	}


	/**
	 * Trash feeds.
	 *
	 * @param array $data An array of ajax posted data.
	 * @since 1.0.0
	 */
	public function trash_feeds_from_list( $data = array() ) {

		$feed_id = ! empty( $data['feed_id'] ) ? sanitize_text_field( wp_unslash( $data['feed_id'] ) ) : '';
		$trash   = wp_trash_post( $feed_id );

		if ( $trash ) {
			return true;
		}
		return false;
	}


	/**
	 * Clear sync log.
	 *
	 * @param array $data An array of ajax posted data.
	 * @since 1.0.0
	 * @return array Response array.
	 */
	public function clear_sync_log( $data = array() ) {
		Mwb_Gf_Integration_With_Zoho_CRM_Admin::clear_log_truncate_table();
		return array( 'success' => true );
	}


	/**
	 * Download logs.
	 *
	 * @param array $data An arraay of ajax posted data.
	 * @since 1.0.0
	 * @return array Response array.
	 */
	public function download_sync_log( $data = array() ) {

		global $wpdb;
		$table_name     = $wpdb->prefix . 'mwb_zgf_log';
		$log_data_query = "SELECT * FROM {$table_name} ORDER BY `id` DESC"; // @codingStandardsIgnoreLine
		$log_data       = $wpdb->get_results( $log_data_query, ARRAY_A ); // @codingStandardsIgnoreLine
		$path           = Mwb_Gf_Integration_With_Zoho_Crm_Helper::create_log_folder( 'zoho-gf-logs' );
		$log_dir        = $path . '/mwb-zgf-sync-log.log';

		$log = '';

		foreach ( $log_data as $key => $value ) {
			$value['zoho_id'] = ! empty( $value['zoho_id'] ) ? $value['zoho_id'] : '-';

			$log .= 'FEED ID: ' . $value['feed_id'] . PHP_EOL;
			$log .= 'FEED : ' . $value['feed'] . PHP_EOL;
			$log .= 'ZOHO ID : ' . $value['zoho_id'] . PHP_EOL;
			$log .= 'ZOHO OBJECT : ' . $value['zoho_object'] . PHP_EOL;
			$log .= 'TIME : ' . gmdate( 'd-m-Y h:i A', esc_html( $value['time'] ) ) . PHP_EOL;
			$log .= 'REQUEST : ' . wp_json_encode( maybe_unserialize( $value['request'] ) ) . PHP_EOL;
			$log .= 'RESPONSE : ' . wp_json_encode( maybe_unserialize( $value['response'] ) ) . PHP_EOL;
			$log .= '------------------------------------' . PHP_EOL;
			file_put_contents( $log_dir, $log ); // @codingStandardsIgnoreLine
		}

		return array(
			'success'  => true,
			'redirect' => admin_url( '?mwb_download=1' ),
		);
	}


	/**
	 * Enable datatable.
	 *
	 * @param mixed $data An array of ajax posted data.
	 */
	public function get_datatable_data_cb( $data = array() ) {

		$request = $_GET; // @codingStandardsIgnoreLine
		$offset  = $request['start'];
		$limit   = $request['length'];

		global $wpdb;
		$table_name     = $wpdb->prefix . 'mwb_zgf_log';
		$log_data_query = $wpdb->prepare( "SELECT * FROM {$table_name} ORDER BY `id` DESC LIMIT %d OFFSET %d ", $limit, $offset ); // @codingStandardsIgnoreLine
		$log_data       = $wpdb->get_results( $log_data_query, ARRAY_A ); // @codingStandardsIgnoreLine
		$count_query    = "SELECT COUNT(*) as `total_count` FROM {$table_name}"; // @codingStandardsIgnoreLine
		$count_data     = $wpdb->get_col( $count_query ); // @codingStandardsIgnoreLine
		$total_count    = $count_data[0];
		$data           = array();

		foreach ( $log_data as $key => $value ) {

			$data_href = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_zoho_link( $value );
			if ( in_array( $value['zoho_object'], array( 'Note', 'Task' ) ) ) { // @codingStandardsIgnoreLine
				$link = $value['zoho_id'];
			} elseif ( ! empty( $data_href ) ) {
				$link = '<a href="' . $data_href . '" target="_blank">' . $value['zoho_id'] . '</a>';
			} else {
				$link = $value['zoho_id'];
			}

			$value['zoho_id'] = ! empty( $value['zoho_id'] ) ? $value['zoho_id'] : '-';

			$temp   = array(
				'<span class="dashicons dashicons-plus-alt"></span>',
				$value['feed'],
				$value['feed_id'],
				$value['zoho_object'],
				$link,
				$value['event'],
				gmdate( 'd-m-Y h:i A', esc_html( $value['time'] ) ),
				wp_json_encode( maybe_unserialize( $value['request'] ) ),
				wp_json_encode( maybe_unserialize( $value['response'] ) ),
			);
			$data[] = $temp;
		}

		$json_data = array(
			'draw'            => intval( $request['draw'] ),
			'recordsTotal'    => $total_count,
			'recordsFiltered' => $total_count,
			'data'            => $data,
		);

		wp_send_json( $json_data );
	}

}

