var suggestion = false;
//PDS-3
jQuery( document ).ready( function( $ ) {

	jQuery("body").on("click", "#place_order", function () {

		let checkout 		= ph_ups_address_suggestion.checkout_address;
		let suggested 		= ph_ups_address_suggestion.suggested_address;
		var is_addr_same 	= false;

			if(checkout.address_1.toLowerCase() == suggested.address_1.toLowerCase() && checkout.city.toLowerCase() == suggested.city.toLowerCase() && ( checkout.state != null && suggested.state != null && checkout.state.toLowerCase() == suggested.state.toLowerCase() ) && checkout.country.toLowerCase() == suggested.country.toLowerCase() && checkout.postcode == suggested.postcode) {
				is_addr_same = true;
			}

			if(is_addr_same == true ){
				jQuery('.checkout').submit();
			}else{

				if (suggestion) {

					jQuery('.checkout').submit();
				}else{

					suggestion = true;
					jQuery('html, body').animate({scrollTop: 0}, 'fast');
					jQuery('#ph_addr_radio').empty();

					//lets populate the address info...
					jQuery('#customer_details').prepend('<br>');

					addr = ((suggested.address_1 == "") ? "" : suggested.address_1 + ", ");
					addr += ((suggested.city == "") ? "" : suggested.city + ", ");
					addr += ((suggested.state == "") ? "" : suggested.state + ", ");
					addr += ((suggested.postcode == "") ? "" : suggested.postcode);
					jQuery('.validation_failed_msg_checkout_page').empty(); 
					jQuery('#customer_details').prepend('<div class="ph-addr-radio">');
					jQuery('#customer_details').prepend('<input type="radio" name="ph_which_to_use" id="xph_radio_obj" value="obj"><b> ' + 'Use Suggested Address: ' + ' </b>' + addr + '');
					jQuery('#customer_details').prepend('</div>');

				    //The hidden fields that get posted back to our plugin
				    jQuery('#ph_addr_radio').append("<div style='display: hidden;'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_corrected_obj_addr1' id='ph_addr_corrected_obj_addr1' value='" + suggested.address_1 + "'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_corrected_obj_city'' id='ph_addr_corrected_obj_city' value='" + suggested.city + "'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_corrected_obj_state' id='ph_addr_corrected_obj_state' value='" + suggested.state + "'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_corrected_obj_zip' id='ph_addr_corrected_obj_zip' value='" + suggested.postcode + "'>");
				    jQuery('#ph_addr_radio').append("</div>");


				    addr = ((checkout.address_1 == "") ? "" : checkout.address_1 + ", ");
				    addr += ((checkout.address_2 == "") ? "" : checkout.address_2 + ", ");
				    addr += ((checkout.city == "") ? "" : checkout.city + ", ");
				    addr += ((checkout.state == "") ? "" : checkout.state + ", ");
				    addr += ((checkout.postcode == "") ? "" : checkout.postcode);

				    jQuery('#customer_details').prepend('<div class="ph-addr-radio">');
				    jQuery('#customer_details').prepend('<input type="radio" name="ph_which_to_use" id="ph_radio_orig" value="orig" checked><b> ' + 'Use Original Address: ' + ' </b>' + addr + '');
				    jQuery('#customer_details').prepend('</div>');
				    jQuery('#customer_details').prepend('<b>Please check the address before proceeding. If the address is correct use original address, or, use UPS suggested address.</b><br><br>');

				    //The hidden fields that get posted back to our plugin
				    jQuery('#ph_addr_radio').append("<div style='display: hidden;'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_orig_addr1' id='ph_addr_orig_addr1' value='" + checkout.address_1 + "'>");
				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_orig_city'' id='ph_addr_orig_city' value='" + checkout.city + "'>");
				    if(checkout.state != null){

				    	jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_orig_state' id='ph_addr_orig_state' value='" + checkout.state + "'>");

				    }

				    jQuery('#ph_addr_radio').append("<input type='hidden' name='ph_addr_orig_zip' id='ph_addr_orig_zip' value='" + checkout.postcode + "'>");
				    jQuery('#ph_addr_radio').append("</div>");

				    jQuery('#ph_addr_correction').show();
				    jQuery("#place_order").removeProp("disabled");

					//capture radio button changes
					jQuery('input[type=radio][name=ph_which_to_use]').change(function () {
						ph_radio_changed(this);
					});
				}

			} 
		return false;
	});


    //Handle the radio button change
    function ph_radio_changed(item) {

		//lets copy the data into the appropriate fields
		if (item.value == 'orig') {

			//go with orig values
			addr1 	= jQuery('#ph_addr_orig_addr1').val();
			addr2 	= jQuery('#ph_addr_orig_addr1').val();
			city 	= jQuery('#ph_addr_orig_city').val();
			state 	= jQuery('#ph_addr_orig_state').val();
			zip 	= jQuery('#ph_addr_orig_zip').val();

		} else {

			//it is one of the corrected fields
			addr1 	= jQuery('#ph_addr_corrected_obj_addr1').val();
			addr2 	= jQuery('#ph_addr_corrected_obj_city').val();
			city 	= jQuery('#ph_addr_corrected_obj_city').val();
			state 	= jQuery('#ph_addr_corrected_obj_state').val();
			zip 	= jQuery('#ph_addr_corrected_obj_zip').val();

		}

		if (jQuery('input[name=ship_to_different_address]').is(':checked')) {

			//shipping to different addr
			jQuery('#shipping_address_1').val(addr1);
			jQuery('#shipping_city').val(city);
			jQuery('#shipping_state').val(state);
			jQuery('#shipping_postcode').val(zip);

		} else {

			//shipping to billing
			jQuery('#billing_address_1').val(addr1);
			jQuery('#billing_city').val(city);
			jQuery('#billing_state').val(state);
			jQuery('#billing_postcode').val(zip);

			//always update the ship to in case they select it!
			jQuery('#shipping_address_1').val(addr1);
			jQuery('#shipping_city').val(city);
			jQuery('#shipping_state').val(state);
			jQuery('#shipping_postcode').val(zip);

		}

		//update checkout section when checkbox selected
			jQuery( 'body' ).trigger( 'update_checkout' );
	}
});