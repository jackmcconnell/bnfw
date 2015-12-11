<?php
/**
 * Handle post notifications.
 *
 * @since 1.3.6
 */

/**
 * Define the list of post notifications.
 *
 * @since 1.3.6
 *
 * @param  array  $notifications List of post notifications
 * @param  string $post_type     Post type
 * @return array                 Filtered list of post notifications
 */
function bnfw_post_notifications( $notifications, $post_type ) {
	$post_obj = get_post_type_object( $post_type );
	$label = $post_obj->labels->singular_name;

	$notifications[] = array(
		'type'  => 'new-' . $post_type,
		'label' => 'New ' . $label . ' Published',
	);

	$notifications[] = array(
		'type'  => 'update-' . $post_type,
		'label' => $label . ' Update',
	);

	$notifications[] = array(
		'type'  => 'pending-' . $post_type,
		'label' => $label . ' Pending',
	);

	$notifications[] = array(
		'type'  => 'future-' . $post_type,
		'label' => $label . ' Scheduled',
	);

	$notifications[] = array(
		'type'  => 'comment-' . $post_type,
		'label' => $label . ' New Comment',
	);

	return $notifications;
}
add_filter( 'bnfw_post_notifications', 'bnfw_post_notifications', 10, 2 );
