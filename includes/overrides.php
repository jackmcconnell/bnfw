<?php
/**
 * Override default WordPress emails
 *
 */

/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @param int     $user_id        User ID.
 * @param string  $plaintext_pass (optional) Optional. The user's plaintext password. Default empty.
 */
if ( ! function_exists( 'wp_new_user_notification' ) ) {
function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
	$bnfw = BNFW::factory();
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	if ( ! $bnfw->notifier->notification_exists( 'admin-user' ) ) {
		$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "\r\n";

		@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
	}

	if ( empty( $plaintext_pass ) ) {
		return;
	}

	if ( $bnfw->notifier->notification_exists( 'new-user' ) ) {
		$notifications = $bnfw->notifier->get_notifications( 'new-user' );
		foreach ( $notifications as $notification ) {
			$bnfw->engine->send_registration_email( $bnfw->notifier->read_settings( $notification->ID ), $user, $plaintext_pass );
		}
	} else {
		$message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n";
		$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail( $user->user_email, sprintf( __( '[%s] Your username and password' ), $blogname ), $message );
	}
}
}
?>
