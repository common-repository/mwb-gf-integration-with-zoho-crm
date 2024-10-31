<?php
/**
 * The complete management for the Zoho-GF feeds custom post type.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * The complete management for the Zoho-GF feeds custom post type.
 *
 * @since      1.0.0
 * @package    MWB_GF_Integration_with_Zoho_CRM
 * @subpackage MWB_GF_Integration_with_Zoho_CRM/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_With_Zoho_CRM_Feed_Cpt {


	/**
	 * The slug prefix for this crm.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string  $crm_prefix  The crm prefix of this class.
	 */
	private $crm_prefix;

	/**
	 * Contrucutor function
	 */
	public function __construct() {

		$this->crm_prefix = 'zoho';

	}

	/**
	 * Register custom post type for feeds.
	 *
	 * @since 1.0.0
	 */
	public function mwb_zgf_feeds_post() {

		$labels = array(
			'name'               => _x( 'Feeds', 'Post Type General Name', 'mwb-gf-integration-with-zoho-crm' ),
			'singular_name'      => _x( 'Feed', 'Post Type Singular Name', 'mwb-gf-integration-with-zoho-crm' ),
			'parent_item_colon'  => __( 'Parent Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'all_items'          => __( 'All Feeds', 'mwb-gf-integration-with-zoho-crm' ),
			'view_item'          => __( 'View Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'add_new_item'       => __( 'Add New Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'add_new'            => __( 'Add New', 'mwb-gf-integration-with-zoho-crm' ),
			'edit_item'          => __( 'Edit Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'update_item'        => __( 'Update Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'search_items'       => __( 'Search Feed', 'mwb-gf-integration-with-zoho-crm' ),
			'not_found'          => __( 'Not Found', 'mwb-gf-integration-with-zoho-crm' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'mwb-gf-integration-with-zoho-crm' ),
		);

		register_post_type(
			'mwb_zoho_feeds',
			array(
				'label'                => __( 'Feeds', 'mwb-gf-integration-with-zoho-crm' ),
				'description'          => __( 'Feeds for crm', 'mwb-gf-integration-with-zoho-crm' ),
				'labels'               => $labels,
				'supports'             => array( 'title' ),
				'hierarchical'         => false,
				'public'               => true,
				'can_export'           => true,
				'has_archive'          => true,
				'menu_position'        => 5,
				'show_in_rest'         => false,
				'exclude_from_search'  => false,
				'publicly_queryable'   => false,
				'show_in_menu'         => false,
				'show_in_nav_menus'    => false,
				'show_in_admin_bar'    => false,
				'register_meta_box_cb' => array( $this, 'mwb_zgf_meta_box' ),
			)
		);
	}

	/**
	 * Callback :: Add feeds metabox.
	 *
	 * @since 1.0.0
	 */
	public function mwb_zgf_meta_box() {
		add_meta_box( 'mwb_zgf_feeds_meta_box', esc_html__( 'Feed details', 'mwb-gf-integration-with-zoho-crm' ), array( $this, 'zgf_feeds_mb_render' ), 'mwb_zoho_feeds' );
		add_meta_box( 'mwb_zgf_feeds_condition_meta_box', esc_html__( 'Conditional Statements', 'mwb-gf-integration-with-zoho-crm' ), array( $this, 'zgf_feeds_cond_render' ), 'mwb_zoho_feeds' );

		do_action( 'mwb_zoho_add_metabox_to_feeds' );
	}

	/**
	 * Callback :: Post type feeds mapping metabox.
	 *
	 * @since 1.0.0
	 */
	public function zgf_feeds_mb_render() {
		global $post;
		$param = array();

		$param['form']          = $this->fetch_feed_data( $post->ID, 'mwb_zgf_form', '-1' );
		$param['object']        = $this->fetch_feed_data( $post->ID, 'mwb_zgf_object', '-1' );
		$param['mapping_data']  = $this->fetch_feed_data( $post->ID, 'mwb_zgf_mapping_data', array() );
		$param['primary_field'] = $this->fetch_feed_data( $post->ID, 'mwb_zgf_primary_field', array() );

		$this->render_mb_data( 'header' );
		$this->render_mb_data( 'select-form', $param );
		$this->render_mb_data( 'select-object', $param );
		$this->render_mb_data( 'select-fields', $param );
		$this->render_mb_data( 'add-new-field', $param );
		$this->render_mb_data( 'primary-field', $param );
		$this->render_mb_data( 'nonce-field', $param );
		$this->render_mb_data( 'footer' );

	}


	/**
	 * Callback :: Post type feeds conditional filter metabox.
	 *
	 * @since 1.0.0
	 */
	public function zgf_feeds_cond_render() {
		global $post;
		$param = array();

		$param = $this->fetch_feed_data( $post->ID, 'mwb_zgf_condtion_field', array() );

		$this->render_mb_data( 'opt-in-condition', $param );

	}


	/**
	 * Render html and data.
	 *
	 * @param string $meta_box Name of the meta box.
	 * @param array  $params   An array of metabox params.
	 * @since 1.0.0
	 * @return void
	 */
	private function render_mb_data( $meta_box = false, $params = array() ) {

		if ( empty( $meta_box ) ) {
			return;
		}

		$path = 'mwb-crm-fw/framework/templates/meta-boxes/' . $meta_box . '.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . $path;
	}

	/**
	 * Save feeds data.
	 *
	 * @param int $post_id Feed ID.
	 * @since 1.0.0
	 * @return void
	 */
	public function save_feeds_data( $post_id ) {

		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}

		if ( ! isset( $_POST['meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['meta_box_nonce'] ) ), 'meta_box_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['post_type'] ) && 'mwb_zoho_feeds' == $_POST['post_type'] ) { // @codingStandardsIgnoreLine

			$crm_form         = isset( $_POST['crm_form'] ) ? sanitize_text_field( wp_unslash( $_POST['crm_form'] ) ) : '';
			$crm_object       = isset( $_POST['crm_object'] ) ? sanitize_text_field( wp_unslash( $_POST['crm_object'] ) ) : '';
			$crm_field_arr    = isset( $_POST['crm_field'] ) ? map_deep( wp_unslash( $_POST['crm_field'] ), 'sanitize_text_field' ) : array();
			$field_type_arr   = isset( $_POST['field_type'] ) ? map_deep( wp_unslash( $_POST['field_type'] ), 'sanitize_text_field' ) : array();
			$field_value_arr  = isset( $_POST['field_value'] ) ? map_deep( wp_unslash( $_POST['field_value'] ), 'sanitize_text_field' ) : array();
			$custom_value_arr = isset( $_POST['custom_value'] ) ? map_deep( wp_unslash( $_POST['custom_value'] ), 'sanitize_text_field' ) : array();
			$custom_field_arr = isset( $_POST['custom_field'] ) ? map_deep( wp_unslash( $_POST['custom_field'] ), 'sanitize_text_field' ) : array();
			$condition        = isset( $_POST['condition'] ) ? map_deep( wp_unslash( $_POST['condition'] ), 'sanitize_text_field' ) : array();
			$primary_field    = isset( $_POST['primary_field'] ) ? sanitize_text_field( wp_unslash( $_POST['primary_field'] ) ) : '';

			$mapping_data = array();
			if ( ! empty( $crm_field_arr ) && is_array( $crm_field_arr ) ) {
				foreach ( $crm_field_arr as $key => $field ) {
					$mapping_data[ $field ] = array(
						'field_type'   => $field_type_arr[ $key ],
						'field_value'  => $field_value_arr[ $key ],
						'custom_value' => $custom_value_arr[ $key ],
						'custom_field' => $custom_field_arr[ $key ],
					);
				}
			}

			update_post_meta( $post_id, 'mwb_zgf_form', $crm_form );
			update_post_meta( $post_id, 'mwb_zgf_object', $crm_object );
			update_post_meta( $post_id, 'mwb_zgf_mapping_data', $mapping_data );
			update_post_meta( $post_id, 'mwb_zgf_primary_field', $primary_field );
			update_post_meta( $post_id, 'mwb_zgf_condtion_field', $condition );

			do_action( 'mwb_zgf_save_feed_post_data', $post_id, $_POST );

		}
	}

	/**
	 * Fetch feeds data.
	 *
	 * @param int    $post_id Feed ID.
	 * @param string $key     Data key.
	 * @param string $default Default value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function fetch_feed_data( $post_id, $key, $default ) {

		$data      = get_post_meta( $post_id, $key, true );
		$feed_data = ! empty( $data ) ? $data : $default;
		return $feed_data;
	}


}
