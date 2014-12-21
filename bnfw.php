<?php
/**
Plugin Name: Better Notifications for WordPress
Plugin URI: http://wordpress.org/plugins/bnfw/
Description: Send customisable HTML emails to your users for different WordPress notifications.
Version: 1.0.1
Author: Voltronik
Author URI: http://www.voltronik.co.uk/
Author Email: plugins@voltronik.co.uk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: bnfw
Domain Path: languages/
**/

/**
  Copyright © 2014 Voltronik (plugins@voltronik.co.uk)

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

        $this->notifier = new BNFW_Notification;
        $this->engine   = new BNFW_Engine;
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
        // Load Engine and post type
        require_once( 'includes/engine/class-bnfw-engine.php' );
        require_once( 'includes/admin/class-bnfw-notification.php' );

        // Load Admin Pages
        if ( is_admin() ) {
            require_once( 'includes/admin/bnfw-settings.php' );
        }

        // uncomment for debugging
        //require_once ('includes/debug.php');
    }

    /**
     * Register Hooks.
     *
     * @since 1.0
     */
    public function hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        add_action( 'draft_to_publish'    , array( $this, 'publish_post' ) );
        add_action( 'publish_to_publish'  , array( $this, 'update_post' ) );

        add_action( 'comment_post'        , array( $this, 'comment_post' ) );
        add_action( 'trackback_post'      , array( $this, 'trackback_post' ) );
        add_action( 'pingback_post'       , array( $this, 'pingback_post' ) );

        add_action( 'user_register'       , array( $this, 'user_register' ) );
        add_action( 'lostpassword_post'   , array( $this, 'lost_password' ) );

        add_action( 'create_term'         , array( $this, 'create_term' ), 10, 3 );

        add_filter( 'plugin_action_links' , array( $this, 'plugin_action_links' ), 10, 4 );
    }

    /**
     * Run this on first-time plugin activation
     *
     * @since 1.0
     */
        // importer
    public function activate() {
        require_once( dirname( __FILE__ ) . '/includes/import.php' );
        $importer = new BNFW_Import;
        $importer->import();
    }

    /**
     * Add 'Settings' link below BNFW in Plugins list.
     *
     * @since 1.0
     */
    public function plugin_action_links( $links, $file ) {
        $plugin_file = 'bnfw/bnfw.php';
        if ( $file == $plugin_file ) {
            $settings_link = '<a href="' . admin_url( 'admin.php?page=bnfw-settings' ) . '">' . 'Settings' . '</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }

    /**
     * When a new term is created.
     *
     * @since 1.0
     */
    public function create_term( $term_id, $tt_id, $taxonomy ) {
        if ( 'category' == $taxonomy ) {
            $this->send_notification( 'new-category', $term_id );
        } else {
            $this->send_notification( 'new-term', $term_id );
        }
    }

    /**
     * Fires when a post is created for the first time.
     *
     * @since 1.0
     */
    function publish_post( $post ) {
        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification( 'new-' . $post_type, $post_id );
        }
    }

    /**
     * Fires when a post is updated.
     *
     * @since 1.0
     */
    function update_post( $post ) {
        $post_id   = $post->ID;
        $post_type = $post->post_type;

        if ( BNFW_Notification::POST_TYPE != $post_type ) {
            $this->send_notification( 'update-' . $post_type, $post_id );
        }
    }

    /**
     * Send notification for new comments
     *
     * @since 1.0
     */
    function comment_post( $comment_id ) {
        $the_comment = get_comment( $comment_id );
        if ( $this->can_send_comment_notification( $the_comment ) ) {
            $this->send_notification( 'new-comment', $comment_id );
        }
    }

    /**
     * Send notification for new trackback
     *
     * @since 1.0
     */
    function trackback_post( $comment_id ){
        $the_comment = get_comment( $comment_id );
        if ( $this->can_send_comment_notification( $the_comment ) ) {
            $this->send_notification( 'new-trackback', $comment_id );
        }
    }

    /**
     * Send notification for new pingbacks
     *
     * @since 1.0
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
    function lost_password() {
        $user = get_user_by( 'login', trim( $_POST['user_login'] ) );
        if ( $user ) {
            $this->send_notification( 'user-password', $user->ID );
        }
    }

    /**
     * Send notification for new uses.
     *
     * @since 1.0
     */
    function user_register( $user_id ) {
        $this->send_notification( 'new-user', $user_id );
    }

    /**
     * Send notification based on type and ref id
     *
     * @access private
     * @since 1.0
     */
    private function send_notification( $type, $ref_id ) {
        $notifications = $this->notifier->get_notifications( $type );
        foreach ( $notifications as $notification ) {
            $this->engine->send_notification( $this->notifier->read_settings( $notification->ID ), $ref_id );
        }
    }

    /**
     * Can send comment notification or not
     *
     * @since 1.0
     */
    private function can_send_comment_notification( $comment ) {
        // Returns false if the comment is marked as spam AND admin has enabled suppression of spam
        $suppress_spam = get_option( 'bnfw_suppress_spam' );
        if ( '1' === $suppress_spam && ( 0 === strcmp( $comment->comment_approved, 'spam' ) ) ) {
            return false;
        }
        return true;
    }
}

/* ------------------------------------------------------------------------ *
 * Fire up the plugin
 * ------------------------------------------------------------------------ */
new BNFW;
