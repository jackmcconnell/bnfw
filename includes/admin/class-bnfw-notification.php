<?php
/**
 * BNFW Notification.
 *
 * @since 1.0
 */

class BNFW_Notification {

	const POST_TYPE       = 'bnfw_notification';
	const META_KEY_PREFIX = 'bnfw_';
	const TEST_MAIL_ARG   = 'test-mail';

	/**
	 *
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_action( 'edit_form_top', array( $this, 'admin_notices' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Custom row actions.
		add_filter( 'post_row_actions', array( $this, 'custom_row_actions' ), 10, 2 );

		// Custom columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'custom_column_row' ), 10, 2 );

		// Enqueue scripts/styles and disables autosave for this post type.
		add_action( 'admin_enqueue_scripts', array( $this, 'is_assets_needed' ) );
	}

	/**
	 * Register bnfw_notification custom post type.
	 *
	 * @since 1.0
	 */
	public function register_post_type() {
		register_post_type( self::POST_TYPE, array(
				'labels' => array(
					'name'               => __( 'Notifications', 'bnfw' ),
					'singular_name'      => __( 'Notification', 'bnfw' ),
					'add_new'            => __( 'Add New', 'bnfw' ),
					'menu_name'          => __( 'Notifications', 'bnfw' ),
					'name_admin_bar'     => __( 'Notifications', 'bnfw' ),
					'add_new_item'       => __( 'Add New Notification', 'bnfw' ),
					'edit_item'          => __( 'Edit Notification', 'bnfw' ),
					'new_item'           => __( 'New Notification', 'bnfw' ),
					'view_item'          => __( 'View Notification', 'bnfw' ),
					'search_items'       => __( 'Search Notifications', 'bnfw' ),
					'not_found'          => __( 'No Notifications found', 'bnfw' ),
					'not_found_in_trash' => __( 'No Notifications found in trash', 'bnfw' ),
					'all_items'          => __( 'All Notifications', 'bnfw' )
				),
				'public'            => false,
				'show_in_nav_menus' => true,
				'show_in_admin_bar' => true,
				'has_archive'       => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'menu_icon'         => 'dashicons-email-alt',
				'menu_position'     => 100,
				'rewrite'           => false,
				'map_meta_cap'      => false,
				'capabilities'      => array(

					// meta caps (don't assign these to roles)
					'edit_post'              => 'manage_options',
					'read_post'              => 'manage_options',
					'delete_post'            => 'manage_options',

					// primitive/meta caps
					'create_posts'           => 'manage_options',

					// primitive caps used outside of map_meta_cap()
					'edit_posts'             => 'manage_options',
					'edit_others_posts'      => 'manage_options',
					'publish_posts'          => 'manage_options',
					'read_private_posts'     => 'manage_options',

					// primitive caps used inside of map_meta_cap()
					'read'                   => 'manage_options',
					'delete_posts'           => 'manage_options',
					'delete_private_posts'   => 'manage_options',
					'delete_published_posts' => 'manage_options',
					'delete_others_posts'    => 'manage_options',
					'edit_private_posts'     => 'manage_options',
					'edit_published_posts'   => 'manage_options',
				),

				// What features the post type supports.
				'supports' => array(
					'title',
				),
			) );
	}

