jQuery(document).ready( function($) {
	if( ! $("#product_ids").hasClass('wcfm_ele_for_vendor') ) {
		$("#product_ids").select2( $wcfm_product_select_args );
	}

	function wcfm_private_vaults_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').slideUp();
		var title = $.trim($('#wcfm_private_vaults_manage_form').find('#title').val());
		var email = $.trim($('#wcfm_private_vaults_manage_form').find('#email').val());
		if(title.length == 0) {
			$is_valid = false;
			$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_private_vaults_manage_messages.no_title).addClass('wcfm-error').slideDown();
			wcfm_notification_sound.play();
		} else if (email.length == 0) {
			$is_valid = false;
			$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_private_vaults_manage_messages.no_email).addClass('wcfm-error').slideDown();
			wcfm_notification_sound.play();
		}
		return $is_valid;
	}

	// Draft Coupon
	$('#wcfm_private_vault_manager_draft_button').click(function(event) {
		event.preventDefault();

		$('.wcfm_submit_button').hide();

		// Validations
		$is_valid = wcfm_private_vaults_manage_form_validate();

		if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-private-vaults-manage',
				wcfm_private_vaults_manage_form : $('#wcfm_private_vaults_manage_form').serialize(),
				status                   : 'draft'
			}
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						wcfm_notification_sound.play();
						$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						wcfm_notification_sound.play();
						$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#private_vault_id').val($response_json.id);
					$('#wcfm-content').unblock();
					$('.wcfm_submit_button').show();
				}
			});
		} else {
			$('.wcfm_submit_button').show();
		}
	});

	// Submit Coupon
	$('#wcfm_private_vault_manager_submit_button').click(function(event) {
		event.preventDefault();

		$('.wcfm_submit_button').hide();

		// Validations
		$is_valid = wcfm_private_vaults_manage_form_validate();
		if( $is_valid ) {
			$wcfm_is_valid_form = true;
			$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_private_vaults_manage_form') );
			$is_valid = $wcfm_is_valid_form;
		}

		if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-private-vaults-manage',
				wcfm_private_vaults_manage_form : $('#wcfm_private_vaults_manage_form').serialize(),
				status                   : 'submit'
			}
			$.post(wcfm_params.ajax_url, data, function(response) {
				// console.log(response);
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
					wcfm_notification_sound.play();
					if($response_json.status) {
						$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						if( $response_json.redirect ) window.location = $response_json.redirect;
						} );
					} else {
						$('#wcfm_private_vaults_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#coupon_id').val($response_json.id);
					wcfmMessageHide();
					$('#wcfm-content').unblock();
					$('.wcfm_submit_button').show();
				}
			});
		} else {
			$('.wcfm_submit_button').show();
		}
	});

});