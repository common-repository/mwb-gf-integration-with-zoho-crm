<?php
/**
 * The core plugin templates are handled here.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * Template manager class, handles plugin templates.
 */
class Mwb_Gf_Integration_With_Zoho_CRM_Template_Manager {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ZOHO_GF_INTEGRATION_VERSION' ) ) {
			$this->version = ZOHO_GF_INTEGRATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
	}

	/**
	 * Add a header panel in the screen of the plugin.
	 * Returns :: HTML
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_header_content_start() {

		/* Header Content Starts */
		?>
		<meta charset="UTF-8" >
		<meta name="viewport" content="width=device-width initial-scale=1.0">
		<main class="mwb-zgf-main" >
			<header class="mwb-zgf-header">
				<h1 class="mwb-zgf-header__title"><?php esc_html_e( 'MWB GF Integration with ZOHO CRM', 'mwb-gf-integration-with-zoho-crm' ); ?></h1>
				<span class="mwb-wfw-version"><?php echo sprintf( 'v%s', esc_html( $this->version ) ); ?></span>
			</header>
		<?php
		/* Header Content Ends */
	}

	/**
	 * Add navigation tab
	 * Returns :: HTML
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_navigation_tab() {

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'accounts'; // @codingStandardsIgnoreLine

		$pro_tabs = get_option( 'mwb_zgf_pro_tabs' );
		if ( ! empty( $pro_tabs ) && ! Mwb_Gf_Integration_With_Zoho_Crm_Helper::is_pro_active() ) {
			if ( in_array( $active_tab, $pro_tabs, true ) ) {
				$active_tab = 'settings';
			}
		}

		/* Tabs section start. */
		?>
		<nav class="mwb-zgf-navbar">
			<div class="mwb-zgf-nav-collapse">
				<ul class="mwb-zgf-nav mwb-zgf-nav-tabs" role="tablist">
					<?php $tabs = $this->retrieve_nav_tabs(); ?>
					<?php if ( ! empty( $tabs ) && is_array( $tabs ) ) : ?>
						<?php foreach ( $tabs as $href => $label ) : ?>
							<li class="mwb-zgf-nav-item">
								<a class="mwb-zgf-nav-link nav-tab <?php echo esc_html( $active_tab == $href ? 'nav-tab-active' : '' ); // @codingStandardsIgnoreLine ?>" href="?page=mwb_zoho_gf&tab=<?php echo esc_html( $href ); ?>"><?php echo esc_html( $label ); ?></a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</nav>

		<?php
		/* Tabs section end */

		$path = apply_filters( 'mwb_zgf_add_tab_template_path', ZOHO_GF_INTEGRATION_DIRPATH . '/admin/partials/templates/zoho-gf-' . $active_tab . '.php', $active_tab );

		require_once $path;

	}

	/**
	 * Selected Tab settings Screen.
	 * Returns :: HTML
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_settings_screen() {

		/* Page settings section start */
		?>
		<div class="mwb-zgf-container">
			<div class="mwb-zgf-row">
				<div class="mwb-zgf-desc">
					<form method="post" action="#" class="mwb-zgf-output-form"></form>
				</div>
			</div>
		</div>
		<?php
		/* Page setting section end */
	}

	/**
	 * Get all nav tabs of current screen.
	 *
	 * @author MakeWebBetter <plugins@makewebbetter.com>
	 * @return array An array of screen tabs.
	 */
	public function retrieve_nav_tabs() {

		$current_screen = ! empty( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false; // @codingStandardsIgnoreLine

		$tabs = '';

		switch ( $current_screen ) {

			case 'mwb_zoho_gf':
				$tabs = array(
					'accounts' => esc_html__( 'Zoho Accounts', 'mwb-gf-integration-with-zoho-crm' ),
					'feeds'    => esc_html__( 'Zoho Feeds', 'mwb-gf-integration-with-zoho-crm' ),
					'logs'     => esc_html__( 'Zoho Logs', 'mwb-gf-integration-with-zoho-crm' ),
					'settings' => esc_html__( 'Settings', 'mwb-gf-integration-with-zoho-crm' ),
					'overview' => esc_html__( 'Overview', 'mwb-gf-integration-with-zoho-crm' ),
				);
				break;
		}

		return apply_filters( $current_screen . '_tab', $tabs );
	}

	/**
	 * Add a header panel end for all screens in plugin.
	 * Returns :: HTML
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_header_content_end() {

		/* Header end start */
		?>

			<div class="mwb-gf_save-wrapper is-hidden">
			</div>
		</main>

		<?php
		/* Header section end */
	}
}
