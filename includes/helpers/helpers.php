<?php
/**
 * General BNFW Helpers.
 *
 * @since 1.3.6
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * Dynamically determine the class name for select2 user dropdown based on user count.
 *
 * @since 1.3.6
 */
function bnfw_get_user_select_class() {
	$user_count = count_users();

	if ( $user_count['total_users'] > 100 ) {
		return 'user-select2';
	} else {
		return 'select2';
	}
}

/**
 * Render users dropdown.
 *
 * @since 1.3.6
 */
function bnfw_render_users_dropdown( $selected_users ) {
	global $wp_roles;
?>
		<optgroup label="User Roles">
	<?php
	$roles = $wp_roles->get_names();

	foreach ( $roles as $role ) {
		$selected = selected( true, in_array( 'role-' . $role, $selected_users ), false );
		echo '<option value="role-', $role, '" ', $selected, '>', $role, '</option>';
	}
?>
		</optgroup>
		<optgroup label="Users">
	<?php
	$user_count = count_users();

	// if there are more than 100 users then use AJAX to load them dynamically.
	// So just get only the selected users
	if ( count( $selected_users ) > 0 && $user_count['total_users'] > 100 ) {
		$users = get_users( array(
			'include'  => $selected_users,
			'order_by' => 'email',
			'fields'   => array( 'ID', 'user_login' ),
		) );
	} else {
		$users = get_users( array(
			'order_by' => 'email',
			'number' => 100,
			'fields' => array( 'ID', 'user_login' ),
		) );
	}

	foreach ( $users as $user ) {
		$selected = selected( true, in_array( $user->ID, $selected_users ), false );
		echo '<option value="', $user->ID, '" ', $selected, '>', $user->user_login, '</option>';
	}
}

/**
 * Find whether the notification name is a comment notification.
 *
 * @param  string $notification_name Notification Name.
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
			if ( 'comment' == $type[0] ) {
				$is_comment_notification = true;
			}
			break;
	}

	return $is_comment_notification;
}
