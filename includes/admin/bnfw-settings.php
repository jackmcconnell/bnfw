<?php
/* ------------------------------------------------------------------------ *
 * Register the Admin pages and load the scripts action
 * ------------------------------------------------------------------------ */

// Sub-menu pages
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

// Settings Page
function bnfw_settings_page() {
    ob_start(); ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e( 'BNFW Settings', 'bnfw'); ?></h2>

        <form method="post" action="options.php" class="bnfw-form">
            <?php
                settings_errors();
                settings_fields('bnfw-settings');
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
function bnfw_general_options() {

    // Set-up - General Options Section
    add_settings_section (
        'bnfw_general_options_section',     // Section ID
        '',                                 // Title above settings section
        'bnfw_general_options_callback',    // Name of function that renders a description of the settings section
        'bnfw-settings'                     // Page to show on
    );

    // Add - Specify Name, Email, CC and BCC Checkbox
    //add_settings_field (
        //'bnfw_specify_email_headers',               // Field ID
        //__( 'Email Headers', 'bnfw' ),              // Label to the left
        //'bnfw_general_options_section_checkbox',    // Name of function that renders options on the page
        //'bnfw-settings',                            // Page to show on
        //'bnfw_general_options_section',             // Associate with which settings section?
        //array(
            //__( 'Do you want to specify a Name, Email, CC and BCC for each notification?', 'bnfw' )
        //)
    //);

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

    //// Add - Minimum access role dropdown
    //add_settings_field (
        //'bnfw_choose_admin',                        // Field ID
        //__( 'Set minimum role to have access to this plugin', 'bnfw' ), // Label to the left
        //'bnfw_choose_admin_dropdown',               // Name of function that renders options on the page
        //'bnfw-settings',                            // Page to show on
        //'bnfw_general_options_section'              // Associate with which settings section?
    //);

    // Register - Specify Name, Email, CC and BCC Checkbox
    //register_setting (
        //'bnfw-settings',
        //'bnfw_specify_email_headers'
    //);

    // Register - Suppress SPAM Checkbox
    register_setting (
        'bnfw-settings',
        'bnfw_suppress_spam'
    );

    //// Register - Minimum access role dropdown
    //register_setting (
        //'bnfw-settings',
        //'bnfw_choose_admin'
    //);
}
add_action('admin_init', 'bnfw_general_options');

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Section Callbacks
 * ------------------------------------------------------------------------ */

function bnfw_general_options_callback() {}

/* ------------------------------------------------------------------------ *
 * Settings Page - Settings Field Callbacks
 * ------------------------------------------------------------------------ */

// Specify Name, Email, CC and BCC Checkbox
function bnfw_general_options_section_checkbox($args) {
    echo '<input type="checkbox" id="bnfw_specify_email_headers" name="bnfw_specify_email_headers" value="1" ' . checked(1, get_option('bnfw_specify_email_headers'), false) . '/>';
    echo '<label for="bnfw_specify_email_headers"> '  . $args[0] . '</label>';
}

/**
 * Suppress SPAM checkbox.
 *
 * @since 1.0
 */
function bnfw_suppress_spam_checkbox( $args ) {
?>
    <input type="checkbox" id="bnfw_suppress_spam" name="bnfw_suppress_spam" value="1" <?php checked( 1, get_option( 'bnfw_suppress_spam' ), true );?>>
    <label for="bnfw_suppress_spam"><?php echo $args[0]; ?></label>
<?php
}

// Minimum access role dropdown
function bnfw_choose_admin_dropdown($args) {

    echo '<select id="bnfw_choose_admin" name="bnfw_choose_admin">';
            global $wp_roles;
            $roles = $wp_roles->get_names();

            foreach($roles as $role) {
                echo '<option value="'.$role.'"' . selected(get_option('bnfw_choose_admin'), $role , false) . '>' . $role . '</option>';
            }
    echo '</select>';
}

/* ------------------------------------------------------------------------ *
 * Notification Generator - Setting Registration
 * ------------------------------------------------------------------------ */
function bnfw_generator_options() {

    // Set-up - Generator Section
    add_settings_section (
        'bnfw_generator_section',           // Section ID
        '',                                 // Title above settings section
        'bnfw_generator_callback',          // Name of function that renders a description of the settings section
        'bnfw-generator'                    // Page to show on
    );

    // Add - Notification Name Text
    add_settings_field (
        'bnfw_notification_name',                   // Field ID
        __( 'Notification Name', 'bnfw' ),                        // Label to the left
        'bnfw_generator_notification_name',         // Name of function that renders options on the page
        'bnfw-generator',                           // Page to show on
        'bnfw_generator_section'                    // Associate with which settings section?
    );

    // Add - Notification User Roles
    add_settings_field (
        'bnfw_user_roles',                          // Field ID
        __( 'User Roles', 'bnfw' ),                               // Label to the left
        'bnfw_generator_user_roles',                // Name of function that renders options on the page
        'bnfw-generator',                           // Page to show on
        'bnfw_generator_section'                    // Associate with which settings section?
    );

    // Add - Notification Users
    add_settings_field (
        'bnfw_users',                               // Field ID
        __( 'User', 'bnfw' ),                                     // Label to the left
        'bnfw_generator_users',                     // Name of function that renders options on the page
        'bnfw-generator',                           // Page to show on
        'bnfw_generator_section'                    // Associate with which settings section?
    );


    // Register - All Generator Fields
    register_setting ( 'bnfw-generator', 'bnfw_notification_name' );
    register_setting ( 'bnfw-generator', 'bnfw_user_roles' );
    register_setting ( 'bnfw-generator', 'bnfw_users' );
}
add_action('admin_init', 'bnfw_generator_options');

/* ------------------------------------------------------------------------ *
 * Notification Generator - Form Section Callbacks
 * ------------------------------------------------------------------------ */

function bnfw_generator_callback() {}

/* ------------------------------------------------------------------------ *
 * Notification Generator - Field Callbacks
 * ------------------------------------------------------------------------ */

function bnfw_generator_notification_name($args) {

    $settings = get_option( 'bnfw_notification_name' );
    // Show this field if page isn't bnfw-notifications.php
    if (strcmp(basename($_SERVER['PHP_SELF']), "bnfw-notifications.php") != 0) {
        echo '<input type="text" name="bnfw_notification_name" value="'. $settings .'">';
    }
}

function bnfw_generator_user_roles($args) {

    $settings = get_option( 'bnfw_user_roles' );

    echo '<select multiple name="bnfw_user_roles" id="' . $settings . '" class="select2 populate">';
        global $wp_roles;
        $roles = $wp_roles->get_names();

        echo '<option value="'.$settings.'" selected>' . $settings . '</option>';

        foreach($roles as $role) {
           echo '<option value="'.$role.'">' . $role . '</option>';
        }
    echo '</select>';
}


function bnfw_generator_users($args) {

    $user_roles = get_option( 'bnfw_user_roles' );
    $single_users = get_option( 'bnfw_users' );

    // Enable Users Checkbox
    echo '<div id="bnfw-users-enable" class="checkbox bnfw-field">
        <label for="bnfw-users-enable-checkbox">',
           __( 'Do you want to specify individual users from the user roles selected above?', 'bnfw' ),
        '</label>
        <input type="checkbox" id="bnfw-users-enable-checkbox">
    </div>';

    echo '<select multiple name="bnfw_users" id="' . $single_users . '" class="select2 populate">';
        global $wp_roles;
        $roles = $wp_roles->get_names();

        echo '<option value="'.$single_users.'" selected>' . $single_users . '</option>';

        foreach($roles as $role) {
           echo '<option value="'.$role.'">' . $role . '</option>';
        }
    echo '</select>';
}
