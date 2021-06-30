<?php
class class_wf_vendor_addon_admin{
	
	public function __construct() {
		$this->wf_init();
	}

	public function wf_init(){
		
		if( !defined('VENDOR_PLUGIN') ) {
			return;
		}

		if( !isset($this->fedex_settings) && empty($this->fedex_settings) ) {
			$this->fedex_settings = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings', null );
		}

		if( !isset($this->multi_carrier_settings) && empty($this->multi_carrier_settings) ) {
			$this->multi_carrier_settings = get_option( 'woocommerce_wf_multi_carrier_shipping_settings', null );
		}

		if( isset($this->fedex_settings['ship_from_address']) && $this->fedex_settings['ship_from_address'] == 'vendor_address' ) {

			// FedEx Shipment Label Packages
			add_filter( 'wf_filter_label_packages', array($this, 'wf_vendor_label_packages'), 10, 4);

			// FedEx Shipment Label From Address
			add_filter( 'wf_filter_label_from_address', array($this, 'wf_vendor_label_from_address'), 10, 4);
		}

		// Add Vendor Id to FedEx Package
		add_filter( 'wf_fedex_packages', __CLASS__.'::add_vendor_id_to_fedex_package', 10, 2 );
		
		// To Support Vendor in UPS Plugin, get UPS Settings
		if( !isset($this->ups_settings) && empty($this->ups_settings) ) {
			$this->ups_settings = get_option( 'woocommerce_wf_shipping_ups_settings', null );
		}

		// If Ship From Address Preference is set to Vendor Address then only execute the UPS related functions
		if( isset($this->ups_settings['ship_from_address']) && $this->ups_settings['ship_from_address'] == 'vendor_address' ) {

			// Update the UPS Credentials in Rate Request
			add_filter( 'wf_ups_rate_request_data', array( $this, 'xa_change_ups_credentials_in_rate_request' ), 10, 3 );

			// Update Ship From Address
			add_filter( 'wf_ups_rate_request', array($this, 'ph_update_ship_from_address_in_rate_request'), 10, 2 );

			// Upadate the Package Origin Address in Shipment Package
			add_filter( 'wf_ups_filter_label_from_packages', array( $this, 'wf_vendor_label_packages' ), 10, 4 );

			// Split UPS Shipment Packages based on Vendor 
			add_filter( 'wf_ups_shipment_data', array( $this, 'xa_ups_split_shipment'),10, 3 );

			// Update UPS Confirm Shipment Request
			add_filter( 'wf_ups_shipment_confirm_request', array( $this, 'xa_ups_update_request_info_confirm_shipment' ), 10, 3 );

			// Update UPS Accept Shipment Request
			add_filter( 'xa_ups_accept_shipment_xml_request', array( $this, 'xa_ups_modify_accept_shipment_xml_request' ), 10, 3 );

			// Void Shipment Request
			add_filter( 'xa_ups_void_shipment_xml_request', array( $this, 'xa_ups_void_shipment_xml_request'), 10, 3 );

			// Send Shipping Label Emails to UPS Vendors
			add_filter( 'xa_add_email_addresses_to_send_label', array($this, 'ph_ups_send_label_to_vendors'), 10, 3 );

		}
		// End of UPS support for vendor

		// Dokan supports only Split and Seperate
		$splitcart = get_option('wc_settings_wf_vendor_addon_splitcart');

		if( $splitcart == 'sum_cart' ) {

			add_filter('wf_filter_package_address', array($this, 'wf_splited_packages'), 10, 4);
		}else{

			add_filter('woocommerce_cart_shipping_packages', array($this, 'wf_splited_packages'), 9999, 4);
		}

		// Add Vendor Options in My Account Page
		add_action ( 'woocommerce_edit_account_form' , array($this, 'xa_register_myaccount_fields') );
		add_action ( 'woocommerce_save_account_details' , array($this, 'xa_save_myaccount_fields') );

		// To add PH Shipping Methods when WCFM Store Shipping is enabled
		if( VENDOR_PLUGIN == 'wcfm_venors' )
		{
			// To remove WCFM filter hook
			add_action ( 'wcfmmp_loaded' , array($this, 'ph_remove_wcfm_hide_admin_shipping_hook') );
		}

	}

	/**
	 * Add Vendor Id to FedEx Package.
	 * @param array $fedex_packages
	 * @param array $package
	 * @return array
	 */
	public static function add_vendor_id_to_fedex_package( $fedex_packages, $package ) {

		$order_id 	= '';

		if( !empty($_GET['wf_fedex_generate_packages']) ) {

			$order_id = base64_decode($_GET['wf_fedex_generate_packages']);
		}

		if( !empty($package['VendorId']) && !empty($order_id) ) {

			$order 				= wc_get_order($order_id);
			$shipping_methods 	= $order->get_shipping_methods();

			if( is_array($shipping_methods) ) {

				foreach( $shipping_methods as $shipping_method ) {

					$shipping_method_vendor_id = $shipping_method->get_meta('VendorId');

					if( $shipping_method_vendor_id == $package['VendorId'] ) {

						$shipping_method_meta 	= $shipping_method->get_meta('_xa_fedex_method');
						$shipping_method_id 	= str_replace( WF_Fedex_ID.':', '', $shipping_method_meta['id'] );
					}
				}
			}

			if( !empty($shipping_method_id) ) {
				foreach( $fedex_packages as &$fedex_package ) {
					$fedex_package['service'] = $shipping_method_id;
				}
			}
		}

		return $fedex_packages;
	}


