<?php

if(!class_exists('ph_ups_custom_checkout_fields')){
	
	class ph_ups_custom_checkout_fields{
		
		public function __construct()
		{
			$this->settings 			= get_option( 'woocommerce_'.WF_UPS_ID.'_settings', null );
			$this->debug	  			= isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;
			$this->recipients_tin 		= ( isset($this->settings['recipients_tin']) && ! empty($this->settings['recipients_tin']) && $this->settings['recipients_tin'] == 'yes' ) ? true : false;
			$this->accesspoint_locator 	= ( isset($this->settings[ 'accesspoint_locator']) && !empty($this->settings['accesspoint_locator']) && $this->settings[ 'accesspoint_locator']=='yes' ) ? true : false;
			$this->suggested_display	= ( isset($this->settings['suggested_display']) && !empty($this->settings['suggested_display']) && $this->settings['suggested_display'] == 'suggested_radio' ) ? 'suggested_radio' : 'suggested_notice';

			$this->init();	
		}
		
		private function init(){

			add_action( 'woocommerce_before_shipping_calculator', array($this, 'reset_custom_data_before_shipping_calculator'), 10, 0 ); 

			// Add  custom field in checkout page
			add_filter( 'woocommerce_checkout_fields' , array( $this, 'ph_ups_add_custom_checkout_fields') );

			add_filter( 'woocommerce_order_formatted_billing_address', array($this,'ph_ups_order_formatted_billing_address'), 10, 2 );
			add_filter( 'woocommerce_order_formatted_shipping_address', array($this,'ph_ups_order_formatted_shipping_address'), 10, 2 );
			
			add_filter( 'woocommerce_formatted_address_replacements',  array($this,'ph_ups_formatted_address_replacements'), 10, 2 );
			add_filter('woocommerce_localisation_address_formats', array($this,'ph_ups_address_formats'));
			
			//Display Custom Data in my-account/address
			add_filter( 'woocommerce_my_account_my_address_formatted_address',array($this,'ph_ups_my_account_formated_address'), 12, 3 );

			// Updating Custom Field value
			add_action( 'woocommerce_checkout_update_order_review', array($this,'ph_ups_update_custom_fields'), 1, 1 );

			// Add Custom Field Data in Woocommerce cart shipping packages.
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'ph_ups_update_custom_fields_details_in_package') );
			
			// Save TIN in Meta Key for Order
			add_action('woocommerce_checkout_update_order_meta', array( $this, 'ph_add_custom_field_meta_data'), 12, 2);

			// Remove added Custom Checkout Fields when WooCommerce Cleanup
			add_filter( 'woocommerce_privacy_remove_order_personal_data_meta', array( $this, 'ph_remove_custom_field_data_when_cleanup'), 10, 1 );

			add_filter( 'wp_enqueue_scripts', array( $this, 'ph_ups_checkout_scripts_for_address_suggestion' ) );
			add_action( 'wp_footer', array( $this, 'ph_address_suggestion_scripts' ) );$this->suggested_display	= ( isset($this->settings['suggested_display']) && !empty($this->settings['suggested_display']) && $this->settings['suggested_display'] == 'suggested_radio' ) ? 'suggested_radio' : 'suggested_notice';

		}

		public function ph_ups_checkout_scripts_for_address_suggestion() {

			if ( is_checkout() ) {

				wp_register_script( 'ph-ups-checkout-address-suggestion', plugins_url( '../resources/js/ph_ups_address_suggestion.js', __FILE__ ) );
				
			}
		}

		public function ph_address_suggestion_scripts() {

			$sugg_address 	= get_option( 'ph_ups_suggested_address_on_checkout' );

			if ( is_checkout() && ! is_order_received_page() && !empty($sugg_address) && $this->suggested_display == 'suggested_radio' ) {

				wp_enqueue_script( 'ph-ups-checkout-address-suggestion', plugins_url( '../resources/js/ph_ups_address_suggestion.js', __FILE__ ), array( 'jquery' ) );
				wp_localize_script( 'ph-ups-checkout-address-suggestion', 'ph_ups_address_suggestion', $sugg_address );


				$html = "<div id='ph_addr_correction' class='' style='display: none;'>
							
							<div id='ph_orig_addr'>
								<div id='ph_addr_radio' class='ph-addr-radio'></div>    
								<div style='display: none;' id='ph_orig_placeholder'></div>                     
							</div>      
						</div>";

				echo $html;
			}
		}

		public function ph_remove_custom_field_data_when_cleanup( $fields ) {
			
			if( !empty($fields) && is_array($fields) ) {

				if( $this->recipients_tin ) {

					$fields['ph_ups_shipping_tax_id_num'] 	= 'text';
					$fields['ph_ups_ship_to_tax_id_num'] 	= 'text';
				}

				if( $this->accesspoint_locator ) {

					$fields['_shipping_accesspoint'] 	= 'text';
				}
			}

			return $fields;
		}

		public function reset_custom_data_before_shipping_calculator()
		{
			$this->ph_update_custom_field_datas();
		}

		private function ph_update_custom_field_datas( $value='', $ship_to = false )
		{	
			
			if( $ship_to ) {

				WC()->session->set('ph_shipto_tax_id_num', $value);
			} else {
				
				WC()->session->set('ph_ups_tax_id_num', $value);
			}
		}

		public function ph_ups_add_custom_checkout_fields( $fields )
		{
			
			if( $this->recipients_tin )
			{
				$fields['billing']['shipping_tax_id_num'] = array(
					'label' 		=> __('Tax Identification Number', 'ups-woocommerce-shipping'),
					'placeholder'	=> _x('', 'placeholder', 'ups-woocommerce-shipping'),
					'required'		=> false,
					'clear'			=> false,
					'type'			=> 'text',
					'priority'		=> '115',
				);

				$fields['shipping']['ship_to_tax_id_num'] = array(
					'label' 		=> __('Tax Identification Number', 'ups-woocommerce-shipping'),
					'placeholder'	=> _x('', 'placeholder', 'ups-woocommerce-shipping'),
					'required'		=> false,
					'clear'			=> false,
					'type'			=> 'text',
					'priority'		=> '115',
				);
			}
			return apply_filters('ph_checkout_fields', $fields, $this->settings);
		}
		
		public function ph_ups_order_formatted_billing_address( $array, $address_fields )
		{ 
			
			if( $this->recipients_tin )
			{

				$recipients_tin			= $this->ph_get_custom_field_datas($address_fields);
				$array['tax_id_num'] 	= $recipients_tin;
			}

			return $array; 
		}

		public function ph_ups_order_formatted_shipping_address( $array,$address_fields )
		{

			if( $this->recipients_tin )
			{
				$recipients_tin			= $this->ph_get_custom_field_datas($address_fields, true);
				$array['tax_id_num'] 	= $recipients_tin;
			}

			return $array; 
		}

		private function ph_get_custom_field_datas( $order_details='', $ship_to = false )
		{	

			if( !empty( $order_details ) )
			{
				if( WC()->version < '2.7.0' )
				{	
					if( $ship_to ) {
						return ( isset($order_details->shipping_tax_id_num) ) ? $order_details->shipping_tax_id_num : '';
					} else {
						return ( isset($order_details->ship_to_tax_id_num) ) ? $order_details->ship_to_tax_id_num : '';
					}
					
				}else{

					$address_field 	= $order_details->get_meta('ph_ups_shipping_tax_id_num');

					if( $ship_to ) {
						$address_field = $order_details->get_meta('ph_ups_ship_to_tax_id_num');
					}

					return $address_field;
				}

			}else{

				if( $ship_to )
				{	
					return WC()->session->get('ph_shipto_tax_id_num');
				} else {
					return WC()->session->get('ph_ups_tax_id_num');
				}
				
			}
		}

		public function ph_ups_formatted_address_replacements( $array, $address_data )
		{
			if( $this->recipients_tin )
			{
				$recipients_tin = ! empty($address_data['tax_id_num']) ? __( 'TIN:  ', 'ups-woocommerce-shipping' ).$address_data['tax_id_num'] :'';

				$array['{tax_id_num}'] = $recipients_tin;
			}

			return $array; 
		}

		public function ph_ups_address_formats( $formats )
		{
			if( $this->recipients_tin )
			{
				foreach ($formats as $key => $format) {
					$formats[$key] = $format."\n{tax_id_num}";
				}	
			}	

			return $formats;
		}

		public function ph_ups_my_account_formated_address( $array, $customer_id, $name  )
		{
			if( $this->recipients_tin )
			{	
				
				$getting_recipients_tin = get_user_meta( $customer_id, $name . '_tax_id_num', true );
				$recipients_tin			= ( isset($getting_recipients_tin) ) ? $getting_recipients_tin : '';

				$array['tax_id_num'] 	= ($name . '_tax_id_num' == 'shipping_tax_id_num') ? $recipients_tin : ( ($name . '_tax_id_num' == 'ship_to_tax_id_num') ? $recipients_tin : '' );
			}
			
			return $array; 
		}

		public function ph_ups_update_custom_fields($updated_data)
		{
			$this->ph_update_custom_field_datas();

			$updated_fields = explode("&",$updated_data);

			if(is_array($updated_fields)){
				foreach($updated_fields as $updated_field){
					$updated_field_values = explode('=',$updated_field);
					if(is_array($updated_field_values)){


						if(in_array('shipping_tax_id_num',$updated_field_values)){
							
							$this->ph_update_custom_field_datas( urldecode($updated_field_values[1] ) );
						}

						if(in_array('ship_to_tax_id_num',$updated_field_values)){
							
							$this->ph_update_custom_field_datas( urldecode($updated_field_values[1] ), true );
						}
					}
				}
			}
			WC()->cart->calculate_shipping();
		}

		public function ph_ups_update_custom_fields_details_in_package( $packages )
		{
			if( $this->recipients_tin )
			{
				foreach( $packages as &$package ) {
					if( ! empty($package['contents']) ) {

						$ups_recipients_tin 		= WC()->session->get('ph_ups_tax_id_num');
						$ups_shipto_recipients_tin 	= WC()->session->get('ph_shipto_tax_id_num');

						if( ! empty($ups_recipients_tin) ) {
							$package['ph_ups_tax_id_num'] = $ups_recipients_tin;
						}
						
						if( ! empty($ups_shipto_recipients_tin) ) {
							$package['ph_shipto_tax_id_num'] = $ups_shipto_recipients_tin;
						}
					}
				}
			}
			return $packages;
		}

		
		public function ph_add_custom_field_meta_data($order_id, $post){

			if( $this->recipients_tin )
			{	
				
				$recipients_tin 		= ( is_array($post) && isset($post['shipping_tax_id_num']) ) ? $post['shipping_tax_id_num'] : '';
				$shipto_recipients_tin 	= ( is_array($post) && isset($post['ship_to_tax_id_num']) ) ? $post['ship_to_tax_id_num'] : '';

				// To check Ship To Diff address is enabled or not in Admin's Order Page
				$ship_to_different_address 	= ( is_array($post) && isset($post['ship_to_different_address']) && !empty($post['ship_to_different_address']) ) ? true : false;

				// When Ship-To Different Address is not enabled, update the Ship To TIN with Billing TIN
				if( !$ship_to_different_address && empty($shipto_recipients_tin) ) {

					$shipto_recipients_tin = $recipients_tin;
				}

				update_post_meta( $order_id, 'ph_ups_shipping_tax_id_num', $recipients_tin );
				update_post_meta( $order_id, 'ph_ups_ship_to_tax_id_num', $shipto_recipients_tin );
				update_post_meta( $order_id, 'ph_ups_ship_to_different_address', $ship_to_different_address );
			}
		}
	}
	
	new ph_ups_custom_checkout_fields();
}