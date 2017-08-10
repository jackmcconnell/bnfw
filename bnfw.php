<?php
/**
 * Plugin Name: Better Notifications for WordPress
 * Plugin URI: https://wordpress.org/plugins/bnfw/
 * Description: Supercharge your WordPress notifications using a WYSIWYG editor and shortcodes. Default and new notifications available. Add more power with Add-ons.
 * Version: 1.6.7
 * Author: Made with Fuel
 * Author URI: https://betternotificationsforwp.com/
 * Author Email: hello@betternotificationsforwp.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bnfw
 * Domain Path: /languages
 */

/**
 * Copyright © 2017 Made with Fuel Ltd. (hello@betternotificationsforwp.com)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once 'includes/freemius.php';

class BNFW {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	function __construct() {
		$this->load_textdomain();
		$this->includes();
		$this->hooks();

		/**
		 * BNFW Notification.
		 *
		 * @var \BNFW_Notification
		 */
		$this->notifier = new BNFW_Notification;

		/**
		 * BNFW Engine.
		 *
		 * @var \BNFW_Engine
		 */
		$this->engine   = new BNFW_Engine;
	}

	/**
	 * Factory method to return the instance of the class.
	 *
	 * Makes sure that only one instance is created.
	 *
	 * @return \BNFW Instance of the class.
	 */
	public static function factory() {
		static $instance = false;
		if ( ! $instance  ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Loads the plugin language files
	 *
	 * @since  1.0
	 */
	public function load_textdomain() {
		// Load localization domain
		$this->translations = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		load_plugin_textdomain( 'bnfw', false, $this->translations );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0
	 */
	public function includes() {

		// Load license related classes
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			require_once 'includes/libraries/EDD_SL_Plugin_Updater.php';
		}
		require_once 'includes/license/class-bnfw-license.php';
		require_once 'includes/license/class-bnfw-license-setting.php';

		// Load Engine
		require_once 'includes/engine/class-bnfw-engine.php';
		require_once 'includes/overrides.php';

		// Load notification post type and notification helpers
		require_once 'includes/admin/class-bnfw-notification.php';
		require_once 'includes/notification/post-notification.php';

		// Helpers
		require_once 'includes/helpers/helpers.php';
		require_once 'includes/helpers/ajax-helpers.php';

		// Load Admin Pages
		if ( is_admin() ) {
			require_once 'includes/admin/bnfw-settings.php';
		}
	}

	/**
	 * Register Hooks.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		global $wp_version;

		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_action( 'draft_to_private'          , array( $this, 'private_post' ) );
		add_action( 'future_to_private'         , array( $this, 'private_post' ) );
		add_action( 'pending_to_private'        , array( $this, 'private_post' ) );
		add_action( 'publish_to_private'        , array( $this, 'private_post' ) );

		add_action( 'wp_insert_post'            , array( $this, 'insert_post' ), 10, 2 );

		add_action( 'draft_to_publish'          , array( $this, 'publish_post' ) );
		add_action( 'future_to_publish'         , array( $this, 'publish_post' ) );
		add_action( 'pending_to_publish'        , array( $this, 'publish_post' ) );
		add_action( 'private_to_publish'        , array( $this, 'publish_post' ) );
		add_action( 'acf/submit_form'           , array( $this, 'acf_submit_form' ), 10, 2 );

		add_action( 'publish_to_publish'        , array( $this, 'update_post' ) );
		add_action( 'private_to_private'        , array( $this, 'update_post' ) );

		add_action( 'init'                      , array( $this, 'custom_post_type_hooks' ), 100 );
		add_action( 'create_term'               , array( $this, 'create_term' ), 10, 3 );

		add_action( 'comment_post'              , array( $this, 'comment_post' ) );
		add_action( 'trackback_post'            , array( $this, 'trackback_post' ) );
		add_action( 'pingback_post'             , array( $this, 'pingback_post' ) );

		add_action( 'user_register'             , array( $this, 'user_register' ) );
		add_action( 'user_register'             , array( $this, 'welcome_email' ) );
		add_action( 'set_user_role'             , array( $this, 'user_role_changed' ), 10, 3 );

		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			add_filter( 'retrieve_password_title', array( $this, 'change_password_email_title' ), 10, 3 );
		} else {
			add_filter( 'retrieve_password_title', array( $this, 'change_password_email_title' ) );
		}
		add_action( 'lostpassword_post'         , array( $this, 'on_lost_password' ) );
		add_filter( 'retrieve_password_message' , array( $this, 'change_password_email_message' ), 10, 4 );

		add_action( 'after_password_reset'      , array( $this, 'on_password_reset' ) );
		add_filter( 'password_change_email'     , array( $this, 'on_password_changed' ), 10, 2 );
		add_filter( 'email_change_email'        , array( $this, 'on_email_changed' ), 10, 2 );

		add_filter( 'auto_core_update_email'    , array( $this, 'on_core_updated' ), 10, 4 );

		add_filter( 'plugin_action_links'       , array( $this, 'plugin_action_links' ), 10, 4 );
		add_action( 'shutdown'                  , array( $this, 'on_shutdown' ) );
	}

	/**
	 * Setup hooks for custom post types.
	 *
	 * @since 1.2
	 */
	function custom_post_type_hooks() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );
		$post_types = array_diff( $post_types, array( BNFW_Notification::POST_TYPE ) );

		foreach ( $post_types as $post_type ) {
			add_action( 'pending_' . $post_type, array( $this, 'on_post_pending' ), 10, 2 );
			add_action( 'future_' . $post_type, array( $this, 'on_post_scheduled' ), 10, 2 );
		}
	}

	/**
	 * importer
	 */
	public function activate() {
		require_once dirname( __FILE__ ) . '/includes/import.php';
		$importer = new BNFW_Import;
		$importer->import();
	}

	/**
	 * Add 'Settings' link below BNFW in Plugins list.
	 *
	 * @since 1.0
	 * @param unknown $links
	 * @param unknown $file
	 * @return unknown
	 */
	public function plugin_action_links( $links, $file ) {
		$plugin_file = 'bnfw/bnfw.php';
		if ( $file == $plugin_file ) {
			$settings_link = '<a href="' . esc_url( admin_url( 'edit.php?post_type=bnfw_notification&page=bnfw-settings' ) ) . '">' . esc_html__( 'Settings', 'bnfw' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * When a new term is created.
	 *
	 * @since 1.0
	 * @param int $term_id
	 * @param int $tt_id
	 * @param string $taxonomy
	 */
	public function create_term( $term_id, $tt_id, $taxonomy ) {
		$this->send_notification( 'newterm-' . $taxonomy, $term_id );
	}

	/**
	 * Fires when a post is created for the first time.
	 *
	 * @since 1.3.1
	 * @param int $post_id Post ID
	 * @param object $post Post object
	 */
	public function insert_post( $post_id, $post ) {
		// Some themes like P2, directly insert posts into DB.
		$insert_post_themes = apply_filters( 'bnfw_insert_post_themes', array( 'P2', 'Syncope' ) );
		$current_theme = wp_get_theme();

		/**
		 * Whether to trigger insert post hook.
		 *
		 * @since 1.4
		 */
		$trigger_insert_post = apply_filters( 'bnfw_trigger_insert_post', false );

		if ( in_array( $current_theme->get( 'Name' ), $insert_post_themes ) || $trigger_insert_post ) {
			$this->handle_inserted_post( $post_id );
		}
	}

	/**
	 * Trigger New Post published notification for ACF forms.
	 *
	 * @param string $form ACF Form.
	 * @param int    $post_id Post ID.
	 */
	public function acf_submit_form( $form, $post_id ) {
		$this->handle_inserted_post( $post_id );
	}

	/**
	 * Trigger correct notifications for inserted posts.
	 *
	 * @since 1.6.7
	 *
	 * @param int $post_id Post id.
	 */
	private function handle_inserted_post( $post_id ) {
		$post = get_post( $post_id );

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		switch ( $post->post_status ) {
			case 'publish':
				$this->publish_post( $post );
				break;

			case 'private':
				$this->private_post( $post );
				break;

			case 'pending':
				$this->on_post_pending( $post_id, $post );
				break;

			case 'future':
				$this->on_post_scheduled( $post_id, $post );
				break;
		}
	}

	/**
	 * Fires when a post is created for the first time.
	 *
	 * @since 1.0
	 * @param object $post Post Object
	 */
	function publish_post( $post ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;

		if ( BNFW_Notification::POST_TYPE != $post_type ) {
			$this->send_notification_async( 'new-' . $post_type, $post_id );
		}
	}

	/**
	 * Fires when a private post is created.
	 *
	 * @since 1.6
	 * @param object $post Post Object
	 */
	public function private_post( $post ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;

		if ( BNFW_Notification::POST_TYPE != $post_type ) {
			$this->send_notification_async( 'private-' . $post_type, $post_id );
		}
	}

	/**
	 * Fires when a post is updated.
	 *
	 * @since 1.0
	 * @param unknown $post
	 */
	function update_post( $post ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;

		if ( BNFW_Notification::POST_TYPE != $post_type ) {
			$this->send_notification_async( 'update-' . $post_type, $post_id );
		}
	}

	/**
	 * Fires when a post is pending for review.
	 *
	 * @since 1.1
	 * @param int $post_id Post ID
	 * @param object $post Post object
	 */
	function on_post_pending( $post_id, $post ) {
		$post_type = $post->post_type;

		if ( BNFW_Notification::POST_TYPE != $post_type ) {
			$this->send_notification_async( 'pending-' . $post_type, $post_id );
		}
	}

	/**
	 * Fires when a post is scheduled.
	 *
	 * @since 1.1.5
	 * @param int $post_id Post ID
	 * @param object $post Post object
	 */
	function on_post_scheduled( $post_id, $post ) {
		$post_type = $post->post_type;

		if ( BNFW_Notification::POST_TYPE != $post_type ) {
			$this->send_notification_async( 'future-' . $post_type, $post_id );
		}
	}

	/**
	 * Send notification for new comments
	 *
	 * @since 1.0
	 * @param int $comment_id
	 */
	function comment_post( $comment_id ) {
		$the_comment = get_comment( $comment_id );
		if ( $this->can_send_comment_notification( $the_comment ) ) {
			$post = get_post( $the_comment->comment_post_ID );
			$notification_type = 'new-comment'; // old notification name
			if ( 'post' != $post->post_type ) {
				$notification_type = 'comment-' . $post->post_type;
			}
			$this->send_notification( $notification_type, $comment_id );

			// comment reply notification.
			if ( $the_comment->comment_parent > 0 ) {
				$notification_type = 'reply-comment'; // old notification name
				if ( 'post' != $post->post_type ) {
					$notification_type = 'commentreply-' . $post->post_type;
				}
				$notifications = $this->notifier->get_notifications( $notification_type );
				if ( count( $notifications ) > 0 ) {
					$parent = get_comment( $the_comment->comment_parent );
					if ( $parent->comment_author_email != $the_comment->comment_author_email ) {
						foreach ( $notifications as $notification ) {
							$this->engine->send_comment_reply_email( $this->notifier->read_settings( $notification->ID ), $the_comment, $parent );
						}
					}
				}
			}
		}
	}

	/**
	 * Send notification for new trackback
	 *
	 * @since 1.0
	 * @param unknown $comment_id
	 */
	function trackback_post( $comment_id ) {
		$the_comment = get_comment( $comment_id );
		if ( $this->can_send_comment_notification( $the_comment ) ) {
			$this->send_notification( 'new-trackback', $comment_id );
		}
	}

	/**
	 * Send notification for new pingbacks
	 *
	 * @since 1.0
	 * @param unknown $comment_id
	 */
	function pingback_post( $comment_id ) {
		$the_comment = get_comment( $comment_id );
		if ( $this->can_send_comment_notification( $the_comment ) ) {
			$this->send_notification( 'new-pingback', $comment_id );
		}
	}

	/**
	 * Send notification for lost password.
	 *
	 * @since 1.0
	 */
	function on_lost_password() {
		$user_login = sanitize_text_field( $_POST['user_login'] );
		$user = get_user_by( 'login', $user_login );
		if ( $user ) {
			$this->send_notification( 'admin-password', $user->ID );
		}
	}

	/**
	 * Change the title of the password reset email that is sent to the user.
	 *
	 * @since 1.1
	 *
	 * @param string $title
	 * @param string $user_login
	 * @param string $user_data
	 *
	 * @return string
	 */
	public function change_password_email_title( $title, $user_login = '', $user_data = '' ) {
		$notifications = $this->notifier->get_notifications( 'user-password' );
		if ( count( $notifications ) > 0 ) {
			// Ideally there should be only one notification for this type.
			// If there are multiple notification then we will read data about only the last one
			$setting = $this->notifier->read_settings( end( $notifications )->ID );

			if ( '' === $user_data ) {
				return $this->engine->handle_shortcodes( $setting['subject'], 'user-password', $user_data->ID );
			} else {
				return $setting['subject'];
			}
		}

		return $title;
	}

	/**
	 * Change the message of the password reset email.
	 *
	 * @since 1.1
	 *
	 * @param string $message
	 * @param string $key
	 * @param string $user_login
	 * @param string $user_data
	 *
	 * @return string
	 */
	public function change_password_email_message( $message, $key, $user_login = '', $user_data = '' ) {
		$notifications = $this->notifier->get_notifications( 'user-password' );
		if ( count( $notifications ) > 0 ) {
			// Ideally there should be only one notification for this type.
			// If there are multiple notification then we will read data about only the last one
			$setting = $this->notifier->read_settings( end( $notifications )->ID );

			$message = $this->engine->handle_password_reset_shortcodes( $setting, $key, $user_login, $user_data );

			if ( 'html' == $setting['email-formatting'] ) {
				add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
				$message = wpautop( $message );
			} else {
				add_filter( 'wp_mail_content_type', array( $this, 'set_text_content_type' ) );
			}
		} else {
			if ( $this->notifier->notification_exists( 'user-password', false ) ) {
				// disabled notification exists, so disable the email by returning empty string.
				return '';
			}
		}

		return $message;
	}

	/**
	 * On Password reset.
	 *
	 * @param WP_User $user User who's password was changed.
	 */
	public function on_password_reset( $user ) {
		$notifications = $this->notifier->get_notifications( 'password-changed' );
		foreach ( $notifications as $notification ) {
			$this->engine->send_password_changed_email( $this->notifier->read_settings( $notification->ID ), $user );
		}
	}

	/**
	 * On Password Changed.
	 *
	 * @since 1.6
	 *
	 * @param array $email_data Email Data.
	 * @param array $user       User data.
	 *
	 * @return array Modified Email Data
	 */
	public function on_password_changed( $email_data, $user ) {
		return $this->handle_filtered_data_notification( 'password-changed', $email_data, $user['ID'] );
	}

	/**
	 * On Email Changed.
	 *
	 * @since 1.6
	 *
	 * @param array $email_data Email Data.
	 * @param array $user       User data.
	 *
	 * @return array Modified Email Data
	 */
	public function on_email_changed( $email_data, $user ) {
		return $this->handle_filtered_data_notification( 'email-changed', $email_data, $user['ID'] );
	}

	/**
	 * Send notification on core updated event.
	 *
	 * @since 1.6
	 *
	 * @param array  $email_data  Email Data.
	 * @param string $type        The type of email being sent. Can be one of
	 *                            'success', 'fail', 'manual', 'critical'.
	 * @param object $core_update The update offer that was attempted.
	 * @param mixed  $result      The result for the core update. Can be WP_Error.
	 *
	 * @return array Modified Email Data.
	 */
	public function on_core_updated( $email_data, $type, $core_update, $result ) {
		$notifications = $this->notifier->get_notifications( 'core-updated' );
		if ( count( $notifications ) > 0 ) {
			// Ideally there should be only one notification for this type.
			// If there are multiple notification then we will read data about only the last one
			$setting = $this->notifier->read_settings( end( $notifications )->ID );

			$email_data = $this->engine->handle_core_updated_notification( $email_data, $setting, $type );
		}

		return $email_data;
	}

	/**
	 * Process User update notifications.
	 *
	 * @since 1.6
	 *
	 * @param string     $notification_name Notification Name.
	 * @param array      $email_data        Email Data.
	 * @param string|int $extra_data        User Id.
	 *
	 * @return array Modified Email Data.
	 */
	private function handle_filtered_data_notification( $notification_name, $email_data, $extra_data ) {
		$notifications = $this->notifier->get_notifications( $notification_name );
		if ( count( $notifications ) > 0 ) {
			// Ideally there should be only one notification for this type.
			// If there are multiple notification then we will read data about only the last one
			$setting = $this->notifier->read_settings( end( $notifications )->ID );

			$email_data = $this->engine->handle_filtered_data_notification( $email_data, $setting, $extra_data );
		}

		return $email_data;
	}

	/**
	 * Set the email formatting to HTML.
	 *
	 * @since 1.4
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Set the email formatting to text.
	 *
	 * @since 1.4
	 */
	public function set_text_content_type() {
		return 'text/plain';
	}

	/**
	 * Send notification for new users.
	 *
	 * @since 1.0
	 * @param int $user_id
	 */
	function user_register( $user_id ) {
		$this->send_notification( 'admin-user', $user_id );
	}

	/**
	 * New User - Post-registration Email
	 *
	 * @since 1.1
	 * @param int $user_id New user id
	 */
	public function welcome_email( $user_id ) {
		$notifications = $this->notifier->get_notifications( 'welcome-email' );
		foreach ( $notifications as $notification ) {
			$this->engine->send_registration_email( $this->notifier->read_settings( $notification->ID ), get_userdata( $user_id ) );
		}
	}

	/**
	 * Send notification when a user role changes.
	 *
	 * @since 1.3.9
	 *
	 * @param int    $user_id   User ID
	 * @param string $new_role  New User role
	 * @param array  $old_roles Old User role
	 */
	public function user_role_changed( $user_id, $new_role, $old_roles ) {
		if ( ! empty( $old_roles ) ) {
			$notifications = $this->notifier->get_notifications( 'user-role' );
			foreach ( $notifications as $notification ) {

				/**
				 * Trigger User Role Changed - For User notification.
				 *
				 * @since 1.6.5
				 */
				if ( apply_filters( 'bnfw_trigger_user-role_notification', true, $notification, $new_role, $old_roles ) ) {
					$this->engine->send_user_role_changed_email(
						$this->notifier->read_settings( $notification->ID ),
						$user_id,
						$old_roles[0],
						$new_role
					);
				}
			}

			$notifications = $this->notifier->get_notifications( 'admin-role' );
			foreach ( $notifications as $notification ) {

				/**
				 * Trigger User Role Changed - For User notification.
				 *
				 * @since 1.6.5
				 */
				if ( apply_filters( 'bnfw_trigger_admin-role_notification', true, $notification, $new_role, $old_roles ) ) {
					$setting            = $this->notifier->read_settings( $notification->ID );
					$setting['message'] = $this->engine->handle_user_role_shortcodes( $setting['message'], $old_roles[0], $new_role );
					$setting['subject'] = $this->engine->handle_user_role_shortcodes( $setting['subject'], $old_roles[0], $new_role );

					$this->engine->send_notification( $setting, $user_id );
				}
			}
		}
	}

	/**
	 * Send notification based on type and ref id
	 *
	 * @access private
	 * @since 1.0
	 * @param string $type Notification type.
	 * @param int $ref_id Reference id.
	 */
	private function send_notification( $type, $ref_id ) {
		$notifications = $this->notifier->get_notifications( $type );
		foreach ( $notifications as $notification ) {
			$this->engine->send_notification( $this->notifier->read_settings( $notification->ID ), $ref_id );
		}
	}

	/**
	 * Send notification async based on type and ref id.
	 *
	 * @access private
	 * @param  string  $type   Notification type.
	 * @param  int     $ref_id Reference id.
	 */
	private function send_notification_async( $type, $ref_id ) {
		$notifications = $this->notifier->get_notifications( $type, false );
		foreach ( $notifications as $notification ) {
			$transient = get_transient( 'bnfw-async-notifications' );
			if ( ! is_array( $transient ) ) {
				$transient = array();
			}

			$transient[] = array( 'ref_id' => $ref_id, 'notification_id' => $notification->ID, 'notification_type' => $type );
			set_transient( 'bnfw-async-notifications', $transient, 600 );
		}
	}

	/**
	 * Can send comment notification or not
	 *
	 * @since 1.0
	 * @param unknown $comment
	 * @return unknown
	 */
	private function can_send_comment_notification( $comment ) {
		// Returns false if the comment is marked as spam AND admin has enabled suppression of spam
		$suppress_spam = get_option( 'bnfw_suppress_spam' );
		if ( '1' === $suppress_spam && ( 0 === strcmp( $comment->comment_approved, 'spam' ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Send notification emails on shutdown.
	 */
	public function on_shutdown() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$transient = get_transient( 'bnfw-async-notifications' );
		if ( is_array( $transient ) ) {
			delete_transient( 'bnfw-async-notifications' );
			foreach ( $transient as $id_pairs ) {
				$this->engine->send_notification( $this->notifier->read_settings( $id_pairs['notification_id'] ), $id_pairs['ref_id'] );
			}
		}
	}
}

/* ------------------------------------------------------------------------ *
 * Fire up the plugin
 * ------------------------------------------------------------------------ */
BNFW::factory();
