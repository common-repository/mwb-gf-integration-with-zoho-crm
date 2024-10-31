<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the feeds listing aspects of the plugin.
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

$_feeds = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_feeds();

$filter_form_id = get_option( 'mwb_zgf_filter_feed_acc_to_form', '-1' );

?>
<div class="mwb-zgf__feedlist-wrap">
	<div class="mwb-zgf__feedlist-head">
		<div class="mwb-zgf__logo-wrap">
			<div class="mwb-zgf__logo-zoho">
				<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/zoho-logo.png' ); ?>" alt="<?php esc_html_e( 'Zoho', 'mwb-gf-integration-with-zoho-crm' ); ?>">
			</div>
			<div class="mwb-zgf__logo-gravity">
				<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'Gravity Form', 'mwb-gf-integration-with-zoho-crm' ); ?>">
			</div>
			<div class="mwb-zgf__newfeed">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mwb_zoho_feeds' ) ); ?>" ><?php esc_html_e( 'Add New Feed', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
			</div>
		</div>

		<div class="mwb-zgf__from_search-wrap">
			<label for="mwb-zgf__from_search"><?php esc_html_e( 'Show', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
			<select name="mwb-zgf__from_search" id="mwb-zgf__from_search" class="mwb-zgf__from_search" >
				<option value="-1"><?php esc_html_e( 'All', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
			<?php
			$forms = GFAPI::get_forms();
			foreach ( $forms as $form ) {
				?>
				<option value="<?php echo esc_html( $form['id'] ); ?>" <?php selected( $form['id'], $filter_form_id ); ?>><?php echo esc_html( $form['title'] ); ?></option>
				<?php
			}
			?>
			</select>
			<span><?php esc_html_e( 'Form Feeds', 'mwb-gf-integration-with-zoho-crm' ); ?></span>
		</div>
	</div>


	<div class="mwb-zgf__feed-list-div">
		<ul class="mwb-zgf__feed-list">
		<?php foreach ( $_feeds as $feed ) : ?>
			<?php $_form_id = get_post_meta( $feed->ID, 'mwb_zgf_form', true ); ?>
			<?php
			if ( $filter_form_id != $_form_id && '-1' != $filter_form_id ) {   // @codingStandardsIgnoreLine
				continue;
			}
			?>

			<?php $_status = get_post_status( $feed->ID ); ?>
			<?php $_object = get_post_meta( $feed->ID, 'mwb_zgf_object', true ); ?>
			<?php $_prime_key = get_post_meta( $feed->ID, 'mwb_zgf_primary_field', true ); ?>
			<?php $_form = GFAPI::get_form( $_form_id ); ?>
			<?php $_form_title = $_form['title']; ?>

			<li class="mwb-zgf__feed-row">
				<div class="mwb-zgf__left-col">
					<h3 class="mwb-title"><?php echo esc_html( $feed->post_title ); ?></h3>
					<div class="mwb-feed-status__wrap">
						<p class="mwb-feed-status mwb-feed-status-text_<?php echo esc_attr( $feed->ID ); ?>" ><strong><?php echo esc_html( 'publish' == $_status ? 'Active' : 'Sandbox' ); // @codingStandardsIgnoreLine ?></strong></p>
						<p>
							<div class="mwb-switch">
								<input type="checkbox" class="mwb-feed-status mwb-switch__checkbox" value="publish" <?php checked( 'publish', $_status ); ?> feed-id=<?php echo esc_attr( $feed->ID ); ?> >
							</div>
						</p>
					</div>
					<p><label><strong><?php esc_html_e( 'Form : ', 'mwb-gf-integration-with-zoho-crm' ); ?></strong><?php echo esc_html( ! empty( $_form_id ) && '-1' != $_form_id ? sanitize_text_field( wp_unslash( $_form_title ) ) : '' ); // @codingStandardsIgnoreLine ?></label></p>
					<p><label><strong><?php esc_html_e( 'Object : ', 'mwb-gf-integration-with-zoho-crm' ); ?></strong><?php echo esc_html( ! empty( $_object ) && '-1' != $_object ? sanitize_text_field( wp_unslash( $_object ) ) : '' );  // @codingStandardsIgnoreLine ?></label></p>
					<p><label><strong><?php esc_html_e( 'Primary key : ', 'mwb-gf-integration-with-zoho-crm' ); ?></strong><?php echo esc_html( ! empty( $_prime_key ) && '-1' != $_prime_key ? sanitize_text_field( wp_unslash( $_prime_key ) ) : '' ); // @codingStandardsIgnoreLine ?></label></p>
				</div>
				<div class="mwb-zgf__right-col">
					<a href="<?php echo esc_url( get_edit_post_link( $feed->ID ) ); ?>"><span class="dashicons dashicons-edit-page"></span></a>
					<div class="mwb-zgf__right-col1">
						<a href="javascript:void(0)" class="mwb_zgf__trash_feed" feed-id="<?php echo esc_html( $feed->ID ); ?>"><span class="dashicons dashicons-trash"></span></a>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
