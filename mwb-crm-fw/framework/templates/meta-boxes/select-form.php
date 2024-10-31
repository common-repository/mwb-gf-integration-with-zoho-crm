<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select form section of feeds.
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

$forms = GFAPI::get_forms();

?>
<div class="mwb-feeds__content  mwb-content-wrap mwb-zgf__form--group">
	<div class="mwb-zgf__form--label">
		<a class="mwb-feeds__header-link active">
			<?php esc_html_e( 'Select Form', 'mwb-gf-integration-with-zoho-crm' ); ?>
		</a>
		<?php
			$desc = esc_html__( 'Select the form you would like to integrate with zoho', 'mwb-gf-integration-with-zoho-crm' );

			Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc );
		?>
	</div>
	<div class="mwb-feeds__meta-box-main-wrapper mwb-zgf__form--field">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select name="crm_form" id="mwb_zgf_select_form" class="mwb-form__dropdown">
					<option value="-1"><?php esc_html_e( 'Select Form', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
					<optgroup label="<?php esc_html_e( 'Gravity Forms', 'mwb-gf-integration-with-zoho-crm' ); ?>" ></optgroup>
					<?php if ( ! empty( $forms ) && is_array( $forms ) ) : ?>
						<?php foreach ( $forms as $key => $value ) : ?>
							<option value="<?php echo esc_html( $value['id'] ); ?>" <?php selected( $params['form'], $value['id'] ); ?>><?php echo esc_html( $value['title'] ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>
	</div>
</div>
