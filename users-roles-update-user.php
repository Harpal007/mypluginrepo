<?php
/*
Plugin Name: Update User 
Description: Create,Delete and Update User.
Plugin URI:  
Author:    Harpal Singh Chahal  
Version:     1.0.0
License: Free
*/





// add top-level administrative menu
function update_user_add_toplevel_menu() {

	add_menu_page(
		esc_html__('Users and Roles: Update User', 'useraccess'),
		esc_html__('Update User', 'useraccess'),
		'manage_options',
		'useraccess',
		'update_user_display_settings_page',
		'dashicons-admin-generic',
		null
	);

}
add_action( 'admin_menu', 'update_user_add_toplevel_menu' );



// display the plugin settings page
function update_user_display_settings_page() {

	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;

	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post">
			<h3><?php esc_html_e( 'Update User', 'useraccess' ); ?></h3>
			<p>
				<label for="email">
					<?php esc_html_e( 'Enter the user&rsquo;s email (required)', 'useraccess' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="email" id="email">
			</p>
			<p>
				<label for="display-name">
					<?php esc_html_e( 'Enter a new Display Name for this user:', 'useraccess' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="display-name" id="display-name">
			</p>
			<p>
				<label for="user-url">
					<?php esc_html_e( 'Enter a new Website URL for this user:', 'useraccess' ); ?>
				</label><br />
				<input class="regular-text" type="text" size="40" name="user-url" id="user-url">
			</p>

			<input type="hidden" name="useraccess-nonce" value="<?php echo wp_create_nonce( 'useraccess-nonce' ); ?>">
			<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Update User', 'useraccess' ); ?>">
		</form>
	</div>

<?php

}



// update user
function update_user_update_user() {

	// check if nonce is valid
	if ( isset( $_POST['useraccess-nonce'] ) && wp_verify_nonce( $_POST['useraccess-nonce'], 'useraccess-nonce' ) ) {

		// check if user is allowed
		if ( ! current_user_can( 'manage_options' ) ) wp_die();

		// get user email
		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {

			$email = sanitize_email( $_POST['email'] );

		} else {

			$email = '';

		}

		// get new display name
		if ( isset( $_POST['display-name'] ) && ! empty( $_POST['display-name'] ) ) {

			$display_name = sanitize_user( $_POST['display-name'] );

		} else {

			$display_name = '';

		}

		// get new website url
		if ( isset( $_POST['user-url'] ) && ! empty( $_POST['user-url'] ) ) {

			$user_url = esc_url( $_POST['user-url'] );

		} else {

			$user_url = '';

		}

		// get the user id
		$user_id = email_exists( $email );

		// user id exists
		if ( is_numeric( $user_id ) ) {

			// define the parameters
			$userdata = array( 'ID' => $user_id, 'display_name' => $display_name, 'user_url' => $user_url );

			// update the user
			$user_id = wp_update_user( $userdata );

			// check for errors
			if ( is_wp_error( $user_id ) ) {

				// get the error message
				$user_id = $user_id->get_error_message();

			}

		} else {

			// user not found
			$user_id = __( 'User not found.', 'useraccess' );

		}

		// set the redirect url
		$location = admin_url( 'admin.php?page=useraccess&result='. urlencode( $user_id ) );

		// redirect
		wp_redirect( $location );

		exit;

	}

}
add_action( 'admin_init', 'update_user_update_user' );



// create the admin notice
function update_user_admin_notices() {

	$screen = get_current_screen();

	if ( 'toplevel_page_useraccess' === $screen->id ) {

		if ( isset( $_GET['result'] ) ) {

			if ( is_numeric( $_GET['result'] ) ) : ?>

				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('User updated successfully.', 'useraccess'); ?></strong></p>
				</div>

			<?php else : ?>

				<div class="notice notice-warning is-dismissible">
					<p><strong><?php echo esc_html( $_GET['result'] ); ?></strong></p>
				</div>

			<?php endif;

		}

	}

}
add_action( 'admin_notices', 'update_user_admin_notices' );