	function  xa_register_myaccount_fields() {
		
		$user 			= wp_get_current_user();
		$show_options 	= !empty(get_option('wc_settings_ph_vendor_show_vendor_optins')) ? get_option('wc_settings_ph_vendor_show_vendor_optins') : 'yes';

		if( !in_array('customer', $user->roles) && !in_array('administrator', $user->roles) && $show_options == 'yes' )
		{
			?>
			<h3>Vendor Options</h3>
			<table>
				<tr>
					<th><label for="tin_number">TIN Number</label></th>
					<td>
						<input type="text" name="tin_number" id="tin_number" value="<?php echo esc_attr( get_the_author_meta( 'tin_number', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your TIN Number.</span>
					</td>
				</tr>

				<tr><th style="font-size: 16px">FedEx Account Details:</th></tr>
				<!--Fedex Account Details -->
				<tr>
					<th><label for="xa_fedex_account_number">FedEx Account Number</label></th>
					<td>
						<input type="text" name="xa_fedex_account_number" id="xa_fedex_account_number" value="<?php echo esc_attr( get_the_author_meta( 'xa_fedex_account_number', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your FedEx Account Number.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_fedex_meter_number">FedEx Meter Number</label></th>
					<td>
						<input type="text" name="xa_fedex_meter_number" id="xa_fedex_meter_number" value="<?php echo esc_attr( get_the_author_meta( 'xa_fedex_meter_number', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your FedEx Meter Number.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_fedex_web_services_key">FedEx Web Services Key</label></th>
					<td>
						<input type="text" name="xa_fedex_web_services_key" id="xa_fedex_web_services_key" value="<?php echo esc_attr( get_the_author_meta( 'xa_fedex_web_services_key', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your FedEx Web Services Key.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_fedex_web_services_password">FedEx Web Services Password</label></th>
					<td>
						<input type="password" name="xa_fedex_web_services_password" id="xa_fedex_web_services_password" value="<?php echo esc_attr( get_the_author_meta( 'xa_fedex_web_services_password', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your FedEx Web Services Password.</span>
					</td>
				</tr>

				<!--UPS Account Details -->
				<tr><th style="font-size: 16px">UPS Account Details:</th></tr>
				<tr>
					<th><label for="xa_ups_user_id">UPS User Id</label></th>

					<td>
						<input type="text" name="xa_ups_user_id" id="xa_ups_user_id" value="<?php echo esc_attr( get_the_author_meta( 'xa_ups_user_id', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your UPS User Id.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_ups_password">UPS Password</label></th>
					<td>
						<input type="password" name="xa_ups_password" id="xa_ups_password" value="<?php echo esc_attr( get_the_author_meta( 'xa_ups_password', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your UPS Password.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_ups_access_key">UPS Access Key</label></th>
					<td>
						<input type="text" name="xa_ups_access_key" id="xa_ups_access_key" value="<?php echo esc_attr( get_the_author_meta( 'xa_ups_access_key', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your UPS Access Key.</span>
					</td>
				</tr>
				<tr>
					<th><label for="xa_ups_account_number">UPS Account Number</label></th>
					<td>
						<input type="text" name="xa_ups_account_number" id="xa_ups_account_number" value="<?php echo esc_attr( get_the_author_meta( 'xa_ups_account_number', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your UPS Account Number.</span>
					</td>
				</tr>

				<!-- USPS Account details -->
				<tr><th style="font-size: 16px">USPS Account Details:</th></tr>
				<tr>
					<th><label for="usps_user_id">USPS User Id</label></th>

					<td>
						<input type="text" name="usps_user_id" id="usps_user_id" value="<?php echo esc_attr( get_the_author_meta( 'usps_user_id', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your USPS User Id.</span>
					</td>
				</tr>
				<tr>
					<th><label for="usps_password">USPS User Password</label></th>

					<td>
						<input type="password" name="usps_password" id="usps_password" value="<?php echo esc_attr( get_the_author_meta( 'usps_password', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your USPS Password.</span>
					</td>
				</tr>

				<!-- Stamos Account details -->
				<tr><th style="font-size: 16px">Stamps Account Details:</th></tr>
				<tr>
					<th><label for="stamps_usps_username">Stamps User Name</label></th>

					<td>
						<input type="text" name="stamps_usps_username" id="stamps_usps_username" value="<?php echo esc_attr( get_the_author_meta( 'stamps_usps_username', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your Stamps User Name.</span>
					</td>
				</tr>
				<tr>
					<th><label for="stamps_usps_password">Stamps Password</label></th>
					<td>
						<input type="password" name="stamps_usps_password" id="stamps_usps_password" value="<?php echo esc_attr( get_the_author_meta( 'stamps_usps_password', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your Stamps Password.</span>
					</td>
				</tr>

				<!-- DHL Account details -->
				<tr><th style="font-size: 16px">DHL Account Details:</th></tr>
				<tr>
					<th><label for="dhl_account_number">DHL Account Number</label></th>
					<td>
						<input type="text" name="dhl_account_number" id="dhl_account_number" value="<?php echo esc_attr( get_the_author_meta( 'dhl_account_number', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your DHL Account Number.</span>
					</td>
				</tr>
				<tr>
					<th><label for="dhl_siteid">DHL Site Id</label></th>
					<td>
						<input type="text" name="dhl_siteid" id="dhl_siteid" value="<?php echo esc_attr( get_the_author_meta( 'dhl_siteid', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your DHL Site Id.</span>
					</td>
				</tr>
				<tr>
					<th><label for="dhl_password">DHL Password</label></th>
					<td>
						<input type="password" name="dhl_password" id="dhl_password" value="<?php echo esc_attr( get_the_author_meta( 'dhl_password', $user->ID ) ); ?>" class="regular-text" style="width: 100%" /><br />
						<span class="description">Please enter your DHL Password.</span>
					</td>
				</tr>

			</table>
			<?php
		}
	}



	function  xa_save_myaccount_fields( $user_id ) {

		if( isset ( $_POST[ 'tin_number' ]) ) {
			// Copy and paste this line for additional fields. Make sure to change 'tin_number' to the field ID. 
			update_user_meta( $user_id, 'tin_number', $_POST['tin_number'] );
			update_user_meta( $user_id, 'xa_fedex_account_number', $_POST['xa_fedex_account_number'] );
			update_user_meta( $user_id, 'xa_fedex_meter_number', $_POST['xa_fedex_meter_number'] );
			update_user_meta( $user_id, 'xa_fedex_web_services_key', $_POST['xa_fedex_web_services_key'] );
			update_user_meta( $user_id, 'xa_fedex_web_services_password', $_POST['xa_fedex_web_services_password'] );
			
			// Save UPS details
			update_user_meta( $user_id, 'xa_ups_user_id', $_POST['xa_ups_user_id'] );
			update_user_meta( $user_id, 'xa_ups_password', $_POST['xa_ups_password'] );
			update_user_meta( $user_id, 'xa_ups_access_key', $_POST['xa_ups_access_key'] );
			update_user_meta( $user_id, 'xa_ups_account_number', $_POST['xa_ups_account_number'] );
			
			//USPS
			update_user_meta( $user_id, 'usps_user_id', $_POST['usps_user_id'] );
			update_user_meta( $user_id, 'usps_password', $_POST['usps_password'] );

			//Stamps
			update_user_meta( $user_id, 'stamps_usps_username', $_POST['stamps_usps_username'] );
			update_user_meta( $user_id, 'stamps_usps_password', $_POST['stamps_usps_password'] );
			
			//DHL
			update_user_meta( $user_id, 'dhl_account_number', $_POST['dhl_account_number'] );
			update_user_meta( $user_id, 'dhl_siteid', $_POST['dhl_siteid'] );
			update_user_meta( $user_id, 'dhl_password', $_POST['dhl_password'] );
		}
	}