	/**
	 * Remove unwanted meta boxes.
	 *
	 * @since 1.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', self::POST_TYPE, 'side' );
		remove_meta_box( 'slugdiv', self::POST_TYPE, 'normal' );
	}

	/**
	 * Add meta box to the post editor screen.
	 *
	 * @since 1.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'bnfw-post-notification',                     // Unique ID
			esc_html__( 'Notification Settings', 'bnfw' ), // Title
			array( $this, 'render_settings_meta_box' ),   // Callback function
			self::POST_TYPE,                              // Admin page (or post type)
			'normal'                                      // Context
		);

		add_meta_box(
			'bnfw_submitdiv',
			__( 'Save Notification', 'bnfw' ),
			array( $this, 'render_submitdiv' ),
			self::POST_TYPE,
			'side',
			'core'
		);
	}

	/**
	 * Render the settings meta box.
	 *
	 * @since 1.0
	 * @param unknown $post
	 */
	public function render_settings_meta_box( $post ) {
		wp_nonce_field(
			// Action
			self::POST_TYPE,
			// Name.
			self::POST_TYPE . '_nonce'
		);

		$setting = $this->read_settings( $post->ID );
?>
    <table class="form-table">
    <tbody>
        <tr valign="top">
            <th scope="row">
                <label for="notification"><?php _e( 'Notification For', 'bnfw' ); ?></label>
            </th>
            <td>
                <select name="notification" id="notification" class="select2" data-placeholder="Select the notification type" style="width:75%">
                    <optgroup label="WordPress Defaults">
                    <option value="new-comment" <?php selected( 'new-comment', $setting['notification'] );?>><?php _e( 'New Comment / Awaiting Moderation', 'bnfw' ); ?></option>
                    <option value="new-trackback" <?php selected( 'new-trackback', $setting['notification'] );?>><?php _e( 'New Trackback', 'bnfw' );?></option>
                    <option value="new-pingback" <?php selected( 'new-pingback', $setting['notification'] );?>><?php _e( 'New Pingback', 'bnfw' );?></option>
                    <option value="admin-password" <?php selected( 'admin-password', $setting['notification'] );?>><?php _e( 'Lost Password - For Admin', 'bnfw' );?></option>
                    <option value="admin-user" <?php selected( 'admin-user', $setting['notification'] );?>><?php _e( 'New User Registration - For Admin', 'bnfw' );?></option>
                    </optgroup>
                    <optgroup label="Transactional">
                    <option value="user-password" <?php selected( 'user-password', $setting['notification'] );?>><?php _e( 'Lost Password - For User', 'bnfw' );?></option>
                    <option value="new-user" <?php selected( 'new-user', $setting['notification'] );?>><?php _e( 'New User Registration - For User', 'bnfw' );?></option>
                    <option value="welcome-email" <?php selected( 'welcome-email', $setting['notification'] );?>><?php _e( 'New User - Post-registration Email', 'bnfw' );?></option>
                    <option value="user-role" <?php selected( 'user-role', $setting['notification'] );?>><?php _e( 'User Role Changed', 'bnfw' );?></option>
                    <option value="reply-comment" <?php selected( 'reply-comment', $setting['notification'] );?>><?php _e( 'Comment Reply', 'bnfw' );?></option>
                    </optgroup>
                    <optgroup label="Posts">
                    <option value="new-post" <?php selected( 'new-post', $setting['notification'] );?>><?php _e( 'New Post Published', 'bnfw' );?></option>
                    <option value="update-post" <?php selected( 'update-post', $setting['notification'] );?>><?php _e( 'Post Updated', 'bnfw' );?></option>
					<option value="pending-post" <?php selected( 'pending-post', $setting['notification'] );?>><?php _e( 'Post Pending Review', 'bnfw' );?></option>
					<option value="future-post" <?php selected( 'future-post', $setting['notification'] );?>><?php _e( 'Post Scheduled', 'bnfw' );?></option>
					<option value="newterm-category" <?php selected( 'newterm-category', $setting['notification'] );?>><?php _e( 'New Category', 'bnfw' ); ?></option>
					<option value="newterm-post_tag" <?php selected( 'newterm-post_tag', $setting['notification'] );?>><?php _e( 'New Tag', 'bnfw' ); ?></option>
					<?php do_action( 'bnfw_after_notification_options', 'post', 'Post', $setting ); ?>
                    </optgroup>
					<optgroup label="Page">
					<option value="new-page" <?php selected( 'new-page', $setting['notification'] );?>><?php _e( 'New Page Published', 'bnfw' );?></option>
					<option value="update-page" <?php selected( 'update-page', $setting['notification'] );?>><?php _e( 'Page Updated', 'bnfw' );?></option>
					<option value="pending-page" <?php selected( 'pending-page', $setting['notification'] );?>><?php _e( 'Page Pending Review', 'bnfw' );?></option>
					<option value="future-page" <?php selected( 'future-page', $setting['notification'] );?>><?php _e( 'Page Scheduled', 'bnfw' );?></option>
					<option value="comment-page" <?php selected( 'comment-page', $setting['notification'] );?>><?php _e( 'Page - New Comment', 'bnfw' );?></option>
					<?php do_action( 'bnfw_after_notification_options', 'page', 'Page', $setting ); ?>
					</optgroup>
<?php
		$types = get_post_types( array(
				'public'   => true,
				'_builtin' => false,
			), 'names'
		);

		foreach ( $types as $type ) {
			if ( $type != self::POST_TYPE ) {
				$post_obj = get_post_type_object( $type );
				$label = $post_obj->labels->singular_name;
?>
                    <optgroup label="<?php printf( "%s - '%s'", __( 'Custom Post Type', 'bnfw' ), $label ); ?>">
                        <option value="new-<?php echo $type; ?>" <?php selected( 'new-' . $type, $setting['notification'] );?>><?php echo __( 'New ', 'bnfw' ), "'$label'"; ?></option>
                        <option value="update-<?php echo $type; ?>" <?php selected( 'update-' . $type, $setting['notification'] );?>><?php echo "'$label' " . __( 'Update ', 'bnfw' ); ?></option>
                        <option value="pending-<?php echo $type; ?>" <?php selected( 'pending-' . $type, $setting['notification'] );?>><?php echo "'$label' ", __( 'Pending Review', 'bnfw' ); ?></option>
                        <option value="future-<?php echo $type; ?>" <?php selected( 'future-' . $type, $setting['notification'] );?>><?php echo "'$label' ", __( 'Scheduled', 'bnfw' ); ?></option>
                        <option value="comment-<?php echo $type; ?>" <?php selected( 'comment-' . $type, $setting['notification'] );?>><?php echo "'$label' ", __( 'New Comment', 'bnfw' ); ?></option>
						<?php do_action( 'bnfw_after_notification_options', $type, $label, $setting ); ?>
                    </optgroup>
<?php
			}
		}

		$taxs = get_taxonomies( array(
				'public'   => true,
				'_builtin' => false,
			), 'objects'
		);

		if ( count( $taxs ) > 0 ) {
?>
                    <optgroup label="<?php _e( 'Custom Taxonomy', 'bnfw' );?>">
<?php
			foreach ( $taxs as $tax ) {
				$tax_name = 'newterm-' . $tax->name;
?>
						<option value="<?php echo $tax_name; ?>" <?php selected( $tax_name, $setting['notification'] );?>><?php printf( "%s '%s'", __( 'New', 'bnfw' ), $tax->labels->name ); ?></option>
<?php
			}
?>
                    </optgroup>
<?php
		}
?>
                </select>
            </td>
        </tr>

		<?php do_action( 'bnfw_after_notification_dropdown', $setting ); ?>

        <tr valign="top" id="user-password-msg">
			<td>&nbsp;</td>
			<td>
				<div>
					<p style="margin-top: 0;"><?php esc_html_e( "This notification doesn't support additional email fields.", 'bnfw' ); ?></p>
				</div>
			</td>
        </tr>

        <tr valign="top" id="email-formatting">
			<th>
				<?php esc_attr_e( 'Email Formatting', 'bnfw' ); ?>
			</th>
			<td>
				<label style="margin-right: 20px;">
					<input type="radio" name="email-formatting" value="html" <?php checked( 'html', $setting['email-formatting'] ); ?>>
					<?php esc_html_e( 'HTML Formatting', 'bnfw' ); ?>
				</label>

				<label>
					<input type="radio" name="email-formatting" value="text" <?php checked( 'text', $setting['email-formatting'] ); ?>>
					<?php esc_html_e( 'Plain Text', 'bnfw' ); ?>
				</label>
			</td>
        </tr>

        <tr valign="top" id="toggle-fields">
			<th>
				<?php esc_attr_e( 'Additional Email Fields', 'bnfw' ); ?>
			</th>
			<td>
				<input type="checkbox" id="show-fields" name="show-fields" value="true" <?php checked( $setting['show-fields'], 'true', true ); ?>>
				<label for="show-fields"><?php esc_html_e( 'Set "From" Name & Email, CC, BCC', 'bnfw' ); ?></label>
			</td>
        </tr>

        <tr valign="top" id="email">
            <th scope="row">
                <?php _e( 'From Name and Email', 'bnfw' ); ?>
            </th>
            <td>
            	<input type="text" name="from-name" value="<?php echo $setting['from-name']; ?>" placeholder="Site Name" style="width: 37.35%">
                <input type="email" name="from-email" value="<?php echo $setting['from-email']; ?>" placeholder="Admin Email" style="width: 37.3%">
            </td>
        </tr>

        <tr valign="top" id="cc">
            <th scope="row">
                <?php _e( 'CC', 'bnfw' ); ?>
            </th>

            <td>
				<select multiple name="cc[]" class="<?php echo bnfw_get_user_select_class(); ?>" data-placeholder="Select User Roles / Users" style="width:75%">
					<?php bnfw_render_users_dropdown( $setting['cc'] ); ?>
                </select>
            </td>
        </tr>

        <tr valign="top" id="bcc">
            <th scope="row">
                <?php _e( 'BCC', 'bnfw' ); ?>
            </th>

            <td>
                <select multiple name="bcc[]" class="<?php echo bnfw_get_user_select_class(); ?>" data-placeholder="Select User Roles / Users" style="width:75%">
					<?php bnfw_render_users_dropdown( $setting['bcc'] ); ?>
                </select>
            </td>
        </tr>

        <tr valign="top" id="post-author">
			<th> </th>
			<td>
				<label>
					<input type="checkbox" id="only-post-author" name="only-post-author" value="true" <?php checked( 'true', $setting['only-post-author'] ); ?>>
					<?php _e( 'Send this notification to the Author only', 'bnfw' ); ?>
				</label>
			</td>
        </tr>

        <tr valign="top" id="users">
            <th scope="row">
                <?php _e( 'Send To', 'bnfw' ); ?>
            </th>
            <td>
                <select multiple id="users-select" name="users[]" class="<?php echo bnfw_get_user_select_class(); ?>" data-placeholder="Select User Roles / Users" style="width:75%">
					<?php bnfw_render_users_dropdown( $setting['users'] ); ?>
                </select>
            </td>
        </tr>

        <tr valign="top" id="current-user">
			<th> </th>
			<td>
				<label>
					<input type="checkbox" name="disable-current-user" value="true" <?php checked( 'true', $setting['disable-current-user'] ); ?>>
					<?php _e( 'Disable this Notification for the User that triggered it', 'bnfw' ); ?>
				</label>
			</td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e( 'Subject', 'bnfw' ); ?>
            </th>
            <td>
                <input type="text" name="subject" value="<?php echo esc_attr( $setting['subject'] ); ?>" style="width:75%;">
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e( 'Message Body', 'bnfw' ); ?>
                <div class="wp-ui-text-highlight">
                	<p>
                		<br />
                		<span class="dashicons dashicons-editor-help"></span> <?php _e( 'Need some help?', 'bnfw' ); ?>
                	</p>
                	<p>
						<a href="https://betternotificationsforwp.com/documentation/?utm_source=WP%20Admin%20Notification%20Editor%20-%20'Documentation'&amp;utm_medium=referral" target="_blank" class="button-secondary"><?php _e( 'Documentation', 'bnfw' ); ?></a>
					</p>
					<p>
                		<a href="" target="_blank" id="shortcode-help" class="button-secondary"><?php _e( 'Shortcode Help', 'bnfw' ); ?></a>
                	</p>
                </div>
            </th>
            <td>
				<?php wp_editor( $setting['message'], 'notification_message', array( 'media_buttons' => true ) ); ?>
				<p> &nbsp; </p>
				<div id="disable-autop">
					<label>
						<input type="checkbox" name="disable-autop" value="true" <?php checked( 'true', $setting['disable-autop'] ); ?>>
						<?php _e( 'Stop additional paragraph and line break HTML from being inserted into my notifications', 'bnfw' ); ?>
					</label>
				</div>
            </td>
        </tr>

    </tbody>
    </table>
<?php
	}

