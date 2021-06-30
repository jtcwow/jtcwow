<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Custom Menus Private Vault Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmpv/controllers
 * @version   1.0.0
 */

class WCFM_Private_Vaults_Manage_Controller {

	public function __construct() {
		global $WCFM, $WCFMu;

		$this->processing();
	}

	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;

		$wcfm_private_vaults_manager_form_data = array();
		parse_str( $_POST['wcfm_private_vaults_manage_form'], $wcfm_private_vaults_manager_form_data );

		$wcfm_private_vault_messages = get_wcfm_private_vaults_manage_messages();
		$has_error = false;

		if(isset($_POST['status']) && ($_POST['status'] == 'draft')) {
			$private_vault_status = 'draft';
		} else {
			if( current_user_can( 'publish_shop_coupons' ) && apply_filters( 'wcfm_is_allow_publish_coupons', true ) )
				$private_vault_status = 'publish';
		}

		if( isset( $wcfm_private_vaults_manager_form_data['title'] ) && !empty( $wcfm_private_vaults_manager_form_data['title'] ) ) {

			$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

			// Creating new private vault
			$new_private_vault = apply_filters( 'wcfm_private_vault_content_before_save', array(
				'post_title'   		=> wc_clean( $wcfm_private_vaults_manager_form_data['title'] ),
				'post_status'  		=> $private_vault_status,
				'post_type'    		=> 'shop_private_vault',
				'post_author'  		=> $current_user_id,
				'post_name'    		=> sanitize_title($wcfm_private_vaults_manager_form_data['title']),
				'post_password' 	=> wp_generate_password( 6, false, false ),
				'meta_input'   		=> array(
					'_wcfmpv_email' 			=> $wcfm_private_vaults_manager_form_data['email'],
					'_wcfmpv_expiry_date' 		=> $wcfm_private_vaults_manager_form_data['expiry_date'],
					'_wcfmpv_product_ids' 		=> implode( ',', $wcfm_private_vaults_manager_form_data['product_ids'] ),
				)
			), $wcfm_private_vaults_manager_form_data );

			if( isset( $wcfm_private_vaults_manager_form_data['private_vault_id'] ) && $wcfm_private_vaults_manager_form_data['private_vault_id'] == 0 ) {
				$new_private_vault_id = wp_insert_post( $new_private_vault, true );
				do_action( 'wcfmpv_insert_private_vault', $new_private_vault_id );
			} else {
				$new_private_vault['ID'] = $wcfm_private_vaults_manager_form_data['private_vault_id'];
				unset( $new_private_vault['post_name'] );
				unset( $new_private_vault['post_password'] );
				unset( $new_private_vault['meta_input'] );
				update_post_meta( $new_private_vault['ID'] , '_wcfmpv_email', $wcfm_private_vaults_manager_form_data['email'] );
				update_post_meta( $new_private_vault['ID'] , '_wcfmpv_expiry_date', $wcfm_private_vaults_manager_form_data['expiry_date'] );
				update_post_meta( $new_private_vault['ID'] , '_wcfmpv_product_ids', implode( ',', $wcfm_private_vaults_manager_form_data['product_ids'] ) );
				$new_private_vault_id = wp_update_post( $new_private_vault, true );
				do_action( 'wcfmpv_update_private_vault', $new_private_vault_id );
			}

			do_action( 'wcfmpv_private_vault_submit', $new_private_vault_id );

			echo json_encode( array( 'status' => true, 'message' => __( 'Private Vault updated.', 'wcfm-private-vault' ), 'redirect' => get_wcfm_private_vaults_manage_url($new_private_vault_id), 'data' => $wcfm_private_vaults_manager_form_data ) );

			exit;

			echo '{ "status": true, "message": "' . __( 'Private Vault updated.', 'wcfm-private-vault' ) . '" }';

			die;

		} else {
			echo '{"status": false, "message": "' . $wcfm_private_vault_messages['no_title'] . '"}';
		}
	}
}