	private function get_vendor_id_from_product($order_details , $id = '') {

		global $woocommerce;

		if( empty($id) )
		{
			if( $woocommerce->version <= 3.0 )
			{	
				if( isset($order_details->obj) ){
					$id = $order_details->obj->id;	
				}

			}else{
				if( isset($order_details->obj) ){
					$id = $order_details->obj->get_id();
				}
			}
		}

		// Vendor is assigned at Parent Level
		$parent_id 	= wp_get_post_parent_id( $id );
		$id 		= !empty($parent_id) ? $parent_id : $id;

		if( VENDOR_PLUGIN == 'product_vendor' && WC_Product_Vendors_Utils::is_vendor_product( $id ) ){
			//get associated user with vendor.
			$woo_vendor = WC_Product_Vendors_Utils::get_vendor_id_from_product( $id );
			$vendor = WC_Product_Vendors_Utils::get_vendor_data_by_id($woo_vendor);

			if( ! is_array($vendor['admins']) ){
				$vendor = explode(',', $vendor['admins']);
			}else{
				$vendor = $vendor['admins'];
			}

			if( !empty($vendor[0]) ){
				return $vendor[0]; //assume only one user associated with vendor, taking fist user.
			}
			// if not user found let retun post auther.
		}

		if ( is_object($order_details) && $order_details instanceof wf_product ) {

			$productObject 	= $order_details->obj;

		} else if ( $order_details instanceof WC_Product || $order_details instanceof WC_Order_Item ) {

			$productObject 	= $order_details;

		} else {

			$productObject 	= $order_details['data'];
		}

		if ( method_exists( $order_details, 'get_parent_id' ) ) {

			$parent_id 		= $productObject->get_parent_id();
		}

		$product_id 	= !empty( $parent_id ) ? $parent_id : $productObject->get_id();
		$post 			= get_post($product_id);

		return $post->post_author;
	}

	public function wf_vendor_label_packages( $packages, $ship_from_address_context='vendor_address' ) {

		// If Origin Preference is not Vendor Address, Do nothing.
		if ($ship_from_address_context !== 'vendor_address')
			return $packages;

		$vendor_packages = array();
		
		foreach ($packages as $package) {

			foreach ($package['contents'] as $order_details) {

				$id 		= ( $order_details['data']->get_id() ) ? $order_details['data']->get_id() : '';
				$vendor_id 	= $this->get_vendor_id_from_product($order_details, $id);

				$vendor_packages[$vendor_id]['contents'][] 		= $order_details;
				$vendor_packages[$vendor_id]['destination'] 	= $package['destination'];

				$vendor_address = $this->get_vendor_address( $vendor_id );

				$vendor_packages[$vendor_id]['VendorId'] 	= $vendor_id;
				$vendor_packages[$vendor_id]['origin'] 		= $this->wf_formate_origin_address($vendor_address);
			}
		}
		
		// Now the Packages array will be indexed by Vendor ID.
		return $vendor_packages;
	}

