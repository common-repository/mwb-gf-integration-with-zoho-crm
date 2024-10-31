<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the accounts creation page.
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

$data_center = get_option( 'mwb-zgf-domain' );
$client      = get_option( 'mwb-zgf-client-id' );
$secret      = get_option( 'mwb-zgf-secret-id' );
$is_active   = get_option( 'mwb_is_crm_active', 0 );
$token       = Mwb_Gf_Integration_With_Zoho_Crm_Helper::get_token_expiry_details();
$total_count = get_option( 'mwb_zgf_synced_form_count', 0 );

$plugin_links = array(
	'doc'     => 'https://docs.makewebbetter.com/mwb-gf-integration-with-zoho-crm/?utm_source=MWB-gf-integration-with-zoho-crm-org&utm_medium=MWB-ORG&utm_campaign=MWB-gf-integration-with-zoho-crm-org',
	'support' => 'https://support.makewebbetter.com/wordpress-plugins-knowledge-base/category/mwb-gf-integration-with-zoho-crm/?utm_source=MWB-gf-integration-with-zoho-crm-org&utm_medium=MWB-ORG&utm_campaign=MWB-gf-integration-with-zoho-crm-org',
	'contact' => 'https://makewebbetter.com/submit-query/?utm_source=MWB-gf-integration-with-zoho-crm-org&utm_medium=MWB-ORG&utm_campaign=MWB-gf-integration-with-zoho-crm-org',
);

