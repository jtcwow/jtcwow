<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://central.tech
 * @since      1.0.0
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/public
 * @author     CTO-CNX <attawit@central.tech>
 */
class Jtcwow_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jtcwow_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jtcwow_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jtcwow-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Jtcwow_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Jtcwow_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jtcwow-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Set order affiliate
	 * @since    1.0.0
	 *
	 * @param int $commission_id
	 * @param int $order_id
	 * @return void
	 */
	public function set_order_affiliate( $commission_id, $order_id )
	{
		$order = wc_get_order( $order_id );
		$obj_referral_users = new Referal_Users();
		$referral_parent = $obj_referral_users->referral_user( 'referral_parent', 'user_id', $order->get_user_id() );
		if ( WC()->session->get( 'wcfm_affiliate' ) != $referral_parent ) {
			WC()->session->set( 'wcfm_affiliate', (int) $referral_parent );
		}
	}

	/**
	 * Override refferal template
	 *
	 * @param string $template_path
	 * @return string
	 */
	public function override_refferal_template( $template_path )
	{
		switch( $template_path ) {
			case 'front/register_form_end_fields.php':
				add_filter( 'wmc_template_path', function( $template_path ){
					$template_path = plugin_dir_path(__FILE__) . 'partials/wmc/front/register_form_end_fields.php';
					return $template_path;
				} );
				break;

			case 'front/join-form.php':
				add_filter( 'wmc_template_path', function( $template_path ){
					$template_path = plugin_dir_path(__FILE__) . 'partials/wmc/front/join-form.php';
					return $template_path;
				} );
				break;

			default:
				return $template_path;
		}
	}

	/**
	 * Require referral code before join
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function require_referral_code_before_join()
	{
		if ( isset( $_POST['join_referral_program'] ) && isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'] , 'referral_program' ) && isset( $_POST['referral_code'] ) && $_POST['referral_code'] == '' ) {
			$user = get_user_by( 'id', get_current_user_id() );
			$is_wcfm_affiliate = false;
			if ( $user && !is_wp_error( $user ) && $user->roles && in_array( apply_filters( 'wcfm_affiliate_user_role', 'wcfm_affiliate' ), (array) $user->roles ) ) {
				$is_wcfm_affiliate = true;
			}

			if ( ! $is_wcfm_affiliate ) {
				$_POST['join_referral_program'] = 1;
			}
		}
	}

	/**
	 * Override some referral shortcode
	 * @since    1.0.0
	 *
	 * @return void
	 */
	public function override_some_referral_shortcode()
	{
		if ( is_user_logged_in() ) {
			$user = get_user_by( 'id', get_current_user_id() );
			$is_wcfm_affiliate = false;
			if ( $user && !is_wp_error( $user ) && $user->roles && in_array( apply_filters( 'wcfm_affiliate_user_role', 'wcfm_affiliate' ), (array) $user->roles ) ) {
				$is_wcfm_affiliate = true;
			}

			$obj_referral_users = new Referal_Users();
			$referral_user = $obj_referral_users->referral_user( 'user_id', 'user_id', get_current_user_id() );

			if ( ! empty( $referral_user ) && ! $is_wcfm_affiliate ) {
				remove_shortcode( 'wmc_my_referral_tab' );
				add_shortcode( 'wmc_my_referral_tab', function () {
					return '<p>You\'ve joined referral program</p>';
				} );

				remove_shortcode( 'wmc_stat_blocks' );
				add_shortcode( 'wmc_stat_blocks', function () {
					$obj_referral_users = new Referal_Users();
					return '<p>You\'ve joined referral program</p>';
				} );
			}

		}
	}

	/**
	 * Limit cart for one vendor only
	 *
	 * @param [type] $is_allow
	 * @param [type] $product_id
	 * @param [type] $quantity
	 * @return void
	 */
	public function add_to_cart_limit_vendor( $is_allow, $product_id, $quantity )
	{
		$product = get_post( $product_id );
		$product_author = $product->post_author;

		//Iterating through each cart item
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$cart_product_id = $cart_item['product_id'];
			$cart_product = get_post( $cart_product_id );
			$cart_product_author = $cart_product->post_author;
			if( $cart_product_author != $product_author ) {
				$is_allow = false;
				break;
			}
		}

		if( !$is_allow ){
			// We display an error message
			wc_clear_notices();
			wc_add_notice( __( "Well, you already have some item in your cart. First checkout with those and then purchase other items!", "wcfm-ecogear" ), 'error' );
		}

		return $is_allow;
	}

	/**
	 * Hide WCFM Menu
	 * @since    1.0.0
	 *
	 * @return array
	 */
	public function hide_wcfm_menus( $wcfm_menus )
	{
		if( wcfm_is_vendor() ) {
			unset( $wcfm_menus['wcfm-coupons'] );
		}
		return $wcfm_menus;
	}

	public function convert_urna_text( $translation, $text, $domain )
	{
		if ( $domain != 'urna' )
			return $translation;

		switch( $text ) {
			case 'Lost password?':
				$translation = __( 'Forget password?', 'jtcwow' );
				break;
		}

		return $translation;
	}

	public function convert_wcfmu_text( $translation, $text, $domain )
	{
		if ( $domain != 'wc-frontend-manager-ultimate' )
			return $translation;

		switch( $text ) {
			case 'Hey! Prompt for verification now and be a verified seller.':
				$translation = __( 'Complete your verification now and become a seller.', 'jtcwow' );
				break;
			case 'Prompt Verify':
				$translation = __( 'Submit a verification', 'jtcwow' );
				break;
			case 'Chat Conversation Copy':
				$translation = __( 'Chat history', 'jtcwow' );
				break;
			case 'Here follows a recap of the details you have entered':
				$translation = __( 'Please your chat history you have initiated as below', 'jtcwow' );
				break;
		}

		return $translation;
	}

	public function convert_wcmm_text( $translation, $text, $domain )
	{
		if ( $domain != 'wc-multivendor-marketplace' )
			return $translation;

		switch( $text ) {
			case 'Refund':
				$translation = __( 'Refund / Return', 'jtcwow' );
				break;
		}

		return $translation;
	}

	public function convert_2c2p_text( $translation, $text, $domain )
	{
		if ( $domain != 'woo_2c2p' )
			return $translation;

		switch( $text ) {
			case 'Pay via 2C2P':
				$translation = __( 'Make a payment', 'jtcwow' );
				break;
		}

		return $translation;
	}

	/**
	 * Custom shipment tracking customer email
	 */
	public function wcfm_custom_shipment_tracking_customer_email( $allow )
	{
		if ( ! $allow )
			return $allow;

		add_action( 'wcfm_after_order_mark_shipped', function( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id ) {
			if( !defined( 'DOING_WCFM_EMAIL' ) )
				define( 'DOING_WCFM_EMAIL', true );

			if( apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
				$shipment_message = apply_filters( 'wcfm_shipment_tracking_email_content', sprintf( __( 'Product <b>%s</b> has been shipped to you.<br/>Tracking Code : %s <br/>Tracking URL : <a target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), $tracking_code, $tracking_url, $order_id, $product_id );
				$notificaton_mail_subject = "[{site_name}] " . __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) . " - {product_title}";
				$notification_mail_body =  '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) . ' {customer_name}' .
																	',<br/><br/>' .
																	'<p>Thank you for shopping with us. We would like you to know that your item(s) has shipped. Your order is on its way, and can no longer be changed. If you need to return an item(s), please visit jtcwow.com</p>'.
																	'<br/><br/>' .
																	__( 'Product Shipment update:', 'wc-frontend-manager-ultimate' ) .
																	'<br/><br/>' .
																	'{shipment_message}' .
																	'<br/><br/>' .
																	sprintf( __( 'Track your package %shere%s.', 'wc-frontend-manager-ultimate' ), '<a href="{tracking_url}">', '</a>' ) .
																	'<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																	'<br/><br/>';
			} else {
				$shipment_message = apply_filters( 'wcfm_shipment_tracking_email_content', sprintf( __( 'Order <b>%s</b> has been shipped to you.<br/>Tracking Code : %s <br/>Tracking URL : <a target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $order_id, $tracking_code, $tracking_url, $tracking_url ), $tracking_code, $tracking_url, $order_id, $product_id );
				$notificaton_mail_subject = "[{site_name}] " . __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) . " - " . $order_id;
				$notification_mail_body =  '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) . ' {customer_name}' .
																	',<br/><br/>' .
																	'<p>Thank you for shopping with us. We would like you to know that your item(s) has shipped. Your order is on its way, and can no longer be changed. If you need to return an item(s), please visit jtcwow.com</p>'.
																	'<br/><br/>' .
																	__( 'Order Shipment update:', 'wc-frontend-manager-ultimate' ) .
																	'<br/><br/>' .
																	'{shipment_message}' .
																	'<br/><br/>' .
																	sprintf( __( 'Track your package %shere%s.', 'wc-frontend-manager-ultimate' ), '<a href="{tracking_url}">', '</a>' ) .
																	'<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																	'<br/><br/>';
			}

			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $notificaton_mail_subject );
			$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
			$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
			$message = str_replace( '{shipment_message}', $shipment_message, $notification_mail_body );
			$message = str_replace( '{tracking_url}', $tracking_url, $message );
			$message = str_replace( '{customer_name}', get_post_meta( $order_id, '_billing_first_name', true ), $message );
			$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) );

			$customer_email = get_post_meta( $order_id, '_billing_email', true );
			if( $customer_email ) {
				wp_mail( $customer_email, $subject, $message );
			}
		}, 10, 5 );

		// return false to force wcfm not send email again
		return false;
	}

	/**
	 * Override WCFM template
	 *
	 * @return void
	 */
	public function override_wcfm_template( $template, $template_name, $template_path, $default_path )
	{
		$override_template = plugin_dir_path(__FILE__) . "partials/{$template_path}{$template_name}";

		if ( file_exists( $override_template ) )
			return $override_template;
		else
			return $template;
	}

	/**
	 * Override WMC template
	 *
	 * @return void
	 */
	public function override_wmc_template( $content, $default_template_path, $template_path, $variables )
	{
		$override_template = plugin_dir_path(__FILE__) . "partials/wmc/{$default_template_path}";

		if ( file_exists( $override_template ) ) {
			extract( $variables );
			require( $override_template );
		} else {
			return $content;
		}
	}

	public function wcfmaf_affiliate_login_redirect( $redirect_to, $user )
	{
		if ( $user && !is_wp_error( $user ) && $user->roles && !in_array( apply_filters( 'wcfm_vendor_user_role', 'wcfm_vendor' ), (array) $user->roles ) ) {
			if ( $user && !is_wp_error( $user ) && $user->roles && in_array( apply_filters( 'wcfm_affiliate_user_role', 'wcfm_affiliate' ), (array) $user->roles ) ) {
				$redirect_to = get_permalink( get_option('woocommerce_myaccount_page_id') );
			}
		}
  		return $redirect_to;
	}

	/**
	 * Add WC account orders columns
	 *
	 * @param [type] $columns
	 * @return void
	 */
	public function add_woocommerce_account_orders_columns( $columns )
	{
		return array_merge( array( 'order-item' => __( 'Item', 'jtxwow' ) ) , $columns );
	}

	/**
	 * Replace WC order number with product image
	 *
	 * @param [type] $column_id
	 * @param [type] $order
	 * @return void
	 */
	public function wc_order_item_column( $order )
	{
		foreach( $order->get_items() as $item_id => $item ) {

			$product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			$is_visible        = $product && $product->is_visible();
			$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

			?>
				<a href="<?php echo $product_permalink;?>"><?php echo $product->get_image( array( 50, 50 ) );?></a>
			<?php
			echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<p><a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'], $item, $is_visible );

			break;
		}

	}

	/**
	 * Custom Woocommerce account menu items
	 *
	 * @param [type] $items
	 * @param [type] $endpoints
	 * @return void
	 */
	public function custom_woo_account_menu_items( $items, $endpoints )
	{
		unset($items['downloads']);

		return $items;
	}

	public function add_cancel_order_button( $status, $order )
	{
		return array( 'pending', 'failed', 'processing' );
	}

	public function remove_join_referal_form_on_checkout()
	{
		global $obj_referal_users;
		remove_filter( 'woocommerce_checkout_fields', array( $obj_referal_users, 'wmc_override_checkout_fields' ) );
	}

	public function auto_add_wcfm_affiliate()
	{
		add_filter( 'update_user_metadata', function( $check, $object_id, $meta_key, $meta_value ){
			if ( $meta_key == 'total_referrals' ) {
				$obj_referral_users = new Referal_Users();

				$wcfm_affiliate = $obj_referral_users->referral_user( 'referral_parent', 'user_id', $object_id );
				if( $wcfm_affiliate ) {
					$wcfm_affiliate = absint( $wcfm_affiliate );

					if( $wcfm_affiliate ) {
						wcfm_aff_log( "WCFMAF Save in User Meta:: Vendor => " . $object_id . " Affiliate => " . $wcfm_affiliate );
						update_user_meta( $object_id, '_wcfm_affiliate', $wcfm_affiliate );

						// Affiliate Unset from Session
						if( apply_filters( 'wcfmmp_is_allow_reset_affiliate_after_vendor_registration', true ) && WC()->session && WC()->session->get( 'wcfm_affiliate' ) ) {
							WC()->session->__unset( 'wcfm_affiliate' );
						}
					}
				}

			}
		}, 10, 4);
	}

	public function force_logged_in_before_become_vendor( $is_allow )
	{
		if( !is_user_logged_in() ) {
			$is_allow = false;
		}
		return $is_allow;
	}

}