	public static function get_vendor_address( $vndr_id ) {

		$vendor_profile 	= get_user_meta($vndr_id);
		$user_data			= get_userdata($vndr_id);
		$vendor_details 	= array();

		switch (VENDOR_PLUGIN) {
			case 'dokan_lite':

			if( function_exists('dokan_get_seller_id_by_order') ){
				$dokan_profile = get_user_meta( $vndr_id, 'dokan_profile_settings', true );
			}

			//For older version of Dokan plugin.
			if( empty($dokan_profile['address']) ){
				$dokan_profile = isset( $vendor_profile['dokan_profile_settings'][0] ) ? unserialize( $vendor_profile['dokan_profile_settings'][0] ) : '';
			}

			$vendor_details['vendor_country'] 	= isset( $dokan_profile['address']['country'] ) ? $dokan_profile['address']['country'] : '';
			$vendor_details['vendor_fname']		= isset( $vendor_profile['billing_first_name'][0] ) ? $vendor_profile['billing_first_name'][0] : '' ;
			$vendor_details['vendor_lname']		= isset( $vendor_profile['billing_last_name'][0] ) ? $vendor_profile['billing_last_name'][0] : '';
			$vendor_details['vendor_company']	= isset( $dokan_profile['store_name'] ) ? $dokan_profile['store_name'] : '';
			$vendor_details['vendor_address1']	= isset( $dokan_profile['address']['street_1'] ) ? $dokan_profile['address']['street_1'] : '';
			$vendor_details['vendor_address2']	= isset( $dokan_profile['address']['street_2'] ) ? $dokan_profile['address']['street_2'] : '';
			$vendor_details['vendor_city']		= isset( $dokan_profile['address']['city'] ) ? $dokan_profile['address']['city'] : '';
			$vendor_details['vendor_state']		= isset( $dokan_profile['address']['state'] ) ? $dokan_profile['address']['state'] : '';
			$vendor_details['vendor_zip']		= isset( $dokan_profile['address']['zip'] ) ? $dokan_profile['address']['zip'] : '';
			$vendor_details['vendor_phone']		= isset( $dokan_profile['phone'] ) ? $dokan_profile['phone'] : '';
			$vendor_details['email']			= isset( $vendor_profile['billing_email'][0] ) ? $vendor_profile['billing_email'][0] : '';
			break;

			case 'wc_vendors_pro':

			// Shipping from address may be other
			$shipping_settings = get_user_meta( $vndr_id, '_wcv_shipping', true );

			$wc_country 	= isset( $vendor_profile['_wcv_store_country'][0] ) ? $vendor_profile['_wcv_store_country'][0] : '';
			$wc_address1 	= isset( $vendor_profile['_wcv_store_address1'][0] ) ? $vendor_profile['_wcv_store_address1'][0] : '';
			$wc_address2 	= isset( $vendor_profile['_wcv_store_address2'][0] ) ? $vendor_profile['_wcv_store_address2'][0] : '';
			$wc_city 	 	= isset( $vendor_profile['_wcv_store_city'][0] ) ? $vendor_profile['_wcv_store_city'][0] : '';
			$wc_state 	 	= isset( $vendor_profile['_wcv_store_state'][0] ) ? $vendor_profile['_wcv_store_state'][0] : '';
			$wc_postcode 	= isset( $vendor_profile['_wcv_store_postcode'][0] ) ? $vendor_profile['_wcv_store_postcode'][0] : '';

			if( !empty($shipping_settings) && is_array($shipping_settings) && $shipping_settings['shipping_from'] != 'store_address' )
			{
				$wc_country 	= isset( $shipping_settings['shipping_address']['country'] ) ? $shipping_settings['shipping_address']['country'] : '';
				$wc_address1 	= isset( $shipping_settings['shipping_address']['address1'] ) ? $shipping_settings['shipping_address']['address1'] : '';
				$wc_address2 	= isset( $shipping_settings['shipping_address']['address2'] ) ? $shipping_settings['shipping_address']['address2'] : '';
				$wc_city 	 	= isset( $shipping_settings['shipping_address']['city'] ) ? $shipping_settings['shipping_address']['city'] : '';
				$wc_state 	 	= isset( $shipping_settings['shipping_address']['state'] ) ? $shipping_settings['shipping_address']['state'] : '';
				$wc_postcode 	= isset( $shipping_settings['shipping_address']['postcode'] ) ? $shipping_settings['shipping_address']['postcode'] : '';
			}

			$vendor_details['vendor_country'] 	= $wc_country;
			$vendor_details['vendor_fname']		= isset( $vendor_profile['first_name'][0] ) ? $vendor_profile['first_name'][0] : '';
			$vendor_details['vendor_lname']		= isset( $vendor_profile['last_name'][0] ) ? $vendor_profile['last_name'][0] : '';
			$vendor_details['vendor_company']	= isset( $vendor_profile['pv_shop_name'][0] ) ? $vendor_profile['pv_shop_name'][0] : '';
			$vendor_details['vendor_address1']	= $wc_address1;
			$vendor_details['vendor_address2']	= $wc_address2;
			$vendor_details['vendor_city']		= $wc_city;
			$vendor_details['vendor_state']		= $wc_state;
			$vendor_details['vendor_zip']		= $wc_postcode;
			$vendor_details['vendor_phone']		= isset( $vendor_profile['_wcv_store_phone'][0] ) ? $vendor_profile['_wcv_store_phone'][0] : '';
			$vendor_details['email']			= isset( $vendor_profile['billing_email'][0] ) ? $vendor_profile['billing_email'][0] : '';
			break;
			
			case 'wcfm_venors':
				// Using Store Address
			$vendor_details['vendor_country'] 	= isset( $vendor_profile['_wcfm_country'][0] ) ? $vendor_profile['_wcfm_country'][0] : '';
			$vendor_details['vendor_fname']		= isset( $vendor_profile['first_name'][0] ) ? $vendor_profile['first_name'][0] : '';
			$vendor_details['vendor_lname']		= isset( $vendor_profile['last_name'][0] ) ? $vendor_profile['last_name'][0] : '';
			$vendor_details['vendor_company']	= isset( $vendor_profile['billing_company'][0] ) ? $vendor_profile['billing_company'][0] : '';
			$vendor_details['vendor_address1']	= isset( $vendor_profile['_wcfm_street_1'][0] ) ? $vendor_profile['_wcfm_street_1'][0] : '';
			$vendor_details['vendor_address2']	= isset( $vendor_profile['_wcfm_street_2'][0] ) ? $vendor_profile['_wcfm_street_2'][0] : '';
			$vendor_details['vendor_city']		= isset( $vendor_profile['_wcfm_city'][0] ) ? $vendor_profile['_wcfm_city'][0] : '';
			$vendor_details['vendor_state']		= isset( $vendor_profile['_wcfm_state'][0] ) ? $vendor_profile['_wcfm_state'][0] : '';
			$vendor_details['vendor_zip']		= isset( $vendor_profile['_wcfm_zip'][0] ) ? $vendor_profile['_wcfm_zip'][0] : '';
			$vendor_details['vendor_phone']		= isset( $vendor_profile['billing_phone'][0] ) ? $vendor_profile['billing_phone'][0] : '';
			$vendor_details['email']			= isset( $vendor_profile['billing_email'][0] ) ? $vendor_profile['billing_email'][0] : '';
			break;

			default:

			$vendor_details['vendor_country'] 	= isset( $vendor_profile['billing_country'][0] ) ? $vendor_profile['billing_country'][0] : '';
			$vendor_details['vendor_fname']		= isset( $vendor_profile['billing_first_name'][0] ) ? $vendor_profile['billing_first_name'][0] : '';
			$vendor_details['vendor_lname']		= isset( $vendor_profile['billing_last_name'][0] ) ? $vendor_profile['billing_last_name'][0] : '';
			$vendor_details['vendor_company']	= isset( $vendor_profile['billing_company'][0] ) ? $vendor_profile['billing_company'][0] : '';
			$vendor_details['vendor_address1']	= isset( $vendor_profile['billing_address_1'][0] ) ? $vendor_profile['billing_address_1'][0] : '';
			$vendor_details['vendor_address2']	= isset( $vendor_profile['billing_address_2'][0] ) ? $vendor_profile['billing_address_2'][0] : '';
			$vendor_details['vendor_city']		= isset( $vendor_profile['billing_city'][0] ) ? $vendor_profile['billing_city'][0] : '';
			$vendor_details['vendor_state']		= isset( $vendor_profile['billing_state'][0] ) ? $vendor_profile['billing_state'][0] : '';
			$vendor_details['vendor_zip']		= isset( $vendor_profile['billing_postcode'][0] ) ? $vendor_profile['billing_postcode'][0] : '';
			$vendor_details['vendor_phone']		= isset( $vendor_profile['billing_phone'][0] ) ? $vendor_profile['billing_phone'][0] : '';
			$vendor_details['email']			= isset( $vendor_profile['billing_email'][0] ) ? $vendor_profile['billing_email'][0] : '';
			break;
		}

		$vendor_details['tin_number']			= isset( $vendor_profile['tin_number'][0] ) ? $vendor_profile['tin_number'][0] : '';

		return apply_filters( 'ph_multi_vendor_addon_vendor_address', $vendor_details, $vndr_id );
	}

