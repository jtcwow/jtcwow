<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://central.tech
 * @since      1.0.0
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/admin
 * @author     CTO-CNX <attawit@central.tech>
 */
class Jtcwow_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jtcwow-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jtcwow-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function print_scripts()
	{
		// Fix bug Uncaught TypeError: Cannot read property 'addEventListener' of null
		if ( class_exists( 'WooCommerce_Multilevel_Referal' ) ) {
			if ( ! is_admin() || isset( $_GET['page'] ) && $_GET['page'] != 'wc_referral' ) {
				wp_dequeue_script( WooCommerce_Multilevel_Referal::PREFIX . 'woocommerce-multilevel-referral-admin' );
			} elseif ( is_admin() && ! isset( $_GET['page'] ) ) {
				wp_dequeue_script( WooCommerce_Multilevel_Referal::PREFIX . 'woocommerce-multilevel-referral-admin' );
			}
		}
	}

}
