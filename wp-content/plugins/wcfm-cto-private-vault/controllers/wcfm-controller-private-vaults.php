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

class WCFM_Private_Vault_Controller {

	public function __construct() {
		global $WCFM, $WCFMu;

		$this->processing();
	}

	public function processing() {
		global $WCFM, $wpdb, $_POST;

		$length = wc_clean($_POST['length']);
		$offset = wc_clean($_POST['start']);

		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'shop_private_vault',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = wc_clean($_POST['search']['value']);

		$args = apply_filters( 'wcfm_coupons_args', $args );

		if( isset($_POST['coupon_vendor']) && !empty($_POST['coupon_vendor']) ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace ) {
				if( !wcfm_is_vendor() ) {
					$args['author'] = absint( $_POST['coupon_vendor'] );
				}
			}
		}

		$wcfm_coupons_array = get_posts( $args );

		// Get Coupon Count
		$coupon_count = 0;
		$filtered_coupon_count = 0;
		$wcfm_coupons_count = wp_count_posts('shop_private_vault');
		$coupon_count = ( $wcfm_coupons_count->publish + $wcfm_coupons_count->pending + $wcfm_coupons_count->draft );
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_coupons_array = get_posts( $args );
		$filtered_coupon_count = count($wcfm_filterd_coupons_array);


		// Generate Coupons JSON
		$wcfm_coupons_json = '';
		$wcfm_coupons_json = '{
															"draw": ' . wc_clean($_POST['draw']) . ',
															"recordsTotal": ' . $coupon_count . ',
															"recordsFiltered": ' . $filtered_coupon_count . ',
															"data": ';
		if(!empty($wcfm_coupons_array)) {
			$index = 0;
			$wcfm_coupons_json_arr = array();
			foreach($wcfm_coupons_array as $wcfm_coupons_single) {
				$wc_coupon = new WC_Coupon( $wcfm_coupons_single->ID );
				// Code
				if( $wcfm_coupons_single->post_status != 'publish' ) {
					$wcfm_coupons_json_arr[$index][] =  '<a href="' . get_wcfm_private_vaults_manage_url($wcfm_coupons_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</a>' . ' -- ' . __( ucfirst( $wcfm_coupons_single->post_status ), 'wc-frontend-manager' );
				} elseif( current_user_can( 'edit_published_shop_coupons' ) && apply_filters( 'wcfm_is_allow_edit_coupons', true ) ) {
					$wcfm_coupons_json_arr[$index][] =  '<a href="' . get_wcfm_private_vaults_manage_url($wcfm_coupons_single->ID) . '" class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</a>';
				} else {
					$wcfm_coupons_json_arr[$index][] =  '<span class="wcfm_dashboard_item_title">' . $wcfm_coupons_single->post_title . '</span>';
				}

				// Type
				$wcfm_coupons_json_arr[$index][] =  '<span>' . esc_html( get_post_meta( $wcfm_coupons_single->ID, '_wcfmpv_email', true ) ) . '</span>';

				// Coupon Amount
				$wcfm_coupons_json_arr[$index][] =  esc_html( get_post_meta( $wcfm_coupons_single->ID, '_wcfmpv_views', true ) );

				// Expire Date
				$wcfm_coupons_json_arr[$index][] = get_post_meta( $wcfm_coupons_single->ID, '_wcfmpv_expiry_date', true ) ?? '&ndash;';

				// Action
				$actions = '';
				if( $wcfm_coupons_single->post_status == 'publish' ) {
				  $actions .= ( current_user_can( 'edit_published_shop_coupons' ) && apply_filters( 'wcfm_is_allow_edit_coupons', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_private_vaults_manage_url($wcfm_coupons_single->ID) . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
				} else {
					$actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_private_vaults_manage_url($wcfm_coupons_single->ID) . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>';
				}
				$wcfm_coupons_json_arr[$index][] = apply_filters ( 'wcfm_coupons_actions', $actions, $wcfm_coupons_single );

				$index++;
			}
		}
		if( !empty($wcfm_coupons_json_arr) ) $wcfm_coupons_json .= json_encode($wcfm_coupons_json_arr);
		else $wcfm_coupons_json .= '[]';
		$wcfm_coupons_json .= '
													}';

		echo $wcfm_coupons_json;
	}
}