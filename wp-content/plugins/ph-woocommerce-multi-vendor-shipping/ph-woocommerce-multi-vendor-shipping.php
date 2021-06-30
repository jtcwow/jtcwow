<?php
/*
	Plugin Name: WooCommerce Multi Vendor Shipping Addon
	Plugin URI: https://www.pluginhive.com/product/woocommerce-multi-vendor-shipping-addon/
	Description: An advanced shipping addon that allows you to use Multi-Vendor plugins along with the WooCommerce shipping plugins by PluginHive.
	Version: 2.0.6
	Author: PluginHive
	Author URI: https://www.pluginhive.com/
	WC requires at least: 3.0.0
	WC tested up to: 5.0.0
	Text Domain : ph-multi-vendor-shipping
*/

	// Check for older version of the plugin - Free
	if( class_exists("wf_vendor_addon_setup") ) {

		deactivate_plugins( basename( __FILE__ ) );
		wp_die( __("<h2>Oops! Unable to activate the plugin.</h2><strong>Reason:</strong> You tried installing the premium version without deactivating and deleting the free version. Kindly deactivate and delete <b>Advanced Shipping for WooCommerce Multi-Vendor</b> plugin and then try again", "ph-multi-vendor-shipping" ), "", array('back_link' => 1 ));
	}

	// Define PH_MULTI_VENDOR_PLUGIN_VERSION
	if ( !defined( 'PH_MULTI_VENDOR_PLUGIN_VERSION' ) )
	{
		define( 'PH_MULTI_VENDOR_PLUGIN_VERSION', '2.0.6' );
	}

	// Include API Manager
	if ( !class_exists( 'PH_Multi_Vendor_API_Manager' ) ) {

		include_once( 'ph-api-manager/ph_api_manager_multi_vendor.php' );
	}

	if ( class_exists( 'PH_Multi_Vendor_API_Manager' ) ) {

		$product_title 		= 'Multi Vendor Addon';
		$server_url 		= 'https://www.pluginhive.com/';
		$product_id 		= '';

		$ph_multi_vendor_api_obj 	= new PH_Multi_Vendor_API_Manager( __FILE__, $product_id, PH_MULTI_VENDOR_PLUGIN_VERSION, 'plugin', $server_url, $product_title );

	}

	/**
	 * Define VENDOR_PLUGIN
	**/
	if(!defined('VENDOR_PLUGIN') ){

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$vendor_plugin_list = array(
			'product_vendor'		=> 'woocommerce-product-vendors/woocommerce-product-vendors.php', 
			'dokan_lite'			=> 'dokan-lite/dokan.php', 
			'wc_vendors_pro'		=> 'wc-vendors-pro/wcvendors-pro.php', 
			'wcfm_venors'			=> 'wc-multivendor-marketplace/wc-multivendor-marketplace.php',
			'dc_wcmp'				=> 'dc-woocommerce-multi-vendor/dc_product_vendor.php'
		);

		foreach ($vendor_plugin_list as $plugin_name => $slug) {

			if ( is_plugin_active($slug) || is_plugin_active_for_network($slug) ){

				define('VENDOR_PLUGIN',$plugin_name);

				break;
			}
		}
	}

	/**
	 * Check for required plugins
	**/
	function ph_check_for_phive_plugins_activation()
	{

		$ph_required_plugins 	= false;

		$ph_plugin_list 	= array(
			'fedex' 			=> 'fedex-woocommerce-shipping/fedex-woocommerce-shipping.php', 
			'ups'				=> 'ups-woocommerce-shipping/ups-woocommerce-shipping.php', 
			'multi_carrier' 	=> 'woocommerce-multi-carrier-shipping/woocommerce-multi-carrier-shipping.php',
		);

		foreach ($ph_plugin_list as $plugin_name => $slug) {

			if ( ( is_plugin_active($slug) || is_plugin_active_for_network($slug) ) && defined('VENDOR_PLUGIN') ) {

				$ph_required_plugins = true;

				break;
			}
		}

		return $ph_required_plugins;
	}

	/**
	 * Auto Decativate Plugin
	**/
	add_action( 'admin_init', 'ph_deactivate_multivendor_shipping_plugin' );

	if ( ! function_exists( 'ph_deactivate_multivendor_shipping_plugin' ) ) {
		function ph_deactivate_multivendor_shipping_plugin() {

			$ph_required_plugins 	= ph_check_for_phive_plugins_activation();

			if ( !$ph_required_plugins ){

				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_safe_redirect( admin_url('plugins.php') );
			}
		}
	}

	/**
	 * Plugin Activation Check
	**/
	function ph_multivendor_shipping_activation_check(){

		$ph_required_plugins 	= ph_check_for_phive_plugins_activation();

		if ( !$ph_required_plugins ) {

			deactivate_plugins( basename( __FILE__ ) );
			wp_die( __("<h2>Oops! Unable to activate the plugin.</h2><strong>Reason:</strong> This plugin requires a <br/> i. PluginHive Shipping plugin - FedEx / UPS / Multi Carrier<br/> ii. Multi-Vendor Plugin - Dokan / WCFM / Product Vendors / WC Vendors Pro / WC Marketplace", "ph-multi-vendor-shipping" ), "", array('back_link' => 1 ));
		}

	}

	register_activation_hook( __FILE__, 'ph_multivendor_shipping_activation_check' );


	class PH_Multi_Vendor_Shipping_Setup {

		public function __construct() {

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));

			add_filter( 'admin_enqueue_scripts', array( $this, 'ph_multivendor_scripts' ) );

			require_once 'includes/class-xa-shipping-carrier-account-switch.php';
			require_once 'includes/class-xa-send-label-to-vendor.php';
			include_once('includes/class-wf-vendor-addon.php');
			include_once('includes/class-wf-vendor-addon-admin.php');
		}

		public function ph_multivendor_scripts() {

			wp_enqueue_script( 'ph-multivendor-admin-script', plugins_url( '/resources/js/ph_admin.js', __FILE__ ), array( 'jquery' ) );
			wp_register_style( 'ph-multivendor-admin-style', plugins_url( '/resources/css/ph_admin.css', __FILE__ ) );
			
		}

		public function plugin_action_links($links) {

			$plugin_links = array(

				'<a href="' . admin_url( 'admin.php?page=' . ph_get_settings_url() . '&tab=ph_multi_vendor_shipping' ) . '">' . __( 'Settings', 'ph-multi-vendor-shipping' ) . '</a>',

				'<a href="https://www.pluginhive.com/knowledge-base/category/woocommerce-multi-vendor-shipping-addon/" target="_blank">' . __('Documentation', 'ph-multi-vendor-shipping') . '</a>',

				'<a href="https://www.pluginhive.com/support/" target="_blank">' . __('Support', 'ph-multi-vendor-shipping') . '</a>'
			);
			return array_merge($plugin_links, $links);
		}
	}

	if ( !function_exists('ph_get_settings_url') ) {

		function ph_get_settings_url() {
			return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
		}
	}

	new PH_Multi_Vendor_Shipping_Setup();

	/**
	 * Add Vendor Option in Settings for FedEx, UPS, Multi-Carrier
	**/
	add_filter('wf_filter_label_ship_from_address_options', 'wf_vendor_label_ship_from_address_options', 10, 4);

	if( !function_exists('wf_vendor_label_ship_from_address_options') ) {
		function wf_vendor_label_ship_from_address_options($args) {

			if( defined('VENDOR_PLUGIN') && VENDOR_PLUGIN !='' ) {
				$args['vendor_address'] = __('Vendor Address', 'ph-multi-vendor-shipping');
			}
			return $args;
		}
	}

	/*
	* Option to change Shipping name.
	* Default is set to seller company name.
	*/
	add_filter('woocommerce_shipping_package_name', 'ph_change_shipping_name', 10, 3 );

	if( !function_exists('ph_change_shipping_name') ) {

		function ph_change_shipping_name( $name, $shipping_number, $package){

			if( !empty($package['origin']) )
				return !empty( $package['origin']['company'] ) ? $package['origin']['company'] : $package['origin']['first_name'] ;
			else
				return $name;
		}
	}

	/**
	 * Add Vendor option in FedEx to Send label in email.
	 */
	add_filter( 'ph_fedex_filter_label_send_in_email_to_options', function($args) {

		$args['vendor'] = __( 'Vendor', 'ph-multi-vendor-shipping' );

		return $args;
	} );


	/**
	 * Add Vendor option in UPS to Send label in email.
	 * @param array $args Automatic label recipients.
	 * @return array.
	 */
	add_filter( 'ph_ups_option_for_automatic_label_recipient', function($args) {

		$args['vendor'] = __( 'Vendor', 'ph-multi-vendor-shipping' );

		return $args;
	} );

	// Create and Print Label Option for Vendors
	$label_for_vendor 	= get_option('wc_settings_ph_vendor_label_for_vendor');

	if( !empty($label_for_vendor) && $label_for_vendor == 'yes' )
	{
		if( !class_exists('PH_Print_Label_Option_To_Vendor') ) {
			class PH_Print_Label_Option_To_Vendor {

				public function __construct() {

					if( defined('VENDOR_PLUGIN') && VENDOR_PLUGIN == 'dokan_lite' )
					{
						require_once 'ph-label-option-for-vendors/class-xa-label-for-vendor-print-labels.php';
					}
				}
			}

			new PH_Print_Label_Option_To_Vendor();
		}
	}