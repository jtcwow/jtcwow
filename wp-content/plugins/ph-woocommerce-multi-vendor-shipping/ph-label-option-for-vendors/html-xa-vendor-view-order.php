<?php

global $xa_current_user;

$url 						= get_permalink( get_option('woocommerce_myaccount_page_id') );
$order 						= wc_get_order($order_id);
$product_belongs_to_author 	= false;
$dokan_dashboard_order 		= isset( $dokan_dashboard ) ? true : false;

if( $order instanceof WC_Order ) {

	$order_date_created 	= date_create($order->get_date_created());
	$date_format 			= get_option('date_format');
	$create_fedex_label		= base64_encode($order_id .'|'. $xa_current_user->ID);
	$create_ups_label		= base64_encode($order_id.'|'. $xa_current_user->ID);

	echo "<div class='ph_main_box'> ";

	if( !$dokan_dashboard_order ){
	
		echo '<div class = "ph_divide_page_into_multiple_parts">';

			echo "<h2> Order #".$order->get_order_number()." details </h2>";
			
			// General details about order
			echo '<div class = "ph_full_width_of_the_page">';
					echo '<table style = "text-align: center;">';
						echo '<tr>';
							echo '<th style="width: 25%">Date Created:</th>';
							echo '<td style="width: 25%">'.$order_date_created->format($date_format).'</td>';
							echo '<th>Status:</th>';
							echo '<td>'.wc_get_order_status_name( $order->get_status() ).'</td>';
						echo '</tr>';
					echo '</table>';
			echo '</div>';

			// Address Details
			$billing_address 	= $order->get_address('billing');
			$shipping_address 	= $order->get_address('shipping');

			// Billing Address
			echo '<div class = "ph_divided_parts_of_the_page_into_two">';
				echo '<div class = "ph_border_box">';

					echo '<div class = "ph_box_header">';
						echo '<strong>Billing Address </strong>';
					echo '</div>';

					echo '<div class = "ph_box_body">';

						echo $billing_address['first_name']." ".$billing_address['last_name']."<br/>";
						
						if( !empty($billing_address['company']) ){

							echo $billing_address['company']."<br/>";
						}
						
						echo $billing_address['address_1']."<br/>";
							
						if( !empty($billing_address['address_2']) ){

							echo $billing_address['address_2']."<br/>";
						}
					
						echo $billing_address['city'].", ";
					
						echo $billing_address['state']." ";
					
						echo $billing_address['postcode']." - ";
					
						echo $billing_address['country']."<br/>";
						
						if( !empty($billing_address['email']) ){

							echo $billing_address['email'];
						}

						if( !empty($billing_address['phone']) ){

							echo " | ".$billing_address['phone']."<br/>";
						}

					echo '</div>';
					
				echo '</div>';
			echo '</div>';

			// Shipping Address
			echo '<div class = "ph_divided_parts_of_the_page_into_two">';
				echo '<div class = "ph_border_box">';

					echo '<div class = "ph_box_header">';
						echo '<strong>Shipping Address </strong>';
					echo '</div>';

					echo '<div class = "ph_box_body">';

						echo $shipping_address['first_name'].' '.$shipping_address['last_name'].'<br/>';
						
						if( !empty($billing_address['company']) ){

							echo $shipping_address['company'].'<br/>';
						}
					
						echo $shipping_address['address_1'].'<br/>';
						
						if( !empty($billing_address['address_2']) ){

							echo $shipping_address['address_2'].'<br/>';
						}
					
						echo $shipping_address['city'].', ';
					
						echo $shipping_address['state'].' ';
					
						echo $shipping_address['postcode'].' - ';
					
						echo $shipping_address['country'].'<br/>';

						if( !empty($billing_address['email']) ){
							echo "<br/>";
						}
					
				echo '</div>';

				echo '</div>';
			echo '</div><br/>';

			// Order Product Details associated with this vendor
			$order_items = $order->get_items();

			echo "<br/><div class='ph_full_width_of_the_page' >";
				echo '<h4>Product Details:</h4>';
				echo '<table style = "text-align : left;">';

					echo '<tr>';
						echo '<th>Product</th>';
						echo "<th>Total</th>";
					echo '</tr>';

					foreach( $order_items as $line_item ) {

						$product 		=	$line_item->get_product();
						$is_visible 	=	$product->is_visible();
						$post 			=	get_post($product->get_id());

						if( $post->post_author == $xa_current_user->ID )
						{
							$product_belongs_to_author 	= true;
							$permalink 					= $is_visible ? $product->get_permalink( $line_item ) :'';
							$product_name				= $permalink ? sprintf( '<a href="%s">%s</a>', $permalink, $line_item->get_name() ) : $line_item->get_name();
							
							echo '<tr>';

								echo "<td>". $product_name ." x ".$line_item->get_quantity(). "</td>";
								echo "<td>". $order->get_formatted_line_subtotal( $line_item ) ."</td>";

							echo '</tr>';

							
						}
					}

					if( $product_belongs_to_author )
					{
						if ( $order->get_shipping_method() )
						{
							echo '<tr>';

								echo "<th>Shipping via</th>";
								echo "<td>".wp_kses_post( $order->get_shipping_method() )."</td>";

							echo '</tr>';
						}

						if ( $order->get_payment_method_title() )
						{	
							echo '<tr>';

								echo "<th>Payment Method</th>";
								echo "<td>".wp_kses_post( $order->get_payment_method_title() )."</td>";

							echo '</tr>';
						}

						// echo '<tr>';

						// 	echo '<th>Order Total</th>';
						// 	echo "<td>". $order->get_formatted_order_total() ."</td>";

						// echo '</tr>';
					}

				echo '</table>';
			echo "</div>";
		echo "</div>";
	}

	if( class_exists('wf_fedEx_wooCommerce_shipping_setup') && !class_exists('wf_fedex_woocommerce_shipping_admin_helper') ) {

		require_once ABSPATH.'wp-content/plugins/fedex-woocommerce-shipping/includes/class-wf-fedex-woocommerce-shipping-admin-helper.php';

		if( !class_exists('wf_order') ) {
			require_once ABSPATH.'wp-content/plugins/fedex-woocommerce-shipping/includes/class-wf-legacy.php';
		}

		$fedex_admin 	=	new wf_fedex_woocommerce_shipping_admin_helper();
		$ph_order 		= ( WC()->version < '2.7.0' ) ? new WC_Order( $order_id ) : new wf_order( $order_id );
		$packages 		=	$fedex_admin->wf_get_package_from_order( $ph_order );

		if( !empty($packages) && is_array($packages) )
		{
			foreach( $packages as $vendor_id => $vendor_package ) {

				$count = 0;

				foreach( $vendor_package['contents'] as $key => $product_data ) {

					$product_id 		= is_object( $product_data['data'] ) ? $product_data['data']->get_id() : '';
					$post 				= !empty( $product_id ) ? get_post($product_id) : '';
					$product_author_id 	= is_object( $post ) ? $post->post_author : null;

					if( $product_author_id != $xa_current_user->ID ) {
						unset($vendor_package['contents'][$key]);
						$count++;
					}else{
						$product_belongs_to_author = true;
					}
				}

				if( $count == count($packages[$vendor_id]['contents']) ) {
					unset($packages[$vendor_id]);
				}
			}

			foreach( $packages as $package ) {
				$fedex_packages[] = $fedex_admin->get_fedex_packages($package);
			}

			echo "<div class='ph_full_width_of_the_page' >";

				echo '<h4>Package Details:</h4>';
				echo '<table style = "text-align : left;">';
					echo '<tr>';
						echo '<th>Weight</th>';
						echo "<th>Length</th>";
						echo "<th>Width</th>";
						echo "<th>Height</th>";
					echo '</tr>';

					foreach( $fedex_packages as $key => $fedex_package ) {

						foreach( $fedex_package as $fedex_package_key => $package ) {

							echo '<tr>';
								echo "<td>".$package['Weight']['Value'].' '.$package['Weight']['Units']."</td>";

								if( isset( $package['Dimensions'] ) ){
									echo "<td>". $package['Dimensions']['Length']." ".$package['Dimensions']['Units'] ."</td>";
									echo "<td>". $package['Dimensions']['Width']." ".$package['Dimensions']['Units'] ."</td>";
									echo "<td>". $package['Dimensions']['Height']." ".$package['Dimensions']['Units'] ."</td>";
								}else{
									echo "<td> - </td>";
									echo "<td> - </td>";
									echo "<td> - </td>";
								}
								
							echo '</tr>';
						}	
					}
				echo '</table>';

			echo "</div>";

		}
	}
	
	echo '<div class = "ph_divide_page_into_multiple_parts">';

		// Fedex Label Details

		if( class_exists('wf_fedEx_wooCommerce_shipping_setup') ) {

			if( !isset($this->fedex_settings) && empty($this->fedex_settings) ) {
				$this->fedex_settings = get_option( 'woocommerce_wf_fedex_woocommerce_shipping_settings', null );
			}

			if( $this->fedex_settings['ship_from_address'] == 'vendor_address' ) {

				echo '<div class = "ph_full_width_of_the_page test">';

					echo '<h4>FedEx Label:</h4>';

					if( VENDOR_PLUGIN == 'dokan_lite' )
					{
						$shipment_ids = get_post_meta( $order_id, 'wf_woo_fedex_shipmentId' );
					}else{
						$shipment_ids = get_post_meta( $order_id, 'wf_vendor_'.$xa_current_user->ID.'_woo_fedex_shipmentId' );
					}	
			
					if( empty($shipment_ids)  && $product_belongs_to_author ) {

						$fedex_services = include 'fedex/data-wf-service-codes.php';

						echo '<table>';

							echo '<tr>';
								echo "<th colspan='2'> Select Service </th>";
							echo '</tr>';

							echo '<tr>';

								echo "<td><select name='xa_create_vendor_fedex_label_service' class ='xa_create_vendor_fedex_label_service ph_service_select'>";

								foreach( $fedex_services as $fedex_service => $fedex_service_name ) {
									echo "<option value='$fedex_service'>$fedex_service_name</option>";
								}

								echo "</select></td>";

							?>

							<script type="text/javascript">
								function xa_create_vendor_fedex_label() {

									let selected_service 	=jQuery(".xa_create_vendor_fedex_label_service option:selected").val();
									let create_fedex_label 	= "<?php echo $order_id ?>";
									let dashboard_order 	= "<?php echo $dokan_dashboard_order ?>";

									location.href 			= '?&wf_fedex_createshipment='+create_fedex_label+'&create_shipment_service='+selected_service+'&dokan_dashboard='+dashboard_order;

									return false;
								};
							</script>
							<td>
								<input type="button" value="Create Label" class="xa-label-button-on-order-page xa_create_vendor_fedex_label"  onclick="xa_create_vendor_fedex_label()" />
							</td>

							<?php

							echo '</tr>';

						echo '</table>';

					}else{
			
						?>

						<script>
							function xa_create_vendor_fedex_return_label(shipment_id){

								let selected_service 	=jQuery(".xa_create_vendor_fedex_label_service option:selected").val();
								let create_fedex_label 	= "<?php echo $order_id ?>";
								let dashboard_order 	= "<?php echo $dokan_dashboard_order ?>";
								location.href 			= '?&wf_create_return_label='+create_fedex_label+'&create_shipment_service='+selected_service+'&ph_fedex_shipment_id='+shipment_id+'&dokan_dashboard='+dashboard_order;

								return false;
							}
						</script>

						<?php

						$fedex_services = include 'fedex/data-wf-service-codes.php';

						echo '<table>';

							echo '<tr>';
								echo '<th>Tracking Details</th>';
								echo '<th>Action</th>';
							echo '</tr>';

							foreach( $shipment_ids as $shipment_id ) {

								if( VENDOR_PLUGIN == 'dokan_lite' )
								{
									$shipment_service	= get_post_meta( $order_id, 'wf_woo_fedex_service_code'.$shipment_id, true);
									$additional_label 	= get_post_meta( $order_id, 'wf_fedex_additional_label_'.$shipment_id, true);

								}else{
									$shipment_service 	= '';
									$additional_label 	= '';
								}
								
								$print_fedex_label = $url.'?xa_print_vendor_fedex_label='.base64_encode($order_id .'|'.$xa_current_user->ID.'|'.$shipment_id);

								$void_fedex_label = $url.'?ph_void_vendor_fedex_shipment='.base64_encode($order_id .'|'.$xa_current_user->ID.'|'.$shipment_id);

								$void_for_vendor 	= get_option('wc_settings_ph_vendor_void_label_for_vendor');

								?>

								<tr>
									<td>
										<?php echo "<b>Tracking Number: </b>".$shipment_id; ?><br/><?php echo !empty($shipment_service) ? "<b>Shipping Service: </b>".$shipment_service : ''; ?>
									</td>
									<td>
										<input type="button" value="Print Label" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $print_fedex_label; ?>'" />

										<?php

										if( !empty($void_for_vendor) && $void_for_vendor == 'yes' ) {

											?>

											<input type="button" value="Void Shipment" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $void_fedex_label; ?>'" />

											<?php
										}

										if(!empty($additional_label) && is_array($additional_label)) {
											foreach($additional_label as $additional_key => $additional_label) {
										
												$print_additional_label = $url.'?ph_print_vendor_additional_label='.base64_encode($order_id .'|'.$xa_current_user->ID.'|'.$shipment_id.'|'.$additional_key);
												?>
												<input type="button" value="Additional Label" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $print_additional_label; ?>'" />
												<?php
											}
										}
										?>

									</td>
								</tr>

								<?php

								if( VENDOR_PLUGIN == 'dokan_lite' )
								{	
									$shipping_return_label 	= get_post_meta( $order_id, 'wf_woo_fedex_returnLabel_'.$shipment_id, true);
									$return_shipment_id 	= get_post_meta( $order_id, 'wf_woo_fedex_returnShipmetId', true);
								}else{
									$shipping_return_label = '';
									$return_shipment_id = get_post_meta( $order_id, 'wf_vendor_'.$xa_current_user->ID.'_woo_fedex_returnShipmetId'.$shipment_id, true);
								}
								
								if( !empty($shipping_return_label) && !empty($return_shipment_id) ){

									$print_fedex_label = $url.'?xa_print_vendor_fedex_return_label='.base64_encode($order_id .'|'.$xa_current_user->ID.'|'.$shipment_id);

									echo "<tr><td><b>Tracking Number: </b>$return_shipment_id</td>";
									?>
									<td>
										<input type="button" value="Print Return Label" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $print_fedex_label; ?>'" />
									</td>

									<?php

									echo '</tr>';

								}else{

									echo '<tr><td>';

									echo "<select name='xa_create_vendor_fedex_label_service' class ='xa_create_vendor_fedex_label_service ph_service_select'>";

									foreach( $fedex_services as $fedex_service => $fedex_service_name ) {
										echo "<option value='$fedex_service'>$fedex_service_name</option>";
									}

									echo "</select></td><td>";

									echo '<input type="button" value="Create Return Label" class="xa-label-button-on-order-page xa_create_vendor_fedex_return_label"  onclick="xa_create_vendor_fedex_return_label('.$shipment_id.')" />';
									echo '</td></tr>';

								}
							}

						echo '</table>';
					}

				echo '</div>';
			}
		}

		// UPS Label Details
		/*echo '<div class = "ph_full_width_of_the_page">';
			echo '<h4>UPS </h4>';
				if(0) {
					?>
						<input type="button" value="Create Label" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $create_ups_label; ?>'" />
					<?php
				}
				else{
					?>
						<input type="button" value="Print Label" class="xa-label-button-on-order-page" onclick="window.location.href='<?php echo $print_ups_label; ?>'" />
					<?php
				}
		echo '</div>';*/

	echo "</div></div><br/>";
	
}