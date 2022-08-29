<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRUserSetPrice' ) ) :

	class WPRUserSetPrice {

		private static $instance;
		private $user;
		private $role_user;

		private $db;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRUserSetPrice ) ) {
				self::$instance = new WPRUserSetPrice;
				self::$instance->includes();
				self::$instance->hooks();

				add_action( 'plugins_loaded', array( self::$instance, 'set_up_class_variable' ), 10 );
			}

			return self::$instance;
		}

		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_filter( 'woocommerce_product_get_price', array( $this, 'update_price_by_role' ), 10, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( $this, 'wpr_update_regular_price_by_role' ), 10, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( $this, 'wpr_update_sale_price_by_role' ), 10, 2 );
		}



		/**
		 * It sets up a class variable.
		 */
		public function set_up_class_variable() {
			$this->user      = wp_get_current_user();
			$this->role_user = $this->user->roles;
		}



		/**
		 * If the user is logged in, and the product has a role price, then return the lowest price for the
		 * user's role
		 *
		 * @param price The price of the product.
		 * @param product The product object
		 * @param price_type sale or regular
		 *
		 * @return The minimum price of the product.
		 */
		public function update_price_by_role( $price, $product, $price_type = 'sale' ) {

			if ( ! empty( $this->role_user ) ) {

				$prod_roles = $this->db->get_product_user_roles( $product->get_id() );
				$key_role   = $this->db->find_role_by_key( $this->role_user, $prod_roles->roles );

				$first_role = reset( $key_role );
				$min_price  = ( ! empty( $first_role[ $price_type ] ) ? $first_role[ $price_type ] : $first_role['regular'] );

				foreach ( $key_role as $role ) {

					if ( $role[ $price_type ] < $min_price ) {
						$min_price = $role[ $price_type ];
					}
				}

				return $min_price;
			}

			return $price;
		}



		/**
		 * If the user is logged in and has a role that has a price modifier, then return the modified price
		 *
		 * @param price The price that is being filtered.
		 * @param product The product object
		 *
		 * @return The price of the product.
		 */
		public function wpr_update_regular_price_by_role( $price, $product ) {
			return $this->update_price_by_role( $price, $product, 'regular' );
		}



		/**
		 * If the product is on sale, and the user is logged in, and the user has a role that has a sale
		 * price, then return the sale price for that role
		 *
		 * @param price The price that is being filtered.
		 * @param product The product object
		 *
		 * @return The price of the product.
		 */
		public function wpr_update_sale_price_by_role( $price, $product ) {
			return $this->update_price_by_role( $price, $product, 'sale' );
		}


		/**
		 * Include/Require PHP files
		 */
		public function includes() {
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-database.php';
			$this->db = \WPRUserRolePriceDB::instance();
		}

	}

endif;

WPRUserSetPrice::instance();
