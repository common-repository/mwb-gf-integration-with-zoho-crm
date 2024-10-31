<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the select object section of feeds.
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
$modules = new Mwb_Gf_Integration_With_Zoho_Crm_Helper();
$object  = $modules->get_modules();
?>
<div class="mwb-feeds__content  mwb-content-wrap mwb-zgf__form--group">
	<div class="mwb-zgf__form--label">
		<a class="mwb-feeds__header-link active">
			<?php esc_html_e( 'Select Object', 'mwb-gf-integration-with-zoho-crm' ); ?>
		</a>
		<?php
			$desc = esc_html__( 'Select the zoho object to create on form submission', 'mwb-gf-integration-with-zoho-crm' );

			Mwb_Gf_Integration_With_Zoho_CRM_Admin::mwb_zgf_tooltip( $desc );
		?>
	</div>
	<div class="mwb-feeds__meta-box-main-wrapper mwb-zgf__form--field">
		<div class="mwb-feeds__meta-box-wrap">
			<div class="mwb-form-wrapper">
				<select name="crm_object" id="mwb-feeds-zoho-object" class="mwb-form__dropdown">
					<option value="-1"><?php esc_html_e( 'Select Object', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
					<?php if ( false != $object && is_array( $object ) ) : // @codingStandardsIgnoreLine?>
						<?php foreach ( $object as $key => $value ) : ?>
							<option value="<?php echo esc_html( $key ); ?>" <?php selected( $params['object'], $key ); ?>><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="mwb-form-wrapper">
				<a class="mwb-btn  refresh-object"><?php esc_html_e( 'Refresh Objects', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				<a class="mwb-btn  refresh-fields"><?php esc_html_e( 'Refresh Fields', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
			</div>
		</div>
	</div>
</div>
