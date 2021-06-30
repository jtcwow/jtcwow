<?php

if( ! class_exists('Xa_Label_For_Vendor_Fedex_Support') ){
	
	class Xa_Label_For_Vendor_Fedex_Support {

		public function __construct() {

			global $xa_current_user;
			$this->user 		= $xa_current_user;
			$this->user_id 		= $this->user->ID;
			$this->order_id 	= isset( $_GET['wf_fedex_createshipment'] ) ? $_GET['wf_fedex_createshipment'] : ( isset( $_GET['wf_create_return_label'] ) ? $_GET['wf_create_return_label'] : '' );
			$this->service_code = isset( $_GET['create_shipment_service'] ) ? $_GET['create_shipment_service'] : '';
			$this->order 		= wc_get_order($this->order_id);

			if( ! class_exists('wf_fedex_woocommerce_shipping_admin_helper') ) {
				require_once ABSPATH.'wp-content/plugins/fedex-woocommerce-shipping/includes/class-wf-fedex-woocommerce-shipping-admin-helper.php';
			}

			add_filter( 'wf_user_permission_roles', array( $this, 'xa_give_permission_to_vendor_to_create_label' ) );
			add_filter( 'xa_fedex_settings', array($this, 'override_fedex_settings_by_vendor_details') );

			if( !empty($_GET['wf_create_return_label']) && isset($_GET['ph_fedex_shipment_id']) ) {

				$this->wf_create_return_shipment( $_GET['ph_fedex_shipment_id'], $this->order_id);
			}
			else if( isset( $_GET['wf_fedex_createshipment'] ) && !empty($this->order_id) && !empty($this->service_code) ) {

				$this->create_shipment();
			}
		}

		public function create_shipment() {

			if( !class_exists('wf_order') ) {
				require_once ABSPATH.'wp-content/plugins/fedex-woocommerce-shipping/includes/class-wf-legacy.php';
			}

			$fedex_admin 		= new wf_fedex_woocommerce_shipping_admin_helper();
			$this->fedex_admin 	= $fedex_admin;
			$this->order 		= ( WC()->version < '2.7.0' ) ? new WC_Order( $this->order_id ) : new wf_order( $this->order_id );
			$fedex_admin->order = $this->order;
			
			$packages 			= $this->xa_modify_the_package_based_on_product_author( $fedex_admin->wf_get_package_from_order($this->order) );
			$fedex_packages 	= array();

			$fedex_admin->order_id = $this->order_id;

			$this->is_international = false;

			// Get Fedex Packages
			if( !empty($packages) && is_array($packages) ){
				foreach( $packages as $package ) {
					$fedex_packages[] = $fedex_admin->get_fedex_packages($package);
				}
			}
		
			if( !empty($fedex_packages) && is_array($fedex_packages) ){

				foreach( $fedex_packages as $key => $fedex_package ) {

					// Assign service to every package
					foreach( $fedex_package as $fedex_package_key => $package ) {
						$fedex_packages[$key][$fedex_package_key]['service'] = $this->service_code;
						$fedex_package[$fedex_package_key]['service'] = $this->service_code;
					}

					// $fedex_admin->print_label_processor($fedex_package, current($packages) );
					$package = current($packages);
					$fedex_admin->residential_address_validation( $package );
					$request_type= '';
					if(! empty( $this->smartpost_hub ) && $package['destination']['country'] == 'US' && $this->service_code == 'SMART_POST'){
						$request_type = 'smartpost';
					}elseif(strpos($this->service_code, 'FREIGHT') !== false){
						$request_type = 'freight';
					}

					if($fedex_admin->validate_package($fedex_package)){

						$fedex_requests   = $fedex_admin->get_fedex_requests( $fedex_package, $package, $request_type);

						if( $package['destination']['country'] != $this->origin_country ) {
							$this->is_international = true;
						}
						if ( $fedex_requests ) {
							$this->run_package_request( $fedex_requests );
						}
						$packages_to_quote_count = sizeof( $fedex_requests );
					}
				}
			}

		}

		/**
		 * Provide permission to generate Label in Fedex
		 * @param $roles array Array of User roles
		 * @return array Array of roles
		 */
		public function xa_give_permission_to_vendor_to_create_label( $roles ) {
			$roles[] = current($this->user->roles);
			return $roles;
		}

		/**
		 * Get the package of this vendor only.
		 */
		public function xa_modify_the_package_based_on_product_author( $packages ) {
			foreach( $packages as $vendor_id => $vendor_package ) {
				$count = 0;
				foreach( $vendor_package['contents'] as $key => $product_data ) {

					$product_author_id = $this->get_product_author_id($product_data['data']);
					if( $product_author_id != $this->user->ID ) {
						unset($vendor_package['contents'][$key]);
						$count++;
					}
				}
				if( $count == count($packages[$vendor_id]['contents']) ) {
					unset($packages[$vendor_id]);
				}
			}
			return $packages;
		}

		public function get_product_author_id( $product ) {
			$product_id = is_object($product) ? $product->get_id() : $product;
			$post 		= get_post($product_id);
			return $post->post_author;
		}

		public function run_package_request( $requests ) {
			$first_package = true;
			foreach ( $requests as $key => $request ) {
				if( $first_package ) {
					if( $this->commercial_invoice && $this->is_international ) {
						$company_logo = !empty($this->settings['company_logo']) ? true : false;
						$digital_signature = !empty($this->settings['digital_signature']) ? true : false;

						$special_servicetypes = !empty($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']) ? $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] : array();
						array_unshift( $special_servicetypes, 'ELECTRONIC_TRADE_DOCUMENTS' );
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = $special_servicetypes;
						
						$request['RequestedShipment']['SpecialServicesRequested']['EtdDetail']['RequestedDocumentCopies'] = 'COMMERCIAL_INVOICE';
						$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'COMMERCIAL_INVOICE';
						$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['Format']['ImageType'] = 'PDF';
						$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['Format']['StockType'] = 'PAPER_LETTER';
						
						if($company_logo){
							$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['CustomerImageUsages'][] = array(
								'Type' 	=> 'LETTER_HEAD', 
								'Id' 	=> 'IMAGE_1', 
							);
						}

						if($digital_signature){
							$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['CustomerImageUsages'][] = array(
								'Type' 	=> 'SIGNATURE',
								'Id' 	=> 'IMAGE_2',
							);
						}
					}
					$result = $this->fedex_admin->get_result( $request );
					$this->process_result( $result , $request );
				} else {
					$result = $this->fedex_admin->get_result( $request );
					$this->process_result( $result, $request );
				}
				$first_package =  false;
			}

			if(!empty($this->fedex_admin->tracking_ids)){
				// Auto fill tracking info.
				$shipment_id_cs = $this->fedex_admin->tracking_ids;
				Ph_FedEx_Tracking_Util::update_tracking_data( $this->order_id, $shipment_id_cs, 'fedex', WF_Tracking_Admin_FedEx::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_FedEx::SHIPMENT_RESULT_KEY );
			}

			if( VENDOR_PLUGIN == 'dokan_lite' && isset( $_GET['dokan_dashboard'] ) && !empty($_GET['dokan_dashboard']) )
			{	
				$dash_board_id 	= 	dokan_get_option( 'dashboard', 'dokan_pages' );
				$url 			= 	esc_url( get_permalink($dash_board_id).'orders/' );
				$page_url		=	html_entity_decode( esc_url( wp_nonce_url( add_query_arg( array( 'order_id' => $this->order_id ), $url ), 'dokan_view_order' ) ) );
				
				wp_redirect( $page_url );

				exit;

			}else{
				$url = get_permalink( get_option('woocommerce_myaccount_page_id') );
				wp_redirect( $url.'ph-all-order/?ph_view_order_on_front_end='.$this->order_id );
				exit;
			}
		}

		private function process_result( $result = '' , $request) {
			if(!$result)
				return false;
			
			if ( $result->HighestSeverity != 'FAILURE' && $result->HighestSeverity != 'ERROR' && ! empty ($result->CompletedShipmentDetail) ) {
				
				if( property_exists($result->CompletedShipmentDetail,'CompletedPackageDetails') ){
					if(is_array($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds)){
						foreach($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds as $track_ids){
							if($track_ids->TrackingIdType != 'USPS'){
								$shipmentId = $track_ids->TrackingNumber;	
								$tracking_completedata = $track_ids; 		
							}else{
								$usps_shipmentId = $track_ids->TrackingNumber;
							}
						}
					}
					else{
						$shipmentId = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;		
						$tracking_completedata = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds;
					}	
				}
				elseif(property_exists($result->CompletedShipmentDetail,'MasterTrackingId')){
					$shipmentId = $result->CompletedShipmentDetail->MasterTrackingId->TrackingNumber;		
					$tracking_completedata = $result->CompletedShipmentDetail->MasterTrackingId;				
				}			
				
				//if return label
				if( !empty($this->shipmentId) && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails->Label,'ShippingDocumentDisposition') && $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ShippingDocumentDisposition == 'RETURNED'){
					
					$package_shipping_label = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
					if(base64_encode(base64_decode($package_shipping_label, true)) === $package_shipping_label){  //For nusoap encoded label response
						$return_label = $package_shipping_label;
					}
					else{
						$return_label = base64_encode($package_shipping_label);
					}
					$returnlabel_type = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType; //Shipment ImageType

					if( VENDOR_PLUGIN == 'dokan_lite' )
					{

						add_post_meta($this->order_id, 'wf_woo_fedex_returnShipmetId', $shipmentId, true);
						add_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_'.$this->shipmentId, $return_label, true);

						if( !empty($returnlabel_type) ){
							
							add_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_image_type_'.$this->shipmentId, $returnlabel_type, true);
						}
					}else{
						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_returnShipmetId'.$this->shipmentId, $shipmentId, true);
						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_returnLabel_'.$shipmentId, $return_label, true);
						if( !empty($returnlabel_type) ){
							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_returnLabel_image_type_'.$shipmentId, $returnlabel_type, true);
						}
					}

					return;				
				}
				
				if( !empty($result->CompletedShipmentDetail->MasterTrackingId) && empty($this->master_tracking_id) )
				{
					$this->master_tracking_id = $result->CompletedShipmentDetail->MasterTrackingId;
					$this->fedex_admin->master_tracking_id = $result->CompletedShipmentDetail->MasterTrackingId;
				}
					
				$shippingLabel 				= array();
				$addittional_label 			= array();
				$addittional_label_type 	= array();

				if( property_exists($result->CompletedShipmentDetail,'CompletedPackageDetails') && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'Label') ){

					$package_shipping_label=$result->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
					if(base64_encode(base64_decode($package_shipping_label, true)) === $package_shipping_label){  //For nusoap encoded label response
						$shippingLabel = $package_shipping_label;
					}
					else{
						$shippingLabel = base64_encode($package_shipping_label);
					}
					$shippinglabel_type = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType; //Shipment ImageType
					
					if(property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'CodReturnDetail') && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails->CodReturnDetail,'Label') ){

						$cod_return_label = $result->CompletedShipmentDetail->CompletedPackageDetails->CodReturnDetail->Label->Parts->Image;

						//For nusoap encoded label response
						if(base64_encode(base64_decode($cod_return_label, true)) === $cod_return_label){  
							$addittional_label['COD Return'] = $cod_return_label;
						}
						else{
							$addittional_label['COD Return'] = base64_encode($cod_return_label);
						}
						$addittional_label_type['COD Return'] = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType;
					}

					if(property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'PackageDocuments')){
						$package_documents = $result->CompletedShipmentDetail->CompletedPackageDetails->PackageDocuments;
						if(is_array($package_documents)){
							foreach($package_documents as $document_key=>$package_document){
								$package_additional_label = $package_document->Parts->Image;
								if(base64_encode(base64_decode($package_additional_label, true)) === $package_additional_label){
									$addittional_label[$document_key] = $package_additional_label;
								}else{
									$addittional_label[$document_key] = base64_encode($package_additional_label);
								}
								$addittional_label_type[$document_key] = $package_document->ImageType;
							}
						}
					}
					
					
					if(property_exists($result->CompletedShipmentDetail,'ShipmentDocuments')){
						$commercial_invoice_label=$result->CompletedShipmentDetail->ShipmentDocuments->Parts->Image;
						if(base64_encode(base64_decode($commercial_invoice_label, true)) === $commercial_invoice_label){
							$addittional_label['Commercial Invoice'] = $commercial_invoice_label;
						}else{
							$addittional_label['Commercial Invoice'] = base64_encode($commercial_invoice_label);
						}
						$addittional_label_type['Commercial Invoice'] = $result->CompletedShipmentDetail->ShipmentDocuments->ImageType;
					}
				} 
				elseif(property_exists($result->CompletedShipmentDetail,'ShipmentDocuments')){ 
					//As per the documentation. This case will never occure. 
					$shipment_document_label = $result->CompletedShipmentDetail->ShipmentDocuments->Parts->Image;
					if(base64_encode(base64_decode($shipment_document_label, true)) === $shipment_document_label){
						$shippingLabel = $shipment_document_label;
					}
					else{
						$shippingLabel = base64_encode($shipment_document_label);
					}
					$shippinglabel_type = $result->CompletedShipmentDetail->ShipmentDocuments->ImageType;
				}
				
				if( !empty($shippingLabel) && property_exists($result->CompletedShipmentDetail,'AssociatedShipments') && property_exists($result->CompletedShipmentDetail->AssociatedShipments,'Label') ){

					$associated_documents = $result->CompletedShipmentDetail->AssociatedShipments->Label;

					if( ! empty($result->CompletedShipmentDetail->AssociatedShipments->TrackingId) ) {

						$associated_documents_tracking_id = $result->CompletedShipmentDetail->AssociatedShipments->TrackingId->TrackingNumber;

						$this->fedex_admin->tracking_ids .= $associated_documents_tracking_id.',';

						if( VENDOR_PLUGIN == 'dokan_lite' )
						{
							add_post_meta( $this->order_id, '_ph_woo_fedex_additional_tracking_number_'.$shipmentId, $associated_documents_tracking_id );
						}else{
							add_post_meta( $this->order_id, '_ph_vendor_'.$this->user_id.'_woo_fedex_additional_tracking_number_'.$shipmentId, $associated_documents_tracking_id );
						}
					}

					if(!empty($associated_documents)){
						
							$associated_shipment_label = $associated_documents->Parts->Image;
							if(base64_encode(base64_decode($associated_shipment_label, true)) === $associated_shipment_label){
								$addittional_label['AssociatedLabel'] = $associated_shipment_label;
							}
							else{
								$addittional_label['AssociatedLabel'] = base64_encode($associated_shipment_label);
							}
							$addittional_label_type['AssociatedLabel'] = $associated_documents->ImageType;
					}
				}
				
				 if(!empty($shipmentId) && !empty($shippingLabel)){

					if( VENDOR_PLUGIN == 'dokan_lite' )
					{
						add_post_meta($this->order_id, 'wf_woo_fedex_shipmentId', $shipmentId, false);
						add_post_meta($this->order_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, $shippingLabel, true);
						add_post_meta($this->order_id, 'wf_woo_fedex_packageDetails_'.$shipmentId, $this->fedex_admin->wf_get_parcel_details($request) , true);
						add_post_meta($this->order_id, 'wf_woo_fedex_request_'.$shipmentId, $request , true);

						if( !empty($shippinglabel_type) ){
							add_post_meta($this->order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipmentId, $shippinglabel_type, true);
						}

						if(isset($tracking_completedata)){
							add_post_meta($this->order_id, 'wf_woo_fedex_tracking_full_details_'.$shipmentId, $tracking_completedata, true);
						}			

						if( !empty($request['RequestedShipment']['ServiceType']) ){
							add_post_meta($this->order_id, 'wf_woo_fedex_service_code'.$shipmentId, $request['RequestedShipment']['ServiceType'], true);
						}

						if(!empty($usps_shipmentId)){
							add_post_meta($this->order_id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, $usps_shipmentId, true);
						}
					}else{

						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_shipmentId', $shipmentId, false);
						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_shippingLabel_'.$shipmentId, $shippingLabel, true);
						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_packageDetails_'.$shipmentId, $this->fedex_admin->wf_get_parcel_details($request) , true);
						add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_request_'.$shipmentId, $request , true);

						if( !empty($shippinglabel_type) ){
							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_shippingLabel_image_type_'.$shipmentId, $shippinglabel_type, true);
						}

						if(isset($tracking_completedata)){
							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_tracking_full_details_'.$shipmentId, $tracking_completedata, true);
						}			
						
						if( !empty($request['RequestedShipment']['ServiceType']) ){
							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_service_code'.$shipmentId, $request['RequestedShipment']['ServiceType'], true);
						}

						if(!empty($usps_shipmentId)){
							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_usps_trackingid_'.$shipmentId, $usps_shipmentId, true);
						}

					}

					if($this->fedex_admin->add_trackingpin_shipmentid == 'yes' && !empty($shipmentId)){
						//$this->order->add_order_note( sprintf( __( 'Fedex Tracking-pin #: %s.', 'ph-multi-vendor-shipping' ), $shipmentId) , true);
						$this->fedex_admin->tracking_ids = $this->fedex_admin->tracking_ids . $shipmentId . ',';			
					}
					
					if($this->fedex_admin->add_trackingpin_shipmentid == 'yes' && !empty($usps_shipmentId)){
						//$this->order->add_order_note( sprintf( __( 'Fedex Smart Post USPS Tracking-pin #: %s.', 'ph-multi-vendor-shipping' ), $usps_shipmentId) , true);
					}
					
					if(!empty($addittional_label)){

						if( VENDOR_PLUGIN == 'dokan_lite' )
						{
							add_post_meta($this->order_id, 'wf_fedex_additional_label_'.$shipmentId, $addittional_label, true);

							if(!empty($addittional_label_type)){
								add_post_meta($this->order_id, 'wf_fedex_additional_label_image_type_'.$shipmentId, $addittional_label_type, true);		
							}

						}else{

							add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_fedex_additional_label_'.$shipmentId, $addittional_label, true);	
							if(!empty($addittional_label_type)){
								add_post_meta($this->order_id, 'wf_vendor_'.$this->user_id.'_fedex_additional_label_image_type_'.$shipmentId, $addittional_label_type, true);		
							}	
						}	
					}							
				} 
				do_action('xa_fedex_label_generated_successfully',$shipmentId,$shippingLabel,$this->order_id);
			}else{

				$this->shipmentErrorMessage = get_post_meta($this->order_id, 'wf_woo_fedex_shipmentErrorMessage', 1);

				if( is_object($result->Notifications) ){

					$this->shipmentErrorMessage .=  $this->fedex_admin->result_notifications($result->Notifications, $error_message='');

				}else if( is_array($result->Notifications) ){

					$this->shipmentErrorMessage .=  $this->fedex_admin->result_notifications($result->Notifications[0], $error_message='');
				}

				if( function_exists('wc_add_notice') && !is_admin() ) {

					if( $this->fedex_admin->debug )
					{
						wc_add_notice( '<pre class="debug_info">'.print_r($request,1).'</pre>','notice');
						wc_add_notice( '<pre class="debug_info">'.print_r($result,1).'</pre>','notice');
					}

					wc_add_notice($this->shipmentErrorMessage,'error');
				}
			}
		}

		/**
		 * Override Fedex settings by vendor details .
		 */
		public function override_fedex_settings_by_vendor_details( $settings) {

			// To get the vendor address
			$this->get_vendor_details();
			$this->settings 							= $settings;

			if( $this->settings['ship_from_address'] == 'vendor_address' ) {

			// Override Fedex Api Credentials with Vendor
				$account_number 	= get_the_author_meta( 'xa_fedex_account_number', $this->user->ID );
				$meter_number 		= get_the_author_meta( 'xa_fedex_meter_number', $this->user->ID );
				$api_key 			= get_the_author_meta( 'xa_fedex_web_services_key', $this->user->ID );
				$api_pass 			= get_the_author_meta( 'xa_fedex_web_services_password', $this->user->ID );

				if( !empty($account_number) && !empty($meter_number) && !empty($api_key) && !empty($api_pass) )
				{
					$this->settings['account_number'] 			= $account_number;
					$this->settings['meter_number'] 			= $meter_number;
					$this->settings['api_key'] 					= $api_key;
					$this->settings['api_pass'] 				= $api_pass;
				}

				// Override address with vendor address
				$this->settings['shipper_person_name'] 		= $this->vendor_address['first_name'].' '. $this->vendor_address['last_name'];
				$this->settings['shipper_company_name']		= $this->vendor_address['company'];
				$this->settings['shipper_phone_number']		= $this->vendor_address['phone'];
				$this->settings['frt_shipper_street']		= $this->vendor_address['address_1'];
				$this->settings['shipper_street_2']			= $this->vendor_address['address_2'];
				$this->settings['origin']					= $this->vendor_address['postcode'];
				$this->settings['freight_shipper_city']		= $this->vendor_address['city'];
				$this->settings['origin_country']			= $this->vendor_address['country'].':'.$this->vendor_address['state'];
				$this->settings['shipper_email']			= $this->vendor_address['email'];

				// Override Tax Payor Identification number
				$this->settings['tin_number']				= $this->vendor_address['tin_number'];

				$this->commercial_invoice 			= (isset($this->settings['commercial_invoice']) && ($this->settings['commercial_invoice'] == 'yes')) ? true : false;
				$this->origin_country 				= $this->vendor_address['country'];
			}
			
			return $this->settings;
		}

		/**
		 * Get Vendor details, like address.
		 */
		public function get_vendor_details() {

			$vendor_address 				= class_wf_vendor_addon_admin::get_vendor_address($this->user->ID);
			
			$this->vendor_address 			= class_wf_vendor_addon_admin::wf_formate_origin_address($vendor_address);
		}

		public function wf_fedex_viewlabel(){
			$shipmentDetails = explode('|', base64_decode($_GET['xa_view_fedex_label_vendor']));

			if (count($shipmentDetails) != 2) {
				exit;
			}
			
			$shipmentId = $shipmentDetails[0]; 
			$post_id = $shipmentDetails[1]; 
			$shipping_label = get_post_meta($post_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
			$shipping_label_image_type = get_post_meta($post_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipmentId, true);
			
			
			if( empty($shipping_label_image_type) ){
				$shipping_label_image_type = $this->image_type;
			}
			$file_name = wp_upload_dir();
			$filename  = $file_name['path']."/fedex_shipment_label_$shipmentId.$shipping_label_image_type";
			file_put_contents( $filename, base64_decode($shipping_label) );
		}

		public function wf_create_return_shipment( $shipment_id, $order_id ){

			$this->shipmentId 		= $shipment_id;
			$fedex_admin 			= new wf_fedex_woocommerce_shipping_admin_helper();
			$this->fedex_admin 		= $fedex_admin;
			$fedex_admin->order 	= $this->order;

			if( VENDOR_PLUGIN == 'dokan_lite' )
			{
				$request = get_post_meta( $order_id, 'wf_woo_fedex_request_'.$shipment_id, true );
			}else{
				$request = get_post_meta( $order_id, 'wf_vendor_'.$this->user_id.'_woo_fedex_request_'.$shipment_id, true );
			}
			
			if( ! empty($request) ){
				$request['RequestedShipment']['ServiceType'] 				= $this->service_code;

				$shipper_address = $request['RequestedShipment']['Shipper'];
				$request['RequestedShipment']['Shipper'] 					= $request['RequestedShipment']['Recipient'];
				$request['RequestedShipment']['Recipient']					= $shipper_address;

				$total_weight = 0;
				foreach ($request['RequestedShipment']['RequestedPackageLineItems'] as $key => $item) {
					$request['RequestedShipment']['RequestedPackageLineItems'][$key]['SequenceNumber'] = 1;
					$request['RequestedShipment']['RequestedPackageLineItems'][$key]['GroupNumber'] = 1;
					$total_weight += $item['Weight']['Value'];
				}
				$request['RequestedShipment']['TotalWeight']['Value']		= $total_weight;


				$request['RequestedShipment']['SpecialServicesRequested']['ReturnShipmentDetail']['ReturnType']	= 'PRINT_RETURN_LABEL';
				// $request['RequestedShipment']['SpecialServicesRequested']['ReturnShipmentDetail']['ReturnEMailDetail']['MerchantPhoneNumber']	= '';
				$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'RETURN_SHIPMENT';
				
				$request['RequestedShipment']['PackageCount'] 				= 1;

				unset($request['RequestedShipment']['RequestedPackageLineItems']['SequenceNumber'], $request['RequestedShipment']['SpecialServicesRequested']['CodDetail'] );

				foreach( $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] as $key => $special_service ) {

					if( $special_service == 'COD' ) {
						unset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][$key]);			// Unset COD in return request
					}

					if( $special_service == 'ELECTRONIC_TRADE_DOCUMENTS' ) {
						unset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][$key]);	// Unset ELECTRONIC_TRADE_DOCUMENTS in return request
					}
				}

				$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']=array_values($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']);

				//unset COD for when COD node added in package level
				if( isset($request['RequestedShipment']['RequestedPackageLineItems']) ){
					foreach( $request['RequestedShipment']['RequestedPackageLineItems'] as $key => $attribute ) {
						foreach( $attribute as $type => $value ){
							if( $type == 'SpecialServicesRequested'){
								foreach( $value['SpecialServiceTypes'] as $index => $special_service ){
									if( $special_service == 'COD' ){
										unset($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes'][$index]);
										unset($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['CodDetail']);

										$request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes'] = array_values($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes']);
									}
								}
							}
						}
					}
				}

				if( isset($request['RequestedShipment']['CustomsClearanceDetail']) && ( $request['RequestedShipment']['Shipper']['Address']['CountryCode'] != $request['RequestedShipment']['Recipient']['Address']['CountryCode'] )  )
				{

					$request['RequestedShipment']['CustomsClearanceDetail']['CustomsOptions'] = array();

					$request['RequestedShipment']['CustomsClearanceDetail']['CustomsOptions']['Type'] = $this->fedex_admin->int_return_label_reason;

					if( $this->fedex_admin->int_return_label_reason == "OTHER" )
					{
						$request['RequestedShipment']['CustomsClearanceDetail']['CustomsOptions']['Description'] = $this->fedex_admin->int_return_label_desc;
					}
				}

				// Alternative Return is not supported for Return Shipments
				if( isset($request['RequestedShipment']['LabelSpecification']) && isset($request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin']) )
				{
					unset( $request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin'] );
				}

			}

			$result = $this->fedex_admin->get_result( $request );

			$this->process_result($result, $request );

			if( VENDOR_PLUGIN == 'dokan_lite' && isset( $_GET['dokan_dashboard'] ) && !empty($_GET['dokan_dashboard']) )
			{	
				$dash_board_id 	= 	dokan_get_option( 'dashboard', 'dokan_pages' );
				$url 			= 	esc_url( get_permalink($dash_board_id).'orders/' );
				$page_url		=	html_entity_decode( esc_url( wp_nonce_url( add_query_arg( array( 'order_id' => $this->order_id ), $url ), 'dokan_view_order' ) ) );
				
				wp_redirect( $page_url );

				exit;

			}else{

				$url = get_permalink( get_option('woocommerce_myaccount_page_id') );
				wp_redirect( $url.'ph-all-order/?ph_view_order_on_front_end='.$this->order_id );

				exit;
			}
			
		}
	}
}

