<?php
/**
 * Cost of Goods for WooCommerce - Analytics Class
 *
 * @version 2.4.1
 * @since   1.7.0
 * @author  WPFactory
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Cost_of_Goods_Analytics' ) ) :

class Alg_WC_Cost_of_Goods_Analytics {

	/**
	 * Constructor.
	 *
	 * @version 2.4.1
	 * @since   1.7.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce-admin/tree/master/docs/examples/extensions
	 * @see     https://woocommerce.wordpress.com/2020/02/20/extending-wc-admin-reports/
	 * @see     https://github.com/woocommerce/woocommerce-admin/issues/5092
	 *
	 * @todo    [next] caching, i.e. `woocommerce_analytics_orders_query_args` and `woocommerce_analytics_orders_stats_query_args`
	 * @todo    [later] columns: exporting (non server)
	 * @todo    [later] columns: sorting
	 * @todo    [later] remove `get_option( 'alg_wc_cog_analytics_orders', 'no' )`?
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts',                                      array( $this, 'register_script' ) );

		// Costs
		add_filter( 'woocommerce_analytics_clauses_join_orders_subquery',         array( $this, 'add_costs_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total',      array( $this, 'add_costs_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval',   array( $this, 'add_costs_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_subquery',       array( $this, 'add_costs_select_orders_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total',    array( $this, 'add_costs_select_orders_stats_total' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( $this, 'add_costs_select_orders_stats_total' ) );
		add_filter( 'woocommerce_rest_reports_column_types',                      array( $this, 'add_costs_total_reports_column_types' ), 10 );
		add_filter( 'woocommerce_export_admin_orders_report_row_data',            array( $this, 'add_costs_row_data_to_export' ),    PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_admin_orders_report_export_column_names',        array( $this, 'add_costs_columns_names_to_export' ), PHP_INT_MAX, 2 );

		// Profit
		add_filter( 'woocommerce_analytics_clauses_join_orders_subquery',         array( $this, 'add_profit_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total',      array( $this, 'add_profit_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval',   array( $this, 'add_profit_join_orders' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_subquery',       array( $this, 'add_profit_select_orders_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total',    array( $this, 'add_profit_select_orders_stats_total' ) );
		add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', array( $this, 'add_profit_select_orders_stats_total' ) );
		add_filter( 'woocommerce_rest_reports_column_types',                      array( $this, 'add_profit_total_reports_column_types' ), 10 );
		add_filter( 'woocommerce_export_admin_orders_report_row_data',            array( $this, 'add_profit_row_data_to_export' ),    PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_admin_orders_report_export_column_names',        array( $this, 'add_profit_columns_names_to_export' ), PHP_INT_MAX, 2 );

		// Test, Debug
		// woocommerce_analytics_orders_stats_select_query
		// woocommerce_analytics_orders_stats_query_args
		// woocommerce_analytics_orders_query_args
		// woocommerce_analytics_orders_select_query
		// add_filter( 'woocommerce_analytics_orders_stats_select_query', array( $this, 'debug' ) );
	}

	/*function debug( $param ) {
		error_log(print_r($param,true));
		return $param;
	}*/

	/**
	 * add_costs_total_reports_column_types.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $types
	 *
	 * @return mixed
	 */
	function add_costs_total_reports_column_types( $types ) {
		$types['costs_total'] = 'floatval';
		return $types;
	}

	/**
	 * add_costs_select_orders_stats_total.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_costs_select_orders_stats_total( $clauses ) {
		if ( 'yes' !== get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ) ) {
			return $clauses;
		}
		$clauses[] = ', SUM(order_cost_postmeta.meta_value) AS costs_total';

		// If we need to convert the currency
		//$clauses[] = ', SUM(order_cost_postmeta.meta_value * COALESCE(NULLIF(REGEXP_REPLACE(REGEXP_SUBSTR(wpo.option_value, CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:".*?(?=";)\')), CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:"\'),\'\'),\'\'),1)) as costs_total';

		return $clauses;
	}

	/**
	 * add_costs_select_orders_subquery.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_costs_select_orders_subquery( $clauses ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$clauses[] = ', IFNULL(order_cost_postmeta.meta_value, 0) AS order_cost';
		}
		return $clauses;
	}

	/**
	 * add_costs_join_orders.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_costs_join_orders( $clauses ) {
		global $wpdb;
		$clauses[] = "LEFT JOIN {$wpdb->postmeta} order_cost_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_cost_postmeta.post_id AND order_cost_postmeta.meta_key = '_alg_wc_cog_order_cost'";

		// If we need to get something fron the options database
		//$clauses[] = "JOIN {$wpdb->options} wpo ON option_name LIKE '%alg_wc_cog_currencies_rates%'";
		return $clauses;
	}

	/**
	 * add_profit_total_reports_column_types.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $types
	 *
	 * @return mixed
	 */
	function add_profit_total_reports_column_types( $types ) {
		$types['profit_total'] = 'floatval';
		return $types;
	}

	/**
	 * add_profit_select_orders_stats_total.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_profit_select_orders_stats_total( $clauses ) {
		if ( 'yes' !== get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ) ) {
			return $clauses;
		}
		$clauses[] = ', SUM(order_profit_postmeta.meta_value) AS profit_total';

		// If we need to convert the currency
		//$clauses[] = ', SUM(order_profit_postmeta.meta_value * COALESCE(NULLIF(REGEXP_REPLACE(REGEXP_SUBSTR(wpo.option_value, CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:".*?(?=";)\')), CONCAT(\'"\',\'USD\',currency_postmeta.meta_value,\'"\',\';(s|d):.+?:"\'),\'\'),\'\'),1)) as profit_total';
		return $clauses;
	}

	/**
	 * add_profit_select_orders_subquery.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_profit_select_orders_subquery( $clauses ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$clauses[] = ', IFNULL(order_profit_postmeta.meta_value, 0) AS order_profit';
		}
		return $clauses;
	}

	/**
	 * add_profit_join_orders.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $clauses
	 *
	 * @return array
	 */
	function add_profit_join_orders( $clauses ) {
		global $wpdb;
		$clauses[] = "LEFT JOIN {$wpdb->postmeta} order_profit_postmeta ON {$wpdb->prefix}wc_order_stats.order_id = order_profit_postmeta.post_id AND order_profit_postmeta.meta_key = '_alg_wc_cog_order_profit'";
		return $clauses;
	}

	/**
	 * add_costs_columns_names_to_export.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $columns
	 * @param $exporter
	 *
	 * @return mixed
	 */
	function add_costs_columns_names_to_export( $columns, $exporter ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$columns['order_cost'] = __( 'Cost', 'cost-of-goods-for-woocommerce' );
		}
		return $columns;
	}

	/**
	 * add_costs_row_data_to_export.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $row
	 * @param $item
	 *
	 * @return mixed
	 */
	function add_costs_row_data_to_export( $row, $item ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$row['order_cost'] = $item['order_cost'];
		}
		return $row;
	}

	/**
	 * add_profit_columns_names_to_export.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $columns
	 * @param $exporter
	 *
	 * @return mixed
	 */
	function add_profit_columns_names_to_export( $columns, $exporter ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$columns['order_profit'] = __( 'Profit', 'cost-of-goods-for-woocommerce' );
		}
		return $columns;
	}

	/**
	 * add_profit_row_data_to_export.
	 *
	 * @version 2.4.1
	 * @since   2.4.1
	 *
	 * @param $row
	 * @param $item
	 *
	 * @return mixed
	 */
	function add_profit_row_data_to_export( $row, $item ) {
		if ( 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ) ) {
			$row['order_profit'] = $item['order_profit'];
		}
		return $row;
	}

	/**
	 * register_script.
	 *
	 * @version 2.4.1
	 * @since   1.7.0
	 */
	function register_script() {
		if (
			! class_exists( 'Automattic\WooCommerce\Admin\Loader' )
			|| ! function_exists( 'wc_admin_is_registered_page' )
			|| ! \Automattic\WooCommerce\Admin\Loader::is_admin_page()
			|| ! apply_filters( 'alg_wc_cog_create_analytics_orders_validation', true )
		) {
			return;
		}
		wp_register_script(
			'alg-wc-cost-of-goods-analytics-report',
			plugins_url( '/build/index.js', __FILE__ ),
			array(
				'wp-hooks',
				'wp-element',
				'wp-i18n',
				'wc-components',
			),
			alg_wc_cog()->version,
			true
		);
		wp_enqueue_script( 'alg-wc-cost-of-goods-analytics-report' );
		wp_localize_script( 'alg-wc-cost-of-goods-analytics-report', 'alg_wc_cog_analytics_obj',
			array(
				'cost_and_profit_totals_enabled'  => 'yes' === get_option( 'alg_wc_cog_analytics_orders_cost_profit_totals', 'no' ),
				'cost_and_profit_columns_enabled' => 'yes' === get_option( 'alg_wc_cog_analytics_orders', 'no' ),
			)
		);
	}

}

endif;

return new Alg_WC_Cost_of_Goods_Analytics();