	/**
	 * Should we enqueue assets?
	 *
	 * @since 1.0
	 */
	public function is_assets_needed() {
		if ( self::POST_TYPE === get_post_type() ) {
			// The enqueue assets function may be included from addons.
			// We want to disable autosave only for notifications
			wp_dequeue_script( 'autosave' );
			$this->enqueue_assets();
			do_action( 'bnfw_after_enqueue_scripts' );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.4
	 */
	public function enqueue_assets() {
		wp_deregister_script( 'select2' );
		wp_dequeue_script( 'select2' );
		wp_deregister_style( 'select2' );
		wp_dequeue_style( 'select2' );

		// Ultimate Member plugin is giving us problems. They should upgrade
		wp_deregister_script( 'um_minified' );
		wp_dequeue_script( 'um_minified' );
		wp_deregister_script( 'um_admin_scripts' );
		wp_dequeue_script( 'um_admin_scripts' );

		wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css', array(), '4.0.1' );
		wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.full.min.js', array( 'jquery' ), '4.0.1', true );

		wp_enqueue_script( 'bnfw', plugins_url( '../assets/js/bnfw.js', dirname( __FILE__ ) ), array( 'select2' ), '0.1', true );
		wp_enqueue_style( 'bnfw', plugins_url( '../assets/css/bnfw.css', dirname( __FILE__ ) ), array( 'dashicons', 'select2' ), '0.1' );

		$strings = array(
			'empty_user' => __( 'You must choose at least one User or User Role to send the notification to before you can save', 'bnfw' ),
		);

		wp_localize_script( 'bnfw', 'BNFW', $strings );
	}

	/**
	 * Save the meta box's post metadata.
	 *
	 * @since 1.0
	 * @param int     $post_id The ID of the post being saved.
	 */
	public function save_meta_data( $post_id ) {
		if ( self::POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		// Check nonce.
		if ( empty( $_POST[ self::POST_TYPE . '_nonce' ] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST[ self::POST_TYPE . '_nonce' ], self::POST_TYPE ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$setting = array(
			'notification'         => $_POST['notification'],
			'subject'              => $_POST['subject'],
			'message'              => $_POST['notification_message'],
			'disabled'             => isset( $_POST['disabled'] ) ? sanitize_text_field( $_POST['disabled'] ) : 'false',
			'email-formatting'     => isset( $_POST['email-formatting'] ) ? sanitize_text_field( $_POST['email-formatting'] ) : 'html',
			'disable-current-user' => isset( $_POST['disable-current-user'] ) ? sanitize_text_field( $_POST['disable-current-user'] ) : 'false',
			'disable-autop'        => isset( $_POST['disable-autop'] ) ? sanitize_text_field( $_POST['disable-autop'] ) : 'false',
			'only-post-author'     => isset( $_POST['only-post-author'] ) ? sanitize_text_field( $_POST['only-post-author'] ) : 'false',
			'users'                => array(),
		);

		if ( isset( $_POST['users'] ) ) {
			$setting['users'] = $_POST['users'];
		}

		if ( isset( $_POST['show-fields'] ) && 'true' == $_POST['show-fields'] ) {
			$setting['show-fields'] = 'true';
			$setting['from-name']   = sanitize_text_field( $_POST['from-name'] );
			$setting['from-email']  = sanitize_email( $_POST['from-email'] );
			$setting['cc']          = isset( $_POST['cc'] ) ? $_POST['cc'] : '';
			$setting['bcc']         = isset( $_POST['bcc'] ) ? $_POST['bcc'] : '';
		} else {
			$setting['show-fields'] = 'false';
		}

		$setting = apply_filters( 'bnfw_notification_setting', $setting );

		$this->save_settings( $post_id, $setting );

		if ( isset( $_POST['send-test-email'] ) ) {
			if ( 'true' == $_POST['send-test-email'] ) {
				BNFW::factory()->engine->send_test_email( $setting );
				add_filter( 'redirect_post_location', array( $this, 'test_mail_sent' ) );
			}
		}
	}

	/**
	 * Add a query parameter to url if test email was sent.
	 *
	 * @since 1.3
	 */
	public function test_mail_sent( $loc ) {
		return add_query_arg( self::TEST_MAIL_ARG, 1, $loc );
	}

	/**
	 * Add a notification if a test email was sent.
	 *
	 * @since 1.3
	 */
	public function admin_notices() {
		if ( isset( $_GET[self::TEST_MAIL_ARG ] ) ) {
            $screen = get_current_screen();
            if ( in_array( $screen->post_type, array( self::POST_TYPE ) ) ) {
?>
				<div class="updated below-h2">
					<p><?php echo __( 'Test Notification Sent.', 'bnfw' ); ?></p>
				</div>
<?php
            }
        }
	}

	/**
	 * Save settings in post meta.
	 *
	 * @since 1.0
	 * @access private
	 * @param unknown $post_id
	 * @param unknown $setting
	 */
	private function save_settings( $post_id, $setting ) {
		foreach ( $setting as $key => $value ) {
			update_post_meta( $post_id, self::META_KEY_PREFIX . $key, $value );
		}
	}

	/**
	 * Read settings from post meta.
	 *
	 * @since 1.0
	 * @param int $post_id
	 * @return array
	 */
	public function read_settings( $post_id ) {
		$setting = array();
		$default = array(
			'notification'         => '',
			'from-name'            => '',
			'from-email'           => '',
			'cc'                   => array(),
			'bcc'                  => array(),
			'users'                => array(),
			'subject'              => '',
			'email-formatting'     => get_option( 'bnfw_email_format', 'html' ),
			'message'              => '',
			'show-fields'          => 'false',
			'disable-current-user' => 'false',
			'disable-autop'        => 'false',
			'only-post-author'     => 'false',
			'disabled'             => 'false',
		);

		$default = apply_filters( 'bnfw_notification_setting_fields', $default );

		foreach ( $default as $key => $default_value ) {
			$value = get_post_meta( $post_id, self::META_KEY_PREFIX . $key, true );
			if ( ! empty( $value ) ) {
				$setting[ $key ] = $value;
			} else {
				$setting[ $key ] = $default_value;
			}
		}

		// compatibility code. This will be removed subsequently
		$user_roles = get_post_meta( $post_id, self::META_KEY_PREFIX . 'user-roles', true );
		if ( ! empty( $user_roles ) && is_array( $user_roles ) ) {
			foreach ( $user_roles as $role ) {
				$setting['users'][] = 'role-' . $role;
			}

			update_post_meta( $post_id, self::META_KEY_PREFIX . 'users', $setting['users'] );
			delete_post_meta( $post_id, self::META_KEY_PREFIX . 'user-roles' );
		}

		$setting['id'] = $post_id;
		return $setting;
	}

	/**
	 * Change the post updated message for notification post type.
	 *
	 * @since 1.0
	 * @param unknown $messages
	 * @return unknown
	 */
	public function post_updated_messages( $messages ) {
		$messages[ self::POST_TYPE ] = array_fill( 0, 11,  __( 'Notification saved.', 'bnfw' ) );

		return $messages;
	}

	/**
	 * Render submit div meta box.
	 *
	 * @since 1.0
	 * @param unknown $post
	 */
	public function render_submitdiv( $post ) {
		global $post;
?>
<div class="submitbox" id="submitpost">

    <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
    <div style="display:none;">
        <?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
    </div>

    <?php // Always publish. ?>
    <div class="disable-notification-checkbox" style="padding: 5px 0 10px 0;">
    	<input type="hidden" name="post_status" id="hidden_post_status" value="publish">

<?php
		$setting = $this->read_settings( $post->ID );
?>
		<label>
			<input type="checkbox" name="disabled" value="true" <?php checked( $setting['disabled'], 'true', true ); ?>><?php _e( 'Disable Notification', 'bnfw' ); ?>
		</label>
		
		<br>
		<br>

<?php if ( 'publish' == $post->post_status ) { ?>
			<input type="hidden" name="send-test-email" id="send-test-email" value="false">
            <input name="test-email" type="submit" class="button button-secondary button-large" id="test-email" value="<?php esc_attr_e( 'Send Me a Test Email', 'bnfw' ); ?>">
<?php } ?>

	</div>

    <div id="major-publishing-actions">

        <div id="delete-action">
        <?php
		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently', 'bnfw' );
		} else {
			$delete_text = __( 'Move to Trash', 'bnfw' );
		}
?>
        <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a>
        </div>

        <div id="publishing-action">
            <span class="spinner"></span>
            <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Save' ) ?>">
            <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e( 'Save' ) ?>">
        </div>
        <div class="clear"></div>

    </div>
    <!-- #major-publishing-actions -->

    <div class="clear"></div>
</div>
<!-- #submitpost -->
<?php
	}

	/**
	 * Get notifications based on type.
	 *
	 * @since 1.0
	 * @param array|string $types
	 * @param bool $exclude_disabled (optional) Whether to exclude disabled notifications or not. True by default.
	 * @return array WP_Post objects
	 */
	public function get_notifications( $types, $exclude_disabled = true ) {
		if ( ! is_array( $types ) ) {
			$types = array( $types );
		}

		$args = array(
			'post_type' => self::POST_TYPE,
			'meta_query' => array(
				array(
					'key'     => self::META_KEY_PREFIX . 'notification',
					'value'   => $types,
					'compare' => 'IN',
				),
			),
		);

		if ( $exclude_disabled ) {
			$args['meta_query'][] = array(
				'key'     => self::META_KEY_PREFIX . 'disabled',
				'value'   => 'true',
				'compare' => '!=',
			);
		}

		$wp_query = new WP_Query();
		$posts = $wp_query->query( $args );
		return $posts;
	}

	/**
	 * Does a particular type of notification exists or not.
	 *
	 * @since 1.1
	 *
	 * @param string $type Notification Type.
	 * @param bool $exclude_disabled (optional) Whether to exclude disabled notifications or not. True by default.
	 * @return bool True if present, False otherwise
	 */
	public function notification_exists( $type, $exclude_disabled = true ) {
		$notifications = $this->get_notifications( $type, $exclude_disabled );

		if ( count( $notifications ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Custom columns for this post type.
	 *
	 * @since 1.0
	 * @filter manage_{post_type}_posts_columns
	 * @param array   $columns
	 * @return array
	 */
	public function columns_header( $columns ) {
		$columns['type']     = __( 'Notification Type', 'bnfw' );
		$columns['disabled'] = __( 'Enabled?', 'bnfw' );
		$columns['subject']  = __( 'Subject', 'bnfw' );
		$columns['users']    = __( 'User Roles / Users', 'bnfw' );

		return $columns;
	}

	/**
	 * Custom column appears in each row.
	 *
	 * @since 1.0
	 * @action manage_{post_type}_posts_custom_column
	 * @param string  $column  Column name
	 * @param int     $post_id Post ID
	 */
	public function custom_column_row( $column, $post_id ) {
		$setting = $this->read_settings( $post_id );
		switch ( $column ) {
			case 'disabled':
				if ( 'true' != $setting['disabled'] ) {
					printf('<span class="dashicons dashicons-yes"></span>');
				}
				break;
			case 'type':
				echo $this->get_notifications_name( $setting['notification'] );
				break;
			case 'subject':
				echo ! empty( $setting['subject'] ) ? $setting['subject'] : '';
				break;
			case 'users':
				if ( 'true' === $setting['only-post-author'] ) {
					echo __( 'Author only', 'bnfw' );
				} else {
					$users = $this->get_names_from_users( $setting['users'] );
					echo implode( ', ', $users );
				}
				break;
		}

		/**
		 * Invoked while displaying a custom column in notification table.
		 *
		 * @since 1.3.9
		 *
		 * @param string  $column  Column name
		 * @param int     $post_id Post ID
		 */
		do_action( 'bnfw_notification_table_column', $column, $post_id );
	}

	/**
	 * Get names from users.
	 *
	 * @since 1.2
	 */
	private function get_names_from_users( $users ) {
		$email_list = array();
		$user_ids = array();
		$user_roles = array();
		$names_from_user_ids = array();

		foreach ( $users as $user ) {
			if ( $this->starts_with( $user, 'role-' ) ) {
				$user_roles[] = str_replace( 'role-', '', $user );
			} else {
				$user_ids[] = absint( $user );
			}
		}

		if ( ! empty( $user_ids ) ) {
			$user_query = new WP_User_Query( array( 'include' => $user_ids ) );
			foreach ( $user_query->results as $user ) {
				$names_from_user_ids[] = $user->user_login;
			}
		}

		return array_merge( $user_roles, $names_from_user_ids );
	}

	/**
	 * Get name of the notification based on slug.
	 *
	 * @param mixed   $slug
	 * @return unknown
	 */
	private function get_notifications_name( $slug ) {
		$name = '';
		switch ( $slug ) {
			case 'new-comment':
				$name = __( 'New Comment', 'bnfw' );
				break;
			case 'new-trackback':
				$name = __( 'New Trackback', 'bnfw' );
				break;
			case 'new-pingback':
				$name = __( 'New Pingback', 'bnfw' );
				break;
			case 'reply-comment':
				$name = __( 'Comment Reply', 'bnfw' );
				break;
			case 'user-password':
				$name = __( 'Lost Password - For User', 'bnfw' );
				break;
			case 'admin-password':
				$name = __( 'Lost Password - For Admin', 'bnfw' );
				break;
			case 'new-user':
				$name = __( 'New User Registration - For User', 'bnfw' );
				break;
			case 'welcome-email':
				$name = __( 'New User - Post-registration Email', 'bnfw' );
				break;
			case 'admin-user':
				$name = __( 'New User Registration - For Admin', 'bnfw' );
				break;
			case 'user-role':
				$name = __( 'User Role Changed', 'bnfw' );
				break;
			case 'new-post':
				$name = __( 'New Post Published', 'bnfw' );
				break;
			case 'update-post':
				$name = __( 'Post Updated', 'bnfw' );
				break;
			case 'pending-post':
				$name = __( 'Post Pending Review', 'bnfw' );
				break;
			case 'future-post':
				$name = __( 'Post Scheduled', 'bnfw' );
				break;
			case 'newterm-category':
				$name = __( 'New Category', 'bnfw' );
				break;
			case 'newterm-post_tag':
				$name = __( 'New Tag', 'bnfw' );
				break;
			default:
				$splited = explode( '-', $slug );
				$label = $splited[1];
				$post_obj = get_post_type_object( $splited[1] );

				if ( null != $post_obj ) {
					$label = $post_obj->labels->singular_name;
				}

				switch ( $splited[0] ) {
					case 'new':
						$name = __( 'New ', 'bnfw' ) . $label;
						break;
					case 'update':
						$name = __( 'Updated ', 'bnfw' ) . $label;
						break;
					case 'pending':
						$name = $label . __( ' Pending Review', 'bnfw' );
						break;
					case 'future':
						$name = $label . __( ' Scheduled', 'bnfw' );
						break;
					case 'comment':
						$name = $label . __( ' Comment', 'bnfw' );
						break;
					case 'newterm':
						$tax = get_taxonomy( $splited[1] );
						if ( ! $tax ) {
							$name = __( 'New Term', 'bnfw' );
						} else {
							$name = __( 'New Term in ', 'bnfw' ) . $tax->labels->name;
						}
						break;
				}
				break;
		}

		$name = apply_filters( 'bnfw_notification_name', $name, $slug );

		return $name;
	}

	/**
	 * Custom row actions for this post type.
	 *
	 * @since 1.0
	 * @filter post_row_actions
	 * @param array   $actions
	 * @return array
	 */
	public function custom_row_actions( $actions ) {
		$post = get_post();

		if ( self::POST_TYPE === get_post_type( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}

		return $actions;
	}

	/**
	 * Find if a string starts with another string.
	 *
	 * @since 1.2
	 */
	public function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return '' === $needle || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
	}
}
