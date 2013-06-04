<?php

/* Register the plugin pages */
function bnfw_admin_menu() {
    add_menu_page(  'Better Notifications for WordPress Notifications', // The Menu Title
                    'Notifications', // The Page title
			  		'manage_options', // The capability required for access to this item
			  		'bnfw-admin', // the slug to use for the page in the URL
                    'bnfw_callback', // The function to call to render the page
                    '', // Icon URL
                    '100.111111' // Position in Admin Menu
                 );

    add_submenu_page( 'bnfw-admin', 'Notifications', 'Notifications', 'manage_options', 'bnfw-admin', 'bnfw_callback');
    add_submenu_page( 'bnfw-admin', 'Email Templates', 'Email Templates', 'manage_options', 'bnfw-email-customise', 'bnfw_email_customise_page');

    /* Enqueue scripts action */
    add_action( 'admin_enqueue_scripts', 'bnfw_admin_scripts' );

}

// Create the Admin menu pages action
add_action( 'admin_menu', 'bnfw_admin_menu' );

// Call the settings page and create it
function bnfw_callback() {
    bnfw_settings_page();
}

function bnfw_admin_scripts($hook) {

    if( strpos($hook, 'bnfw') === false){
        return;
	}

    wp_enqueue_style('bnfw-admin-css', plugin_dir_url(__FILE__) . 'css/plugin_styles.css');
}

// creates our settings in the options table
function bnfw_register_settings() {
	register_setting('bnfw_settings_group', 'bnfw_settings');
	register_setting('bnfw_email_settings_group', 'bnfw_custom_email_settings');

}
add_action('admin_init', 'bnfw_register_settings');



/***** Settings Page *****/
function bnfw_settings_page() {

	$bnfw_options = get_option('bnfw_settings');

	ob_start(); ?>


		<div class="wrap">
			
			<?php settings_errors(); ?>

			<div class="row clearfix">
				<div class="eightcol first">
					<h2>Better Notifications for WordPress - Notifications</h2>
					<h3>Email Settings for User Roles</h3>
					<p>Here, you can set which user roles you'd like to receive emails notifications for.</p>
				</div>

				<div id="paypal-donate" class="fourcol last">
					<strong class="sevencol first">We worked really hard on this plugin. If you found it useful, please consider donating via the button to the right. Thanks!</strong>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
						">
						<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
			
			<form method="post" action="options.php">
			
				<?php settings_fields('bnfw_settings_group'); ?>
			
				<div id="role-settings" class="twelvecol">

					<div id="roles" class="row clearfix">
						<div class="threecol first clearfix">&nbsp;</div>
						<?php foreach ($GLOBALS['BNFW_ACTION_TYPES'] as $bnfw_events) { ?>
							<div class="role twocol clearfix">
								<strong><?php echo $GLOBALS['BNFW_ACTION_TYPES_PRETTY'][$bnfw_events] ?></strong>
							</div>
						<?php } ?>
					</div>

					<?php $getted_roles = get_editable_roles(); foreach (get_editable_roles() as $field => $roles) { ?>

						<div class="event row clearfix">
							<div class="first threecol clearfix"><strong><?php echo $getted_roles[$field]['name']; ?></strong></div>

							<?php foreach ($GLOBALS['BNFW_ACTION_TYPES'] as $bnfw_events) { ?>
								<div class="role twocol clearfix">
									<input id="bnfw_settings[<?php echo $bnfw_events ?>-<?php echo $field ?>]" name="bnfw_settings[<?php echo $bnfw_events ?>-<?php echo $field ?>]" type="checkbox" value="1" <?php checked(1, $bnfw_options[$bnfw_events.'-'.$field]); ?> />
								</div>
							<?php } ?>

						</div>

					<?php } ?>
					
				</div>

				<div id="other-options" class="twelvecol first">
					<label for="bnfw_settings[bnfw_settings_spam]">Suppress notifications for comments marked as spam?</label>
					<input id="bnfw_settings[bnfw_settings_spam]" name="bnfw_settings[bnfw_settings_spam]" type="checkbox" value="1" <?php checked(1, $bnfw_options['bnfw_settings_spam']); ?> />
				</div>

				<p class="submit clearfix">
					<input type="submit" class="button-primary" value="Save Settings" />
				</p>
			
			</form>
		
		</div>

	<?php echo ob_get_clean();

}



/***** Email Customisation Page *****/
function bnfw_email_customise_page() {

	$bnfw_options = get_option('bnfw_custom_email_settings');

	ob_start(); ?>

		<div class="wrap">

			<?php settings_errors(); ?>

			<div class="row clearfix">
				<div class="eightcol first">
					<h2>Better Notifications for WordPress - Email Templates</h2>
					<p>Here you can customise the emails that are sent out for each of the notifications.</p>
					<p>Email are sent out in HTML (you can add HTML tags) and can include shortcodes to insert more detail where required. For a full list of all shortcodes available, please go <a href="http://www.voltronik.co.uk/wordpress-plugins/better-notifications-for-wordpress/" title="Better Notifications for WordPress - Shortcodes" target="_blank">here</a>. <br />PLEASE NOTE: Not all shortcodes can be used with all notification types due to restrictions imposed by WordPress.</p>
				</div>

				<div id="paypal-donate" class="fourcol last">
					<strong class="sevencol first">We worked really hard on this plugin. If you found it useful, please consider donating via the button to the right. Thanks!</strong>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
						">
						<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
			
			<form method="post" action="options.php">
			
				<?php settings_fields('bnfw_email_settings_group'); ?>
			
				<p class="submit clearfix">
					<input type="submit" class="button-primary" value="Save Settings" />
				</p>
			
					<div class="wrap clearfix">
						<?php foreach ($GLOBALS['BNFW_ACTION_TYPES'] as $bnfw_events) { ?>
							<div class="bnfw-email-custom-form sixcol clearfix">

								<!-- Event Type -->
								<h3 class="elevencol first clearfix"><?php echo $GLOBALS['BNFW_ACTION_TYPES_PRETTY'][$bnfw_events] ?></h3>

								<div class="sixcol first clearfix">
									<!-- Subject -->
									<label class="subject twelvecol first" for="bnfw_custom_email_settings[payload-subject-<?php echo $bnfw_events; ?>]">Email Subject</label>
									<input type="text" id="bnfw_custom_email_settings[payload-subject-<?php echo $bnfw_events; ?>]" name="bnfw_custom_email_settings[payload-subject-<?php echo $bnfw_events; ?>]" value="<?php echo $bnfw_options['payload-subject-'.$bnfw_events] ?>" />

									<!-- Message Body -->
									<label class="message-body twelvecol first" for="bnfw_custom_email_settings[payload-body-<?php echo $bnfw_events; ?>]">Email Message Body</label>
									<textarea id="bnfw_custom_email_settings[payload-body-<?php echo $bnfw_events; ?>]" name="bnfw_custom_email_settings[payload-body-<?php echo $bnfw_events; ?>]"><?php echo $bnfw_options['payload-body-'.$bnfw_events] ?></textarea>
								</div>

								<div class="bnfw-preview left">
									<h4>Email Message Body Preview</h4>

									<div class="bnfw-preview-content">
										<?php echo $bnfw_options['payload-body-'.$bnfw_events] ?>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					
				<p class="submit clearfix">
					<input type="submit" class="button-primary" value="Save Settings" />
				</p>
			
			</form>

		</div>

	<?php echo ob_get_clean();
}