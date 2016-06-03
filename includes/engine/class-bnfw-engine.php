<?php
/**
 * BNFW Engine
 *
 * @since 1.0
 */

class BNFW_Engine {

	/**
	 * Send test email.
	 *
	 * @since 1.2
	 */
	public function send_test_email( $setting ) {
		$subject = $setting['subject'];
		$message = '<p><strong>This is a test email. All shortcodes below will show in place but not be replaced with content.</strong></p>' . stripslashes( $setting['message'] );

		if ( 'true' != $setting['disable-autop'] && 'html' == $setting['email-formatting'] ) {
			$message = wpautop( $message );
		}

		$current_user = wp_get_current_user();
		$email = $current_user->user_email;

		$headers = array();
		if ( 'html' == $setting['email-formatting'] ) {
			$headers[] = 'Content-type: text/html';
		}

		wp_mail( $email, stripslashes( $subject ), $message, $headers );
	}

	/**
	 * Send the notification email.
	 *
	 * @since 1.0
	 * @param array $setting
	 * @param int $id
	 */
	public function send_notification( $setting, $id ) {
		/**
		 * BNFW - Whether notification is disabled?
		 *
		 * @since 1.3.6
		 */
		$notification_disabled = apply_filters( 'bnfw_notification_disabled', false, $id, $setting );

		if ( ! $notification_disabled ) {
			$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $id );
			$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $id );
			$emails  = $this->get_emails( $setting, $id );
			$headers = $this->get_headers( $emails );

			if ( 'true' != $setting['disable-autop'] && 'html' == $setting['email-formatting'] ) {
				$message = wpautop( $message );
			}

			if ( 'html' == $setting['email-formatting'] ) {
				$headers[] = 'Content-type: text/html';
			} else {
				$headers[] = 'Content-type: text/plain';
			}

