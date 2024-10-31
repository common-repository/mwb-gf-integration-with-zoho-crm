<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/public
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_Crm_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Insatance of the current fom fields
	 *
	 * @since 1.0.0
	 * @var   array An array of form fields data.
	 */
	public $form_fields;

	/**
	 * Framework instance.
	 *
	 * @var object $framework Stores the framework instance.
	 * @since 1.0.0
	 */
	public $framework;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name  The name of the plugin.
	 * @param    string $version      The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->framework = Mwb_Zgf_Connect_Framework::get_instance();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_With_Zoho_CRM_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_With_Zoho_CRM_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zoho-gf-integration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_With_Zoho_CRM_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_With_Zoho_CRM_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zoho-gf-integration-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Get entry of the form after submission
	 *
	 * @param array $entry Entry fields array.
	 * @param array $form  Form attributes array with field object.
	 * @return void
	 */
	public function get_form_entries( $entry, $form ) {

		$field_values = array();

		$entry = apply_filters( 'mwb_zgf_modify_entry_filter', $entry );

		do_action( 'mwb_zgf_use_entry_data', $entry, $form );

		foreach ( $form['fields'] as $field_obj ) {
			if ( ! empty( $field_obj->inputs ) && is_array( $field_obj->inputs ) && 'time' != $field_obj->type ) {   // @codingStandardsIgnoreLine
				foreach ( $field_obj->inputs as $field_options ) {

					$field_values[ $field_options['id'] ] = $entry[ $field_options['id'] ];
				}
			} else {

				$field_values[ $field_obj->id ] = $entry[ $field_obj->id ];
			}
		}

		$form_data = array(
			'id'     => $entry['form_id'],
			'name'   => $form['title'],
			'fields' => $form['fields'],
			'values' => $field_values,
		);

		$this->form_fields = $form['fields'];

		$this->send_to_crm( $form_data, $entry );

	}


	/**
	 * Send form data over crm(ZOHO).
	 *
	 * @param array $data An array of form data and entries.
	 * @param array $entry Array of entry submission.
	 *
	 * @since 1.0.0
	 */
	public function send_to_crm( $data = array(), $entry ) {

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		$active_feeds = $this->get_feeds_by_form_id( $data['id'] );
		$api          = Zgf_Api::get_instance();

		$filter_exist = false;
		if ( ! empty( $active_feeds ) && is_array( $active_feeds ) ) {
			foreach ( $active_feeds as $key => $feed_id ) {

				$filter_exist = $this->check_filter( $feed_id );
				$crm_object   = get_post_meta( $feed_id, 'mwb_zgf_object', true );
				$log_data     = array(
					'feed_id'     => $feed_id,
					'feed_name'   => get_the_title( $feed_id ),
					'zoho_object' => $crm_object,
				);

				$request = array();
				$result  = array();

				do_action( 'mwb_zgf_before_send_data_to_crm', $feed_id, $entry, $log_data, $data, $api );

				if ( ! empty( $filter_exist ) ) {

					$filtered = $this->validate_filter( $filter_exist, $data['values'] );

					if ( $filtered ) { // If filter results true, then send data to crm.

						$request = $this->framework->get_request( str_replace( '_', '', $crm_object ), $feed_id, $crm_object, $data['values'] );
						if ( false != get_option( 'mwb_zgf_zoho_token_data', false ) ) { // @codingStandardsIgnoreLine
							$result = $api->create_single_record( $crm_object, $request, false, $log_data );
							$this->check_error_for_mail( $result, $data );
						}
					} elseif ( is_array( $filtered ) && false == $filtered['result'] ) { // @codingStandardsIgnoreLine

						$request = $this->framework->get_request( str_replace( '_', '', $crm_object ), $feed_id, $crm_object, $data['values'] );
						if ( false != get_option( 'mwb_zgf_zoho_token_data', false ) ) { // @codingStandardsIgnoreLine
							$result = $api->create_single_record( $crm_object, $request, false, $log_data );
							$this->check_error_for_mail( $result, $data );
						}
					}
				} else {
					$request = $this->framework->get_request( str_replace( '_', '', $crm_object ), $feed_id, $crm_object, $data['values'] );
					if ( false != get_option( 'mwb_zgf_zoho_token_data', false ) ) { // @codingStandardsIgnoreLine
						$result = $api->create_single_record( $crm_object, $request, false, $log_data );
						$this->check_error_for_mail( $result, $data );
					}
				}
				if ( ! empty( $result ) && ! empty( $request ) ) {

					do_action( 'mwb_zgf_after_send_data_to_crm', $feed_id, $entry, $request, $result, $log_data, $data );
				}
			}
		}
	}

	/**
	 * Validate form entries with feeds filter conditions.
	 *
	 * @param array $filters An array of filter data.
	 * @param array $data    Form data.
	 * @since 1.0.0
	 * @return bool
	 */
	public function validate_filter( $filters = array(), $data = array() ) {

		if ( ! empty( $filters ) && is_array( $filters ) ) {

			foreach ( $filters as $or_key => $or_filters ) {
				$result = true;
				if ( is_array( $or_filters ) ) {

					foreach ( $or_filters as $and_key => $and_filter ) {
						if ( '-1' == $and_filter['field'] || '-1' == $and_filter['option'] ) { // @codingStandardsIgnoreLine
							return array( 'result' => false );
						}
						$form_field = $and_filter['field'];
						$feed_value = isset( $and_filter['value'] ) ? $and_filter['value'] : '';
						$entry_val  = $this->get_entry_values( $form_field, $data );
						$result     = $this->is_value_allowed( $and_filter['option'], $feed_value, $entry_val );

						if ( false == $result ) { // @codingStandardsIgnoreLine
							break;
						}
					}
				}

				if ( true === $result ) {
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Verify and get entered field values.
	 *
	 * @param string $field   Form field whose value to verify.
	 * @param array  $entries An array of form entries.
	 * @since 1.0.0
	 * @return mixed value of the field
	 */
	public function get_entry_values( $field, $entries ) {

		$value = false;

		$form_fields = $this->form_fields;
		$field_type  = isset( $form_fields[ $field ]['type'] ) ? $form_fields[ $field ]['type'] : '';

		if ( ! empty( $field ) && ! empty( $entries ) && is_array( $entries ) ) {

			if ( isset( $entries[ $field ] ) ) {
				$value = $entries[ $field ];
			}
		}

		$discard_fields = array( 'list' );

		$discard_fields = apply_filters( 'mwb_zgf_discard_fields_to_zoho', $discard_fields, $field, $entries );

		if ( ! empty( $value ) && in_array( $field_type, $discard_fields ) ) { // @codingStandardsIgnoreLine
			$value = false;
		}

		return $value;
	}


	/**
	 * Get all feeds of a respective form id.
	 *
	 * @param int $form_id Form id.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_feeds_by_form_id( $form_id = '' ) {

		if ( empty( $form_id ) ) {
			return;
		}

		// Get all feeds.
		$active_feeds = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'mwb_zoho_feeds',
				'post_staus'  => 'publish',
				'order'       => 'DESC',
				'meta_query'  => array( // @codingStandardsIgnoreLine
					array(
						'relation' => 'AND',
						array(
							'key'     => 'mwb_zgf_form',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'mwb_zgf_form',
							'value'   => $form_id,
							'compare' => '==',
						),
					),
				),
			)
		);

		return $active_feeds;
	}

	/**
	 * Check if filter exists in feed.
	 *
	 * @param int $feed_id Feed ID.
	 * @since 1.0.0
	 * @return bool|array
	 */
	public function check_filter( $feed_id = '' ) {

		if ( empty( $feed_id ) ) {
			return;
		}
		if ( metadata_exists( 'post', $feed_id, 'mwb_zgf_condtion_field' ) ) {
			$meta = get_post_meta( $feed_id, 'mwb_zgf_condtion_field', true );

			if ( ! empty( $meta ) && is_array( $meta ) && count( $meta ) > 0 ) {
				return $meta;
			}
		}

		return false;
	}

	/**
	 * Validate form values with conditions.
	 *
	 * @param String $option_type Filter conditon type.
	 * @param String $feed_value  Value to compare with entry value.
	 * @param String $form_value  Entry value .
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_value_allowed( $option_type = false, $feed_value = false, $form_value = false ) {

		if ( false == $option_type || false == $form_value ) { // @codingStandardsIgnoreLine
			return;
		}

		$time   = current_time( 'timestamp' ); // @codingStandardsIgnoreLine
		$result = false;
		if ( false != $form_value ) { // @codingStandardsIgnoreLine

			switch ( $option_type ) {

				case 'exact_match':
					if ( $feed_value === $form_value ) { // @codingStandardsIgnoreLine
						$result = true;
					}
					break;

				case 'no_exact_match':
					if ( $feed_value !== $form_value ) { // @codingStandardsIgnoreLine
						$result = true;
					}
					break;

				case 'contains':
					if ( false !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_contains':
					if ( false === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'exist':
					if ( false !== strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_exist':
					if ( false === strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'starts':
					if ( 0 === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_starts':
					if ( 0 !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'ends':
					if ( strlen( $form_value ) == strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // @codingStandardsIgnoreLine
						$result = true;
					}
					break;

				case 'not_ends':
					if ( strlen( $form_value ) != strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // @codingStandardsIgnoreLine
						$result = true;
					}
					break;

				case 'less_than':
					if ( (float) $form_value < (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'greater_than':
					if ( (float) $form_value > (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'less_than_date':
					if ( strtotime( $form_value, $time ) < strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'greater_than_date':
					if ( strtotime( $form_value, $time ) > strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'equal_date':
					if ( strtotime( $form_value, $time ) == strtotime( $feed_value, $time ) ) { // @codingStandardsIgnoreLine
						$result = true;
					}
					break;

				case 'empty':
					if ( empty( $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_empty':
					if ( ! empty( $form_value ) ) {
						$result = true;
					}
					break;

				default:
					$result = false;
					break;
			}
		}
		return $result;
	}


	/**
	 * Check if error occurs in response and send mail.
	 *
	 * @param array $response Api response.
	 * @param array $data     Form data and entries.
	 * @since 1.0.0
	 */
	public function check_error_for_mail( $response = array(), $data = array() ) {

		if ( ! is_array( $response ) && ! is_array( $data ) ) {
			return;
		}

		if ( isset( $response['data']['status'] ) && 'error' == $response['data']['status'] ) { // @codingStandardsIgnoreLine
			if ( null !== Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'notif' ) ) {
				$this->send_email( $response, $data );
			}
		} elseif ( isset( $response['data'] ) ) {
			foreach ( $response as $_data => $value ) {
				foreach ( $value as $meta ) {
					if ( isset( $meta['status'] ) && 'error' == $meta['status'] ) { // @codingStandardsIgnoreLine
						if ( null !== Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'notif' ) ) {
							$this->send_email( $meta, $data );
						}
					} elseif ( isset( $meta['code'] ) && 'SUCCESS' == $meta['code'] ) { // @codingStandardsIgnoreLine
						$count = get_option( 'mwb_zgf_synced_forms_count', 0 );
						update_option( 'mwb_zgf_synced_forms_count', $count + 1 );
					}
				}
			}
		}
	}

	/**
	 * Send email on error
	 *
	 * @param mixed $info Info to send.
	 * @param array $data An array of form data and entries.
	 * @since 1.0.0
	 * @return void
	 */
	public function send_email( $info, $data ) {

		if ( ! empty( $info ) && is_array( $info ) ) {

			$to        = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'email' );
			$from_name = get_bloginfo( 'name' );
			$subject   = esc_html__( 'Error While Posting form data over Zoho', 'mwb-gf-integration-with-zoho-crm' );
			$logs_link = add_query_arg(
				array(
					'page' => 'mwb_zoho_gf',
					'tab'  => 'logs',
				),
				admin_url( 'admin.php' )
			);

			$detail = array(
				'Form Title' => $data['name'],
				'Form ID'    => $data['id'],
				'Time'       => gmdate( 'd-M-y H:i:s', current_time( 'timestamp' ) ), // @codingStandardsIgnoreLine
				'Logs'       => '<a href="' . $logs_link . '" target="_blank" style="word-break:break-all;">' . esc_html__( 'Go to logs', 'mwb-gf-integration-with-zoho-crm' ) . '</a>',
			);

			if ( isset( $info['data']['message'] ) && isset( $info['data']['details'] ) ) {
				$info_msg    = $info['data']['message'];
				$info_detail = is_array( $info['data']['details'] ) ? wp_json_encode( $info['data']['details'] ) : $info['data']['details'];
			} elseif ( isset( $info['message'] ) && isset( $info['details'] ) ) {
				$info_msg    = $info['message'];
				$info_detail = is_array( $info['details'] ) ? wp_json_encode( $info['details'] ) : $info['details'];
			}

			$email_data = array(
				'Title'          => esc_html__( 'Zoho error', 'mwb-gf-integration-with-zoho-crm' ),
				'Code'           => ! empty( $info['code'] ) ? $info['code'] : '',
				'CRM message'    => ! empty( $info['message'] ) ? $info['message'] : '',
				'Error message ' => $info_msg,
				'Details'        => $info_detail,
				'More details'   => $detail,
			);

			$email_body = $this->get_email_body( $email_data );

			wp_mail(
				! empty( $to ) ? $to : '',
				$subject,
				$email_body,
				array(
					'Content-Type: text/html; charset=UTF-8',
					'From: ' . $from_name,
				)
			);

		}

	}


	/**
	 * Returns email body to be sent as email.
	 *
	 * @param  array $data An array of information to be sent as email.
	 * @since 1.0.0
	 * @return string email body
	 */
	public function get_email_body( $data ) {

		if ( ! empty( $data ) && is_array( $data ) ) {
			ob_start();
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta http-equiv="Content-Type" content="text/html charset=UTF-8" >
				<title><?php echo esc_html( ! empty( $data['title'] ) ? $data['title'] : '' ); ?></title>
			</head>
			<body>
				<table>
					<tr>
						<td style="font-family: sans-serif;background-color: #1f1f1f; height: 36px; color: #fff; font-size: 24px; padding: 0px 10px"><?php echo esc_html( $data['Title'] ); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%;">
								<tbody>    
									<?php foreach ( $data as $key => $value ) : ?>
										<?php if ( is_array( $value ) ) { ?>
											<?php foreach ( $value as $k => $v ) : ?>
												<tr>
													<td style="padding-top: 10px;color: #303030;font-family: Helvetica;font-size: 13px;line-height: 150%;text-align: right; font-weight: bold; width: 28%; padding-right: 10px;"><?php echo esc_html( $k ); ?></td>
													<td style="padding-top: 10px;color: #303030;font-family: Helvetica;font-size: 13px;line-height: 150%;text-align: left; word-break:break-all;"><?php echo $v; // @codingStandardsIgnoreLine ?></td>
												</tr>   
											<?php endforeach; ?>	
										<?php } else { ?>
											<tr>
												<td style="padding-top: 10px;color: #303030;font-family: Helvetica;font-size: 13px;line-height: 150%;text-align: right; font-weight: bold; width: 28%; padding-right: 10px;"><?php echo esc_html( $key ); ?></td>
												<td style="padding-top: 10px;color: #303030;font-family: Helvetica;font-size: 13px;line-height: 150%;text-align: left; word-break:break-all;"><?php echo esc_html( $value ); ?></td>
											</tr> 
										<?php } ?>    
									<?php endforeach; ?>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</body>
			</html>
			<?php
		}
		return ob_get_clean();
	}

}
