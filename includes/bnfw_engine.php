<?php

/*
Logic to retrieve to whom the notif should be sent
*/
function bnfw_get_recipients_for_type($notification_type){

	$bnfw_options = get_option('bnfw_settings');
	
	$recipients = '';

	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

		foreach ($wp_roles->roles as $field => $role) {

			if($bnfw_options[$notification_type.'-'.$field] === "1"){
				
				$users = get_users('role='.$field);

				foreach ($users as $the_email) {
					$recipients .= $the_email->user_email . ',';
				}

			}
		}

	return $recipients;
}

function bnfw_launch_payload($recipients, $subject, $payload){

	$headers[] = 'content-type: text/html';
	$headers[] = 'Bcc: '.$recipients;
	$headers[] = 'From: '.get_option('blogname').' <'.get_option('admin_email').'>';
	wp_mail("", $subject, $payload, $headers);

}

/*
*	Subject getters -----------------------------------------------
*/

function bnfw_get_subject_for_publish_post($the_post){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$user_info = get_userdata($the_post->post_author);

	$message_body = $bnfw_options['payload-subject-publish_post'];

	$message_body = str_replace("[ID]", $the_post->ID, $message_body);
	$message_body = str_replace("[post_author]", $the_post->post_author, $message_body);
	$message_body = str_replace("[post_date]", $the_post->post_date, $message_body);
	$message_body = str_replace("[post_date_gmt]", $the_post->post_date_gmt, $message_body);
	$message_body = str_replace("[post_content]", $the_post->post_content, $message_body);
	$message_body = str_replace("[post_title]", $the_post->post_title, $message_body);

	$category_list = "";

	foreach ($the_post->post_category as $category_int){

		$category_list .= get_term_by('id', $category_int, 'category') . ", ";

	}

	$message_body = str_replace("[post_category]", $category_list, $message_body);
	$message_body = str_replace("[post_excerpt]", implode(", ", $the_post->post_excerpt), $message_body);
	$message_body = str_replace("[post_status]", $the_post->post_status, $message_body);
	$message_body = str_replace("[comment_status]", $the_post->comment_status, $message_body);
	$message_body = str_replace("[ping_status]", $the_post->ping_status, $message_body);
	$message_body = str_replace("[post_password]", $the_post->post_password, $message_body);
	$message_body = str_replace("[post_name]", $the_post->post_name, $message_body);
	$message_body = str_replace("[to_ping]", $the_post->to_ping, $message_body);
	$message_body = str_replace("[pinged]", implode(", ", $the_post->pinged), $message_body);
	$message_body = str_replace("[post_modified]", $the_post->post_modified, $message_body);
	$message_body = str_replace("[post_modified_gmt]", $the_post->post_modified_gmt, $message_body);
	$message_body = str_replace("[post_content_filtered]", $the_post->post_content_filtered, $message_body);
	$message_body = str_replace("[post_parent]", $the_post->post_parent, $message_body);
	$message_body = str_replace("[guid]", $the_post->guid, $message_body);
	$message_body = str_replace("[menu_order]", $the_post->menu_order, $message_body);
	$message_body = str_replace("[post_type]", $the_post->post_type, $message_body);
	$message_body = str_replace("[post_mime_type]", $the_post->post_mime_type, $message_body);
	$message_body = str_replace("[comment_count]", $the_post->comment_count, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;

}

function bnfw_get_subject_for_term_created($the_term){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-create_term'];

	$message_body = str_replace("[slug]", $the_term->slug, $message_body);
	$message_body = str_replace("[name]", $the_term->name, $message_body);
	$message_body = str_replace("[description]", $the_term->description, $message_body);

	return $message_body;
}


function bnfw_get_subject_for_comment_post($the_comment){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-comment_post'];
	$user_info = get_userdata($the_comment->comment_author);
	$the_post = get_post($the_comment->comment_post_ID);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	$message_body = str_replace("[post_author]", $the_post->post_author, $message_body);
	$message_body = str_replace("[post_date]", $the_post->post_date, $message_body);
	$message_body = str_replace("[post_date_gmt]", $the_post->post_date_gmt, $message_body);
	$message_body = str_replace("[post_content]", $the_post->post_content, $message_body);
	$message_body = str_replace("[post_title]", $the_post->post_title, $message_body);
	$category_list = "";

	foreach ($the_post->post_category as $category_int){

		$category_list .= get_term_by('id', $category_int, 'category') . ", ";

	}

	$message_body = str_replace("[post_category]", $category_list, $message_body);
	$message_body = str_replace("[post_excerpt]", implode(", ", $the_post->post_excerpt), $message_body);
	$message_body = str_replace("[post_status]", $the_post->post_status, $message_body);
	$message_body = str_replace("[comment_status]", $the_post->comment_status, $message_body);
	$message_body = str_replace("[ping_status]", $the_post->ping_status, $message_body);
	$message_body = str_replace("[post_password]", $the_post->post_password, $message_body);
	$message_body = str_replace("[post_name]", $the_post->post_name, $message_body);
	$message_body = str_replace("[to_ping]", $the_post->to_ping, $message_body);
	$message_body = str_replace("[pinged]", implode(", ", $the_post->pinged), $message_body);
	$message_body = str_replace("[post_modified]", $the_post->post_modified, $message_body);
	$message_body = str_replace("[post_modified_gmt]", $the_post->post_modified_gmt, $message_body);
	$message_body = str_replace("[post_content_filtered]", $the_post->post_content_filtered, $message_body);
	$message_body = str_replace("[post_parent]", $the_post->post_parent, $message_body);
	$message_body = str_replace("[guid]", $the_post->guid, $message_body);
	$message_body = str_replace("[menu_order]", $the_post->menu_order, $message_body);
	$message_body = str_replace("[post_type]", $the_post->post_type, $message_body);
	$message_body = str_replace("[post_mime_type]", $the_post->post_mime_type, $message_body);
	$message_body = str_replace("[comment_count]", $the_post->comment_count, $message_body);

	return $message_body;
}

function bnfw_get_subject_for_user_register($user_info){
	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-user_register'];

	$message_body = str_replace("[ID]", $user_info->ID, $message_body);
	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}

function bnfw_get_subject_for_trackback_post($the_comment){
	
	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-trackback_post'];
	$user_info = get_userdata($the_comment->comment_author);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}

function bnfw_get_subject_for_pingback_post($the_comment){
	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-pingback_post'];
	$user_info = get_userdata($the_comment->comment_author);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}

function bnfw_get_subject_for_lostpassword_post($user_info){
	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-subject-lostpassword_post'];

	$message_body = str_replace("[ID]", $user_info->ID, $message_body);
	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}


/*
*	Payload getters -----------------------------------------------
*/

function bnfw_get_payload_for_publish_post($the_post){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$user_info = get_userdata($the_post->post_author);

	$message_body = $bnfw_options['payload-body-publish_post'];

	$message_body = str_replace("[ID]", $the_post->ID, $message_body);
	$message_body = str_replace("[post_author]", $the_post->post_author, $message_body);
	$message_body = str_replace("[post_date]", $the_post->post_date, $message_body);
	$message_body = str_replace("[post_date_gmt]", $the_post->post_date_gmt, $message_body);
	$message_body = str_replace("[post_content]", $the_post->post_content, $message_body);
	$message_body = str_replace("[post_title]", $the_post->post_title, $message_body);
	$category_list = "";

	foreach ($the_post->post_category as $category_int){

		$category_list .= get_term_by('id', $category_int, 'category') . ", ";

	}

	$message_body = str_replace("[post_category]", $category_list, $message_body);
	$message_body = str_replace("[post_excerpt]", implode(", ", $the_post->post_excerpt), $message_body);
	$message_body = str_replace("[post_status]", $the_post->post_status, $message_body);
	$message_body = str_replace("[comment_status]", $the_post->comment_status, $message_body);
	$message_body = str_replace("[ping_status]", $the_post->ping_status, $message_body);
	$message_body = str_replace("[post_password]", $the_post->post_password, $message_body);
	$message_body = str_replace("[post_name]", $the_post->post_name, $message_body);
	$message_body = str_replace("[to_ping]", $the_post->to_ping, $message_body);
	$message_body = str_replace("[pinged]", implode(", ", $the_post->pinged), $message_body);
	$message_body = str_replace("[post_modified]", $the_post->post_modified, $message_body);
	$message_body = str_replace("[post_modified_gmt]", $the_post->post_modified_gmt, $message_body);
	$message_body = str_replace("[post_content_filtered]", $the_post->post_content_filtered, $message_body);
	$message_body = str_replace("[post_parent]", $the_post->post_parent, $message_body);
	$message_body = str_replace("[guid]", $the_post->guid, $message_body);
	$message_body = str_replace("[menu_order]", $the_post->menu_order, $message_body);
	$message_body = str_replace("[post_type]", $the_post->post_type, $message_body);
	$message_body = str_replace("[post_mime_type]", $the_post->post_mime_type, $message_body);
	$message_body = str_replace("[comment_count]", $the_post->comment_count, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}			

function bnfw_get_payload_for_term_created($the_term){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-create_term'];

	$message_body = str_replace("[slug]", $the_term->slug, $message_body);
	$message_body = str_replace("[name]", $the_term->name, $message_body);
	$message_body = str_replace("[description]", $the_term->description, $message_body);

	return $message_body;
}

function bnfw_get_payload_for_comment_post($the_comment){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-comment_post'];
	$user_info = get_userdata($the_comment->comment_author);
	$the_post = get_post($the_comment->comment_post_ID);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	$message_body = str_replace("[post_author]", $the_post->post_author, $message_body);
	$message_body = str_replace("[post_date]", $the_post->post_date, $message_body);
	$message_body = str_replace("[post_date_gmt]", $the_post->post_date_gmt, $message_body);
	$message_body = str_replace("[post_content]", $the_post->post_content, $message_body);
	$message_body = str_replace("[post_title]", $the_post->post_title, $message_body);
	$category_list = "";

	foreach ($the_post->post_category as $category_int){

		$category_list .= get_term_by('id', $category_int, 'category') . ", ";

	}

	$message_body = str_replace("[post_category]", $category_list, $message_body);
	$message_body = str_replace("[post_excerpt]", implode(", ", $the_post->post_excerpt), $message_body);
	$message_body = str_replace("[post_status]", $the_post->post_status, $message_body);
	$message_body = str_replace("[comment_status]", $the_post->comment_status, $message_body);
	$message_body = str_replace("[ping_status]", $the_post->ping_status, $message_body);
	$message_body = str_replace("[post_password]", $the_post->post_password, $message_body);
	$message_body = str_replace("[post_name]", $the_post->post_name, $message_body);
	$message_body = str_replace("[to_ping]", $the_post->to_ping, $message_body);
	$message_body = str_replace("[pinged]", implode(", ", $the_post->pinged), $message_body);
	$message_body = str_replace("[post_modified]", $the_post->post_modified, $message_body);
	$message_body = str_replace("[post_modified_gmt]", $the_post->post_modified_gmt, $message_body);
	$message_body = str_replace("[post_content_filtered]", $the_post->post_content_filtered, $message_body);
	$message_body = str_replace("[post_parent]", $the_post->post_parent, $message_body);
	$message_body = str_replace("[guid]", $the_post->guid, $message_body);
	$message_body = str_replace("[menu_order]", $the_post->menu_order, $message_body);
	$message_body = str_replace("[post_type]", $the_post->post_type, $message_body);
	$message_body = str_replace("[post_mime_type]", $the_post->post_mime_type, $message_body);
	$message_body = str_replace("[comment_count]", $the_post->comment_count, $message_body);

	return $message_body;
}

function bnfw_get_payload_for_user_register($user_info){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-user_register'];

	$message_body = str_replace("[ID]", $user_info->ID, $message_body);
	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;

}

function bnfw_get_payload_for_trackback_post($the_comment){$message_body = $bnfw_options['payload-body-comment_post'];

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-trackback_post'];
	$user_info = get_userdata($the_comment->comment_author);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}

function bnfw_get_payload_for_pingback_post($the_comment){

	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-pingback_post'];
	$user_info = get_userdata($the_comment->comment_author);

	$message_body = str_replace("[comment_ID]", $the_comment->comment_ID, $message_body);
	$message_body = str_replace("[comment_post_ID]", $the_comment->comment_post_ID, $message_body);
	$message_body = str_replace("[comment_author]", $the_comment->comment_author, $message_body);
	$message_body = str_replace("[comment_author_email]", $the_comment->comment_author_email, $message_body);
	$message_body = str_replace("[comment_author_url]", $the_comment->comment_author_url, $message_body);
	$message_body = str_replace("[comment_author_IP]", $the_comment->comment_author_IP, $message_body);
	$message_body = str_replace("[comment_date]", $the_comment->comment_date, $message_body);
	$message_body = str_replace("[comment_date_gmt]", $the_comment->comment_date_gmt, $message_body);
	$message_body = str_replace("[comment_content]", $the_comment->comment_content, $message_body);
	$message_body = str_replace("[comment_karma]", $the_comment->comment_karma, $message_body);
	$message_body = str_replace("[comment_approved]", str_replace(array("0", "1", "spam"), array("awaiting moderation", "approved", "spam"), $the_comment->comment_approved), $message_body);
	$message_body = str_replace("[comment_agent]", $the_comment->comment_agent, $message_body);
	$message_body = str_replace("[comment_type]", $the_comment->comment_type, $message_body);
	$message_body = str_replace("[comment_parent]", $the_comment->comment_parent, $message_body);
	$message_body = str_replace("[user_id]", $the_comment->user_id, $message_body);

	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;
}

function bnfw_get_payload_for_lostpassword_post($user_info){
	
	$bnfw_options = get_option('bnfw_custom_email_settings');
	$message_body = $bnfw_options['payload-body-lostpassword_post'];

	$message_body = str_replace("[ID]", $user_info->ID, $message_body);
	$message_body = str_replace("[user_login]", $user_info->user_login, $message_body);
	$message_body = str_replace("[user_nicename]", $user_info->user_nicename, $message_body);
	$message_body = str_replace("[user_email]", $user_info->user_email, $message_body);
	$message_body = str_replace("[user_url]", $user_info->user_url, $message_body);
	$message_body = str_replace("[user_registered]", $user_info->user_registered, $message_body);
	$message_body = str_replace("[display_name]", $user_info->display_name, $message_body);
	$message_body = str_replace("[user_firstname]", $user_info->user_firstname, $message_body);
	$message_body = str_replace("[user_lastname]", $user_info->user_lastname, $message_body);
	$message_body = str_replace("[nickname]", $user_info->nickname, $message_body);
	$message_body = str_replace("[user_description]", $user_info->user_description, $message_body);
	$message_body = str_replace("[wp_capabilities]", implode(", ", $user_info->wp_capabilities), $message_body);
	$message_body = str_replace("[admin_color]", $user_info->admin_color, $message_body);
	$message_body = str_replace("[closedpostboxes_page]", $user_info->closedpostboxes_page, $message_body);
	$message_body = str_replace("[primary_blog]", implode(", ", $user_info->primary_blog), $message_body);
	$message_body = str_replace("[rich_editing]", $user_info->rich_editing, $message_body);
	$message_body = str_replace("[source_domain]", $user_info->source_domain, $message_body);

	return $message_body;

}

?>