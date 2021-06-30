<?php
/**
 * Plugin Name: WCFM CTO - Private Vault
 * Description: WCFM Private Vault.
 * Author: CTO-CNX
 * Version: 1.0.0
 *
 * Text Domain: wcfm-private-vault
 * Domain Path: /lang/
 *
 * WC requires at least: 4.4.1
 * WC tested up to: 4.4.1
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!class_exists('WCFM')) return; // Exit if WCFM not installed

/**
 * WCFM - Private Vault Query Var
 */
function wcfmpv_query_vars( $query_vars ) {
	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );

	$query_custom_menus_vars = array(
		'wcfm-private-vaults' => ! empty( $wcfm_modified_endpoints['wcfm-private-vaults'] ) ? $wcfm_modified_endpoints['wcfm-private-vaults'] : 'private-vaults',
		'wcfm-private-vaults-manage' => ! empty( $wcfm_modified_endpoints['wcfm-private-vaults-manage'] ) ? $wcfm_modified_endpoints['wcfm-private-vaults-manage'] : 'private-vaults-manage'
	);

	$query_vars = array_merge( $query_vars, $query_custom_menus_vars );

	return $query_vars;
}
add_filter( 'wcfm_query_vars', 'wcfmpv_query_vars', 50 );

/**
 * WCFM - Private Vault End Point Title
 */
function wcfmpv_endpoint_title( $title, $endpoint ) {
	global $wp;
	switch ( $endpoint ) {
		case 'wcfm-private-vaults' :
			$title = __( 'Private Vaults', 'wcfm-private-vaults' );
		break;

		case 'wcfm-private-vaults-manage' :
			$title = __( 'Private Vaults Manage', 'wcfm-private-vault-manage' );
		break;
	}

	return $title;
}
add_filter( 'wcfm_endpoint_title', 'wcfmpv_endpoint_title', 50, 2 );

/**
 * WCFM - Private Vault Endpoint Intialize
 */
function wcfmpv_init() {
	global $WCFM_Query;

	// Intialize WCFM End points
	$WCFM_Query->init_query_vars();
	$WCFM_Query->add_endpoints();

	if( !get_option( 'wcfm_updated_end_point_cms' ) ) {
		// Flush rules after endpoint update
		flush_rewrite_rules();
		update_option( 'wcfm_updated_end_point_cms', 1 );
	}
}
add_action( 'init', 'wcfmpv_init', 50 );

/**
 * WCFM - Private Vault Endpoiint Edit
 */
function wcfmpv_custom_menus_endpoints_slug( $endpoints ) {

	$custom_menus_endpoints = array(	'wcfm-private-vaults' => 'private-vaults',
										'wcfm-private-vaults-manage' => 'private-vaults-manage'
									);

	$endpoints = array_merge( $endpoints, $custom_menus_endpoints );

	return $endpoints;
}
add_filter( 'wcfm_endpoints_slug', 'wcfmpv_custom_menus_endpoints_slug' );

if(!function_exists('get_wcfm_custom_menus_url')) {
	function get_wcfm_custom_menus_url( $endpoint ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_custom_menus_url = wcfm_get_endpoint_url( $endpoint, '', $wcfm_page );
		return $wcfm_custom_menus_url;
	}
}

/**
 * WCFM - Private Vault
 */
function wcfmpv_wcfm_menus( $menus ) {
	global $WCFM;

	$custom_menus = array( 'wcfm-private-vaults' => array(   'label'  => __( 'Private Vaults', 'wcfm-private-vaults'),
															'url'       => get_wcfm_custom_menus_url( 'wcfm-private-vaults' ),
															'icon'      => 'user-shield',
															'has_new'    => 'yes',
															'new_class'  => 'wcfm_sub_menu_items_private_vault_manage',
															'new_url'    => get_wcfm_private_vaults_manage_url(),
															'priority'  => 5.1
													)
											);

	$menus = array_merge( $menus, $custom_menus );

	return $menus;
}
add_filter( 'wcfm_menus', 'wcfmpv_wcfm_menus', 20 );