	// Function to get Vendor Address for Label API Request
	public function wf_vendor_label_from_address( $from_address , $package, $ship_from_address_context='vendor_address' ) {

		// If Origin Preference is not Vendor Address & Package Content is empty, Do nothing.
		if( empty($package['contents']) || $ship_from_address_context !== 'vendor_address' ) {
			return $from_address;
		}

		if( !isset($package['origin']) || isset($package['origin']) && isset($package['origin']['country']) && empty($package['origin']['country']) ) {

			$vendor_id 			=	$this->get_vendor_id_from_product( array_shift($package['contents']) );
			$package['origin'] 	=	$this->wf_formate_origin_address( $this->get_vendor_address($vendor_id) );
		}
		
		if( empty($package['origin']['country']) || empty($package['origin']['postcode']) ){
			return $from_address;
		}

		$from_address = array(
			'name' 		=> $package['origin']['first_name'] . ' ' . $package['origin']['last_name'],
			'company' 	=> $package['origin']['company'],
			'phone' 	=> $package['origin']['phone'],
			'address_1'	=> $package['origin']['address_1'],
			'address_2'	=> $package['origin']['address_2'],
			'city' 		=> strtoupper($package['origin']['city']),
			'state' 	=> strlen($package['origin']['state']) == 2 ? strtoupper($package['origin']['state']) : '',
			'country' 	=> $package['origin']['country'],
			'postcode' 	=> str_replace(' ', '', strtoupper($package['origin']['postcode'])),
			'tin_number' 	=> $package['origin']['tin_number'],
			'email'		=> $package['origin']['email'],
		);

		return $from_address;
	}

	function wf_splited_packages($packages, $ship_from_address_context='' ){
		
		// If Origin Preference is not Vendor Address , Do nothing.
		if ( ( $ship_from_address_context != '' && $ship_from_address_context !== 'vendor_address' ) ) {
			return $packages;
		}

		// Return Package if Origin Preference is not Vendor Address in FedEx
		if( class_exists('wf_fedEx_wooCommerce_shipping_setup') && isset($this->fedex_settings) && !empty($this->fedex_settings) && $this->fedex_settings['ship_from_address'] != 'vendor_address' ) {
			return $packages;
		}

		// Return Package if Origin Preference is not Vendor Address in Multi-Carrier
		if( class_exists('eha_multi_carrier_shipping_setup') && isset($this->multi_carrier_settings) && !empty($this->multi_carrier_settings) && $this->multi_carrier_settings['ship_from_address'] != 'vendor_address' ) {
			return $packages;
		}

		$package_splitted_already = false;
		// Add the required data to package if cart is already splitted based on vendor
		foreach( $packages as &$package ) {
			if( isset($package['vendorID']) || isset($package['vendor_id']) || isset($package['seller_id']) ) {
				// Get the Vendor Id
				if( isset($package['seller_id']) ) {
					$vendor_id = $package['seller_id'];			// Dokan
				}
				elseif( isset($package['vendor_id']) ) {
					$vendor_id = $package['vendor_id'];			// WC Vendors
				}
				elseif( isset($package['vendorID']) ) {
					$vendor_id = $package['vendorID'];
				}

				$package_splitted_already	= true;
				$vendor_address				= $this->get_vendor_address($vendor_id);
				$package['vendorID']		= $vendor_id;
				$package['origin']			= $this->wf_formate_origin_address($vendor_address);
			}
		}
		
		// Return the package if its already splitted based on vendor
		if( $package_splitted_already ) {
			return $packages;
		}
		
		$new_packages 		= array();		
		//Init splitted package
		$splitted_packages	=	array();
		$vendor_id 			= '';

		// Group items by Vendor
		foreach ( WC()->cart->get_cart() as $item_key => $item ) {
			if ( $item['data']->needs_shipping() ) {
				
				$vendor_id	=	$this->get_vendor_id_from_product($item, $item['product_id']);
				$splitted_packages[$vendor_id][$item_key]	=	$item;
			}
		}

		// Add grouped items as packages 
		if(is_array($splitted_packages)){
			
			foreach($splitted_packages as $vendor_id => $splitted_package_items){

				$vendor_address = $this->get_vendor_address($vendor_id);

				$new_packages[] = array(
					'contents'		=> $splitted_package_items,
					'contents_cost'   => array_sum( wp_list_pluck( $splitted_package_items, 'line_total' ) ),
					'applied_coupons' => WC()->cart->get_applied_coupons(),
					'user'			=> array(
						'ID' => $vendor_id
					),
					'vendorID'		=>	$vendor_id,
					'origin'		 => $this->wf_formate_origin_address($vendor_address),
					'destination'	=> array(
						'country'	=> WC()->customer->get_shipping_country(),
						'state'	  => WC()->customer->get_shipping_state(),
						'postcode'   => WC()->customer->get_shipping_postcode(),
						'city'	   => WC()->customer->get_shipping_city(),
						'address'	=> WC()->customer->get_shipping_address(),
						'address_2'  => WC()->customer->get_shipping_address_2()
					)
				);
			}
		}
		
		return $new_packages;
	}

	public static function wf_formate_origin_address($vendor_address){

		return array(
			'country' 		=> $vendor_address['vendor_country'],
			'first_name'	=> $vendor_address['vendor_fname'],
			'last_name'		=> $vendor_address['vendor_lname'],
			'company'		=> $vendor_address['vendor_company'],
			'address_1'		=> $vendor_address['vendor_address1'],
			'address_2'		=> $vendor_address['vendor_address2'],
			'city' 			=> $vendor_address['vendor_city'],
			'state'			=> $vendor_address['vendor_state'],
			'postcode' 		=> $vendor_address['vendor_zip'],
			'phone' 		=> $vendor_address['vendor_phone'],
			'email' 		=> $vendor_address['email'],
			'tin_number' 	=> isset($vendor_address['tin_number']) ? $vendor_address['tin_number'] : '',

		);
	}
	
	
	/**
	 * To update UPS Credentials and Shipper Address in Rate Request.
	 * @param array $rate_request_data UPS credentials and origin address
	 * @param type $package UPS Packages of single vendor
	 * @return array Updated UPS credentials and origin address.
	 */
	public function xa_change_ups_credentials_in_rate_request( $rate_request_data, $main_package, $package ) {

		$package 	= current($package);
		$items 		= array();

		if( isset($package['Package']['items']) ) {

			$items = current($package['Package']['items']);
		}
		
		$item 		= is_array($items) ? current($items) : $items;
		$vendor 	= null;

		if( !empty($item) ) {

			if( isset($main_package['vendorID']) && VENDOR_PLUGIN == 'product_vendor' )
			{
				$vendor_id 	= $this->get_vendor_id_from_product($item, $item->get_id() );
				$author 	= get_user_by('id', $vendor_id);
			}else{

				$parent_id 		= $item->get_parent_id();
				$product_id 	= !empty( $parent_id ) ? $parent_id : $item->get_id();

				$author 	= $this->xa_get_post_author($product_id);
			}

			foreach( $author->roles as $role ) {

				// Seller for Dokan Vendor
				if( strstr( $role, 'vendor') || strstr( $role, 'seller') ) {
					$vendor = $author;
					break;
				}
			}

			$ups_user_id 	= !empty($vendor) ? $vendor->get('xa_ups_user_id') : null;

			if( !empty($ups_user_id) ) {
				
				$vendor_address					= $this->get_vendor_address($author->ID);
				$formatted_vendor_address 		= $this->wf_formate_origin_address($vendor_address);
				
				$rate_request_data	=	array(
					'user_id'			=>	$ups_user_id,
					'password'			=>	str_replace( '&', '&amp;', $vendor->get('xa_ups_password') ), // Ampersand will break XML doc, so replace with encoded version.
					'access_key'		=>	$vendor->get('xa_ups_access_key'),
					'shipper_number'	=>	$vendor->get('xa_ups_account_number'),
					'origin_addressline'=>	$formatted_vendor_address['address_1'].' '.$formatted_vendor_address['address_2'],
					'origin_postcode'	=>	$formatted_vendor_address['postcode'],
					'origin_city'		=>	$formatted_vendor_address['city'],
					'origin_state'		=>	$formatted_vendor_address['state'],
					'origin_country'	=>	$formatted_vendor_address['country'],
				);
			}
			
		}

		return $rate_request_data;
	}

