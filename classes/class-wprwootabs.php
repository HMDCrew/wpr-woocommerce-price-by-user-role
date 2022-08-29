<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRWooTabs' ) ) :

	class WPRWooTabs {

		private static $instance;
		private $db;

		/**
		 * It creates a singleton instance of the class.
		 *
		 * @return The instance of the class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRWooTabs ) ) {
				self::$instance = new WPRWooTabs;
				self::$instance->includes();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'wpr_user_role_price_product_tab' ), 10, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'wpr_user_role_price_tab_data' ) );
		}

		/**
		 * Enqueue Scripts
		 */
		public function scripts( $hook ) {

			if ( 'product' === $this->wpr_get_current_post_type() ) {

				// Page: Edit Product (wp-admin)
				if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
					wp_enqueue_script( 'wpr_user_role_js', WPR_USER_ROLE_PRICE_PLUGIN_ASSETS . 'js/wpr-user-role.js', array( 'jquery' ) );
					wp_add_inline_script(
						'wpr_user_role_js',
						'var wpr_user_role_js = ' .
						json_encode(
							array(
								'root'  => esc_url_raw( rest_url() ) . 'roles/user_roles',
								'nonce' => wp_create_nonce( 'wp_rest' ),
							)
						),
						'before'
					);
				}

				// Page: List products (wp-admin)
				if ( 'edit.php' === $hook ) {

				}
			}
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

				// Page: List products (wp-admin)
				if ( 'edit.php' === $hook ) {

				}
			}
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-wpruserrolepricedb.php';
			$this->db = \WPRUserRolePriceDB::instance();
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

			$role_mixer = $this->role_mixer(
				wp_roles()->get_names(),
				$this->db->get_product_user_roles( wc_get_product()->get_id() )
			);

			$args = array(
				'user_roles' => $role_mixer,
			);

			wc_get_template(
				'user-role.php',
				$args,
				'',
				WPR_USER_ROLE_PRICE_PLUGIN_TEMPLATE . '/backend/'
			);
		}



		/**
		 * It takes two arrays, one with the system roles and one with the database roles, and merges them
		 * together
		 *
		 * @param sys_roles This is the array of roles that are defined in the plugin.
		 * @param db_roles This is the database roles object.
		 */
		public function role_mixer( $sys_roles, $db_roles ) {

			foreach ( $sys_roles as $key => $role_name ) {

				$key_role = ( ! empty( $db_roles ) ? $this->db->find_role_by_key( $key, $db_roles->roles ) : false );

				$sys_roles[ $key ]           = array();
				$sys_roles[ $key ]['name']   = $role_name;
				$sys_roles[ $key ]['values'] = ( ! empty( $key_role ) ? $key_role : array(
					'regular' => '',
					'sale'    => '',
				) );
			}

			return $sys_roles;
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
