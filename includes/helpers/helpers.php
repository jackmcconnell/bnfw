<?php
/**
 * General BNFW Helpers.
 *
 * @since 1.3.6
 * @package bnfw
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'bnfw_get_user_select_class' ) ) {
	/**
	 * Dynamically determine the class name for select2 user dropdown based on user count.
	 *
	 * @since 1.3.6
	 */
	function bnfw_get_user_select_class() {
		$user_count = count_users();

		if ( $user_count['total_users'] > 200 ) {
			return 'user-ajax-select2';
		} else {
			return 'user-select2';
		}
	}
}

if ( ! function_exists( 'bnfw_expanded_alowed_tags' ) ) {
	/**
	 * Get allowed html for wp_kses.
	 *
	 * @return array
	 */
	function bnfw_expanded_alowed_tags() {
		$allowed_html['select'] = array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
			'type'  => array(),
		);
		// select options.
		$allowed_html['option'] = array(
			'selected' => array(),
		);
		return $allowed_html;
	}
}

if ( ! function_exists( 'bnfw_render_users_dropdown' ) ) {
	/**
	 * Render users dropdown.
	 *
	 * @since 1.3.6
	 *
	 * @param array $selected_users Selected users.
	 */
	function bnfw_render_users_dropdown( $selected_users ) {
		global $wp_roles;
		$allowed_html = bnfw_expanded_alowed_tags();

		$non_wp_users = $selected_users;
		$user_count   = count_users();
		?>
		<optgroup label="<?php esc_attr_e( 'User Roles', 'bnfw' ); ?>">
			<?php
			$roles = $wp_roles->get_names();

			foreach ( $roles as $role_slug => $role_name ) {
				$selected = selected( true, in_array( 'role-' . $role_slug, $selected_users, true ), false );

				if ( ! empty( $selected ) ) {
					$non_wp_users = array_diff( $non_wp_users, array( 'role-' . $role_slug ) );
				}

				// Compatibility code, which will be eventually removed.
				$selected_old = selected( true, in_array( 'role-' . $role_name, $selected_users, true ), false );
				if ( ! empty( $selected_old ) ) {
					$selected = $selected_old;
				}

				$count = 0;
				if ( isset( $user_count['avail_roles'][ $role_slug ] ) ) {
					$count = $user_count['avail_roles'][ $role_slug ];
				}
				echo wp_kses( '<option value="role-', esc_attr( $role_slug ), '" ', $selected, '>', esc_html( $role_name ), ' (', $count, ' ' . __( 'Users', 'bnfw' ) . ')', '</option>', $allowed_html );
			}
			?>
		</optgroup>

		<optgroup label="<?php esc_attr_e( 'Users', 'bnfw' ); ?>">
		<?php
		$args = array(
			'order_by' => 'email',
			'fields'   => array( 'ID', 'user_login' ),
			'number'   => 200,
		);

		// if there are more than 200 users then use AJAX to load them dynamically.
		// So just get only the selected users.
		if ( $user_count['total_users'] > 200 ) {
			$selected_user_ids = array();
			foreach ( $selected_users as $selected_user ) {
				if ( absint( $selected_user ) > 0 ) {
					$selected_user_ids[] = $selected_user;
				}
			}

			if ( $selected_user_ids > 0 ) {
				$args['include'] = $selected_user_ids;
			}
		}

		$users = get_users( $args );

		foreach ( $users as $user ) {
			$selected = selected( true, in_array( $user->ID, $selected_users, true ), false );

			if ( ! empty( $selected ) ) {
				$non_wp_users = array_diff( $non_wp_users, array( $user->ID ) );
			}

			echo wp_kses( '<option value="', esc_attr( $user->ID ), '" ', $selected, '>', esc_html( $user->user_login ), '</option>', $allowed_html );
		}

		?>
		</optgroup>

		<?php if ( ! empty( $non_wp_users ) ) { ?>
			<optgroup label="<?php esc_attr_e( 'Non WordPress Users', 'bnfw' ); ?>">
				<?php
				foreach ( $non_wp_users as $non_wp_user ) {
					echo '<option value="', esc_attr( $non_wp_user ), '" selected >', esc_html( $non_wp_user ), '</option>';
				}
				?>
			</optgroup>
			<?php
		}
	}
}

if ( ! function_exists( 'bnfw_is_comment_notification' ) ) {
	/**
	 * Find whether the notification name is a comment notification.
	 *
	 * @param  string $notification_name Notification Name.
	 *
	 * @return bool                      True if it is a comment notification, False otherwise.
	 */
	function bnfw_is_comment_notification( $notification_name ) {
		$is_comment_notification = false;

		switch ( $notification_name ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
				$is_comment_notification = true;
				break;

			default:
				$type = explode( '-', $notification_name, 2 );
				if ( 'comment' === $type[0] || 'moderate' === $type[0] || 'approve' === $type[0] ) {
					$is_comment_notification = true;
				}
				break;
		}

		return $is_comment_notification;
	}
}

if ( ! function_exists( 'bnfw_format_user_capabilities' ) ) {
	/**
	 * Format user capabilities.
	 *
	 * @param array $wp_capabilities User capabilities.
	 *
	 * @return string Formatted capabilities.
	 */
	function bnfw_format_user_capabilities( $wp_capabilities ) {
		$capabilities = array();

		if ( is_array( $wp_capabilities ) ) {
			foreach ( $wp_capabilities as $capability => $enabled ) {
				if ( $enabled ) {
					$capabilities[] = $capability;
				}
			}
		}

		return implode( ', ', $capabilities );
	}
}

if ( ! function_exists( 'bnfw_is_tracking_allowed' ) ) {
	/**
	 * Has the user opted-in for tracking?
	 *
	 * @return bool True if tracking is allowed, False otherwise.
	 */
	function bnfw_is_tracking_allowed() {
		$tracking_allowed = false;

		if ( get_option( 'bnfw_allow_tracking' ) === 'on' ) {
			$tracking_allowed = true;
		}

		return $tracking_allowed;
	}
}

if ( ! function_exists( 'bnfw_get_post_id_from_comment' ) ) {
	/**
	 * Get post id from comment id.
	 *
	 * @param int $comment_id Comment ID for which we need Post ID.
	 * @return int Post ID. 0 if invalid comment id.
	 */
	function bnfw_get_post_id_from_comment( $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( null !== $comment ) {
			return $comment->comment_post_ID;
		}

		return 0;
	}
}

if ( ! function_exists( 'bnfw_format_date' ) ) {
	/**
	 * Format date based on date format stored in options.
	 *
	 * @param string $date Date.
	 *
	 * @return string Formatted date.
	 */
	function bnfw_format_date( $date ) {
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		return date( $date_format . ' ' . $time_format, strtotime( $date ) );
	}
}
