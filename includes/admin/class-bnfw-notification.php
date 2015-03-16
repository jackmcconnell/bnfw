<?php
/**
 * BNFW Notification.
 *
 * @since 1.0
 */

class BNFW_Notification {

	const POST_TYPE       = 'bnfw_notification';
	const META_KEY_PREFIX = 'bnfw_';

	/**
	 *
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_meta_boxes' ) );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Custom row actions.
		add_filter( 'post_row_actions', array( $this, 'custom_row_actions' ), 10, 2 );

		// Custom columns
		add_filter( sprintf( 'manage_%s_posts_columns', self::POST_TYPE ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', self::POST_TYPE ), array( $this, 'custom_column_row' ), 10, 2 );

		// Enqueue scripts/styles and disables autosave for this post type.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
		global $wp_roles;
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
                    <option value="welcome-email" <?php selected( 'welcome-email', $setting['notification'] );?>><?php _e( 'New User - Welcome Email', 'bnfw' );?></option>
                    </optgroup>
                    <optgroup label="Posts">
                    <option value="new-post" <?php selected( 'new-post', $setting['notification'] );?>><?php _e( 'New Post Published', 'bnfw' );?></option>
                    <option value="update-post" <?php selected( 'update-post', $setting['notification'] );?>><?php _e( 'Post Updated', 'bnfw' );?></option>
					<option value="pending-post" <?php selected( 'pending-post', $setting['notification'] );?>><?php _e( 'Post Pending Review', 'bnfw' );?></option>
					<option value="new-category" <?php selected( 'new-category', $setting['notification'] );?>><?php _e( 'New Category', 'bnfw' ); ?></option>
					<option value="new-post_tag" <?php selected( 'new-post_tag', $setting['notification'] );?>><?php _e( 'New Tag', 'bnfw' ); ?></option>
                    </optgroup>
<?php
		$types = get_post_types( array(
				'_builtin' => false,
			), 'names'
		);

		foreach ( $types as $type ) {
			if ( $type != self::POST_TYPE ) {
				$post_obj = get_post_type_object( $type );
				$label = $post_obj->labels->singular_name;
?>
                    <optgroup label="<?php _e( 'Custom Post Type - ', 'bnfw' ); echo $type; ?>">
                        <option value="new-<?php echo $type; ?>" <?php selected( 'new-' . $type, $setting['notification'] );?>><?php echo __( 'New ', 'bnfw' ), "'$label'"; ?></option>
                        <option value="update-<?php echo $type; ?>" <?php selected( 'update-' . $type, $setting['notification'] );?>><?php echo "'$label' " . __( 'Update ', 'bnfw' ); ?></option>
                        <option value="pending-<?php echo $type; ?>" <?php selected( 'pending-' . $type, $setting['notification'] );?>><?php echo "'$label' ", __( 'Pending Review', 'bnfw' ); ?></option>
                    </optgroup>
<?php
			}
		}

		$taxs = get_taxonomies( array(
			'public'   => true,
			'_builtin' => false,
		), 'objects'
	);

		if ( count( $taxs > 0 ) ) {
			foreach ( $taxs as $tax ) {
				$tax_name = 'newterm-' . $tax->name;
?>
                    <optgroup label="<?php _e( 'Custom Taxonomy', 'bnfw' );?>">
						<option value="<?php echo $tax_name; ?>" <?php selected( $tax_name, $setting['notification'] );?>><?php printf( '%s %s', __( 'New', 'bnfw' ), $tax->labels->name ); ?></option>
                    </optgroup>
<?php
			}
		}
?>
                </select>
            </td>
        </tr>

        <tr valign="top" id="user-password-msg">
			<td>&nbsp;</td>
			<td>
				<div style="background:#FFFFE1;padding:15px;">
					<p style="margin-top: 0;"><?php esc_html_e( "This notification doesn't support additional email fields or shortcodes in the subject line", 'bnfw' ); ?></p>
				</div>
			</td>
        </tr>

        <tr valign="top" id="user-role">
            <th scope="row">
                <?php _e( 'User Roles', 'bnfw' ); ?>
            </th>
<?php
		$roles_style = '';
		$user_style = 'display:none';

		if ( count( $setting['users'] ) > 0 ) {
			$roles_style = 'display:none';
			$user_style = '';
		}
?>
            <td>
                <div id="bnfw_user_role_container" style="<?php echo $roles_style; ?>">
                <select multiple name="user-roles[]" class="select2" data-placeholder="Select User Role" style="width:75%">
<?php
		$roles = $wp_roles->get_names();

		foreach ( $roles as $role ) {
			$selected = selected( true, in_array( $role, $setting['user-roles'] ), false );
			echo '<option value="', $role, '" ', $selected, '>', $role, '</option>';
		}
?>
                </select><br>
                <a id="bnfw_user_role_toggle" href="#"><?php _e( 'Define individual users instead', 'bnfw' );?></a>
                </div>

                <div id="bnfw_user_container" style="<?php echo $user_style; ?>">
                <select multiple name="users[]" class="select2" data-placeholder="Select Users" style="width:75%">
<?php
		$users = get_users( array(
				'order_by' => 'email',
			) );

		foreach ( $users as $user ) {
			$selected = selected( true, in_array( $user->ID, $setting['users'] ), false );
			echo '<option value="', $user->ID, '" ', $selected, '>', $user->user_login, '</option>';
		}
?>
                </select><br>
                <a id="bnfw_user_toggle" href="#"><?php _e( 'Define user roles instead', 'bnfw' );?></a>
                </div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e( 'Subject', 'bnfw' ); ?>
            </th>
            <td>
                <input type="text" name="subject" value="<?php echo $setting['subject']; ?>" style="width:75%;">
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php _e( 'Message Body', 'bnfw' ); ?>
            </th>
            <td>
                <textarea name="message" rows="10" style="width:75%;"><?php echo esc_textarea( $setting['message'] ); ?></textarea>
            </td>
        </tr>
    </tbody>
    </table>
<?php
	}

	/**
	 * Render user roles dropdown.
	 *
	 * @since 1.0
	 * @param unknown $field
	 * @param unknown $value
	 * @param unknown $multiple (optional)
	 * @param unknown $width    (optional)
	 */
	private function render_roles_dropdown( $field, $value, $multiple = '', $width = 25 ) {
		global $wp_roles;
?>
    <select <?php echo $multiple; ?> name="<?php echo $field; if ( ! empty( $multiple ) ) echo '[]';?>" id="<?php echo $field;?>" class="select2" data-placeholder="Select User Role" style="width:<?php echo $width; ?>%">
<?php
		$roles = $wp_roles->get_names();

		foreach ( $roles as $role ) {
			if ( empty( $multiple ) ) {
				$selected = selected( $value, $role, false );
			} else {
				$selected = selected( true, in_array( $role, $value ), false );
			}
			echo '<option value="', $role, '" ', $selected, '>', $role, '</option>';
		}
?>
        </select>
<?php
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {
		if ( self::POST_TYPE === get_post_type() ) {
			wp_dequeue_script( 'autosave' );

			wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css', array(), '3.5.2' );
			wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js', array( 'jquery' ), '3.5.2', true );
			wp_enqueue_script( 'bnfw', plugins_url( '../assets/js/bnfw.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.1', true );
		}
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
			'notification' => $_POST['notification'],
			'subject'      => sanitize_text_field( $_POST['subject'] ),
			'message'      => $_POST['message'],
			'disabled'     => sanitize_text_field( $_POST['disabled'] ),
			//'show-fields'  => sanitize_text_field( $_POST['show-fields'] ),
		);

		if ( isset( $_POST['user-roles'] ) ) {
			$setting['user-roles'] = $_POST['user-roles'];
			$setting['users']      = array();
		} else {
			$setting['user-roles'] = array();
			$setting['users']      = $_POST['users'];
		}

		//if ( 'true' == $setting['show-fields'] ) {
			//$setting['from-name']  = sanitize_text_field( $_POST['from-name'] );
			//$setting['from-email'] = sanitize_email( $_POST['from-email'] );
			//$setting['cc-email']   = sanitize_email( $_POST['cc-email'] );
			//$setting['cc-roles']   = $_POST['cc-roles'];
			//$setting['bcc-email']  = sanitize_email( $_POST['bcc-email'] );
			//$setting['bcc-roles']  = $_POST['bcc-roles'];
		//}

		$this->save_settings( $post_id, $setting );
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
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function read_settings( $post_id ) {
		$setting = array();
		$default = array(
			'notification' => '',
			'from-name'    => '',
			'from-email'   => '',
			'cc-email'     => '',
			'cc-roles'     => '',
			'bcc-email'    => '',
			'bcc-roles'    => '',
			'user-roles'   => array(),
			'users'        => array(),
			'subject'      => '',
			'message'      => '',
			//'show-fields'  => 'false',
			'disabled'     => 'false',
		);

		foreach ( $default as $key => $default_value ) {
			$value = get_post_meta( $post_id, self::META_KEY_PREFIX . $key, true );
			if ( ! empty( $value ) ) {
				$setting[ $key ] = $value;
			} else {
				$setting[ $key ] = $default_value;
			}
		}

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
		<input type="checkbox" name="disabled" value="true" <?php checked( $setting['disabled'], 'true', true ); ?>><?php _e( 'Disable Notification', 'bnfw' ); ?>
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
	 * @param unknown $type
	 * @return unknown
	 */
	public function get_notifications( $type ) {
		$args = array(
			'post_type' => self::POST_TYPE,
			'meta_query' => array(
				array(
					'key'     => self::META_KEY_PREFIX . 'notification',
					'value'   => $type,
				),
				array(
					'key'     => self::META_KEY_PREFIX . 'disabled',
					'value'   => 'true',
					'compare' => '!=',
				),
			),
		);

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
	 * @return bool True if present, False otherwise
	 */
	public function notification_exists( $type ) {
		$notifications = $this->get_notifications( $type );

		if ( count( $notifications ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Custom columns for this post type.
	 *
	 *
	 * @since 1.0
	 * @filter manage_{post_type}_posts_columns
	 * @param array   $columns
	 * @return array
	 */
	public function columns_header( $columns ) {
		$columns['type']       = __( 'Notification Type', 'bnfw' );
		$columns['disabled']   = __( 'Enabled?', 'bnfw' );
		$columns['subject']    = __( 'Subject', 'bnfw' );
		$columns['user-roles'] = __( 'User Roles/Users', 'bnfw' );

		return $columns;
	}

	/**
	 * Custom column appears in each row.
	 *
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
					printf( '<img src="%s" style="width:25px;">', plugins_url( '../assets/images/notification-enabled.png', dirname( __FILE__ ) ) );
				}
				break;
			case 'type':
				echo $this->get_notifications_name( $setting['notification'] );
				break;
			case 'subject':
				echo ! empty( $setting['subject'] ) ? $setting['subject'] : '';
				break;
			case 'user-roles':
				if ( ! empty( $setting['users'] ) ) {
					$users = array();
					$user_query = new WP_User_Query( array( 'include' => $setting['users'] ) );
					foreach ( $user_query->results as $user ) {
						$users[] = $user->user_login;
					}
					echo implode( ', ', $users );
				} else {
					echo ! empty( $setting['user-roles'] ) ? implode( ', ', $setting['user-roles'] ) : '';
				}
				break;
		}
	}

	/**
	 * Get name of the notification based on slug.
	 *
	 * @param mixed   $slug
	 * @return unknown
	 */
	private function get_notifications_name( $slug ) {
		switch ( $slug ) {
			case 'new-comment':
				return __( 'New Comment', 'bnfw' );
				break;
			case 'new-trackback':
				return __( 'New Trackback', 'bnfw' );
				break;
			case 'new-pingback':
				return __( 'New Pingback', 'bnfw' );
				break;
			case 'user-password':
				return __( 'Lost Password - For User', 'bnfw' );
				break;
			case 'admin-password':
				return __( 'Lost Password - For Admin', 'bnfw' );
				break;
			case 'new-user':
				return __( 'User Registration - For User', 'bnfw' );
				break;
			case 'welcome-email':
				return __( 'New User - Welcome email', 'bnfw' );
				break;
			case 'admin-user':
				return __( 'User Registration - For Admin', 'bnfw' );
				break;
			case 'new-post':
				return __( 'New Post Published', 'bnfw' );
				break;
			case 'update-post':
				return __( 'Post Updated', 'bnfw' );
				break;
			case 'pending-post':
				return __( 'Post Pending Review', 'bnfw' );
				break;
			case 'new-category':
				return __( 'New Category', 'bnfw' );
				break;
			case 'new-post_tag':
				return __( 'New Tag', 'bnfw' );
				break;
			default:
				$splited = explode( '-', $slug );
				switch ( $splited[1] ) {
					case 'new':
						return __( 'New ', 'bnfw' ) . $splited[1];
						break;
					case 'update':
						return __( 'Updated ', 'bnfw' ) . $splited[1];
						break;
					case 'pending':
						return $splited[1] . __( ' Pending Review', 'bnfw' );
							break;
				}
				break;
		}
	}

	/**
	 * Custom row actions for this post type.
	 *
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
}
