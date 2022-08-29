<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPRUserRolePriceEndPoints' ) ) :

	class WPRUserRolePriceEndPoints {

		private static $instance;
		private $db;

		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPRUserRolePriceEndPoints ) ) {
				self::$instance = new WPRUserRolePriceEndPoints;
				self::$instance->includes();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Action/filter hooks
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'update_prices_for_user_roles_endpoints' ) );
		}

		/**
		 * Include/Require PHP files
		 */
		public function includes() {
			require_once WPR_USER_ROLE_PRICE_PLUGIN_CLASSES . 'class-database.php';
			$this->db = \WPRUserRolePriceDB::instance();
		}

		/**
		 * It registers a new endpoint for the REST API that can be accessed at
		 * `/wp-json/roles/user_roles/update` and that accepts a POST request
		 *
		 * @return json.
		 */
		public function update_prices_for_user_roles_endpoints() {

			register_rest_route(
				'roles/user_roles',
				'/update',
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_prices_for_user_roles' ),
					'args'                => array(
						'post_id' => array(),
						'rules'   => array(),
					),
					'permission_callback' => function () {
						return current_user_can( 'edit_others_posts' );
					},
				)
			);
		}


		/**
		 * It updates the prices for user roles.
		 *
		 * @param \WP_REST_Request request The request object.
		 */
		public function update_prices_for_user_roles( \WP_REST_Request $request ) {

			$params = $request->get_params();

			$product_id = preg_replace( '/[^0-9]/i', '', $params['post_id'] );
			$rules      = preg_replace( '/[^a-z0-9\[\]\"\:\{\}\,\-\_]/', '', json_encode( $params['rules'] ) );

			$status = $this->db->set_product_user_roles( $product_id, $rules );

			wp_send_json(
				array(
					'status'  => ( $status ? 'success' : 'error' ),
					'message' => ( $status ? __( 'Rules update successfully', 'wpr-user-role-price' ) : __( 'Rules update has failed', 'wpr-user-role-price' ) ),
				)
			);
		}
	}

endif;

WPRUserRolePriceEndPoints::instance();
