jQuery(document).ready(function($) {
    function toggle_fields() {
    	var show_fields = $('#show-fields').is(":checked");

    	if ( show_fields ) {
			$('#email, #cc, #bcc').show();
    	} else {
			$('#email, #cc, #bcc').hide();
    	}
    }

    function toggle_users() {
    	if ( $( '#only-post-author' ).is( ':checked' ) ) {
    		$( '#users, #current-user' ).hide();
    	} else {
    		$( '#users, #current-user' ).show();
    	}
    }

	function init() {
		var notification = $('#notification').val();

		$(".select2").select2();
		$(".user-select2").select2( {
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				data: function( params ) {
					return {
						action: 'bnfw_search_users',
						query: params.term,
						page: params.page
					};
				},
				processResults: function( data, page ) {
					return {
						results: data
					};
				}
			},
			minimumInputLength: 1
		} );

		if ( ! $( '#notification' ).length ) {
			return;
		}

		toggle_fields();

		if ( 'new-user' === $('#notification').val() || 'welcome-email' === $('#notification').val() || 'reply-comment' === $('#notification').val() || notification.startsWith( 'commentreply-' ) ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #disable-autop, #current-user, #post-author').hide();
			$('#user-password-msg').show();
		} else if ( 'user-password' === $('#notification').val() || 'user-role' === notification ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #current-user, #post-author').hide();
			$('#user-password-msg, #disable-autop, #email-formatting').show();
		} else if ( 'new-comment' === $('#notification').val() || 'new-trackback' === $('#notification').val() || 'new-pingback' === $('#notification').val() || 'admin-password' === $('#notification').val() || 'admin-user' === $('#notification').val() || 'admin-role' === notification ) {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			toggle_fields();
			toggle_users();
			$('#user-password-msg').hide();
		} else {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			toggle_fields();
			toggle_users();
			$('#user-password-msg').hide();
		}
	}

	init();
    $('#notification').on('change', function() {
		var $this = $(this),
			notification = $this.val();

		if ( 'new-user' === $this.val() || 'welcome-email' === $this.val() || 'reply-comment' === $this.val() || notification.startsWith( 'commentreply-' ) ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #email-formatting, #disable-autop, #current-user, #post-author').hide();
			$('#user-password-msg').show();
		} else if ( 'user-password' === $this.val() || 'user-role' === notification ) {
			$('#toggle-fields, #email, #cc, #bcc, #users, #current-user, #post-author').hide();
			$('#user-password-msg, #disable-autop, #email-formatting').show();
		} else if ( 'admin-password' === $('#notification').val() || 'admin-user' === $('#notification').val() || 'admin-role' === notification ) {
			$('#post-author').hide();
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user').show();
			$('#user-password-msg').hide();
			toggle_fields();
			toggle_users();
		} else {
			$('#toggle-fields, #users, #email-formatting, #disable-autop, #current-user, #post-author').show();
			$('#user-password-msg').hide();
			toggle_fields();
			toggle_users();
		}
    });

    $('#show-fields').change(function() {
    	toggle_fields();
    });

    $( '#only-post-author' ).change(function() {
		toggle_users();
	} );

	// send test email
	$( '#test-email' ).click(function() {
		$( '#send-test-email' ).val( 'true' );
	});

	// Validate before saving notification
	$( '#publish' ).click(function() {
		if ( $('#users').is(':visible') ) {
			if ( null === $('#users-select').val() ) {
				$('#bnfw_error').remove();
				$('.wrap h1').after('<div class="error" id="bnfw_error"><p>' + BNFW.empty_user + '</p></div>');
				return false;
			}
		}

		return true;
	});

	$( '#shortcode-help' ).on( 'click', function() {
		var notification = $( '#notification' ).val(),
			notification_slug = '',
			splited;

		switch( notification ) {
			case 'new-comment':
			case 'new-trackback':
			case 'new-pingback':
			case 'reply-comment':
			case 'commentreply-page':
			case 'user-password':
			case 'admin-password':
			case 'new-user':
			case 'welcome-email':
			case 'user-role':
			case 'admin-role':
			case 'admin-user':
			case 'new-post':
			case 'update-post':
			case 'pending-post':
			case 'future-post':
			case 'newterm-category':
			case 'newterm-post_tag':
				notification_slug = notification;
				break;

			default:
				splited = notification.split( '-' );
				switch( splited[0] ) {
					case 'new':
					case 'update':
					case 'pending':
					case 'future':
					case 'comment':
						notification_slug = splited[0] + '-post';
						break;
					case 'commentreply':
						notification_slug = splited[0] + '-post';
						break;
					case 'newterm':
						notification_slug = 'newterm-category';
						break;
				}

				break;
		}

		$(this).attr( 'href', 'https://betternotificationsforwp.com/shortcodes/?notification=' + notification_slug + '&utm_source=WP%20Admin%20Notification%20Editor%20-%20"Shortcode%20Help"&utm_medium=referral' );
	});
});
