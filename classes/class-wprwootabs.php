<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRWooTabs' ) ) :

	class WPRWooTabs {

		private static $instance;

		/**
		 * It creates a singleton instance of the class.
		 *
		 * @return The instance of the class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRWooTabs ) ) {
				self::$instance = new WPRWooTabs;
				self::$instance->hooks();
			}

			return self::$instance;
		}


		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'wpr_user_role_price_product_tab' ), 10, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'wpr_user_role_price_tab_data' ) );

			add_action( 'post_updated', array( $this, 'wpr_update_product_prices_by_role' ), 10, 3 );
		}


		/**
		 * Enqueue Styles
		 */
		public function styles( $hook ) {

			if ( 'product' === $this->wpr_get_current_post_type() ) {

				// Page: Edit Product (wp-admin)
				if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
					wp_enqueue_style( 'wpr-user-role-css', WPR_USER_ROLE_PRICE_PLUGIN_ASSETS . 'css/wpr-user-role.css', array(), false );
				}
			}
		}


		/**
		 * It adds a new tab to the product edit page.
		 *
		 * @param default_tabs This is the array of default tabs that WooCommerce uses.
		 *
		 * @return The default tabs are being returned.
		 */
		public function wpr_user_role_price_product_tab( $default_tabs ) {

			$default_tabs['wpr_user_role_price'] = array(
				'label'    => __( 'User Role Price', 'wpr-user-role-price' ),
				'target'   => 'wpr_user_role_price_tab_data',
				'priority' => 100,
				'class'    => array(),
			);

			return $default_tabs;
		}


		/**
		 * It's a function that gets the template file user-role.php from the plugin's template folder and
		 * passes it an array of arguments
		 */
		public function wpr_user_role_price_tab_data() {

			$args = array(
				'user_roles' => wp_roles()->get_names(),
			);

			wc_get_template(
				'user-role.php',
				$args,
				'',
				WPR_USER_ROLE_PRICE_PLUGIN_TEMPLATE . '/backend/'
			);
		}


		/**
		 * It updates the product prices by role when a product is updated
		 *
		 * @param post_ID The ID of the post being saved.
		 * @param post_after The post object after the update.
		 * @param post_before The original post object before the update.
		 */
		public function wpr_update_product_prices_by_role( $post_ID, $post_after, $post_before ) {
			if ( ! empty( $_POST['wpr_price_role'] ) ) {

				$wpr_price_role = $_POST['wpr_price_role'];

				foreach ( $wpr_price_role as $key => $value ) {
					update_post_meta( $post_ID, 'wpr_price_by_user_role_regular_price_' . $key, preg_replace( '/[^0-9]/', '', $value['regular'] ) );
					update_post_meta( $post_ID, 'wpr_price_by_user_role_sale_price_' . $key, preg_replace( '/[^0-9]/', '', $value['sale'] ) );
				}
			}
		}



		/**
		 * It returns the current post type in the WordPress Admin
		 *
		 * @return The post type of the current page.
		 */
		public function wpr_get_current_post_type() {

			global $post, $typenow, $current_screen;

			if ( $post && $post->post_type ) {
				return $post->post_type;
			} elseif ( $typenow ) {
				return $typenow;
			} elseif ( $current_screen && $current_screen->post_type ) {
				return $current_screen->post_type;
			} elseif ( isset( $_REQUEST['post_type'] ) ) {
				return sanitize_key( $_REQUEST['post_type'] );
			}

			return null;
		}
	}

endif;

WPRWooTabs::instance();
