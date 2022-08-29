<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRUserRolePriceDB' ) ) :

	class WPRUserRolePriceDB {

		private static $instance;
		private $table_name;

		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRUserRolePriceDB ) ) {
				self::$instance = new WPRUserRolePriceDB;
			}

			self::$instance->table_name = self::$instance->get_table_name();

			return self::$instance;
		}

		/**
		 * If the table doesn't exist, create it
		 */
		public function setup() {
			global $wpdb;
			$my_products_db_version = '1.0.0';
			$charset_collate        = $wpdb->get_charset_collate();

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $this->table_name ) ) ) !== $this->table_name ) {

				$sql = "CREATE TABLE $this->table_name (
						ID mediumint(9) NOT NULL AUTO_INCREMENT,
						`product_id` mediumint(9) NOT NULL,
						`roles` MEDIUMTEXT NOT NULL,
						PRIMARY KEY  (ID),
                        UNIQUE INDEX uindex ( product_id )
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				add_option( 'my_db_version', $my_products_db_version );
			}
		}

		/**
		 * It returns the name of the table that stores the role prices
		 *
		 * @return The table name for the role price table.
		 */
		private function get_table_name() {
			global $wpdb;
			return $wpdb->prefix . 'wpr_role_price';
		}

		/**
		 * It takes a product ID and a comma-separated list of user roles, and saves them to the database
		 *
		 * @param product_id The ID of the product you want to set the user roles for.
		 * @param roles The roles that are allowed to purchase the product.
		 *
		 * @return The number of rows affected by the query.
		 */
		public function set_product_user_roles( $product_id, $roles ) {

			global $wpdb;

			return $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->table_name} (product_id, roles) VALUES(%d, %s) ON DUPLICATE KEY UPDATE roles=%s", array( $product_id, $roles, $roles ) ) ); // phpcs:ignore
		}

		/**
		 * It returns the product_id and roles from the table_name where the product_id is equal to the
		 * product_id passed to the function
		 *
		 * @param product_id The product ID.
		 *
		 * @return The product_id and roles for the product_id passed in.
		 */
		public function get_product_user_roles( $product_id ) {

			global $wpdb;

			$row = $wpdb->get_results( $wpdb->prepare( "SELECT product_id, roles FROM {$this->table_name} WHERE product_id = %d", array( $product_id ) ), OBJECT ); // phpcs:ignore

			if ( ! empty( $row ) ) {
				$row[0]->roles = ( is_string( $row[0]->roles ) ? json_decode( $row[0]->roles, true ) : $row[0]->roles );
				return reset( $row );
			}

			return false;
		}

		/**
		 * It takes an array of arrays and returns the first array that contains a specific value
		 *
		 * @param key The key to search for in the array.
		 * @param roles An array of roles.
		 */
		public function find_role_by_key( $key, $roles ) {

			if ( is_string( $key ) ) {
				foreach ( $roles as $idx => $role ) {
					if ( ! empty( array_search( $key, $role, true ) ) ) {
						unset( $role['role'] );
						return $role;
					}
				}
			} else {

				$user_multi_role = array();

				foreach ( $key as $role ) {
					$user_multi_role[] = $this->find_role_by_key( $role, $roles );
				}

				return $user_multi_role;
			}

			return false;
		}
	}

endif;

WPRUserRolePriceDB::instance();
