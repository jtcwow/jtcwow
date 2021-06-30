jQuery(function($){

	ph_void_label_for_vendor();
	jQuery('#wc_settings_ph_vendor_label_for_vendor').click(function(){
		ph_void_label_for_vendor();
	});

});


function ph_void_label_for_vendor(){
	
	var checked	=	jQuery('#wc_settings_ph_vendor_label_for_vendor').is(":checked");

	if(checked){
		jQuery('#wc_settings_ph_vendor_void_label_for_vendor').closest('tr').show();
	}else{
		jQuery('#wc_settings_ph_vendor_void_label_for_vendor').closest('tr').hide();
	}
}