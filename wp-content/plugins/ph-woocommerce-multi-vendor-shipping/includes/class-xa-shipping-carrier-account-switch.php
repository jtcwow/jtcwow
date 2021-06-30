<?php

/**
 * Switch the Shipping Service Account Credentials
 */
if( !class_exists('Xa_Shipping_Carrier_Account_Switch') ) {
	class Xa_Shipping_Carrier_Account_Switch{
		
		public function __construct() {

			if( !isset($this->fedex_settings) && empty($this->fedex_settings) ) {
				$this->fedex_settings = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings', null );
			}

			if( !isset($this->multi_carrier_settings) && empty($this->multi_carrier_settings) ) {
				$this->multi_carrier_settings = get_option( 'woocommerce_wf_multi_carrier_shipping_settings', null );
			}

			if( isset($this->fedex_settings['ship_from_address']) && $this->fedex_settings['ship_from_address'] == 'vendor_address' ) {

				// Change the FedEx Credentials while fetching the rates
				add_filter( 'xa_fedex_rate_request', array( $this, 'ph_switch_fedex_account_in_rate_request'), 9, 2 );

				// Change the FedEx Credentials while generating the label
				add_filter( 'wf_fedex_request', array( $this, 'ph_switch_fedex_account_in_shipment_request' ), 9, 3 );

				// Change the FedEx Credentials while voiding the shipment
				add_filter( 'ph_fedex_void_shipment_request', array( $this, 'ph_switch_fedex_account_in_void_shipment_request' ), 9, 4 );

				// Change the FedEx Account Number in Return Request for Duties Payor
				add_filter( 'ph_fedex_return_label_request', array( $this, 'ph_switch_fedex_account_in_return_shipment_request' ), 9, 2 );

			}

			if( isset($this->multi_carrier_settings['ship_from_address']) && $this->multi_carrier_settings['ship_from_address'] == 'vendor_address' ) {
				// Change the Multi Carrier Credentials while fetching the rates
				add_filter( 'xa_multicarrier_carriers_accounts', array( $this, 'xa_get_multicarrier_vendor_accounts'), 9, 2 );

			}
		}
		
		public function xa_get_multicarrier_vendor_accounts( $account_details, $package ) {

			$first_product	= current($package['contents']);
			
			$parent_id 		= $first_product['data']->get_parent_id();
			$product_id 	= !empty( $parent_id ) ? $parent_id : $first_product['data']->get_id();

			$post_author_details	= $this->xa_get_post_author($product_id);
			
			if( $post_author_details->get('xa_fedex_account_number') ) {

				$account_details['fedex']['api_key'] 		= $post_author_details->get('xa_fedex_web_services_key');
				$account_details['fedex']['api_password']	= $post_author_details->get('xa_fedex_web_services_password');
				$account_details['fedex']['account_number']	= $post_author_details->get('xa_fedex_account_number');
				$account_details['fedex']['meter_number'] 	= $post_author_details->get('xa_fedex_meter_number');
			}

			if( $post_author_details->get('xa_ups_account_number') ) {

				$account_details['ups']['key'] 				= $post_author_details->get('xa_ups_access_key');
				$account_details['ups']['password']			= $post_author_details->get('xa_ups_password');
				$account_details['ups']['account_number'] 	= $post_author_details->get('xa_ups_account_number');
				$account_details['ups']['username'] 		= $post_author_details->get('xa_ups_user_id');
			}
			
			if( $post_author_details->get('usps_user_id') ){
				$account_details['usps']['username'] 	= $post_author_details->get('usps_user_id');
				$account_details['usps']['password'] 	= $post_author_details->get('usps_password');
			}
			
			if( $post_author_details->get('stamps_usps_username') ) {

				$account_details['stamps']['username'] 	= $post_author_details->get('stamps_usps_username');
				$account_details['stamps']['password'] 	= $post_author_details->get('stamps_usps_password');
			}

			if( $post_author_details->get('dhl_account_number') ) {

				$account_details['dhl']['account_number'] 	= $post_author_details->get('dhl_account_number');
				$account_details['dhl']['siteid'] 			= $post_author_details->get('dhl_siteid');
				$account_details['dhl']['password'] 		= $post_author_details->get('dhl_password');
			}

			return $account_details;
		}

		/**
		 * Change the FedEx Credentials depending on Product Author
		 * @param array $request FedEx request
		 * @param array $fedex_package FedEx Package
		 * @return array FedEx request
		 */
		public function ph_switch_fedex_account_in_rate_request( $request, $fedex_packages ) {
			
			foreach( $fedex_packages as $fedex_package ) {

				if( !empty($fedex_package['packed_products']) ) {

					$ph_product 	= current($fedex_package['packed_products']);
					
					$parent_id 		= $ph_product->get_parent_id();
					$product_id 	= !empty( $parent_id ) ? $parent_id : $ph_product->get_id();

					$post_author_details 	= $this->xa_get_post_author($product_id);

					if( $post_author_details->get('xa_fedex_account_number') )
					{
						$request['WebAuthenticationDetail']['UserCredential']	=   array(
								'Key'		=>	$post_author_details->get('xa_fedex_web_services_key'),
								'Password'  =>	$post_author_details->get('xa_fedex_web_services_password'),
						);

						$request['ClientDetail']	= array(
							'AccountNumber'		=> $post_author_details->get('xa_fedex_account_number'),
							'MeterNumber'		=> $post_author_details->get('xa_fedex_meter_number'),
						);

						if( isset($request['RequestedShipment']['ShippingChargesPayment']) && isset($request['RequestedShipment']['ShippingChargesPayment']['PaymentType']) && $request['RequestedShipment']['ShippingChargesPayment']['PaymentType'] == 'SENDER' )
						{
							$request['RequestedShipment']['ShippingChargesPayment']['Payor']['ResponsibleParty']['AccountNumber'] = $post_author_details->get('xa_fedex_account_number');
						}
					}
				}
			}

			return $request;
		}

		/**
		 * Change the FedEx Credentials depending on the Product Author in Shipment Request
		 * @param array $request FedEx request
		 * @param object $order Wc_Order
		 * @param array $fedex_package FedEx package
		 * @return array FedEx Request
		 */
		public function ph_switch_fedex_account_in_shipment_request( $request, $order, $fedex_package ) {

			if( !empty($fedex_package['packed_products']) ) {

				$ph_product 	= current($fedex_package['packed_products']);
				
				$parent_id 		= $ph_product->get_parent_id();
				$product_id 	= !empty( $parent_id ) ? $parent_id : $ph_product->get_id();

				$post_author_details 	= $this->xa_get_post_author($product_id);

				if( $post_author_details->get('xa_fedex_account_number') )
				{
					$request['WebAuthenticationDetail']['UserCredential']	=   array(
							'Key'		=>	$post_author_details->get('xa_fedex_web_services_key'),
							'Password'  =>	$post_author_details->get('xa_fedex_web_services_password'),
					);
					$request['ClientDetail']	= array(
						'AccountNumber'		=> $post_author_details->get('xa_fedex_account_number'),
						'MeterNumber'		=> $post_author_details->get('xa_fedex_meter_number'),
					);

					if( isset($request['RequestedShipment']['ShippingChargesPayment']) && isset($request['RequestedShipment']['ShippingChargesPayment']['PaymentType']) && $request['RequestedShipment']['ShippingChargesPayment']['PaymentType'] == 'SENDER' )
					{
						$request['RequestedShipment']['ShippingChargesPayment']['Payor']['ResponsibleParty']['AccountNumber'] = $post_author_details->get('xa_fedex_account_number');
					}

					if( isset($request['RequestedShipment']['CustomsClearanceDetail']) ) {

						$custom_clearance_details = $request['RequestedShipment']['CustomsClearanceDetail'];

						if( isset($custom_clearance_details['DutiesPayment']) && isset($custom_clearance_details['DutiesPayment']['PaymentType']) && $custom_clearance_details['DutiesPayment']['PaymentType'] == 'SENDER' ) {


							$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
								'AccountNumber'	=> $post_author_details->get('xa_fedex_account_number'),
								'CountryCode' 	=> $request['RequestedShipment']['Shipper']['Address']['CountryCode'],
							);
						}

					}
				}
			}

			return $request;
		}

		/**
		 * Change the FedEx Credentials depending on the Product Author in Void Shipment Request
		 * @param array $request FedEx request
		 * @param int $order_id Order Id
		 * @param int $shipment_id Tracking Number
		 * @param object $tracking_data Tracking Data
		 * @return array FedEx Request
		 */
		public function ph_switch_fedex_account_in_void_shipment_request( $request, $order_id, $shipment_id, $tracking_data ) {

			if( !empty($request) && !empty($order_id) ) {

				$fedex_request 	= get_post_meta( $order_id, 'wf_woo_fedex_request_'.$shipment_id );

				if( !empty( $fedex_request ) && is_array( $fedex_request ) && isset( $fedex_request[0] ) )
				{
					$request['WebAuthenticationDetail']['UserCredential']	=   array(
						'Key'		=>	$fedex_request[0]['WebAuthenticationDetail']['UserCredential']['Key'],
						'Password'  =>	$fedex_request[0]['WebAuthenticationDetail']['UserCredential']['Password'],
					);

					$request['ClientDetail']	= array(
						'AccountNumber'		=> $fedex_request[0]['ClientDetail']['AccountNumber'],
						'MeterNumber'		=> $fedex_request[0]['ClientDetail']['MeterNumber'],
					);
				}

			}

			return $request;
		}

		/**
		 * Change the FedEx Account Number depending on the Product Author in Return Shipment Request
		 * @param array $request FedEx request
		 * @param int $order_id Order Id
		 * @return array FedEx Request
		 */
		public function ph_switch_fedex_account_in_return_shipment_request( $request, $order_id ) {

			if( !empty($request) && !empty($order_id) ) {

				if( isset($request['RequestedShipment']['CustomsClearanceDetail']) ) {

					$custom_clearance_details = $request['RequestedShipment']['CustomsClearanceDetail'];

					// Duties Payment Type as Recepient is not allowed for Return Shipments
					if( isset($custom_clearance_details['DutiesPayment']) && isset($custom_clearance_details['DutiesPayment']['PaymentType']) && $custom_clearance_details['DutiesPayment']['PaymentType'] == 'SENDER' ) {

						
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
							'AccountNumber'	=> $request['ClientDetail']['AccountNumber'],
							'CountryCode' 	=> $request['RequestedShipment']['Recipient']['Address']['CountryCode'],
						);
					}

				}
			}

			return $request;
		}
		
		/**
		 * Get the Author Details of the Post
		 * @param int $id post id
		 * @return object WP_User
		 */
		public function xa_get_post_author( $id ) {

			$post 		= get_post($id);
			$author 	= get_user_by('id', $post->post_author);

			return $author;
		}
	}

	new Xa_Shipping_Carrier_Account_Switch();
}