<?php
/**
 * Plugin Name: WPR User Role Price
 * Plugin URI: #
 * Description: Set price to products based user role (plugin allow multi roles for user) but use the lower price
 * Version: 0.0.1
 * Author: Andrei Leca
 * Author URI:
 * Text Domain: wpr-user-role-price
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

namespace WPRUserRolePrice;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRUserRolePrice' ) ) :

	class WPRUserRolePrice {

		private static $instance;
		private $db;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRUserRolePrice ) ) {
				self::$instance = new WPRUserRolePrice;
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Constants
		 */
		public function constants() {
			// Plugin version
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_VERSION' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_VERSION', '0.0.1' );
			}

			// Plugin file
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_FILE' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_FILE', __FILE__ );
			}

			// Plugin basename
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_BASENAME' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_BASENAME', plugin_basename( WPR_USER_ROLE_PRICE_PLUGIN_FILE ) );
			}

			// Plugin directory path
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_DIR_PATH' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( WPR_USER_ROLE_PRICE_PLUGIN_FILE ) ) );
			}

			// Plugin directory URL
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_DIR_URL' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( WPR_USER_ROLE_PRICE_PLUGIN_FILE ) ) );
			}

			// Plugin directory classes
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_CLASSES' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_CLASSES', trailingslashit( WPR_USER_ROLE_PRICE_PLUGIN_DIR_PATH . 'classes' ) );
			}

			// Plugin directory template
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_TEMPLATE' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_TEMPLATE', trailingslashit( WPR_USER_ROLE_PRICE_PLUGIN_DIR_PATH . 'templates' ) );
			}

			// Plugin directory assets
			if ( ! defined( 'WPR_USER_ROLE_PRICE_PLUGIN_ASSETS' ) ) {
				define( 'WPR_USER_ROLE_PRICE_PLUGIN_ASSETS', trailingslashit( WPR_USER_ROLE_PRICE_PLUGIN_DIR_URL . 'assets' ) );
			}
		}

		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			register_activation_hook( WPR_USER_ROLE_PRICE_PLUGIN_FILE, array( $this, 'activate' ) );
			register_deactivation_hook( WPR_USER_ROLE_PRICE_PLUGIN_FILE, array( $this, 'deactivate' ) );
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wpruserrolepricedb.php';
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wprwootabs.php';
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wpruserrolepriceendpoints.php';
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wprusersetprice.php';

			$this->db = \WPRUserRolePriceDB::instance();

			\WPRWooTabs::instance();
			\WPRUserRolePriceEndPoints::instance();
			\WPRUserSetPrice::instance();
		}

		/**
		 * Run on plugin activation
		 */
		public function activate() {
			$this->db->setup();
		}

		/**
		 * Run on plugin de-activation
		 */
		public function deactivate() {

		}
	}

endif;

WPRUserRolePrice::instance();
