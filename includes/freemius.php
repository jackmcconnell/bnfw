<?php
/**
 * All Freemius related code.
 */

// Create a helper function for easy Freemius SDK access.
function bnfw_fs() {
	global $bnfw_fs;

	if ( ! isset( $bnfw_fs ) ) {
		require_once dirname( __FILE__ ) . '/../freemius/start.php';

		$bnfw_fs = fs_dynamic_init( array(
			'id'             => '1196',
			'slug'           => 'bnfw',
			'type'           => 'plugin',
			'public_key'     => 'pk_86a5f01815501058ac6e3ec74ac16',
			'is_premium'     => false,
			'has_addons'     => false,
			'has_paid_plans' => false,
			'menu'           => array(
				'slug'    => 'edit.php?post_type=bnfw_notification',
				'account' => false,
				'contact' => false,
				'support' => false,
			),
		) );
	}

	return $bnfw_fs;
}
bnfw_fs();
do_action( 'bnfw_fs_loaded' );

// Freemius Custom Opt-in Message (Existing Users)
function bnfw_fs_custom_connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
	return sprintf(
		__fs( 'hey-x' ) . '<br>' .
		__( 'Please help me improve %2$s! If you opt-in, some data about your usage of %2$s will be recorded and sent to me. If you skip this, that\'s okay! %2$s will still work just fine.', 'bnfw' ),
		$user_first_name,
		'<b>' . $plugin_title . '</b>',
		'<b>' . $user_login . '</b>',
		$site_link,
		$freemius_link
	);
}
bnfw_fs()->add_filter( 'connect_message_on_update', 'bnfw_fs_custom_connect_message_on_update', 10, 6 );

// Freemius Custom Opt-in Message (New Users)
function bnfw_fs_custom_connect_message( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {
	return sprintf(
		__fs( 'hey-x' ) . '<br>' .
		__( 'Never miss an important update for %2$s - opt-in to security and feature update notifications and help me improve the plugin by sending non-sensitive diagnostic tracking.', 'bnfw' ),
		$user_first_name,
		'<b>' . $plugin_title . '</b>',
		'<b>' . $user_login . '</b>',
		$site_link,
		$freemius_link
	);
}
bnfw_fs()->add_filter( 'connect_message', 'bnfw_fs_custom_connect_message', 10, 6 );

// Freemius Custom Icon
function bnfw_fs_custom_icon() {
	return dirname( __FILE__ ) . '/../assets/img/icon-256x256.png';
}
bnfw_fs()->add_filter( 'plugin_icon', 'bnfw_fs_custom_icon' );

// Freemius Custom Text Strings
if ( function_exists( 'fs_override_i18n' ) ) {
	fs_override_i18n( array(
		'few-plugin-tweaks'                        => __( "I've made a few tweaks to the plugin, %s", 'bnfw' ),
		'opt-out-message-appreciation'             => __( 'I appreciate your help in making the %s better by letting me track some usage data.', 'bnfw' ),
		'opt-out-message-usage-tracking'           => __( "Usage tracking is done in the name of making %s better. Making a better user experience, prioritizing new features, and more good things. I'd really appreciate if you'll reconsider letting me continue with the tracking.", 'bnfw' ),
		'opt-out-message-clicking-opt-out'         => __( 'By clicking "Opt Out", I will no longer be sending any data from %s to %s.', 'bnfw' ),
		'opt-out'                                  => __( 'Opt Out', 'bnfw' ),
		'opt-out-cancel'                           => __( 'On second thought - I want to continue helping', 'bnfw' ),
		'opting-out'                               => __( 'Opting out...', 'bnfw' ),
		'opting-in'                                => __( 'Opting in...', 'bnfw' ),
		'anonymous-feedback'                       => __( 'Anonymous feedback', 'bnfw' ),
		'quick-feedback'                           => __( 'Quick feedback', 'bnfw' ),
		'deactivation-share-reason'                => __( 'If you have a moment, please let me know why you are deactivating', 'bnfw' ),
		'deactivation-modal-button-confirm'         => __( 'Yes - Deactivate', 'bnfw' ),
		'deactivation-modal-button-submit'         => __( 'Submit & Deactivate', 'bnfw' ),
		'cancel'                                   => __( 'Cancel', 'bnfw' ),
		'reason-no-longer-needed'                  => __( 'I no longer need the plugin', 'bnfw' ),
		'reason-found-a-better-plugin'             => __( 'I found a better plugin', 'bnfw' ),
		'reason-needed-for-a-short-period'         => __( 'I only needed the plugin for a short period', 'bnfw' ),
		'reason-broke-my-site'                     => __( 'The plugin broke my site', 'bnfw' ),
		'reason-suddenly-stopped-working'          => __( 'The plugin suddenly stopped working', 'bnfw' ),
		'reason-cant-pay-anymore'                  => __( "I can't pay for it anymore", 'bnfw' ),
		'reason-temporary-deactivation'            => __( "It's a temporary deactivation. I'm just debugging an issue.", 'bnfw' ),
		'reason-other'                             => __( 'Other',
			'the text of the "other" reason for deactivating the plugin that is shown in the modal box.', 'bnfw' ),
		'ask-for-reason-message'                   => __( 'Kindly tell me the reason so I can improve.', 'bnfw' ),
		'placeholder-plugin-name'                  => __( "What's the plugin's name?", 'bnfw' ),
		'placeholder-comfortable-price'            => __( 'What price would you feel comfortable paying?', 'bnfw' ),
		'reason-couldnt-make-it-work'              => __( "I couldn't understand how to make it work", 'bnfw' ),
		'reason-great-but-need-specific-feature'    => __( "The plugin is great, but I need specific feature that you don't support", 'bnfw' ),
		'reason-not-working'                       => __( 'The plugin is not working', 'bnfw' ),
		'reason-not-what-i-was-looking-for'        => __( "It's not what I was looking for", 'bnfw' ),
		'reason-didnt-work-as-expected'            => __( "The plugin didn't work as expected", 'bnfw' ),
		'placeholder-feature'                      => __( 'What feature?', 'bnfw' ),
		'placeholder-share-what-didnt-work'        => __( "Kindly share what didn't work so I can fix it for future users...", 'bnfw' ),
		'placeholder-what-youve-been-looking-for'  => __( "What you've been looking for?", 'bnfw' ),
		'placeholder-what-did-you-expect'          => __( "What did you expect?", 'bnfw' ),
		'reason-didnt-work'                        => __( "The plugin didn't work", 'bnfw' ),
		'reason-dont-like-to-share-my-information' => __( "I don't like to share my information with you", 'bnfw' ),
		'dont-have-to-share-any-data'              => __( "You might have missed it, but you don't have to share any data and can just %s the opt-in.", 'bnfw' ),
	), 'bnfw' );
}
