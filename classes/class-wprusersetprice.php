<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRUserSetPrice' ) ) :

	class WPRUserSetPrice {

		private static $instance;
		private $user;
		private $role_user;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRUserSetPrice ) ) {
				self::$instance = new WPRUserSetPrice;
				self::$instance->hooks();

				add_action( 'plugins_loaded', array( self::$instance, 'set_up_class_variable' ), 10 );
			}

			return self::$instance;
		}


		/**
		 * It sets up a class variable.
		 */
		public function set_up_class_variable() {
			$this->user      = wp_get_current_user();
			$this->role_user = $this->user->roles;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_filter( 'woocommerce_product_get_price', array( $this, 'update_price_by_role' ), 30, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( $this, 'wpr_update_regular_price_by_role' ), 30, 2 );
			add_filter( 'woocommerce_product_get_sale_price', array( $this, 'wpr_update_sale_price_by_role' ), 30, 2 );
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
		public function update_price_by_role( $price, $product ) {

			if ( is_user_logged_in() ) {

				$orginal_regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
				$orginal_sale_price    = get_post_meta( $product->get_id(), '_sale_price', true );
				$orginal_sale_price    = ( empty( $orginal_sale_price ) ? $orginal_regular_price : $orginal_sale_price );

				$minor_price = false;
				foreach ( $this->role_user as $role ) {

					$price_meta      = $product->get_meta( 'wpr_price_by_user_role_regular_price_' . $role );
					$sale_price_meta = $product->get_meta( 'wpr_price_by_user_role_sale_price_' . $role );

					$minor_price     = ( empty( $minor_price ) ? $orginal_regular_price : $minor_price );
					$price_meta      = ( empty( $price_meta ) ? $orginal_regular_price : $price_meta );
					$sale_price_meta = ( empty( $sale_price_meta ) ? $orginal_regular_price : $sale_price_meta );

					$minor_price = min( $orginal_regular_price, $orginal_sale_price, $price_meta, $sale_price_meta, $minor_price );
				}

				return $minor_price;
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

			if ( is_user_logged_in() ) {

				$orginal_regular_price = get_post_meta( $product->get_id(), '_regular_price', true );

				$minor_price = false;
				foreach ( $this->role_user as $role ) {

					$price_meta = $product->get_meta( 'wpr_price_by_user_role_regular_price_' . $role );

					$minor_price = ( empty( $minor_price ) ? $orginal_regular_price : $minor_price );
					$price_meta  = ( empty( $price_meta ) ? $orginal_regular_price : $price_meta );

					if ( ! empty( $price_meta ) && ! empty( $minor_price ) ) {
						$minor_price = min( $price_meta, $minor_price );
					} elseif ( ! empty( $price_meta ) ) {
						$minor_price = $price_meta;
					}
				}

				return $minor_price;
			}

			return $price;
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
			if ( is_user_logged_in() ) {

				$orginal_sale_price = get_post_meta( $product->get_id(), '_sale_price', true );

				$minor_price = false;
				foreach ( $this->role_user as $role ) {

					$price_meta = $product->get_meta( 'wpr_price_by_user_role_sale_price_' . $role );

					$minor_price = ( empty( $minor_price ) ? $orginal_sale_price : $minor_price );
					$price_meta  = ( empty( $price_meta ) ? $orginal_sale_price : $price_meta );

					if ( ! empty( $price_meta ) && ! empty( $minor_price ) ) {
						$minor_price = min( $price_meta, $minor_price );
					} elseif ( ! empty( $price_meta ) ) {
						$minor_price = $price_meta;
					}
				}

				return $minor_price;
			}

			return $price;
		}
	}

endif;

WPRUserSetPrice::instance();
