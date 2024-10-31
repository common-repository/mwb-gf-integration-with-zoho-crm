<?php
/**
 * Base Api Class
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/mwb-crm-fw/api
 */

/**
 * Base Api Class.
 *
 * This class defines all code necessary api communication.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/mwb-crm-fw/api
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Zgf_Api extends MWB_Zgf_Api_Base {

	/**
	 * Access token data.
	 *
	 * @var string Stores access token
	 * @since 1.0.0
	 */
	private static $access_token;

	/**
	 * Refresh token data
	 *
	 * @var string Stores refresh token
	 * @since 1.0.0
	 */
	private static $refresh_token;

	/**
	 * Access url
	 *
	 * @var string Stores access url.
	 * @since 1.0.0
	 */
	private static $acc_url;

	/**
	 * Api domain data
	 *
	 * @var string Stores api domain.
	 * @since 1.0.0
	 */
	private static $api_domain;

	/**
	 * Access token expiry data
	 *
	 * @var integer Stores access token expiry data.
	 * @since 1.0.0
	 */
	private static $expiry;

	/**
	 * Creates an instance of the class
	 *
	 * @var object $_instance An instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null; // @codingStandardsIgnoreLine

	/**
	 * Main Zoho_Api Instance.
	 *
	 * Ensures only one instance of Zoho_Api is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Zgf_Api - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self();
			self::initialize();
		}

		return self::$_instance;
	}

	/**
	 * Initalize the api class.
	 *
	 * @param array $token_data An array of access token data.
	 * @since 1.0.0
	 * @return void
	 */
	protected static function initialize( $token_data = array() ) {

		if ( empty( $token_data ) ) {
			$token_data = get_option( 'mwb_zgf_zoho_token_data', array() );
		}

		self::$access_token  = isset( $token_data['access_token'] ) ? $token_data['access_token'] : '';
		self::$refresh_token = isset( $token_data['refresh_token'] ) ? $token_data['refresh_token'] : '';
		self::$expiry        = isset( $token_data['expiry'] ) ? $token_data['expiry'] : '';
		self::$api_domain    = isset( $token_data['api_domain'] ) ? $token_data['api_domain'] : '';

	}

	/**
	 * Get api domain
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_api_domain() {
		return self::$api_domain;
	}

	/**
	 * Get access token
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_access_token() {
		return self::$access_token;
	}

	/**
	 * Get refresh token
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_refresh_token() {
		return self::$refresh_token;
	}

	/**
	 * Retrieve access token expiry
	 *
	 * @since 1.0.0
	 * @return integer
	 */
	public function get_access_token_expiry() {
		return self::$expiry;
	}

	/**
	 * Access token validation
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_access_token_valid() {
		return ( self::$expiry > time() );
	}


	/**
	 * Renew access token.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function renew_access_token() {

		$endpoint       = '/oauth/v2/token';
		$client_id      = get_option( 'mwb-zgf-client-id', false );
		$client_secret  = get_option( 'mwb-zgf-secret-id', false );
		$domain         = get_option( 'mwb-zgf-domain', 'in' );
		$acc_url        = 'https://accounts.zoho.' . $domain;
		$refresh_token  = $this->get_refresh_token();
		$params         = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'refresh_token' => $refresh_token,
		);
		$this->base_url = $acc_url;
		$response       = $this->post( $endpoint, $params );
		$this->log_request( __FUNCTION__, $endpoint, $params, $response );
		if ( 200 == $response['code'] && $this->check_response_error( $response ) ) { // @codingStandardsIgnoreLine
			$this->save_token_data( $response['data'] );
			return true;
		}
		return false;
	}

	/**
	 * Save token data in an option.
	 *
	 * @param  array $token_data An array of token data to save.
	 * @since 1.0.0
	 */
	public function save_token_data( $token_data ) {
		$old_token_data = get_option( 'mwb_zgf_zoho_token_data' );
		foreach ( $token_data as $key => $value ) {
			$old_token_data[ $key ] = $value;
			if ( 'expires_in' == $key ) { // @codingStandardsIgnoreLine
				$old_token_data['expiry'] = time() + $value;
			}
		}
		$this->initialize( $old_token_data );
		update_option( 'mwb_zgf_zoho_token_data', $old_token_data );
	}

	/**
	 * Get referesh token data form zoho.
	 *
	 * @param mixed $code Status code.
	 * @since 1.0.0
	 * @return bool
	 */
	public function get_refresh_token_data( $code ) {

		$endpoint       = '/oauth/v2/token';
		$client_id      = get_option( 'mwb-zgf-client-id', false );
		$client_secret  = get_option( 'mwb-zgf-secret-id', false );
		$domain         = get_option( 'mwb-zgf-domain', 'in' );
		$acc_url        = 'https://accounts.zoho.' . $domain;
		$redirect_uri   = admin_url();
		$params         = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'redirect_uri'  => rtrim( $redirect_uri, '/' ),
			'code'          => $code,
		);
		$this->base_url = $acc_url;
		$response       = $this->post( $endpoint, $params );
		$this->log_request( __FUNCTION__, $endpoint, $params, $response );
		if ( 200 == $response['code'] && $this->check_response_error( $response ) ) { // @codingStandardsIgnoreLine
			$this->save_token_data( $response['data'] );
			return true;
		}
		return false;
	}

	/**
	 * Get account authorization URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_auth_code_url() {

		$client_id     = get_option( 'mwb-zgf-client-id', false );
		$client_secret = get_option( 'mwb-zgf-secret-id', false );
		$domain        = get_option( 'mwb-zgf-domain', 'in' );

		if ( ! $client_id || ! $client_secret || ! $domain ) {
			return false;
		}

		$redirect_uri = admin_url();
		$acc_url      = 'https://accounts.zoho.' . $domain . '/oauth/v2/auth';
		$auth_params  = array(
			'scope'         => apply_filters( 'mwb_zgf_api_scope', 'ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,ZohoCRM.users.Read,ZohoCRM.coql.READ,ZohoFiles.files.ALL,ZohoCRM.bulk.ALL,ZohoCRM.org.READ' ),
			'client_id'     => $client_id,
			'response_type' => 'code',
			'access_type'   => 'offline',
			'redirect_uri'  => rtrim( $redirect_uri, '/' ),
		);

		$auth_url = add_query_arg( $auth_params, $acc_url );
		return $auth_url;
	}

	/**
	 * Get auth header data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_auth_header() {
		$headers = array(
			'Authorization' => sprintf( 'Zoho-oauthtoken %s', $this->get_access_token() ),
		);
		return $headers;
	}

	/**
	 * Get selected module fields.
	 *
	 * @param array $module An array of module data.
	 * @param bool  $force  True if refresh fields.
	 * @return array
	 */
	public function get_module_fields( $module, $force = false ) {

		$data = array();

		// Get new access token if current token is expired.
		if ( ! $this->is_access_token_valid() ) {
			$this->renew_access_token();
		}

		$data = get_transient( 'mwb_zgf_' . $module . '_fields' );
		if ( ! $force && false !== ( $data ) ) { // @codingStandardsIgnoreLine
			return $data;
		}

		$response = $this->get_fields( $module );

		if ( $this->is_success( $response ) ) {
			$data = $response['data'];
			set_transient( 'mwb_zgf_' . $module . '_fields', $data );
		}

		return $data;
	}

	/**
	 * Get all zoho modules data.
	 *
	 * @param bool $force Whether to get data from api, or from db.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_modules_data( $force = false ) {

		$data = array();
		if ( ! $this->is_access_token_valid() ) {
			$this->renew_access_token();
		}
		$data = get_transient( 'mwb_zgf_modules_data' );
		if ( ! $force && false !== ( $data ) ) { // @codingStandardsIgnoreLine
			return $data;
		}
		$response = $this->get_modules();
		if ( $this->is_success( $response ) ) {
			$data = $response['data'];
			set_transient( 'mwb_zgf_modules_data', $data );
		}
		return $data;
	}


	/**
	 * Get records data
	 *
	 * @param  string  $module Crm object.
	 * @param  boolean $force  Fetch from api.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_records_data( $module, $force = false ) {
		$data = array();
		if ( ! $this->is_access_token_valid() ) {
			$this->renew_access_token();
		}

		$data = get_transient( 'mwb_zgf_' . $module . '_data' );
		if ( ! $force && false !== ( $data ) ) { // @codingStandardsIgnoreLine
			return $data;
		}

		$response = $this->get_records( $module );
		if ( $this->is_success( $response ) ) {
			$data = $response['data'];
			set_transient( 'mwb_zgf_' . $module . '_data', $data );
		}

		return $data;
	}

	/**
	 * Get single record data.
	 *
	 * @param string $module    Crm object.
	 * @param int    $record_id Record ID.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_single_record_data( $module, $record_id ) {
		$data = array();
		if ( ! $this->is_access_token_valid() ) {
			$this->renew_access_token();
		}
		$response = $this->get_single_record( $module, $record_id );
		if ( $this->is_success( $response ) ) {
			$data = $response['data'];
		}
		return $data;
	}

	/**
	 * Create single record.
	 *
	 * @param string $module      Crm object.
	 * @param array  $record_data An array of data to be sent over crm.
	 * @param bool   $is_bulk     Whether to send bulk data.
	 * @param array  $log_data    An array of data to log.
	 * @since 1.0.0
	 * @return array
	 */
	public function create_single_record( $module, $record_data, $is_bulk = false, $log_data = array() ) {
		$data = array();
		if ( ! $this->is_access_token_valid() ) {
			$this->renew_access_token();
		}
		$response = $this->create_or_update_record( $module, $record_data, $is_bulk, $log_data );

		if ( $this->is_success( $response ) ) {

			$data   = $response['data'];
			$action = isset( $data['data'][0]['action'] ) ? $data['data'][0]['action'] : '';

			if ( ! empty( $action ) && 'insert' == $action ) {    // @codingStandardsIgnoreLine
				$synced_form_count = get_option( 'mwb_zgf_synced_form_count', 0 );
				$synced_form_count++;
				update_option( 'mwb_zgf_synced_form_count', $synced_form_count );
			}
		} else {
			$data = $response;
		}
		return $data;
	}

	/**
	 * Create or update a record.
	 *
	 * @param string $module      Crm object.
	 * @param array  $record_data An array of data to be sent to zoho.
	 * @param bool   $is_bulk     Whether to send bulk data.
	 * @param array  $log_data    An array of data to log.
	 * @since 1.0.0
	 * @return array
	 */
	protected function create_or_update_record( $module, $record_data, $is_bulk, $log_data ) {

		$feed_id        = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;
		$this->base_url = $this->get_api_domain();
		$endpoint       = '/crm/v2/' . $module . '/upsert';

		$record_data = apply_filters( 'mwb_zgf_alter_record_data', $record_data );

		if ( true == $is_bulk ) { // @codingStandardsIgnoreLine
			$request['data'] = $record_data;
		} else {
			$request['data'] = array( $record_data );
		}

		if ( $feed_id ) {
			$duplicate_check_fields = get_post_meta( $feed_id, 'mwb_zgf_primary_field' );
			if ( ! empty( $duplicate_check_fields ) ) {
				$request['duplicate_check_fields'] = $duplicate_check_fields;
			}
		}

		$request_data = wp_json_encode( $request );
		$headers      = $this->get_auth_header();
		$response     = $this->post( $endpoint, $request_data, $headers );

		$this->log_request( __FUNCTION__, $endpoint, $request, $response, $is_bulk );
		$this->log_request_in_db( __FUNCTION__, $module, $request, $response, $log_data, $is_bulk );
		return $response;

	}

	/**
	 * Retrieve object ID from crm response.
	 *
	 * @param array $response An array of response data from crm.
	 * @since 1.0.0
	 * @return integer
	 */
	protected function get_object_id_from_response( $response ) {
		$id = '-';
		if ( isset( $response['data'] ) && isset( $response['data']['data'] ) ) {
			$data = $response['data']['data'];
			if ( isset( $data[0] ) && isset( $data[0]['details'] ) ) {
				return ! empty( $data[0]['details']['id'] ) ? $data[0]['details']['id'] : $id;
			}
		}
		return $id;
	}

	/**
	 * Insert log data in db.
	 *
	 * @param string $event       Trigger event/ Feed .
	 * @param string $zoho_object Name of zoho module.
	 * @param array  $request     An array of request data.
	 * @param array  $response    An array of response data.
	 * @param array  $log_data    Data to log.
	 * @param bool   $is_bulk  True if the data is in bulk.
	 *
	 * @return void
	 */
	protected function log_request_in_db( $event, $zoho_object, $request, $response, $log_data, $is_bulk = false ) {

		$zoho_object = ! empty( $log_data['zoho_object'] ) ? $log_data['zoho_object'] : false;
		$feed        = ! empty( $log_data['feed_name'] ) ? $log_data['feed_name'] : false;
		$feed_id     = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;
		$time        = time();

		if ( true != $is_bulk ) {   // @codingStandardsIgnoreLine

			$zoho_id = $this->get_object_id_from_response( $response );

			$request  = serialize( $request ); // @codingStandardsIgnoreLine
			$response = serialize( $response ); // @codingStandardsIgnoreLine

			$event = ! empty( $event ) ? $event : false;

			$log_data = compact( 'event', 'zoho_object', 'request', 'response', 'zoho_id', 'feed_id', 'feed', 'time' );
			$this->insert_log_data( $log_data );
		} else {
			$_request  = $request['data'];
			$_response = $response;
			if ( is_array( $_request ) && count( $_request ) > 1 ) {
				foreach ( $_request as $k => $v ) {
					$zoho_id = ! empty( $_response['data']['data'][ $k ]['details']['id'] ) ? $_response['data']['data'][ $k ]['details']['id'] : '';

					$request  = serialize( $v );                                     // @codingStandardsIgnoreLine
					$response = serialize( $_response['data']['data'][ $k ] );       // @codingStandardsIgnoreLine

					$event = ! empty( $event ) ? $event : false;

					$log_data = compact( 'event', 'zoho_object', 'request', 'response', 'zoho_id', 'feed_id', 'feed', 'time' );
					$this->insert_log_data( $log_data );
				}
			}
		}
	}

	/**
	 * Insert data to db.
	 *
	 * @param array $data Data to log.
	 * @since 1.0.0
	 * @return void
	 */
	private function insert_log_data( $data ) {

		if ( ! Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'logs' ) ) {
			return;
		}

		global $wpdb;
		$table    = $wpdb->prefix . 'mwb_zgf_log';
		$response = $wpdb->insert( $table, $data ); // @codingStandardsIgnoreLine
	}

	/**
	 * Create log of all the api responses.
	 *
	 * @param string $event    Trigger event.
	 * @param string $endpoint Endpoint to interact with.
	 * @param array  $request  Requested data.
	 * @param array  $response Response data.
	 * @param bool   $is_bulk  True if the data is in bulk.
	 * @since 1.0.0
	 */
	protected function log_request( $event, $endpoint, $request, $response, $is_bulk = false ) {
		$url = $this->base_url . $endpoint;

		$path = Mwb_Gf_Integration_With_Zoho_Crm_Helper::create_log_folder( 'zoho-gf-logs' );
		$file = $path . '/mwb-zgf-' . gmdate( 'Y-m-d' ) . '.log';

		if ( true != $is_bulk ) {      // @codingStandardsIgnoreLine

			$log  = 'Url : ' . $url . PHP_EOL;
			$log .= 'Method : ' . $event . PHP_EOL;
			$log .= 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL;
			$log .= 'Request : ' . wp_json_encode( $request ) . PHP_EOL;
			$log .= 'Response : ' . wp_json_encode( $response ) . PHP_EOL;
			$log .= '------------------------------------' . PHP_EOL;

		} else {

			if ( is_array( $request['data'] ) && count( $request['data'] ) > 1 ) {
				foreach ( $request['data'] as $k => $v ) {
					$log  = 'Url : ' . $url . PHP_EOL;
					$log .= 'Method : ' . $event . PHP_EOL;
					$log .= 'Time: ' . current_time( 'F j, Y  g:i a' ) . PHP_EOL;
					$log .= 'Request : ' . wp_json_encode( $v ) . PHP_EOL;
					$log .= 'Response : ' . wp_json_encode( $response['data']['data'][ $k ] ) . PHP_EOL;
					$log .= '------------------------------------' . PHP_EOL;
				}
			}
		}

		file_put_contents( $file, $log, FILE_APPEND ); // @codingStandardsIgnoreLine

	}

	/**
	 * Create record over crm
	 *
	 * @param string $module      Crm object.
	 * @param array  $record_data An array of data to be sent over crm.
	 * @since 1.0.0
	 * @return array
	 */
	private function create_record( $module, $record_data ) {
		$this->base_url  = $this->get_api_domain();
		$endpoint        = '/crm/v2/' . $module;
		$request['data'] = array( $record_data );
		$request_data    = wp_json_encode( $request );
		$headers         = $this->get_auth_header();
		return $this->post( $endpoint, $request_data, $headers );
	}

	/**
	 * Retrieve records of an object.
	 *
	 * @param string $module Crm object.
	 * @since 1.0.0
	 * @return array
	 */
	private function get_records( $module ) {
		$this->base_url = $this->get_api_domain();
		$endpoint       = '/crm/v2/' . $module;
		$data           = array();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, $data, $headers );
		$this->log_request( __FUNCTION__, $endpoint, $data, $response );
		return $response;
	}

	/**
	 * Retrieve single record.
	 *
	 * @param string $module    Crm object.
	 * @param int    $record_id Record ID.
	 * @since 1.0.0
	 * @return array
	 */
	private function get_single_record( $module, $record_id ) {
		$this->base_url = $this->get_api_domain();
		$endpoint       = '/crm/v2/' . $module . '/' . $record_id;
		$data           = array();
		$headers        = $this->get_auth_header();
		return $this->get( $endpoint, $data, $headers );
	}

	/**
	 * Get modules from zoho.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_modules() {
		$this->base_url = $this->get_api_domain();
		$endpoint       = '/crm/v2/settings/modules';
		$data           = array();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, $data, $headers );
		$this->log_request( __FUNCTION__, $endpoint, $data, $response );
		return $response;
	}

	/**
	 * Get fields
	 *
	 * @param string $module Zoho module.
	 * @return array
	 * @since 1.0.0
	 */
	private function get_fields( $module ) {

		$this->base_url = $this->get_api_domain();
		$endpoint       = '/crm/v2/settings/fields';
		$data           = array( 'module' => $module );
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, $data, $headers );
		$this->log_request( __FUNCTION__, $endpoint, $data, $response );

		return $response;
	}

	/**
	 * Check if response is a success response.
	 *
	 * @param array $response An array of response data.
	 * @since 1.0.0
	 * @return bool
	 */
	protected function is_success( $response ) {
		if ( isset( $response['code'] ) ) {
			return in_array( $response['code'], array( 200, 201, 204, 202 ) ); // @codingStandardsIgnoreLine
		}
		return false;
	}

	/**
	 * Validate error response.
	 *
	 * @param array $response An array of response data.
	 * @return bool
	 * @since 1.0.0
	 */
	private function check_response_error( $response ) {

		if ( ! empty( $response['data'] ) ) {
			if ( ! empty( $response['data']['error'] ) ) {
				return false;
			} else {
				return true;
			}
		}
		return false;
	}

}
