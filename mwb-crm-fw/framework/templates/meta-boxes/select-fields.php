<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select fields section of feeds.
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
<div id="mwb-fields-form-section-wrapper"  class="mwb-feeds__content  mwb-content-wrap row-hide">
	<a class="mwb-feeds__header-link active">
		<?php esc_html_e( 'Map Fields', 'mwb-gf-integration-with-zoho-crm' ); ?>
	</a>
	<?php
		$desc = esc_html__( 'Map your gravity form fields with appropriate Zoho fields.', 'mwb-gf-integration-with-zoho-crm' );
		Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc );
	?>
	<div id="mwb-fields-form-section" class="mwb-feeds__meta-box-main-wrapper" mapping_data="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $params['mapping_data'] ) ) ); ?> " crm_object="<?php echo esc_attr( $params['object'] ); ?>">
	</div>
</div>