function wcfmpv_menu_dependancy_map( $list ) {
	$list['wcfm-private-vaults-manage'] = 'wcfm-private-vaults';
	return $list;
}
add_filter( 'wcfm_menu_dependancy_map', 'wcfmpv_menu_dependancy_map', 20 );

/**
 *  WCFM - Private Vault Views
 */
function wcfm_pv_load_views( $end_point ) {
	global $WCFM, $WCFMu;
	$plugin_path = trailingslashit( dirname( __FILE__  ) );

	switch( $end_point ) {
		case 'wcfm-private-vaults':
			require_once( $plugin_path . 'views/wcfm-views-private-vaults.php' );
		break;

		case 'wcfm-private-vaults-manage':
			require_once( $plugin_path . 'views/wcfm-views-private-vaults-manage.php' );
		break;
	}
}
add_action( 'wcfm_load_views', 'wcfm_pv_load_views', 50 );
add_action( 'before_wcfm_load_views', 'wcfm_pv_load_views', 50 );

// Custom Load WCFM Scripts
function wcfm_pv_load_scripts( $end_point ) {
	global $WCFM;
	$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );

	switch( $end_point ) {
		case 'wcfm-private-vaults':
			wp_enqueue_script( 'wcfm_private_vaults_js', $plugin_url . 'js/wcfm-script-private-vaults.js', array( 'jquery' ), $WCFM->version, true );
			$WCFM->library->load_datatable_lib();
		break;

		case 'wcfm-private-vaults-manage':
			wp_enqueue_script( 'wcfm_private_vaults_manage_js', $plugin_url . 'js/wcfm-script-private-vaults-manage.js', array( 'jquery' ), $WCFM->version, true );
			// Localized Script
			$wcfm_messages = get_wcfm_private_vaults_manage_messages();
			wp_localize_script( 'wcfm_private_vaults_manage_js', 'wcfm_private_vaults_manage_messages', $wcfm_messages );
		break;
	}
}

add_action( 'wcfm_load_scripts', 'wcfm_pv_load_scripts' );
add_action( 'after_wcfm_load_scripts', 'wcfm_pv_load_scripts' );

// Custom Load WCFM Styles
function wcfm_pv_load_styles( $end_point ) {
	global $WCFM, $WCFMu;
	$plugin_url = trailingslashit( plugins_url( '', __FILE__ ) );

	switch( $end_point ) {
		case 'wcfm-private-vault':
			wp_enqueue_style( 'wcfmu_private-vaults_css', $plugin_url . 'css/wcfm-style-private-vaults.css', array(), $WCFM->version );
		break;

		case 'wcfm-private-vaults-manage':
			wp_enqueue_style( 'wcfmu_private-vaults-manage_css', $plugin_url . 'css/wcfm-style-private-vaults-manage.css', array(), $WCFM->version );
		break;
	}
}
add_action( 'wcfm_load_styles', 'wcfm_pv_load_styles' );
add_action( 'after_wcfm_load_styles', 'wcfm_pv_load_styles' );

/**
 *  WCFM - Private Vault Ajax Controllers
 */
function wcfm_pv_ajax_controller() {
	global $WCFM, $WCFMu;

	$plugin_path = trailingslashit( dirname( __FILE__  ) );

	$controller = '';
	if( isset( $_POST['controller'] ) ) {
		$controller = $_POST['controller'];

		switch( $controller ) {
			case 'wcfm-private-vaults':
				require_once( $plugin_path . 'controllers/wcfm-controller-private-vaults.php' );
				new WCFM_Private_Vault_Controller();
			break;

			case 'wcfm-private-vaults-manage':
				require_once( $plugin_path . 'controllers/wcfm-controller-private-vaults-manage.php' );
				new WCFM_Private_Vaults_Manage_Controller();
			break;
		}
	}
}
add_action( 'after_wcfm_ajax_controller', 'wcfm_pv_ajax_controller' );


