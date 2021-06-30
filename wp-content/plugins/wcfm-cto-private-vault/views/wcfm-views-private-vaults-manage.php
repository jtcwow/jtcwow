<?php
global $wp, $WCFM, $wp_query;

$private_vault_id = 0;
$name = '';
$email = '';
$expiry_date = '';
$product_ids = array();
$products_array = array();

if( isset( $wp->query_vars['wcfm-private-vaults-manage'] ) && !empty( $wp->query_vars['wcfm-private-vaults-manage'] ) ) {
	$private_vault_post = get_post( $wp->query_vars['wcfm-private-vaults-manage'] );

	if( $private_vault_post->post_type != 'shop_private_vault' ) {
		wcfm_restriction_message_show( "Invalid Private Vault" );
		return;
	}

	// Fetching Coupon Data
	if( $private_vault_post && !empty( $private_vault_post ) ) {
		$private_vault_id = $wp->query_vars['wcfm-private-vaults-manage'];
		$wcfm_private_vaults_single = $private_vault_post;
		$wc_private_vault = new WCFM_Private_Vault( $private_vault_id );

		if( !is_a( $wc_private_vault, 'WCFM_Private_Vault' ) ) {
			wcfm_restriction_message_show( "Invalid Private Vault" );
			return;
		}

		$name = $private_vault_post->post_title;
		$email = get_post_meta( $private_vault_post->ID, '_wcfmpv_email', true );
		$expiry_date = get_post_meta( $private_vault_post->ID, '_wcfmpv_expiry_date', true );
		$product_ids = get_post_meta( $private_vault_post->ID, '_wcfmpv_product_ids', true );
		$product_ids = explode( ',', $product_ids );

		$products_array = array();

		if( !empty( $product_ids ) ) {
			foreach( $product_ids as $include_product_id ) {
				if ( get_post_status ( $include_product_id ) ) {
					$products_array[$include_product_id] = get_post( absint($include_product_id) )->post_title;	
				}
			}
		}

	} else {
		wcfm_restriction_message_show( "Invalid Private Vault" );
		return;
	}
}

?>

<div class="collapse wcfm-collapse" id="wcfm_private-vault_listing">

	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-user-shield"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Private Vault', 'wcfm-private-vault' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_private_vault_manage' ); ?>

		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php if( $private_vault_id ) { _e('Edit Private Vault', 'wcfm-private-vault' ); } else { _e('Add Private Vault', 'wcfm-private-vault' ); } ?></h2>
			<?php if ( $private_vault_id ) :?>
				<?php echo '<a target="_blank" href="' . apply_filters( 'wcfm_product_preview_url', get_permalink( $private_vault_post->ID ) ) . '">'; ?>
				<span class="view_count"><span class="wcfmfa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wcfm-private-vault' ); ?>"></span>
				<?php echo get_post_meta( $private_vault_post->ID, '_wcfmpv_views', true ) . '</span></a>';?>
			<?php endif;?>
			<?php if( $has_new = apply_filters( 'wcfmpv_add_new_private_vault_sub_menu', true ) ) {
				echo '<a id="add_new_private_vault_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_private_vaults_manage_url().'" data-tip="' . __('Add New Private Vault', 'wcfm-private-vault') . '"><span class="wcfmfa fa-gift"></span><span class="text">' . __( 'Add New', 'wcfm-private-vault') . '</span></a>';
			} ?>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />

		<form id="wcfm_private_vaults_manage_form" class="wcfm">
			<?php do_action( 'begin_wcfm_private_vaults_manage_form' ); ?>
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="wcfm_private-vault_listing_expander" class="wcfm-content">
					<!---- Add Content Here ----->
					<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'coupon_manager_fields_restriction', array(
							"title" => array('label' => __('Private Vault Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $name ),
							"email" => array('label' => __('Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $email ),
							"expiry_date" => array('label' => __('Expiry date', 'wc-frontend-manager'), 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'custom_attributes' => array( 'date_format' => 'yy-mm-dd' ), 'class' => 'wcfm-text wcfm_datepicker', 'label_class' => 'wcfm_title', 'value' => $expiry_date ),
							"private_vault_id" => array('type' => 'hidden', 'value' => $private_vault_id ),
							"product_ids" => array(
								'label' => __('Products', 'wc-frontend-manager-ultimate') ,
								'type' => 'select',
								'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ),
								'class' => 'wcfm-select ',
								'label_class' => 'wcfm_title ',
								'options' => $products_array,
								'value' => $product_ids,
								// 'hints' => __( 'Products which need to be in the cart to use this coupon or, for `Product Discounts`, which products are discounted.', 'wc-frontend-manager-ultimate' )
							),
						), $private_vault_id ) );
					?>

					<div class="wcfm-clearfix"></div>
				</div>
				<div class="wcfm-clearfix"></div>
			</div>
			<!-- end collapsible -->
			<div class="wcfm_clearfix"></div><br />
			<?php do_action( 'end_wcfm_private_vaults_manage_form' ); ?>

			<div id="wcfm_private_vault_manager_submit" class="wcfm_form_simple_submit_wrapper">
			  	<div class="wcfm-message" tabindex="-1"></div>

			  	<?php if( $coupon_id && ( $coupon_post->post_status == 'publish' ) ) { ?>
				  	<input type="submit" name="submit-data" value="<?php if( apply_filters( 'wcfm_is_allow_publish_live_coupons', true ) ) { _e( 'Submit', 'wc-frontend-manager' ); } else { _e( 'Submit for Review', 'wc-frontend-manager' ); } ?>" id="wcfm_private_vault_manager_submit_button" class="wcfm_submit_button" />
				<?php } else { ?>
					<input type="submit" name="submit-data" value="<?php if( current_user_can( 'publish_shop_coupons' ) && apply_filters( 'wcfm_is_allow_publish_coupons', true ) ) { _e( 'Submit', 'wc-frontend-manager' ); } else { _e( 'Submit for Review', 'wc-frontend-manager' ); } ?>" id="wcfm_private_vault_manager_submit_button" class="wcfm_submit_button" />
			  	<?php } ?>

				<?php if( apply_filters( 'wcfm_is_allow_draft_published_coupons', true ) && apply_filters( 'wcfm_is_allow_add_coupons', true ) ) { ?>
				  	<input type="submit" name="draft-data" value="<?php _e( 'Draft', 'wc-frontend-manager' ); ?>" id="wcfm_private_vault_manager_draft_button" class="wcfm_submit_button" />
				<?php } ?>
			</div>
			<input type="hidden" name="wcfm_nonce" value="<?php echo wp_create_nonce( 'wcfm_private_vault_manage' ); ?>" />

		</form>

		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_private_vault_manage' );
		?>
	</div>
</div>