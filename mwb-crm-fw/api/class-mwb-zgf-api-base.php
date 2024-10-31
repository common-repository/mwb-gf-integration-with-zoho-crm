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
class MWB_Zgf_Api_Base {

	/**
	 * Base usrl of the api
	 *
	 * @var string $base_url
	 * @since 1.0.0
	 */
	public $base_url;

	/**
	 * Get Request.
	 *
	 * @param string $endpoint Api endpoint of mautic.
	 * @param array  $data Data to be used in request.
	 * @param array  $headers header to be used in request.
	 */
	public function get( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'GET', $endpoint, $data, $headers );
	}

	/**
	 * Post Request.
	 *
	 * @param string $endpoint Api endpoint of mautic.
	 * @param array  $data Data to be used in request.
	 * @param array  $headers header to be used in request.
	 */
	public function post( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'POST', $endpoint, $data, $headers );
	}

	/**
	 * Put Request.
	 *
	 * @param string $endpoint Api endpoint of mautic.
	 * @param array  $data Data to be used in request.
	 * @param array  $headers header to be used in request.
	 */
	public function put( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'PUT', $endpoint, $data, $headers );
	}

	/**
	 * Delete Request.
	 *
	 * @param string $endpoint Api endpoint of mautic.
	 * @param array  $data Data to be used in request.
	 * @param array  $headers header to be used in request.
	 */
	public function delete( $endpoint, $data = array(), $headers = array() ) {
		return $this->request( 'DELETE', $endpoint, $data, $headers );
	}


	/**
	 * Send api request
	 *
	 * @param string $method   HTTP method.
	 * @param string $endpoint Api endpoint.
	 * @param array  $data     Request data.
	 * @param array  $headers header to be used in request.
	 */
	private function request( $method, $endpoint, $data = array(), $headers = array() ) {

		$method  = strtoupper( trim( $method ) );
		$url     = $this->base_url . $endpoint;
		$headers = array_merge( $headers, $this->get_headers() );
		$args    = array(
			'method'    => $method,
			'headers'   => $headers,
			'timeout'   => 20,
			'sslverify' => apply_filters( 'mwb_zgf_use_sslverify', true ),
		);
		if ( ! empty( $data ) ) {
			if ( in_array( $method, array( 'GET', 'DELETE' ), true ) ) {
				$url = add_query_arg( $data, $url );
			} else {
				$args['body'] = $data;
			}
		}

		$args     = apply_filters( 'mwb_zgf_http_request_args', $args, $url );
		$response = wp_remote_request( $url, $args );

		// Add better exception handling.
		try {
			$data = $this->parse_response( $response );
		} catch ( Exception $e ) { // @codingStandardsIgnoreLine
			$data = $e->getMessage();
		}

		return $data;
	}


	/**
	 * Parse crm response.
	 *
	 * @param mixed $response Crm response.
	 * @since 1.0.0
	 * @throws Exception Throws exception on error.
	 * @return array
	 */
	protected function parse_response( $response ) {

		if ( $response instanceof WP_Error ) {
			throw new Exception( 'Error', 0 );
		}
		$code    = (int) wp_remote_retrieve_response_code( $response );
		$message = wp_remote_retrieve_response_message( $response );
		$body    = wp_remote_retrieve_body( $response );
		$data    = json_decode( $body, ARRAY_A );
		return compact( 'code', 'message', 'data' );
	}

	/**
	 * Return headers.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_headers() {
		return array();
	}
}
