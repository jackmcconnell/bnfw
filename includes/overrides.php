<?php
/* ------------------------------------------------------------------------ *
 * Disable all default WordPress emails
 * ------------------------------------------------------------------------ */

function bnfw_disable_emails($result = '') {
	extract($result);
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	if (strstr(sprintf(__('[%s] New User Registration'), $blogname), $subject)) {
		$to = '';
		$subject = '';
		$message = '';
		$headers = '';
		$attachments = array ();
		return compact('to', 'subject', 'message', 'headers', 'attachments');
	}
	else if (strstr(sprintf(__('[%s] Password Lost/Changed'), $blogname), $subject)) {
		$to = '';
		$subject = '';
		$message = '';
		$headers = '';
		$attachments = array ();
		return compact('to', 'subject', 'message', 'headers', 'attachments');
	}
	else if (strstr(sprintf(__('[%s] Comment:'), $blogname), $subject)) {
		$to = '';
		$subject = '';
		$message = '';
		$headers = '';
		$attachments = array ();
		return compact('to', 'subject', 'message', 'headers', 'attachments');
	}
	else if (strstr(sprintf(__('[%s] Please moderate:'), $blogname), $subject)) {
		$to = '';
		$subject = '';
		$message = '';
		$headers = '';
		$attachments = array ();
		return compact('to', 'subject', 'message', 'headers', 'attachments');
	}

	return $result;
}

add_filter('wp_mail', 'bnfw_disable_emails');
?>
