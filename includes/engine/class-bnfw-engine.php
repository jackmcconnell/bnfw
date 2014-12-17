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
     * Generate message for notification.
     *
     * @since 1.0
     */
    private function handle_shortcodes( $message, $notification, $id ) {
        // handle new comments, trackbacks and pingbacks
        if ( 'new-comment' == $notification || 'new-trackback' == $notification || 'new-pingback' == $notification ) {
            $message = $this->comment_shortcodes( $message, $id );
            $comment = get_comment( $id );
            $message = $this->post_shortcodes( $message, $comment->comment_post_ID );
        }

        // handle users (lost password and new user registration)
        if ( 'user-password' == $notification || 'new-user' == $notification ) {
            $message = $this->user_shortcodes( $message, $id );
        }

        // handle new category
        if ( 'new-category' == $notification || 'new-term' == $notification ) {
            $message = $this->category_shortcodes( $message, $id );
        }

        // handle new and update posts
        $post_types = get_post_types( array( '_builtin' => false ), 'names' );
        $post_types = array_diff( $post_types, array( BNFW_Notification::POST_TYPE ) );
        array_push( $post_types, 'post' );

        $type = explode( '-', $notification, 2 );
        if ( $type[0] == 'new' || $type[0] == 'update' ) {
            if ( in_array( $type[1], $post_types ) ) {
                $message = $this->post_shortcodes( $message, $id );
                $post = get_post( $id );
                $message = $this->user_shortcodes( $message, $post->post_author );
            }
        }

        return $message;
    }

    /**
     * Handle post shortcodes.
     *
     * @since 1.0
     */
    private function post_shortcodes(  $message, $post_id  ) {
        $post = get_post(  $post_id  );

        $message = str_replace( '[ID]', $post->ID, $message );
        $message = str_replace( '[post_author]', $post->post_author, $message );
        $message = str_replace( '[post_date]', $post->post_date, $message );
        $message = str_replace( '[post_date_gmt]', $post->post_date_gmt, $message );
        $message = str_replace( '[post_content]', $post->post_content, $message );
        $message = str_replace( '[post_title]', $post->post_title, $message );
        $message = str_replace( '[post_excerpt]', $post->post_excerpt, $message );
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

        $category_list = implode( ',', wp_get_post_categories( $post_id, array( 'fields' => 'name') ) );
        $message = str_replace( '[post_category]', $category_list, $message );

        return $message;
    }

    /**
     * Handle comment shortcodes.
     *
     * @since 1.0
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
        $message = str_replace( '[comment_content]', $comment->comment_content, $message );
        $message = str_replace( '[comment_karma]', $comment->comment_karma, $message );
        $message = str_replace( '[comment_approved]', str_replace( array( '0', '1', 'spam' ), array( 'awaiting moderation', 'approved', 'spam' ), $comment->comment_approved ), $message );
        $message = str_replace( '[comment_agent]', $comment->comment_agent, $message );
        $message = str_replace( '[comment_type]', $comment->comment_type, $message );
        $message = str_replace( '[comment_parent]', $comment->comment_parent, $message );
        $message = str_replace( '[user_id]', $comment->user_id, $message );

        return $message;
    }

    /**
     * Handle user shortcodes.
     *
     * @access private
     * @since 1.0
     */
    private function user_shortcodes( $message, $user_id ) {
        $user_info = get_userdata( $user_id );

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
        $message = str_replace( '[wp_capabilities]', implode( ',', $user_info->wp_capabilities ), $message );
        $message = str_replace( '[admin_color]', $user_info->admin_color, $message );
        $message = str_replace( '[closedpostboxes_page]', $user_info->closedpostboxes_page, $message );
        $message = str_replace( '[primary_blog]', $user_info->primary_blog, $message );
        $message = str_replace( '[rich_editing]', $user_info->rich_editing, $message );
        $message = str_replace( '[source_domain]', $user_info->source_domain, $message );

        return $message;
    }

    /**
     * Handle category shortcodes.
     *
     * @access private
     * @since 1.0
     */
    private function category_shortcodes( $message, $cat_id ) {
        $cat_info = get_category( $cat_id );

        $message = str_replace( '[slug]', $cat_info->slug, $message );
        $message = str_replace( '[name]', $cat_info->name, $message );
        $message = str_replace( '[description]', $cat_info->description, $message );
        return $message;
    }

    /**
     * Get the list of emails from the notification settings.
     *
     * @since 1.0
     */
    private function get_emails( $setting ) {
        $emails = array();
        if ( ! empty( $setting['from-name'] ) && ! empty( $setting['from-email'] ) ) {
            $emails['from'] = $setting['from-name'] . ' <' . $setting['from-email'] . '>' ;
        } else {
            $emails['from'] = get_option('blogname') . ' <' . get_option('admin_email') . '>' ;
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
     * @param mixed $user_ids
     * @since 1.0
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
     */
    private function get_emails_from_role( $roles ) {
        if ( ! is_array( $roles ) ) {
            $roles = array( $roles );
        }

        $email_list = array();
        foreach ( $roles as $role ) {
            $users = get_users( array(
                'role' => $role,
                'fields' => array( 'user_email' ),
            ));

            foreach( $users as $user ) {
                $email_list[] = $user->user_email;
            }
        }

        return $email_list;
    }

    /**
     * Generate email headers based on the emails.
     *
     * @since 1.0
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
