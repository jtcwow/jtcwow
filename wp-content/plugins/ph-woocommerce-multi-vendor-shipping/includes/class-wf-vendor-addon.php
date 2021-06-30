<?php
class ph_multi_vendor_shipping_settings_page{
	public function __construct() {
		
		add_filter( 'woocommerce_settings_tabs_array',  array($this ,'add_settings_tab'), 50 );
		add_action( 'woocommerce_settings_tabs_ph_multi_vendor_shipping',  array($this ,'settings_tab') );
		add_action( 'woocommerce_update_options_ph_multi_vendor_shipping',  array($this ,'update_settings' ));
	}

	public function add_settings_tab( $settings_tabs ) {

		$settings_tabs['ph_multi_vendor_shipping'] = __( 'Multi Vendor Addon', 'ph-multi-vendor-shipping' );

		return $settings_tabs;
	}

	public function settings_tab() {

		wp_enqueue_style('ph-multivendor-admin-style');

		include('market.php');

		woocommerce_admin_fields( $this->get_settings() );
	}

	public function update_settings() {

		woocommerce_update_options( $this->get_settings() );
	}

	public function get_settings() {

		$settings = array(

			'section_title' 	=> array(
				'name' 			=> __( 'Multi Vendor Shipping Addon', 'ph-multi-vendor-shipping' ),
				'type' 			=> 'title',
				'desc' 			=> __( 'This addon allows you to use PluginHive shipping plugins in a Multi-Vendor scenario', 'ph-multi-vendor-shipping' ),
				'value' 		=> __( 'Multi Vendor Shipping Addon', 'ph-multi-vendor-shipping' ),
			),
			
			'splitcart' 		=> array(
				'title'			=> __( 'Display Shipping Rates On Cart', 'ph-multi-vendor-shipping' ),
				'type'			=> 'select',
				'options'		=> array(
					'sum_cart'		=> __( 'Split and Sum', 'ph-multi-vendor-shipping' ),
					'split_cat'		=> __( 'Split and Seperate', 'ph-multi-vendor-shipping' ),
				),
				'desc'			=> __( 'Choose how you want to display shipping rates on the cart page in case of multiple vendors', 'ph-multi-vendor-shipping' ),
				'desc_tip'		=> true,
				'id'			=> 'wc_settings_wf_vendor_addon_splitcart'
			),

			'show_vendor_optins' 	=> array(
				'title' 		=> __( 'Enable Vendor Options', 'ph-multi-vendor-shipping' ),
				'label' 		=> __( 'Enable', 'ph-multi-vendor-shipping' ),
				'type' 			=> 'checkbox',
				'default' 		=> 'yes',
				'desc'		 	=> __( 'Enabling this will allow vendors to add Carrier Account Details. <br/>Visit:<strong> My Account -> Account Details</strong>', 'ph-multi-vendor-shipping' ),
				'desc_tip' 		=> true,
				'id'			=> 'wc_settings_ph_vendor_show_vendor_optins'
			),
		);
		
		if( defined('VENDOR_PLUGIN') && VENDOR_PLUGIN == 'dokan_lite' && class_exists('wf_fedEx_wooCommerce_shipping_setup') )
		{
			$settings['label_for_vendor'] = array(
				'title' 		=> __( 'Allow Vendors to Generate Label', 'ph-multi-vendor-shipping' ),
				'label' 		=> __( 'Enable', 'ph-multi-vendor-shipping' ),
				'type' 			=> 'checkbox',
				'default' 		=> 'no',
				'desc'		 	=> __( 'Enabling this will allow Vendors to generate and download shipping labels<br/><br/> <strong>Note:</strong> Vendors will be able to generate Shipping Labels only if <br/> <b>Ship From Address Preference is set to Vendor Address </b>', 'ph-multi-vendor-shipping' ),
				'desc_tip' 		=> true,
				'id'			=> 'wc_settings_ph_vendor_label_for_vendor'
			);

			$settings['void_label_for_vendor'] = array(
				'title' 		=> __( 'Allow Vendors to Void Shipment', 'ph-multi-vendor-shipping' ),
				'label' 		=> __( 'Enable', 'ph-multi-vendor-shipping' ),
				'type' 			=> 'checkbox',
				'default' 		=> 'no',
				'desc'		 	=> __( 'Enabling this will allow Vendors to Void the Shipment', 'ph-multi-vendor-shipping' ),
				'desc_tip' 		=> true,
				'id'			=> 'wc_settings_ph_vendor_void_label_for_vendor'
			);
		}

		$settings['section_end'] = array(
			'type' 			=> 'sectionend',
			'id' 			=> 'wc_settings_wf_vendor_addon_section_end',
			'value' 		=> '',
		);

		return apply_filters( 'wc_settings_ph_multi_vendor_shipping_settings', $settings );
	}
}

new ph_multi_vendor_shipping_settings_page;