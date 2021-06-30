<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://central.tech
 * @since      1.0.0
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Jtcwow
 * @subpackage Jtcwow/includes
 * @author     CTO-CNX <attawit@central.tech>
 */
class Jtcwow {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Jtcwow_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'JTCWOW_VERSION' ) ) {
			$this->version = JTCWOW_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'jtcwow';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_product_custom_fields_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Jtcwow_Loader. Orchestrates the hooks of the plugin.
	 * - Jtcwow_i18n. Defines internationalization functionality.
	 * - Jtcwow_Admin. Defines all hooks for the admin area.
	 * - Jtcwow_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jtcwow-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jtcwow-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jtcwow-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jtcwow-public.php';

		/**
		 * The class responsible for defining all actions that occur in the product-custom-fields-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'product-custom-fields/class-jtcwow-product-custom-fields.php';

		$this->loader = new Jtcwow_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Jtcwow_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Jtcwow_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Jtcwow_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_print_scripts', $plugin_admin, 'print_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Jtcwow_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// -------------------------------- Hide Menu --------------------------------
		$this->loader->add_filter( 'wcfm_menus', $plugin_public, 'hide_wcfm_menus', 600 );
		// -------------------------------- Hide Menu --------------------------------

		// -------------------------------- Referral --------------------------------
		// Auto set affiliate with referral parent
		$this->loader->add_action( 'wcfmmp_order_item_processed', $plugin_public, 'set_order_affiliate', 1, 2 );
		// Override Refferal form in the register form
		$this->loader->add_action( 'wmc_render_template_pre', $plugin_public, 'override_refferal_template', 10, 2 );
		// Force non affiliate account must have referral code before join refferal
		$this->loader->add_action( 'init', $plugin_public, 'require_referral_code_before_join' );
		// Override some referral shortcode
		$this->loader->add_action( 'template_redirect', $plugin_public, 'override_some_referral_shortcode', 11 );
		// -------------------------------- Referral --------------------------------

		// -------------------------------- Limit cart for one vendor only --------------------------------
		$this->loader->add_action( 'woocommerce_add_to_cart_validation', $plugin_public , 'add_to_cart_limit_vendor', 50, 3 );

		// Convert urna text
		$this->loader->add_filter( 'gettext_urna', $plugin_public, 'convert_urna_text', 10, 3 );
		// Convert wcfmu text
		$this->loader->add_filter( 'gettext_wc-frontend-manager-ultimate', $plugin_public, 'convert_wcfmu_text', 10, 3 );
		// Convert wcmm text
		$this->loader->add_filter( 'gettext_wc-multivendor-marketplace', $plugin_public, 'convert_wcmm_text', 10, 3 );
		// Convert 2c2p text
		$this->loader->add_filter( 'gettext_woo_2c2p', $plugin_public, 'convert_2c2p_text', 10, 3 );

		// Custom shipment tracking customer email
		$this->loader->add_filter( 'wcfm_is_allow_shipment_tracking_customer_email', $plugin_public, 'wcfm_custom_shipment_tracking_customer_email', 1 );

		// Override wcfm template
		$this->loader->add_filter( 'wcfm_locate_template', $plugin_public, 'override_wcfm_template', 10, 4 );

		// Override wmc template
		$this->loader->add_filter( 'wmc_template_content', $plugin_public, 'override_wmc_template', 10, 4 );

		// Set Affiliate Home Page
		$this->loader->add_filter( 'wcfm_login_redirect', $plugin_public , 'wcfmaf_affiliate_login_redirect', 60, 2 );

		$this->loader->add_filter( 'woocommerce_account_orders_columns', $plugin_public, 'add_woocommerce_account_orders_columns', 10 );
		$this->loader->add_action( 'woocommerce_my_account_my_orders_column_order-item', $plugin_public, 'wc_order_item_column', 10 );

		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'custom_woo_account_menu_items', 10, 2 );

		// Add cancel button on processing order
		// $this->loader->add_filter( 'woocommerce_valid_order_statuses_for_cancel', $plugin_public, 'add_cancel_order_button', 50, 2 );

		$this->loader->add_action( 'wp_loaded', $plugin_public, 'remove_join_referal_form_on_checkout', 100 );

		$this->loader->add_action( 'init', $plugin_public, 'auto_add_wcfm_affiliate', 10, 4 );

		$this->loader->add_filter( 'wcfm_is_allow_my_account_become_vendor', $plugin_public, 'force_logged_in_before_become_vendor' );
	}

	/**
	 * Register all of the hooks related to the product-custom-fields-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_product_custom_fields_hooks() {
		$plugin_pdcf = new Jtcwow_Product_Custom_Fields( $this->get_plugin_name(), $this->get_version() );

		// Register product custom fields
		$this->loader->add_filter( 'option_wcfm_product_custom_fields', $plugin_pdcf, 'register_custom_fields' );

		// Hide JCTWOW product custom fields in store manager
		$this->loader->add_action( 'end_wcfm_settings', $plugin_pdcf, 'remove_filter_product_custom_fields' );
		$this->loader->add_action( 'wcfm_settings_update', $plugin_pdcf, 'remove_filter_product_custom_fields' );

		// Override woocommerce format weight
		$this->loader->add_filter( 'woocommerce_format_weight', $plugin_pdcf, 'override_woocommerce_format_weight', 10, 2 );
		// Override woocommerce format dimensions (size unit)
		$this->loader->add_filter( 'woocommerce_format_dimensions', $plugin_pdcf, 'override_woocommerce_format_dimensions', 10, 2 );

		// -------------------------------- Product custom fields visibility --------------------------------
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_pdcf, 'icon_single_product_summary', 25 );
		// -------------------------------- Product custom fields visibility --------------------------------

		// -------------------------------- Product custom tabs --------------------------------
		$this->loader->add_filter( 'woocommerce_product_tabs', $plugin_pdcf, 'custom_product_tabs' );
		// -------------------------------- Product custom tabs --------------------------------

		// -------------------------------- Table products additional data --------------------------------
		// Show column additional data
		$this->loader->add_filter( 'wcfm_products_additonal_data_hidden', $plugin_pdcf, 'show_wcfm_products_additonal_data', 600 );
		$this->loader->add_filter( 'wcfm_products_additonal_data', $plugin_pdcf, 'wcfm_products_additonal_data', 600, 2 );
		// -------------------------------- Table products additional data --------------------------------

		// Delete product meta custom fields if empty
		$this->loader->add_action( 'wcfm_after_pm_custom_field_save', $plugin_pdcf, 'wcfm_delete_empty_product_custom_field', 1000, 4 );

		// Disallow policy product settings for all users except admin
		// $this->loader->add_filter( 'wcfm_is_allow_policy_product_settings', $plugin_pdcf, 'disallow_policy_product_settings', 1000 );
		// Override store policy tab title
		$this->loader->add_filter( 'wcfm_product_policy_tab_title', $plugin_pdcf, 'override_wcfm_product_policy_tab_title', 1000, 2 );

		// Convert WCFM text
		$this->loader->add_filter( 'gettext_wc-frontend-manager', $plugin_pdcf, 'convert_wcfm_text', 10, 3 );
		// Convert Woocommerce text
		$this->loader->add_filter( 'gettext_woocommerce', $plugin_pdcf, 'convert_wc_text', 10, 3 );

		//Hide edit product policy tab label
		$this->loader->add_filter( 'wcfm_product_manage_fields_policies', $plugin_pdcf, 'hide_wcfm_product_policies_tab_label', 10, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Jtcwow_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
