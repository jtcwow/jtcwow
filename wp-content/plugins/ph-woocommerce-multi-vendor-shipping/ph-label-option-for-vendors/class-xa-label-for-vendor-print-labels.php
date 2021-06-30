<?php

if( ! class_exists('Xa_Label_For_Vendor_Print_Labels') ) {
	class Xa_Label_For_Vendor_Print_Labels {

		public	$allowed_user_roles 	= array(
			'wc_product_vendors_admin_vendor',
			'vendor',
			'seller'
		);

		public function __construct(){

			global $xa_active_plugins;

			// Enque Styling for All Orders Page for Vendors
			add_filter( 'wp_enqueue_scripts', array( $this, 'ph_label_for_vendors_scripts' ) );
			
			add_action( 'woocommerce_after_register_post_type', array( $this, 'check_for_create_shipment_action' ) );
			
			add_action( 'init', array( $this, 'my_custom_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'my_custom_query_vars'), 0 );
			add_action( 'wp_loaded', array( $this, 'my_custom_flush_rewrite_rules' ));
			add_action( 'woocommerce_account_ph-all-order_endpoint', array( $this, 'my_custom_endpoint_content') );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'add_navigation_menu') );
			add_filter( 'the_title', array( $this,  'ph_all_order_endpoint_title'), 10, 2 );

			// Add Label Creation Option for Dokan Dashboard Order

			if( class_exists('wf_fedEx_wooCommerce_shipping_setup') ) {
				
				add_action( 'dokan_order_detail_after_order_items', array( $this, 'ph_add_option_to_create_label') );
			}
		}

		public function check_for_create_shipment_action() {

			global $xa_current_user;
			$xa_current_user = wp_get_current_user();

			// To Print Label
			if( ! empty($_GET['xa_print_vendor_fedex_label']) || ! empty($_GET['xa_print_vendor_ups_label']) ) {
				$this->print_labels();
			}
			// To Print Return Label
			elseif( ! empty($_GET['xa_print_vendor_fedex_return_label']) ) {
				$this->print_return_label();
			}
			// To Print  Additional Label
			elseif( ! empty($_GET['ph_print_vendor_additional_label']) ) {
				$this->print_additional_label();
			}
			// To Void Shipment
			elseif( ! empty($_GET['ph_void_vendor_fedex_shipment']) ) {
				$this->ph_void_shipment();
			}
			// To Create Shipment
			elseif( ! empty($_GET['wf_fedex_createshipment']) || ! empty($_GET['wf_create_return_label']) ) {

				if( ! class_exists('Xa_Label_For_Vendor_Fedex_Support') || ! empty($_GET['wf_create_return_label']) ) {
					require_once 'fedex/class-xa-label-for-vendor-fedex-support.php';
				}
				$fedex_support = new Xa_Label_For_Vendor_Fedex_Support();
			}

		}

		/**
		 * Print Labels for the particular Shipment Id.
		 */
		public function print_labels() {

			if( ! empty($_GET['xa_print_vendor_fedex_label']) ) {

				list( $order_id, $user_id, $shipment_id ) = explode( '|', base64_decode($_GET['xa_print_vendor_fedex_label']) );

				if( ! class_exists('Xa_Print_Vendor_Fedex_Label') ) {
					require_once 'fedex/class-xa-label-for-vendor-fedex-support.php';
				}
				new Xa_Print_Vendor_Fedex_Label( $order_id, $user_id, $shipment_id );
			}
		}

		/**
		 * Print Fedex Return Label.
		 */
		public function print_return_label(){

			if( ! class_exists('Xa_Print_Vendor_Fedex_Label') ) {
				require_once 'fedex/class-xa-label-for-vendor-fedex-support.php';
			}

			Xa_Print_Vendor_Fedex_Label::print_fedex_return_label();
		}

		/**
		 * Print Fedex Additional Label.
		 */
		public function print_additional_label(){

			if( ! class_exists('Xa_Print_Vendor_Fedex_Label') ) {
				require_once 'fedex/class-xa-label-for-vendor-fedex-support.php';
			}

			Xa_Print_Vendor_Fedex_Label::print_fedex_additional_label();
		}

		/**
		 * Print Fedex Additional Label.
		 */
		public function ph_void_shipment(){

			if( ! class_exists('Xa_Print_Vendor_Fedex_Label') ) {
				require_once 'fedex/class-xa-label-for-vendor-fedex-support.php';
			}

			Xa_Print_Vendor_Fedex_Label::void_shipment();
		}

		public function my_custom_flush_rewrite_rules() {
			flush_rewrite_rules();
		}

		public function my_custom_endpoints() {
			add_rewrite_endpoint( 'ph-all-order', EP_ROOT | EP_PAGES );
		}

		public function my_custom_query_vars($vars) {
			$vars[] = 'ph-all-order';

			return $vars;
		}

		public function add_navigation_menu( $items ) {

			global $xa_current_user;
			
			$status = $this->check_permission($xa_current_user);

			if($status)
			{
				$items = array_slice( $items, 0, 5, true ) 
				+ array( 'ph-all-order' => 'All Orders' )
				+ array_slice( $items, 5, NULL, true );
			}
			
			return $items;
		}

		public function ph_all_order_endpoint_title($title, $id)
		{
			global $wp_query;

			$is_endpoint = isset( $wp_query->query_vars[ 'ph-all-order' ] );

			if ( $is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

				$title = __( 'All Orders', 'woocommerce' );
				remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
			}

			return $title;
		}

		public function my_custom_endpoint_content() {

			global $xa_current_user;
			// Limit here based on the user capabilities
			$status = $this->check_permission($xa_current_user);

			if($status) {

				if( !empty($_GET['ph_view_order_on_front_end']) )
				{

					$order_id 	= $_GET['ph_view_order_on_front_end'];

					include 'html-xa-vendor-view-order.php';

				}else{
					include 'xa-vendor-common.php';
				}
			}
		}

		public function check_permission($xa_current_user) {

			$current_roles 	= $xa_current_user->roles;

			$allowed_roles = apply_filters( 'xa_generate_vendor_label_role_permission', $this->allowed_user_roles, $xa_current_user );

			$compare_role = array_intersect( $current_roles, $allowed_roles);

			if( !empty($compare_role) ) {
				return true;
			}else{
				return false;
			}
		}

		public function print_label( $order_id ) {

			$order 	= wc_get_order($order_id);
			
			if( ! class_exists('Xa_Label_For_Vendor_Fedex_Support') ) {
				require_once 'class-xa-label-for-vendor-fedex-support.php';
			}
			$fedex = new Xa_Label_For_Vendor_Fedex_Support($order);

		}

		public function ph_label_for_vendors_scripts() {

			wp_enqueue_style( 'ph-label-for-vendors-admin-style', plugins_url( '/../resources/css/ph_label_admin.css', __FILE__ ));
		}

		public function ph_add_option_to_create_label( $order ){

			$order_id 			= $order->get_order_number();
			$dokan_dashboard 	= true;

			include 'html-xa-vendor-view-order.php';
		}


	} 	//End of Class Xa_Label_For_Vendor_Print_Labels

	new Xa_Label_For_Vendor_Print_Labels();
}