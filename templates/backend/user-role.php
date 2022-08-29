<?php
/**
 * The Template for edit user roles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/backend/user-role.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

?>


<div id="wpr_user_role_price_tab_data" class="panel woocommerce_options_panel">

	<?php foreach ( $user_roles as $key => $role ) : ?>

		<div class="row" data-key="<?php echo $key; ?>">

			<div class="col-6 role-name">
				<h4><?php echo $role['name']; ?></h4>
			</div>

			<div class="col-6 role-prices">

				<div class="regular-price">
					<label for="regular-price-<?php echo $key; ?>">
						<?php echo __( 'Regular price', 'wpr-user-role-price' ); ?>
					</label>
					<input type="text" id="regular-price-<?php echo $key; ?>" class="role-regular-price" value="<?php echo $role['values']['regular']; ?>" />
				</div>

				<div class="sale-price">
					<label for="sale-price-<?php echo $key; ?>">
						<?php echo __( 'Sale price', 'wpr-user-role-price' ); ?>
					</label>
					<input type="text" id="sale-price-<?php echo $key; ?>" class="role-sale-price" value="<?php echo $role['values']['sale']; ?>" />
				</div>

			</div> <!-- .role-prices -->

		</div> <!-- .row -->

	<?php endforeach; ?>

	<button type="button" class="button save-wpr-user-role button-primary"><?php echo __( 'Save prices', 'wpr-user-role-price' ); ?></button>

</div> <!-- #wpr_user_role_price_tab_data -->