function wcfm_pv_register_post_types() {
	register_post_type(
		'shop_private_vault',
		apply_filters(
			'wcfm_pv_register_post_type_shop_private_vault',
			array(
				'labels'              => array(
					'name'                  => __( 'Private Vaults', 'wcfm-private-vault' ),
					'singular_name'         => __( 'Private Vault', 'wcfm-private-vault' ),
					'menu_name'             => _x( 'Private Vaults', 'Admin menu name', 'wcfm-private-vault' ),
					'add_new'               => __( 'Add private vault', 'wcfm-private-vault' ),
					'add_new_item'          => __( 'Add new private vault', 'wcfm-private-vault' ),
					'edit'                  => __( 'Edit', 'wcfm-private-vault' ),
					'edit_item'             => __( 'Edit private vault', 'wcfm-private-vault' ),
					'new_item'              => __( 'New private vault', 'wcfm-private-vault' ),
					'view_item'             => __( 'View private vault', 'wcfm-private-vault' ),
					'search_items'          => __( 'Search private vaults', 'wcfm-private-vault' ),
					'not_found'             => __( 'No private vaults found', 'wcfm-private-vault' ),
					'not_found_in_trash'    => __( 'No private vaults found in trash', 'wcfm-private-vault' ),
					'parent'                => __( 'Parent private vault', 'wcfm-private-vault' ),
					'filter_items_list'     => __( 'Filter private vaults', 'wcfm-private-vault' ),
					'items_list_navigation' => __( 'Private Vaults navigation', 'wcfm-private-vault' ),
					'items_list'            => __( 'Private Vaults list', 'wcfm-private-vault' ),
				),
				'description'         => __( 'This is where you can add new private vaults that customers can use in your store.', 'wcfm-private-vault' ),
				'public'              => true,
				'show_ui'             => true,
				'capability_type'     => 'shop_coupon',
				'map_meta_cap'        => true,
				// 'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => current_user_can( 'edit_others_shop_orders' ) ? 'woocommerce' : true,
				'hierarchical'        => false,
				// 'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
			)
		)
	);
}
add_action( 'init', 'wcfm_pv_register_post_types' );

add_filter( 'wcfm_products_args', function ( $args ) {
	// for private vault search password product only
	if (strpos( wp_get_referer() , 'private-vaults-manage') !== false) {
		$args['has_password'] = true;
	}
	return $args;
} );

// Add checkbox to check is private vault product
function wcfm_pv_products_manage_private ( $product_id ) {
	global $WCFM, $WCFMu, $wp;

	$product_object     = $product_id ? wc_get_product( $product_id ) : new WC_Product();

	$dfvalue = ! empty( $product_object->get_post_password() ) ? 'yes' : 'no';

	$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_visibility', array(
																											"is_private" => array('label' => __( 'Private Product:', 'wcfm-private-vault' ), 
																											'type' => 'checkboxoffon', 
																											'class' => 'wcfm-checkbox catalog_visibility_ele', 
																											'label_class' => 'catalog_visibility wcfm_title  wcfm_full_ele catalog_visibility_ele', 
																											'value' => 'yes',
																											'dfvalue' => $dfvalue ),
																											)) );

}
add_action( 'wcfm_product_manager_right_panel_after', 'wcfm_pv_products_manage_private', 21 );


// Hidden product in category if set private
add_filter( 'wcfm_product_data_factory', function ( $wcfm_data, $new_product_id, $product, $wcfm_products_manage_form_data ) {
	$is_private = $wcfm_products_manage_form_data['is_private'] ?? '';

	if ( $is_private == 'yes' ) {
		$wcfm_data['catalog_visibility'] = 'hidden';
	}

	return $wcfm_data;
}, 100, 4 );


// Set or unset post password on save product
add_action( 'woocommerce_update_product', function ( $product_id ) {
	if( defined('WCFM_REST_API_CALL') ) {
		$wcfm_products_manage_form_data = wc_clean($_POST['wcfm_products_manage_form']);
	} else {
		parse_str($_POST['wcfm_products_manage_form'], $wcfm_products_manage_form_data);
	}

	$is_private = $wcfm_products_manage_form_data['is_private'] ?? '-';

	$post = get_post( $product_id );

	if ( $is_private == 'yes' ) {
		if ( empty( $post->post_password ) ) {
			// set post password
			wp_update_post( array(
				'ID' => $post->ID,
				'post_password' => wp_generate_password( 10, false, false )
			) );
		}
	} else {
		// delete post password
		wp_update_post( array(
			'ID' => $post->ID,
			'post_password' => ''
		) );
	}
});