	/**
	 * Change Ship From Address in Rate Request.
	 */
	public function ph_update_ship_from_address_in_rate_request( $xml_request, $package ) {

		if( !empty($package['contents']) ) {

			$line_item 	= current($package['contents']);			

			if( isset($package['vendorID']) && VENDOR_PLUGIN == 'product_vendor' )
			{
				$vendor_id 		= $this->get_vendor_id_from_product($line_item, $line_item['data']->get_id() );
				$author 		= get_user_by('id', $vendor_id);

				$package['origin'] = $this->wf_formate_origin_address( $this->get_vendor_address($vendor_id) );

			}else{

				$parent_id 		= $line_item['data']->get_parent_id();
				$product_id 	= !empty( $parent_id ) ? $parent_id : $line_item['data']->get_id();

				$author 	= $this->xa_get_post_author($product_id);

				if( !isset($package['origin']) && empty($package['origin']) )
				{
					$package['origin'] = $this->wf_formate_origin_address( $this->get_vendor_address($author->ID) );
				}
			}

			foreach( $author->roles as $role ) {
				// Seller for Dokan Vendor
				if( strstr( $role, 'vendor') || strstr( $role, 'seller') ) {
					$vendor = $author;
					break;
				}
			}

			// Add Ship From Address only for Vendor not for Admin
			if( !empty($vendor) && !empty($package['origin'])  ) {

				$request = "		<ShipFrom>" . "\n";
				$request .= "			<Address>" . "\n";
				$request .= "				<AddressLine>" . $package['origin']['address_1'] . "</AddressLine>" . "\n";
				$request .= "				<City>" . $package['origin']['city'] . "</City>" . "\n";
				if( ! empty($package['origin']['state']) ) {
					$request .= "<StateProvinceCode>".$package['origin']['state']."</StateProvinceCode>\n";
				}

				if( ! empty($package['origin']['postcode']) ) {
					$request .= "<PostalCode>".$package['origin']['postcode']."</PostalCode>\n";
				}
				$request .= "				<CountryCode>" . $package['origin']['country'] . "</CountryCode>" . "\n";
				$request .= "			</Address>" . "\n";
				$request .= "		</ShipFrom>" . "\n";
				$xml_request_arr = explode( '<ShipFrom>', $xml_request );
				if( ! empty($xml_request_arr[1]) ) {
					$new_xml_request = $xml_request_arr[0].$request;
					$xml_request_arr = explode( '</ShipFrom>', $xml_request_arr[1]);
					$new_xml_request .= $xml_request_arr[1];
					$xml_request = $new_xml_request;
				}else{
					$xml_request_arr = explode( '<ShipTo>', $xml_request );
					$new_xml_request = $xml_request_arr[0].$request.'<ShipTo>'.$xml_request_arr[1];
					$xml_request = $new_xml_request;
				}
			}
		}

		return $xml_request;
	}
	
	/**
	* Get the author details of the post
	* @param int $id post id
	* @return object WP_User
	*/
	public function xa_get_post_author($id){
		$post = get_post($id);
		$author = get_user_by('id', $post->post_author);
		return $author;
	}

	
	/**
	 * Split the UPS Shipment based on Vendors while Confirming the Shipment .
	 * @param array $shipments Shipments
	 * @param object $order Order object
	 * @return array Shipment
	 */
	public function xa_ups_split_shipment( $shipments, $order ) {

		$order 	= wc_get_order($order);
		$items 	= $order->get_items();
		$vendor = null;

		if( !empty($items) && is_array($items) ) {
			foreach( $items as $item ) {

				if( VENDOR_PLUGIN == 'product_vendor' )
				{
					$vendor_id = $this->get_vendor_id_from_product($item, $item->get_product_id() );
					$author = get_user_by('id', $vendor_id );
				}else{
					$author = $this->xa_get_post_author($item->get_product_id());
				}

				foreach( $author->roles as $role ) {
					// Seller for Dokan Vendor
					if( strstr( $role, 'vendor') || strstr( $role, 'seller') ) {
						$vendor = $author->ID;
						break;
					}
				}
			}
		}
		
		// No Vendor Products exist in Order then return
		if( empty($vendor) ) {
			return $shipments;
		}

		foreach( $shipments as $key => $shipment ) {

			$services = $shipment['shipping_service'];

			foreach( $shipment['packages'] as $package ) {

				$all_shipments[] = array(
					'shipping_service'	=> $services,
					'packages'			=> array($package),
				);
			}
		}

		return !empty($all_shipments) ? $all_shipments : $shipments;
	}
	
