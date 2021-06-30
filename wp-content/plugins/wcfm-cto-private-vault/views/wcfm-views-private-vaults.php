<?php
global $WCFM, $wp_query;

?>

<div class="collapse wcfm-collapse" id="wcfm_private-vault_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-user-shield"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Private Vault', 'wcfm-private-vault' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_private-vault' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Private Vault', 'wcfm-private-vault' ); ?></h2>
			<?php if( $has_new = apply_filters( 'wcfmpv_add_new_private_vault_sub_menu', true ) ) {
				echo '<a id="add_new_private_vault_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_private_vaults_manage_url().'" data-tip="' . __('Add New Private Vault', 'wcfm-private-vault') . '"><span class="wcfmfa fa-gift"></span><span class="text">' . __( 'Add New', 'wcfm-private-vault') . '</span></a>';
			} ?>
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
		

		<div class="wcfm-container">
			<div id="wcfm_private-vault_listing_expander" class="wcfm-content">
			
				<!---- Add Content Here ----->
			
				<div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	
		<div class="wcfm-clearfix"></div>

		<div class="wcfm-container">
			<div id="wcfm_coupons_listing_expander" class="wcfm-content">
				<table id="wcfm-coupons" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Name', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Email', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Views', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Expiry date', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Action', 'wcfm-private-vault' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Name', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Email', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Views', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Expiry date', 'wcfm-private-vault' ); ?></th>
							<th><?php _e( 'Action', 'wcfm-private-vault' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_private-vault' );
		?>
	</div>
</div>