// No password required on allow user
function wcfmpv_allow_access_post( $required, $post ) {
	if ( 'shop_private_vault' == $post->post_type && is_user_logged_in() ) {

		$email = get_post_meta( $post->ID, '_wcfmpv_email', true );
		$user = get_user_by( 'id', get_current_user_id() );

		if ( current_user_can( 'administrator' )
			|| $post->post_author == $user->ID
			|| $email == $user->user_email
		) {
			$required = false;
		}
	}
	return $required;
}

add_filter( 'post_password_required', 'wcfmpv_allow_access_post', 10, 2 );

function wcfmpv_allow_access_product( $required, $post ) {
	if ( $post->post_type == 'product' && ! empty( $post->post_password ) && is_user_logged_in() ) {

		$user = get_user_by( 'id', get_current_user_id() );

		if ( $post->post_author == $user->ID ) {
			return false;
		}

		$private_vaults = get_posts( array(
											'post_type' => 'shop_private_vault',
											'post_status' => 'publish',
											'posts_per_page' => -1,
											'meta_query' => array(
												array(
													'key'     => '_wcfmpv_email',
													'value'   => $user->user_email,
													'compare' => '=',
												)
											)
										) );
		if ( ! empty ( $private_vaults ) ) {
			foreach ( $private_vaults as $private_vault ) {
				$product_ids = get_post_meta( $private_vault->ID, '_wcfmpv_product_ids', true );
				$product_ids = explode( ',', $product_ids );
				if ( in_array( $post->ID, $product_ids ) ) {
					$required = false;
					break;
				}
			}
		}
	} elseif ( $post->post_type == 'product' && ! empty( $post->post_password ) ) {

		$private_vaults = get_posts( array(
					'post_type' => 'shop_private_vault',
					'post_status' => 'publish',
					'posts_per_page' => -1,
				) );
		if ( ! empty ( $private_vaults ) && ! empty( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {

			require_once ABSPATH . WPINC . '/class-phpass.php';
			$hasher = new PasswordHash( 8, true );

			foreach ( $private_vaults as $private_vault ) {

				if ( $hasher->CheckPassword( $private_vault->post_password, $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
					$product_ids = get_post_meta( $private_vault->ID, '_wcfmpv_product_ids', true );
					$arr_product_ids = explode( ',', $product_ids );
					if ( in_array( $post->ID, $arr_product_ids ) ) {
						$required = false;
						break;
					}

				} else {
					continue;
				}

			}
		}

	}

	return $required;
}
add_filter( 'post_password_required', 'wcfmpv_allow_access_product', 10, 2 );


// Show custom data
function wcfmpv_render_custom_data( $content ) {
	global $post;

	if ( ! is_singular( array( 'shop_private_vault' ) ) ) return $content;

	// if ( ! is_user_logged_in() ) {
	// 	return wp_login_form();
	// }

	if ( post_password_required( $post->ID ) ) return $content;

	$expiry_date = get_post_meta( $post->ID, '_wcfmpv_expiry_date', true ) ?? false;
	$product_ids = get_post_meta( $post->ID, '_wcfmpv_product_ids', true );

	echo '<h2>Expiry: '.$expiry_date.'</h2>';
	echo do_shortcode('[products limit="4" columns="4" ids="'.$product_ids.'" visibility="hidden"]');

	$wcfmpv_views = (int) get_post_meta( $post->ID, '_wcfmpv_views', true );
	if( !$wcfmpv_views ) $wcfmpv_views = 1;
	else $wcfmpv_views += 1;
	update_post_meta( $post->ID, '_wcfmpv_views', $wcfmpv_views );

	return $content;
}
add_filter( 'the_content', 'wcfmpv_render_custom_data' );

// Replace prepend title
function wcfmpv_title_format( $prepend, $post ) {
	if ( $post->post_type == 'shop_private_vault' )
		$prepend = __( 'Private Vault: %s', 'wcfm-private-vault' );

	return $prepend;
}
add_filter( 'protected_title_format', 'wcfmpv_title_format', 10, 2 );

// Check Private Vault expired
function wcfmpv_check_expired() {
	if ( is_singular( array( 'shop_private_vault' ) ) ) {
		global $post, $wp_query;

		if ( $post->post_status == 'publish' ) {
			$expiry_date = get_post_meta( $post->ID, '_wcfmpv_expiry_date', true ) ?? false;
			if ( $expiry_date && current_time( 'Y-m-d' ) > $expiry_date ) {
				wp_update_post( array(
					'ID' => $post->ID,
					'post_status' => 'draft',
				) );
				$wp_query->set_404();
				status_header( 404 );
				include get_query_template( '404' );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'wcfmpv_check_expired' );

function wcfmpv_send_email_private_vault_submitted ( $private_vault_id ) {

	if ( get_post_status( $private_vault_id ) == 'publish' && get_post_meta( $private_vault_id, '_wpfmpv_send_email_status', true ) != 'success' ) {
		wcfm_send_submitted_private_vault_email( $private_vault_id );
		update_post_meta( $private_vault_id, '_wpfmpv_send_email_status', 'success' );
	}

}
add_action( 'wcfmpv_private_vault_submit', 'wcfmpv_send_email_private_vault_submitted' );


// Functions

if(!function_exists('get_wcfm_private_vaults_manage_messages')) {
	function get_wcfm_private_vaults_manage_messages() {
		global $WCFM;

		$messages = array(
						'no_title' => __( 'Please Private Vault Name before submit.', 'wcfm-private-vault' ),
						'no_email' => __( 'Please insert email before submit.', 'wcfm-private-vault' ),
						'private_vault_saved' => __( 'Private Vault Successfully Saved.', 'wcfm-private-vault' ),
						'private_vault_published' => __( 'Private Vault Successfully Published.', 'wcfm-private-vault' ),
						);

		return $messages;
	}
}

if(!function_exists('get_wcfm_private_vaults_manage_url')) {
	function get_wcfm_private_vaults_manage_url( $private_vault_id = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_private_vault_manage_url = wcfm_get_endpoint_url( 'wcfm-private-vaults-manage', $private_vault_id, $wcfm_page );
		return apply_filters( 'wcfm_private_vault_manage_url',  $wcfm_private_vault_manage_url, $private_vault_id );
	}
}

if(!function_exists('wcfm_send_submitted_private_vault_email')) {
	function wcfm_send_submitted_private_vault_email( $private_vault_id ) {
		WC()->mailer();
		$_wcfmpv_email = get_post_meta( $private_vault_id, '_wcfmpv_email', true );
		$post = get_post( $private_vault_id );
        $email = $_wcfmpv_email;
        $subject = 'You received new Private Vault';
        $subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
		$headers = "Content-Type: text/html\r\n";
		$link = get_post_permalink( $private_vault_id );
        $content = "Password: {$post->post_password}<br>";
        $content .= "<a href=\"{$link}\">Go to Private Vault</a>";
        $mail = new WC_Email();

        $message = apply_filters( 'wcfm_email_content_wrapper', $content, __( 'Private Vault', 'wcfm-private-vault' ) );

        $mail->send( $email, $subject, $message, $headers, array() );
	}
}

// Class
if ( ! class_exists( 'WCFM_Private_Vault') ) :
	class WCFM_Private_Vault {
		/**
		 * Data array, with defaults.
		 *
		 * @since 3.0.0
		 * @var array
		 */
		protected $data = array(
			'name'                        => '',
			'date_created'                => null,
			'date_modified'               => null,
			'date_expires'                => null,
			'description'                 => '',
			'product_ids'                 => array(),
			'email_restrictions'          => array(),
		);

		/**
		 * Cache group.
		 *
		 * @var string
		 */
		protected $cache_group = 'private-vault';

		/**
		 * Coupon constructor. Loads coupon data.
		 *
		 * @param mixed $data Coupon data, object, ID or code.
		 */
		public function __construct( $data ) {

		}
	}
endif;