$plugin_links = apply_filters( 'mwb_zgf_plugin_dashboard_links', $plugin_links );

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

	<div class="mwb_zgf_crm_connected <?php echo esc_attr( '1' != $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>">
		<ul>
			<li class="mwb-zgf__conn-row">
				<div class="mwb-zgf__left-col">
					<h3 class="mwb-title"><?php echo esc_html_e( 'Connection Authorized', 'mwb-gf-integration-with-zoho-crm' ); ?></h3>
					<div class="mwb-zgf-token-notice__wrap">
						<p id="mwb-zgf-token-notice" >
							<?php if ( ! $token ) : ?>
								<?php esc_html_e( 'Access token has been expired.', 'mwb-gf-integration-with-zoho-crm' ); ?>
							<?php else : ?>
								<?php echo sprintf( 'Access token will expire in %s minute(s).', esc_html( $token ) ); ?>
							<?php endif; ?>
						</p>
					<p><img id ="mwb_zgf_refresh_token" src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/refresh.svg' ); ?>"></p>
					</div>
				</div>
				<div class="mwb-zgf__right-col">
					<a id="mwb_zgf_reauthorize" href="<?php echo esc_url( wp_nonce_url( admin_url( '?mwb_get_gf_code=1' ) ) ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Reauthorize', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
					<a id="mwb_zgf_revoke" href="#" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Disconnect', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				</div>
			</li>
		</ul>
	</div>
	<div class="mwb-dashboard__about <?php echo esc_attr( '1' != $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>">
		<div class="mwb-dashboard__about-list">
			<div class="mwb-content__list-item-text">
				<h2 class="mwb-section__heading"><?php esc_html_e( 'Synced Gravity Forms', 'mwb-gf-integration-with-zoho-crm' ); ?></h2>
				<div class="mwb-dashboard__about-number">
					<span><?php echo esc_html( ! empty( $total_count ) ? $total_count : 0 ); ?></span>
				</div>
				<div class="mwb-dashboard__about-number-desc">
					<p>
						<i><?php esc_html_e( 'Total number of Gravity Form submission data which are synced over Zoho CRM.', 'mwb-gf-integration-with-zoho-crm' ); ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=mwb_zoho_gf&tab=logs' ) ); ?>" target="_blank"><?php esc_html_e( 'View log', 'mwb-gf-integration-with-zoho-crm' ); ?></a></i>
					</p>
				</div>
			</div>
			<div class="mwb-content__list-item-image">
				<img src="<?php echo esc_url( ZOHO_GF_INTEGRATION_URL . 'admin/images/deals.svg' ); ?>" alt="">
			</div>
		</div>
	</div>
	<div class="mwb-content-wrap <?php echo esc_attr( '1' != $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>">
		<ul class="mwb-about__list">
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Need any help ? Check our documentation.', 'mwb-gf-integration-with-zoho-crm' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( $plugin_links['doc'] ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Documentation', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				</div>
			</li>
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Facing any issue ? Open a support ticket.', 'mwb-gf-integration-with-zoho-crm' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( $plugin_links['support'] ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Support', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				</div>
			</li>
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Need personalized solution, contact us !', 'mwb-gf-integration-with-zoho-crm' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( $plugin_links['contact'] ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Connect', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
				</div>
			</li>
		</ul>	
	</div>

	<form method="post" id="mwb_zgf_account_form">
		<?php wp_nonce_field( 'mwb_zoho_gf_accounts', 'zoho_gf_account_nonce' ); ?>

		<p class="<?php echo esc_attr( '1' == $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>" ><?php esc_html_e( 'If you don\'t have a Zoho account, you can ', 'mwb-gf-integration-with-zoho-crm' ); ?><a href="<?php echo esc_url( 'https://www.zoho.com/' ); ?>" target="_blank"><?php esc_html_e( 'sign up here', 'mwb-gf-integration-with-zoho-crm' ); ?></a></p>

		<div class="mwb_zgf_table_wrapper">
			<div class="mwb_zgf_account_setup <?php echo esc_attr( '1' == $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>"><h2><?php esc_html_e( 'Account', 'mwb-gf-integration-with-zoho-crm' ); ?></h2></div>
			<input type="hidden" name="mwb_is_crm_active" value="<?php echo esc_html( $is_active ); ?>">
			<div class="mwb-zgf__signup-instruction-wrap <?php echo esc_attr( '1' == $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>"> 
				<h4><?php esc_html_e( 'Instructions', 'mwb-gf-integration-with-zoho-crm' ); ?></h4>
				<div class="mwb-zgf__instruction-icon">
					<span class="mwb-zgf__instruction-open-icon"></span>
					<span class="mwb-zgf__instruction-close-icon"></span>
				</div>
				<div class="mwb-zgf__instruction">
					<p>
						&rarr;<?php esc_html_e( 'Create new client', 'mwb-gf-integration-with-zoho-crm' ); ?> <a href="<?php echo esc_url( 'https://api-console.zoho.in/' ); ?>" target="_blank"><?php esc_html_e( ' here', 'mwb-gf-integration-with-zoho-crm' ); ?></a></br>
						&rarr;<?php esc_html_e( 'Choose client type as "Server based"', 'mwb-gf-integration-with-zoho-crm' ); ?></br>
						&rarr;<?php echo sprintf( '%s (%s)', esc_html__( 'Enter client name', 'mwb-gf-integration-with-zoho-crm' ), esc_html__( 'For eg. Demo app or My app', 'mwb-gf-integration-with-zoho-crm' ) ); ?></br>
						&rarr;<?php esc_html_e( 'Enter ', 'mwb-gf-integration-with-zoho-crm' ); ?><code><?php echo esc_url( admin_url() ); ?></code><?php esc_html_e( 'as redirect URI', 'mwb-gf-integration-with-zoho-crm' ); ?></br>
						&rarr;<?php esc_html_e( 'Create app', 'mwb-gf-integration-with-zoho-crm' ); ?>
					</p>
				</div>
			</div>
			<table class="mwb_zgf_table mwb-zgf__unauthorized--table <?php echo esc_attr( '1' == $is_active ? 'is_hidden' : '' ); // @codingStandardsIgnoreLine ?>">
				<tbody>

					<!-- Zoho Domain start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Zoho Domain', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Select Zoho domain', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_Crm_Admin::mwb_zgf_tooltip( $desc ) );

							$_domain = ! empty( $data_center ) ? sanitize_text_field( wp_unslash( $data_center ) ) : 'in';
							?>
							<select name="mwb_account[domain]" id="mwb-zgf-domain" >
								<option value="in" <?php selected( $_domain, 'in' ); ?>><?php esc_html_e( 'India (.in)', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
								<option value="com.cn" <?php selected( $_domain, 'com.cn' ); ?>><?php esc_html_e( 'China (.com.cn)', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
								<option value="com.au" <?php selected( $_domain, 'com.au' ); ?>><?php esc_html_e( 'Australia (.com.au)', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
								<option value="eu" <?php selected( $_domain, 'eu' ); ?>><?php esc_html_e( 'Europe (.eu)', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
								<option value="com" <?php selected( $_domain, 'com' ); ?>><?php esc_html_e( 'USA & Others (.com)', 'mwb-gf-integration-with-zoho-crm' ); ?></option>
							</select>
						</td>
					</tr>
					<!-- Zoho domain end -->

					<!-- App ID start  -->
					<tr>
						<th>							
							<label><?php esc_html_e( 'Client ID', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enter app id', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_Crm_Admin::mwb_zgf_tooltip( $desc ) );

							$app_id = ! empty( $client ) ? sanitize_text_field( wp_unslash( $client ) ) : '';
							?>
							<div class="mwb-zgf__secure-field">
								<input type="password"  name="mwb_account[app_id]" id="mwb-zgf-client-id" value="<?php echo esc_html( $app_id ); ?>" required>
								<div class="mwb-zgf__trailing-icon">
									<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
								</div>
							</div>
						</td>
					</tr>
					<!-- App ID end -->

					<!-- Secret Key start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Secret key', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enter secret key', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_Crm_Admin::mwb_zgf_tooltip( $desc ) );

							$secret_key = ! empty( $secret ) ? sanitize_text_field( wp_unslash( $secret ) ) : '';
							?>

							<div class="mwb-zgf__secure-field">
								<input type="password" name="mwb_account[secret_key]" id="mwb-zgf-secret-id" value="<?php echo esc_html( $secret_key ); ?>" required>
								<div class="mwb-zgf__trailing-icon">
									<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
								</div>
							</div>
						</td>
					</tr>
					<!-- Secret Key End -->

					<!-- Redirect url start -->
					<tr>
						<th>
							<label><?php esc_html_e( 'Redirect URL', 'mwb-gf-integration-with-zoho-crm' ); ?></label>
						</th>

						<td>
							<?php
							$desc = esc_html__( 'Enter redirect URL', 'mwb-gf-integration-with-zoho-crm' );
							echo esc_html( Mwb_Gf_Integration_With_Zoho_Crm_Admin::mwb_zgf_tooltip( $desc ) );
							?>

							<input type="url" name="mwb_account[redirect_url]" value="<?php echo esc_html( rtrim( admin_url(), '/' ) ); ?>" readonly>
						</td>
					</tr>
					<!-- Redirect url end -->

					<!-- Save & connect account start -->
					<tr>
						<th>
						</th>
						<td>
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?mwb_get_zgf_code=1' ) ) ); ?>" class="mwb-btn mwb-btn--filled mwb_gf_submit_account" id="mwb-zgf-authorize-button" ><?php esc_html_e( 'Authorize', 'mwb-gf-integration-with-zoho-crm' ); ?></a>
						</td>
					</tr>
					<!-- Save & connect account end -->
				</tbody>
			</table>
		</div>
	</form>

</div>
<?php

