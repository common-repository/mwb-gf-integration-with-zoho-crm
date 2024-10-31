<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the header of feeds section.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes/framework/templates/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="mwb_zgf__feeds-wrap">
	<div class="mwb-zgf__logo-wrap">
		<div class="mwb-zgf__logo-zoho">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/zoho-logo.png' ); ?>" alt="<?php esc_html_e( 'Zoho', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
		<div class="mwb-zgf__logo-gravity">
			<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'Gravity Form', 'mwb-gf-integration-with-zoho-crm' ); ?>">
		</div>
	</div>