	/**
	 * Update Vendor Account info and Address in UPS Request while Confirm Shipment.
	 * @param array $request_arr UPS request array
	 * @param object $order wf_order object
	 * @param array $shipment Shipment
	 * @return array UPS request array
	 */
	public function xa_ups_update_request_info_confirm_shipment( $xml_request, $order, $shipment ) {

		$package	= current($shipment['packages']);

		if( !isset($package['Package']) || !isset($package['Package']['items']) )
		{
			return $xml_request;
		}
		
		$item		= current($package['Package']['items']);

		if( VENDOR_PLUGIN == 'product_vendor' )
		{
			$vendor_id = $this->get_vendor_id_from_product( $item, $item->get_id() );
			$author = get_user_by('id', $vendor_id );
		}
		else
		{
			$parent_id 		= $item->get_parent_id();
			$product_id 	= !empty( $parent_id ) ? $parent_id : $item->get_id();

			$author	= $this->xa_get_post_author($product_id);
		}

		foreach( $author->roles as $role ) {

			// Seller for Dokan Vendor
			if( strstr( $role, 'vendor') || strstr( $role, 'seller') ) {
				$vendor = $author->ID;
				break;
			}
		}
		
		if( !empty($vendor) ) {

			// If Products belong to Vendor and Vendor UPS Account is configured then only proceed

			$ups_account_number 		= $author->get('xa_ups_account_number');
			$vendor_address				= $this->get_vendor_address($author->ID);
			$formatted_vendor_address	= $this->wf_formate_origin_address($vendor_address);

			if( !empty($ups_account_number) ) {

				$req_arr			= explode('<?xml version="1.0" ?>', $xml_request);
				$new_xml_request	=	'<?xml version="1.0" encoding="UTF-8"?>';
				$new_xml_request	.=	'<AccessRequest xml:lang="en-US">';
				$new_xml_request	.=	'<AccessLicenseNumber>'.$author->get('xa_ups_access_key').'</AccessLicenseNumber>';
				$new_xml_request	.=	'<UserId>'.$author->get('xa_ups_user_id').'</UserId>';
				$new_xml_request	.=	'<Password>'.$author->get('xa_ups_password').'</Password>';
				$new_xml_request	.=	'</AccessRequest>';
				
				$xml_request_obj_1													= simplexml_load_string($req_arr[1]);
				$xml_request_obj_1->Shipment->Shipper->Name							= $formatted_vendor_address['first_name'].' '.$formatted_vendor_address['last_name'];
				$xml_request_obj_1->Shipment->Shipper->AttentionName				= $formatted_vendor_address['company'];
				$xml_request_obj_1->Shipment->Shipper->PhoneNumber					= $formatted_vendor_address['phone'];
				$xml_request_obj_1->Shipment->Shipper->EMailAddress					= $formatted_vendor_address['email'];
				$xml_request_obj_1->Shipment->Shipper->ShipperNumber				= $ups_account_number;
				$xml_request_obj_1->Shipment->Shipper->Address->AddressLine1		= $formatted_vendor_address['address_1'];
				
				if( ! empty($formatted_vendor_address['address_2']) ) {
					$xml_request_obj_1->Shipment->Shipper->Address->AddressLine2	= $formatted_vendor_address['address_2'];
				}
				
				$xml_request_obj_1->Shipment->Shipper->Address->City				= $formatted_vendor_address['city'];
				$xml_request_obj_1->Shipment->Shipper->Address->StateProvinceCode	= $formatted_vendor_address['state'];
				$xml_request_obj_1->Shipment->Shipper->Address->CountryCode			= $formatted_vendor_address['country'];
				$xml_request_obj_1->Shipment->Shipper->Address->PostalCode			= $formatted_vendor_address['postcode'];
				
				if( isset($xml_request_obj_1->Shipment->ItemizedPaymentInformation->ShipmentCharge) ) {

					foreach ($xml_request_obj_1->Shipment->ItemizedPaymentInformation->ShipmentCharge as $key => $value) {

						$value->BillShipper->AccountNumber = $ups_account_number;
					}

				}

				if( strstr($xml_request, 'ReturnService') )
				{
					$xml_request_obj_1->Shipment->ShipTo->CompanyName 		= $formatted_vendor_address['first_name'].' '.$formatted_vendor_address['last_name'];
					$xml_request_obj_1->Shipment->ShipTo->AttentionName		= $formatted_vendor_address['company'];
					$xml_request_obj_1->Shipment->ShipTo->PhoneNumber 		= $formatted_vendor_address['phone'];
					$xml_request_obj_1->Shipment->ShipTo->EMailAddress 		= $formatted_vendor_address['email'];

					$xml_request_obj_1->Shipment->ShipTo->Address->AddressLine1 	= $formatted_vendor_address['address_1'];

					if( ! empty($formatted_vendor_address['address_2']) ) {
						$xml_request_obj_1->Shipment->ShipTo->Address->AddressLine2 	= $formatted_vendor_address['address_2'];
					}

					$xml_request_obj_1->Shipment->ShipTo->Address->City 		= $formatted_vendor_address['city'];
					$xml_request_obj_1->Shipment->ShipTo->Address->StateProvinceCode 	= $formatted_vendor_address['state'];
					$xml_request_obj_1->Shipment->ShipTo->Address->CountryCode 	= $formatted_vendor_address['country'];
					$xml_request_obj_1->Shipment->ShipTo->Address->PostalCode 	= $formatted_vendor_address['postcode'];
				}

				$doc = new DOMDocument();
				$doc->loadXML($xml_request_obj_1->asXML());
				$new_xml_request .= $doc->saveXML();
			}
			// Change Shipper Address only when Shipper and ShipFrom address belongs to same Country
			elseif( !empty($vendor) ) {
				$xml_request_arr = explode( '<ShipmentConfirmRequest>', $xml_request );
				$xml_request_arr[1] = '<ShipmentConfirmRequest>'.$xml_request_arr[1];
				$xml_request_obj_1													= simplexml_load_string($xml_request_arr[1]);
				// Replace Shipper address only when Shipper and ShipFrom address belongs to Same Country
				if( $xml_request_obj_1->Shipment->Shipper->Address->CountryCode == $formatted_vendor_address['country'] ) {
					$xml_request_obj_1->Shipment->Shipper->Name							= $formatted_vendor_address['first_name'].' '.$formatted_vendor_address['last_name'];
					$xml_request_obj_1->Shipment->Shipper->AttentionName				= $formatted_vendor_address['company'];
					$xml_request_obj_1->Shipment->Shipper->PhoneNumber					= $formatted_vendor_address['phone'];
					$xml_request_obj_1->Shipment->Shipper->EMailAddress					= $formatted_vendor_address['email'];
					$xml_request_obj_1->Shipment->Shipper->Address->AddressLine1		= $formatted_vendor_address['address_1'];
					
					if( ! empty($formatted_vendor_address['address_2']) ) {
						$xml_request_obj_1->Shipment->Shipper->Address->AddressLine2	= $formatted_vendor_address['address_2'];
					}
					
					$xml_request_obj_1->Shipment->Shipper->Address->City				= $formatted_vendor_address['city'];
					$xml_request_obj_1->Shipment->Shipper->Address->StateProvinceCode	= $formatted_vendor_address['state'];
					$xml_request_obj_1->Shipment->Shipper->Address->PostalCode			= $formatted_vendor_address['postcode'];

					if( strstr($xml_request, 'ReturnService') )
					{
						$xml_request_obj_1->Shipment->ShipTo->CompanyName 		= $formatted_vendor_address['first_name'].' '.$formatted_vendor_address['last_name'];
						$xml_request_obj_1->Shipment->ShipTo->AttentionName		= $formatted_vendor_address['company'];
						$xml_request_obj_1->Shipment->ShipTo->PhoneNumber 		= $formatted_vendor_address['phone'];
						$xml_request_obj_1->Shipment->ShipTo->EMailAddress 		= $formatted_vendor_address['email'];

						$xml_request_obj_1->Shipment->ShipTo->Address->AddressLine1 	= $formatted_vendor_address['address_1'];

						if( ! empty($formatted_vendor_address['address_2']) ) {
							$xml_request_obj_1->Shipment->ShipTo->Address->AddressLine2 	= $formatted_vendor_address['address_2'];
						}

						$xml_request_obj_1->Shipment->ShipTo->Address->City 		= $formatted_vendor_address['city'];
						$xml_request_obj_1->Shipment->ShipTo->Address->StateProvinceCode 	= $formatted_vendor_address['state'];
						$xml_request_obj_1->Shipment->ShipTo->Address->CountryCode 	= $formatted_vendor_address['country'];
						$xml_request_obj_1->Shipment->ShipTo->Address->PostalCode 	= $formatted_vendor_address['postcode'];
					}
					
					$doc = new DOMDocument();
					$doc->loadXML($xml_request_obj_1->asXML());
					$new_xml_request = $xml_request_arr[0].$doc->saveXML();
					$new_xml_request = str_replace( '<?xml version="1.0"?>', null, $new_xml_request);
				}
			}
		}

		return !empty($new_xml_request) ? $new_xml_request : $xml_request;
	}
	
