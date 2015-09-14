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
 * @param int    $user_id        User ID.
 * @param string $notify (optional) Optional. Whether admin and user should be notified ('both') or
 *                        only the admin ('admin' or empty).
 */
if ( ! function_exists( 'wp_new_user_notification' ) ) {
	function wp_new_user_notification( $user_id, $notify = '' ) {
		global $wp_version;

		$bnfw = BNFW::factory();
		$user = get_userdata( $user_id );

		if ( version_compare( $wp_version, '4.3', '>=' ) ) {
			// for WordPress 4.3 and above
			global $wpdb;

			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			if ( ! $bnfw->notifier->notification_exists( 'admin-user' ) ) {
				$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
				$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
				$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

				@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
			}

			if ( 'admin' === $notify || empty( $notify ) ) {
				return;
			}

			// Generate something random for a password reset key.
			$key = wp_generate_password( 20, false );

			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $user->user_login, $key );

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

			if ( $bnfw->notifier->notification_exists( 'new-user' ) ) {
				$notifications = $bnfw->notifier->get_notifications( 'new-user' );
				$password_url = network_site_url( "wp-login.php?action=rp&key=" . $key . "&login=" . rawurlencode( $user->user_login ), 'login' );
				foreach ( $notifications as $notification ) {
					$bnfw->engine->send_registration_email( $bnfw->notifier->read_settings( $notification->ID ), $user, $password_url );
				}
			} else {
				$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
				$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
				$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

				$message .= wp_login_url() . "\r\n";

				wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
			}
		} else {

			// for WordPress below 4.3
			$plaintext_pass = $notify;

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
}
?>
