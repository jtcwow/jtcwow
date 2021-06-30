<?php

global	$xa_current_user;
global  $wpdb;

$current_user 		= $xa_current_user->ID;
$wp_date_format 	= get_option('date_format');
$url 				= get_permalink( get_option('woocommerce_myaccount_page_id') );
$current_page 		= !empty( get_query_var( 'ph-all-order', 1 ) ) ? get_query_var( 'ph-all-order', 1 ) : 1;
$result_posts 		= array();
$total_order_count 	= 0;
$page_limit 		= 7;
$page_offset       	= ( $current_page - 1 ) * $page_limit;


if( VENDOR_PLUGIN == 'dokan_lite' )
{

	$post_query 	= "SELECT * FROM {$wpdb->prefix}dokan_orders AS ph_do LEFT JOIN {$wpdb->prefix}posts ph_p ON ph_do.order_id = ph_p.ID WHERE ph_do.seller_id = {$current_user} ORDER BY ph_p.post_date DESC LIMIT {$page_offset}, {$page_limit}";

	$result_posts 	= 	$wpdb->get_results( $post_query );
	
}else{
	$result_posts = array();
}

if( !empty($result_posts) && is_array($result_posts) )
{

	echo "<table>";
	echo "<thead>";
		echo "<tr>";
			echo "<th>Order</th>";
			echo "<th>Date</th>";
			echo "<th>Status</th>";
			echo "<th>Order Total</th>";
			echo "<th>Actions</th>";
		echo "</tr>";
	echo "</thead>";
	echo "<tbody>";

	foreach( $result_posts as $order_post ) {

		$order 		= wc_get_order( $order_post );

		if( $order instanceof WC_Order ) {
			
			$view_url 		= $url."ph-all-order/?ph_view_order_on_front_end=".$order_post->ID;
			$date 			= date_create($order_post->post_date);
			$date 			= date_format( $date, $wp_date_format);
			$order_total 	= isset($order_post->order_total) && !empty($order_post->order_total) ? $order_post->order_total : '-';

			echo "<tr>";

			echo "<td> <a href='".$view_url."'> #".$order_post->ID."</a> </td>";
			echo "<td>".$date."</td>";
			echo "<td>".wc_get_order_status_name($order_post->post_status)."</td>";
			echo "<td>".wp_kses_post( sprintf( '%1$s', $order_total ) )."</td>";

			?>

			<td><a style="margin: 4px 4px;" class="button button-primary tips onclickdisable ph_view_order_on_front_end" href="<?php echo $view_url; ?>">	<?php _e('View', 'ph-multi-vendor-shipping'); ?></a></td>

			<?php

			echo "</tr>";
		}
	}
	
	echo "</tbody>";
	echo "</table>";

	$total_order_count  = wp_cache_get( 'ph_total_vendor_orders' );

	if( !$total_order_count )
	{

		if( VENDOR_PLUGIN == 'dokan_lite' ){
			$count_query = "SELECT COUNT(ph_do.order_id) as count
			FROM {$wpdb->prefix}dokan_orders AS ph_do
			LEFT JOIN {$wpdb->prefix}posts ph_p ON ph_do.order_id = ph_p.ID
			WHERE ph_do.seller_id = {$current_user}";

			$result_count 		= $wpdb->get_row( $count_query );
			$total_order_count 	= $result_count->count;

			wp_cache_set( 'ph_total_vendor_orders' , $total_order_count );
		}
	}

	$total_pages 	= ceil( $total_order_count / $page_limit );

	if ( $total_pages > 1 ) {
		?>
		
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">

			<?php if ( 1 != $current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'ph-all-order', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php } if ( intval( $total_pages ) != $current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'ph-all-order', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php } ?>
		</div>
		<?php 
	}

}else{

	?>

	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>

	<?php 
}