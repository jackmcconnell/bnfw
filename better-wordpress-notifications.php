<?php
/*
Plugin Name: Better Notifications for WordPress
Plugin URI: http://wordpress.org/extend/plugins/bnfw/
Description: Send customisable HTML emails to user roles for different WordPress notifications.
Version: 0.2.1 Beta
Author: Voltronik
Author URI: http://www.voltronik.co.uk/
Author Email: hello@voltronik.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

  Copyright 2013 Voltronik Web Design (hello@voltronik.co.uk)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

/*
hooks which we support
*/
$GLOBALS['BNFW_ACTION_TYPES'] =  array("create_term", "publish_post", "comment_post", "user_register", "trackback_post", "pingback_post", "lostpassword_post");

/*
pretty names for the hooks
*/
$GLOBALS['BNFW_ACTION_TYPES_PRETTY'] =  array("create_term" => "New Category",
											 "publish_post" => "Publish / Update Post", 
											 "comment_post" => "New Comment / Awaiting Moderation", 
											 "user_register" => "New User Registration", 
											 "trackback_post" => "New Trackback", 
											 "pingback_post" => "New Pingback", 
											 "lostpassword_post" => "Lost password reset");


// Load Engine
require_once ('includes/bnfw_engine.php');

// Load Settings page
if(is_admin()){
	require_once ('includes/admin-page.php');
}

//add_filter('the_content', 'bnfw_debug');

add_action('create_term', 'bnfw_term_created');
add_action('publish_post', 'bnfw_publish_post');
add_action('comment_post', 'bnfw_comment_post');
add_action('user_register', 'bnfw_user_register');
add_action('trackback_post', 'bnfw_trackback_post');
add_action('pingback_post', 'bnfw_pingback_post');
add_action('lostpassword_post', 'bnfw_lostpassword_post');

add_filter('wp_mail', 'bnfw_disable_emails');

register_activation_hook( __FILE__, 'bnfw_activate' );


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

function bnfw_activate(){

	//set default values
	$bnfw_options = get_option('bnfw_settings');
	if($bnfw_options === false){

		$bnfw_options = array('create_term-administrator' => '1',
			'publish_post-administrator' => '1',
			'comment_post-administrator' => '1',
			'user_register-administrator' => '1',
			'trackback_post-administrator' => '1',
			'pingback_post-administrator' => '1',
			'lostpassword_post-administrator' => '1'
		 );

		update_option('bnfw_settings', $bnfw_options);
	}

	$bnfw_options = get_option('bnfw_custom_email_settings');
	if($bnfw_options === false){

	$bnfw_options = array('payload-subject-publish_post' => 'A new post has been published!',
			'payload-subject-create_term' => 'New category created',
			'payload-subject-comment_post' => 'This is a comment notification email',
			'payload-subject-user_register' => 'A new user has registered!',
			'payload-subject-trackback_post' => 'You have a new trackback!',
			'payload-subject-pingback_post' => 'You have a new pingback!',
			'payload-subject-lostpassword_post' => '[user_login] has lost their password!',
			'payload-body-publish_post' => 'A post was published by <strong>[display_name]</strong> on <strong>[post_date]</strong>

<p>Here\'s an excerpt: <br /></p>
<em>[post_excerpt]</em>',
			'payload-body-create_term' => 'A new category has been created called: <strong>[name]</strong>',
			'payload-body-comment_post' => 'A new comment was posted by <strong>[comment_author_email]</strong>.
<p>It\'s status has been set to: <strong>[comment_approved]</strong>.</p>',
			'payload-body-user_register' => 'Say hello to <strong>[user_login]</strong>!',
			'payload-body-trackback_post' => 'From: <strong>[comment_author_url]</strong>',
			'payload-body-pingback_post' => 'From: <strong>[comment_author_url]</strong>',
			'payload-body-lostpassword_post' => '<strong>[user_login]</strong> has lost their password.

<p>An email has been sent to them so they can reset their password.</p>'
		 );

		update_option('bnfw_custom_email_settings', $bnfw_options);

	}

}


function bnfw_term_created($termID){
	
	$the_term = get_term_by('id', $termID, 'category');
	bnfw_launch_payload(bnfw_get_recipients_for_type('create_term'), bnfw_get_subject_for_term_created($the_term), bnfw_get_payload_for_term_created($the_term));

}

function bnfw_publish_post($postID){

	$the_post = get_post($postID);
	bnfw_launch_payload(bnfw_get_recipients_for_type('publish_post'), bnfw_get_subject_for_publish_post($the_post), bnfw_get_payload_for_publish_post($the_post));

}

function bnfw_comment_post($comment_id){

	$the_comment = get_comment($comment_id);

	if(!bnfw_check_for_spam($the_comment)){
		bnfw_launch_payload(bnfw_get_recipients_for_type('comment_post'), bnfw_get_subject_for_comment_post($the_comment), bnfw_get_payload_for_comment_post($the_comment));
	}

}

function bnfw_user_register($user_id){

	$the_user = get_user_by('id', $user_id);
	bnfw_launch_payload(bnfw_get_recipients_for_type('user_register'), bnfw_get_subject_for_user_register($the_user), bnfw_get_payload_for_user_register($the_user));

}

function bnfw_trackback_post($comment_id){

	$the_comment = get_comment($comment_id);
	if(!bnfw_check_for_spam($the_comment)){
		bnfw_launch_payload(bnfw_get_recipients_for_type('trackback_post'), bnfw_get_subject_for_trackback_post($the_comment), bnfw_get_payload_for_trackback_post($the_comment));
	}
}

function bnfw_pingback_post($comment_id){
	
	$the_comment = get_comment($comment_id);
	if(!bnfw_check_for_spam($the_comment)){
		bnfw_launch_payload(bnfw_get_recipients_for_type('pingback_post'), bnfw_get_subject_for_pingback_post($the_comment), bnfw_get_payload_for_pingback_post($the_comment));
	}
}

function bnfw_lostpassword_post(){
	$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
	bnfw_launch_payload(bnfw_get_recipients_for_type('lostpassword_post'), bnfw_get_subject_for_lostpassword_post($user_data), bnfw_get_payload_for_lostpassword_post($user_data));
}


function bnfw_debug($content){

	$bnfw_options = get_option('bnfw_settings');

	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();
	var_dump($wp_roles->get_names());

	var_dump($bnfw_options);
	return $content;
}