<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the zoho logs listing aspects of the plugin.
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

$response   = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_logs();
$enable_log = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_settings_details( 'logs' );

?>
<div class="mwb-zgf__logs-wrap" id="mwb-zgf-logs" ajax_url="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>">
	<div class="mwb-zgf__logo-wrap">
		<div class="mwb-zgf__logo-zoho">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/zoho-logo.png' ); ?>" alt="<?php esc_html_e( 'Zoho', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
		<div class="mwb-zgf__logo-gravity">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'Gravity Form', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
		<?php if ( $enable_log ) : ?>
			<?php if ( $response ) : ?>
				<ul class="mwb-logs__settings-list">
					<li class="mwb-logs__settings-list-item">
						<a id="mwb-zgf-clear-log" href="#" class="mwb-logs__setting-link">
							<?php esc_html_e( 'Clear Log', 'mwb-gf-integration-with-zoho-crm' ); ?>	
						</a>
					</li>
					<li class="mwb-logs__settings-list-item">
						<a href='#' id="mwb-zgf-download-log" class="mwb-logs__setting-link">
							<?php esc_html_e( 'Download', 'mwb-gf-integration-with-zoho-crm' ); ?>	
						</a>
					</li>
				</ul>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div>
		<div>
			<?php if ( $enable_log ) : ?>
			<div class="mwb-zgf__logs-table-wrap">
				<table id="mwb-zgf-table" class="display mwb-zoho__logs-table dt-responsive nowrap" style="width: 100%;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Expand', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Feed', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Feed ID', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Zoho Object', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Zoho ID', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Event', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th><?php esc_html_e( 'Timestamp', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th class="column-hide"><?php esc_html_e( 'Request', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
							<th class="column-hide"><?php esc_html_e( 'Response', 'mwb-gf-integration-with-zoho-crm' ); ?></th>
						</tr>
					</thead>
				</table>
			</div>
			<?php else : ?>
				<div class="mwb-content-wrap">
					<?php esc_html_e( 'Please enable the logs from ', 'mwb-gf-integration-with-zoho-crm' ); ?><a href="<?php echo esc_url( 'admin.php?page=mwb_zoho_gf&tab=settings' ); ?>" target="_blank"><?php esc_html_e( 'Settings tab', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