			if ( isset( $emails['to'] ) && is_array( $emails['to'] ) ) {
				foreach ( $emails['to'] as $email ) {
					wp_mail( $email, stripslashes( $subject ), $message, $headers );
				}
			}
		}
	}

	/**
	 * Send new user registration notification email.
	 *
	 * @since 1.1
	 * @param array  $setting  Notification setting
	 * @param object $user     User object
	 * @param string $password_url Plain text password in WP < 4.3 and password url in WP > 4.3
	 */
	public function send_registration_email( $setting, $user, $password_url = '' ) {
		$user_id = $user->ID;

		$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $user_id );
		$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $user_id );

		$subject = str_replace( '[password]', $password_url, $subject );
		$message = str_replace( '[password]', $password_url, $message );

		$subject = str_replace( '[password_url]', $password_url, $subject );
		$message = str_replace( '[password_url]', $password_url, $message );

		$subject = str_replace( '[login_url]', wp_login_url() , $subject );
		$message = str_replace( '[login_url]', wp_login_url(), $message );

		if ( 'true' != $setting['disable-autop'] && 'html' == $setting['email-formatting'] ) {
			$message = wpautop( $message );
		}

		$headers = array();
		if ( 'html' == $setting['email-formatting'] ) {
			$headers[] = 'Content-type: text/html';
		}

		wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
	}

	/**
	 * Send comment reply notification email.
	 *
	 * @since 1.3
	 * @param array  $setting        Notification setting
	 * @param object $comment        Comment object
	 * @param object $parent_comment Parent comment object
	 */
	public function send_comment_reply_email( $setting, $comment, $parent_comment ) {
		$comment_id = $comment->comment_ID;

		$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $comment_id );
		$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $comment_id );

		$headers = array();
		if ( 'html' == $setting['email-formatting'] ) {
			$headers[] = 'Content-type: text/html';
		}

		if ( 'true' != $setting['disable-autop'] && 'html' == $setting['email-formatting'] ) {
			$message = wpautop( $message );
		}

		wp_mail( $parent_comment->comment_author_email, stripslashes( $subject ), $message, $headers );
	}

	/**
	 * Send user role changed email.
	 *
	 * @since 1.3.9
	 *
	 * @param array $setting Notification setting
	 * @param int   $user_id User ID
	 */
	public function send_user_role_changed_email( $setting, $user_id ) {
		$subject = $this->handle_shortcodes( $setting['subject'], $setting['notification'], $user_id );
		$message = $this->handle_shortcodes( $setting['message'], $setting['notification'], $user_id );

		$headers = array();
		if ( 'html' == $setting['email-formatting'] ) {
			$headers[] = 'Content-type: text/html';
		}

		if ( 'true' != $setting['disable-autop'] && 'html' == $setting['email-formatting'] ) {
			$message = wpautop( $message );
		}

		$user = get_user_by( 'id', $user_id );
		wp_mail( $user->user_email, stripslashes( $subject ), $message, $headers );
	}

	/**
	 * Handle shortcode for password reset email message.
	 *
	 * @since 1.1
	 */
	public function handle_password_reset_shortcodes( $setting, $key, $user_login, $user_data ) {
		if ( '' != $user_login ) {
			// For WordPress version 4.1.0 or less, we could have empty user_login
			$message = $this->user_shortcodes( $setting['message'], $user_data->ID );

			$reset_link = wp_login_url() . "?action=rp&key=$key&login=$user_login";
			$message = str_replace( '[password_reset_link]', $reset_link, $message );
		}
		return $message;
	}

	/**
	 * Generate message for notification.
	 *
	 * @since 1.0
	 * @param string $message
	 * @param string $notification
	 * @param int $id
	 * @return string
	 */
	private function handle_shortcodes( $message, $notification, $id ) {
		switch ( $notification ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
				// handle new comments, trackbacks and pingbacks
				$message = $this->comment_shortcodes( $message, $id );
				$comment = get_comment( $id );
				$message = $this->post_shortcodes( $message, $comment->comment_post_ID );
				if ( 0 != $comment->user_id ) {
					$message = $this->user_shortcodes( $message, $comment->user_id );
				}
				break;

			case 'admin-password':
			case 'user-password':
			case 'admin-user':
			case 'welcome-email':
			case 'new-user':
			case 'user-role':
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
					if ( 0 != $comment->user_id ) {
						$message = $this->user_shortcodes( $message, $comment->user_id );
					}
				}
				break;
		}

		$message = apply_filters( 'bnfw_shortcodes', $message, $notification, $id, $this );
		return $message;
	}

	/**
	 * Handle post shortcodes.
	 *
	 * @since 1.0
	 * @param string $message
	 * @param int $post_id
	 * @return string
	 */
	public function post_shortcodes(  $message, $post_id  ) {
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
		$message = str_replace( '[post_slug]', $post->post_name, $message );
		$message = str_replace( '[to_ping]', $post->to_ping, $message );
		$message = str_replace( '[pinged]', $post->pinged, $message );
		$message = str_replace( '[post_modified]', $post->post_modified, $message );
		$message = str_replace( '[post_modified_gmt]', $post->post_modified_gmt, $message );
		$message = str_replace( '[post_content_filtered]', $post->post_content_filtered, $message );
		$message = str_replace( '[post_parent]', $post->post_parent, $message );
		$message = str_replace( '[post_parent_permalink]', get_permalink( $post->post_parent ), $message );
		$message = str_replace( '[guid]', $post->guid, $message );
		$message = str_replace( '[menu_order]', $post->menu_order, $message );
		$message = str_replace( '[post_type]', $post->post_type, $message );
		$message = str_replace( '[post_mime_type]', $post->post_mime_type, $message );
		$message = str_replace( '[comment_count]', $post->comment_count, $message );
		$message = str_replace( '[permalink]', get_permalink( $post->ID ), $message );
		$message = str_replace( '[edit_post]', get_edit_post_link( $post->ID ), $message );

		if ( 'future' == $post->post_status ) {
			$message = str_replace( '[post_scheduled_date]', $post->post_date, $message );
			$message = str_replace( '[post_scheduled_date_gmt]', $post->post_date_gmt, $message );
		} else {
			$message = str_replace( '[post_scheduled_date]', 'Published', $message );
			$message = str_replace( '[post_scheduled_date_gmt]', 'Published', $message );
		}

		$category_list = implode( ', ', wp_get_post_categories( $post_id, array( 'fields' => 'names' ) ) );
		$message = str_replace( '[post_category]', $category_list, $message );

		$tag_list = implode( ', ', wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ) );
		$message = str_replace( '[post_tag]', $tag_list, $message );

		$user_info = get_userdata( $post->post_author );
		$message = str_replace( '[post_author]', $user_info->display_name, $message );

		$message = str_replace( '[author_link]', get_author_posts_url( $post->post_author ), $message );

		if ( $last_id = get_post_meta( $post->ID, '_edit_last', true ) ) {
			if ( $post->post_author != $last_id ) {
				$last_user_info = get_userdata( $last_id );
			} else {
				$last_user_info = $user_info;
			}

			$message = str_replace( '[post_update_author]', $last_user_info->display_name, $message );
		}

		$message = apply_filters( 'bnfw_shortcodes_post', $message, $post_id );
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

		// deperecated
		$message = str_replace( '[ID]', $user_info->ID, $message );

		$message = str_replace( '[user_id]', $user_info->ID, $message );
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

		$message = apply_filters( 'bnfw_shortcodes_user', $message, $user_id );
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
	 * @param array $setting Notification settings
	 * @param int $id
	 * @return array Emails
	 */
	private function get_emails( $setting, $id ) {
		global $current_user;

		$emails = array();

		$exclude = null;
		if ( 'true' == $setting['disable-current-user'] ) {
			if ( isset( $current_user->ID ) ) {
				$exclude = $current_user->ID;
			}
		}

		if ( 'true' === $setting['only-post-author'] ) {

			$post_id = $id;
			if ( bnfw_is_comment_notification( $setting['notification'] ) ) {
				$comment = get_comment( $id );
				$post_id = $comment->comment_post_ID;
			}

			$author = get_user_by( 'id', get_post_field( 'post_author', $post_id ) );
			if ( false !== $author ) {
				$emails['to'] = array( $author->user_email );
			}
		} else {
			$to_emails = array();

			if ( ! empty( $setting['users'] ) ) {
				$to_emails = $this->get_emails_from_users( $setting['users'], $exclude );
			}

			/**
			 * BNFW get to emails.
			 */
			$emails['to'] = apply_filters( 'bnfw_to_emails', $to_emails, $setting, $id );
		}

		if ( 'true' == $setting['show-fields'] ) {
			if ( ! empty( $setting['from-name'] ) && ! empty( $setting['from-email'] ) ) {
				$emails['from'] = $setting['from-name'] . ' <' . $setting['from-email'] . '>' ;
			} else {
				$emails['from'] = get_option( 'blogname' ) . ' <' . get_option( 'admin_email' ) . '>' ;
			}

			if ( ! empty( $setting['cc'] ) ) {
				$emails['cc'] = $this->get_emails_from_users( $setting['cc'], $exclude );
			}

			if ( ! empty( $setting['bcc'] ) ) {
				$emails['bcc'] = $this->get_emails_from_users( $setting['bcc'], $exclude );
			}
		}

		return $emails;
	}

	/**
	 * Get emails from users.
	 *
	 * @since 1.2
	 * @param array $users Users Array
	 * @param int $exclude User id to exclude
	 */
	public function get_emails_from_users( $users, $exclude = null ) {
		$email_list = array();
		$user_ids = array();
		$user_roles = array();

		if ( empty( $users ) ) {
			return array();
		}

		foreach ( $users as $user ) {
			if ( $this->starts_with( $user, 'role-' ) ) {
				$user_roles[] = str_replace( 'role-', '', $user );
			} else {
				$user_ids[] = absint( $user );
			}
		}

		if ( null != $exclude ) {
			$user_ids = array_diff( $user_ids, array( $exclude ) );
		}
		$emails_from_user_ids   = $this->get_emails_from_id( $user_ids );

		$emails_from_user_roles = $this->get_emails_from_role( $user_roles, $exclude );

		return array_merge( $emails_from_user_roles, $emails_from_user_ids );
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
		if ( is_array( $user_ids ) && count( $user_ids ) > 0 ) {
			$user_query = new WP_User_Query( array( 'include' => $user_ids ) );
			foreach ( $user_query->results as $user ) {
				$email_list[] = $user->user_email;
			}
		}
		return $email_list;
	}

	/**
	 * Get emails of users based on role.
	 *
	 * @since 1.0
	 * @param array $roles User Roles
	 * @param int $exclude User id to exclude
	 * @return array Email ids
	 */
	private function get_emails_from_role( $roles, $exclude = null ) {
		if ( ! is_array( $roles ) ) {
			$roles = array( $roles );
		}

		$email_list = array();
		foreach ( $roles as $role ) {
			$role_name = $this->get_role_name_by_label( $role );
			$users = get_users( array(
					'role' => $role_name,
					'fields' => array( 'user_email', 'ID' ),
				) );

			foreach ( $users as $user ) {
				if ( null != $exclude ) {
					if ( $user->ID == $exclude ) {
						continue;
					}
				}
				$email_list[] = $user->user_email;
			}
		}

		return $email_list;
	}

	/**
	 * Find if a string starts with another string.
	 *
	 * @since 1.2
	 */
	private function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return '' === $needle || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
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
	 * @param array $emails
	 * @return array
	 */
	private function get_headers( $emails ) {
		$headers = array();

		if ( ! empty( $emails['from'] ) ) {
			$headers[] = 'From:' . $emails['from'];
		}

		if ( ! empty( $emails['cc'] ) ) {
			$headers[] = 'Cc:' . implode( ',', $emails['cc'] );
		}
		if ( ! empty( $emails['bcc'] ) ) {
			$headers[] = 'Bcc:' . implode( ',', $emails['bcc'] );
		}

		return $headers;
	}
}
?>