	/**
	 * Update the UPS Credentials in Accept Shipment XML Request .
	 * @param xml $xml_request XML request for confirm shipment .
	 * @param string $shipment_id Shipment id for which shipment has to be accepted .
	 * @param object $order wc_order.
	 * @return xml XML request for accept shipment .
	 */
	public function xa_ups_modify_accept_shipment_xml_request( $xml_request, $shipment_id, $order_id ) {

		$order  = wc_get_order($order_id);

		$xml_request_array		= $order->get_meta( 'ups_created_shipments_xml_request_array', true );
		$stored_xml_request		= !empty($xml_request_array["$shipment_id"]) ? $xml_request_array["$shipment_id"] : null;

		if( !empty($stored_xml_request) ) {

			$req_arr_0			=	strstr( $stored_xml_request, '<?xml version="1.0"?>', true );										// It will contain the xml with vendor details
			$req_arr_0			=	empty($req_arr_0) ? strstr( $stored_xml_request, '<?xml version="1.0" ?>', true ) : $req_arr_0;		// It will contain the xml with vendor details
			$req_arr_1			=	strstr( $xml_request, '<?xml version="1.0" ?>');													// It will contain the remaining xml elements
			$new_xml_request	= $req_arr_0.$req_arr_1;
		}

		return !empty($new_xml_request) ? $new_xml_request : $xml_request;
	}
	
	/**
	 * Update the UPS Credentials in Void Shipment XML Request .
	 * @param xml $xml_request Void Shipment XML Request.
	 * @param string $shipment_id Shipment Id for which shipment has to be voided .
	 * @param object $order wc_order object
	 * @return xml Updated void shipment XML request.
	 */
	public function xa_ups_void_shipment_xml_request( $xml_request, $shipment_id, $order_id ) {
		
		$order 	= wc_get_order($order_id);

		$stored_xml_request_array	= $order->get_meta( 'ups_created_shipments_xml_request_array', true );
		$stored_xml_request			= ! empty($stored_xml_request_array["$shipment_id"]) ? $stored_xml_request_array["$shipment_id"] : null;
		
		if( !empty($stored_xml_request) ) {

			list( $req_arr_0, $req_arr_1 )			= explode( "</AccessRequest>", $xml_request );
			$stored_xml_req_arr = explode( '</AccessRequest>', $stored_xml_request );
			$new_xml_request	=	$stored_xml_req_arr[0].'</AccessRequest>'.$req_arr_1;
		}
		
		return ! empty($new_xml_request) ? $new_xml_request : $xml_request;
	}

	/**
	 * Send UPS Label to the Vendor or Shipper.
	 * @param array $to_emails To email addresses.
	 * @param string $shipment_id Shipment Id.
	 * @param object $order WC_Order.
	 * @return array Array of email addresses.
	 */
	public function ph_ups_send_label_to_vendors( $to_emails, $shipment_id, $order ) {

		$order_id 		= ( WC()->version < 3.0 ) ? $order->ID : $order->get_id();
		$xml_requests 	= get_post_meta( $order_id, 'ups_created_shipments_xml_request_array', true );

		if( ! empty($xml_requests[$shipment_id]) ) {

			$xml_request_arr 	= explode( '<ShipmentConfirmRequest>', $xml_requests[$shipment_id] );
			$xml_request_arr[1] = '<ShipmentConfirmRequest>'.$xml_request_arr[1];
			$xml_request_obj_1	= new SimpleXMLElement($xml_request_arr[1]);

			if( ! in_array($xml_request_obj_1->Shipment->Shipper->EMailAddress, $to_emails) && in_array( 'vendor', $this->ups_settings['auto_email_label']) ) {
				$to_emails[] = trim( (string) $xml_request_obj_1->Shipment->Shipper->EMailAddress);
			}
		}

		return $to_emails;
	}

	public function ph_remove_wcfm_hide_admin_shipping_hook() {

		// WCFM Marketplace plugin class
		global $WCFMmp;

		//Remove WCFM filter hook that will remove Admin Shipping if Vendor Shipping is available
		remove_filter( 'woocommerce_package_rates', array( $WCFMmp->wcfmmp_shipping, 'wcfmmp_hide_admin_shipping'), 100 );

	}
}

new class_wf_vendor_addon_admin;