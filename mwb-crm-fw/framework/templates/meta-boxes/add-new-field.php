<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the add new field section of feeds.
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
<div id="mwb-add-new-field-section-wrapper"  class="mwb-feeds__content  mwb-content-wrap row-hide">
	<div class="mwb-zgf__form--group">
		<div class="mwb-zgf__form--label">
			<a class="mwb-feeds__header-link active">
				<?php esc_html_e( 'Add New Field', 'mwb-gf-integration-with-zoho-crm' ); ?>
			</a>
		</div>	
		<div class="mwb-feeds__meta-box-main-wrapper mwb-zgf__form--field">
			<div class="mwb-feeds__meta-box-wrap">
				<div class="mwb-form-wrapper">
					<select id="add-new-field-select"></select>
				</div>
			</div>
			<a id="add-new-field-btn" class="mwb-btn"><?php esc_html_e( 'Add Field', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
		</div>
	</div>
</div>
