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
		 * Include/Require PHP files
		 */
		public function includes() {

			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wprwootabs.php';
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wprusersetprice.php';

			\WPRWooTabs::instance();
			\WPRUserSetPrice::instance();
		}
	}

endif;

WPRUserRolePrice::instance();
