<?php
/*
Plugin Name: Add User
Description: Create,Delete and Update User ().
Plugin URI:  
Author:    Harpal Singh Chahal  
Version:     1.0.0
License: Free
*/




// add top-level administrative menu
function create_user_add_toplevel_menu() {

	add_menu_page(
		esc_html__('Users and Roles: Create User', 'useraccess'),
		esc_html__('Create User', 'useraccess'),
		'manage_options',
		'useraccess',
		'create_user_display_settings_page',
		'dashicons-admin-generic',
		null
	);

}
add_action( 'admin_menu', 'create_user_add_toplevel_menu' );



// display the plugin settings page
function create_user_display_settings_page() {

	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;

	?>

	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form method="post">
			<h3><?php esc_html_e( 'Add New User', 'useraccess' ); ?></h3>
			<p>
				<label for="username"><?php esc_html_e( 'Username', 'useraccess' ); ?></label><br />
				<input class="regular-text" type="text" size="40" name="username" id="username">
			</p>
			<p>
				<label for="email"><?php esc_html_e( 'Email', 'useraccess' ); ?></label><br />
				<input class="regular-text" type="text" size="40" name="email" id="email">
			</p>
			<p>
				<label for="password"><?php esc_html_e( 'Password', 'useraccess' ); ?></label><br />
				<input class="regular-text" type="text" size="40" name="password" id="password">
			</p>

			<p><?php esc_html_e( 'The user will receive this information via email.', 'useraccess' ); ?></p>

			<input type="hidden" name="useraccess-nonce" value="<?php echo wp_create_nonce( 'useraccess-nonce' ); ?>">
			<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Add User', 'useraccess' ); ?>">
		</form>
	</div>

<?php

}



// add new user
function create_user_add_user() {

	// check if nonce is valid
	if ( isset( $_POST['useraccess-nonce'] ) && wp_verify_nonce( $_POST['useraccess-nonce'], 'useraccess-nonce' ) ) {

		// check if user is allowed
		if ( ! current_user_can( 'manage_options' ) ) wp_die();

		// get submitted username
		if ( isset( $_POST['username'] ) && ! empty( $_POST['username'] ) ) {

			$username = sanitize_user( $_POST['username'] );

		} else {

			$username = '';

		}

		// get submitted email
		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {

			$email = sanitize_email( $_POST['email'] );

		} else {

			$email = '';

		}

		// get submitted password
		if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {

			$password = $_POST['password']; // sanitized by wp_create_user()

		} else {

			$password = wp_generate_password();

		}

		// set user_id variable
		$user_id = '';

		// check if user exists
		$username_exists = username_exists( $username );
		$email_exists = email_exists( $email );

		if ( $username_exists || $email_exists ) {

			$user_id = esc_html__( 'The user already exists.', 'useraccess' );

		}

		// check non-empty values
		if ( empty( $username ) || empty( $email ) ) {

			$user_id = esc_html__( 'Required: username and email.', 'useraccess' );

		}

		// create the user
		if ( empty( $user_id ) ) {

			$user_id = wp_create_user( $username, $password, $email );

			if ( is_wp_error( $user_id ) ) {

				$user_id = $user_id->get_error_message();

			} else {

				// email password
				$subject = __( 'Welcome to WordPress!', 'useraccess' );
				$message = __( 'You can log in using your chosen username and this password: ', 'useraccess' ) . $password;

				wp_mail( $email, $subject, $message );

			}

		}

		$location = admin_url( 'admin.php?page=useraccess&result='. urlencode( $user_id ) );

		wp_redirect( $location );

		exit;

	}

}
add_action( 'admin_init', 'create_user_add_user' );



// create the admin notice
function create_user_admin_notices() {

	$screen = get_current_screen();

	if ( 'toplevel_page_useraccess' === $screen->id ) {

		if ( isset( $_GET['result'] ) ) {

			if ( is_numeric( $_GET['result'] ) ) : ?>

				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('User added successfully.', 'useraccess'); ?></strong></p>
				</div>

			<?php else : ?>

				<div class="notice notice-warning is-dismissible">
					<p><strong><?php echo esc_html( $_GET['result'] ); ?></strong></p>
				</div>

			<?php endif;

		}

	}

}
add_action( 'admin_notices', 'create_user_admin_notices' );


