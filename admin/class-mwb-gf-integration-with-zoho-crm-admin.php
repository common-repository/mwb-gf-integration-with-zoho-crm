<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/admin
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_CRM_Admin {

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
	 * Creating Instance of the Zoho Api class.
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public $zoho_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param string $plugin_name       The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->zoho_api = Zgf_Api::get_instance();
	}

	/**
	 * Register the stylesheets for the admin area.
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
		if ( $this->is_valid_screen() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zoho-gf-integration-admin.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-tooltip', plugin_dir_url( __FILE__ ) . 'css/tooltip.css', array(), $this->version, 'all' );

		}
	}

	/**
	 * Register the JavaScript for the admin area.
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
		if ( $this->is_valid_screen() ) {
			wp_enqueue_script( $this->plugin_name . '-tooltip', plugin_dir_url( __FILE__ ) . 'js/jquery.tipTip.js', array( 'jquery' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zoho-gf-integration-admin.min.js', array( 'jquery', $this->plugin_name . '-tooltip' ), $this->version, false );

			wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery', $this->plugin_name . '-tooltip' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-swal2', plugin_dir_url( __FILE__ ) . 'js/sweet-alert2.js', array( 'jquery', $this->plugin_name . '-tooltip' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'mwb_zgf_ajax',
				array(
					'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
					'feedBackLink'     => admin_url( 'admin.php?page=mwb_zoho_gf&tab=feeds' ),
					'feedBackText'     => esc_html__( 'Back to feeds', 'mwb-gf-integration-with-zoho-crm' ),
					'feedFormSettings' => $this->get_feed_form_settings(),
					'feedFormFilters'  => $this->get_feeds_form_filter(),
					'isPage'           => isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '', // @codingStandardsIgnoreLine
					'notFoundError'    => esc_html__( 'Settings not found', 'mwb-gf-integration-with-zoho-crm' ),
					'criticalError'    => esc_html__( 'Internal server error', 'mwb-gf-integration-with-zoho-crm' ),
					'authSecure'       => wp_create_nonce( 'mwb_zgf_nonce' ),
				)
			);
		}
	}


	/**
	 * Check for the screens provided by the plugin.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_valid_screen() {

		$result = false;

		$valid_screens = array(
			'forms_page_mwb_zoho_gf',
			'mwb_zoho_feeds',
		);

		$screen = get_current_screen();

		if ( ! empty( $screen->id ) ) {

			$pages = $screen->id;

			foreach ( $valid_screens as $screen ) {
				if ( false !== strpos( $pages, $screen ) ) { // @codingStandardsIgnoreLine
					$result = true;
				}
			}
		}

		return $result;
	}

	/**
	 * Get all valid screens to add scripts and templates.
	 *
	 * @param array $valid_screens An array of plugin scrrens.
	 * @since    1.0.0
	 */
	public function add_mwb_frontend_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'forms_page_mwb_zoho_gf' );
		}

		return $valid_screens;
	}

	/**
	 * Get all valid slugs to add deactivate popup.
	 *
	 * @param array $valid_screens An array of plugin scrrens.
	 * @since    1.0.0
	 */
	public function add_mwb_deactivation_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'mwb-gf-integration-with-zoho-crm' );
		}

		return $valid_screens;
	}

	/**
	 * Get zoho modules & save it.
	 */
	public function save_zoho_modules() {

		if ( get_option( 'mwb_zgf_modules', false ) ) {
			return;
		}

		$zoho_api = Zgf_Api::get_instance();
		$response = $zoho_api->get_modules_data();
		update_option( 'mwb_zgf_modules', $response );

	}

	/**
	 * Add Zoho submenu to Forms menu.
	 *
	 * @param  array $menu_items Added Menu Item.
	 * @return array
	 */
	public function mwb_zoho_gf_add_submenu( $menu_items ) {

		$menu_items[] = array(
			'name'       => 'mwb_zoho_gf',
			'label'      => 'Zoho',
			'callback'   => array( $this, 'mwb_zoho_gf_submenu_cb' ),
			'permission' => 'edit_posts',
		);
		return $menu_items;
	}

	/**
	 * Submenu Callback.
	 *
	 * @return void
	 */
	public function mwb_zoho_gf_submenu_cb() {

		require_once ZOHO_GF_INTEGRATION_DIRPATH . '/admin/partials/zoho-gf-integration-admin-display.php';
	}

	/**
	 * Function to run at admin intitialization
	 */
	public function admin_init_process() {

		/* Authorize and redirect */
		if ( isset( $_GET['mwb_get_zgf_code'] ) && true == $_GET['mwb_get_zgf_code'] ) {                 	// @codingStandardsIgnoreLine
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {

				$client_id = ! empty( $_GET['client_id'] ) ? sanitize_text_field( wp_unslash( $_GET['client_id'] ) ) : '';
				$secret_id = ! empty( $_GET['secret_id'] ) ? sanitize_text_field( wp_unslash( $_GET['secret_id'] ) ) : '';
				$domain    = ! empty( $_GET['domain'] ) ? sanitize_text_field( wp_unslash( $_GET['domain'] ) ) : '';

				if ( empty( $client_id ) || empty( $secret_id ) || empty( $domain ) ) {
					return false;
				}

				update_option( 'mwb-zgf-client-id', $client_id );
				update_option( 'mwb-zgf-secret-id', $secret_id );
				update_option( 'mwb-zgf-domain', $domain );

				$auth_url = $this->zoho_api->get_auth_code_url();
				wp_redirect( $auth_url ); 																	// @codingStandardsIgnoreLine
			}
		}

		/* If authorized redirect to plugin page */
		if ( isset( $_GET['code'] ) && isset( $_GET['accounts-server'] ) && isset( $_GET['location'] ) ) {
			$code    = sanitize_text_field( wp_unslash( $_GET['code'] ) );                                                                       // @codingStandardsIgnoreLine
			$success = $this->zoho_api->get_refresh_token_data( $code ) ? '1' : '0';
			update_option( 'mwb_is_crm_active', $success );
			wp_safe_redirect( admin_url( 'admin.php?page=mwb_zoho_gf' ) );
		}

		/* Download log file */
		if ( isset( $_GET['mwb_download'] ) ) {                                                             // @codingStandardsIgnoreLine
			$filename = WP_CONTENT_DIR . '/uploads/zoho-gf-logs/mwb-zgf-sync-log.log';
			header( 'Content-type: text/plain' );
			header( 'Content-Disposition: attachment; filename="' . basename( $filename ) . '"' );
			readfile( $filename ); 																			// @codingStandardsIgnoreLine
			exit;
		}
	}

	/**
	 * Tooltip image
	 *
	 * @param string $tip Tip to display.
	 * @since 1.0.0
	 */
	public static function mwb_zgf_tooltip( $tip ) {
		?>
			<i class="mwb_zgf_icons mwb_zgf_tips" data-tip="<?php echo esc_html( $tip ); ?>"><span class="dashicons dashicons-editor-help"></span></i> 
		<?php

	}

	/**
	 * Get feed field types.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_feed_form_settings() {

		$page     = get_current_screen();
		$settings = array();

		if ( 'mwb_zoho_feeds' == $page->id ) { // @codingStandardsIgnoreLine

			$feed_id = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : ''; // @codingStandardsIgnoreLine

			if ( ! empty( $feed_id ) ) {

				$form_id = get_post_meta( $feed_id, 'mwb_zgf_form', true );

				$form_data = array();

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
					'label'   => __( 'Field Value', 'mwb-gf-integration-with-zoho-crm' ),
					'options' => $form_data,
				);

				$settings['custom_value'] = array(
					'label' => esc_html__( 'Custom Value', 'mwb-gf-integration-with-zoho-crm' ),
				);
			}
		}

		return $settings;
	}


	/**
	 * Notices :: Display admin notices.
	 *
	 * @param string $class Type of notice.
	 * @param string $msg   Message to display.
	 * @since 1.0.0
	 */
	public static function mwb_zgf_notices( $class = false, $msg = false ) {
		ob_start();
		?>
			<div class="notice notice-<?php echo esc_html( $class ); ?> is-dismissible mwb-notice">
				<p><strong><?php echo esc_html( $msg ); ?></strong></p>
			</div>
		<?php
		return ob_get_clean();
	}


	/**
	 * Feeds conditional filter options.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_feeds_form_filter() {

		$filter = array(
			'exact_match'       => esc_html__( 'Matches exactly', 'mwb-gf-integration-with-zoho-crm' ),
			'no_exact_match'    => esc_html__( 'Does not match exactly', 'mwb-gf-integration-with-zoho-crm' ),
			'contains'          => esc_html__( 'Contains (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'not_contains'      => esc_html__( 'Does not contain (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'exist'             => esc_html__( 'Exist in (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'not_exist'         => esc_html__( 'Does not Exists in (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'starts'            => esc_html__( 'Starts with (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'not_starts'        => esc_html__( 'Does not start with (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'ends'              => esc_html__( 'Ends with (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'not_ends'          => esc_html__( 'Does not end with (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'less_than'         => esc_html__( 'Less than (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'greater_than'      => esc_html__( 'Greater than (Text)', 'mwb-gf-integration-with-zoho-crm' ),
			'less_than_date'    => esc_html__( 'Less than (Date/Time)', 'mwb-gf-integration-with-zoho-crm' ),
			'greater_than_date' => esc_html__( 'Greater than (Date/Time)', 'mwb-gf-integration-with-zoho-crm' ),
			'equal_date'        => esc_html__( 'Equals (Date/Time)', 'mwb-gf-integration-with-zoho-crm' ),
			'empty'             => esc_html__( 'Is empty', 'mwb-gf-integration-with-zoho-crm' ),
			'not_empty'         => esc_html__( 'Is not empty', 'mwb-gf-integration-with-zoho-crm' ),
		);

		return apply_filters( 'mwb_zgf_condition_filter', $filter );
	}

	/**
	 * Feeds Condtional html.
	 *
	 * @param string $and_condition  The and condition of current html.
	 * @param string $and_index      The and offset of current html.
	 * @param string $or_index       The or offset of current html.
	 *
	 * @since 1.0.0
	 */
	public static function render_and_conditon( $and_condition = array(), $and_index = '1', $or_index = '' ) {

		if ( empty( $and_index ) || empty( $and_condition ) || empty( $or_index ) ) {
			return;
		}

		?>
		<div class="and-condition-filter" data-and-index=<?php echo esc_attr( $and_index ); ?> >
			<select name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][field]"  class="condition-form-field">
				<option value="-1" ><?php esc_html_e( 'Select Field', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
				<?php foreach ( $and_condition['form'] as $key => $value ) : ?>
					<option value="<?php echo esc_html( $key ); ?>" <?php selected( $and_condition['field'], $key ); ?> ><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][option]" class="condition-option-field">
				<option value="-1"><?php esc_html_e( 'Select Condition', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
				<?php foreach ( self::get_feeds_form_filter() as $key => $value ) : ?>
					<option value="<?php echo esc_html( $key ); ?>" <?php selected( $and_condition['option'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="text" name="condition[<?php echo esc_html( $or_index ); ?>][<?php echo esc_html( $and_index ); ?>][value]" class="condition-value-field" value="<?php echo esc_html( ! empty( $and_condition['value'] ) ? $and_condition['value'] : '' ); ?>" placeholder="<?php esc_html_e( 'Enter value', 'mwb-gf-integration-with-zoho-crm' ); ?>" required>
			<?php if ( 1 != $and_index ) : // @codingStandardsIgnoreLine ?>
				<span class="dashicons dashicons-no"></span>
			<?php endif; ?>
		</div>
		<?php
	}


	/**
	 * Save hook :: Saves data of the reffered object.
	 *
	 * @param array  $formdata An array of form data.
	 * @param string $obj      Which data to save Account or Setting.
	 * @since 1.0.0
	 * @return array An array of status and message.
	 */
	public function save_crm_settings( $formdata = array(), $obj = false ) {

		$result       = array();
		$setting_data = array();

		if ( empty( $formdata ) || ! is_array( $formdata ) ) {

			$result['error'] = array(
				'status'  => false,
				'class'   => 'error',
				'message' => esc_html__( 'No data found', 'mwb-gf-integration-with-zoho-crm' ),
			);

		} else {

			switch ( $obj ) {

				case 'setting':
					foreach ( $formdata as $data_key => $data_value ) {

						if ( 'email_notif' == $data_key ) { // @codingStandardsIgnoreLine

							if ( '' != $data_value && ! self::validate_email( $data_value ) ) { // @codingStandardsIgnoreLine
								$setting_data['email_notif'] = '';

								$result['email_error'] = array(
									'status'  => false,
									'class'   => 'error',
									'message' => esc_html__( 'Inavlid email', 'mwb-gf-integration-with-zoho-crm' ),
								);
								continue;

							} elseif ( '' == $data_value ) {
								$setting_data['email_notif'] = get_option( 'admin_email', '' );
							} else {
								$setting_data['email_notif'] = $data_value;
							}
						} else {
							$setting_data[ $data_key ] = $data_value;
						}
					}

					$setting_data = apply_filters( 'mwb_zgf_manage_settings_data', $setting_data, $formdata );

					update_option( 'mwb_zgf_setting', $setting_data );

					$result['db_response'] = array(
						'status'  => true,
						'class'   => 'success',
						'message' => esc_html__( 'Settings saved successfully', 'mwb-gf-integration-with-zoho-crm' ),
					);
					break;
			}
		}

		return $result;

	}

	/**
	 * Email validation.
	 *
	 * @param string $email E-mail to validate.
	 * @since 1.0.0
	 * @return bool
	 */
	public static function validate_email( $email = false ) {

		if ( function_exists( 'filter_var' ) ) {

			if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return true;
			}
		} elseif ( function_exists( 'is_email' ) ) {

			if ( is_email( $email ) ) {
				return true;
			}
		} else {

			if ( preg_match( '/@.+\./', $email ) ) {
				return true;
			}
		}

		return false;

	}


	/**
	 * Clear sync log callback.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function clear_sync_log() {

		$delete_duration = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'delete_logs' );
		if ( ! empty( $delete_duration ) ) {
			$dura = time() - ( $delete_duration * 24 * 60 * 60 );
			self::delete_sync_log( $dura );
		}
	}


	/**
	 * Clear log table.
	 *
	 * @param int $duration delete logs duration.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public static function delete_sync_log( $duration ) {

		global $wpdb;
		$table_name     = $wpdb->prefix . 'mwb_zgf_log';
		$log_data_query = $wpdb->prepare( "DELETE FROM {$table_name} WHERE time<=$duration" ); // @codingStandardsIgnoreLine
		$wpdb->query( $log_data_query, ARRAY_A ); // @codingStandardsIgnoreLine
	}

	/**
	 * Clear logs manually on button click.
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public static function clear_log_truncate_table() {

		global $wpdb;
		$table_name     = $wpdb->prefix . 'mwb_zgf_log';
		$log_data_query = $wpdb->prepare( "TRUNCATE TABLE {$table_name}" ); // @codingStandardsIgnoreLine
		$wpdb->query( $log_data_query, ARRAY_A ); // @codingStandardsIgnoreLine
	}


}
