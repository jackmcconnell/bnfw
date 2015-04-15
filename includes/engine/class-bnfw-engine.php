<?php
/**
 * BNFW Engine
 *
 * @since 1.0
 */

class BNFW_Engine {

	/**
	 * Send the notification email.
	 *
	 * @since 1.0
	 * @param unknown $setting
	 * @param unknown $id
	 */
	public function send_notification( $setting, $id ) {
		$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $id );
		$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $id );
		$emails  = $this->get_emails( $setting );
		$headers = $this->get_headers( $emails );
		$headers[] = 'Content-type: text/html';

		foreach ( $emails['to'] as $email ) {
			wp_mail( $email, $subject, $message, $headers );
		}
	}

	/**
	 * Send new user registration notification email.
	 *
	 * @since 1.1
	 * @param array  $setting  Notification setting
	 * @param object $user     User object
	 * @param string $password Plain text password
	 */
	public function send_registration_email( $setting, $user, $password = '' ) {
		$user_id = $user->ID;

		$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $user_id );
		$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $user_id );

		$subject = str_replace( '[password]', $password, $subject );
		$message = str_replace( '[password]', $password, $message );

		$subject = str_replace( '[login_url]', wp_login_url() , $subject );
		$message = str_replace( '[login_url]', wp_login_url(), $message );

		$headers = array( 'Content-type: text/html' );

		wp_mail( $user->user_email, $subject, $message, $headers );
	}

	/**
	 * Handle shortcode for password reset email message.
	 *
	 * @since 1.1
	 */
	public function handle_password_reset_shortcodes( $setting, $key, $user_login, $user_data ) {
		$message = $this->user_shortcodes( $setting['message'], $user_data->ID );

		$reset_link = wp_login_url() . "?action=rp&key=$key&login=$user_login";
		$message = str_replace( '[password_reset_link]', $reset_link, $message );

		return $message;
	}

	/**
	 * Generate message for notification.
	 *
	 * @since 1.0
	 * @param unknown $message
	 * @param unknown $notification
	 * @param unknown $id
	 * @return unknown
	 */
	private function handle_shortcodes( $message, $notification, $id ) {
		switch ( $notification ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
				// handle new comments, trackbacks and pingbacks
				$message = $this->comment_shortcodes( $message, $id );
				$comment = get_comment( $id );
				$message = $this->post_shortcodes( $message, $comment->comment_post_ID );
				break;

			case 'admin-password':
			case 'user-password':
			case 'admin-user':
			case 'welcome-email':
				// handle users (lost password and new user registration)
				$message = $this->user_shortcodes( $message, $id );
				break;

			case 'new-category':
				// handle new category
				$message = $this->taxonomy_shortcodes( $message, 'category', $id );
				break;

			case 'new-post_tag':
				// handle new tag
				$message = $this->taxonomy_shortcodes( $message, 'post_tag', $id );
				break;

			default:
				$type = explode( '-', $notification, 2 );
				if ( 'newterm' == $type[0] ) {
					// handle new terms
					$message = $this->taxonomy_shortcodes( $message, $type[1], $id );

				} else if ( 'new' == $type[0] || 'update' == $type[0] || 'pending' == $type[0] || 'future' == $type[0] ) {
					// handle new, update and pending posts
					$post_types = get_post_types( array( 'public' => true ), 'names' );
					$post_types = array_diff( $post_types, array( BNFW_Notification::POST_TYPE ) );

					if ( in_array( $type[1], $post_types ) ) {
						$message = $this->post_shortcodes( $message, $id );
						$post = get_post( $id );
						$message = $this->user_shortcodes( $message, $post->post_author );
					}
				} else if ( 'comment' == $type[0] ) {
					$message = $this->comment_shortcodes( $message, $id );
					$comment = get_comment( $id );
					$message = $this->post_shortcodes( $message, $comment->comment_post_ID );
				}
				break;
		}

		return $message;
	}

	/**
	 * Handle post shortcodes.
	 *
	 * @since 1.0
	 * @param unknown $message
	 * @param unknown $post_id
	 * @return unknown
	 */
	private function post_shortcodes(  $message, $post_id  ) {
		$post = get_post( $post_id );

		$post_content = apply_filters( 'the_content', $post->post_content );
		$post_content = str_replace( ']]>', ']]&gt;', $post_content );

		$message = str_replace( '[ID]', $post->ID, $message );
		$message = str_replace( '[post_date]', $post->post_date, $message );
		$message = str_replace( '[post_date_gmt]', $post->post_date_gmt, $message );
		$message = str_replace( '[post_content]', $post_content, $message );
		$message = str_replace( '[post_title]', $post->post_title, $message );
		$message = str_replace( '[post_excerpt]', ( $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post_content ) ), $message );
		$message = str_replace( '[post_status]', $post->post_status, $message );
		$message = str_replace( '[comment_status]', $post->comment_status, $message );
		$message = str_replace( '[ping_status]', $post->ping_status, $message );
		$message = str_replace( '[post_password]', $post->post_password, $message );
		$message = str_replace( '[post_name]', $post->post_name, $message );
		$message = str_replace( '[to_ping]', $post->to_ping, $message );
		$message = str_replace( '[pinged]', $post->pinged, $message );
		$message = str_replace( '[post_modified]', $post->post_modified, $message );
		$message = str_replace( '[post_modified_gmt]', $post->post_modified_gmt, $message );
		$message = str_replace( '[post_content_filtered]', $post->post_content_filtered, $message );
		$message = str_replace( '[post_parent]', $post->post_parent, $message );
		$message = str_replace( '[guid]', $post->guid, $message );
		$message = str_replace( '[menu_order]', $post->menu_order, $message );
		$message = str_replace( '[post_type]', $post->post_type, $message );
		$message = str_replace( '[post_mime_type]', $post->post_mime_type, $message );
		$message = str_replace( '[comment_count]', $post->comment_count, $message );
		$message = str_replace( '[permalink]', get_permalink( $post->ID ), $message );

		if ( 'future' == $post->post_status ) {
			$message = str_replace( '[post_scheduled_date]', $post->post_date, $message );
			$message = str_replace( '[post_scheduled_date_gmt]', $post->post_date_gmt, $message );
		} else {
			$message = str_replace( '[post_scheduled_date]', 'Published', $message );
			$message = str_replace( '[post_scheduled_date_gmt]', 'Published', $message );
		}

		$category_list = implode( ',', wp_get_post_categories( $post_id, array( 'fields' => 'names' ) ) );
		$message = str_replace( '[post_category]', $category_list, $message );

		$tag_list = implode( ',', wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ) );
		$message = str_replace( '[post_tag]', $tag_list, $message );

		$user_info = get_userdata( $post->post_author );
		$message = str_replace( '[post_author]', $user_info->display_name, $message );

		return $message;
	}

	/**
	 * Handle comment shortcodes.
	 *
	 * @since 1.0
	 * @param unknown $message
	 * @param unknown $comment_id
	 * @return unknown
	 */
	private function comment_shortcodes( $message, $comment_id ) {
		$comment = get_comment( $comment_id );

		$message = str_replace( '[comment_ID]', $comment->comment_ID, $message );
		$message = str_replace( '[comment_post_ID]', $comment->comment_post_ID, $message );
		$message = str_replace( '[comment_author]', $comment->comment_author, $message );
		$message = str_replace( '[comment_author_email]', $comment->comment_author_email, $message );
		$message = str_replace( '[comment_author_url]', $comment->comment_author_url, $message );
		$message = str_replace( '[comment_author_IP]', $comment->comment_author_IP, $message );
		$message = str_replace( '[comment_date]', $comment->comment_date, $message );
		$message = str_replace( '[comment_date_gmt]', $comment->comment_date_gmt, $message );
		$message = str_replace( '[comment_content]', get_comment_text( $comment->comment_ID ), $message );
		$message = str_replace( '[comment_karma]', $comment->comment_karma, $message );
		$message = str_replace( '[comment_approved]', str_replace( array( '0', '1', 'spam' ), array( 'awaiting moderation', 'approved', 'spam' ), $comment->comment_approved ), $message );
		$message = str_replace( '[comment_agent]', $comment->comment_agent, $message );
		$message = str_replace( '[comment_type]', $comment->comment_type, $message );
		$message = str_replace( '[comment_parent]', $comment->comment_parent, $message );
		$message = str_replace( '[user_id]', $comment->user_id, $message );
		$message = str_replace( '[permalink]', get_comment_link( $comment->comment_ID ), $message );

		return $message;
	}

	/**
	 * Handle user shortcodes.
	 *
	 * @access private
	 * @since 1.0
	 * @param unknown $message
	 * @param unknown $user_id
	 * @return unknown
	 */
	private function user_shortcodes( $message, $user_id ) {
		$user_info = get_userdata( $user_id );

		$message = str_replace( '[ID]', $user_info->ID, $message );
		$message = str_replace( '[user_login]', $user_info->user_login, $message );
		$message = str_replace( '[user_nicename]', $user_info->user_nicename, $message );
		$message = str_replace( '[user_email]', $user_info->user_email, $message );
		$message = str_replace( '[user_url]', $user_info->user_url, $message );
		$message = str_replace( '[user_registered]', $user_info->user_registered, $message );
		$message = str_replace( '[display_name]', $user_info->display_name, $message );
		$message = str_replace( '[user_firstname]', $user_info->user_firstname, $message );
		$message = str_replace( '[user_lastname]', $user_info->user_lastname, $message );
		$message = str_replace( '[nickname]', $user_info->nickname, $message );
		$message = str_replace( '[user_description]', $user_info->user_description, $message );
		if ( is_array( $user_info->wp_capabilities ) ) {
			$message = str_replace( '[wp_capabilities]', implode( ',', $user_info->wp_capabilities ), $message );
		}

		return $message;
	}

	/**
	 * Handle taxonomy shortcodes.
	 *
	 * @access private
	 * @since 1.1
	 * @param string $message
	 * @param string $taxonomy
	 * @param int $term_id
	 * @return string
	 */
	private function taxonomy_shortcodes( $message, $taxonomy, $term_id ) {
		$term_info = get_term( $term_id, $taxonomy );

		$message = str_replace( '[slug]', $term_info->slug, $message );
		$message = str_replace( '[name]', $term_info->name, $message );
		$message = str_replace( '[description]', $term_info->description, $message );

		return $message;
	}

	/**
	 * Get the list of emails from the notification settings.
	 *
	 * @since 1.0
	 * @param unknown $setting
	 * @return unknown
	 */
	private function get_emails( $setting ) {
		$emails = array();
		if ( ! empty( $setting['from-name'] ) && ! empty( $setting['from-email'] ) ) {
			$emails['from'] = $setting['from-name'] . ' <' . $setting['from-email'] . '>' ;
		} else {
			$emails['from'] = get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>' ;
		}

		if ( ! empty( $setting['users'] ) ) {
			$emails['to'] = $this->get_emails_from_id( $setting['users'] );
		} else {
			$emails['to'] = $this->get_emails_from_role( $setting['user-roles'] );
		}

		$emails['cc'] = $this->get_emails_from_role( $setting['cc-roles'] );
		if ( ! empty( $setting['cc-email'] ) ) {
			$emails['cc'][] = $setting['cc-email'];
		}

		$emails['bcc'] = $this->get_emails_from_role( $setting['bcc-roles'] );
		if ( ! empty( $setting['bcc-email'] ) ) {
			$emails['bcc'][] = $setting['bcc-email'];
		}

		return $emails;
	}

	/**
	 * Get user emails by user ids.
	 *
	 * @since 1.0
	 * @param mixed   $user_ids
	 * @return unknown
	 */
	private function get_emails_from_id( $user_ids ) {
		$email_list = array();
		$user_query = new WP_User_Query( array( 'include' => $user_ids ) );
		foreach ( $user_query->results as $user ) {
			$email_list[] = $user->user_email;
		}
		return $email_list;
	}

	/**
	 * Get emails of users based on role.
	 *
	 * @since 1.0
	 * @param unknown $roles
	 * @return unknown
	 */
	private function get_emails_from_role( $roles ) {
		if ( ! is_array( $roles ) ) {
			$roles = array( $roles );
		}

		$email_list = array();
		foreach ( $roles as $role ) {
			$role_name = $this->get_role_name_by_label( $role );
			$users = get_users( array(
					'role' => $role_name,
					'fields' => array( 'user_email' ),
				) );

			foreach ( $users as $user ) {
				$email_list[] = $user->user_email;
			}
		}

		return $email_list;
	}

	/**
	 * Get User role name by label.
	 *
	 * @param mixed   $role_label
	 * @return unknown
	 */
	protected function get_role_name_by_label( $role_label ) {
		global $wp_roles;
		foreach ( $wp_roles->roles as $role_name => $role_info ) {
			if ( $role_label == $role_info['name'] ) {
				return $role_name;
			}
		}

		// There is something wrong
		return '';
	}

	/**
	 * Generate email headers based on the emails.
	 *
	 * @since 1.0
	 * @param unknown $emails
	 * @return unknown
	 */
	private function get_headers( $emails ) {
		$headers = array();
		return $headers;

		$headers[] = 'From:' . $emails['from'];
		if ( ! empty( $emails['cc'] ) ) {
			$headers[] = 'Cc:' . implode( ',', $emails['cc'] );
		}
		if ( ! empty( $emails['bcc'] ) ) {
			$headers[] = 'Bcc:' . implode( ',', $emails['bcc'] );
		}
	}
}
?>