if( ! class_exists('Xa_Print_Vendor_Fedex_Label') ) {
	class Xa_Print_Vendor_Fedex_Label {

		public function __construct( $order_id, $user_id, $shipment_id ) {
			$this->print_label( $order_id, $user_id, $shipment_id );
		}

		/**
		 * Print FedEx Label
		 */
		public function print_label( $order_id, $user_id, $shipment_id ) {

			if( VENDOR_PLUGIN == 'dokan_lite' )
			{
				$shipping_label_image_type 	= get_post_meta( $order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipment_id, true );
				$shipping_label 			= get_post_meta( $order_id, 'wf_woo_fedex_shippingLabel_'.$shipment_id, true );
			}else{
				$shipping_label_image_type 	= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_shippingLabel_image_type_'.$shipment_id, true );
				$shipping_label 			= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_shippingLabel_'.$shipment_id, true );
			}

			header('Content-Type: application/'.$shipping_label_image_type);
			header('Content-disposition: attachment; filename="ShipmentArtifact-' . $shipment_id . '.'.$shipping_label_image_type.'"');
			print(base64_decode($shipping_label)); 
			exit;
		}

		public static function print_fedex_return_label(){

			list( $order_id, $user_id, $shipment_id ) = explode( '|', base64_decode($_GET['xa_print_vendor_fedex_return_label']) );

			if( VENDOR_PLUGIN == 'dokan_lite' )
			{

				$shipping_label_image_type 	= get_post_meta( $order_id, 'wf_woo_fedex_returnLabel_image_type_'.$shipment_id, true );
				$shipping_label 			= get_post_meta( $order_id, 'wf_woo_fedex_returnLabel_'.$shipment_id, true );

				if( empty($shipping_label_image_type) )
				{
					$shipping_label_image_type 	= get_post_meta( $order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipment_id, true );
				}

			}else{

				$shipping_label_image_type 	= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_returnLabel_image_type_'.$shipment_id, true );
				$shipping_label 			= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_returnLabel_'.$shipment_id, true );
			}

			header('Content-Type: application/'.$shipping_label_image_type);
			header('Content-disposition: attachment; filename="ShipmentArtifact-' . $shipment_id . '.'.$shipping_label_image_type.'"');
			print(base64_decode($shipping_label)); 
			exit;
		}

		public static function print_fedex_additional_label() {

			list( $order_id, $user_id, $shipment_id, $add_key ) = explode( '|', base64_decode($_GET['ph_print_vendor_additional_label']) );

			if( VENDOR_PLUGIN == 'dokan_lite' )
			{
				$additional_label_image_type 	= get_post_meta( $order_id, 'wf_fedex_additional_label_image_type_'.$shipment_id, true );
				$additional_label 			= get_post_meta( $order_id, 'wf_fedex_additional_label_'.$shipment_id, true );
				$image_type 				= '';

				if( !empty($additional_label_image_type[$add_key])){

					$image_type 	= $additional_label_image_type[$add_key];
				}else{
					$image_type 	= get_post_meta( $order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipment_id, true );
				}

			}else{
				// $additional_label_image_type 	= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_shippingLabel_image_type_'.$shipment_id, true );
				// $additional_label 			= get_post_meta( $order_id, 'wf_vendor_'.$user_id.'_woo_fedex_shippingLabel_'.$shipment_id, true );
			}

			if( !empty($additional_label) && !empty($additional_label[$add_key]) && !empty($image_type) )
			{
				header('Content-Type: application/'.$image_type);
				header('Content-disposition: attachment; filename="Addition-doc-' . $shipment_id . '.'.$image_type.'"');
				print(base64_decode($additional_label[$add_key])); 
				exit;
			}
			
		}

		/**
		 * Void FedEx Shipment
		 */
		public static function void_shipment() {

			list( $order_id, $user_id, $shipment_id ) = explode( '|', base64_decode($_GET['ph_void_vendor_fedex_shipment']) );

			if( ! class_exists('wf_fedex_woocommerce_shipping_admin_helper') ) {
				require_once ABSPATH.'wp-content/plugins/fedex-woocommerce-shipping/includes/class-wf-fedex-woocommerce-shipping-admin-helper.php';
			}

			if( VENDOR_PLUGIN == 'dokan_lite' )
			{
				$tracking_completedata 	= get_post_meta($order_id, 'wf_woo_fedex_tracking_full_details_'.$shipment_id, true);

				$ph_fedex_admin_helper 	= new wf_fedex_woocommerce_shipping_admin_helper();
				
				if( !empty($tracking_completedata) ) {

					$a = $ph_fedex_admin_helper->void_shipment($order_id,$shipment_id,$tracking_completedata);

					$void_shipments = get_post_meta($order_id, 'wf_woo_fedex_shipment_void',false);

					if( empty($void_shipments) )
					{
						// Do Nothing.
					}else{
						foreach($void_shipments as $void_shipment_id){
							delete_post_meta($order_id, 'wf_woo_fedex_packageDetails_'.$void_shipment_id);
							delete_post_meta($order_id, 'wf_woo_fedex_shippingLabel_'.$void_shipment_id);
							delete_post_meta($order_id, 'wf_woo_fedex_service_code'.$void_shipment_id);
							delete_post_meta($order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$void_shipment_id);
							delete_post_meta($order_id, 'wf_woo_fedex_shipmentId',$void_shipment_id);
							delete_post_meta($order_id, 'wf_woo_fedex_shipment_void',$void_shipment_id);
							delete_post_meta($order_id, 'wf_fedex_additional_label_',$void_shipment_id);
						}

						delete_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage');		
						delete_post_meta($order_id, 'wf_woo_fedex_service_code');
						delete_post_meta($order_id, 'wf_woo_fedex_shipmentErrorMessage');
					}

				}
			}

			if( VENDOR_PLUGIN == 'dokan_lite' && isset( $_GET['dokan_dashboard'] ) && !empty($_GET['dokan_dashboard']) )
			{	
				$dash_board_id 	= 	dokan_get_option( 'dashboard', 'dokan_pages' );
				$url 			= 	esc_url( get_permalink($dash_board_id).'orders/' );
				$page_url		=	html_entity_decode( esc_url( wp_nonce_url( add_query_arg( array( 'order_id' => $order_id ), $url ), 'dokan_view_order' ) ) );
				
				wp_redirect( $page_url );

				exit;

			}else{
				$url = get_permalink( get_option('woocommerce_myaccount_page_id') );
				wp_redirect( $url.'ph-all-order/?ph_view_order_on_front_end='.$order_id );
				exit;
			}

			
		}

	}	// End of Class
}