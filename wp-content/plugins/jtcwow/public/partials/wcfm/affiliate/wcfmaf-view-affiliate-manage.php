<?php
/**
 * WCFM plugin views
 *
 * Plugin Deivery Boys Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmaf/views
 * @version   1.0.0
 */
global $wp, $WCFM, $WCFMaf;

if( !apply_filters( 'wcfm_is_allow_manage_affiliate', true ) || !apply_filters( 'wcfm_is_allow_affiliate', true )  || wcfm_is_vendor() ) {
	wcfm_restriction_message_show( "Affiliate Manage" );
	return;
}

$affiliate_id = 0;
$affiliate_code = '';
$user_name = '';
$user_phone = '';
$user_email = '';
$first_name = '';
$last_name = '';

$commission = get_option( 'wcfm_affiliate_commission', array() );

if( isset( $wp->query_vars['wcfm-affiliate-manage'] ) && empty( $wp->query_vars['wcfm-affiliate-manage'] ) ) {
	if( !apply_filters( 'wcfm_is_allow_add_affiliate', true ) ) {
		wcfm_restriction_message_show( "Add Affiliate" );
		return;
	}
	if( !apply_filters( 'wcfm_is_allow_affiliate_limit', true ) ) {
		wcfm_restriction_message_show( "Affiliate Limit Reached" );
		return;
	}
}

if( isset( $wp->query_vars['wcfm-affiliate-manage'] ) && !empty( $wp->query_vars['wcfm-affiliate-manage'] ) ) {
	$affiliate_id = absint( $wp->query_vars['wcfm-affiliate-manage'] );
	$affiliate_user = get_userdata( $affiliate_id );

	// Fetching Affiliate Data
	if($affiliate_user && !empty($affiliate_user)) {

		if ( !in_array( 'wcfm_affiliate', (array) $affiliate_user->roles ) ) {
			wcfm_restriction_message_show( "Invalid Affiliate" );
			return;
		}

		$user_name = $affiliate_user->user_login;
		$user_email = $affiliate_user->user_email;
		$first_name = $affiliate_user->first_name;
		$last_name = $affiliate_user->last_name;

		$user_phone = get_user_meta( $affiliate_id, 'billing_phone', true );

		$affiliate_code = get_user_meta( $affiliate_id, 'affiliate_code', true );

		$commission = get_user_meta( $affiliate_id, 'wcfm_affiliate_commission', true );
		//$commission = get_user_meta( $affiliate_id, 'commission', true );

		if( !$commission ) {
			$commission = get_option( 'wcfm_affiliate_commission', array() );
		}

		//print_r(wcfm_get_affiliate_vendors( $affiliate_id ));
		//$has_custom_capability = get_user_meta( $affiliate_id, '_wcfm_user_has_custom_capability', true ) ? get_user_meta( $affiliate_id, '_wcfm_user_has_custom_capability', true ) : 'no';
	} else {
		wcfm_restriction_message_show( "Invalid Affiliate" );
		return;
	}
}

do_action( 'before_wcfm_affiliate_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-user-friends"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Affiliate', 'wc-frontend-manager-affiliate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>

	  <div class="wcfm-container wcfm-top-element-container">
	    <h2><?php if( $affiliate_id ) { _e('Manage Affiliate', 'wc-frontend-manager-affiliate' ); } else { _e('Add Affiliate', 'wc-frontend-manager-affiliate' ); } ?></h2>

			<?php
			if( apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('user-new.php'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-affiliate' ); ?>"><span class="fab fa-wordpress"></span></a>
				<?php

				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_affiliate_stats_url( $affiliate_id ).'" data-tip="' . __('Affiliate User Commission Stats', 'wc-frontend-manager-affiliate') . '"><span class="wcfmfa fa-chart-line"></span></a>';
			}

			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_affiliate_dashboard_url().'" data-tip="' . __('Manage Affiliate', 'wc-frontend-manager-affiliate') . '"><span class="wcfmfa fa-user-friends"></span></a>';

			if( apply_filters( 'wcfm_add_new_staff_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_affiliate_manage_url().'" data-tip="' . __('Add New Affiliate', 'wc-frontend-manager-affiliate') . '"><span class="wcfmfa fa-user-plus"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager' ) . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />

	  <?php do_action( 'begin_wcfm_affiliate_manage' ); ?>

		<form id="wcfm_affiliate_manage_form" class="wcfm">

		  <?php do_action( 'begin_wcfm_affiliate_manage_form' ); ?>

			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="affiliate_manage_general_expander" class="wcfm-content">
						<?php
						  if( $affiliate_id ) {
						  	$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "user_name" => array( 'label' => __('Username', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'attributes' => array( 'readonly' => true ), 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_name ) ) );
						  } else {
						  	$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "user_name" => array( 'label' => __('Username', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_name ) ) );
						  }
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_affiliate_manager_fields_general', array(
																																						"user_email" => array( 'label' => __('Email', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_email),
																																						"user_phone" => array( 'label' => __('Phone', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $user_phone),
																																						"first_name" => array( 'label' => __('First Name', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $first_name),
																																						"last_name" => array( 'label' => __('Last Name', 'wc-frontend-manager-affiliate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $last_name),
																																						"affiliate_id" => array('type' => 'hidden', 'value' => $affiliate_id )
																																					), $affiliate_id ) );
						?>

				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->

			<?php do_action( 'end_wcfm_affiliate_manage_form' ); ?>

			<div id="wcfm_affiliate_manager_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>

				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager' ); ?>" id="wcfm_affiliate_manager_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_affiliate_manage' );
			?>
			<div class="wcfm_clearfix"></div>
		</form>

	</div>
</div>