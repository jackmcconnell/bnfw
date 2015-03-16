<?php
/**
 * Register the Admin pages and load the scripts action
 **/

/**
 * Sub-menu pages
 */
function bnfw_admin_menu() {

	// New Notifications Sub-menu
	add_submenu_page(
		'edit.php?post_type=bnfw_notification',
		__( 'Notification Settings', 'bnfw' ),
		__( 'Settings', 'bnfw' ),
		'manage_options',
		'bnfw-settings',
		'bnfw_settings_page'
	);
}
// Add the Admin pages to the WordPress menu
add_action( 'admin_menu', 'bnfw_admin_menu' );

/* ------------------------------------------------------------------------ *
 * Menu Pages
 * ------------------------------------------------------------------------ */

/**
 * Settings Page
 */
function bnfw_settings_page() {
	ob_start(); ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e( 'BNFW Settings', 'bnfw' ); ?></h2>

        <form method="post" action="options.php" class="bnfw-form">
<?php
	settings_errors();
	settings_fields( 'bnfw-settings' );
	do_settings_sections( 'bnfw-settings' );

	submit_button( 'Save Settings' );
?>
        </form>
    </div>

    <?php echo ob_get_clean();
}

/* ------------------------------------------------------------------------ *
 * Settings Page - Setting Registration
 * ------------------------------------------------------------------------ */

/**
 *
 */
function bnfw_general_options() {

	// Set-up - General Options Section
	add_settings_section (
		'bnfw_general_options_section',     // Section ID
		'',                                 // Title above settings section
		'bnfw_general_options_callback',    // Name of function that renders a description of the settings section
		'bnfw-settings'                     // Page to show on
	);

	// Suppress notifications for SPAM comments
	add_settings_field (
		'bnfw_suppress_spam',           // Field ID
		__( 'Suppress SPAM comment notification', 'bnfw' ),  // Label to the left
		'bnfw_suppress_spam_checkbox',  // Name of function that renders options on the page
		'bnfw-settings',                // Page to show on
		'bnfw_general_options_section', // Associate with which settings section?
		array(
			__( "Don't send notifications for comments marked as SPAM by Akismet", 'bnfw' )
		)
	);

	// Register - Suppress SPAM Checkbox
	register_setting (
		'bnfw-settings',
		'bnfw_suppress_spam'
	);
}
add_action( 'admin_init', 'bnfw_general_options' );

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Section Callbacks
 * ------------------------------------------------------------------------ */

/**
 *
 */
function bnfw_general_options_callback() {}

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Field Callbacks
 * ------------------------------------------------------------------------ */

/**
 * Suppress SPAM checkbox.
 *
 * @since 1.0
 * @param unknown $args
 */
function bnfw_suppress_spam_checkbox( $args ) {
?>
    <input type="checkbox" id="bnfw_suppress_spam" name="bnfw_suppress_spam" value="1" <?php checked( 1, get_option( 'bnfw_suppress_spam' ), true );?>>
    <label for="bnfw_suppress_spam"><?php echo $args[0]; ?></label>
<?php
}
