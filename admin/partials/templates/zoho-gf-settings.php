<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the settings page.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/admin/partials/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_POST['mwb_zgf_submit_setting'] ) ) {

	/* Nonce verification */
	check_admin_referer( 'mwb_zoho_gf_setting', 'zoho_gf_setting_nonce' );

	$formdata = ! empty( $_POST['mwb_setting'] ) ? map_deep( wp_unslash( $_POST['mwb_setting'] ), 'sanitize_text_field' ) : '';
	$response = apply_filters( 'mwb_zgf_save', $formdata, 'setting' );
}

$option = get_option( 'mwb_zgf_setting', Mwb_Gf_Integration_With_Zoho_Crm_Helper::mwb_zgf_default_settings() );

?>

<div class="mwb_zgf__account-wrap">

	<div class="mwb-zgf__logo-wrap">
		<div class="mwb-zgf__logo-zoho">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/zoho-logo.png' ); ?>" alt="<?php esc_html_e( 'Zoho', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
		<div class="mwb-zgf__logo-gravity">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'Gravity Form', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
	</div>

	<?php
	if ( ! empty( $response ) && is_array( $response ) ) {

		if ( array_key_exists( 'email_error', $response ) ) {
			Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_notices( $response['email_error']['class'], $response['email_error']['message'] );
		}

		if ( array_key_exists( 'db_response', $response ) ) {
			Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_notices( $response['db_response']['class'], $response['db_response']['message'] );
		}

		if ( array_key_exists( 'error', $response ) ) {
			Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_notices( $response['error']['class'], $response['error']['message'] );
		}
	}
	?>

	<form method="post" id="mwb_zgf_settings_form">
		<?php wp_nonce_field( 'mwb_zoho_gf_setting', 'zoho_gf_setting_nonce' ); ?>
		<div class="mwb_zgf_table_wrapper">
			<table class="mwb_zgf_table mwb-zgf__setting--table">
				<tbody>

					<!-- Enable logs start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Enable logs', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enable logging of all the forms data to be sent over zoho', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc ) );

							$enable_logs = ! empty( $option['enable_logs'] ) ? sanitize_text_field( wp_unslash( $option['enable_logs'] ) ) : 'off';
							?>
							<div class="mwb-switch">
								<input type="checkbox" class="mwb-switch__checkbox" name="mwb_setting[enable_logs]" value="on" <?php checked( 'on', $enable_logs ); ?>>
							</div>
						</td>
					</tr>
					<!-- Enable logs end-->

					<!-- Data delete start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Plugin Data', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enable plugin data delete at uninstall', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc ) );

							$data_delete = ! empty( $option['data_delete'] ) ? sanitize_text_field( wp_unslash( $option['data_delete'] ) ) : 'off';
							?>
							<div class="mwb-switch">
								<input type="checkbox" class="mwb-switch__checkbox" name="mwb_setting[data_delete]" value="on"  <?php checked( 'on', $data_delete ); ?>>
							</div>
						</td>
					</tr>
					<!-- data delete end -->

					<!-- Enable email notif start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Email notification', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
								$desc = esc_html__( 'Enable email notification on errors', 'mwb-gf-integration-with-zoho-crm' );
								echo esc_html( Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc ) );

								$enable_notif = ! empty( $option['enable_notif'] ) ? sanitize_text_field( wp_unslash( $option['enable_notif'] ) ) : 'off';
							?>
							<div class="mwb-switch">
								<input type="checkbox" class="mwb-switch__checkbox" name="mwb_setting[enable_notif]" value="on" <?php checked( 'on', $enable_notif ); ?> >
							</div>
						</td>
					</tr>
					<!-- Enable email notif end -->

					<!-- Email field start -->
					<tr >
						<th>
						</th>
						<td>
							<div id="mwb_zgf_email_notif" class="<?php echo esc_attr( ( 'on' != $enable_notif ) ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>">
								<?php
									$desc = esc_html__( 'Enter email address. An email will be sent if any sort of error occurs', 'mwb-gf-integration-with-zoho-crm' );
									echo esc_html( Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc ) );

									$email_notif = ! empty( $option['email_notif'] ) ? sanitize_email( wp_unslash( $option['email_notif'] ) ) : '';
								?>
								<input type="email" name="mwb_setting[email_notif]" value="<?php echo esc_html( $email_notif ); ?>" >
							</div>
						</td>
					</tr>	
					<!--Email field end  -->

					<!-- Delete logs start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Delete N days before logs', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'This will delete the N days earlier log data', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc ) );

							$delete_logs = ! empty( $option['delete_logs'] ) ? sanitize_text_field( wp_unslash( $option['delete_logs'] ) ) : 30;
							?>
							<input type="number" name="mwb_setting[delete_logs]" value="<?php echo esc_html( $delete_logs ); ?>" min="7" >
						</td>
					</tr>
					<!-- Delete logs end -->

					<?php do_action( 'mwb_zgf_add_setting_fields', $option ); ?>

					<!-- Save settings start -->
					<tr>
						<th>
						</th>
						<td>
							<input type="submit" name="mwb_zgf_submit_setting" class="mwb_zgf_submit_setting" value="<?php esc_html_e( 'Save', 'mwb-gf-integration-with-zoho-crm' ); ?>" >
						</td>
					</tr>
					<!-- Save settings end -->

				</tbody>
			</table>
		</div>
	</form>
</div>
